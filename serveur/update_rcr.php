<?php
  // Script dont le but est d'ajouter les RCR qui manquent dans la table
  // bibdix_rcr mais pas possible pour le moment car le libellé court
  // est pas récupérable via les API

  include("include/log.php");
  include("../include/db_config.php");
  include("../include/utils.php");
  
  $res = SQL("select rcr from bibdix_versions");
  while ($row = mysql_fetch_assoc($res))
  {
    $liste_rcr = $row["rcr"];
    $tab_rcr = preg_split("/,/", $liste_rcr);
    foreach ($tab_rcr as $rcr)
    {
      $res_rcr = SQL("select * from bibdix_rcr where 
      print $rcr."<br/>";
    }
  }
?>
