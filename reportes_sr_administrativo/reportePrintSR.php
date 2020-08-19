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

// Preparamos
$sql="SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area 
  from solicitud_recursos sr 
  join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo 
  join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo 
  join areas a on sr.cod_area=a.codigo 
  where sr.cod_estadoreferencial=1 and (sr.cod_estadosolicitudrecurso in ($stringEstadoX)) and year(sr.fecha)=$nombre_gestion and month(sr.fecha)=$cod_mes_x 
  order by sr.numero desc";
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


?>
 <script> 
          gestion_reporte='<?=$nombre_gestion;?>';
          mes_reporte='<?=$nombre_mes;?>';
 </script>
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
                        <table id="reporte_sr" class="table table-bordered table-condensed" style="width:100%">
                          <thead>
                            <tr>
                              <th style="border:2px solid;"><small><b>Of. - Area</b></small></th>
                              <th style="border:2px solid;"><small><b>Nº Sol.</b></small></th>
                              <th style="border:2px solid;"><small><b>Cod. Servicio</b></small></th>
                              <th style="border:2px solid;"><small><b>Proveedor</b></small></th>
                              <th style="border:2px solid;"><small><b>Cuenta</b></small></th>
                              <th style="border:2px solid;" width="16%"><small><b>Solicitante</b></small></th>
                              <th style="border:2px solid;"><small><b>Fecha</b></small></th>
                              <th style="border:2px solid;" width="16%"><small><b>Observaciones</b></small></th>
                              <th style="border:2px solid;" width="16%"><small><b>Personal Pago</b></small></th>
                              <th style="border:2px solid;"><small><b>Estado</b></small></th>
                            </tr>
                          </thead>
                            <thead>
                              <!--<tr style="border:2px solid;">
                                  <th colspan="6" class="text-left"><small> Razón Social : <?=$razon_social?><br>Sucursal : <?=$sucursal?></small></th>   
                                  <th colspan="6" class="text-left"><small> Nit : <?=$nit?><br>Dirección : <?=$direccion?></small></th>   
                              </tr>-->                                
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
?>
                        <tr>
                          <td><?=$unidad;?>- <?=$area;?></td>
                          <td class="font-weight-bold"><?=$numeroSol;?></td>
                          <td><?=$codigoServicio;?></td>
                          <td><small><?=$nombreProveedor?></small></td>
                          <td><small><?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?></small></td>
                          <td><?=$solicitante;?>
                          </td>
                          <td><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                          <td class="text-muted font-weight-bold"><small><b><?=$glosa_estadoX?></b></small></td>
                          <td class="text-muted font-weight-bold"><small><b><?=obtenerNombreConcatenadoEncargadoSolicitudRecurso($codigo)?></b></small></td>
                          <td class="td-actions text-right"><button class="btn <?=$btnEstado?> btn-sm btn-round btn-block"><?=$estado;?></button>
                          </td> 
                        </tr>
<?php
              $index++;
            }
?>
                            </tbody>
                        </table>
                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>

