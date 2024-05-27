<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$dbh = new Conexion();

/*SACAR LOS ESTADOS DEL FINANCIERO O DE IBNORCA*/
$configuracionEstados=obtenerValorConfiguracion(100);


// Datos de Filtro
$start          = isset($_POST['date_start'])?$_POST['date_start']:"";
$end            = isset($_POST['date_end'])?$_POST['date_end']:"";
$cod_cliente    = isset($_POST['cod_cliente'])?$_POST['cod_cliente']:"";
$cod_personal   = isset($_POST['personal'])?$_POST['personal']:"";
$filter_list    = (!empty($start)?(" AND sc.fecha >= '$start' "):"").
(!empty($end)?(" AND sc.fecha <= '$end' "):"").
(!empty($cod_cliente)?(" AND sc.cod_cliente = '$cod_cliente' "):"").
(!empty($cod_personal)?(" AND sc.cod_responsable = '$cod_personal' "):"");

// Preparamos
$listSC = "";
// URL actual
$query_q = isset($_GET['q'])?("&q=".$_GET['q']):"";
$query_s = isset($_GET['s'])?("&s=".$_GET['s']):"";
$query_u = isset($_GET['u'])?("&u=".$_GET['u']):"";
$listSC = $query_q.$query_s.$query_u;

if(isset($_GET['q'])){

  $q=isset($_GET['q'])?$_GET['q']:"";
  $item_3=isset($_GET['r'])?$_GET['r']:"";
  $u=isset($_GET['u'])?$_GET['u']:"";
  $s="";
  if(isset($_GET['s'])){
    $s=$_GET['s'];
    $wordsOficina = array("IdOficina", "idoficina", "Idoficina", "idOficina");
    $wordsArea = array("IdArea", "idarea", "Idarea", "idArea");

    $sqlFilter1 = str_replace($wordsOficina, "p.cod_unidadorganizacional", $_GET['s']);
    $sqlFilter2 = "and ".str_replace($wordsArea, "p.cod_area", $sqlFilter1);
    $sqlFilter = str_replace("%20", " ", $sqlFilter2);
  }else{
    $sqlFilter = "";
  } 
?>
  <input type="hidden" name="id_servicioibnored" value="<?=$q?>" id="id_servicioibnored"/>
  <input type="hidden" name="id_servicioibnored_rol" value="<?=$item_3?>" id="id_servicioibnored_rol"/>
  <input type="hidden" name="idPerfil" value="<?=$u?>" id="idPerfil"/>
  <input type="hidden" name="ss" value="<?=$s?>" id="ss"/>
  <input type="hidden" name="id_servicioibnored_u" value="<?=$u?>" id="id_servicioibnored_u"/>
<?php
}else{
  $item_3=obtenerIdRolDeIbnorca($globalUser);
  $sqlFilter="";
}
// Preparamos
$sqlAdmin="SELECT p.cod_unidadorganizacional,p.cod_area,sc.*,sc.cod_unidadorganizacional as unidad1, es.nombre as estado,c.nombre as cliente 
from simulaciones_servicios sc 
join estados_simulaciones es on sc.cod_estadosimulacion=es.codigo 
join clientes c on c.codigo=sc.cod_cliente 
join plantillas_servicios p on p.codigo=sc.cod_plantillaservicio
where sc.cod_estadoreferencial=1 
and sc.cod_estadosimulacion!=1 $sqlFilter ".
$filter_list.
" order by sc.codigo desc
  LIMIT 0, 50";

//echo $sqlAdmin;

$stmt = $dbh->prepare($sqlAdmin);
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
$stmt->bindColumn('descripcion_servicio', $alcanceX);
$stmt->bindColumn('unidad1', $oficinaX);


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
                          <th class="font-weight-bold">Numero</th>
                          <th>Cliente</th>
                          <th>Descripción Servicio</th>
                          <th>Responsable</th>
                          <th>Fecha</th>
                          <th>Estado</th>
                          <th>Servicio</th>
                          <th class="text-center">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
            $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          
                          $unidadX=abrevUnidad_solo($codUnidadX);
                          $areaX=abrevArea_solo($codAreaX);
                          $codigoServicio="SIN CODIGO";
                          $sql="SELECT codigo FROM ibnorca.servicios where idServicio=$idServicioX";
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
                            //echo $sql2;
                            $stmt2 = $dbh -> prepare($sql2);
                            $stmt2 -> execute();
                            $idEstadoExt=0;
                            $nombreEstadoExt="";
                            if($row2 = $stmt2 -> fetch(PDO::FETCH_ASSOC)){
                                $idEstadoExt=$row2['IdEstado'];
                                $nombreEstadoExt=$row2['descr'];
                            }
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
                              $nEst=60;$barEstado="progress-bar-info";$btnEstado="btn-info";
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
                        <tr class="<?=$estiloFila?>">
                          <td align="center"><?=$index;?></td>
                          <td class="font-weight-bold"><?=$nombre;?> - <?=$areaX?></td>
                          <td><?=$cliente;?></td>
                          <td class="text-left small"><small><?=$alcanceX;?></small></td>
                          <td class="text-left">
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
                            <div class="btn-group dropdown">
                              <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">list</i> <?=$estado;?>
                              </button>
                              <div class="dropdown-menu">
                                <?php 
                                if(isset($_GET['q'])){
                                  ?>
                                   <a href="<?=$urlVer;?>?cod=<?=$codigo;?>&admin=0&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>" class="dropdown-item">
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
                                 --><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>" class="dropdown-item">
                                    <i class="material-icons text-dark">refresh</i> Rechazar Solicitud
                                 </a><!--
                                 <a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=2&q=<?=$q?>" class="dropdown-item">
                                    <i class="material-icons text-danger">clear</i> Anular Solicitud
                                 </a>--><?php 
                               }else{
                                ?><a href="#" onclick="mostrarCambioEstadoObjeto(<?=$codigo?>)" class="dropdown-item">
                                    <i class="material-icons text-warning">dns</i> Cambiar Estado
                                 </a>
                                <!--<a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=3" class="dropdown-item">
                                    <i class="material-icons text-success">offline_pin</i> Aprobar Solicitud
                                 </a>
                                 --><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1" class="dropdown-item">
                                    <i class="material-icons text-dark">refresh</i> Rechazar Solicitud
                                 </a><!--
                                 <a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=2" class="dropdown-item">
                                    <i class="material-icons text-danger">clear</i> Anular Solicitud
                                 </a>--><?php 
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
                                    ?><!--<a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=4" class="dropdown-item">
                                    <i class="material-icons text-dark">reply</i> Deshacer Cambios
                                 </a>-->
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
                ?><a href="<?=$urlList2?>&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>" class="btn btn-info"><i class="material-icons">refresh</i> Refrescar</a><a href="#" target="_blank" class="btn btn-warning float-right"><i class="material-icons">fullscreen</i> Ver Pantalla Completa</a><?php
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
<!-- Filtro de Datos Propuestas de Servicios TCP - TCS -->
<div class="modal fade" id="modalBuscadorFacturas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button  class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Filtrar Datos</h4>
      </div>
        <form action="index.php?opcion=listSimulacionesServAdmin<?=$listSC;?>" method="POST">
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
                                FROM clientes where cod_tipocliente='E'
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