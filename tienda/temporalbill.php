<?php


header("Access-Control-Allow-Origin:*");
//header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
//header("Access-Control-Allow-Headers: Content-Type, Authorization");


session_start();
include("./pass.php");
include("./ini.php");
include("./history_units.php");
include("./Functions.php");
$connection = connection_db();

$data = array();
$Total = 0;
//var_dump($_POST);

//var_dump($_POST['products']);
//die();  


// Datos PayU Moringa
$merchantId = '975047';
$API_Key = 'VNR7MrgaIKuQtavUovj9s7Z8AO';
$accountId  = '982977';


// Datos PayU Prueba
/*$merchantId = '508029';
$API_Key = '4Vj8eK4rloUd272L48hsrarnUA';
$accountId  = '512321';
*/


// Validación de datos
$name_client = Validate_Data($_POST["name"]);
$number_document = Validate_Data($_POST["number_document"]);
$phone = Validate_Data($_POST["phone"]);
$email = Validate_Data($_POST["email"]);
$address = Validate_Data($_POST["address"]);
$city = Validate_Data($_POST["city"]);
$products = json_decode($_POST['products']);

$Id_WareHouse = 1;
$Id_Business = 1;
$CUNI_BILL = Consult_CUNI($Id_Business, $p3_t1_bills);

// Datos Prefijo
$Array_Prefix = Consult_Prefix('102010000003', 0, $Id_Business);
$Prefix_billing = $Array_Prefix['Prefix'];
$Resolution = $Array_Prefix['Resolution'];
$NumeroDeFactura = $Array_Prefix['Consecutive'];
$Id_Prefix = $Array_Prefix['Id'];



$Query_Client = "SELECT * FROM " . $p2_t1_clients . " WHERE Identity='" . $number_document . "'";
$Qry_Client = mysqli_query($connection, $Query_Client);

if(mysqli_num_rows($Qry_Client) > 0){
  $Rows_Client = mysqli_fetch_assoc($Qry_Client);
  $Code_Client = $Rows_Client['Id'];
}else{
  /**
   *  Inserta cliente 
   */
  $sql = "INSERT INTO $p2_t1_clients 
  (Id_Business,
  Name,
  Identity,
  Email,
  Phone,
  Address,
  City,
  Date_Register
  ) VALUES 
  (
    $Id_Business,
    '$name_client',
    '$number_document',
    '$email',
    '$phone',
    '$address',
    '$city',
    ".date('U')."
  )";
  $result = mysqli_query($connection, $sql);
  $Code_Client = mysqli_insert_id($connection);
}



$ConsultAutoIncrement = mysqli_query($connection, "SHOW TABLE STATUS LIKE '001_droi_p3_t1_bills'");
$rowAuto = mysqli_fetch_assoc($ConsultAutoIncrement);
$Id_Bill = $rowAuto['Auto_increment'];


$Insert_Bill = '
      INSERT INTO ' . $p3_t1_bills . '
        (
          CUNI,
          Prefix_Bill,
          Id_Business,
          Id_Warehouse,
          Bill,
          Code_Client,
          Client,
          Identity,
          Phone,
          Address,
          City,
          Email,
          Paid,
          Comment_Bill,
          Id_Marker,
          Date,
          User_Code,
          Comment_Internal,
          Last_Update,
          Type,
          state,
          Comment_Cancel
        )
      VALUES
        (
          "' . $CUNI_BILL . '",
          "' . $Prefix_billing . '",
          ' . $Id_Business . ',
          ' . $Id_WareHouse . ',
          "' . $NumeroDeFactura . '",
          "' . $Code_Client . '",
          "' . $name_client . '",
          "' . $number_document . '",
          "' . $phone . '",
          "' . $address . '",
          "' . $city . '",
          "' . $email . '",
          "y",
          "<h2>Dirección de envio: <br>
            Nombre cliente.: ' . $name_client . ', <br>
            Documento cliente.: ' . $number_document . ', <br>
            Dirección: ' . $address . ', <br>
            Ciudad: ' . $city . ', <br>
            Telefono: ' . $phone . ' </h2>
          ",
          1,
          ' . date('U') . ',
          102010000003,
          "Factura realizada por medio de la pagina web",
          ' . date('U') . ',
          "Counted",
          "Erased",
          "Factura sin finalizar pago"
        ) ';


$Qry_Bill = mysqli_query($connection, $Insert_Bill);


