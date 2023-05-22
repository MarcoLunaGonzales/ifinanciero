<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$dbh = new Conexion();

// codigo de Versión
$cod_version = $_GET['cod_version'];

// Datos de Filtro
$start          = isset($_POST['date_start'])?$_POST['date_start']:"";
$end            = isset($_POST['date_end'])?$_POST['date_end']:"";
$cod_tipo       = isset($_POST['cod_tipo'])?$_POST['cod_tipo']:"";
$cod_personal   = isset($_POST['personal'])?$_POST['personal']:"";
$fil_nombre     = isset($_POST['fil_nombre'])?$_POST['fil_nombre']:"";
$fil_cod_curos  = isset($_POST['fil_cod_curos'])?$_POST['fil_cod_curos']:"";

$filter_list    = ((!empty($start)&&!empty($end))?(" AND sc.fecha >= '$start' AND sc.fecha <= '$end' "):"").
(!empty($cod_tipo)?(" AND sc.cod_tipocurso = '$cod_tipo' "):"").
(!empty($cod_personal)?(" AND sc.cod_responsable = '$cod_personal' "):"").
(!empty($fil_nombre)?(" AND sc.nombre like '%$fil_nombre%' "):"").
(!empty($fil_cod_curos)?(" AND vc.cod_curso like '%$fil_cod_curos%' "):"");

// Preparamos

$q=0;
$u=0;
$s=0;

$listSC = "";
// URL actual
$query_q = isset($_GET['q'])?("&q=".$_GET['q']):"";
$query_s = isset($_GET['s'])?("&s=".$_GET['s']):"";
$query_u = isset($_GET['u'])?("&u=".$_GET['u']):"";
$query_cod_version = isset($_GET['cod_version'])?("&cod_version=".$_GET['cod_version']):"";
$listSC = $query_q.$query_s.$query_u.$query_cod_version;

