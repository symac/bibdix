#!/bin/bash -e
#
# Purpose: Pack a Chromium extension directory into crx format

# update_manifest.sh bx3 "Bordeaux 3 - Bibdix" "DESC" "VERSION"

APP_VERSION="0.1.1"
APP_CODE=$1
DIR_ORIG="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
  
if [ -z "$APP_CODE" ]
then
	echo "Manque le code bib"
	exit
fi

# On récupère la secret_key qui va nous servir à mettre les fichiers en ligne
SECRET_KEY=$(head -n1 versions/"$APP_CODE"/key.txt)

./00_get_remote_config.sh $APP_CODE

config_file=versions/"$APP_CODE"/config.ini

if [ ! -f $config_file ]
then
  echo "#### Fichier de configuration absent ####"
  exit
fi

APP_NAME=$(awk -F '=' '/APP_NAME/ {print $2}' $config_file)
APP_DESCRIPTION=$(awk -F '=' '/APP_DESCRIPTION/ {print $2}' $config_file)

APP_URL_0=$(grep "APP_URL_0" $config_file | cut -d= -f2-)
APP_URL_1=$(grep "APP_URL_1" $config_file | cut -d= -f2-)
APP_URL_2=$(grep "APP_URL_2" $config_file | cut -d= -f2-)
APP_RCR=$(awk -F '=' '/APP_RCR/ {print $2}' $config_file)

APP_ICONS_HEIGHT=$(awk -F '=' '/APP_ICONS_HEIGHT/ {print $2}' $config_file)
APP_ICONS_WIDTH=$(awk -F '=' '/APP_ICONS_WIDTH/ {print $2}' $config_file)

CHROME_APP_ID=$(grep "CHROME_APP_ID" $config_file | cut -d= -f2-)
FIREFOX_APP_ID=$(grep "FIREFOX_APP_ID" $config_file | cut -d= -f2-)

#############
## COMMUNS ##
#############
# 1. On va reconstruire les fichier config.js de chrome & firefox
if [ -e chrome/data/config.js ]
then
  rm chrome/data/config.js
fi

if [ -e firefox/lib/config.js ]
then
  rm firefox/lib/config.js
fi

./01_commun_update_config.sh "$APP_RCR" "$APP_URL_0" "$APP_URL_1" "$APP_URL_2" "$APP_NAME" "$APP_ICONS_HEIGHT" "$APP_ICONS_WIDTH"

# 2. On va copier les images de l'extension qu'on est en train de reconstruire
for image in {"absent.png","inconnu.png","present.png","icon.png","icon_inactif.png","icon_actif.png"}; do
  if [ -e chrome/data/img/"$image" ]
  then
    rm chrome/data/img/"$image"
  fi
  cp versions/"$APP_CODE"/img/"$image" chrome/data/img/"$image"
  
  if [ -e firefox/data/img/"$image" ]
  then
    rm firefox/data/img/"$image"
  fi
  cp versions/"$APP_CODE"/img/"$image" firefox/data/img/"$image"
done

############
## CHROME ##
############


# 1. On va reconstruire le fichier manifest.json de chrome
if [ -e chrome/manifest.json ]
then
  rm chrome/manifest.json
fi
./02_chrome_update_manifest.sh "$APP_CODE" "$APP_VERSION" "$APP_NAME" "$APP_DESCRIPTION"


# Construction de l'extension
# Deux cas : soit chrome.pem est déjà disponible pour cette extension, auquel cas c'est une mise à jour, on va utiliser le script
# standard. Soit c'est une création, on fait appel à chrome

if [ -e versions/"$APP_CODE"/chrome.pem ]
then
    ./03_chrome_build_crx.sh chrome "versions/$APP_CODE/chrome.pem" "versions/$APP_CODE/bibdix_chrome_"$APP_CODE"_"$APP_VERSION".crx"
    # On va récupérer l'id
    CHROME_APP_ID=$(cat versions/"$APP_CODE"/chrome.pem | \
    openssl rsa -pubout -outform DER | \
    openssl dgst -sha256 | \
    awk '{print $2}' | \
    cut -c 1-32 | \
    tr '0-9a-f' 'a-p')

    # On va mettre à jour le chrome_id sur le serveur pour le récupérer plus tard
    echo http://www.geobib.fr/bibdix/admin/update_keys?app_code="$APP_CODE"\&app_chrome_id="$CHROME_APP_ID"
    wget --quiet http://www.geobib.fr/bibdix/admin/update_keys?app_code="$APP_CODE"\&app_chrome_id="$CHROME_APP_ID"
