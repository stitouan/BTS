<?php
	include "requette.php"; 
	$getCr= file_get_contents("https://bts.aymerickmichelet.fr/compteRendu?idCR=7");
	$cr = json_decode($getCr,true);
	var_dump( $cr);
	saveRapport($db,$cr);
?>