<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$globalAdmin = $_SESSION["globalAdmin"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT codigo,nombre,abreviatura,descripcion,nro_meses,fecha_inicio,fecha_fin
                     FROM $table_dotaciones where cod_estadoreferencial=1");

$stmt->execute();

$stmt->bindColumn('codigo', $cod_dotacion);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('abreviatura', $abreviatura);
$stmt->bindColumn('descripcion', $descripcion);
$stmt->bindColumn('nro_meses', $nro_meses);
$stmt->bindColumn('fecha_inicio', $fecha_inicio);
$stmt->bindColumn('fecha_fin', $fecha_fin);

?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?= $colorCard; ?> card-header-icon">
            <div class="card-icon">
              <i class="material-icons"><?= $iconCard; ?></i>
            </div>
            <h4 class="card-title"><?= $moduleNamePlural ?></h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="tablePaginator" class="table table-condensed">
                <thead>
                  <tr>
                    <th class="text-left">#</th>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Abreviatura</th>
                    <th class="text-center">Descripcion</th>
                    <th class="text-center">Nro. de Meses</th>
                    <th class="text-center">Inicio</th>
                    <th class="text-center">Fin</th>
                    <th class="text-right">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                     $index = 1;
                      while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                  ?>
                    <tr>
                      <td class="text-center"><?= $index; ?></td>
                      <td class="text-left"><?= $nombre; ?></td>
                      <td class="text-center"><?= $abreviatura; ?></td>
                      <td class="text-left"><?= $descripcion; ?></td>
                      <td class="text-center"><?= $nro_meses; ?></td>
                      <td class="text-center"><?= MesAnioEnLetra($fecha_inicio); ?></td>
                      <td class="text-center"><?= MesAnioEnLetra($fecha_fin); ?></td>
                      <td class="td-actions text-right">
                        <?php
                        if ($globalAdmin == 1) {
                        ?>
                          <a href='<?=$urlListDotacionPersonal;?>&cod_dot=<?=$cod_dotacion;?>' rel="tooltip" class="<?=$buttonDetailMin;?>">
                              <i class="material-icons" title="Detalle">playlist_add</i>
                          </a>
                          <a href='<?= $urlEdit; ?>&cod_dot=<?= $cod_dotacion; ?>' rel="tooltip" class="<?= $buttonEdit; ?>">
                            <i class="material-icons"><?= $iconEdit; ?></i>
                          </a>
                          <button rel="tooltip" class="<?= $buttonDelete; ?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?= $urlDelete; ?>&cod_dot=<?= $cod_dotacion; ?>')">
                            <i class="material-icons"><?= $iconDelete; ?></i>
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
                  if ($globalAdmin == 1) {
        ?>
          <div class="card-footer fixed-bottom">
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegister;?>'">Registrar</button>
              </div>
        <?php
        }
        ?>


      </div>
    </div>
  </div>
</div>