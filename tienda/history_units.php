<?php

  function consult_unists_close($IdClose, $IdProduct, $InventorySale=true){
    global $connection, $p1_t1_inventory_sele_massive_history, $p1_t1_inventory_sele_massive_history_products, $p1_t2_inventory_production_massive_history, $p1_t2_inventory_production_massive_history_products	;

    if(!$InventorySale){
      $p1_t1_inventory_sele_massive_history = $p1_t2_inventory_production_massive_history;
      $p1_t1_inventory_sele_massive_history_products = $p1_t2_inventory_production_massive_history_products;
    }

    $query = '
    SELECT PD.Units, PD.Type FROM '.$p1_t1_inventory_sele_massive_history.' CL
      INNER JOIN '.$p1_t1_inventory_sele_massive_history_products.' PD ON PD.Id_Massive=CL.Id
    WHERE CL.Id = '.$IdClose.' AND PD.Id_Product='.$IdProduct.' ';
    $result_1 = mysqli_query($connection, $query);
    $row_1 = mysqli_fetch_assoc($result_1);
    $Unit = empty($row_1['Units'])?0:$row_1['Units'];

    if($row_1['Type']=="Remove"){
      $Unit = abs($Unit)*-1;
    }

    return $Unit;
  }


  function Consult_History_UnitsOld($Code, $Date_End = '', $Date_In = '', $CloseCode="x", $Batch = ''){
    global $connection, $p1_t1_inventory_sale_c2_products_history_units, $p1_t1_inventory_sele_massive_history;

    // DECLARACION  Dos variables de concatenacion and y and2 para codigo sql
    $And = '';
    $And2 = '';
    $UnitsInClose = 0;

    //Si se le envia una fecha Max entonces va definirla tanto para Historico de undiades como para buscar el primer  cierre de mes anterior a esa fecha
    if (!empty($Date_End)) {
      $And = ' AND Date < '.$Date_End;
      $And2 = ' AND Date_Realiced < '.$Date_End;
    }

    // Consulta si existe un cierre de mes anterior a la fecha enviada, o en su defecto el cierre más reciente
    //   $DateClose = 0;
    //   $query = 'SELECT * FROM '.$p1_t1_inventory_sele_massive_history.' WHERE Close = 1 AND Id_Business='.$_SESSION['Id_Business'].' '.$And2.' ORDER BY Date_Realiced DESC LIMIT 1 ';
    // // die ('SELECT * FROM '.$p1_t1_inventory_sele_massive_history.' WHERE Close = 1 AND Id_Business='.$_SESSION['Id_Business'].' '.$And2.' ORDER BY Date_Realiced DESC LIMIT 1 ');
    //   $result_1 = mysqli_query($connection,$query);
    //   while($row_1 = @mysqli_fetch_assoc($result_1)){
    //     $DateClose = $row_1['Date_Realiced'];
    //     $CUNIClose = $row_1['CUNI'];
    //     $IdClose = $row_1['Id'];
    //   }


    if (!empty($DateClose) && $CloseCode!=$CUNIClose) { // Si existe un cierre de mes, cuyo cierre no sea el mismo que esta en proceso de creacion
      $UnitsInClose = consult_unists_close($IdClose, $Code); // Consulteme las undiades de ese cierre y tengas pendiente para sumarlas a los enventos de add y remove de histori
      if(!empty($Date_End)){
        $And = ' AND Method!="Close" AND Date BETWEEN '.$DateClose.' AND '.$Date_End;  // Se escluye los eventos close en histori de units y comienza contar despues del cierre
      } else {
        $And = ' AND Method!="Close" AND Date >= '.$DateClose;
      }
    }

    // si hay un lote asignado, solo consultamos la unidades en él
    $where_batch = ($Batch == '')? '': 'AND Id_Batch = '.$Batch;

    $Sql = '
    SELECT SUM(Unit) AS SUMAdd ,
        (SELECT SUM(Unit) FROM '.$p1_t1_inventory_sale_c2_products_history_units.' WHERE Id_Warehouse= 1 AND Code_Item ='.$Code.' AND Type = "Remove" '.$And.' '.$where_batch.' ORDER BY Date ASC) AS SUMRemove
      FROM '.$p1_t1_inventory_sale_c2_products_history_units.'
    WHERE
      Id_Warehouse=1 AND
      Code_Item ='.$Code.' AND
      Type = "Add" '.$And.' '.$where_batch.' ORDER BY Date ASC';

    //  die($Sql);
    $result_1 = mysqli_query($connection,$Sql);
    $row_1 = mysqli_fetch_assoc($result_1);

    //captuda de datos
    $SUMAdd = (empty($row_1['SUMAdd']))?0:$row_1['SUMAdd'];
    $SUMRemove = (empty($row_1['SUMRemove']))?0:$row_1['SUMRemove'];
    //cruce de unidades agregadas con las removidas
    $SUMTotal = $SUMAdd-$SUMRemove+$UnitsInClose;

    return $SUMTotal;
  }

  function Consult_History_Units($Code, $Date_End = '', $Date_In = '', $CloseCode="x", $Batch = ''){
    global $connection,$p1_t0_initial,$p1_t0_initial_product, $p1_t1_inventory_sale_c2_products_history_units, $p1_t1_inventory_sele_massive_history;

    $Debug = false;
    // $Debug = true;

    // DECLARACION  Dos variables de concatenacion and y and2 para codigo sql
    $And = '';
    $And2 = '';
    $UnitsInClose = 0;
    $Units_I = 0;

    if($Debug) echo 'PROD ID:'.$Code.' '.((!empty($Date_End))? 'DATE: '.date('Y-m-d H:i:s', $Date_End):'').' <br>';


    //BUSCA CORTE DE MES
    $SqlInitital = 'SELECT * FROM '.$p1_t0_initial.' WHERE Id_Warehouse = 1  '.((!empty($Date_End)) ? ' AND Date <= '.$Date_End : '' ).' ORDER BY Date DESC LIMIT 1';
    $result_I = mysqli_query($connection,$SqlInitital);

    if(mysqli_num_rows($result_I)>0){
      $row_I = mysqli_fetch_assoc($result_I);
      $sqlIP = 'SELECT COALESCE(SUM(Units),0) AS SUMUnits FROM '.$p1_t0_initial_product.' WHERE Id_Initial='.$row_I['Id']. ' AND Id_Warehouse=1 AND Id_Product= '.$Code;
    // die($sqlIP);
      $qry_IP= mysqli_query($connection , $sqlIP );
      $row_IP = mysqli_fetch_assoc($qry_IP);

      if(mysqli_num_rows($qry_IP)>0){
        $Units_I = $row_IP['SUMUnits'];
        $And = ' AND Date > '.$row_I['Date'];
      }
      if($Debug) echo 'INITIAL ID:'.$row_I['Id'].'> DATE:'.date('Y-m-d H:i:s',$row_I['Date']).' > UNIT:'.$Units_I.'<br>';
    }


    //Si se le envia una fecha Max entonces va definirla tanto para Historico de undiades como para buscar el primer  cierre de mes anterior a esa fecha
    if (!empty($Date_End)) {
      $And = ' AND Date < '.$Date_End;
      $And2 = ' AND Date_Realiced < '.$Date_End;
    }

    // Consulta si existe un cierre de mes anterior a la fecha enviada, o en su defecto el cierre más reciente
    //   $DateClose = 0;
    //   $query = 'SELECT * FROM '.$p1_t1_inventory_sele_massive_history.' WHERE Close = 1 AND Id_Business='.$_SESSION['Id_Business'].' '.$And2.' ORDER BY Date_Realiced DESC LIMIT 1 ';
    // // die ('SELECT * FROM '.$p1_t1_inventory_sele_massive_history.' WHERE Close = 1 AND Id_Business='.$_SESSION['Id_Business'].' '.$And2.' ORDER BY Date_Realiced DESC LIMIT 1 ');
    //   $result_1 = mysqli_query($connection,$query);
    //   while($row_1 = @mysqli_fetch_assoc($result_1)){
    //     $DateClose = $row_1['Date_Realiced'];
    //     $CUNIClose = $row_1['CUNI'];
    //     $IdClose = $row_1['Id'];
    //   }


    // if (!empty($DateClose) && $CloseCode!=$CUNIClose) { // Si existe un cierre de mes, cuyo cierre no sea el mismo que esta en proceso de creacion
    //   $UnitsInClose = consult_unists_close($IdClose, $Code); // Consulteme las undiades de ese cierre y tengas pendiente para sumarlas a los enventos de add y remove de histori
    //   if(!empty($Date_End)){
    //     $And = ' AND Method!="Close" AND Date BETWEEN '.$DateClose.' AND '.$Date_End;  // Se escluye los eventos close en histori de units y comienza contar despues del cierre
    //   } else {
    //     $And = ' AND Method!="Close" AND Date >= '.$DateClose;
    //   }
    // }

    // si hay un lote asignado, solo consultamos la unidades en él
    $where_batch = ($Batch == '')? '': 'AND Id_Batch = '.$Batch;

    $Sql = '
    SELECT COALESCE(SUM(Unit),0) AS SUMAdd ,
        (SELECT COALESCE(SUM(Unit),0) FROM '.$p1_t1_inventory_sale_c2_products_history_units.' WHERE Id_Warehouse= 1 AND Code_Item ='.$Code.' AND Type = "Remove" '.$And.' '.$where_batch.' ORDER BY Date ASC) AS SUMRemove
      FROM '.$p1_t1_inventory_sale_c2_products_history_units.'
    WHERE
      Id_Warehouse=1 AND
      Code_Item ='.$Code.' AND
      Type = "Add" '.$And.' '.$where_batch.' ORDER BY Date ASC';

    //die($Sql);
    $result_1 = mysqli_query($connection,$Sql);
    $row_1 = mysqli_fetch_assoc($result_1);

    //captuda de datos
    $SUMAdd = (empty($row_1['SUMAdd']))?0:$row_1['SUMAdd'];
    $SUMRemove = (empty($row_1['SUMRemove']))?0:$row_1['SUMRemove'];

    if($Debug) echo 'HISTORY ADD:'.$row_1['SUMAdd'].'> REM:'.$row_1['SUMRemove'].'<br>';

    //cruce de unidades agregadas con las removidas
    $SUMTotal = $SUMAdd-$SUMRemove+$UnitsInClose;

    return $Units_I+$SUMTotal;
  }

  function Consult_History_Units_Production($Code, $Date_End = '', $Date_In = '', $CloseCode="x", $Batch = ''){
    global $connection, $p1_t2_inventory_production_c2_products_history_units, $p1_t2_inventory_production_massive_history;

    // DECLARACION  Dos variables de concatenacion and y and2 para codigo sql
    $And = '';
    $And2 = '';
    $UnitsInClose = 0;

    //Si se le envia una fecha Max entonces va definirla tanto para Historico de undiades como para buscar el primer  cierre de mes anterior a esa fecha
    if (!empty($Date_End)) {
      $And = ' AND Date < '.$Date_End;
      $And2 = ' AND Date_Realiced < '.$Date_End;
    }


    // Consulta si existe un cierre de mes anterior a la fecha enviada, o en su defecto el cierre más reciente
      // $DateClose = 0;
      // $query = 'SELECT * FROM '.$p1_t2_inventory_production_massive_history.' WHERE Close = 1 AND Id_Business='.$_SESSION['Id_Business'].' '.$And2.' ORDER BY Date_Realiced DESC LIMIT 1 ';
      // $result_1 = mysqli_query($connection,$query);
      // while($row_1 = mysqli_fetch_assoc($result_1)){
      //   $DateClose = $row_1['Date_Realiced'];
      //   $CUNIClose = $row_1['CUNI'];
      //   $IdClose = $row_1['Id'];
      // }


      // if (!empty($DateClose) && $CloseCode!=$CUNIClose) { // Si existe un cierre de mes, cuyo cierre no sea el mismo que esta en proceso de creacion
      //   $UnitsInClose = consult_unists_close($IdClose, $Code, false); // Consulteme las undiades de ese cierre y tengas pendiente para sumarlas a los enventos de add y remove de histori
      //   if(!empty($Date_End)){
      //     $And = ' AND Method!="Close" AND Date BETWEEN '.$DateClose.' AND '.$Date_End;  // Se escluye los eventos close en histori de units y comienza contar despues del cierre
      //   } else {
      //     $And = ' AND Method!="Close" AND Date >= '.$DateClose;
      //   }
      // }

      // die('
      // SELECT SUM(Unit) AS SUMAdd ,
      //     (SELECT SUM(Unit) FROM '.$p1_t2_inventory_production_c2_products_history_units.' WHERE Id_Warehouse='.$_SESSION['Id_Warehouse'].' AND Code_Item ='.$Code.' AND Type = "Remove" '.$And.' ORDER BY Date ASC) AS SUMRemove
      //   FROM '.$p1_t2_inventory_production_c2_products_history_units.'
      // WHERE
      //   Id_Warehouse='.$_SESSION['Id_Warehouse'].' AND
      //   Code_Item ='.$Code.' AND
      //   Type = "Add" '.$And.'  ORDER BY Date ASC');
     
    $where_batch = ($Batch == '')? '': 'AND Id_Batch = '.$Batch;
    

    //Preceso normal de suma de eventos

    $Query_1 = ' SELECT SUM(Unit) AS SUMAdd ,
    (SELECT SUM(Unit) FROM '.$p1_t2_inventory_production_c2_products_history_units.' WHERE Id_Warehouse=1 AND Code_Item ='.$Code.' AND Type = "Remove" '.$And.' '.$where_batch.' ORDER BY Date ASC) AS SUMRemove
    FROM '.$p1_t2_inventory_production_c2_products_history_units.'
    WHERE
    Id_Warehouse=1 AND
    Code_Item ='.$Code.' AND
    Type = "Add" '.$And.' '.$where_batch.'  ORDER BY Date ASC';
    // die($Query_1);

    $result_1 = mysqli_query($connection, $Query_1);

    $row_1 = mysqli_fetch_assoc($result_1);

    $SUMAdd = (empty($row_1['SUMAdd']))?0:$row_1['SUMAdd'];
    $SUMRemove = (empty($row_1['SUMRemove']))?0:$row_1['SUMRemove'];

    $SUMTotal = $SUMAdd-$SUMRemove;
    // die($SUMTotal);

    return $SUMTotal;
  }

  function Consult_History_Units_Aside($Code, $Date_End = '', $Date_In = '', $CloseCode="x"){
    global $connection, $p1_t1_inventory_sale_c2_products_history_units, $p1_t1_inventory_sele_massive_history;

    // DECLARACION  Dos variables de concatenacion and y and2 para codigo sql
    $And = '';
    $And2 = '';
    $UnitsInClose = 0;

    //Si se le envia una fecha Max entonces va definirla tanto para Historico de undiades como para buscar el primer  cierre de mes anterior a esa fecha
    if (!empty($Date_End)) {
      $And = ' AND Date < '.$Date_End;
      $And2 = ' AND Date_Realiced < '.$Date_End;
    }


    // Consulta si existe un cierre de mes anterior a la fecha enviada, o en su defecto el cierre más reciente
      $DateClose = 0;
      $query = 'SELECT * FROM '.$p1_t1_inventory_sele_massive_history.' WHERE Close = 1 AND Id_Business=1 '.$And2.' ORDER BY Date_Realiced DESC LIMIT 1 ';
      $result_1 = mysqli_query($connection,$query);
      while($row_1 = mysqli_fetch_assoc($result_1)){
        $DateClose = $row_1['Date_Realiced'];
        $CUNIClose = $row_1['CUNI'];
        $IdClose = $row_1['Id'];
      }


      if (!empty($DateClose) && $CloseCode!=$CUNIClose) { // Si existe un cierre de mes, cuyo cierre no sea el mismo que esta en proceso de creacion
        $UnitsInClose = consult_unists_close($IdClose, $Code, false); // Consulteme las undiades de ese cierre y tengas pendiente para sumarlas a los enventos de add y remove de histori
        if(!empty($Date_End)){
          $And = ' AND Method!="Close" AND Date BETWEEN '.$DateClose.' AND '.$Date_End;  // Se escluye los eventos close en histori de units y comienza contar despues del cierre
        } else {
          $And = ' AND Method!="Close" AND Date >= '.$DateClose;
        }
      }

    //Preceso normal de suma de eventos
    $result_1 = mysqli_query($connection,'
    SELECT SUM(Unit) AS SUMAside
      FROM '.$p1_t1_inventory_sale_c2_products_history_units.'
    WHERE
      Id_Warehouse=1 AND
      Code_Item ='.$Code.' AND
      Type = "Aside" '.$And.'  ORDER BY Date ASC');

    $row_1 = mysqli_fetch_assoc($result_1);

    return $row_1['SUMAside'];
  }

  function Consult_Resume($InventorySale=true, $Lote_Products){
    global $connection, $p1_t1_inventory_sale_c2_products_history_units, $p1_t1_inventory_sele, $p1_t1_warehouse_inventory, $p0_t2_warehouse, $SYS_OPTIMIZAR, $p1_t2_inventory_production, $p1_t1_warehouse_inventory_production;

    if($SYS_OPTIMIZAR>0){
      return "";
    }

    //Por si es de producción
    if(!$InventorySale){
      $p1_t1_inventory_sele = $p1_t2_inventory_production;
      $p1_t1_warehouse_inventory = $p1_t1_warehouse_inventory_production;
    }


    $query = 'SELECT INV.* FROM '.$p1_t1_inventory_sele.' INV
    WHERE

    (SELECT State FROM '.$p1_t1_warehouse_inventory.' WHERE Id_Inventory = INV.Id AND Id_Warehouse = 1 ORDER BY FIELD (State,"Active","Erased") LIMIT 1 ) = "Active"

    AND (Type = "Recurrent" OR Type = "Temporaly") ';

    if ( $Lote_Products == 1 ) {
      $Price_Cost = 0;
      $Price_Sell = 0;
      $Units_Temp = 0;

        $result_1 = mysqli_query($connection, $query);
        if(!$result_1){
          echo mysqli_error($connection)."<br>".$query;
        }

        $num = 0;

        while($row_1 = mysqli_fetch_assoc($result_1)){

          $consult_batch = mysqli_query($connection, "SELECT Id,Name,Price_Suggest,Price_Cost FROM ".$p1_t1_warehouse_inventory." WHERE Id_Inventory = ".$row_1['Id']."  AND Id_Warehouse = 1 AND State = 'Active'");
  		    $Unidades = 0;
  				foreach ($consult_batch as $row_batch ) {
            $num ++;

            if(!$InventorySale){
              $Units_Batch = Consult_History_Units_Production($row_1['Id']);
            }else {
              $Units_Batch = Consult_History_Units($row_1['Id'],'','','x',$row_batch['Id']);
            }

  					$Unidades += $Units_Batch;
  					if($Units_Batch >= 0){
      					$Price_Cost += $row_batch['Price_Cost']*$Units_Batch;
      					$Price_Sell += $row_batch['Price_Suggest']*$Units_Batch;
      					$Units_Temp += $Units_Batch;
  					}

  				}
        }

        $row_1['Total_Units'] = $Units_Temp;
        $row_1['Total_Cost'] = $Price_Cost;
        $row_1['Total_Sale'] = $Price_Sell;


      $array = array(
        'Total_Units' => $row_1['Total_Units'],
        'Total_Cost' => $row_1['Total_Cost'],
        'Total_Sale' => $row_1['Total_Sale'],
        'Total_batch' => $num

      );
    }else{
      //Codigo fredy
        $Total_Units = 0;
        $Total_Cost = 0;
        $Total_Sale = 0;

        $result_1 = mysqli_query($connection, $query);
        if(!$result_1){
          echo mysqli_error($connection)."<br>".$query;
        }

        $num = 0;
        while($row_1 = mysqli_fetch_assoc($result_1)){

          $num ++;
          if(!$InventorySale){
            $Agrega = Consult_History_Units_Production($row_1['Id']);
          }else {
            $Agrega = Consult_History_Units($row_1['Id']);
          }
            if($Agrega>0){
              $Total_Units += $Agrega;
              $Total_Cost += $row_1['Price_Cost']*$Agrega;
              $Total_Sale += $row_1['Price_Sugerido']*$Agrega;
            }
        }
        $row_1['Total_Units'] = $Total_Units;
        $row_1['Total_Cost'] = $Total_Cost;
        $row_1['Total_Sale'] = $Total_Sale;


      $array = array(
        'Total_Units' => $row_1['Total_Units'],
        'Total_Cost' => $row_1['Total_Cost'],
        'Total_Sale' => $row_1['Total_Sale'],
        'Total_batch' => $num

      );
    }


    return $array;
  }


?>
