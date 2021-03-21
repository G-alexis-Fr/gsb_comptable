<?php

/**
 * Vue Paiement des Fiches de Frais
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
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            <span class="glyphicon glyphicon-bookmark"></span>
            Fiches de Frais Validées, en attentes de remboursement :
        </h3>
    </div>
    <div class="panel-body">
        <div class="col-md-12">
            <form id="formPaiement" method="post" action="index.php?uc=paiement&action=payer" role="form">

                <table class="table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th class="nom">Nom</th>
                            <th class="prenom">Prénom</th>
                            <th class="mois text-center">Mois de la fiche</th>
                            <th class="montant text-center">Montant</th>
                            <th class="date text-center">Date de validation</th>
                            <th class="action text-center">Voir le détail</th>
                            <th class="action text-center">Payer :
                                <input type="checkbox" id="all" onclick="selectAll()">
                            </th>


                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($ficheRembourser as $uneFiche) {
                            $id = $uneFiche['id'];
                            $nomVisiteur = $uneFiche['nom'];
                            $prenomVisiteur = $uneFiche['prenom'];
                            $mois = $uneFiche['mois'];
                            $montant = $uneFiche['montant'];
                            $date = dateAnglaisVersFrancais($uneFiche['date']);

                        ?>
                            <tr>
                                <td> <?php echo $nomVisiteur; ?></td>
                                <td> <?php echo $prenomVisiteur; ?></td>
                                <td class="text-center"><?php echo $mois; ?></td>
                                <td class="text-center"><?php echo number_format($montant, 2, ',', ' '); ?> €</td>
                                <td class="text-center"><?php echo $date; ?></td>
                                <td class="text-center"><button type="button" class="btn btn-orange">
                                        <span class="glyphicon glyphicon-eye-open">&nbsp</span>
                                        <a href="index.php?uc=paiement&action=voirFiche&id=<?php echo $id; ?>&mois=<?php echo $mois; ?>" style="color:white !important">
                                            Voir la fiche
                                        </a>
                                    </button></td>
                                <td class="text-center"><input type="checkbox" name="aPayer[]" value="<?php echo $id . '&' . $mois; ?>"></td>
                            </tr>
                        <?php
                        }

                        ?>
                    </tbody>
                </table>

                <button class="btn btn-success btnValide" type="submit">
                    Payer les fiches 
                    <span class="glyphicon glyphicon-eur"> </span>
                </button>
            </form>
        </div>
    </div>
</div>