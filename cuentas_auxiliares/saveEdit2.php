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
$tipo=$_POST["tipo"];
$proveedorCliente=$_POST["proveedor_cliente"];
$codEstado="1";

require_once 'configModule.php';

// Prepare
$sql="UPDATE $table SET nombre=:nombre, cod_tipoauxiliar=:cod_tipoauxiliar, cod_proveedorcliente=:cod_proveedorcliente WHERE codigo=:codigo";
//echo $sql;
$stmt = $dbh->prepare($sql);
$values=array(':codigo'=>$codigoX,
':nombre'=>$nombre,
':cod_tipoauxiliar'=>$tipo,
':cod_proveedorcliente'=>$proveedorCliente
);

$exQuery=str_replace(array_keys($values), array_values($values), $sql);
//echo $exQuery;
$flagSuccess=$stmt->execute($values);	

showAlertSuccessError($flagSuccess,$urlList);

?>