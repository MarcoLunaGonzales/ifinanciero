<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';
$dbh = new Conexion();
$unidad=$_POST['unidad'];
if(VerificarProyFinanciacion($unidad)==null){
   echo "0";
}else{
    if(VerificarProyFinanciacion($unidad)==1){
       echo "1";
    }else{
       echo "2";
    }  
}

