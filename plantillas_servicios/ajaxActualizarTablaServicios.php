<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();
$direccion=obtenerValorConfiguracion(42);
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$sIde = "monitoreo"; 
$sKey = "837b8d9aa8bb73d773f5ef3d160c9b17";

//SERVICIOS TLQ
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "lista"=>"Niveles", "padre"=>"80");
$url=$direccion."clasificador/ws-clasificador-post.php";

$lista=json_decode(callService($parametros, $url));

$contador=0;
$idUsuario=$_SESSION['globalUser'];
foreach ($lista->listaNivel1 as $listas) {
	if($contador==0){
		$sql="DELETE FROM cla_servicios";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(); 
	}
	$codigo=$listas->IdClasificador;
	$nombre=$listas->Descripcion;
    $abrev=$listas->Abrev;
    foreach ($listas->ListaNivel2 as $nivel2) {
    	$codigo_n2=$nivel2->IdClasificador;
	    $nombre_n2=$nivel2->Descripcion;
        $abrev_n2=$nivel2->Abrev;
        foreach ($nivel2->ListaNivel3 as $nivel3) {
         	$id_n3=$nivel3->IdClaServicio;
	        $nombre_n3=$nivel3->Descripcion;
            $codigo_n3=$nivel3->Codigo;
            $tipo_n3=$nivel3->IdTipo;
            $sql="INSERT INTO cla_servicios (IdClaServicio,Descripcion,IdUMedida,Codigo,IdArea,IdTipo,Observacion,TiempoEntrega,vigente,codigo_n2,descripcion_n2,abreviatura_n2,codigo_n1,descripcion_n1,abreviatura_n1)
                  VALUES ('$id_n3','$nombre_n3','','$codigo_n3','','$tipo_n3','','',1,'$codigo_n2','$nombre_n2','$abrev_n2','$codigo','$nombre','$abrev_n2')";
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
         } 
    }	  
     $contador++;
}