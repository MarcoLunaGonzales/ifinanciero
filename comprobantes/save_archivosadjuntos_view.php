<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';
session_start();
$dbh = new Conexion();
$codComprobante=$_POST["codigo"];
$nArchivosCabecera=$_POST["cantidad_archivosadjuntos"];
$codPadreArchivos=obtenerValorConfiguracion(84);
$flagArchivo=true;
for ($ar=1; $ar <= $nArchivosCabecera ; $ar++) { 
  if(isset($_POST['codigo_archivo'.$ar])){

    if($_FILES['documentos_cabecera'.$ar]["name"]){
    	// echo "aqui";
      $filename = $_FILES['documentos_cabecera'.$ar]["name"]; //Obtenemos el nombre original del archivos
      $source = $_FILES['documentos_cabecera'.$ar]["tmp_name"]; //Obtenemos un nombre temporal del archivos    
      $directorio = '../assets/archivos-respaldo/COMP-'.$codComprobante.'';
      //Validamos si la ruta de destino existe, en caso de no existir la creamos
      if(!file_exists($directorio)){
                mkdir($directorio, 0777,true) or die("No se puede crear el directorio de extracci&oacute;n");    
      }
      $target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivos
      //Movemos y validamos que el archivos se haya cargado correctamente
      //El primer campo es el origen y el segundo el destino
      if(move_uploaded_file($source, $target_path)) { 
         echo "ok";
        // $tipo=$_POST['codigo_archivo'.$ar];
         $tipo=-100;
        $descripcion=$_POST['nombre_archivo'.$ar];
        $tipoPadre=$codPadreArchivos;
        $codArchivoAdjunto=obtenerCodigoUltimoTabla('archivos_adjuntos');
        $sqlInsert="INSERT INTO archivos_adjuntos (codigo,cod_tipoarchivo,descripcion,direccion_archivo,cod_tipopadre,cod_padre,cod_objeto) 
        VALUES ($codArchivoAdjunto,'$tipo','$descripcion','$target_path','$tipoPadre',0,'$codComprobante')";
        $stmtInsert = $dbh->prepare($sqlInsert);
        //echo $sqlInsert;
        $flagArchivo=$stmtInsert->execute();    
        //print_r($sqlInsert);
        //showAlertSuccessError($flagArchivo,"../comprobantes/archivoComprobante_2.php?codigo=".$codComprobante); 
      } else {    
          $flagArchivo=false;
      } 
    }
  }
}

showAlertSuccessError($flagArchivo,"../comprobantes/archivoComprobante_2.php?codigo=".$codComprobante);

?>