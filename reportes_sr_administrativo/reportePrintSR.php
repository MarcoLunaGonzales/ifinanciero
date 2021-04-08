<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';

require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$gestion = $_POST["gestiones"];
$cod_mes_x = $_POST["cod_mes_x"];

$estadoPost=$_POST["estado"];
$stringEstadoX=implode(",", $estadoPost);

$nombre_gestion=nameGestion($gestion);
$nombre_mes=nombreMes($cod_mes_x);

$oficinaPost=$_POST["unidad_costo"];
$stringOficinaX=implode(",", $oficinaPost);

$areaPost=$_POST["area_costo"];
$stringAreaX=implode(",", $areaPost);

$personalPost=$_POST["personal"];
$stringPersonalX=implode(",", $personalPost);

$cuentaPost=$_POST["cuenta"];
$stringCuentaX=implode(",", $cuentaPost);

// Preparamos
$sql="SELECT l.* from (SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area,(SELECT count(*) from solicitud_recursosdetalle where cod_solicitudrecurso=sr.codigo AND cod_plancuenta in ($stringCuentaX) and cod_unidadorganizacional in ($stringOficinaX) and cod_area in ($stringAreaX)) as detalle_sr,
  (SELECT group_concat(codigo) from solicitud_recursosdetalle where cod_solicitudrecurso=sr.codigo AND cod_plancuenta in ($stringCuentaX) and cod_unidadorganizacional in ($stringOficinaX) and cod_area in ($stringAreaX)) as grupo_detalles
  from solicitud_recursos sr 
  join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo 
  join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo 
  join areas a on sr.cod_area=a.codigo) l 
  where l.cod_estadoreferencial=1 and (l.cod_estadosolicitudrecurso in ($stringEstadoX)) and l.cod_personal in ($stringPersonalX) and year(l.fecha)=$nombre_gestion and month(l.fecha)=$cod_mes_x and l.detalle_sr>0
  order by l.numero desc";
//  echo $sql;
$stmt = $dbh->prepare($sql);
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('unidad', $unidad);
$stmt->bindColumn('area', $area);
$stmt->bindColumn('fecha', $fecha);
$stmt->bindColumn('cod_personal', $codPersonal);
$stmt->bindColumn('cod_simulacion', $codSimulacion);
$stmt->bindColumn('cod_proveedor', $codProveedor);
$stmt->bindColumn('cod_estadosolicitudrecurso', $codEstado);
$stmt->bindColumn('estado', $estado);
$stmt->bindColumn('cod_comprobante', $codComprobante);
$stmt->bindColumn('cod_simulacionservicio', $codSimulacionServicio);
$stmt->bindColumn('numero', $numeroSol);
$stmt->bindColumn('idServicio', $idServicio);
$stmt->bindColumn('glosa_estado', $glosa_estadoX);
$stmt->bindColumn('revisado_contabilidad', $estadoContabilidadX);
$stmt->bindColumn('grupo_detalles', $grupoDetallesX);


if (isset($_POST["check_formato2"])) {
  $check_formato2=$_POST["check_formato2"]; 
  if($check_formato2){    
    $sw_check="1";
  }else{
    $sw_check="0";
  }
}else{
  $sw_check="0";
}


if($sw_check=="0"){
  $idTabla="reporte_datos_busqueda";
}else{
  $idTabla=""; 
}
?>
 <script> 
          gestion_reporte='<?=$nombre_gestion;?>';
          mes_reporte='<?=$nombre_mes;?>';
 </script>
 <style>
  #reporte_datos_busqueda_filter{
         display: none !important;
       }      
