<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$cod_cargo=$codigo;

$stmt = $dbh->prepare("UPDATE cargos_escala_salarial set cod_estadoreferencial=2 where cod_cargo=$cod_cargo");
$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlCargoEscalaSalarialGeneral."&codigo=".$cod_cargo);

?>