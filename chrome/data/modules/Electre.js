var Module_Electre = {
  process: function() {
    var path = window.location.pathname;

    if ( path.indexOf("ShowNotice.aspx") != -1) {
      this._processProduct();
    }
    else if (path.indexOf("Search.aspx") != -1 ) {
      this._processProductList();
      console.log("EVENT LISTENER")
      var t = document.getElementById("rechercheList");
      t.addEventListener('DOMAttrModified', function (event) {
        console.log("Changement de DOM : " + event.target.id);
        if (event.target.id == "ext-gen9")
        {
          if ( (document.getElementById("ext-gen14").innerHTML.length > 200) && (document.getElementById(event.target.id).innerHTML.length > 200))
          {
            Module_Electre._processProductList();
            console.log("Mise à jour table");
          }
        }
      });
    }
    
    // On va ajouter un event pour qu'au changement de page
    // via javascript on relance
    window.addEventListener('DOMAttrModified', function (event) {
      console.log("Changement de DOME : " + event.target.id);
      /* if (event.target.id == 'ctl00_mainBody_NextNav') {
         Module_Electre._processProduct();
      } */
    }, false);
  },

  _processProduct: function() {
    var isbn = $("td[class='txtStyle-1']:contains('ISBN')").next().text();
    isbn = isbn.replace(" Voir OPAC", ""); // Pour ceux qui auraient activé le rebond vers l'OPAC dans ELECTRE
    
    if (!isbn)
    {
      return true;
    }
    
    // var insertionPoint = $("#ctl00_mainBody_TitreLabel").get(0);
    var insertionPoint = $(".n-col-1").get(0);
    insertionPoint && checkBookAvailability("isbn:" + isbn, insertionPoint);
  },
  
  _processProductList: function() {
    // On va ajouter la colonne qui nous intéresse
    $("#searchResults").find("div.x-grid3-scroller").find("table.x-grid3-row-table").each(function()
      {
        console.log("XXXXXXXXXXXXXXXXx Affichage d'une ligne");
        var isbn_zone = $(this).find("div.x-grid3-cell-inner.x-grid3-col-10");
        var isbn = isbn_zone.html()
        if (isbn != "")
        {
          var insertionPoint = $(isbn_zone).get(0);
          insertionPoint && checkBookAvailability("isbn:" + isbn, insertionPoint);
        }
      }
    );
    // find("tr.x-grid3-hd-row").append("<td class='x-grid3-hd x-grid3-cell'><a style='height: 19px;' id='ext-bibdix' class='x-grid3-hd-btn' href='#'></a>BibidX</td>");
  }
};