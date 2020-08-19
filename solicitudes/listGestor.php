<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$globalArea=$_SESSION["globalArea"];
$dbh = new Conexion();
if(isset($_GET['q'])){
  $q=$_GET['q'];
}else{
  $q=0;
}


if($q==123456){
// Preparamos
$stmt = $dbh->prepare("SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area 
  from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo 
  where sr.cod_estadoreferencial=1 and (sr.cod_estadosolicitudrecurso in (1)) order by sr.numero desc");
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

?>
<style>
  body{
    background:#23183D !important;color:#ffffff !important;
  }
</style>
<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card" style="background:#143A56 !important;color:#ffffff !important;">
                <div class="card-header card-header-default card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">content_paste</i>
                  </div>
                  <h4 class="card-title"><b class="text-white"><?=$moduleNamePlural?> - <b class="text-danger">ADMIN</b></b></h4>
                </div>
                <div class="card-body">
                    <table class="table table-condesed" id="tablePaginator">
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
                          <th>Estado</th>
                          <th>Observaciones</th>
                          <th class="text-right" width="18%">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
            $index=1;
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
?>
                        <tr>
                          <td><?=$unidad;?>- <?=$area;?></td>
                          <td class="font-weight-bold"><?=$numeroSolTitulo?></td>
                          <td><?=$codigoServicio;?></td>
                          <!--<td><?=$nombreCliente;?></td>-->
                          <td><small><?=obtenerNombreConcatenadoProveedorDetalleSolicitudRecurso($codigo)?></small></td>
                          <td><small><?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?></small></td>
                          <td>
                                 <img src="assets/img/faces/persona1.png" width="20" height="20"/><?=$solicitante;?>
                          </td>
                          <td><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                          <td><button class="btn <?=$btnEstado?> btn-sm btn-link"><?=$estado;?></button> <!--<?=$nEst?> %
                             <div class="progress">
                               <div class="progress-bar <?=$barEstado?>" role="progressbar" aria-valuenow="<?=$nEst?>" aria-valuemin="0" aria-valuemax="100" style="width:<?=$nEst?>%">
                                  <span class="sr-only"><?=$nEst?>% Complete</span>
                               </div>
                             </div>-->
                          </td> 
                          <td class="text-danger font-weight-bold"><small><b><?=$glosa_estadoX?></b></small></td>
                          <td class="td-actions text-right">
                            <?php
                              if($codEstado==4||$codEstado==3||$codEstado==5){
                            ?>
                            <a title="Imprimir" href='#' onclick="javascript:window.open('<?=$urlImp;?>?sol=<?=$codigo;?>&mon=1')" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconImp;?></i>
                            </a>
                            <?php 
                                   if($codComprobante!=0&&$codEstado==3){
                                   ?>
                                   <div class="btn-group dropdown">
                                     <button type="button" class="btn btn-primary dropdown-toggle" title="COMPROBANTE - DEVENGADO" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       <i class="material-icons"><?=$iconImp;?></i>
                                     </button>
                                    <div class="dropdown-menu">
                                      <?php
                                        $stmtMoneda = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM monedas where cod_estadoreferencial=1 order by 2");
                                       $stmtMoneda->execute();
                                       while ($row = $stmtMoneda->fetch(PDO::FETCH_ASSOC)) {
                                         $codigoX=$row['codigo'];
                                         $nombreX=$row['nombre'];
                                         $abrevX=$row['abreviatura'];
                                            ?>
                                             <a href="#" onclick="javascript:window.open('<?=$urlImpComp;?>?comp=<?=$codComprobante;?>&mon=<?=$codigoX?>')" class="dropdown-item">
                                                 <i class="material-icons">list_alt</i> <?=$abrevX?>
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
                              if($codEstado==4){
                                 ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="dropdown-item">
                                    <i class="material-icons text-danger">reply</i> Descartar Envío
                                 </a><?php 
                                 }else{
                                   ?>
                                   <!--<a href="<?=$urlPagos;?>&codigo=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="dropdown-item">
                                    <i class="material-icons text-success">attach_money</i> PAGOS
                                   </a>-->
                                   <?php 
                                 }
                                 ?>
                                 <a href="<?=$urlVer;?>?cod=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="dropdown-item">
                                    <i class="material-icons text-warning">bar_chart</i> Ver Solicitud
                                 </a>
                             <?php
                            }else{
                               if($codEstado==4){
                                 ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&admin=0" class="dropdown-item">
                                    <i class="material-icons text-danger">reply</i> Descartar Envío
                                 </a><?php 
                                 }else{
                                   ?>
                                   <!--<a href="<?=$urlPagos;?>&codigo=<?=$codigo;?>" class="dropdown-item">
                                    <i class="material-icons text-success">attach_money</i> PAGOS
                                   </a>-->
                                   <?php 
                                 }
                                 ?>
                                 <a href="<?=$urlVer;?>?cod=<?=$codigo;?>" class="dropdown-item">
                                    <i class="material-icons text-warning">bar_chart</i> Ver Solicitud
                                 </a>
                             <?php  
                            }    
                           ?>       
                              </div>
                            </div>                           
                            <?php    
                              }else{
                                if($codEstado==6){
                                  //para el envio a administracion
                                  ?><a title="Solicitud Enviada" href='#' class="btn btn-warning">
                                     Enviado
                                    </a><?php
                                }else{
                                 if(isset($_GET['q'])){
                                   ?>
                                    <a title="Enviar a Autorización - Solicitud Recursos" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=6&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>' class="btn btn-default">
                                      <i class="material-icons">send</i>
                                    </a>
                                   <?php
                                  }else{
                                    ?>
                                     <a title="Enviar a Autorización - Solicitud Recursos" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=6&admin=0'  class="btn btn-default">
                                       <i class="material-icons">send</i>
                                     </a>
                                    <?php
                                  }                                   
                                }
                            if(isset($_GET['q'])){
                              ?>
                            <a title="Imprimir" href='#' onclick="javascript:window.open('<?=$urlImp;?>?sol=<?=$codigo;?>&mon=1')" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconImp;?></i>
                            </a>
                            <a title=" Ver Solicitud de Recursos" target="_blank" href="<?=$urlVer;?>?cod=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="btn btn-success">
                                    <i class="material-icons">preview</i>
                            </a> 
                            <?php
                             if($codEstado==1){
                             ?>
                            <a title="Editar solicitud - detalle" href='<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>'  class="btn btn-info">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            
                            <button title="Eliminar solicitud"  class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button>
                              <?php  
                              
                             }
                            }else{
                              ?>
                            <a title="Imprimir" href='#' onclick="javascript:window.open('<?=$urlImp;?>?sol=<?=$codigo;?>&mon=1')" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconImp;?></i>
                            </a>
                            <a title=" Ver Solicitud de Recursos" target="_blank" href="<?=$urlVer;?>?cod=<?=$codigo;?>" class="btn btn-success">
                                    <i class="material-icons">preview</i>
                            </a>
                            <?php
                             if($codEstado==1){
                             ?>
                            <a title="Editar solicitud - detalle" href='<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>'  class="btn btn-info">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <?php
                             if(!($idServicioX>0)){
                               ?>
                            <button title="Eliminar solicitud"  class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button>
                              <?php
                              }
                             
                             } 
                            }    
                              
                           }
                            ?>
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
              <div class="card-footer fixed-bottom col-sm-9" style="background:#143A56 !important;color:#ffffff !important;">
                <?php
                $codUrl="";$codUrl2=""; 
                if(isset($_GET['cod_sim'])){
                 $codUrl="&sim=$codSimCosto&det=1";
                 $codUrl2="?sim=$codSimCosto&det=1";
                }

                if(isset($_GET['q'])){
                ?><a href="<?=$urlRegister3;?>?q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?><?=$codUrl?>" target="_self" class="<?=$buttonNormal;?>">Registrar</a><?php
                }else{
                 ?><a href="#" onclick="javascript:window.open('<?=$urlRegister3;?><?=$codUrl2?>')" class="<?=$buttonNormal;?>">Registrar</a>
                  <?php
                } 
                ?>
                
                <a href="#" onclick="abrirModal('modalListSolEliminados');moverModal('modalListSolEliminados');" class="btn btn-danger float-right"><i class="material-icons"><?=$iconDelete;?></i> <small id="cantidad_eliminados"></small> Eliminados</a>
                <a href="#" onclick="abrirModal('modalListSolHistorial');moverModal('modalListSolHistorial');" class="btn btn-info float-right"><i class="material-icons">history</i> <small id="cantidad_historico"></small> Histórico</a>
              </div>      
            </div>
          </div>  
        </div>
    </div>


<?php
$stmt = $dbh->prepare("SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area 
  from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo 
  where sr.cod_estadoreferencial=2 order by sr.numero desc");
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
?>
<!-- small modal -->
<div class="modal fade modal-primary" id="modalListSolEliminados" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card" style="background:#23183D !important;color:#ffffff !important;">
              <div class="card-header card-header-danger card-header-icon">
                <div class="card-icon">
                  <i class="material-icons"><?=$iconDelete;?></i>
                </div>
                <h4 class="card-title">Solicitudes Eliminadas</h4>
              </div>

              <div class="card-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="material-icons">close</i>
              </button>
              <div class="row" id="div_cabecera" >
                    
              </div>
                <table class="table table-condesed" id="tablePaginatorHead">
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
                          <th>Estado</th>
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
                              $btnEstado="btn-warning";
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
                          <td><button class="btn <?=$btnEstado?> btn-sm btn-link"><?=$estado;?></button> <!--<?=$nEst?> %
                             <div class="progress">
                               <div class="progress-bar <?=$barEstado?>" role="progressbar" aria-valuenow="<?=$nEst?>" aria-valuemin="0" aria-valuemax="100" style="width:<?=$nEst?>%">
                                  <span class="sr-only"><?=$nEst?>% Complete</span>
                               </div>
                             </div>-->
                          </td> 
                          <td class="td-actions text-right">
                            <?php
                              if(isset($_GET['q'])){
                              ?>
                            <button title="Restaurar Solicitud Recurso"  class="btn btn-info" onclick="alerts.showSwal('warning-message-and-confirmation-restart','<?=$urlDeleteRestart;?>&codigo=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>')">
                              <i class="material-icons">reply</i>
                            </button>
                              <?php
                            }else{
                             ?>
                            <button title="Restaurar Solicitud Recurso"  class="btn btn-info" onclick="alerts.showSwal('warning-message-and-confirmation-restart','<?=$urlDeleteRestart;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons">reply</i>
                            </button>
                              <?php      
                            }
                                   
                            ?>
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


<?php
// Preparamos
$stmt = $dbh->prepare("SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area 
  from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo 
  where sr.cod_estadoreferencial=1 and (sr.cod_estadosolicitudrecurso in (7,6,4,3,5,2)) order by sr.numero desc");
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
?>
<!-- small modal -->
<div class="modal fade modal-primary" id="modalListSolHistorial" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card" style="background:#23183D !important;color:#ffffff !important;">
              <div class="card-header card-header-info card-header-icon">
                <div class="card-icon">
                  <i class="material-icons">history</i>
                </div>
                <h4 class="card-title">Histórico de Solicitudes</h4>
              </div>

              <div class="card-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="material-icons">close</i>
              </button>
              <div class="row" id="div_cabecera" >
                    
              </div>
                <table class="table table-condesed" id="tablePaginator100">
                      <thead>
                        <tr class="bg-principal text-white">
                          <th>Of. - Area</th>
                          <th>Nº Sol.</th>
                          <th>Cod. Servicio</th>
                          <!--<th>Cliente</th>-->
                          <th>Proveedor</th>
                          <th>Cuenta</th>
                          <th>Solicitante</th>
                          <th>Fecha</th>
                          <th>Estado</th>
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
?>
                        <tr>
                          <td><?=$unidad;?>- <?=$area;?></td>
                          <td class="font-weight-bold"><?=$numeroSolTitulo?></td>
                          <td><?=$codigoServicio;?></td>
                          <!--<td><?=$nombreCliente;?></td>-->
                          <td><small><?=obtenerNombreConcatenadoProveedorDetalleSolicitudRecurso($codigo)?></small></td>
                          <td><small><?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?></small></td>
                          <td>
                                 <img src="assets/img/faces/persona1.png" width="20" height="20"/><?=$solicitante;?>
                          </td>
                          <td><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                          <td><button class="btn <?=$btnEstado?> btn-sm btn-link"><?=$estado;?></button> <!--<?=$nEst?> %
                             <div class="progress">
                               <div class="progress-bar <?=$barEstado?>" role="progressbar" aria-valuenow="<?=$nEst?>" aria-valuemin="0" aria-valuemax="100" style="width:<?=$nEst?>%">
                                  <span class="sr-only"><?=$nEst?>% Complete</span>
                               </div>
                             </div>-->
                          </td> 
                          <td class="td-actions text-right">
                            <?php
                              if($codEstado==4||$codEstado==3||$codEstado==5){
                            ?>
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
                                      <?php
                                        $stmtMoneda = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM monedas where cod_estadoreferencial=1 order by 2");
                                       $stmtMoneda->execute();
                                       while ($row = $stmtMoneda->fetch(PDO::FETCH_ASSOC)) {
                                         $codigoX=$row['codigo'];
                                         $nombreX=$row['nombre'];
                                         $abrevX=$row['abreviatura'];
                                            ?>
                                             <a href="#" onclick="javascript:window.open('<?=$urlImpComp;?>?comp=<?=$codComprobante;?>&mon=<?=$codigoX?>')" class="dropdown-item">
                                                 <i class="material-icons">list_alt</i> <?=$abrevX?>
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
                              <div class="dropdown-menu">
                                <?php 
                            if(isset($_GET['q'])){
                              if($codEstado==4){
                                 ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="dropdown-item">
                                    <i class="material-icons text-danger">reply</i> Descartar Envío
                                 </a><?php 
                                 }else{
                                   ?>
                                   <!--<a href="<?=$urlPagos;?>&codigo=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="dropdown-item">
                                    <i class="material-icons text-success">attach_money</i> PAGOS
                                   </a>-->
                                   <?php 
                                 }
                                 ?>
                                 <a href="<?=$urlVer;?>?cod=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="dropdown-item">
                                    <i class="material-icons text-warning">bar_chart</i> Ver Solicitud
                                 </a>
                             <?php
                            }else{
                               if($codEstado==4){
                                 ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&admin=0" class="dropdown-item">
                                    <i class="material-icons text-danger">reply</i> Descartar Envío
                                 </a><?php 
                                 }else{
                                   ?>
                                   <!--<a href="<?=$urlPagos;?>&codigo=<?=$codigo;?>" class="dropdown-item">
                                    <i class="material-icons text-success">attach_money</i> PAGOS
                                   </a>-->
                                   <?php 
                                 }
                                 ?>
                                 <a href="<?=$urlVer;?>?cod=<?=$codigo;?>" class="dropdown-item">
                                    <i class="material-icons text-warning">bar_chart</i> Ver Solicitud
                                 </a>
                             <?php  
                            }    
                           ?>       
                              </div>
                            </div>                           
                            <?php    
                              }else{
                                if($codEstado==6){
                                  //para el envio a administracion
                                  ?><a title="Solicitud Enviada" href='#' class="btn btn-warning">
                                     Enviado
                                    </a><?php
                                }else{
                                 if(isset($_GET['q'])){
                                   ?>
                                    <a title="Enviar a Autorización - Solicitud Recursos" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=6&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>' class="btn btn-default">
                                      <i class="material-icons">send</i>
                                    </a>
                                   <?php
                                  }else{
                                    ?>
                                     <a title="Enviar a Autorización - Solicitud Recursos" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=6&admin=0'  class="btn btn-default">
                                       <i class="material-icons">send</i>
                                     </a>
                                    <?php
                                  }                                   
                                }
                            if(isset($_GET['q'])){
                              ?>
                            <a title="Imprimir" href='#' onclick="javascript:window.open('<?=$urlImp;?>?sol=<?=$codigo;?>&mon=1')" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconImp;?></i>
                            </a>
                            <a title=" Ver Solicitud de Recursos" target="_blank" href="<?=$urlVer;?>?cod=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="btn btn-success">
                                    <i class="material-icons">preview</i>
                            </a> 
                            <?php
                             if($codEstado==1){
                             ?>
                            <a title="Editar solicitud - detalle" href='<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>'  class="btn btn-info">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            
                            <button title="Eliminar solicitud"  class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button>
                              <?php  
                              
                             }
                            }else{
                              ?>
                            <a title="Imprimir" href='#' onclick="javascript:window.open('<?=$urlImp;?>?sol=<?=$codigo;?>&mon=1')" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconImp;?></i>
                            </a>
                            <a title=" Ver Solicitud de Recursos" target="_blank" href="<?=$urlVer;?>?cod=<?=$codigo;?>" class="btn btn-success">
                                    <i class="material-icons">preview</i>
                            </a>
                            <?php
                             if($codEstado==1){
                             ?>
                            <a title="Editar solicitud - detalle" href='<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>'  class="btn btn-info">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            
                            <button title="Eliminar solicitud"  class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button>
                              <?php      
                             } 
                            }    
                              
                           }
                            ?>
                          </td>
                        </tr>
<?php
              $index++;
            }
?>
                      </tbody>
                </table>
              </div>
             <script>$("#cantidad_historico").html("("+<?=$index-1?>+")");</script> 
    </div>  
  </div>
</div>
<!--    end small modal -->
<?php
}else{
   ?><script type="text/javascript">
           $(document).ready(function(e) { 
               $("#minimizeSidebar").click()
               $("#minimizeSidebar").addClass("d-none");
             });
    </script><?php
  require_once ("error.html");
}
