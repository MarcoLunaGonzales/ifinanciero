<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$monto=$_POST["monto"];
$codPersona=$_POST["personal"];
$codProyecto=$_POST["proyecto"];
$codEstado="1";

$sql="INSERT INTO $table_personalfin ( cod_proyecto,cod_personal,monto_subsidio, cod_estado_referencial) 
                        VALUES ('$codProyecto','$codPersona','$monto', '$codEstado')";

$stmt = $dbh->prepare($sql);
$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,"../".$urlList);

?>
