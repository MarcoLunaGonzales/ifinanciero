<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$codigoCheque=$_GET['codigo'];
//echo "test cod bono: ".$codigoCheque;

$globalAdmin=$_SESSION["globalAdmin"];
$nombreGestion=$_SESSION['globalNombreGestion'];
$codGestionActiva=$_SESSION['globalGestion'];

$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT ce.*
FROM cheques_emitidos ce where ce.cod_cheque=$codigoCheque and  ce.cod_estadoreferencial=1 order by ce.fecha");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre_beneficiario', $nombre);
$stmt->bindColumn('fecha', $fecha);
$stmt->bindColumn('monto', $monto);


//Mostrar tipo bono
$stmtb = $dbh->prepare("SELECT p.nombre,c.nro_cheque,c.nro_serie FROM $table c join bancos p on c.cod_banco=p.codigo WHERE c.codigo=$codigoCheque");
// Ejecutamos
$stmtb->execute();
// bindColumn
$stmtb->bindColumn('nombre', $nombreBanco);
$stmtb->bindColumn('nro_serie', $serie);
$stmtb->bindColumn('nro_cheque', $cheque);

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
                  
                  <?php
                  while ($row = $stmtb->fetch(PDO::FETCH_BOUND)) {
                    ?>
                  <h4 class="card-title" align="center"><?=$nombreBanco?> <b>NRO. CUENTA: <?=$serie?></b> / <b>NRO. CHEQUE: <?=$cheque?></b></h4>
                  <?php
                  }
                  ?>
                </div>
                
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Beneficiario</th>
                          <th>Fecha</th>
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
                          <td class="text-left"><?=$nombre?></td>
                          <td class="text-center"><?=strftime('%d/%m/%Y',strtotime($fecha))?></td>
                          <td class="text-center"><?=number_format($monto,2,".","")?></td>
                          <td class="td-actions text-right">
                          <?php
                            if($globalAdmin==1){
                            ?>
                            <?php
                            }
                            ?>

                

                           <!-- <a href='<?=$urlListMesPersona;?>&cod_bono=<?=$codigoCheque;?>&cod_mes=<?=$codigo;?>' rel="tooltip" class="<?=$buttonMorado;?>">
                              <i class="material-icons" title="Ver Personal">playlist_add</i>
                            </a>-->
                            
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
                <button class="<?=$buttonCancel;?>" onClick="location.href='<?=$urlList;?>'">Volver</button>
                <!--<button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegisterBonoPeriodoPersona;?>&cod_bono=<?=$codigoCheque;?>'">Registrar por periodo</button>
                <button class="btn btn-warning" onClick="location.href='<?=$urlFinBonoPeriodoPersona;?>&cod_bono=<?=$codigoCheque;?>'">Detener Bonos Indefinidos</button>-->
              </div>
              

              <?php
              }
              ?>
		  
            </div>
          </div>  
              


        </div>
    </div>
