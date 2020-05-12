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
 foreach ($lista->lista as $listas) {
	$nombre=$listas->BeneficiarioNombre;
	$apellido=$listas->BeneficiarioApellido;
    $cuenta=$listas->NroCuenta;
    ?>
    <script>
     $("#nombre_beneficiario").val('<?=$nombre?>');
     $("#apellido_beneficiario").val('<?=$apellido?>');
     $("#cuenta_beneficiario").val('<?=$cuenta?>');
    </script>
    <?php
 }
}

