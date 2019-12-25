<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$stmt = $dbh->prepare("select * from aportes_laborales where estado=1");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('salario_minimo_nacional', $salario_minimo_nacional);
$stmt->bindColumn('cuenta_individual_vejez', $cuenta_individual_vejez);
$stmt->bindColumn('seguro_invalidez', $seguro_invalidez);
$stmt->bindColumn('comision_afp', $comision_afp);
$stmt->bindColumn('provivienda', $provivienda);

$stmt->bindColumn('iva', $iva);
$stmt->bindColumn('asa', $asa);
$stmt->bindColumn('aporte_nac_solidario_13', $aporte_nac_solidario_13);
$stmt->bindColumn('aporte_nac_solidario_25', $aporte_nac_solidario_25);
$stmt->bindColumn('aporte_nac_solidario_35', $aporte_nac_solidario_35);


$stmt->bindColumn('estado', $cod_estadoreferencial);
$stmt->bindColumn('created_at', $created_at);
$stmt->bindColumn('created_by', $created_by);
$stmt->bindColumn('modified_at', $modified_at);
$stmt->bindColumn('modified_by', $modified_by);
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
                  <h4 class="card-title"><?=$nombrePluralaportes_laborales?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                    <thead>
    <tr>
        <th>Codigo</th>
        <th>Salario Min. Nac.</th>
        <th>cuenta_individual_vejez</th>
        <th>seguro_invalidez</th>
        <th>comision_afp</th>
        <th>Provivienda</th>
        
        <th></th>
    </tr>
</thead>
<tbody>
<?php $index=1;
while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
    <tr>
        <td><?=$codigo;?></td>
        <td><?=$salario_minimo_nacional;?></td>
        <td><?=$cuenta_individual_vejez;?></td>
        <td><?=$seguro_invalidez;?></td>
        <td><?=$comision_afp;?></td>
        <td><?=$provivienda;?></td>
        
        <td class="td-actions text-right">
        <?php
          if($globalAdmin==1){
        ?>
          <a href='<?=$urlFormaportes_laborales;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
            <i class="material-icons"><?=$iconEdit;?></i>
          </a>
          <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteaportes_laborales;?>&codigo=<?=$codigo;?>')">
            <i class="material-icons"><?=$iconDelete;?></i>
          </button>
          <?php
            }
          ?>
        
        </td>
    </tr>
<?php $index++; } ?>
</tbody>
                    
                    </table>
                  </div>
                </div>
              </div>
              <?php





              if($globalAdmin==1){
              ?>
      				<div class="card-footer fixed-bottom">
                    <!--<button class="<?=$buttonNormal;?>" onClick="location.href='index.php?opcion=registerUbicacion'">Registrar</button>-->
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormaportes_laborales;?>&codigo=0'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>
