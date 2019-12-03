<?php

require_once '../conexion.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

session_start();
$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

//$idFila=$_GET['idFila'];

$valor=$_GET["valor"];
if($valor==null||$valor==""){
  $valor="";
}

$dbh = new Conexion();
$query="SELECT u.nombre,c.cod_gestion,m.nombre as moneda,tc.nombre as tipo_comprobante,c.fecha,c.numero,c.codigo,c.glosa,ec.nombre as estado, ec.codigo as codigo_estado from comprobantes c join unidades_organizacionales u on c.cod_unidadorganizacional=u.codigo join monedas m on c.cod_moneda=m.codigo join tipos_comprobante tc on c.cod_tipocomprobante=tc.codigo join estados_comprobantes ec on c.cod_estadocomprobante=ec.codigo join comprobantes_detalle cd on c.codigo=cd.cod_comprobante join plan_cuentas p on cd.cod_cuenta=p.codigo where c.cod_estadocomprobante!=2 and (u.nombre like '%".$valor."%' or m.nombre like '%".$valor."%' or tc.nombre like '%".$valor."%' or c.fecha like '%".$valor."%' or c.numero like '%".$valor."%' or c.codigo like '%".$valor."%' or c.glosa like '%".$valor."%' or ec.nombre like '%".$valor."%' or p.nombre like '%".$valor."%' or cd.glosa like '%".$valor."%') group by c.codigo;";


//echo $sqlBusqueda;

$stmt = $dbh->prepare($query);
$stmt->execute();
$i=1;
?>
<table class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Gestion</th>
                          <th>Fecha</th>
                          <th>Tipo</th>
                          <th>Correlativo</th>
                          <th>Moneda</th>
                          <th>Glosa</th>
                          <th>Estado</th>
                          <th class="text-right" width="20%">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
 while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
 	$codigo=(int)$row['codigo'];
 	$codigoEstado=(int)$row['codigo_estado'];
?>
	<tr>
                          <td align="center"><?=$i;?></td>
                          <td><?=$row['cod_gestion'];?></td>
                          <td><?=$row['fecha'];?></td>
                          <td><?=$row['tipo_comprobante'];?></td>
                          <td><?=$row['numero'];?></td>
                          <td><?=$row['moneda'];?></td>
                          <td><?=$row['glosa'];?></td>
                          <td><?=$row['estado'];?></td>
                          <td class="td-actions text-right">
                            
                            <?php
                             if($_GET['estado']=="registrado"){
                             ?>
                             <div class="btn-group dropdown">
                              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons"><?=$iconImp;?></i>
                              </button>
                              <div class="dropdown-menu">
                                <?php
                                  $stmtMoneda = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM monedas where cod_estadoreferencial=1 order by 2");
                                 $stmtMoneda->execute();
                                  while ($row = $stmtMoneda->fetch(PDO::FETCH_ASSOC)) {
                                    $codigoX=$row['codigo'];
                                    $nombreX=$row['nombre'];
                                    $abrevX=$row['abreviatura'];
                                    if($codigoX!=1){
                                      ?>
                                       <a href="#" onclick="javascript:window.open('<?=$urlImp;?>?comp=<?=$codigo;?>&mon=<?=$codigoX?>')" class="dropdown-item">
                                           <i class="material-icons">keyboard_arrow_right</i> <?=$abrevX?>
                                       </a> 
                                     <?php
                                    }
                                  
                                   }
                                   ?>
                              </div>
                            </div>
                              <a href='<?=$urlEdit2;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button>
                            <a href='<?=$urlArchivo;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="btn btn-warning">
                              <i class="material-icons">attachment</i>
                            </a>
                             <?php
                             }else{
                             	switch ($codigoEstado) {
                            case 1:
                              ?>
                              <a href="#modalAprobar" data-toggle="modal" data-target="#modalAprobar" onclick="sendAprobacion(<?=$codigo?>,3)" target="_blank" rel="tooltip" class="btn btn-default btn-link btn-fab">
                              <i class="material-icons">done_all</i>
                               </a> 
                              <?php
                              break;
                            case 2:
                              ?>
                              <a href="#modalAprobar" data-toggle="modal" data-target="#modalAprobar" onclick="sendAprobacion(<?=$codigo?>,3)" target="_blank" rel="tooltip" class="btn btn-danger btn-link btn-fab">
                              <i class="material-icons">clear</i>
                               </a> 
                              <?php
                              break;
                              case 3:
                              ?>
                              <a href="#modalAprobar" data-toggle="modal" data-target="#modalAprobar" onclick="sendAprobacion(<?=$codigo?>,1)" target="_blank" rel="tooltip" class="btn btn-info btn-link btn-fab">
                              <i class="material-icons">done_all</i>
                               </a> 
                              <?php
                              break;
                            default:
                              # code...
                              break;
                          }
                                ?>
                            <div class="btn-group dropdown">
                              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons"><?=$iconImp;?></i>
                              </button>
                              <div class="dropdown-menu">
                                <?php
                                  $stmtMoneda = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM monedas where cod_estadoreferencial=1 order by 2");
                                 $stmtMoneda->execute();
                                  while ($row = $stmtMoneda->fetch(PDO::FETCH_ASSOC)) {
                                    $codigoX=$row['codigo'];
                                    $nombreX=$row['nombre'];
                                    $abrevX=$row['abreviatura'];
                                    if($codigoX!=1){
                                      ?>
                                       <a href="#" onclick="javascript:window.open('<?=$urlImp;?>?comp=<?=$codigo;?>&mon=<?=$codigoX?>')" class="dropdown-item">
                                           <i class="material-icons">keyboard_arrow_right</i> <?=$abrevX?>
                                       </a> 
                                     <?php
                                    }
                                  
                                   }
                                   ?>
                              </div>
                            </div>
                            <a href='<?=$urlArchivo;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="btn btn-warning">
                              <i class="material-icons">attachment</i>
                            </a>
                            <?php
                             }
                            ?>
                            
                          </td>
                        </tr>		 
<?php
$i++;
}
?>
 </tbody>
                    </table>

