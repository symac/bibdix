// config.js - Bibeverywhere's module
// author: Sylvain Machefert (Bordeaux 3)

var data = require("sdk/self").data;
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
// Notice absente de nos bibliothèques (ou statut inconnu)
exports.URL_REBOND.res0 = "%%APP_URL_0%%";
// Notice présente dans nos bibliothèques, variable {{ISBN}} à l'endroit adéquat
exports.URL_REBOND.res1 = "%%APP_URL_1%%";

/********************************/
/* Affichage des disponibilités */
/********************************/
exports.IMG.absent = data.url("img/absent.png");
exports.IMG.present = data.url("img/present.png");
exports.IMG.inconnu = data.url("img/inconnu.png");
exports.IMG.height = %%APP_ICONS_HEIGHT%%;
exports.IMG.width = %%APP_ICONS_WIDTH%%;

/************************************/
/* Affichage dans la barre d'outils */
/************************************/
// Icônes utilisées dans la barre d'outils pour indiquer le statut de l'extension : active ou inactive
exports.IMG.actif = data.url("img/icon_actif.png");
exports.IMG.inactif = data.url("img/icon_inactif.png");

// Libellés affichés au moment du passage sur l'icône en bas de l'écran
exports.LIB.extension_toolbar = "%%APP_NAME%%";