</style>
<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon bg-blanco">
                    <img class="" width="60" height="60" src="../assets/img/logo_ibnorca_origen.png">
                  </div>                  
                  <h3 class="card-title text-center" ><b>Solicitudes de Recursos</b>
                    <span><br><h6>
                    Del Período: <?=$nombre_mes;?>/<?=$nombre_gestion;?><br>
                    <!--Expresado En Bolivianos</h6></span></h3>                  -->
                  <!-- <h6 class="card-title">Unidad: <?=$stringUnidades;?></h6> -->
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                        <table id="<?=$idTabla?>" class="table table-bordered table-condensed" style="width:100%">
                          <thead>
                            <tr>
                              <th style="border:2px solid;" width="2%"><small><b>#</b></small></th>
                              <th style="border:2px solid;"><small><b>Of. - Area</b></small></th>
                              <th style="border:2px solid;"><small><b>Nº Sol.</b></small></th>
                              <th style="border:2px solid;"><small><b>Comprobante</b></small></th>
                              <th style="border:2px solid;"><small><b>Fecha</b></small></th>
                              <th style="border:2px solid;" width="22%"><small><b>Glosa</b></small></th>
                              <th style="border:2px solid;"><small><b>Cod. Servicio</b></small></th>
                              <th style="border:2px solid;"><small><b>Cliente</b></small></th>
                              <th style="border:2px solid;" width="12%"><small><b>Proveedor</b></small></th>
                              <th style="border:2px solid;"><small><b>Cuenta</b></small></th>
                              <th style="border:2px solid;" width="12%"><small><b>Solicitante</b></small></th>
                              <th style="border:2px solid;"><small><b>Fecha</b></small></th>
                              <!--<th style="border:2px solid;" width="16%"><small><b>Observaciones</b></small></th>-->
                              <th style="border:2px solid;" width="12%"><small><b>Personal Pago</b></small></th>
                              <th style="border:2px solid;" width="12%"><small><b>Observaciones</b></small></th>
                              <th style="border:2px solid;"><small><b>Presupuesto</b></small></th>
                              <th style="border:2px solid;"><small><b>Retención</b></small></th>
                              <th style="border:2px solid;"><small><b>Monto</b></small></th>
                              <th style="border:2px solid;"><small><b>Estado</b></small></th>
                            </tr>
                          </thead>
                            <tbody>
                             <?php
            $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $solicitante=namePersonal($codPersonal);
                          switch ($codEstado) {
                            case 1:
                              $nEst=40;$barEstado="progress-bar-default";$btnEstado="btn-default";
                            break;
                            case 2:
                              $nEst=10;$barEstado="progress-bar-danger";$btnEstado="btn-danger";
                            break;
                            case 3:
                              $nEst=100;$barEstado="progress-bar-success";$btnEstado="btn-success";
                            break;
                            case 4:
                              $nEst=60;$barEstado="progress-bar-warning";$btnEstado="btn-warning";
                            break;
                            case 5:
                              $nEst=100;$barEstado="progress-bar-primary";$btnEstado="btn-primary";
                            break;
                            case 6:
                              $nEst=50;$barEstado="progress-bar-rose";$btnEstado="btn-rose";
                            break;
                            case 7:
                              $nEst=55;$barEstado="progress-bar-info";$btnEstado="btn-info";
                            break;
                          }
                          
                          $nombreComprobante="";
                          $fechaComprobante="";
                          $glosaComprobante="";
                          if($codEstado==5||$codEstado==8){
                            $nombreComprobante=nombreComprobante($codComprobante);
                            $fechaComprobante=obtenerFechaComprobante($codComprobante); 
                            $glosaComprobante=obtenerGlosaComprobante($codComprobante); 
                          }
                          $tamanioGlosa=obtenerValorConfiguracion(72); 
                          if($glosaComprobante>$tamanioGlosa){
                            $glosaComprobante=substr($glosaComprobante, 0, $tamanioGlosa);
                          }

                          if($codSimulacion!=0){
                           $nombreCliente="Sin Cliente";
                           $nombreSimulacion=nameSimulacion($codSimulacion);
                          }else{
                           $nombreCliente=nameClienteSimulacionServicio($codSimulacionServicio);
                           $nombreSimulacion=nameSimulacionServicio($codSimulacionServicio);
                          }
                          $codigoServicio="-";
                          $sql="SELECT codigo FROM ibnorca.servicios where idServicio=$idServicio";
                          $stmt1=$dbh->prepare($sql);
                          $stmt1->execute();
                           while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                             $codigoServicio=$row1['codigo'];
                           }

                       $nombreProveedor=obtenerNombreConcatenadoProveedorDetalleSolicitudRecurso($codigo);
                       $otrosPagosCuenta=comprobarCuentasOtrosPagosDeSolicitudRecursos($codigo);

                       $glosa_estadoX = preg_replace("[\n|\r|\n\r]", ", ", $glosa_estadoX);
                       $glosaArray=explode("####", $glosa_estadoX);
                       $glosa_estadoX = str_replace("####", " - ", $glosa_estadoX);



                       //retenciones y presupuesto cuentas
                       //codigos del detalle
                       $arrayPresupuesto=[];
                       $arrayRetencion=[];
                       $anioSol=strftime('%Y',strtotime($fecha));
                       $mesSol=strftime('%m',strtotime($fecha));

                       $arrayCodigos=explode(",",$grupoDetallesX);
                       for ($ic=0; $ic < count($arrayCodigos); $ic++) { 
                         $codigoDetalle=$arrayCodigos[$ic];
                         $datosDetalle=obtenerDatosDetalleSolicitudRecurso($codigoDetalle);
                        //seguimiento presupuestal   
                         $datosSeg=obtenerPresupuestoEjecucionDelServicio($datosDetalle['cod_unidadorganizacional'],$datosDetalle['cod_area'],(int)$anioSol,(int)$mesSol,obtieneNumeroCuenta($datosDetalle['cod_plancuenta']));
                         if(!($datosSeg->presupuesto==null||$datosSeg->presupuesto==0||$datosSeg->presupuesto=="")){
                            $segPres=$datosSeg->presupuesto;
                            $porcentSegPres=($datosSeg->ejecutado*100)/$datosSeg->presupuesto; 
                         }
                        $arrayPresupuesto[$ic]="<small>[".obtieneNumeroCuenta($datosDetalle['cod_plancuenta'])."] ".abrevUnidad_solo($datosDetalle['cod_unidadorganizacional'])."/".abrevArea_solo($datosDetalle['cod_area']).":".number_format($segPres, 0, '.', ',')."</small>";  

                        //retencion     
                       if($datosDetalle['cod_confretencion']!=0){
                         $tituloImporte=abrevRetencion($datosDetalle['cod_confretencion']);
                         $porcentajeRetencion=100-porcentRetencionSolicitud($datosDetalle['cod_confretencion']);
                        $montoImporte=$datosDetalle['importe']*($porcentajeRetencion/100);       
                         //if(($datosDetalle['cod_confretencion']==8)||($datosDetalle['cod_confretencion']==10)){ //validacion del descuento por retencion
                          //$montoImporte=$datosDetalle['importe'];
                         //}
                         $montoImporteRes=$datosDetalle['importe']-$montoImporte;
                      }else{
                       $tituloImporte="Ninguno";
                       $montoImporte=$datosDetalle['importe'];
                       $montoImporteRes=0; 
                      }
                      $arrayRetencion[$ic]="<small>".$tituloImporte.":".number_format($montoImporteRes, 2, '.', ',')."</small>";  

                       }//fin de for

                       $arrayPresupuesto=array_unique($arrayPresupuesto);
                       //$arrayRetencion=array_unique($arrayRetencion);

