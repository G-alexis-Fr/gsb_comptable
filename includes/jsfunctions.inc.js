/**
 * Fonction Javascript Paiement des Frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Alexis Goutorbe <contact@g-alexis.com>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */

/**
 * Fonction qui permet de cocher une ou toutes les cases
 *
 * @return null
 */
function selectAll() {
  var chb = document
    .getElementById("formPaiement")
    .getElementsByTagName("input");
  if (chb.length > 1) {
    if (document.getElementById("all").checked === true) {
      for (var i = 0; i < chb.length; i++) {
        if (chb[i].name.substr(0, 6) === "aPayer") {
          chb[i].checked = true;
        }
      }
    } else {
      for (var i = 0; i < chb.length; i++) {
        if (chb[i].name.substr(0, 6) === "aPayer") {
          chb[i].checked = false;
        }
      }
    }
  } else {
    return;
  }
}
