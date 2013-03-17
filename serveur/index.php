<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>BibdiX - Dissémination des bibliothèques universitaires françaises</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

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
        color:#999;
      }
      
      h1
      {
        font-size:2em;
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
          <a class="brand" href="#">BibdiX</a>
        </div>
      </div>
    </div>

    <div class="container">

      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
        <p id='logos'>
          <img src='img/logo_chrome.png' alt='Logo Chrome' title='Logo Chrome'/>
          <img src='img/logo_firefox.png' alt='Logo Firefox' title='Logo Firefox'/>
        </p>
        <h1>BibdiX</h1>
        <p>Extension pour Chrome & Firefox enrichissant les sites des <a title="" data-placement="top" data-toggle="tooltip" href="#" data-original-title="Amazon, Appel du livre, Decitre, Fnac, Electre, Mollat" id='ppaux_libraires'>principaux libraires</a> en ligne avec les informations de disponibilité des ouvrages dans les bibliothèques universitaires françaises.</p>
      </div>

      <!-- Example row of columns -->
      <div class="row ">
        <div class="span12">
          <h1>Installation</h1>
          <table class='table'>
            <tr>
              <th>Établissement</th>
              <th>Chrome</th>
              <th>Firefox<br/>(13.0 et +)</th>
            </tr>
            <?php
            
              include("../include/db_config.php");
              include("../include/utils.php");
              
              $res = SQL("select * from bibdix_versions where actif = 1 order by etab");
              while ($row = mysql_fetch_assoc($res))
              {
                print "<tr>";
                print "<td>[<a href='detail.php?id=".$row["id"]."'>?</a>] ".$row["etab"]."</td>";
                print "<td><a href='".build_dl_link("chrome", $row["version"], $row["code"])."'>installer</a></td>";
                print "<td><a href='".build_dl_link("firefox", $row["version"] , $row["code"])."'>installer</a></td>";
                print "<tr>";
              }
              
              function build_dl_link($browser, $version, $app_code)
              {
                return "download.php?browser=$browser&app_code=$app_code&version=$version";
              }
            ?>
          </table>
          
          <h1>Historique</h1>
          <dl>
            <dt>0.1.1 (2013-03-06)</dt>
            <dd>
              <ul>
                <li>Intégration d'une gestion ISBN-10 / ISBN-13</li>
              </ul>
            </dd>
            <dt>0.1 (2013-03-05)</dt>
            <dd>
              <ul>
                <li>Première version diffusée</li>
                <li>Modules pour Amazon, Appel du livre, Decitre, Electre et Mollat</li>
              </ul>
            </dd>
          </dl>
          
          <h1>À faire</h1>
          <ul>
            <li>Permettre un rebond vers les catalogues qui nécessitent la version "avec tirets" des isbn (via <a href='http://xisbn.worldcat.org/webservices/xid/isbn/9782821000162?method=hyphen&format=json'>WS OCLC</a> par exemple)</li>
            <li>Gérer les sites web qui ne fournissent pas l'ISBN en direct mais un identifiant interne (Fnac)</li>
            <li>Permettre l'installation de plusieurs versions de l'extension en parallèle</li>
          </ul>
        </div>
      </div>
      <hr/>
      <footer>
        <div><a href='http://www.sylvainmachefert.com/'>Sylvain Machefert</a> - <a href='http://twitter.com/symac'>@symac</a> - <a href='http://appicns.com/'>icones app.icns</a></div>
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
    <?php
      include("../google.php");
    ?>
  </body>
</html>
