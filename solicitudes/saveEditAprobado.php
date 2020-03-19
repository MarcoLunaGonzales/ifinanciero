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
if($flagSuccess==true){

    //crear el comprobante
    $codComprobante=obtenerCodigoComprobante();

    $codGestion=date("Y");
    $tipoComprobante=3;
    $nroCorrelativo=numeroCorrelativoComprobante($globalGestion,$globalUnidad,3);
    $fechaHoraActual=date("Y-m-d H:i:s");
    $glosa="PAGOS DEVENGADOS (SOLICITUD - RECURSOS)";
    $userSolicitud=obtenerPersonalSolicitanteRecursos($codSolicitud);
    $unidadSol=obtenerUnidadSolicitanteRecursos($codSolicitud);
    $areaSol=obtenerAreaSolicitanteRecursos($codSolicitud);

    $sqlInsert="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa, created_at, created_by, modified_at, modified_by) 
    VALUES ('$codComprobante', '1', '$globalUnidad', '$codGestion', '1', '1', '$tipoComprobante', '$fechaHoraActual', '$nroCorrelativo', '$glosa', '$fechaHoraActual', '$userSolicitud', '$fechaHoraActual', '$userSolicitud')";
    echo $sqlInsert;
    $stmtInsert = $dbh->prepare($sqlInsert);
    $flagSuccessComprobante=$stmtInsert->execute();
    //insertar en detalle comprobante
    $nuevosDetalles=obtenerDetalleSolicitudParaComprobante($codSolicitud); 

        $sqlDelete="";
        $sqlDelete="DELETE from comprobantes_detalle where cod_comprobante='$codComprobante'";
        $stmtDel = $dbh->prepare($sqlDelete);
        $flagSuccess=$stmtDel->execute();
    $i=0;$codProveedor=0;$sumaDevengado=0;$nombresProveedor="";$nombreProveedor="";
    while ($rowNuevo = $nuevosDetalles->fetch(PDO::FETCH_ASSOC)) {
        $i++;
        
        if($codProveedor!=$rowNuevo['cod_proveedor']){
           if($codProveedor!=0){
            //proveedor devengado
          
          $cuentaProv=obtenerValorConfiguracion(36);
          $cuentaAuxiliarProv=0;
          $numeroCuentaProv=trim(obtieneNumeroCuenta($cuentaProv));
          $inicioNumeroProv=$numeroCuentaProv[0];
          $unidadareaProv=obtenerUnidadAreaCentrosdeCostos($inicioNumeroProv);
          if($unidadareaProv[0]==0){
              $unidadDetalleProv=$unidadSol;
              $areaProv=$areaSol;
          }else{
              $unidadDetalleProv=$unidadareaProv[0];
              $areaProv=$unidadareaProv[1];
          }

            $debeProv=0;
            $haberProv=$sumaDevengado;
            $glosaDetalleProv="PROVEEDOR: ".nameProveedor($codProveedor);
        
            $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
            $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
            VALUES ('$codComprobanteDetalle','$codComprobante', '$cuentaProv', '$cuentaAuxiliarProv', '$unidadDetalleProv', '$areaProv', '$debeProv', '$haberProv', '$glosaDetalleProv', '$i')";
            $stmtDetalle = $dbh->prepare($sqlDetalle);
            $flagSuccessDetalle=$stmtDetalle->execute();
           
            $sumaDevengado=0;
            $i++; 
           } 
         $codProveedor=$rowNuevo['cod_proveedor'];
         /*if($cambio==1){
          $cambio=0;
         }else{
           $cambio=1; 
         } */
        }
        
        $cuenta=$rowNuevo['cod_plancuenta'];
        $cuentaAuxiliar=0;
        $numeroCuenta=trim(obtieneNumeroCuenta($cuenta));
        $inicioNumero=$numeroCuenta[0];
        $unidadarea=obtenerUnidadAreaCentrosdeCostos($inicioNumero);
        if($unidadarea[0]==0){
            $unidadDetalle=$unidadSol;
            $area=$areaSol;
        }else{
            $unidadDetalle=$unidadarea[0];
            $area=$unidadarea[1];
        }
        
        $debe=$rowNuevo['monto'];
        $haber=0;
        $glosaDetalle=$rowNuevo['glosa'];

        if($rowNuevo['cod_confretencion']==0){
          $sumaDevengado+=$debe;
          $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
        $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
        VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$i')";
        $stmtDetalle = $dbh->prepare($sqlDetalle);
        $flagSuccessDetalle=$stmtDetalle->execute();  
        }else{
            //
            $codigoRet=$rowNuevo['cod_confretencion'];
            $importeOriginal=$rowNuevo['monto'];
            $ii=$i;
          // retencion de costos
      $nom_cuenta_auxiliar="";
      $importeOriginal2=0;$j=0;
      $totalRetencion=0;
    //obtener datos de retenciones
    $stmtRetenciones = $dbh->prepare("SELECT cd.*,c.porcentaje_cuentaorigen from configuracion_retenciones c join configuracion_retencionesdetalle cd on cd.cod_configuracionretenciones=c.codigo where cd.cod_configuracionretenciones=$codigoRet order by cd.codigo");
    $stmtRetenciones->execute();
   while ($rowRet = $stmtRetenciones->fetch(PDO::FETCH_ASSOC)) {
   $ii++;                         
    $porcentajeX=$rowRet['porcentaje'];                         
    $glosaX=$rowRet['glosa'];
    $debehaberX=$rowRet['debe_haber'];

    $porcentajeCuentaX=$rowRet['porcentaje_cuentaorigen'];
    if($porcentajeCuentaX>100){
      $importe=($porcentajeCuentaX/100)*$importeOriginal;
    }else{
      $importeOriginal2=($porcentajeCuentaX/100)*$importeOriginal;
      $importe=$importeOriginal;
    }
    $montoRetencion=($porcentajeX/100)*$importe;
    
    //$totalRetencion+=$montoRetencion;

    $montoRetencion=number_format($montoRetencion, 2, '.', '');   

   if($debehaberX==1){
      $debeRet=$montoRetencion;
      $haberRet=0;    
    }else{
      $debeRet=0;
      $haberRet=$montoRetencion;
    }
    $importe=number_format($importe, 2, '.', '');
    /*if($rowRet['cod_cuenta']==0){
      $n_cuenta="";
      $nom_cuenta="";
      include "addFilaVacio.php";
    }else{*/
      $cuentaRetencion=$rowRet['cod_cuenta'];  
      $cuentaAuxiliar=0;
      $n_cuenta=trim(obtieneNumeroCuenta($cuentaRetencion));
      $nom_cuenta=nameCuenta($cuentaRetencion);
      $inicioNumeroRet=$n_cuenta[0];
      $unidadareaRet=obtenerUnidadAreaCentrosdeCostos($inicioNumeroRet);
      if($unidadareaRet[0]==0){
            $unidadDetalleRet=$unidadSol;
            $areaRet=$areaSol;
      }else{
           $unidadDetalleRet=$unidadareaRet[0];
            $areaRet=$unidadareaRet[1];
      }
    
      $retenciones[$j]['cuenta']=$cuentaRetencion;
      $retenciones[$j]['unidad']=$unidadDetalleRet;
      $retenciones[$j]['area']=$areaRet;
      $retenciones[$j]['debe']=$debeRet;
      $retenciones[$j]['haber']=$haberRet;
      $retenciones[$j]['glosa']=$glosaX;
      $retenciones[$j]['numero']=$ii; 
      $retenciones[$j]['debe_haber']=$debehaberX;

    /*}*/
    $j++;

   // $sumaDevengado+=$totalRetencion;
  }

  $i=$ii;     

        
       $totalRetencion=0;  
     //if($totalRetencion!=0){
        for ($j=0; $j < count($retenciones); $j++) { 
        $cuentaRetencion=$retenciones[$j]['cuenta'];
        $unidadDetalleRet=$retenciones[$j]['unidad'];
        $areaRet=$retenciones[$j]['area'];
        $debeRet=$retenciones[$j]['debe'];
        $haberRet=$retenciones[$j]['haber'];
        $glosaX=$retenciones[$j]['glosa'];
        $ii=$retenciones[$j]['numero']; 

        if($retenciones[$j]['debe_haber']==1){
          $totalRetencion+=(float)$debeRet;
        }else{
          $totalRetencion+=(float)$haberRet;
        }   
        $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
        $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
        VALUES ('$codComprobanteDetalle','$codComprobante', '$cuentaRetencion', '$cuentaAuxiliar', '$unidadDetalleRet', '$areaRet', '$debeRet', '$haberRet', '$glosaX', '$ii')";
        $stmtDetalle = $dbh->prepare($sqlDetalle);
        $flagSuccessDetalle=$stmtDetalle->execute();

        //$sumaDevengado+=$totalRetencion;   
        }

          // fin de retencion 
        $haber=0;
        
        //$glosaDetalle.="( ".$totalRetencion." --- ".$rowNuevo['monto'].")"; 
        if($porcentajeCuentaX<=100){
          $debe=$importeOriginal2;
          $sumaDevengado+=$importeOriginal; 
          $debe=number_format($debe, 2, '.', ''); 
        }else{
          $debe=$importe;
          $sumaDevengado+=$importeOriginal; 
          $debe=number_format($debe, 2, '.', ''); 
        }

        $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
        $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
        VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$i')";
        $stmtDetalle = $dbh->prepare($sqlDetalle);
        $flagSuccessDetalle=$stmtDetalle->execute();

      //}
      $totalRetencion=0;
     } //fin else
          
        
    }  

     if($sumaDevengado!=0){
        //proveedor devengado
          $i++;
          $cuentaProv=obtenerValorConfiguracion(36);
          $cuentaAuxiliarProv=0;
          $numeroCuentaProv=trim(obtieneNumeroCuenta($cuentaProv));
          $inicioNumeroProv=$numeroCuentaProv[0];
          $unidadareaProv=obtenerUnidadAreaCentrosdeCostos($inicioNumeroProv);
          if($unidadareaProv[0]==0){
              $unidadDetalleProv=$unidadSol;
              $areaProv=$areaSol;
          }else{
              $unidadDetalleProv=$unidadareaProv[0];
              $areaProv=$unidadareaProv[1];
          }

            $debeProv=0;
            $haberProv=$sumaDevengado;
            $glosaDetalleProv="PROVEEDOR: ".nameProveedor($codProveedor);
        
            $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
            $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
            VALUES ('$codComprobanteDetalle','$codComprobante', '$cuentaProv', '$cuentaAuxiliarProv', '$unidadDetalleProv', '$areaProv', '$debeProv', '$haberProv', '$glosaDetalleProv', '$i')";
            $stmtDetalle = $dbh->prepare($sqlDetalle);
            $flagSuccessDetalle=$stmtDetalle->execute();
    }    
        

    //fin de crear comprobante 

    if($flagSuccessComprobante==true){
       $sqlUpdateSolicitud="UPDATE solicitud_recursos SET  cod_estadosolicitudrecurso=3,cod_comprobante=$codComprobante where codigo=$codSolicitud";
       $stmtUpdateSolicitud = $dbh->prepare($sqlUpdateSolicitud);
       $stmtUpdateSolicitud->execute();
    }
       
	showAlertSuccessError(true,"../".$urlList2);	
}else{
	showAlertSuccessError(false,"../".$urlList2);
}

?>
