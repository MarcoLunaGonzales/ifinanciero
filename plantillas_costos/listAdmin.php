<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT p.*,e.nombre as estado_plantilla, u.abreviatura as unidad,a.abreviatura as area from plantillas_costo p,unidades_organizacionales u, areas a, estados_plantillascosto e 
  where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and e.codigo=p.cod_estadoplantilla and p.cod_estadoreferencial!=2 order by codigo");
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
$stmt->bindColumn('utilidad_minimalocal', $utilidadLocal);
$stmt->bindColumn('utilidad_minimaexterno', $utilidadExterno);
$stmt->bindColumn('cod_estadoplantilla', $codEstado);
$stmt->bindColumn('estado_plantilla', $estadoPlantilla);
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
                          <th>Utilidad Ibnorca</th>
                          <th>Utilidad Fuera Ibnorca</th>
                          <!--<th>Estado</th>-->
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
                          <td><?=$utilidadLocal;?> %</td> 
                          <td><?=$utilidadExterno;?> %</td>
                           <!--<td class="<?=$textEstado?>"><?=$estadoPlantilla;?></td>-->
                          <td class="td-actions text-right">
                            <div class="btn-group dropdown">
                              <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">list</i> <?=$estadoPlantilla;?>
                              </button>
                              <div class="dropdown-menu">
                                <!--<a href="<?=$urlVer;?>?cod=<?=$codigo;?>&admin=0" class="dropdown-item">
                                    <i class="material-icons text-info">bar_chart</i> Ver simulacion
                                 </a>-->     
                                <?php 
                                if($codEstado==1){
                                 ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=3" class="dropdown-item">
                                    <i class="material-icons text-success">offline_pin</i> Aprobar Plantilla
                                 </a>
                                 <a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=2" class="dropdown-item">
                                    <i class="material-icons text-danger">clear</i> Anular Plantilla
                                 </a><?php 
                                }else{
                                ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1" class="dropdown-item">
                                    <i class="material-icons text-dark">reply</i> Deshacer Cambios
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