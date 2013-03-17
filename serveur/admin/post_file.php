<?php
  // On va stocker dans la base la clé CHROME
  require("../../include/db_config.php");
  require("../../include/utils.php");
   
  
  $secret_key = getParam("secret_key");
  $app_code   = getParam("app_code");
  $file_type  = getParam("file_type");
  
  # On va vérifier la concordance entre la clé secrète et ce qu'on a dans la base
  $res = SQL("select * from bibdix_versions where code = '$app_code' and secret_key='$secret_key'");
  if (mysql_numrows($res) != 1)
  {
    print "ERREUR dans l'envoi du fichier pour $file_type / $app_code [concordance de clé KO]";
    exit;
  }
  if ($_FILES["upFile"]["error"] == UPLOAD_ERR_OK)
  {
    // On va copier le fichier dans le répertoire qui va bien
    $tmp_name = $_FILES["upFile"]["tmp_name"];
    $name = $_FILES["upFile"]["name"];
    move_uploaded_file($tmp_name, "../versions/".$app_code."/$name");
  }
?>