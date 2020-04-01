<!-- small modal -->
<div class="modal fade modal-primary" id="modalCuentas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">settings_applications</i>
                  </div>
                  <h4 class="card-title">Cuentas Registradas</h4>
                </div>
                <div class="card-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                  <i class="material-icons">close</i>
                </button>
                  <table class="table table-condensed">
                    <thead>
                      <tr class="text-dark bg-plomo">
                      <th>#</th>
                      <th>Nombre</th>
                      <th>Numero</th>  
                      </tr>
                    </thead>
                    <tbody id="tablas_registradas">
                      
                    </tbody>
                  </table>
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->
<div class="modal fade modal-mini modal-primary" id="modalAlert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-small">
    <div id="modalAlertStyle" class="modal-content bg-danger text-white">
      <div class="modal-header">
        <i class="material-icons" data-notify="icon">notifications_active</i>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="material-icons">clear</i></button>
      </div>
      <div class="modal-body">
        <div id="msgError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-white btn-link" data-dismiss="modal">Aceptar
          <div class="ripple-container"></div>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- small modal -->
<div class="modal fade modal-primary" id="modalEstadosCuentas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
      <div class="card-header card-header-danger card-header-icon">
        <div class="card-icon">
          <i class="material-icons text-dark">ballot</i>
        </div>
        <h4 class="card-title">Estados de cuenta</h4>
      </div>
      <div class="card-body">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        <i class="material-icons">close</i>
      </button>
      <!--<input class="form-control" type="text" name="est_codcuenta" id="est_codcuenta"/>
      <input class="form-control" type="text" name="est_codcuentaaux" id="est_codcuentaaux"/>-->
      <input class="form-control" type="hidden" name="estFila" id="estFila"/>
      <!-- <input class="form-control" type="hidden" name="cuentas_formu" id="cuentas_formu"/> -->
      <!-- <script>
        var cod_cuenta_form1=$("#cuenta_auto_id").val();        
        alert(cod_cuenta_form1);
      </script> -->  
      <div class="card-title"><center><h6>Datos de la nueva transaccion</h6></center></div>
        <div class="row">
          <label class="col-sm-2 col-form-label">Monto</label>
          <div class="col-sm-3">
            <div class="form-group">
              <input class="form-control" type="number" step="0.001" readonly name="monto_estadocuenta" id="monto_estadocuenta"/>
            
            </div>
          </div>
        </div>
        <div class="row" id="div_cuentasorigen">
              <label class="col-sm-2 col-form-label">Cuenta Origen</label>
              <div class="col-sm-10">
                <div class="form-group">
                  <!-- <?php
                   // $codigo_cuenta_form="<script> document.write(cod_cuenta_form); </script>";
                  $codigo_cuenta_form="<script> document.write(cod_cuenta_form); </script>";
                   ?> -->
                 <select class="selectpicker form-control form-control-sm" onchange="verEstadosCuentasCred_cc()" name="cuentas_origen" id="cuentas_origen" data-style="<?=$comboColor;?>">
                   <!-- <option disabled selected value=""><?=$codigo_cuenta_form?></option> -->
                   <option disabled selected value="">Seleccione una Cuenta</option>
                   <?php
                   
                   // echo $codigo_cuenta_form;

                    $stmt = $dbh->prepare("SELECT p.* FROM plan_cuentas p, configuracion_estadocuentas c where c.cod_plancuenta=p.codigo and c.tipo=2 and c.cod_cuentaaux=0 order by codigo");
                    $stmt->execute();
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      $codigoX=$row['codigo'];
                      $nombreX=$row['nombre'];
                      $numeroX=$row['numero'];
                      ?>
                      <option value="<?=$codigoX;?>###NNN"><?=trim($numeroX);?> - <?=trim($nombreX);?></option>  
                      <?php
                        }
                        ?>
                      <?php
                    $stmt = $dbh->prepare("SELECT p.* FROM cuentas_auxiliares p, configuracion_estadocuentas c where c.cod_cuentaaux=p.codigo and c.tipo=1 and c.cod_plancuenta=0 order by codigo");
                    $stmt->execute();
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      $codigoX=$row['codigo'];
                      $nombreX=$row['nombre'];
                      $numeroX=$row['nro_cuenta'];
                      ?>
                      <option value="<?=$codigoX;?>###AUX"><?=trim($numeroX);?> - <?=trim($nombreX);?></option>  
                      <?php
                        }
                        ?>  
                   </select>
                </div> 
             </div>      
        </div>
        <br>
        <div class="card-title"><center><h6>Estado de Cuenta</h6> <div id="tituloCuentaModal"></div></center></div>
        <br>
        <div id="div_estadocuentas"></div>
          <div id="mensaje_estadoscuenta"></div>
            <div class="form-group float-right">
              <button type="button" class="btn btn-info btn-round" onclick="agregarEstadoCuenta_cajachica()">Agregar</button>
              <button type="button" class="btn btn-danger btn-round" onclick="quitarEstadoCuenta_cajachica()">Quitar</button>
            </div>
          </div>
        </div>  
  </div>
</div>
<!--    end small modal -->

