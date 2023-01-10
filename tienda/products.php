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
    $Groups = json_decode($_POST['Groups']);  
    $NumItems = $_POST['NumItems'] == 0 ? '' : ' LIMIT '.$_POST['NumItems'];

    $WhereGroup = $Groups != [] ?  ' AND Code_Group IN (': '';

    foreach ($Groups as $GroupItem) {
        $WhereGroup .= $GroupItem.' ,';
    }

    if($Groups != []){
        $WhereGroup = substr($WhereGroup, 0, -1).' )';
    }

    $sql = 'SELECT INV.*, g.Code AS Name_Group,
        (SELECT (SELECT COALESCE(SUM(Unit),0) FROM 001_droi_p1_t1_inventory_sale_c2_products_history_units WHERE Code_Item = INV.Id AND Type = "Add") - (SELECT COALESCE(SUM(Unit),0) FROM 001_droi_p1_t1_inventory_sale_c2_products_history_units WHERE Code_Item = INV.Id AND Type = "Remove") AS cantidad) AS cantidad
        FROM 001_droi_p1_t1_inventory_sele INV
        JOIN 001_droi_p1_t1_inventory_sele_c1_products_groups g ON g.Code = INV.Code_Group
        JOIN 001_droi_p1_t1_warehouse_inventory i ON i.Id_Inventory = INV.Id 
    WHERE i.State = "Active"  AND INV.isPublicWeb = 1'.$WhereGroup.' '.$NumItems;

    $qry = mysqli_query($connection, $sql);

    foreach($qry AS $product){
        $product['Units'] = Consult_History_Units($product['Id']); 
        array_push($data, $product);
    }
  

    echo json_encode($data);
