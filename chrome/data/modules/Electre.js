var Module_Electre = {
  process: function() {
    var path = window.location.pathname;

    if ( path.indexOf("ShowNotice.aspx") != -1) {
      this._processProduct();
    }
    else if (path.indexOf("Search.aspx") != -1 ) {
      // this._processProductList();
    }
    
    // On va ajouter un event pour qu'au changement de page
    // via javascript on relance
    window.addEventListener('DOMAttrModified', function (event) {
      console.log("Changement de DOM : " + event.target.id);
      if (event.target.id == 'ctl00_mainBody_NextNav') {
        Module_Electre._processProduct();
      }
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
    $("#searchResults").find("tr.x-grid3-hd-row > td:eq(2)").after("<td>Dispo Babord</td>");
  }
};