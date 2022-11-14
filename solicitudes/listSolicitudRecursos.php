<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$globalArea=$_SESSION["globalArea"];
$dbh = new Conexion();


 error_reporting(E_ALL);
 ini_set('display_errors', '1');

 

if(isset($_GET['q'])){
  $q=$_GET['q'];
  $u=$_GET['u'];
  $sqlAreas="";
  $sqlServicio="";
  if(isset($_GET['v'])&&$_GET['v']!=0){
    $v=$_GET['v'];
    $sqlServicio="and sr.idServicio=".$v;
  }else{
    $v=0;
  }
  if(isset($_GET['s'])){
    $s=$_GET['s'];
    $arraySql=explode("IdArea",$s);
    $codigoArea='0';  
    if(isset($arraySql[1])){
      $codigoArea=trim($arraySql[1]);
    }   
    if($codigoArea=='0'){
      $sqlAreas="and (sr.cod_area=0 or sr.cod_area=".obtenerValorConfiguracion(65).")";             
    }else{
      $sqlAreas="and (sr.cod_area ".$codigoArea." or sr.cod_area=".obtenerValorConfiguracion(65).")";  
    } 
  }

}else{
  $sqlAreas="";
  $sqlServicio="";
}
$sqlSimCosto="";
if(isset($_GET['cod_sim'])){
  $codSimCosto=$_GET['cod_sim'];
  $sqlSimCosto=" and sr.cod_simulacion=$codSimCosto";
}
// Preparamos
$sql="SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area 
  from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo 
  where sr.cod_estadoreferencial=1 and (sr.cod_estadosolicitudrecurso in (1,2,3,6,7,4)) $sqlServicio $sqlSimCosto and sr.cod_personal='$globalUser' order by sr.numero desc";


//echo "<br><br><br>".$sql;

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
$stmt->bindColumn('idServicio', $idServicioX);
$stmt->bindColumn('glosa_estado', $glosa_estadoX);
$stmt->bindColumn('cod_area', $codAreaCabecera);

?>
<style>


