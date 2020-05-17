<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
set_time_limit(300);

$codigo=$_GET['codigo'];
$lista=obtenerListaCuentaBancoProveedorWS($codigo);
if($lista->totalLista>0){
 ?><option disabled selected value="">--Seleccione--</option><?php   
 foreach ($lista->lista as $listas) {
    $cuenta=$listas->NroCuenta;
    $banco=$listas->Banco;
    $cbanco=$listas->IdCuentaBanco;
    ?>
    <option value="<?=$cbanco?>"><?=$banco?> - (<?=$cuenta?>)</option>
    <?php
 }
}

