<?php 
include 'requette.php';
$role="Responsable";
if(isset($_GET['id_visiteur'])){
	//si parametre matricule
	$matricule = $_GET['id_visiteur'];
	if(isset($_GET['pwd'])){
		//parmetre password
		$mdp = $_GET['pwd'];
		echo connectionVisiteur($matricule,$mdp,$db);
	}
	else{
			echo infoVisiteur($matricule,$db);
	}
	
}
elseif(isset($_GET['list'])){
	if (isset($_GET['region'])){
		$region = $_GET['region'];
		echo getVisiteurByRegion($role,$region,$db);

	}
	elseif(isset($_GET['secteur'])){
		$secteur=$_GET['secteur'];
		echo getVisiteurBySecteur($role,$secteur,$db);

	}
	else{
		echo listVisiteur($role,$db);

	}
}
else{
	echo "ca marche pas ";
}
?>