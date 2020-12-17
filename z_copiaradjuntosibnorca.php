<?php
set_time_limit(0);
require_once 'conexion.php';

    $dbh = new Conexion();
    $sqlX="SET NAMES 'utf8'";
    $stmtX = $dbh->prepare($sqlX);
    $stmtX->execute();


    $encontrados=0;
    $leidos=0;
    $no_encontrados=0;
    $no_leidos=0;
    $todos=0;
    $cod_objetos=[];
    $cod_archivos=[];
    $cod_archivos_copiados=[];
    //archivos solicitudes de recursos
    $sql="SELECT codigo,direccion_archivo as origen,cod_objeto,descripcion  from archivos_adjuntos where cod_tipopadre!=-2020 and (cod_archivoibnorca=0 or cod_archivoibnorca is null);";		
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    
    while ($rowDetalle = $stmt->fetch(PDO::FETCH_ASSOC)) {
       $cod_archivo=$rowDetalle['codigo'];
       $dir=$rowDetalle['origen'];
       $dirCorrecto=str_replace("../","",$dir);
       $codigo=$rowDetalle['cod_objeto'];
       $array1=explode("/",$dir);
       $descripcion=$rowDetalle['descripcion'];
       $codigoArchivo=$cod_archivo;       
       $nombreArchivo=$array1[count($array1)-1];
       
       $usuarioRegistro=90;  /////////////////////////////////////////////////////////////USUARIO QUE REGISTRARA EL CAMBIO
       $ubicacionDestinoNoLeido="ibn_archivos_no_leidos/3596/".$nombreArchivo;
       $carpeta="ibn_archivos/3596/";
       $carpeta_no_leidos = "ibn_archivos_no_leidos/";
       $todos++; 
       if (file_exists($dirCorrecto)) {
          echo "Archivo Encontrado: $dirCorrecto<br>";
          $encontrados++;
          if (is_readable($dirCorrecto)){
            echo "Archivo leído: $dirCorrecto<br>";  
            $leidos++;  
            if(!file_exists($carpeta)){
                mkdir($carpeta, 0777,true) or die("No se puede crear el directorio de extracci&oacute;n");    
            }

            //insertar registro dbdocumentos
            $tipoArchivo=filetype($dirCorrecto);
            $tamanioArchivo=filesize($dirCorrecto);
            $codigoFila=obtenerCodigoUltimoTabla("dbdocumentos.documentos");
            $codigoPrevio=$codigoFila.$codigoArchivo;
            $ubicacionDestino="ibn_archivos/3596/".$codigoPrevio.$nombreArchivo;
            $sqlDocumento="INSERT INTO dbdocumentos.documentos (idDocumento,IdTipo,Descripcion,NombreCodigo,NombreArchivo,Tipo,Tamanio,FechaRegistro,IdUsuarioRegistro,Observaciones,path) 
            VALUES('$codigoFila',3596,'$descripcion','','$nombreArchivo','$tipoArchivo','$tamanioArchivo',NOW(),$usuarioRegistro,'-','/$ubicacionDestino')";    
            $stmtDocumento = $dbh->prepare($sqlDocumento);
            $flagSuccess=$stmtDocumento->execute();
            if($flagSuccess){
              $sqlDocumentoAlter="UPDATE archivos_adjuntos set cod_archivoibnorca=$codigoFila where codigo=$codigoArchivo";    
              $stmtDocumentoAlter = $dbh->prepare($sqlDocumentoAlter);
              $stmtDocumentoAlter->execute();
             if(copy($dirCorrecto,$ubicacionDestino)) { 
               echo "(COPIAR EXITOSO)";   
             }else{    
               echo "**ERROR**";
             }              
            }

            array_push($cod_archivos_copiados,$cod_archivo);	
          }else{
            echo "No se pudo leer el archivo: $dirCorrecto<br>"; 
            $no_leidos++;
            if(!file_exists($carpeta_no_leidos)){
                mkdir($carpeta_no_leidos, 0777,true) or die("No se puede crear el directorio de extracci&oacute;n");    
            }
            if(copy($dirCorrecto,$ubicacionDestinoNoLeido)) { 
              echo "(COPIAR EXITOSO)";   
            } else {    
                echo "**ERROR**";
            }   	
          } 

       }else{
           echo "<br>El Archivo no existe $dirCorrecto<br>";
           $no_encontrados++;
           array_push($cod_objetos,$codigo);
           array_push($cod_archivos,$cod_archivo);
       }

    }//fin while


    echo "<hr><br><br>TODOS:".$todos."<BR>";
    echo "ENCONTRADOS:".$encontrados."<BR>";
    echo "LEIDOS:".$leidos."<BR>";
    echo "NO LEIDOS:".$no_leidos."<BR>";
    echo "NO ENCONTRADOS:".$no_encontrados."<BR>LISTA DE CODIGOS (".implode(",",array_unique($cod_objetos)).")";
    echo "<BR>LISTA DE ARCHIVOS NO ENCONTRADOS(".implode(",",array_unique($cod_archivos)).")";
    echo "<BR>LISTA DE ARCHIVOS COPIADOS(".implode(",",array_unique($cod_archivos_copiados)).")";


  function obtenerCodigoUltimoTabla($tabla){
    $dbh = new Conexion();
     $stmt = $dbh->prepare("SELECT IFNULL(max(c.idDocumento)+1,1)as idDocumento from $tabla c");
     $stmt->execute();
     $valor=0;
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $valor=$row['idDocumento'];
     }
     return($valor);
  }