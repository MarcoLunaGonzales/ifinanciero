<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$globalAdmin = $_SESSION["globalAdmin"];

$codDotacion=$cod_dot;
$codDotacionPersonal=$cod_dot_per;

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT dpm.cod_dotacionpersonal as cod_dotacion_personal, 
(SELECT m.nombre FROM meses m WHERE dpm.cod_mes=m.codigo) as mes ,
(SELECT g.nombre FROM gestiones g WHERE dpm.cod_gestion=g.codigo) as gestion,
dpm.monto_mes as monto_mensual FROM dotaciones_personal_mes dpm WHERE dpm.cod_dotacionpersonal=$codDotacionPersonal");

$stmt->execute();

$stmt->bindColumn('cod_dotacion_personal', $codDotacionPersonal);
$stmt->bindColumn('mes', $mes);
$stmt->bindColumn('gestion', $gestion);
$stmt->bindColumn('monto_mensual', $monto_mensual);

//Mostrar tipo dotacion
$stmtb = $dbh->prepare("SELECT nombre FROM dotaciones WHERE codigo=$codDotacion");
$stmtb->execute();
$stmtb->bindColumn('nombre', $nombreDotacion);

while ($row = $stmtb->fetch(PDO::FETCH_BOUND)) {
  $nomDotacion = $nombreDotacion;
}

//Mostrar Persona
$stmtb = $dbh->prepare("select dp.codigo as codigo,dp.monto as monto,
(select concat(p.paterno,' ', p.materno,' ', p.primer_nombre) from personal p where p.codigo=dp.cod_personal) as nombrepersonal
FROM dotaciones_personal dp WHERE dp.codigo=$codDotacionPersonal");
$stmtb->execute();
$stmtb->bindColumn('monto', $monto);
$stmtb->bindColumn('nombrepersonal', $nombrePersonal);

while ($row = $stmtb->fetch(PDO::FETCH_BOUND)) {
  $montoX = $monto;
  $nomPersonal = $nombrePersonal;
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
            <h4 class="card-title" align="center"><?=  "Detalle de Dotación de " . $nomDotacion. " : ".$nombrePersonal ?></h4>
            <!--h4 class="card-title" align="center"><?=  "Monto Total : " . $montoX ?></h4-->


          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-condensed">
                <thead>
                  <tr>
                    <th class="text-left">#</th>
                    <th class="text-center">Mes</th>
                    <th class="text-center">Gestion</th>
                    <th class="text-center">Monto Mensual</th>
                    <!--th class="text-right">Actions</th-->
                  </tr>
                </thead>
                <tbody>
                  <?php
                     $index = 1;
                      while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                  ?>
                    <tr>
                      <td class="text-center"><?= $index; ?></td>
                      <td class="text-left"><?= $mes; ?></td>
                      <td class="text-center"><?= $gestion; ?></td>
                      <td class="text-center"><?= $monto_mensual; ?></td>
                      <!--td class="td-actions text-right">
                        <?php
                        if ($globalAdmin == 1) {
                        ?>
                       
                          <button rel="tooltip" class="<?= $buttonDelete; ?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?= $urlDeleteDotacionPersonal; ?>&cod_dot=<?= $codDotacion; ?>&cod_dot_per=<?= $codDotacionPersonal; ?>')">
                            <i class="material-icons"><?= $iconDelete; ?></i>
                          </button>
                        <?php
        }
                        ?>
                      </td-->
                    </tr>
                    
                  <?php
                          $index++;
                         }
                  ?>
                  
                </tbody>
                <tfoot>
                <tr></tr>
                <tr>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <th class="text-center">Total : </th>
                <th class="text-center"><?= $montoX ; ?></th>
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
                   
                    <button class="<?= $buttonCancel; ?>" onClick="location.href='<?= $urlListDotacionPersonal; ?>&cod_dot=<?=$codDotacion;?>'"> <-- Volver </button>

              </div>
        <?php
        }
        ?>


      </div>
    </div>
  </div>
</div>