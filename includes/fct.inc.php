<?php

/**
 * Fonctions pour l'application GSB
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL - CNED <jgil@ac-nice.fr>
 * @author    Alexis Goutorbe <contact@g-alexis.com>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

/**
 * Teste si un quelconque comptable est connecté
 *
 * @return vrai ou faux
 */
function estConnecte()
{
    return isset($_SESSION['idComptable']);
}

/**
 * Enregistre dans une variable session les infos d'un comptable
 *
 * @param String $idComptable ID du comptable
 * @param String $nom        Nom du comptable
 * @param String $prenom     Prénom du comptable
 *
 * @return null
 */
function connecter($idComptable, $nom, $prenom)
{
    $_SESSION['idComptable'] = $idComptable;
    $_SESSION['nom'] = $nom;
    $_SESSION['prenom'] = $prenom;
}

/**
 * Détruit la session active
 *
 * @return null
 */
function deconnecter()
{
    session_destroy();
}

/**
 * Transforme une date au format anglais aaaa-mm-jj vers le format
 * français jj/mm/aaaa
 *
 * @param String $maDate au format  aaaa-mm-jj
 *
 * @return Date au format français jj/mm/aaaa
 */
function dateAnglaisVersFrancais($maDate)
{
    @list($annee, $mois, $jour) = explode('-', $maDate);
    $date = $jour . '/' . $mois . '/' . $annee;
    return $date;
}

/**
 * Retourne le mois au format aaaamm selon le jour dans le mois
 *
 * @param String $date au format  jj/mm/aaaa
 *
 * @return String Mois au format aaaamm
 */
function getMois($date)
{
    @list($jour, $mois, $annee) = explode('/', $date);
    unset($jour);
    if (strlen($mois) == 1) {
        $mois = '0' . $mois;
    }
    return $annee . $mois;
}

/* gestion des erreurs */

/**
 * Indique si une valeur est un entier positif ou nul
 *
 * @param Integer $valeur Valeur
 *
 * @return Boolean vrai ou faux
 */
function estEntierPositif($valeur)
{
    return preg_match('/[^0-9]/', $valeur) == 0;
}

/**
 * Indique si un tableau de valeurs est constitué d'entiers positifs ou nuls
 *
 * @param Array $tabEntiers Un tableau d'entier
 *
 * @return Boolean vrai ou faux
 */
function estTableauEntiers($tabEntiers)
{
    $boolReturn = true;
    foreach ($tabEntiers as $unEntier) {
        if (!estEntierPositif($unEntier)) {
            $boolReturn = false;
        }
    }
    return $boolReturn;
}

/**
 * Vérifie si une date est inférieure d'un an à la date actuelle
 *
 * @param String $dateTestee Date à tester
 *
 * @return Boolean vrai ou faux
 */
function estDateDepassee($dateTestee)
{
    $dateActuelle = date('d/m/Y');
    @list($jour, $mois, $annee) = explode('/', $dateActuelle);
    $annee--;
    $anPasse = $annee . $mois . $jour;
    @list($jourTeste, $moisTeste, $anneeTeste) = explode('/', $dateTestee);
    return ($anneeTeste . $moisTeste . $jourTeste < $anPasse);
}

/**
 * Vérifie la validité du format d'une date française jj/mm/aaaa
 *
 * @param String $date Date à tester
 *
 * @return Boolean vrai ou faux
 */
function estDateValide($date)
{
    $tabDate = explode('/', $date);
    $dateOK = true;
    if (count($tabDate) != 3) {
        $dateOK = false;
    } else {
        if (!estTableauEntiers($tabDate)) {
            $dateOK = false;
        } else {
            if (!checkdate($tabDate[1], $tabDate[0], $tabDate[2])) {
                $dateOK = false;
            }
        }
    }
    return $dateOK;
}

/**
 * Ajoute le libellé d'une erreur au tableau des erreurs
 *
 * @param String $msg Libellé de l'erreur
 *
 * @return null
 */
function Erreur($msg)
{
    if (!isset($_REQUEST['erreurs'])) {
        $_REQUEST['erreurs'] = array();
    }
    $_REQUEST['erreurs'][] = $msg;
}

/**
 * Retoune le nombre de lignes du tableau des erreurs
 *
 * @return Integer le nombre d'erreurs
 */
function nbErreurs()
{
    if (!isset($_REQUEST['erreurs'])) {
        return 0;
    } else {
        return count($_REQUEST['erreurs']);
    }
}

/**
 * Retourne le mois précédent, au format aaaamm
 *
 * @param String $mois aaaamm
 *
 * @return String $mois précédent aaaamm
 */
function getMoisPrec($mois)
{
    $numAnnee = substr($mois, 0, 4);
    $numMois = substr($mois, 4, 2);
    $numMoisPrecedent = intval($numMois) - 1;
    $numMoisPrecedent = strval($numMoisPrecedent);
    if (strlen($numMoisPrecedent) == 1) {
        $numMoisPrecedent = '0' . $numMoisPrecedent;
    }
    return $numAnnee . $numMoisPrecedent;
}

/* Fonction qui contrôle les entrées de l'utilisateur, et les "nettoie" si besoin */
function checkInput($var)
{
    $var = trim($var);
    $var = stripslashes($var);
    $var = htmlspecialchars($var);
    return $var;
}

/**
 * Fonction qui prend en paramètre un mois au format aaaamm et qui retourne le
 * mois suivant au même format
 *
 * @param type String         $mois
 *
 * @return type String        $mois
 */
function getLeMoisSuivant($mois)
{
    $numAnnee = substr($mois, 0, 4);
    $numMois = substr($mois, 4, 2);
    $numAnnee = intval($numAnnee);
    $numMois = intval($numMois);
    if ($numMois === 12) {
        $numMois = 1;
        $numAnnee += 1;
    } else {
        $numMois += 1;
    }
    $numMois = strval($numMois);
    $numAnnee = strval($numAnnee);
    if (strlen($numMois === 1)) {
        return $numAnnee . '0' . $numMois;
    } else {
        return $numAnnee . $numMois;
    }
}

/**
 * Fonction qui parcours un tableau avec les fiches de frais des visiteurs,
 * et qui extrait le montant d'une fiche pour un visiteur et un mois passé
 * en paramètres
 *
 * @param array()       $ficheAPayer
 * @param String        $idVisiteur
 * @param String        $mois au format aaaamm
 *
 * @return float
 */
function getLeMontantAPayer($ficheAPayer, $idVisiteur, $mois)
{
    foreach ($ficheAPayer as $fiche) {
        if (($fiche['id'] === $idVisiteur) && ($fiche['mois'] === $mois)) {
            return floatval($fiche['montant']);
        }
    }
}

// Cette fonction n'ai pas de moi, prise sur le site PHP en exemple.

/**
 * Fonction qui prend en paramètre un array multidimensionnel, et qui en
 * supprime les doublons
 *
 * @param Array          $array : tableau duquel on veut supp les doublons
 * @param String         $key : clé sur laquelle on veut que se fasse la
 *                          sélection
 * @return Array
 */
function unique_multidim_array($array, $key)
{
    $temp_array = array();
    $i = 0;
    $key_array = array();

    foreach ($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $temp_array[$i] = $val;
        }
        $i++;
    }
    return $temp_array;
}
