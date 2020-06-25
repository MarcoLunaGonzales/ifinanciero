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
$lista=listaInstanciasEnvio();
?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">mail</i>
                  </div>
                  <h4 class="card-title"><?=$moduleNameSingular?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive" id="data_comprobantes">
                    <table id="tablePaginator" class="table table-condensed">
                      <thead>
                        <tr style="background:#21618C; color:#fff;">
                          <td class="text-center">#</td>
                          <td>Instancia Envios</td>
                          <td>Personal</td>
                          <td>Correo</td>
                          <!--<td>Correo Alternativo</td>-->
                          <td class="text-right">Actions</td>
                        </tr>
                      </thead>
                      <tbody>
<?php
						     $index=1;$cont=0;

                      	while ($row = $lista->fetch(PDO::FETCH_ASSOC)) {
                          $codigo=$row['codigo'];
                          $correo=$row['email'];
                          $correoAlternativo=$row['correo_alternativo'];
                          $personal=$row['personal'];
                          $instancia=$row['instancia'];
?>
                        <tr>
                          <td align="center"><?=$index;?></td>                          
                          <td class="text-left font-weight-bold"><?=$instancia;?> (CC)</td>
                          <td class="text-left"><?=$personal;?></td>
                          <td><?=$correo;?></td>
                          <!--<td><?=$correoAlternativo;?></td>-->
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
