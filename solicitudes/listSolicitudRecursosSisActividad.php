<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalMesActivo=$_SESSION['globalMes'];
$userAdmin=obtenerValorConfiguracion(74);
$dbh = new Conexion();
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $item_3=$_GET['r'];
  $s=$_GET['s'];
  $u=$_GET['u'];

    $arraySql=explode("IdArea=",$s);
    $codigoArea=trim($arraySql[1]);
    $sqlAreas="and sr.cod_area=".$codigoArea;

    // $sqlAreas=""; quitar cuando se registre la unidad y el area de la solicitud propuesta
  ?>
  <input type="hidden" name="id_servicioibnored" value="<?=$q?>" id="id_servicioibnored"/>
  <input type="hidden" name="id_servicioibnored_rol" value="<?=$item_3?>" id="id_servicioibnored_rol"/>
  <input type="hidden" name="id_servicioibnored_s" value="<?=$s?>" id="id_servicioibnored_s"/>
  <input type="hidden" name="id_servicioibnored_u" value="<?=$u?>" id="id_servicioibnored_u"/>
<?php
}else{
  //$item_3=obtenerIdRolDeIbnorca($globalUser);
  $item_3=79;
  $s=0;
  $u=0;
  $sqlAreas="";
}



//Solicitudes SIS
// Preparamos
$stmtSIS = $dbh->prepare("SELECT l.* FROM (SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area,(select count(*) from solicitud_recursosdetalle where cod_solicitudrecurso=sr.codigo and (cod_unidadorganizacional=3000 or cod_area=1235)) as sis_detalle 
  from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo 
  where sr.cod_estadoreferencial=1 and sr.cod_estadosolicitudrecurso in (3,5,8)) l  
where (l.cod_unidadorganizacional=3000 or l.cod_area=1235 or l.sis_detalle>0) order by l.cod_comprobante,l.numero desc");
// Ejecutamos
$stmtSIS->execute();
// bindColumn
$stmtSIS->bindColumn('codigo', $codigo);
$stmtSIS->bindColumn('unidad', $unidad);
$stmtSIS->bindColumn('area', $area);
$stmtSIS->bindColumn('fecha', $fecha);
$stmtSIS->bindColumn('cod_personal', $codPersonal);
$stmtSIS->bindColumn('cod_simulacion', $codSimulacion);
$stmtSIS->bindColumn('cod_proveedor', $codProveedor);
$stmtSIS->bindColumn('cod_estadosolicitudrecurso', $codEstado);
$stmtSIS->bindColumn('estado', $estado);
$stmtSIS->bindColumn('cod_comprobante', $codComprobante);
$stmtSIS->bindColumn('cod_simulacionservicio', $codSimulacionServicio);
$stmtSIS->bindColumn('numero', $numeroSol);
$stmtSIS->bindColumn('idServicio', $idServicio);
$stmtSIS->bindColumn('glosa_estado', $glosa_estadoX);
$stmtSIS->bindColumn('revisado_contabilidad', $estadoContabilidadX);
$stmtSIS->bindColumn('devengado', $devenX);
$item_1=2708;
?>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">content_paste</i>
                  </div>
                  <h4 class="card-title"><b>Lista Solicitudes de Recursos SIS</b></h4>
                </div>
                <div class="card-body">
                     <table class="table table-condesed" id="tablePaginator100NoFidexHead">
                      <thead>
                        <tr class="bg-info text-white">
                          <th>Of. - Area</th>
                          <th>Nº Sol.</th>
                          <th>Cod. Servicio</th>
                          <!--<th>Cliente</th>-->
                          <th>Proveedor</th>
                          <th>Cuenta</th>
                          <th>Solicitante</th>
                          <th>Fecha</th>
                          <th class="text-right" width="25%">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
            $index=1;
                        while ($row = $stmtSIS->fetch(PDO::FETCH_BOUND)) {
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
                              $nEst=50;$barEstado="progress-bar-default";$btnEstado="btn-default";
                            break;
                            case 7:
                              $nEst=55;$barEstado="progress-bar-info";$btnEstado="btn-info";
                            break;
                            case 8:
                              $nEst=100;$barEstado="progress-bar-default";$btnEstado="btn-deafult";
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
                      $numeroSolTitulo='<a href="#" title="SOLICITUD DE RECURSOS SIS" class="btn btn-default btn-sm btn-round">'.$numeroSol.'</a>';

                       $nombreProveedor=obtenerNombreConcatenadoProveedorDetalleSolicitudRecurso($codigo);
                       $otrosPagosCuenta=comprobarCuentasOtrosPagosDeSolicitudRecursos($codigo);
                       $montoDetalleSoliditud=number_format(obtenerSumaDetalleSolicitud($codigo),2,'.',',');
                       $arrayEnc=implode(',',obtenerPersonalEncargadoSolicitud($codigo)[0]);

                       $glosa_estadoX = preg_replace("[\n|\r|\n\r]", ", ", $glosa_estadoX);
                       $glosaArray=explode("####", $glosa_estadoX);
                       $glosa_estadoX = str_replace("####", " - ", $glosa_estadoX);
                       if($codComprobante!=0&&($codEstado==5||$codEstado==8)){
                        $numeroSolTitulo='<a href="#" title="SOLICITUD DE RECURSOS SIS" class="btn btn-rose btn-sm btn-round">'.$numeroSol.'</a>';
                       }

                       $estiloComprobante="btn btn-primary";
                       if($devenX==0){
                          $estiloComprobante="btn btn-info";
                       }
?>
                        <tr>
                          
                          <td><?=$unidad;?>- <?=$area;?></td>
                          <td class="font-weight-bold"><?=$numeroSolTitulo;?></td>
                          <td><?=$codigoServicio;?></td>
                          <!--<td><?=$nombreCliente;?></td>-->
                          <td><small><?=$nombreProveedor?></small></td>
                          <td><small><?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?></small></td>

                          <td>
                                 <img src="assets/img/faces/persona1.png" width="20" height="20"/><?=$solicitante;?>
                          </td>
                          <td><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                          
                          
                          <td class="td-actions text-right">
                            <a title="Imprimir" href='#' onclick="javascript:window.open('<?=$urlImp;?>?sol=<?=$codigo;?>&mon=1')" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconImp;?></i>
                            </a>

                           <a title="Cambiar Actividad Proyecto" onclick="cambiarActividadesProyectoSolicitudRecursoModal(<?=$codigo?>,<?=$numeroSol?>,'<?=$montoDetalleSoliditud?>','<?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?>','<?=$nombreProveedor?>');return false;" target="_blank" class="btn btn-orange">
                                      <i class="material-icons">assignment</i>
                            </a> 
                            <div class="btn-group dropdown">
                              <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">list</i> <?=$estado;?>
                              </button>
                              <div class="dropdown-menu menu-fixed-sm-table">

                                <?php
                              if(isset($_GET['q'])){
                                ?><a href="<?=$urlVer;?>?cod=<?=$codigo;?>&comp=2&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-info">bar_chart</i> Ver Solicitud
                                 </a>
                              
                               <?php 
                              }else{
                                ?><a href="<?=$urlVer;?>?cod=<?=$codigo;?>&comp=2" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-info">bar_chart</i> Ver Solicitud
                                 </a>
                              
                                <?php   
                              }
                                 ?>
                                
                              </div>
                             </div>
                          </td> 
                        </tr>
<?php
              $index++;
            }
