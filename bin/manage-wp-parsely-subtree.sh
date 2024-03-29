#!/bin/sh

set -e

if [ $# -lt 2 ]; then
  echo "Syntax: manage-wp-parsely-subtree.sh <tree_version> <parsely_tag>"
  echo
  echo "Example: manage-wp-parsely-subtree.sh 2.4 2.4.1"
  echo
  echo "If subtree already exists:"
  echo "  This command will update the wp-parsely-2.4 folder to the tag 2.4.1"
  echo "If the subtree does not already exist:"
  echo "  This command will create a new folder wp-parsely-2.4 using the tag 2.4.1"
  exit 1
fi

tree_version=$1
parsely_tag=$2

tree_dir="wp-parsely-${tree_version}"
if [ -e "$tree_dir" ]; then
  echo "Removing existing wp-parsely folder $tree_dir with tag $parsely_tag"

  rm -rf $tree_dir
fi

echo "Creating new wp-parsely folder $tree_dir using tag: $parsely_tag"

wget "https://downloads.wordpress.org/plugin/wp-parsely.$parsely_tag.zip"
unzip "wp-parsely.$parsely_tag.zip"
mv wp-parsely $tree_dir
rm "wp-parsely.$parsely_tag.zip"
