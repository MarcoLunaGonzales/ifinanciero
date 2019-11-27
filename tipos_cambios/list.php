<?php
require_once 'conexion.php';
require_once 'functionsGeneral.php';
require_once 'functions.php';
require_once 'tipos_cambios/configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];

$fechaActual=date("Y-m-d");
$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT codigo,nombre,abreviatura,cod_estadoreferencial from monedas");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('cod_estadoreferencial', $codEstadoRef);
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('abreviatura', $abreviatura);
$stmt->bindColumn('nombre', $nombre);
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
                  <h4 class="card-title"><?=$moduleNameSingular?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive" id="data_comprobantes">
                    <table class="table">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Nombre</th>
                          <th>Abrev</th>
                          <th>Tipo de Cambio (Hoy)</th>
                          <th>Estado</th>
                          <th class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
						$index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                        if($codigo!=1){
                          $valorTipo=obtenerValorTipoCambio($codigo,$fechaActual);
                          if($valorTipo==0){
                           $valor="Sin registro actual"; 
                           $estiloTipo="text-danger";
                          }else{
                            $valor=$valorTipo;
                            $estiloTipo="text-success";
                          }
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td><?=$nombre;?></td>
                          <td><?=$abreviatura;?></td>
                          <td class="<?=$estiloTipo?>"><h3><b><?=$valor;?></b></h3></td>
                          <td><?=$codEstadoRef;?></td>
                          <td class="td-actions text-right">
                            <a href='<?=$urlEdit;?>?codigo=<?=$codigo;?>' title="editar" target="_blank" rel="" class="btn btn-info btn-link">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <a href='<?=$urlEdit2;?>?codigo=<?=$codigo;?>' title="historial" target="_blank" rel="" class="btn btn-info btn-link">
                              <i class="material-icons">history</i>
                            </a>
                            <button rel="" title="borrar" class="<?=$buttonDelete;?> btn-link" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button>
                          </td>
                        </tr>
<?php
							$index++;
						}
          }
?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
      				<!--<div class="card-footer fixed-bottom">
                <a href="#" onclick="javascript:window.open('<?=$urlRegister;?>')" class="<?=$buttonNormal;?>">Registrar</a>
              </div>-->		  
            </div>
          </div>  
        </div>
    </div>
