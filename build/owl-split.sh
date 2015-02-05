#
# This will split up each widget to its own github repo
#

./git-subsplit.sh init git@github.com:october-widgets/library.git
./git-subsplit.sh publish --no-tags src/FormWidgets/Comment:git@github.com:october-widgets/comment.git
./git-subsplit.sh publish --no-tags src/FormWidgets/Hasmany:git@github.com:october-widgets/hasmany.git
./git-subsplit.sh publish --no-tags src/FormWidgets/Tagbox:git@github.com:october-widgets/tagbox.git
./git-subsplit.sh publish --no-tags src/FormWidgets/GeoAddress:git@github.com:october-widgets/geoaddress.git
rm -rf .subsplit/