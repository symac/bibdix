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
exports.RCR = "861942252,861942251,861949901,861942250,160152101,791912101,861942108,173002102,861942207,161132101,861942204,860622101,160152201,861942303,861942238,860622201,861942220,861942107,861942212,860622202,861942205,861942201,861942106,861942103,861942102,861942101,791912201";

/********************************/
/* Rebonds vers catalogue local */
/********************************/
// Notice absente de nos bibliothèques ou bug, on renvoie vers la page d'accueil des bibliothèques
exports.URL_REBOND.res0 = "http://scd.univ-poitiers.fr/masc/default.asp?INSTANCE=EXPLOITATION";
// Notice présente dans nos bibliothèques, variable {{ISBN}} à l'endroit adéquat
exports.URL_REBOND.res1 = "http://cataloguescd.univ-poitiers.fr/default.Asp?instance=EXPLOITATION&URL=/ClientBookLine/recherche/executerRechercheprogress.asp%3FbNewSearch%3Dtrue%26strTypeRecherche%3Dpr_multicritere%26cboIndexFormatANY%3Dtouslesmots%26cboOtherIndex%3DISBN%26CodeDocBaseList%3DBU%255FPOITIERS%26CodeDocBaseListAnyDefault_BU_POITIERS%3D%26CodeDocBaseListRestrictionDefault_BU_POITIERS%3D%26BackUrl%3D%252FClientBookLine%252Ftoolkit%252FP%255Frequests%252Fformulaire%252Easp%253FGRILLE%253DSIMPLE2%255F0%2526INSTANCE%253D%26txtOtherIndex%3D{{ISBN}}";

/********************************/
/* Affichage des disponibilités */
/********************************/
exports.IMG.absent = chrome.extension.getURL("data/img/absent.png");
exports.IMG.present = chrome.extension.getURL("data/img/present.png");
exports.IMG.inconnu = chrome.extension.getURL("data/img/inconnu.png");
exports.IMG.height = 32;
exports.IMG.width = 32;

/************************************/
/* Affichage dans la barre d'outils */
/************************************/
// Icônes utilisées dans la barre d'outils pour indiquer le statut de l'extension : active ou inactive
exports.IMG.actif = chrome.extension.getURL("data/img/present.png");
exports.IMG.inactif = chrome.extension.getURL("data/img/gris.png");

// Libellés affichés au moment du passage sur l'icône en bas de l'écran
exports.LIB.extension_toolbar = "Poitiers - BibdiX";
