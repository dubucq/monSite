<?php

class PdoMonSite
{   		
      	private static $serveur='mysql:host=localhost';
      	private static $bdd='dbname=monsite';   		
      	private static $user='root' ;    		
      	private static $mdp='' ;	
		private static $monPdo;
		private static $monPdoMonSite = null;
/**
 * Constructeur privé, crée l'instance de PDO qui sera sollicitée
 * pour toutes les méthodes de la classe
 */				
	private function __construct()
	{
    		PdoMonSite::$monPdo = new PDO(PdoMonSite::$serveur.';'.PdoMonSite::$bdd, PdoMonSite::$user, PdoMonSite::$mdp); 
			PdoMonSite::$monPdo->query("SET CHARACTER SET utf8");
	}
	public function _destruct(){
		PdoMonSite::$monPdo = null;
	}
/**
 * Fonction statique qui crée l'unique instance de la classe
 *
 * Appel : $instancePdoMonSite = PdoMonSite::getPdoMonSite();
 * @return l'unique objet de la classe PdoMonSite
 */
	public  static function getPdoMonSite()
	{
		if(PdoMonSite::$monPdoMonSite == null)
		{
			PdoMonSite::$monPdoMonSite= new PdoMonSite();
		}
		return PdoMonSite::$monPdoMonSite;  
	}

	//fonction qui sert à récupérer le matricule, nom et prénom de tous les visiteurs

	public static function getVisiteurs()
	{
		$req = "SELECT VIS_MATRICULE, VIS_NOM, VIS_PRENOM FROM VISITEUR";
		$res = PdoMonSite::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes;
	}

	//fonction qui sert à récupérer un visiteur en fonction de son matricule

	public static function getLeVisiteur($matricule)
	{
		$req = "SELECT  VIS_NOM, VIS_PRENOM FROM VISITEUR 
		WHERE VIS_MATRICULE = '".$matricule."'";
		$res = PdoMonSite::$monPdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne;
	}

	//fonction qui permet de créer une fiche d'accompagnement dans la bdd

	public function creerFicheAccompagnement($matricule, $date, $n1, $n2, $n3, $n4){
		$req = PdoMonSite::$monPdo->prepare('INSERT INTO `RAPPORT_VISITE`( `VIS_MATRICULE`,
		`VIS_MATRICULE_1`, `RAP_DATE`, `RAP_CRITERE_1`, `RAP_CRITERE_2`, `RAP_CRITERE_3`,
		`RAP_CRITERE_4`) 
		VALUES (:matricule,:matricule1, :dateR, :n1, :n2, :n3, :n4)');
		$req->bindValue('matricule',$matricule);
		$req->bindValue('matricule1',$matricule);
	    $req->bindValue('dateR', $date);
	    $req->bindValue('n1', $n1);
	    $req->bindValue('n2', $n2);
	    $req->bindValue('n3', $n3);
	    $req->bindValue('n4', $n4);
		$req->execute();
	}

	//fonction qui permet de créer une fiche d'entretien annuel dans la bdd

	public function creerFicheAnnuel($matricule, $date, $objFixe, $objAtteint, $nouvelleObj, $amelioration, $salaire, $prime){
		$req = PdoMonSite::$monPdo->prepare('INSERT INTO `bilan_annuel`( `VIS_MATRICULE`, `VIS_MATRICULE_1`, 
		`BILAN_DATE`, `OBJECTIF_FIXE`, `OBJECTIF_ATTEINT`, `OBJECTIF_NOUVEAU`,`CRITERE_A_AMELIORER`, `SALAIRE`, 
		`PRIME`) 
		VALUES (:matricule, :matricule1, :dateB, :objF, :objA, :objN, :critereA, :salaire, :prime)');
		$req->bindValue('matricule', $matricule);
		$req->bindValue('matricule1', $matricule);
	    $req->bindValue('dateB', $date);
	    $req->bindValue('objF', $objFixe);
	    $req->bindValue('objA', $objAtteint);
	    $req->bindValue('objN', $nouvelleObj);
	    $req->bindValue('critereA', $amelioration);
	    $req->bindValue('salaire', $salaire);
	    $req->bindValue('prime', $prime);
		$req->execute();
	}

