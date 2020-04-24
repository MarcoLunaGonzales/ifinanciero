<?php
set_time_limit(0);
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


// Preparamos
$stmtSolicitud = $dbh->prepare("SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo where sr.cod_estadoreferencial=1 and sr.codigo=$codSolicitud");
// Ejecutamos
$stmtSolicitud->execute();
// bindColumn
$stmtSolicitud->bindColumn('unidad', $unidadX);
$stmtSolicitud->bindColumn('area', $areaX);
$stmtSolicitud->bindColumn('cod_simulacion', $codSimulacion);
$stmtSolicitud->bindColumn('cod_proveedor', $codProveedor);
$stmtSolicitud->bindColumn('cod_simulacionservicio', $codSimulacionServicio);
$stmtSolicitud->bindColumn('numero', $numeroSol);

while ($rowSolicitud = $stmtSolicitud->fetch(PDO::FETCH_BOUND)) {
      $unidadX=$unidadX;
      $areaX=$areaX;
      $codSimulacion=$codSimulacion;
      $codProveedor=$codProveedor;
      $codSimulacionServicio=$codSimulacionServicio;
      $numeroSol=$numeroSol;

      if($codSimulacion!=0){
        $nombreCliente="Sin Cliente";
        $nombreSimulacion=nameSimulacion($codSimulacion);
      }else{
        $nombreCliente=nameClienteSimulacionServicio($codSimulacionServicio);
        $nombreSimulacion=nameSimulacionServicio($codSimulacionServicio);
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
if($flagSuccess==true){

     

    /*if($flagSuccessComprobante==true){
       $sqlUpdateSolicitud="UPDATE solicitud_recursos SET  cod_estadosolicitudrecurso=3,cod_comprobante=$codComprobante where codigo=$codSolicitud";
       $stmtUpdateSolicitud = $dbh->prepare($sqlUpdateSolicitud);
       $stmtUpdateSolicitud->execute();
    }*/
if(!isset($_GET['control_admin'])){
 $urlList2=$urlList;
}   

  if(isset($_POST['usuario_ibnored'])){
    $q=$_POST['usuario_ibnored'];
    $r=$_POST['usuario_ibnored_rol'];
    $s=$_POST['usuario_ibnored_s'];
    $u=$_POST['usuario_ibnored_u'];
    showAlertSuccessError(true,"../".$urlList2."&q=".$q."&r=".$r."&s=".$s."&u=".$u);  
  }else{
  showAlertSuccessError(true,"../".$urlList2); 
   }     
	
}else{
  if(isset($_POST['usuario_ibnored'])){
    $q=$_POST['usuario_ibnored'];
    $r=$_POST['usuario_ibnored_rol'];
    $s=$_POST['usuario_ibnored_s'];
    $u=$_POST['usuario_ibnored_u'];
    $v=$_POST['usuario_ibnored_v'];
   showAlertSuccessError(false,"../".$urlList2."&q=".$q."&r=".$r."&s=".$s."&u=".$u."&v=".$v);
  }else{
  showAlertSuccessError(false,"../".$urlList2);
   } 
	
}

?>
