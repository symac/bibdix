<?php
  /*
  Fichier  : getdispo.php
  Auteur    : Sylvain Machefert (Bordeaux 3)
  Fonction  : Indique si un document est disponible dans un ensemble de bibliothèques, en fonction d'une liste de RCR et d'un (ou plusieurs) ISBN
  Paramètre :
    - q : isbn
    - rcr : la liste des RCR, séparés par le signe #

  Retour :
    - status : 1 pour une opération qui s'est déroulée correctement, 0 pour une opération avec erreur
    - error : message d'erreur si nécessaire
    - data["dispo"] : 0 pour un document absent; 1 pour un document présent
  
  Historique :
    - 20121024 : Mise à jour du script pour le sudoc, généralisation  
    - 20110914 : version initiale du script (pour babordplus)

  */
  
//  require_once("include/simple_html_dom.php");
  require_once("include/ISBN.php");
  require_once("include/connect.php");
ini_set("display_errors", 1);

  // On prépare le tableau de sortie
  $sortie = Array();
  $sortie["function"] = "getdispo";
  $sortie["status"] = 0; // Par défaut on considère qu'il y a eu un problème, on mettra à jour dans les endroits où ça passe bien
  $sortie["error"] = "";
  $sortie["data"] = Array();
  // Par défaut on considère que l'ouvrage n'est pas disponible
  $sortie["data"]["dispo"] = 0;
  
  // On doit regarder ce qu'on a en entrée pour finir par avoir un isbn. On peut avoir des identifiants spécifiques à certains sites
  // dans le cas où l'on n'a pas l'isbn dès la liste de résultats.
  $tokens = explode(":", $q);
  if ($tokens[0] == "isbn") {
    $isbn = $tokens[1];
  } else {
    $isbn = translate_site_id($tokens[0], $tokens[1]);
  }
  
  $sortie["data"]["req"] = $q;
  
  if ($isbn == -1)
  {
    // On n'a pas réussi à traduire le site id, on va donc l'indiquer en sortie
    $sortie["error"] = "Traduction du code d'entrée impossible (".$q.")";
    $isbn = "";
  }

  if ($isbn)
  {
    
    $sortie["data"]["isbn"] = $isbn;
    
    $isbn10 = "";
    $isbn13 = "";
    
    $isbn = str_replace("-", "", $isbn);

    if (strlen($isbn) == 10)
    {
      $isbn10 = $isbn;
      $isbn13 = convertToISBN13($isbn);
    }
    elseif (strlen($isbn) == 13)
    {
      $isbn10 = convertToISBN10($isbn);
      $isbn13 = $isbn;
    }
    else
    {
      // On a un problème avec l'isbn passé en entrée !
      $sortie["error"]  = "Problème d'isbn (".$isbn." / taille : ".strlen($isbn).")";
      $sortie["status"] = 0;
    }

    $rcr = $_GET["rcr"];
    $tab_rcr = Array();
    foreach (preg_split("/,/", $rcr) as $un_rcr)
    {
      $tab_rcr[$un_rcr] = 1;
    }

    // 1ère étape : on va aller interroger isbn2ppn pour voir s'il y a des PPN intéressants
    $url = "http://www.sudoc.fr/services/isbn2ppn/$isbn10,$isbn13";

    $xml = @simplexml_load_file($url);
    if ($xml === false)
    {
      // isbn2ppn ne nous renvoie rien, on est face à une erreur 404, inutile d'aller plus loin.
      // On met le statut à 1, il n'y a pas eu de bug à proprement parler, simplement un ouvrage absent du sudoc
      $sortie["status"] = 1;
    }
    else
    {
        $tab_ppn = Array();
        foreach ($xml->query->result as $un_result)
        {
          foreach ($un_result->ppn as $ppn)
          {
            $tab_ppn[(string) $ppn] = 1;
          }
        }

        // 2ème étape, on va faire un multiwhere sur l'ensemble des PPN ainsi récoltés
        $url = "http://www.sudoc.fr/services/multiwhere/";
        foreach ($tab_ppn as $ppn => $dummy)
        {
          $url .= $ppn.",";
        }
        $url = substr_replace($url, "", -1);
        $xml = simplexml_load_file($url);
        // Par défaut on considère l'ouvrage comme non disponible
        $sortie["data"]["dispo"] = 0;
        foreach ($xml->query as $req)
        {
          foreach ($req->result->library as $une_bib)
          {
            $un_rcr = $une_bib->rcr;
            if (isset($tab_rcr[(string)$un_rcr]))
            {
              $sortie["data"]["dispo"] = 1;
            }
          }
        }
        $sortie["status"] = 1;
      }
  }
  elseif ($sortie["error"] == "")
  {
    $sortie["status"] = 0;
    $sortie["error"]  = "Manque un argument en paramètre de getdispo.php";
  }

  print json_encode($sortie);
  exit;
  
  function translate_site_id($id_site, $id_doc) {
    global $config;
    $req = "select * from ".$config["TAB_EQUIV"]." where id_site = '".$id_site."' and id_doc = '".$id_doc."';";

    $res = SQL($req);
    if (mysql_numrows($res) == 0)
    {
      // On doit lancer la recherche sur le site pour obtenir l'isbn
      if ($id_site == "fnac") {
        $url = "http://livre.fnac.com/a${id_doc}/";
        $pattern = '<th scope="row" align="left"><span>ISBN</span></th>\\s+'
                 . '<td><span>\\s+'
//                 . '([\\dX]+)\\s+'
                 . '([\\dX]+)\\s*' // Correc SMA
//                 . '</span></td>';
                 . '</tr>';
        // Mise à jour 20121024 : changement format sur site fnac.com, on récupère maintenant dans les meta, plus simple
        $pattern = '<meta property="og:isbn" content="([\\dX]+)"';
      } else if ($id_site == "chapitre") {
        $url = "http://www.chapitre.com/CHAPITRE/fr/BOOK//,${id_doc}.aspx";
        $pattern = '<div class="productDetails-items-content">\\s+'
                  . '(\\d{13})\\s+'
                  . '</div>';
      }
        
      $http_content = file_get_contents($url);
      
      if (preg_match("|404|", $http_response_header[0]))
      {
        return -1;
      }
      
      if (!preg_match("|${pattern}|m", $http_content, $matches)) { // XXX: set ISBN to NULL if not available for this site ID ??
        return -1;
      }
      else
      {
        $isbn = $matches[1];
      }
      SQL("insert into ".$config["TAB_EQUIV"]." (`id_site`, `id_doc`, `isbn`) values ('$id_site', '$id_doc', '$isbn');");
    }
    else
    {
      // On va récupérer l'isbn stocké dans la base.
      $row = mysql_fetch_assoc($res);
      $isbn = $row["isbn"];
    }

    return $isbn;
  }
?>
