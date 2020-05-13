
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


<!-- modal tipo pago porcentaje -->
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