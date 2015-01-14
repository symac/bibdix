<?php
  include("include/log.php");
  require_once("../../include/all_includes.php");
  
  
  // On va aller chercher les téléchargements
  SQL("delete from bibdix_log where user_agent like '%Googlebot%'");
  SQL("delete from bibdix_log where user_agent like 'BUbiNG%'");
  SQL("delete from bibdix_log where user_agent like '%phpcrawl%'");
  SQL("delete from bibdix_log where user_agent like '%bnf.fr_bot%'");
  SQL("delete from bibdix_log where user_agent like '%Exabot%'");
  SQL("delete from bibdix_log where user_agent like '%Robot%'");
  SQL("delete from bibdix_log where user_agent like 'Java/1.%'"); // Controle Amethys
  SQL("delete from bibdix_log where user_agent like 'Ametys%'"); // Controle Amethys
  SQL("delete from bibdix_log where user_agent like '%bot%'");
  SQL("delete from bibdix_log where user_agent like '%slurp%'");
  SQL("delete from bibdix_log where user_agent like '%Lipperhey SEO Service%'");


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
