<?php

/**
 * Controleur Connexion
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @author    Alexis Goutorbe <contact@g-alexis.com>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if (!$uc) {
    $uc = 'demandeConnexion';
}

switch ($action) {
    case 'demandeConnexion':
        include 'vues/v_connexion.php';
        break;
    case 'valideConnexion':
        /**
         * Nous vérifions si les Mots de passes sont hachés 
         */
        $mDPasseDP = $pdo->getMDPComptables();

        foreach ($mDPasseDP as $comptable) {
            $mDPasse = $comptable['mdp'];
            $id = $comptable['id'];
            $hashage = substr($mDPasse, 0, 4);
            if ($hashage !== '$2y$') {
                $pdo->setMDPHashComptables($id, $mDPasse);
            }
        }
        // Récupèration des identifiants saisi par l'utilisateur
        $login = checkInput($_POST['login']);
        $mdp = checkInput($_POST['mdp']);

        // Récupèration des informations du comptable connecté
        $comptable = $pdo->getInfosComptable($login);

        // Si aucun comptable avec ce login alors nous affichons un message erreur
        if (!is_array($comptable)) {
            Erreur('Login ou mot de passe incorrect');
            include 'vues/v_erreurs.php';
            include 'vues/v_connexion.php';
        } else {
            // Sinon, vérification du mdp hashé
            $mdpValid = password_verify($mdp, $comptable['mdp']);
            // Si mdp est valide, création du cookie de session
            if ($mdpValid) {
                $id = $comptable['id'];
                $nom = $comptable['nom'];
                $prenom = $comptable['prenom'];
                connecter($id, $nom, $prenom);
                header('Location: index.php');
            } else {
                // Si les identifiants sont incorrect alors nous redirigons la personne 
                // et affichons un message d'erreur
                Erreur('Login ou mot de passe incorrect');
                include 'vues/v_erreurs.php';
                include 'vues/v_connexion.php';
            }
        }
        break;
    default:
        include 'vues/v_connexion.php';
        break;
}
