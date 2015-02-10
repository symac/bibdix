// This is an active module of the symac Add-on
const PageMod   = require('sdk/page-mod').PageMod;
const tabs      = require('sdk/tabs');
const self      = require('sdk/self');
const storage   = require('sdk/simple-storage').storage;
const widgets   = require('sdk/widget');
const req_serveur = require("req_serveur");
var contextMenu = require("sdk/context-menu");
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
    
    // tabs.open("http://www.decitre.fr/livres/marseille-en-quelques-jours-9782816109054.html");
    if (!bouton_actif)
    {
      bouton_depart = config.IMG.inactif;
    }
    else
    {
      bouton_depart = config.IMG.actif;
      bibdixMod = createPageMod();
    }

    var widget = widgets.Widget({
      id: 'toggle-switch',
      label: config.LIB.extension_toolbar,
      contentURL: bouton_depart,
      contentScriptWhen: 'ready',
      contentScriptFile: self.data.url('widget.js')
    });
    
    widget.port.on('left-click', function() {
      if (toggleActivation())
      {
        console.log("TOGGLE A");
        widget.contentURL = config.IMG.actif;
        // On active, on doit donc créer un nouveau pagemod
        bibdixMod = createPageMod();
      }
      else
      {
        console.log("TOGGLE B");
        widget.contentURL = config.IMG.inactif;
        // On désactive, on détruit donc le pagemod devenu inutile
        bibdixMod.destroy();
      }
    });
};

function createPageMod()
{
  return new PageMod({
        include: ['*.amazon.fr', '*.appeldulivre.fr', '*.appeldulivre.com', '*.decitre.fr', '*.electre.com', '*.mollat.com'],
        contentScriptFile:
          [
            self.data.url('jquery-1.7.1.min.js'),
            self.data.url('Utils.js'),
            self.data.url('modules/Amazon.js'),
            self.data.url('modules/AppelDuLivre.js'),
            self.data.url('modules/Decitre.js'),
            self.data.url('modules/Electre.js'),
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