else
    if [ -e chrome.pem ]
    then
        rm chrome.pem
    fi
    if [ -e chrome.crx ]
    then
        rm chrome.crx
    fi
    
    google-chrome --pack-extension="chrome"

    cp chrome.crx "versions/$APP_CODE/bibdix_chrome_"$APP_CODE"_"$APP_VERSION".crx"
    cp chrome.pem "versions/$APP_CODE"

    CHROME_APP_ID=$(cat versions/"$APP_CODE"/chrome.pem | \
    openssl rsa -pubout -outform DER | \
    openssl dgst -sha256 | \
    awk '{print $2}' | \
    cut -c 1-32 | \
    tr '0-9a-f' 'a-p')

    # On va mettre à jour le chrome_id sur le serveur pour le récupérer plus tard
    echo http://www.geobib.fr/bibdix/admin/update_keys?app_code="$APP_CODE"\&app_chrome_id="$CHROME_APP_ID"
    wget --quiet http://www.geobib.fr/bibdix/admin/update_keys?app_code="$APP_CODE"\&app_chrome_id="$CHROME_APP_ID"
    # On va remettre à jour le fichier de config (pas forcément nécessaire mais permet de partir sur une base propre)
    ./00_get_remote_config.sh $APP_CODE
fi

# 3. Construction du fichier updates
./04_chrome_update_update.sh "$APP_CODE" "$APP_VERSION" "$CHROME_APP_ID"

#############
## FIREFOX ##
#############
# Trouver les infos de mise à jour. Générer un fichier de mise à jour : https://developer.mozilla.org/en-US/docs/Extension_Versioning,_Update_and_Compatibility#Update_RDF_Format
if [ -e firefox/package.json ]
then
  rm firefox/package.json
fi
./05_firefox_update_package.sh "$APP_CODE" "$APP_VERSION" "$APP_NAME" "$APP_DESCRIPTION"

./06_firefox_update_update.sh  "$APP_CODE" "$APP_VERSION" "$FIREFOX_APP_ID"

firefox_sdk_dir=/home/sylvain/Dev/addon-sdk-1.13.2
cd $firefox_sdk_dir
source bin/activate
cfx xpi --pkgdir=/home/sylvain/Dropbox/boulot/bibdix/firefox --update-url https://ssl2.ovh.net/~opensour/bibdix/check_for_updates.php?app_code="$APP_CODE"\&browser=firefox

mv "$firefox_sdk_dir"/bibdix-"$APP_CODE".xpi /home/sylvain/Dropbox/boulot/bibdix/versions/"$APP_CODE"/bibdix_firefox_"$APP_CODE"_"$APP_VERSION".xpi
cd /home/sylvain/Dropbox/boulot/bibdix/versions/"$APP_CODE"/
# On va extraire le install.rdf
unzip bibdix_firefox_"$APP_CODE"_"$APP_VERSION".xpi install.rdf
sed -i "s/<em:minVersion>.*/<em:minVersion>2.9.*<\/em:minVersion>/g" install.rdf
zip bibdix_firefox_"$APP_CODE"_"$APP_VERSION".xpi install.rdf
rm install.rdf

# À la fin on gère la mise en ligne des fichiers de manière ineractive
read -p "Envoyer les fichiers sur le serveur (O/N) :" ANSWER
if [ $ANSWER == "O" ]
then
  # On va poster les fichiers sur le serveur
  cd $DIR_ORIG
  for file in {"versions/"$APP_CODE"/update_firefox.rdf","versions/"$APP_CODE"/update_chrome.xml","versions/"$APP_CODE"/bibdix_firefox_"$APP_CODE"_"$APP_VERSION".xpi","versions/"$APP_CODE"/bibdix_chrome_"$APP_CODE"_"$APP_VERSION".crx"}; do
    if [ -e $file ]
    then
      # On copie sur le serveur
      curl -F secret_key=$SECRET_KEY -F app_code=$APP_CODE -F upFile="@"$file http://www.geobib.fr/bibdix/admin/post_file.php
    else
      echo "Fichier introuvale : "$file
    fi
  done
else
  echo "***** Opération effectuée mais fichiers non envoyés sur le serveur"
fi