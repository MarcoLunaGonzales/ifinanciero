<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$globalAdmin = $_SESSION["globalAdmin"];

$dbh = new Conexion();
$codRefrigerio=$cod_refrigerio;
$codMes=$cod_mes;

$stmt = $dbh->prepare("SELECT rd.codigo as cod_ref_detalle,
(select concat(p.paterno,' ', p.materno,' ', p.primer_nombre) from personal p where p.codigo=rd.cod_personal)as nombrepersonal,
rd.dias_asistidos as dias_asistencia,
rd.monto as monto_refrigerio,
(SELECT (rd.dias_asistidos*rd.monto)) AS total_mensual  
FROM refrigerios_detalle rd WHERE rd.cod_estadoreferencial=1 and rd.cod_refrigerio=$codRefrigerio");

$stmt->execute();

$stmt->bindColumn('cod_ref_detalle', $cod_ref_detalle);
$stmt->bindColumn('nombrepersonal', $nombrepersonal);
$stmt->bindColumn('dias_asistencia', $dias_asistencia);
$stmt->bindColumn('monto_refrigerio', $monto_refrigerio);
$stmt->bindColumn('total_mensual', $total_mensual);

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
                    <th class="text-center">Persona</th>
                    <th class="text-center">Dias Asistidos</th>
                    <th class="text-center">Monto Refrigerio</th>
                    <th class="text-center">Monto Mensual</th>
                    <th class="text-right">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                     $index = 1;
                      while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                  ?>
                    <tr>
                      <td class="text-center"><?= $index; ?></td>
                      <td class="text-left"><?= $nombrepersonal; ?></td>
                      <td class="text-center"><?= $dias_asistencia; ?></td>
                      <td class="text-center"><?= $monto_refrigerio; ?></td>
                      <td class="text-center"><?= $total_mensual; ?></td>
                      <td class="td-actions text-right">
                        <?php
                        if ($globalAdmin == 1) {
                        ?>
                        <!--a href='<?= $urlDetalle; ?>&cod_ref=<?= $codRefrigerio; ?>' rel="tooltip" class="<?=$buttonDetailMin;?>">
                              <i class="material-icons" title="Detalle">playlist_add</i>
                            </a>
                          <a href='<?= $urlAprobar; ?>&cod_ref=<?= $codRefrigerio; ?>' rel="tooltip" class="btn btn-warning">
                            <i class="material-icons" title="Aprobar" style="color:black">extension</i>
                          </a-->
                          
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
              <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$calculaRefrigerioMes;?>&cod_ref=<?= $codRefrigerio; ?>&cod_mes=<?= $codMes; ?>'">Generar</button>
              <button class="<?= $buttonCancel; ?>" onClick="location.href='<?= $urlList; ?>'">Cancelar</button>
              </div>
        <?php
        }
        ?>


      </div>
    </div>
  </div>
</div>