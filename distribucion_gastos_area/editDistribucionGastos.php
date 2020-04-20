<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$globalAdmin = $_SESSION["globalAdmin"];
$codigo=$codigo;
$dbh = new Conexion();
$stmtDist = $dbh->prepare("SELECT nombre from distribucion_gastosarea where codigo=$codigo");
$stmtDist->execute();
$resultDist=$stmtDist->fetch();
$nombre_d=$resultDist['nombre'];

$sqlArea="SELECT dgd.codigo as codDistribucion, dgd.cod_area as codUnidad,
  (SELECT uo.nombre FROM areas uo WHERE uo.codigo=dgd.cod_area) as unidad_nomb,
  (SELECT uo.abreviatura FROM areas uo WHERE uo.codigo=dgd.cod_area) as unidad_abrev,dgd.porcentaje
from distribucion_gastosarea_detalle dgd where dgd.cod_distribucionarea=$codigo";
//echo $sqlArea;
$stmt = $dbh->prepare($sqlArea);

$stmt->execute();

$stmt->bindColumn('codDistribucion', $cod_distribucionDetalle);
$stmt->bindColumn('codUnidad', $cod_unidad);
$stmt->bindColumn('unidad_nomb', $unidad_nombre);
$stmt->bindColumn('unidad_abrev', $unidad_abreviatura);
$stmt->bindColumn('porcentaje', $porcentaje);
?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
      <form name="form1" id="form1" action="<?=$urlSaveEditDistribucionGasto;?>" method="POST">
        <input type="hidden" name="codDistribucionGastos" id="codDistribucionGastos" value="<?=$codigo?>" >
        <div class="card">
          <div class="card-header <?= $colorCard; ?> card-header-icon">
            <div class="card-icon">
              <i class="material-icons"><?= $iconCard; ?></i>
            </div>
            <h4 class="card-title">Detalle <?= $moduleNamePlural ?></h4>
            <h4 class="card-title" align="center"><?=$nombre_d?></h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">

              
              <table class="table table-condensed">
                <thead>
                  <tr>
                    <th class="text-left">#</th>
                    <th class="text-center">Area</th>
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
                      <td class="text-center">
                      <input type="hidden" name="codigo_distribucion[]"  value="<?=$cod_distribucionDetalle;?>"/>
                      <input class="form-control sm" type="number" onchange="sumarPorcentaje()" id="porcentaje<?= $index; ?>" name="porcentaje[]"  required="true" value="<?= $porcentaje; ?>" onkeyup="sumarPorcentaje(); javascript:this.value=this.value.toUpperCase();"/>  

                      </td>
                      <!--td class="td-actions text-right">
                        <?php
                        $sum=$sum+$porcentaje;
                        
                        if ($globalAdmin == 1) {
                        ?>
                          
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
                         }
                  ?>
                </tbody>
                <tfoot>
                  <tr></tr>
                  <tr>
                  <td colspan="2"></td>
                  <th >Porcentaje Total (100 %) : </th>
                  <th><input type="text" required="true" id="total" name="total" value="<?= $sum; ?>" readonly="readonly" ></th>
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
          <button type="submit" id="botonGuardar" class="<?=$buttonCeleste;?>">Guardar</button>
          <a href='<?= $urlDistribucionGastosDetalle; ?>&codigo=<?=$codigo?>'  rel="tooltip" class="<?=$buttonCancel;?>">Volver
              
          </a>
          </div>
        <?php
        }
        ?>
</form>

      </div>
    </div>
  </div>
</div>

