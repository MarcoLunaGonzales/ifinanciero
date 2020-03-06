<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
if(isset($_GET['q'])){
  $q=$_GET['q'];
}
$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

// Preparamos
if(isset($_GET['q'])){
  $stmt = $dbh->prepare("SELECT sc.*,es.nombre as estado,c.nombre as cliente from simulaciones_servicios sc join estados_simulaciones es on sc.cod_estadosimulacion=es.codigo join clientes c on c.codigo=sc.cod_cliente where sc.cod_estadoreferencial=1 and sc.idServicio=$q order by sc.codigo");
}else{
  $stmt = $dbh->prepare("SELECT sc.*,es.nombre as estado,c.nombre as cliente from simulaciones_servicios sc join estados_simulaciones es on sc.cod_estadosimulacion=es.codigo join clientes c on c.codigo=sc.cod_cliente where sc.cod_estadoreferencial=1 order by sc.codigo");
}

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
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">polymer</i>
                  </div>
                  <h4 class="card-title"><b><?=$moduleNamePlural?></b></h4>
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
                          <th class="text-right">Actions</th>
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
                              $nEst=60;$barEstado="progress-bar-warning";$btnEstado="btn-warning";
                            break;
                          }
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td><?=$nombre;?></td>
                          <td><?=$cliente?></td>
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
                            <?php
                              if($codEstado==4||$codEstado==3){
                            ?>
                            <div class="btn-group dropdown">
                              <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">list</i> <?=$estado;?>
                              </button>
                              <div class="dropdown-menu">
                                <?php 
                                 if($codEstado==4){
                                 ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&admin=0" class="dropdown-item">
                                    <i class="material-icons text-danger">clear</i> Cancelar solicitud
                                 </a>
                                 <?php 
                                 }?>
                                 <a href="<?=$urlVer;?>?cod=<?=$codigo;?>" class="dropdown-item">
                                    <i class="material-icons text-warning">bar_chart</i> Ver simulacion
                                 </a>
                                 <a href="<?=$urlSolicitudRecursos?>?cod=<?=$codigo?>" class="dropdown-item">
                                    <i class="material-icons text-info">list</i> Solicitud de Recursos
                                 </a> 
                              </div>
                            </div>                           
                            <?php    
                              }else{
                              ?>
                            <!--<a href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=4&admin=0' itle="Enviar Solicitud" class="btn btn-warning">
                              <i class="material-icons">send</i>
                            </a>-->  
                            <a title="Editar Simulación - Detalle" target="_blank" href='<?=$urlRegister;?>?cod=<?=$codigo;?>' class="btn btn-info">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <!--<a title="Editar Simulación" href='<?=$urlEdit;?>&codigo=<?=$codigo;?>' class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>-->
                            <button title="Eliminar Simulación" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button>
                            <a class="btn btn-warning" title="Solicitud de Facturación" href='<?=$urlSolicitudfactura;?>&cod=<?=$codigo;?>'>
                              <i class="material-icons" >description</i>
                            </a>
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
              </div>
              <div class="card-footer fixed-bottom">
               <?php 
              if($globalAdmin==1){
                if(isset($_GET['q'])){
                  ?><a href="<?=$urlRegister2;?>&q=<?=$q?>" target="_self" class="<?=$buttonNormal;?>">Registrar</a><?php
                }else{
                  ?><a href="#" onclick="javascript:window.open('<?=$urlRegister2;?>')" class="<?=$buttonNormal;?>">Registrar</a><?php
                }  
              } 
               ?>
              </div>      
            </div>
          </div>  
        </div>
    </div>