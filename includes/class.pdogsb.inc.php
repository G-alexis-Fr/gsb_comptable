<?php

/**
 * Classe d'accès aux données.
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL - CNED <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

/**
 * Classe d'accès aux données.
 *
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL - CNED <jgil@ac-nice.fr>
 * @author    Alexis Goutorbe -  <contact@g-alexis.com>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   Release: 1.0
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

class PdoGsb
{
    // Adresse du serveur : localhost hébergé sur mon propre serveur VPS,
    private static $serveur = 'mysql:host=localhost';
    // Nom BDD
    private static $bdd = 'dbname=gsb_restore';
    // Identifiant acces BDD
    private static $userId = 'root';
    //  Mdp acces BDD
    private static $mdp = '';
    // Curseur PDO 
    private static $monPdo;
    // Curseur GSB
    private static $monPdoGsb = null;


    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct()
    {
        PdoGsb::$monPdo = new PDO(
            PdoGsb::$serveur . ';' . PdoGsb::$bdd,
            PdoGsb::$userId,
            PdoGsb::$mdp
        );
        PdoGsb::$monPdo->query('SET CHARACTER SET utf8');
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct()
    {
        PdoGsb::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     *
     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb()
    {
        if (PdoGsb::$monPdoGsb == null) {
            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un comptable
     *
     * @param String $login Login du comptable
     * @return l'id, le nom et le prénom ainsique le mdp sous la forme d'un tableau associatif
     * 
     */
    public function getInfosComptable($login)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT comptable.id AS id, comptable.nom AS nom, '
                . 'comptable.prenom AS prenom, comptable.mdp AS mdp '
                . 'FROM comptable '
                . 'WHERE comptable.login = :unLogin'
        );

        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux arguments.
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT * FROM lignefraishorsforfait '
                . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraishorsforfait.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        for ($i = 0; $i < count($lesLignes); $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux arguments
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return l'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
    public function getLesFraisForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fraisforfait.id as idfrais, '
                . 'fraisforfait.libelle as libelle, '
                . 'lignefraisforfait.quantite as quantite '
                . 'FROM lignefraisforfait '
                . 'INNER JOIN fraisforfait '
                . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
                . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraisforfait.mois = :unMois '
                . 'ORDER BY lignefraisforfait.idfraisforfait'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return un tableau associatif
     */
    public function getLesIdFrais()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fraisforfait.id as idfrais '
                . 'FROM fraisforfait ORDER BY fraisforfait.id'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais)
    {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE lignefraisforfait '
                    . 'SET lignefraisforfait.quantite = :uneQte '
                    . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                    . 'AND lignefraisforfait.mois = :unMois '
                    . 'AND lignefraisforfait.idfraisforfait = :idFrais'
            );
            $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }

    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné
     *
     * @param String  $idVisiteur      ID du visiteur
     * @param String  $mois            Mois sous la forme aaaamm
     * @param Integer $nbJustificatifs Nombre de justificatifs
     *
     * @return null
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'UPDATE fichefrais '
                . 'SET nbjustificatifs = :unNbJustificatifs '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(
            ':unNbJustificatifs',
            $nbJustificatifs,
            PDO::PARAM_INT
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois)
    {
        $boolReturn = false;
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.mois FROM fichefrais '
                . 'WHERE fichefrais.mois = :unMois '
                . 'AND fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        if (!$requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT MAX(mois) as dernierMois '
                . 'FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois)
    {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'INSERT INTO fichefrais (idvisiteur,mois,nbjustificatifs,'
                . 'montantvalide,datemodif,idetat) '
                . "VALUES (:unIdVisiteur,:unMois,0,0,now(),'CR')"
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {
            $requetePrepare = PdoGsb::$monPdo->prepare(
                'INSERT INTO lignefraisforfait (idvisiteur,mois,'
                    . 'idfraisforfait,quantite) '
                    . 'VALUES(:unIdVisiteur, :unMois, :idFrais, 0)'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(
                ':idFrais',
                $unIdFrais['idfrais'],
                PDO::PARAM_STR
            );
            $requetePrepare->execute();
        }
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return null
     */
    public function supprimerFraisHorsForfait($idFrais)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'DELETE FROM lignefraishorsforfait '
                . 'WHERE lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.mois AS mois FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un
     * mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.idetat as idEtat, '
                . 'fichefrais.datemodif as dateModif,'
                . 'fichefrais.nbjustificatifs as nbJustificatifs, '
                . 'fichefrais.montantvalide as montantValide, '
                . 'etat.libelle as libEtat '
                . 'FROM fichefrais '
                . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais.
     * Modifie le champ idEtat et met la date de modif à aujourd'hui.
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $etat       Nouvel état de la fiche de frais
     *
     * @return null
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            "UPDATE fichefrais "
                . "SET idetat = :unEtat, datemodif = now() "
                . "WHERE fichefrais.idvisiteur = :unIdVisiteur "
                . "AND fichefrais.mois = :unMois"
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne un tableau avec tous les visiteurs dont le mois
     * en paramètre n'est pas encore clôturé
     *
     * @param String $mois            Mois sous la forme aa/aa/mm
     *
     * @return un tableau avec les id de tous les visiteurs concernés
     */
    public function getVisiteursPasClos($mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            "SELECT idvisiteur FROM fichefrais WHERE idetat = 'CR' "
                . "AND mois = :unMois"
        );
        $requetePrepare->bindParam('unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Fonction qui retourne le nom et le prénom d'un visiteur selon l'id donné
     *
     * @param String $id        l'id du visiteur
     *
     * @return Array     Nom et Prénom du visiteur selon l'id 
     */
    public function getInformationsVisiteur($idVisiteur)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT nom, prenom "
                . "FROM visiteur "
                . "WHERE id= :idVisiteur"
        );
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne un tableau de tous les visiteurs
     *
     * @return un tableau avec id, nom et prénom de tous les visiteurs
     */
    public function getVisiteurs()
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            "SELECT id, nom, prenom FROM visiteur ORDER BY nom ASC"
        );
        $requetePrepare->execute();
        $lesVisiteurs = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $lesVisiteurs[] = array(
                $laLigne['id'] => $laLigne['nom'] . ' ' . $laLigne['prenom'],
            );
        }
        return $lesVisiteurs;
    }

    /**
     * Retourne l'état d'une fiche de frais du mois en parametre d'un visiteur
     *
     * @param String $idVisiteur        id du visiteur
     * @param String $mois              mois au format aa/aa/mm
     *
     * @return String         'CL' ou 'CR' ou 'VA' ou 'RB' correspondant à
     * l'étape de traitement de la fiche de frais, ne retourne rien s'il n'existe
     * pas de fiches pour ce visiteurs ce mois
     */
    public function getEtatFicheFrais($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT idetat "
                . "FROM fichefrais "
                . "WHERE idvisiteur = :unVisiteur "
                . "AND mois = :unMois"
        );
        $requetePrepare->bindParam(':unVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $etat = $requetePrepare->fetch();
        return $etat['idetat'];
    }

    /**
     * Retourne le montant des frais forfait en cours
     *
     * @param String $idVisiteur        id du visiteur
     * @param String $mois              mois au format aa/aa/mm
     *
     * @return Float          Le calcul des frais forfait pour le mois en paramètre d'un visiteur
     *
     */
    public function calculFraisForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT montant, quantite "
                . "FROM lignefraisforfait "
                . "JOIN fraisforfait "
                . "ON lignefraisforfait.idfraisforfait = fraisforfait.id "
                . "WHERE lignefraisforfait.idvisiteur = :unVisiteur "
                . "AND lignefraisforfait.mois = :unMois"
        );
        $requetePrepare->bindParam(':unVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $totalForfait = 0.00;
        while ($donnees = $requetePrepare->fetch()) {
            $totalForfait = $totalForfait + floatval($donnees['montant']) * floatval($donnees['quantite']);
        }
        return $totalForfait;
    }

    /**
     * Retourne le total des frais hors forfait pour un visiteur
     *
     * @param String $idVisiteur        id du visiteur
     * @param String $mois              mois au format aa/aa/mm
     *
     * @return Float          Le calcul des frais forfait pour le mois en paramètre d'un visiteur
     */
    public function calculFraisHF($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT id, montant "
                . "FROM lignefraishorsforfait "
                . "WHERE idvisiteur = :unVisiteur "
                . "AND mois = :unMois"
        );
        $requetePrepare->bindParam(':unVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $totalHorsForfait = 0.00;
        while ($donnees = $requetePrepare->fetch()) {
            if (!($this->horsForfaitRefuse($donnees['id']))) {
                $totalHorsForfait = $totalHorsForfait + floatval($donnees['montant']);
            }
        }
        return $totalHorsForfait;
    }

    /**
     * Modifie le libellé de la ligne de frais hors forfait
     * passée en paramètre, en ajoutant "REFUSE" au début du libellé
     *
     * @param int $idFrais
     *
     * @return null
     */
    public function refuserFraisHF($idFrais)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT libelle "
                . "FROM lignefraishorsforfait "
                . "WHERE id= :idFrais"
        );
        $requetePrepare->bindParam(':idFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
        $libelleOriginal = $requetePrepare->fetch();

        $libelleModifie = 'REFUSE  ' . $libelleOriginal['libelle'];
        $requeteModifie = PdoGsb::$monPdo->prepare(
            "UPDATE lignefraishorsforfait "
                . "set libelle = :nouveauLibelle "
                . "WHERE id = :idFrais"
        );
        $requeteModifie->bindParam(':nouveauLibelle', $libelleModifie, PDO::PARAM_STR);
        $requeteModifie->bindParam(':idFrais', $idFrais, PDO::PARAM_STR);
        $requeteModifie->execute();
    }

    /**
     * Retourne True si la ligne de frais hors forfait a été refusée
     *
     * @param INT $idFrais
     *
     * @return bool    Si frais refusé retourne True sinon False
     */
    public function horsForfaitRefuse($idFrais)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT libelle "
                . "FROM lignefraishorsforfait "
                . "WHERE id= :idFrais"
        );
        $requetePrepare->bindParam(':idFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
        $libelle = $requetePrepare->fetch();
        $libelleDebut = substr($libelle['libelle'], 0, 6);
        if ($libelleDebut == 'REFUSE') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Permet la validation des montants d'une fiche de frais,ainsi que la
     * MAJ du montant valide de la fiche de frais
     *
     * @param type String     $idVisiteur
     * @param type String     $mois au format aa/aa/mm
     * @param type Float      $montant
     *
     * @return null
     */
    public function valideFrais($idVisiteur, $mois, $montant)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "UPDATE fichefrais "
                . "SET montantvalide = :montant "
                . "WHERE idvisiteur = :idVisiteur "
                . "AND mois = :unMois"
        );
        $requetePrepare->bindParam(':montant', $montant, PDO::PARAM_STR);
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Reporte ligne de frais hors forfait au mois suivant
     *
     * @param String        $idFrais
     * @param String        $mois au format aa/aa/mm
     *
     * @return null
     */
    public function reportFraisHF($idFrais, $mois)
    {
        $moisSuivant = getLeMoisSuivant($mois);
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "UPDATE lignefraishorsforfait "
                . "SET mois = :unMois "
                . "WHERE id = :idFrais"
        );
        $requetePrepare->bindParam(':unMois', $moisSuivant, PDO::PARAM_STR);
        $requetePrepare->bindParam(':idFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne un tableau de toutes les fiches visiteurs avec status :
     *  "validée et mise en paiement"
     *
     * @return  un tableau avec l'id, le nom et le prénom du visiteur,
     * le mois, nbr de justificatifs, montant et la date
     * de modification de la fiche
     */
    public function getFichesVisiteursAPayer()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT id, nom, prenom, mois, nbjustificatifs, montantvalide, datemodif "
                . "FROM visiteur INNER JOIN fichefrais "
                . "ON (visiteur.id = fichefrais.idvisiteur) "
                . "WHERE idetat='VA' ORDER BY datemodif ASC"
        );
        $requetePrepare->execute();
        $fiche = array();
        while ($donnees = $requetePrepare->fetch()) {
            $fiche[] = array(
                'id' => $donnees['id'],
                'nom' => $donnees['nom'],
                'prenom' => $donnees['prenom'],
                'mois' => $donnees['mois'],
                'nbjustificatifs' => $donnees['nbjustificatifs'],
                'montant' => $donnees['montantvalide'],
                'date' => $donnees['datemodif']
            );
        }
        return $fiche;
    }

    /**
     * Retourne un tableau de tous les id des visiteurs
     * dont l'état de la fiche est "CL"
     *
     * @return null un tableau avec les id, nom, prenom, mois
     */
    public function getLesVisiteursAValider()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT id, nom, prenom, mois "
                . "FROM visiteur "
                . "INNER JOIN fichefrais "
                . "ON (visiteur.id = fichefrais.idvisiteur) "
                . "WHERE idetat='CL' "
        );
        $requetePrepare->execute();
        $lesVisiteurs = array();
        while ($donnees = $requetePrepare->fetch()) {
            $lesVisiteurs[] = array(
                'id' => $donnees['id'],
                'nom' => $donnees['nom'],
                'prenom' => $donnees['prenom'],
                'mois' => $donnees['mois']
            );
        }
        return $lesVisiteurs;
    }

    /**
     * 
     * Permet la correction des données du jeu test, et contrôle que le montant dans les fiches validées
     * soit égale aux frais Forfait plus Hors Forfait.
     *
     * @return null
     *
     */
    public function correctionAutoMontantValide()
    {
        $fichesVisiteurs = $this->getFichesVisiteursAPayer();
        foreach ($fichesVisiteurs as $uneFicheVisiteur) {
            $idVisiteur = $uneFicheVisiteur['id'];
            $mois = $uneFicheVisiteur['mois'];
            $montantValide = $uneFicheVisiteur['montant'];
            $montantForfait = $this->calculFraisForfait($idVisiteur, $mois);
            $montantHorsForfait = $this->calculFraisHF($idVisiteur, $mois);
            $montantTotal = $montantForfait + $montantHorsForfait;

            if ($montantValide !== $montantTotal) {
                $requetePrepare = PdoGsb::$monPdo->prepare(
                    "UPDATE fichefrais "
                        . "SET montantvalide = :montant "
                        . "WHERE idvisiteur = :idVisiteur AND mois = :unMois"
                );
                $requetePrepare->bindParam(':montant', $montantTotal, PDO::PARAM_STR);
                $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
                $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
                $requetePrepare->execute();
            }
        }
    }

    /**
     * 
     * Permet la correction des données du jeu test, et contrôle que le nombre de justificatifs dans les fiches validées
     * soit égale ou plus petit qu'aux nombres de lignes de frais Hors Forfait.
     *
     * @return null
     *
     */
    public function correctionAutoNbrJustificatifs()
    {
        $fichesVisiteurs = $this->getFichesVisiteursAPayer();
        foreach ($fichesVisiteurs as $uneFicheVisiteur) {
            $idVisiteur = $uneFicheVisiteur['id'];
            $mois = $uneFicheVisiteur['mois'];
            $nbJustificatifs = $uneFicheVisiteur['nbjustificatifs'];
            $fraisHorsForfait = $this->getLesFraisHorsForfait($idVisiteur, $mois);

            if (count($fraisHorsForfait) < $nbJustificatifs) {
                $requetePrepare = PdoGsb::$monPdo->prepare(
                    "UPDATE fichefrais "
                        . "SET nbjustificatifs = :nbJustificatifs "
                        . "WHERE idvisiteur = :idVisiteur AND mois = :unMois"
                );
                $requetePrepare->bindParam(':nbJustificatifs', count($fraisHorsForfait), PDO::PARAM_STR);
                $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
                $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
                $requetePrepare->execute();
            }
        }
    }

    /**
     * Récupère les mots de passe de tous les visiteurs
     *
     * @return Array        un tableau avec les identifiants des
     * visiteurs et leur mot de passe
     */
    public function getMDPVisiteurs()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT id, mdp "
                . "FROM visiteur "
        );
        $requetePrepare->execute();
        while ($donnees = $requetePrepare->fetch()) {
            $mdp[] = array(
                'id' => $donnees['id'],
                'mdp' => $donnees['mdp']
            );
        }
        return $mdp;
    }

    /**
     * Modifie le mot de passe d'un visiteur par un Hash
     *
     * @param String           $idVisiteur = id du visiteur
     * @param String            $mdp mot de passe non hashé
     *
     * @return null
     */
    public function setMDPHashVisiteurs($idVisiteur, $mdp)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "UPDATE visiteur "
                . "SET mdp = :motHash "
                . "WHERE id = :id "
        );
        $mdp = password_hash($mdp, PASSWORD_BCRYPT);
        $requetePrepare->bindParam(':motHash', $mdp, PDO::PARAM_STR);
        $requetePrepare->bindParam(':id', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Récupèration des mots de passe de tous les comptables
     *
     * @return Array  un tableau contenant les identifiants des
     * comptables et leur mot de passe
     */
    public function getMDPComptables()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT id, mdp "
                . "FROM comptable "
        );
        $requetePrepare->execute();
        while ($donnees = $requetePrepare->fetch()) {
            $mdp[] = array(
                'id' => $donnees['id'],
                'mdp' => $donnees['mdp']
            );
        }
        return $mdp;
    }

    /**
     * Modifie le mot de passe d'un comptable par un Hash
     *
     * @param String           $idComptable = id du comptable
     * @param String            $mdp mot de passe non hashé
     *
     * @return null
     */
    public function setMDPHashComptables($idComptable, $mdp)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "UPDATE comptable "
                . "SET mdp = :motHash "
                . "WHERE id = :id "
        );
        $mdp = password_hash($mdp, PASSWORD_BCRYPT);
        $requetePrepare->bindParam(':motHash', $mdp);
        $requetePrepare->bindParam(':id', $idComptable, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Permet mise a jour de la table ligneFraisHorsForfait d'un visiteur 
     * du mois concerne avec les nouveaux frais
     *
     * @param String $idVisiteur Identifiant du visiteur
     * @param String $mois       Mois sous la forme aa/aa/mm
     * @param Array  $lesFrais   Tableau avec clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisHF($idVisiteur, $mois, $lesFrais)
    {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $montant = $lesFrais[$unIdFrais];
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE lignefraishorsforfait '
                    . 'SET lignefraishorsforfait.montant = :unMontant '
                    . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
                    . 'AND lignefraishorsforfait.mois = :unMois '
                    . 'AND lignefraishorsforfait.id = :idFrais'
            );
            $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }
}
