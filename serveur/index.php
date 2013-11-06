<?php
  include("common/header.php");
  include("../include/db_config.php");
  include("../include/utils.php");
?>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Jour', 'Installations', 'Mises à jour'],
          <?php
            $minimum = date('Y-m-d', strtotime('-15 days'));
            $res = SQL("select * from bibdix_stats where jour > '$minimum' order by jour");
            
            $stats = array();
            while ($row = mysql_fetch_assoc($res))
            {
              $jour = substr($row["jour"], 5, 5);
              // $jour = substr($jour, 3, 2)."/".substr($jour, 0, 2);
              $stats[$jour][$row["action"]] += $row["nb"];
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

            ksort($stats);
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
      <div class="row" style="margin-top:0px">
        <div class="span12">
          <h1>Captures d'écran</h1>
        </div>
      </div>
      
      <div class="row">
          <div class='span4'><a title="Exemple sur le site de l'Appel du Livre" class="fancybox-buttons" data-fancybox-group="button" href="img/capture_adl.png"><img src="img/capture_adl_vig.png" alt="" /></a></div>
          <div class='span4'><a title="Exemple sur le site de Decitre avec la version Rennes 2 de BibdiX" class="fancybox-buttons" data-fancybox-group="button" href="img/capture_decitre_r2.png"><img src="img/capture_decitre_r2_vig.png" alt="" /></a></div>
          <div class='span4'><a  title="Exemple sur le site d'Electre avec la version Bordeaux 3 de BibdiX" class="fancybox-buttons" data-fancybox-group="button" href="img/capture_electre_bx3.png"><img src="img/capture_electre_bx3_vig.png" alt="" /></a></div>
        </div>
    
    
      <!-- Example row of columns -->
      <div class="row ">
        <div class="span12">
          <h1>Installation</h1>
          <table class='table'>
            <tr>
              <th>Établissement</th>
              <th class="class_center">Chrome<br/><a href="#myModal" style='font-weight:normal; color:red' role="button" data-toggle="modal">[note sur l'installation]</a></th>
              <th class="class_center">Firefox<br/>(13.0 et +)</th>
              <th class="class_center">Utilisateurs</th>
            </tr>
            <?php
              $res = SQL("select * from bibdix_versions where actif = 1 order by etab");
              while ($row = mysql_fetch_assoc($res))
              {
                print "<tr>\n";
                print "<td>[<a href='detail.php?id=".$row["id"]."'>?</a>] ".$row["etab"]."</td>\n";
                print "<td class='class_center'><a href='".build_dl_link("chrome", $row["version"], $row["code"])."'>installer</a></td>\n";
                print "<td class='class_center'><a href='".build_dl_link("firefox", $row["version"] , $row["code"])."'>installer</a></td>\n";

                // On va regarder le maximum sur la dernière semaine
                $minimum = date('Y-m-d', strtotime('-4 days'));
                $resB = SQL("select max(nb) as max from bibdix_stats where app_code = '".$row["code"]."' and action = 'update' and jour > '".$minimum."'");
                $rowB = mysql_fetch_assoc($resB);
                $max = $rowB["max"];
                if ($max == "")
                {
                  $max = 0;
                }
                print "<td class='class_center'>~ ".$max."</td>";
                print "<tr>\n";
              }
              
              function build_dl_link($browser, $version, $app_code)
              {
                return "download.php?browser=$browser&app_code=$app_code&version=$version";
              }
            ?>
          </table>
          <h1>Statistiques</h1>
          <div id="chart_div" style='height:300px'></div>
          <h1>Historique</h1>
          <dl>
            <dt>0.1.3 (2013-04-17)</dt>
            <dd>
              <ul>
                <li>Correction d'un bug quand une notice n'est possédée que par un établissement</li>
              </ul>
            </dd>
            <dt>0.1.2 (2013-03-19)</dt>
            <dd>
              <ul>
                <li>Ajout des tirets via webservice XISBN. Permet de rebondir vers les catalogues qui gèrent mal les tirets dans les ISBN (Poitiers, Reims)</li>
                <li>Correction bugs divers</li>
              </ul>
            </dd>

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
            <li>Gérer les sites web qui ne fournissent pas l'ISBN en direct mais un identifiant interne (Fnac)</li>
            <li>Permettre l'installation de plusieurs versions de l'extension en parallèle</li>
            <li>Autoriser la récupération des dispos en direct depuis un OPAC plutôt que via web services ABES pour permettre une meilleure réactivité (en particulier pour voir dans les disponibles les ouvrages en commande par exemple)</li>
          </ul>
        </div>
      </div>
      <hr/>
      <footer>
        <div><a href='http://www.sylvainmachefert.com/'>Sylvain Machefert</a> - <a href='http://twitter.com/symac'>@symac</a> - <a href='http://appicns.com/'>icones app.icns</a></a></div>
      </footer>

    </div> <!-- /container -->

    <!-- Modal -->
    <div id="myModal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Installation Chrome</h3>
    </div>
    <div class="modal-body">
    <p>Pour des raisons de sécurité, Chrome n'autorise plus l'installation simplifiée d'extensions depuis d'autres sites que le <span style="font-style:italic">Chrome Web Store</span>.</p>
    <p>Pour installer BibdiX sur Chrome, la procédure à suivre est donc la suivante :
    <ol>
      <li>Téléchargez le fichier de l'extension depuis le site Web et enregistrez-le sur votre ordinateur.</li>
      <li>Cliquez sur l'icône représentant une clé à molette dans la barre d'outils du navigateur.</li>
      <li>Sélectionnez Outils > Extensions.</li>
      <li>Localisez le fichier de l'extension sur votre ordinateur et faites-le glisser vers la page "Extensions".</li>
      <li>Examinez la liste des autorisations dans la boîte de dialogue qui s'affiche. Si vous souhaitez continuer, cliquez sur Installer.</li>
    </ol><small>(<a href='https://support.google.com/chrome_webstore/answer/2664769?p=crx_warning&rd=1'>source sur le site de google</a>)</small></p>
    </div>
    <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">OK</button>
    </div>
    </div>
    
    
<?php
  include("common/footer.php");
?>
