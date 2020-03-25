<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$cantidadFilas=$_POST["cantidad_filas"];
$facturas= json_decode($_POST['facturas']);
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaHoraActual=date("Y-m-d H:i:s");

$codSolicitud=$_POST['cod_solicitud'];

$flagSuccess=true;
//subir archivos al servidor
//Como el elemento es un arreglos utilizamos foreach para extraer todos los valores
    foreach($_FILES["archivos"]['tmp_name'] as $key => $tmp_name)
    {
        //Validamos que el archivos exista
        if($_FILES["archivos"]["name"][$key]) {
            $filename = $_FILES["archivos"]["name"][$key]; //Obtenemos el nombre original del archivos
            $source = $_FILES["archivos"]["tmp_name"][$key]; //Obtenemos un nombre temporal del archivos
            
            $directorio = '../assets/archivos-respaldo/archivos_solicitudes/SOL-'.$codSolicitud.'/'; //Declaramos una  variable con la ruta donde guardaremos los archivoss
            //Validamos si la ruta de destino existe, en caso de no existir la creamos
            if(!file_exists($directorio)){
                mkdir($directorio, 0777,true) or die("No se puede crear el directorio de extracci&oacute;n");    
            }
            
            
            $target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivos
            
            //Movemos y validamos que el archivos se haya cargado correctamente
            //El primer campo es el origen y el segundo el destino
            if(move_uploaded_file($source, $target_path)) { 
                echo "ok";
                } else {    
                echo "error";
            }
            
        }
    }

//guardar las ediciones
    $fila=0;
for ($i=1;$i<=$cantidadFilas;$i++){	
    if(isset($_POST["habilitar".$i])){      
    $data[$fila][0]=$_POST["partida_cuenta_id".$i]; 
    $data[$fila][1]=$_POST["detalle_detalle".$i]; 
    $data[$fila][2]=$_POST["importe_presupuesto".$i]; 
    $data[$fila][3]=$_POST["importe".$i];           
    $data[$fila][4]=0; 
    $data[$fila][5]="";
    $data[$fila][6]=$_POST["proveedor".$i];
    $data[$fila][7]=$_POST["cod_detalleplantilla".$i];
    $data[$fila][8]=$_POST["cod_servicioauditor".$i];
    $data[$fila][9]=$_POST["cod_retencion".$i];
    //$dataInsert  
    $fila++;
      foreach($_FILES["archivos".$i]['tmp_name'] as $key => $tmp_name)
      {
        //Validamos que el archivos exista
        if($_FILES["archivos".$i]["name"][$key]) {
            $filename = $_FILES["archivos".$i]["name"][$key]; //Obtenemos el nombre original del archivos
            $source = $_FILES["archivos".$i]["tmp_name"][$key]; //Obtenemos un nombre temporal del archivos
            
            $directorio = '../assets/archivos-respaldo/archivos_solicitudes/SOL-'.$codSolicitud.'/DET-'.$fila.'/'; //Declaramos una  variable con la ruta donde guardaremos los archivoss
            //Validamos si la ruta de destino existe, en caso de no existir la creamos
            if(!file_exists($directorio)){
                mkdir($directorio, 0777,true) or die("No se puede crear el directorio de extracci&oacute;n");    
            }
            
            
            $target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivos
            
            //Movemos y validamos que el archivos se haya cargado correctamente
            //El primer campo es el origen y el segundo el destino
            if(move_uploaded_file($source, $target_path)) { 
                echo "ok";
                } else {    
                echo "error";
            }
            
        }
      }   
    }
} 
$cab[0]="cod_plancuenta";
$cab[1]="detalle";
$cab[2]="importe_presupuesto";
$cab[3]="importe";
$cab[4]="numero_factura";
$cab[5]="archivo";
$cab[6]="cod_proveedor";
$cab[7]="cod_detalleplantilla";
$cab[8]="cod_servicioauditor";
$cab[9]="cod_confretencion";
$solDet=contarSolicitudDetalle($codSolicitud);
$solDet->bindColumn('total', $contador);
while ($row = $solDet->fetch(PDO::FETCH_BOUND)) {
 $cont1=$contador;
}

$stmt1 = obtenerSolicitudesDet($codSolicitud);
editarComprobanteDetalle($codSolicitud,'cod_solicitudrecurso',$cont1,$fila,$stmt1,'solicitud_recursosdetalle',$cab,$data,$facturas);


$stmt1 = obtenerSolicitudesDet($codSolicitud);
//PARA registro de facturas
editarComprobanteDetalle($codSolicitud,'cod_solicitudrecurso',$cont1,$fila,$stmt1,'solicitud_recursosdetalle',$cab,$data,$facturas);

if(isset($_POST['usuario_ibnored'])){;
    $q=$_POST['usuario_ibnored'];
  if($flagSuccess==true){
    showAlertSuccessError(true,"../".$urlList."&q=".$q); 
  }else{
    showAlertSuccessError(false,"../".$urlList."&q=".$q);
  }
}else{
  if($flagSuccess==true){
    showAlertSuccessError(true,"../".$urlList); 
  }else{
    showAlertSuccessError(false,"../".$urlList);
  }
}
  
?>
