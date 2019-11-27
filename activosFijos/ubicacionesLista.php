<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];


$dbh = new Conexion();

// Preparamos
//echo $table;
$stmt = $dbh->prepare("SELECT uo.nombre,  u.*, a.nombre as area FROM ubicaciones u, unidades_organizacionales uo, areas a where u.cod_estado=1 and uo.codigo = u.cod_unidades_organizacionales and a.codigo = u.cod_areas;");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('cod_unidades_organizacionales', $cod_unidades_organizacionales);
$stmt->bindColumn('nombre', $nombre);

$stmt->bindColumn('edificio', $edificio);
$stmt->bindColumn('oficina', $oficina);

$stmt->bindColumn('area', $area);
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
                  <h4 class="card-title"><?=$moduleNamePlural2?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Unidad</th>
                          <th>Edificio</th>
                          <th>Oficina</th>
                          <th class="text-right">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
						$index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td><?=$nombre;?></td>
                          <td><?=$edificio;?></td>
                          <td><?=$oficina;?></td>
                          <td class="td-actions text-right">
                            <?php
                            if($globalAdmin==1){
                                ?>
                                <a href='<?=$urlEdit2;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                  <i class="material-icons"><?=$iconEdit;?></i>
                                </a>
                                <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete2;?>&codigo=<?=$codigo;?>')">
                                  <i class="material-icons"><?=$iconDelete;?></i>
                                </button>
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
              </div>
              <?php
              if($globalAdmin==1){
              ?>
      				<div class="card-footer fixed-bottom">
                    <!--<button class="<?=$buttonNormal;?>" onClick="location.href='index.php?opcion=registerUbicacion'">Registrar</button>-->
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegistrar_ubicacion;?>&codigo=0'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>
