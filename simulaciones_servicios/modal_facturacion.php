
<!-- modal tipo pago porcentaje -->
<div class="modal fade" id="modalTipoPagoPorcentaje" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel"><b>Porcentaje de Distribución del Ingreso por Tipo de Pago</b></h3>
      </div>
      <div class="modal-body">
        <!-- <input type="hidden" name="cod_tipopago" id="cod_tipopago" value="0">    -->
        <div class="row" id="div_cabecera_hidden_tipo_pago">
          
        </div>
        <div class="row">          
          <div class="col-sm-12" id="divResultadoListaModalTiposPago">
            
          </div>
          <input type="hidden" id="total_items_tipopago" name="total_items_tipopago">
            
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="registrarCuentaAsociada" name="registrarCuentaAsociada" onclick="savePorcentajeTipopago()">Agregar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- modal area porcentaje -->
<div class="modal fade" id="modalAreasPorcentaje" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel"><b>Porcentaje de Distribución del Ingreso por Area</b></h3>
      </div>
      <div class="modal-body">
        <div class="row" id="div_cabecera_hidden_areas">
          
        </div>
        <div class="row">          
            <div class="col-sm-12" id="divResultadoListaModalAreas">
            
            </div>                      
            <input type="hidden" id="total_items_areas" name="total_items_areas" >
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="registrarCuentaAsociada" name="registrarCuentaAsociada" onclick="savePorcentajeAreas()">Agregar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- modal unidad porcentaje -->
<div class="modal fade" id="modalUnidadesPorcentaje" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <!-- <h3 class="modal-title" id="myModalLabel"><b>Porcentaje de Distribución del Ingreso por Oficina</b></h3> -->
        <div id="div_cabecera_hidden_unidad">
          
        </div>
      </div>
      <div class="modal-body">
        <div class="row">          
            <input type="hidden" name="id_area" id="id_area">
            <div class="col-sm-12" id="divResultadoListaModalUnidades">
            
            </div>                      
            <input type="hidden" id="total_items_unidades" name="total_items_unidades" >
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="registrarCuentaAsociada" name="registrarCuentaAsociada" onclick="savePorcentajeUnidades()">Agregar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- alert para validaciones en facturacion -->
<div class="modal fade modal-mini modal-primary" id="modalAlert" style="z-index:99999" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
<!-- modal para bancarizacion -->

<!-- modal tipo pago porcentaje -->
<div class="modal fade" id="modalBancarizacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel"><b>Formulario de Bancarización</b></h3>
      </div>
      <div class="modal-body">
        <!-- <input type="hidden" name="cod_tipopago" id="cod_tipopago" value="0">    -->        
        <div class="row">
          <label class="col-sm-4 col-form-label" style="color:#424242">Nro de Contrato: </label>
          <div class="col-sm-7">
            <div class="form-group">
              <input type="number" name="nro_contrato_modal" id="nro_contrato_modal" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label" style="color:#424242">No de Cuenta del Documento de Pago: </label>
          <div class="col-sm-7">
            <div class="form-group">
              <input type="number" name="nro_cuenta_doc_modal" id="nro_cuenta_doc_modal" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label" style="color:#424242">NIT Entidad Financiera: </label>
          <div class="col-sm-7">
            <div class="form-group">
              <input type="number" name="nit_entidad_financiera_modal" id="nit_entidad_financiera_modal" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label" style="color:#424242">Nro de Operación o Transacción: </label>
          <div class="col-sm-7">
            <div class="form-group">
              <input type="number" name="nro_transaccion_modal" id="nro_transaccion_modal" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label" style="color:#424242">Tipo de Doc. de Pago: </label>
          <div class="col-sm-7">
            <div class="form-group">
              <select name="tipo_doc_pago_modal" id="tipo_doc_pago_modal" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true">
                <option value="0"></option>
                <option value="1">1 - Cheque de Cualquier Naturaleza</option>  
                <option value="2">2 - Orden de Transferencia</option>  
                <option value="3">3 - Ordenes de Transf Electrónica de Fondos</option>  
                <option value="4">4 - Transferencia de Fondos</option>  
                <option value="5">5 - Tarjeta de Débito</option>  
                <option value="6">6 - Tarjeta de Crédito</option>  
                <option value="7">7 - Tarjeta Prepagada</option>  
                <option value="8">8 - Déposito en Cuentas</option>  
                <option value="9">9 - Cartas de crédito</option>  
                <option value="10">10 -Otros</option>  
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label" style="color:#424242">Fecha del Doc, de Pago: </label>
          <div class="col-sm-7">
            <div class="form-group">
              <input type="date" name="fecha_doc_pago_modal" id="fecha_doc_pago_modal" class="form-control">
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="registrarCuentaAsociada" name="registrarCuentaAsociada" onclick="saveDatosBancarizacion()">Agregar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
      </div>
    </div>
  </div>
</div>
