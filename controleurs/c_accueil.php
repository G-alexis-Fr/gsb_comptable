<?php

/**
 * Controleur Accueil
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
 * Si l'utilisateur est connecté, on récupère le nombre de fiches à clôturer,
 * valider, et payer, pour information
 */
if ($estConnecte) {
    $ficheRembourser = $pdo->getFichesVisiteursAPayer();
    $nbrFichePayer = count($ficheRembourser);

    $fichesAValider = $pdo->getLesVisiteursAValider();
    $nbrFicheValider = count($fichesAValider);

    $mois = getMois(date('d/m/Y'));
    $moisPrec = getMoisPrec($mois);
    $fichesACloturer = $pdo->getVisiteursPasClos($moisPrec);
    $nbrFicheCloturer = count($fichesACloturer);
    include 'vues/v_accueil.php';
} else {
    include 'vues/v_connexion.php';
}
