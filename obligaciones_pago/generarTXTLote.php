<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once 'configModule.php';
$dbh = new Conexion();
$codigoLote=$_GET["cod"];
session_start();
$fechaHoraActual=date("Y-m-d H:i:s");
$fh = fopen('data_ebisa.txt', 'w');

$sqlLote="SELECT codigo from pagos_proveedores where cod_pagolote=$codigoLote";
$stmtLote = $dbh->prepare($sqlLote);
$stmtLote->execute();
while ($rowLote = $stmtLote->fetch(PDO::FETCH_ASSOC)) {
  $codigo=$rowLote['codigo'];
  $sql="SELECT sd.nro_cuenta_beneficiario,pd.monto,sd.detalle,sd.nombre_beneficiario,sd.apellido_beneficiario from pagos_proveedoresdetalle pd join solicitud_recursosdetalle sd on pd.cod_solicitudrecursosdetalle=sd.codigo where pd.cod_pagoproveedor=$codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fwrite($fh, $row['nro_cuenta_beneficiario']."@".number_format($row['monto'], 2, '.', '')."@".$row['detalle']."@".$row['nombre_beneficiario']." ".$row['apellido_beneficiario']);
    fwrite($fh, PHP_EOL);
  }
}

fclose($fh);
$fileName = basename("pagosebisalote.txt");
$filePath = "data_ebisa.txt";
if($_GET['a']==1){
  require_once '../layouts/bodylogin.php';
  require_once '../functionsGeneral.php';
  $sqlUpdate="UPDATE pagos_proveedores SET  cod_ebisa=1 where cod_pagolote=$codigoLote;
  UPDATE pagos_lotes SET  cod_ebisalote=1 where codigo=$codigoLote;";
  $stmtUpdate = $dbh->prepare($sqlUpdate);
  $flagSuccess=$stmtUpdate->execute();
  if($flagSuccess==true){
    showAlertSuccessError(true,"../".$urlListPagoLotes); 
  }else{
    showAlertSuccessError(false,"../".$urlListPagoLotes);
  }
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
        //unlink('data_ebisa.txt');
        
        exit;
      }else{
          echo 'The file does not exist.';
      }
}