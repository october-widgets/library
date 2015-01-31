#
# This will split up each widget to its own github repo
#

./git-subsplit.sh init git@github.com:october-widgets/library.git
./git-subsplit.sh publish --no-tags src/FormWidgets/Comment:git@github.com:october-owl/comment.git
./git-subsplit.sh publish --no-tags src/FormWidgets/GeoAddress:git@github.com:october-owl/geoaddress.git
./git-subsplit.sh publish --no-tags src/FormWidgets/Hasmany:git@github.com:october-owl/hasmany.git
./git-subsplit.sh publish --no-tags src/FormWidgets/Tagbox:git@github.com:october-owl/tagbox.git
rm -rf .subsplit/