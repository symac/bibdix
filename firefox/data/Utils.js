var dispos_tmp = {};

var BASE_URL = "http://www.geobib.fr/babordplus";

var QUERY_MODE_MULTIPLE = false;
var registry = {};

function register(aID, aFunction) {
  if (!registry[aID]){
    registry[aID] = [aFunction];  
  } else {
    registry[aID].push(aFunction);
  }
}

function extractISBN(aURL)
{
  var isbn;
  console.log("RECHERCHE ISBN : " + aURL);
  try {
    // isbn = aURL.match(/\/((\d{13}|\d{9}[\d|X]))\//)[1];
    isbn = aURL.match(/[\/\-](\d{13}|\d{9}[\d|X])[\/\.]?/)[1];
    // 20110918 : pb sur Amazon, on conserve un / en début d'isbn, supprimé ici, 
    isbn = isbn.replace("/", "");
  } catch (e) { console.log("Erreur ExtractISBN : " + e); }
  
  
  return isbn;
}

function checkBookAvailability(aQuery, aInsertionPoint) {
  var container = showBookAvailability_onStart(aInsertionPoint, aQuery);
  if (QUERY_MODE_MULTIPLE) {
    register(aQuery, function(aResponse) {
      showBookAvailability_onFinish(aQuery, aResponse, container);
    });
  } else {
    var aQueryClass = aQuery;
    aQueryClass = aQueryClass.replace(":", "");
    // On va regarder si on n'a pas déjà interrogé pour cet ouvrage
    if (dispos_tmp[aQueryClass] != undefined)
    {
      console.log("LOG LOCAL pour " + aQueryClass + "(Dispo : " + dispos_tmp[aQueryClass].data.dispo + ")");
      var msg = dispos_tmp[aQueryClass];
      showBookAvailability_onFinish(msg);
    }
    else
    {
      var msg = {};
      msg.handler = "getdispo";
      msg.data = {};
      msg.data.q = aQuery;
      self.postMessage(msg);
    }
  }
}

function getNbRes(aQuery, aInsertionPoint) {
  if ($("#bplus_google"))
  {
    $("#bplus_google").remove();
  }
  var snippet = "<div id='zoneBabord' style='padding-top:10px; padding-left:10px; border-top:1px solid #CCC'>";
  snippet += '<table width="240px" cellpadding="0" cellspacing="0" id="mbEnd" style="padding:0"><tbody><tr>';
  snippet += '<td class="std">';
  snippet += '<h2 style="font-size:11px;padding:1px 0 4px;margin:0;text-align:left">Catalogue Babord+</h2>';
  snippet += '<img src="' + BASE_URL + '/img/present64.png" alt="" height="55" style="border:1px solid #CCC; padding:5px 5px; float:left; margin-right:10px;" width="60"/>';
  snippet += '<span id="bplus_google_nb">&nbsp;</span>';
  snippet += '</td></tr></tbody></table>';
  snippet += '</div>';	

  // On va ajouter une zone babord+. Not used anymore
  // var container = document.createElement("div");
  // container.id = "bplus_google";
  // container.innerHTML = snippet;
  // aInsertionPoint.appendChild(container);
  
  
  
  $("#bplus_google").hide();
  
  var msg = {};
  msg.handler = "nb_res";
  msg.data = {};
  msg.data.q = aQuery;
  self.postMessage(msg);
}

function getNbRes_finish(message)
{
  aQuery = message.data.req;
  
  if (aQuery == global_google_q)
  {
    aNb = message.data.nb;
    $("#bplus_google_nb").html("<a target='_blank' href='" + BASE_URL + "/redirect.php?q=" + aQuery + "'>" + aNb + " résultats</a> dans Babord+");
    
    if ( (aNb != "") && (aNb != 0) )
    {
      $("#bplus_google").show();
    }
  }
}

