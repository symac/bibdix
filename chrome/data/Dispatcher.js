var MODULES =
{
  "amazon.fr":        Module_Amazon,
  "appeldulivre.fr":  Module_Appeldulivre,
  "decitre.fr":       Module_Decitre,  
  "electre.com":      Module_Electre,
  "mollat.com":       Module_Mollat,
  "google.fr":        Module_GoogleBooks
};

var host = window.location.host;
for ( var domain in MODULES )
{
  if ( RegExp(domain + "$").test(host) )
  {
    console.log("OK pour " + domain);
    var module = MODULES[domain];
    module.process();
  }
}