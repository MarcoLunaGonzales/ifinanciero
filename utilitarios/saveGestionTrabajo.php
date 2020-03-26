<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

$dbh = new Conexion();

session_start();

$urlRedirect="../index.php?opcion=listGestionTrabajo";
$gestion=$_POST["gestion"];
$nombreGestion=nameGestion($gestion);

$_SESSION['globalGestion']=$gestion;
$_SESSION['globalNombreGestion']=$nombreGestion;

showAlertSuccessError(true,$urlRedirect);	

?>
