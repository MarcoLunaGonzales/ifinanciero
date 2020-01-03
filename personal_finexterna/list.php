<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$globalAdmin = $_SESSION["globalAdmin"];
$codGestionActiva = $_SESSION['globalGestion'];
$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT pfp.*,concat(primer_nombre,' ',paterno,' ',materno) as personal,py.nombre,py.abreviatura
                     FROM $table_personalfin pfp, $table_personal p,$table_proyectos py where pfp.cod_estado_referencial=1 
                     and pfp.cod_personal=p.codigo and pfp.cod_proyecto=py.codigo");

$stmt->execute();
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $proyecto);
$stmt->bindColumn('personal', $personal);
$stmt->bindColumn('abreviatura', $abrev);
$stmt->bindColumn('monto_subsidio', $monto);
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
                    <th class="text-center">Proyecto</th>
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
                      <td class="text-left"><?= $proyecto ?></td>
                      <td class="text-left"><?= $personal ?></td>
                      <td class="text-right"><?= $monto; ?></td>
                      <td class="td-actions text-right">
                        <?php
                        if ($globalAdmin == 1) {
                        ?>
                          <a href='<?= $urlEdit; ?>&codigo=<?= $codigo; ?>' rel="tooltip" class="<?= $buttonEdit; ?>">
                            <i class="material-icons"><?= $iconEdit; ?></i>
                          </a>
                          <button rel="tooltip" class="<?= $buttonDelete; ?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?= $urlDelete; ?>&codigo=<?= $codigo; ?>')">
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
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?= $urlRegister; ?>'">Registrar</button>
              </div>
        <?php
        }
        ?>


      </div>
    </div>
  </div>
</div>