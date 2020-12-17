<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT p.*,e.nombre as estado_plantilla, u.abreviatura as unidad,a.abreviatura as area from plantillas_costo p,unidades_organizacionales u, areas a, estados_plantillascosto e 
  where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and e.codigo=p.cod_estadoplantilla and p.cod_estadoreferencial!=2 order by codigo");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('abreviatura', $abreviatura);
$stmt->bindColumn('cod_unidadorganizacional', $codUnidad);
$stmt->bindColumn('cod_area', $codArea);
$stmt->bindColumn('unidad', $unidad);
$stmt->bindColumn('area', $area);
$stmt->bindColumn('utilidad_minimalocal', $utilidadLocal);
$stmt->bindColumn('utilidad_minimaexterno', $utilidadExterno);
$stmt->bindColumn('cod_estadoplantilla', $codEstado);
$stmt->bindColumn('estado_plantilla', $estadoPlantilla);
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
                  <div class="table-responsive" id="data_comprobantes">
                    <table class="table table-condensed" id="tablePaginator">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th class="text-left">Nombre</th>
                          <th>Abreviatura</th>
                          <th>Unidad</th>
                          <th>Area</th>
                          <th>Utilidad Ibnorca</th>
                          <!--<th>Utilidad Fuera Ibnorca</th>-->
                          <th>Estado</th>
                          <th class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
						$index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          switch ($codEstado) {
                            case 1:
                             $textEstado="text-info";$btnEstilo="btn-warning";//$estadoIcon="how_to_vote";
                            break;
                            case 2:
                            $textEstado="text-danger";$btnEstilo="btn-danger";//$estadoIcon="thumb_down";
                            break;
                            case 3:
                              $textEstado="text-warning";$btnEstilo="btn-success";//$estadoIcon="thumb_up";
                            break;
                          }
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td class="text-left"><?=$nombre;?></td>
                          <td><?=$abreviatura;?></td>
                          <td><?=$unidad;?></td>
                          <td><?=$area;?></td>
                          <td><?=$utilidadLocal;?> %</td> 
                          <!--<td> %</td>-->
                           <td class="<?=$textEstado?>"><button class="btn <?=$btnEstilo?> btn-sm"><?=$estadoPlantilla;?></button></td>
                          <td class="td-actions text-right">
                            <a href='<?=$urlReporte;?>?cod=<?=$codigo;?>' rel="tooltip" class="btn btn-primary">
                              <i class="material-icons" title="Ver Detalle">list</i>
                            </a>
                            <a target="_blank" href='<?=$urlRegister;?>?cod=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <button title="Duplicar Registro ACTUALIZADO al <?=date("Y")?>" class="btn btn-info" onclick="alerts.showSwal('warning-message-and-confirmation-clonar','<?=$urlClonar;?>&codigo=<?=$codigo;?>&pr=0')">
                              <i class="material-icons"><?=$iconCopy?></i>
                            </button>
                            <?php if($codEstado!=3){
                              if($globalAdmin==1){

                              ?>
                              <button title="ACTUALIZAR al <?=date("Y")?>" class="btn btn-warning" onclick="alerts.showSwal('warning-message-and-confirmation-actualizar-plantilla','<?=$urlClonar2;?>&codigo=<?=$codigo;?>&pr=0')">
                                <i class="material-icons">refresh</i>
                              </button>                                         
                            <button title="Eliminar Registro" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button>
                            <?php
                              }
                            }?>
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