	//fonction qui permet de récupérer toutes les équipes

	public function getEquipes(){
		$req = "SELECT ID_EQUIPE, NOM_EQUIPE FROM EQUIPE";
		$res = PdoMonSite::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes;
	}

	//fonction qui permet de récupérer une équipe par rapport a son id

	public function getEquipe($id){
		$req = "SELECT ID_EQUIPE, NOM_EQUIPE FROM EQUIPE WHERE ID_EQUIPE = '".$id."'";
		$res = PdoMonSite::$monPdo->query($req);
		$lesLignes = $res->fetch();
		return $lesLignes;
	}

	//fonction qui permet de récupérer les visiteurs d'une équipe

	public function getVisiteursEquipe($id){
		$req = "SELECT  VIS_MATRICULE, VIS_NOM, VIS_PRENOM 	FROM VISITEUR
		WHERE ID_EQUIPE='".$id."'";

		$res = PdoMonSite::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes;
	}

	//fonction qui permet de récupérer les matricules des visiteurs en fonction d'une équipe

	public function getIdVisiteursEquipe($id){
		$req = "SELECT  VIS_MATRICULE FROM VISITEUR
		WHERE ID_EQUIPE='".$id."'";

		$res = PdoMonSite::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes;
	}

	//fonction qui permet de récupérer les objectifs d'un visiteurs 

	public function getObjectifs($id){
		$req = "SELECT OBJECTIF_FIXE, OBJECTIF_ATTEINT, OBJECTIF_NOUVEAU FROM BILAN_ANNUEL WHERE VIS_MATRICULE = '".$id."'";
		$res = PdoMonSite::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes;
	}

	//fonction qui permet de faire une moyenne des objectifs fixés au visiteur d'une même équipe

	public function getMoyenneObjFixe($id){
		$req = "SELECT AVG(OBJECTIF_FIXE) AS 'MOF' FROM BILAN_ANNUEL, VISITEUR, EQUIPE
		WHERE BILAN_ANNUEL.VIS_MATRICULE = VISITEUR.VIS_MATRICULE
		AND VISITEUR.ID_EQUIPE = EQUIPE.ID_EQUIPE
		AND EQUIPE.ID_EQUIPE = '".$id."'";
		$res = PdoMonSite::$monPdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne;
	}

	//fonction qui permet de faire une moyenne des objectifs atteints au visiteur d'une même équipe

	public function getMoyenneObjAtteint($id){
		$req = "SELECT AVG(OBJECTIF_ATTEINT) AS 'MOA' FROM BILAN_ANNUEL, VISITEUR, EQUIPE
		WHERE BILAN_ANNUEL.VIS_MATRICULE = VISITEUR.VIS_MATRICULE
		AND VISITEUR.ID_EQUIPE = EQUIPE.ID_EQUIPE
		AND EQUIPE.ID_EQUIPE = '".$id."'";
		$res = PdoMonSite::$monPdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne;
	}

	//fonction qui permet de faire une moyenne des objectifs nouveaux au visiteur d'une même équipe

	public function getMoyenneObjNouveau($id){
		$req = "SELECT AVG(OBJECTIF_NOUVEAU) AS 'MON' FROM BILAN_ANNUEL, VISITEUR, EQUIPE
		WHERE BILAN_ANNUEL.VIS_MATRICULE = VISITEUR.VIS_MATRICULE
		AND VISITEUR.ID_EQUIPE = EQUIPE.ID_EQUIPE
		AND EQUIPE.ID_EQUIPE = '".$id."'";
		$res = PdoMonSite::$monPdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne;
	}

	//fonction qui permet de faire la connexion en tant que visiteur

	public function connexionVisiteur($login, $mdp){
		$req = "SELECT  VIS_NOM, VIS_PRENOM FROM VISITEUR
		WHERE VIS_MATRICULE='".$login."' AND VIS_MDP = '".$mdp."'";
		$res = PdoMonSite::$monPdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne;
	}

	//fonction qui permet de faire la connexion en tant que délégué régionaux

