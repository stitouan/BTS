<?php 
include 'requette.php';

// $laListMedecin=listMedecin($db)
// var_dump($laListMedecin);
if (isset($_GET['id_medecin'])){
	$idMedecin = $_GET['id_medecin'];
	echo getMedecinById($idMedecin,$db);

}
elseif(isset($_GET['list'])){ // list
	echo(listMedecin($db));
	}
 
 ?>