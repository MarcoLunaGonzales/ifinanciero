<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$globalUnidad=$_SESSION['globalUnidad'];

$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT (select u.nombre from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)unidad, c.cod_gestion, 
(select m.nombre from monedas m where m.codigo=c.cod_moneda)moneda, 
(select t.nombre from tipos_comprobante t where t.codigo=c.cod_tipocomprobante)tipo_comprobante, c.fecha, c.numero,c.codigo, c.glosa,ec.nombre,c.cod_estadocomprobante
from comprobantes c join estados_comprobantes ec on c.cod_estadocomprobante=ec.codigo where c.cod_estadocomprobante!=2 and c.cod_unidadorganizacional=$globalUnidad order by c.fecha desc");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('unidad', $nombreUnidad);
$stmt->bindColumn('cod_gestion', $nombreGestion);
$stmt->bindColumn('moneda', $nombreMoneda);
$stmt->bindColumn('tipo_comprobante', $nombreTipoComprobante);
$stmt->bindColumn('fecha', $fechaComprobante);
$stmt->bindColumn('numero', $nroCorrelativo);
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('glosa', $glosaComprobante);
$stmt->bindColumn('nombre', $estadoComprobante);
$stmt->bindColumn('cod_estadocomprobante', $estadoC);
?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title"><?=$moduleNamePlural?></h4>
                </div>
                <div class="card-body">
                  <!--
                  //busqueda de comprobantes por cuenta
                  <div class="col-sm-4 float-right">
                      <div class="input-group no-border">
                        <input type="text" value="" id="buscar_comprobantes" class="form-control" placeholder="Buscar..." onChange="buscarComprobantes('registrado')" OnKeyUp="buscarComprobantes('registrado')">
                        <a href="#" onclick="buscarComprobantes('registrado')" class="btn btn-white btn-round btn-just-icon">
                          <i class="material-icons">search</i>
                          <div class="ripple-container"></div>
                        </a>
                      </div>
                  </div>-->
                  <div class="" id="data_comprobantes">
                    <table id="tablePaginator" class="table table-condensed">
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
						$index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          switch ($estadoC) {
                            case 1:
                             $btnEstado="btn-info";$estadoIcon="how_to_vote";
                            break;
                            case 2:
                            $btnEstado="btn-danger";$estadoIcon="thumb_down";
                            break;
                            case 3:
                              $btnEstado="btn-warning";$estadoIcon="thumb_up";
                            break;
                          }
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td><?=$nombreGestion;?></td>
                          <td><?=strftime('%d/%m/%Y',strtotime($fechaComprobante));?></td>
                          <td><?=$nombreTipoComprobante;?></td>
                          <td><?=$nroCorrelativo;?></td>
                          <td><?=$nombreMoneda;?></td>
                          <td><?=$glosaComprobante;?></td>
                          <td><button class="btn <?=$btnEstado?> btn-sm btn-link"><?=$estadoComprobante;?>  <span class="material-icons small"><?=$estadoIcon?></span></button></td>
                          <td class="td-actions text-right">
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
                                    //if($codigoX!=1){
                                      ?>
                                       <a href="#" onclick="javascript:window.open('<?=$urlImp;?>?comp=<?=$codigo;?>&mon=<?=$codigoX?>')" class="dropdown-item">
                                           <i class="material-icons">keyboard_arrow_right</i> <?=$abrevX?>
                                       </a> 
                                     <?php
                                    //}
                                   }
                                   ?>
                              </div>
                            </div>
                            <a href='<?=$urlArchivo;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="btn btn-default">
                              <i class="material-icons">attachment</i>
                            </a>
                            <a href='<?=$urlEdit2;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button>
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
              </div>
      				<div class="card-footer fixed-bottom">
                <a href="#" onclick="javascript:window.open('<?=$urlRegister2;?>')" class="<?=$buttonNormal;?>">Registrar</a>
              </div>		  
            </div>
          </div>  
        </div>
    </div>
