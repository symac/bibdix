// config.js - Bibeverywhere's module
// author: Sylvain Machefert (Bordeaux 3)

var exports = new Object();
exports.IMG = {};
exports.LIB = {};
exports.URL_REBOND = {};

/***************************/
/* RCR à prendre en compte */
/***************************/
// Lister l'ensemble des RCR concernés en les séparant par une virgule.
exports.RCR = "%%APP_RCR%%";
exports.DASHES = %%APP_DASHES%%;

/********************************/
/* Rebonds vers catalogue local */
/********************************/
// Notice absente de nos bibliothèques ou bug, on renvoie vers la page d'accueil des bibliothèques
exports.URL_REBOND.res0 = "%%APP_URL_0%%";
// Notice présente dans nos bibliothèques, variable {{ISBN}} à l'endroit adéquat
exports.URL_REBOND.res1 = "%%APP_URL_1%%";

/********************************/
/* Affichage des disponibilités */
/********************************/
exports.IMG.absent = chrome.extension.getURL("data/img/absent.png");
exports.IMG.present = chrome.extension.getURL("data/img/present.png");
exports.IMG.inconnu = chrome.extension.getURL("data/img/inconnu.png");
exports.IMG.height = %%APP_ICONS_HEIGHT%%;
exports.IMG.width = %%APP_ICONS_WIDTH%%;

/************************************/
/* Affichage dans la barre d'outils */
/************************************/
// Icônes utilisées dans la barre d'outils pour indiquer le statut de l'extension : active ou inactive
exports.IMG.actif = chrome.extension.getURL("data/img/present.png");
exports.IMG.inactif = chrome.extension.getURL("data/img/gris.png");

// Libellés affichés au moment du passage sur l'icône en bas de l'écran
exports.LIB.extension_toolbar = "%%APP_NAME%%";
