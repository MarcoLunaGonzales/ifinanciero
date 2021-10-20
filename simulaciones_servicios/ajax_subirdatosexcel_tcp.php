<?php

require_once('../assets/importar_excel/php-excel-reader/excel_reader2.php');
require_once('../assets/importar_excel/SpreadsheetReader.php');

// $valor_defecto=6;//filas que recorrera  para el detalle
// $fila_x=36;//36 es la fila donde empieza el detalle
// for ($j=0; $j < $valor_defecto; $j++) { 
// }

$file_excel=$_FILES['file_excel'];
if(isset($_FILES['file_excel'])){
    //lectura de archivo Excel
    $allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];  
    if(in_array($file_excel["type"],$allowedFileType)){
        $targetPath = '../uploads_temp/'.$file_excel['name'];
        // echo "<br>***".$file_excel['name']."***<br>";
        move_uploaded_file($file_excel['tmp_name'], $targetPath);        
        $Reader = new SpreadsheetReader($targetPath);       
        //$sheetCount = count($Reader->sheets());
        //for($i=0;$i<$sheetCount;$i++){//hojas
        $Reader->ChangeSheet(0);
        $i=0;


        $razon_social="";
        $nit="";
        $direccion_c="";
        $ciudad="";
        $depto="";
        $pais="";
        $telefono="";
        $fax="";
        $email="";
        $web="";
        $mae_nombre="";
        $mae_cargo="";
        $mae_telefono="";
        $mae_email="";
        $contacto_nombre="";
        $contacto_cargo="";
        $contacto_telefono="";
        $contacto_email="";
        
        $depto_pais=0;

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
                    $depto_pais=trim($Row[7]);
                    $array_depto_pais=explode("-", $depto_pais);
                    $pais=$array_depto_pais[1];
                    $depto=$array_depto_pais[0];
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
            if($i==14){//datos de la cabecera
                if(isset($Row[3])){$mae_nombre=trim($Row[3]);}                
            }
            if($i==15){//datos de la cabecera
                if(isset($Row[3])){$mae_cargo=trim($Row[3]);}                
            }
            if($i==16){//datos de la cabecera
                if(isset($Row[3])){$mae_telefono=trim($Row[3]);}                
            }
            if($i==17){//datos de la cabecera
                if(isset($Row[3])){$mae_email=trim($Row[3]);}                
            }

            if($i==19){//datos de la cabecera
                if(isset($Row[3])){$contacto_nombre=trim($Row[3]);}                
            }
            if($i==20){//datos de la cabecera
                if(isset($Row[3])){$contacto_cargo=trim($Row[3]);}                
            }
            if($i==21){//datos de la cabecera
                if(isset($Row[3])){$contacto_telefono=trim($Row[3]);}                
            }
            if($i==22){//datos de la cabecera
                if(isset($Row[3])){$contacto_email=trim($Row[3]);}                
            } 



            //****DETALLE
            if($i==35 || $i==36 || $i==37 || $i==38 || $i==39 || $i==40){//desde aqui empieza el detalle
                $nombre="";
                $marca="";
                if(isset($Row[1])){$nombre=trim($Row[1]);};
                if(isset($Row[4])){$marca=trim($Row[4]);};
                if(isset($Row[5])){$norma=trim($Row[5]);};
                if(isset($Row[7])){$sello=trim($Row[7]);};
                if(isset($Row[8])){$direccion=trim($Row[8]);};
                if(!empty($nombre) && !empty($marca)){
                    //echo $nombre."**".$marca."**".$norma."**".$sello."**".$direccion."<br><br>";
                ?>
                    <script>itemDatosProductosPlantilla.push({nombre:"<?=$nombre?>",marca:"<?=$marca?>",norma:"<?=$norma?>",sello:"<?=$sello?>",direccion:"<?=$direccion?>"});
                    </script><?php
                }
            }

            $i++;        
        }//fin foreach



        //echo $depto."**".$mae_telefono."**".$mae_email."**".$contacto_nombre."**".$contacto_cargo."<br><br>";
        ?>
        <script>itemDatosProductosPlantilla_cabecera.push({razon_social:"<?=$razon_social?>",nit:"<?=$nit?>",direccion_c:"<?=$direccion_c?>",ciudad:"<?=$ciudad?>",pais:"<?=$pais?>",depto:"<?=$depto?>",telefono:"<?=$telefono?>",fax:"<?=$fax?>",email:"<?=$email?>",web:"<?=$web?>",mae_nombre:"<?=$mae_nombre?>",mae_cargo:"<?=$mae_cargo?>",mae_telefono:"<?=$mae_telefono?>",mae_email:"<?=$mae_email?>",contacto_nombre:"<?=$contacto_nombre?>",contacto_cargo:"<?=$contacto_cargo?>",contacto_telefono:"<?=$contacto_telefono?>",contacto_email:"<?=$contacto_email?>"});
        </script><?php

         //}//fin for
             //eliminarArchivo
             unlink($targetPath);
    }else{ 

    }

}


