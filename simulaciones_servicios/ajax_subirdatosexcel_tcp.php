<?php

require_once('../assets/importar_excel/php-excel-reader/excel_reader2.php');
require_once('../assets/importar_excel/SpreadsheetReader.php');


$file_excel=$_FILES['file_excel'];

if(isset($_FILES['file_excel'])){
    //lectura de archivo Excel
    $filaArchivo=0;
    $allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];  
    if(in_array($file_excel["type"],$allowedFileType)){
        $targetPath = '../uploads_temp/'.$file_excel['name'];
        // echo "<br>***".$file_excel['name']."***<br>";
        move_uploaded_file($file_excel['tmp_name'], $targetPath);        
        $Reader = new SpreadsheetReader($targetPath);       
        $sheetCount = count($Reader->sheets());
        for($i=0;$i<$sheetCount;$i++){         
        $Reader->ChangeSheet($i);
            foreach ($Reader as $Row){ 
                if ($filaArchivo>0){
                    $nombre="";
                    $marca="";
                    if(isset($Row[0])){$nombre=trim($Row[0]);};
                    if(isset($Row[1])){$marca=trim($Row[1]);};
                    
nombre: nombre=datos[fila][0],//nombre
      direccion: datos[fila][4],//direccion
      norma:datos[fila][2],//norma
      norma_cod:codigo_norma,//codigo norma
      norma_otro:"",
      marca:datos[fila][1],//marca,
      sello:datos[fila][3],//sello
      pais:26,
      estado:480,
      ciudad:62,
      nom_pais:"BOLIVIA",
      nom_estado:"LA PAZ",
      nom_ciudad:"LA PAZ"

                    if(!empty($nombre) && !empty($marca)){ ?>
                        <script>itemDatosProductosPlantilla.push({nombre:"<?=$codigoCuentaAux?>",direccion:"<?=$nombreCuentaAux?>",norma:"<?=$codigoCuenta?>",norma_cod:"<?=$codigoCuenta?>",norma_otro:"",marca:"<?=$codigoCuenta?>",sello:"<?=$codigoCuenta?>",pais:26,estado:480,estado:480,ciudad:62,nom_pais:"BOLIVIA",nom_estado:"LA PAZ",nom_ciudad:"LA PAZ"});
                        </script><?php
                    }else{}
                }
                $filaArchivo++;
               }//fin foreach
             }//fin for
             //eliminarArchivo
             unlink($targetPath);
    }else{ 

    }

}


