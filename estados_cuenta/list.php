<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

//echo dirname(__DIR__)."/".basename(__DIR__);
// Preparamos
$sql="SELECT c.*,p.numero,p.nombre, (select tec.codigo from tipos_estado_cuenta tec where tec.codigo=c.cod_tipoestadocuenta)as codtipoestadocuenta, (select tec.nombre from tipos_estado_cuenta tec where tec.codigo=c.cod_tipoestadocuenta)as tipoestadocuenta from configuracion_estadocuentas c,plan_cuentas p where c.cod_plancuenta=p.codigo order by p.nombre";

//echo $sql;

$stmt = $dbh->prepare($sql);
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('cod_plancuenta', $codCuenta);
$stmt->bindColumn('cod_cuentaaux', $codCuentaAux);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('numero', $numero);
$stmt->bindColumn('tipo', $tipo);
$stmt->bindColumn('codtipoestadocuenta', $codTipoEstadoCuenta);
$stmt->bindColumn('tipoestadocuenta', $tipoEstadoCuenta);

?>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
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
                          <th class="text-left">Cuenta</th>
                          <th class="text-left">Tipo Estado Cuenta</th>
                          <th>Acumular En</th>
                          <th class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
						$index=1;
                      while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                       if($tipo==1){
                       $tipoDes="Debe";
                       }else{
                        $tipoDes="Haber";
                       }
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td class="text-left">[<?=$numero;?>] - <?=$nombre;?></td>
                          <td>
                            <?=$tipoEstadoCuenta;?>
                          </td>                 
                          <td>
                            <img src="assets/img/logoibnorca.png" width="20" height="20"/> <?=$tipoDes;?>
                          </td>
                          <td class="td-actions text-right">
                            <a title="Detalle" href='#' onclick="verEstadosCuentasModal('<?=$nombre?>',<?=$codCuenta?>,<?=$codCuentaAux?>,<?=$tipo?>,<?=$codTipoEstadoCuenta?>)" class="btn btn-default">
                             <i class="material-icons">ballot</i>
                            </a>
                            <a href='<?=$urlEdit;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <button title="Eliminar Cuenta" class="btn btn-danger" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
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

    <!-- small modal -->
<div class="modal fade modal-primary" id="modalCuentas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
                <div class="card-header card-header-danger card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons text-dark">ballot</i>
                  </div>
                  <h4 class="card-title">Estado de Cuentas <small id="titulo_cuenta" class="text-danger"></small></h4>
                </div>
                <div class="card-body">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                  <i class="material-icons">close</i>
                </button>
                  <div id="div_estadocuentas">
                  </div>
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->