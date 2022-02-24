
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
        // $sheetCount = count($Reader->sheets());
        // for($i=0;$i<$sheetCount;$i++){
        $Reader->ChangeSheet(0);
        $i=0;
        $array_datos=[];
        $array_datos2=[];
        $nit=0;
        foreach ($Reader as $Row){ 
            if($i==8){//datos de la cabecera
                if(isset($Row[3])){$razon_social=trim($Row[3]);}
                if(isset($Row[8])){$nit=trim($Row[8]);}
            }
            if($i==9){//datos de la cabecera
                if(isset($Row[3])){$direccion_c=trim($Row[3]);}
            }
            if($i==10){//datos de la cabecera
                if(isset($Row[3])){$ciudad=trim($Row[3]);}
                if(isset($Row[7])){
                    $depto=trim($Row[7]);
                    $pais="BOLIVIA";
                }
            }
            if($i==11){//datos de la cabecera
                if(isset($Row[3])){$telefono=trim($Row[3]);}
                if(isset($Row[7])){$fax=trim($Row[7]);}
            }
            if($i==12){//datos de la cabecera
                if(isset($Row[3])){$email=trim($Row[3]);}
                if(isset($Row[7])){$web=trim($Row[7]);}
            }
            if($i==15){//datos de la cabecera
                if(isset($Row[3])){$mae_nombre=trim($Row[3]);}                
            }
            if($i==16){//datos de la cabecera
                if(isset($Row[3])){$mae_cargo=trim($Row[3]);}                
            }
            if($i==17){//datos de la cabecera
                if(isset($Row[3])){$mae_telefono=trim($Row[3]);}                
            }
            if($i==18){//datos de la cabecera
                if(isset($Row[3])){$mae_email=trim($Row[3]);}                
            }

            if($i==20){//datos de la cabecera
                if(isset($Row[3])){$contacto_nombre=trim($Row[3]);}                
            }
            if($i==21){//datos de la cabecera
                if(isset($Row[3])){$contacto_cargo=trim($Row[3]);}                
            }
            if($i==22){//datos de la cabecera
                if(isset($Row[3])){$contacto_telefono=trim($Row[3]);}                
            }
            if($i==23){//datos de la cabecera
                if(isset($Row[3])){$contacto_email=trim($Row[3]);}                
            } 



            if($i==64){//nombre sitio
                $array_datos[0]=$Row[4];
                $array_datos[1]=$Row[5];
                $array_datos[2]=$Row[6];
                $array_datos[3]=$Row[7];
                $array_datos[4]=$Row[8];
            }
            if($i==65){//direccion
                $array_datos2[0]=$Row[4];
                $array_datos2[1]=$Row[5];
                $array_datos2[2]=$Row[6];
                $array_datos2[3]=$Row[7];
                $array_datos2[4]=$Row[8];
            }
            $i++;
        }//fin foreach
        for ($j=0; $j <count($array_datos) ; $j++) { 
            $nombre=$array_datos[$j];
            $direccion=$array_datos2[$j];
            if(!empty($nombre) && !empty($direccion)){ ?>
                <script>itemDatosProductosPlantilla.push({nombre:"<?=$nombre?>",direccion:"<?=$direccion?>"});
                </script><?php
            }
        }
        //echo $depto."**".$telefono."**".$fax."**".$email."**".$web."<br><br>";
        if(!empty($razon_social) && !empty($nit)){?>
            <script>itemDatosProductosPlantilla_cabecera.push({razon_social:"<?=$razon_social?>",nit:"<?=$nit?>",direccion_c:"<?=$direccion_c?>",ciudad:"<?=$ciudad?>",pais:"<?=$pais?>",depto:"<?=$depto?>",telefono:"<?=$telefono?>",fax:"<?=$fax?>",email:"<?=$email?>",web:"<?=$web?>",mae_nombre:"<?=$mae_nombre?>",mae_cargo:"<?=$mae_cargo?>",mae_telefono:"<?=$mae_telefono?>",mae_email:"<?=$mae_email?>",contacto_nombre:"<?=$contacto_nombre?>",contacto_cargo:"<?=$contacto_cargo?>",contacto_telefono:"<?=$contacto_telefono?>",contacto_email:"<?=$contacto_email?>"});
        </script><?php
        }        
        
    
     //}//fin for
     //eliminarArchivo
     unlink($targetPath);
    }else{ 

    }

}


