<?php

require_once 'conexion.php';
require_once 'functions.php';

$dbh = new Conexion();

$codigo=$codigo;
$codigoIndicador=$codigo_indicador;

$table="actividades_poa";
$urlRedirect="?opcion=listActividadesPOA&codigo=$codigoIndicador&area=0&unidad=0";

$stmt = $dbh->prepare("UPDATE $table set cod_estado=2 where codigo=:codigo");
$stmt->bindParam(':codigo', $codigo);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlRedirect);

?>