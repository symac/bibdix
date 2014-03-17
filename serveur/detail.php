<!DOCTYPE html>
<?php
  include("../include/db_config.php");
  include("../include/utils.php");
  $id = $_GET["id"];
  $res = SQL("select * from bibdix_versions where id = $id");
  $row = mysql_fetch_assoc($res);
  $code = $row["code"];
  $app_version = $row["version"];
?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>BibdiX - Dissémination des bibliothèques universitaires françaises</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Jour', 'Installations', 'Mises à jour'],
          <?php
            $minimum = date('Y-m-d', strtotime('-15 days'));
            $res = SQL("select * from bibdix_stats where app_code = '$code' and jour > '$minimum' order by jour");
            
            $stats = array();
            while ($row = mysql_fetch_assoc($res))
            {
              $jour = substr($row["jour"], 5, 5);
              $stats[$jour][$row["action"]] = $row["nb"];
            }
            
            foreach ($stats as $jour => $substats)
            {
              if (!isset($stats[$jour]["install"]))
              {
                $stats[$jour]["install"] = 0;
              }
              if (!isset($stats[$jour]["update"]))
              {
                $stats[$jour]["update"] = 0;
              }
            }

            asort($stats);
            foreach ($stats as $jour => $substats)
            {
              print "['$jour', ".$substats["install"].", ".$substats["update"]."],\n";
            }
          ?>
        ]);

        var options = {
          title: 'Utilisateurs',
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
    
    
    <!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      
      .extension
      {
        border:1px solid #CCC;
        padding:0px 10px;
      }
      
      .extension h2
      {
        font-size:1.5em;
      }
      
      #logos
      {
        float:right;
      }
      
      footer
      {
        margin:auto;
        text-align:center;
      }
      
      footer div
      {
        border-top:1px dashed #BBB;
      }
      
      footer a
      {
        color:#BBB;
      }
      
      .img_ext
      {
        padding-right:20px;
      }
    </style>
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand" href="index.php">BibdiX</a>
        </div>
      </div>
    </div>

    <div class="container">
      <!-- Example row of columns -->
      <div class="row ">
        <div class="span6 offset3">
          <table class='table'>
            <?php
             
              afficheLigne("nom", "Nom extension");
              afficheLigne("etab", "Établissement");
              
              print "<tr><th>Installer</th>";
              print "<td>";
              print "<ul><li><a href='".build_dl_link("chrome", $app_version, $code)."'>Sous Chrome</a></li>";
              print "<li><a href='".build_dl_link("firefox", $app_version, $code)."'>Sous Firefox</a></li></ul>";
              print "</td></tr>";
              function build_dl_link($browser, $version, $app_code)
              {
                return "download.php?browser=$browser&app_code=$app_code&version=$version";
              }
              print "<tr><th>Icônes</th>";
              print "<td>";
              print "<img class='img_ext' src='versions/".$code."/img/icon.png'/>";
              print "<img class='img_ext' src='versions/".$code."/img/present.png'/>";
              print "<img class='img_ext' src='versions/".$code."/img/absent.png'/>";
              print "<img class='img_ext' src='versions/".$code."/img/inconnu.png'/>";
              print "</td>";
              print "</tr>";
            ?>
              <tr>
                <td colspan='2'>
                  <span style='font-weight:bold'>Statistiques</span>
                  <div id="chart_div"/>
                </td>
              </tr>
            <?php
              afficheLigne("rcr", "Bibliothèques");
              
              function afficheLigne($code, $lib)
              {
                $id = $_GET["id"];
                $res = SQL("select * from bibdix_versions where id = $id");
                $row = mysql_fetch_assoc($res);
                
                print "<tr>";
                print "<th>$lib</th>";
                
                if ($code == "rcr")
                {
                  $tab_rcr = split(",", $row[$code]);
                  print "<td><ul>";
                  foreach ($tab_rcr as $rcr)
                  {
                    $res2 = SQL("select * from bibdix_rcr where rcr='$rcr'");
                    
                    if (mysql_num_rows($res2) != 1)
                    {
                      print "<li>$rcr</li>";
                    }
                    else
                    {
                      $row2 = mysql_fetch_assoc($res2);
                      print "<li>".$row2["lib"]."</li>";
                    }
                  }
                  print "</ul></td>";
                }
                else
                {
                  print "<td>".$row[$code]."</td>";  
                }
                print "</tr>";
              }
            ?>
          </table>
          
        </div>
        <div class="span12 pagination-centered">
          <a href='index.php'>Retour</a>
        </div>
      </div>

      <hr/>
      <footer>
        <div><a href='http://www.sylvainmachefert.com/'>Sylvain Machefert</a> - <a href='http://twitter.com/symac'>@symac</a></div>
      </footer>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery-1.9.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script type='text/javascript'>
    $(document).ready(function() {
      // Handler for .ready() called.
        $('#ppaux_libraires').tooltip();
    });
    </script>
  </body>
</html>
