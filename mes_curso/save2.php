<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

session_start();

$urlRedirect="../index.php?opcion=listUnidadOrganizacional";
$mes=$_POST["mes"];

$_SESSION['globalMes']=$mes;

showAlertSuccessError(true,"../".$urlList2);

?>
