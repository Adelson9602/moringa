<?php


function line_break($text, $characters)
{
  $output = array(); //salida
  $words = explode(' ', $text); //separamos por espacio
  $TEMP_COUNTchars = 0; //caracteres actuales por linea
  $TEMPchars = '';
  foreach ($words as $key => $value) { //recorremos las palabras
    $count = strlen($value); //cantidad de caracteres de la palabra
    if (($count + $TEMP_COUNTchars) <= $characters) {
      $TEMPchars .= $value . ' ';
      $TEMP_COUNTchars += ($count + 1);
    } else {
      array_push($output, $TEMPchars);
      $TEMPchars = $value . ' ';
      $TEMP_COUNTchars = $count + 1;
    }
  }
  if (!empty($TEMPchars)) {
    array_push($output, $TEMPchars);
  }
  return $output;
}

function Consult_Waiter($Code)
{
  global $connection, $p3_t1_bills_c2_waiters;

  $WaiterConsult = mysqli_query($connection, 'SELECT Name FROM ' . $p3_t1_bills_c2_waiters . ' WHERE Code =' . $Code);
  $RowWaiter = mysqli_fetch_assoc($WaiterConsult);
  $WaiterNameUser = $RowWaiter['Name'];

  return $WaiterNameUser;
}

function Consult_User($Code)
{
  global $connection, $m2_s1_user_data;

  $User_Consult = mysqli_query($connection, 'SELECT First_Name,Surname FROM ' . $m2_s1_user_data . ' WHERE User_Code =' . $Code);
  $Row_User = mysqli_fetch_assoc($User_Consult);
  $User_Name = $Row_User['First_Name'] . ' ' . $Row_User['Surname'];

  return $User_Name;
}

function Translator_method_pay($method)
{
  switch ($method) {
    case 'Cash':
      return 'Efectivo';
      break;

    case 'Card':
      return 'Tarjeta';
      break;

    case 'Both':
      return 'Ambos';
      break;

    default:
      return '--';
      break;
  }
}

function Consult_CUNI($Id_Business, $table)
{
  global $connection, $p0_t1_config_business, $ACROM;

  $acrom = '';
  if ($table == '001_droi_p3_t1_bills') { // si la tabla es bills asigna el acrom de ini
    $acrom = $ACROM;
  } else { // si no lo consulta de config
    $sql_config = "SELECT Acrom FROM " . $p0_t1_config_business . " WHERE Id =" . $Id_Business;
    $cosult_config = mysqli_query($connection, $sql_config);
    $row_config = mysqli_fetch_assoc($cosult_config);
    $acrom = $row_config['Acrom'];
  }

  $sql_AI = "SHOW TABLE STATUS LIKE '" . $table . "'";
  $consult_ai = mysqli_query($connection, $sql_AI);
  $row_ai = mysqli_fetch_assoc($consult_ai);
  $auto_increment = $row_ai['Auto_increment'];

  $cuni = $acrom . $auto_increment;

  return $cuni;
}

function Cosult_fund($Id_Client, $data, $Date_End = '', $Date_In = '')
{
  global $connection, $p2_t1_clients_c3_funds_history;
  $And = '';

  if (!empty($Date_In)) {
    $And = "AND Date BETWEEN " . $Date_In . " AND " . $Date_End;
  } else if (!empty($Date_End)) {
    $And = "AND Date <= " . $Date_End;
  }

  $sql = "SELECT SUM(Value) AS Total_Add,
     (SELECT SUM(Value) FROM " . $p2_t1_clients_c3_funds_history . " WHERE Id_Business=" . $_SESSION['Id_Business'] . " AND Type = 'Remove' " . $And . " AND State = 'Active' AND Id_Cliente = " . $Id_Client . " ) AS Total_Remove
     FROM " . $p2_t1_clients_c3_funds_history . " WHERE Id_Business=" . $_SESSION['Id_Business'] . " AND Type = 'Add' " . $And . " AND State = 'Active' AND Id_Cliente = " . $Id_Client;

  $consult = mysqli_query($connection, $sql);
  $row = mysqli_fetch_assoc($consult);

  $total_add = (empty($row['Total_Add'])) ? 0 : $row['Total_Add'];
  $total_remove = (empty($row['Total_Remove'])) ? 0 : $row['Total_Remove'];

  $return  = '';

  switch ($data) {
    case 'all':
      $return = $total_add - $total_remove;
      break;

    case 'remove':
      $return = $total_remove;
      break;

    case 'add':
      $return = $total_add;
      break;
  }

  return $return;
}


//-----------------------CONSULTA TOTAL DE NOTAS CREDITO ------------------------------------//
function Cosult_note_credit($Id_Client, $data, $Date_End = '', $Date_In = '')
{
  global $connection, $p2_t2_c2_clients_credit_notes_history;
  $And = '';

  if (!empty($Date_In)) {
    $And = "AND Date BETWEEN " . $Date_In . " AND " . $Date_End;
  } else if (!empty($Date_End)) {
    $And = "AND Date <= " . $Date_End;
  }

  $sql = "SELECT SUM(Value) AS Total_Add,
		 (SELECT SUM(Value) FROM " . $p2_t2_c2_clients_credit_notes_history . " WHERE Id_Business=" . $_SESSION['Id_Business'] . " AND Type = 'Remove' " . $And . " AND State = 'Active' AND Id_Client = " . $Id_Client . " ) AS Total_Remove
		 FROM " . $p2_t2_c2_clients_credit_notes_history . " WHERE Id_Business=" . $_SESSION['Id_Business'] . " AND Type = 'Add' " . $And . " AND State = 'Active' AND Id_Client = " . $Id_Client;

  $consult = mysqli_query($connection, $sql);
  $row = mysqli_fetch_assoc($consult);

  $total_add = (empty($row['Total_Add'])) ? 0 : $row['Total_Add'];
  $total_remove = (empty($row['Total_Remove'])) ? 0 : $row['Total_Remove'];

  $return  = '';

  switch ($data) {
    case 'all':
      $return = $total_add - $total_remove;
      break;

    case 'remove':
      $return = $total_remove;
      break;

    case 'add':
      $return = $total_add;
      break;
  }

  return $return;
}

//-----------------------CONSULTA TOTAL DE NOTAS DEBITO ------------------------------------//
function Cosult_note_debit($Id_Provider, $data, $Date_End = '', $Date_In = '')
{
  global $connection, $p2_t2_c2_provider_debit_notes_history;
  $And = '';

  if (!empty($Date_In)) {
    $And = "AND Date BETWEEN " . $Date_In . " AND " . $Date_End;
  } else if (!empty($Date_End)) {
    $And = "AND Date <= " . $Date_End;
  }

  $sql = "SELECT SUM(Value) AS Total_Add,
		 (SELECT SUM(Value) FROM " . $p2_t2_c2_provider_debit_notes_history . " WHERE Id_Business=" . $_SESSION['Id_Business'] . " AND Type = 'Remove' " . $And . " AND State = 'Active' AND Id_Provider = " . $Id_Provider . " ) AS Total_Remove
		 FROM " . $p2_t2_c2_provider_debit_notes_history . " WHERE Id_Business=" . $_SESSION['Id_Business'] . " AND Type = 'Add' " . $And . " AND State = 'Active' AND Id_Provider = " . $Id_Provider;

  $consult = mysqli_query($connection, $sql);
  $row = mysqli_fetch_assoc($consult);

  $total_add = (empty($row['Total_Add'])) ? 0 : $row['Total_Add'];
  $total_remove = (empty($row['Total_Remove'])) ? 0 : $row['Total_Remove'];

  $return  = '';

  switch ($data) {
    case 'all':
      $return = $total_add - $total_remove;
      break;

    case 'remove':
      $return = $total_remove;
      break;

    case 'add':
      $return = $total_add;
      break;
  }

  return $return;
}


function Consult_Units_Sell($Id_Product, $Id_Client, $Date_In = '', $Date_End = '')
{
  global $connection, $p3_t1_bills, $p3_t1_bills_c1_products;

  $And = '';

  if (!empty($Date_In) || !empty($Date_End)) {
    $And = "AND Date BETWEEN " . $Date_In . " AND " . $Date_End;
  }

  $sql_1 = "SELECT Id FROM " . $p3_t1_bills . " WHERE Code_Client = " . $Id_Client . " " . $And . " ";
  // die($sql_1);
  $consult_bills = mysqli_query($connection, $sql_1);

  $total_units = 0;
  while ($row_bill = mysqli_fetch_assoc($consult_bills)) {
    $sql_2 = "SELECT SUM(Units) AS units FROM " . $p3_t1_bills_c1_products . " WHERE Id_Bill = " . $row_bill['Id'] . " AND Code_Product=" . $Id_Product;
    $consult_bills_products = mysqli_query($connection, $sql_2);

    $row_bill_products = mysqli_fetch_assoc($consult_bills_products);
    $total_units += $row_bill_products['units'];
  }

  return $total_units;
}

function Consult_warehouse_user($User_Code)
{
  global $connection, $m2_s1_user_data, $p0_t2_warehouse_c1_assign;

  $consult_assign = mysqli_query($connection, "SELECT Assignment_code FROM " . $m2_s1_user_data . " WHERE User_Code=" . $User_Code);
  $row_assign = mysqli_fetch_assoc($consult_assign);

  $consult_ware_busi = mysqli_query($connection, "SELECT Id_Business, Id_Warehouse FROM " . $p0_t2_warehouse_c1_assign . " WHERE Id = " . $row_assign['Assignment_code']);
  $row_ware_busi = mysqli_fetch_assoc($consult_ware_busi);

  $data_user = array(
    'Id_Business' => $row_ware_busi['Id_Business'],
    'Id_Warehouse' => $row_ware_busi['Id_Warehouse']
  );

  return $data_user;
}

function Check_availability($Id_doctor, $date_ini, $date_end)
{
  global $connection, $p11_t6_schedule;

  $consult_availability = mysqli_query($connection, "SELECT Id FROM " . $p11_t6_schedule . " WHERE Id_Doctor = " . $Id_doctor . " AND (Date_ini >= " . $date_end . " OR Date_end <= " . $date_ini . ")");

  $availability = (mysqli_num_rows($consult_availability) > 0) ? false : true;
  return $availability;
}

function Consult_credit_total($Id_client)
{
  global $connection, $p3_t1_bills, $p3_t1_bills_c3_deposit;

  $Total_Pendiente = 0;

  $results_2 = mysqli_query($connection, "SELECT * FROM " . $p3_t1_bills . " WHERE Id_Business=" . $_SESSION['Id_Business'] . " AND Code_Client='" . $Id_client . "' AND Paid='n' AND State='Active'");
  while ($row_2 = mysqli_fetch_assoc($results_2)) {

    $PetitionCalculateAbono = mysqli_query($connection, 'SELECT SUM(Abono) AS SUMAbono FROM ' . $p3_t1_bills_c3_deposit . ' WHERE Id_Business = ' . $_SESSION['Id_Business'] . ' AND Id_bill ="' . $row_2['Id'] . '" ');
    $NumRow3 = mysqli_fetch_assoc($PetitionCalculateAbono);
    $NumRow3['SUMAbono'] = (empty($NumRow3['SUMAbono'])) ? 0 : @$NumRow3['SUMAbono'];
    $SaldoPendiente = $row_2['Total'] - $NumRow3['SUMAbono'];

    $Total_Pendiente += $SaldoPendiente;
  }

  return $Total_Pendiente;
}

function Calculate_Product_Row($Row)
{
  global $connection, $p1_t1_warehouse_inventory;
  //Recorre los
  $Row['Data_Batch'] = (empty($Row['Data_Batch'])) ? '[]' : $Row['Data_Batch']; // se crea solo porque bill-touch aun no guarda el data_batch de los productos de cuentas temporales
  $Batch = @json_decode($Row['Data_Batch']);
  $ArrayBatches = array();

  foreach ($Batch as $value) {
    $results_3 = mysqli_query($connection, "SELECT * FROM " . $p1_t1_warehouse_inventory . " WHERE Id = " . $value->Id_Batch);
    $row_3 = @mysqli_fetch_assoc($results_3);
    $row_3['Units'] = $value->Units;

    array_push($ArrayBatches, $row_3);
  }
  $Row['Batch'] = $ArrayBatches;

  //obtiene valores atomicos
  $Base = $Row['Price'];
  $Desc = $Row['Discount'];
  $Impo = $Row['Porcetage_Impo'];
  $Iva = $Row['Porcentaje'];
  $Unit = $Row['Units'];


  //suma los porcentajes  impuestos estalecidos para capturar los dos impuestos
  $TempImpues =  $Impo + $Iva;
  //establece la cantidad de descuento por cada cantidad registrada
  $Temp_DescxUnit = $Desc / $Unit;
  //al descuento le retira el valor que deberia ser de los impuestos
  $TemDesc = $Temp_DescxUnit / (($TempImpues / 100) + 1);

  //establece valor de impoconsumo
  $TempImpo = ($Base - $TemDesc) * ($Impo / 100);
  $TempImpo = ($Base - (($Desc / $Unit) / ((($Impo + $Iva) / 100) + 1))) * ($Impo / 100);

  //establece valor de iva
  $TempIva = ($Base - $TemDesc) * ($Iva / 100);

  //Establece el valor de Interes del porcentaje asignado
  // $TemInterest = $Base*(floatval($row_1['PercentageInterest'])/100);
  $TemInterest = 0;


  //subtotal multiplicando por unidades
  $TempSubTotal = ($Base - $TemDesc + $TemInterest) * $Unit;

  //Genera total
  $TempTotal = $TempSubTotal + (($TempImpo + $TempIva) * $Unit);


  //Asigna los valores optenidos al producto
  $Row['Price'] = $Base;
  $Row['Price_U'] = ($Base) + $TempImpo + $TempIva;
  $Row['Discount'] = $Desc;

  $Row['Subtotal'] = $TempSubTotal;
  $Row['Porcetage_Impo'] = $Impo;
  $Row['Porcentaje'] = $Iva;

  $Row['Units'] = $Unit;
  $Row['Iva'] = $TempIva;
  $Row['ipoconsumo'] = $TempImpo;

  $Row['Interest'] = $TemInterest;

  $Row['Total'] = $TempTotal;


  return $Row;
}

function Calculate_Total_Bill_Old($Id, $Show = array('Products', 'Credit', 'Notes'))
{

  global $connection, $p3_t1_bills, $p3_t1_bills_c1_products, $p1_t1_warehouse_inventory, $p4_t1_vouchers_ingress, $p4_t3_vouchers_ingress_c3_method_pay, $p2_t2_clients_credit_notes, $p2_t2_c1_clients_credit_notes_products, $p3_t1_bills_c7_credits_program_pay, $p3_t1_bills_c7_history_program_pay;

  // var_dump($Show);
  //  var_dump(in_array("Credit", $Show));
  // Consulta factura
  $results_1 = mysqli_query($connection, "SELECT * FROM " . $p3_t1_bills . " WHERE Id = " . $Id . " ");
  $row_1 = @mysqli_fetch_assoc($results_1);

  //Variables Totalidades de la factura
  $ArrProducts = array();
  $SUMTempTotal = 0;
  $SUMTempSubTotal = 0;
  $SUMTemDesc = 0;
  $SUMTempImpo = 0;
  $SUMTempIva = 0;
  $SUMBase = 0;
  $SUMInterest = 0;

  //Consulta productos

  $results_2 = mysqli_query($connection, "SELECT * FROM " . $p3_t1_bills_c1_products . " WHERE Id_Bill = " . $row_1['Id'] . " ORDER BY Id DESC");
  while ($row_2 = @mysqli_fetch_assoc($results_2)) {

    $row_2 = Calculate_Product_Row($row_2);


    $Base = $row_2['Price'];
    $Desc = $row_2['Discount'];
    $Unit = $row_2['Units'];

    $SUMBase += $Base * $Unit; // ACUMULA
    $SUMTemDesc += $Desc; // ACUMULA

    $SUMTempImpo += $row_2['ipoconsumo'] * $Unit; // ACUMULA

    $SUMTempIva += $row_2['Iva'] * $Unit; // ACUMULA

    $SUMInterest += $row_2['Interest'] * $Unit;

    $SUMTempSubTotal += $row_2['Subtotal']; // ACUMULA

    $SUMTempTotal += $row_2['Total']; // ACUMULA

    array_push($ArrProducts, $row_2);
  }

  //Suma la propina al total
  if ($row_1['Propina'] < 100) {
    $row_1['Propina'] = $SUMTempTotal * ($row_1['Propina'] / 100);
  }

  $SUMTempTotal = $SUMTempTotal + $row_1['Propina'];

  //agrega los productos a la factura
  if (in_array("Products", $Show)) {
    $row_1['Productos'] = $ArrProducts;
  }
  //Asigna los valores optenidos a la factura
  $row_1['Products'] = $SUMBase;
  $row_1['Discount'] = $SUMTemDesc;
  $row_1['Subtotal'] = $SUMTempSubTotal;

  $row_1['Iva'] = $SUMTempIva;
  $row_1['Ipoconsumo'] = $SUMTempImpo;

  $row_1['Total_Products'] = $SUMTempTotal - $row_1['Propina'];
  $row_1['Propina'] = 0 + @$row_1['Propina'];
  $row_1['Interest'] = $SUMInterest;
  $row_1['Total'] = $SUMTempTotal;

  //================ Si es credito, aporta la informacion de pagado y pendiente ================
  if (($row_1['Type'] == "Credit" || $row_1['Type'] == "Aside") && in_array("Credit", $Show)) {
    $ArrMethods = array();
    $ArrHistory = array();

    $Pay = 0;
    $Pending = 0;

    $results_2 = mysqli_query($connection, "SELECT * FROM " . $p4_t1_vouchers_ingress . " WHERE Source ='deposit' AND Source_Value = " . $row_1['Id'] . " ");
    while ($row_2 = @mysqli_fetch_assoc($results_2)) { //recorre comprobantes
      $Abono = 0;
      $results_3 = mysqli_query($connection, "SELECT * FROM " . $p4_t3_vouchers_ingress_c3_method_pay . " WHERE Id_Source = " . $row_2['Id'] . "");
      while ($row_3 = @mysqli_fetch_assoc($results_3)) { //recorre metodos de pago
        if (!isset($ArrMethods[$row_3['Id_Method']])) {
          $ArrMethods[$row_3['Id_Method']] = 0;
        }
        if ($row_2['State'] == "Active") {
          $Pay += $row_3['Value'];
        }
        $Abono  += $row_3['Value'];
        $ArrMethods[$row_3['Id_Method']] += $row_3['Value'];
      }
      array_push($ArrHistory, array('Id' => $row_2['Id'], 'Date' => $row_2['Date'], 'Abono' => $Abono, 'Name_Payer' => $row_2['Name_Payer'], 'State' => $row_2['State']));
    }

    $ArrProgramPay = array();
    $State = 'Al dia';

    $results_4 = mysqli_query($connection, "SELECT * FROM " . $p3_t1_bills_c7_credits_program_pay . " WHERE Id_bill = " . $row_1['Id'] . "");
    while ($row_4 = @mysqli_fetch_assoc($results_4)) { //recorre metodos de pago

      if ($row_4['Date'] <= date('U') && $row_4['Paid'] == 'n') {
        $State = 'En mora';
      }

      //$Pay = 0;
      $Date = '';

      $results_5 = mysqli_query($connection, "SELECT * FROM " . $p3_t1_bills_c7_history_program_pay . " WHERE Id_Payment = " . $row_4['Id'] . " ORDER BY Id ASC");
      while ($row_5 = mysqli_fetch_assoc($results_5)) {
        //   $Pay += $row_5['Value_Pay'];
        $Date = $row_5['Date'];
      }

      $row_4['Date_Pay'] = ($row_4['Paid'] == 'y') ? $Date : ''; //si ya se encuentra pago establece la fecha de pago al ultimo abono
      $row_4['Value_Pay'] =  $Pay;

      array_push($ArrProgramPay, $row_4);
    }


    $Pending = $row_1['Total_Total'] - $Pay;
    $row_1['Data_Credit'] = array('Pay' =>  $Pay, 'Pending' => $Pending, 'Methods' => $ArrMethods, 'History' => $ArrHistory, 'ProgramPay' =>  $ArrProgramPay, 'ProgramPayState' =>  $State);
  }
  //================ Fin seccion de credito ================


  //================ Notas Credito =========================
  if (in_array("Notes", $Show)) {

    $Consult_NotesCredits = mysqli_query($connection, "SELECT * FROM " . $p2_t2_clients_credit_notes . " WHERE State='Active' AND Id_Bill = " . $row_1['Id']);

    if (mysqli_num_rows($Consult_NotesCredits) > 0) {
      $ArrNotesCredit = array();
      $SUMTotalNotes = 0;
      foreach ($Consult_NotesCredits as $Row_Note) {
        $SUMTotalNotes += $Row_Note['Total'];

        $ArrProductsNote = array();
        $Query_Products = mysqli_query($connection, 'SELECT Id_BillP,(SELECT Product FROM ' . $p3_t1_bills_c1_products . ' WHERE Id = Id_BillP) AS Product,Units FROM ' . $p2_t2_c1_clients_credit_notes_products . ' WHERE Id_CreditNote = ' . $Row_Note['Id']);

        foreach ($Query_Products as $row) {
          $row['State'] = '';
          array_push($ArrProductsNote, $row);
        }

        $Row_Note['Products'] = $ArrProductsNote;
        array_push($ArrNotesCredit, $Row_Note);
      }

      $row_1['Credit_Notes'] = array(
        'Total' => $SUMTotalNotes,
        'Notes' => $ArrNotesCredit
      );
    }
  }


  //================ Fin seccion de Notas credito ================



  return $row_1;
}

