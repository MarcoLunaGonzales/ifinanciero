<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$sql="SELECT * from areas where areas_ingreso=1";
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('abreviatura', $abreviatura);
$stmt->bindColumn('cod_cuenta_ingreso', $cod_cuenta);

// //plan de cuentas
// $query_cuentas = "SELECT codigo,numero,nombre from plan_cuentas where cod_estadoreferencial=1";
// $statementCuentas = $dbh->query($query_cuentas);

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
            <h4 class="card-title">Plan De Cuentas para Areas</h4>            
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <div class="" id="data_activosFijos">
                <table class="table table-condensed" id="tablePaginatorHead">
                  <thead>
                    <tr>                      
                      <th>Codigo</th>
                      <th>Nombre</th>
                      <th>Abrev</th>
                      <th>Cuenta Contable</th>                      
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $index=1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                      $datos=$codigo."/".$nombre."/".$cod_cuenta;
                      $nombre_cuenta=nameCuenta($cod_cuenta);
                      $numero_cuenta=obtieneNumeroCuenta($cod_cuenta);

                     ?>
                      <tr>                        
                          <td class="text-center small"><?=$codigo;?></td>
                          <td class="text-left small"><?=$nombre;?></td>
                          <td class="text-center small"><?=$abreviatura;?></td>
                          <td class="text-left small"><?=$numero_cuenta;?> - <?=$nombre_cuenta;?></td>                          
                          <td class="td-actions text-right">
                          <?php
                            if($globalAdmin==1){
                          ?>

                            <button type="button" class="btn btn-warning btn-round btn-fab btn-sm" data-toggle="modal" data-target="#modalAgregarC" onclick="agregarDatosModalCuenta_areas('<?=$datos;?>')">
                                <i class="material-icons" title="Asociar Cuenta Contable">add</i>
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
        </div>
        
        <div class="card-footer fixed-bottom">
          <a href="index.php?opcion=listPlanCuentas" class="<?=$buttonCancel;?>"> <-- Volver </a>
        </div>
        
      </div>
    </div>  
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalAgregarC" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Asociar Cuenta Contable</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cod_area" id="cod_area" value="0">   

        <div class="row">
          <label class="col-sm-2 col-form-label" style="color:#424242"> Tipo de Pago: </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input class="form-control" type="text" name="tipo_pago" id="tipo_pago"  readonly="true" style="background-color:#E3CEF6;text-align: left"/>
            </div>
          </div>
        </div>

        <div class="row">
          <label class="col-sm-2 col-form-label" style="color:#424242">Cuenta Asociada:</label>
          <div class="col-sm-8">
            <div class="form-group" id="div_cuenta_contable_sol_fac_areas">               
            </div>
          </div>
        </div>        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="registrarCuentaAsociadaAreas" name="registrarCuentaAsociadaAreas" data-dismiss="modal">Agregar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#registrarCuentaAsociadaAreas').click(function(){    
      cod_area=document.getElementById("cod_area").value;
      cod_cuenta=$('#cod_cuenta').val();      
      registrarCuentaAsociadaSOLFAC_areas(cod_area,cod_cuenta);
    });    

  });
</script>