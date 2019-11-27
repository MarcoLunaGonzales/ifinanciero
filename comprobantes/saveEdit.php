<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codGestion=$_POST["gestion"];
//$fechaComprobante=$_POST["fecha"];
$codUnidad=$_POST["unidad_organizacional"];
$cantidadFilas=$_POST["cantidad_filas"];
$tipoComprobante=$_POST["tipo_comprobante"];
$nroCorrelativo=$_POST["nro_correlativo"];
$glosa=$_POST["glosa"];
$facturas= json_decode($_POST['facturas']);
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaHoraActual=date("Y-m-d H:i:s");

$codComprobante=$_POST['codigo_comprobante'];
$sqlUpdate="UPDATE comprobantes SET  glosa='$glosa', modified_at='$fechaHoraActual', modified_by='$globalUser' where codigo=$codComprobante";
echo $sqlUpdate;
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();

//subir archivos al servidor
//Como el elemento es un arreglos utilizamos foreach para extraer todos los valores
    foreach($_FILES["archivos"]['tmp_name'] as $key => $tmp_name)
    {
        //Validamos que el archivos exista
        if($_FILES["archivos"]["name"][$key]) {
            $filename = $_FILES["archivos"]["name"][$key]; //Obtenemos el nombre original del archivos
            $source = $_FILES["archivos"]["tmp_name"][$key]; //Obtenemos un nombre temporal del archivos
            
            $directorio = '../assets/archivos-respaldo/COMP-'.$codComprobante.'/'; //Declaramos un  variable con la ruta donde guardaremos los archivoss
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
for ($i=1;$i<=$cantidadFilas;$i++){
	$cuenta=$_POST["cuenta".$i];

	if($cuenta!=0 || $cuenta!=""){
    $data[$i-1][0]=$_POST["cuenta".$i]; 
    $data[$i-1][1]=$_POST["cuenta_auxiliar".$i]; 
    $data[$i-1][2]=$_POST["unidad".$i]; 
    $data[$i-1][3]=$_POST["area".$i]; 
    $data[$i-1][4]=$_POST["debe".$i]; 	    	
    $data[$i-1][5]=$_POST["haber".$i]; 
    $data[$i-1][6]=$_POST["glosa_detalle".$i];
    $data[$i-1][7]=$i;
    //$dataInsert 	
	}
} 
$cab[0]="cod_cuenta";
$cab[1]="cod_cuentaauxiliar";
$cab[2]="cod_unidadorganizacional";
$cab[3]="cod_area";
$cab[4]="debe";
$cab[5]="haber";
$cab[6]="glosa";
$cab[7]="orden";

//$codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
$comDet=contarComprobantesDetalle($codComprobante);
$comDet->bindColumn('total', $contador);
while ($row = $comDet->fetch(PDO::FETCH_BOUND)) {
 $cont1=$contador;
}

$stmt1 = obtenerComprobantesDet($codComprobante);
editarComprobanteDetalle($codComprobante,'cod_comprobante',$cont1,$cantidadFilas,$stmt1,'comprobantes_detalle',$cab,$data,$facturas);
if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList);	
}else{
	showAlertSuccessError(false,"../".$urlList);
}


?>
