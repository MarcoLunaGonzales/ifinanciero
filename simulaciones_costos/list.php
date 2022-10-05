<?php
require_once 'conexion2.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$dbh = new Conexion2();

// Preparamos

$q=0;
$u=0;
$s=0;

$listSC = "";

if(isset($_GET['q'])){
    // URL actual
    $listSC = $_GET['q'];
    // Datos de Filtro
    $start          = isset($_POST['date_start'])?$_POST['date_start']:"";
    $end            = isset($_POST['date_end'])?$_POST['date_end']:"";
    $cod_tipo       = isset($_POST['cod_tipo'])?$_POST['cod_tipo']:"";
    $cod_personal   = isset($_POST['personal'])?$_POST['personal']:"";
    
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
  (select cli.nombre from clientes cli where cli.codigo=sc.cod_cliente)as cliente 
  from simulaciones_costos sc 
  join estados_simulaciones es on sc.cod_estadosimulacion=es.codigo 
  where sc.cod_estadoreferencial=1 $sqlModulos ".
  (!empty($start)?(" AND sc.fecha >= '$start' AND sc.fecha <= '$end' "):"").
  (!empty($cod_tipo)?(" AND sc.cod_tipocurso = '$cod_tipo' "):"").
  (!empty($cod_personal)?(" AND sc.cod_responsable = '$cod_personal' "):"").
  " order by sc.codigo desc";
  $stmt = $dbh->prepare($sql);

}else{
  $s=0;
  $u=0;
  // Preparamos
$stmt = $dbh->prepare("SELECT sc.*,es.nombre as estado,(select cli.nombre from clientes cli where cli.codigo=sc.cod_cliente)as cliente
 from simulaciones_costos sc join estados_simulaciones es on sc.cod_estadosimulacion=es.codigo where sc.cod_estadoreferencial=1 and sc.cod_responsable=$globalUser order by sc.codigo desc limit 0,150");
}


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
                  <div class="row">
                    <div class="col-sm-6">
                        <h4 class="card-title"><b><?=$moduleNamePlural?></b></h4>
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
                          <th>Responsable</th>
                          <th>Fecha</th>
                          <th>Cliente</th>
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
                          <td>
                                 <img src="assets/img/faces/persona1.png" width="20" height="20"/><?=$responsable;?>
                          </td>
                          <td><?=$fecha;?></td>
                          <td><?=$nombreCliente;?></td>
                          <td><?=$estado;?> <?=$nEst?> %
                             <div class="progress">
                               <div class="progress-bar <?=$barEstado?>" role="progressbar" aria-valuenow="<?=$nEst?>" aria-valuemin="0" aria-valuemax="100" style="width:<?=$nEst?>%">
                                  <span class="sr-only"><?=$nEst?>% Complete</span>
                               </div>
                             </div>
                          </td> 
                          <td class="td-actions text-right">
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
        <form action="index.php?opcion=listSimulacionesCostos&q=<?=$listSC;?>" method="POST">
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
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Filtrar</button>
            </div>
        </form> 
    </div>
  </div>
</div>