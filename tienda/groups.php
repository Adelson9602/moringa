<?php

header("Access-Control-Allow-Origin:*");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Access-Control-Allow-Headers: Content-Type, Authorization");


    session_start();
    include("./pass.php");
    include("./ini.php");

    $connection = connection_db();
    $data = array();

    $sql = 'SELECT 
                Code,
                Name,
                Image
            FROM '.$p1_t1_inventory_sele_c1_products_groups.'
                WHERE Id_Business = 1 AND isPublicWeb = 1
                ORDER BY Name';
    $qry = mysqli_query($connection, $sql);
    $data = mysqli_fetch_all($qry , MYSQLI_ASSOC);

    echo json_encode($data);
















?>