<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$globalAdmin = $_SESSION["globalAdmin"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT dgp.codigo as codDistribucion,
(SELECT uo.nombre FROM unidades_organizacionales uo WHERE uo.codigo=dgp.cod_unidadorganizacional) as unidad_nomb,
(SELECT uo.abreviatura FROM unidades_organizacionales uo WHERE uo.codigo=dgp.cod_unidadorganizacional) as unidad_abrev,
porcentaje
FROM $table_distribucion dgp");

$stmt->execute();

$stmt->bindColumn('codDistribucion', $cod_distribucion);
$stmt->bindColumn('unidad_nomb', $unidad_nombre);
$stmt->bindColumn('unidad_abrev', $unidad_abreviatura);
$stmt->bindColumn('porcentaje', $porcentaje);

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
              <table class="table table-condensed">
                <thead>
                  <tr>
                    <th class="text-left">#</th>
                    <th class="text-center">Unidad</th>
                    <th class="text-center">Abreviatura</th>
                    <th class="text-center">Porcentaje</th>
                    <!--th class="text-right">Actions</th-->
                  </tr>
                </thead>
                <tbody>
                  <?php
                     $index = 1;
                     $sum=0;
                      while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                  ?>
                    <tr>
                      <td class="text-center"><?= $index; ?></td>
                      <td class="text-left"><?= $unidad_nombre; ?></td>
                      <td class="text-center"><?= $unidad_abreviatura; ?></td>
                      <td class="text-center"><?= $porcentaje." %"; ?></td>
                      <!--td class="td-actions text-right">
                        <?php
                        if ($globalAdmin == 1) {
                        ?>
                          <a href='<?= $urlEdit; ?>&cod_esc_ant=<?= $cod_escala_antiguedad; ?>' rel="tooltip" class="<?= $buttonEdit; ?>">
                            <i class="material-icons"><?= $iconEdit; ?></i>
                          </a>
                          <button rel="tooltip" class="<?= $buttonDelete; ?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?= $urlDelete; ?>&cod_esc_ant=<?= $cod_escala_antiguedad; ?>')">
                            <i class="material-icons"><?= $iconDelete; ?></i>
                          </button>
                        <?php
        }
                        ?>
                      </td-->
                    </tr>
                  <?php
                          $index++;
                          $sum=$sum+$porcentaje;
                         }
                  ?>
                </tbody>
                <tfoot>
                <tr></tr>
                <tr>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <th class="text-center">Total : </th>
                <th class="text-center"><?= $sum; ?> %</th>
              </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
        <?php
                  if ($globalAdmin == 1) {
        ?>
          <div class="card-footer fixed-bottom">
            <!--button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegister;?>'">Registrar</button-->
            <button class="<?= $buttonCeleste; ?>" onClick="location.href='<?= $urlEditarDistribucionGastos; ?>'">Editar en Grupo</button>

          </div>
        <?php
        }
        ?>


      </div>
    </div>
  </div>
</div>