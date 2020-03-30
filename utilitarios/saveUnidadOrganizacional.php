<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

$dbh = new Conexion();

session_start();

$urlRedirect="../index.php?opcion=listUnidadOrganizacional";
$oficina=$_POST["oficina"];
$nombreUnidad=abrevUnidad($oficina);

$_SESSION['globalUnidad']=$oficina;
$_SESSION['globalNombreUnidad']=$nombreUnidad;

showAlertSuccessError(true,$urlRedirect);	

?>
