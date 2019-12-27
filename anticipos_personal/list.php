<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$globalAdmin = $_SESSION["globalAdmin"];
$codGestionActiva = $_SESSION['globalGestion'];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT ap.codigo as codigo_antPersonal, ap.cod_personal as cod_personal, ap.monto as monto
                     FROM $table_anticiposPersonal ap, $table_personal p where ap.cod_estadoreferencial=1 and ap.cod_personal=p.codigo and cod_gestion=$codGestionActiva");

$stmt->execute();

$stmt->bindColumn('codigo_antPersonal', $codigo_antPersonal);
$stmt->bindColumn('cod_personal', $cod_personal);
$stmt->bindColumn('monto', $monto);

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
                    <th class="text-center">#</th>
                    <th class="text-center">Personal</th>
                    <th class="text-center">Monto</th>
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
                      <td class="text-left"><?= nombrePersona($cod_personal); ?></td>
                      <td class="text-right"><?= $monto; ?></td>
                      <td class="td-actions text-right">
                        <?php
                        if ($globalAdmin == 1) {
                        ?>
                          <a href='<?= $urlEdit; ?>&cod_ant_per=<?= $codigo_antPersonal; ?>' rel="tooltip" class="<?= $buttonEdit; ?>">
                            <i class="material-icons"><?= $iconEdit; ?></i>
                          </a>
                          <button rel="tooltip" class="<?= $buttonDelete; ?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?= $urlDelete; ?>&cod_ant_per=<?= $codigo_antPersonal; ?>')">
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