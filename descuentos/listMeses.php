<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$codigoDescuento=$codigo_descuento;
//echo "test cod bono: ".$codigoDescuento;

$globalAdmin=$_SESSION["globalAdmin"];
$nombreGestion=$_SESSION['globalNombreGestion'];
$codGestionActiva=$_SESSION['globalGestion'];

$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT m.codigo as codigo, m.nombre as nombre, count(dpm.cod_personal) as cantidad FROM meses m LEFT JOIN descuentos_personal_mes dpm 
ON  dpm.cod_mes=m.codigo AND dpm.cod_descuento=$codigoDescuento and dpm.cod_gestion=$codGestionActiva  and dpm.cod_estadoreferencial=1 GROUP BY m.codigo");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('cantidad',$cantidad);


//Mostrar tipo descuento
$stmtb = $dbh->prepare("SELECT nombre FROM $table WHERE codigo=$codigoDescuento");
// Ejecutamos
$stmtb->execute();
// bindColumn
$stmtb->bindColumn('nombre', $nombreDescuento);
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
                  <h4 class="card-title" align="center"><?=$nombreDescuento?></h4>
                  <?php
                  }
                  ?>
                </div>
                
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="tablePaginator" class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Mes</th>
                          <th>Cantidad de Registros</th>
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
                          <td class="text-left"><?=$nombre."/".$nombreGestion;?></td>
                          <td class="text-center"><?=$cantidad." registros";?></td>
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

                

                            <a href='<?=$urlListMesPersona;?>&cod_descuento=<?=$codigoDescuento;?>&cod_mes=<?=$codigo;?>' rel="tooltip" class="<?=$buttonMorado;?>">
                              <i class="material-icons" title="Ver Personal">playlist_add</i>
                            </a>
                            
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
                    <button class="<?=$buttonCancel;?>" onClick="location.href='<?=$urlList;?>'">Cancelar</button>
              </div>
              

              <?php
              }
              ?>
		  
            </div>
          </div>  
              


        </div>
    </div>
