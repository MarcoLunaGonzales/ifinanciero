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
$lista=listaLibretasBancarias();
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
                          <th>Libreta</th>
                          <th>Banco</th>
                          <th>Cuenta</th>
                          <th>Contra Cuenta</th>
                          <th class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
						     $index=1;$cont=0;

                      	while ($row = $lista->fetch(PDO::FETCH_ASSOC)) {
                          $codigo=$row['codigo'];
                          $nombre=$row['nombre'];
                          $banco=$row['banco'];
                          $cuenta=$row['nro_cuenta'];
                          $cod_cuenta=$row['cod_cuenta'];
                          $cod_contraCuenta=$row['cod_contracuenta'];
                          $nombreCuenta=obtieneNumeroCuenta($cod_cuenta)." ".nameCuenta($cod_cuenta);
                          $ContraCuenta=obtieneNumeroCuenta($cod_contraCuenta)." ".nameCuenta($cod_contraCuenta);
?>
                        <tr>
                          <td align="center text-left"><?=$index;?></td>                          
                          <td class="text-left font-weight-bold"><?=$nombre;?></td>
                          <td class="text-left"><?=$banco;?></td>
                          <td class="small text-left"><small><?=$nombreCuenta;?></small></td>
                          <td class="small text-left"><small><?=$ContraCuenta;?></small></td>
                      
                          <td class="td-actions text-right">
                            <a href='<?=$urlList2;?>&codigo=<?=$codigo;?>' class="<?=$buttonDetailMin;?>">
                              <i class="material-icons" title="Detalle">playlist_add</i>
                            </a>
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
