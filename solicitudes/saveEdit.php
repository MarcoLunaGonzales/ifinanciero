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


if(isset($_POST['numero'])){
    $numero=$_POST['numero'];
    $tipoSol=$_POST['tipo_solicitud'];
  if($tipoSol!=2){
    $codProv=0;
    if($tipoSol==3){
      //datos para solicitud recursos manual manual
      $codSim=0;
      $codSimServ=0;
      $globalArea=$_POST['area_solicitud'];
     $globalUnidad=$_POST['unidad_solicitud'];  
    }else{
      //datos para solicitud recursos SIMULACION (PROPUESTA)
     $simu=explode("$$$",$_POST['simulaciones']);
     if($simu[1]=="TCP"){
      //tcp o tcs
      $codSim=0;
      $codSimServ=$simu[0];
      $areaUnidad=obtenerUnidadAreaPorSimulacionServicio($codSimServ);
     }else{
      // sec
      $codSim=$simu[0];
      $codSimServ=0;
      $areaUnidad=obtenerUnidadAreaPorSimulacionCosto($codSim);
     }
     $globalArea=$areaUnidad[0];
     $globalUnidad=$areaUnidad[1];     
    }
  }else{
    //datos para solicitud recursos proveeedor
    $codProv=$_POST['proveedores'];
    $codSim=0;
    $codSimServ=0;
  }

   $codCont=0;//CODIGO DE CONTRATO
  $fecha= date("Y-m-d h:m:s");
  $codSolicitud=obtenerCodigoSolicitudRecursos();
  $dbh = new Conexion();
  if(isset($_POST['usuario_ibnored_v'])){
       $v=$_POST['usuario_ibnored_v'];
       $sqlInsert="INSERT INTO solicitud_recursos (codigo, cod_personal,cod_unidadorganizacional,cod_area,fecha,numero,cod_simulacion,cod_proveedor,cod_simulacionservicio,cod_contrato,idServicio) 
       VALUES ('".$codSolicitud."','".$globalUser."','".$globalUnidad."', '".$globalArea."', '".$fecha."','".$numero."','".$codSim."','".$codProv."','".$codSimServ."','".$codCont."','".$v."')";
  }else{
    $sqlInsert="INSERT INTO solicitud_recursos (codigo, cod_personal,cod_unidadorganizacional,cod_area,fecha,numero,cod_simulacion,cod_proveedor,cod_simulacionservicio,cod_contrato) 
    VALUES ('".$codSolicitud."','".$globalUser."','".$globalUnidad."', '".$globalArea."', '".$fecha."','".$numero."','".$codSim."','".$codProv."','".$codSimServ."','".$codCont."')";
  }
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();

  //enviar propuestas para la actualizacion de ibnorca
  $fechaHoraActual=date("Y-m-d H:i:s");
  $idTipoObjeto=2708;
  $idObjeto=2721; //regristado
  $obs="Registro de Solicitud";
  if(isset($_POST['usuario_ibnored_u'])){
       $u=$_POST['usuario_ibnored_u'];
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codSolicitud,$fechaHoraActual,$obs);
  }else{
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codSolicitud,$fechaHoraActual,$obs);
  }

}




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
    $data[$fila][1]=$_POST["unidad_fila".$i]; 
    $data[$fila][2]=$_POST["area_fila".$i];  
    $data[$fila][3]=$_POST["detalle_detalle".$i]; 
    $data[$fila][4]=$_POST["importe_presupuesto".$i]; 
    $data[$fila][5]=$_POST["importe".$i];           
    $data[$fila][6]=0; 
    $data[$fila][7]="";
    $data[$fila][8]=$_POST["proveedor".$i];
    $data[$fila][9]=$_POST["cod_detalleplantilla".$i];
    $data[$fila][10]=$_POST["cod_servicioauditor".$i];
    $data[$fila][11]=$_POST["cod_retencion".$i];
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
$cab[1]="cod_unidadorganizacional";
$cab[2]="cod_area";
$cab[3]="detalle";
$cab[4]="importe_presupuesto";
$cab[5]="importe";
$cab[6]="numero_factura";
$cab[7]="archivo";
$cab[8]="cod_proveedor";
$cab[9]="cod_detalleplantilla";
$cab[10]="cod_servicioauditor";
$cab[11]="cod_confretencion";
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

if(isset($_POST['usuario_ibnored'])){
    $q=$_POST['usuario_ibnored'];
    $s=$_POST['usuario_ibnored_s'];
    $u=$_POST['usuario_ibnored_u'];
    $v=$_POST['usuario_ibnored_v'];
  if($flagSuccess==true){
    showAlertSuccessError(true,"../".$urlList."&q=".$q."&s=".$s."&u=".$u."&v=".$v); 
  }else{
    showAlertSuccessError(false,"../".$urlList."&q=".$q."&s=".$s."&u=".$u."&v=".$v);
  }
}else{
  if($flagSuccess==true){
    showAlertSuccessError(true,"../".$urlList); 
  }else{
    showAlertSuccessError(false,"../".$urlList);
  }
}
  
?>
