<?php
  include("include/log.php");
  include("../include/db_config.php");
  include("../include/utils.php");
  
  $browser = getParam("browser");
  $app_code = getParam("app_code");

  if ( (!$browser) or (!$app_code) )
  {
    print "Manque un paramètre";
    exit;
  }
  
  $filename = "versions/$app_code/";
  if( $browser == "firefox")
  {
    $filename .= "update_firefox.rdf";
  }
  elseif ($browser == "chrome")
  {
    $filename .= "update_chrome.xml";
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
  SQL("insert into bibdix_log (`app_code`, `app_nav`, `action`, `ip`) values ('$app_code', '$browser', 'update', '$ip')");    
  
  header("Location: $filename");
?>