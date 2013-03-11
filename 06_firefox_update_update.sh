#!/bin/bash -e
APP_CODE=$1
APP_VERSION=$2
FIREFOX_APP_ID=$3

sed -e s/%%APP_CODE%%/"$APP_CODE"/g firefox/update_firefox-default.rdf | \
sed -e s/%%APP_VERSION%%/"$APP_VERSION"/g | \
sed -e s/%%FIREFOX_APP_ID%%/"$FIREFOX_APP_ID"/g \
> versions/"$APP_CODE"/update_firefox.rdf