<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$dbh = new Conexion();

error_reporting(E_ALL);
ini_set('display_errors', '1');

$sqlServicio="";

if(isset($_GET['q'])){
  $q=$_GET['q'];
  $u=$_GET['u'];
  $sqlAreas="";
  $sqlServicio="";
  if(isset($_GET['v'])){
    $v=$_GET['v'];
    $sqlServicio="and sr.idServicio=".$v;
  }else{
    $v=0;
  }
  if(isset($_GET['s'])){
    $s=$_GET['s'];
    //$arraySql=explode("IdArea=",$s);
    //$codigoArea=trim($arraySql[1]);
    //$sqlAreas="and sr.cod_area=".$codigoArea;
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
    //echo $s."<br>";
    //echo var_dump($arraySql);
    //echo $sqlAreas; 
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

//Sacamos las configuraciones de los proyectos que existan
$stringOficinasProyectosExt=obtenerValorConfiguracion(69);
$stringAreasProyectosExt=obtenerValorConfiguracion(65);


$sqlProyectosExt="SELECT l.* FROM (SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area,
    (select count(*) from solicitud_recursosdetalle where cod_solicitudrecurso=sr.codigo and (cod_unidadorganizacional in ($stringOficinasProyectosExt) or cod_area in ($stringAreasProyectosExt))) as sis_detalle 
  from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo 
  where sr.cod_estadoreferencial=1 and sr.cod_estadosolicitudrecurso in (7,4,3,5) order by sr.numero desc) l  
where (l.cod_unidadorganizacional in ($stringOficinasProyectosExt) or l.cod_area in ($stringAreasProyectosExt) or l.sis_detalle>0)";
//echo $sqlProyectosExt;
$stmt = $dbh->prepare($sqlProyectosExt);
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
<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div style="color:#E2AF0F !important;font-weight:bold !important;" class="card-header card-header-danger card-header-icon" >
                  <div class="card-icon" style="background:rgb(116, 24, 153) !important;">
                    <i class="material-icons">content_paste</i>
                  </div>
                  <h4 class="card-title"><b><?=$moduleNamePlural?> - Aprobación de Proyectos</b></h4>
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
                            case 7:
                              $btnEstado="btn-primary";
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
                       $glosa_estadoX = preg_replace("[\n|\r|\n\r]", ", ", $glosa_estadoX);
                       $glosaArray=explode("####", $glosa_estadoX);
                       $glosa_estadoX = str_replace("####", " - ", $glosa_estadoX);
?>
                        <tr>
                          <td><?=$unidad;?>- <?=$area;?></td>
                          <td class="font-weight-bold"><?=$numeroSolTitulo?></td>
                          <td><?=$codigoServicio;?></td>
                          <!--<td><?=$nombreCliente;?></td>-->
                          <td><small><?=$nombreProveedor?></small></td>
                          <td><small><?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?></small></td>
                          <td>
                                 <img src="assets/img/faces/persona1.png" width="20" height="20"/><?=$solicitante;?>
                          </td>
                          <td><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                          <td><button class="btn <?=$btnEstado?> btn-sm btn-link"><?=$estado;?></button>
                          </td> 
                          <td class="text-warning font-weight-bold"><small><b><?php if(isset($glosaArray[1])){
                                echo "".$glosaArray[0].""."<u class='text-muted'> ".$glosaArray[1]."</u>";
                            }else{
                                echo $glosa_estadoX;
                            }?></b></small></td>
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
                              <div class="dropdown-menu menu-fixed-sm-table">
                                <?php 
                            if(isset($_GET['q'])){
                              if($codEstado==4){
                                 ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=7&admin=0&reg=2&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="dropdown-item">
                                    <i class="material-icons text-danger">reply</i> Descartar Autorización
                                 </a><?php 
                                 }else{
                                   ?>
                                   <!--<a href="<?=$urlPagos;?>&codigo=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="dropdown-item">
                                    <i class="material-icons text-success">attach_money</i> PAGOS
                                   </a>-->
                                   <?php 
                                 }
                                 ?>
                             <?php
                            }else{
                               if($codEstado==4){
                                 ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=7&admin=0&reg=2" class="dropdown-item">
                                    <i class="material-icons text-danger">reply</i> Descartar Autorización
                                 </a><?php 
                                 }else{
                                   ?>
                                   <!--<a href="<?=$urlPagos;?>&codigo=<?=$codigo;?>" class="dropdown-item">
                                    <i class="material-icons text-success">attach_money</i> PAGOS
                                   </a>-->
                                   <?php 
                                 }
                                 ?>
                             <?php  
                            }    
                           ?>       
                              </div>
                            </div>                           
                            <?php    
                              }else{
                                if($codEstado==7){
                                  //para el envio a administracion
                                  if(isset($_GET['q'])){
                                   ?>
                                    <a title="Aprobar Solicitud Recurso SIS" onclick="alerts.showSwal('aprobar-solicitud-recurso-sis','<?=$urlEdit2?>?cod=<?=$codigo?>&reg=2&estado=4&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>')" href='#'  class="btn btn-primary">
                                      <i class="material-icons">assignment_turned_in</i>
                                    </a>
                                    <a onclick="devolverSolicitudRecurso(<?=$numeroSol?>,'<?=$codigoServicio?>','<?=$urlEdit2?>?cod=<?=$codigo?>&reg=2&estado=1&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>','<?=$nombreProveedor?>','<?=$glosa_estadoX?>')" title="Devolver Solicitud Recurso" href='#'  class="btn btn-danger">
                                      <i class="material-icons">reply</i>
                                    </a>
                                   <?php
                                  }else{
                                    ?>
                                     <a title="Aprobar Solicitud Recurso SIS" onclick="alerts.showSwal('aprobar-solicitud-recurso-sis','<?=$urlEdit2?>?cod=<?=$codigo?>&reg=2&estado=4&admin=0')" href='#'  class="btn btn-primary">
                                       <i class="material-icons">assignment_turned_in</i>
                                     </a>
                                     <a onclick="devolverSolicitudRecurso(<?=$numeroSol?>,'<?=$codigoServicio?>','<?=$urlEdit2?>?cod=<?=$codigo?>&reg=2&estado=1&admin=0','<?=$nombreProveedor?>','<?=$glosa_estadoX?>')" title="Devolver Solicitud Recurso" href='#'  class="btn btn-danger">
                                       <i class="material-icons">reply</i>
                                     </a>
                                    <?php
                                  }
                                }else{
                                 if(isset($_GET['q'])){
                                   ?>
                                    <a title="Enviar a Autorización - Solicitud Recursos" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=6&admin=0&reg=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>' class="btn btn-default">
                                      <i class="material-icons">send</i>
                                    </a>
                                   <?php
                                  }else{
                                    ?>
                                     <a title="Enviar a Autorización - Solicitud Recursos" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=6&admin=0&reg=0'  class="btn btn-default">
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
                             <a title=" Ver Solicitud de Recursos" target="_blank" href="<?=$urlVer;?>?cod=<?=$codigo;?>&reg=2&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="btn btn-success">
                                    <i class="material-icons">preview</i>
                            </a>
                            <a title="Editar solicitud - detalle" href='<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&reg=2&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>'  class="btn btn-info">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            
                            <?php
                             if($codEstado==1){
                             ?>
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
                            <a title=" Ver Solicitud de Recursos" target="_blank" href="<?=$urlVer;?>?cod=<?=$codigo;?>&reg=2" class="btn btn-success">
                                    <i class="material-icons">preview</i>
                            </a>
                            <a title="Editar solicitud - detalle" href='<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&reg=2'  class="btn btn-info">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            
                            <?php
                             if($codEstado==1){
                             ?>
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
              </div>
              <div class="card-footer fixed-bottom col-sm-9">
                <?php
                $codUrl="";$codUrl2=""; 
                if(isset($_GET['cod_sim'])){
                 $codUrl="&sim=$codSimCosto&det=1";
                 $codUrl2="?sim=$codSimCosto&det=1";
                }
                if(isset($_GET["q"])){
                  ?><a href="#" target="_blank" class="btn btn-warning float-right"><i class="material-icons">fullscreen</i> Ver Pantalla Completa</a><?php
                }
?>              
                
                <a href="#" onclick="abrirModal('modalListSolEliminados');moverModal('modalListSolEliminados');" class="btn btn-danger float-right" style="background:rgb(116, 24, 153) !important; color:#E2AF0F;"><i class="material-icons"><?=$iconDelete;?></i> <small id="cantidad_eliminados"></small> Eliminados</a>
                <a href="index.php?opcion=reportesSolicitudRecursosSis" class="btn btn-danger float-right" style="background:rgb(116, 24, 153) !important; color:#E2AF0F;">Reporte Solicitudes SIS</a>
              </div>      
            </div>
          </div>  
        </div>
    </div>


<?php
$stmt = $dbh->prepare("SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area 
  from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo 
  where sr.cod_estadoreferencial=2 $sqlServicio $sqlSimCosto $sqlAreas and sr.cod_unidadorganizacional in ($stringOficinasProyectosExt) order by sr.numero desc");
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
              <div style="color:#E2AF0F !important;font-weight:bold !important;" class="card-header card-header-danger card-header-icon">
                <div class="card-icon" style="background:rgb(116, 24, 153) !important;">
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
                        <tr style="background:rgb(116, 24, 153) !important; color:#E2AF0F;">
                          <th>Of. - Area</th>
                          <th>Nº Sol.</th>
                          <th>Cod. Servicio</th>
                          <!--<th>Cliente</th>-->
                          <th>Proveedor</th>
                          <th>Cuenta</th>
                          <th>Solicitante</th>
                          <th class="text-right">Actions</th>
                          <th>Fecha</th>
                          <th>Estado</th>
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

