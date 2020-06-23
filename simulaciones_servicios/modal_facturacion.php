
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
<!-- small modal detalle facturacion-->
<div class="modal fade modal-primary" id="modalDetalleFac" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
      <div class="card-header card-header-warning card-header-icon">
        <div class="card-icon">
          <i class="material-icons">settings_applications</i>
        </div>
        <h4 class="card-title">Detalle Solicitud</h4>
      </div>

      <div class="card-body">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        <i class="material-icons">close</i>
      </button>
      <div class="row" id="div_cabecera" >
            
      </div>
        <table class="table table-condensed">
          <thead>
            <tr class="text-dark bg-plomo">
            <th>#</th>
            <th>Item</th>
            <th>Cantidad</th>
            <!-- <th>Precio(BOB)</th>  
              <th>Desc(%)</th> 
              <th>Desc(BOB)</th>  -->
              <th width="10%">Importe(BOB)</th> 
              <th width="45%">Glosa</th>                   
            </tr>
          </thead>
          <tbody id="tablasA_registradas">
            
          </tbody>
        </table>
      </div>
    </div>  
  </div>
</div>
<!-- FActura manual-->
<div class="modal fade" id="modalFacturaManual" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel"><b>Factura Manual</b></h3>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cod_solicitudfacturacion_factmanual" id="cod_solicitudfacturacion_factmanual" value="0">
        <input type="hidden" name="cod_libreta_manual" id="cod_libreta_manual" value="0" >
        <div class="row">
          <!-- <label class="col-sm-5 text-right col-form-label" style="color:#424242">Importe de Solicitud de Facturacón</label> -->
          <div class="col-sm-12">
            <div class="form-group">
              <input type="text" name="importe_total" id="importe_total" class="form-control text-center" readonly="true" style="background-color:#E3CEF6;text-align: left">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Numero de Factura: </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="number" name="nro_factura" id="nro_factura" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Nro de Autorización: </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="number" name="nro_autorizacion" id="nro_autorizacion" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">        
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Fecha de Factura </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="date" name="fecha_factura" id="fecha_factura" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Nit Cliente </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="number" name="nit_cliente" id="nit_cliente" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Razón Social </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="text" name="razon_social" id="razon_social" class="form-control">
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="guardarFacturaManual" name="guardarFacturaManual">Agregar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- factura pagos -->
<div class="modal fade" id="modalGenerarFacturapagos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-xl" role="document">
    <form id="formFacturaParcial" class="form-horizontal" action="<?=$urlGenerarFacturaParciales;?>" method="post" onsubmit="return valida_modalFacPar(this)" enctype="multipart/form-data">  
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h3 class="modal-title" id="myModalLabel"><b>Generar Factura Parcial</b></h3>
        </div>      
        <div class="modal-body">
          <input type="hidden" name="cod_solicitudfacturacion_factpagos" id="cod_solicitudfacturacion_factpagos" value="0">

          <div class="row" id="contenedor_GenerarFactParcial_cabecera">
          
          </div>
          <div id="contenedor_GenerarFactParcial">
          
          </div>
          <input type="hidden" name="cantidad_items" id="cantidad_items" value="0">
          <input type="hidden" name="cod_libreta_pagos" id="cod_libreta_pagos" value="0" >
        </div>    
        <div class="modal-footer">
          <!-- <button type="button" class="btn btn-success" id="guardarFacturaPagos" name="guardarFacturaPagos"></button> -->
          <button type="submit" class="btn btn-success">Generar Factura</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
        </div>    
      </div>
    </form>
  </div>
</div>
<!-- modal devolver solicitud -->
<div class="modal fade" id="modalDevolverSolicitud" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Rechazar Solicitud</h4>
      </div>
      <div class="modal-body">        
        <input type="hidden" name="cod_solicitudfacturacion" id="cod_solicitudfacturacion" value="0">
        <input type="hidden" name="estado" id="estado" value="0">
        <input type="hidden" name="admin" id="admin" value="0">
        <input type="hidden" name="direccion" id="direccion" value="0">
        
        <input type="hidden" name="q" value="0" id="q"/>
        <input type="hidden" name="s" value="0" id="s"/>
        <input type="hidden" name="v" value="0" id="v"/>
        <input type="hidden" name="u" value="0" id="u"/>
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><small>Nro. Solicitud</small></label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input type="text" class="form-control" name="nro_solicitud" id="nro_solicitud" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><small>Código<br>Servicio</small></label>
          <div class="col-sm-8">
            <div class="form-group" >              
              <input type="text" class="form-control" name="codigo_servicio" id="codigo_servicio" readonly="true" style="background-color:#e2d2e0">
            </div>
          </div>
        </div>                
        <div class="row">
          <label class="col-sm-12 col-form-label" style="color:#7e7e7e"><small>Observaciones</small></label>
        </div>
        <div class="row">
          <div class="col-sm-12" style="background-color:#f9edf7">
            <div class="form-group" >              
              <textarea type="text" name="observaciones" id="observaciones" class="form-control" required="true"></textarea>
            </div>
          </div>
        </div>        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="rechazarSolicitud" name="rechazarSolicitud" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- modal reenviar solicitud devuelto -->
