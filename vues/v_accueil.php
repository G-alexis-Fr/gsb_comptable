<?php

/**
 * Vue page d'Accueil
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
<div id="accueil">
    <h2>
        Gestion des frais Comptable :<small>
            <?php
            echo $_SESSION['nom'] . ' ' . $_SESSION['prenom']
            ?></small>
    </h2><br>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <span class="glyphicon glyphicon-bookmark"></span>
                    Navigation
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-4 col-md-4">
                        <a href="index.php?uc=cloture&action=cloturer" class="btn btn-secondary btn-lg" role="button">
                            <span class="glyphicon glyphicon-folder-close"></span>
                            <br>
                            <p class="overflow-visible"><br>Clôture automatique </p>
                        </a>
                    </div>
                    <div class="col-xs-4 col-md-4">
                        <a href="index.php?uc=validation&action=selectionVisiteurMois" class="btn btn-primary btn-lg" role="button">
                            <span class="glyphicon glyphicon-hand-right"></span>
                            <br>
                            <p class="overflow-visible"><br>Validation<br></p>
                        </a>
                    </div>
                    <div class="col-xs-4 col-md-4">
                        <a href="index.php?uc=paiement&action=suiviPaiement" class="btn btn-success btn-lg" role="button">
                            <span class="glyphicon glyphicon-euro"></span>
                            <br>
                            <p class="overflow-visible"><br>Paiement<br></p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<h2>Il y a : </h2>
<div class="affichage" role="alert">
    <h3> - <?php echo ($nbrFicheCloturer > 0) ? $nbrFicheCloturer : 'aucune'; ?> fiche(s) à clôturer</h3>
    <h3> - <?php echo ($nbrFicheValider > 0) ? $nbrFicheValider : 'aucune' ?> fiche(s) à valider</h3>
    <h3> - <?php echo ($nbrFichePayer > 0) ? $nbrFichePayer : 'aucune' ?> fiche(s) à payer</h3>
</div>