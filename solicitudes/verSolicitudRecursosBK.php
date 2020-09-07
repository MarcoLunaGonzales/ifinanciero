<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';


setlocale(LC_TIME, "Spanish");
$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaActual=date("Y-m-d");
$dbh = new Conexion();
if(isset($_GET['cod'])){
	$codigo=$_GET['cod'];
}else{
	$codigo=0;
}
if(isset($_GET['admin'])){
	$urlList=$urlList2;
}

$stmt = $dbh->prepare("SELECT p.*,e.nombre as estado_solicitud, u.abreviatura as unidad,a.abreviatura as area 
        from solicitud_recursos p,unidades_organizacionales u, areas a,estados_solicitudrecursos e
  where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and e.codigo=p.cod_estadosolicitudrecurso and p.codigo='$codigo' order by codigo");
      $stmt->execute();
      $stmt->bindColumn('codigo', $codigoX);
            $stmt->bindColumn('cod_personal', $codPersonalX);
            $stmt->bindColumn('fecha', $fechaX);
            $stmt->bindColumn('cod_unidadorganizacional', $codUnidadX);
            $stmt->bindColumn('cod_area', $codAreaX);
            $stmt->bindColumn('area', $areaX);
            $stmt->bindColumn('unidad', $unidadX);
            $stmt->bindColumn('estado_solicitud', $estadoX);
            $stmt->bindColumn('cod_estadosolicitudrecurso', $codEstadoX);
            $stmt->bindColumn('numero', $numeroX);
            $stmt->bindColumn('cod_simulacion', $codSimulacionX);
            $stmt->bindColumn('cod_proveedor', $codProveedorX);

?>
<div id="logo_carga" class="logo-carga" style="display:none;"></div>
<div class="content">
	<div id="contListaGrupos" class="container-fluid">
			<input type="hidden" name="cod_solicitudrecursos" id="cod_solicitudrecursos" value="<?=$codigo?>">
           <div class="row">
             <div class="col-sm-12">
			  <div class="card">
				<div class="card-header card-header-deafult card-header-text text-center">
					<div class="card-text">
					  <h4 class="card-title"><b>SOLICITUD RECURSOS</b></h4>
					</div>
				</div>
				<div class="card-body">
				
					<div class=""> 	
					<div class="col-sm-8 div-center">	
						<table class="table table-bordered table-condensed table-warning">
							<thead>
								<tr class="">
									<th>Solicitante</th>
									<th>Fecha</th>
									<th>Numero</th>
									<th>Unidad</th>
									<th>Area</th>
								</tr>
							</thead>
							<tbody>
								<tr>
							<?php 
                            while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                                $solicitante=namePersonal($codPersonalX);
                                $fechaSolicitud=strftime('%d/%m/%Y',strtotime($fechaX));
                                ?><td><?=$solicitante?></td><td><?=$fechaSolicitud?></td><td><?=$numeroX?></td><td><?=$unidadX?></td><td><?=$areaX?></td><?php

                            }
                        	?>
                        	</tr>
							</tbody>
						</table>
					</div>
					<div class="col-sm-4 div-center"><center><h4>Detalle de la Solicitud de Recursos</h4></center></div>
					<div class="col-sm-12 div-center">	
						<table class="table table-bordered table-condensed table-warning">
							<thead>
								<tr class="">
									<th>#</th>
									<th>Numero</th>
									<th class="text-left">Nombre Cuenta</th>
									<th>Detalle</th>
									<th>Retenci&oacute;n</th>
									<th class="text-right">Presupuestado</th>
									<th class="text-right">Importe</th>			
									<th>Proveedor</th>
								</tr>
							</thead>
							<tbody>
							<?php 
							$solicitudDetalle=obtenerSolicitudRecursosDetalle($codigo);
							$index=1;$totalImportePres=0;$totalImporte=0;
                             while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                             	$codCuentaX=$rowDetalles['cod_plancuenta'];
                             	$detalleX=$rowDetalles["detalle"];
                             	$importeX=$rowDetalles["importe_presupuesto"];
							    $importeSolX=$rowDetalles["importe"];
							    $proveedorX=nameProveedor($rowDetalles["cod_proveedor"]);
							    $retencionX=$rowDetalles["cod_confretencion"];
							    $totalImportePres+=$importeX;
							    $totalImporte+=$importeSolX;
							    if($retencionX!=0){
							   	  $tituloImporte="<strong>".nameRetencion($retencionX)."</strong>";
							    }else{
							      $tituloImporte="Ninguno";	
							    }
							    $numeroCuentaX=trim($rowDetalles['numero']);
							    $nombreCuentaX=trim($rowDetalles['nombre']);

                                ?>
                                <tr>
                                    <td><?=$index?></td>
                                	<td class="font-weight-bold"><?=$numeroCuentaX?></td>
                                    <td class="text-left"><?=$nombreCuentaX?></td>
                                    <td><?=$detalleX?></td>
                                    <td><?=$tituloImporte?></td>
                                    <td class="text-right"><?=number_format($importeX, 2, '.', ',')?></td>
                                    <td class="text-right"><?=number_format($importeSolX, 2, '.', ',')?></td>
                                    <td><?=$proveedorX?></td>
                                </tr><?php
                              $index++;
                             }
                        	?>
                        	  <tr class="font-weight-bold bg-white text-dark">
                        	  	    <td colspan="5" class="text-left">Total</td>
                                    <td class="text-right"><?=number_format($totalImportePres, 2, '.', ',')?></td>
                                    <td class="text-right"><?=number_format($totalImporte, 2, '.', ',')?></td>
                                    <td></td>
                        	  </tr>
							</tbody>
						</table>
					</div>		
				  	<div class="card-footer fixed-bottom">
						
						<?php 
					if(isset($_GET['q'])){
	                 $q=$_GET['q'];
	                 $s=$_GET['s'];
	                 $u=$_GET['u'];
	                 
	                 if(isset($_GET['r'])){
                      $r=$_GET['r'];
                      ?><a href="../<?=$urlList;?>&q=<?=$q?>&r=<?=$r?>&s=<?=$s?>&u=<?=$u?>" class="btn btn-danger">Volver</a><?php
                    }else{
                    	$v=$_GET['v'];
                      ?><a href="../<?=$urlList;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="btn btn-danger">Volver</a><?php
                    }
	                 
	                 if(isset($_GET['admin'])){
                          if($codEstadoX==4){
                          	?><!--<a href="../<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&q=<?=$q?>" class="btn btn-success">Verificar Solicitud</a>
                            <a href="../<?=$urlEdit2?>?cod=<?=$codigo?>&estado=2&q=<?=$q?>" class="btn btn-danger">Anular Solicitud</a>
                            <a href="../<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&q=<?=$q?>" class="btn btn-default">Rechazar Solicitud</a>--> 
                           <?php
                          }else{
                            ?><!--<a href="../<?=$urlEdit2?>?cod=<?=$codigo?>&estado=2&q=<?=$q?>" class="btn btn-info">Deshacer Cambios</a>--><?php
                          }	
                        }
	                }else{
	                	?><a href="../<?=$urlList;?>" class="btn btn-danger">Volver</a><?php
	                  if(isset($_GET['admin'])){
                          if($codEstadoX==4){
                          	?><!--<a href="../<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>" class="btn btn-success">Verificar Solicitud</a>
                            <a href="../<?=$urlEdit2?>?cod=<?=$codigo?>&estado=2" class="btn btn-danger">Anular Solicitud</a>
                            <a href="../<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1" class="btn btn-default">Rechazar Solicitud</a> -->
                           <?php
                          }else{
                            ?><!--<a href="../<?=$urlEdit2?>?cod=<?=$codigo?>&estado=2" class="btn btn-info">Deshacer Cambios</a>--><?php
                          }	
                        }	 
	                }	   
						?>
				  	</div>
				 </div>
			    </div><!--div end card-->			
               </div>
            </div>
	</div>
