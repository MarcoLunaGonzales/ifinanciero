<?php
require_once '../conexion.php';
require_once 'configModule.php'; //configuraciones
require_once '../styles.php';
require_once '../functionsGeneral.php';
require 'PHPMailer/send.php';

$correo=$_GET['correo'];
$personal=$_GET['personal'];
$evento=$_GET['evento'];
$titulo=$_GET['titulo'];
$mensaje="";

//datos para el envio
$dbhB = new Conexion();
 
     	echo "1$$$".$correo;
     	$sqlB="INSERT INTO eventos_sistemapersonal (cod_eventosistema,cod_personal,cod_estadoreferencial,texto) 
     	VALUES('$evento','$personal','1','$mensaje')";
        $stmtB = $dbhB->prepare($sqlB);
        $stmtB->execute();
?>