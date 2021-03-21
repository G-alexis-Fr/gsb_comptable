<?php

/**
 * Vue Confirmation de clôture
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
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <h2>Souhaitez-vous clôturer <?php echo $nbrFicheCloturer; ?> note(s) de frais ?</h2>
                    </div>
                    <div class="col-xs-6 col-md-6">
                        <a href="index.php?uc=cloture&action=succesCloture" class="btn btn-primary btn-lg" role="button">
                            <br>
                            <p class="overflow-visible">Oui<br></p>
                        </a>
                    </div>
                    <div class="col-xs-6 col-md-6">
                        <a href="index.php?uc=accueil" class="btn btn-danger btn-lg" role="button">
                            <br>
                            <p class="overflow-visible">Non<br></p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>