<?php

/**
 * Vue en-tête de la fiche à valider
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

?>
<div class="row"><button type="button" class="btn btn-secondary" id="reSelect"><a href="index.php?uc=validation&action=selectionVisiteurMois">Retour à la sélection</a></button></div>
<div class="row">
    <div class="panel panel-primary">
        <div class="panel-heading">Fiche de frais du mois
            <?php echo $numMois . '-' . $numAnnee ?> pour <?php echo $personne[0]['nom'] . ' ' . $personne[0]['prenom']; ?> : </div>
        <div class="panel-body">
            <strong><u>Etat :</u></strong> <?php echo $libEtat ?>
            depuis le <?php echo $dateModif; ?> <br>
            <strong>
                <u>Montant frais forfait : </u>
            </strong> <?php echo number_format($montantForfait, 2, ',', ' '); ?> € <br>
            <strong>
                <u>Montant frais hors forfait : </u>
            </strong> <?php echo number_format($montantHorsForfait, 2, ',', ' '); ?> € <br>
            <strong>
                <u>Total en cours :</u>
            </strong> <?php echo number_format($enCours, 2, ',', ' ') ?> €
        </div>
    </div>
</div>