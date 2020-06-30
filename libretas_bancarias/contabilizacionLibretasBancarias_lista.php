<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT *,date_format(fecha,'%d/%m/%Y') as fecha_x from depositos_no_facturados where cod_estadoreferencial=1");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('cod_libretabancaria', $cod_libretabancaria);
$stmt->bindColumn('cod_gestion', $cod_gestion);
$stmt->bindColumn('cod_mes', $cod_mes);
$stmt->bindColumn('fecha_x', $fecha);
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
                  <h4 class="card-title">Listado de Depósitos No Facturados</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">
                    <thead>
                      <tr>
                          <th></th>
                          <th>Libreta</th>
                          <th>Gestión</th>
                          <th>Mes</th>
                          <th>Fecha</th>
                          <th></th>
                      </tr>
                  </thead>
                  <tbody>
                  <?php $index=1;
                  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
                      <tr>
                          <td  class="td-actions text-right"><?=$index?></td>
                          <td><?=obtenerNombreDepositoNoFacturado($cod_libretabancaria);?></td>
                          <td><?=nameGestion($cod_gestion);?></td>
                          <td><?=nombreMes($cod_mes);?></td>
                          <td><?=$fecha;?></td>
                          <td class="td-actions text-right">
                            <a href='<?=$urlList_2detalle;?>&codigo=<?=$codigo;?>' title="Detalles" class="<?=$buttonEdit;?>">
                              <i class="material-icons">list</i>
                            </a>

                          <?php
                            if($globalAdmin==1){
                          ?>
                            <!-- <a href='<?=$urlEdit6;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete2;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button> -->
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
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlList3?>&codigo=0'">Registrar</button>     
              </div>
              <?php
              }
              ?>
      
            </div>
          </div>  
        </div>
    </div>