if(isset($_GET['q'])){
    
  $q=$_GET['q'];
  if(isset($_GET['s'])){
    $s=$_GET['s'];    
  }
  if(isset($_GET['u'])){
    $u=$_GET['u'];
  }

  $sqlModulos="";
  if(isset($_GET['s'])){
    $s=$_GET['s'];
    $u=$_GET['u'];
    if($u>0){
      $sqlModulos="and sc.IdModulo=".$u;      
    }
  }
  $globalUser=$q;
  // Preparamos

  $sql = "SELECT sc.*,es.nombre as estado, 
  (select cli.nombre from clientes cli where cli.codigo=sc.cod_cliente)as cliente,vc.cod_curso as codigo_curso
  FROM simulaciones_costos sc 
  LEFT JOIN v_cursos vc on vc.IdCurso = sc.IdCurso
  JOIN estados_simulaciones es on sc.cod_estadosimulacion=es.codigo 
  WHERE sc.cod_estadoreferencial=1 
  AND sc.cod_version = '$cod_version' $sqlModulos ".
  $filter_list.
  " GROUP BY sc.codigo, sc.nombre, sc.observacion, sc.fecha, sc.cod_tipocurso, sc.cod_plantillacosto, sc.cod_estadosimulacion, sc.cod_responsable, sc.cod_area_registro,cliente, estado
  order by sc.codigo desc
  LIMIT 0, 50";
  $stmt = $dbh->prepare($sql);

}else{
  $s=0;
  $u=0;
  // Preparamos
$stmt = $dbh->prepare("SELECT sc.*,es.nombre as estado,(select cli.nombre from clientes cli where cli.codigo=sc.cod_cliente)as cliente
-- , vc.cod_curso as codigo_curso
 FROM simulaciones_costos sc 
 JOIN estados_simulaciones es on sc.cod_estadosimulacion=es.codigo 
--  LEFT JOIN v_cursos vc on vc.IdCurso = sc.IdCurso
 WHERE sc.cod_estadoreferencial=1 
 AND sc.cod_version = '$cod_version'".
 (empty($filter_list)?(' and sc.cod_responsable='.$globalUser):'').
 $filter_list.
 " GROUP BY sc.codigo, sc.nombre, sc.observacion, sc.fecha, sc.cod_tipocurso, sc.cod_plantillacosto, sc.cod_estadosimulacion, sc.cod_responsable, sc.cod_area_registro,cliente, estado
 order by sc.codigo desc
 LIMIT 0, 200");
}

//echo $sql;
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('observacion', $observacion);
$stmt->bindColumn('fecha', $fecha);
$stmt->bindColumn('cod_tipocurso', $codTipoCurso);
$stmt->bindColumn('cod_plantillacosto', $codPlantilla);
$stmt->bindColumn('cod_estadosimulacion', $codEstado);
$stmt->bindColumn('cod_responsable', $codResponsable);
$stmt->bindColumn('cod_area_registro', $codArea);
$stmt->bindColumn('estado', $estado);
$stmt->bindColumn('cliente', $nombreCliente);
$stmt->bindColumn('codigo_curso', $codigoCurso);
$stmt->bindColumn('cod_version', $cod_version);
$stmt->bindColumn('nro_version', $nro_version);
$stmt->bindColumn('estado_version', $estado_version);

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
                        <h4 class="card-title"><b>Versiones - <?=$moduleNamePlural?></b></h4>
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
                    <table class="table" id="tablePaginator">
                      <thead>
                        <tr>
                          <!--<th class="text-center">#</th>-->
                          <th>Codigo</th>
                          <th>Tipo</th>
                          <th>Origen</th>
                          <th>Nombre</th>
                          <th>COD Curso</th>
                          <th>Responsable</th>
                          <th>Fecha</th>
                          <th>Cliente</th>
                          <th class="text-center">Versión</th>
                          <th class="text-center">Estado de Registro</th>
                          <th>Estado</th>
                          <th class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <div id="divBuscadorPropuestas">
<?php
            $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $responsable=namePersonal($codResponsable);
                          $tipoCurso=nameTipoCurso($codTipoCurso);
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
                          <!--<td align="center"><?=$index;?></td>-->
                          <td><?=$codigo;?></td>
                          <td><?=$tipoCurso;?></td>
                          <td><?=abrevArea_solo($codArea);?></td>
                          <td><?=$nombre;?></td>
                          <td><?=$codigoCurso;?></td>
                          <td>
                                 <img src="assets/img/faces/persona1.png" width="20" height="20"/><?=$responsable;?>
                          </td>
                          <td><?=$fecha;?></td>
                          <td><?=$nombreCliente;?></td>
                          <td class="text-center"><?=$nro_version;?></td>
                          <td class="text-center"><span class="badge badge-<?=($estado_version?'success':'danger')?>"><?=($estado_version?'Activo':'Inactivo')?></span></td>
                          <td><?=$estado;?> <?=$nEst?> %
                             <div class="progress">
                               <div class="progress-bar <?=$barEstado?>" role="progressbar" aria-valuenow="<?=$nEst?>" aria-valuemin="0" aria-valuemax="100" style="width:<?=$nEst?>%">
                                  <span class="sr-only"><?=$nEst?>% Complete</span>
                               </div>
                             </div>
                          </td> 
                          <td class="td-actions text-right">
                            <!-- Actualización de Proveedores - MODAL -->
                              <button type="button" class="btn btn-info btn-fab btn-sm btn-sim-update-cliente" data-sim_codigo="<?=$codigo;?>">
                                    <i class="material-icons" title="Editar Cliente">edit</i>
                                    <div class="ripple-container"></div>
                              </button>

                            <a title="Imprimir Propuesta" href='#' onclick="javascript:window.open('simulaciones_costos/imp.php?cod=<?=$codigo;?>')" class="btn btn-success">
                                     <i class="material-icons"><?=$iconImp;?></i>
                                 </a>
                            <?php
                              if( ($codEstado==4||$codEstado==3) ){
                                if($codEstado==3){
                                 $anteriorCod=obtenerCodigoSolicitudRecursosSimulacion(1,$codigo);
                                 if(isset($_GET['q'])){
                                  ?>
                                 <a href="solicitudes/registerSolicitudDetalle.php?sim=<?=$codigo?>&det=1&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=0" target="_blank" title="Solicitud De Recursos"class="btn btn-danger">
                                    <i class="material-icons">content_paste</i>
                                 </a>
                                 <?php
                                 }else{
                                 ?>
                                 <a href="solicitudes/registerSolicitudDetalle.php?sim=<?=$codigo?>&det=1" target="_blank" title="Solicitud De Recursos"class="btn btn-danger">
                                    <i class="material-icons">content_paste</i>
                                 </a>
                                  
                                 <?php
                                  
                                 } 
                                }
                            ?>


                            <div class="btn-group dropdown">
                              <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">list</i> <?=$estado;?>
                              </button>

                              <div class="dropdown-menu">
                                <?php 
                                if(isset($_GET['q']) && $codResponsable==$globalUser ){
                                 if($codEstado==4){
                                 ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>" class="dropdown-item">
                                    <i class="material-icons text-danger">clear</i> Cancelar solicitud
                                 </a><?php 
                                 }?>
                                 <a href="<?=$urlVer;?>?cod=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>" class="dropdown-item">
                                    <i class="material-icons text-warning">bar_chart</i> Ver Propuesta
                                 </a> 
                                 <?php
                                }else{
                                  if($codEstado==4){
                                  ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&admin=0" class="dropdown-item">
                                    <i class="material-icons text-danger">clear</i> Cancelar solicitud
                                 </a><?php 
                                 }?>
                                 <a href="<?=$urlVer;?>?cod=<?=$codigo;?>" class="dropdown-item">
                                    <i class="material-icons text-warning">bar_chart</i> Ver Propuesta
                                 </a> 
                                 <?php 
                                }
                                ?>
                              </div>
                            </div>
                            <!-- <a class="btn btn-warning" title="Solicitud de Facturación" href='<?=$urlSolicitudfactura;?>&cod=<?=$codigo;?>'>
                               <i class="material-icons" >receipt</i>                              
                             </a>   -->                         
                            <?php    
                              }else{

                             if(isset($_GET['q']) && $codResponsable==$globalUser){
                                 ?> 
                            <a title="Editar Propuesta - Detalle" target="_blank" href='<?=$urlRegister;?>?cod=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>' class="btn btn-info">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <button title="Eliminar Propuesta" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button>
                              <?php 
                             } elseif ($codResponsable==$globalUser) {
                               ?> 
                            <a title="Editar Propuesta - Detalle" target="_blank" href='<?=$urlRegister;?>?cod=<?=$codigo;?>' class="btn btn-info">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <button title="Eliminar Propuesta" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button>
                              <?php 
                             }  
                              }
                            ?>
                            <!-- Duplicar Registro de Propuesta -->
                            <button title="Duplicar Propuesta" class="btn btn-warning propuesta_duplicar" data-codigo="<?=$codigo;?>">
                              <i class="material-icons">content_copy</i>
                            </button>
                          </td>
                        </tr>
<?php
              $index++;
            }
