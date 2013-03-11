// This is an active module of the symac Add-on
const PageMod   = require('page-mod').PageMod;
const tabs      = require('tabs');
const self      = require('self');
const storage   = require('simple-storage').storage;
const widgets   = require('widget');
const req_serveur = require("req_serveur");
var contextMenu = require("context-menu");
var config      = require("config");

const QUERY_MODE_MULTIPLE = true;

var bouton_actif = "";

if (!storage.bibdix_active)
{
  storage.bibdix_active = "actif";
  bouton_actif = true;
}
else if (storage.bibdix_active == "actif")
{
  bouton_actif = true;
}
else if (storage.bibdix_active == "inactif")
{
  bouton_actif = false;
}

function toggleActivation() {
  bouton_actif = !bouton_actif;
  bouton_actif ? storage.bibdix_active = "actif" : storage.bibdix_active = "inactif"; 
  return bouton_actif;
}

exports.main = function() {
    // Avant d'activer le modificateur de page, on va vérifier que l'outil est bien actif
    var bibdixMod;
    // tabs.open("http://www.mollat.com/livres/le-sport-guerre-xixe-xxe-siecles-9782753521261.html");
    if (!bouton_actif)
    {
      return;
    }
    else
    {
      bibdixMod = createPageMod();
    }

    var widget = widgets.Widget({
      id: 'toggle-switch',
      label: config.LIB.extension_toolbar,
      contentURL: config.IMG.actif,
      contentScriptWhen: 'ready',
      contentScriptFile: self.data.url('widget.js')
    });
    
    widget.port.on('left-click', function() {
      if (toggleActivation())
      {
        widget.contentURL = config.IMG.actif;
        // On active, on doit donc créer un nouveau pagemod
        bibdixMod = createPageMod();
      }
      else
      {
        widget.contentURL = config.IMG.inactif;
        // On désactive, on détruit donc le pagemod devenu inutile
        bibdixMod.destroy();
      }
    });
};

function createPageMod()
{
  return new PageMod({
        include: ['*.fnac.com', '*.amazon.fr', '*.appeldulivre.fr', '*.decitre.fr', '*.electre.com', '*.mollat.com'],
        contentScriptFile:
          [
            self.data.url('jquery-1.7.1.min.js'),
            self.data.url('Utils.js'),
            self.data.url('modules/Amazon.js'),
            self.data.url('modules/AppelDuLivre.js'),
            self.data.url('modules/Decitre.js'),
            self.data.url('modules/Electre.js'),
            self.data.url('modules/Fnac.js'),
            self.data.url('modules/Mollat.js'),
            self.data.url('dispatcher.js')
          ],
        contentScriptWhen: 'ready',
        onAttach: function onAttach(worker) {
          worker.on('message', function(msg) {
            local_dispatch(msg, worker);
          });
        }
    });  
}

function local_dispatch(msg, worker)
{
  var answer = "";
  
  if (msg.handler == "getdispo")
  {
    answer = req_serveur.getdispo(msg, worker, config);
  }
  else if (msg.handler == "nb_res")
  {
    answer = req_serveur.nb_res(msg, worker, tableau_images);
  }
  else
  {
    console.log("RIEN");
  }
  return answer;
}