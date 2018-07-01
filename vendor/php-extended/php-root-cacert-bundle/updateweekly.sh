#!/bin/sh
# script for php-extended/php-root-cacert-bundle
# update of the cacert.pem once a week, and update the tag of the repo

# change directory to __DIR__
cd `dirname $0`

php updatecacert.php

git push git@gitlab.com:php-extended/php-root-cacert-bundle
git push --tags git@gitlab.com:php-extended/php-root-cacert-bundle

