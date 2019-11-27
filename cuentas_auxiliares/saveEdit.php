<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

$dbh = new Conexion();

$codigoX=$_POST["codigo"];
$codigoCuenta=$_POST["codigo_padre"];
$codigo=$codigoCuenta;
$nombre=$_POST["nombre"];
$banco=$_POST["banco"];
$nroCuenta=$_POST["nro_cuenta"];
$direccion=$_POST["direccion"];
$telefono=$_POST["telefono"];
$codEstado="1";
$referencia1=$_POST["referencia1"];
$referencia2=$_POST["referencia2"];

require_once 'configModule.php';

// Prepare
$sql="UPDATE $table SET nombre=:nombre, cod_banco=:banco, nro_cuenta=:nro_cuenta, direccion=:direccion, telefono=:telefono, referencia1=:referencia1, referencia2=:referencia2 WHERE codigo=:codigo";
echo $sql;
$stmt = $dbh->prepare($sql);
$values=array(':codigo'=>$codigoX,
':nombre'=>$nombre,
':banco'=>$banco,
':nro_cuenta'=>$nroCuenta,
':direccion'=>$direccion,
':telefono'=>$telefono,
':referencia1'=>$referencia1,
':referencia2'=>$referencia2);

$exQuery=str_replace(array_keys($values), array_values($values), $sql);
//echo $exQuery;
$flagSuccess=$stmt->execute($values);	

showAlertSuccessError($flagSuccess,$urlList);

?>