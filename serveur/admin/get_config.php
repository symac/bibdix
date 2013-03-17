<?php
  include("../../include/db_config.php");
  include("../../include/utils.php");

  $code = getParam("code");
  
  if (!$code)
  {
    print "Manque un code en param&egrave;tre";
    exit;
  }
  
  $res = SQL("select * from bibdix_versions where code = '$code'");
  if (mysql_num_rows($res) != 1)
  {
    print "Erreur d'interrogation";
    exit;
  }
  $row = mysql_fetch_assoc($res);
  header("Content-type: text/plain");
  
  print "APP_NAME=".$row["nom"]."\n";
  print "APP_DESCRIPTION=".$row["description"]."\n\n";

  print "APP_URL_0=".preg_replace("/&/", "\&", $row["url_0"])."\n";
  print "APP_URL_1=".preg_replace("/&/", "\&", $row["url_1"])."\n";
  
  if (file_exists("../versions/$code/img/present.png"))
  {
    list($width, $height, $type, $attr) = getimagesize("../versions/$code/img/present.png");
    
    print "APP_ICONS_HEIGHT=".$height."\n";
    print "APP_ICONS_WIDTH=".$width."\n\n";
  }
  else
  {
    
  }
  

  print "APP_RCR=".$row["rcr"]."\n\n";
  
  print "CHROME_APP_ID=".$row["chrome_app_id"]."\n";
  print "FIREFOX_APP_ID=".$row["firefox_app_id"]."\n";

?>