<?php
require_once '../conexion.php';
require_once '../functions.php';
$dbh = new Conexion();
$area=$_POST['area'];
if(VerificarAreaServicio($area)==null){
   echo "0";
}else{
    if(VerificarAreaServicio($area)==1){
       echo "1";
    }else{
       echo "2";
    }  
}

