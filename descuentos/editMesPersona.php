<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$codDescuento = $codigo_descuento;
$codMes = $codigo_mes;


$globalAdmin = $_SESSION["globalAdmin"];
$nombreGestion = $_SESSION['globalNombreGestion'];
$idGestion= $_SESSION['globalGestion'];

$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("select p.codigo as cod_persona,
(select dpm.codigo from descuentos_personal_mes dpm where p.codigo=dpm.cod_personal and dpm.cod_descuento=$codDescuento and dpm.cod_mes=$codMes and dpm.cod_gestion=$idGestion and dpm.cod_estadoreferencial=1) as codigo,
(select u.abreviatura from unidades_organizacionales u where u.codigo=p.cod_unidadorganizacional)as unidad,
(select c.nombre from cargos c where c.codigo=p.cod_cargo)as cargo,
concat(p.paterno,' ', p.materno,' ', p.primer_nombre) as nombrepersonal,
(select dpm.monto from descuentos_personal_mes dpm where p.codigo=dpm.cod_personal and dpm.cod_descuento=$codDescuento and dpm.cod_mes=$codMes and dpm.cod_gestion=$idGestion and dpm.cod_estadoreferencial=1) as detalle
from personal p  ORDER BY nombrepersonal");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('cod_persona',$cod_persona);
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('unidad', $unidad);
$stmt->bindColumn('cargo', $cargo);
$stmt->bindColumn('nombrepersonal', $nombrepersonal);
$stmt->bindColumn('detalle', $detalle);

//Mostrar tipo descuento
$stmtb = $dbh->prepare("SELECT nombre FROM $table WHERE codigo=$codDescuento");
$stmtb->execute();
$stmtb->bindColumn('nombre', $nombreDescuento);

while ($row = $stmtb->fetch(PDO::FETCH_BOUND)) {
  $nomDescuento = $nombreDescuento;
}

//Mostrar Mes
$stmtc = $dbh->prepare("SELECT nombre FROM meses WHERE codigo=$codMes");
$stmtc->execute();
$stmtc->bindColumn('nombre', $nombreMes);

while ($row = $stmtc->fetch(PDO::FETCH_BOUND)) {
  $nomMes = $nombreMes;
}

?>



<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
      <form id="form_bonosgrupos" class="form-horizontal" action="<?=$urlSaveEditGrupo;?>" method="POST">

      <input type="hidden" name="cod_mes"  value="<?=$codMes;?>"/>
                      <input type="hidden" name="cod_descuento"  value="<?=$codDescuento;?>"/>
                      <input type="hidden" name="cod_gestion"  value="<?=$idGestion;?>"/>
        <div class="card">
          <div class="card-header <?= $colorCard; ?> card-header-icon">
            <div class="card-icon">
              <i class="material-icons"><?= $iconCard; ?></i>
            </div>
            <h4 class="card-title"><?= $moduleNamePluralDetalle ?></h4>
            <h4 class="card-title" align="center"><?= $nomDescuento . " : " . $nomMes . " " . $nombreGestion ?></h4>
          </div>
          
          <div class="card-body">
            <div class="table-responsive">
              
              <table class="table table-condensed">
                <thead>
                  <tr>
                    <th class="text-center">#</th>
                    <th>Unidad</th>
                    <th>Cargo</th>
                    <th>Personal</th>
                    <th>Monto</th>
                    <th class="text-left">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $index = 1;
                  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                    $data[$index-1][0]=$index;
                    $data[$index-1][1]=$cod_persona;
                    $data[$index-1][2]=$codigo;
                    $data[$index-1][3]=$detalle;
                    ?>
                    <tr>
                      <td align="center"><?= $index; ?></td>
                      <td class="text-center"><?= $unidad; ?></td>
                      <td class="text-left"><?= $cargo; ?></td>
                      <td class="text-left"><?= $nombrepersonal; ?></td>
                      <td class="text-right">
                      <input type="hidden" name="codigo_persona[]"  value="<?=$cod_persona;?>"/>
                      <input type="hidden" name="codDescPerMes[]"  value="<?=$codigo;?>"/>
                      <input class="form-control text-right" type="text" name="detalle[]" id="monto_detalle<?=$index?>" autocomplete="off" value="<?= $detalle; ?>" onchange="registrarMontoPersonal(<?=$index?>)"/>
                      </td>
                      

                     

                      <td class="td-actions text-right">
                        <?php
                          if ($globalAdmin == 1) {
                            ?>
                        
                        <?php
                          }
                          ?>

                        <!--button rel="tooltip" class="<?= $buttonDelete; ?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?= $urlDeleteDescuentoMesPersona; ?>&codigo=<?= $codigo; ?>&cod_descuento=<?= $codDescuento; ?>&cod_mes=<?= $codMes; ?>')">
                          <i class="material-icons" title="Eliminar"><?= $iconDelete; ?></i>
                        </button-->
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

          <button type="submit" class="<?=$buttonCeleste;?>">Guardar</button>

          <a href="<?=$urlListMesPersona;?>&cod_descuento=<?= $codDescuento; ?>&cod_mes=<?= $codMes; ?>" class="<?=$buttonCancel;?>"> <-- Volver </a>



          </div>
        <?php
        }
        ?>
      </form>

      </div>
    </div>



  </div>
</div>
<?php 
for ($i=0; $i < $index; $i++) { 
  ?><script>montos_personal.push({codigo:<?=$data[$i][0]?>,cod_persona:'<?=$data[$i][1]?>',desc_mes:'<?=$data[$i][2]?>',monto:'<?=$data[$i][3]?>'});</script><?php
}
?>