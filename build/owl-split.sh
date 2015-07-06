#
# Form Widgets
#
./git-subsplit.sh init git@github.com:october-widgets/library.git
./git-subsplit.sh publish --no-tags src/FormWidgets/Comment:git@github.com:october-widgets/comment.git
./git-subsplit.sh publish --no-tags src/FormWidgets/Hasmany:git@github.com:october-widgets/hasmany.git
./git-subsplit.sh publish --no-tags src/FormWidgets/Knob:git@github.com:october-widgets/knob.git
./git-subsplit.sh publish --no-tags src/FormWidgets/Tagbox:git@github.com:october-widgets/tagbox.git
./git-subsplit.sh publish --no-tags src/FormWidgets/Address:git@github.com:october-widgets/address.git
./git-subsplit.sh publish --no-tags src/FormWidgets/Money:git@github.com:october-widgets/money.git
./git-subsplit.sh publish --no-tags src/FormWidgets/Password:git@github.com:october-widgets/password.git

#
# Widgets
#
./git-subsplit.sh publish --no-tags src/Widgets/TreeSort:git@github.com:october-widgets/treesort.git

#
# Behaviors
#
./git-subsplit.sh publish --no-tags src/Behaviors/ListCheckboxDelete:git@github.com:october-widgets/list-checkbox-delete.git

rm -rf .subsplit/