?>
                        </div>
                      </tbody>
                    </table>
                </div>
              </div>
              <div class="card-footer fixed-bottom">
                <?php
                if(isset($_GET['q'])){                  
                  ?><a href="<?=$urlRegister2;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>" target="_self" class="<?=$buttonNormal;?>">Registrar</a><?php
                }else{
                  ?><a href="#" onclick="javascript:window.open('<?=$urlRegister2;?>')" class="<?=$buttonNormal;?>">Registrar</a><?php
                } 
                ?>
                
              </div>      
            </div>
          </div>  
        </div>
    </div>
<!-- Filtro de Datos Propuestas de Presupuestos SEC -->
<div class="modal fade" id="modalBuscadorFacturas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button  class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Filtrar Datos</h4>
      </div>
        <form action="index.php?opcion=listSimulacionesCostos<?=$listSC;?>" method="POST">
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
                    <label class="col-sm-6 col-form-label text-center">Tipo</label> 
                    <label class="col-sm-6 col-form-label text-center">Responsable</label> 
                    </div>
                    <div class="row">
                    <div class="form-group col-sm-6">
                            <select class="selectpicker form-control form-control-sm" name="cod_tipo" id="cod_tipo" data-style="select-with-transition" data-actions-box="true" data-live-search="true">
                            <option value="">Todos</option>
                            <?php
                                $sql="SELECT cod_tipocurso
                                FROM simulaciones_costos
                                WHERE cod_tipocurso is not null
                                GROUP BY cod_tipocurso ";
                                $stmtSC = $dbh->prepare($sql);
                                $stmtSC->execute();
                            while ($rowSC = $stmtSC->fetch()){ 
                                $tipoCurso = nameTipoCurso($rowSC['cod_tipocurso']);
                                ?>
                                <option value="<?=$rowSC['cod_tipocurso']?>" ><?=$tipoCurso;?></option><?php 
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
                <div class="row">
                    <label class="col-sm-6 col-form-label text-center">Nombre</label> 
                    <label class="col-sm-6 col-form-label text-center">Código Curso</label> 
                    </div>
                    <div class="row">
                    <div class="form-group col-sm-6">
                        <input class="form-control input-sm" type="text" name="fil_nombre" id="fil_nombre">
                    </div>
                    <div class="form-group col-sm-6">
                        <input class="form-control input-sm" type="text" name="fil_cod_curos" id="fil_cod_curos">
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

<!-- Actualización de Cliente -->
<div class="modal fade" id="modalActualizarCliente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button  class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Actualizar Cliente</h4>
      </div>
        <div class="modal-body ">
            <input type="text" hidden name="sim_codigo" id="sim_codigo">
            <div class="row">
                <label class="col-sm-12 col-form-label text-center">Cliente</label>
                <div class="form-group col-sm-12">
                    <select class="selectpicker form-control" data-size="10" data-live-search-placeholder="Buscar cliente..." data-live-search="true" name="sim_cod_cliente" id="sim_cod_cliente" data-style="btn btn-info"  required>
                        <option value="0">-- --</option>
                        <?php
                            $stmt = $dbh->prepare("SELECT c.codigo, c.nombre FROM clientes c where c.cod_estadoreferencial=1 order by 2");
                            $stmt->execute();
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $codigoX=$row['codigo'];
                            $nombreX=$row['nombre'];
                        ?>
                        <option value="<?=$codigoX;?>"><?=$nombreX;?></option> 
                      <?php
                            }
                      ?>
                    </select>
                </div>        
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-success" onclick="simulacionUpdateCliente()">Actualizar</button>
        </div>
    </div>
  </div>