// function Calculate_Total_Bill($Id,$Show = array('Products','Credit','Notes')){
//    global $connection, $p3_t1_bills, $p3_t1_bills_c1_products,$p1_t1_warehouse_inventory,$p4_t1_vouchers_ingress,$p4_t3_vouchers_ingress_c3_method_pay,$p2_t2_clients_credit_notes,$p2_t2_c1_clients_credit_notes_products,$p3_t1_bills_c7_credits_program_pay,$p3_t1_bills_c7_history_program_pay,$p3_t1_bills_c1_products_c1_components;

//   // var_dump($Show);
//   //  var_dump(in_array("Products", $Show));
//     // Consulta factura
//     $results_1 = mysqli_query($connection, "SELECT * FROM p3_t1_bills WHERE Id = ".$Id." ");
// 		// die("SELECT * FROM p3_t1_bills WHERE Id = ".$Id." ");
//     $row_1 = @mysqli_fetch_assoc($results_1);
//     //================ Productos =========================

//     if (in_array("Products", $Show)) {
//       $ArrProducts = array();
// 			$ArrProductsComponents = array();

//      // die("SELECT * FROM p3_t1_bills_products WHERE Id_Bill = ".$row_1['Id']." ORDER BY Id DESC");
//       $results_2 = mysqli_query($connection, "SELECT * FROM p3_t1_bills_products WHERE Id_Bill = ".$row_1['Id']." ORDER BY Id DESC");
// 			// die("SELECT * FROM p3_t1_bills_products WHERE Id_Bill = ".$row_1['Id']." ORDER BY Id DESC");
//       foreach ($results_2 as $row_2) {
// 			// while ($row_2 = mysqli_fetch_assoc($results_2)) {
// 				$results_components = mysqli_query($connection, "SELECT * FROM ".$p3_t1_bills_c1_products_c1_components." WHERE Id_Bill_Product = ".$row_2['Id']." ORDER BY Id DESC");
// 				 // echo "SELECT * FROM ".$p3_t1_bills_c1_products_c1_components." WHERE Id_Bill_Product = ".$row_2['Id']." ORDER BY Id DESC <br />";
// 				foreach ($results_components as $row_components) {

// 					array_push($ArrProductsComponents,$row_components);
// 				}
// 				// code...
// 				array_push($ArrProducts,$row_2);
// 			}
//       // }
//       $row_1['Productos'] = $ArrProducts;
// 			// var_dump($ArrProductsComponents);
// 			$row_1['Components'] = $ArrProductsComponents;

//     }
//     //================ Fin seccion de productos ================


//     //================  Credito =========================

//     if (($row_1['Type'] == "Credit" || $row_1['Type'] == "Aside") && in_array("Credit", $Show)) {
//       $ArrMethods = array();
//       $ArrHistory = array();

//       $Pay = 0;
//       $Pending = 0;

//       $results_2 = mysqli_query($connection, "SELECT * FROM ".$p4_t1_vouchers_ingress." WHERE Source ='deposit' AND Source_Value = ".$row_1['Id']." ");
//       while($row_2 = @mysqli_fetch_assoc($results_2)){//recorre comprobantes
//         $Abono = 0;
//         $results_3 = mysqli_query($connection, "SELECT * FROM ".$p4_t3_vouchers_ingress_c3_method_pay." WHERE Id_Source = ".$row_2['Id']."");
//         while($row_3 = @mysqli_fetch_assoc($results_3)){//recorre metodos de pago
//           if (!isset($ArrMethods[$row_3['Id_Method']])) {
//            $ArrMethods[$row_3['Id_Method']] = 0;
//           }
//           if ($row_2['State'] == "Active") {
//             $Pay += $row_3['Value'];
//           }
//           $Abono  += $row_3['Value'];
//           $ArrMethods[$row_3['Id_Method']] += $row_3['Value'];

//         }
//         array_push($ArrHistory,array('Id' => $row_2['Id'] ,'Date' => $row_2['Date'] , 'Abono' => $Abono, 'Name_Payer' => $row_2['Name_Payer'], 'State' => $row_2['State']));
//       }

//       $ArrProgramPay = array();
//       $State = 'Al dia';

//       $results_4 = mysqli_query($connection, "SELECT * FROM ".$p3_t1_bills_c7_credits_program_pay." WHERE Id_bill = ".$row_1['Id']."");
//       while($row_4 = @mysqli_fetch_assoc($results_4)){//recorre metodos de pago

//         if ($row_4['Date'] <= date('U') && $row_4['Paid'] == 'n') {
//           $State = 'En mora';
//         }

//         //$Pay = 0;
//         $Date= '';

//         $results_5 = mysqli_query($connection, "SELECT * FROM ".$p3_t1_bills_c7_history_program_pay." WHERE Id_Payment = ".$row_4['Id']." ORDER BY Id ASC");
//         while($row_5 = mysqli_fetch_assoc($results_5)){
//        //   $Pay += $row_5['Value_Pay'];
//           $Date= $row_5['Date'];

//         }

//         $row_4['Date_Pay'] = ($row_4['Paid'] == 'y') ? $Date : ''; //si ya se encuentra pago establece la fecha de pago al ultimo abono
//         $row_4['Value_Pay'] =  $Pay;

//         array_push($ArrProgramPay,$row_4);

//       }


//       $Pending = $row_1['Total_Total'] - $Pay;
//       $row_1['Data_Credit'] = array('Pay' =>  $Pay, 'Pending' => $Pending, 'Methods' => $ArrMethods, 'History' => $ArrHistory, 'ProgramPay' =>  $ArrProgramPay, 'ProgramPayState' =>  $State);
//     }
//     //================ Fin seccion de credito ================


//     //================ Notas Credito =========================
//     if (in_array("Notes", $Show)) {

//       $Consult_NotesCredits = mysqli_query($connection, "SELECT * FROM ".$p2_t2_clients_credit_notes." WHERE State='Active' AND Id_Bill = ".$row_1['Id']);

//       if (mysqli_num_rows($Consult_NotesCredits) > 0) {
//         $ArrNotesCredit = array();
//         $SUMTotalNotes = 0;
//         foreach ($Consult_NotesCredits as $Row_Note ) {
//           $SUMTotalNotes += $Row_Note['Total'];

//             $ArrProductsNote = array();
//             $Query_Products = mysqli_query($connection , 'SELECT Id_BillP,(SELECT Product FROM '.$p3_t1_bills_c1_products.' WHERE Id = Id_BillP) AS Product,Units FROM '.$p2_t2_c1_clients_credit_notes_products.' WHERE Id_CreditNote = '.$Row_Note['Id']);

//             foreach ($Query_Products as $row ) {
//               $row['State'] = '';
//               array_push($ArrProductsNote,$row);

//             }

//             $Row_Note['Products'] = $ArrProductsNote;
//             array_push($ArrNotesCredit,$Row_Note);

//         }

//         $row_1['Credit_Notes'] = array(
//           'Total' => $SUMTotalNotes,
//           'Notes' => $ArrNotesCredit
//         );
//       }
//     }

//   //   die(var_dump($row_1['products']));
//    return $row_1;
// }


function Calculate_Total_Bill($Id, $Show = array('Products', 'Credit', 'Notes', 'Printers'), $viewConsult = 'lite',$Total_Visual = true)
{
  global $connection, $p3_t1_bills, $p3_t1_bills_c1_products, $p1_t1_warehouse_inventory, $p4_t1_vouchers_ingress, $p4_t3_vouchers_ingress_c3_method_pay, $p2_t2_clients_credit_notes, $p2_t2_c1_clients_credit_notes_products, $p3_t1_bills_c7_credits_program_pay, $p3_t1_bills_c7_history_program_pay, $p1_t1_inventory_sele_c1_products_groups;

  $Printers = array();

  $View = ($viewConsult == 'lite') ? 'p3_t1_bills_lite' : 'p3_t1_bills'; //se establece a que vista consulta ya que en facturación electrónica si requiere los metodos

  $results_1 = mysqli_query($connection, "SELECT * FROM " . $View . " WHERE Id = " . $Id . " ");
  $row_1 = @mysqli_fetch_assoc($results_1);

  $row_1['Total_Total'] = $row_1['Total_Total'] - $row_1['Rete_Fuente'] - $row_1['Rete_ICA'];

  //================ Productos =========================
  if (in_array("Products", $Show)) {
    $ArrProducts = array();
    $Cant_Units = 0;
    $results_2 = mysqli_query($connection, "SELECT BP.*,(SELECT Printer FROM " . $p1_t1_inventory_sele_c1_products_groups . " WHERE Code = BP.Code_Group ) AS Name_Printer FROM p3_t1_bills_products BP WHERE BP.Id_Bill = " . $row_1['Id'] . " ORDER BY BP.Code_Group DESC");
    foreach ($results_2 as $row_2) {
      $Cant_Units += $row_2['Units'];


      if (!$Total_Visual) {
        $row_2['Subtotal'] = $row_2['Price']*$row_2['Units'];
        $row_2['Total'] = ($row_2['Price']+$row_2['Iva_Total']+$row_2['ipoconsumo_Total'])*$row_2['Units'];
      }


      array_push($ArrProducts, $row_2);

      if (!in_array($row_2['Name_Printer'], $Printers)) {
        array_push($Printers,  $row_2['Name_Printer']);
      }

    }
    $row_1['Productos'] = $ArrProducts;
    $row_1['Cant_Units'] = $Cant_Units;
  }

  //================ Fin seccion de productos ================

  //================  Credito =========================

  if (($row_1['Type'] == "Credit" || $row_1['Type'] == "Aside") && in_array("Credit", $Show)) {
    $ArrMethods = array();
    $ArrHistory = array();

    $Pay = 0;
    $Pending = 0;
    $Total = 0;

    $results_2 = mysqli_query($connection, "SELECT * FROM " . $p4_t1_vouchers_ingress . " WHERE Source ='deposit' AND Source_Value = " . $row_1['Id'] . " ");
    while ($row_2 = @mysqli_fetch_assoc($results_2)) { //recorre comprobantes
      $Abono = 0;
      $results_3 = mysqli_query($connection, "SELECT * FROM " . $p4_t3_vouchers_ingress_c3_method_pay . " WHERE Id_Source = " . $row_2['Id'] . "");
      while ($row_3 = @mysqli_fetch_assoc($results_3)) { //recorre metodos de pago
        if (!isset($ArrMethods[$row_3['Id_Method']])) {
          $ArrMethods[$row_3['Id_Method']] = 0;
        }
        if ($row_2['State'] == "Active") {
          $Pay += $row_3['Value'];
        }
        $Abono  += $row_3['Value'];
        $ArrMethods[$row_3['Id_Method']] += $row_3['Value'];
      }
      array_push($ArrHistory, array('Id' => $row_2['Id'], 'Date' => $row_2['Date'], 'Abono' => $Abono, 'Name_Payer' => $row_2['Name_Payer'], 'State' => $row_2['State']));
    }

    $ArrProgramPay = array();
    $State = 'Al dia';
    $Financed_value = 0;

    $sql = "SELECT PP.*,((PP.Value+PP.Interest+PP.Value_Mora+PP.Cobranza)-PP.Discount) AS Total, (SELECT SUM(HP.Value_Pay) FROM ".$p3_t1_bills_c7_history_program_pay. " HP WHERE  HP.Id_Payment = PP.Id) AS Total_Paid FROM " . $p3_t1_bills_c7_credits_program_pay . " PP WHERE PP.Id_bill = " . $row_1['Id'] ;
    // die($sql);

    $results_4 = mysqli_query($connection, $sql);

    while ($row_4 = @mysqli_fetch_assoc($results_4)) { //recorre cuotas


      if ($row_4['Total_Paid'] >= $row_4['Total']) {
        $row_4['Paid'] = 'y';
      } else {
        $row_4['Paid'] = 'n';
      }

      // if ($Pay >= $row_4['Number'] * $row_4['Value']) {
      //   $row_4['Paid'] = 'y';
      // } else {
      //   $row_4['Paid'] = 'n';
      // }
      
      if($State != 'Mora'){
        if ($row_4['Date'] <= date('U') && $row_4['Paid'] == 'n') {
          $State = 'Vencida';
        }

        $Date_31 = $row_4['Date'] + 2716143;
        // echo $Date_31.'---------';
        if($Date_31 <= date('U') && $row_4['Paid'] == 'n'){
            $State = 'Mora';
        }
      }


      //$Pay = 0;
      $Date = '';

      $results_5 = mysqli_query($connection, "SELECT * FROM " . $p3_t1_bills_c7_history_program_pay . " WHERE Id_Payment = " . $row_4['Id'] . " ORDER BY Id ASC");
      while ($row_5 = mysqli_fetch_assoc($results_5)) {
        //   $Pay += $row_5['Value_Pay'];
        $Date = $row_5['Date'];
      }

      $row_4['Date_Pay'] = ($row_4['Paid'] == 'y') ? $Date : ''; //si ya se encuentra pago establece la fecha de pago al ultimo abono
      $row_4['Value_Pay'] =  $Pay;

      $Total += $row_4['Total'];
      $Pending += $row_4['Total'] - $row_4['Total_Paid'];

      $Financed_value += $row_4['Value'];
      array_push($ArrProgramPay, $row_4);
    }


    
    $row_1['Data_Credit'] = array(
      'Pay' =>  $Pay,
      'Pending' => $Pending,
      'Methods' => $ArrMethods,
      'History' => $ArrHistory,
      'ProgramPay' => $ArrProgramPay,
      'Financed_value' => $Financed_value,
      'Total_value' => $Total,
      'ProgramPayState' => $State
    );
  }
  //================ Fin seccion de credito ================


  //================ Notas Credito =========================
  if (in_array("Notes", $Show)) {

    $Consult_NotesCredits = mysqli_query($connection, "SELECT * FROM " . $p2_t2_clients_credit_notes . " WHERE State='Active' AND Id_Bill = " . $row_1['Id']);

    if (mysqli_num_rows($Consult_NotesCredits) > 0) {
      $ArrNotesCredit = array();
      $SUMTotalNotes = 0;
      foreach ($Consult_NotesCredits as $Row_Note) {
        $SUMTotalNotes += $Row_Note['Total'];

        $ArrProductsNote = array();
        $Query_Products = mysqli_query($connection, 'SELECT Id_BillP,(SELECT Product FROM ' . $p3_t1_bills_c1_products . ' WHERE Id = Id_BillP) AS Product,Units FROM ' . $p2_t2_c1_clients_credit_notes_products . ' WHERE Id_CreditNote = ' . $Row_Note['Id']);

        foreach ($Query_Products as $row) {
          $row['State'] = '';
          array_push($ArrProductsNote, $row);
        }

        $Row_Note['Products'] = $ArrProductsNote;
        array_push($ArrNotesCredit, $Row_Note);
      }

      $row_1['Credit_Notes'] = array(
        'Total' => $SUMTotalNotes,
        'Notes' => $ArrNotesCredit
      );
    }
  }
  if (in_array("Printers", $Show)) {
    $row_1['Printers'] = $Printers;
  }

  //   die(var_dump($row_1['products']));
  return $row_1;
}

function Calculate_Total_Remission($Id, $Show = array('Products', 'Credit', 'Notes'))
{
  global $connection, $p7_t1_remissions, $p7_t1_remissions_c1_products, $p1_t1_warehouse_inventory;

  $results_1 = mysqli_query($connection, "SELECT * FROM p7_t1_remissions WHERE Id = " . $Id . " ");
  $row_1 = @mysqli_fetch_assoc($results_1);
  //================ Productos =========================
  if (in_array("Products", $Show)) {
    $ArrProducts = array();
    $results_2 = mysqli_query($connection, "SELECT * FROM p7_t1_remissions_products WHERE Id_Bill = " . $row_1['Id'] . " ORDER BY Id DESC");
    foreach ($results_2 as $row_2) {
      array_push($ArrProducts, $row_2);
    }
    $row_1['Productos'] = $ArrProducts;
  }
  //================ Fin seccion de productos ================
  return $row_1;
}


function Calculate_Total_Buy($Id, $Show = array('Products', 'Credit', 'Notes'))
{
  global $connection, $p9_t1_buys, $p9_t1_buys_c1_products, $p1_t1_warehouse_inventory, $p9_t1_buys_c2_payments, $p9_t1_buys_payments_c3_method_pay, $p2_t2_clients_credit_notes, $p2_t2_c1_clients_credit_notes_products; // var_dump($Show);
  //  var_dump(in_array("Products", $Show));
  // Consulta factura
  $results_1 = mysqli_query($connection, "SELECT * FROM p9_t1_buy WHERE Id = " . $Id . " ");
  // die("SELECT * FROM p3_t1_bills WHERE Id = ".$Id." ");
  $row_1 = @mysqli_fetch_assoc($results_1);
  //================ Productos =========================

  if (in_array("Products", $Show)) {
    $ArrProducts = array();
    $ArrProductsComponents = array();

    // die("SELECT * FROM p3_t1_bills_products WHERE Id_Bill = ".$row_1['Id']." ORDER BY Id DESC");
    $results_2 = mysqli_query($connection, "SELECT * FROM p9_t1_buy_products WHERE Id_Bill = " . $row_1['Id'] . " ORDER BY Id DESC");
    // die("SELECT * FROM p3_t1_bills_products WHERE Id_Bill = ".$row_1['Id']." ORDER BY Id DESC");
    foreach ($results_2 as $row_2) {
      // while ($row_2 = mysqli_fetch_assoc($results_2)) {
      // $results_components = mysqli_query($connection, "SELECT * FROM ".$p3_t1_bills_c1_products_c1_components." WHERE Id_Bill_Product = ".$row_2['Id']." ORDER BY Id DESC");
      // echo "SELECT * FROM ".$p3_t1_bills_c1_products_c1_components." WHERE Id_Bill_Product = ".$row_2['Id']." ORDER BY Id DESC <br />";
      // foreach ($results_components as $row_components) {

      // 	// array_push($ArrProductsComponents,$row_components);
      // }
      // code...
      array_push($ArrProducts, $row_2);
    }
    // }
    $row_1['Productos'] = $ArrProducts;
    // var_dump($ArrProductsComponents);
    // $row_1['Components'] = $ArrProductsComponents;

  }
  //================ Fin seccion de productos ================


  //================  Credito =========================

  if (($row_1['Type'] == "Credit" || $row_1['Type'] == "Aside") && in_array("Credit", $Show)) {
    $ArrMethods = array();
    $ArrHistory = array();

    $Pay = 0;
    $Pending = 0;

    $results_2 = mysqli_query($connection, "SELECT * FROM " . $p9_t1_buys_c2_payments . " WHERE Source ='deposit' AND Source_Value = " . $row_1['Id'] . " ");
    while ($row_2 = @mysqli_fetch_assoc($results_2)) { //recorre comprobantes
      $Abono = 0;
      $results_3 = mysqli_query($connection, "SELECT * FROM " . $p9_t1_buys_payments_c3_method_pay . " WHERE Id_Source = " . $row_2['Id'] . "");
      while ($row_3 = @mysqli_fetch_assoc($results_3)) { //recorre metodos de pago
        if (!isset($ArrMethods[$row_3['Id_Method']])) {
          $ArrMethods[$row_3['Id_Method']] = 0;
        }
        if ($row_2['State'] == "Active") {
          $Pay += $row_3['Value'];
        }
        $Abono  += $row_3['Value'];
        $ArrMethods[$row_3['Id_Method']] += $row_3['Value'];
      }
      $Comment = $row_2['Comment'];
      array_push(
        $ArrHistory,
        array(
          'Id' => $row_2['Id'],
          'Date' => $row_2['Date'],
          'Abono' => $Abono,
          'Comment' => $Comment,
          'Name_Payer' => $row_2['Name_Payer'],
          'State' => $row_2['State']
        )
      );
    }

    // $ArrProgramPay = array();
    // $State = 'Al dia';
    //
    // $results_4 = mysqli_query($connection, "SELECT * FROM ".$p3_t1_bills_c7_credits_program_pay." WHERE Id_bill = ".$row_1['Id']."");
    // while($row_4 = @mysqli_fetch_assoc($results_4)){//recorre metodos de pago
    //
    // 	if ($row_4['Date'] <= date('U') && $row_4['Paid'] == 'n') {
    // 		$State = 'En mora';
    // 	}
    //
    // 	//$Pay = 0;
    // 	$Date= '';
    //
    // 	$results_5 = mysqli_query($connection, "SELECT * FROM ".$p3_t1_bills_c7_history_program_pay." WHERE Id_Payment = ".$row_4['Id']." ORDER BY Id ASC");
    // 	while($row_5 = mysqli_fetch_assoc($results_5)){
    //  //   $Pay += $row_5['Value_Pay'];
    // 		$Date= $row_5['Date'];
    //
    // 	}
    //
    // 	$row_4['Date_Pay'] = ($row_4['Paid'] == 'y') ? $Date : ''; //si ya se encuentra pago establece la fecha de pago al ultimo abono
    // 	$row_4['Value_Pay'] =  $Pay;
    //
    // 	array_push($ArrProgramPay,$row_4);
    //
    // }

    $Pending = $row_1['Total_Total'] - $Pay;
    $row_1['Data_Credit'] = array('Pay' =>  $Pay, 'Pending' => $Pending, 'Methods' => $ArrMethods, 'History' => $ArrHistory);
  }
  //================ Fin seccion de credito ================


  //================ Notas Credito =========================
  if (in_array("Notes", $Show)) {

    $Consult_NotesCredits = mysqli_query($connection, "SELECT * FROM " . $p2_t2_clients_credit_notes . " WHERE State='Active' AND Id_Bill = " . $row_1['Id']);

    if (mysqli_num_rows($Consult_NotesCredits) > 0) {
      $ArrNotesCredit = array();
      $SUMTotalNotes = 0;
      foreach ($Consult_NotesCredits as $Row_Note) {
        $SUMTotalNotes += $Row_Note['Total'];

        $ArrProductsNote = array();
        $Query_Products = mysqli_query($connection, 'SELECT Id_BillP,(SELECT Product FROM ' . $p9_t1_buys_c1_products . ' WHERE Id = Id_BillP) AS Product,Units FROM ' . $p2_t2_c1_clients_credit_notes_products . ' WHERE Id_CreditNote = ' . $Row_Note['Id']);

        foreach ($Query_Products as $row) {
          $row['State'] = '';
          array_push($ArrProductsNote, $row);
        }

        $Row_Note['Products'] = $ArrProductsNote;
        array_push($ArrNotesCredit, $Row_Note);
      }

      $row_1['Credit_Notes'] = array(
        'Total' => $SUMTotalNotes,
        'Notes' => $ArrNotesCredit
      );
    }
  }

  //   die(var_dump($row_1['products']));
  return $row_1;
}

