<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');

/*SACAR LOS ESTADOS DEL FINANCIERO O DE IBNORCA*/
$configuracionEstados=obtenerValorConfiguracion(100);


if(isset($_GET['q'])){
  $q=$_GET['q'];
}
$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$dbh = new Conexion();



// Datos de Filtro
$start          = isset($_POST['date_start'])?$_POST['date_start']:"";
$end            = isset($_POST['date_end'])?$_POST['date_end']:"";
$cod_cliente    = isset($_POST['cod_cliente'])?$_POST['cod_cliente']:"";
$cod_personal   = isset($_POST['personal'])?$_POST['personal']:"";
$filter_list    = (!empty($start)?(" AND sc.fecha >= '$start' "):"").
(!empty($end)?(" AND sc.fecha <= '$end' "):"").
(!empty($cod_cliente)?(" AND sc.cod_cliente = '$cod_cliente' "):"").
(!empty($cod_personal)?(" AND sc.cod_responsable = '$cod_personal' "):"");

$sql="";
// Preparamos
$listSC = "";
// URL actual
$query_q = isset($_GET['q'])?("&q=".$_GET['q']):"";
$query_s = isset($_GET['s'])?("&s=".$_GET['s']):"";
$query_u = isset($_GET['u'])?("&u=".$_GET['u']):"";
$listSC = $query_q.$query_s.$query_u;
if(isset($_GET['q'])){

  $q=isset($_GET['q'])?$_GET['q']:"";
  $s=isset($_GET['s'])?$_GET['s']:"";
  $u=isset($_GET['u'])?$_GET['u']:"";

  $sqlAreas = "";
  if(isset($_GET['s'])){
    $s=$_GET['s'];
    $u=$_GET['u'];

    if(strpos($_GET['s'],"=")){
      $arraySql=explode("IdArea=",$_GET['s']);
      $codigoArea=trim($arraySql[1]);
      $sqlAreas="and p.cod_area=".$codigoArea;
    }else{
      $arraySql=explode("IdArea in",$_GET['s']);
      $codigoArea=trim($arraySql[1]);
      $sqlAreas="and p.cod_area in ".$codigoArea;
    }

    
  }
  //cargarDatosSession();
  $sql="SELECT p.cod_unidadorganizacional,p.cod_area,sc.*,es.nombre as estado,c.nombre as cliente 
    from simulaciones_servicios sc 
    join estados_simulaciones es on sc.cod_estadosimulacion=es.codigo 
    join clientes c on c.codigo=sc.cod_cliente 
    join plantillas_servicios p on p.codigo=sc.cod_plantillaservicio
    where sc.cod_estadoreferencial=1 ".
    $filter_list.
    (empty($filter_list)?(' and sc.cod_responsable='.$globalUser):''). 
    " $sqlAreas 
    order by sc.fecha desc
    LIMIT 0, 50";

  $stmt = $dbh->prepare($sql);
}else{
  $s=0;
  $u=0;
  $sql="SELECT p.cod_unidadorganizacional,p.cod_area,sc.*,es.nombre as estado,c.nombre as cliente 
from simulaciones_servicios sc 
join estados_simulaciones es on sc.cod_estadosimulacion=es.codigo 
join clientes c on c.codigo=sc.cod_cliente 
join plantillas_servicios p on p.codigo=sc.cod_plantillaservicio
where sc.cod_estadoreferencial=1 ".
(empty($filter_list)?(' and (sc.cod_responsable='.$globalUser.' or sc.cod_responsableactual='.$globalUser.') '):''). 
$filter_list.
" order by sc.codigo desc limit 0,50";
  $stmt = $dbh->prepare($sql);
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
$stmt->bindColumn('cod_unidadorganizacional', $codUnidadX);
$stmt->bindColumn('cod_area', $codAreaX);
$stmt->bindColumn('estado_registro', $estadoRegistroX);
$stmt->bindColumn('cod_unidadorganizacional', $oficinaX);
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
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">polymer</i>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                        <h4 class="card-title"><b> Gestionar <?=$moduleNamePlural?></b></h4>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group" align="right">
                          <button type="button" class="btn btn-warning btn-round btn-fab btn-sm" data-toggle="modal" data-target="#modalBuscadorFacturas">
                              <i class="material-icons" title="Buscador Avanzado">search</i>
                          </button>                               
                      </div>
                    </div>
                  </div>
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
                          if($codUnidadX==""){
                            $codUnidadX=0;
                          }
                          if($codAreaX==""){
                            $codAreaX=0;
                          }
                          $unidadX=abrevUnidad_solo($codUnidadX);
                          $areaX=abrevArea_solo($codAreaX);
                          $codigoServicio="SIN CODIGO";
                          $sql="SELECT codigo FROM ibnorca.servicios where idServicio='$idServicioX'";
                          $stmt1=$dbh->prepare($sql);
                          $stmt1->execute();
                           while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                             $codigoServicio=$row1['codigo'];
                           }
                          $responsable=namePersonal($codResponsable);

                          $idEstadoZ=0;
                          //revisamos la configuracion de los estados
                          if($configuracionEstados==1){
                            $sql2="SELECT ibnorca.id_estadoobjeto(2707, $codigo) AS IdEstado, ibnorca.d_clasificador(ibnorca.id_estadoobjeto(2707, $codigo)) AS descr";
                            $stmt2 = $dbh -> prepare($sql2);
                            $stmt2 -> execute();
                            if($row2 = $stmt2 -> fetch(PDO::FETCH_ASSOC)){
                                $idEstadoExt=$row2['IdEstado'];
                                $nombreEstadoExt=$row2['descr'];
                            }
                            $idEstadoExt=0;
                            $nombreEstadoExt="";
                            $sql3="SELECT e.codigo, e.nombre from estados_simulaciones e where e.codigo_ibnorca=$idEstadoExt";
                            $stmt3 = $dbh -> prepare($sql3);
                            $stmt3 -> execute();
                            if($row3 = $stmt3 -> fetch(PDO::FETCH_ASSOC)){
                                $idEstadoZ=$row3['codigo'];
                                $nombreEstadoZ=$row3['nombre'];
                            }

                            $codEstado=$idEstadoZ;
                            $estado=$nombreEstadoExt;
                          }

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
                          if($estadoRegistroX==0){
                            $estado="<b class='text-danger'>Con Errores!</b>";
                          }
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td class="font-weight-bold"><?=$nombre;?> - <?=$areaX?></td>
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
                              <div class="dropdown-menu menu-fixed-sm-table menu-fixed-sm-table">
                                <?php 
                                if(isset($_GET['q'])){
                                 if($codEstado==4){
                                 ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>" class="dropdown-item">
                                    <i class="material-icons text-danger">clear</i> Cancelar solicitud
                                 </a>
                                 <?php 
                                 }else{
                                  if($codEstado==3){
                                   ?><a href="#" onclick="alerts.showSwal('warning-message-crear-servicio','<?=$urlEdit2?>?cod=<?=$codigo?>&estado=5&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>')" class="dropdown-item">
                                    <i class="material-icons text-success">emoji_events</i> EJECUTAR
                                 </a><a title="Editar Propuesta - Detalle" target="_blank" href='<?=$urlRegister;?>?cod=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&edit=1' class="dropdown-item">
                                    <i class="material-icons text-success"><?=$iconEdit;?></i> Editar Propuesta
                                 </a><?php
                                  }else{
                                   ?>
                                   <a title="Editar Propuesta - Detalle" target="_blank" href='<?=$urlRegister;?>?cod=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>' class="dropdown-item">
                                    <i class="material-icons text-success"><?=$iconEdit;?></i> Editar Propuesta
                                 </a> 
                                   <?php  
                                  }
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
                                 }else{
                                   if($codEstado==3){
                                   ?><a href="#" onclick="alerts.showSwal('warning-message-crear-servicio','<?=$urlEdit2?>?cod=<?=$codigo?>&estado=5&admin=0')" class="dropdown-item">
                                      <i class="material-icons text-success">emoji_events</i> EJECUTAR
                                   </a>
                                   <a title="Editar Propuesta - Detalle" target="_blank" href='<?=$urlRegister;?>?cod=<?=$codigo;?>&edit=1' class="dropdown-item">
                                    <i class="material-icons text-success"><?=$iconEdit;?></i> Editar Propuesta
                                 </a><?php
                                   }else{
                                    ?>
                                   <a title="Editar Propuesta - Detalle" target="_blank" href='<?=$urlRegister;?>?cod=<?=$codigo;?>' class="dropdown-item">
                                    <i class="material-icons text-success"><?=$iconEdit;?></i> Editar Propuesta
                                 </a> 
                                   <?php
                                   }
                                 }?>
                                 <a href="<?=$urlVer;?>?cod=<?=$codigo;?>" class="dropdown-item">
                                    <i class="material-icons text-warning">bar_chart</i> Ver Propuesta
                                 </a>
                                 <?php
                               }?>
                                 
                  
                              </div>
                            </div>                           
                            <?php
                            if(isset($_GET['q'])){
                                  ?>
                                  <div class="btn-group dropdown">
                                    <!--button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" title="Ver Oferta en PDF" aria-expanded="false">
                                      <i class="material-icons text-dark">picture_as_pdf</i>
                                    </button>
                                    <div class="dropdown-menu menu-fixed-sm-table menu-fixed-sm-table">

                                      <a href="<?=$urlOferta?>?cod=<?=$codigo;?>&cod_area=<?=$codAreaX?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$idServicioX?>" class="dropdown-item"  target="_blank">
                                         <i class="material-icons text-danger">picture_as_pdf</i> PDF
                                      </a> 
                                    </div-->
                                  </div>
                             <?php
                             }else{
                                ?><div class="btn-group dropdown">
                                    <!--button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" title="Ver Oferta en PDF" aria-expanded="false">
                                      <i class="material-icons text-dark">picture_as_pdf</i>
                                    </button>
                                    <div class="dropdown-menu menu-fixed-sm-table">
                                      <a href="<?=$urlOferta?>?cod=<?=$codigo;?>&cod_area=<?=$codAreaX?>" class="dropdown-item"  target="_blank">
                                         <i class="material-icons text-danger">picture_as_pdf</i> PDF
                                      </a> 
                                    </div-->
                                  </div><?php
                             }     
                             if($codEstado==5){
                               $anteriorCod=obtenerCodigoSolicitudRecursosSimulacion(2,$codigo);
                               if(isset($_GET['q'])){
                                  ?>      
                                  <a href="solicitudes/registerSolicitudDetalle.php?sim=<?=$codigo?>&det=2&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$idServicioX?>" target="_self" title="Solicitud De Recursos"class="btn btn-danger">
                                    <i class="material-icons">content_paste</i>
                                 </a>
                                 <a title="Imprimir Solicitud de Recursos" href='#' onclick="javascript:window.open('solicitudes/imp.php?sol=<?=$anteriorCod;?>&mon=1')" class="btn btn-primary">
                                     <i class="material-icons"><?=$iconImp;?></i>
                                 </a> 

                                 <a class="btn btn-warning" title="Solicitud de Facturación" href='<?=$urlSolicitudfactura;?>&cod=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$idServicioX?>'>
                                   <i class="material-icons">receipt</i>                              
                                 </a>

                                 <?php 
                                 if($idServicioX>0){
                                   ?>
                                 <button title="Servicio Creado <?=$codigoServicio?>" class="btn btn-success" onclick="">
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
                                ?>
                                  
                                <a href="solicitudes/registerSolicitudDetalle.php?sim=<?=$codigo?>&det=2" target="_blank" title="Solicitud De Recursos"class="btn btn-danger">
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
                                 <button title="Servicio Creado <?=$codigoServicio?>" class="btn btn-success" onclick="">
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
                                  if($estadoRegistroX!=0){
                                    ?>
                                   <a title="Editar Propuesta - Detalle" target="_self" href='<?=$urlRegister;?>?cod=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>' class="btn btn-info">
                                    <i class="material-icons"><?=$iconEdit;?></i>
                                  </a>
                                    <?php 
                                  }
                                 ?>
                                  <button title="Eliminar Propuesta" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>')">
                                    <i class="material-icons"><?=$iconDelete;?></i>
                                  </button>
                                 <?php 
                                }else{
                                  if($estadoRegistroX!=0){
                                    ?>
                                   <a title="Editar Propuesta - Detalle" target="_blank" href='<?=$urlRegister;?>?cod=<?=$codigo;?>' class="btn btn-info">
                                    <i class="material-icons"><?=$iconEdit;?></i>
                                  </a>
                                    <?php 
                                  }
                                 ?>
                                  <button title="Eliminar Propuesta" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
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
                  ?><a href="<?=$urlRegister2;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>" target="_self" class="<?=$buttonNormal;?>">Registrar</a><a href="#" target="_blank" class="btn btn-warning float-right"><i class="material-icons">fullscreen</i> Ver Pantalla Completa</a><?php
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

<!-- Filtro de Datos Propuestas de Servicios TCP - TCS -->
<div class="modal fade" id="modalBuscadorFacturas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button  class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Filtrar Datos</h4>
      </div>
        <form action="index.php?opcion=listSimulacionesServicios<?=$listSC;?>" method="POST">
            <div class="modal-body ">
                <input type="text" hidden name="q" value="<?=$listSC;?>">
                <div class="row">
                    <label class="col-sm-6 col-form-label text-center">Fecha Inicial</label> 
                    <label class="col-sm-6 col-form-label text-center">Fecha Final</label> 
                    </div>
                    <div class="row">
                    <div class="form-group col-sm-6">
                        <input class="form-control input-sm" type="date" name="date_start" id="date_start" require>
                    </div>
                    <div class="form-group col-sm-6">
                        <input class="form-control input-sm" type="date" name="date_end" id="date_end" require>
                    </div>        
                    </div>
                    <div class="row">
                    <label class="col-sm-6 col-form-label text-center">Cliente</label> 
                    <label class="col-sm-6 col-form-label text-center">Responsable</label> 
                    </div>
                    <div class="row">
                    <div class="form-group col-sm-6">
                            <select class="selectpicker form-control form-control-sm" name="cod_cliente" id="cod_cliente" data-style="select-with-transition" data-actions-box="true" data-live-search="true">
                            <option value="">Todos</option>
                            <?php
                                $sql="SELECT codigo, nombre
                                FROM clientes
                                GROUP BY nombre ";
                                $stmtSC = $dbh->prepare($sql);
                                $stmtSC->execute();
                            while ($rowSC = $stmtSC->fetch()){
                                ?>
                                <option value="<?=$rowSC['codigo'];?>" ><?=$rowSC['nombre'];?></option><?php 
                            } ?>
                            </select> 
                    </div>
                    <div class="form-group col-sm-6">
                            <?php
                            ?>
                            <select class="selectpicker form-control form-control-sm" name="personal" id="personal" data-style="select-with-transition" data-actions-box="true" data-live-search="true">
                            <option value="">Todos</option>
                            <?php 
                                $sqlP="SELECT CONCAT(p.paterno, ' ',p.materno, ' ',p.primer_nombre) as nombre_personal, p.codigo
                                from personal p
                                order by nombre_personal ASC";
                                $stmtP = $dbh->prepare($sqlP);
                                $stmtP->execute();
                                while ($rowP = $stmtP->fetch()){
                            ?>
                                <option value="<?=$rowP["codigo"];?>" ><?=$rowP["nombre_personal"];?></option>
                            <?php 
                                } 
                            ?>
                            </select> 
                    </div>        
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Filtrar</button>
            </div>
        </form> 
    </div>
  </div>
</div>



