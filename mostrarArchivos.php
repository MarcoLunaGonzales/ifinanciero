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
    	if(strtolower($extencionArchivo)=="jpg"||strtolower($extencionArchivo)=="png"||strtolower($extencionArchivo)=="gif"||strtolower($extencionArchivo)=="sgv"||strtolower($extencionArchivo)=="tif"||strtolower($extencionArchivo)=="jpeg"||strtolower($extencionArchivo)=="ai"){
           Header("Content-Type: image/".$extencionArchivo);
     	}else{
     		header('Content-Type: application/force-download');
            header('Content-Disposition: attachment; filename='.$nombreArchivo);
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: '.filesize($url));
     	}
      
    }	
    @readfile($url);	
}else{
	echo "NO EXISTE EL ARCHIVO";
}