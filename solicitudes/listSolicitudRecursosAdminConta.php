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
// Preparamos
$stmt = $dbh->prepare("SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo where sr.cod_estadoreferencial=1 and (sr.cod_estadosolicitudrecurso in (3)) order by sr.revisado_contabilidad,sr.numero desc");
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
                  <h4 class="card-title"><b>Contabilización de <?=$moduleNamePlural?></b> - Mes y Gestión de Trabajo <b style="color:#FF0000;">[<?=nombreMes($globalMesActivo);?> - <?=$globalNombreGestion?>]</b></h4>
                </div>
                <div class="card-body">
                    <table class="table table-condesed" id="tablePaginator100">
                      <thead>
                        <tr>
                          <th>Of. - Area</th>
                          <th>Nº Sol.</th>
                          <th>Cod. Servicio</th>
                          <!--<th>Cliente</th>-->
                          <th>Proveedor</th>
                          <th>Cuenta</th>
                          <th>Solicitante</th>
                          <th>Fecha</th>
                          <th>Observaciones</th>
                          <th>Personal Pago</th>
                          <th class="text-right" width="25%">Actions</th>
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
                              $nEst=50;$barEstado="progress-bar-default";$btnEstado="btn-default";
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
                      $numeroSolTitulo=$numeroSol;
                       if(verificarMontoPresupuestadoSolicitadoSR($codigo)==1){
                        $numeroSolTitulo='<a href="#" title="El Monto Solicitado es Mayor al Presupuestado" class="btn btn-warning btn-sm btn-round">'.$numeroSol.'</a>';
                       }

                       $nombreProveedor=obtenerNombreConcatenadoProveedorDetalleSolicitudRecurso($codigo);
                       $otrosPagosCuenta=comprobarCuentasOtrosPagosDeSolicitudRecursos($codigo);
                       $montoDetalleSoliditud=number_format(obtenerSumaDetalleSolicitud($codigo),2,'.',',');
                       $arrayEnc=implode(',',obtenerPersonalEncargadoSolicitud($codigo)[0]);

                       $glosa_estadoX = preg_replace("[\n|\r|\n\r]", ", ", $glosa_estadoX);
                       $glosaArray=explode("####", $glosa_estadoX);
                       $glosa_estadoX = str_replace("####", " - ", $glosa_estadoX);
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
                          <td class="text-muted font-weight-bold"><small><b><?php if(isset($glosaArray[1])){
                                echo "".$glosaArray[0].""."<u class='text-muted'> ".$glosaArray[1]."</u>";
                            }else{
                                echo $glosa_estadoX;
                            }?></b></small></td>
                          <td class="text-muted font-weight-bold"><small><b><?=obtenerNombreConcatenadoEncargadoSolicitudRecurso($codigo)?></b></small></td>
                          <td class="td-actions text-right">
                            <a title="Imprimir" href='#' onclick="javascript:window.open('<?=$urlImp;?>?sol=<?=$codigo;?>&mon=1')" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconImp;?></i>
                            </a>
                            <?php 
                            if($estadoContabilidadX==1){
                              if(isset($_GET['q'])){
                                ?>
                                <a title="Quitar la Revisión" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=10&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>'  class="btn btn-rose" style="background:#661E1B">
                                       <i class="material-icons">check_box</i><!--check_box-->
                                </a>
                                <?php
                              }else{
                                ?>
                                <a title="Quitar la Revisión" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=10'  class="btn btn-rose" style="background:#661E1B">
                                       <i class="material-icons">check_box</i><!--check_box-->
                                </a>
                                <?php
                              }
                            }else{
                              $iconRevisado="check_box_outline_blank";
                              $estiloIconRevisado="btn-default";
                              if($estadoContabilidadX==2){
                                $iconRevisado="adjust";
                                $estiloIconRevisado="btn-info";
                              }
                              if(isset($_GET['q'])){
                                ?>
                                <a title="Marcar como Revisado" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=11&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>'  class="btn <?=$estiloIconRevisado?>">
                                       <i class="material-icons"><?=$iconRevisado?></i>
                                </a>
                                <?php
                              }else{
                                ?>
                                <a title="Marcar como Revisado" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=11'  class="btn <?=$estiloIconRevisado?>">
                                       <i class="material-icons"><?=$iconRevisado?></i>
                                </a>
                                <?php
                              }
                            }
                            
                            if($codEstado==4){
                              if(isset($_GET['q'])){
                                ?>
                                <a title="Volver al Estado Registro" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>'  class="btn btn-danger">
                                       <i class="material-icons">refresh</i>
                                </a>
                                <?php
                              }else{
                                ?>
                                <a title="Volver al Estado Registro" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1'  class="btn btn-danger">
                                       <i class="material-icons">refresh</i>
                                </a>
                                <?php
                              }
                            }

                                   if($codComprobante!=0&&$codEstado==5){
                                   ?>
                                   <div class="btn-group dropdown">
                                     <button type="button" class="btn btn-primary dropdown-toggle" title="COMPROBANTE - DEVENGADO" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       <i class="material-icons"><?=$iconImp;?></i>
                                     </button>
                                    <div class="dropdown-menu">
                                       <a href="#" onclick="javascript:window.open('<?=$urlImpComp;?>?comp=<?=$codComprobante;?>&mon=-1')" class="dropdown-item">
                                                 <i class="material-icons text-muted">monetization_on</i> BIMONETARIO (Bs - Usd)
                                      </a>
                                      <div class="dropdown-divider"></div>
                                      <?php
                                        $stmtMoneda = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM monedas where cod_estadoreferencial=1 order by 2");
                                       $stmtMoneda->execute();
                                       while ($row = $stmtMoneda->fetch(PDO::FETCH_ASSOC)) {
                                         $codigoX=$row['codigo'];
                                         $nombreX=$row['nombre'];
                                         $abrevX=$row['abreviatura'];
                                            ?>
                                             <a href="#" onclick="javascript:window.open('<?=$urlImpComp;?>?comp=<?=$codComprobante;?>&mon=<?=$codigoX?>')" class="dropdown-item">
                                                 <i class="material-icons">keyboard_arrow_right</i> <?=$abrevX?>
                                             </a> 
                                           <?php
                                         }
                                         ?>
                                    </div>
                                  </div> 
                                   <?php       
                                   }
                              ?>
                            <div class="btn-group dropdown">
                              <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">list</i> <?=$estado;?>
                              </button>
                              <div class="dropdown-menu menu-fixed-sm-table">
                                <?php
                              if(isset($_GET['q'])){
                                ?><a href="<?=$urlVer;?>?cod=<?=$codigo;?>&admin=2&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-info">bar_chart</i> Ver Solicitud
                                 </a>
                              
                                <?php 
                                if($codEstado==4){
                                 ?>
                                 <a href="#" onclick="mostrarCambioEstadoObjeto(<?=$codigo?>)" class="dropdown-item">
                                    <i class="material-icons text-warning">dns</i> Cambiar Estado
                                 </a>
                                 <a href="<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&admin=2&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>&v=<?=$idServicio?>" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-success">edit</i> Editar Solicitud
                                 </a><?php 
                                }else{
                                  if($codEstado==3){
                                    ?>
                                    <a href="<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&admin=2&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>&v=<?=$idServicio?>" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-success">edit</i> Editar Solicitud
                                   </a>
                                   <?php 
                                  if($otrosPagosCuenta==0){
                                    ?>
                                   <a title="Contabilizar Solicitud" onclick="alerts.showSwal('contabilizar-solicitud-recurso','<?=$urlConta?>?admin=0&cod=<?=$codigo?>&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>&v=<?=$idServicio?>')" href='#'  class="dropdown-item">
                                      <i class="material-icons text-danger">assignment_turned_in</i> Contabilizar Solicitud
                                    </a>
                                    <?php
                                  }else{
                                    ?>
                                   <a title="Contabilizar Solicitud"  href="#" onclick="javascript:window.open('<?=$urlRegisterCompro;?>')"  class="dropdown-item">
                                      <i class="material-icons text-warning">assignment_turned_in</i> Contabilización Manual Solicitud
                                    </a>
                                    <?php
                                  }
                                  }else{
                                  ?><a href="#" onclick="mostrarCambioEstadoObjeto(<?=$codigo?>)" class="dropdown-item">
                                    <i class="material-icons text-warning">dns</i> Cambiar Estado
                                 </a><?php  
                                  }
                                ?>
                                 <?php 
                                }
                              }else{
                                ?><a href="<?=$urlVer;?>?cod=<?=$codigo;?>&admin=2" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-info">bar_chart</i> Ver Solicitud
                                 </a>
                              
                                <?php 
                                if($codEstado==4){
                                 ?>
                                 <a href="#" onclick="mostrarCambioEstadoObjeto(<?=$codigo?>)" class="dropdown-item">
                                    <i class="material-icons text-warning">dns</i> Cambiar Estado
                                 </a>
                                 <a href="<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&admin=2" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-success">edit</i> Editar Solicitud
                                 </a>
                                 <?php 
                                }else{
                                  if($codEstado==3){
                                    ?>
                                    <a href="<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&admin=2" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-success">edit</i> Editar Solicitud
                                   </a>
                                   <a onclick="devolverSolicitudRecurso(<?=$numeroSol?>,'<?=$codigoServicio?>','<?=$urlEdit2?>?cod=<?=$codigo?>&conta=2&estado=1','<?=$nombreProveedor?>')" href="#" class="dropdown-item">
                                    <i class="material-icons text-danger">reply</i> Devolver Solicitud
                                  </a>
                                   <?php 
                                  if($otrosPagosCuenta==0){
                                    
                                    ?>
                                    <!--onclick="alerts.showSwal('contabilizar-solicitud-recurso','<?=$urlConta?>?admin=0&cod=<?=$codigo?>')"-->
                                   <a title="Contabilizar Solicitud" onclick="contabilizarSolicitudRecursoModal(<?=$codigo?>,1,<?=$numeroSol?>,'<?=$montoDetalleSoliditud?>','<?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?>','<?=$urlConta?>?admin=0&cod=<?=$codigo?>','<?=$nombreProveedor?>','<?=$arrayEnc?>');return false;" href='#'  class="dropdown-item">
                                      <i class="material-icons text-danger">assignment_turned_in</i> Contabilizar Solicitud
                                    </a>
                                    <?php
                                  }else{
                                    ?>
                                   <a title="Contabilizar Solicitud"  href="#" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlEdit2?>?cod=<?=$codigo?>&conta=2&estado=5')" class="dropdown-item">
                                      <i class="material-icons text-dark">dns</i> <b class="text-muted">Cambiar a <u class="text-dark">Contabilizado</u></b>
                                    </a>
                                    <a title="Pagar Solicitud"  href="#" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlEdit2?>?cod=<?=$codigo?>&conta=2&estado=8')" class="dropdown-item">
                                      <i class="material-icons text-warning">dns</i> <b class="text-muted">Cambiar a <u class="text-warning">Pagado</u></b>
                                    </a>
                                    <?php
                                   }
                                  }else{
                                  ?><a href="#" onclick="mostrarCambioEstadoObjeto(<?=$codigo?>)" class="dropdown-item">
                                    <i class="material-icons text-warning">dns</i> Cambiar Estado
                                 </a><?php  
                                  }

                                }
                                
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
                <a href="#" onclick="abrirModal('modalListSolEliminados');moverModal('modalListSolEliminados');" class="btn btn-info float-right"><i class="material-icons">history</i> <small id="cantidad_eliminados"></small> Histórico</a>
              </div>    
            </div>
          </div>  
        </div>
    </div>
    <!-- small modal -->
<div class="modal fade modal-arriba modal-primary" id="modalEstadoObjeto" tabindex="-1" style="z-index:99999" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice" style="max-width: 50% !important;">
    <div class="modal-content card">
                <div class="card-header card-header-warning card-header-text">
                  <div class="card-text">
                    <h4>Cambiar de Estado</h4>
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <input type="hidden" class="form-control" name="modal_adminconta" id="modal_adminconta" value="">
                <input type="hidden" class="form-control" name="modal_codigopropuesta" id="modal_codigopropuesta" value="">
                <input type="hidden" class="form-control" name="modal_tipoobjeto" id="modal_tipoobjeto" value="<?=$item_1?>">
                <input type="hidden" class="form-control" name="modal_rolpersona" id="modal_rolpersona" value="<?=$item_3?>">
                <div class="card-body">
                 <div class="card-body">
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Estado</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                             <select class="selectpicker form-control" name="modal_codigoestado" id="modal_codigoestado" data-style="btn btn-primary">
                                  
                             </select>
                         </div>
                        </div>
                      </div>
                      <div class="row">
                          <label class="col-sm-2 col-form-label">Observaciones</label>
                           <div class="col-sm-10">                     
                             <div class="form-group">
                               <textarea type="text" class="form-control" name="modal_observacionesestado" id="modal_observacionesestado"></textarea>
                             </div>
                           </div>  
                      </div> 
                      <div class="form-group float-right">
                        <button type="button" id="boton_guardarsim" class="btn btn-default" onclick="cambiarEstadoObjetoSol()">Cambiar Estado</button>
                      </div> 
                </div>   
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->

<?php
$stmt = $dbh->prepare("SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo where sr.cod_estadoreferencial=1 and (sr.cod_estadosolicitudrecurso in (5,8)) order by sr.numero desc");
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

$item_1=2708;
?>
<!-- small modal -->
<div class="modal fade modal-primary" id="modalListSolEliminados" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
              <div class="card-header card-header-info card-header-icon">
                <div class="card-icon">
                  <i class="material-icons">history</i>
                </div>
                <h4 class="card-title">Solicitudes Recursos</h4>
              </div>

              <div class="card-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="material-icons">close</i>
              </button>
              <div class="row" id="div_cabecera" >
                    
              </div>
                <table class="table table-condesed" id="tablePaginator100NoFidexHead">
                      <thead>
                        <tr class="bg-info">
                          <th>Of. - Area</th>
                          <th>Nº Sol.</th>
                          <th>Cod. Servicio</th>
                          <!--<th>Cliente</th>-->
                          <th>Proveedor</th>
                          <th>Cuenta</th>
                          <th>Solicitante</th>
                          <th>Fecha</th>
                          <th>Personal Pago</th>
                          <th class="text-right">Actions</th>
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
                      $numeroSolTitulo=$numeroSol;
                       if(verificarMontoPresupuestadoSolicitadoSR($codigo)==1){
                        $numeroSolTitulo='<a href="#" title="El Monto Solicitado es Mayor al Presupuestado" class="btn btn-warning btn-sm btn-round">'.$numeroSol.'</a>';
                       }
                       $otrosPagosCuenta=comprobarCuentasOtrosPagosDeSolicitudRecursos($codigo);
                       $montoDetalleSoliditud=number_format(obtenerSumaDetalleSolicitud($codigo),2,'.',',');
                       $arrayEnc=implode(',',obtenerPersonalEncargadoSolicitud($codigo)[0]);

                       $nombreProveedor=obtenerNombreConcatenadoProveedorDetalleSolicitudRecurso($codigo);
                       
?>
                        <tr>
                          <td><?=$unidad;?> - <?=$area;?></td>
                          <td class="font-weight-bold"><?=$numeroSolTitulo;?></td>
                          <td><?=$codigoServicio;?></td>
                          <!--<td><?=$nombreCliente;?></td>-->
                          <td><small><?=obtenerNombreConcatenadoProveedorDetalleSolicitudRecurso($codigo)?></small></td>
                          <td><small><?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?></small></td>
                          <td>
                                 <img src="assets/img/faces/persona1.png" width="20" height="20"/><?=$solicitante;?>
                          </td>
                          <td><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                          <td class="text-muted font-weight-bold"><small><b><?=obtenerNombreConcatenadoEncargadoSolicitudRecurso($codigo)?></b></small></td>
                          <td class="td-actions text-right">
                            <a title="Imprimir" href='#' onclick="javascript:window.open('<?=$urlImp;?>?sol=<?=$codigo;?>&mon=1')" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconImp;?></i>
                            </a>
                            <?php 
                            
                                   if($codComprobante!=0&&$codEstado==5){
                                   ?>
                                   <div class="btn-group dropdown">
                                     <button type="button" class="btn btn-primary dropdown-toggle" title="COMPROBANTE - DEVENGADO" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       <i class="material-icons"><?=$iconImp;?></i>
                                     </button>
                                    <div class="dropdown-menu">
                                       <a href="#" onclick="javascript:window.open('<?=$urlImpComp;?>?comp=<?=$codComprobante;?>&mon=-1')" class="dropdown-item">
                                                 <i class="material-icons text-muted">monetization_on</i> BIMONETARIO (Bs - Usd)
                                      </a>
                                      <div class="dropdown-divider"></div>
                                      <?php
                                        $stmtMoneda = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM monedas where cod_estadoreferencial=1 order by 2");
                                       $stmtMoneda->execute();
                                       while ($row = $stmtMoneda->fetch(PDO::FETCH_ASSOC)) {
                                         $codigoX=$row['codigo'];
                                         $nombreX=$row['nombre'];
                                         $abrevX=$row['abreviatura'];
                                            ?>
                                             <a href="#" onclick="javascript:window.open('<?=$urlImpComp;?>?comp=<?=$codComprobante;?>&mon=<?=$codigoX?>')" class="dropdown-item">
                                                 <i class="material-icons">keyboard_arrow_right</i> <?=$abrevX?>
                                             </a> 
                                           <?php
                                         }
                                         ?>
                                    </div>
                                  </div> 
                                   <?php 
                                   //opciones Admin
                                    if(verificarEdicionComprobanteUsuario($globalUser)!=0){
                                    ?>
                                    <a title="Editar Personal Procesar Pago" onclick="contabilizarSolicitudRecursoModal(<?=$codigo?>,2,<?=$numeroSol?>,'<?=$montoDetalleSoliditud?>','<?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?>','<?=$urlEncargado?>?admin=0&cod=<?=$codigo?>','<?=$nombreProveedor?>','<?=$arrayEnc?>');return false;" target="_blank" class="btn btn-default">
                                      <i class="material-icons text-dark">people_alt</i>
                                    </a> 
                                    <?php  
                                      if(verificarMesEnCursoSolicitudRecursos($codigo)!=0){

                                    ?>
                                    <a title="Editar Solicitud" href="<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&admin=2" target="_blank" class="btn btn-warning">
                                      <i class="material-icons text-dark">vpn_key</i><i class="material-icons text-dark">lock_open</i>
                                    </a>
                                    
                                    <?php 
                                    if($otrosPagosCuenta==0){
                                    ?>
                                   <a title="Contabilizar Solicitud" onclick="contabilizarSolicitudRecursoModal(<?=$codigo?>,1,<?=$numeroSol?>,'<?=$montoDetalleSoliditud?>','<?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?>','<?=$urlConta?>?admin=0&cod=<?=$codigo?>&existe=<?=$codComprobante?>','<?=$nombreProveedor?>','<?=$arrayEnc?>');return false;" href='#'  class="btn btn-danger">
                                      <i class="material-icons">assignment_turned_in</i>
                                    </a>
                                    <?php
                                      } 
                                     }else{//if mes en curso
                                       //si tiene permisos pero no está en el mes en curso
                                      ?><a title="Solicitud No Editable" href='#'  class="btn btn-danger">
                                      <i class="material-icons text-dark">lock</i>
                                    </a><?php
                                      }
                                    }     
                                   }
                              ?>
                            <div class="btn-group dropdown">
                              <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">list</i> <?=$estado;?>
                              </button>
                              <div class="dropdown-menu">
                                <?php
                              if(isset($_GET['q'])){
                                ?><a href="<?=$urlVer;?>?cod=<?=$codigo;?>&admin=2&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>" target="_blank"  class="dropdown-item">
                                    <i class="material-icons text-info">bar_chart</i> Ver Solicitud
                                 </a>
                              
                                <?php 
                                if($codEstado==4){
                                 ?>
                                 <a href="#" targetonclick="mostrarCambioEstadoObjeto(<?=$codigo?>)" class="dropdown-item">
                                    <i class="material-icons text-warning">dns</i> Cambiar Estado
                                 </a>
                                 <a targethref="<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&admin=2&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>&v=<?=$idServicio?>" class="dropdown-item" target="_blank">
                                    <i class="material-icons text-success">edit</i> Editar Solicitud
                                 </a><?php 
                                }else{
                                  ?>
                                 
                                 <?php
                                ?>
                                 <?php 
                                }
                              }else{
                                ?><a href="<?=$urlVer;?>?cod=<?=$codigo;?>&admin=2" class="dropdown-item" target="_blank">
                                    <i class="material-icons text-info">bar_chart</i> Ver Solicitud
                                 </a>
                                 <?php 
                                if($otrosPagosCuenta>0&&$codEstado!=8){
                                 ?>
                                 <a title="Pagar Solicitud"  href="#" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlEdit2?>?cod=<?=$codigo?>&conta=2&estado=8')" class="dropdown-item">
                                      <i class="material-icons text-warning">dns</i> <b class="text-muted">Cambiar a <u class="text-warning">Pagado</u></b>
                                    </a>
                                <?php 
                                  
                                }
                                if($codEstado==4){
                                 ?>
                                 <a href="#" onclick="mostrarCambioEstadoObjeto(<?=$codigo?>)" class="dropdown-item">
                                    <i class="material-icons text-warning">dns</i> Cambiar Estado
                                 </a>
                                 <a href="<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&admin=2" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-success">edit</i> Editar Solicitud
                                 </a><?php 
                                }else{
                                ?>
                                 <?php 
                                }
                                
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
              </div>
             <script>$("#cantidad_eliminados").html("("+<?=$index-1?>+")");</script> 
    </div>  
  </div>
</div>
<!--    end small modal -->

<!-- modal devolver solicitud -->
<div class="modal fade" id="modalDevolverSolicitudRecurso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Rechazar Solicitud</h4>
      </div>
      <div class="modal-body">        
        <input type="hidden" name="urlEnvioModal" id="urlEnvioModal" value="">
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id="campo_nro_fact"><small>Nro.<br>Solicitud.</small></span></label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input type="text" class="form-control" name="nro_solicitud" id="nro_solicitud" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id="campo_rs_fact"><small >Código<br>Servicio</small></span></label>
          <div class="col-sm-8">
            <div class="form-group" >              
              <input type="text" class="form-control" name="codigo_servicio" id="codigo_servicio" readonly="true" style="background-color:#e2d2e0">
            </div>
          </div>
        </div> 
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id="campo_proveedor"><small>Proveedor</small></span></label>
          <div class="col-sm-11">
            <div class="form-group" >
              <input type="text" class="form-control" name="proveedor_nombre" id="proveedor_nombre" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
        </div>                
        <div class="row">
          <label class="col-sm-12 col-form-label" style="color:#7e7e7e"><small>Observaciones</small></label>
        </div>
        <div class="row">
          <div class="col-sm-12" style="background-color:#f9edf7">
            <div class="form-group" >              
              <textarea type="text" name="observaciones_modal" id="observaciones_modal" class="form-control" required="true"></textarea>
            </div>
          </div>
        </div>        
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-success" onclick="devolverSolicitudRecursoModal()">Aceptar</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- modal reenviar solicitud devuelto -->


<!-- modal devolver solicitud -->
<div class="modal fade" id="modalContabilizarSolicitudRecurso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" id="cabecera_conta" style="background:#DA053C !important;color:#fff;">
        <h4 class="modal-title" id="titulo_conta">Contabilizar Solicitud Recurso</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
      </div>
      <div class="modal-body">        
        <input type="hidden" name="urlEnvioModalConta" id="urlEnvioModalConta" value="">
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id="campo_nro_fact_conta"><small>Nro.<br>Solicitud.</small></span></label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input type="text" class="form-control" name="nro_solicitud_conta" id="nro_solicitud_conta" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id="campo_cuentas_conta"><small >Cuentas</small></span></label>
          <div class="col-sm-8">
            <div class="form-group" >              
              <input type="text" class="form-control" name="cuenta_conta" id="cuenta_conta" readonly="true" style="background-color:#e2d2e0">
            </div>
          </div>
        </div> 
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id="campo_proveedor_conta"><small>Proveedor</small></span></label>
          <div class="col-sm-8">
            <div class="form-group" >
              <input type="text" class="form-control" name="proveedor_nombre_conta" id="proveedor_nombre_conta" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
           <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id="campo_monto_conta"><small>Monto</small></span></label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input type="text" class="form-control" name="monto_nombre_conta" id="monto_nombre_conta" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
        </div>                
        <div class="row">
          <label class="col-sm-12 col-form-label" style="color:#7e7e7e"><small>Persona que Procesará el Pago</small></label>
        </div>
        <div class="row">
          <div class="col-sm-12" style="background-color:#f9edf7">
            <div class="form-group" >              
              <select class="selectpicker form-control form-control-sm" name="personal_encargado" id="personal_encargado" data-live-search="true" data-size="6" data-style="btn btn-default text-dark">
                         <option value="-1">Ninguno</option>
                        <?php 
                         $stmt3 = $dbh->prepare("SELECT p.codigo,CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno)as nombre from personal p join configuracion_encargado c on c.cod_personal=p.codigo order by 2");
                         $stmt3->execute();
                          while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                           $codigoSel=$rowSel['codigo'];
                          $nombreSelX=$rowSel['nombre'];
                          ?><option value="<?=$codigoSel;?>"><?=$nombreSelX?></option><?php 
                          }
                        ?>
                    </select>
            </div>
          </div>
        </div>        
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-success" onclick="saveContaSolicitudRecursoModal()">Guardar</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- modal reenviar solicitud devuelto -->