<?php

/**
 * Controleur Clôture Automatique
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
 * Pour la clôture nous regardons le mois précédent pour avoir la date
 *  de connexion et ainsi vérifier les fiches à clôturer
 */
$mois = getMois(date('d/m/Y'));
$moisPrec = getMoisPrec($mois);
$visiteurs = $pdo->getVisiteursPasClos($moisPrec);
$nbrFicheCloturer = count($visiteurs);

$numMoisPrec = substr($moisPrec, 4, 2);
$numAnnee = substr($moisPrec, 0, 4);

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

// Si fiche en attente de clôture nous affichons message d'erreur sinon on clôture
switch ($action) {
    case 'cloturer':
        if ($nbrFicheCloturer === 0) {
            
            Erreur("Aucune fiche visiteur est en attente de clôture ! ");
            include 'vues/v_erreurs.php';
        } else {
            include 'vues/v_clotureAuto.php';
        }
        break;
        
    case 'succesCloture':
        $nbrCloture = count($visiteurs);
        foreach ($visiteurs as $visiteur) {
            $pdo->majEtatFicheFrais($visiteur['idvisiteur'], $moisPrec, 'CL');
            $pdo->creeNouvellesLignesFrais($visiteur['idvisiteur'], $mois);
        }
        include 'vues/v_termCloture.php';
        break;
    default:
        include 'vues/v_accueil.php';
}
