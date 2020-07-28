<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

if(isset($_GET['codigo'])){
	$codigo=$_GET['codigo'];
  $direccion=obtenerLinkDirectoArchivoAdjunto_sf($codigo);
  $sqlInsert4="DELETE FROM archivos_adjuntos_solicitud_facturacion where codigo=$codigo";
  $stmtInsert4 = $dbh->prepare($sqlInsert4);
  $stmtInsert4->execute();
  if($direccion!=""){
    unlink($direccion); 
  }  
}

?>
