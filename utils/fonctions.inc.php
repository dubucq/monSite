<?php

//fonction qui test si la variable session est vide et si c'est le cas elle renvoit à la page connexion
function testConnexion()
{
	if(empty($_SESSION))
	{
		header("Location: index.php?uc=connexion&action=connexion");
	}
}

?>

