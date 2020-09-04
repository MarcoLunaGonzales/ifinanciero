<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';
require_once '../styles.php';
require_once '../layouts/librerias.php';
require_once '../solicitudes/configModule.php';
$dbh = new Conexion();

$codigoSeleccionado=$_GET['codigo'];
$fila=$_GET['fila'];

$sqlEstados="and (sr.cod_estadosolicitudrecurso in (3))";//"and (sr.cod_estadosolicitudrecurso in (3))";
// Preparamos
$stmt = $dbh->prepare("SELECT l.* FROM (SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area,(select count(*) from solicitud_recursosdetalle where cod_solicitudrecurso=sr.codigo and (cod_unidadorganizacional=3000 or cod_area=1235)) as sis_detalle,(SELECT sum(importe) from solicitud_recursosdetalle where cod_solicitudrecurso=sr.codigo) as monto_solicitado  
  from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo 
  where sr.cod_estadoreferencial=1 and sr.cod_estadosolicitudrecurso in (3) order by sr.numero desc) l  
where (l.cod_unidadorganizacional=3000 or l.cod_area=1235 or l.sis_detalle>0)");

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
$stmt->bindColumn('idServicio', $idServicioX);
$stmt->bindColumn('glosa_estado', $glosa_estadoX);
$stmt->bindColumn('monto_solicitado', $monto_solicitadoX);

?>
<style>
  tfoot input {
    width: 100%;
    padding: 3px;
  }
</style> 
<table id="libreta_bancaria_reporte_modal" class="table table-condensed table-bordered table-sm" style="width:100% !important;">
    <thead>
      <tr style="background:#DA053C; color:#fff;">
        <th class="small" width="3%"><small>Unidad / Area</small></th>
        <th class="small" width="8%"><small>Nº Solicitud</small></th> 
        <th class="small" width="15%"><small>Proveedor</small></th>
        <th class="small" width="15%"><small>Cuenta</small></th> 
        <th class="small" width="10%"><small>Solicitante</small></th> 
        <th class="small" width="10%"><small>Fecha</small></th>      
        <th class="small" width="5%"><small>Estado</small></th>
        <th class="small" width="8%"><small>Monto</small></th>      
        <th class="text-center" width="3%">*</th>
      </tr>
    </thead>
    <tbody>
      <?php
            $index=1;$numeroSeleccionado="-";
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $solicitante=namePersonal($codPersonal);
                          switch ($codEstado) {
                            case 1:
                              $btnEstado="btn-default";
                            break;
                            case 2:
                              $btnEstado="btn-danger";
                            break;
                            case 3:
                              $btnEstado="btn-success";
                            break;
                            case 4:
                              $btnEstado="btn-warning";
                            break;
                            case 5:
                              $btnEstado="btn-primary";
                            break;
                            case 6:
                              $btnEstado="btn-default";
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
                          $sql="SELECT codigo FROM ibnorca.servicios where idServicio=$idServicioX";
                          $stmt1=$dbh->prepare($sql);
                          $stmt1->execute();
                           while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                             $codigoServicio=$row1['codigo'];
                           }
                       $numeroSolTitulo=$numeroSol;
                       if(verificarMontoPresupuestadoSolicitadoSR($codigo)==1){
                        $numeroSolTitulo='<a href="#" title="El Monto Solicitado es Mayor al Presupuestado" class="btn btn-warning btn-sm btn-round">'.$numeroSol.'</a>';
                       }
                       $estiloFila="";    
                       if($codigo==$codigoSeleccionado){
                        $numeroSeleccionado=$numeroSol;
                       $estiloFila="style='background:#DA053C; color:#fff;'";
                       }
?>     
<tr <?=$estiloFila?>>
                          <td><?=$unidad;?>- <?=$area;?></td>
                          <td class="font-weight-bold"><?=$numeroSolTitulo?></td>
                          <td><small><?=obtenerNombreConcatenadoProveedorDetalleSolicitudRecurso($codigo)?></small></td>
                          <td><small><?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?></small></td>
                          <td>
                                 <img src="../assets/img/faces/persona1.png" width="20" height="20"/><?=$solicitante;?>
                          </td>
                          <td><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                          <td><button class="btn <?=$btnEstado?> btn-sm btn-link"><?=$estado;?></button>
                          </td>
                          <td><?=number_format($monto_solicitadoX,2, '.', ',')?>
                          </td>  

                          <td class="td-actions text-right">
                            <div class="btn-group">
                            <a title="Imprimir" href='#' onclick="javascript:window.open('../<?=$urlImp;?>?sol=<?=$codigo;?>&mon=1')" class="btn btn-default">
                              <i class="material-icons text-dark"><?=$iconImp;?></i>
                            </a>
                            <?php 
                            if($codigo!=$codigoSeleccionado){
                             if($codEstado==3){
                            ?>
                             <a title="Seleccionar Solicitud" href='#' onclick="ponerCodigoSolicitudComprobante(<?=$fila?>,<?=$codigo;?>)" class="<?=$buttonEdit;?>">
                              <i class="material-icons">check_box</i>
                             </a>
                            <?php 
                             }else{
                             ?>
                             <a title="No se puede seleccionar el item" href='#' class="btn btn-danger">
                              <i class="material-icons">check_box</i>
                             </a>
                            <?php 
                             }  
                            }
                           ?>    
                         </div>
                          </td>
                        </tr>
<?php
              $index++;
            }
?>
    </tbody>
    <tfoot>
      <tr style="background:#DA053C; color:#fff;">
        <th class="small" width="3%"><small>Unidad / Area</small></th>
        <th class="small" width="8%"><small>Nº Solicitud</small></th> 
        <th class="small" width="15%"><small>Proveedor</small></th>
        <th class="small" width="15%"><small>Cuenta</small></th> 
        <th class="small" width="10%"><small>Solicitante</small></th> 
        <th class="small" width="10%"><small>Fecha</small></th>      
        <th class="small" width="5%"><small>Estado</small></th>
        <th class="small" width="8%"><small>Monto</small></th>      
        <td class="text-center" width="3%">*</td>        
      </tr>
    </tfoot>
</table>
<script>$("#numero_solicitud_relacionado").html('SR: <b class="text-rose">'+<?=$numeroSeleccionado?>+'</b>');
$("#numero_badge_sr").html(<?=$numeroSeleccionado?>);</script>