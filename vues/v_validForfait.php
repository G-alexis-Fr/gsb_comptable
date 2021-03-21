<?php

/**
 * Vue frais forfait de la fiche à valider
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
<div class="col-md-12">
    <form method="post" action="index.php?uc=validation&action=corrigerForfait&idVisiteur=<?php echo $idVisiteur; ?>&leMois=<?php echo $leMois; ?>" role="form">
        <fieldset>
            <h3>Eléments forfaitisés</h3>
            <?php
            foreach ($lesFraisForfait as $unFrais) {
                $idFrais = $unFrais['idfrais'];
                $libelle = htmlspecialchars($unFrais['libelle']);
                $quantite = $unFrais['quantite']; ?>
                <div class="form-group">
                    <label for="idFrais"><?php echo $libelle; ?></label>
                    <input type="number" 
                    class="form-control" 
                    id="idFrais" 
                    name="lesFrais[<?php echo $idFrais; ?>]" 
                    min="0" step="1" 
                    value="<?php echo $quantite; ?>">
                </div>
            <?php
            }
            ?>
            <button name="corrigerForfait" class="btn btn-primary" type="submit" value="corriger">Corriger</button>
        </fieldset>
    </form>
</div>