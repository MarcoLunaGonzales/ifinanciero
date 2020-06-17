<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once 'configModule.php';
$dbh = new Conexion();
$codigos=json_decode($_GET["codigos"]);
session_start();
$fechaHoraActual=date("Y-m-d H:i:s");
$fh = fopen('data_ebisa.txt', 'w');
$filas=0;
for ($i=0; $i < count($codigos); $i++) { 
    if($codigos[$i]!=0){
      $codigo=$codigos[$i];
      $sql="SELECT sd.nro_cuenta_beneficiario,pd.monto,sd.detalle,sd.nombre_beneficiario,sd.apellido_beneficiario from pagos_proveedoresdetalle pd join solicitud_recursosdetalle sd on pd.cod_solicitudrecursosdetalle=sd.codigo where pd.cod_pagoproveedor=$codigo";
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	    fwrite($fh, $row['nro_cuenta_beneficiario']."@".number_format($row['monto'], 2, '.', '')."@".$row['detalle']."@".$row['nombre_beneficiario']." ".$row['apellido_beneficiario']);
	    fwrite($fh, PHP_EOL);
        $filas++;
      }
  // actualizar pagos
      /*$sqlUpdate="UPDATE pagos_proveedores SET  cod_ebisa=1 where codigo=$codigo";
      $stmtUpdate = $dbh->prepare($sqlUpdate);
      $flagSuccess=$stmtUpdate->execute();*/   
    }
}
fclose($fh);
$fileName = basename("pagoslote.txt");
$filePath = "data_ebisa.txt";
if($filas==0){
    echo "1111";
}else{
    if(!empty($fileName) && file_exists($filePath)){
        // Define headers
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: binary");
        // Read the file
        readfile($filePath);
        exit;    
  }else{
    echo '0000';
  }
}