<div class="modal fade" id="modalReenviarSolicitudDevuelto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Reenviar Solicitud</h4>
      </div>
      <div class="modal-body">        
        <input type="hidden" name="cod_solicitudfacturacion_r" id="cod_solicitudfacturacion_r" value="0">
        <input type="hidden" name="estado_r" id="estado_r" value="0">
        <input type="hidden" name="admin_r" id="admin_r" value="0">
        <input type="hidden" name="direccion_r" id="direccion_r" value="0">
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><small>Nro. Solicitud</small></label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input type="text" class="form-control" name="nro_solicitud_r" id="nro_solicitud_r" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><small>Código<br>Servicio</small></label>
          <div class="col-sm-8">
            <div class="form-group" >              
              <input type="text" class="form-control" name="codigo_servicio_r" id="codigo_servicio_r" readonly="true" style="background-color:#e2d2e0">
            </div>
          </div>
        </div>                
        <div class="row">
          <label class="col-sm-12 col-form-label" style="color:#7e7e7e"><small>Observaciones</small></label>
        </div>
        <div class="row">
          <div class="col-sm-12" style="background-color:#f9edf7">
            <div class="form-group" >
              <textarea type="text" name="observaciones_r" id="observaciones_r" class="form-control" required="true"></textarea>
            </div>
          </div>
        </div>        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="ReenviarSolicitud" name="ReenviarSolicitud" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- modal libreta bancaria -->
