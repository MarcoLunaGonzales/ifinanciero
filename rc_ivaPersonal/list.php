<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$globalAdmin = $_SESSION["globalAdmin"];
$codGestionActiva = $_SESSION['globalGestion'];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT rcip.codigo as codigo_rciva, rcip.cod_personal as cod_personal, rcip.monto as monto,rcip.monto_iva as monto_iva
                     FROM $table_rcivaPersonal rcip, personal p where rcip.cod_estadoreferencial=1 and rcip.cod_personal=p.codigo and cod_gestion=$codGestionActiva");

$stmt->execute();

$stmt->bindColumn('codigo_rciva', $codigo_rciva);
$stmt->bindColumn('cod_personal', $cod_personal);
$stmt->bindColumn('monto', $monto);
$stmt->bindColumn('monto_iva', $monto_iva);

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
                    <th class="text-center">Monto IVA</th>
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
                      <td class="text-right"><?= $monto_iva; ?></td>
                      <td class="td-actions text-right">
                        <?php
                        if ($globalAdmin == 1) {
                        ?>
                          <a href='<?= $urlEdit; ?>&cod_rciva=<?= $codigo_rciva; ?>' rel="tooltip" class="<?= $buttonEdit; ?>">
                            <i class="material-icons"><?= $iconEdit; ?></i>
                          </a>
                          <button rel="tooltip" class="<?= $buttonDelete; ?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?= $urlDelete; ?>&cod_rciva=<?= $codigo_rciva; ?>')">
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