</div>

<script>
    // Cambiar Estado de Planilla a Cerrado en Vacio
    $('body').on('click','.propuesta_duplicar', function(){
      let formData = new FormData();
      // codigo Planilla
      formData.append('codigo', $(this).data('codigo'));
      swal({
          title: '¿Esta seguro de duplicar?',
          text: "Se duplicará el registro con todos su datos realacionados, no se podrá revertir la acción.",
          type: 'warning',
          showCancelButton: true,
          confirmButtonClass: 'btn btn-success',
          cancelButtonClass: 'btn btn-danger',
          confirmButtonText: 'Si',
          cancelButtonText: 'No',
          buttonsStyling: false
      }).then((result) => {
          if (result.value) {
              $(".cargar-ajax").removeClass("d-none");
              $.ajax({
                  url:"simulaciones_costos/registerSimulacionDuplicado.php",
                  type:"POST",
                  contentType: false,
                  processData: false,
                  data: formData,
                  success:function(response){
                  let resp = JSON.parse(response);
                  if(resp.status){
                      $(".cargar-ajax").addClass("d-none");// Mensaje
                      Swal.fire({
                          type: 'success',
                          title: 'Correcto!',
                          text: 'El proceso se completo correctamente!',
                          showConfirmButton: false,
                          timer: 1500
                      });
                      
                      setTimeout(function(){
                          location.reload()
                      }, 1550);
                  }else{
                      Swal.fire('ERROR!','El proceso tuvo un problema!. Contacte con el administrador!','error'); 
                      }
                  }
              });
          }
      });
    });
    
    
  </script>