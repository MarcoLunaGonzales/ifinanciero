<?php
require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
set_time_limit(0);
session_start();
$globalUser=$_SESSION["globalUser"];
//RECIBIMOS LAS VARIABLES
$cod_facturaventa=$_POST['cod_facturaventa_e'];
$razon_social=$_POST['razon_social_e'];
$glosa_factura3=$_POST['glosa_factura3_e'];

try{    
    $stmt = $dbh->prepare("UPDATE facturas_venta set razon_social='$razon_social',glosa_factura3='$glosa_factura3',modified_by=$globalUser,modified_at=NOW() where codigo=$cod_facturaventa");
    $flagSuccess=$stmt->execute();
    if($flagSuccess){
        echo 1;
    }else echo 0;   
} catch(PDOException $ex){
    echo 0;
}

?>
