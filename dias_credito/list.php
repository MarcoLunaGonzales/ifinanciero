<?php
require_once 'conexion.php';
require_once 'functionsGeneral.php';
require_once 'functions.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$mes=$_SESSION["globalMes"];
$codGestionGlobal=$_SESSION["globalGestion"];
$nombreGestion=$_SESSION['globalNombreGestion'];
$fechaActual=date("Y-m-d");
setlocale(LC_TIME, "Spanish");
$dbh = new Conexion();

// Preparamos
$lista=listaDiasCreditoProveedores();
?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">today</i>
                  </div>
                  <h4 class="card-title"><?=$moduleNameSingular?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive" id="data_comprobantes">
                    <table id="tablePaginator" class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Proveedor</th>
                          <th>Cuenta</th>
                          <th class="bg-warning text-dark">D&iacute;as</th>
                          <th class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
						     $index=1;$cont=0;

                      	while ($row = $lista->fetch(PDO::FETCH_ASSOC)) {
                          $codigo=$row['codigo'];
                          $cuenta="Sin Cuenta";
                          $dias=$row['cantidad_dias'];
                          $proveedor=$row['nombre'];
?>
                        <tr>
                          <td align="center"><?=$index;?></td>                          
                          <td class="text-left font-weight-bold"><?=$proveedor;?></td>
                          <td><?=$cuenta;?></td>
                          <td class="bg-warning text-dark text-right font-weight-bold" style="font-size:20px"><?=$dias;?></td>
                          <td class="td-actions text-right">
                           <a href='<?=$urlEdit;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
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
                    <a href="#" onclick="location.href='<?=$urlRegister2;?>'" class="<?=$buttonNormal;?>">Registrar</a>
                 </div>
                <?php
               }
              ?>
            </div>
          </div>  
        </div>
    </div>
