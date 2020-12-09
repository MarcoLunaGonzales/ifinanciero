<?php
$globalUser=$_SESSION["globalUser"];
//subir archivos al servidor
//Como el elemento es un arreglos utilizamos foreach para extraer todos los valores
$nArchivosCabecera=$_POST["cantidad_archivosadjuntos"];
for ($ar=1; $ar <= $nArchivosCabecera ; $ar++) { 
    if(isset($_POST['codigo_archivo'.$ar])){
        if($_FILES['documentos_cabecera'.$ar]["name"]){
          $filename = $_FILES['documentos_cabecera'.$ar]["name"]; //Obtenemos el nombre original del archivos
          $filename = str_replace("%","",$filename);//quitamos el % del nombre;
          $source = $_FILES['documentos_cabecera'.$ar]["tmp_name"]; //Obtenemos un nombre temporal del archivos    
          // echo $filename."--";
          $directorio = '../assets/archivos-respaldo/archivos_solicitudes_facturacion/SOLFAC-'.$cod_facturacion; //Declaramos una  variable con la ruta donde guardaremos los archivoss
          //Validamos si la ruta de destino existe, en caso de no existir la creamos
          if(!file_exists($directorio)){
                    mkdir($directorio, 0777,true) or die("No se puede crear el directorio de extracci&oacute;n");    
          }
          $target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivos                      
          //Movemos y validamos que el archivos se haya cargado correctamente
          //El primer campo es el origen y el segundo el destino
          // echo $filename."--";
          if(move_uploaded_file($source, $target_path)) { 
            echo "Archivo guargado.";
            $tipo=$_POST['codigo_archivo'.$ar];
            $descripcion=$_POST['nombre_archivo'.$ar];
            $codArchivoAdjunto=obtenerCodigoUltimoTabla('archivos_adjuntos_solicitud_facturacion');
            // $tipoPadre=2708;
            $sqlInsert="INSERT INTO archivos_adjuntos_solicitud_facturacion(codigo,cod_tipoarchivo,descripcion,direccion_archivo,cod_solicitud_facturacion) 
            VALUES ($codArchivoAdjunto,'$tipo','$descripcion','$target_path','$cod_facturacion')";
            $stmtInsert = $dbh->prepare($sqlInsert);
            $flagArchivo=$stmtInsert->execute();    
            
            if(obtenerValorConfiguracion(93)==1&&$flagArchivo){ //registrar en documentos de ibnorca al final se borra en documento del ifinanciero
            //sibir archivos al servidor de documentos
            $parametros=array(
            "idD" => 16,
            "idR" => $codArchivoAdjunto,
            "idusr" => $globalUser,
            "Tipodoc" => 3596,
            "descripcion" => $descripcion,
            "codigo" => "",
            "observacion" => "-",
            "r" => "http://www.google.com",
            "v" => true
            );
            $resultado=enviarArchivoAdjuntoServidorIbnorca($parametros,$target_path);
           //unlink($target_path);
           //print_r($resultado);        
          }

          }else {    
              echo "Error al guardar archivo.";
          } 
        }
    }
}

?>