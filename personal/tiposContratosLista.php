<?php

require_once 'conexion.php';
require_once 'rrhh/configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$sql="SELECT * from tipos_contrato_personal where cod_estadoreferencial=1 ";
$stmt = $dbh->prepare($sql);

//echo $sql;
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('duracion_meses', $duracion_meses);
$stmt->bindColumn('meses_alerta', $meses_alerta);
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
                  <h4 class="card-title">Tipos de Contrato</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">
                      <thead>
                      <tr>                          
                          <th>#</th>
                          <th>Codigo</th>
                          <th>Nombre</th>
                          <th>Duración De Meses</th>
                          <th>Meses Alerta</th>                                          
                          <th></th>
                      </tr>
                  </thead>
                  <tbody>
                  <?php $index=1;
                  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
                      <tr>
                        <td><?=$index;?></td>
                        <td><?=$codigo;?></td>
                        <td><?=$nombre;?></td>
                        <td><?=$duracion_meses;?></td>
                        <td><?=$meses_alerta;?></td>                        
                          <td class="td-actions text-right">
                          <?php
                            if($globalAdmin==1){
                          ?>
                            <a href='<?=$urlFormTiposContratos;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteTiposContrato;?>&codigo=<?=$codigo;?>')">
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
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormTiposContratos;?>&codigo=0'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
</div>
