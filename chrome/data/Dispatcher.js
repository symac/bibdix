var MODULES =
{
  "amazon.fr":        Module_Amazon,
  "appeldulivre.fr":  Module_Appeldulivre,
  "decitre.fr":       Module_Decitre,  
  "electre.com":      Module_Electre,
  "fnac.com":         Module_Fnac,
  "mollat.com":       Module_Mollat
};

var host = window.location.hostname;
for ( var domain in MODULES )
{
  if ( RegExp(domain + "$").test(host) )
  {
    console.log("OK pour " + domain);
    var module = MODULES[domain];
    module.process();
  }
}