function Set_Discount_Global($Id, $Discount = 0)
{
  global $connection,  $p3_t1_bills_c1_products;

  //consultamos los datos de la factura
  // $Bill = array();
  $Bill = Calculate_Total_Bill($Id);
  //die(var_dump($Bill));

  //si es porcentual, obtiene el valor de la factura y obtiene su valor
  $Discount = ($Discount < 99) ? $Discount = $Bill['Sub_Total'] * ($Discount / 100) : $Discount;

  //recorre los productos de la factura
  foreach ($Bill['Productos'] as $row) {

    //establece el porcentaje de descuento que le corresponde
    $PorcentAssign = ($row['Subtotal'] * 100) / $Bill['Sub_Total'];
    // die('---'.$BILL$rowbtotal']);

    //obtiene el valor monetario del descuento obtenido
    $DiscountAssign = $Discount * ($PorcentAssign / 100);

    //actualiza el campo descuento
    $Qry = 'UPDATE ' . $p3_t1_bills_c1_products . ' SET Discount = ' . $DiscountAssign . ' WHERE Id = ' . $row['Id'];
    $update = mysqli_query($connection, $Qry);
  }
}

function ConsultItems($Id_Products, $MaxItem = 3)
{
  global $connection, $p3_t1_bills_c1_products;

  $CountItems = 0;
  $data = "";

  if (is_array($Id_Products)) { // si en algun lado aun le tre un array
    return $data;
  }
  // die("SELECT * FROM p3_t1_bills_products WHERE Id_Bill = ".$Id_Products);
  $results_3 = mysqli_query($connection, "SELECT
  Units,
  Product,
  ROUND(
      (
          (
              (
                  `bp`.`Price` -(
                      (`bp`.`Discount` / `bp`.`Units`) /(
                          (
                              (
                                  `bp`.`Porcentaje` + `bp`.`Porcetage_Impo`
                              ) / 100
                          ) + 1
                      )
                  )
              ) * `bp`.`Units`
          ) +(
              (
                  (
                      (
                          `bp`.`Price` -(
                              (`bp`.`Discount` / `bp`.`Units`) /(
                                  (
                                      (
                                          `bp`.`Porcentaje` + `bp`.`Porcetage_Impo`
                                      ) / 100
                                  ) + 1
                              )
                          )
                      ) *(`bp`.`Porcentaje` / 100)
                  ) +(
                      (
                          `bp`.`Price` -(
                              (`bp`.`Discount` / `bp`.`Units`) /(
                                  (
                                      (
                                          `bp`.`Porcentaje` + `bp`.`Porcetage_Impo`
                                      ) / 100
                                  ) + 1
                              )
                          )
                      ) *(`bp`.`Porcetage_Impo` / 100)
                  )
              ) * `bp`.`Units`
          )
      ),
      2
  ) AS `Total`
  FROM ".$p3_t1_bills_c1_products." bp WHERE Id_Bill = " . $Id_Products);

  foreach ($results_3 as $row) {
    if ($CountItems <= $MaxItem) {
      $cantidad = $row['Units'];
      $compara = $cantidad - intval($cantidad);
      if ($compara == 0) {
        $cantidad = intval($cantidad);
      }
      $data .= '- ' . $row['Product'] . ' [CANT. ' . $cantidad . '] [$ ' . number_format($row['Total']) . '] <br>';
    } else {
      $data .= '...';
      return $data;
    }
    $CountItems++;
  }
  return $data;
}

function ConsultItems_Rem($Id_Products, $MaxItem = 3)
{
  global $connection;

  $CountItems = 0;
  $data = "";

  if (is_array($Id_Products)) { // si en algun lado aun le tre un array
    return $data;
  }

  $results_3 = mysqli_query($connection, "SELECT * FROM p7_t1_remissions_products WHERE Id_Bill = " . $Id_Products);

  foreach ($results_3 as $row) {
    if ($CountItems <= $MaxItem) {
      $cantidad = $row['Units'];
      $compara = $cantidad - intval($cantidad);
      if ($compara == 0) {
        $cantidad = intval($cantidad);
      }
      $data .= '- ' . $row['Product'] . ' [CANT. ' . $cantidad . '] [$ ' . number_format($row['Total']) . '] <br>';
    } else {
      $data .= '...';
      return $data;
    }
    $CountItems++;
  }
  return $data;
}

function ConsultItems_Buy($Id_Products, $MaxItem = 3)
{
  global $connection;

  $CountItems = 0;
  $data = "";

  if (is_array($Id_Products)) { // si en algun lado aun le tre un array
    return $data;
  }

  $results_3 = mysqli_query($connection, "SELECT * FROM p9_t1_buy_products WHERE Id_Bill = " . $Id_Products);

  foreach ($results_3 as $row) {
    if ($CountItems <= $MaxItem) {
      $cantidad = $row['Units'];
      $compara = $cantidad - intval($cantidad);
      if ($compara == 0) {
        $cantidad = intval($cantidad);
      }
      $data .= '- ' . $row['Product'] . ' [CANT. ' . $cantidad . '] [$ ' . number_format($row['Total']) . '] <br>';
    } else {
      $data .= '...';
      return $data;
    }
    $CountItems++;
  }
  return $data;
}

function ConsultItems_Buy_Order($Id_Products, $MaxItem = 3)
{
  global $connection, $p33_t2_purchase_products;

  $CountItems = 0;
  $data = "";

  if (is_array($Id_Products)) { // si en algun lado aun le tre un array
    return $data;
  }

  $results_3 = mysqli_query($connection, "SELECT * FROM " . $p33_t2_purchase_products . " WHERE Id_Bill = " . $Id_Products);

  foreach ($results_3 as $row) {
    if ($CountItems <= $MaxItem) {
      $cantidad = $row['Units'];
      $compara = $cantidad - intval($cantidad);
      if ($compara == 0) {
        $cantidad = intval($cantidad);
      }
      $data .= '- ' . $row['Product'] . ' [CANT. ' . $cantidad . '] [$ ' . number_format($row['Price_Cost']) . '] <br>';
    } else {
      $data .= '...';
      return $data;
    }
    $CountItems++;
  }
  return $data;
}

function ConsultItems_Buy_Service($Id_Products, $MaxItem = 3)
{
  global $connection, $p34_t2_purchase_service_products;

  $CountItems = 0;
  $data = "";

  if (is_array($Id_Products)) { // si en algun lado aun le tre un array
    return $data;
  }

  $results_3 = mysqli_query($connection, "SELECT * FROM " . $p34_t2_purchase_service_products . " WHERE Id_Bill = " . $Id_Products);

  foreach ($results_3 as $row) {
    if ($CountItems <= $MaxItem) {
      $cantidad = $row['Units'];
      $compara = $cantidad - intval($cantidad);
      if ($compara == 0) {
        $cantidad = intval($cantidad);
      }
      $data .= '- ' . $row['Product'] . ' [CANT. ' . $cantidad . '] [$ ' . number_format($row['Price_Cost']) . '] <br>';
    } else {
      $data .= '...';
      return $data;
    }
    $CountItems++;
  }
  return $data;
}

function Insert_Method_Pay($Id_Source, $Datos, $Table)
{
  global $connection;

  $Data = 0;

  $Query_Delete = 'DELETE FROM ' . $Table . ' WHERE Id_Source = ' . $Id_Source;
  $Qry_Delete = mysqli_query($connection, $Query_Delete);

  if ($Qry_Delete) {
    // for($i=0;$i<100;$i++){

    if (count($Datos) != 0) {
      for ($i = 0; $i < count($Datos); $i++) {

        if (empty($Datos[$i]['Id_Method'])) {
          continue;
        }
        // die($Datos[$i]['Id_Method'].'----');
        $Query_Method_Pay = 'INSERT INTO ' . $Table . ' ( Id_Source , Id_Method , Value ) VALUES ( ' . $Id_Source . ' , ' . $Datos[$i]['Id_Method'] . ' , ' . $Datos[$i]['Value'] . ' )';
        $Qry_Method_Pay = mysqli_query($connection, $Query_Method_Pay);

        if (!$Qry_Method_Pay) {
          echo  mysqli_error($connection);
          echo  '<script>console.log(`' . $Query_Method_Pay . '`)</script>';
          die($Qry_Method_Pay);
        } else {
          $Data = "";
        }
      }
    }
  } else {
    echo mysqli_error($connection) . "Mtds Pay";
  }

  return $Data;
}

function Insert_Log($Type, $Description, $Name_Element, $Id_Element, $Id_module)
{
  global $p0_t3_segurity_log, $connection;
  $Query_Log = ' INSERT INTO ' . $p0_t3_segurity_log . ' (Type , Description , Name_Element , Id_Element , Id_module , Date , User ) VALUES ("' . $Type . '" , "' . $Description . '" , "' . $Name_Element . '" , ' . $Id_Element . ' , ' . $Id_module . ' , ' . date('U') . ' , "'.@$_SESSION['User_Code']. '")';

  $Qry_Log = mysqli_query($connection, $Query_Log);
  if (!$Qry_Log) {
    echo ' <script> console.log(`'.$Query_Log.'`) </script> ';
    echo ' <script> console.log(`'.mysqli_error($connection).'`) </script> ';
  }
}


function Consult_Methods_Bill($Id_Source, $Total_Bill = 0, $Type = "Bill", $view_array = false)
{
  global $connection, $p4_t3_vouchers_ingress_c3_method_pay, $p0_t8_method_pay, $p4_t4_vouchers_egress_c4_method_pay, $p3_t1_bills_c1_method_pay, $p9_t1_buys_c3_method_pay, $p9_t1_buys_payments_c3_method_pay, $p7_t1_remissions_c1_method_pay, $p31_t3_document_support_c4_method_pay;

  $Data_Method = '';
  $Total_Methods = 0;

  if ($Type == 'Bill') {

    $ArrayMethods = array();

    $Query_Consult =  ' SELECT MP.Id,Name,Value FROM ' . $p3_t1_bills_c1_method_pay . ' ING INNER JOIN ' . $p0_t8_method_pay . ' MP ON ING.Id_Method = MP.Id WHERE Id_Source = ' . $Id_Source;
    $Qry_Consult = mysqli_query($connection, $Query_Consult);

    foreach ($Qry_Consult as $Data_Consult) {
      $Total_Methods += $Data_Consult['Value'];
      $Data_Method .= $Data_Consult['Name'] . ': $' . number_format($Data_Consult['Value']) . '<br>';

      $arrayMethod = array('Id' => $Data_Consult['Id'], 'Name' => $Data_Consult['Name'], 'Value' => $Data_Consult['Value']);
      array_push($ArrayMethods, $arrayMethod);
    }

    $Data_Method .= 'Efectivo: $' . (number_format($Total_Bill - $Total_Methods));

    if ($view_array) { //si se necesita enviar el arreglo
      $ValueCash = $Total_Bill - $Total_Methods;
      $arrayMethod = array('Id' => 1, 'Name' => 'Efectivo', 'Value' => $ValueCash);
      array_push($ArrayMethods, $arrayMethod);

      $Data_Method = $ArrayMethods; //retorna arreglo con los metodos
    }
  } elseif ($Type == 'Ingress') {
    $Data_Method = '
                      <table>
                      <tbody>';
    $Total_Ingress = 0;
    $Query_Consult =  ' SELECT Name,Value FROM ' . $p4_t3_vouchers_ingress_c3_method_pay . ' ING INNER JOIN ' . $p0_t8_method_pay . ' MP ON ING.Id_Method = MP.Id WHERE Id_Source = ' . $Id_Source;
    $Qry_Consult = mysqli_query($connection, $Query_Consult);

    foreach ($Qry_Consult as $Data_Consult) {
      $Total_Methods += $Data_Consult['Value'];
      $Data_Method .= '<tr><td style="text-align:right"> <b> ' . $Data_Consult['Name'] . ': </b> </td><td> $' . number_format($Data_Consult['Value'], 2, ",", ".") . '</td></tr>';
    }

    $Data_Method .= '</tbody>
                      </table>
                      ';
  } elseif ($Type == 'Egress') {

    $Query_Consult =  ' SELECT Name,Value FROM ' . $p4_t4_vouchers_egress_c4_method_pay . ' ING INNER JOIN ' . $p0_t8_method_pay . ' MP ON ING.Id_Method = MP.Id WHERE Id_Source = ' . $Id_Source;
    $Qry_Consult = mysqli_query($connection, $Query_Consult);
    $Data_Method = '
                      <table>
                      <tbody>';
    foreach ($Qry_Consult as $Data_Consult) {
      $Total_Methods += $Data_Consult['Value'];
      $Data_Method .= '<tr><td style="text-align:right"> <b> ' . $Data_Consult['Name'] . ': </b> </td><td> $' . number_format($Data_Consult['Value'], 2, ",", ".") . '</td></tr>';
      // $Data_Method.= $Data_Consult['Name'].': $'.number_format($Data_Consult['Value']).'<br>';
    }
    $Data_Method .= '</tbody>
      </table>
      ';
  } elseif ($Type == 'Support') {

    $Query_Consult =  ' SELECT Name,Value FROM ' . $p31_t3_document_support_c4_method_pay . ' ING INNER JOIN ' . $p0_t8_method_pay . ' MP ON ING.Id_Method = MP.Id WHERE Id_Source = ' . $Id_Source;
    //die($Query_Consult);
    $Qry_Consult = mysqli_query($connection, $Query_Consult);

    foreach ($Qry_Consult as $Data_Consult) {
      $Total_Methods += $Data_Consult['Value'];
      $Data_Method .= $Data_Consult['Name'] . ': $' . number_format($Data_Consult['Value']) . '<br>';
    }
  } elseif ($Type == 'buys_pays') {

    $Query_Consult =  'SELECT Name,Value FROM ' . $p9_t1_buys_payments_c3_method_pay . ' ING INNER JOIN ' . $p0_t8_method_pay . ' MP ON ING.Id_Method = MP.Id WHERE Id_Source = ' . $Id_Source;
    // die($Query_Consult);
    $Qry_Consult = mysqli_query($connection, $Query_Consult);

    foreach ($Qry_Consult as $Data_Consult) {
      $Total_Methods += $Data_Consult['Value'];
      $Data_Method .= $Data_Consult['Name'] . ': $' . number_format($Data_Consult['Value']) . '<br>';
    }
  } elseif ($Type == 'buys') {
    $ArrayMethods = array();

    $Query_Consult =  ' SELECT MP.Id,Name,Value FROM ' . $p9_t1_buys_c3_method_pay . ' ING INNER JOIN ' . $p0_t8_method_pay . ' MP ON ING.Id_Method = MP.Id WHERE Id_Source = ' . $Id_Source;
    $Qry_Consult = mysqli_query($connection, $Query_Consult);

    foreach ($Qry_Consult as $Data_Consult) {
      $Total_Methods += $Data_Consult['Value'];
      $Data_Method .= $Data_Consult['Name'] . ': $' . number_format($Data_Consult['Value']) . '<br>';

      $arrayMethod = array('Id' => $Data_Consult['Id'], 'Name' => $Data_Consult['Name'], 'Value' => $Data_Consult['Value']);
      array_push($ArrayMethods, $arrayMethod);
    }

    $Data_Method .= 'Efectivo: $' . (number_format($Total_Bill - $Total_Methods));

    // if ($view_array) {//si se necesita enviar el arreglo
    //   $ValueCash = $Total_Bill-$Total_Methods;
    //   $arrayMethod = array('Id' => 1,'Name' => 'Efectivo','Value' => $ValueCash);
    //   array_push($ArrayMethods,$arrayMethod);

    //   $Data_Method = $ArrayMethods;//retorna arreglo con los metodos
    // }
  } elseif ($Type == 'Remission') {
    $ArrayMethods = array();

    $Query_Consult =  ' SELECT MP.Id,Name,Value FROM ' . $p7_t1_remissions_c1_method_pay . ' ING INNER JOIN ' . $p0_t8_method_pay . ' MP ON ING.Id_Method = MP.Id WHERE Id_Source = ' . $Id_Source;
    $Qry_Consult = mysqli_query($connection, $Query_Consult);

    foreach ($Qry_Consult as $Data_Consult) {
      $Total_Methods += $Data_Consult['Value'];
      $Data_Method .= $Data_Consult['Name'] . ': $' . number_format($Data_Consult['Value']) . '<br>';

      $arrayMethod = array('Id' => $Data_Consult['Id'], 'Name' => $Data_Consult['Name'], 'Value' => $Data_Consult['Value']);
      array_push($ArrayMethods, $arrayMethod);
    }

    $Data_Method .= 'Efectivo: $' . (number_format($Total_Bill - $Total_Methods));

    if ($view_array) { //si se necesita enviar el arreglo
      $ValueCash = $Total_Bill - $Total_Methods;
      $arrayMethod = array('Id' => 1, 'Name' => 'Efectivo', 'Value' => $ValueCash);
      array_push($ArrayMethods, $arrayMethod);

      $Data_Method = $ArrayMethods; //retorna arreglo con los metodos
    }
  }
  return $Data_Method;
}
//Devuelve un ID de lote(Relacion Warehouse_Inventory)
function Consult_Batch_Assign($Id_Business, $Id_Warehouse, $Code_Product, $Suggest_Batch = '')
{
  global $connection, $p0_t1_config_business, $p1_t1_warehouse_inventory, $p1_t1_inventory_sale_c2_products_history_units;
  //consulta el metodo que esta usando actualmente PEPS,UEPS,PP
  $Consult_Business = mysqli_query($connection, 'SELECT Method_Inventory FROM ' . $p0_t1_config_business . ' WHERE Id=' . $Id_Business);
  $Row_Business = mysqli_fetch_assoc($Consult_Business);
  echo mysqli_error($connection);
  // 'PEPS', 'UEPS', 'PP'
  $Order = ''; // se establece inicialmente como promedio ponderado(sin orden)

  //se cambia el orden segun se este establecido el metodo de inventario
  switch ($Row_Business['Method_Inventory']) {
    case 'PEPS': //Primeros en entrar, primeros en salir
      $Order = 'ASC';
      break;
    case 'UEPS': //Ultimos en entrar, primeros en salir
      $Order = 'DESC';
      break;
  }
  //si ya viene con un lote sugerido se llama solo ese lote para el deceso de las unidades
  $Where = (empty($Suggest_Batch)) ? ' Id_Warehouse = ' . $Id_Warehouse . ' AND Id_Inventory = ' . $Code_Product . ' AND State="Active" ' :  ' Id =' . $Suggest_Batch;

  $ArrayBatches = array();
  // Traemos los datos del batch cargado
  // die('SELECT * FROM ' . $p1_t1_warehouse_inventory . ' WHERE ' . $Where . ' ORDER BY Id ' . $Order);
  $Query_Batch = mysqli_query($connection, 'SELECT * FROM ' . $p1_t1_warehouse_inventory . ' WHERE ' . $Where . ' ORDER BY Id ' . $Order);
  $num = 0;
  //recorre los lotes
  while ($Row_Batch = mysqli_fetch_assoc($Query_Batch)) {

    $Batch_Id = $Row_Batch['Id'];
    // Consultamos la sunidads disponibles de este producti-lote
    $Units = Consult_History_Units($Code_Product, '', '', 'x', $Batch_Id);
    $Row_Batch['Unit'] = $Units;

    //agregamos al arreglo
    array_push($ArrayBatches, $Row_Batch);
  }
  //retorna arreglo
  return $ArrayBatches;
}


function Consult_Batch_Assign_Production($Id_Business, $Id_Warehouse, $Code_Product, $Suggest_Batch = '')
{
  global $connection, $p0_t1_config_business, $p1_t1_warehouse_inventory_production;

  $Consult_Business = mysqli_query($connection, 'SELECT Method_Inventory FROM ' . $p0_t1_config_business . ' WHERE Id=' . $Id_Business);
  $Row_Business = mysqli_fetch_assoc($Consult_Business);
  echo mysqli_error($connection);
  // 'PEPS', 'UEPS', 'PP'
  $Order = '';
  //se cambia el orden segun se este establecido el metodo de inventario
  switch ($Row_Business['Method_Inventory']) {
    case 'PEPS':
      $Order = 'ASC';
      break;
    case 'UEPS':
      $Order = 'DESC';
      break;
      // default:
      //   $Order = 'ASC LIMIT 1';
      //   break;
  }
  //si ya viene con un lote sugerido se llama solo ese lote para el deceso de las unidades
  $Where = (empty($Suggest_Batch)) ? ' Id_Warehouse = ' . $Id_Warehouse . ' AND Id_Inventory = ' . $Code_Product . ' AND State="Active" ' :  ' Id =' . $Suggest_Batch;

  $ArrayBatches = array();
  // die('SELECT * FROM '. $p1_t1_warehouse_inventory_production .' WHERE ' . $Where . ' ORDER BY Id ' . $Order);
  $Query_Batch = mysqli_query($connection, 'SELECT * FROM '. $p1_t1_warehouse_inventory_production .' WHERE ' . $Where . ' ORDER BY Id ' . $Order);
  $num = 0;
  while ($Row_Batch = mysqli_fetch_assoc($Query_Batch)) {

    // $Batch_Id = ($Row_Business['Method_Inventory'] != 'PP')? $Row_Batch['Id']: '';
    $Batch_Id = $Row_Batch['Id'];
    $Units = Consult_History_Units_Production($Code_Product, '', '', 'x', $Batch_Id);
    $Row_Batch['Unit'] = $Units;

    // echo $Row_Batch;
    // echo ++$num.'---';
    // var_dump($Row_Batch);

    array_push($ArrayBatches, $Row_Batch);
  }

  // var_dump(json_encode($ArrayBatches));
  // die();
  return $ArrayBatches;
}

//Funcion para el deceso de unidades desde cualquier tipo de facturación
function Checked_Bill_History($CUNI, $PRINT = false, $Apartado = 'n')
{
  // se invocan las tablas necesarias de la base de datos
  global
    $p1_t1_inventory_sale_c2_products_history_units,
    $p3_t1_bills,
    $p3_t1_bills_c1_products,
    $p1_c1_relation_inventory,
    $p1_t1_warehouse_inventory_production,
    $p1_t2_inventory_production_c2_products_history_units,
    $p3_t1_bills_c1_products_c1_components,
    $p1_t1_inventory_sele_c3_components,
    $p1_t1_inventory_sele,
    $p1_t1_warehouse_inventory,
    $p0_t1_config_business;

  //conexion con la bd
  $connection = connection_db();

  $UnidsRecord = "";
// die('SELECT * FROM ' . $p3_t1_bills . ' WHERE CUNI = "' . $CUNI . '" ORDER BY Last_Update DESC, State ASC');
  // Consulta facturas que hay con el mismo cuni y las ordena por fecha desc y estado comenzado por a active y erased, history
  $Consult_Bills = mysqli_query($connection, 'SELECT * FROM ' . $p3_t1_bills . ' WHERE CUNI = "' . $CUNI . '" ORDER BY Last_Update DESC, State ASC');
  echo mysqli_error($connection);

  $num = 0;
  // inicia recorrido de las facturas
  while ($Row_Bill = mysqli_fetch_assoc($Consult_Bills)) {
    $num++;

    // Solo se selecciona la primera factura de todos los registros y se le cambia el estado a active
    if ($num == 1) {

      // se consultan las configuraciones de la empresa de la factura
      $Consult_Business = mysqli_query($connection, 'SELECT * FROM ' . $p0_t1_config_business . ' WHERE Id = ' . $Row_Bill['Id_Business']);
      $Row_Business = mysqli_fetch_assoc($Consult_Business);
      echo mysqli_error($connection);


      // Elimina registro unidades de venta antiguos
      $Query_History = mysqli_query($connection, 'DELETE FROM ' . $p1_t1_inventory_sale_c2_products_history_units . ' WHERE Id_Method = ' . $Row_Bill['Id'] . ' AND Method="Bill" ');
      echo mysqli_error($connection);

      // Elimina registro unidades materia prima antiguos
      $Query_History = mysqli_query($connection, 'DELETE FROM ' . $p1_t2_inventory_production_c2_products_history_units . ' WHERE Id_Method = ' . $Row_Bill['Id'] . ' AND Method="Bill" ');
      echo mysqli_error($connection);


      //Cambia el estado a activado
      if ($Row_Bill['State'] == 'History') {
        $Query_Update = mysqli_query($connection, 'UPDATE ' . $p3_t1_bills . ' SET State="Active" WHERE Id = ' . $Row_Bill['Id'] . ' ');
        echo mysqli_error($connection);
      }

      //Si se elimina correctamente
      if ($Query_History  && $Row_Bill['State'] != 'Temporal' && $Row_Bill['State'] != 'Erased') {

        // consulta que une el inventario con los productos vendidos en la factura
        // die('SELECT BP.Id, BP.Units, BP.Code_Product, BP.Id_Bill, BP.Suggest_Batch, I.Id_Compl_Prod, I.Compl_Value FROM ' . $p3_t1_bills_c1_products . ' BP INNER JOIN ' . $p1_t1_inventory_sele . ' I ON I.Id = BP.Code_Product WHERE BP.Id_Bill = ' . $Row_Bill['Id']);
        $Query_BillProducts = mysqli_query($connection, '
	        SELECT
	          BP.Id,
	          BP.Units,
	          BP.Code_Product,
	          BP.Id_Bill,
	          BP.Suggest_Batch,
	          I.Id_Compl_Prod,
	          I.Compl_Value,
            I.Id_Templ_Relation
	        FROM ' . $p3_t1_bills_c1_products . ' BP
	        INNER JOIN ' . $p1_t1_inventory_sele . ' I
	        ON I.Id = BP.Code_Product
	        WHERE BP.Id_Bill = ' . $Row_Bill['Id']);
        echo mysqli_error($connection);

        while ($Row_BillProducts = mysqli_fetch_assoc($Query_BillProducts)) {

          // Se declara el arreglo donde se van a guardar las unidades por cada lote
          $ArrayUnitsxBatch = array();

          //Obtiene el id warehouse de la factura
          // Nota: en caso de que la empresa tenga mas de dos bodegas se va a consultaar el Id_Warehouse de la factura
          // die($Row_Bill['Id_Warehouse'].'------------'.$Row_Bill['Id_Business']);
          $Id_Warehouse_Bill = (Get_Warehouse_Assigned($Row_Bill['Id_Business']) == false) ? $Row_Bill['Id_Warehouse'] : Get_Warehouse_Assigned($Row_Bill['Id_Business']);
          $Id_Warehouse_Bill = 1;
          // die($Id_Warehouse_Bill);

          //Trae arreglo con los lotes disponibles
          $Batches = Consult_Batch_Assign($Row_Bill['Id_Business'], $Id_Warehouse_Bill, $Row_BillProducts['Code_Product'], $Row_BillProducts['Suggest_Batch']);

          // Variable a la cual se le asignan las unidades pendientes, se usa en caso de que los lotes esten activados
          $UnitsPending = $Row_BillProducts['Units'];

          $Bathces = 0;
          $Iva_Porc = 0;
          $Impo_Porc = 0;

          /**************************************************************************
           * INICIO DEL RECORRIDO DE LOS PRODUCTOS SIN COMPONENTES NI MATERIA PRIMA *
           **************************************************************************/
          // se realiza el recorrido de los lotes
          foreach ($Batches as $RowBatches) {
            $Bathces++;

            //si ya no hay unidades pendientes se sale del bucle.
            if ($UnitsPending <= 0) {
              break;
            }

            //si esta por promedio ponderado se fuerza a solo realizar un solo registro || es es ultimo lote disponible
            $UnitsAvailable = $RowBatches['Unit'];

            /***************************************************************
             * INICIO VALIDACION PARA VERIFICAR CUANTAS UNIDADES SE RESTAN *
             ***************************************************************/
            // si aun quedan unidades disponibles
            if ($UnitsAvailable > 0) {
              if ($Row_Business['Lote_Products'] == 1) {
                $UnidsRecord = ($UnitsAvailable < $UnitsPending) ? $UnitsAvailable : $UnitsPending;
              } else {
                $UnidsRecord = $UnitsPending;
              }
            } else {
              if ($Row_Business['Delete_Zero_Items'] == 1) { // validacion en caso de que el eliminar al quedar cero esta activado
                $Update_batch = mysqli_query($connection, "UPDATE " . $p1_t1_warehouse_inventory . " SET State = 'Erased' WHERE Id = " . $RowBatches['Id']);
                continue;
              } else {
                if ($Row_Business['Lote_Products'] == 1) {
                  $UnidsRecord = ($UnitsAvailable < $UnitsPending) ? $UnitsAvailable : $UnitsPending;
                } else {
                  $UnidsRecord = $UnitsPending;
                }
              }
            }

            /***************************
             * FIN VALIDACION UNIDADES *
             ***************************/

            // realiza el insert de
            $type_history = 'Remove';
            $motive_history = '[Faturación de ' . floatval($UnidsRecord) . ' por factura ' . $Row_Bill['Bill'] . '.]';

            if ($Apartado == 'y') {
              $type_history = 'Aside';
              $motive_history = '[Apartado de ' . floatval($UnidsRecord) . ' por factura ' . $Row_Bill['Bill'] . '.]';
            }

            $sql_petition = '
	            INSERT INTO ' . $p1_t1_inventory_sale_c2_products_history_units . ' (Code_Item, Id_Batch, Id_Warehouse, Type, Method, Id_Method, Unit, Motive, User_Code, Date)
	            VALUES
	            (
	              ' . $Row_BillProducts['Code_Product'] . ',
	              ' . $RowBatches['Id'] . ',
	              ' . $Id_Warehouse_Bill . ',
	              "' . $type_history . '" ,
	              "Bill" ,
	              ' . $Row_Bill['Id'] . ',
	              ' . $UnidsRecord . ',
	              "' . $motive_history . '" ,
	              ' . $Row_Bill['User_Code'] . ' ,
	              ' . $Row_Bill['Date'] . '
	            ) ';

            $Query_NewHistory = mysqli_query($connection, $sql_petition);

            if (!$Query_NewHistory) {
              echo 'Error -->' . mysqli_error($connection) . ' / ' . $sql_petition . ' .<br/>';
              die($sql_petition);
            } else {
              if ($PRINT) {
                echo ' Ok ';
              }
            }

            array_push($ArrayUnitsxBatch, array('Id_Batch' => $RowBatches['Id'], 'Units' => $UnidsRecord));
            $UnitsPending -= $UnidsRecord;
          }


          //Asigna los lotes cargados al producto de la factura
          $Up_Bill_Prod = "UPDATE " . $p3_t1_bills_c1_products . "  SET Data_Batch='" . json_encode($ArrayUnitsxBatch) . "' WHERE Id=" . $Row_BillProducts['Id'];
          $ConsultProduct = mysqli_query($connection, $Up_Bill_Prod);
          if (!$ConsultProduct) {
            echo 'Error -->' . mysqli_error($connection) . ' / ' . $Up_Bill_Prod . ' .<br/>';
          } else {
            if ($PRINT) {
              echo ' Ok ';
            }
          }

          /***********************************************************************
           * FIN DEL RECORRIDO DE LOS PRODUCTOS SIN COMPONENTES NI MATERIA PRIMA *
           ***********************************************************************/


          /****************************************************
           * INICIA EL DESCUENTO DE PRODUCTOS COMPLEMENTARIOS *
           ****************************************************/

          if (!empty($Row_BillProducts['Id_Compl_Prod'])) {
            $ConsultProduct = mysqli_query($connection, 'SELECT I.Id, I.Type, I.Compl_Value, I.Product FROM ' . $p1_t1_inventory_sele . ' I WHERE I.Id=' . $Row_BillProducts['Id_Compl_Prod'] . ' ');
            echo mysqli_error($connection);
            $RowProduct = mysqli_fetch_assoc($ConsultProduct);

            $batch = Consult_Batch_Assign($Row_Bill['Id_Business'], $Id_Warehouse_Bill, $RowProduct['Id']);
            $batch = $batch[0]['Id'];

            //Inserta movimientos hechos del original
            $query = '
	              INSERT INTO ' . $p1_t1_inventory_sale_c2_products_history_units . '
	                (
	                  Code_Item,
	                  Id_Batch,
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
	                  "' . $RowProduct['Id'] . '",
	                  ' . $batch . ',
	                  ' . $Id_Warehouse_Bill . ',
	                  "Remove",
	                  "Bill",
	                  ' . $Row_Bill['Id'] . ',
	                  ' . ($Row_BillProducts['Units'] * $Row_BillProducts['Compl_Value']) . ',
	                  "[PC][Facturación de ' . $RowProduct['Product'] . ' de ' . ($Row_BillProducts['Units'] * $Row_BillProducts['Compl_Value']) . ' por factura n° ' . $Row_Bill['Bill'] . '.] ",
	                  "' . $Row_Bill['User_Code'] . '",
	                  ' . $Row_Bill['Date'] . '
	                )';
            $InsertHistoryProduct =  mysqli_query($connection, $query);

            if (!$InsertHistoryProduct) {
              echo '<br> Error INSERT en complementarios  -> ' . $p1_t1_inventory_sale_c2_products_history_units . ' -> ' . mysqli_error($connection) . '<br />..' . $query;
            } else {
              if ($PRINT) {
                echo ' OkPC ';
              }
            }
          }
          /***************************************************
           *  FIN EL DESCUENTO DE PRODUCTOS COMPLEMENTARIOS  *
           ***************************************************/


          /********************************************
           *   INICIA EL DESCUENTO DE MATERIA PRIMA   *
           ********************************************/
          $where = ($Row_BillProducts['Id_Templ_Relation'] == NULL) ? "Code_Product_Sell='" . $Row_BillProducts['Code_Product'] . "'" : "(Code_Product_Sell ='" . $Row_BillProducts['Code_Product'] . "' OR Code_Product_Sell='" . $Row_BillProducts['Id_Templ_Relation'] . "' )";
          
          $Query_Materia = "SELECT * FROM " . $p1_c1_relation_inventory . " WHERE " . $where . " AND Type ='Product'";
          // die($Query_Materia);
          $results_1 = mysqli_query($connection, $Query_Materia);    // Tabla 1 de productos
          //]
          echo mysqli_error($connection);
          while ($row_1 = mysqli_fetch_assoc($results_1)) {

            //Trae arreglo con los lotes disponibles
            $batch = Consult_Batch_Assign_Production($Row_Bill['Id_Business'], $Id_Warehouse_Bill, $row_1['Code_Product_Producction']);

            $UnitsPending_Production = ($row_1['Quantity'] * $Row_BillProducts['Units']);
            // die($UnitsPending_Production);

            $Bathces_Production = 0;
            // $Iva_Porc = 0;
            // $Impo_Porc = 0;

            /**************************************************************************
             * INICIO DEL RECORRIDO DE LOS PRODUCTOS SIN COMPONENTES NI MATERIA PRIMA *
             **************************************************************************/
            // se realiza el recorrido de los lotes
            foreach ($batch as $RowBatches_Production) {
              $Bathces_Production++;

              //si ya no hay unidades pendientes se sale del bucle.
              if ($UnitsPending_Production <= 0) {
                break;
              }


              //si esta por promedio ponderado se fuerza a solo realizar un solo registro || es es ultimo lote disponible
              $UnitsAvailable_Production = $RowBatches_Production['Unit'];
              // die($UnitsAvailable_Production);

              /***************************************************************
               * INICIO VALIDACION PARA VERIFICAR CUANTAS UNIDADES SE RESTAN *
               ***************************************************************/
              // si aun quedan unidades disponibles
              if ($UnitsAvailable_Production > 0) {
                if ($Row_Business['Lote_Products'] == 1) {
                  $UnidsRecord_Production = ($UnitsAvailable_Production < $UnitsPending_Production) ? $UnitsAvailable_Production : $UnitsPending_Production;
                  // die($UnidsRecord_Production);
                } else {
                  $UnidsRecord_Production = $UnitsPending_Production;
                }

                /***************************
               * FIN VALIDACION UNIDADES *
               ***************************/

              }else {
                if ($Row_Business['Delete_Zero_Items'] == 1) { // validacion en caso de que el eliminar al quedar cero esta activado
                  // $Update_batch = mysqli_query($connection, "UPDATE " . $p1_t1_warehouse_inventory . " SET State = 'Erased' WHERE Id = " . $RowBatches['Id']);
                   continue;
                } else {
                    if ($Row_Business['Lote_Products'] == 1) {
                      $UnidsRecord_Production = ($UnitsAvailable_Production < $UnitsPending_Production) ? $UnitsAvailable_Production : $UnitsPending_Production;
                    } else {
                      $UnidsRecord_Production = $UnitsPending_Production;
                    }
                 }
               }


          		// realiza el insert de
                $type_history_Production = 'Remove';
                $motive_history_Production = '[MP][Facturación de ' . $UnidsRecord_Production . ' por factura n° ' . $Row_Bill['Bill'] . '.]';


                $sql_petition1 = '
                INSERT INTO ' . $p1_t2_inventory_production_c2_products_history_units . '
                  (Id_Warehouse, Code_Item, Id_Batch, Type, Motive, User_Code, Date, Method, Id_Method, Unit, Price_Sell)
                VALUES
                  (' . $Id_Warehouse_Bill . ',
                  ' . $row_1['Code_Product_Producction'] . ',
                  ' . $RowBatches_Production['Id'] . ',
                  "' . $type_history_Production . '",
                  "' . $motive_history_Production . '",
                  "' . $Row_Bill['User_Code'] . '",
                  ' . $Row_Bill['Date'] . ',
                  "Bill",
                  ' . $Row_Bill['Id'] . ',
                  ' . $UnidsRecord_Production . ',
                  '. $RowBatches_Production['Price_Cost'] .' )';

                  //die($sql_petition1);
                $Query_NewHistory1 = mysqli_query($connection, $sql_petition1);

                if (!$Query_NewHistory1) {
                  echo 'Error -->' . mysqli_error($connection) . ' / ' . $sql_petition1 . ' .<br/>';
                  die($sql_petition1);
                } else {
                  if ($PRINT) {
                    echo ' Ok ';
                  }
                }

                // array_push($ArrayUnitsxBatch, array('Id_Batch' => $RowBatches['Id'], 'Units' => $UnidsRecord));
                $UnitsPending_Production -= $UnidsRecord_Production;

            }
          }
          /*****************************************
           *   FIN EL DESCUENTO DE MATERIA PRIMA   *
           *****************************************/


          /*****************************************
           *  INICIA EL DESCUENTO DE COMPONENTES   *
           *****************************************/
          // die('SELECT * FROM ' . $p3_t1_bills_c1_products_c1_components . ' WHERE Id_Bill_Product=' . $Row_BillProducts['Id'] . ' ');
          $results_1 = mysqli_query($connection, 'SELECT * FROM ' . $p3_t1_bills_c1_products_c1_components . ' WHERE Id_Bill_Product=' . $Row_BillProducts['Id'] . ' ');
          echo mysqli_error($connection);

          while ($row_1 = mysqli_fetch_assoc($results_1)) {
            
            $Query_Component = 'SELECT Type, Code_Item, Is_Affect, Quantity_Affect FROM ' . $p1_t1_inventory_sele_c3_components . ' WHERE Code = ' . $row_1['Id_Component'];
            // die($Query_Component);
            
            $Qry_Component = mysqli_query($connection, $Query_Component);
            echo mysqli_error($connection);
            $Row_Component = mysqli_fetch_assoc($Qry_Component);

            //Verifica que sea un componmenteq ue afecta inventyario
            if ($Row_Component['Is_Affect'] == 1 && ($Row_Component['Type'] == "Item_Sele" || $Row_Component['Type'] == "Item_Prod")) {

              if ($Row_Component['Quantity_Affect'] >= 0) {
                $accion = "Remove";
                $palabra = "Removió";
              } else {
                $accion = "Add";
                $palabra = "Compenso";
              }

              if ($Row_Component['Type'] == "Item_Sele") {

                $batch = Consult_Batch_Assign($Row_Bill['Id_Business'], $Id_Warehouse_Bill, $Row_Component['Code_Item']);
                $batch = $batch[0]['Id'];

                $Qry = '
	                  INSERT INTO ' . $p1_t1_inventory_sale_c2_products_history_units . '
	                    (Id_Warehouse, Code_Item, Id_Batch, Type, Motive, User_Code, Date, Method, Id_Method, Unit)
	                  VALUES
	                    (' . $Id_Warehouse_Bill . ', ' . $Row_Component['Code_Item'] . ', ' . $batch . ', "' . $accion . '", "[CO][' . $palabra . ' ' . number_format(abs($Row_Component['Quantity_Affect']) * $Row_BillProducts['Units']) . '] Unidad(es) por factura ' . $Row_Bill['Bill'] . ']", ' . $Row_Bill['User_Code'] . ', ' . $Row_Bill['Date'] . ', "Bill", ' . $Row_Bill['Id'] . ', ' . abs($Row_Component['Quantity_Affect']) * $Row_BillProducts['Units'] . ')';
                $Qry_Component = mysqli_query($connection, $Qry);
              } else {


                $batch_Component = Consult_Batch_Assign_Production($Row_Bill['Id_Business'], $Id_Warehouse_Bill, $Row_Component['Code_Item']);

                $UnitsPending_Component = ($Row_Component['Quantity_Affect'] * $Row_BillProducts['Units']);
                // die($UnitsPending_Production);

                $Bathces_Component = 0;
                // $Iva_Porc = 0;
                // $Impo_Porc = 0;

                /**************************************************************************
                 * INICIO DEL RECORRIDO DE LOS PRODUCTOS SIN COMPONENTES NI MATERIA PRIMA *
                 **************************************************************************/
                // se realiza el recorrido de los lotes
                foreach ($batch_Component as $RowBatches_Component) {
                  $Bathces_Component++;

                  //si ya no hay unidades pendientes se sale del bucle.
                  if ($UnitsPending_Component <= 0) {
                    break;
                  }


                  //si esta por promedio ponderado se fuerza a solo realizar un solo registro || es es ultimo lote disponible
                  $UnitsAvailable_Component = $RowBatches_Component['Unit'];
                  // die($UnitsAvailable_Production);

                  /***************************************************************
                   * INICIO VALIDACION PARA VERIFICAR CUANTAS UNIDADES SE RESTAN *
                   ***************************************************************/
                  // si aun quedan unidades disponibles
                  if ($UnitsAvailable_Component > 0) {
                    if ($Row_Business['Lote_Products'] == 1) {
                      $UnidsRecord_Component = ($UnitsAvailable_Component < $UnitsPending_Component) ? $UnitsAvailable_Component : $UnitsPending_Component;
                      // die($UnidsRecord_Production);
                    } else {
                      $UnidsRecord_Component = $UnitsPending_Component;
                    }

                    /***************************
                   * FIN VALIDACION UNIDADES *
                   ***************************/

                    // realiza el insert de
                    $type_history_Component = 'Remove';
                    $motive_history_Component = '[CO][' . $palabra . ' ' . number_format(abs($UnidsRecord_Component)) . '] Unidad(es) por factura ' . $Row_Bill['Bill'] . ']';


                    $sql_petition2 = '
                    INSERT INTO ' . $p1_t2_inventory_production_c2_products_history_units . '
                      (Id_Warehouse, Code_Item, Id_Batch, Type, Motive, User_Code, Date, Method, Id_Method, Unit)
                    VALUES
                      (' . $Id_Warehouse_Bill . ',
                      ' . $Row_Component['Code_Item'] . ',
                      ' . $RowBatches_Component['Id'] . ',
                      "' . $type_history_Component . '",
                      "' . $motive_history_Component . '",
                      "' . $Row_Bill['User_Code'] . '",
                      ' . $Row_Bill['Date'] . ',
                      "Bill",
                      ' . $Row_Bill['Id'] . ',
                      ' . $UnidsRecord_Component . ' )';

                      // die($sql_petition1);
                    $Query_NewHistory2 = mysqli_query($connection, $sql_petition2);

                    if (!$Query_NewHistory2) {
                      echo 'Error -->' . mysqli_error($connection) . ' / ' . $sql_petition2 . ' .<br/>';
                      die($sql_petition2);
                    } else {
                      if ($PRINT) {
                        echo ' Ok ';
                      }
                    }

                    // array_push($ArrayUnitsxBatch, array('Id_Batch' => $RowBatches['Id'], 'Units' => $UnidsRecord));
                    $UnitsPending_Component -= $UnidsRecord_Component;

                  }

                }
                // $batch = Consult_Batch_Assign_Production($Row_Bill['Id_Business'], $Id_Warehouse_Bill, $Row_Component['Code_Item']);
                // $batch = $batch[0]['Id'];

                // $Qry = '
	              //     INSERT INTO ' . $p1_t2_inventory_production_c2_products_history_units . '
	              //       (Id_Warehouse, Code_Item, Id_Batch, Type, Motive, User_Code, Date, Method, Id_Method, Unit)
	              //     VALUES
	              //       (' . $Id_Warehouse_Bill . ', ' . $Row_Component['Code_Item'] . ', ' . $batch . ', "' . $accion . '", "[CO][' . $palabra . ' ' . number_format(abs($Row_Component['Quantity_Affect']) * $Row_BillProducts['Units']) . '] Unidad(es) por factura ' . $Row_Bill['Bill'] . ']", ' . $Row_Bill['User_Code'] . ', ' . $Row_Bill['Date'] . ', "Bill", ' . $Row_Bill['Id'] . ', ' . abs($Row_Component['Quantity_Affect']) * $Row_BillProducts['Units'] . ')';
                // $Qry_History = mysqli_query($connection, $Qry);
              }

              // if (!$Qry_History) {
              //   echo 'Error INSERT en tabla -> ' . $p1_t2_inventory_production_c2_products_history_units . ' -> ' . mysqli_error($connection) . '<br />...' . $Qry;
              // } else {
              //   if ($PRINT) {
              //     echo ' OkCO ';
              //   }
              // }
            }

            if ($Row_Component['Type'] == "Item_Free") {

              $query_consult = 'SELECT Code_Product_Producction , Quantity FROM '.$p1_c1_relation_inventory.' WHERE  Type = "Component" AND Code_Product_Sell = '.$row_1['Id_Component'];
              $qry_consult = mysqli_query($connection, $query_consult);

              foreach ($qry_consult as $dataRecetaComponentes) {

                $batch = Consult_Batch_Assign_Production($Row_Bill['Id_Business'], $Id_Warehouse_Bill, $dataRecetaComponentes['Code_Product_Producction']);
                $UnitsPending_Production = $dataRecetaComponentes['Quantity']*$Row_BillProducts['Units'];

                foreach ($batch as $RowBatches_Production) {

                  //si ya no hay unidades pendientes se sale del bucle.
                  if ($UnitsPending_Production <= 0) {
                    break;
                  }

                  //si esta por promedio ponderado se fuerza a solo realizar un solo registro || es es ultimo lote disponible
                  $UnitsAvailable_Production = $RowBatches_Production['Unit'];

                  /***************************************************************
                   * INICIO VALIDACION PARA VERIFICAR CUANTAS UNIDADES SE RESTAN *
                   ***************************************************************/
                  if ($UnitsAvailable_Production > 0) {
                    if ($Row_Business['Lote_Products'] == 1) {
                      $UnidsRecord_Production = ($UnitsAvailable_Production < $UnitsPending_Production) ? $UnitsAvailable_Production : $UnitsPending_Production;
                      // die($UnidsRecord_Production);
                    } else {
                      $UnidsRecord_Production = $UnitsPending_Production;
                    }

                    /***************************
                   * FIN VALIDACION UNIDADES *
                   ***************************/

                  }else {
                    if ($Row_Business['Delete_Zero_Items'] == 1) { // validacion en caso de que el eliminar al quedar cero esta activado
                       continue;
                    } else {
                      if ($Row_Business['Lote_Products'] == 1) {
                        $UnidsRecord_Production = ($UnitsAvailable_Production < $UnitsPending_Production) ? $UnitsAvailable_Production : $UnitsPending_Production;
                      } else {
                        $UnidsRecord_Production = $UnitsPending_Production;
                      }
                    }
                  }


                    $type_history_Production = 'Remove';
                    $motive_history_Production = '[MP][COMP][Facturación de ' . $UnidsRecord_Production . ' por factura n° ' . $Row_Bill['Bill'] .  '.]';


                    $sql_petition1 = '
                    INSERT INTO ' . $p1_t2_inventory_production_c2_products_history_units . '
                      (Id_Warehouse, Code_Item, Id_Batch, Type, Motive, User_Code, Date, Method, Id_Method, Unit, Price_Sell)
                    VALUES
                      (' . $Id_Warehouse_Bill . ',
                      ' . $dataRecetaComponentes['Code_Product_Producction'] . ',
                      ' . $RowBatches_Production['Id'] . ',
                      "' . $type_history_Production . '",
                      "' . $motive_history_Production . '",
                      "' . $Row_Bill['User_Code'] . '",
                      ' . $Row_Bill['Date'] . ',
                      "Bill",
                      ' . $Row_Bill['Id'] . ',
                      ' . floatval($UnidsRecord_Production) . ',
                      '. $RowBatches_Production['Price_Cost'] .' )';

                      //die($sql_petition1);
                    $Query_NewHistory1 = mysqli_query($connection, $sql_petition1);

                    if (!$Query_NewHistory1) {
                      echo 'Error -->' . mysqli_error($connection) . ' / ' . $sql_petition1 . ' .<br/>';
                      die($sql_petition1);
                    } else {
                      if ($PRINT) {
                        echo ' Ok ';
                      }
                    }

                    $UnitsPending_Production -= $UnidsRecord_Production;

                }
              }




            }

          }

          /**************************************
           *  FIN EL DESCUENTO DE COMPONENTES   *
           **************************************/
        } //fin producto

      } else if ($Row_Bill['State'] != 'Erased' && $Row_Bill['State'] != 'Temporal' && $Row_Bill['State'] != 'History') {
        echo 'Error ->' . mysqli_error($connection) . ' <br />';
      }
    } else {
      //Elimna registros de units
      $Query_History = mysqli_query($connection, 'DELETE FROM ' . $p1_t1_inventory_sale_c2_products_history_units . ' WHERE Id_Method = ' . $Row_Bill['Id'] . ' AND Method="Bill" ');
      echo mysqli_error($connection);

      //Si se elimina correctamente actualiza su estado a history
      $Query_Update = mysqli_query($connection, 'UPDATE ' . $p3_t1_bills . ' SET State="History" WHERE Id = ' . $Row_Bill['Id'] . ' ');
      echo mysqli_error($connection);

      if ($PRINT) {
        echo ' History ';
      }
    }
  }
}

function Checked_Rem_History($CUNI, $PRINT = false, $Apartado = 'n', $Type = "Bill")
{

  global $p1_t1_inventory_sale_c2_products_history_units,  $p7_t1_remissions,  $p7_t1_remissions_c1_products, $connection, $p1_c1_relation_inventory, $p1_t1_warehouse_inventory_production, $p1_t2_inventory_production_c2_products_history_units, $p3_t1_bills_c1_products_c1_components, $p1_t1_inventory_sele_c3_components, $p1_t1_inventory_sele, $p1_t1_warehouse_inventory, $p0_t1_config_business;

  $connection = connection_db();

  $UnidsRecord = "";

  $Consult_Bills = mysqli_query($connection, 'SELECT * FROM ' . $p7_t1_remissions . ' WHERE CUNI = "' . $CUNI . '" ORDER BY Last_Update DESC, State ASC');
  echo mysqli_error($connection);

  $num = 0;
  while ($Row_Bill = mysqli_fetch_assoc($Consult_Bills)) {
    $num++;

    if ($num == 1) {

      $Consult_Business = mysqli_query($connection, 'SELECT * FROM ' . $p0_t1_config_business . ' WHERE Id=' . $Row_Bill['Id_Business']);
      $Row_Business = mysqli_fetch_assoc($Consult_Business);
      echo mysqli_error($connection);


      // Elimina registro undades de venta antiguos
      $Query_History = mysqli_query($connection, 'DELETE FROM ' . $p1_t1_inventory_sale_c2_products_history_units . ' WHERE Id_Method = ' . $Row_Bill['Id'] . ' AND Method="Bill" ');
      echo mysqli_error($connection);

      // Elimina registro undades materia prima antiguos
      $Query_History = mysqli_query($connection, 'DELETE FROM ' . $p1_t2_inventory_production_c2_products_history_units . ' WHERE Id_Method = ' . $Row_Bill['Id'] . ' AND Method="Bill" ');
      echo mysqli_error($connection);


      //Cambia el estado a activado
      if ($Row_Bill['State'] == 'History') {
        $Query_Update = mysqli_query($connection, 'UPDATE ' . $p7_t1_remissions . ' SET State="Active" WHERE Id = ' . $Row_Bill['Id'] . ' ');
        echo mysqli_error($connection);
      }

      //Si se elimina correctamente

      if ($Query_History  && $Row_Bill['State'] != 'Temporal' && $Row_Bill['State'] != 'Erased') {


        $Query_BillProducts = mysqli_query($connection, 'SELECT BP.Id, BP.Units, BP.Code_Product, BP.Id_Bill, BP.Suggest_Batch, I.Id_Compl_Prod,I.Compl_Value FROM ' . $p7_t1_remissions_c1_products . ' BP INNER JOIN ' . $p1_t1_inventory_sele . ' I ON I.Id = BP.Code_Product WHERE BP.Id_Bill = ' . $Row_Bill['Id']);
        echo mysqli_error($connection);
        while ($Row_BillProducts = mysqli_fetch_assoc($Query_BillProducts)) {
          $ArrayUnitsxBatch = array();

          //Obtiene el id warehouse de la factura
          $Id_Warehouse_Bill = $Id_Warehouse_Bill = Get_Warehouse_Assigned($Row_Bill['Id_Business']) == false ? $Row_Bill['Id_Warehouse'] : Get_Warehouse_Assigned($Row_Bill['Id_Business']);;

          //Trae arreglo con los lotes disponibles
          $Batches = Consult_Batch_Assign($Row_Bill['Id_Business'], $Id_Warehouse_Bill, $Row_BillProducts['Code_Product'], $Row_BillProducts['Suggest_Batch']);
          echo '<hr>';

          $UnitsPending = $Row_BillProducts['Units'];
          $Bathces = 0;
          $Iva_Porc = 0;
          $Impo_Porc = 0;

          foreach ($Batches as $RowBatches) {
            $Bathces++;

            //si ya no hay unidades pendientes, no tiene sentido continuar.
            if ($UnitsPending <= 0) {
              break;
            }

            //si esta por promedio ponderado se fuerza a solo realizar un solo registro || es es ultimo lote disponible
            $UnitsAvailable = $RowBatches['Unit'];
            if ($UnitsAvailable > 0) {
              if ($Row_Business['Lote_Products'] == 1) {
                $UnidsRecord = ($UnitsAvailable < $UnitsPending) ? $UnitsAvailable : $UnitsPending;
              } else {
                $UnidsRecord = $UnitsPending;
              }
            } else {
              if ($Row_Business['Delete_Zero_Items'] == 1) {
                $Update_batch = mysqli_query($connection, "UPDATE " . $p1_t1_warehouse_inventory . " SET State = 'Erased' WHERE Id = " . $RowBatches['Id']);
                continue;
              } else {
                if ($Row_Business['Lote_Products'] == 1) {
                  $UnidsRecord = ($UnitsAvailable < $UnitsPending) ? $UnitsAvailable : $UnitsPending;
                } else {
                  $UnidsRecord = $UnitsPending;
                }
              }
            }


            $type_history = 'Remove';
            $motive_history = '[Salida de ' . floatval($UnidsRecord) . ' por remisión ' . $Row_Bill['Bill'] . '.]';


            $sql_petition = '
                INSERT INTO ' . $p1_t1_inventory_sale_c2_products_history_units . ' (Code_Item, Id_Batch, Id_Warehouse, Type, Method, Id_Method, Unit, Motive, User_Code, Date)
                VALUES
                (
                  ' . $Row_BillProducts['Code_Product'] . ',
                  ' . $RowBatches['Id'] . ',
                  ' . $Id_Warehouse_Bill . ',
                  "' . $type_history . '" ,
                  "Remission" ,
                  ' . $Row_Bill['Id'] . ',
                  ' . $UnidsRecord . ',
                  "' . $motive_history . '" ,
                  ' . $Row_Bill['User_Code'] . ' ,
                  ' . $Row_Bill['Date'] . '
                ) ';

            $Query_NewHistory = mysqli_query($connection, $sql_petition);

            if (!$Query_NewHistory) {
              echo 'Error -->' . mysqli_error($connection) . ' / ' . $sql_petition . ' .<br/>';
              die($sql_petition);
            } else {
              if ($PRINT) {
                echo ' Ok ';
              }
            }

            array_push($ArrayUnitsxBatch, array('Id_Batch' => $RowBatches['Id'], 'Units' => $UnidsRecord));
            $UnitsPending -= $UnidsRecord;
          }

          //Asigna los lotes cargados al producto de la factura
          $Up_Bill_Prod = "UPDATE " . $p7_t1_remissions_c1_products . "  SET Data_Batch='" . json_encode($ArrayUnitsxBatch) . "' WHERE Id=" . $Row_BillProducts['Id'];
          //echo $Up_Bill_Prod;
          $ConsultProduct = mysqli_query($connection, $Up_Bill_Prod);
          if (!$ConsultProduct) {
            echo 'Error -->' . mysqli_error($connection) . ' / ' . $Up_Bill_Prod . ' .<br/>';
          } else {
            if ($PRINT) {
              echo ' Ok ';
            }
          }

          //Descuento de unidades de productos complementarios
          if (!empty($Row_BillProducts['Id_Compl_Prod'])) {
            $ConsultProduct = mysqli_query($connection, '
                    SELECT I.Id, I.Type, I.Compl_Value, I.Product FROM ' . $p1_t1_inventory_sele . ' I WHERE I.Id=' . $Row_BillProducts['Id_Compl_Prod'] . ' ');
            echo mysqli_error($connection);
            $RowProduct = mysqli_fetch_assoc($ConsultProduct);
            $batch = Consult_Batch_Assign($Row_Bill['Id_Business'], $Id_Warehouse_Bill, $RowProduct['Id']);
            $batch = $batch[0]['Id'];

            //Inserta movimientos hechos del original
            $query = '
                        INSERT INTO ' . $p1_t1_inventory_sale_c2_products_history_units . '
                          (
                            Code_Item,
                            Id_Batch,
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
                            "' . $RowProduct['Id'] . '",
                            ' . $batch . ',
                            ' . $Id_Warehouse_Bill . ',
                            "Remove",
                            "Remission",
                            ' . $Row_Bill['Id'] . ',
                            ' . ($Row_BillProducts['Units'] * $Row_BillProducts['Compl_Value']) . ',
                            "[PC][Salida de ' . $RowProduct['Product'] . ' de ' . ($Row_BillProducts['Units'] * $Row_BillProducts['Compl_Value']) . ' por remisión n° ' . $Row_Bill['Bill'] . '.] ",
                            "' . $Row_Bill['User_Code'] . '",
                            ' . $Row_Bill['Date'] . '
                          )';
            $InsertHistoryProduct =  mysqli_query($connection, $query);

            if (!$InsertHistoryProduct) {
              echo '<br> Error INSERT en complementarios  -> ' . $p1_t1_inventory_sale_c2_products_history_units . ' -> ' . mysqli_error($connection) . '<br />..' . $query;
            } else {
              if ($PRINT) {
                echo ' OkPC ';
              }
            }
          }


          //Descuento de materia prima
          $results_1 = mysqli_query($connection, "SELECT * FROM " . $p1_c1_relation_inventory . " WHERE Code_Product_Sell='" . $Row_BillProducts['Code_Product'] . "' ");    // Tabla 1 de productos
          echo mysqli_error($connection);
          while ($row_1 = mysqli_fetch_assoc($results_1)) {

            //Trae arreglo con los lotes disponibles
            $batch = Consult_Batch_Assign_Production($Row_Bill['Id_Business'], $Id_Warehouse_Bill, $row_1['Code_Product_Producction']);
            $batch = $batch[0]['Id'];
            //Inserta Historia de unidad de produccion
            $query = '
                    INSERT INTO ' . $p1_t2_inventory_production_c2_products_history_units . '
                      (Id_Warehouse, Code_Item, Id_Batch, Type, Motive, User_Code, Date, Method, Id_Method, Unit)
                    VALUES
                      (' . $Id_Warehouse_Bill . ', ' . $row_1['Code_Product_Producction'] . ', ' . $batch . ',  "Remove",  "[MP][Salida de ' . $row_1['Quantity'] * $Row_BillProducts['Units'] . ' por remisión n° ' . $Row_Bill['Bill'] . '.] ",  "' . $Row_Bill['User_Code'] . '",  ' . $Row_Bill['Date'] . ', "Bill", ' . $Row_Bill['Id'] . ', ' . ($row_1['Quantity'] * $Row_BillProducts['Units']) . ' )';
            $InsertHistoryProduct =  mysqli_query($connection, $query);

            if (!$InsertHistoryProduct) {
              echo '<br>Error INSERT en mp -> ' . $p1_t2_inventory_production_c2_products_history_units . ' -> ' . mysqli_error($connection) . '<br />...' . $query;
            } else {
              if ($PRINT) {
                echo ' OkMP ';
              }
            }
          }
        } //fin producto


      } else if ($Row_Bill['State'] != 'Erased' && $Row_Bill['State'] != 'Temporal' && $Row_Bill['State'] != 'History') {
        echo 'Error ->' . mysqli_error($connection) . ' <br />';
      }
    } else {
      //Elimna registros de units
      $Query_History = mysqli_query($connection, 'DELETE FROM ' . $p1_t1_inventory_sale_c2_products_history_units . ' WHERE Id_Method = ' . $Row_Bill['Id'] . ' AND Method="Bill" ');
      echo mysqli_error($connection);

      //Si se elimina correctamente actualiza su estado a history
      $Query_Update = mysqli_query($connection, 'UPDATE ' . $p7_t1_remissions . ' SET State="History" WHERE Id = ' . $Row_Bill['Id'] . ' ');
      echo mysqli_error($connection);

      if ($PRINT) {
        echo ' History ';
      }
    }
  }
}

function Checked_Buy_History($CUNI,  $PRINT = false)
{

  global $p1_t1_inventory_sale_c2_products_history_units,  $p9_t1_buys,  $p9_t1_buys_c1_products, $connection, $p1_c1_relation_inventory, $p1_t1_warehouse_inventory_production, $p1_t2_inventory_production_c2_products_history_units, $p3_t1_bills_c1_products_c1_components, $p1_t1_inventory_sele_c3_components, $p1_t1_inventory_sele, $p1_t1_warehouse_inventory, $p0_t1_config_business,$p1_t2_inventory_production;


  $UnidsRecord = "";

  $Consult_Bills = mysqli_query($connection, 'SELECT * FROM ' . $p9_t1_buys . ' WHERE CUNI = "' . $CUNI . '" ORDER BY Last_Update DESC, State ASC');
  echo mysqli_error($connection);

  $num = 0;
  while ($Row_Bill = mysqli_fetch_assoc($Consult_Bills)) {
    $num++;


    if ($num == 1) {

      $Consult_Business = mysqli_query($connection, 'SELECT * FROM ' . $p0_t1_config_business . ' WHERE Id=' . $Row_Bill['Id_Business']);
      $Row_Business = mysqli_fetch_assoc($Consult_Business);
      echo mysqli_error($connection);


      // Elimina registro undades de venta antiguos
      $Query_History = mysqli_query($connection, 'DELETE FROM ' . $p1_t1_inventory_sale_c2_products_history_units . ' WHERE Id_Method = ' . $Row_Bill['Id'] . ' AND Method="Purchase" ');
      echo mysqli_error($connection);

      // Elimina registro undades de materia prima antiguos
      $Query_History = mysqli_query($connection, 'DELETE FROM ' . $p1_t2_inventory_production_c2_products_history_units	 . ' WHERE Id_Method = ' . $Row_Bill['Id'] . ' AND Method="Purchase" ');
      echo mysqli_error($connection);

      //Si se elimina correctamente
      if ($Query_History  && $Row_Bill['State'] != 'Temporal' && $Row_Bill['State'] != 'Erased') {

        // die('SELECT BP.Id, BP.Units, BP.Code_Product, BP.Type_Product,  BP.Price, BP.Porcentaje, BP.Price_Cost, BP.Id_Bill, BP.Suggest_Batch, I.Id_Compl_Prod,I.Compl_Value FROM ' . $p9_t1_buys_c1_products . ' BP INNER JOIN ' . $p1_t1_inventory_sele . ' I ON I.Id = BP.Code_Product WHERE BP.Id_Bill = ' . $Row_Bill['Id']);

        $Query_BillProducts = mysqli_query($connection, 'SELECT BP.Id, BP.Units, BP.Code_Product, BP.Type_Product,  BP.Price, BP.Porcentaje, BP.Price_Cost, BP.Id_Bill, BP.Suggest_Batch, I.Id_Compl_Prod,I.Compl_Value FROM ' . $p9_t1_buys_c1_products . ' BP INNER JOIN ' . $p1_t1_inventory_sele . ' I ON I.Id = BP.Code_Product WHERE BP.Id_Bill = ' . $Row_Bill['Id']);
        echo mysqli_error($connection);
        while ($Row_BillProducts = mysqli_fetch_assoc($Query_BillProducts)) {

          $table_inventory = ($Row_BillProducts['Type_Product'] == "Sale") ? $p1_t1_inventory_sele : $p1_t2_inventory_production ;
          $table_warehouse = ($Row_BillProducts['Type_Product'] == "Sale") ? $p1_t1_warehouse_inventory : $p1_t1_warehouse_inventory_production ;
          $table_history = ($Row_BillProducts['Type_Product'] == "Sale") ? $p1_t1_inventory_sale_c2_products_history_units : $p1_t2_inventory_production_c2_products_history_units ;

          // die($Row_BillProducts['Suggest_Batch'].'--------');

          $Id_Warehouse_Bill = Get_Warehouse_Assigned($Row_Bill['Id_Business']) == false ? $Row_Bill['Id_Warehouse'] : Get_Warehouse_Assigned($Row_Bill['Id_Business']);

          // die($Row_Business['Lote_Products']);

          // ------- SI LOS LOTES ESTAN ACTIVADOS Y NO SE ENVIA VARIABLE ESPECIFICANDO EL LOTE -----//
          if ($Row_Business['Lote_Products'] == 1) {

            // die("SHOW TABLE STATUS LIKE '" . $table_warehouse . "'");
            $ConsultAutoIncrement_batch = mysqli_query($connection, "SHOW TABLE STATUS LIKE '" . $table_warehouse . "'");
            $rowAuto_batch = mysqli_fetch_assoc($ConsultAutoIncrement_batch);
            // Validamos si hay lote sugerido, y agregamos las unidades a ese lote.
            if($Row_BillProducts['Suggest_Batch'] != ""){

              // die("INSERT INTO " . $table_history . " (Code_Item, Id_Warehouse, Id_Batch, Type, Method, Id_Method, Unit, Motive, User_Code, Date, Price_Buy) VALUES (" . $Row_BillProducts['Code_Product'] . ", " . $Id_Warehouse_Bill . ", " . $Row_Business['Lote_Products'] . ", 'Add', 'Purchase', " . $Row_Bill['Id'] . ", " . $Row_BillProducts['Units'] . ", '[Se añadieron " . floatval($Row_BillProducts['Units']) . " por compra en la factura " . $Row_Bill['Bill'] . "]', " . $_SESSION['User_Code'] . ", " . date('U') . ", ". $Row_BillProducts['Price_Cost'] .") ");

              $petition_1 = mysqli_query($connection, "INSERT INTO " . $table_history . " (Code_Item, Id_Warehouse, Id_Batch, Type, Method, Id_Method, Unit, Motive, User_Code, Date, Price_Buy) VALUES (" . $Row_BillProducts['Code_Product'] . ", " . $Id_Warehouse_Bill . ", " . $Row_Business['Lote_Products'] . ", 'Add', 'Purchase', " . $Row_Bill['Id'] . ", " . $Row_BillProducts['Units'] . ", '[Se añadieron " . floatval($Row_BillProducts['Units']) . " por compra en la factura " . $Row_Bill['Bill'] . "]', " . $_SESSION['User_Code'] . ", " . date('U') . ", ". $Row_BillProducts['Price_Cost'] .") ");

            }else{
              // Si no hay un lote sugerido, creamos uno nuevo y agregamos las unidades a ese.
              $petition_Warehouse = mysqli_query($connection, "INSERT INTO " . $table_warehouse . "
              (Id, Id_Warehouse, Id_Inventory, Id_Provider, Name,	Barcode_batch, Unit, Units_Pack, Price_Base, Price_Suggest, Price_Cost, Price_Wholesale, Iva_Perc, Impo_Perc, Expiration_Date, State)
							VALUES
							(" . $rowAuto_batch['Auto_increment'] . ", ". $Id_Warehouse_Bill . ", " . $Row_BillProducts['Code_Product'] . ", " . $Row_Bill['Code_Provider'] . ", '" . $rowAuto_batch['Auto_increment'] . "', '0', " . $Row_BillProducts['Units'] . ", 0, " . $Row_BillProducts['Price'] . ", " . $Row_BillProducts['Price'] . ", " . $Row_BillProducts['Price_Cost'] . ", 0, " . $Row_BillProducts['Porcentaje'] . ", 0, 0, 'Active')");


              $petition_1 = mysqli_query($connection, "INSERT INTO " . $table_history . " (Code_Item, Id_Warehouse, Id_Batch, Type, Method, Id_Method, Unit, Motive, User_Code, Date, Price_Buy) VALUES (" . $Row_BillProducts['Code_Product'] . ", " . $Id_Warehouse_Bill . ", " . $rowAuto_batch['Auto_increment'] . ", 'Add', 'Purchase', " . $Row_Bill['Id'] . ", " . $Row_BillProducts['Units'] . ", '[Se añadieron " . floatval($Row_BillProducts['Units']) . " por compra en la factura " . $Row_Bill['Bill'] . "]', " . $_SESSION['User_Code'] . ", " . date('U') . ", ". $Row_BillProducts['Price_Cost'] .") ");
            }

            // die("INSERT INTO " . $table_warehouse . "(Id_Warehouse, Id_Inventory, Id_Provider, Name,	Barcode_batch, Unit, Units_Pack, Price_Base, Price_Suggest, Price_Cost, Price_Wholesale, Iva_Perc, Impo_Perc, Expiration_Date, State) VALUES (" . $Id_Warehouse_Bill . ", " . $Row_BillProducts['Code_Product'] . ", " . $Row_Bill['Code_Provider'] . ", '" . $rowAuto_batch['Auto_increment'] . "', '0', " . $Row_BillProducts['Units'] . ", 0, " . $Row_BillProducts['Price'] . ", " . $Row_BillProducts['Price'] . ", " . $Row_BillProducts['Price_Cost'] . ", 0, " . $Row_BillProducts['Porcentaje'] . ", 0, 0, 'Active')");
          } else {
            //--------------- si no lotes esta desactivado --------------------//

            $query_relation = 'SELECT Id FROM ' . $table_warehouse . ' WHERE Id_inventory = ' . $Row_BillProducts['Code_Product'] . ' AND Id_Warehouse = ' . $Id_Warehouse_Bill;

            $qry_relation = mysqli_query($connection, $query_relation);
            $data_relation = mysqli_fetch_assoc($qry_relation);

            $sql_petition = '
								INSERT INTO ' . $table_history . ' (Code_Item, Id_Batch, Id_Warehouse, Type, Method, Id_Method, Price_Buy, Unit, Motive, User_Code, Date)
								VALUES
								(
									' . $Row_BillProducts['Code_Product'] . ',
									' . $data_relation['Id'] . ',
									' . $Id_Warehouse_Bill . ',
									"Add" ,
									"Purchase" ,
									' . $Row_Bill['Id'] . ',
                  ' .$Row_BillProducts['Price_Cost']. ',
									' . $Row_BillProducts['Units'] . ',
									"[Se añadieron ' . floatval($Row_BillProducts['Units']) . ' por compra en la factura ' . $Row_Bill['Bill'] . ']" ,
									' . $Row_Bill['User_Code'] . ' ,
									' . $Row_Bill['Date'] . '
								) ';

            $Query_NewHistory = mysqli_query($connection, $sql_petition);

            if (!$Query_NewHistory) {
              echo 'Error -->' . mysqli_error($connection) . ' / ' . $sql_petition . ' .<br/>';
              die($sql_petition);
            } else {
              if ($PRINT) {
                echo ' Ok ';
              }
            }
          }
        }
      } else if ($Row_Bill['State'] != 'Erased' && $Row_Bill['State'] != 'Temporal' && $Row_Bill['State'] != 'History') {
        echo 'Error ->' . mysqli_error($connection) . ' <br />';
      }
    } else {
      $Query_History = mysqli_query($connection, 'DELETE FROM ' . $table_history . ' WHERE Id_Method = ' . $Row_Bill['Id'] . ' AND Method="Bill" ');
      echo mysqli_error($connection);

      $Query_Update = mysqli_query($connection, 'UPDATE ' . $p3_t1_bills . ' SET State="History" WHERE Id = ' . $Row_Bill['Id'] . ' ');
      echo mysqli_error($connection);

      if ($PRINT) {
        echo ' History ';
      }
    }
  }
}


//

function Anulated_Bill($IdFactura, $Comment_Cancel = '')
{
  global $connection, $p0_t1_config_business, $p1_t1_warehouse_inventory, $p1_t1_inventory_sele, $p1_t1_inventory_sale_c2_products_history_units, $p3_t1_bills, $p3_t1_bills_c1_products, $p3_t1_bills_c1_products_c1_components, $p1_t1_inventory_sele_c3_components, $p1_t2_inventory_production_c2_products_history_units, $p2_t1_clients_c3_funds_history, $p2_t1_clients_c2_points_red, $p2_t1_clients_c1_points_add, $p4_t1_vouchers_ingress, $p1_c1_relation_inventory, $p0_t3_segurity_log, $p3_t1_bills_c1_method_pay;

  //consulta la empresa
  $PetitionProduction = mysqli_query($connection, 'SELECT Inventory_Production,Create_History FROM ' . $p0_t1_config_business . ' WHERE Id=' . $_SESSION['Id_Business']);

  $Rowconfig = mysqli_fetch_assoc($PetitionProduction);

  $Inventory_Production = $Rowconfig['Inventory_Production'];
  $Create_History = $Rowconfig['Create_History'];


  //COnsulta datos de la factura
  //die('SELECT Prefix_Bill,Bill,Id_Business,Fund,Code_Client,Type,Date FROM ' . $p3_t1_bills . ' WHERE Id=' . $IdFactura . '');
  $Petition = mysqli_query($connection, 'SELECT Prefix_Bill,Bill,Id_Business,Fund,Code_Client,Type,Date FROM ' . $p3_t1_bills . ' WHERE Id=' . $IdFactura . '');
  $NumPetition = mysqli_fetch_assoc($Petition);
  $Id_Business = $NumPetition['Id_Business'];

  // seleccionamos los productos con las unidades, de la factura q se va a eliminar
  // die('SELECT Id,Code_Product,Units,Id_Warehouse FROM ' . $p3_t1_bills_c1_products . ' WHERE Id_Bill=' . $IdFactura . '');
  $ConsultProducts = mysqli_query($connection, 'SELECT Id,Code_Product,Units,Id_Warehouse FROM ' . $p3_t1_bills_c1_products . ' WHERE Id_Bill=' . $IdFactura . '');
  while ($Row = mysqli_fetch_assoc($ConsultProducts)) {

    //buscamos el tipo de procuto del cual se registró
    // die("SELECT  I.Type,I.Id_Compl_Prod,I.Compl_Value,I.Product,R.Unit FROM " . $p1_t1_inventory_sele . " I INNER JOIN " . $p1_t1_warehouse_inventory . " R ON I.Id=R.Id_Inventory WHERE  I.Id='" . $Row['Code_Product'] . "'  AND R.Id_Warehouse=" . $Row['Id_Warehouse']);
    $results_1a = mysqli_query($connection, "SELECT  I.Type,I.Id_Compl_Prod,I.Compl_Value,I.Product,R.Unit FROM " . $p1_t1_inventory_sele . " I INNER JOIN " . $p1_t1_warehouse_inventory . " R ON I.Id=R.Id_Inventory WHERE  I.Id='" . $Row['Code_Product'] . "'  AND R.Id_Warehouse=" . $Row['Id_Warehouse']);
    $row_1a = mysqli_fetch_assoc($results_1a);

    if ($row_1a['Type'] == 'Complement') {

      if ($Create_History == 1) {
        $Query_History = '
            INSERT INTO ' . $p1_t1_inventory_sale_c2_products_history_units . ' (Code_Item,Id_Batch,Id_Warehouse,Type,Method,Id_Method,Unit,Motive,User_Code,Date)
              VALUES
            (' . $row_1a['Id_Compl_Prod'] . ',0,' . $Row['Id_Warehouse'] . ',"Add","Bill",' . $IdFactura . ',' . $row_1a['Compl_Value'] . ',"[Devolución de ' . number_format($row_1a['Compl_Value']) . ' por factura n ' . $NumPetition['Bill'] . ']",' . $_SESSION['User_Code'] . ',' . $NumPetition['Date'] . ')';
        // die($Query_History);
        $Qry_History = mysqli_query($connection, $Query_History);

        if (!$Qry_History) {
          die("Error description -> history_1 : " . mysqli_error($connection));
        }
      } else {
        $Query_Remove = 'DELETE FROM ' . $p1_t1_inventory_sale_c2_products_history_units . ' WHERE Id_Warehouse  = ' . $_SESSION['Id_Warehouse'] . ' AND  Method = "Bill" AND Id_Method = ' . $_POST['Id_Bill'];
        $Qry_Remove = mysqli_query($connection, $Query_Remove);
      }
    } else {

      if ($Create_History == 1) {

        $Query_Componentes = 'SELECT BC.Id_Component,IC.* FROM ' . $p3_t1_bills_c1_products_c1_components . ' BC INNER JOIN ' . $p1_t1_inventory_sele_c3_components . ' IC ON IC.Code = BC.Id_Component WHERE BC.Id_Bill_Product = ' . $Row['Id'];
        $Qry_Componentes = mysqli_query($connection, $Query_Componentes);
        $Table = "";
        foreach ($Qry_Componentes as $Data_componentes) {
          $Table = ($Data_componentes == "Item_Prod") ?  $p1_t1_inventory_sale_c2_products_history_units : $p1_t2_inventory_production_c2_products_history_units;

          if ( is_null ($Data_componentes['Code_Item']) || $Data_componentes['Code_Item'] == ""  ) {

            $query_consult_ComponentMP = 'SELECT Code_Product_Producction , Quantity FROM '.$p1_c1_relation_inventory.' WHERE Code_Product_Sell = '.$Data_componentes['Id_Component'].' AND Type = "Component"';
            $qry_consult_ComponentMP = mysqli_query($connection , $query_consult_ComponentMP);
            foreach ($qry_consult_ComponentMP as $data_ComponentMP) {

              $query_consultBatch = 'SELECT Id_Batch FROM '.$p1_t2_inventory_production_c2_products_history_units.' WHERE Code_Item = '.$data_ComponentMP['Code_Product_Producction'].' AND  Type = "Remove" AND Method = "Bill" AND Id_Method = '.$IdFactura.'  ';
              $qry_consultBatch = mysqli_query($connection , $query_consultBatch);
              foreach ($qry_consultBatch as $data_batchMP) {

                $Query_History = '
              INSERT INTO ' . $p1_t2_inventory_production_c2_products_history_units . ' (Code_Item,Id_Batch,Id_Warehouse,Type,Method,Id_Method,Unit,Motive,User_Code,Date)
                VALUES
              (' . $data_ComponentMP['Code_Product_Producction'] . ', '.$data_batchMP['Id_Batch'].' ,  ' . $Row['Id_Warehouse'] . ',"Add","Bill",' . $IdFactura . ',' .  number_format($Row['Units'] * $data_ComponentMP['Quantity'] )  . ',"[Devolución de ' . number_format($Row['Units'] * $data_ComponentMP['Quantity'] ) . ' por factura n° ' . $NumPetition['Bill'] . ']",' . $_SESSION['User_Code'] . ',' . $NumPetition['Date'] . ')';
              $Qry_History = mysqli_query($connection, $Query_History);
              if (!$Qry_History) {
                echo "Error description -> history_ComponentMP : " . mysqli_error($connection);
                echo " <script>console.log( ".$Query_History." )</script> ";
              }

              }

            }

            continue;

          }

          $Query_History = '
              INSERT INTO ' . $Table . ' (Code_Item,Id_Warehouse,Type,Method,Id_Method,Unit,Motive,User_Code,Date)
                VALUES
              (' . $Data_componentes['Code_Item'] . ',' . $Row['Id_Warehouse'] . ',"Add","Bill",' . $IdFactura . ',' . $Data_componentes['Quantity_Affect'] . ',"[Devolución de ' . number_format($Data_componentes['Quantity_Affect']) . ' por factura n ' . $NumPetition['Bill'] . ']",' . $_SESSION['User_Code'] . ',' . $NumPetition['Date'] . ')';
          $Qry_History = mysqli_query($connection, $Query_History);
          if (!$Qry_History) {
            echo "Error description -> history_2 : " . mysqli_error($connection);
            echo " <script>console.log( ".$Query_History." )</script> ";
          }
        }

        $Query_Batch_History = 'SELECT Id_Batch FROM ' . $p1_t1_inventory_sale_c2_products_history_units . ' WHERE Code_Item = ' . $Row['Code_Product'] . ' AND Id_Warehouse = ' . $_SESSION['Id_Warehouse'] . ' AND Method = "Bill" AND Id_Method = ' . $IdFactura . ' AND Type != "Add" ';
        $qry_Batch_History = mysqli_query($connection, $Query_Batch_History);
        $data_batch = mysqli_fetch_assoc($qry_Batch_History);

        if ($data_batch['Id_Batch'] == "") {
          $data_batch['Id_Batch'] = $Row['Id_Warehouse'];
        }

        $Query_History = '
            INSERT INTO ' . $p1_t1_inventory_sale_c2_products_history_units . ' (Code_Item,Id_Warehouse,Id_Batch,Type,Method,Id_Method,Unit,Motive,User_Code,Date)
              VALUES
            (' . $Row['Code_Product'] . ',' . $Row['Id_Warehouse'] . ', ' . $data_batch['Id_Batch'] . ' , "Add","Bill",' . $IdFactura . ',' . $Row['Units'] . ',"[Devolución de ' . number_format($Row['Units']) . ' por factura n ' . $NumPetition['Bill'] . ']",' . $_SESSION['User_Code'] . ',' . $NumPetition['Date'] . ')';

        $Qry_History = mysqli_query($connection, $Query_History);

        if (!$Qry_History) {
          die("Error description -> history_3 : " . mysqli_error($connection));
        }

        if ($NumPetition['Type'] == 'Aside') {
          $Delete_History = "DELETE FROM " . $p1_t1_inventory_sale_c2_products_history_units . " WHERE Id_Method = " . $IdFactura . " AND Method = 'Bill' AND Type='Aside'";
          $Qry_delete = mysqli_query($connection, $Delete_History);

          $Delete_Abonos = "UPDATE " . $p4_t1_vouchers_ingress . " SET State = 'Erased' WHERE Code_Bill = " . $IdFactura;
          // die($Delete_Abonos);
          $Qry_Abonos = mysqli_query($connection, $Delete_Abonos);
        }
      } else {

        $Query_Remove = 'DELETE FROM ' . $p1_t1_inventory_sale_c2_products_history_units . ' WHERE Id_Warehouse  = ' . $_SESSION['Id_Warehouse'] . ' AND  Method = "Bill" AND Id_Method = ' . $_POST['Id_Bill'];
        $Qry_Remove = mysqli_query($connection, $Query_Remove);
        if (!$Qry_Remove) {
          die(mysqli_error($connection) . '<br />' . $Qry_Remove);
        }
      }
    }

    if ($Inventory_Production == 1) {
      //busca los productos de produccion relacionados con el de venta
      $results_1 = mysqli_query($connection, "SELECT * FROM " . $p1_c1_relation_inventory . " WHERE Code_Product_Sell='" . $Row['Code_Product'] . "' ");    // Tabla 1 de productos
      while ($row_1 = mysqli_fetch_assoc($results_1)) {
        //actualiza las unidades de productos de produccion

        $Query_Batch_History = 'SELECT Id_Batch, Unit FROM ' . $p1_t2_inventory_production_c2_products_history_units . ' WHERE Code_Item = ' . $row_1['Code_Product_Producction'] . ' AND Id_Warehouse = ' . $_SESSION['Id_Warehouse'] . ' AND Method = "Bill" AND Id_Method = ' . $IdFactura . ' AND Type != "Add" ';
        // die( $Query_Batch_History);
        $qry_Batch_History = mysqli_query($connection, $Query_Batch_History);

        // die($data_batch['Id_Batch']);


        while($data_batch = mysqli_fetch_assoc($qry_Batch_History)){
          if ($data_batch['Id_Batch'] == "") {
            $data_batch['Id_Batch'] = $Row['Id_Warehouse'];
          }

          if ($Create_History == 1) {

            $Query_History = '
                INSERT INTO ' . $p1_t2_inventory_production_c2_products_history_units . ' (Code_Item, Id_Warehouse, Id_Batch, Type, Method, Id_Method, Unit, Motive, User_Code,Date)
                  VALUES
                (' . $row_1['Code_Product_Producction'] . ',' . $Row['Id_Warehouse'] . ', ' . $data_batch['Id_Batch'] . ',"Add","Bill",' . $IdFactura . ',' . $data_batch['Unit'] . ',"[Devolución de ' . round($data_batch['Unit']) . ' por factura n° ' . $NumPetition['Bill'] . ']",' . $_SESSION['User_Code'] . ',' . date('U') . ')';

            // die($Query_History);
            $Qry_History = mysqli_query($connection, $Query_History);

            if (!$Qry_History) {
              die("----Error description: " . mysqli_error($connection));
            }
          } else {

            $Query_Remove_production = 'DELETE FROM ' . $p1_t2_inventory_production_c2_products_history_units . ' WHERE Id_Warehouse  = ' . $_SESSION['Id_Warehouse'] . ' AND  Method = "Bill" AND Id_Method = ' . $_POST['Id_Bill'];
            $Qry_Remove_production = mysqli_query($connection, $Query_Remove_production);


            if (!$Qry_Remove_production) {
              die(mysqli_error($connection) . '<br />' . $Qry_Remove_production);
            }
          }


        }


      }
    }
  }

  //eliminamos factura de tabla facturacion
  $Query_PetitionBill = 'UPDATE ' . $p3_t1_bills . ' SET State = "Erased",Comment_Cancel="' . $Comment_Cancel . '" WHERE Id = ' . $IdFactura;

  Insert_Log( 'Anulación de factura' , 'Anulación de factura  <b> '.$NumPetition['Prefix_Bill'].' '.$NumPetition['Bill'].' </b> ' , 'Facturas' , $IdFactura , 0);


  $PetitionBill = mysqli_query($connection, $Query_PetitionBill);
  if (!$PetitionBill) {
    die(mysqli_error($connection) . '<br />' . $Query_PetitionBill);

  }
  // return true;
  //anulacomprobante de ingreso con depositos
  $Query_DeleteBillDeposit = 'UPDATE ' . $p4_t1_vouchers_ingress . ' SET State = "Erased" WHERE Id_Business =' . $Id_Business . ' AND Code_Bill = ' . $IdFactura . '';
  $DeleteBillDeposit = mysqli_query($connection, $Query_DeleteBillDeposit);
  if (!$DeleteBillDeposit) {
    die(mysqli_error($connection) . '<br />' . $Query_DeleteBillDeposit);
  }

  //anulapuntos
  $results_1 = mysqli_query($connection, "SELECT * FROM " . $p2_t1_clients_c1_points_add . " WHERE Id_bill='" . $IdFactura . "' ");
  while ($row_1 = mysqli_fetch_assoc($results_1)) {
    $connection = connection_db();
    $DeleteBillDeposit = mysqli_query($connection, 'INSERT INTO ' . $p2_t1_clients_c2_points_red . ' (Id_Business, Id_bill, Id_Client, Id_point_add, Point_calculate, Type, Date) VALUES (' . $Id_Business . ',' . $IdFactura . ',' . $row_1['Id_Client'] . ',' . $row_1['Id'] . ',' . $row_1['Point_calculate'] . ',"Anulated",' . date('U') . ') ');
  }

  //Consulta si tiene metodo de pago por fondos
  $Petition = mysqli_query($connection, 'SELECT Id_Method FROM '.$p3_t1_bills_c1_method_pay.' WHERE Id_Source=' . $IdFactura . ' AND Id_Method = 7');

  // die('SELECT Id_Method FROM '.$p3_t1_bills_c1_method_pay.' WHERE Id=' . $IdFactura . ' AND Id_Method = 7');
  if (mysqli_num_rows($Petition) > 0) {
    $History_Fund = mysqli_query($connection, "UPDATE " . $p2_t1_clients_c3_funds_history . " SET State = 'Erase' WHERE Source_Value= " . $IdFactura . " AND Id_Cliente =" . $NumPetition['Code_Client']);
  }

  $Petition = mysqli_query($connection, 'INSERT INTO ' . $p0_t3_segurity_log . ' (Type, Description, User, Date, Name_Element, Id_Element, Id_module) VALUES ("Anulacion de Factura", "Se anula la factura ' . $NumPetition['Prefix_Bill'] . ' ' . $NumPetition['Bill'] . ' ", "' . $_SESSION['User_Code'] . '", "' . date('U') . '", "Factura", "' . $IdFactura . '", 21)');

  return true;
}

function Anulated_Rem($IdFactura, $Comment_Cancel = '')
{
  global $connection, $p0_t1_config_business, $p1_t1_warehouse_inventory, $p1_t1_inventory_sele, $p1_t1_inventory_sale_c2_products_history_units, $p7_t1_remissions, $p7_t1_remissions_c1_products, $p3_t1_bills_c1_products_c1_components, $p1_t1_inventory_sele_c3_components, $p1_t2_inventory_production_c2_products_history_units, $p2_t1_clients_c3_funds_history, $p2_t1_clients_c2_points_red, $p2_t1_clients_c1_points_add, $p4_t1_vouchers_ingress, $p1_c1_relation_inventory, $p0_t3_segurity_log;

  //consulta la empresa
  $PetitionProduction = mysqli_query($connection, 'SELECT Inventory_Production,Create_History FROM ' . $p0_t1_config_business . ' WHERE Id=' . $_SESSION['Id_Business']);

  $Rowconfig = mysqli_fetch_assoc($PetitionProduction);

  $Inventory_Production = $Rowconfig['Inventory_Production'];
  $Create_History = $Rowconfig['Create_History'];


  //COnsulta datos de la factura
  $Petition = mysqli_query($connection, 'SELECT Prefix_Bill,Bill,Id_Business,Fund,Code_Client,Type FROM ' . $p7_t1_remissions . ' WHERE Id=' . $IdFactura . '');
  $NumPetition = mysqli_fetch_assoc($Petition);
  $Id_Business = $NumPetition['Id_Business'];

  $ConsultProducts = mysqli_query($connection, 'SELECT Id,Code_Product,Units,Id_Warehouse FROM ' . $p7_t1_remissions_c1_products . ' WHERE Id_Bill=' . $IdFactura . '');
  while ($Row = mysqli_fetch_assoc($ConsultProducts)) {

    //buscamos el tipo de procuto del cual se registró
    $results_1a = mysqli_query($connection, "SELECT  I.Type,I.Id_Compl_Prod,I.Compl_Value,I.Product,R.Unit FROM " . $p1_t1_inventory_sele . " I INNER JOIN " . $p1_t1_warehouse_inventory . " R ON I.Id=R.Id_Inventory WHERE  I.Id='" . $Row['Code_Product'] . "'  AND R.Id_Warehouse=" . $Row['Id_Warehouse']);
    $row_1a = mysqli_fetch_assoc($results_1a);

    if ($row_1a['Type'] == 'Complement') {

      if ($Create_History == 1) {
        $Query_History = '
            INSERT INTO ' . $p1_t1_inventory_sale_c2_products_history_units . ' (Code_Item,Id_Warehouse,Type,Method,Id_Method,Unit,Motive,User_Code,Date)
              VALUES
            (' . $Row['Code_Product'] . ',' . $Row['Id_Warehouse'] . ',"Add","Remission",' . $IdFactura . ',' . $Row['Units'] . ',"[Devolución de ' . number_format($Row['Units']) . ' por remisión n ' . $NumPetition['Bill'] . ']",' . $_SESSION['User_Code'] . ',' . date('U') . ')';

        $Qry_History = mysqli_query($connection, $Query_History);

        if (!$Qry_History) {
          die("Error description -> history_1 : " . mysqli_error($connection));
        }
      } else {
        $Query_Remove = 'DELETE FROM ' . $p1_t1_inventory_sale_c2_products_history_units . ' WHERE Id_Warehouse  = ' . $_SESSION['Id_Warehouse'] . ' AND  Method = "Bill" AND Id_Method = ' . $_POST['Id_Bill'];
        $Qry_Remove = mysqli_query($connection, $Query_Remove);
      }
    } else {

      if ($Create_History == 1) {

        // $Query_Componentes = 'SELECT BC.Id_Component,IC.* FROM '.$p3_t1_bills_c1_products_c1_components.' BC INNER JOIN '.$p1_t1_inventory_sele_c3_components.' IC ON IC.Code = BC.Id_Component WHERE BC.Id_Bill_Product = '.$Row['Id'];
        // $Qry_Componentes = mysqli_query($connection,$Query_Componentes);

        // $Table= "";
        // foreach ($Qry_Componentes as $Data_componentes ) {
        //   $Table = ( $Data_componentes == "Item_Prod" ) ?  $p1_t1_inventory_sale_c2_products_history_units : $p1_t2_inventory_production_c2_products_history_units  ;

        //   $Query_History = '
        //   INSERT INTO '.$Table.' (Code_Item,Id_Warehouse,Type,Method,Id_Method,Unit,Motive,User_Code,Date)
        //     VALUES
        //   ('.$Data_componentes['Code_Item'].','.$Row['Id_Warehouse'].',"Add","Bill",'.$IdFactura.','.$Data_componentes['Quantity_Affect'].',"[Devolución de '.number_format($Data_componentes['Quantity_Affect']).' por factura n '.$NumPetition['Bill'].']",'.$_SESSION['User_Code'].','.date('U').')';
        //   $Qry_History = mysqli_query($connection,$Query_History);
        //   if (!$Qry_History) {
        //     echo "Error description -> history_2 : " .mysqli_error($connection);
        //   }
        // }

        $Query_Batch_History = 'SELECT Id_Batch FROM ' . $p1_t1_inventory_sale_c2_products_history_units . ' WHERE Code_Item = ' . $Row['Code_Product'] . ' AND Id_Warehouse = ' . $_SESSION['Id_Warehouse'] . ' AND Method = "Remission" AND Id_Method = ' . $IdFactura . ' AND Type != "Add" ';
        $qry_Batch_History = mysqli_query($connection, $Query_Batch_History);
        $data_batch = mysqli_fetch_assoc($qry_Batch_History);

        if ($data_batch['Id_Batch'] == "") {
          $data_batch['Id_Batch'] = $Row['Id_Warehouse'];
        }

        $Query_History = '
            INSERT INTO ' . $p1_t1_inventory_sale_c2_products_history_units . ' (Code_Item,Id_Warehouse,Id_Batch,Type,Method,Id_Method,Unit,Motive,User_Code,Date)
              VALUES
            (' . $Row['Code_Product'] . ',' . $Row['Id_Warehouse'] . ', ' . $data_batch['Id_Batch'] . ' , "Add","Remission",' . $IdFactura . ',' . $Row['Units'] . ',"[Devolución de ' . number_format($Row['Units']) . ' por remisión n ' . $NumPetition['Bill'] . ']",' . $_SESSION['User_Code'] . ',' . date('U') . ')';

        $Qry_History = mysqli_query($connection, $Query_History);

        if (!$Qry_History) {
          die("Error description -> history_3 : " . mysqli_error($connection));
        }
      } else {

        $Query_Remove = 'DELETE FROM ' . $p1_t1_inventory_sale_c2_products_history_units . ' WHERE Id_Warehouse  = ' . $_SESSION['Id_Warehouse'] . ' AND  Method = "Remission" AND Id_Method = ' . $_POST['Id_Bill'];
        $Qry_Remove = mysqli_query($connection, $Query_Remove);
        if (!$Qry_Remove) {
          die(mysqli_error($connection) . '<br />' . $Qry_Remove);
        }
      }
    }

    if ($Inventory_Production == 1) {
      //busca los productos de produccion relacionados con el de venta
      $results_1 = mysqli_query($connection, "SELECT * FROM " . $p1_c1_relation_inventory . " WHERE Code_Product_Sell='" . $Row['Code_Product'] . "' ");    // Tabla 1 de productos
      while ($row_1 = mysqli_fetch_assoc($results_1)) {
        //actualiza las unidades de productos de produccion

        $Query_Batch_History = 'SELECT Id_Batch FROM ' . $p1_t1_inventory_sale_c2_products_history_units . ' WHERE Code_Item = ' . $row_1['Code_Product_Producction'] . ' AND Id_Warehouse = ' . $_SESSION['Id_Warehouse'] . ' AND Method = "Remission" AND Id_Method = ' . $IdFactura . ' AND Type != "Add" ';
        $qry_Batch_History = mysqli_query($connection, $Query_Batch_History);
        $data_batch = mysqli_fetch_assoc($qry_Batch_History);

        if ($data_batch['Id_Batch'] == "") {
          $data_batch['Id_Batch'] = $Row['Id_Warehouse'];
        }

        if ($Create_History == 1) {

          $Query_History = '
              INSERT INTO ' . $p1_t2_inventory_production_c2_products_history_units . ' (Code_Item, Id_Warehouse, Id_Batch, Type, Method, Id_Method, Unit, Motive, User_Code,Date)
                VALUES
              (' . $row_1['Code_Product_Producction'] . ',' . $Row['Id_Warehouse'] . ', ' . $data_batch['Id_Batch'] . ',"Add","Remission",' . $IdFactura . ',' . ($row_1['Quantity'] * $Row['Units']) . ',"[Devolución de ' . $row_1['Quantity'] * $Row['Units'] . ' por remisión n ' . $NumPetition['Bill'] . ']",' . $_SESSION['User_Code'] . ',' . date('U') . ')';


          $Qry_History = mysqli_query($connection, $Query_History);

          if (!$Qry_History) {
            die("----Error description: " . mysqli_error($connection));
          }
        } else {

          $Query_Remove_production = 'DELETE FROM ' . $p1_t2_inventory_production_c2_products_history_units . ' WHERE Id_Warehouse  = ' . $_SESSION['Id_Warehouse'] . ' AND  Method = "Remission" AND Id_Method = ' . $_POST['Id_Bill'];
          $Qry_Remove_production = mysqli_query($connection, $Query_Remove_production);

          if (!$Qry_Remove_production) {
            die(mysqli_error($connection) . '<br />' . $Qry_Remove_production);
          }
        }
      }
    }
  }

  //eliminamos factura de tabla facturacion
  $Query_PetitionBill = 'UPDATE ' . $p7_t1_remissions . ' SET State = "Erased",Comment_Cancel="' . $Comment_Cancel . '" WHERE Id = ' . $IdFactura;
  // die($Query_PetitionBill);

  $PetitionBill = mysqli_query($connection, $Query_PetitionBill);
  if (!$PetitionBill) {
    die(mysqli_error($connection) . '<br />' . $Query_PetitionBill);
  }
  return true;

  //anulapuntos
  $results_1 = mysqli_query($connection, "SELECT * FROM " . $p2_t1_clients_c1_points_add . " WHERE Id_bill='" . $IdFactura . "' ");
  while ($row_1 = mysqli_fetch_assoc($results_1)) {
    $connection = connection_db();
    $DeleteBillDeposit = mysqli_query($connection, 'INSERT INTO ' . $p2_t1_clients_c2_points_red . ' (Id_Business, Id_bill, Id_Client, Id_point_add, Point_calculate, Type, Date) VALUES (' . $Id_Business . ',' . $IdFactura . ',' . $row_1['Id_Client'] . ',' . $row_1['Id'] . ',' . $row_1['Point_calculate'] . ',"Anulated",' . date('U') . ') ');
  }


  if ($NumPetition['Fund'] > 0) {
    $History_Fund = mysqli_query($connection, "UPDATE " . $p2_t1_clients_c3_funds_history . " SET State = 'Erase' WHERE Source_Value= " . $IdFactura . " AND Id_Cliente =" . $NumPetition['Code_Client']);
  }

  $Petition = mysqli_query($connection, 'INSERT INTO ' . $p0_t3_segurity_log . ' (Type, Description, User, Date, Name_Element, Id_Element, Id_module) VALUES ("Anulacion de Remisión", "Se anula la remisión ' . $NumPetition['Prefix_Bill'] . ' ' . $NumPetition['Bill'] . ' ", "' . $_SESSION['User_Code'] . '", "' . date('U') . '", "Remisión", "' . $IdFactura . '", 21)');

  return true;
}

function Anulated_Buy($IdFactura, $Comment_Cancel = '')
{
  global $connection, $p0_t1_config_business, $p1_t1_warehouse_inventory, $p1_t1_inventory_sele, $p1_t1_inventory_sale_c2_products_history_units, $p9_t1_buys, $p9_t1_buys_c1_products, $p3_t1_bills_c1_products_c1_components, $p1_t1_inventory_sele_c3_components, $p1_t2_inventory_production_c2_products_history_units, $p2_t1_clients_c3_funds_history, $p2_t1_clients_c2_points_red, $p2_t1_clients_c1_points_add, $p4_t2_vouchers_egress, $p1_c1_relation_inventory, $p0_t3_segurity_log, $p9_t1_buys_c2_payments , $p1_t1_warehouse_inventory_production , $p1_t2_inventory_production  ;

  //consulta la empresa
  $PetitionProduction = mysqli_query($connection, 'SELECT Inventory_Production,Create_History FROM ' . $p0_t1_config_business . ' WHERE Id=' . $_SESSION['Id_Business']);

  $Rowconfig = mysqli_fetch_assoc($PetitionProduction);

  $Inventory_Production = $Rowconfig['Inventory_Production'];
  $Create_History = $Rowconfig['Create_History'];


  //COnsulta datos de la factura
  $Petition = mysqli_query($connection, 'SELECT Prefix_Bill,Bill,Id_Business,Code_Provider,Type FROM ' . $p9_t1_buys . ' WHERE Id=' . $IdFactura . '');
  $NumPetition = mysqli_fetch_assoc($Petition);
  $Id_Business = $NumPetition['Id_Business'];

  // seleccionamos los productos con las unidades, de la factura q se va a eliminar
  $ConsultProducts = mysqli_query($connection, 'SELECT Id,Code_Product,Units,Id_Warehouse,Type_Product FROM ' . $p9_t1_buys_c1_products . ' WHERE Id_Bill=' . $IdFactura . '');
  while ($Row = mysqli_fetch_assoc($ConsultProducts)) {

    $table_inventory = ($Row['Type_Product'] == "Sale") ? $p1_t1_inventory_sele : $p1_t2_inventory_production ;
    $table_warehouse = ($Row['Type_Product'] == "Sale") ? $p1_t1_warehouse_inventory : $p1_t1_warehouse_inventory_production ;
    $table_history = ($Row['Type_Product'] == "Sale") ? $p1_t1_inventory_sale_c2_products_history_units : $p1_t2_inventory_production_c2_products_history_units	 ;

    //buscamos el tipo de procuto del cual se registró
    $results_1a = mysqli_query($connection, "SELECT  I.Type,I.Id_Compl_Prod,I.Compl_Value,I.Product,R.Unit FROM " . $table_inventory . " I INNER JOIN " . $table_warehouse . " R ON I.Id=R.Id_Inventory WHERE  I.Id='" . $Row['Code_Product'] . "'  AND R.Id_Warehouse=" . $Row['Id_Warehouse']);
    $row_1a = mysqli_fetch_assoc($results_1a);
    if ($Create_History == 1) {

      // $Query_Componentes = 'SELECT BC.Id_Component,IC.* FROM '.$p3_t1_bills_c1_products_c1_components.' BC INNER JOIN '.$p1_t1_inventory_sele_c3_components.' IC ON IC.Code = BC.Id_Component WHERE BC.Id_Bill_Product = '.$Row['Id'];
      // $Qry_Componentes = mysqli_query($connection,$Query_Componentes);
      //
      // $Table= "";
      // foreach ($Qry_Componentes as $Data_componentes ) {
      //   $Table = ( $Data_componentes == "Item_Prod" ) ?  $p1_t1_inventory_sale_c2_products_history_units : $p1_t2_inventory_production_c2_products_history_units  ;
      //
      //   $Query_History = '
      //   INSERT INTO '.$Table.' (Code_Item,Id_Warehouse,Type,Method,Id_Method,Unit,Motive,User_Code,Date)
      //     VALUES
      //   ('.$Data_componentes['Code_Item'].','.$Row['Id_Warehouse'].',"Add","Bill",'.$IdFactura.','.$Data_componentes['Quantity_Affect'].',"[Devolución de '.number_format($Data_componentes['Quantity_Affect']).' por factura n '.$NumPetition['Bill'].']",'.$_SESSION['User_Code'].','.date('U').')';
      //   $Qry_History = mysqli_query($connection,$Query_History);
      //   if (!$Qry_History) {
      //     echo "Error description -> history_2 : " .mysqli_error($connection);
      //   }
      // }

      $Query_Batch_History = 'SELECT Id_Batch FROM ' . $table_history . ' WHERE Code_Item = ' . $Row['Code_Product'] . ' AND Id_Warehouse = ' . $_SESSION['Id_Warehouse'] . ' AND Method = "Purchase" AND Id_Method = ' . $IdFactura . ' AND Type != "Remove" ';
      $qry_Batch_History = mysqli_query($connection, $Query_Batch_History);
      $data_batch = mysqli_fetch_assoc($qry_Batch_History);

      if ($data_batch['Id_Batch'] == "") {
        $data_batch['Id_Batch'] = $Row['Id_Warehouse'];
      }

      $Query_History = '
            INSERT INTO ' . $table_history . ' (Code_Item,Id_Warehouse,Id_Batch,Type,Method,Id_Method,Unit,Motive,User_Code,Date)
              VALUES
            (' . $Row['Code_Product'] . ',' . $Row['Id_Warehouse'] . ', ' . $data_batch['Id_Batch'] . ' , "Remove", "Purchase",' . $IdFactura . ',' . $Row['Units'] . ',"[Devolución de ' . number_format($Row['Units']) . ' por factura de compra N° ' . $NumPetition['Bill'] . ']",' . $_SESSION['User_Code'] . ',' . date('U') . ')';

      $Qry_History = mysqli_query($connection, $Query_History);
      echo mysqli_error($connection);

      if (!$Qry_History) {
        die("Error description -> history_3 : " . mysqli_error($connection));
      }
    } else {

      $Query_Remove = 'DELETE FROM ' . $table_history . ' WHERE Id_Warehouse  = ' . $_SESSION['Id_Warehouse'] . ' AND  Method = "Purchase" AND Id_Method = ' . $IdFactura;
      $Qry_Remove = mysqli_query($connection, $Query_Remove);
      if (!$Qry_Remove) {
        die(mysqli_error($connection) . '<br />' . $Qry_Remove);
      }
    }
  }

  //eliminamos factura de tabla facturacion
  $Query_PetitionBill = 'UPDATE ' . $p9_t1_buys . ' SET State = "Erased",Comment_Cancel="' . $Comment_Cancel . '" WHERE Id = ' . $IdFactura;

  $PetitionBill = mysqli_query($connection, $Query_PetitionBill);
  if (!$PetitionBill) {
    die(mysqli_error($connection) . '<br />' . $Query_PetitionBill);
  }
  //anulacomprobante de ingreso con depositos
  $Query_DeleteBillDeposit = 'UPDATE ' . $p4_t2_vouchers_egress . ' SET State = "Erased" WHERE Id_Business =' . $Id_Business . ' AND Source="puschase" AND Source_Value = ' . $IdFactura . '';
  $DeleteBillDeposit = mysqli_query($connection, $Query_DeleteBillDeposit);
  if (!$DeleteBillDeposit) {
    die(mysqli_error($connection) . '<br />' . $Query_DeleteBillDeposit);
  }

  $Query_deposits = 'SELECT Id FROM ' . $p9_t1_buys_c2_payments . ' WHERE Source= "deposit" AND Source_Value = ' . $IdFactura . '';
  $sql_deposits = mysqli_query($connection, $Query_deposits);

  while ($row_deposits = mysqli_fetch_assoc($sql_deposits)) {

    $Query_2 = "UPDATE " . $p9_t1_buys_c2_payments . " SET State='Erased' WHERE Id='" . $row_deposits['Id'] . "'";
    $petition_2 = mysqli_query($connection, $Query_2);
    if (!$petition_2) {
      die('Error al eliminar comprobante de pago de la factura de compra');
    }

    $Query_3 = "UPDATE " . $p4_t2_vouchers_egress . " SET State='Erased' WHERE Source_Value='" . $row_deposits['Id'] . "' AND Source='deposit'";
    $petition_3 = mysqli_query($connection, $Query_3);
    if (!$petition_3) {
      die('Error al eliminar comprobante de egreso');
    }
  }

  $Petition = mysqli_query($connection, 'INSERT INTO ' . $p0_t3_segurity_log . ' (Type, Description, User, Date, Name_Element, Id_Element, Id_module) VALUES ("Anulacion de Factura", "Se anula la factura ' . $NumPetition['Prefix_Bill'] . ' ' . $NumPetition['Bill'] . ' ", "' . $_SESSION['User_Code'] . '", "' . date('U') . '", "Factura", "' . $IdFactura . '", 21)');

  return true;
}

function Evaluate_Program_Pay_Complete($Id)
{
  global $connection, $p3_t1_bills_c7_credits_program_pay, $p3_t1_bills_c7_history_program_pay, $p3_t1_bills;

  $results_4 = mysqli_query($connection, 'SELECT CPP.* ,(SELECT SUM(Value_Pay) FROM ' . $p3_t1_bills_c7_history_program_pay . ' WHERE Id_Payment = CPP.Id ) AS Payed FROM ' . $p3_t1_bills_c7_credits_program_pay . ' CPP  WHERE Id_bill=' . $Id);

  $Pending = 0;
  while ($row_4 = @mysqli_fetch_assoc($results_4)) { //recorre metodos de pago

    $Pending += $row_4['Value'] - $row_4['Payed'];

    if ($row_4['Value'] == ($row_4['Payed'] - $row_4['Value_Mora'])) {
      $petition = mysqli_query($connection, "UPDATE " . $p3_t1_bills_c7_credits_program_pay . " SET Paid = 'y' WHERE Id = " . $row_4['Id'] . "");
    }
  }
  // die('---'.$Pending);
  if ($Pending == 0) {
    $petition = mysqli_query($connection, "UPDATE " . $p3_t1_bills . " SET Paid = 'y' WHERE Id = " . $Id . "");
  }
}


function Get_Methods_Pay()
{

  global $connection, $p0_t8_method_pay;

  $Array_Methods = array();

  $Query_Methods = 'SELECT Id,Name FROM ' . $p0_t8_method_pay . ' WHERE State ="Active" ';
  $Qry_Methods = mysqli_query($connection, $Query_Methods);
  $Array_Methods = array();
  foreach ($Qry_Methods as $Data_Method) {
    array_push($Array_Methods, array('Id' => $Data_Method['Id'], 'Name' => $Data_Method['Name']));
  }

  return $Array_Methods;
}


function Send_Bill_Msj($Id_Bill)
{
  global $connection;

  $Row_Bill = Calculate_Total_Bill($Id_Bill);
  $Row_Bill = json_encode($Row_Bill);


  //API URL
  $url = 'http://www.gesadmin.co/api/sms/consume.php';

  //create a new cURL resource
  $ch = curl_init($url);

  $payload = json_encode($Row_Bill);

  //attach encoded JSON string to the POST fields
  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

  //set the content type to application/json
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

  //return response instead of outputting
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  //execute the POST request
  $result = curl_exec($ch);

  //close cURL resource
  curl_close($ch);

  return $result;
}


function Calculate_price_average($Id_product, $type)
{
  // $connection = connection_db();
  global $p1_t1_warehouse_inventory, $connection;
  if ($type == 'sale') {
    $query_inventory = "SELECT Price_Suggest, Id FROM " . $p1_t1_warehouse_inventory . " WHERE Id_Inventory = " . $Id_product . " AND Id_Warehouse =" . $_SESSION['Id_Warehouse'];
    $sql_inventory = mysqli_query($connection, $query_inventory);

    $num = 0;
    $Total_price_sale = 0;
    $Units_total = 0;
    foreach ($sql_inventory as $row_inventory) {
      $num++;
      // $Total_price_sale = 0;

      $price_sale = $row_inventory['Price_Suggest'];
      $units = Consult_History_Units($Id_product, '', '', 'x', $row_inventory['Id']);

      $Total_price_sale += $price_sale * $units;
      $Units_total += $units;
      // echo "$num. $price_sale * $units = $Total_price_sale <br />";
    }

    if ($Units_total == 0) {
      $price_average = 0;
    } else {
      $price_average = $Total_price_sale / $Units_total;
    }

    return $price_average;
    // echo "Precio promedio = $Total_price_sale / $Units_total = $price_average";
  }
}

function array_sort_by(&$arrIni, $col, $order = SORT_ASC)
{
  $arrAux = array();
  foreach ($arrIni as $key => $row) {
    $arrAux[$key] = is_object($row) ? $arrAux[$key] = $row->$col : $row[$col];
    $arrAux[$key] = strtolower($arrAux[$key]);
  }

  array_multisort($arrAux, $order, $arrIni);
}

function Consult_History_Client($Id_Client)
{
  global $p28_t1_crm_c5_events, $connection;

  $array_return  = array();

  $sql_client_event = mysqli_query($connection, 'SELECT * FROM ' . $p28_t1_crm_c5_events . ' WHERE Id_Client =' . $Id_Client);
  while ($row_client_event = mysqli_fetch_assoc($sql_client_event)) {
    array_push(
      $array_return,
      array(
        'type' => 'event',
        'date' => $row_client_event['Date'],
        'data' => $row_client_event
      )
    );
  }

  $sql_client_bill = mysqli_query($connection, 'SELECT * FROM p3_t1_bills_lite WHERE Code_Client =' . $Id_Client . ' AND State="Active"');
  while ($row_client_bill = mysqli_fetch_assoc($sql_client_bill)) {
    array_push(
      $array_return,
      array(
        'type' => 'bill',
        'date' => $row_client_bill['Date'],
        'data' => $row_client_bill
      )
    );
  }

  $sql_client_quotation = mysqli_query($connection, 'SELECT * FROM p3_t1_bills_lite WHERE Code_Client =' . $Id_Client . ' AND State="Active" AND Type_Bill = "Quotation"');
  while ($row_client_quotation = mysqli_fetch_assoc($sql_client_quotation)) {
    array_push(
      $array_return,
      array(
        'type' => 'quotation',
        'date' => $row_client_quotation['Date'],
        'data' => $row_client_quotation
      )
    );
  }

  array_sort_by($array_return, 'date', SORT_DESC);

  return $array_return;
}

function Consult_All_Documents($MaxRow = 50, $OrderBy = "date", $SearchBy = "")
{
  global $connection, $p8_t1_provider;


  $array_return  = array();

  $MaxRow = round($MaxRow / 6);
  // die($MaxRow);

  $Limit = 'LIMIT ' . $MaxRow;

  $sql_bills = mysqli_query($connection, 'SELECT * FROM p3_t1_bills_lite WHERE Type != "InviteHouse" AND Type_Bill = "Bill" AND Id_Business = ' . $_SESSION['Id_Business'] . ' AND State = "Active" ');

  foreach ($sql_bills as $row_bills) {
    array_push(
      $array_return,
      array(
        'type' => 'Factura',
        'consecutive' => $row_bills['Bill'],
        'tercero' => $row_bills['Client'],
        'total' => $row_bills['Total_Total'],
        'date' => $row_bills['Date'],
        'data' => $row_bills
      )
    );
  }

  $sql_ingress = mysqli_query($connection, 'SELECT * FROM 	p4_t1_voucher_ingress WHERE Id_Business = ' . $_SESSION['Id_Business'] . ' AND State = "Active" ');
  foreach ($sql_ingress as $row_ingress) {

    array_push(
      $array_return,
      array(
        'type' => 'Ingreso',
        'consecutive' => $row_ingress['Comprobante'],
        'tercero' => 'N/A',
        'total' => $row_ingress['Total'],
        'date' => $row_ingress['Date'],
        'data' => $row_ingress
      )
    );
  }

  $sql_egress = mysqli_query($connection, 'SELECT * FROM 	p4_t1_voucher_egress WHERE Id_Business = ' . $_SESSION['Id_Business'] . ' AND State = "Active" ');

  foreach ($sql_egress as $row_egress) {
    array_push(
      $array_return,
      array(
        'type' => 'Egreso',
        'consecutive' => $row_egress['Comprobante'],
        'tercero' => $row_egress['Provider_Name'],
        'total' => $row_egress['Total'],
        'date' => $row_egress['Date'],
        'data' => $row_egress
      )
    );
  }

  $sql_buys = mysqli_query($connection, 'SELECT * FROM 	p9_t1_buy WHERE Id_Business = ' . $_SESSION['Id_Business'] . ' AND State = "Active" ');
  foreach ($sql_buys as $row_buys) {

    array_push(
      $array_return,
      array(
        'type' => 'Compra',
        'consecutive' => $row_buys['Bill'],
        'tercero' => $row_buys['Provider'],
        'total' => $row_buys['Total_Total'],
        'date' => $row_buys['Date'],
        'data' => $row_buys
      )
    );
  }

  $sql_NoteCredits = mysqli_query($connection, 'SELECT * FROM p2_t2_credit_notes  WHERE Id_Business = ' . $_SESSION['Id_Business'] . ' AND State = "Active" ');
  foreach ($sql_NoteCredits as $row_NoteCredits) {

    array_push(
      $array_return,
      array(
        'type' => 'Nota Credito',
        'consecutive' => $row_NoteCredits['Consecutive'],
        'tercero' => $row_NoteCredits['Name_Client'],
        'total' => $row_NoteCredits['Total_Total'],
        'date' => $row_NoteCredits['Date'],
        'data' => $row_NoteCredits
      )
    );
  }

  // $sql_NoteDebits = mysqli_query($connection, 'SELECT * FROM 	p9_t1_buy WHERE Id_Business = ' . $_SESSION['Id_Business'] . ' AND State = "Active" ');
  // foreach ($sql_NoteDebits as $row_NoteDebits) {

  //   array_push(
  //     $array_return,
  //     array(
  //       'type' => 'Compra',
  //       'consecutive' => $row_NoteDebits['Bill'],
  //       'tercero' => $row_NoteDebits['Provider'],
  //       'total' => $row_NoteDebits['Total_Total'],
  //       'date' => $row_NoteDebits['Date'],
  //       'data' => $row_NoteDebits
  //     )
  //   );
  // }


  array_sort_by($array_return, $OrderBy, SORT_DESC);

  return $array_return;
}


// Info Documentos ------------------------------------------------------------------------------------------
function Info_Documents($Id, $Type)
{
  global $connection, $p8_t1_provider;


  $array_return  = array();

  // $MaxRow = round($MaxRow / 4);
  // die($MaxRow);

  $OrderBy = 'date';

  // $Limit = 'LIMIT '.$MaxRow;

  if ($Type == "Factura") {
    $sql_bills = mysqli_query($connection, 'SELECT * FROM p3_t1_bills_lite WHERE Type != "InviteHouse" AND Type_Bill = "Bill" AND Id_Business = ' . $_SESSION['Id_Business'] . ' AND State = "Active" AND Id = ' . $Id);

    foreach ($sql_bills as $row_bills) {
      array_push(
        $array_return,
        array(
          'type' => 'Factura',
          'consecutive' => $row_bills['Bill'],
          'id' =>  $row_bills['Code_Client'],
          'tercero' => $row_bills['Client'],
          'identity' => $row_bills['Identity'],
          'address' => $row_bills['Address'],
          'email' => $row_bills['Email'],
          'phone' => $row_bills['Phone'],
          'city' => $row_bills['City'],
          'total' => $row_bills['Total_Total'],
          'date' => $row_bills['Date'],
          'data' => $row_bills
        )
      );
    }
  } else if ($Type == "Ingreso") {
    $sql_ingress = mysqli_query($connection, 'SELECT * FROM 	p4_t1_voucher_ingress WHERE Id_Business = ' . $_SESSION['Id_Business'] . ' AND State = "Active" AND Id = ' . $Id);
    foreach ($sql_ingress as $row_ingress) {

      array_push(
        $array_return,
        array(
          'type' => 'Ingreso',
          'consecutive' => $row_ingress['Comprobante'],
          'id' =>  '',
          'tercero' => '',
          'identity' => '',
          'address' => '',
          'email' => '',
          'phone' => '',
          'city' => '',
          'total' => $row_ingress['Total'],
          'date' => $row_ingress['Date'],
          'data' => $row_ingress
        )
      );
    }
  } else if ($Type == "Egreso") {
    // die('SELECT E.*, P.* FROM 	p4_t1_voucher_egress E INNER JOIN '.$p8_t1_provider.' P ON (E.Code_Provider = P.Code) WHERE E.Id_Business = '.$_SESSION['Id_Business'].' AND E.State = "Active" AND E.Id = '.$Id);
    $sql_egress = mysqli_query($connection, 'SELECT E.*, P.* FROM 	p4_t1_voucher_egress E INNER JOIN ' . $p8_t1_provider . ' P ON (E.Code_Provider = P.Code) WHERE E.Id_Business = ' . $_SESSION['Id_Business'] . ' AND E.State = "Active" AND E.Id = ' . $Id);

    foreach ($sql_egress as $row_egress) {
      array_push(
        $array_return,
        array(
          'type' => 'Egreso',
          'consecutive' => $row_egress['Comprobante'],
          'id' =>  $row_egress['Code_Provider'],
          'tercero' => $row_egress['Provider_Name'],
          'identity' => $row_egress['Provider_Identity'],
          'address' => $row_egress['Address'],
          'email' => $row_egress['Email'],
          'phone' => $row_egress['Phone'],
          'city' => $row_egress['City'],
          'total' => $row_egress['Total'],
          'date' => $row_egress['Date'],
          'data' => $row_egress
        )
      );
    }
  } else if ($Type == "Compra") {
    $sql_buys = mysqli_query($connection, 'SELECT * FROM 	p9_t1_buy WHERE Id_Business = ' . $_SESSION['Id_Business'] . ' AND State = "Active" AND Id = ' . $Id);
    foreach ($sql_buys as $row_buys) {

      array_push(
        $array_return,
        array(
          'type' => 'Compra',
          'consecutive' => $row_buys['Bill'],
          'id' =>  $row_buys['Code_Provider'],
          'tercero' => $row_buys['Provider'],
          'identity' => $row_buys['Identity'],
          'address' => $row_buys['Address'],
          'email' => $row_buys['Email'],
          'phone' => $row_buys['Phone'],
          'city' => $row_buys['City'],
          'total' => $row_buys['Total_Total'],
          'date' => $row_buys['Date'],
          'data' => $row_buys
        )
      );
    }
  }
  array_sort_by($array_return, $OrderBy, SORT_DESC);

  return $array_return;
}
// FIN Info Documentos ------------------------------------------------------------------------------------------------------------------

function Save_Signature($Id_Source, $Type, $Img)
{
  global $connection, $p4_t1_vouchers_ingress, $p4_t2_vouchers_egress, $business_address;


  if ($Type == "Ingreso") {
    $table = $p4_t1_vouchers_ingress;
    $Printer = "P4_F2_PreviewIngreso.php";
  } else {
    $table = $p4_t2_vouchers_egress;
    $Printer = "P4_F1_PreviewEgreso.php";
  }

  $query_update = 'UPDATE ' . $table . ' SET Digital_Sign = "' . base64_decode($Img) . '" WHERE Id = ' . $Id_Source;
  $qry_update = mysqli_query($connection, $query_update);
  if ($qry_update) {
    $response = ' <div class="alert alert-success"> Firma guardada correctamente! <br> <b> <a href="' . $business_address . '/modules/Gen_Printer/processes/' . $Printer . '?EgressId=' . $Id_Source . '&Method=PDF"> Ver comprobante </a>  </b>  </div> ';
    $response .= ' <script> $(`.signature_box`).hide() </script>';
    $response .= ' <script> setTimeout(() => { window.close(); }, 4500) </script> ';
  } else {
    $response = ' <div class="alert alert-danger"> Error al guardar firma! </div> ';
    $response .= ' <script> console.log(`' . $query_update . '`) </script> ';
  }

  return $response;
}

function validate_signature($Id_Source, $Type)
{
  global $connection, $p4_t1_vouchers_ingress, $p4_t2_vouchers_egress;


  if ($Type == "Ingreso") {
    $table = $p4_t1_vouchers_ingress;
  } else {
    $table = $p4_t2_vouchers_egress;
  }

  $query_update = 'SELECT Digital_Sign FROM ' . $table . '  WHERE Id = ' . $Id_Source;
  $qry_update = mysqli_query($connection, $query_update);
  $data_update = mysqli_fetch_assoc($qry_update);

  return $data_update['Digital_Sign'];
}

function consult_data_creditNote($Code)
{
  global $connection, $p2_t2_clients_credit_notes, $p2_t2_c1_clients_credit_notes_products, $p3_t1_bills;

  $query_CreditNote = 'SELECT * FROM p2_t2_credit_notes WHERE Id = ' . $Code . '; ';
  $qry_CreditNote = mysqli_query($connection, $query_CreditNote);
  $data_CreditNote = mysqli_fetch_assoc($qry_CreditNote);

  /* ADICIONA PRODCUTOS AFECTADOS EN NOTA CREDITO*/
  $query_NoteProducts = 'SELECT * FROM p2_t2_credit_notes_products WHERE Id_CreditNote = ' . $Code;
  $qry_NoteProducts = mysqli_query($connection, $query_NoteProducts);
  $ArrProducts = array();

  foreach ($qry_NoteProducts as $Array_temp) {
    array_push($ArrProducts, $Array_temp);
  }

  $data_CreditNote['Products'] = $ArrProducts;

  $query_Bill = 'SELECT DocumentID,Bill,Prefix_Bill,Date FROM ' . $p3_t1_bills . ' WHERE Id = ' . $data_CreditNote['Id_Bill'] . '; ';
  $qry_Bill = mysqli_query($connection, $query_Bill);
  $data_Bill = mysqli_fetch_assoc($qry_Bill);

  $data_CreditNote['Data_Bill'] = $data_Bill;

  // die(json_encode($data_CreditNote));
  return $data_CreditNote;
}


function ConsultItems_Note($Code, $MaxItem = 3)
{
  global $connection, $p3_t1_bills_c1_products, $p2_t2_c1_clients_credit_notes_products;
  $CountItems = 0;
  $data = "";
  $Query_Products = mysqli_query($connection, 'SELECT Id_BillP,(SELECT Product FROM ' . $p3_t1_bills_c1_products . ' WHERE Id = Id_BillP) AS Product,Units FROM ' . $p2_t2_c1_clients_credit_notes_products . ' WHERE Id_CreditNote = ' . $Code);

  if (mysqli_num_rows($Query_Products) < 1) {
    return '--';
  }

  foreach ($Query_Products as $row) {
    if ($CountItems <= $MaxItem) {
      $cantidad = $row['Units'];
      $compara = $cantidad - intval($cantidad);
      if ($compara == 0) {
        $cantidad = intval($cantidad);
      }
      $data .= '- ' . $row['Product'] . ' [CANT. ' . $cantidad . ']  <br>';
    } else {
      $data .= '...';
      return $data;
    }
    $CountItems++;
  }
  return $data;
}

function Consult_name_business($Id_Business){
  global $connection, $p0_t1_config_business;

  $results_1 = mysqli_query($connection, "SELECT Name_Business FROM ".$p0_t1_config_business." WHERE Id = ".$Id_Business);
  $row_1 = mysqli_fetch_assoc($results_1);

  return $row_1['Name_Business'];
}







	//header("Content-Type: text/html;charset=utf-8");
//	date_default_timezone_set("America/Bogota");
