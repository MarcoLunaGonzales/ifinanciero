<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $item_3=$_GET['r'];
  $s=$_GET['s'];
  $u=$_GET['u'];

    $sqlFilter1 = str_replace("IdOficina", "p.cod_unidadorganizacional", $s);
    $sqlFilter2 = "and ".str_replace("IdArea", "p.cod_area", $sqlFilter1);
    $sqlFilter = str_replace("%20", " ", $sqlFilter2);
?>
  <input type="hidden" name="id_servicioibnored" value="<?=$q?>" id="id_servicioibnored"/>
  <input type="hidden" name="id_servicioibnored_rol" value="<?=$item_3?>" id="id_servicioibnored_rol"/>
  <input type="hidden" name="id_servicioibnored_s" value="<?=$s?>" id="id_servicioibnored_s"/>
  <input type="hidden" name="id_servicioibnored_u" value="<?=$u?>" id="id_servicioibnored_u"/>
<?php
}else{
  $sqlFilter="";
  $item_3=0;
}
// Preparamos
$stmt = $dbh->prepare("SELECT p.*,e.nombre as estado_plantilla, u.abreviatura as unidad,a.abreviatura as area from plantillas_servicios p,unidades_organizacionales u, areas a, estados_plantillascosto e 
  where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and e.codigo=p.cod_estadoplantilla and p.cod_estadoreferencial!=2 $sqlFilter order by codigo");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('abreviatura', $abreviatura);
$stmt->bindColumn('cod_unidadorganizacional', $codUnidad);
$stmt->bindColumn('cod_area', $codArea);
$stmt->bindColumn('unidad', $unidad);
$stmt->bindColumn('area', $area);
$stmt->bindColumn('fecha_registro', $fechaAuditoria);
$stmt->bindColumn('dias_auditoria', $diasAuditoria);
$stmt->bindColumn('cod_estadoplantilla', $codEstado);
$stmt->bindColumn('estado_plantilla', $estadoPlantilla);

$item_1=2706;
?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Gesti&oacute;n  de <?=$moduleNamePlural?></h4>
                </div>
                <div class="card-body">
                  <div class="" id="data_comprobantes">
                    <table class="table table-condensed" id="tablePaginator">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th class="text-left">Nombre</th>
                          <th>Abreviatura</th>
                          <th>Unidad</th>
                          <th>Area</th>
                          <th>Fecha Registro</th>
                          <!--<th>D&iacute;as Audiror&iacute;a</th>-->
                          <th class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
						$index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          switch ($codEstado) {
                            case 1:
                              $nEst=1;$barEstado="progress-bar-default";$btnEstado="btn-default";$textEstado="text-info";
                            break;
                            case 2:
                              $nEst=2;$barEstado="progress-bar-danger";$btnEstado="btn-danger";$textEstado="text-danger";
                            break;
                            case 3:
                              $nEst=3;$barEstado="progress-bar-success";$btnEstado="btn-success";$textEstado="text-success";
                            break;
                          }
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td class="text-left"><?=$nombre;?></td>
                          <td><?=$abreviatura;?></td>
                          <td><?=$unidad;?></td>
                          <td><?=$area;?></td>
                          <td><?=strftime('%d/%m/%Y',strtotime($fechaAuditoria));?></td> 
                          <!--<td><?=$diasAuditoria;?></td>-->
                          <td class="td-actions text-right">
                            <div class="btn-group dropdown">
                              <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">list</i> <?=$estadoPlantilla;?>
                              </button>
                              <div class="dropdown-menu">    
                                <?php 
                                if($codEstado==1){
                                  if(isset($_GET['q'])){
                                    ?>
                                   <a href="#" onclick="mostrarCambioEstadoObjeto(<?=$codigo?>)" class="dropdown-item">
                                    <i class="material-icons text-warning">dns</i> Cambiar Estado
                                  </a>
                                    <!--<a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=3&q=<?=$q?>" class="dropdown-item">
                                    <i class="material-icons text-success">offline_pin</i> Aprobar Plantilla
                                 </a>
                                 <a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=2&q=<?=$q?>" class="dropdown-item">
                                    <i class="material-icons text-danger">clear</i> Anular Plantilla
                                 </a>--><?php 
                                  }else{
                                    ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=3" class="dropdown-item">
                                    <i class="material-icons text-success">offline_pin</i> Aprobar Plantilla
                                 </a>
                                 <a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=2" class="dropdown-item">
                                    <i class="material-icons text-danger">clear</i> Anular Plantilla
                                 </a><?php 
                                  }
                                 
                                }else{
                                   if(isset($_GET['q'])){
                                    ?>
                                    <a href="#" onclick="mostrarCambioEstadoObjeto(<?=$codigo?>)" class="dropdown-item">
                                    <i class="material-icons text-warning">dns</i> Cambiar Estado
                                  </a>
                                   <!--<a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&q=<?=$q?>" class="dropdown-item">
                                    <i class="material-icons text-dark">reply</i> Deshacer Cambios
                                   </a>-->
                                    <?php
                                   }else{
                                    ?>
                                <a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1" class="dropdown-item">
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
              </div>
      				<div class="card-footer fixed-bottom">
                <!--<a href="#" onclick="javascript:window.open('<?=$urlRegister2;?>')" class="<?=$buttonNormal;?>">Registrar</a>-->
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
                        <button type="button" id="boton_guardarsim" class="btn btn-default" onclick="cambiarEstadoObjetoPlan()">Cambiar Estado</button>
                      </div> 
                </div>   
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->