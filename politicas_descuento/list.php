<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$globalAdmin = $_SESSION["globalAdmin"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT codigo,nombre, minutos_inicio, minutos_final,porcentaje_diahaber
                     FROM $table_politicaDescuento WHERE cod_estadoreferencial=1");

$stmt->execute();

$stmt->bindColumn('codigo', $cod_politica_descuento);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('minutos_inicio', $minutos_inicio);
$stmt->bindColumn('minutos_final', $minutos_final);
$stmt->bindColumn('porcentaje_diahaber', $porcentaje_diahaber);

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
                    <th class="text-left"> # </th>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Minutos Inicio</th>
                    <th class="text-center">Minutos Final</th>
                    <th class="text-center">Porcentaje Día Haber</th>
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
                      <td class="text-center"><?= $minutos_inicio; ?></td>
                      <td class="text-center"><?= $minutos_final; ?></td>
                      <td class="text-center"><?= $porcentaje_diahaber." %"; ?></td>
                      <td class="td-actions text-right">
                        <?php
                        if ($globalAdmin == 1) {
                        ?>
                          <a href='<?= $urlEdit; ?>&cod_pol_desc=<?= $cod_politica_descuento; ?>' rel="tooltip" class="<?= $buttonEdit; ?>">
                            <i class="material-icons"><?= $iconEdit; ?></i>
                          </a>
                          <button rel="tooltip" class="<?= $buttonDelete; ?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?= $urlDelete; ?>&cod_pol_desc=<?= $cod_politica_descuento; ?>')">
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