<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo where sr.cod_estadoreferencial=1 and sr.cod_estadosolicitudrecurso!=1 order by sr.codigo");
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

?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">content_paste</i>
                  </div>
                  <h4 class="card-title"><b>Gesti&oacute;n de <?=$moduleNamePlural?></b></h4>
                </div>
                <div class="card-body">
                    <table class="table table-condesed" id="tablePaginator">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Unidad</th>
                          <th>Area</th>
                          <th>Responsable</th>
                          <th>Fecha</th>
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
                              $nEst=60;$barEstado="progress-bar-info";$btnEstado="btn-info";
                            break;
                          }
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td><?=$unidad;?></td>
                          <td><?=$area;?></td>
                          <td>
                                 <img src="assets/img/faces/persona1.png" width="20" height="20"/><?=$solicitante;?>
                          </td>
                          <td><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                          <td class="td-actions text-right">
                            <div class="btn-group dropdown">
                              <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">list</i> <?=$estado;?>
                              </button>
                              <div class="dropdown-menu">
                                <a href="<?=$urlVer;?>?cod=<?=$codigo;?>&admin=0" class="dropdown-item">
                                    <i class="material-icons text-info">bar_chart</i> Ver Solicitud
                                 </a>
                              
                                <?php 
                                if($codEstado==4){
                                 ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=3" class="dropdown-item">
                                    <i class="material-icons text-success">offline_pin</i> Aprobar Solicitud
                                 </a>
                                 <a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1" class="dropdown-item">
                                    <i class="material-icons text-dark">report</i> Rechazar Solicitud
                                 </a>
                                 <a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=2" class="dropdown-item">
                                    <i class="material-icons text-danger">clear</i> Anular Solicitud
                                 </a><?php 
                                }else{
                                ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=4" class="dropdown-item">
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
              <div class="card-footer fixed-bottom">
                <a href="#" onclick="javascript:window.open('<?=$urlRegister2;?>')" class="<?=$buttonNormal;?>">Registrar</a>
              </div>      
            </div>
          </div>  
        </div>
    </div>