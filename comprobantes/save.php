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

$salvado_temporal=0;
if(isset($_POST['salvado_temporal'])){
  $salvado_temporal=1;
}
session_start();

$codPadreArchivos=obtenerValorConfiguracion(84);

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalMes=$_SESSION['globalMes'];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaHoraActual=$_POST["fecha"];
//$porcionesFecha = explode("/", $_POST['fecha']);
//$fechaHoraActual=$porcionesFecha[2]."-".$porcionesFecha[1]."-".$porcionesFecha[0];
$fechaHoraSistema=date("Y-m-d H:i:s");

$nroCorrelativo=numeroCorrelativoComprobante($globalGestion,$_SESSION['globalUnidad'],$tipoComprobante,$globalMes);

$codComprobante=obtenerCodigoComprobante();
$sqlInsert="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa, created_at, created_by,salvado_temporal) VALUES ('$codComprobante', '1', '$globalUnidad', '$codGestion', '1', '1', '$tipoComprobante', '$fechaHoraActual', '$nroCorrelativo', '$glosa', '$fechaHoraSistema', '$globalUser',$salvado_temporal)";
//echo $sqlInsert;

$stmtInsert = $dbh->prepare($sqlInsert);
$flagSuccess=$stmtInsert->execute();	

//subir archivos al servidor
//borramos los archivos
  $sqlDel="DELETE FROM archivos_adjuntos where cod_objeto=$codComprobante and cod_tipopadre=$codPadreArchivos"; //codigo del padre para comprobantes
  $stmtDel = $dbh->prepare($sqlDel);
  $stmtDel->execute();

//Como el elemento es un arreglos utilizamos foreach para extraer todos los valores
    /*foreach($_FILES["archivos"]['tmp_name'] as $key => $tmp_name)
    {
        //Validamos que el archivo exista
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
    }*/


    $nArchivosCabecera=$_POST["cantidad_archivosadjuntos"];
for ($ar=1; $ar <= $nArchivosCabecera ; $ar++) { 
  if(isset($_POST['codigo_archivo'.$ar])){
    if($_FILES['documentos_cabecera'.$ar]["name"]){
      $filename = $_FILES['documentos_cabecera'.$ar]["name"]; //Obtenemos el nombre original del archivos
      $source = $_FILES['documentos_cabecera'.$ar]["tmp_name"]; //Obtenemos un nombre temporal del archivos    
      $directorio = '../assets/archivos-respaldo/COMP-'.$codComprobante.'/';
      //Validamos si la ruta de destino existe, en caso de no existir la creamos
      if(!file_exists($directorio)){
                mkdir($directorio, 0777,true) or die("No se puede crear el directorio de extracci&oacute;n");    
      }
      $target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivos
      //Movemos y validamos que el archivos se haya cargado correctamente
      //El primer campo es el origen y el segundo el destino
      if(move_uploaded_file($source, $target_path)) { 
        echo "ok";
        $tipo=$_POST['codigo_archivo'.$ar];
        $descripcion=$_POST['nombre_archivo'.$ar];
        $tipoPadre=$codPadreArchivos;
        $sqlInsert="INSERT INTO archivos_adjuntos (cod_tipoarchivo,descripcion,direccion_archivo,cod_tipopadre,cod_padre,cod_objeto) 
        VALUES ('$tipo','$descripcion','$target_path','$tipoPadre',0,'$codComprobante')";
        $stmtInsert = $dbh->prepare($sqlInsert);
        $stmtInsert->execute();    
        print_r($sqlInsert);
      } else {    
          echo "error";
      } 
    }
  }
}

    //BORRAMOS LA TABLA DETALLE
		$sqlDelete="";
		$sqlDelete="DELETE from comprobantes_detalle where cod_comprobante='$codComprobante'";
		$stmtDel = $dbh->prepare($sqlDelete);
		$flagSuccess=$stmtDel->execute();

