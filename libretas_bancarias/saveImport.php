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
$fechaActual=date("Y-m-d h:m:s");
$cod_libretabancariaregistro=obtenerCodigoRegistroLibreta();
$flagSuccess=false;
$globalUser=$_SESSION["globalUser"];
if (isset($_POST["codigo"])){
$codigoLibreta=$_POST["codigo"];
$observaciones=$_POST["observaciones"];
$tipo_formato=$_POST["tipo_formato"];
$tipo_cargado=$_POST["tipo_cargado"];
$cod_estadoreferencial="1";   
$message="";
$index=0;
$totalFilasCorrectas=0;
$filasErroneas=0;
$filasErroneasCampos=0;
$filasErroneasFechas=0;
$filaArchivo=0;
$listaFilasFechas=[];
$listaFilasCampos=[];
if($tipo_cargado==2){
  /*$sqlDelete="DELETE FROM  libretas_bancariasdetalle where cod_libretabancaria=$codigoLibreta";
  $stmtDetalle = $dbh->prepare($sqlDelete);
  $stmtDetalle->execute();*/
}
$allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
  
$sqlInserts=[];  
  if(in_array($_FILES["documentos_excel"]["type"],$allowedFileType)){

        $targetPath = 'subidas/'.$_FILES['documentos_excel']['name'];
        move_uploaded_file($_FILES['documentos_excel']['tmp_name'], $targetPath);
        
        $Reader = new SpreadsheetReader($targetPath);       
        $sheetCount = count($Reader->sheets());
        for($i=0;$i<$sheetCount;$i++){         
        $Reader->ChangeSheet($i);
        $validacionFila=1;
           foreach ($Reader as $Row){ 
             if ($filaArchivo>0){
            	if($index==0){
            		// Prepare
                	$sqlRegistro="INSERT INTO libretas_bancariasregistro (codigo,fecha,cod_personal,observaciones,cod_estadoreferencial) 
                    	VALUES ($cod_libretabancariaregistro,'$fechaActual','$globalUser','$observaciones',1)";

                  $sqlInserts[$index]=$sqlRegistro;   
                    //$stmtDetalle = $dbh->prepare($sqlRegistro);
                    //$stmtDetalle->execute();
            	}
                $index++;
                $fecha_hora = "";
                if(isset($Row[0])) {
                  //echo ($Row[0]);
                  $fechaFila=$Row[0]."";
                  $fe=explode("-", $fechaFila);
                  if(count($fe)==3){
                    if(strlen($fe[2])==2){
                      $fe[2]="20".$fe[2];
                      $fechafilaAux=$fe[2]."-".$fe[0]."-".$fe[1];
                    }else{
                      $fechafilaAux=$fe[2]."-".$fe[1]."-".$fe[0];
                    }
                    
                    if(verificarFecha(trim($fechafilaAux))==1){
                       $fecha_hora=$fechafilaAux;
                    }else{
                      $verSi=1;
                    }
                  }else{
                    $fe=explode("/", $fechaFila);
                    if(count($fe)==3){
                      if(strlen($fe[2])==2){
                        $fe[2]="20".$fe[2];
                        $fechafilaAux=$fe[2]."-".$fe[0]."-".$fe[1];
                      }else{
                        $fechafilaAux=$fe[2]."-".$fe[1]."-".$fe[0];
                      }
                      if(verificarFecha(trim($fechafilaAux))==1){
                        $fecha_hora=$fechafilaAux;
                      }else{
                        $verSi=1;
                      }
                    }else{
                      $verSi=1;
                      $fechaFila="";
                    }
                  } 
                	/*if(verificarFecha(trim($fechaFila))==1){
                     $fe=explode("-", $fechaFila);
                     $fecha_hora=$fe[2]."-".$fe[1]."-".$fe[0];
                	}else{
                    if(verificarFecha(trim($fechaFila))==2){
                       $validacionFila=0; 
                       $fe=explode("/", $fechaFila);
                       $fecha_hora=$fe[2]."-".$fe[1]."-".$fe[0];
                    }else{
                      $validacionFila=0; 
                       $verSi=1;
                    }
                     
                	}*/
                }
                
                $hora = "";
                if(isset($Row[1])&&$tipo_formato==1) {
                  $hora=explode(":", $Row[1]);
                  if(count($hora)>2){
                    $horaFecha=$Row[1];
                  }else{
                    $horaFecha=$hora[0].":".$hora[1].":00";
                  }
                	if(verificarHora($Row[1])==true){
                     $fecha_hora.=" ".$horaFecha;
                	}else{
                     $validacionFila=0; 
                     $fecha_hora.=" ".$horaFecha;
                	}
                }
                
                $nro_cheque = "";
                if(isset($Row[2])&&$tipo_formato==1) {
                    $nro_cheque = $Row[2];
                }

                $descripcion = "";
                if(isset($Row[3])&&$tipo_formato==1) {
                    $descripcion = trim($Row[3]);
                }else{
                    $descripcion = trim($Row[2]);
                }

                $monto = "";
                if(isset($Row[4])&&$tipo_formato==1) {
                    $monto = trim($Row[4]);
                }else{
                    $monto = trim($Row[4]);
                }

                $saldo = "";
                if(isset($Row[5])&&$tipo_formato==1) {
                    $saldo = trim($Row[5]);
                }else{
                    $saldo = trim($Row[5]);
                }

                $informacion_complementaria = "";
                if(isset($Row[6])&&$tipo_formato==1) {
                    $informacion_complementaria = $Row[6];
                }

                $agencia = "";
                if(isset($Row[7])&&$tipo_formato==1) {
                    $agencia = $Row[7];
                }else{
                    $agencia = $Row[1];
                }

                $nro_documento = "";
                if(isset($Row[3])&&$tipo_formato!=1) {
                    $nro_documento = $Row[3];
                }else{
                   $nro_documento = $Row[9];
                }
                

                $canal = "";
                if(isset($Row[8])&&$tipo_formato==1) {
                    $canal = $Row[8];
                }

                $nro_referencia = "";
                /*if(isset($Row[9])&&$tipo_formato==1) {
                    $nro_referencia = $Row[9];
                }*/

                $cod_fila = 0;
                if(isset($Row[10])&&$tipo_formato==1) {
                    $cod_fila = $Row[10];
                }
                              
                if (!empty($fecha_hora) || !empty($descripcion) || !empty($monto)) {
                	// Prepare
                  $verSi=0;
                  if(verificarFechaMaxDetalleLibreta($fecha_hora,$codigoLibreta)!=0&&$tipo_cargado==2){
                    $verSi=1;
                    //se encontraron fechas mayores a la fila
                  }
                  if($verSi==0){
                    if($descripcion=="" && ($monto==""||$monto==0)){

                    }else{
                   $totalFilasCorrectas++; 
                	$sql="INSERT INTO libretas_bancariasdetalle (cod_libretabancaria,fecha_hora,nro_documento,descripcion,informacion_complementaria,agencia,monto,nro_cheque,cod_libretabancariaregistro,cod_estadoreferencial,canal,nro_referencia,codigo_fila,saldo) 
                    	VALUES ('$codigoLibreta','$fecha_hora','$nro_documento','$descripcion','$informacion_complementaria','$agencia','$monto','$nro_cheque','$cod_libretabancariaregistro','$cod_estadoreferencial','$canal','$nro_referencia','$cod_fila','$saldo')";
                   // $stmt = $dbh->prepare($sql);
                    //$flagSuccess=$stmt->execute();
                    $sqlInserts[$index]=$sql;
                      
                    }
                    /*if ($flagSuccess==true) {
                        $type = "success";
                        $message = "Excel importado correctamente";
                    } else {
                        $type = "error";
                        $message = "Hubo un problema al importar registros";
                    }*/
                  }else{
                    $listaFilasFechas[$filasErroneasFechas]=$index;
                    $filasErroneas++;
                    $filasErroneasFechas++;
                  }
                }else{
                  if($descripcion=="" && ($monto==""||$monto==0)){

                  }else{
                     $listaFilasCampos[$filasErroneasCampos]=$index;
                     $filasErroneasCampos++;
                     $filasErroneas++;
                  }  
                }
              } //fin de if  
                $filaArchivo++;
           }//fin foreach
        
         }//fin for

         //eliminarArchivo
         unlink($targetPath);
  }
  else
  { 
        $type = "error";
        $message = "El archivo enviado es invalido. Por favor vuelva a intentarlo";
  }
}
if($filasErroneas>0){
  $htmlInforme='';
  $htmlInforme='Errores sin formato: <b>'.$filasErroneasCampos.'</b> <a href="#colapseFormato" class="btn btn-default btn-sm" data-toggle="collapse">Ver más...</a>'.
  '<div id="colapseFormato" class="collapse small">'.
         'Filas:['.implode(",",$listaFilasCampos).']'.
       '</div>'.
  '<br>Errores de fecha: <b>'.$filasErroneasFechas.'</b><a href="#colapseFechas" class="btn btn-default btn-sm" data-toggle="collapse">Ver más...</a>'.
  '<div id="colapseFechas" class="collapse small">'.
         'Filas:['.implode(",",$listaFilasFechas).']'.
       '</div>'. 
  '<br><i class="material-icons text-danger">clear</i> Filas con errores: <b>'.$filasErroneas.'</b>'.    
  '<br><i class="material-icons text-success">check</i> Filas Correctas: <b>'.$totalFilasCorrectas.'</b>'.
  '<br>Total Filas: <b>'.$index.'</b>';
  showAlertSuccessErrorFilasLibreta("../".$urlList2."&codigo=".$codigoLibreta,$htmlInforme);  
}else{
  if($index>0){ // para registrar solo si hay filas en el archivo
    $sqlAcumulados=implode(";", $sqlInserts);
    $stmtAcumulados = $dbh->prepare($sqlAcumulados.";");
    $flagSuccess=$stmtAcumulados->execute();
  }
  
  if($flagSuccess==true){
  	showAlertSuccessError(true,"../".$urlList2."&codigo=".$codigoLibreta);	
  }else{
	  showAlertSuccessError(false,"../".$urlList2."&codigo=".$codigoLibreta);
  }
}

function verificarFecha($x) {
    if (date('Y-m-d', strtotime($x)) == $x) {
      return 1;
    }else{
      return 0;
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