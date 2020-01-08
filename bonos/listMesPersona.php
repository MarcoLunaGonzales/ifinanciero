<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$codBono=$codigo_bono;
$codMes=$codigo_mes;

//echo "bono y mes: ".$codBono." ".$codMes;

$globalAdmin=$_SESSION["globalAdmin"];
$nombreGestion=$_SESSION['globalNombreGestion'];
$codGestionActiva=$_SESSION['globalGestion'];

$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("select bpm.cod_personal as cod_persona, bpm.codigo as codigo ,
(select u.abreviatura from personal p, unidades_organizacionales u where p.codigo=bpm.cod_personal and u.codigo=p.cod_unidadorganizacional)as unidad,
(select c.nombre from personal p, cargos c where p.codigo=bpm.cod_personal and c.codigo=p.cod_cargo)as cargo,
(select concat(p.paterno,' ', p.materno,' ', p.primer_nombre) from personal p where p.codigo=bpm.cod_personal)as nombrepersonal,
bpm.monto as detalle from bonos_personal_mes bpm where bpm.cod_bono=$codBono and bpm.cod_mes=$codMes and bpm.cod_gestion=$codGestionActiva and bpm.cod_estadoreferencial=1 ORDER BY nombrepersonal");
// Ejecutamos
$stmt->execute();
// bindColumn
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
  $nomMes= $nombreMes;
}

?>



<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title"><?=$moduleNamePluralDetalle?></h4>
                  <h4 class="card-title" align="center"><?=$nomBono." : ".$nomMes." ".$nombreGestion?></h4>
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
                          <th>Monto</th>
                          <th class="text-right">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
						$index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                           
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td class="text-center"><?= $unidad; ?></td>
                          <td class="text-left"><?= $cargo; ?></td>
                          <td class="text-left"><?= $nombrepersonal; ?></td>
                          <td class="text-right"><?= $detalle; ?></td>
                          <td class="td-actions text-right">
                          <?php
                            if($globalAdmin==1){
                            ?>
                            <!--a href='<?=$urlEdit;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button-->
                            <?php
                            }
                            ?>

                            <!--a href='<?=$urlArchivo;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="btn btn-default">
                              <i class="material-icons">attachment</i>
                            </a-->

                  

                            <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteBonoMesPersona;?>&codigo=<?=$codigo;?>&cod_bono=<?=$codBono;?>&cod_mes=<?=$codMes;?>')">
                              <i class="material-icons" title="Eliminar"><?=$iconDelete;?></i>
                            </button>
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
              if($globalAdmin==1){
                
              ?>
      				<div class="card-footer fixed-bottom">
                <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegisterBonoMesPersona;?>&cod_mes=<?=$codMes;?>&cod_bono=<?=$codBono;?>'">Registrar</button>

                <button class="<?= $buttonCeleste; ?>" onClick="location.href='<?= $urlEditarBonosMesPersona; ?>&cod_mes=<?= $codMes; ?>&cod_bono=<?= $codBono; ?>'">Registrar/Editar en Grupo</button>

                <button class="<?= $buttonExcel; ?>" onClick="location.href='<?= $urlSubirBonoExcel; ?>&cod_mes=<?= $codMes; ?>&cod_bono=<?= $codBono; ?>'">Subir Datos desde Excel</button>

                <button class="<?=$buttonCancel;?>" onClick="location.href='<?=$urlListMes;?>&codigo=<?=$codBono;?>'">Cancelar</button>
              
              <?php

              if($codBono==5){
              ?>
                <button class="<?=$comboColorDist;?>" onClick="location.href='<?=$calculaBonoProfesion;?>&cod_mes=<?=$codMes;?>&cod_bono=<?=$codBono;?>'">Calcular Bono</button>

      				</div>
              <?php
              }
              }
              ?>


		  
            </div>
          </div>  
              


        </div>
    </div>