function showBookAvailability_onStart(aInsertionPoint, aQuery) {
  var container = "";
  var aQueryClass = aQuery;
  aQueryClass = aQueryClass.replace(":", "");
  
  if ($(".bplus" + aQueryClass).length > 0)
  {
//    console.log("Container existe déjà !");
    container = $("#bplus" + aQueryClass).get(0);
  }
  else
  {
//    console.log("Création de container : " + aQueryClass + "!");
    // var snippet = '<img style="width:32px; height:32px" class="search-start" src="' + BASE_URL + '/img/searching.gif" title="Interrogation de Babord+ en cours" alt="interrogation de Babord+ en cours"/>';
    var snippet = $("<img>", 
        {
            style: "width:32px; height:32px", 
            class: "search-start",
            src: "http://geobib.fr/bibdix/img/searching.gif",
            title: "Interrogation en cours",
            alt: "Interrogation en cours"
        });
        
    var container = $("<div>",
        {
            class: "babordplus bplus" + aQueryClass
        }
    ).get(0);
    $(container).html(snippet);
    aInsertionPoint.appendChild(container);
    // $(aInsertionPoint).append("<div class='babordplus bplus" + aQueryClass + "'>" + aQueryClass + '<img style="width:32px; height:32px" class="search-start" src="' + BASE_URL + '/img/searching.gif" title="Interrogation de Babord+ en cours" alt="interrogation de Babord+ en cours"/>' + "</div>");
  }
  
  return container;
}

function showBookAvailability_onFinish(message) {
  // Ici on récupère le message final après récupération des infos
  
  if (!message.status)
  {
    console.log("Erreur de mise à jour : " + message.error);
    aQuery = message.data.req;
    var aQueryClass = aQuery;
    aQueryClass = aQueryClass.replace(":", "");
    $(".bplus" + aQueryClass).html('<a target="_blank" href="' + BASE_URL + '/redirect.php?index=1"><img style="width:' + message.config.IMG.width + 'px; height:' + message.config.IMG.height + 'px" class="search-start" src="' + 
            message.config.IMG.inconnu + '" title="Document présent dans une de vos bibliothèques, suivre le lien pour vérifier qu\'il n\'est pas emprunté" '
            + ' alt="Document présent dans une de vos bibliothèques, suivre le lien pour vérifier qu\'il n\'est pas emprunté"/></a>');
  }
  else
  {
    aQuery = message.data.req;
    
    var aQueryClass = aQuery;
    aQueryClass = aQueryClass.replace(":", "");
    
    // On stocke le message pour ne pas avoir à réinterroger le serveur si on a la même demande au cours de la session
    dispos_tmp[aQueryClass] = message;
    if (message.data.dispo == 0)
    {
        var url = message.config.URL_REBOND.res0;
        
      $(".bplus" + aQueryClass).html('<a target="_blank" href="' + url + '"><img style="width:' + message.config.IMG.width + 'px; height:' + message.config.IMG.height + 'px" class="search-start" src="' + 
            message.config.IMG.absent + '" title="Le document n\'a pas été trouvé automatiquement dans vos bibliothèques, n\'hésitez pas à vérifier à l\'aide d\'une recherche '
            + 'manuelle." alt="Le document n\'a pas été trouvé automatiquement dans vos bibliothèques, n\'hésitez pas à vérifier à l\'aide d\'une recherche manuelle"/></a>')
//      $(".bplus" + aQueryClass).html('<a target="_blank" href="' + BASE_URL + '/redirect.php?index=1"><img style="width:32px; height:32px" class="search-start" src="' + BASE_URL + '/img/absent.png" title="Le document n\'a pas été trouvé automatiquement dans Babord+, n\'hésitez pas à vérifier à l\'aide d\'une recherche manuelle." alt="Le document n\'a pas été trouvé automatiquement dans Babord+, n\'hésitez pas à vérifier à l\'aide d\'une recherche manuelle"/></a>')
    }
    else if (message.data.dispo == 1)
    {
        var url = message.config.URL_REBOND.res1;
        url = url.replace("{{ISBN}}", message.data.isbn);
        
//      $(".bplus" + aQueryClass).html('<a target="_blank" href="' + BASE_URL + '/redirect_isbn.php?q=' + message.data.req + '"><img class="search-start" src="' + BASE_URL + '/img/present.png" title="Interrogation de Babord+ en cours" alt="interrogation de Babord+ en cours"/></a>')
      $(".bplus" + aQueryClass).html('<a target="_blank" href="' + url +
            '"><img style="width:' + message.config.IMG.width + 'px; height:' + message.config.IMG.height + 'px" class="search-start" src="' + message.config.IMG.present + 
            '" title="Document présent dans vos bibliothèques, suivre le lien pour vérifier qu\'il n\'est pas emprunté" alt="Document présent dans vos bibliothèques, suivre le ' +
            'lien pour vérifier qu\'il n\'est pas emprunté"/></a>')
    }
    else if (message.data.dispo == 2)
    {
        var url = message.config.URL_REBOND.res0;
//      $(".bplus" + aQueryClass).html('<a target="_blank" href="' + BASE_URL + '/redirect_isbn.php?q=' + message.data.req + '"><img class="search-start" src="' + BASE_URL + '/img/present.png" title="Interrogation de Babord+ en cours" alt="interrogation de Babord+ en cours"/></a>')
      $(".bplus" + aQueryClass).html('<a target="_blank" href="' + url + '"><img style="width:' + message.config.IMG.width + 'px; height:' + message.config.IMG.height + 'px" class="search-start" src="' + 
      message.config.IMG.inconnu + '" title="Nous n\'avons pas réussi à déterminer si le document était présent dans vos bibliothèques, n\'hésitez pas à effectuer la ' +
      'recherche manuellement." alt="Nous n\'avons pas réussi à déterminer si le document était présent dans vos bibliothèques, n\'hésitez pas à effectuer la recherche manuellement."/></a>')
    }
    else
    {
      console.log("Mise à jour impossible : " + message.data.dispo);
    }
    

    $(".bplus" + aQueryClass + " img").css("border", "0px");
  }
}

