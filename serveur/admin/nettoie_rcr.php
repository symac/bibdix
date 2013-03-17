<?php
  // Nettoie la table bibdix_rcr créé à partir du fichier XLS fourni par l'ABES
  require("../../include/db_config.php");
  require("../../include/utils.php");
  
  $res = SQL("select * from bibdix_rcr");
  
  while ($row = mysql_fetch_assoc($res))
  {
    $id = $row["id"];
    $lib = $row["lib"];
    $lib_light = appauvrit($lib);
    
    SQL("update bibdix_rcr set lib_light = '$lib_light' where id = $id");
  }
  
  print "Mise à jour OK";
?>