</style>
<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-danger card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">content_paste</i>
                  </div>
                  <h4 class="card-title"><b><?=$moduleNamePlural?></b></h4>
                </div>
                <div class="card-body table-responsive">
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
                       $nombreProveedor=obtenerNombreConcatenadoProveedorDetalleSolicitudRecurso($codigo);
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
                          <td><button class="btn <?=$btnEstado?> btn-sm btn-link"><?=$estado;?></button>
                          </td> 
                          <td class="text-danger font-weight-bold"><small><b><?=$glosa_estadoX?></b></small></td>
                          <td class="td-actions text-right">
                            <?php
                            $glosa_estadoX = preg_replace("[\n|\r|\n\r]", ", ", $glosa_estadoX);
                            $glosa_estadoX = str_replace("####", " ", $glosa_estadoX);  
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
                              if($codEstado==4){
                                 ?><?php 
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
                                 ?><?php 
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
                                  $estadoSiguiente=6;
                                  if(verificarSolicitudRecursosManual($codigo)!=0){
                                    if(verificarImporteMayorAlPresupuestado($codigo)!=0){
                                          $estadoSiguiente=4;
                                    }
                                  }

                                  //SI EL AREA DE SERVICIO ES TCP Y TCS DEBE IR DIRECTAMENTE A APROBACION
                                  if($codAreaCabecera==38 || $codAreaCabecera==39 || $codAreaCabecera==2957){
                                    $estadoSiguiente=6;
                                  }

                                 if($glosa_estadoX!=""){
                                    if(isset($_GET['q'])){
                                   ?>

                                    <a title="Enviar a Autorización - Solicitud Recursos" onclick="devolverSolicitudRecurso(<?=$numeroSol?>,'<?=$codigoServicio?>','<?=$urlEdit2?>?cod=<?=$codigo?>&estado=<?=$estadoSiguiente?>&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>&ll=0','<?=$nombreProveedor?>','<?=$glosa_estadoX?>')" href='#' class="btn btn-default">
                                      <i class="material-icons">send</i>
                                    </a>
                                   <?php
                                  }else{
                                    ?>
                                     <a title="Enviar a Autorización - Solicitud Recursos" onclick="devolverSolicitudRecurso(<?=$numeroSol?>,'<?=$codigoServicio?>','<?=$urlEdit2?>?cod=<?=$codigo?>&estado=<?=$estadoSiguiente?>&admin=0&ll=0','<?=$nombreProveedor?>','<?=$glosa_estadoX?>')" href='#'  class="btn btn-default">
                                       <i class="material-icons">send</i>
                                     </a>
                                    <?php
                                    }
                                 }else{
                                    if(isset($_GET['q'])){
                                   ?>
                                    <a title="Enviar a Autorización - Solicitud Recursos" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=<?=$estadoSiguiente?>&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>&ll=0' class="btn btn-default">
                                      <i class="material-icons">send</i>
                                    </a>
                                   <?php
                                  }else{
                                    ?>
                                     <a title="Enviar a Autorización - Solicitud Recursos" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=<?=$estadoSiguiente?>&admin=0&ll=0'  class="btn btn-default">
                                       <i class="material-icons">send</i>
                                     </a>
                                    <?php
                                    }  
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
              <div class="card-footer fixed-bottom col-sm-9">
                <?php
                $codUrl="";$codUrl2=""; 
                if(isset($_GET['cod_sim'])){
                 $codUrl="&sim=$codSimCosto&det=1";
                 $codUrl2="?sim=$codSimCosto&det=1";
                }

                if(isset($_GET['q'])){
                ?><a href="<?=$urlRegister3;?>?q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?><?=$codUrl?>" target="_self" class="<?=$buttonNormal;?>">Registrar</a>
                <a href="#" target="_blank" class="btn btn-warning float-right"><i class="material-icons">fullscreen</i> Ver Pantalla Completa</a><?php
                }else{
                 ?><a href="#" onclick="javascript:window.open('<?=$urlRegister3;?><?=$codUrl2?>')" class="<?=$buttonNormal;?>">Registrar</a>
                  <?php
                } 
                ?>
                <div class="btn-group float-right">
                  <a href="#" onclick="abrirModal('modalListSolHistorial');moverModal('modalListSolHistorial');" class="btn btn-info"><i class="material-icons">history</i> <small id="cantidad_historico"></small> Histórico</a>
                  <a href="#" onclick="abrirModal('modalListSolEliminados');moverModal('modalListSolEliminados');" class="btn btn-danger"><i class="material-icons"><?=$iconDelete;?></i> <small id="cantidad_eliminados"></small> Eliminados</a>
                  <a href="#" onclick="abrirModal('modalListSolSis');moverModal('modalListSolSis');" class="btn btn-default"><small id="cantidad_sis"></small> SIS</a>
                </div>
              </div>      
            </div>
          </div>  
        </div>
    </div>


<?php
$stmt = $dbh->prepare("SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area 
  from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo 
  where sr.cod_estadoreferencial=2 $sqlServicio $sqlSimCosto and sr.cod_personal='$globalUser' order by sr.numero desc");
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
    <div class="modal-content card">
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
                          <td><button class="btn <?=$btnEstado?> btn-sm btn-link"><?=$estado;?></button> 
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
  where sr.cod_estadoreferencial=1 and (sr.cod_estadosolicitudrecurso in (5,8,9)) $sqlServicio $sqlSimCosto and sr.cod_personal='$globalUser' order by sr.numero desc");
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
    <div class="modal-content card">
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
                            case 7:
                              $btnEstado="btn-info";
                            break;
                            case 8:
                              $nEst=100;$barEstado="progress-bar-default";$btnEstado="btn-deafult";
                            break;
                            case 9:
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
                          <td><button class="btn <?=$btnEstado?> btn-sm btn-link"><?=$estado;?></button>
                          </td> 
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
                              <div class="dropdown-menu">
                                <?php 
                            if(isset($_GET['q'])){
                              if($codEstado==4){
                                 ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="dropdown-item">
                                    <i class="material-icons text-danger">reply</i> Descartar Envío
                                 </a><?php 
                                 }else{
                                   ?>

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
// Preparamos
$stmt = $dbh->prepare("SELECT * FROM (SELECT (select count(*) from solicitud_recursosdetalle where cod_solicitudrecurso=sr.codigo and (cod_area=1235 and cod_unidadorganizacional=3000)) as detalle_sis ,
sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area

 
from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo 
)l


where l.cod_estadoreferencial=1 and (l.cod_estadosolicitudrecurso in (7,6,4,3,5,2,8)) and (l.cod_unidadorganizacional=3000 or l.cod_area=1235 or l.detalle_sis>0) order by l.numero desc
");
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
<div class="modal fade modal-primary" id="modalListSolSis" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
              <div class="card-header card-header-info card-header-icon">
                <div class="card-icon">
                  <i class="material-icons">history</i>
                </div>
                <h4 class="card-title">Solicitudes - SIS</h4>
              </div>

              <div class="card-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="material-icons">close</i>
              </button>
              <div class="row" id="" >
                    
              </div>
                <table class="table table-condesed" id="tablePaginator50">
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
                            case 7:
                              $btnEstado="btn-info";
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
                          <td><button class="btn <?=$btnEstado?> btn-sm btn-link"><?=$estado;?></button>
                          </td> 
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
                              <div class="dropdown-menu">
                                <?php 
                            if(isset($_GET['q'])){
                              if($codEstado==4){
                                 ?><!--<a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="dropdown-item">
                                    <i class="material-icons text-danger">reply</i> Descartar Envío
                                 </a>--><?php 
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
                                 ?><!--<a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&admin=0" class="dropdown-item">
                                    <i class="material-icons text-danger">reply</i> Descartar Envío
                                 </a>--><?php 
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
                                 /*if(isset($_GET['q'])){
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
                                  }*/                                   
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
                            <!--<a title="Editar solicitud - detalle" href='<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>'  class="btn btn-info">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            
                            <button title="Eliminar solicitud"  class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button>-->
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
                            <!--<a title="Editar solicitud - detalle" href='<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>'  class="btn btn-info">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            
                            <button title="Eliminar solicitud"  class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button>-->
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
             <script>$("#cantidad_sis").html("("+<?=$index-1?>+")");</script> 
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
        <h4 class="modal-title" id="myModalLabel">Enviar Solicitud</h4>
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
              <textarea type="text" name="observaciones_modal_lg" id="observaciones_modal_lg" class="form-control" required="true" readonly></textarea>
            </div>
          </div>
        </div>

        <div class="row">
          <label class="col-sm-12 col-form-label" style="color:#7e7e7e"><small>Correcciones</small></label>
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

