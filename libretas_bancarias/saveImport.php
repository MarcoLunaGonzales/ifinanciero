<?php
set_time_limit(0);
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

require_once('../assets/importar_excel/php-excel-reader/excel_reader2.php');
require_once('../assets/importar_excel/SpreadsheetReader.php');
session_start();

$dbh = new Conexion();
$fechaActual=date("Y-m-d");
$cod_libretabancariaregistro=obtenerCodigoRegistroLibreta();
$flagSuccess=false;
$globalUser=$_SESSION["globalUser"];
if (isset($_POST["codigo"])){
$codigoLibreta=$_POST["codigo"];
$observaciones=$_POST["observaciones"];
$cod_estadoreferencial="1";   
$message="";
$index=0;

$allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
  
  if(in_array($_FILES["documentos_excel"]["type"],$allowedFileType)){

        $targetPath = 'subidas/'.$_FILES['documentos_excel']['name'];
        move_uploaded_file($_FILES['documentos_excel']['tmp_name'], $targetPath);
        
        $Reader = new SpreadsheetReader($targetPath);       
        $sheetCount = count($Reader->sheets());
        for($i=0;$i<$sheetCount;$i++){         
        $Reader->ChangeSheet($i);
        $validacionFila=1;
            foreach ($Reader as $Row){ 
            	if($index==0){
            		// Prepare
                	$sqlRegistro="INSERT INTO libretas_bancariasregistro (codigo,fecha,cod_personal,observaciones,cod_estadoreferencial) 
                    	VALUES ($cod_libretabancariaregistro,'$fechaActual','$globalUser','$observaciones',1)";
                    $stmtDetalle = $dbh->prepare($sqlRegistro);
                    $stmtDetalle->execute();
            	}
                $index++;
                $fecha_hora = "";
                if(isset($Row[0])) {
                	if(verificarFecha(trim($Row[0]))==true){
                     $fe=explode("-", $Row[0]);
                     $fecha_hora=$fe[2]."-".$fe[1]."-".$fe[0];
                	}else{
                     $validacionFila=0; 
                     $fe=explode("-", $Row[0]);
                     $fecha_hora=$fe[2]."-".$fe[1]."-".$fe[0];
                	}
                }
                
                $hora = "";
                if(isset($Row[1])) {
                	if(verificarHora($Row[1])==true){
                     $fecha_hora.=" ".$Row[1];
                	}else{
                     $validacionFila=0; 
                     $fecha_hora.=" ".$Row[1];
                	}
                }

                $descripcion = "";
                if(isset($Row[2])) {
                    $descripcion = $Row[2];
                }

                $informacion_complementaria = "";
                if(isset($Row[3])) {
                    $informacion_complementaria = $Row[3];
                }

                $nro_documento = "";
                if(isset($Row[4])) {
                    $nro_documento = $Row[4];
                }

                $monto = "";
                if(isset($Row[5])) {
                    $monto = $Row[5];
                }

                $agencia = "";
                if(isset($Row[6])) {
                    $agencia = $Row[6];
                }

                $nro_cheque = "";
                if(isset($Row[7])) {
                    $nro_cheque = $Row[7];
                }
				
                              
                if (!empty($fecha_hora) || !empty($descripcion) || !empty($monto)) {
                	// Prepare
                	$sql="INSERT INTO libretas_bancariasdetalle (cod_libretabancaria,fecha_hora,nro_documento,descripcion,informacion_complementaria,agencia,monto,nro_cheque,cod_libretabancariaregistro,cod_estadoreferencial) 
                    	VALUES ('$codigoLibreta','$fecha_hora','$nro_documento','$descripcion','$informacion_complementaria','$agencia','$monto','$nro_cheque','$cod_libretabancariaregistro','$cod_estadoreferencial')";
                    $stmt = $dbh->prepare($sql);

                    $flagSuccess=$stmt->execute();
                    if ($flagSuccess==true) {
                        $type = "success";
                        $message = "Excel importado correctamente";
                    } else {
                        $type = "error";
                        $message = "Hubo un problema al importar registros";
                    }
                }
             }
        
         }
  }
  else
  { 
        $type = "error";
        $message = "El archivo enviado es invalido. Por favor vuelva a intentarlo";
  }
}

echo $message;
if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList2."&codigo=".$codigoLibreta);	
}else{
	showAlertSuccessError(false,"../".$urlList2."&codigo=".$codigoLibreta);
}

function verificarFecha($x) {
    if (date('d-m-Y', strtotime($x)) == $x) {
      return true;
    } else {
      return false;
    }
}
function verificarHora($x) {
    if (date('H:m:s', strtotime($x)) == $x) {
      return true;
    } else {
      return false;
    }
}
?>