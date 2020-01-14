<?php
require_once '../conexion.php';
require_once 'configModule.php'; //configuraciones
require_once '../styles.php';
require_once '../functionsGeneral.php';

$codigo = trim($_GET['codigo']); 
$cod_cargo = $_GET['cod_cargo'];
$cantidad=1;
//agregar nuevo dato
      $dbh = new Conexion();
      $sql="SELECT count(*) as existe from cargos_areasorganizacion where cod_areaorganizacion=$codigo and cod_cargo=$cod_cargo and cod_estadoreferencial=1";
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
         if($row['existe']==0){
          $dbhB = new Conexion();
          $sqlB="INSERT INTO cargos_areasorganizacion (cod_areaorganizacion,cod_cargo,cantidad,cod_estadoreferencial)
           VALUES('$codigo','$cod_cargo','$cantidad',1)";
          $stmtB = $dbhB->prepare($sqlB);
          $stmtB->execute();
          echo "Transacción realizada";
         }else{
          echo "El cargo ya esta registrado!";
         }
      }
      
?>