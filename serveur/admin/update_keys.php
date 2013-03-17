<?php
    // On va stocker dans la base la clé CHROME
    require("../../include/db_config.php");
    require("../../include/utils.php");
    
    $app_code = getParam("app_code");
    if (!$app_code)
    {
        print "Manque le code d'appli";
        exit;
    }
    $app_chrome_id = getParam("app_chrome_id");
    if ( $app_chrome_id )
    {
        $res = SQL("select chrome_app_id from bibdix_versions where code = '$app_code'");
        if (mysql_num_rows($res) == 1)
        {
            $row = mysql_fetch_assoc($res);
            if ($row["chrome_app_id"] != "")
            {
                // On ne fait pas de mise à jour, on le signale
                print "Pas de mise a jour de chrome_app_id, deja present\n";
            }
            else
            {
                SQL("update bibdix_versions set chrome_app_id = '$app_chrome_id' where code = '$app_code'");
            }
        }
    }
?>