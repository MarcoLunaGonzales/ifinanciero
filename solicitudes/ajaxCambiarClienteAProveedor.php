<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();
$direccion=obtenerValorConfiguracion(42);//direccion des servicio web
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
$globalUser=$_SESSION["globalUser"];


$sIde = "ifinanciero";
$sKey = "ce94a8dabdf0b112eafa27a5aa475751";

$idCliente=$_GET['idCliente'];
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						  "accion"=>"AsignarAtributoProveedor", 
						  "IdCliente"=>$idCliente, 
						  "IdUsuario"=>$globalUser // valor del id del usuario retornado en el login						  
						  );
$parametros=json_encode($parametros);
// abrimos la sesión cURL
$ch = curl_init();
// definimos la URL a la que hacemos la petición
//curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/registro/ws-registro-proveedor.php"); // OFFICIAL
curl_setopt($ch, CURLOPT_URL,$direccion."registro/ws-registro-proveedor.php"); // PRUEBA
// indicamos el tipo de petición: POST
curl_setopt($ch, CURLOPT_POST, TRUE);
// definimos cada uno de los parámetros
curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
// recibimos la respuesta y la guardamos en una variable
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$remote_server_output = curl_exec ($ch);
// cerramos la sesión cURL
curl_close ($ch);
		