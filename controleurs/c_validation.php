<?php

/**
 * Controleur Validation des Fiches de Frais
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

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

/**
 * Récupération de l'id visiteur, 
 * 
 * - A travers la sélection dans les listes déroulantes, 
 * - Ou soit via l'URL
 */
if (!empty($_POST['lstVisiteurs']) && !empty($_POST['lstMois'])) {
    $idVisiteur = $_POST['lstVisiteurs'];
    $leMois = $_POST['lstMois'];
} else {
    $idVisiteur = filter_input(INPUT_GET, 'idVisiteur', FILTER_SANITIZE_STRING);
    $leMois = filter_input(INPUT_GET, 'leMois', FILTER_SANITIZE_STRING);
}

/**
 *  Si  visiteur et  mois, alors nous formattons f numAnnee et numMois
 *  et nous récupération le nom et prénom du visiteur concerné dans la bdd
 */

if (isset($idVisiteur) && isset($leMois)) {
    $numAnnee = substr($leMois, 0, 4);
    $numMois = substr($leMois, -2);
    $personne = $pdo->getInformationsVisiteur($idVisiteur);
}

switch ($action) {
    case 'selectionVisiteurMois':
        // Permet de retourner les infos dans listes déroulantes
        $lesVisiteurs = $pdo->getLesVisiteursAValider();
        if (!is_array($lesVisiteurs) || count($lesVisiteurs) === 0) {
            Erreur("Aucune fiche visiteur à valider !");
            include 'vues/v_erreurs.php';
        } else {
            /**
             * Récupèration des mois à valider pour tous les visiteurs
             * sélectionnés au dessus
             */
            foreach ($lesVisiteurs as $unVisiteur) {
                $lesMois[] = $unVisiteur['mois'];
            }

            // Suppression des doublons 

            $lesMois = array_unique($lesMois);
            $visiteurASelectionner = $lesVisiteurs[0];
            $moisASelectionner = $lesMois[0];
            $lesVisiteurs = unique_multidim_array($lesVisiteurs, 'id');
        }
        include 'vues/v_selectVisiteurMois.php';
        break;
    case 'valider':
        // Récupération des informations pour la fiche de frais
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
        $etatFicheVisiteur = $pdo->getEtatFicheFrais($idVisiteur, $leMois);

        if (empty($etatFicheVisiteur)) {
            Erreur("Aucune fiche de frais disponible pour ce visiteur pour ce mois");
            include 'vues/v_erreurs.php';
        } elseif ($etatFicheVisiteur != 'CL') {
            Erreur("La fiche de ce mois n'est pas à valider pour ce visiteur");
            include 'vues/v_erreurs.php';
        } else {
            $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
            $montantForfait = $pdo->calculFraisForfait($idVisiteur, $leMois);
            $montantHorsForfait = $pdo->calculFraisHF($idVisiteur, $leMois);
            $libEtat = $lesInfosFicheFrais['libEtat'];
            $enCours = $montantForfait + $montantHorsForfait;
            $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
            $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
        }

        include 'vues/v_validEntete.php';
        include 'vues/v_validForfait.php';
        include 'vues/v_validHorsForfait.php';
        include 'vues/v_validFiche.php';
        break;
        /* 
        * Correction des frais forfait, 
        * Récupération id du frais dans l'URL, et MAJ des données
        */
    case 'corrigerForfait':
        $lesFrais = filter_input(
            INPUT_POST,
            'lesFrais',
            FILTER_DEFAULT,
            FILTER_FORCE_ARRAY
        );
        $pdo->majFraisForfait($idVisiteur, $leMois, $lesFrais);
        header('Location:index.php?uc=validation&action=valider&idVisiteur=' . $idVisiteur . '&leMois=' . $leMois);
        /* 
        * Correction des frais hors forfait, on récupère l'id du frais dans
        * l'URL, et on met à jour la bdd avant de revenir à la fiche 
        */
        break;
    case 'corrigerHorsForfait':
        $fraisHorsForfait = filter_input(
            INPUT_POST,
            'horsForfait',
            FILTER_DEFAULT,
            FILTER_FORCE_ARRAY
        );
        $nbJustificatifs = filter_input(INPUT_POST, 'justifs', FILTER_SANITIZE_NUMBER_INT);
        $pdo->majFraisHF($idVisiteur, $leMois, $fraisHorsForfait);
        $pdo->majNbJustificatifs($idVisiteur, $leMois, $nbJustificatifs);
        header('Location:index.php?uc=validation&action=valider&idVisiteur=' . $idVisiteur . '&leMois=' . $leMois);
        break;
    case 'supprimerFrais':

        $idFrais = filter_input(INPUT_GET, 'idFrais', FILTER_SANITIZE_STRING);
        $pdo->refuserFraisHF($idFrais);
        header('Location:index.php?uc=validation&action=valider&idVisiteur=' . $idVisiteur . '&leMois=' . $leMois);
        include 'vues/v_validation.php';
        break;

        // Report des frais de la fiche en cours au mois suivant
    case 'reporterFrais':
        $idFrais = filter_input(INPUT_GET, 'idFrais', FILTER_SANITIZE_STRING);
        $pdo->reportFraisHF($idFrais, $leMois);
        header('Location:index.php?uc=validation&action=valider&idVisiteur=' . $idVisiteur . '&leMois=' . $leMois);
        include 'vues/v_validation.php';
        break;
        // Nous validons apres avoir effectué toutes les vérifications 
    case 'succesValidation':
        $montantForfait = $pdo->calculFraisForfait($idVisiteur, $leMois);
        $montantHorsForfait = $pdo->calculFraisHF($idVisiteur, $leMois);
        $enCours = $montantForfait + $montantHorsForfait;
        $pdo->valideFrais($idVisiteur, $leMois, $enCours);
        $pdo->majEtatFicheFrais($idVisiteur, $leMois, 'VA');
        include 'vues/v_termValidation.php';
        header('Refresh:5 ; URL=index.php?uc=validation&action=selectionVisiteurMois');
        break;
    default:
        include 'vues/v_accueil.php';
}
