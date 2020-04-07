<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$dbh = new Conexion();
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $item_3=$_GET['r'];
?>
  <input type="hidden" name="id_servicioibnored" value="<?=$q?>" id="id_servicioibnored"/>
  <input type="hidden" name="id_servicioibnored_rol" value="<?=$item_3?>" id="id_servicioibnored_rol"/>
<?php
}else{
  $item_3=0;
}
// Preparamos
$stmt = $dbh->prepare("SELECT sc.*,es.nombre as estado,c.nombre as cliente from simulaciones_servicios sc join estados_simulaciones es on sc.cod_estadosimulacion=es.codigo join clientes c on c.codigo=sc.cod_cliente where sc.cod_estadoreferencial=1 and sc.cod_estadosimulacion!=1 order by codigo");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('observacion', $observacion);
$stmt->bindColumn('fecha', $fecha);
$stmt->bindColumn('cod_plantillaservicio', $codPlantilla);
$stmt->bindColumn('cod_estadosimulacion', $codEstado);
$stmt->bindColumn('cod_responsable', $codResponsable);
$stmt->bindColumn('estado', $estado);
$stmt->bindColumn('cliente', $cliente);


//datos para servidor ibnorca
$item_1=2707;
// $item_2 codigo propuesta
//$item_3=obtenerRolPersonaIbnorcaSesion($globalUser);

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
                    <i class="material-icons">polymer</i>
                  </div>
                  <h4 class="card-title"><b> Gestionar <?=$moduleNamePlural?></b></h4>
                </div>
                <div class="card-body">
                    <table class="table" id="tablePaginator">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Numero</th>
                          <th>Cliente</th>
                          <th>Responsable</th>
                          <th>Fecha</th>
                          <th>Estado</th>
                          <th class="text-center">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
            $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $responsable=namePersonal($codResponsable);
                          switch ($codEstado) {
                            case 1:
                              $nEst=40;$barEstado="progress-bar-default";$btnEstado="btn-default";
                            break;
                            case 2:
                              $nEst=10;$barEstado="progress-bar-danger";$btnEstado="btn-danger";
                            break;
                            case 3:
                              $nEst=80;$barEstado="progress-bar-primary";$btnEstado="btn-primary";
                            break;
                            case 4:
                              $nEst=60;$barEstado="progress-bar-info";$btnEstado="btn-info";
                            break;
                            case 5:
                              $nEst=100;$barEstado="progress-bar-success";$btnEstado="btn-success";
                            break;
                          }
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td><?=$nombre;?></td>
                          <td><?=$cliente;?></td>
                          <td>
                                 <img src="assets/img/faces/persona1.png" width="20" height="20"/><?=$responsable;?>
                          </td>
                          <td><?=$fecha;?></td>
                          <td><?=$estado;?> <?=$nEst?> %
                             <div class="progress">
                               <div class="progress-bar <?=$barEstado?>" role="progressbar" aria-valuenow="<?=$nEst?>" aria-valuemin="0" aria-valuemax="100" style="width:<?=$nEst?>%">
                                  <span class="sr-only"><?=$nEst?>% Complete</span>
                               </div>
                             </div>
                          </td> 
                          <td class="td-actions text-right">
                            <div class="btn-group dropdown">
                              <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">list</i> <?=$estado;?>
                              </button>
                              <div class="dropdown-menu">
                                <?php 
                                if(isset($_GET['q'])){
                                  ?>
                                   <a href="<?=$urlVer;?>?cod=<?=$codigo;?>&admin=0&q=<?=$q?>&r=<?=$item_3?>" class="dropdown-item">
                                    <i class="material-icons text-info">bar_chart</i> Ver Propuesta
                                 </a>
                                 <?php 
                                }else{
                                 ?>
                                   <a href="<?=$urlVer;?>?cod=<?=$codigo;?>&admin=0" class="dropdown-item">
                                    <i class="material-icons text-info">bar_chart</i> Ver Propuesta
                                 </a>
                                 <?php  
                                }
                                ?>
                               
                              
                                <?php 
                                if($codEstado==4){
                                 /*
                                 $stmt2=$dbh->prepare("SELECT * FROM ibnorca.estadoobjeto ");
                                 $stmt2->execute(); 
                                 while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {  
                                 }
                                 */
                                  
                                 if(isset($_GET['q'])){      
                                  ?>
                                  <a href="#" onclick="mostrarCambioEstadoObjeto(<?=$codigo?>)" class="dropdown-item">
                                    <i class="material-icons text-warning">dns</i> Cambiar Estado
                                 </a>
                                  <!--
                                  <a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=3&q=<?=$q?>" class="dropdown-item">
                                    <i class="material-icons text-success">offline_pin</i> Aprobar Solicitud
                                 </a>
                                 <a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&q=<?=$q?>" class="dropdown-item">
                                    <i class="material-icons text-dark">report</i> Rechazar Solicitud
                                 </a>
                                 <a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=2&q=<?=$q?>" class="dropdown-item">
                                    <i class="material-icons text-danger">clear</i> Anular Solicitud
                                 </a>--><?php 
                               }else{
                                ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=3" class="dropdown-item">
                                    <i class="material-icons text-success">offline_pin</i> Aprobar Solicitud
                                 </a>
                                 <a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1" class="dropdown-item">
                                    <i class="material-icons text-dark">report</i> Rechazar Solicitud
                                 </a>
                                 <a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=2" class="dropdown-item">
                                    <i class="material-icons text-danger">clear</i> Anular Solicitud
                                 </a><?php 
                                 }
                                }else{
                                  ?><a href="#" onclick="mostrarCambioEstadoObjeto(<?=$codigo?>)" class="dropdown-item">
                                    <i class="material-icons text-warning">dns</i> Cambiar Estado
                                 </a><?php
                                  if(isset($_GET['q'])){
                                     ?><!--<a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=4&q=<?=$q?>" class="dropdown-item">
                                    <i class="material-icons text-dark">reply</i> Deshacer Cambios
                                 </a>-->
                                 <?php 
                                  }else{
                                    ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=4" class="dropdown-item">
                                    <i class="material-icons text-dark">reply</i> Deshacer Cambios
                                 </a>
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
              </div>
              <div class="card-footer fixed-bottom">
                <?php 
                if(isset($_GET['q'])){
                ?><a href="<?=$urlList2?>&q=<?=$q?>&r=<?=$item_3?>" class="btn btn-info"><i class="material-icons">refresh</i> Refrescar</a><?php
                }else{
                 ?><a href="<?=$urlList2?>" class="btn btn-info"><i class="material-icons">refresh</i> Refrescar</a><?php
                } 
                ?>
                
              </div>      
            </div>
          </div>  
        </div>
    </div>

<!-- small modal -->
<div class="modal fade modal-arriba modal-primary" id="modalEstadoObjeto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                        <button type="button" id="boton_guardarsim" class="btn btn-default" onclick="cambiarEstadoObjeto()">Cambiar Estado</button>
                      </div> 
                </div>   
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->