<div class="modal fade" id="modalListaLibretaBancaria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel"><b>Libreta Bancaria</b></h3>        
      </div>
      <div id="contenedor_cabecera_libreta_bancaria"></div>
      <div class="modal-body">
        <input type="hidden" name="cod_solicitudfacturacion" id="cod_solicitudfacturacion" value="0">
        <input type="hidden" name="direccion" id="direccion" value="0">
        <input type="hidden" name="datos" id="datos" value="0">
        <input type="hidden" name="indice" id="indice" value="0">       
        <div class="row">          
          <div class="table-responsive">
            <?php
              $codigo=0;
              $lista=obtenerObtenerLibretaBancaria($codigo);
              ?>
              <style>
                tfoot input {
                  width: 100%;
                  padding: 3px;
                  
                }
              </style>            
            <table id="libreta_bancaria_reporte_modal" class="table table-condensed table-bordered table-sm small" style="width:100% !important;">
              <thead>
                <tr style="background:#21618C; color:#fff;">
                  <th class="text-center" width="3%">#</th>
                  <th class="small" width="5%"><small>Fecha</small></th>      
                  <th class="small" width="30%"><small>Información Complementaria</small></th>      
                  <th class="small" width="5%"><small>Monto</small></th>
                  <th class="small" width="3%"><small><small>N° Ref</small></small></th>
                  <th class="small bg-success" width="4%"><small>Fecha Fac.</small></th>
                  <th class="small bg-success" width="4%"><small>N° Fac.</small></th>      
                  <th class="small bg-success"><small>Nit Fac.</small></th>
                  <th class="small bg-success"><small>Razón Social Fac.</small></th>
                  <th class="small bg-success" width="7%"><small>Monto Fac.</small></th>
                  <!-- <th class="text-right bg-success" width="3%"></th> -->
                </tr>
              </thead>
              <tbody>
                <?php
                if($lista->estado==1){
                  $j=1;
                    foreach ($lista->libretas as $v) {
                      $Nombre=$v->Nombre;
                      $Banco=$v->Banco;
                      $detalle=$v->detalle;
                      $index=1;?>
                      <tr>
                        <td class="d-none"></td>
                        <td class="d-none"></td>
                        <td class="d-none"></td>
                        <td class="d-none"></td>                        
                        <td class="d-none"></td>
                        <td class="d-none"></td>
                        <td class="d-none"></td>
                        <td class="d-none"></td>
                        <td class="d-none"></td>
                        <!-- <td class="d-none"></td> -->
                        <td align="center" colspan="10" style="background:#e58400; color:#fff;"><button title="Detalles" id="botonLibreta<?=$j?>" style="border:none; background:#e58400; color:#fff;" onclick="activardetalleLibreta(<?=$j?>)"><small><?=$Banco;?> - <?=$Nombre;?></small></button></td>
                      </tr>     
                      <?php
                        foreach ($detalle as $v_detalle) {
                        $CodLibretaDetalle=$v_detalle->CodLibretaDetalle;
                        $Descripcion=$v_detalle->Descripcion;
                        $InformacionComplementaria=$v_detalle->InformacionComplementaria;
                        $Agencia=$v_detalle->Agencia;
                        $NumeroCheque=$v_detalle->NumeroCheque;
                        $NumeroDocumento=$v_detalle->NumeroDocumento;
                        $Fecha=$v_detalle->Fecha;
                        $Hora=$v_detalle->Hora;
                        $FechaHoraCompleta=$v_detalle->FechaHoraCompleta;
                        $monto=$v_detalle->monto;

                        $CodFactura=$v_detalle->CodFactura;
                        $FechaFactura=$v_detalle->FechaFactura;
                        $NumeroFactura=$v_detalle->NumeroFactura;
                        $NitFactura=$v_detalle->NitFactura;
                        $RSFactura=$v_detalle->RSFactura;
                        $MontoFactura=$v_detalle->MontoFactura; ?>
                        <tr>
                          <td style="" class="libretaDetalles_<?=$j?> small" align="center"><?=$index;?></td>
                          <td style="" class="libretaDetalles_<?=$j?> text-center small"><span style="padding:0px;border: 0px;"><?=strftime('%d/%m/%Y',strtotime($FechaHoraCompleta))?><br><?=strftime('%H:%M:%S',strtotime($FechaHoraCompleta))?></span></td>           
                          <td style="" class="libretaDetalles_<?=$j?> text-left "><small><small><?=$InformacionComplementaria?></small></small></td>
                          <td style="" class="libretaDetalles_<?=$j?> text-right small"><?=number_format($monto,2)?></td>
                          <td style="" class="libretaDetalles_<?=$j?> text-left small"><?=$NumeroDocumento?></td>
                          <td style=" color: #ff0000;" class="libretaDetalles_<?=$j?> text-center small"><?=$FechaFactura?></td>
                          <td style=" color: #ff0000;" class="libretaDetalles_<?=$j?> text-right small"><?=$NumeroFactura?></td>            
                          <td style=" color: #ff0000;" class="libretaDetalles_<?=$j?> text-right small"><?=$NitFactura?></td>
                          <td style=" color: #ff0000;" class="libretaDetalles_<?=$j?> text-left"><small><small><?=$RSFactura?></small></small></td>
                          <td style=" color: #ff0000;" class="libretaDetalles_<?=$j?> text-right small"><?=$MontoFactura?> <?php
                            if($CodFactura==null || $CodFactura==''){?>
                              <a href="#" style="padding: 0;font-size:10px;width:25px;height:25px;" onclick="seleccionar_libretaBancaria(<?=$CodLibretaDetalle?>)" class="btn btn-fab btn-success btn-sm" title="Seleccionar Item"><i class="material-icons">done</i></a>
                            <?php }?></td>
                          <!-- <td style=" color: #ff0000;" class="libretaDetalles_<?=$j?> td-actions text-right small">
                            
                          </td> -->
                        </tr>
                      <?php
                      $index++;
                      }
                      $j++;
                  }
                }
                ?>
              </tbody>
              <tfoot>
                <tr style="background:#21618C; color:#fff;">
                  <th class="text-center" width="3%">#</th>
                  <th class="small" width="5%"><small>Fecha</small></th>      
                  <th class="small" width="30%"><small>Información Complementaria</small></th>      
                  <th class="small" width="5%"><small>Monto</small></th>
                  <th class="small" width="3%"><small><small>N° Ref</small></small></th>
                  <th class="small bg-success" width="4%"><small>Fecha<br>Fac.</small></th>
                  <th class="small bg-success" width="4%"><small>N° Fac.</small></th>      
                  <th class="small bg-success"><small>Nit Fac.</small></th>
                  <th class="small bg-success"><small>Razón Social Fac.</small></th>
                  <th class="small bg-success" width="7%"><small>Monto Fac.</small></th>
                  <!-- <th class="text-right bg-success" width="3%"></th> -->
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-success" id="libreta_bancaria" name="libreta_bancaria">Agregar</button> -->
        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button> -->
      </div>
    </div>
  </div>
</div>

<!-- bUSCADOR AVANZADO LIBRETA BANCARIA -->
<div class="modal fade" id="modalBuscadorLibretaBancaria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Buscador de Detalle Libreta Bancaria</h4>
      </div>
      <div class="modal-body ">
        <div class="row">          
          <label class="col-sm-5 col-form-label text-center">Información</label>
          <label class="col-sm-2 col-form-label text-center">Monto</label>
          <label class="col-sm-5 col-form-label text-center">Razón Social</label>
        </div> 
        <div class="row">                    
          <div class="form-group col-sm-5">
            <input class="form-control input-sm" type="text" name="informacion_libreta" id="informacion_libreta">
          </div>           
          <div class="form-group col-sm-2">
            <input class="form-control input-sm" type="text" name="monto_libreta" id="monto_libreta"  >
          </div>           
          <div class="form-group col-sm-5">
            <input class="form-control input-sm" type="text" name="razon_social_libreta" id="razon_social_libreta"  >
          </div>           
        </div> 

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="botonBuscarLibretaBancariaDetalle" name="botonBuscarLibretaBancariaDetalle" onclick="botonBuscarLibretaBancariaDetalle()">Buscar</button>
        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal"> Cerrar </button> -->
      </div>
    </div>
  </div>
</div>
<!-- carga datos -->
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>