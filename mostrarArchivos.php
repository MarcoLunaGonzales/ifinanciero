<?php
if(isset($_GET['name'])){
	$url=$_GET['name'];
    $urlArray=explode("/",$url);  
    $nombreArchivo=$urlArray[count($urlArray)-1];
    $ext=explode(".",$nombreArchivo);
    $extencionArchivo=$ext[count($ext)-1];
    if($extencionArchivo=="pdf"){
      Header("Content-Type: application/pdf"); // se envia la cabecera...
    }else{
      Header("Content-Type: image/".$extencionArchivo);
    }	
    @readfile($url);	
}else{
	echo "NO EXISTE EL ARCHIVO";
}