<?php
require_once '../conexion.php';
require_once 'configModule.php'; //configuraciones
require_once '../styles.php';
require_once '../functionsGeneral.php';

$cod_fila = $_GET['codigo_fila'];

$dbhB = new Conexion();
$sqlB="DELETE FROM cargos_areasorganizacion where codigo=$cod_fila";
$stmtB = $dbhB->prepare($sqlB);
$stmtB->execute();
echo "Registro Eliminado!";
?>