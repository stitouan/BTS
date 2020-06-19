<?php 
include 'requette.php';

if(isset($_GET['list'])){

	if (isset($_GET['idPraticien'])) {

		$idPraticien=$_GET['idPraticien'];

		echo listCRPraticien($idPraticien,$db);

	}
	elseif ( isset($_GET['idVisiteur'])){

		$idVisiteur=$_GET ['idVisiteur'];
		echo listCRVisiteur($idVisiteur,$db);

	}
	elseif (isset($_GET['date'])) {

		$dateCR= $_GET['date'];
		echo listCRByDate($dateCR,$db);
	}
	else{
		echo listCR($db);
	}

}
elseif (isset($_GET['idCR'])) {
	$idCR= $_GET['idCR'];
	echo recupCRById($idCR,$db);
}



 ?>