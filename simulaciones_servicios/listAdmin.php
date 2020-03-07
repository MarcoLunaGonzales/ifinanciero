<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

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

?>

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
                          <th>Nombre</th>
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
                              $nEst=100;$barEstado="progress-bar-success";$btnEstado="btn-success";
                            break;
                            case 4:
                              $nEst=60;$barEstado="progress-bar-info";$btnEstado="btn-info";
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
                                <a href="<?=$urlVer;?>?cod=<?=$codigo;?>&admin=0" class="dropdown-item">
                                    <i class="material-icons text-info">bar_chart</i> Ver Propuesta
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
                <a href="<?=$urlList2?>" class="btn btn-info"><i class="material-icons">refresh</i> Refrescar</a>
              </div>      
            </div>
          </div>  
        </div>
    </div>