//ITERAMOS EL DETALLE
for ($i=1;$i<=$cantidadFilas;$i++){ 	    	
	$cuenta=$_POST["cuenta".$i];

	if($cuenta!=0 || $cuenta!=""){
		$cuentaAuxiliar=$_POST["cuenta_auxiliar".$i];
		$unidadDetalle=$_POST["unidad".$i];
		$area=$_POST["area".$i];
		$debe=$_POST["debe".$i];
		$haber=$_POST["haber".$i];
		$glosaDetalle=$_POST["glosa_detalle".$i];
		$codSolicitudRecurso=$_POST["cod_detallesolicitudsis".$i];
    $codActividadProyecto=$_POST["cod_actividadproyecto".$i];
    $codAccNum=$_POST["cod_accnum".$i];
    if($codSolicitudRecurso!=""||$codSolicitudRecurso!=0){
      //actualizar SOLICITUDES SIS AL ESTADO PAGADO
     //verificar que la validacion si tiene centro de costo SIS     
      $sqlUpdate="UPDATE solicitud_recursos SET  cod_estadosolicitudrecurso=8 where codigo=$codSolicitudRecurso";
      $stmtUpdate = $dbh->prepare($sqlUpdate);
      $flagSuccess=$stmtUpdate->execute();

    //habilitar cuando exista el estado pagado
    /*$fechaHoraActual=date("Y-m-d H:i:s");
    $idTipoObjeto=2708;
    $idObjeto=2725; //regristado
    $obs="Solicitud Contabilizada";
    if(isset($_GET['u'])){
       $u=$_GET['u'];
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);    
     }else{
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);    
     }*/
    }
    
    $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
		$sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden,cod_solicitudrecurso,cod_actividadproyecto,cod_accnum) VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$i','$codSolicitudRecurso','$codActividadProyecto','$codAccNum')";
		$stmtDetalle = $dbh->prepare($sqlDetalle);
		$flagSuccessDetalle=$stmtDetalle->execute();	
    
    if($_POST["cod_detallelibreta".$i]!=0){
      $codDetalleLibreta=$_POST["cod_detallelibreta".$i];
      $sqlDetalleUpdate="UPDATE libretas_bancariasdetalle SET cod_comprobante=$codComprobante, cod_comprobantedetalle=$codComprobanteDetalle,cod_estado=1 where codigo=$codDetalleLibreta";
      $stmtDetalleUpdate = $dbh->prepare($sqlDetalleUpdate);
      $stmtDetalleUpdate->execute();  
    }

    /*ACA INSERTAMOS EL ESTADO DE CUENTAS DE FORMA AUTOMATICA CON LA VALIDACION DE TIPO(DEBE/HABER)*/
    $verificaEC=verificarCuentaEstadosCuenta($cuenta);
    $tipoEstadoCuenta=verificarTipoEstadoCuenta($cuenta); // DEBE O HABER PARA ACUMULAR

    $flagSuccessInsertEC=false;

    if( ($verificaEC>0 && $tipoEstadoCuenta==1 && $debe>0) || ($verificaEC>0 && $tipoEstadoCuenta==2 && $haber>0) ) {
      $codTipoEC=obtenerTipoEstadosCuenta($cuenta); //PFOVEEDOR O CLIENTE
      $codProveedorCliente=obtenerCodigoProveedorClienteEC($cuentaAuxiliar);
      //Insertamos el estado de cuentas por el detalle
      $montoEC=0;
      if($debe>0){
        $montoEC=$debe;
      }else{
        $montoEC=$haber;
      }
      $sqlInsertEC="INSERT INTO estados_cuenta (cod_comprobantedetalle, cod_plancuenta, monto, cod_proveedor, fecha,cod_comprobantedetalleorigen,cod_cuentaaux,glosa_auxiliar) 
      VALUES ('$codComprobanteDetalle', '$cuenta', '$montoEC', '$codProveedorCliente', '$fechaHoraActual','0','$cuentaAuxiliar','$glosaDetalle')";
      $stmtInsertEC = $dbh->prepare($sqlInsertEC);
      $flagSuccessInsertEC=$stmtInsertEC->execute();      
    }
    //Fin insertar estado de cuentas acumular.


        $nF=cantidadF($facturas[$i-1]);
        
         for($j=0;$j<$nF;$j++){
         	  $nit=$facturas[$i-1][$j]->nit;
         	  $nroFac=$facturas[$i-1][$j]->nroFac;
         	  
         	  $fechaFac=$facturas[$i-1][$j]->fechaFac;

            $razonFac=$facturas[$i-1][$j]->razonFac;
            $impFac=$facturas[$i-1][$j]->impFac;            
            $autFac=$facturas[$i-1][$j]->autFac;
            $conFac=$facturas[$i-1][$j]->conFac;
            
            $exeFac=$facturas[$i-1][$j]->exeFac;
            $tipoFac=$facturas[$i-1][$j]->tipoFac;
            $tazaFac=$facturas[$i-1][$j]->tazaFac;
            $iceFac=$facturas[$i-1][$j]->iceFac;

            // echo "razonFac:".$razonFac."<br>";
            // echo "autFac:".$autFac."<br>";
            // echo "impFac:".$impFac."<br>";
            // echo "iceFac:".$iceFac."<br>";
            // echo "exc:".$exeFac."<br>";
            // echo "tipo:".$tipoFac."<br>";
            // echo "tasa:".$tazaFac."<br>";
            // echo "nit:".$nit."<br>";

		      $sqlDetalle2="INSERT INTO facturas_compra (cod_comprobantedetalle, nit, nro_factura, fecha, razon_social, importe, exento, nro_autorizacion, codigo_control,ice,tasa_cero,tipo_compra) VALUES ('$codComprobanteDetalle', '$nit', '$nroFac', '$fechaFac', '$razonFac', '$impFac', '$exeFac', '$autFac', '$conFac','$iceFac','$tazaFac','$tipoFac')";
		      $stmtDetalle2 = $dbh->prepare($sqlDetalle2);
		      $flagSuccessDetalle2=$stmtDetalle2->execute();
         }

         //itemEstadosCuenta
         if($flagSuccessInsertEC==false){
          $nC=cantidadF($estadosCuentas[$i-1]);
          for($j=0;$j<$nC;$j++){
              $fecha=date("Y-m-d H:i:s");
            if(isset($estadosCuentas[$i-1][$j]->cod_plancuenta)){
              $codPlanCuenta=$estadosCuentas[$i-1][$j]->cod_plancuenta;
              $codPlanCuentaAux=$estadosCuentas[$i-1][$j]->cod_plancuentaaux;
              $monto=$estadosCuentas[$i-1][$j]->monto;
              $codProveedor=obtenerCodigoProveedorCuentaAux($codPlanCuentaAux);
              $codComprobanteDetalleOrigen=$estadosCuentas[$i-1][$j]->cod_comprobantedetalle;
              $fecha=$fecha;
              $sqlDetalle3="INSERT INTO estados_cuenta (cod_comprobantedetalle, cod_plancuenta, monto, cod_proveedor, fecha,cod_comprobantedetalleorigen,cod_cuentaaux) VALUES ('$codComprobanteDetalle', '$codPlanCuenta', '$monto', '$codProveedor', '$fechaHoraActual','$codComprobanteDetalleOrigen','$codPlanCuentaAux')";
              $stmtDetalle3 = $dbh->prepare($sqlDetalle3);
              $flagSuccessDetalle3=$stmtDetalle3->execute();
             }
          }    
         }
         //FIN DE ESTADOS DE CUENTA
	}
} 

echo "<script>window.opener.location.reload();window.close();</script>";

?>
