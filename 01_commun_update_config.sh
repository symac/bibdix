#!/bin/bash -e
APP_RCR=$1
APP_URL_0=$2
APP_URL_1=$3
APP_URL_2=$4
APP_NAME=$5
APP_ICONS_HEIGHT=$6
APP_ICONS_WIDTH=$7
APP_DASHES=$8

sed -e s/%%APP_RCR%%/"$APP_RCR"/ chrome/data/config-default.js | \
sed -e "s;%%APP_URL_0%%;$APP_URL_0;" | \
sed -e "s;%%APP_URL_1%%;$APP_URL_1;" | \
sed -e "s;%%APP_URL_2%%;$APP_URL_2;" | \
sed -e s/%%APP_ICONS_HEIGHT%%/"$APP_ICONS_HEIGHT"/ | \
sed -e s/%%APP_ICONS_WIDTH%%/"$APP_ICONS_WIDTH"/ | \
sed -e s/%%APP_DASHES%%/"$APP_DASHES"/ | \
sed -e s/%%APP_NAME%%/"$APP_NAME"/ \
> chrome/data/config.js

sed -e s/%%APP_RCR%%/"$APP_RCR"/ firefox/lib/config-default.js | \
sed -e "s;%%APP_URL_0%%;$APP_URL_0;" | \
sed -e "s;%%APP_URL_1%%;$APP_URL_1;" | \
sed -e "s;%%APP_URL_2%%;$APP_URL_2;" | \
sed -e s/%%APP_ICONS_HEIGHT%%/"$APP_ICONS_HEIGHT"/ | \
sed -e s/%%APP_ICONS_WIDTH%%/"$APP_ICONS_WIDTH"/ | \
sed -e s/%%APP_DASHES%%/"$APP_DASHES"/ | \
sed -e s/%%APP_NAME%%/"$APP_NAME"/ \
> firefox/lib/config.js

