/*
  Fichier   : Decitre.js
  Auteur    : Sylvain Machefert (Bordeaux 3)
  Fonction  : Analyse des pages de Decitre.
  
  Historique :
    - 20130222 : modifications j. sicot (Rennes 2)
    - 20121024 : adaptation à nouvelle version du site
    - 20111005 : version initiale du script
*/

var Module_Decitre = {
  process: function() {
    var path = window.location.pathname;
    if ( path.indexOf("/livres/") != -1) {
      this._processProduct();
    }
    else if ( path.indexOf("/rechercher/") != -1)
    {
      this._processProductlist();
    }
  },
  
  _processProductlist: function() {
    $("div.product_detail").each(function(i)
    {
      var isbn = $("div.google-apercu", this).attr("id");
      isbn = isbn.replace("ISBN:", "");
      //isbn = isbn.replace(/^\s*/, '').replace(/\s*$/, '');
      
      if (!isbn)
      {
        return true;
      }
      // var insertionPoint = $("#ctl00_mainBody_TitreLabel").get(0);
      var insertionPoint = $("div.resume", this).get(0);
      insertionPoint && checkBookAvailability("isbn:" + isbn, insertionPoint);
    });
  },
  
  _processProduct: function() {
    // Version non active au 24/10/2012 :
    // var isbn = $("div#nomean").text();
    var isbn = $(".technic ul li:contains('ISBN') em").text().trim();
    if (!isbn)
    {
        // TODO : logguer les erreurs pour contrôler un peu mieux
        return true;
    }
    // isbn = isbn.replace(/\-/g, "");
    isbn = isbn.replace(":", "");
    isbn = isbn.replace(/^\s*/, '').replace(/\s*$/, '');
    console.log("ISBN : #" + isbn + "#");
    var insertionPoint = $("div.rate").get(0);
    insertionPoint && checkBookAvailability("isbn:" + isbn, insertionPoint);    
  }
};