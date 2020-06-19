<?php 
include 'requette.php';

if(isset($_GET['list'])){
	echo listMedicament($db);

	

}
elseif (isset($_GET['id_medicament'])) {
	$idMedicament= $_GET['id_medicament'];
	echo recupMedicamentById($idMedicament,$db);
}



 ?>