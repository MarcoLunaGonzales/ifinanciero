<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$stmt = $dbh->prepare("select * from aportes_patronales where estado=1");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('seguro_riesgo_profesional', $seguro_riesgo_profesional);
$stmt->bindColumn('provivienda', $provivienda);
$stmt->bindColumn('infocal', $infocal);
$stmt->bindColumn('cns', $cns);
$stmt->bindColumn('aporte_patronal_solidario', $aporte_patronal_solidario);
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
                  <h4 class="card-title"><?=$nombreSingularaportes_patronales?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                    <thead>
    <tr>
        <th>Codigo</th>
        <th>Seguro Rie. Prof.</th>
        <th>Provivienda</th>
        <th>Infocal</th>
        <th>Cns</th>
        <th>Aporte patro. Sol.</th>
        
        <th></th>
    </tr>
</thead>
<tbody>
<?php $index=1;
while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
    <tr>
        <td><?=$codigo;?></td>
        <td><?=$seguro_riesgo_profesional;?></td>
        <td><?=$provivienda;?></td>
        <td><?=$infocal;?></td>
        <td><?=$cns;?></td>
        <td><?=$aporte_patronal_solidario;?></td>
        
        <td class="td-actions text-right">
        <?php
          if($globalAdmin==1){
        ?>
          <a href='<?=$urlFormaportes_patronales;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
            <i class="material-icons"><?=$iconEdit;?></i>
          </a>
          <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteaportes_patronales;?>&codigo=<?=$codigo;?>')">
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
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormaportes_patronales;?>&codigo=0'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>
