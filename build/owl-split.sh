#
# This will split up each widget to its own github repo
#

./git-subsplit.sh init git@github.com:october-widgets/library.git
./git-subsplit.sh publish --no-tags src/FormWidgets/Comment:git@github.com:october-owl/comment.git
rm -rf .subsplit/