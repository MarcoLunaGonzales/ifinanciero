<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$dbh = new Conexion();

 error_reporting(E_ALL);
 ini_set('display_errors', '1');

 
$arraySql="";
$codigoArea="";
$sqlAreas="";


if(isset($_GET['q'])){
  $q=$_GET['q'];
  $u=$_GET['u'];
  $sqlAreas="";
  $sqlServicio="";
  $sqlAreasLista="";
  if(isset($_GET['v'])){
    $v=$_GET['v'];
    $sqlServicio="and sr.idServicio=".$v;
  }else{
    $v=0;
  }
  if(isset($_GET['s'])){
    $s=$_GET['s'];

    //CORTAMOS LA PARTE DE OFICINA PORQUE EXISTEN 2 IGUALES
    $s_recortado=substr($s,12);
    if(strpos($s_recortado,"=")){
      if(strpos($s_recortado,">=")){
        $arraySql=explode("IdArea>=",$s_recortado);
      }else{      
        $arraySql=explode("IdArea=",$s_recortado);
      }
      $codigoArea=trim($arraySql[1]);
      $sqlAreas="and sr.cod_area='".$codigoArea."' ";
    }else{
      $arraySql=explode("IdArea in",$s_recortado);
      //var_dump($arraySql);
      $codigoArea=trim($arraySql[1]);
      $sqlAreas="and sr.cod_area in ".$codigoArea;
    }

    //echo "s recortado".$s_recortado;
    
    if($codigoArea=='0'){
      $sqlAreas="and (sr.cod_area>=0) ";// or sr.cod_area=".obtenerValorConfiguracion(65).")             
      $sqlAreasLista="and (a.codigo>=0) ";// or a.codigo=".obtenerValorConfiguracion(65).")"             
    }else{
      if($q==32 || $q==177){
        $sqlAreas.=$sqlAreas." or (sr.cod_area in (".obtenerValorConfiguracion(65).") or sr.cod_area=2957)";
        $sqlAreasLista.=$sqlAreas." or (a.codigo in (".obtenerValorConfiguracion(65).") or a.cod_area=2957)";
      }else{
        $sqlAreas.=$sqlAreas." or (sr.cod_area in (".obtenerValorConfiguracion(65).") )";
        $sqlAreasLista.=$sqlAreas." or (a.codigo in (".obtenerValorConfiguracion(65).") )";
      }                    
    }
    //echo $s."<br>";
    //echo var_dump($arraySql);
    //echo "SQL ARESA: ".$sqlAreas; 
  }
}else{
  $sqlAreas="";
  $sqlServicio="";
  $sqlAreasLista="";
}
$sqlSimCosto="";
if(isset($_GET['cod_sim'])){
  $codSimCosto=$_GET['cod_sim'];
  $sqlSimCosto=" and sr.cod_simulacion=$codSimCosto";
}
// Preparamos
$sqlSR="SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area 
  from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo 
  where sr.cod_estadoreferencial=1 and sr.cod_estadosolicitudrecurso in (6,7) $sqlServicio $sqlSimCosto $sqlAreas and sr.cod_estadosolicitudrecurso in (6,7) order by sr.numero desc";

//echo $sqlSR;

$stmt = $dbh->prepare($sqlSR);
//echo $sqlSR;
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
$stmt->bindColumn('revisado_contabilidad', $estadoContabilidadX);
?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div style="color:#E2AF0F !important;font-weight:bold !important;" class="card-header card-header-danger card-header-icon" >
                  <div class="card-icon" style="background:rgb(37, 98, 83) !important;">
                    <i class="material-icons">content_paste</i>
                  </div>
                  <h4 class="card-title"><b><?=$moduleNamePlural?> - Aprobación</b></h4>
                  
                </div>
                <div class="card-body">
                  <?php
                   if(isset($_GET['q'])){
                  ?>
                  <select class="selectpicker form-control form-control-sm float-right col-sm-2" name="area_solicitud_lista" id="area_solicitud_lista" data-style="btn btn-rose">
                    <option disabled value="">--Areas--</option>
                    <option selected value="">TODOS</option>
                                     <?php
                                         $stmtAreasLista = $dbh->prepare("SELECT a.codigo, a.nombre, a.abreviatura FROM areas a join areas_activas aa on aa.cod_area=a.codigo where a.cod_estado=1 $sqlAreasLista order by 2");
                                         $stmtAreasLista->execute();
                                         $cont=0;
                                         while ($rowLista = $stmtAreasLista->fetch(PDO::FETCH_ASSOC)) {
                                           $codigoX=$rowLista['codigo'];
                                           $nombreX=$rowLista['nombre'];
                                           $abrevX=$rowLista['abreviatura'];
                                             ?><option value="<?=$abrevX;?>"><?=$abrevX;?></option><?php                                           
                                         } 
                                         ?>
                    </select>
                    <?php
                    }
                  ?>
                  <div class="table-responsive">
                    <table class="table table-condesed" id="tablePaginator">
                      <thead>
                        <tr>
                          <th>Of. - Area</th>
                          <th>Nº Sol.</th>
                          <th>Cod. Servicio</th>
                          <th>Cliente</th>
                          <th>Proveedor</th>
                          <th>Cuenta</th>
                          <th>Solicitante</th>
                          <th>Fecha</th>
                          <!--<th>Estado</th>-->
                          <th>Observaciones</th>
                          <td><small>Monto</small></td>
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
                       $nombreProveedor=obtenerNombreConcatenadoProveedorDetalleSolicitudRecurso($codigo);   

                       $glosa_estadoX = preg_replace("[\n|\r|\n\r]", ", ", $glosa_estadoX);
                       $glosaArray=explode("####", $glosa_estadoX);
                       $glosa_estadoX = str_replace("####", " - ", $glosa_estadoX);
                       $montoDetalleSoliditud=number_format(obtenerSumaDetalleSolicitud($codigo),2,'.',',');
