<?php
// if ($pass !== "DROI") {
// 	die(";)");
// }

// Config sistem
$prefix = "001_droi_";
$GC = "1";

date_default_timezone_set('America/Bogota');

include("data_system.php");

if(isset($_SESSION['lock'])){
	if($_SESSION['lock']=="all"){
		die('(/)
			<script>
					console.log("Este módulo es de uso administrativo, no deberías de estar aquí, realizaremos registro de intento de acceso no permitido.");
					window.location="../Gen_Login/processes/unlog.php";
					document.body.innerHTML = "";
			</script>');//Si no hay modulo redirecciona
	}
}

// SERVIDOR DE RED LOCAL
// $db_server="192.168.100.188:3306";
// $db_user="publicgesadmin";
// $db_password="12345";
// $db_name="gesadmin_abastos";

// SERVIDOR LOCAL

// $db_name = "gesadmin_cabecitas"; //Temporal
//

// DROI
// $db_server="http://160.153.197.204:3306";
// $db_user="droi_master";
// $db_password="admin_droi!";
// $db_name="gesadmin";

// GESADMIN
// $db_server="localhost";
// $db_user="droi_master";
// $db_password="admin_droi!";
// $db_name="gesadmin_";

$db_server = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "gesadmin_moringa";

function connection_db(){
	global $db_server,$db_user,$db_password,$db_name;

	$connection_db = @mysqli_connect($db_server,$db_user, $db_password);
	mysqli_set_charset($connection_db,"'UTF8'");
	mysqli_select_db($connection_db, $db_name);
	mysqli_query($connection_db,"SET NAMES 'UTF8'");

	return $connection_db;
}

function get_valiable_connection($variable){
	global $db_server,$db_user,$db_password,$db_name;

	switch ($variable) {
		case 'db_server':
			return $db_server;
			break;
		case 'db_user':
			return $db_user;
			break;
		case 'db_password':
			return $db_password;
			break;
		case 'db_name':
			return $db_name;
			break;
	}
}

//Quita acentos
	function RemoveAccents($cadena){
		$originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ
	ßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
		$modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuy
	bsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
		$cadena = utf8_decode($cadena);
		$cadena = strtr($cadena, utf8_decode($originales), $modificadas);
		$cadena = strtolower($cadena);
		$cadena = ucfirst($cadena);
		$cadena = str_replace("&lt;br&gt;","",$cadena);
		$cadena = str_replace("&lt;","",$cadena);
		$cadena = str_replace("&gt;","",$cadena);

		return utf8_encode($cadena);
	}

function CleanTextUrl ($s){
	$s = preg_replace("/[äáàâãª]/","a",$s);
	$s = preg_replace("/[ÄÁÀÂÃ]/","A",$s);
	$s = preg_replace("/[ÏÍÌÎ]/","I",$s);
	$s = preg_replace("/[ïíìî]/","i",$s);
	$s = preg_replace("/[ëéèê]/","e",$s);
	$s = preg_replace("/[ËÉÈÊ]/","E",$s);
	$s = preg_replace("/[öóòôõº]/","o",$s);
	$s = preg_replace("/[ÖÓÒÔÕ]/","O",$s);
	$s = preg_replace("/[üúùû]/","u",$s);
	$s = preg_replace("/[ÜÚÙÛ]/","U",$s);
	$s = preg_replace("/[çÇ]/","c",$s);
	$s = preg_replace("/[ñÑ]/","n",$s);
	$s = preg_replace ("[()¿?!¡/_´'&,:-=+#.;%@]","",$s);
	$s = str_replace('"',"",$s);
	$s = str_replace('[',"",$s);
	$s = str_replace(']',"",$s);
	$s = str_replace("\\","",$s);
	$s = strtolower(str_replace(" ","-", $s));
	return $s;
}


// Sanea Datos
	function Validate_Data($data) {
		$data = addslashes($data);
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

//Cierra eqtitas
function closetags($html) {
	preg_match_all('#< (?!meta|img|br|hr|input\b)\b([az]+)(?: .*)?(?#iU', $html, $result);
	$openedtags = $result[1];
	preg_match_all('##iU', $html, $result);
	$closedtags = $result[1];
	$len_opened = count($openedtags);
	if (count($closedtags) == $len_opened) { return $html;
	}
	 	$openedtags = array_reverse($openedtags);
	for ($i=0;		$i < $len_opened;		$i++) {
	 	if (!in_array($openedtags[$i], $closedtags)) {
			$html .= '';
		}
		 	else { unset($closedtags[array_search($openedtags[$i], $closedtags)]);
		}
 	} return $html;
}

// Senar texto
	function CleanTextSimple($Text){
		$Text = strip_tags($Text);
		$Text = htmlspecialchars($Text);
		$Text = addslashes($Text);
		return $Text;
	}

// Limpia texto para hacer appends
	function CleanTextForAppend($Text){
		$Text = preg_replace('[\n|\r|\n\r]', '', $Text);
		return $Text;
	}

// Limites de texto
	function RemoveText($Text, $Limit){
		$Text = strip_tags($Text); //Elimina etiquetas
		$Count = strlen($Text); //Cuenta caracteres
		if ($Count > $Limit){
			$Text = mb_substr($Text, 0, $Limit,'UTF-8'); //Elimina hasta el numero dado
			//$Text .="[...]";
		}
		$Text = RemoveAccents($Text);
		return $Text;
	}

/* Fecha en español */
function fecha($unix){
	switch(date("F",$unix)){
		case "January":
			$mes = "Enero";
		break;
		case "February":
			$mes = "Febrero";
		break;
		case "March":
			$mes = "Marzo";
		break;
		case "April":
			$mes = "Abril";
		break;
		case "May":
			$mes = "Mayo";
		break;
		case "June":
			$mes = "Junio";
		break;
		case "July":
			$mes = "Julio";
		break;
		case "August":
			$mes = "Agosto";
		break;
		case "September":
			$mes = "Septiembre";
		break;
		case "October":
			$mes = "Octubre";
		break;
		case "November":
			$mes = "Noviembre";
		break;
		case "December":
			$mes = "Diciembre";
		break;
	}

	$num=strlen($mes);
	$nuevo = "";
	for($a=0; $a<$num; $a++){
		$nuevo .= "\\".$mes[$a];
	}
	/*$fecha = $nuevo;*/
	$fecha = date('d \d\e '.$nuevo.' \d\e\l Y',$unix);

	return $fecha;
}

function BirthDay($DateBirthDay){
	$DateBirthDay = explode("/",$DateBirthDay);
	$dia = $DateBirthDay[0];
	$mes = $DateBirthDay[1];
	if ($dia==0&&$mes==0) {
		return("N/A");
	}
	switch($mes)
	{
	    case'01':
			$mes='Enero';
		break;

	    case'02':
			$mes='Febrero';
		break;

	    case'03':
			$mes='Marzo';
		break;

	    case'04':
			$mes='Abril';
		break;

	    case'05':
			$mes='Mayo';
		break;

	    case'06':
			$mes='Junio';
		break;

	    case'07':
			$mes='Julio';
		break;

	    case'08':
			$mes='Agosto';
		break;

	    case'09':
			$mes='Septiembre';
		break;

	    case'10':
			$mes='Octubre';
		break;

		 case'11':
			$mes='Noviembre';
		break;

		 case'12':
			$mes='Diciembre';
		break;
	}
	return($dia." de ".$mes);
}

/* Formateo de imagenes */
	$typesImages = array("image/gif","image/jpeg","image/jpg","image/png","image/GIF","image/JPEG","image/JPG","image/PNG");
	$maxSizeImage = 5242880; // 5 mb

	//Formateo General
	function formatting_basic($Name_img){
		$ext = strstr($Name_img, '.');
		$Name_img = date('U')."_".rand(0,10000).$ext;

		return $Name_img;
	}

	//Formateo con numero de documento
	function FormattingWithDocument($Name_img ,$NumberDocument){
		$ext = strstr($Name_img, '.');
		$Name_img = $NumberDocument.$ext;

		return $Name_img;
	}

	 function inicio_fin_semana($fecha){

    $diaInicio="Monday";
    $diaFin="Sunday";

    $strFecha = strtotime($fecha);

    $fechaInicio = date('Y-m-d',strtotime('last '.$diaInicio,$strFecha));
    $fechaFin = date('Y-m-d',strtotime('next '.$diaFin,$strFecha));

    if(date("l",$strFecha)==$diaInicio){
        $fechaInicio= date("Y-m-d",$strFecha);
    }
    if(date("l",$strFecha)==$diaFin){
        $fechaFin= date("Y-m-d",$strFecha);
    }
    return Array("fechaInicio"=>$fechaInicio,"fechaFin"=>$fechaFin);
	}

	function clean_texts($text){
		$text = str_replace("'","\'",$text);
		return $text;

	}

	function Get_Warehouse_Assigned($Id_Business){

		global $p0_t2_warehouse_c1_assign;

		$connection = connection_db();

		// se consultan las bodegas de la empresa
		$ConsultWare = 'SELECT Id_Warehouse FROM '.$p0_t2_warehouse_c1_assign.' WHERE Id_Business='.$Id_Business;
		// die($ConsultWare);
		$Qry_Ware = mysqli_query($connection, $ConsultWare);

		// si la empresa tiene mas de dos bodegas asignadas retornara falso
		if(mysqli_num_rows($Qry_Ware) > 1){
			return false;
		}else{
			$Row_Ware = mysqli_fetch_assoc($Qry_Ware);
			return $Row_Ware['Id_Warehouse'];
		}

	}


	function Update_Units_Relation($Id_Product,$Id_WareHouse,$Table_Relation,$Units){
		$connection = connection_db();
		$Qry='UPDATE '.$Table_Relation.' SET Unit = Unit-'.$Units.' WHERE Id_Warehouse='.$Id_WareHouse.' AND Id_Inventory='.$Id_Product;
		die($Qry);
		$Qry_Relation = mysqli_query($connection,$Qry);
		return $Qry_Relation;
	}

	function deleteDirectory($dir) {
		// echo $dir.'<br />';
    if(!$dh = @opendir($dir)) return;
    while (false !== ($current = readdir($dh))) {
        if($current != '.' && $current != '..') {
            //echo 'Se ha borrado el archivo '.$dir.'/'.$current.'<br/>';
            if (!@unlink($dir.'/'.$current))
                deleteDirectory($dir.'/'.$current);
        }
    }
    closedir($dh);
    //echo 'Se ha borrado el directorio '.$dir.'<br/>';
    @rmdir($dir);
	}

	function Active_Type_Licence($Type){
		global $connection, $m1_s2_modules_lv2, $m1_s1_modules_lv1, $p0_t1_config_business;
		//A continuacion se declaran los arreglos que contienen los modulos que se desean activar, ordenados por el tipo de licencia, si se deseaq modificar solo hay que modificar los codigos de cada uno de los modulos en los arreglos correspondientes

		if ($Type=='Basic') {//licencia basica
			$Modules= array(
				'Level_1' => array (
					'101010000001', // P0_Config_Modules
					'101010000002', // P0_Config_Users
					'101010000003', // P0_Profile_User
					'101010000009', // Facturación
					'101010000013', // P2_Clients
					'101010000015', // P1_Inventory
					'101010000017', // Configuración
					'101010000020', // P5_Report_Closed_Box
					'101010000021', // TOUCH
					'101010000027', // P3_Opening_Box
					'101010000032', // P0_Segurity_Log
					'101010000034', // P16_Backup
					'101010000050', // Facturación Electrónica
					'101010000051', // Mi Informacion
					'101010000055', // Covid
					'101010000063', // P35_Menu
					'101010000065', // P36_Events_General
				),

				'Level_2' => array(
					'4',  // P3_Bill_Table
					'5',  // P3_Search_Bills
					'9',  // P0_Config_System
					'21', // P3_Search_Bill_Admin
					'23', // P0_Config_Printers_POS
					'29', // P3_Bill_Touch
					'36', // P0_Config_Licence_Offline
					'59', // P3_Bill_Electronic_Check
					'58', // P0_Load_Information
					'60', // P3_Bill_Electronic_Pending
					'61', // P0_Erase_Information
					'66', // P30_Covid
					'90' // P0_Config_Devices
				)
			);
		}elseif ($Type=='Middle'){//Licencia Intermedia
			$Modules= array(
				'Level_1' => array (
					'101010000001', // P0_Config_Modules
					'101010000002', // P0_Config_Users
					'101010000003', // P0_Profile_User
					'101010000009', // Facturación
					'101010000011', // Comprobantes
					'101010000013', // P2_Clients
					'101010000014', // P8_Provider
					'101010000015', // P1_Inventory
					'101010000017', // Configuración
					'101010000019', // P9_Purchases
					'101010000020', // P5_Report_Closed_Box
					'101010000022', // P1_Inventory_Production
					'101010000021', // TOUCH
					'101010000027', // P3_Opening_Box
					'101010000024', // Cotización
					'101010000032', // P0_Segurity_Log
					'101010000034', // P16_Backup
					'101010000050', // Facturación Electrónica
					'101010000051', // Mi Informacion
					'101010000055', // Covid
					'101010000059', // P3_Devolutions
					'101010000063', // P35_Menu
					'101010000065' // P36_Events_General
				),

				'Level_2' => array(
					'4',  // P3_Bill_Table
					'5',  // P3_Search_Bills
					'6',  // P3_New_Advance
					'7',  // P4_Vouchers_Ingress
					'8',  // P4_Vouchers_Egress
					'9',  // P0_Config_System
					'13', // P6_Quotations
					'14', // P6_Search_Quotations
					'21', // P3_Search_Bill_Admin
					'22', // P3_Asides
					'23', // P0_Config_Printers_POS
					'29', // P3_Bill_Touch
					'36', // P0_Config_Licence_Offline
					'59', // P3_Bill_Electronic_Check
					'58', // P0_Load_Information
					'60', // P3_Bill_Electronic_Pending
					'61', // P0_Erase_Information
					'66', // P30_Covid
					'90' // P0_Config_Devices
				)
			);
		}elseif ($Type=='Complete') {//Licencia Completa
			$Modules= array(
				'Level_1' => array (
					'101010000001', // P0_Config_Modules
					'101010000002', // P0_Config_Users
					'101010000003', // P0_Profile_User
					'101010000009', // Facturación
					'101010000011', // Comprobantes
					'101010000013', // P2_Clients
					'101010000014', // P8_Provider
					'101010000015', // P1_Inventory
					'101010000017', // Configuración
					'101010000019', // Compras
					'101010000020', // P5_Report_Closed_Box
					'101010000021', // TOUCH
					'101010000022', // P1_Inventory_Production
					'101010000024', // Cotización
					'101010000025', // Remisión
					'101010000027', // P3_Opening_Box
					'101010000034', // P16_Backup
					'101010000032', // P0_Segurity_Log
					'101010000035', // Reportes
					'101010000050', // Facturación Electrónica
					'101010000051', // Mi Informacion
					'101010000052', // Notas
					'101010000056', // P31_Document_Support
					'101010000055', // Covid
					'101010000059', // P3_Devolutions
					'101010000060', // P32_Dispatch
					'101010000061', // Orden de compra
					'101010000062', // Orden de servicio
					'101010000063', // P35_Menu
					'101010000065' // P36_Events_General
				),

				'Level_2' => array(
					'4',  // P3_Bill_Table
					'5',  // P3_Search_Bills
					'6',  // P3_New_Advance
					'7',  // P4_Vouchers_Ingress
					'8',  // P4_Vouchers_Egress
					'9',  // P0_Config_System
					'13', // P6_Quotations
					'14', // P6_Search_Quotations
					'15', // P7_Remissions
					'16', // P7_Search_Remissions
					'21', // P3_Search_Bill_Admin
					'23', // P0_Config_Printers_POS
					'29', // P3_Bill_Touch
					'30', // P3_Kitchen_Monitor
					'31', // P3_Portfolio
					'32', // P15_Reports_Sales
					'33', // P15_Reports_Items
					'34', // P15_Reports_General
					'36', // P0_Config_Licence_Offline
					'47', // P26_Delivery_Domicile
					'59', // P3_Bill_Electronic_Check
					'58', // P0_Load_Information
					'60', // P3_Bill_Electronic_Pending
					'61', // P0_Erase_Information
					'62', // P2_Credit_Notes
					'63', // P2_Debit_Notes
					'66', // p30_Covid
					'68', // P3_Reserve
					'69', // P9_Purchases
					'70', // P9_Search_Buy_Admin
					'71', // P9_New_Advance
					'72', // P33_Order_Purchase
					'73', // P33_Search_Order_Purchase
					'74', // P34_Order_Service
					'75', // P34_Search_Order_Service
					'90' // P0_Config_Devices
				)
			);
		}

		$consult_modules_L1 = mysqli_query($connection,'SELECT Module_Code_LV1, Module_Name, Folder_Module FROM '.$m1_s1_modules_lv1.' ');
		while ($row_lv1 = mysqli_fetch_assoc($consult_modules_L1)) {

			$show_L1 = 0;
			if (in_array($row_lv1['Module_Code_LV1'],$Modules['Level_1'])){
				$show_L1 = 1;

			}else{
				if (!empty($row_lv1['Folder_Module'])) {
					// deleteDirectory('../../../modules/'.$row_lv1['Folder_Module']);
				}
			}

			$consult_modules_L2 = mysqli_query($connection,'SELECT Module_Code_LV2, Module_Name, Folder_Module FROM '.$m1_s2_modules_lv2.' WHERE Module_Code_LV1='.$row_lv1['Module_Code_LV1']);
			while ($row_lv2 = mysqli_fetch_assoc($consult_modules_L2)) {
				$show_L2 = 0;

				if (in_array($row_lv2['Module_Code_LV2'],$Modules['Level_2'])){
					$show_L2 = 1;
				}else{
					if (!empty($row_lv2['Folder_Module'])) {
						//deleteDirectory('../../../modules/'.$row_lv2['Folder_Module']);
					}
				}
				$Update_modules_l2 = mysqli_query($connection,'UPDATE '.$m1_s2_modules_lv2.' SET Is_Show='.$show_L2.' WHERE Module_Code_LV2='.$row_lv2['Module_Code_LV2']);
			}

			$Update_modules_l1 = mysqli_query($connection,'UPDATE '.$m1_s1_modules_lv1.' SET Is_Show='.$show_L1.' WHERE Module_Code_LV1='.$row_lv1['Module_Code_LV1']);
		}

		$Update_Licence = mysqli_query($connection,'UPDATE '.$p0_t1_config_business.' SET Licence="'.$Type.'" WHERE Id=1');
		if ($Update_Licence) {
			// echo 'LICENCIA ACTUALIZADA CORRECTAMENTE';
			return 'SE HAN ACTIVADO LOS MODULOS CORRECTAMENTE';
		}

	}




		function Checked_Bill_History_Remission($CUNI, $PRINT=false){
				global $p1_t1_inventory_sale_c2_products_history_units,	$p7_t1_remissions,	$p7_t1_remissions_c1_products, $connection, $p1_c1_relation_inventory, $p1_t1_warehouse_inventory_production, $p1_t2_inventory_production_c2_products_history_units, $p3_t1_bills_c1_products_c1_components, $p1_t1_inventory_sele_c3_components, $p1_t1_inventory_sele,$p1_t1_warehouse_inventory;

				$connection = connection_db();
				// Consulta facturas que hay con el mismo cuni y las ordena por fecha desc y estado comenzado por a active e erased history
				// die('SELECT * FROM '.$p3_t1_bills.' WHERE CUNI = "'.$CUNI.'" ORDER BY Last_Update DESC, State ASC');
				$Consult_Bills = mysqli_query($connection, 'SELECT * FROM '.$p7_t1_remissions.' WHERE CUNI = "'.$CUNI.'" ORDER BY Last_Update DESC, State ASC');
				echo mysqli_error($connection);

				$num = 0;
				while($Row_Bill = mysqli_fetch_assoc($Consult_Bills)){
					$num++;

					if($num==1){

				// Elimina registro undades de venta antiguos
						$Query_History = mysqli_query($connection,'DELETE FROM '.$p1_t1_inventory_sale_c2_products_history_units.' WHERE Id_Method = '.$Row_Bill['Id'].' AND Method="Bill" ');
						echo mysqli_error($connection);

				// Elimina registro undades materia prima antiguos
						$Query_History = mysqli_query($connection,'DELETE FROM '.$p1_t2_inventory_production_c2_products_history_units.' WHERE Id_Method = '.$Row_Bill['Id'].' AND Method="Bill" ');
						echo mysqli_error($connection);


						//Cambia el estado a activado
						if($Row_Bill['State']=='History'){
							$Query_Update = mysqli_query($connection,'UPDATE '.$p7_t1_remissions.' SET State="Active" WHERE Id = '.$Row_Bill['Id'].' ');
							echo mysqli_error($connection);
						}

						//Si se elimina correctamente
							if ($Query_History  && $Row_Bill['State']!='Temporal' && $Row_Bill['State']!='Erased') {
								$Query_BillProducts = mysqli_query($connection,'SELECT BP.Id, BP.Units, BP.Code_Product, BP.Id_Bill, I.Id_Compl_Prod,I.Compl_Value FROM '.$p7_t1_remissions_c1_products.' BP INNER JOIN '.$p1_t1_inventory_sele.' I ON I.Id = BP.Code_Product WHERE BP.Id_Bill = '.$Row_Bill['Id']);
								echo mysqli_error($connection);
								while($Row_BillProducts = mysqli_fetch_assoc($Query_BillProducts)){

									//Obtiene el id warehouse de la factura
												$Id_Warehouse_Bill = Get_Warehouse_Assigned($Row_Bill['Id_Business']);

									//Descuento de unidades normales
												$sql_petition = '
												INSERT INTO '.$p1_t1_inventory_sale_c2_products_history_units.' (Code_Item, Id_Warehouse, Type, Method, Id_Method, Unit, Motive, User_Code, Date)
												VALUES
												(
													'.$Row_BillProducts['Code_Product'].',
													'.$Id_Warehouse_Bill.',
													"Remove" ,
													"Remission" ,
													'.$Row_Bill['Id'].',
													'.$Row_BillProducts['Units'].',
													"[Remisión de '.floatval($Row_BillProducts['Units']).' por remisión '.$Row_Bill['Bill'].'.]" ,
													'.$Row_Bill['User_Code'].' ,
													'.$Row_Bill['Date'].'
												) ';
												$Query_NewHistory = mysqli_query($connection, $sql_petition);

												if (!$Query_NewHistory) {
													echo 'Error 1 -->'.mysqli_error($connection).' / '.$sql_petition.' .<br/>';
												} else {
													if($PRINT){
														echo ' Ok ';
													}
												}

												//Update_Units_Relation($Row_BillProducts['Code_Product'],$Id_Warehouse_Bill,$p1_t1_warehouse_inventory,$Row_BillProducts['Units']);

									//Descuento de unidades de productos complementarios
										if(!empty($Row_BillProducts['Id_Compl_Prod'])){
												$ConsultProduct = mysqli_query($connection, '
												SELECT I.Id, I.Type, I.Compl_Value, I.Product FROM '.$p1_t1_inventory_sele.' I WHERE I.Id='.$Row_BillProducts['Id_Compl_Prod'].' ');
												echo mysqli_error($connection);
												$RowProduct = mysqli_fetch_assoc($ConsultProduct);

													//Inserta movimientos hechos del original
													$query = '
														INSERT INTO '.$p1_t1_inventory_sale_c2_products_history_units.'
															(
																Code_Item,
																Id_Warehouse,
																Type,
																Method,
																Id_Method,
																Unit,
																Motive,
																User_Code,
																Date
															)
														VALUES
															(
																"'.$RowProduct['Id'].'",
																'.$Id_Warehouse_Bill.',
																"Remove",
																"Remission",
																'.$Row_Bill['Id'].',
																'.($Row_BillProducts['Units']*$Row_BillProducts['Compl_Value']).',
																"[PC][Remisión de '.$RowProduct['Product'].' de '.($Row_BillProducts['Units']*$Row_BillProducts['Compl_Value']).' por remisión n° '.$Row_Bill['Bill'].'.] ",
																"'.$Row_Bill['User_Code'].'",
																'.$Row_Bill['Date'].'
															)';
													$InsertHistoryProduct =  mysqli_query($connection, $query);

													if (!$InsertHistoryProduct) {
														echo '<br> Error INSERT en complementarios  -> '.$p1_t1_inventory_sale_c2_products_history_units.' -> '.mysqli_error($connection).'<br />..'.$query;
													}else {
														if($PRINT){
															echo ' OkPC ';
														}
													}
											}


									//Descuento de materia prima

												$results_1 = mysqli_query($connection, "SELECT * FROM ".$p1_c1_relation_inventory." WHERE Code_Product_Sell='".$Row_BillProducts['Code_Product']."' ");    // Tabla 1 de productos
												echo mysqli_error($connection);
												while($row_1 = mysqli_fetch_assoc($results_1)){
														 //Inserta Historia de unidad de produccion
														 $query = '
															INSERT INTO '.$p1_t2_inventory_production_c2_products_history_units.'
																(Id_Warehouse, Code_Item, Type, Motive, User_Code, Date, Method, Id_Method, Unit)
															VALUES
																('.$Id_Warehouse_Bill.', '.$row_1['Code_Product_Producction'].',  "Remove",  "[MP][Remisión de '.$row_1['Quantity']*$Row_BillProducts['Units'].' por remisión n° '.$Row_Bill['Bill'].'.] ",  "'.$Row_Bill['User_Code'].'",  '.$Row_Bill['Date'].', "Remission", '.$Row_Bill['Id'].', '.($row_1['Quantity']*$Row_BillProducts['Units']).' )';
														 $InsertHistoryProduct =  mysqli_query($connection, $query);
														 // $second++;
															if (!$InsertHistoryProduct) {
																	echo '<br>Error INSERT en mp -> '.$p1_t2_inventory_production_c2_products_history_units.' -> '.mysqli_error($connection).'<br />...'.$query;
															}else {
																if($PRINT){
																	echo ' OkMP ';
																}
															}
												 }


										//Descuento de componentes
														$results_1 = mysqli_query($connection, 'SELECT * FROM '.$p3_t1_bills_c1_products_c1_components.' WHERE Id_Bill_Product='.$Row_BillProducts['Id'].' ');
														echo mysqli_error($connection);
														while($row_1 = mysqli_fetch_assoc($results_1)){
																$Qry_Component = mysqli_query($connection,'SELECT Type, Code_Item, Is_Affect, Quantity_Affect FROM '.$p1_t1_inventory_sele_c3_components.' WHERE Code = '.$row_1['Id_Component']);
																echo mysqli_error($connection);
																$Row_Component = mysqli_fetch_assoc($Qry_Component);

																//Verifica que sea un componmenteq ue afecta inventyario
																if ($Row_Component['Is_Affect']==1 && ($Row_Component['Type'] == "Item_Sele" || $Row_Component['Type'] == "Item_Prod" )) {

																		if($Row_Component['Quantity_Affect']>=0){
																			$accion = "Remove";
																			$palabra = "Removió";
																		}else {
																			$accion = "Add";
																			$palabra = "Compenso";
																		}

																		if ( $Row_Component['Type'] == "Item_Sele" ) {

																			$Qry = '
																			INSERT INTO '.$p1_t1_inventory_sale_c2_products_history_units.'
																				(Code_Item, Type, Method, Id_Method, Unit, Motive, User_Code, Date, Id_Warehouse)
																			VALUES
																				('.$Row_Component['Code_Item'].', "'.$accion.'", "Remission", '.$Row_Bill['Id'].', '.abs($Row_Component['Quantity_Affect'])*$Row_BillProducts['Units'].', "[CO]['.$palabra.' '.number_format(abs($Row_Component['Quantity_Affect'])*$Row_BillProducts['Units']).'] Unidad(es) por remisión '.$Row_Bill['Bill'].'", '.$Row_Bill['User_Code'].', '.$Row_Bill['Date'].', '.$Id_Warehouse_Bill.')';

																		} else{

																			$Qry =  '
																				INSERT INTO '.$p1_t2_inventory_production_c2_products_history_units.'
																					(Code_Item, Id_Warehouse, Type, Method, Id_Method, Unit, Motive, User_Code, Date)
																				VALUES
																					('.$Row_Component['Code_Item'].', '.$Id_Warehouse_Bill.', "'.$accion.'", "Remission", '.$Row_Bill['Id'].', '.abs($Row_Component['Quantity_Affect'])*$Row_BillProducts['Units'].', "[CO]['.$palabra.' '.number_format(abs ($Row_Component['Quantity_Affect'])*$Row_BillProducts['Units']).'] Unidad(es) por remisión '.$Row_Bill['Bill'].'", '.$Row_Bill['User_Code'].',  '.$Row_Bill['Date'].')';
																		}
																			// die($Qry);
																		$Qry_History = mysqli_query($connection, $Qry);
																		if (!$Qry_History) {
																				echo 'Error INSERT en tabla -> '.$p1_t2_inventory_production_c2_products_history_units.' -> '.mysqli_error($connection).'<br />...'.$Qry;
																		}else {
																			if($PRINT){
																				echo ' OkCO ';
																			}
																		}
																}
														}
								} //fin producto

						} else if($Row_Bill['State']!='Erased' && $Row_Bill['State']!='Temporal' && $Row_Bill['State']!='History'){
								echo 'Error ->'.mysqli_error($connection).' <br />';
						}
				} else {
						//Elimna registros de units
						$Query_History = mysqli_query($connection,'DELETE FROM '.$p1_t1_inventory_sale_c2_products_history_units.' WHERE Id_Method = '.$Row_Bill['Id'].' AND Method="Bill" ');
						echo mysqli_error($connection);

							//Si se elimina correctamente actualiza su estado a history
								$Query_Update = mysqli_query($connection,'UPDATE '.$p7_t1_remissions.' SET State="History" WHERE Id = '.$Row_Bill['Id'].' ');
								echo mysqli_error($connection);

								if($PRINT){
									echo ' History ';
								}

					}
				}
			}

		//Crea historial individual
		function Checked_Masive_History($CUNI, $PRINT=false, $InventorySale=true , $EXCEL = false){
			if($InventorySale){
				global $p1_t1_inventory_sale_c2_products_history_units,	$p1_t1_inventory_sele_massive_history,	$p1_t1_inventory_sele_massive_history_products, $connection,$p1_t1_warehouse_inventory, $p1_t1_inventory_sele, $p1_t1_warehouse_inventory;
			}else {
				global $p1_t2_inventory_production_c2_products_history_units,$p1_t2_inventory_production,$p1_t1_warehouse_inventory_production,	$p1_t2_inventory_production_massive_history,	$p1_t2_inventory_production_massive_history_products, $connection;

				$p1_t1_inventory_sale_c2_products_history_units = $p1_t2_inventory_production_c2_products_history_units;
				$p1_t1_inventory_sele_massive_history = $p1_t2_inventory_production_massive_history;
				$p1_t1_inventory_sele_massive_history_products = $p1_t2_inventory_production_massive_history_products;

				$p1_t1_warehouse_inventory = $p1_t1_warehouse_inventory_production;
				$p1_t1_inventory_sele = $p1_t2_inventory_production;
			}

				$Query_NewHistory= true;
				$Masive_Id = 0;
				$TipoMes = "";
				$date_massive = 0;
				//Consulta masivos que hay con el mismo cuni y las ordena por fecha desc y estado comenzado por a active e erased history
				$Consult_Bills = mysqli_query($connection, 'SELECT * FROM '.$p1_t1_inventory_sele_massive_history.' WHERE CUNI = "'.$CUNI.'" ORDER BY Last_Update DESC, State ASC');
				echo mysqli_error($connection);

				$num = 0;
				while($Row_Masive = mysqli_fetch_assoc($Consult_Bills)){
					$num++;
					$Masive_Id = $Row_Masive['Id'];
					if($num==1){
						$Query_History = mysqli_query($connection,'DELETE FROM '.$p1_t1_inventory_sale_c2_products_history_units.' WHERE Id_Method = '.$Row_Masive['Id'].' AND (Method="Massive" OR Method="Close") ');
						echo mysqli_error($connection);

						//Cambia el estado a activado
						if($Row_Masive['State']=='History'){
							$Query_Update = mysqli_query($connection,'UPDATE '.$p1_t1_inventory_sele_massive_history.' SET State="Active" WHERE Id = '.$Row_Masive['Id'].' ');
							echo mysqli_error($connection);
						}

						//Si se elimina correctamente
						if ($Query_History  && $Row_Masive['State']!='Temporal' && $Row_Masive['State']!='Erased') {
							$Query_BillProducts = mysqli_query($connection,'SELECT Units, Id_Product, Type, Id_Warehouse FROM '.$p1_t1_inventory_sele_massive_history_products.' WHERE Id_Massive = '.$Row_Masive['Id']);
							echo mysqli_error($connection);
							while($Row_Masive_Products = mysqli_fetch_assoc($Query_BillProducts)){

								//Obtiene el id warehouse de la factura
								// Get_Warehouse_Assigned($Row_Masive['Id_Business'])
								$Id_Warehouse_Bill = $Row_Masive_Products['Id_Warehouse'];

								//Si es cierre entonces recompone unidades
								$date_massive = $Row_Masive['Date_Realiced'];
								$MensajeNormal = '[Masivo '.$Row_Masive_Products['Type'].' de '.floatval($Row_Masive_Products['Units']).' por masivo '.$Row_Masive['CUNI'].'.]';
								if ($EXCEL == true) {
									$MensajeNormal = '[Agregó '.floatval($Row_Masive_Products['Units']).' ] Por masivo de Excel # '.$Row_Masive['Consecutive'];
								}
								if($Row_Masive['Close']==1){
									$RowMethod = $Row_Masive['Method'];
									$TipoMes = "mes";
									if($RowMethod=="Close_day"){
										$TipoMes = "dia";
									}

									$MensajeNormal = '[Corte '.$TipoMes.' con '.floatval($Row_Masive_Products['Units']).' por masivo '.$Row_Masive['CUNI'].'.]';
									 // El cierre de mes se hace el dia anterior para que quede como saldo
									 if($InventorySale){
										 $UnitsOld = Consult_History_Units($Row_Masive_Products['Id_Product'], $date_massive, '', $CUNI);
									 }else {
										 $UnitsOld = Consult_History_Units_Production($Row_Masive_Products['Id_Product'], $date_massive, '', $CUNI);
									 }
										if(empty($Row_Masive['Bring'])){

												if($UnitsOld<0){
													$Ajuste = abs($UnitsOld);
													//INSERT HISTORICO

													$query_relation = 'SELECT Id FROM '.$p1_t1_warehouse_inventory.' WHERE Id_inventory = '.$Row_Masive_Products['Id_Product'].' AND Id_Warehouse = '.$_SESSION['Id_Warehouse'];
                          $qry_relation = mysqli_query($connection , $query_relation);
                          $data_relation = mysqli_fetch_assoc($qry_relation);

													$petition_3 = mysqli_query($connection, "
													INSERT INTO ".$p1_t1_inventory_sale_c2_products_history_units."
													(Code_Item, Id_Warehouse, Type, Method, Id_Method, Unit, Motive, User_Code, Date,Id_Batch)
													VALUES
													(".$Row_Masive_Products['Id_Product'].", ".$Id_Warehouse_Bill.",  'Add',  'Close', ".$Row_Masive['Id'].", ".$Ajuste.", '[Se agrega ".$Ajuste." por ajuste de cierre de ".$TipoMes."]  por masivo ".$Row_Masive['CUNI']." ',  '".$Row_Masive['Id_User']."',  ".$date_massive." , ".$data_relation['Id']." ) ");
														if(!$petition_3){
														    echo 'Error History_01 ->'.mysqli_error($connection);
														}


												}else if($UnitsOld>0){
													$Ajuste = $UnitsOld;
													//INSERT HISTORICO

													$query_relation = 'SELECT Id FROM '.$p1_t1_warehouse_inventory.' WHERE Id_inventory = '.$Row_Masive_Products['Id_Product'].' AND Id_Warehouse = '.$_SESSION['Id_Warehouse'];
													$qry_relation = mysqli_query($connection , $query_relation);
													$data_relation = mysqli_fetch_assoc($qry_relation);

													$Query_3  ="
													INSERT INTO ".$p1_t1_inventory_sale_c2_products_history_units."
													(Code_Item, Id_Warehouse, Type, Method, Id_Method, Unit, Motive, User_Code, Date , Id_Batch)
													VALUES
													(".$Row_Masive_Products['Id_Product'].", ".$Id_Warehouse_Bill.",  'Remove',  'Close', ".$Row_Masive['Id'].", ".$Ajuste.", '[Se remueve ".$Ajuste." por ajuste de cierre de ".$TipoMes."] por masivo ".$Row_Masive['CUNI']." ',  '".$Row_Masive['Id_User']."',  ".$date_massive." , ".$data_relation['Id'].") ";
													$petition_3 = mysqli_query($connection, $Query_3);
													if(!$petition_3){
													    echo 'Error History_02 ->'.mysqli_error($connection);
													}

												}
										} else {

													if($UnitsOld<0){
														$Ajuste = abs($UnitsOld)+$Row_Masive_Products['Units'];
														//INSERT HISTORICO

														$query_relation = 'SELECT Id FROM '.$p1_t1_warehouse_inventory.' WHERE Id_inventory = '.$Row_Masive_Products['Id_Product'].' AND Id_Warehouse = '.$_SESSION['Id_Warehouse'];
														$qry_relation = mysqli_query($connection , $query_relation);
														$data_relation = mysqli_fetch_assoc($qry_relation);

														$petition_3 = mysqli_query($connection, "
														INSERT INTO ".$p1_t1_inventory_sale_c2_products_history_units."
														(Code_Item, Id_Warehouse, Type, Method, Id_Method, Unit, Motive, User_Code, Date,Id_Batch)
														VALUES
														(".$Row_Masive_Products['Id_Product'].", ".$_SESSION['Id_Warehouse'].",  'Add',  'Close', ".$Row_Masive['Id'].", ".$Ajuste.", '[Se agrega (".$Ajuste.") ".abs($UnitsOld)." por ajuste de cierre de ".$TipoMes." e inicia ".$TipoMes." con ".$Row_Masive_Products['Units']."] por masivo ".$Row_Masive['CUNI']." ',  '".$_SESSION['User_Code']."',  ".$date_massive." ,  ".$data_relation['Id'].") ");
															echo  'Error History_03 ->'.mysqli_error($connection);

													}else if($UnitsOld>=0){
														if(($Row_Masive_Products['Units']-$UnitsOld)>=0){
															$Ajuste = abs($Row_Masive_Products['Units']-$UnitsOld);
															//INSERT HISTORICO
															$petition_3 = mysqli_query($connection, "
															INSERT INTO ".$p1_t1_inventory_sale_c2_products_history_units."
															(Code_Item, Id_Warehouse, Type, Method, Id_Method, Unit, Motive, User_Code, Date)
															VALUES
															(".$Row_Masive_Products['Id_Product'].", ".$_SESSION['Id_Warehouse'].",  'Add',  'Close', ".$Row_Masive['Id'].", ".$Ajuste.", '[Se agrega  ".$Ajuste." por ajuste de cierre de ".$TipoMes." e inicia ".$TipoMes." con ".$Row_Masive_Products['Units']."] por masivo ".$Row_Masive['CUNI']." ',  '".$_SESSION['User_Code']."',  ".$date_massive.") ");
																echo 'Error History_04 ->'.mysqli_error($connection);



														}else{
															$Ajuste = abs($Row_Masive_Products['Units']-$UnitsOld);
															//INSERT HISTORICO
															$petition_3 = mysqli_query($connection, "
															INSERT INTO ".$p1_t1_inventory_sale_c2_products_history_units."
															(Code_Item, Id_Warehouse, Type, Method, Id_Method, Unit, Motive, User_Code, Date)
															VALUES
															(".$Row_Masive_Products['Id_Product'].", ".$_SESSION['Id_Warehouse'].",  'Remove',  'Close', ".$Row_Masive['Id'].", ".$Ajuste.", '[Se remueve ".$Ajuste." por ajuste de cierre de ".$TipoMes." e incia con ".$Row_Masive_Products['Units']."] por masivo ".$Row_Masive['CUNI']." ',  '".$_SESSION['User_Code']."',  ".$date_massive.") ");
																echo 'Error History_05 ->'.mysqli_error($connection);
														}

												}
										}


								} // if type close


								$Accion = "Massive";
								if($Row_Masive['Close']==1){
									$Accion = "Close";
								}

								if(empty($Row_Masive['Bring'])){

								        $query_relation = 'SELECT Id FROM '.$p1_t1_warehouse_inventory.' WHERE Id_inventory = '.$Row_Masive_Products['Id_Product'].' AND Id_Warehouse = '.$_SESSION['Id_Warehouse'];
                                        $qry_relation = mysqli_query($connection , $query_relation);
                                        $data_reltaion = mysqli_fetch_assoc($qry_relation);


										//Inserta unidades que son
										$sql_petition = '
										INSERT INTO '.$p1_t1_inventory_sale_c2_products_history_units.' (Code_Item, Id_Warehouse, Type, Method, Id_Method, Unit, Motive, User_Code, Date,Id_Batch)
										VALUES
										(
											'.$Row_Masive_Products['Id_Product'].',
											'.$Id_Warehouse_Bill.',
											"'.$Row_Masive_Products['Type'].'" ,
											"'.$Accion.'" ,
											'.$Row_Masive['Id'].',
											'.$Row_Masive_Products['Units'].',
											"'.$MensajeNormal.'" ,
											'.$Row_Masive['Id_User'].' ,
											'.$date_massive.',
											'.$data_reltaion['Id'].'
										) ';
										$Query_NewHistory = mysqli_query($connection, $sql_petition);
								}


								if (!$Query_NewHistory) {
									echo 'Error 2 -->'.mysqli_error($connection).' / '.$sql_petition.' .<br/>';
								} else {
									if($PRINT){
										echo ' Ok ';
									}
								}

							}
						}else if($Row_Masive['State']!='Erased' && $Row_Masive['State']!='Temporal' && $Row_Masive['State']!='History'){
								echo 'Error ->'.mysqli_error($connection).' <br />';
						}
					} else {
						//Elimna registros de units
						$Query_History = mysqli_query($connection,'DELETE FROM '.$p1_t1_inventory_sale_c2_products_history_units.' WHERE Id_Method = '.$Row_Masive['Id'].' AND Method="Bill" ');
						echo mysqli_error($connection);

							//Si se elimina correctamente actualiza su estado a history
								$Query_Update = mysqli_query($connection,'UPDATE '.$p1_t1_inventory_sele_massive_history.' SET State="History" WHERE Id = '.$Row_Masive['Id'].' ');
								echo mysqli_error($connection);

								if($PRINT){
									echo ' History ';
								}

					}
				}

				//--------------------- INSERTA HISTORIALES DE LOS PRODUCTO ELIMINADOS PARA DEJARLO EN CEROS ----------------------------------------//
					$Query_List = '
					SELECT DISTINCT
						INVENT.Id, INVENT.Product, INVENT.Barcode, INVENT.Code_Group, WARE.Id as Id_Batch
					FROM '.$p1_t1_inventory_sele.' INVENT
					LEFT JOIN '.$p1_t1_warehouse_inventory.' WARE ON Id_Inventory = INVENT.Id
					WHERE WARE.Id_Warehouse ='.$_SESSION['Id_Warehouse'].' AND WARE.State = "Erased" ';
					// die($Query_List);
					$Qry_List = mysqli_query($connection, $Query_List);
					$type = '';
					foreach ($Qry_List as $row_erased){
						$unints_products = Consult_History_Units($row_erased['Id']);
						// echo $unints_products.'<br>';

						if ($unints_products == 0){
							continue;
						}elseif($unints_products < 0){
							// echo $unints_products;
							$type = 'add';
							$unints_products = $unints_products*-1;
						}elseif($unints_products > 0){
							// echo $unints_products;
							$type = 'remove';
						}

							$petition_erased = mysqli_query($connection, "INSERT INTO ".$p1_t1_inventory_sale_c2_products_history_units."
							(Code_Item, Id_Warehouse, Type, Method, Id_Method, Unit, Motive, User_Code, Date,Id_Batch)
							VALUES
							(".$row_erased['Id'].", ".$_SESSION['Id_Warehouse'].",  '".$type."',  'Close', ".$Masive_Id.", ".$unints_products.", '[Ajuste de (".$unints_products.") por ajuste de cierre de ".$TipoMes." e inicia ".$TipoMes." con 0] por masivo ".$Row_Masive['CUNI']." ',  '".$_SESSION['User_Code']."',  ".$date_massive." ,  ".$row_erased['Id_Batch'].") ");

							if(!$petition_erased){
								echo  'Error History_Erased ->'.mysqli_error($connection);
							}
					}
			}


			//Crea historial individual
			function Checked_Trasnfers_History($CUNI,  $PRINT=false){
					global $p1_t1_inventory_sale_c2_products_history_units,	$p14_t1_transfers,	$p14_t1_transfers_c1_products, $connection, $p1_t2_inventory_production_c2_products_history_units, $m0_sql_errors,$p1_t1_warehouse_inventory,$prefix,$p1_t1_warehouse_inventory_production;

					// Consulta masivos que hay con el mismo cuni y las ordena por fecha desc y estado comenzado por a active e erased history
					$Consult_Transfer = mysqli_query($connection, 'SELECT * FROM '.$p14_t1_transfers.' WHERE CUNI = "'.$CUNI.'" ORDER BY Last_Update DESC');
					echo mysqli_error($connection);

					$num = 0;
					while($Row_Transfer = mysqli_fetch_assoc($Consult_Transfer)){
						$num++;
						if($num==1){
							//Cambia el estado a activado
								if($Row_Transfer['State']=='History'){
									$Query_Update = mysqli_query($connection,'UPDATE '.$p14_t1_transfers.' SET State="Processing" WHERE Id = '.$Row_Transfer['Id'].' ');
									echo mysqli_error($connection);
								}

							//History units
								$Query_History = mysqli_query($connection,'DELETE FROM '.$p1_t1_inventory_sale_c2_products_history_units.' WHERE Id_Method = '.$Row_Transfer['Id'].' AND Method="Transfer" ');
								echo mysqli_error($connection);

								$Query_History = mysqli_query($connection,'DELETE FROM '.$p1_t2_inventory_production_c2_products_history_units.' WHERE Id_Method = '.$Row_Transfer['Id'].' AND Method="Transfer" ');
								echo mysqli_error($connection);


							if ($Query_History  && ($Row_Transfer['State']=="Processing" || $Row_Transfer['State']=="Accepted")){ // En estado enviado de bodega

									$Query_BillProducts = mysqli_query($connection,'SELECT Quantity_Dispatch, Quantity_Received, Id_Product, Type FROM '.$p14_t1_transfers_c1_products.' WHERE Id_Transfers = '.$Row_Transfer['Id']);
									echo mysqli_error($connection);
									while($Row_Transfer_Products = mysqli_fetch_assoc($Query_BillProducts)){

										$p1_t1_inventory_sale_c2_products_history_units = ($Row_Transfer_Products['Type'] == 'Production')? $p1_t2_inventory_production_c2_products_history_units : $p1_t1_inventory_sale_c2_products_history_units;
										$p1_t1_warehouse_inventory = ($Row_Transfer_Products['Type'] == 'Production')? $p1_t1_warehouse_inventory_production : $p1_t1_warehouse_inventory;

										$Units_Transfer = ($Row_Transfer['Type_Request'] == 'Request')?$Row_Transfer_Products['Quantity_Dispatch']:$Row_Transfer_Products['Quantity_Received'];
					          $Date_transfer = (!empty($Row_Transfer['Date_Received']))?$Row_Transfer['Date_Received']:date('U');

					          $Units_Transfer = (empty($Units_Transfer))?0:$Units_Transfer;

							      $query_relation = 'SELECT Id FROM '.$p1_t1_warehouse_inventory.' WHERE Id_inventory = '.$Row_Transfer_Products['Id_Product'].' AND Id_Warehouse = '.$Row_Transfer['Id_Warehouse_Origin'];
										$qry_relation = mysqli_query($connection , $query_relation);
                    $data_relation = mysqli_fetch_assoc($qry_relation);

					            //Insertamos historia de trasnferencia solo de la bodega de origen
					            $Query_Insert_History = "
					                INSERT INTO ".$p1_t1_inventory_sale_c2_products_history_units."
					                  (Code_Item, Id_Warehouse, Type, Method, Id_Method, Unit, Motive, User_Code, Date, Id_Batch)
					                VALUES
														(".$Row_Transfer_Products['Id_Product'].", ".$Row_Transfer['Id_Warehouse_Origin'].", 'Remove', 'Transfer', ".$Row_Transfer['Id'].", ".$Units_Transfer.", '[Envio ".floatval($Units_Transfer)." por transferencia n ".$Row_Transfer['CUNI']."]', ".$Row_Transfer['User_Code_Sent'].",".$Date_transfer." , ".$data_relation['Id'].") ";
											$Query_NewHistory = mysqli_query($connection, $Query_Insert_History);


					            if (!$Query_NewHistory) { //Insertamos error de sql
											 	echo 'Error 3 -->'.mysqli_error($connection).' / '.$sql_petition.' .<br/>';
					              $Insert_Errors_SQL = mysqli_query($connection,'INSERT INTO '.$m0_sql_errors.' (Type, CUNI, Query, Date) VALUES ("Units_History", "'.$Row_Transfer['CUNI'].'","'.$Query_Insert_History.'", '.date('U').' )');
											} else {
												if($PRINT){
													echo ' Ok ';
												}
											}
									}
							}

							if ($Query_History  && $Row_Transfer['State']=='Accepted') {
									$Query_BillProducts = mysqli_query($connection,'SELECT Quantity_Received, Id_Product, CUNI,Type FROM '.$p14_t1_transfers_c1_products.' WHERE Id_Transfers = '.$Row_Transfer['Id']);
									echo mysqli_error($connection);
									while($Row_Transfer_Products = mysqli_fetch_assoc($Query_BillProducts)){
										// echo $Row_Transfer_Products['Type'];

										$p1_t1_inventory_sale_c2_products_history_units = ($Row_Transfer_Products['Type'] == 'Production')? $prefix.'p1_t2_inventory_production_c2_products_history_units' : $prefix.'p1_t1_inventory_sale_c2_products_history_units';
										$p1_t1_warehouse_inventory = ($Row_Transfer_Products['Type'] == 'Production')? $prefix.'p1_t1_warehouse_inventory_production' : $prefix.'p1_t1_warehouse_inventory';

										$Unidacero = (empty($Row_Transfer_Products['Quantity_Received']))?0:$Row_Transfer_Products['Quantity_Received'];

                    $query_relation = 'SELECT Id FROM '.$p1_t1_warehouse_inventory.' WHERE Id_inventory = '.$Row_Transfer_Products['Id_Product'].' AND Id_Warehouse = '.$Row_Transfer['Id_Warehouse_Destination'];
                    $qry_relation = mysqli_query($connection , $query_relation);
										$data_relation = mysqli_fetch_assoc($qry_relation);

										$sql_petition = '
										INSERT INTO '.$p1_t1_inventory_sale_c2_products_history_units.' (Code_Item, Id_Warehouse, Type, Method, Id_Method, Unit, Motive, User_Code, Date, Id_Batch)
										VALUES
										(
											'.$Row_Transfer_Products['Id_Product'].',
											'.$Row_Transfer['Id_Warehouse_Destination'].',
											"Add" ,
											"Transfer" ,
											'.$Row_Transfer['Id'].',
											'.$Unidacero.',
											"[Recibio por Trasnferencia de '.floatval($Row_Transfer_Products['Quantity_Received']).' n '.$Row_Transfer['CUNI'].' .]" ,
											'.$Row_Transfer['User_Code_Received'].' ,
                      '.$Row_Transfer['Date_Received'].',
                      '.$data_relation['Id'].'
										) ';

										// echo $sql_petition.'<br>';

										$Query_NewHistory = mysqli_query($connection, $sql_petition);

										if (!$Query_NewHistory) {
											echo 'Error 4 -->'.mysqli_error($connection).' / '.$sql_petition.' .<br/>';
										} else {
											if($PRINT){
												echo ' Ok ';
											}
										}
									}
							}
						} else {
							//Eliminamos unidades
								$Query_History = mysqli_query($connection,'DELETE FROM '.$p1_t1_inventory_sale_c2_products_history_units.' WHERE Id_Method = '.$Row_Transfer['Id'].' AND Method="Transfer" ');
								echo mysqli_error($connection);

							//Actualizamos a history
								$Query_Update = mysqli_query($connection,'UPDATE '.$p14_t1_transfers.' SET State="History" WHERE Id = '.$Row_Transfer['Id'].' ');
								echo mysqli_error($connection);

								if($PRINT){
									echo ' History ';
								}
						}
					}
			}


			// function Checked_Buy_History($CUNI, $PRINT=false){
			// 	global $p1_t1_inventory_sale_c2_products_history_units, $p1_t2_inventory_production_c2_products_history_units, $p1_t1_inventory_sele, $p0_t1_config_business,	$p4_t2_vouchers_egress_c1_motives, $p9_t1_buys,	$p9_t1_buys_c1_products, $p4_t2_vouchers_egress, $connection, $ACROM;

			// 	// Consulta masivos que hay con el mismo cuni y las ordena por fecha desc y estado comenzado por a active e erased history
			// 	$Consult_Buy = mysqli_query($connection, 'SELECT * FROM '.$p9_t1_buys.' WHERE Code = "'.$CUNI.'" ORDER BY Last_Update DESC');
			// 	echo mysqli_error($connection);

			// 	$num = 0;
			// 	while($Row_Buy = mysqli_fetch_assoc($Consult_Buy)){
			// 		$num++;
			// 		if($num==1){
			// 			//Cambia el estado a activado
			// 				if($Row_Buy['State']=='History'){
			// 					$Query_Update = mysqli_query($connection,'UPDATE '.$p9_t1_buys.' SET State="Active" WHERE Code = '.$Row_Buy['Code'].' ');
			// 					echo mysqli_error($connection);
			// 				}

			// 			//History units
			// 				$Query_History = mysqli_query($connection,'DELETE FROM '.$p1_t1_inventory_sale_c2_products_history_units.' WHERE Id_Method = '.$Row_Buy['Code'].' AND Method="Purchase" ');
			// 				echo mysqli_error($connection);


			// 			//History units
			// 				$Query_History = mysqli_query($connection,'DELETE FROM '.$p1_t2_inventory_production_c2_products_history_units.' WHERE Id_Method = '.$Row_Buy['Code'].' AND Method="Purchase" ');
			// 				echo mysqli_error($connection);


			// 			if ($Query_History  && $Row_Buy['State']=="Active"){ // En estado enviado de bodega

			// 				$Query_BillProducts = mysqli_query($connection,'SELECT Update_Price, Valor_Unitario, Affect_Units, Cantidad, Code_Product, Type, Id_Warehouse FROM '.$p9_t1_buys_c1_products.' WHERE Code_Bill_Buy = '.$Row_Buy['Code']);
			// 				echo mysqli_error($connection);

			// 					while($Row_Buy_Products = mysqli_fetch_assoc($Query_BillProducts)){
			// 							if($Row_Buy_Products['Affect_Units']==1 && $Row_Buy_Products['Type']=="Int"){
			// 									//Insertamos historia de trasnferencia solo de la bodega de origen
			// 									$sql_petition = "
			// 											INSERT INTO ".$p1_t1_inventory_sale_c2_products_history_units."
			// 												(Code_Item, Id_Warehouse, Type, Method, Id_Method, Unit, Motive, User_Code, Date)
			// 											VALUES
			// 												(".$Row_Buy_Products['Code_Product'].", ".$Row_Buy_Products['Id_Warehouse'].", 'Add', 'Purchase', ".$Row_Buy['Code'].", ".$Row_Buy_Products['Cantidad'].", '[Agrego ".floatval($Row_Buy_Products['Cantidad'])." por compra factura n ".$Row_Buy['Num_Bill']."]',  ".$Row_Buy['User_Code'].", ".$Row_Buy['Date_Bill']." )
			// 										";
			// 									$Query_NewHistory = mysqli_query($connection, $sql_petition);

			// 									if (!$Query_NewHistory) { //Insertamos error de sql
			// 										echo 'Error 5 -->'.mysqli_error($connection).' / '.$sql_petition.' .<br/>';
			// 										$Insert_Errors_SQL = mysqli_query($connection,'INSERT INTO '.$m0_sql_errors.' (Type, CUNI, Query, Date) VALUES ("Units_History", "'.$Row_Buy['Code'].'","'.$Query_Insert_History.'", '.date('U').' )');
			// 									} else {
			// 										if($PRINT){
			// 											echo ' Ok ';
			// 										}
			// 									}
			// 							}

			// 							if($Row_Buy_Products['Update_Price']==1  && $Row_Buy_Products['Type']=="Int"){
			// 									$CodeProduct = $Row_Buy_Products['Code_Product'];
			// 									$Precio_Unitario = $Row_Buy_Products['Valor_Unitario'];

			// 									$updateInvenSele= mysqli_query ($connection, 'UPDATE '.$p1_t1_inventory_sele.' SET Price_Cost = '.$Precio_Unitario.' WHERE  Id ='.$CodeProduct);
			// 									echo mysqli_error($connection);
			// 							}


			// 							//Producción

			// 							if($Row_Buy_Products['Affect_Units']==1 && $Row_Buy_Products['Type']=="Prod"){
			// 									//Insertamos historia de trasnferencia solo de la bodega de origen
			// 									$sql_petition = "
			// 											INSERT INTO ".$p1_t2_inventory_production_c2_products_history_units."
			// 												(Code_Item, Id_Warehouse, Type, Method, Id_Method, Unit, Motive, User_Code, Date)
			// 											VALUES
			// 												(".$Row_Buy_Products['Code_Product'].", ".$Row_Buy_Products['Id_Warehouse'].", 'Add', 'Purchase', ".$Row_Buy['Code'].", ".$Row_Buy_Products['Cantidad'].", '[Agrego ".floatval($Row_Buy_Products['Cantidad'])." por compra factura n ".$Row_Buy['Num_Bill']."]',  ".$Row_Buy['User_Code'].", ".$Row_Buy['Date_Bill']." )
			// 										";
			// 									$Query_NewHistory = mysqli_query($connection, $sql_petition);

			// 									if (!$Query_NewHistory) { //Insertamos error de sql
			// 										echo 'Error 6 -->'.mysqli_error($connection).' / '.$sql_petition.' .<br/>';
			// 										$Insert_Errors_SQL = mysqli_query($connection,'INSERT INTO '.$m0_sql_errors.' (Type, CUNI, Query, Date) VALUES ("Units_History", "'.$Row_Buy['Code'].'","'.$Query_Insert_History.'", '.date('U').' )');
			// 									} else {
			// 										if($PRINT){
			// 											echo ' Ok ';
			// 										}
			// 									}
			// 							}

			// 							if($Row_Buy_Products['Update_Price']==1  && $Row_Buy_Products['Type']=="Prod"){
			// 									$CodeProduct = $Row_Buy_Products['Code_Product'];
			// 									$Precio_Unitario = $Row_Buy_Products['Valor_Unitario'];

			// 									$updateInvenSele= mysqli_query ($connection, 'UPDATE '.$p1_t2_inventory_production.' SET Price_Cost = '.$Precio_Unitario.' WHERE  Id ='.$CodeProduct);
			// 									echo mysqli_error($connection);
			// 							}

			// 					}

			// 					if($Row_Buy['Create_Egres']){
			// 						//Elimna anterior
			// 							$Query_History = mysqli_query($connection,'DELETE FROM '.$p4_t2_vouchers_egress.' WHERE Source_Value = '.$Row_Buy['Code'].' AND Source="puschase" ');
			// 							echo mysqli_error($connection);

			// 						//Consulta consecutivo de egreso
			// 							$PetitionConfig = mysqli_query($connection,'SELECT Number_Egreso FROM '.$p0_t1_config_business.' WHERE Id='.$Row_Buy['Id_Business']);
			// 							while($Row = mysqli_fetch_assoc($PetitionConfig)){
			// 								$NumeroEgreso = $Row['Number_Egreso'];
			// 							}

			// 						//Consulta Auto Increment
			// 							$Consult_Egress = mysqli_query($connection, "SHOW TABLE STATUS LIKE '".$p4_t2_vouchers_egress."'");
			// 							$row_egress = mysqli_fetch_assoc($Consult_Egress);
			// 							$id_egress = $row_egress['Auto_increment'];


			// 						//Crea nuevo
			// 							$Query ='
			// 								INSERT INTO '.$p4_t2_vouchers_egress.'
			// 										(Id_Business, CUNI, User_Code, Source, Source_Value, Code_Provider, Comprobante, Motive, Concept, Comment, Valor, Date, Cash, Code_Motive)
			// 								VALUES
			// 										('.$Row_Buy['Id_Business'].', "'.$ACROM.$id_egress.'" , '.$Row_Buy['User_Code'].', "puschase", '.$Row_Buy['Code'].', "'.$Row_Buy['Code_Provider'].'", "'.$NumeroEgreso.'", "Compras", "Compras a la factura numero '.$Row_Buy['Num_Bill'].'", "Se realiza el pago de $ '.number_format($Row_Buy['Amount_Paid']).' en la compra.", "'.$Row_Buy['Amount_Paid'].'", "'.$Row_Buy['Date_Bill'].'", '.$Row_Buy['Amount_Paid'].', -1) ';

			// 							$Petition = mysqli_query($connection, $Query);
			// 							echo mysqli_error($connection);

			// 						//Crea motivo
			// 								$Query = mysqli_query($connection,'
			// 								INSERT IGNORE INTO '.$p4_t2_vouchers_egress_c1_motives.'
			// 									(Id, Id_Business, Motive)
			// 								VALUES
			// 									(-1, '.$_SESSION['Id_Business'].',  "Compra")');
			// 								echo mysqli_error($connection);


			// 						if (!$Petition) {
			// 							die (mysqli_error($connection).'<br />'.$Query);
			// 						}
			// 					}


			// 			}
			// 		}
			// 	}

			// }

			// Funcion que conulta los datos del prefijo de facturacion
			// verificando si esta activo lo de prefijo por usuario


		function Consult_Prefix($User_Code = 0,$Electronic = 0 , $Id_Business = 0 ){
				/*By Juan*/
			global $connection,$p0_t1_config_c2_list_prefix,$p0_t1_config_business,$m2_s1_user_data;

			$Id_Business = ( $Id_Business == 0) ?  $_SESSION['Id_Business'] : $Id_Business ;

			if($Electronic == 1){
				$sqlElectro = "SELECT * FROM ".$p0_t1_config_c2_list_prefix." WHERE Is_Electronic=1";
				$consultElectro = mysqli_query($connection,$sqlElectro);
				$rowElectro = mysqli_fetch_assoc($consultElectro);
				return $rowElectro;
			}

			// Primero verifica que el prefijo por usuario no este activado
			$sql_conf = "SELECT Prefix_User, Prefix_Active FROM ".$p0_t1_config_business." WHERE Id=".$Id_Business;
			$consult_conf = mysqli_query($connection,$sql_conf);
			$row_conf = mysqli_fetch_assoc($consult_conf);

			// si esta activado muestra el prefijo de el usuario
			if ($row_conf['Prefix_User'] == 1){
				$sql_user = "SELECT Prefix_Bill FROM ".$m2_s1_user_data." WHERE User_Code=".(($User_Code == 0)?$_SESSION['User_Code']:$User_Code);
				$consult_user = mysqli_query($connection,$sql_user);
				$row_user = mysqli_fetch_assoc($consult_user);

				$sql = "SELECT * FROM ".$p0_t1_config_c2_list_prefix." WHERE Id=".$row_user['Prefix_Bill'];
				$consult = mysqli_query($connection,$sql);
				$row = mysqli_fetch_assoc($consult);

				return $row;

			}else{ //si no esta activado busca el prefijo que este seleccionado
				$sql = "SELECT * FROM ".$p0_t1_config_c2_list_prefix." WHERE Id=".$row_conf['Prefix_Active'];
				$consult = mysqli_query($connection,$sql);
				$row = mysqli_fetch_assoc($consult);

				return $row;
			}
		}
		// /\<br(\s*)?\/?\>/i
		function Salto_Linea($string){
			$string= preg_replace('/%0D%0A/', "<br>", $string);
			$string= preg_replace('/%20/', " ", $string);
			return $string;
    }


		//arreglo de precios

		$CONST_GetPriceParams= array(
			"-1"=>array(
				"PrecioNeto" => "Price",
				"PrecioSugerido" => "Price_Sugerido",
				"TipoIva" => "type_iva",
				"PorcenIva" => "Porcertage",
				"ValorIva" => "Iva",
				"PorcenIpc" => "ipoconsumo",
				"ValorIpc" => "valor_ipoconsumo",
				"ImpBolsa" => "impobolsa"
			),
			"-2" => array(
				"PrecioNeto" => "Price_Distributor_Neto",
				"PrecioSugerido" => "Price_Distributor",
				"TipoIva" => "type_iva_3",
				"PorcenIva" => "Iva_Distributor",
				"ValorIva" => "Valor_Iva_Distributor",
				"PorcenIpc" => "ipoconsumo_Distributor",
				"ValorIpc" => "Valor_ipoconsumo_Distributor",
				"ImpBolsa" => "impobolsa"
			),
			"-3" => array(
				"PrecioNeto" => "Price_Wholesale_Neto",
				"PrecioSugerido" => "Price_Wholesale",
				"TipoIva" => "type_iva_2",
				"PorcenIva" => "Iva_Wholesale",
				"ValorIva" => "Valor_Iva_Wholesale",
				"PorcenIpc" => "ipoconsumo_Wholesale",
				"ValorIpc" => "Valor_ipoconsumo_Wholesale",
				"ImpBolsa" => "impobolsa"
			)
		);


	$c0_countries = $prefix."c0_countries";
	$c1_departments = $prefix."c1_departments";
	$c2_municipality = $prefix."c2_municipality";
	$c3_eps = $prefix."c3_eps";
	$c5_ethnicity = $prefix."c5_ethnicity";
	$c6_document_types = $prefix."c6_document_types";
	$c7_activities = $prefix."c7_activities";
	$c8_entitys = $prefix."c8_entitys";
	$c9_appointments = $prefix."c9_appointments";
	$m0_sql_errors = $prefix."m0_sql_errors";
	$m1_s1_modules_lv1 = $prefix."m1_s1_modules_lv1";
	$m1_s2_modules_lv2 = $prefix."m1_s2_modules_lv2";
	$m1_s3_modules_lv3 = $prefix."m1_s3_modules_lv3";
	$m2_s1_c1_user_groups = $prefix."m2_s1_c1_user_groups";
	$m2_s1_user_data = $prefix."m2_s1_user_data";
	$m2_s2_printers_name = $prefix."m2_s2_printers_name";
	$m3_s1_access_permits = $prefix."m3_s1_access_permits";
	$p0_t1_config_business = $prefix."p0_t1_config_business";
	$p0_t1_config_c1_locked_permiss = $prefix."p0_t1_config_c1_locked_permiss";
	$p0_t2_warehouse = $prefix."p0_t2_warehouse";
	$p0_t2_warehouse_c1_assign = $prefix."p0_t2_warehouse_c1_assign";
	$p0_t3_segurity_log = $prefix."p0_t3_segurity_log";
	$p0_t4_remote_printer = $prefix."p0_t4_remote_printer";
	$p0_t5_synchronization = $prefix."p0_t5_synchronization";
	$p0_t5_synchronization_records = $prefix."p0_t5_synchronization_records";
	$p0_t6_licence_offline = $prefix."p0_t6_licence_offline";
	$p0_t6_licence_offline_c1_business = $prefix."p0_t6_licence_offline_c1_business";
	$p0_t8_method_pay = $prefix."p0_t8_method_pay";
	$p0_t11_type_room = $prefix."p0_t11_type_room";
	$p0_co1_t1_account_matrix = $prefix."p0_co1_t1_account_matrix";
	$p0_co1_t2_bill_configuration = $prefix."p0_co1_t2_bill_configuration";
	$p0_co1_t3_accounts_bills = $prefix."p0_co1_t3_accounts_bills";
	$p0_co1_t4_cont_config_items = $prefix."p0_co1_t4_cont_config_items";
	$p0_co1_t5_cont_Items_accounts = $prefix."p0_co1_t5_cont_Items_accounts";
	$p0_co1_t6_bill_element = $prefix."p0_co1_t6_bill_element";
	$p0_t12_list_devices = $prefix."p0_t12_list_devices";
	$p10_t1_employee = $prefix."p10_t1_employee";
	$p10_t2_payrolls = $prefix."p10_t2_payrolls";
	$p10_t2_payrolls_c1_data = $prefix."p10_t2_payrolls_c1_data";
	$p10_t3_novelties = $prefix."p10_t3_novelties";
	$p10_t4_access_control = $prefix . "p10_t4_access_control";
	$p10_t5_rest_reason = $prefix . "p10_t5_rest_reason";
	$p10_t6_workgroup = $prefix . "p10_t6_workgroup";
	$p10_t7_Appointment = $prefix . "p10_t7_Appointment";
	$p10_t8_parametrization_salary = $prefix . "p10_t8_parametrization_salary";

	$p11_t1_patients = $prefix."p11_t1_patients";
	$p11_t2_patients_consult = $prefix."p11_t2_patients_consult";
	$p11_t3_patients_inrooms = $prefix."p11_t3_patients_inrooms";

	$p12_t1_c1_room_reserve = $prefix."p12_t1_c1_room_reserve";
	$p12_t1_rooms = $prefix."p12_t1_rooms";
	$p12_t1_rooms_c1_floors = $prefix."p12_t1_rooms_c1_floors";
	$p12_t2_c1_relation_rooms_products = $prefix."p12_t2_c1_relation_rooms_products";
	$p12_t2_inventory_rooms = $prefix."p12_t2_inventory_rooms";
	$p12_t3_bill_rooms = $prefix."p12_t3_bill_rooms";
	$p12_t3_bills_rooms_inventory = $prefix."p12_t3_bills_rooms_inventory";
	$p12_t3_bills_rooms_inventory_c1_history = $prefix."p12_t3_bills_rooms_inventory_c1_history";
	$p12_t4_companions_rooms = $prefix."p12_t4_companions_rooms";
	$p12_t5_table_info_guests = $prefix."p12_t5_table_info_guests";

	$p13_t1_information_client_aditional = $prefix."p13_t1_information_client_aditional";
	$p13_t2_client_bodymeasurements = $prefix."p13_t2_client_bodymeasurements";
	$p13_t3_plans = $prefix."p13_t3_plans";
	$p13_t4_relation_plan_client = $prefix."p13_t4_relation_plan_client";
	$p13_t5_registry_day = $prefix."p13_t5_registry_day";

	$p14_t1_transfers = $prefix."p14_t1_transfers";
	$p14_t1_transfers_c1_products = $prefix."p14_t1_transfers_c1_products";
	$p14_t1_transfers_c2_registre_states = $prefix."p14_t1_transfers_c2_registre_states";

	$p15_t0_orders = $prefix."p15_t0_orders";
	$p15_t1_orders_products = $prefix."p15_t1_orders_products";

	$p18_t1_routes_day = $prefix."p18_t1_routes_day";
	$p18_t1_routes_day_c1_arrival = $prefix."p18_t1_routes_day_c1_arrival";
	$p18_t2_routes_day_predetermined = $prefix."p18_t2_routes_day_predetermined";

	$p19_inventory_reference_c1_groups = $prefix."p19_inventory_reference_c1_groups";
	$p19_inventory_reference_c1_states = $prefix."p19_inventory_reference_c1_states";
	$p19_t0_reference = $prefix."p19_t0_reference";
	$p19_t1_inventory_reference = $prefix."p19_t1_inventory_reference";
	$p19_t2_events = $prefix."p19_t2_events";
	$p19_t2_events_c1_inventory = $prefix."p19_t2_events_c1_inventory";

	$p1_c1_relation_inventory = $prefix."p1_c1_relation_inventory";

	$p1_t0_initial = $prefix."p1_t0_initial";
	$p1_t0_initial_product = $prefix."p1_t0_initial_product";

	$p1_t1_inventory_sale_c2_products_history_units = $prefix."p1_t1_inventory_sale_c2_products_history_units";
	$p1_t1_inventory_sele = $prefix."p1_t1_inventory_sele";
	$p1_t1_inventory_sele_c1_products_groups = $prefix."p1_t1_inventory_sele_c1_products_groups";
	$p1_t1_inventory_sele_c2_price_groups = $prefix."p1_t1_inventory_sele_c2_price_groups";
	$p1_t1_inventory_sele_c3_components = $prefix."p1_t1_inventory_sele_c3_components";
	$p1_t1_inventory_sele_c4_group_components = $prefix."p1_t1_inventory_sele_c4_group_components";
	$p1_t1_inventory_sele_c5_products_brands = $prefix.'p1_t1_inventory_sele_c5_products_brands';
	$p1_t1_inventory_sele_massive_history = $prefix."p1_t1_inventory_sele_massive_history";
	$p1_t1_inventory_sele_massive_history_products = $prefix."p1_t1_inventory_sele_massive_history_products";
	$p1_t1_warehouse_inventory = $prefix."p1_t1_warehouse_inventory";

	$p1_t1_warehouse_inventory_history = $prefix."p1_t1_warehouse_inventory_history";

	$p1_t2_inventory_production_c2_products_history_units = $prefix."p1_t2_inventory_production_c2_products_history_units";
	$p1_t2_inventory_production_c2_products_units = $prefix."p1_t2_inventory_production_c2_products_units";
	$p1_t2_inventory_production = $prefix."p1_t2_inventory_production";
	$p1_t2_inventory_production_c1_products_groups = $prefix."p1_t2_inventory_production_c1_products_groups";
	$p1_t2_inventory_production_c2_price_groups = $prefix."p1_t2_inventory_production_c2_price_groups";
	$p1_t2_inventory_production_c3_components = $prefix."p1_t2_inventory_production_c3_components";
	$p1_t2_inventory_production_c4_group_components = $prefix."p1_t2_inventory_production_c4_group_components";
	$p1_t1_inventory_sele_c6_relation_size = $prefix."p1_t1_inventory_sele_c6_relation_size";
	$p1_t2_inventory_production_massive_history = $prefix."p1_t2_inventory_production_massive_history";
	$p1_t2_inventory_production_massive_history_products = $prefix."p1_t2_inventory_production_massive_history_products";
	$p1_t1_warehouse_inventory_production = $prefix."p1_t1_warehouse_inventory_production";


	$p20_t1_relationship_empleoyees = $prefix."p20_t1_relationship_empleoyees";
	$p20_t2_data_reader = $prefix."p20_t2_data_reader";

	$p22_animals = $prefix."p22_animals";
	$p22_animals_inrooms = $prefix."p22_animals_inrooms";
	$p22_t2_animals_consult = $prefix."p22_t2_animals_consult";
	$p22_t3_animals_breed = $prefix."p22_t3_animals_breed";

	$p24_t1_parametrization = $prefix."p24_t1_parametrization";
	$p24_t2_services = $prefix."p24_t2_services";
	$p24_t2_services_c1_divice = $prefix."p24_t2_services_c1_divice";
	$p24_t2_services_c2_components = $prefix."p24_t2_services_c2_components";
	$p24_t2_services_c3_images = $prefix."p24_t2_services_c3_images";

	$p2_t1_clients = $prefix."p2_t1_clients";
	$p2_t1_clients_a1_additional = $prefix."p2_t1_clients_a1_additional";
	$p2_t2_clients_additional_info = $prefix."p2_t2_clients_additional_info";
	$p2_t1_clients_c1_points_add = $prefix."p2_t1_clients_c1_points_add";
	$p2_t1_clients_c2_points_red = $prefix."p2_t1_clients_c2_points_red";
	$p2_t1_clients_c3_funds_history = $prefix."p2_t1_clients_c3_funds_history";
	$p2_t1_clients_c6_business = $prefix."p2_t1_clients_c6_business";
	$p2_t2_clients_credit_notes = $prefix."p2_t2_clients_credit_notes";
	$p2_t2_c1_clients_credit_notes_products = $prefix."p2_t2_c1_clients_credit_notes_products";

$p2_t1_clients_c7_references = $prefix . "p2_t1_clients_c7_references";


	$p2_t2_clients_debit_notes = $prefix."p2_t2_clients_debit_notes";
	$p2_t2_c2_clients_credit_notes_history =  $prefix."p2_t2_c2_clients_credit_notes_history";
	$p2_t2_c1_provider_debit_notes_products =  $prefix . "p2_t2_c1_provider_debit_notes_products";
	$p2_t2_c2_provider_debit_notes_history =  $prefix . "p2_t2_c2_provider_debit_notes_history";
	$p2_t2_provider_debit_notes =  $prefix . "p2_t2_provider_debit_notes";
	$p2_t1_clients_address =  $prefix . "p2_t1_clients_address";


	$p3_t1_bills = $prefix."p3_t1_bills";
	$p3_t1_bills_c7_credits_program_pay = $prefix."p3_t1_bills_c7_credits_program_pay";
	$p3_t1_bills_c1_products = $prefix."p3_t1_bills_c1_products";
	$p3_t1_bills_c1_products_c1_components = $prefix."p3_t1_bills_c1_products_c1_components";
	$p3_t1_bills_c7_history_program_pay = $prefix."p3_t1_bills_c7_history_program_pay";
	$p3_t1_bills_c2_waiters = $prefix."p3_t1_bills_c2_waiters";
	$p3_t1_bills_c3_deposit = $prefix."p3_t1_bills_c3_deposit";
	$p3_t1_bills_c3_deposit_sc1_movements = $prefix."p3_t1_bills_c3_deposit_sc1_movements";
	$p3_t1_bills_c4_points = $prefix."p3_t1_bills_c4_points";
	$p3_t1_bills_c5_objects = $prefix."p3_t1_bills_c5_objects";
	$p3_t1_bills_c5_objects_c1_spaces = $prefix."p3_t1_bills_c5_objects_c1_spaces";
	$p3_t1_bills_c5_objects_c1_spaces_c1_permits = $prefix."p3_t1_bills_c5_objects_c1_spaces_c1_permits";
	$p3_t1_bills_c5_s1_reserve = $prefix."p3_t1_bills_c5_s1_reserve";
	$p3_t1_bills_c6_courier = $prefix."p3_t1_bills_c6_courier";
	$p3_t1_bills_c7_method_pay = $prefix."p3_t1_bills_c7_method_pay";
	$p3_t1_bills_interactive_groups_permiss = $prefix."p3_t1_bills_interactive_groups_permiss";
	$p3_t1_bills_interactive_parameterization = $prefix."p3_t1_bills_interactive_parameterization";
	$p3_t2_opening_box = $prefix."p3_t2_opening_box";
	$p3_t3_discounts = $prefix."p3_t3_discounts";
	$p3_t4_program_bill = $prefix."p3_t4_program_bill";
	$p3_t4_program_bill_history = $prefix."p3_t4_program_bill_history";
	$p3_t4_program_bill_products = $prefix."p3_t4_program_bill_products";
	$p3_t6_aside = $prefix."p3_t6_aside";
	$p3_t6_aside_c1_program_pay = $prefix."p3_t6_aside_c1_program_pay";
	$p3_t7_markers = $prefix."p3_t7_markers";
	$p3_t8_config_monitors = $prefix."p3_t8_config_monitors";
	$p3_t9_settings_adds = $prefix."p3_t9_settings_adds";
	$p3_t10_devolutions = $prefix."p3_t10_devolutions";
	$p3_t10_devolutions_products = $prefix."p3_t10_devolutions_products";
	$p3_t11_changes = $prefix.'p3_t11_changes';
	$p3_t11_change_products = $prefix.'p3_t11_change_products';
	$p3_t1_bills_c7_history_change_fee = $prefix . 'p3_t1_bills_c7_history_change_fee';

	$p4_t1_vouchers_ingress = $prefix."p4_t1_vouchers_ingress";
	$p4_t2_vouchers_egress = $prefix."p4_t2_vouchers_egress";
	$p4_t2_vouchers_egress_c1_motives = $prefix."p4_t2_vouchers_egress_c1_motives";
	$p4_t2_vouchers_egress_c2_preliminary = $prefix."p4_t2_vouchers_egress_c2_preliminary";
	$p4_t3_vouchers_ingress_c3_method_pay = $prefix."p4_t3_vouchers_ingress_c3_method_pay";
	$p4_t4_vouchers_egress_c4_method_pay = $prefix."p4_t4_vouchers_egress_c4_method_pay";

	$p5_t1_closed_box = $prefix."p5_t1_closed_box";
	$p5_t1_closed_box_remission = $prefix."p5_t1_closed_box_remission";
	$p5_t2_movements = $prefix."p5_t2_movements";
	$p5_t3_closed_box_user = $prefix."p5_t3_closed_box_user";

	$p6_t1_quotations = $prefix."p6_t1_quotations";
	$p6_t1_quotations_c1_products = $prefix."p6_t1_quotations_c1_products";


	$p7_t1_remissions = $prefix."p7_t1_remissions";
	$p7_t1_remissions_c1_products = $prefix."p7_t1_remissions_c1_products";
	$p7_t1_remissions_c1_method_pay = $prefix."p7_t1_remissions_c1_method_pay";

	$p8_t1_provider = $prefix."p8_t1_provider";
	$p8_t2_provider_groups = $prefix."p8_t2_provider_groups";

	$p9_t1_buys = $prefix."p9_t1_buys";
	$p9_t1_buys_c1_products = $prefix."p9_t1_buys_c1_products";
	$p9_t1_buys_c2_payments = $prefix."p9_t1_buys_c2_payments";
	$p9_t1_buys_c3_method_pay = $prefix."p9_t1_buys_c3_method_pay";
	$p9_t1_buys_payments_c3_method_pay = $prefix."p9_t1_buys_payments_c3_method_pay";

	$p4_t2_vouchers_ingress_c2_preliminary = $prefix."p4_t2_vouchers_ingress_c2_preliminary";
	$p25_t1_brands = $prefix."p25_t1_brands";
	$p25_t1_consultants = $prefix."p25_t1_consultants";
	$p25_t1_enlistments = $prefix."p25_t1_enlistments";
	$p25_t1_expenses = $prefix."p25_t1_expenses";
	$p25_t1_type_vehicle = $prefix."p25_t1_type_vehicle";
	$p25_t2_sales = $prefix."p25_t2_sales";
	$p25_t2_sales_consultants = $prefix."p25_t2_sales_consultants";
	$p25_t2_sales_enlistments = $prefix."p25_t2_sales_enlistments";
	$p25_t3_reports = $prefix."p25_t3_reports";
	$p25_t3_reports_expenses = $prefix."p25_t3_reports_expenses";
	$p4_t2_vouchers_ingress_c1_motives = $prefix."p4_t2_vouchers_ingress_c1_motives";
	$p25_t2_sales_method_pay = $prefix."p25_t2_sales_method_pay";
	$p0_t1_config_c1_list_processes = $prefix."p0_t1_config_c1_list_processes";
	$p19_t2_events_c2_services = $prefix."p19_t2_events_c2_services";
	$p26_t1_patients = $prefix."p26_t1_patients";
	$p26_t2_patients_historys = $prefix."p26_t2_patients_historys";
	$p26_t2_patients_historys_tabs = $prefix."p26_t2_patients_historys_tabs";
	$p0_t1_config_c2_list_prefix =$prefix."p0_t1_config_c2_list_prefix";
	$p3_t1_bills_touch_register = $prefix."p3_t1_bills_touch_register";
	$p3_t1_bills_c1_method_pay = $prefix."p3_t1_bills_c1_method_pay";
	$p11_t4_roles_users  = $prefix."p11_t4_roles_users";
	$p11_t4_roles = $prefix."p11_t4_roles";
	$p26_t3_patients_historys_tabs = $prefix."p26_t3_patients_historys_tabs";
	$p26_t4_evolution = $prefix."p26_t4_evolution";
	$p26_t4_procedures = $prefix."p26_t4_procedures";
	$p26_t4_evolution_procedure = $prefix."p26_t4_evolution_procedure";
	$p2_t1_clients_c4_inventory_unavaliable = $prefix."p2_t1_clients_c4_inventory_unavaliable";
	$p11_t5_doctors = $prefix."p11_t5_doctors";
	$p11_t6_schedule = $prefix."p11_t6_schedule";
	$p11_t7_closed_hospital = $prefix."p11_t7_closed_hospital";
	$p2_t1_clients_c5_groups = $prefix."p2_t1_clients_c5_groups";
	$p28_t1_crm_c2_business = $prefix."p28_t1_crm_c2_business";
	$p28_t1_crm_c3_additional = $prefix."p28_t1_crm_c3_additional";
	$p28_t1_crm_c4_adviser = $prefix."p28_t1_crm_c4_adviser";
	$p28_t1_crm_c1_more_data = $prefix."p28_t1_crm_c1_more_data";
	$p28_t1_crm_c5_events = $prefix."p28_t1_crm_c5_events";
	$p28_t1_crm_c6_history_adviser = $prefix."p28_t1_crm_c6_history_adviser";

	$p29_t1_data_visitor = $prefix."p29_t1_data_visitor";
	$p29_t1_visit = $prefix."p29_t1_visit";
	$p30_t1_info_user = $prefix."p30_t1_info_user";
	$p30_t1_registers = $prefix."p30_t1_registers";

	$p31_t1_document_support = $prefix."p31_t1_document_support";
	$p31_t2_document_support_c1_motives = $prefix."p31_t2_document_support_c1_motives";
	$p31_t3_document_support_c4_method_pay = $prefix."p31_t3_document_support_c4_method_pay";

	$p32_dispatched_products = $prefix."p32_dispatched_products";
	$p33_t1_purchase_order = $prefix."p33_t1_purchase_order";
	$p33_t2_purchase_products = $prefix."p33_t2_purchase_products";

	$p34_t1_purchase_service = $prefix."p34_t1_purchase_service";
	$p34_t2_purchase_service_products = $prefix."p34_t2_purchase_service_products";
	$p35_t1_events_general = $prefix."p35_t1_events_general";

	$p36_t1_list_price = $prefix."p36_t1_list_price";
	$p36_t2_list_price_product = $prefix."p36_t2_list_price_product";

	$p39_t1_parametrization_communication = $prefix."p39_t1_parametrization_communication";
	$p39_t2_cases = $prefix."p39_t2_cases";
	$p39_t2_cases_c1_messages = $prefix."p39_t2_cases_c1_messages";

	$Tybe_Bill_Text = array('Credit' => 'Crédito','Aside' => 'Apartado','InviteHouse' => 'Invitacion','Counted' => 'Contado')


?>
