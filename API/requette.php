<?php 
function dbLink(){
	//connection a la bdd
		$db = 0;
        $ip = 'localhost';
        $dbname = 'BTS';
        $username = 'aymnms';
        $password = 'weshalors';
		try
		{
			$db = new PDO('mysql:host = ' . $ip . ';dbname=' . $dbname,$username,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
		}
		catch(PDOException $e)
		{
			die('error : ' . $e->getMessage());
		}
		return $db;
	}

function getMedecin ($id,$db){
	$getMedecin_request = "SELECT * FROM PRATICIEN INNER JOIN TYPE_PRATICIEN ON PRATICIEN.TYP_CODE = TYPE_PRATICIEN.TYP_CODE WHERE PRA_NUM ='".$id."' ";
	$getMedecin_db_request = $db->query($getMedecin_request);
	$getMedecin_db_result = $getMedecin_db_request->fetch();
	$getMedecin_detail = array(
				'id' => $getMedecin_db_result['PRA_NUM'],
				'nom' => utf8_encode($getMedecin_db_result['PRA_NOM']),
				'prenom' => utf8_encode($getMedecin_db_result['PRA_PRENOM']),
				'adresse' => utf8_encode($getMedecin_db_result['PRA_ADRESSE']),
				'cp' => $getMedecin_db_result['PRA_CP'],
				'ville'=> utf8_encode($getMedecin_db_result['PRA_VILLE']),
				'coef' => $getMedecin_db_result['PRA_COEFNOTORIETE'],
				'type_praticien' => utf8_encode($getMedecin_db_result['TYP_LIBELLE']),
				'type_lieu' => utf8_encode($getMedecin_db_result['TYP_LIEU'])

			);
	return $getMedecin_detail;


}
function listMedecin($db){
	//renvoi la liste de tout les medecins 
	$praticien_request = "SELECT PRA_NUM AS idMedecin FROM PRATICIEN  ";
	$praticien_db_request = $db->query($praticien_request);
	$praticien_db_result = $praticien_db_request->fetchAll();

	$json['response'] = 'OK';
	$json['listMedecin'] = array();
	foreach ($praticien_db_result as $praticien ) {
		$MedecinInfo = getMedecin($praticien['idMedecin'],$db);
		array_push($json['listMedecin'],$MedecinInfo);

			}
	
	return json_encode($json);

}
function getMedecinById($idMedecin,$db){

	
	$json['response']="OK";
	$json['listMedecin']=array();
	$jsonDetailMedecin=getMedecin($idMedecin,$db);
	array_push($json['listMedecin'],$jsonDetailMedecin);
	return json_encode($json);
}
function connectionVisiteur($matricule,$mdp,$db){
	// permet de verifier si le visiteur a rentrer les bonne info de connection
	$connection_request= 'SELECT VIS_PASSWORD FROM VISITEUR WHERE VIS_MATRICULE="'.$matricule.'"';
	$connection_db_request= $db->query($connection_request);
	$connection_db_result = $connection_db_request->fetch();
	$json['response']='OK';
	if ($connection_db_result[0]==$mdp){
		$json['connexion'] = true ;
	}
	else{
		$json['connexion']= false ;
	}

	return json_encode($json);
}
function getVisiteur($matricule,$db){
	$getVisiteur_request="SELECT * FROM VISITEUR INNER JOIN TRAVAILLER ON VISITEUR.VIS_MATRICULE = TRAVAILLER.VIS_MATRICULE INNER JOIN REGION ON TRAVAILLER.REG_CODE=REGION.REG_CODE INNER JOIN SECTEUR ON REGION.SEC_CODE=SECTEUR.SEC_CODE INNER JOIN LABO ON VISITEUR.LAB_CODE = LABO.LAB_CODE WHERE VISITEUR.VIS_MATRICULE='".$matricule."'";
	$getVisiteur_db_request = $db->query($getVisiteur_request);
	$getVisiteur_db_result = $getVisiteur_db_request->fetch();
	$jsonDetails = array(
			'id' => utf8_encode($getVisiteur_db_result['VIS_MATRICULE']),
			'nom' => utf8_encode($getVisiteur_db_result['VIS_NOM']),
			'prenom' => utf8_encode($getVisiteur_db_result['VIS_PRENOM']),
			'adresse' => utf8_encode($getVisiteur_db_result['VIS_ADRESSE']),
			'cp' => $getVisiteur_db_result['VIS_CP'],
			'ville' => utf8_encode($getVisiteur_db_result['VIS_VILLE']),
			'date_embauche' => $getVisiteur_db_result['VIS_DATEEMBAUCHE'],
			'role' => utf8_encode($getVisiteur_db_result['TRA_ROLE']),
			'region' => utf8_encode($getVisiteur_db_result['REG_NOM']),
			'secteur' => utf8_encode($getVisiteur_db_result['SEC_LIBELLE']),
			'labo' => utf8_encode($getVisiteur_db_result['LAB_NOM'])
			
		);
	return $jsonDetails;

}
function infoVisiteur($matricule,$db){
	// renvoie les info sur un visiteur en fonction de son matricule
	$json['response'] = 'OK';
	$json['listVisiteur']=array();
	$infoVisiteur=getVisiteur($matricule,$db);

	array_push($json['listVisiteur'],$infoVisiteur);
	return json_encode($json);
}
function listVisiteur($role,$db){

	//renvoi la liste des visiteur en fonction de si ils sont visiteur delegue ou responsable 
	$list_Visiteur_request = "SELECT VISITEUR.VIS_MATRICULE AS matricule FROM VISITEUR INNER JOIN TRAVAILLER ON VISITEUR.VIS_MATRICULE = TRAVAILLER.VIS_MATRICULE  WHERE TRAVAILLER.TRA_ROLE='".$role."'";
	$list_Visiteur_db_request = $db->query($list_Visiteur_request);
	$list_Visiteur_db_result = $list_Visiteur_db_request->fetchAll();
	$json['response'] = 'OK';
	$json['listVisiteur'] = array();
	foreach ($list_Visiteur_db_result as $Visiteur ) {

		$infoVisiteur=getVisiteur($Visiteur['matricule'],$db);
		
		array_push($json['listVisiteur'], $infoVisiteur);
	}
	
	return json_encode($json);


}
function getVisiteurByRegion($role,$region,$db){

	//renvoie la liste des visiteur en fonction de leurs region 
	$region_request = "SELECT VISITEUR.VIS_MATRICULE AS matricule FROM VISITEUR INNER JOIN TRAVAILLER ON VISITEUR.VIS_MATRICULE = TRAVAILLER.VIS_MATRICULE INNER JOIN REGION ON TRAVAILLER.REG_CODE=REGION.REG_CODE  WHERE REGION.REG_NOM=".$region." AND TRAVAILLER.TRA_ROLE='".$role."'";
	$region_db_request = $db->query($region_request);
	$region_db_result = $region_db_request->fetchAll();
	$json['response'] = 'OK';
	$json['listVisiteur'] = array();
	foreach ($region_db_result as $Visiteur) {
		$infoVisiteur=getVisiteur($Visiteur['matricule'],$db);
		array_push($json['listVisiteur'], $infoVisiteur);
		
	}
	
	
	return json_encode($json);
		
	}
function getVisiteurBySecteur($role,$secteur,$db){

	//renvoi la liste des visiteurs en fonction de leurs secteur 
	$visiteur_secteur_request = "SELECT VISITEUR.VIS_MATRICULE as matricule FROM VISITEUR INNER JOIN TRAVAILLER ON VISITEUR.VIS_MATRICULE = TRAVAILLER.VIS_MATRICULE INNER JOIN REGION ON TRAVAILLER.REG_CODE=REGION.REG_CODE INNER JOIN SECTEUR ON REGION.SEC_CODE=SECTEUR.SEC_CODE  WHERE SECTEUR.SEC_LIBELLE=".$secteur." AND TRAVAILLER.TRA_ROLE='".$role."'";
	$visiteur_secteur_db_request = $db->query($visiteur_secteur_request);
	$visiteur_secteur_db_result = $visiteur_secteur_db_request->fetchAll();
	$json['response'] = 'OK';
	$json['listVisiteur'] = array();
	foreach ($visiteur_secteur_db_result as $Visiteur) {
		$infoVisiteur=getVisiteur($Visiteur['matricule'],$db);
		array_push($json['listVisiteur'], $infoVisiteur);
		
	}
	
	
	return json_encode($json);
		
}
function getMedicament($id,$db){
	$medicament_request = "SELECT * FROM  MEDICAMENT INNER JOIN PRIX_MEDICAMENT ON MEDICAMENT.MED_DEPOTLEGAL=PRIX_MEDICAMENT.MED_DEPOTLEGAL INNER JOIN FAMILLE ON MEDICAMENT.FAM_CODE=FAMILLE.FAM_CODE WHERE MEDICAMENT.MED_DEPOTLEGAL='".$id."'";
	$medicament_db_request = $db->query($medicament_request);
	$medicament_db_result = $medicament_db_request->fetch();
	$leMedicament= array(
			'id' => utf8_encode($medicament_db_result['MED_DEPOTLEGAL']),
			'nom' => utf8_encode($medicament_db_result['MED_NOMCOMMERCIAL']),
			'famille' => utf8_encode($medicament_db_result['FAM_LIBELLE']),
			'composition' => utf8_encode($medicament_db_result['MED_COMPOSITION']),
			'effets' => utf8_encode($medicament_db_result['MED_EFFETS']),
			'contreindication'=>utf8_encode($medicament_db_result['MED_CONTREINDIC']),
			'prix'=>utf8_encode($medicament_db_result['PRIX'] )
		);
	return $leMedicament;

}
function listMedicament($db){
	//list tout les medicaments 
	$list_medicament_request = "SELECT MED_DEPOTLEGAL AS idMedicament FROM MEDICAMENT ";
	$list_medicament_db_request = $db->query($list_medicament_request);
	$list_medicament_db_result = $list_medicament_db_request->fetchAll();
	$json['response']='OK';
	$json['listMedicament']=array();
	foreach ($list_medicament_db_result as $medicament) {
		$leMedicament=getMedicament($medicament["idMedicament"],$db);
		array_push($json['listMedicament'],$leMedicament );
	}
	return json_encode($json);

}
function recupMedicamentById($idMedicament,$db){
	$json['response']='OK';
	$json['listMedicament']=array();
	$leMedicament=getMedicament($idMedicament,$db);
	array_push($json['listMedicament'],$leMedicament );
	return json_encode($json);

}


function getCR($idCR,$db){
	//renvoie un rapport de visite en fonction de son id 

	$list_cr_request = "SELECT * FROM RAPPORT_VISITE  WHERE RAP_NUM='".$idCR."'";
	$list_cr_db_request = $db->query($list_cr_request);
	$Cr = $list_cr_db_request->fetch();
	
	$date= array();
	$json_detail_date=array(
		'date_creation' => utf8_encode($Cr['RAP_DATE']),
		'date_modif' => utf8_encode($Cr['RAP_MODIF'])
	);
	array_push($date, $json_detail_date);
	$visiteur =getVisiteur($Cr['VIS_MATRICULE'],$db);
	$medecin = getMedecin($Cr['PRA_NUM'],$db);
	$Medicament= array();
	$list_medicament_request = "SELECT MED_DEPOTLEGAL AS idMedicament FROM PRESENTER WHERE RAP_NUM='".$idCR."'";
	$list_medicament_db_request = $db->query($list_medicament_request);
	$list_medicament_db_result = $list_medicament_db_request->fetchAll();
	foreach ($list_medicament_db_result as $medicament) {
		$leMedicament=getMedicament($medicament["idMedicament"],$db);
		array_push($Medicament,$leMedicament );
	}
	$Echantillons= array();
	$list_echantillons_request = "SELECT MED_DEPOTLEGAL AS idMedicament , OFF_QTE AS quantite FROM OFFRIR WHERE RAP_NUM='".$idCR."'";
	$list_echantillons_db_request = $db->query($list_echantillons_request);
	$list_echantillons_db_result = $list_echantillons_db_request->fetchAll();
	foreach ($list_echantillons_db_result as $echantillons) {
		$leMedicament=getMedicament($echantillons['idMedicament'],$db);

		$unEchantillon= array(
			'quantite'=> $echantillons['quantite'],
			'medicament'=> $leMedicament
			);
		
		array_push($Echantillons,$unEchantillon );
	}
	$json_detail= array(
		'id' => utf8_encode($Cr['RAP_NUM']),
		'motif' => utf8_encode($Cr['RAP_MOTIF']),
		'bilan' => utf8_encode($Cr['RAP_BILAN']),
		'date' => $date,
		'visiteur' => $visiteur,
		'medecin' => $medecin,
		'medicament'=> $Medicament,
		'echantillons'=>$Echantillons			
	);
	
	
	
	return $json_detail;

}
function listCRPraticien($idPraticien,$db){
	// renvoi la liste des rapport de visite d'un praticien 
	$CRPraticien_request="SELECT PRA_NUM AS idCR FROM RAPPORT_VISITE  WHERE PRA_NUM ='".$idPraticien."'";
	$CRPraticien_db_request = $db->query($CRPraticien_request);
	$CRPraticien_db_result = $CRPraticien_db_request->fetchAll();
	$json['response']='OK';
	$json['listCompteRendu']=array();
	foreach ($CRPraticien_db_result as $idRap) {
		$Cr=getCR($idRap['idCR'],$db);
		array_push($json['listCompteRendu'],$Cr);
		
	}
	return json_encode($json);

}

function listCRVisiteur($idVisiteur,$db){

	//renvoie les rapport de visite d'un visiteur 
	$CRVisiteur_request="SELECT RAP_NUM AS idCR FROM RAPPORT_VISITE WHERE VIS_MATRICULE ='".$idVisiteur."'";
	$CRVisiteur_db_request = $db->query($CRVisiteur_request);
	$CRVisiteur_db_result = $CRVisiteur_db_request->fetchAll();
	var_dump($CRVisiteur_db_result);
	$json['response']='OK';
	$json['listCompteRendu']=array();
	foreach ($CRVisiteur_db_result as $idRap ) {
		$Cr=getCR($idRap['idCR'],$db);
		array_push($json['listCompteRendu'],$Cr);
	}
	return json_encode($json);
	

}
function recupCRById($idCR,$db)
{
	$json['response']='OK';
	$json['listCompteRendu']=array();
	$Cr=getCR($idCR,$db);
	array_push($json['listCompteRendu'], $Cr);
	return json_encode($json);
}
function listCRByDate($dateCR,$db){

	// liste des rapport de visite en fonction de la date 
	$CRDate_request="SELECT RAP_NUM FROM RAPPORT_VISITE  WHERE RAP_DATE ='".$dateCR."'";
	$CRDate_db_request = $db->query($CRDate_request);
	$CRDate_db_result = $CRDate_db_request->fetchAll();
	$json['response']='OK';
	$json['listCompteRendu']=array();
	foreach ($CRDate_db_result as $idRapByDate ) {
		$Cr=getCR($idRapByDate['RAP_NUM'],$db);
		array_push($json['listCompteRendu'],$Cr);
	}
	return json_encode($json);

}
function listCR($db){
	$list_cr_request = "SELECT RAP_NUM AS idCR FROM RAPPORT_VISITE ";
	$list_cr_db_request = $db->query($list_cr_request);
	$list_cr_db_result = $list_cr_db_request->fetchAll();

	$json['response'] = 'OK';
	$json['listCompteRendu'] = array();
	foreach ($list_cr_db_result as $listIdRap) {
		$Cr=getCR($listIdRap['idCR'],$db);
		array_push($json['listCompteRendu'],$Cr);	
	}
	return json_encode($json);
	

}
function testSaveRapport(){
	$json['response']='OK';
	$medicament=array();
	$medicament.add("AMOX45");
	$medicament.add("DIMIRTAM6");
	$echantillons=array();
	
	$json['newCompteRendu']= array(
		"date_creation"=>"yes",
		"date_visite"=>"2003-05-21",
		"motif"=>"faut bien faire un motif pour pouvoir tester que ca marche frero ",
		"coef_confience"=>"123,43",
		"bilan"=>"un petit bilan bien sympatoche ",
		"id_visiteur"=> "b16",
		"id_medecin"=>"68",
		"medicament"=>$medicament,
		"echantillons"=>$echantillons
					
	);
	
	
	
	return json_encode($json_detail);

}
function saveRapport($db,$cr){
	//enregistre un rapport de visite 
	$saveRapport_request="INSERT INTO RAPPORT_VISITE(`VIS_MATRICULE`, `RAP_NUM`, `PRA_NUM`, `RAP_DATE`, `RAP_BILAN`, `RAP_MOTIF`) VALUES ('".$cr["CR"]["idVisiteur"]."','".$cr["CR"]["idRapport"]."','".$cr["CR"]["idPraticien"]."','".$cr["CR"]["dateRapport"]. "','".$cr["CR"]["bilanRapport"]."','".$cr["CR"]["motifRapport"]."')";
	echo ($saveRapport_request);
	$saveRapport_db_request = $db->prepare($saveRapport_request);
	$saveRapport_db_request->execute();
	$getIdMed_request = "SELECT MED_DEPOTLEGAL FROM MEDICAMENT WHERE MED_NOMCOMMERCIAL = '".$cr["cr"]["nomMedicament"]."'";
	$getIdMed_db_request = $db->query($CRDate_request);
	$getIdMed_db_result = $CRDate_db_request->fetch();
	$saveOffrir_request= " INSERT INTO OFFRIR (`VIS_MATRICULE`,`RAP_NUM`,`MED_DEPOTLEGAL`,`OFF_QTE`) VALUES ('".$cr["CR"]["idVisiteur"]."','".$cr["CR"]["idRapport"]."','".$getIdMed_db_result['MED_DEPOTLEGAL']."','".$cr["CR"]["quantiteMedicament"]."')";
	$saveOffrir_db_request = $db->prepare($saveOffrir_request);
	$saveOffrir_db_request->execute();

}
function erreur($message){
	$json['response']='ERROR';
	$json['error']=$message ;
	return json_encode($json);

}

$db=dbLink();

 ?>