?>                     
                        <tr>
                          <td><?=$unidad;?>- <?=$area;?></td>
                          <td class="font-weight-bold"><?=$numeroSolTitulo?></td>
                          <td><?=$codigoServicio;?></td>
                          <td></small><?=$nombreCliente;?></small></td>
                          <td><small><?=$nombreProveedor?></small></td>
                          <td><small><?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?></small></td>
                          <td>
                                 <img src="assets/img/faces/persona1.png" width="20" height="20"/><?=$solicitante;?>
                          </td>
                          <td><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                          <!--<td><button class="btn <?=$btnEstado?> btn-sm btn-link"><?=$estado;?></button>-->
                          </td> 
                          <td class="text-dark font-weight-bold"><small><b>
                            <?php if(isset($glosaArray[1])){
                                echo "".$glosaArray[0].""."<u class='text-muted'> ".$glosaArray[1]."</u>";
                            }else{
                                echo $glosa_estadoX;
                            }?></small></b></td>
                            <td class="text-right small font-weight-bold">
                              <small><?=$montoDetalleSoliditud?></small>
                            </td>
                          <td class="td-actions text-right">
                            <?php 
                            if(($codEstado==4||$codEstado==6)&&verificarComprobanteUsuarioRevisor($globalUser)!=0){
                            if($estadoContabilidadX==1){
                              if(isset($_GET['q'])){
                                ?>
                                <a title="Quitar la Revisión" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=10&admin=0&reg=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>'  class="btn btn-rose" style="background:#661E1B">
                                       <i class="material-icons">check_box</i><!--check_box-->
                                </a>
                                <?php
                              }else{
                                ?>
                                <a title="Quitar la Revisión" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=10&admin=0&reg=0'  class="btn btn-rose" style="background:#661E1B">
                                       <i class="material-icons">check_box</i><!--check_box-->
                                </a>
                                <?php
                              }
                            }else{
                              $iconRevisado="check_box_outline_blank";
                              $estiloIconRevisado="btn-default";
                              $irEstado=12;
                              $mensajeTitle="Marca como Examinado";
                              if($estadoContabilidadX==2){
                                $iconRevisado="adjust";
                                $estiloIconRevisado="btn-info";
                                $irEstado=10;
                                $mensajeTitle="Quitar Examinado";
                              }
                              if(isset($_GET['q'])){
                                ?>
                                <a title="<?=$mensajeTitle?>" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=<?=$irEstado?>&admin=0&reg=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>'  class="btn <?=$estiloIconRevisado?>">
                                       <i class="material-icons"><?=$iconRevisado?></i>
                                </a>
                                <?php
                              }else{
                                ?>
                                <a title="<?=$mensajeTitle?>" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=<?=$irEstado?>&admin=0&reg=0'  class="btn <?=$estiloIconRevisado?>">
                                       <i class="material-icons"><?=$iconRevisado?></i>
                                </a>
                                <?php
                              }
                            }

                          }

                           //fin de boton extra de estado
                              
                              if($codEstado==4||$codEstado==3||$codEstado==5||$codEstado==8||$codEstado==9){
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
                                 ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=6&admin=0&reg=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="dropdown-item">
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
                                 ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=6&admin=0&reg=0" class="dropdown-item">
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
                                if($codEstado==6){
                                  //para el envio a administracion
                                  if(isset($_GET['q'])){
                                   ?>
                                    <a title="Autorizar Solicitud Recurso" onclick="alerts.showSwal('aprobar-solicitud-recurso','<?=$urlEdit2?>?cod=<?=$codigo?>&reg=1&estado=4&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>')" href='#'  class="btn btn-warning">
                                      <i class="material-icons">assignment_turned_in</i>
                                    </a>
                                    <a onclick="devolverSolicitudRecurso(<?=$numeroSol?>,'<?=$codigoServicio?>','<?=$urlEdit2?>?cod=<?=$codigo?>&reg=1&estado=1&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>','<?=$nombreProveedor?>','<?=$glosa_estadoX?>')" title="Devolver Solicitud Recurso" href='#'  class="btn btn-danger">
                                      <i class="material-icons">reply</i>
                                    </a>
                                   <?php
                                  }else{
                                    ?>
                                     <a title="Autorizar Solicitud Recurso" onclick="alerts.showSwal('aprobar-solicitud-recurso','<?=$urlEdit2?>?cod=<?=$codigo?>&reg=1&estado=4&admin=0')" href='#'  class="btn btn-warning">
                                       <i class="material-icons">assignment_turned_in</i>
                                     </a>
                                     <a onclick="devolverSolicitudRecurso(<?=$numeroSol?>,'<?=$codigoServicio?>','<?=$urlEdit2?>?cod=<?=$codigo?>&reg=1&estado=1&admin=0','<?=$nombreProveedor?>','<?=$glosa_estadoX?>')" title="Devolver Solicitud Recurso" href='#'  class="btn btn-danger">
                                       <i class="material-icons">reply</i>
                                     </a>
                                    <?php
                                  }
                                }else{
                                  if($codEstado==7){
                                  //para el envio a administracion
                                  if(isset($_GET['q'])){
                                   ?>
                                    <a title="Enviado para Aprobación de Proyectos"  href='#'  class="btn btn-primary">
                                      <i class="material-icons">assignment_turned_in</i>
                                    </a>
                                   <?php
                                  }else{
                                    ?>
                                     <a title="Enviado para Aprobación de Proyectos" href='#'  class="btn btn-primary">
                                       <i class="material-icons">assignment_turned_in</i>
                                     </a>
                                    <?php
                                  }
                                }else{
                                 if(isset($_GET['q'])){
                                   ?>
                                    <a title="Enviar a Autorización- Solicitud Recursos" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=6&admin=0&reg=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>' class="btn btn-default">
                                      <i class="material-icons">send</i>
                                    </a>
                                   <?php
                                  }else{
                                    ?>
                                     <a title="Enviar a Autorización- Solicitud Recursos" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=6&admin=0&reg=0'  class="btn btn-default">
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
                             <a title=" Ver Solicitud de Recursos" target="_blank" href="<?=$urlVer;?>?cod=<?=$codigo;?>&reg=1&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="btn btn-success">
                                    <i class="material-icons">preview</i>
                            </a>
                            <?php
                             if($codEstado!=7){
                               ?>
                             <a title="Editar solicitud - detalle" href='<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&reg=1&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>'  class="btn btn-info">
                               <i class="material-icons"><?=$iconEdit;?></i>
                             </a>
                            <?php
                             }  
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
                            <a title=" Ver Solicitud de Recursos" target="_blank" href="<?=$urlVer;?>?cod=<?=$codigo;?>&reg=1" class="btn btn-success">
                                    <i class="material-icons">preview</i>
                            </a>
                            <?php
                             if($codEstado!=7){
                               ?>
                             <a title="Editar solicitud - detalle" href='<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&reg=1'  class="btn btn-info">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a> 
                            <?php
                             }  
                            
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
                <a href="#" onclick="abrirModal('modalListSolEliminados');moverModal('modalListSolEliminados');" class="btn btn-danger float-right" style="background:rgb(37, 98, 83) !important; color:#E2AF0F;"><i class="material-icons"><?=$iconDelete;?></i> <small id="cantidad_eliminados"></small> Eliminados</a>
                <a href="<?=$urlList8?>" target="_blank" class="btn btn-primary float-right"><i class="material-icons">history</i> <small id="cantidad_historico"></small> Histórico</a>
              </div>      
            </div>
          </div>  
        </div>
    </div>


<?php
$stmt = $dbh->prepare("SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area 
  from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo 
  where sr.cod_estadoreferencial=2 $sqlServicio $sqlSimCosto $sqlAreas order by sr.numero desc");
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
                <div class="card-icon" style="background:rgb(37, 98, 83) !important;">
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
                        <tr style="background:rgb(37, 98, 83) !important; color:#E2AF0F;">
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
                            case 7:
                              $btnEstado="btn-info";
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
$stmt = $dbh->prepare("SELECT count(*) as cantidad_historico
  from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo 
  where sr.cod_estadoreferencial=1 and sr.cod_estadosolicitudrecurso in (3,5,8,9) $sqlServicio $sqlSimCosto $sqlAreas order by sr.numero desc");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('cantidad_historico', $cantidad_historico);
while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
  ?> <script>$("#cantidad_historico").html("("+<?=$cantidad_historico-1?>+")");</script> <?php
}
?>

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

