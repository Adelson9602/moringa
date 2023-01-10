<?php
session_start();
include("./pass.php");
include("./ini.php");
include("./history_units.php");
include("./Functions.php");

$connection = connection_db();

$data = array();
//$date_bill = array();
$Total = 0;
    //var_dump($_POST);
     //var_dump($_POST['products']);
    //die();  
/**
 * 
 * Validación de datos
 *
 */
//https://jumbocolombiaio.vtexassets.com/arquivos/ids/186379-800-600?v=637813982015970000&width=800&height=600&aspect=true
  $name_client = Validate_Data($_POST["name"]);
  $number_document = Validate_Data($_POST["number_document"]);
  $phone = Validate_Data($_POST["phone"]);
  $email = Validate_Data($_POST["email"]);
  $address = Validate_Data($_POST["address"]);
  $city = Validate_Data($_POST["city"]);

/**
 * 
 * Fin Validación de datos
 *
 */

// Datos PayU Moringa
/* $merchantId = '961230';
$API_Key = 'oMA15W0AuppNA8a0TLB2WzeG1c';
$accountId  = '968965'; */

// Datps PayU Prueba
// $merchantId = '508029';
// $API_Key = '4Vj8eK4rloUd272L48hsrarnUA';
// $accountId  = '512321';

$products = json_decode($_POST['products']);
// var_dump($Data_Address);
// die();
$Id_WareHouse = 1;
$Id_Business = 1;
$CUNI_BILL = Consult_CUNI($Id_Business, $p3_t1_bills);

// Datos Prefijo
$Array_Prefix = Consult_Prefix('102010000003', 0, $Id_Business);
$Prefix_billing = $Array_Prefix['Prefix'];
$Resolution = $Array_Prefix['Resolution'];
$NumeroDeFactura = $Array_Prefix['Consecutive'];
$Id_Prefix = $Array_Prefix['Id'];
/* 
    // Datos Cliente
    $Name_Client = $Data_Client[0]->value;
    $Identity_Client = $Data_Client[4]->value;
    $Phone_Client = $Data_Client[5]->value;
    $Address_Client = $Data_Client[6]->value;
    $City_Client = $Data_Client[7]->value;
    $Email_Client = $Data_Client[1]->value;

    // Dirección de envio
    $Name_Address = $Data_Address[3]->value;
    $Address_Delivery = $Data_Address[4]->value;
    $Code_Postal = $Data_Address[5]->value;
    $City_Address = $Data_Address[6]->value;
    $Departament_Address = $Data_Address[7]->value;
    $Phone_Address = $Data_Address[8]->value;
 */
// Consulta el codigo del cliente
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
// die($Insert_Bill);

$Qry_Bill = mysqli_query($connection, $Insert_Bill);

if ($Qry_Bill) {
  // if (1 == 1) {
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

  //   Checked_Bill_History($CUNI_BILL);
  $UpdateNumberBill = mysqli_query($connection, 'UPDATE ' . $p0_t1_config_c2_list_prefix . ' SET Consecutive = Consecutive+1  WHERE Id=' . $Id_Prefix);

  $data = array(
    'code' => 1,
    'state' => '
        <div class="row">
            <div class="m-auto text-center">
              <img src="img/buy.png" alt="" style="width: 20%" >
              <h3>Gracias por realizar la compra</h3>
            </div>

            <div class="text-center col-12">
              <button type="button" name="button" class="btn btn_Payu" onclick="$(\'#F2_Validate_Pay_Online\').submit();finised_buy_refresh()"> Pagar por PayU</button>
            </div>

            <div class="col-md-12 col-xs-12" style="margin-top: 20px">
              <img src="img/logos_pay.png" alt="" style="width: 100%">
            </div>

            <form method="post" action="https://sandbox.checkout.payulatam.com/ppp-web-gateway-payu/">
              <input name="merchantId"      type="hidden"  value="508029"   >
              <input name="accountId"       type="hidden"  value="512321" >
              <input name="description"     type="hidden"  value="Test PAYU"  >
              <input name="referenceCode"   type="hidden"  value="TestPayU" >
              <input name="amount"          type="hidden"  value="20000"   >
              <input name="tax"             type="hidden"  value="3193"  >
              <input name="taxReturnBase"   type="hidden"  value="16806" >
              <input name="currency"        type="hidden"  value="COP" >
              <input name="signature"       type="hidden"  value="7ee7cf808ce6a39b17481c54f2c57acc"  >
              <input name="test"            type="hidden"  value="0" >
              <input name="buyerEmail"      type="hidden"  value="test@test.com" >
              <input name="responseUrl"     type="hidden"  value="http://www.test.com/response" >
              <input name="confirmationUrl" type="hidden"  value="http://www.test.com/confirmation" >
              <input name="Submit"          type="submit"  value="Enviar" >
            </form>

        </div>'
  );

  /**
   * Envia la factura por whatsapp
   */
/*   include("../GenerateJson.php");
  $jsonBill = generateJsonPdf($Id_Bill,$phone);
  echo '

  <script>
  const sendDataServer = (jsonBill) => {
    $.ajax({
      method: \'POST\',
      url: \'https://droi-server.org/bill_generator/billGenerator.php\',
      data: \'data=\' + JSON.stringify(jsonBill),
      success: function (response) {
        console.log(response);
      },
      error: function (xhr, status) {
        console.log(xhr);
        console.log(status);
      },
    });
  };
  sendDataServer('.$jsonBill.')
  //window.close()
  </script>
  
  ';
 */


  // 
  // PRUEBA SANDBOX
  // https://sandbox.checkout.payulatam.com/ppp-web-gateway-payu/
  //$data['status'] = $status;
} else {
  $data = array(
    'code' => 0,
    'state' => 'Error al realizar la factura.'
  );

  //$data['status'] = $status;
}

// ---------------------------------------------------------------------------

echo json_encode($data);