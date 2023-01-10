<?php
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    session_start();
    include("./pass.php");
    include("./ini.php");
    include("./history_units.php");

    $connection = connection_db();
    $data = array();
    
    // Params  
    $NumItems = $_POST['NumItems'] == 0 ? '' : ' LIMIT '.$_POST['NumItems'];
    $Keyword = $_POST['Keyword'];

    $sql = 'SELECT
	            INV.*,
	            (SELECT Name FROM '.$p1_t1_inventory_sele_c1_products_groups.' WHERE Code = INV.Code_Group) AS Name_Group
	        FROM '.$p1_t1_inventory_sele.' INV 
            WHERE 
                (SELECT State FROM '.$p1_t1_warehouse_inventory.' WHERE Id_Inventory = INV.Id and Id_Warehouse = 1 ORDER BY FIELD (State,"Active","Erased") LIMIT 1 ) = "Active"
                AND INV.Product LIKE "%'.$Keyword. '%" 
                AND INV.isPublicWeb = 1 '.$NumItems;


    $qry = mysqli_query($connection, $sql);
    foreach ($qry as $product) {
        $product['Units'] = Consult_History_Units($product['Id']);
        array_push($data, $product);
    }
  


    echo json_encode($data);
