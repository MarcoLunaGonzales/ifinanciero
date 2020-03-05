<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$globalAdmin = $_SESSION["globalAdmin"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT * from distribucion_gastosporcentaje where cod_estadoreferencial=1");
$stmt->execute();          
$stmt->bindColumn('codigo', $codigo_distribucion);
$stmt->bindColumn('nombre', $nombre_distribucion);
$stmt->bindColumn('estado', $estado_distribucion);

?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?= $colorCard; ?> card-header-icon">
            <div class="card-icon">
              <i class="material-icons"><?= $iconCard; ?></i>
            </div>
            <h4 class="card-title"><?= $moduleNamePlural ?></h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-condensed">
                <thead>
                  <tr>
                    <th class="text-left">#</th>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Estado</th>
                    <th>Actions</th>
                    <!-- <th class="text-center">Abreviatura</th>      -->               
                    <!--th class="text-right">Actions</th-->
                  </tr>
                </thead>
                <tbody>
                  <?php
                     $index = 1;
                     $sum=0;
                      while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                        if($estado_distribucion==1){
                          $label='<span class="badge badge-success">Activo</span>';
                        }else
                          $label='<span class="badge badge-danger">Inactivo</span>';
                  ?>
                    <tr>
                      <td class="text-center"><?= $index; ?></td>
                      <td class="text-left"><?= $nombre_distribucion; ?></td>
                      <td class="text-left"><?= $label; ?></td>                                    
                      <td class="td-actions text-right">
                        <?php
                        if ($globalAdmin == 1) {
                        ?>                         
                          <a href='<?= $urlDistribucionGastosDetalle; ?>&codigo=<?=$codigo_distribucion?>' rel="tooltip" class="btn btn-primary">
                            <i class="material-icons" title="Detalle">playlist_add</i>
                          </a>
                          <a href='<?=$urlRegisterDistribucionGastos; ?>&codigo=<?=$codigo_distribucion?>' rel="tooltip" class="<?= $buttonEdit; ?>">
                            <i class="material-icons"><?= $iconEdit; ?></i>
                          </a>
                          <button rel="tooltip" class="<?= $buttonDelete; ?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?= $urlDeleteDistribucion; ?>&codigo=<?=$codigo_distribucion?>')">
                            <i class="material-icons"><?= $iconDelete; ?></i>
                          </button>
                          <button rel="tooltip" class="btn btn-warning" onclick="alerts.showSwal('warning-message-and-confirmation-cambiar-estado','<?= $urlCambiarEstado; ?>&codigo=<?=$codigo_distribucion?>')">
                            <i class="material-icons" title="Activar Distribución">settings_ethernet</i>

                          </button>                          

                        <?php
        }
                        ?>
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
            <!--button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegister;?>'">Registrar</button-->
            <!-- <button class="<?= $buttonCeleste; ?>" onClick="location.href='<?= $urlEditarDistribucionGastos; ?>'">Editar Porcentajes en Grupo</button> -->
            <button class="btn btn-success" onClick="location.href='<?=$urlRegisterDistribucionGastos; ?>&codigo=0'">Registrar</button>

           

          </div>
        <?php
        }
        ?>


      </div>
    </div>
  </div>
</div>



