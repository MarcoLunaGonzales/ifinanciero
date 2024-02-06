<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$globalAdmin = $_SESSION["globalAdmin"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT r.codigo as cod_refrigerio,
(SELECT g.nombre FROM gestiones g WHERE r.cod_gestion=g.codigo) as gestion,
(SELECT m.nombre FROM meses m WHERE r.cod_mes=m.codigo) as mes,
(SELECT m.codigo FROM meses m WHERE r.cod_mes=m.codigo) as codigo_mes,
(SELECT ep.nombre FROM estados_planilla ep WHERE r.cod_estadoplanilla=ep.codigo) as estado_planilla,r.cod_comprobante,r.cod_gestion
FROM  $table_refrigerios r order by r.cod_gestion DESC, codigo_mes DESC");

$stmt->execute();

$stmt->bindColumn('cod_refrigerio', $codRefrigerio);
$stmt->bindColumn('gestion', $gestion);
$stmt->bindColumn('mes', $mes);
$stmt->bindColumn('codigo_mes', $codigo_mes);
$stmt->bindColumn('estado_planilla', $estadoPlanilla);
$stmt->bindColumn('cod_comprobante', $comprobante_x);
$stmt->bindColumn('cod_gestion', $cod_gestion);



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
                    <th class="text-left"> # </th>
                    <th class="text-center">Gestión</th>
                    <th class="text-center">Mes</th>
                    <th class="text-center">Estado Planilla</th>
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
                      <td class="text-left"><?= $gestion; ?></td>
                      <td class="text-center"><?= $mes; ?></td>
                      <td class="text-center"><?= $estadoPlanilla; ?></td>
                      <td class="td-actions text-right">
                        <?php
                        if ($globalAdmin == 1) {
                        ?>
                        <a href='<?= $urlDetalle; ?>&cod_ref=<?= $codRefrigerio; ?>&cod_mes=<?=$codigo_mes;?>' rel="tooltip" class="<?=$buttonDetailMin;?>">
                          <i class="material-icons" title="Detalle">playlist_add</i>
                        </a>
                        <a href='<?= $urlAprobar; ?>&cod_ref=<?= $codRefrigerio; ?>' rel="tooltip" class="btn btn-success">
                          <i class="material-icons" title="Aprobar" style="color:white">done</i>
                        </a>
                          <?php if($comprobante_x==0){ ?>
                            <a  href="#" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','refrigerios/execute_comprobante.php?cod_ref=<?=$codRefrigerio;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$codigo_mes;?>')" class="btn btn-danger"> 
                              <i class="material-icons" title="Generar Comprobante" style="color:white">input</i>
                            </a>                            
                            <?php } ?>
                          
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