function _isbn_from_img_fnac(src)
{
  var file = src.substr(src.lastIndexOf("/")+1,src.length);
  var isbn = file.replace(".gif", "");
  return isbn;
}

// Fonctions de gestion des ISBN dupliquées dans utils.js et req_serveur.js, on doit pouvoir factoriser, voir pour la visilibté
// Cette fonction va nous retourner l'ISBN10 quand on lui passe l'ISBN13 et inversement
function getOtherISBN($in)
{
  $in_light = $in.replace("-", "");
  $in_light = $in_light.replace(" ", "");
  
  if ($in_light.length == 10)
  {
    return ISBN10toISBN13($in_light);
  }
  else if ($in_light.length == 13)
  {
    return ISBN13toISBN10($in_light);
  }
  else
  {
    return null;
  }
}

/*
 * Converts a isbn10 number into a isbn13.
 * The isbn10 is a string of length 10 and must be a legal isbn10. No dashes.
 */
function ISBN10toISBN13(isbn10) {
     
    var sum = 38 + 3 * (parseInt(isbn10[0]) + parseInt(isbn10[2]) + parseInt(isbn10[4]) + parseInt(isbn10[6])
                + parseInt(isbn10[8])) + parseInt(isbn10[1]) + parseInt(isbn10[3]) + parseInt(isbn10[5]) + parseInt(isbn10[7]);
     
    var checkDig = (10 - (sum % 10)) % 10;
     
    return "978" + isbn10.substring(0, 9) + checkDig;
}
 
/*
 * Converts a isbn13 into an isbn10.
 * The isbn13 is a string of length 13 and must be a legal isbn13. No dashes.
 */
function ISBN13toISBN10(isbn13) {
 
    var start = isbn13.substring(3, 12);
    var sum = 0;
    var mul = 10;
    var i;
     
    for(i = 0; i < 9; i++) {
        sum = sum + (mul * parseInt(start[i]));
        mul -= 1;
    }
     
    var checkDig = 11 - (sum % 11);
    if (checkDig == 10) {
        checkDig = "X";
    } else if (checkDig == 11) {
        checkDig = "0";
    }
     
    return start + checkDig;
}