?>
                        <tr>
                          <td><small><?=$index;?></small></td>
                          <td><small><?=$unidad;?>- <?=$area;?></small></td>
                          <td class="font-weight-bold"><small><?=$numeroSol;?></small></td>
                          <td class="font-weight-bold"><small><?=$nombreComprobante;?></small></td>
                          <td class=""><small><?=$fechaComprobante;?></small></td>
                          <td class=""><small><?=$glosaComprobante;?></small></td>
                          <td><small><?=$codigoServicio;?></small></td>
                          <td><small><?=$nombreCliente;?></small></td>
                          
                          <td class="text-left"><small><?=$nombreProveedor?></small></td>
                          <td class="text-left"><small><?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?></small></td>
                          <td class="text-left"><small><?=$solicitante;?></small></td>
                          <td><small><?=strftime('%d/%m/%Y',strtotime($fecha));?></small></td>
                          <!--<td class="text-muted font-weight-bold"><small><b><?=$glosa_estadoX?></b></small></td>-->
                          <td class="text-muted font-weight-bold"><small><b><?=obtenerNombreConcatenadoEncargadoSolicitudRecurso($codigo)?></b></small></td>
                          <td class="font-weight-bold"><small><b><?=$glosa_estadoX?> <br><?=obtenerFechaCambioEstadoSolicitudRecurso($codigo)?></b></small></td>
                          <td><small><?=implode("<br>",$arrayPresupuesto)?></small></td>
                          <td><small><?=implode("<br>",$arrayRetencion)?></small></td>
                          <td><small><?=number_format(obtenerSumaDetalleSolicitud($codigo),2,'.',',')?></small></td>
                          <td class="td-actions text-right"><small><button class="btn <?=$btnEstado?> btn-sm btn-round btn-block"><?=$estado;?></button></small>
                          </td> 
                        </tr>
<?php
              $index++;
            }
?>
                            </tbody>
                       <?php if($sw_check=="0"){?>     
                            <tfoot>
                            <tr>
                              <td class="small" style="border:2px solid;" width="2%"><small><small><b>#</b></small></small></td>
                              <th class="small" style="border:2px solid;"><small><small><b>Of. - Area</b></small></small></th>
                              <th class="small" style="border:2px solid;"><small><small><b>Nº Sol.</b></small></small></th>
                              <th class="small" style="border:2px solid;"><small><small><b>Comprobante</b></small></small></th>
                              <th class="small" style="border:2px solid;"><small><small><b>Fecha</b></small></small></th>
                              <th class="small" style="border:2px solid;" width="22%"><small><small><b>Glosa</b></small></small></th>
                              <th class="small" style="border:2px solid;"><small><small><b>Cod. Servicio</b></small></small></th>
                              <th style="border:2px solid;"><small><b>Cliente</b></small></th>
                              <th class="small" style="border:2px solid;" width="12%"><small><small><b>Proveedor</b></small></small></th>
                              <th class="small" style="border:2px solid;"><small><small><b>Cuenta</b></small></small></th>
                              <th class="small" style="border:2px solid;" width="12%"><small><small><b>Solicitante</b></small></small></th>
                              <th class="small" style="border:2px solid;"><small><small><b>Fecha</b></small></small></th>
                              <!--<th class="small" style="border:2px solid;" width="16%"><small><small><b>Observaciones</b></small></small></th>-->
                              <th class="small" style="border:2px solid;" width="12%"><small><small><b>Personal Pago</b></small></small></th>
                              <th class="small" style="border:2px solid;" width="12%"><small><small><b>Observaciones</b></small></small></th>
                              <th class="small" style="border:2px solid;"><small><small><b>Presupuesto</b></small></small></th>
                              <th class="small" style="border:2px solid;"><small><small><b>Retención</b></small></small></th>
                              <th class="small" style="border:2px solid;"><small><small><b>Monto</b></small></small></th>
                              <th class="small" style="border:2px solid;"><small><small><b>Estado</b></small></small></th>
                            </tr>
                           </tfoot>
                           <?php }?> 
                        </table>
                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>

