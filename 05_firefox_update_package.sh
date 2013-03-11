#!/bin/bash -e
APP_CODE=$1
APP_VERSION=$2
APP_NAME=$3
APP_DESCRIPTION=$4

sed -e s/%%APP_CODE%%/"$APP_CODE"/ firefox/package-default.json | \
sed -e s/%%APP_VERSION%%/"$APP_VERSION"/ | \
sed -e s/%%APP_NAME%%/"$APP_NAME"/ | \
sed -e s/%%APP_DESCRIPTION%%/"$APP_DESCRIPTION"/ \
> firefox/package.json