if ($Qry_Bill) {
  foreach ($products as $product) {
    if (isset($product->{'Id'})) {

      $Product_Id = $product->{'Id'};
      $Product_Name = $product->{'Product'};
      $Product_Price = ($product->{'Price_Distributor'} > 0) ? $product->{'Price_Distributor'} : $product->{'Price'};
      $Product_iva = $product->{'Iva'};
      $Product_Impoconsumo = $product->{'ipoconsumo'};
      $Product_Units = $product->{'Cant'};

      $ConsultAutoIncrement = mysqli_query($connection, "SHOW TABLE STATUS LIKE '" . $p3_t1_bills_c1_products . "'");
      $next_product = mysqli_fetch_assoc($ConsultAutoIncrement);

      $Query = '
            INSERT INTO ' . $p3_t1_bills_c1_products . '
              (Id_Warehouse, Id_Bill, Code_Product, Product, Price, Units, Porcentaje, Porcetage_Impo, Date_Petition)
            VALUES
              (' . $Id_WareHouse . ', ' . $Id_Bill . ', ' . $Product_Id . ', "' . $Product_Name . '", ' . $Product_Price . ', ' . $Product_Units . ',' . $Product_iva . ' , ' . $Product_Impoconsumo . ', ' . date('U') . ')';

      $Petition = mysqli_query($connection, $Query);
      if (!$Petition) {
        echo 'Error INSERT en tabla -> ' . $p3_t1_bills_c1_products . ' -> ' . mysqli_error($connection) . '<br />' . $Query;
        die();
      }

      $Total += $Product_Price * $Product_Units;
    }
  }
  
  
  $UpdateNumberBill = mysqli_query($connection, 'UPDATE ' . $p0_t1_config_c2_list_prefix . ' SET Consecutive = Consecutive+1  WHERE Id=' . $Id_Prefix);

  $data = array(
    'code' => 1,
    'state' => '
              <div class="row">
        <div class="m-auto text-center">
          <img src="img/buy.png" alt="" style="width: 20%" >
          <h3>Gracias por realizar la compra</h3>
        </div>
        <br>
        <div class="text-center col-12">
          <a class="btn_Payu" onclick="$(\'#F2_Validate_Pay_Online\').submit();finised_buy_refresh()"><img src="https://ecommerce.payulatam.com/img-secure-2015/boton_pagar_grande.png"></a>
        </div>

        <div class="col-md-12 col-xs-12" style="margin-top: 20px">
          <img src="img/logos_pay.png" alt="" style="width: 100%">
        </div>
        <form id="F2_Validate_Pay_Online" method="post" action="https://checkout.payulatam.com/ppp-web-gateway-payu/" target="_blank">
          <input name="merchantId"      type="hidden"  value="' . $merchantId . '"   >
          <input name="accountId"       type="hidden"  value="' . $accountId . '" >
          <input name="description"     type="hidden"  value="Compra tienda virtual Moringa">
          <input name="referenceCode"   type="hidden"  value="' . $Id_Bill . '" >
          <input name="amount"          type="hidden"  value="' . intval($Total) . '"   >
          <input name="tax"             type="hidden"  value="0"  >
          <input name="taxReturnBase"   type="hidden"  value="0" >
          <input name="currency"        type="hidden"  value="COP" >
          <input name="signature"       type="hidden"  value="' . md5('' . $API_Key . '~' . $merchantId . '~' . $Id_Bill . '~' . intval($Total) . '~COP') . '"  >
          <input name="test"            type="hidden"  value="1" >
          <input name="buyerFullName"   type="hidden"  value="' . $name_client . '">
          <input name="buyerEmail"      type="hidden"  value="' . $email . '" >
          <input name="telephone"       type="hidden"  value="' . $phone . '" >
          <input name="billingCity"     type="hidden"  value="' . $city . '" >
          <input name="billingAddress"  type="hidden"  value="' . $address . '" >
          <input name="responseUrl"     type="hidden"  value="https://gesadmin.com.co/ges/moringaTest/gesadmin/config/tienda/ResponsePayU.php" >
          <input name="confirmationUrl" type="hidden"  value="https://gesadmin.com.co/ges/moringaTest/gesadmin/config/tienda/ConfirmationPayU.php" >
        </form>

    </div>'
  );
  
}else{
  
    $data = array(
    'code' => 0,
    'state' => 'Error al realizar la factura.'
  );
  
}

echo json_encode($data);

?>