	public function connexionDelegueR($login, $mdp){
		$req = "SELECT  VIS_NOM, VIS_PRENOM FROM DELEGUE_REGIONAUX
		WHERE VIS_MATRICULE='".$login."' AND VIS_MDP = '".$mdp."'";
		$res = PdoMonSite::$monPdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne;
	}

	//fonction qui permet de faire la connexion en tant que responsable

	public function connexionResponsable($login, $mdp){
		$req = "SELECT  VIS_NOM, VIS_PRENOM FROM RESPONSABLE
		WHERE VIS_MATRICULE='".$login."' AND VIS_MDP = '".$mdp."'";
		$res = PdoMonSite::$monPdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne;
	}

	//fonction qui permet de récupérer la fiche annuel d'une année pour un visiteur

	public function getFicheAnnuelle($id, $annee){
		$req = "SELECT  * FROM BILAN_ANNUEL 
		WHERE VIS_MATRICULE='".$id."' AND YEAR(BILAN_DATE) = '".$annee."'";
		$res = PdoMonSite::$monPdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne;
	}

	//fonction qui permet de récupérer la fiche d'accompagnement d'une année pour un visiteur

	public function getFichesAccompagnement($id, $annee){
		$req = "SELECT  * FROM RAPPORT_VISITE 
		WHERE VIS_MATRICULE='".$id."' AND YEAR(RAP_DATE) = '".$annee."'";
		$res = PdoMonSite::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes;
	}

	//fonction qui permet de récupérer la carrière d'un visiteur

	public function getCarriere($matricule)
	{
		$req = "SELECT CAR_ROLE, CAR_DATE, REG_NOM FROM CARRIERE, REGION
		WHERE CARRIERE.REG_CODE=REGION.REG_CODE
		AND VIS_MATRICULE = '".$matricule."'";
		$res = PdoMonSite::$monPdo->query($req);
		$LesLignes = $res->fetchAll();
		return $LesLignes;
	}

	//fonction qui permet de compter le nombre de fiche d'accompagnement qui existe pour une année

	public function testFicheAccompagnement($id, $annee){
		$req = "SELECT  COUNT(VIS_MATRICULE) AS 'nb' FROM RAPPORT_VISITE 
		WHERE VIS_MATRICULE='".$id."'
		AND YEAR(RAP_DATE)='".$annee."'";
		$res = PdoMonSite::$monPdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne;
	}

	//fonction qui permet de compter le nombre de fiche annuel qui existe pour une année

	public function testFicheAnnuel($id, $annee){
		$req = "SELECT  COUNT(VIS_MATRICULE) AS 'nb' FROM BILAN_ANNUEL 
		WHERE VIS_MATRICULE='".$id."'
		AND YEAR(BILAN_DATE)='".$annee."'";
		$res = PdoMonSite::$monPdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne;
	}

	//fonction qui permet de faire un commentaire sur une fiche annuel

	public function commentaireFiche($commentaire, $id){
		$req = PdoMonSite::$monPdo->prepare ("UPDATE RAPPORT_VISITE SET `RAP_COMMENTAIRE` = '".$commentaire."' WHERE RAP_NUM = '".$id."'");
		$req->execute();
	}

	public function getVisiteur($nomVisiteur)
	{
		$req = "select * from visiteur WHERE VIS_NOM = '$nomVisiteur'";
		$res = PdoMonSite::$monPdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne;
	}

	public function getLesSecteurs()
	{
		$req = "select * from secteur";
		$res = PdoMonSite::$monPdo->query($req);
		$LesLignes = $res->fetchAll();
		return $LesLignes;
	}
	public function getLesLabos()
	{
		$req = "select * from labo";
		$res = PdoMonSite::$monPdo->query($req);
		$LesLignes = $res->fetchAll();
		return $LesLignes;
	}

	public function modifVisiteur($matricule,$nomVisiteur,$prenomVisiteur,$adresseVisiteur,$villeVisiteur)
	{
		$res = PdoMonSite::$monPdo->prepare("UPDATE visiteur SET VIS_NOM='$nomVisiteur', VIS_PRENOM='$prenomVisiteur' ,VIS_ADRESSE='$adresseVisiteur' ,VIS_VILLE='$villeVisiteur' WHERE VIS_MATRICULE='$matricule' ");
		$res->execute();
	}
}
?>