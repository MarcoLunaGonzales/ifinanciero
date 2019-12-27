<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$codBono = $codigo_bono;
$codMes = $codigo_mes;


$globalAdmin = $_SESSION["globalAdmin"];
$nombreGestion = $_SESSION['globalNombreGestion'];
$idGestion= $_SESSION['globalGestion'];

$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("select p.codigo as cod_persona,
(select bpm.codigo from bonos_personal_mes bpm where p.codigo=bpm.cod_personal and bpm.cod_bono=$codBono and bpm.cod_mes=$codMes and bpm.cod_gestion=$idGestion and bpm.cod_estadoreferencial=1) as codigo,
(select u.abreviatura from unidades_organizacionales u where u.codigo=p.cod_unidadorganizacional)as unidad,
(select c.nombre from cargos c where c.codigo=p.cod_cargo)as cargo,
concat(p.paterno,' ', p.materno,' ', p.primer_nombre) as nombrepersonal,
(select bpm.monto from bonos_personal_mes bpm where p.codigo=bpm.cod_personal and bpm.cod_bono=$codBono and bpm.cod_mes=$codMes and bpm.cod_gestion=$idGestion and bpm.cod_estadoreferencial=1) as detalle
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

//Mostrar tipo bono
$stmtb = $dbh->prepare("SELECT nombre FROM $table WHERE codigo=$codBono");
$stmtb->execute();
$stmtb->bindColumn('nombre', $nombreBono);

while ($row = $stmtb->fetch(PDO::FETCH_BOUND)) {
  $nomBono= $nombreBono;
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
      <form id="form1" class="form-horizontal" action="<?=$urlSaveEditGrupo;?>" method="POST">

      <input type="hidden" name="cod_mes"  value="<?=$codMes;?>"/>
                      <input type="hidden" name="cod_bono"  value="<?=$codBono;?>"/>
                      <input type="hidden" name="cod_gestion"  value="<?=$idGestion;?>"/>
        <div class="card">
          <div class="card-header <?= $colorCard; ?> card-header-icon">
            <div class="card-icon">
              <i class="material-icons"><?= $iconCard; ?></i>
            </div>
            <h4 class="card-title"><?= $moduleNamePluralDetalle ?></h4>
            <h4 class="card-title" align="center"><?= $nomBono . " : " . $nomMes . " " . $nombreGestion ?></h4>
          </div>
          
          <div class="card-body">
            <div class="table-responsive">
              
              <table id="tablePaginator" class="table table-condensed">
                <thead>
                  <tr>
                    <th class="text-center">#</th>
                    <th>Unidad</th>
                    <th>Cargo</th>
                    <th>Personal</th>
                    <th>Detalle</th>
                    <th class="text-left">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $index = 1;
                  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {

                    ?>
                    <tr>
                      <td align="center"><?= $index; ?></td>
                      <td class="text-center"><?= $unidad; ?></td>
                      <td class="text-left"><?= $cargo; ?></td>
                      <td class="text-left"><?= $nombrepersonal; ?></td>
                      <td class="text-right">
                      <input type="hidden" name="codigo_persona[]"  value="<?=$cod_persona;?>"/>
                      <input type="hidden" name="codBonPerMes[]"  value="<?=$codigo;?>"/>
                      <input class="form-control" type="text" name="detalle[]"  required="true" value="<?= $detalle; ?>" />
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

          <a href="<?=$urlListMesPersona;?>&cod_bono=<?= $codBono; ?>&cod_mes=<?= $codMes; ?>" class="<?=$buttonCancel;?>">Cancelar</a>



          </div>
        <?php
        }
        ?>
      </form>

      </div>
    </div>



  </div>
</div>