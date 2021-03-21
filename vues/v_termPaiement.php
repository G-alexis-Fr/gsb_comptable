<?php

/**
 * Vue Succès de Paiement
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

            
<div class="alert alert-success" role="alert">
    <h4>
        Virements sélectionnés
        pour un montant total de 
        <?php echo number_format($virementMontant, 2, ',', ' '); ?> € 
        ont bien été notifiés à la banque !
    </h4>
</div>
