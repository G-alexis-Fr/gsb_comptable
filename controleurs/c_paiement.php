<?php

/**
 * Controleur Paiement des Fiches de Frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    José GIL <jgil@ac-nice.fr>
 * @author    Alexis Goutorbe <contact@g-alexis.com>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

switch ($action) {
    case 'suiviPaiement':
        // Correction des éléments du jeu test (montant valide + nb justificatifs)
        $pdo->correctionAutoMontantValide();
        $pdo->correctionAutoNbrJustificatifs();

        $ficheRembourser = $pdo->getFichesVisiteursAPayer();

        // Si aucune fiche à payer nous envoyons une erreur
        if (count($ficheRembourser) === 0) {
            Erreur("Aucune fiche visiteur en attente de paiement !");
            include 'vues/v_erreurs.php';
        } else {
            include 'vues/v_suivrePaiement.php';
        }
        break;
    case 'voirFiche':
        // Récupération de tous les éléments de la fiche de frais
        $idVisiteur = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
        $leMois = filter_input(INPUT_GET, 'mois', FILTER_SANITIZE_STRING);

        $numAnnee = substr($leMois, 0, 4);
        $numMois = substr($leMois, -2);

        $personne = $pdo->getInformationsVisiteur($idVisiteur);

        $fraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
        $etatFicheVisiteur = $pdo->getEtatFicheFrais($idVisiteur, $leMois);

        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
        $montantForfait = $pdo->calculFraisForfait($idVisiteur, $leMois);
        $montantHorsForfait = $pdo->calculFraisHF($idVisiteur, $leMois);
        $montantValide = $lesInfosFicheFrais['montantValide'];
        $libEtat = $lesInfosFicheFrais['libEtat'];
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
        include 'vues/v_etatFrais.php';
        break;
    case 'payer':
        // Récupération des cases cochées et MAJ des donnees payées
        $ficheRembourser = $pdo->getFichesVisiteursAPayer();
        $virementMontant = 0;
        foreach ($_POST['aPayer'] as $virement) {
            $posId = stripos($virement, '&');
            $idVisiteur = substr($virement, 0, $posId);
            $moisPaye = substr($virement, $posId + 1);
            $montantAPayer = getLeMontantAPayer($ficheRembourser, $idVisiteur, $moisPaye);
            $virementMontant += $montantAPayer;
            $pdo->majEtatFicheFrais($idVisiteur, $moisPaye, 'RB');
        }
        include 'vues/v_termPaiement.php';
        break;
    default:
        include 'vues/v_accueil.php';
}
