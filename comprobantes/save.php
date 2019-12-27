<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codGestion=$_POST["gestion"];
$codUnidad=$_POST["unidad_organizacional"];
$cantidadFilas=$_POST["cantidad_filas"];
$tipoComprobante=$_POST["tipo_comprobante"];
$nroCorrelativo=$_POST["nro_correlativo"];
$glosa=$_POST["glosa"];
$facturas= json_decode($_POST['facturas']);
$estadosCuentas= json_decode($_POST['estados_cuentas']);
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaHoraActual=date("Y-m-d H:i:s");

$codComprobante=obtenerCodigoComprobante();
$sqlInsert="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa, created_at, created_by, modified_at, modified_by) VALUES ('$codComprobante', '1', '$globalUnidad', '$codGestion', '1', '1', '$tipoComprobante', '$fechaHoraActual', '$nroCorrelativo', '$glosa', '$fechaHoraActual', '$globalUser', '$fechaHoraActual', '$globalUser')";
echo $sqlInsert;
$stmtInsert = $dbh->prepare($sqlInsert);
$flagSuccess=$stmtInsert->execute();	

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
       //BORRAMOS LA TABLA
		$sqlDelete="";
		$sqlDelete="DELETE from comprobantes_detalle where cod_comprobante='$codComprobante'";
		$stmtDel = $dbh->prepare($sqlDelete);
		$flagSuccess=$stmtDel->execute();

for ($i=1;$i<=$cantidadFilas;$i++){ 	    	
	$cuenta=$_POST["cuenta".$i];

	if($cuenta!=0 || $cuenta!=""){
		$cuentaAuxiliar=$_POST["cuenta_auxiliar".$i];
		$unidadDetalle=$_POST["unidad".$i];
		$area=$_POST["area".$i];
		$debe=$_POST["debe".$i];
		$haber=$_POST["haber".$i];
		$glosaDetalle=$_POST["glosa_detalle".$i];

		
        $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
		$sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$i')";
		$stmtDetalle = $dbh->prepare($sqlDetalle);
		$flagSuccessDetalle=$stmtDetalle->execute();	

        $nF=cantidadF($facturas[$i-1]);
        
         for($j=0;$j<$nF;$j++){
         	  $nit=$facturas[$i-1][$j]->nit;
         	  $nroFac=$facturas[$i-1][$j]->nroFac;
         	  
         	  $fecha=$facturas[$i-1][$j]->fechaFac;
         	  $porciones = explode("/", $fecha);
         	  $fechaFac=$porciones[2]."-".$porciones[1]."-".$porciones[0];
         	  
         	  $razonFac=$facturas[$i-1][$j]->razonFac;
         	  $impFac=$facturas[$i-1][$j]->impFac;
         	  $exeFac=$facturas[$i-1][$j]->exeFac;
         	  $autFac=$facturas[$i-1][$j]->autFac;
         	  $conFac=$facturas[$i-1][$j]->conFac;

		      $sqlDetalle2="INSERT INTO facturas_compra (cod_comprobantedetalle, nit, nro_factura, fecha, razon_social, importe, exento, nro_autorizacion, codigo_control) VALUES ('$codComprobanteDetalle', '$nit', '$nroFac', '$fechaFac', '$razonFac', '$impFac', '$exeFac', '$autFac', '$conFac')";
		      $stmtDetalle2 = $dbh->prepare($sqlDetalle2);
		      $flagSuccessDetalle2=$stmtDetalle2->execute();
         }

         //itemEstadosCuenta
          $nC=cantidadF($estadosCuentas[$i-1]);
          for($j=0;$j<$nC;$j++){
              $fecha=date("Y-m-d H:i:s");
              $codPlanCuenta=$estadosCuentas[$i-1][$j]->cod_plancuenta;
              $monto=$estadosCuentas[$i-1][$j]->monto;
              $codProveedor=$estadosCuentas[$i-1][$j]->cod_proveedor;
              $codComprobanteDetalleOrigen=$estadosCuentas[$i-1][$j]->cod_comprobantedetalle;
              $fecha=$fecha;
              $sqlDetalle3="INSERT INTO estados_cuenta (cod_comprobantedetalle, cod_plancuenta, monto, cod_proveedor, fecha,cod_comprobantedetalleorigen) VALUES ('$codComprobanteDetalle', '$codPlanCuenta', '$monto', '$codProveedor', '$fecha','$codComprobanteDetalleOrigen')";
              $stmtDetalle3 = $dbh->prepare($sqlDetalle3);
              $flagSuccessDetalle3=$stmtDetalle3->execute();
         }
         //FIN DE ESTADOS DE CUENTA
	}
} 

if($flagSuccessDetalle==true){
	showAlertSuccessError(true,"../".$urlList);	
}else{
	showAlertSuccessError(false,"../".$urlList);
}


?>
