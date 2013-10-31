<?php
  include("include/log.php");
  include("../include/db_config.php");
  include("../include/utils.php");
  
  
  // On va aller chercher les téléchargements

  $all = getParam("all");
  if ($all == 1)
  {
    $res = SQL("SELECT substring( time, 1, 10 ) as jour, app_code, action, count(*) as nb FROM `bibdix_log` group by jour, app_code, action");
  }
  else
  {
    // On va chercher le jour d'il y a 3 jours (durée correcte)
    $jour_base = date('Y-m-d', strtotime('-3 days')). " 00:00:00";
    $sql = "SELECT substring( time, 1, 10 ) as jour, app_code, action, count(*) as nb FROM `bibdix_log` where time > '$jour_base' group by jour, app_code, action";
    $res = SQL($sql);
  }
  
  while ($row = mysql_fetch_assoc($res))
  {
    $app_code = $row["app_code"];
    $jour = $row["jour"];
    $action = $row["action"];
    $nb = $row["nb"];
    // On supprimer de la table stats, que ça y soit ou pas, tant pis
    SQL("delete from bibdix_stats where app_code = '".$app_code."' and jour = '".$jour."' and action = '".$action."'");
    SQL("insert into bibdix_stats (`app_code`, `jour`, `action`, `nb`) values ('$app_code', '$jour', '$action', $nb)");
  }
  
  print "Mise a jour OK";
?>