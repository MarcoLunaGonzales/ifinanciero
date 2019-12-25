<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$globalAdmin = $_SESSION["globalAdmin"];

$codDotacion=$cod_dot;

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT dp.codigo as cod_dotacion_personal, dp.cod_personal as cod_persona,
(select u.abreviatura from personal p, unidades_organizacionales u where p.codigo=dp.cod_personal and u.codigo=p.cod_unidadorganizacional)as unidad,
(select c.nombre from personal p, cargos c where p.codigo=dp.cod_personal and c.codigo=p.cod_cargo)as cargo,
(select concat(p.paterno,' ', p.materno,' ', p.primer_nombre) from personal p where p.codigo=dp.cod_personal)as nombrepersonal,
dp.monto as detalle
FROM dotaciones_personal dp where dp.cod_estadoreferencial=1 and dp.cod_dotacion=$codDotacion");

$stmt->execute();

$stmt->bindColumn('cod_dotacion_personal', $codDotacionPersonal);
$stmt->bindColumn('cod_persona', $codPersona);
$stmt->bindColumn('unidad', $unidad);
$stmt->bindColumn('cargo', $cargo);
$stmt->bindColumn('nombrepersonal', $nombrePersonal);
$stmt->bindColumn('detalle', $detalle);


//Mostrar tipo dotacion
$stmtb = $dbh->prepare("SELECT nombre FROM dotaciones WHERE codigo=$codDotacion");
$stmtb->execute();
$stmtb->bindColumn('nombre', $nombreDotacion);

while ($row = $stmtb->fetch(PDO::FETCH_BOUND)) {
  $nomDotacion = $nombreDotacion;
}



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
            <h4 class="card-title"><?= $moduleNamePluralDP ?></h4>
            <h4 class="card-title" align="center"><?=  "Dotación de " . $nomDotacion ?></h4>

          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="tablePaginator" class="table table-condensed">
                <thead>
                  <tr>
                    <th class="text-left">#</th>
                    <th class="text-center">Unidad</th>
                    <th class="text-center">Cargo</th>
                    <th class="text-center">Personal</th>
                    <th class="text-center">Detalle</th>
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
                      <td class="text-left"><?= $unidad; ?></td>
                      <td class="text-center"><?= $cargo; ?></td>
                      <td class="text-left"><?= $nombrePersonal; ?></td>
                      <td class="text-center"><?= $detalle; ?></td>
                      <td class="td-actions text-right">
                        <?php
                        if ($globalAdmin == 1) {
                        ?>
                        <a href='<?=$urlListDotacionPersonal;?>&cod_dot=<?=$cod_dotacion;?>' rel="tooltip" class="<?=$buttonDetailMin;?>">
                              <i class="material-icons" title="Detalle">playlist_add</i>
                          </a>
                          <button rel="tooltip" class="<?= $buttonDelete; ?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?= $urlDeleteDotacionPersonal; ?>&cod_dot=<?= $codDotacion; ?>&cod_dot_per=<?= $codDotacionPersonal; ?>')">
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
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegisterDotacionPersonal;?>&cod_dot=<?=$codDotacion;?>'">Registrar</button>
                    <button class="<?= $buttonCancel; ?>" onClick="location.href='<?= $urlList; ?>'">Cancelar</button>

              </div>
        <?php
        }
        ?>


      </div>
    </div>
  </div>
</div>