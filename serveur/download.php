<?php
  include("include/log.php");
  include("../include/db_config.php");
  include("../include/utils.php");
  
  $browser = getParam("browser");
  $app_code = getParam("app_code");
  $version = getParam("version");
  if ( (!$browser) or (!$app_code) )
  {
    print "Manque un paramètre";
    exit;
  }
  
  if ($version == "latest")
  {
    // On doit aller dans la base chercher le code de la dernière version
    $res = SQL("select * from bibdix_versions where code = '$app_code'");
    if (mysql_numrows($res) != 1)
    {
      print "Erreur dans le téléchargement, merci d'envoyer un mail à smachefert <à> gmail.com";
      die;
    }
    else
    {
      $row = mysql_fetch_assoc($res);
      $version = $row["version"];
    }
  }
  
  $filename = "versions/".$app_code."/bibdix_".$browser."_".$app_code."_".$version;
  if( $browser == "firefox")
  {
    $filename .= ".xpi";
  }
  elseif ($browser == "chrome")
  {
    $filename .= ".crx";
  }
  else
  {
    print "Extension non disponible pour ce navigateur : \"$browser\"";
    exit;
  }
  
  if (!file_exists($filename))
  {
    print "Cette combinaison de navigateur et de version n'existe pas ($filename)";
    exit;
  }
  
  // On va stocker les informations
  $ip = $_SERVER["REMOTE_ADDR"];
  $user_agent = $_SERVER['HTTP_USER_AGENT'];
  
  SQL("insert into bibdix_log (`app_code`, `user_agent`, `app_nav`, `action`, `ip`) values ('$app_code', '".addslashes($user_agent)."','$browser', 'install', '$ip')");
  header("Location: $filename");
?>