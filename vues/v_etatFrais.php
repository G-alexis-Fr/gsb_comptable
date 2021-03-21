<?php

/**
 * Vue Visualisation fiche à payer
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

?>
<div class="row">
    <button type="button" class=btn btn-warning id="reSelect">
        <a href="index.php?uc=paiement&action=suiviPaiement">Retour à liste des fiches</a>
    </button>
</div>
<div class="row">
    <div class="panel panel-primary">
        <div class="panel-heading">Fiche de frais du mois
            <?php echo $numMois . '-' . $numAnnee ?> pour <?php echo $personne[0]['nom'] . ' ' . $personne[0]['prenom']; ?> : </div>
        <div class="panel-body">
            <strong><u>Etat :</u></strong> <?php echo $libEtat ?>
            depuis le <?php echo $dateModif ?> <br>
            <strong>
                <u>Montant frais forfait : </u>
            </strong> <?php echo number_format($montantForfait, 2, ',', ' '); ?> € <br>
            <strong>
                <u>Montant frais hors forfait : </u>
            </strong> <?php echo number_format($montantHorsForfait, 2, ',', ' '); ?> € <br>
            <strong>
                <u>Montant validé :</u>
            </strong> <?php echo number_format($montantValide, 2, ',', ' ') ?> €
        </div>
    </div>

    <div class="col-md-12">
        <fieldset>
            <h3>Eléments forfaitisés</h3>
            <?php
            foreach ($lesFraisForfait as $unFrais) {
                $idFrais = $unFrais['idfrais'];
                $libelle = htmlspecialchars($unFrais['libelle']);
                $quantite = $unFrais['quantite']; ?>
                <div class="form-group">
                    <label for="idFrais"><?php echo $libelle; ?></label>
                    <input type="text" id="idFrais" name="lesFrais[<?php echo $idFrais; ?>]" size="10" maxlength="5" value="<?php echo $quantite; ?>" class="form-control">
                </div>
            <?php
            }
            ?>
        </fieldset>
        <h3>Descriptif des éléments hors forfait</h3>
        <p>Nbre de justificatifs reçus = <?php echo $nbJustificatifs; ?></p>
        <table class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th class="date">Date</th>
                    <th class="libelle">Libellé</th>
                    <th class="montant">Montant</th>


                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($fraisHorsForfait as $unFraisHorsForfait) {
                    $libelle = checkInput($unFraisHorsForfait['libelle']);
                    $date = $unFraisHorsForfait['date'];
                    $montant = $unFraisHorsForfait['montant'];
                    $id = $unFraisHorsForfait['id'];
                ?>
                    <tr>
                        <td> <?php echo $date ?></td>
                        <td> <?php echo $libelle ?></td>
                        <td><?php echo $montant ?></td>

                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>