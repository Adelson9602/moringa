<?php 
header('Access-Control-Allow-Origin: *');

session_start();
include("./pass.php");
include("./ini.php");
include("./history_units.php");
include("./Functions.php");

?>

<link rel="stylesheet" href="http://www.iziyapp.com/App/MovilProcesses/system/resources/css/Style-Dynamic.css?37.0">
<link href="http://www.iziyapp.com/App/MovilProcesses/resources/css/commons.css?37.0" rel="stylesheet" type="text/css">
<script type="text/javascript" charset="utf-8"
    src="http://www.iziyapp.com/App/MovilProcesses/resources/js/jquery.js?37.0"></script>
<!--<script src="http://www.iziyapp.com/App/MovilProcesses/system/resources/js/main_response.js?37.0"></script>-->
<!--<script src="http://www.iziyapp.com/App/MovilProcesses/system/resources/js/md5.js?37.0"></script>-->
<script type="text/javascript" charset="utf-8"
    src="http://www.iziyapp.com/App/MovilProcesses/resources/js/bootstrap.js?37.0"></script>
<meta charset="utf-8">

<?php
	//$ApiKey = "4Vj8eK4rloUd272L48hsrarnUA";//APIKEY DE PRUEBA
   	$ApiKey = "VNR7MrgaIKuQtavUovj9s7Z8AO"; //APIKEY DE MORINGA
	$merchant_id = $_REQUEST['merchantId'];
	$referenceCode = $_REQUEST['referenceCode'];
	$TX_VALUE = $_REQUEST['TX_VALUE'];
	$New_value = number_format($TX_VALUE, 1, '.', '');
	$currency = $_REQUEST['currency'];
	$transactionState = $_REQUEST['transactionState'];
	$reference_pol = $_REQUEST['reference_pol'];
	$cus = $_REQUEST['cus'];
	$extra1 = $_REQUEST['description'];
	$pseBank = $_REQUEST['pseBank'];
	$lapPaymentMethod = $_REQUEST['lapPaymentMethod'];
	$transactionId = $_REQUEST['transactionId'];
	
// 	die($_REQUEST['transactionState']);

	if ($_REQUEST['transactionState'] == 4 ) {
	    $estadoTx = "Transacción aprobada";
	}else if ($_REQUEST['transactionState'] == 6 ) {
		$estadoTx = "Transacción rechazada11";
	}else if ($_REQUEST['transactionState'] == 104 ) {
		$estadoTx = "Error";
	}else if ($_REQUEST['transactionState'] == 7 ) {
		//$estadoTx = "Transacción pendiente";
		$estadoTx = "Transacción rechazada";
	}else {
		$estadoTx=$_REQUEST['mensaje'];
	}

?>
<div style="display: flex; justify-content: center;">
    <div id="Tab_Choose_City"
        style="border: 2px solid black; width: 52%; border-radius: 15px; display: flex; flex-direction: column; align-items: center;">
        <div class="Content_Chosee_City" style="width: 98%;">
            <div id="Response" style="width: 100%;">

                <div style="text-align: center">
                    <h1 style="font-size: 55px;">TIENDA MORINGA</h1>
                </div>
                <h2 style="font-size: 51px;margin-bottom: 23px; text-align:center;">Resumen Transacción</h2>
                <div class="Text_Center_White" style="font-size: 50px; text-align:center;font-weight: bolder;">
                    <?php echo $estadoTx; ?> </div>
                <table class="table table-hover table-bordered"
                    style="width: 100%;text-align:  center;background-color: white;font-size: 55px;height:  516px;">
                    <tr>
                        <td class="TxtTopProfile" style="font-size : 40px;">ID de la transaccion</td>
                    </tr>
                    <tr>
                        <td><?php  echo $transactionId;?> </td>
                    </tr>
                    <tr>
                        <td class="TxtTopProfile" style="font-size : 40px;">Referencia de la venta</td>
                    </tr>
                    <tr>
                        <td><?php echo $reference_pol; ?></td>
                    </tr>
                    <tr>
                        <td class="TxtTopProfile" style="font-size : 40px;">Referencia de la transaccion</td>
                    </tr>
                    <tr>
                        <td><?php  echo $referenceCode; ?></td>
                    </tr>
                    <tr>
                        <?php if($pseBank != null) { ?>
                    <tr>
                        <td class="TxtTopProfile" style="font-size : 40px;">cus </td>
                    </tr>
                    <tr>
                        <td><?php  echo $cus; ?></td>
                    </tr>
                    <tr>
                        <td class="TxtTopProfile" style="font-size : 40px;">Banco </td>
                    </tr>
                    <tr>
                        <td><?php  echo $pseBank; ?> </td>
                    </tr>

                    <?php } ?>

                    <tr>
                        <td class="TxtTopProfile" style="font-size : 40px;">Valor total</td>
                    </tr>
                    <tr>
                        <td> $ <?php echo number_format($TX_VALUE); ?></td>
                    </tr>
                    <tr>
                        <td class="TxtTopProfile" style="font-size : 40px;">Moneda</td>
                    </tr>
                    <tr>
                        <td> <?php echo $currency;?> </td>
                    </tr>
                    <tr>
                        <td class="TxtTopProfile" style="font-size : 40px;">Descripción</td>
                    </tr>
                    <tr>
                        <td><?php echo ($extra1); ?></td>
                    </tr>
                    <tr>
                        <td class="TxtTopProfile" style="font-size : 40px;">Entidad:</td>
                    </tr>
                    <tr>
                        <td><?php echo ($lapPaymentMethod); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="ContentCenter">
            <!--<div class="Text_Center_White" style="font-size: 40px;"> No ha sido posible recibir tu pagoddd</div>-->
            <button class="BtnContinue" onclick="CloseWindow();"
                style="border-radius: 6px;font-size: 48px;padding: 21px 46px;margin: 15px auto 50px;">Cerrar</button>
        </div>
    </div>
</div>
<script type="text/javascript">
function CloseWindow() {
    window.close();
}
</script>