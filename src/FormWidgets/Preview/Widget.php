<?php namespace Owl\FormWidgets\Preview;

use Backend\Classes\FormWidgetBase;
use System\Models\File;
use System\Classes\SystemException;
use October\Rain\Support\ValidationException;
// use RainLab\Blog\Models\Post;
use Validator;
use Input;
use Response;
use Exception;
use Lang;
use October\Rain\Support\Markdown;
use Owl\FormWidgets\Preview\Classes\TagProcessor;

/**
 * Preview area for the Create/Edit Post form.
 *
 * @package rainlab\blog
 * @author Alexey Bobkov, Samuel Georges
 */
class Widget extends FormWidgetBase
{

    public function formatHtml($input, $preview = false)
    {
        $result = Markdown::parse(trim($input));
        if ($preview)
            $result = str_replace('<pre>', '<pre class="prettyprint">', $result);
        $result = TagProcessor::instance()->processTags($result, $preview);
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        $this->vars['preview_html'] = $this->formatHtml($this->model->content, true);
        return $this->makePartial('widget');
    }

    /**
     * {@inheritDoc}
     */
    public function loadAssets()
    {
        $this->addJs('js/widget.js');
    }

    public function init()
    {
        $this->checkUploadPostback();
    }
    protected function checkUploadPostback()
    {
        if (!post('X_BLOG_IMAGE_UPLOAD'))
            return;
        $uploadedFileName = null;
        try {
            $uploadedFile = Input::file('file');
            if ($uploadedFile)
                $uploadedFileName = $uploadedFile->getClientOriginalName();
            $validationRules = ['max:'.File::getMaxFilesize()];
            $validationRules[] = 'mimes:jpg,jpeg,bmp,png,gif';
            $validation = Validator::make(
                ['file_data' => $uploadedFile],
                ['file_data' => $validationRules]
            );
            if ($validation->fails())
                throw new ValidationException($validation);
            if (!$uploadedFile->isValid())
                throw new SystemException(Lang::get('cms::lang.asset.file_not_valid'));
            $fileRelation = $this->model->content_images();
            $file = new File();
            $file->data = $uploadedFile;
            $file->is_public = true;
            $file->save();
            $fileRelation->add($file, $this->sessionKey);
            $result = [
                'file' => $uploadedFileName,
                'path' => $file->getPath()
            ];
            $response = Response::make()->setContent($result);
            $response->send();
            die();
        } catch (Exception $ex) {
            $message = $uploadedFileName
                ? Lang::get('cms::lang.asset.error_uploading_file', ['name' => $uploadedFileName, 'error' => $ex->getMessage()])
                : $ex->getMessage();
            $result = [
                'error' => $message,
                'file' => $uploadedFileName
            ];
            $response = Response::make()->setContent($result);
            $response->send();
            die();
        }
    }
}