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
$tipo_formato=$_POST["tipo_formato"];
$tipo_cargado=$_POST["tipo_cargado"];
$cod_estadoreferencial="1";   
$message="";
$index=0;
$totalFilasCorrectas=0;
$filasErroneas=0;
$filasErroneasCampos=0;
$filasErroneasFechas=0;
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

                $descripcion = "";
                if(isset($Row[2])&&$tipo_formato==1) {
                    $descripcion = $Row[2];
                }else{
                    $descripcion = $Row[1];
                }

                $informacion_complementaria = "";
                if(isset($Row[3])&&$tipo_formato==1) {
                    $informacion_complementaria = $Row[3];
                }

                $nro_documento = "";
                if(isset($Row[4])&&$tipo_formato==1) {
                    $nro_documento = $Row[4];
                }else{
                   $nro_documento = $Row[2];
                }

                $monto = "";
                if(isset($Row[5])&&$tipo_formato==1) {
                    $monto = $Row[5];
                }else{
                    $monto = $Row[3];
                }

                $agencia = "";
                if(isset($Row[6])&&$tipo_formato==1) {
                    $agencia = $Row[6];
                }else{
                    $agencia = $Row[4];
                }

                $nro_cheque = "";
                if(isset($Row[7])&&$tipo_formato==1) {
                    $nro_cheque = $Row[7];
                }
				
                              
                if (!empty($fecha_hora) || !empty($descripcion) || !empty($monto)) {
                	// Prepare
                  $verSi=0;
                  if(verificarFechaMaxDetalleLibreta($fecha_hora,$codigoLibreta)!=0&&$tipo_cargado==2){
                    $verSi=1;
                    //se encontraron fechas mayores a la fila
                  }
                  if($verSi==0){
                   $totalFilasCorrectas++; 
                	$sql="INSERT INTO libretas_bancariasdetalle (cod_libretabancaria,fecha_hora,nro_documento,descripcion,informacion_complementaria,agencia,monto,nro_cheque,cod_libretabancariaregistro,cod_estadoreferencial) 
                    	VALUES ('$codigoLibreta','$fecha_hora','$nro_documento','$descripcion','$informacion_complementaria','$agencia','$monto','$nro_cheque','$cod_libretabancariaregistro','$cod_estadoreferencial')";
                   // $stmt = $dbh->prepare($sql);
                    //$flagSuccess=$stmt->execute();
                    $sqlInserts[$index]=$sql;
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
                  $listaFilasCampos[$filasErroneasCampos]=$index;
                  $filasErroneasCampos++;
                  $filasErroneas++;
                }
             }
        
         }//fin for
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