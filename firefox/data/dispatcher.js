(function() {
  self.on('message', function (message)
  {
    // On redispatche au niveau du content script selon la fonction
    if (message.function == "getdispo")
    {
      showBookAvailability_onFinish(message);
    }
    else if (message.function == "getdispo_all")
    {
      showBookAvailability_onFinish_all(message);
    }
    else if (message.function == "nb_res")
    {
      getNbRes_finish(message);
    }
    
  });
  
  var MODULES =
  {
    "amazon.fr":        Module_Amazon,
    "appeldulivre.fr":  Module_Appeldulivre,
    "decitre.fr":       Module_Decitre,  
    "electre.com":      Module_Electre,
    "mollat.com":       Module_Mollat
  };

  var host = window.location.hostname;
  for ( var domain in MODULES )
  {
    if ( RegExp(domain + "$").test(host) )
    {
      console.log("Domaine OK : " + domain);
      var module = MODULES[domain];
      module.process();
    }
  }
})();