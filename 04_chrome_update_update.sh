#!/bin/bash -e
APP_CODE=$1
APP_VERSION=$2
CHROME_APP_ID=$3

sed -e s/%%CHROME_APP_ID%%/"$CHROME_APP_ID"/ chrome/update_chrome-default.xml | \
sed -e s/%%APP_CODE%%/"$APP_CODE"/g | \
sed -e s/%%APP_VERSION%%/"$APP_VERSION"/g \
> versions/$APP_CODE/update_chrome.xml