#!/bin/bash -e
APP_CODE=$1

wget --quiet -O versions/"$APP_CODE"/config.ini http://www.geobib.fr/bibdix/admin/get_config.php?code="$APP_CODE"

for image in {"absent.png","inconnu.png","present.png","icon.png","icon_inactif.png","icon_actif.png"}; do
  wget -S -O versions/"$APP_CODE"/img/"$image" http://www.geobib.fr/bibdix/versions/"$APP_CODE"/img/"$image"
done