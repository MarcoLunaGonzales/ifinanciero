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
                    if(isset($Row[2])){$norma=trim($Row[2]);};
                    if(isset($Row[3])){$sello=trim($Row[3]);};
                    if(isset($Row[4])){$direccion=trim($Row[4]);};
                    
                    if(!empty($nombre) && !empty($marca)){ ?>
                        <script>itemDatosProductosPlantilla.push({nombre:"<?=$nombre?>",marca:"<?=$marca?>",norma:"<?=$norma?>",sello:"<?=$sello?>",direccion:"<?=$direccion?>"});
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