</div>


<!-- BK COMPROBANTE DEVENGADO-->
<?php
//require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();


session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$codigo=$_GET["codigo"];
$estado=$_GET["estado"];

$iEstado=obtenerEstadoIfinancieroSolicitudes($estado);
$fechaHoraActual=date("Y-m-d H:i:s");

$sqlUpdate="UPDATE solicitud_recursos SET  cod_estadosolicitudrecurso=$iEstado where codigo=$codigo";
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();

//enviar propuestas para la actualizacion de ibnorca
    $fechaHoraActual=date("Y-m-d H:i:s");
    $idTipoObjeto=2708;
    $idObjeto=$estado; //variable desde get
    $obs=$_GET['obs']; //$obs="Registro de propuesta";
    if(isset($_GET['u'])){
    	$u=$_GET['u'];
    	actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);
    }else{
    	actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);
    }

//////////////////////////////fin cambio estado//////////////////////////777

 if($estado=3){
 	//  CREAR EL COMPROBANTE DEBENGADO
 	// Preparamos
$stmtSolicitud = $dbh->prepare("SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo where sr.cod_estadoreferencial=1 and sr.codigo=$codigo");
// Ejecutamos
$stmtSolicitud->execute();
// bindColumn
$stmtSolicitud->bindColumn('unidad', $unidadX);
$stmtSolicitud->bindColumn('area', $areaX);
$stmtSolicitud->bindColumn('cod_unidadorganizacional', $cod_unidadX);
$stmtSolicitud->bindColumn('cod_area', $cod_areaX);
$stmtSolicitud->bindColumn('cod_simulacion', $codSimulacion);
$stmtSolicitud->bindColumn('cod_proveedor', $codProveedor);
$stmtSolicitud->bindColumn('cod_simulacionservicio', $codSimulacionServicio);
$stmtSolicitud->bindColumn('numero', $numeroSol);

while ($rowSolicitud = $stmtSolicitud->fetch(PDO::FETCH_BOUND)) {
      $unidadX=$unidadX;
      $areaX=$areaX;
      $cod_unidadX=$cod_unidadX;
      $cod_areaX=$cod_areaX;
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

 	//crear el comprobante
    $codComprobante=obtenerCodigoComprobante();

    $codGestion=date("Y");
    $tipoComprobante=3;
    $nroCorrelativo=numeroCorrelativoComprobante($globalGestion,$cod_unidadX,3);
    $fechaHoraActual=date("Y-m-d H:i:s");
    $glosa="SOL.".$numeroSol." - ".$areaX." - ".$nombreSimulacion." COMPROBANTE DEVENGADOS";
    $userSolicitud=obtenerPersonalSolicitanteRecursos($codigo);
    $unidadSol=$cod_unidadX;
    $areaSol=$cod_areaX;

    $sqlInsert="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa, created_at, created_by, modified_at, modified_by) 
    VALUES ('$codComprobante', '1', '$cod_unidadX', '$codGestion', '1', '1', '$tipoComprobante', '$fechaHoraActual', '$nroCorrelativo', '$glosa', '$fechaHoraActual', '$userSolicitud', '$fechaHoraActual', '$userSolicitud')";
    echo $sqlInsert;
    $stmtInsert = $dbh->prepare($sqlInsert);
    $flagSuccessComprobante=$stmtInsert->execute();

    $sqlUpdateSolicitud="UPDATE solicitud_recursos SET cod_comprobante=$codComprobante where codigo=$codigo";
    $stmtUpdateSolicitudRecurso = $dbh->prepare($sqlUpdateSolicitud);
    $stmtUpdateSolicitudRecurso->execute();

    //insertar en detalle comprobante
    $nuevosDetalles=obtenerDetalleSolicitudParaComprobante($codigo); 

        $sqlDelete="";
        $sqlDelete="DELETE from comprobantes_detalle where cod_comprobante='$codComprobante'";
        $stmtDel = $dbh->prepare($sqlDelete);
        $flagSuccess=$stmtDel->execute();
    $i=0;$codProveedor=0;$sumaDevengado=0;$nombresProveedor="";$nombreProveedor="";
    while ($rowNuevo = $nuevosDetalles->fetch(PDO::FETCH_ASSOC)) {
        $i++;
        
        if($codProveedor!=$rowNuevo['cod_proveedor']){
           if($codProveedor!=0){
            //proveedor devengado //para cuando cambie de proveedor (ULTIMO PROVEEEDOR)
          
          $cuentaProv=obtenerValorConfiguracion(36);
          $cuentaAuxiliarProv=obtenerCodigoCuentaAuxiliarProveedorCliente(1,$codProveedor);
          $numeroCuentaProv=trim(obtieneNumeroCuenta($cuentaProv));
          $inicioNumeroProv=$numeroCuentaProv[0];
          //unidad y area de la solicitud
          $unidadareaProv=obtenerUnidadAreaCentrosdeCostos($inicioNumeroProv);           ////////////////////////unidad y area para el detalle
          if($unidadareaProv[0]==0){
              $unidadDetalleProv=$unidadSol;
              $areaProv=$areaSol;
          }else{
              $unidadDetalleProv=$unidadareaProv[0];
              $areaProv=$unidadareaProv[1];
          }

            $debeProv=0;
            $haberProv=$sumaDevengado;
            $glosaDetalleProv=$glosa." - PROVEEDOR ".nameProveedor($codProveedor);
        
            $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
            $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
            VALUES ('$codComprobanteDetalle','$codComprobante', '$cuentaProv', '$cuentaAuxiliarProv', '$unidadDetalleProv', '$areaProv', '$debeProv', '$haberProv', '$glosaDetalleProv', '$i')";
            $stmtDetalle = $dbh->prepare($sqlDetalle);
            $flagSuccessDetalle=$stmtDetalle->execute();
           
            $sumaDevengado=0;
            $i++; 
            
           } 
         $codProveedor=$rowNuevo['cod_proveedor'];
        }
        
        $cuenta=$rowNuevo['cod_plancuenta'];
        $cuentaAuxiliar=0;
        $numeroCuenta=trim(obtieneNumeroCuenta($cuenta));
        $inicioNumero=$numeroCuenta[0];
        $unidadarea=obtenerUnidadAreaCentrosdeCostos($inicioNumero);               ////////////////////////unidad y area para el detalle
        if($unidadarea[0]==0){
            $unidadDetalle=$unidadSol;
            $area=$areaSol;
        }else{
            $unidadDetalle=$unidadarea[0];
            $area=$unidadarea[1];
        }
        
        $debe=$rowNuevo['monto'];
        $haber=0;
        /*if($facturaNueva==){
          $detalleFac="F/";
        }*/
        $glosaDetalle=$glosa." D/".$rowNuevo['glosa'];
        $codSolicitudDetalle=$rowNuevo['codigo'];
        if($rowNuevo['cod_confretencion']==0){
          //detalle comprobante SIN RETENCION //////////////////////////////////////////////////////////////7
          $sumaDevengado+=$debe;
          $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
        $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
        VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$i')";
        $stmtDetalle = $dbh->prepare($sqlDetalle);
        $flagSuccessDetalle=$stmtDetalle->execute();  
        }else{
            // SI TIENE RETENCION **********************************************************************************

            $codigoRet=$rowNuevo['cod_confretencion'];
            $importeOriginal=$rowNuevo['monto'];
            $ii=$i;
          // retencion de costos
            $nom_cuenta_auxiliar="";
            $importeOriginal2=0;
            $totalRetencion=0;
            //obtener datos de retenciones
            $stmtRetenciones = $dbh->prepare("SELECT cd.*,c.porcentaje_cuentaorigen from configuracion_retenciones c join configuracion_retencionesdetalle cd on cd.cod_configuracionretenciones=c.codigo where cd.cod_configuracionretenciones=$codigoRet order by cd.codigo");
            $stmtRetenciones->execute();
            $j=0;
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

             $montoRetencion=number_format($montoRetencion, 2, '.', '');   

             if($debehaberX==1){
                $debeRet=$montoRetencion;
                $haberRet=0;    
              }else{
                $debeRet=0;
                $haberRet=$montoRetencion;
              }
             $importe=number_format($importe, 2, '.', '');
             $cuentaRetencion=$rowRet['cod_cuenta'];  
             $cuentaAuxiliar=0;
             $n_cuenta=trim(obtieneNumeroCuenta($cuentaRetencion));
             $nom_cuenta=nameCuenta($cuentaRetencion);
             $inicioNumeroRet=$n_cuenta[0];
             $unidadareaRet=obtenerUnidadAreaCentrosdeCostos($inicioNumeroRet);////////////////////////unidad y area para el detalle
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
             $retenciones[$j]['glosa']=$glosaX." D/".$rowNuevo['glosa'];
             $retenciones[$j]['numero']=$ii; 
             $retenciones[$j]['debe_haber']=$debehaberX;
           $j++;
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

            if($porcentajeCuentaX<=100){
              $debe=$importeOriginal2;
              $sumaDevengado+=$importeOriginal; 
              $debe=number_format($debe, 2, '.', ''); 
            }else{
              $debe=$importe;
              $sumaDevengado+=$importeOriginal; 
              $debe=number_format($debe, 2, '.', ''); 
            }
           //detalle comprobante CON RETENCION //////////////////////////////////////////////////////////////7
           $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
           $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
           VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$i')";
           $stmtDetalle = $dbh->prepare($sqlDetalle);
           $flagSuccessDetalle=$stmtDetalle->execute();
      //}
        $totalRetencion=0;
      } //fin else *********************************** SI TIENE RETENCION ****************************************************+    
        

       //ASOCIAR PASIVO A DETALLE CUENTA
       

    }  

     if($sumaDevengado!=0){
        //proveedor devengado
          $i++;
          $cuentaProv=obtenerValorConfiguracion(36);
          $cuentaAuxiliarProv=obtenerCodigoCuentaAuxiliarProveedorCliente(1,$codProveedor);
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
            $glosaDetalleProv=$glosa." - PROVEEDOR: ".nameProveedor($codProveedor);
        
            $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
            $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
            VALUES ('$codComprobanteDetalle','$codComprobante', '$cuentaProv', '$cuentaAuxiliarProv', '$unidadDetalleProv', '$areaProv', '$debeProv', '$haberProv', '$glosaDetalleProv', '$i')";
            $stmtDetalle = $dbh->prepare($sqlDetalle);
            $flagSuccessDetalle=$stmtDetalle->execute();

            $codProveedorEstado=$codProveedor;
              //estado de cuentas devengado
              $codEstadoCuenta=obtenerCodigoEstadosCuenta();
              $sqlDetalleEstadoCuenta="INSERT INTO estados_cuenta (codigo,cod_comprobantedetalle, cod_plancuenta, monto, cod_proveedor, fecha,cod_comprobantedetalleorigen,cod_cuentaaux) 
              VALUES ('$codEstadoCuenta','$codComprobanteDetalle', '0', '$haberProv', '$codProveedorEstado', '$fechaHoraActual','0','$cuentaAuxiliarProv')";
              $stmtDetalleEstadoCuenta = $dbh->prepare($sqlDetalleEstadoCuenta);
              $stmtDetalleEstadoCuenta->execute();             
             echo $sqlDetalleEstadoCuenta."";
              //actualizamos con el codigo de comprobante detalle la solicitud recursos detalle 
              $sqlUpdateSolicitudRecursoDetalle="UPDATE solicitud_recursosdetalle SET cod_proveedor=$codProveedorEstado,cod_estadocuenta=$codEstadoCuenta where codigo=$codSolicitudDetalle";
              $stmtUpdateSolicitudRecursoDetalle = $dbh->prepare($sqlUpdateSolicitudRecursoDetalle);
              $stmtUpdateSolicitudRecursoDetalle->execute();

             echo $sqlUpdateSolicitudRecursoDetalle."";
         }    
        
    //fin de crear comprobante
 }   
  

?>

<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

session_start();
$dbh = new Conexion();

$listaSR=json_decode($_POST["solicitudes"]);

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalMes=$_SESSION['globalMes'];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$fechaHoraActual=date("Y-m-d H:i:s");
$userAdmin=obtenerValorConfiguracion(74);




//  CREAR EL COMPROBANTE

//INICIO DE VARIABLES
$glosaDetalleGeneral="";
$tipoComprobante=3;
$sumaDevengado=0;
$codComprobante=obtenerCodigoComprobante();
if(isset($_POST['existe'])&&verificarEdicionComprobanteUsuario($globalUser)!=0){
  $codComprobante=$_POST['existe'];  
}

//personal encargado

$fechaHoraActualSitema=date("Y-m-d H:i:s");

//fecha hora actual para el comprobante (SESIONES)
$anioActual=date("Y");
$mesActual=date("m");
$diaActual=date("d");
$codMesActiva=$_SESSION['globalMes']; 
$month = $globalNombreGestion."-".$codMesActiva;
$aux = date('Y-m-d', strtotime("{$month} + 1 month"));
$diaUltimo = date('d', strtotime("{$aux} - 1 day"));
if((int)$globalNombreGestion<(int)$anioActual){
  $fechaHoraActual=$globalNombreGestion."-".$codMesActiva."-".$diaUltimo;
}else{
  if((int)$mesActual==(int)$codMesActiva){
      $fechaHoraActual=date("Y-m-d");
  }else{
    $fechaHoraActual=$globalNombreGestion."-".$codMesActiva."-".$diaUltimo;
  } 
}
// FIN DE LA FECHA


//crear comprobante
$cod_unidadX=3000;
$nroCorrelativo=numeroCorrelativoComprobante($globalGestion,$cod_unidadX,3,$globalMes);    
$datosServicio="";

//CREACION DEL COMPROBANTE
    if(isset($_POST['existe'])&&verificarEdicionComprobanteUsuario($globalUser)!=0){
       $sqlUpdateComprobantes="UPDATE comprobantes SET modified_at='$fechaHoraActualSitema',modified_by=$globalUser where codigo=$codComprobante";
       $stmtUpdateComprobante = $dbh->prepare($sqlUpdateComprobantes);
       $stmtUpdateComprobante->execute();

       $sqlEstadosCuenta="SELECT codigo from comprobantes_detalle where cod_comprobante=$codComprobante";
       $stmtEstadosCuenta = $dbh->prepare($sqlEstadosCuenta);
       $stmtEstadosCuenta->execute();
       while ($rowEsta = $stmtEstadosCuenta->fetch(PDO::FETCH_ASSOC)) {
        $codigoDetalle=$rowEsta['codigo'];
        $sqlDelete="DELETE from estados_cuenta where cod_comprobantedetalle='$codigoDetalle'";
        $stmtDel = $dbh->prepare($sqlDelete);
        $stmtDel->execute();
       }
      
    }else{
      $sqlInsert="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa, created_at, created_by, modified_at, modified_by) 
      VALUES ('$codComprobante', '1', '$cod_unidadX', '$globalNombreGestion', '1', '1', '$tipoComprobante', '$fechaHoraActual', '$nroCorrelativo', '', '$fechaHoraActualSitema', '$globalUser', '$fechaHoraActualSitema', '$globalUser')";
      $stmtInsert = $dbh->prepare($sqlInsert);
      $flagSuccessComprobante=$stmtInsert->execute();
    }


    $sqlDelete="";
    $sqlDelete="DELETE from comprobantes_detalle where cod_comprobante='$codComprobante'";
    $stmtDel = $dbh->prepare($sqlDelete);
    $flagSuccess=$stmtDel->execute();



//inicio de for
foreach ($listaSR as $liSR) {
  $codigo=$liSR->codigo_item;


//DATOS DE LA SOLICITUD CABECERA
  // Preparamos
$stmtSolicitud = $dbh->prepare("SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo where sr.cod_estadoreferencial=1 and sr.codigo=$codigo");
// Ejecutamos
$stmtSolicitud->execute();
// bindColumn
$stmtSolicitud->bindColumn('unidad', $unidadX);
$stmtSolicitud->bindColumn('area', $areaX);
$stmtSolicitud->bindColumn('cod_unidadorganizacional', $cod_unidadX);
$stmtSolicitud->bindColumn('cod_area', $cod_areaX);
$stmtSolicitud->bindColumn('cod_simulacion', $codSimulacion);
$stmtSolicitud->bindColumn('cod_proveedor', $codProveedor);
$stmtSolicitud->bindColumn('cod_simulacionservicio', $codSimulacionServicio);
$stmtSolicitud->bindColumn('numero', $numeroSol);
$stmtSolicitud->bindColumn('idServicio', $idServicioX);

while ($rowSolicitud = $stmtSolicitud->fetch(PDO::FETCH_BOUND)) {
      $unidadX=3000;
      $areaX=obtenerValorConfiguracion(65);
      $cod_unidadX=$cod_unidadX;
      $cod_areaX=$cod_areaX;
      $codSimulacion=$codSimulacion;
      $codProveedor=$codProveedor;
      $codSimulacionServicio=$codSimulacionServicio;
      $numeroSol=$numeroSol;
      if($codSimulacion!=0){
        $nombreCliente="";
        $nombreSimulacion=nameSimulacion($codSimulacion);
      }else{
        $nombreCliente=nameClienteSimulacionServicio($codSimulacionServicio);
        $nombreSimulacion=nameSimulacionServicio($codSimulacionServicio);
      }
      $glosa=$nombreCliente." SR ".$numeroSol;
}



//FIN DE LA SOLICITUD CABECERA

$facturaCabecera=obtenerNumeroFacturaSolicitudRecursos($codigo);
$glosa="Beneficiario: ".obtenerProveedorSolicitudRecursos($codigo)." ".$datosServicio."  F/".$facturaCabecera." ".$nombreCliente." SR ".$numeroSol;
    $userSolicitud=obtenerPersonalSolicitanteRecursos($codigo);
    $unidadSol=$cod_unidadX;
    $areaSol=$cod_areaX;

    $sqlUpdateSolicitud="UPDATE solicitud_recursos SET cod_comprobante=$codComprobante where codigo=$codigo";
    $stmtUpdateSolicitudRecurso = $dbh->prepare($sqlUpdateSolicitud);
    $stmtUpdateSolicitudRecurso->execute();


//INICIO CREACION DE DETALLES EN COMPROBANTE
    /*Lista detalles de la solicitud de recursos*/
    $nuevosDetalles=obtenerDetalleSolicitudParaComprobante($codigo); 

//VARIABLES INICIO DETALLES
    $i=0;
    $codProveedor=0;

    $nombresProveedor="";
    $nombreProveedor="";
    $sumaRetencionDiferido=0;
    $sumaProveedorPasivo=0;
    $unidadDetalleGrupal=0;
    $areaDetalleGrupal=0;

//CARGAR SI HAY UNA RETENCION (PARA AGRUPAR TODA LA SOLUCITUD)   

    $numeroRetencionFactura=2; //numeroDeRetencionesIVA($codigo)[0];
    $codRetencionGlobal=numeroDeRetencionesIVA($codigo)[1];
    $porcentajeRetencionGlobal=porcentRetencion($codRetencionGlobal);


    while ($rowNuevo = $nuevosDetalles->fetch(PDO::FETCH_ASSOC)) {
        
        //DATOS PARA EL REGISTRO DE LA CUENTA DE GASTO
        $cuentaAuxiliar=0;
        $i++;
        $cuenta=$rowNuevo['cod_plancuenta'];
        $numeroCuenta=trim(obtieneNumeroCuenta($cuenta));
        $inicioNumero=$numeroCuenta[0];
        $unidadarea=obtenerUnidadAreaCentrosdeCostos($inicioNumero);
        $codSolicitudDetalle=$rowNuevo['codigo'];
        $codSolicitudDetalleOrigen=$rowNuevo['codigo'];
        $tituloFactura="";
        if(obtenerNumeroFacturaSolicitudRecursoDetalle($rowNuevo['codigo'])!=""){
          $numeroFacturas=obtenerFacturasSolicitudDetalleArray($rowNuevo['codigo']);
          $numerosFacturasDetalle=[];
          for ($y=0; $y < count($numeroFacturas); $y++) { 
            $numerosFacturasDetalle[$y]=$numeroFacturas[$y][1];
          }
          $tituloFactura="F/ ".implode($numerosFacturasDetalle,',')." - ";
        }

        $glosaDetalle="Beneficiario: ".nameProveedor($rowNuevo['cod_proveedor'])." ".str_replace("-", "",$rowNuevo['glosa'])." ".$tituloFactura." ".$datosServicio." ".$glosa;
        $glosaDetalleRetencion="Beneficiario: ".nameProveedor($rowNuevo['cod_proveedor'])." ".str_replace("-", "",$rowNuevo['glosa'])." ".$datosServicio." ".$glosa;
        

        if($codProveedor!=$rowNuevo['cod_proveedor']){
           if($codProveedor!=0){
            //proveedor devengado //para cuando cambie de proveedor (ULTIMO PROVEEEDOR)
          
            $sumaDevengado=0;
            $i++; 
            
           } 
         $codProveedor=$rowNuevo['cod_proveedor'];
        }
        
         //CARGAR UNIDAD Y AREA DEL DETALLE CENTRO DE COSTOS
        if($unidadarea[0]==0){
            $unidadDetalle=$rowNuevo['cod_unidadorganizacional'];
            $area=$rowNuevo['cod_area'];
        }else{
            $unidadDetalle=$rowNuevo['cod_unidadorganizacional'];
            $area=$rowNuevo['cod_area'];
        }

        $unidadDetalleGlobal=$unidadDetalle;
        $areaDetalleGlobal=$area;

        $debe=$rowNuevo['monto'];
        $sumaProveedorPasivo+=$debe;
        $haber=0;
        if($numeroRetencionFactura==1){
          $sumaRetencionDiferido+=($debe-(($porcentajeRetencionGlobal/100)*$debe));
          $debe=($porcentajeRetencionGlobal/100)*$debe;          
        }

      //REGISTRO DE LA CUENTA DE GASTO

        //SIN RETENCION   
        if(verificarListaDistribucionGastoSolicitudRecurso($codigo)==0){
            //detalle comprobante SIN RETENCION ///////////////////////////////////////////////////////////////
            $sumaDevengado+=$debe;
            $codComprobanteDetalle=obtenerCodigoComprobanteDetalle(); 
            $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
            VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$i')";
            $stmtDetalle = $dbh->prepare($sqlDetalle);
            $flagSuccessDetalle=$stmtDetalle->execute();  
          }else{
            //distribuir gastos
             include "distribucionComprobanteDevengado.php";
          }


       //PASIVO A DETALLE DEL COMPROBANTE
       //datos para el pasivo
          $i++;            

            $tituloFactura="";
            if(obtenerNumeroFacturaSolicitudRecursoDetalle($rowNuevo['codigo'])!=""){
              $tituloFactura="F/".obtenerNumeroFacturaSolicitudRecursoDetalle($rowNuevo['codigo'])." - ";
            }

            $glosaDetalleProv="Beneficiario: ".nameProveedor($rowNuevo['cod_proveedor'])." ".str_replace("-", "",$rowNuevo['glosa'])." ".$tituloFactura." ".$datosServicio." ".$glosa;

            $unidadDetalleProv=$unidadDetalle;
            $areaProv=$area;


            $codProveedorEstado=$codProveedor;
            //   

             $glosaDetalleGeneral=" ".$glosaDetalleProv;
  
    }//FIN WHILE DETALLES DE SOLICITUD

//ACTUALIZAR LA GLOSA DEL COMPROBANTE CABECERA 
    $sqlUpdate="UPDATE comprobantes SET glosa='$glosaDetalleGeneral' WHERE codigo=$codComprobante";
    $stmtUpdate = $dbh->prepare($sqlUpdate);
    $flagSuccessCompro=$stmtUpdate->execute();

//FIN DE CREACION DEL COPMROBANTE


/* ACTUALIZAR ESTADOS DE LA SOLICITUD A CONTABILIZADO*/  
if($flagSuccessCompro==true){
  $sqlUpdate="UPDATE solicitud_recursos SET  cod_estadosolicitudrecurso=5,devengado=0 where codigo=$codigo";
  $stmtUpdate = $dbh->prepare($sqlUpdate);
  $flagSuccess=$stmtUpdate->execute();

//enviar propuestas para la actualizacion de ibnorca
    $fechaHoraActual=date("Y-m-d H:i:s");
    $idTipoObjeto=2708;
    $idObjeto=2725; //regristado
    $obs="Solicitud Contabilizada";
    if(isset($_POST['u'])){
       $u=$_POST['u'];
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);    
     }else{
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);    
     }

  //actualizar al estado pagado 
    $sqlUpdate="UPDATE solicitud_recursos SET  cod_estadosolicitudrecurso=8 where codigo=$codigo";
    $stmtUpdate = $dbh->prepare($sqlUpdate);
    $flagSuccess=$stmtUpdate->execute();
  }

}//FOR SOLICITUDES

    //ACTUALIZAR LA GLOSA DEL COMPROBANTE CABECERA 
    $sqlUpdate="UPDATE comprobantes SET glosa='$glosaDetalleGeneral' WHERE codigo=$codComprobante";
    $stmtUpdate = $dbh->prepare($sqlUpdate);
    $flagSuccessCompro=$stmtUpdate->execute();

$cuentaProv=obtenerValorConfiguracion(83);
$cuentaAuxiliarProv=0;
$debeProv=0;
$haberProv=$sumaDevengado;

$codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
$sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
            VALUES ('$codComprobanteDetalle','$codComprobante', '$cuentaProv', '$cuentaAuxiliarProv', '$unidadDetalleProv', '$areaProv', '$debeProv', '$haberProv', '$glosaDetalleProv', '$i')";
$stmtDetalle = $dbh->prepare($sqlDetalle);
$flagSuccessDetalle=$stmtDetalle->execute();

echo "0";


?>