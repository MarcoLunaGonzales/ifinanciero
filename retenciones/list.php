<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT * from configuracion_retenciones where cod_estadoreferencial=1 order BY codigo");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('porcentaje_cuentaorigen', $porcentaje);
$stmt->bindColumn('cod_estadoreferencial', $estado);
?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">build</i>
                  </div>
                  <h4 class="card-title"><b><?=$moduleNamePlural?></b></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="tablePaginator" class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Nombre</th>
                          <th>Estado</th>
                          <th>Porcentaje Origen</th>
                          <th class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
						$index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                       if($estado==1){
                       $estadoDes="Habilitado";
                       }else{
                        $estadoDes="Deshabilitado";
                       }
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td><?=$nombre;?></td>
                          <td>
                                 <img src="assets/img/logoibnorca.png" width="20" height="20"/> <?=$estadoDes;?>
                          </td>
                          <td><?=$porcentaje;?> %</td>
                          <td class="td-actions text-right">
                            <a href='<?=$urlEdit;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <a href='<?=$urlRegister;?>?cod=<?=$codigo;?>' rel="tooltip" class="btn btn-info">
                              <i class="material-icons">list</i>
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
      				<div class="card-footer fixed-bottom">
                <a href="#" onclick="location.href='<?=$urlRegister2;?>'" class="<?=$buttonNormal;?>">Registrar</a>
              </div>		  
            </div>
          </div>  
        </div>
    </div>