?>
                      </tbody>
                    </table>
                    <br><br><br><br><br><br><br><br> 
                </div>
              </div>
              <div class="card-footer fixed-bottom col-sm-9">
              </div>    
            </div>
          </div>  
        </div>
    </div>


<!-- modal devolver solicitud -->
<div class="modal fade" id="modalCambiarActividadesProyecto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background:#DA053C !important;color:#fff;">
        <h4 class="modal-title">Actividades del Proyecto</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
      </div>
      <div class="modal-body">        
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span><small>Nro.<br>Solicitud.</small></span></label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input type="text" class="form-control" name="nro_solicitud_conta_2" id="nro_solicitud_conta_2" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span><small >Cuentas</small></span></label>
          <div class="col-sm-8">
            <div class="form-group" >              
              <input type="text" class="form-control" name="cuenta_conta_2" id="cuenta_conta_2" readonly="true" style="background-color:#e2d2e0">
            </div>
          </div>
        </div> 
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span><small>Proveedor</small></span></label>
          <div class="col-sm-8">
            <div class="form-group" >
              <input type="text" class="form-control" name="proveedor_nombre_conta_2" id="proveedor_nombre_conta_2" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
           <!--<label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span><small>Monto</small></span></label>-->
          <!--<div class="col-sm-2">
            <div class="form-group" >-->
              <input type="hidden" class="form-control" name="monto_nombre_conta_2" id="monto_nombre_conta_2" readonly="true" style="background-color:#e2d2e0">              
            <!--</div>
          </div>-->
        </div>  
        <br><br>              
        <div class="row" id="solicitud_recurso_detalle_sis">
          
        </div>
        
         <input type="hidden" id="cantidad_registros_detalle_sis" value="0">
         <input type="hidden" id="codigo_solicitud" value="0">                                          
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-success" onclick="saveActividadProyectoSolicitudRecursoModal()">Guardar</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- modal reenviar solicitud devuelto -->

