// req_serveur.js - Bibeverywhere's module
// author: symac

var Request = require('request').Request;

var BASE_URL = "http://www.geobib.fr/babordplus/";

function getdispo(msg, myworker, config)
{
  var aQuery = msg["data"]["q"];  
  var jsonResult = new Object();
  jsonResult.data = new Object();
  jsonResult.function = "getdispo";
  jsonResult.data.dispo = 2;
  jsonResult.data.req = aQuery;
  jsonResult.status = 0;
  jsonResult.config = config;
  
  // Variables globales
  var isbn = "";
  var equiv_PPN_ISBN = new Object();
  
  if (aQuery.indexOf("isbn:") != 0)
  {
    // On doit aller récupérer l'ISBN, en passant par le serveur bibdix
    // TODO : utiliser la fonction translateSiteId
  }
  else
  {
    isbn = aQuery.replace("isbn:", "");
    jsonResult.data.isbn = isbn;
    
    var url = "http://www.sudoc.fr/services/isbn2ppn/" + isbn;
    var isbn2 = getOtherISBN(isbn);
    if (isbn2)
    {
      url += "," + isbn2;
    }
    url += "&format=text/json";
    
    Request({
    url: url,
    onComplete: function (response) {
        if (response.json)
        {
          var data_out = response.json;
          if (data_out.sudoc.error)
          {
            // Notice inconnue au niveau du sudoc
            jsonResult.status = 1;
            jsonResult.data.dispo = 0;
            myworker.postMessage(jsonResult);
            return;
          }
          else
          {
            var ppn_list = "";
            
            if (data_out.sudoc.length > 0)
            {
              for (var i=0; i < data_out.sudoc.length; i++)
              {
                var isbn_courant = data_out.sudoc[i].query.isbn;
                if (data_out.sudoc[i].query.result.length > 0)
                {
                  for (var j=0; j < data_out.sudoc[i].query.result.length; j++)
                  {
                    var ppn = data_out.sudoc[i].query.result[j].ppn;
                    ppn_list = ppn_list + ppn + ",";
                    equiv_PPN_ISBN[ppn] = isbn_courant;
                  }
                }
                else
                {
                  var ppn = data_out.sudoc[i].query.result.ppn;
                  ppn_list = ppn_list + ppn + ",";
                  equiv_PPN_ISBN[ppn] = isbn_courant;
                }
              }
            }
            else
            {
              var isbn_courant = data_out.sudoc.query.isbn;
              if (data_out.sudoc.query.result.length > 0)
              {
                for (var j=0; j < data_out.sudoc.query.result.length; j++)
                {
                  var ppn = data_out.sudoc.query.result[j].ppn;
                  ppn_list = ppn_list + ppn + ",";
                  equiv_PPN_ISBN[ppn] = isbn_courant;
                }
              }
              else
              {
                var ppn = data_out.sudoc.query.result.ppn;
                ppn_list = ppn_list + ppn + ",";
                equiv_PPN_ISBN[ppn] = isbn_courant;
              }
            }
            ppn_list = ppn_list.replace(/,$/, "");

            // On va faire la seconde étape
            
            var url2 = "http://www.sudoc.fr/services/multiwhere/" + ppn_list + "&format=text/json";
            Request({
              url: url2,
              onComplete: function (response) {
                // TODO : gérer la q? des erreurs 404
                var data_out = response.json;
                var present = false;
                var ppn_present = "";
                var ppn_courant = "";
                
                if (data_out.sudoc.query.length > 0)
                {
                  for (var i=0; i < data_out.sudoc.query.length; i++)
                  {
                    ppn_courant = data_out.sudoc.query[i].ppn;
                    
                    if (data_out.sudoc.query[i].result.library.length > 0)
                    {
                      for (var j=0; j < data_out.sudoc.query[i].result.library.length; j++)
                      {
                        var rcr = data_out.sudoc.query[i].result.library[j].rcr;
                        if (config.RCR.match(rcr))
                        {
                          present = true;
                          ppn_present = ppn_courant;
                        }
                      }
                    }
                    else
                    {
                      var rcr = data_out.sudoc.query[i].result.library.rcr;
                      if (config.RCR.match(rcr))
                      {
                        present = true;
                        ppn_present = ppn_courant;
                      }
                    }
                  }
                }
                else
                {
                  ppn_courant = data_out.sudoc.query.ppn;
                  if (data_out.sudoc.query.result.library.length > 0)
                  {
                    for (var j=0; j < data_out.sudoc.query.result.library.length; j++)
                    {
                      var rcr = data_out.sudoc.query.result.library[j].rcr;
                      if (config.RCR.match(rcr))
                      {
                        present = true;
                        ppn_present = ppn_courant;
                      }
                    }
                  }
                  else
                  {
                    var rcr = data_out.sudoc.query.result.library.rcr;
                    if (config.RCR.match(rcr))
                    {
                      present = true;
                    }
                  }
                }

                jsonResult.status = 1;
                if (present)
                {
                  jsonResult.data.dispo = 1;
                  jsonResult.data.isbn = equiv_PPN_ISBN[ppn_present];
                }
                else
                {
                  jsonResult.data.dispo = 0;
                }
                myworker.postMessage(jsonResult);
                return;
              }
            }).get();
            
          }
        }
        else
        {
          console.log("ERREUR 124");
        }
    }
  }).get();
  }
}

function getdispo___BPLUS___(msg, myworker, tableau_images, RCR)
{
  var aQuery = msg["data"]["q"];
  Request({
    url: BASE_URL + "getdispo.php?type=" + msg["handler"] + "&q=" + aQuery,
    onComplete: function (response) {
        var jsonResult = new Array();
        if (response.json)
        {
            jsonResult = response.json;
        }
        else
        {
            jsonResult.data.dispo = 2
        }
        jsonResult.img = tableau_images;
        myworker.postMessage(jsonResult);
    }
  }).get();
}

function nb_res(msg, myworker, tableau_images)
{
  var aQuery = msg["data"]["q"];
  
  Request({
    url: BASE_URL + "nb_res.php?q=" + aQuery,
    onComplete: function (response) {
      response.json.img = tableau_images;
      myworker.postMessage(response.json);
    }
  }).get();
}

exports.nb_res = nb_res;
exports.getdispo = getdispo;
exports.getOtherISBN = getOtherISBN;


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