<?php

require_once("utils/fonctions.inc.php");
require_once("utils/class.PdoMonSite.inc.php");

if(!isset($_REQUEST['uc']))
     $uc = 'accueil';
else
	$uc = $_REQUEST['uc'];

$pdo = PdoMonSite::getPdoMonSite();	 
switch($uc)
{
	case 'accueil':
		{include("vues/v_accueil.php");break;}
	case 'completeFiche':
		{include("controleurs/c_FicheEvaluation.php");break;}
}

?>

