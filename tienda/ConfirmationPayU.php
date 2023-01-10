<?php 
header('Access-Control-Allow-Origin: *');

session_start();
include("./pass.php");
include("./ini.php");
include("./Functions.php");
include("./history_units.php");

	$connection = connection_db();

 	//$ApiKey = "4Vj8eK4rloUd272L48hsrarnUA";//APIKEY DE PRUEBA
    $ApiKey = "VNR7MrgaIKuQtavUovj9s7Z8AO";//APIKEY MORINGA
	$merchant_id = $_REQUEST['merchant_id'];
	$referenceCode = $_REQUEST['reference_sale'];
	$txtValue = $_REQUEST['value'];
	$newValue = number_format($txtValue, 1, '.', '');
	$currency = $_REQUEST['currency'];
	$statePol = $_REQUEST['state_pol'];
	$sign = $_REQUEST['sign'];
	$estadoTxt = 'Firma no concuerda';

	$firma = "$ApiKey~$merchant_id~$referenceCode~$newValue~$currency~$statePol";
	$firmaMd5 = md5($firma);

// if($firmaMd5 === $sign){

	if ($statePol == 4 ) {
		$Query_Update = 'UPDATE '. $p3_t1_bills .' SET State = "Active", Comment_Cancel = "" WHERE Id = '.$referenceCode;
// 		die($Query_Update);
		$Qry_Update = mysqli_query($connection, $Query_Update);

		$Query_Bill = 'SELECT CUNI FROM '. $p3_t1_bills .' WHERE Id = '.$referenceCode;
// 		die($Query_Bill);
		$Qry_Bill = mysqli_query($connection, $Query_Bill);
		
		$Row_Bill = mysqli_fetch_assoc($Qry_Bill);

		Checked_Bill_History($Row_Bill['CUNI']);

	    $estadoTx = "Transacción aprobada";
	}else if ($statePol == 6 ) {
		$estadoTx = "Transacción rechazada";
	}else {
		$estadoTx=$_POST['mensaje'];
	}
	echo $estadoTxt;

// }else{
// 	echo $estadoTxt;
// 	die();
// }


?>
