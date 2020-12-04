<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];

$item_3=0;
$queryTipoCurso="";
$tituloPropuestaFiltro="";
$estiloFormacion='';
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $u=$_GET['u'];
  $s="";
  if(isset($_GET['s'])){
    $s=$_GET['s'];
  }
  if(gestorDeCursosFormacion($q)>0){
    $queryTipoCurso=" and sc.cod_tipocurso!=3";
    $tituloPropuestaFiltro="<h4 style='color:#C70039;'>LISTA: FORMACIÓN</h4>";
    $estiloFormacion='style="background:#C70039;color:white;"';
   }else if(gestorDeCursosComercializacion($q)>0){
    $queryTipoCurso=" and sc.cod_tipocurso=3";
    $tituloPropuestaFiltro="<h4 style='color:#FF5733;'>LISTA: COMERCIALIZACIÓN</h4>";
    $estiloFormacion='style="background:#FF5733;color:white;"';
  }else{
    $queryTipoCurso=" and sc.cod_tipocurso=9999"; //9999 -> para que no encuentre ningun registro
    $tituloPropuestaFiltro="USTED NO PUEDE GESTIONAR LAS PROPUESTAS";
  }
  ?>
  <input type="hidden" name="id_servicioibnored" value="<?=$q?>" id="id_servicioibnored"/>
  <input type="hidden" name="idPerfil" value="<?=$u?>" id="idPerfil"/>
  <input type="hidden" name="ss" value="<?=$s?>" id="ss"/>
  <input type="hidden" name="id_servicioibnored_u" value="<?=$u?>" id="id_servicioibnored_u"/>
<?php

}else{

}

$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT sc.*,es.nombre as estado from simulaciones_costos sc join estados_simulaciones es on sc.cod_estadosimulacion=es.codigo where sc.cod_estadoreferencial=1 and sc.cod_estadosimulacion in (4,3) $queryTipoCurso order by codigo desc");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('observacion', $observacion);
$stmt->bindColumn('fecha', $fecha);
$stmt->bindColumn('cod_plantillacosto', $codPlantilla);
$stmt->bindColumn('cod_estadosimulacion', $codEstado);
$stmt->bindColumn('cod_responsable', $codResponsable);
$stmt->bindColumn('estado', $estado);

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
                  <h4 class="card-title"><b> Gestionar <?=$moduleNamePlural?> <small class="text-muted"><?=$tituloPropuestaFiltro?></small></b></h4>
                </div>
                <div class="card-body">
                    <table class="table" id="tablePaginator">
                      <thead>
                        <tr <?=$estiloFormacion?>>
                          <!--<th class="text-center">#</th>-->
                          <th>Codigo</th>
                          <th>Nombre</th>
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
                          <!---<td align="center"><?=$index;?></td>-->
                          <td><?=$codigo;?></td>
                          <td><?=$nombre;?></td>
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
                            if($codEstado==4){
                              if(isset($_GET['q'])){
                                 ?> 
                            <a title="Editar Propuesta - Detalle" target="_blank" href='<?=$urlRegister;?>?cod=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&admin=1' class="btn btn-info">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                              <?php 
                             } else{
                               ?> 
                            <a title="Editar Propuesta - Detalle" target="_blank" href='<?=$urlRegister;?>?cod=<?=$codigo;?>&admin=1' class="btn btn-info">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                              <?php 
                             }  
                            }?>
                            <div class="btn-group dropdown">
                              <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">list</i> <?=$estado;?>
                              </button>
                              <div class="dropdown-menu">
                               <?php 
                               if(isset($_GET['q'])){
                                ?>
                                <a href="<?=$urlVer;?>?cod=<?=$codigo;?>&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>" class="dropdown-item">
                                    <i class="material-icons text-info">bar_chart</i> Ver Propuesta
                                 </a>
                                <?php 
                                if($codEstado==4){
                                 ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=3&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>" class="dropdown-item">
                                    <i class="material-icons text-success">offline_pin</i> Aprobar Solicitud
                                 </a>
                                 <a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>" class="dropdown-item">
                                    <i class="material-icons text-dark">report</i> Rechazar Solicitud
                                 </a>
                                 <a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=2&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>" class="dropdown-item">
                                    <i class="material-icons text-danger">clear</i> Anular Solicitud
                                 </a><?php 
                                }else{
                                ?><!--<a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=4&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>" class="dropdown-item">
                                    <i class="material-icons text-dark">reply</i> Deshacer Cambios
                                 </a>-->
                                 <?php 
                                }
                                ?>
                                <?php
                               }else{
                                ?>
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
                                ?><!--<a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=4" class="dropdown-item">
                                    <i class="material-icons text-dark">reply</i> Deshacer Cambios
                                 </a>-->
                                 <?php 
                                }
                                ?> 
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
                <?php 
                if(isset($_GET['q'])){
                ?><a href="<?=$urlList2?>&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>" class="btn btn-info"><i class="material-icons">refresh</i> Refrescar</a><?php
                }else{
                 ?><a href="<?=$urlList2?>" class="btn btn-info"><i class="material-icons">refresh</i> Refrescar</a><?php
                } 
                ?>
              </div>      
            </div>
          </div>  
        </div>
    </div>