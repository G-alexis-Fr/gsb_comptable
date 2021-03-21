<?php

/**
 * Vue Fiche Validée avec Succès
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
<div class="row">
    <h2> La fiche de frais du mois de <?php echo $numMois . ' / ' . $numAnnee . ' '; ?>
        pour <?php echo $personne[0]['nom'] . ' ' . $personne[0]['prenom'] . ' '; ?>
        a bien été validée !
    </h2><br>

    <a href="index.php?uc=validation&action=selectionVisiteurMois">
        Cliquez si votre navigateur ne vous redirige pas automatiquement
    </a>
</div>