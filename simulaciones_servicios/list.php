<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
if(isset($_GET['q'])){
  $q=$_GET['q'];
}
$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$dbh = new Conexion();

// Preparamos
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $s=$_GET['s'];
  $u=$_GET['u'];
  if(isset($_GET['s'])){
    $s=$_GET['s'];
    $u=$_GET['u'];
    $arraySql=explode("IdArea=",$_GET['s']);
    $codigoArea=trim($arraySql[1]);

    $sqlAreas="and p.cod_area=".$codigoArea;
  }
  //cargarDatosSession();
  $stmt = $dbh->prepare("SELECT sc.*,es.nombre as estado,c.nombre as cliente from simulaciones_servicios sc join estados_simulaciones es on sc.cod_estadosimulacion=es.codigo join clientes c on c.codigo=sc.cod_cliente join plantillas_servicios p on p.codigo=sc.cod_plantillaservicio where sc.cod_estadoreferencial=1 and sc.cod_responsable=$globalUser $sqlAreas order by sc.fecha desc");
}else{
  $s=0;
  $u=0;
  $stmt = $dbh->prepare("SELECT sc.*,es.nombre as estado,c.nombre as cliente from simulaciones_servicios sc join estados_simulaciones es on sc.cod_estadosimulacion=es.codigo join clientes c on c.codigo=sc.cod_cliente join plantillas_servicios p on p.codigo=sc.cod_plantillaservicio where sc.cod_estadoreferencial=1 and sc.cod_responsable=$globalUser order by sc.fecha desc");
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
$stmt->bindColumn('idServicio', $idServicioX);

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
                    <table class="table table-condensed table-striped" id="tablePaginator">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Numero</th>
                          <th>Cliente</th>
                          <th>Responsable</th>
                          <th>Fecha</th>
                          <th>Estado</th>
                          <th>Servicio</th>
                          <th class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                        $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $codigoServicio="SIN CODIGO";
                          $sql="SELECT codigo FROM ibnorca.servicios where idServicio=$idServicioX";
                          $stmt1=$dbh->prepare($sql);
                          $stmt1->execute();
                           while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                             $codigoServicio=$row1['codigo'];
                           }
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
                              $nEst=60;$barEstado="progress-bar-warning";$btnEstado="btn-warning";
                            break;
                            case 5:
                              $nEst=100;$barEstado="progress-bar-success";$btnEstado="btn-success";
                            break;
                          }
                          $estiloFila="";$iconoAdjudicado="";
                          if($codEstado==5){
                            $estiloFila="bg-plomo";
                            $iconoAdjudicado="check_circle";
                          }
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td class="font-weight-bold"><?=$nombre;?></td>
                          <td><?=$cliente?></td>
                          <td>
                                 <img src="assets/img/faces/persona1.png" width="20" height="20"/><?=$responsable;?>
                          </td>
                          <td><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                          <td class="font-weight-bold"><i class="material-icons text-warning"><?=$iconoAdjudicado?></i> <?=$estado;?></td> 
                             <!--<?=$nEst?> % <div class="progress">
                               <div class="progress-bar <?=$barEstado?>" role="progressbar" aria-valuenow="<?=$nEst?>" aria-valuemin="0" aria-valuemax="100" style="width:<?=$nEst?>%">
                                  <span class="sr-only"><?=$nEst?>% Complete</span>
                               </div>
                             </div>
                          </td>--> 
                          <td><?=$codigoServicio?></td>
                          <td class="td-actions text-right">
                            <?php
                              if($codEstado==4||$codEstado==3||$codEstado==5){
                               
                            ?>
                            <div class="btn-group dropdown">
                              <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">list</i> <?=$estado;?>
                              </button>
                              <div class="dropdown-menu">
                                <?php 
                                if(isset($_GET['q'])){
                                 if($codEstado==4){
                                 ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>" class="dropdown-item">
                                    <i class="material-icons text-danger">clear</i> Cancelar solicitud
                                 </a>
                                 <?php 
                                 }?>
                                 <a href="<?=$urlVer;?>?cod=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>" class="dropdown-item">
                                    <i class="material-icons text-warning">bar_chart</i> Ver Propuesta
                                 </a>
                                 <!--<a href="#" class="dropdown-item">
                                    <i class="material-icons text-warning">bar_chart</i> Anular propuesta
                                 </a>--> 
                                 <?php
                               }else{
                                if($codEstado==4){
                                 ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&admin=0" class="dropdown-item">
                                    <i class="material-icons text-danger">clear</i> Cancelar solicitud
                                 </a>
                                 <?php 
                                 }?>
                                 <a href="<?=$urlVer;?>?cod=<?=$codigo;?>" class="dropdown-item">
                                    <i class="material-icons text-warning">bar_chart</i> Ver Propuesta
                                 </a>
                                 <?php
                               }?>
                                 
                  
                              </div>
                            </div>                           
                            <?php
                             if($codEstado==5){
                               $anteriorCod=obtenerCodigoSolicitudRecursosSimulacion(2,$codigo);
                               if(isset($_GET['q'])){
                                  ?><a href="<?=$urlSolicitudRecursos?>?cod=<?=$codigo?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>" target="_self" title="Solicitud De Recursos"class="btn btn-danger">
                                    <i class="material-icons">content_paste</i>
                                 </a>
                                 <a title="Imprimir Solicitud de Recursos" href='#' onclick="javascript:window.open('solicitudes/imp.php?sol=<?=$anteriorCod;?>&mon=1')" class="btn btn-primary">
                                     <i class="material-icons"><?=$iconImp;?></i>
                                 </a> 
                                 <a class="btn btn-warning" title="Solicitud de Facturación" href='<?=$urlSolicitudfactura;?>&cod=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>'>
                                   <i class="material-icons" >receipt</i>                              
                                 </a>
                                 <?php 
                                 if($idServicioX>0){
                                   ?>
                                 <button title="Servicio Creado C: <?=$codigoServicio?>" class="btn btn-success" onclick="">
                                    <i class="material-icons">check</i>
                                  </button>
                                  <?php  
                                  }else{
                                    ?>
                                    <button title="Crear Servicio" class="btn btn-danger" onclick="alerts.showSwal('warning-message-crear-servicio','<?=$urlRegisterNewServicio;?>&codigo=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>')">
                                    <i class="material-icons">add</i>
                                  </button>                                 
                                  <?php 
                                  } 
                                }else{
                                ?><a href="<?=$urlSolicitudRecursos?>?cod=<?=$codigo?>" target="_blank" title="Solicitud De Recursos"class="btn btn-danger">
                                    <i class="material-icons">content_paste</i>
                                 </a>
                                 <a title="Imprimir Solicitud de Recursos" href='#' onclick="javascript:window.open('solicitudes/imp.php?sol=<?=$anteriorCod;?>&mon=1')" class="btn btn-primary">
                                     <i class="material-icons"><?=$iconImp;?></i>
                                 </a> 
                                 <a class="btn btn-warning" title="Solicitud de Facturación" href='<?=$urlSolicitudfactura;?>&cod=<?=$codigo;?>'>
                                   <i class="material-icons" >receipt</i>                              
                                 </a>
                                 <?php 
                                 if($idServicioX>0){
                                   ?>
                                 <button title="Servicio Creado C: <?=$codigoServicio?>" class="btn btn-success" onclick="">
                                    <i class="material-icons">check</i>
                                  </button>
                                  <?php  
                                  }else{
                                    ?>
                                    <button title="Crear Servicio" class="btn btn-danger" onclick="alerts.showSwal('warning-message-crear-servicio','<?=$urlRegisterNewServicio;?>&codigo=<?=$codigo;?>')">
                                    <i class="material-icons">add</i>
                                  </button>                                
                                  <?php 
                                  } 
                                
                                }
                              }    
                              }else{
                                if(isset($_GET['q'])){
                                 ?>
                                  <a title="Editar Simulación - Detalle" target="_self" href='<?=$urlRegister;?>?cod=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>' class="btn btn-info">
                                    <i class="material-icons"><?=$iconEdit;?></i>
                                  </a>
                                  <button title="Eliminar Simulación" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>')">
                                    <i class="material-icons"><?=$iconDelete;?></i>
                                  </button>
                                 <?php 
                                }else{
                                 ?>
                                  <a title="Editar Simulación - Detalle" target="_blank" href='<?=$urlRegister;?>?cod=<?=$codigo;?>' class="btn btn-info">
                                    <i class="material-icons"><?=$iconEdit;?></i>
                                  </a>
                                  <button title="Eliminar Simulación" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
                                    <i class="material-icons"><?=$iconDelete;?></i>
                                  </button>
                                 <?php  
                                }
                              ?>                            
                            
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
              //if($globalAdmin==1){
                if(isset($_GET['q'])){
                  ?><a href="<?=$urlRegister2;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>" target="_self" class="<?=$buttonNormal;?>">Registrar</a><?php
                }else{
                  ?><a href="#" onclick="javascript:window.open('<?=$urlRegister2;?>')" class="<?=$buttonNormal;?>">Registrar</a><?php
                }  
              //} 
               ?>
              </div>      
            </div>
          </div>  
        </div>
    </div>



