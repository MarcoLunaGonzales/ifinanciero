<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
?>
<!-- modal tipo pago porcentaje -->
<div class="modal fade" id="modalTipoPagoPorcentaje" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel"><b>Porcentaje de Distribución del Ingreso por Forma de Pago</b></h3>
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
<div class="modal fade" id="modalAreasPorcentaje" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="position: absolute;top: 5%; margin-top: -150px;">
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
<div class="modal fade" id="modalUnidadesPorcentaje" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
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
        <input type="hidden" name="cod_estadocuenta_manual" id="cod_estadocuenta_manual" value="0" >
        <input type="hidden" name="cuenta_auxiliar_manual" id="cuenta_auxiliar_manual" value="0" >
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
        <input type="hidden" name="codigo_factura" id="codigo_factura" value="0">
        <input type="hidden" name="codigo_comprobante" id="codigo_comprobante" value="0">
        
        <input type="hidden" name="q" value="0" id="q"/>
        <input type="hidden" name="s" value="0" id="s"/>
        <input type="hidden" name="v" value="0" id="v"/>
        <input type="hidden" name="u" value="0" id="u"/>
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id="campo_nro_fact"><small>Nro.<br>Solicitud.</small></span></label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input type="text" class="form-control" name="nro_solicitud" id="nro_solicitud" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id="campo_rs_fact"><small >Código<br>Servicio</small></span></label>
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
<div class="modal fade modal-arriba" id="modalListaLibretaBancaria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-primary card-header-text">
        <div class="card-text">
          <h4>Libreta Bancaria <div id="contenedor_cabecera_libreta_bancaria"></div></h4>      
        </div>
        
        <button title="Cerrar" type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
        
        <a href="#" id="boton_libreta_detalle_facturas" title="Lista Libretas Detalle" onclick="mostrar_listado_facturas()" class="btn btn-primary btn-sm btn-fab float-right">
          <i class="material-icons">L</i><span id="nfacturaslibretas" class="count bg-warning">0</span>
        </a>
        <!--<button id="boton_libreta_detalle_todo" title="Listar todo" onclick="ajax_listado_libreta_bancaria_filtrar()" type="button" class="btn btn-warning btn-sm btn-fab float-right" >
          <i class="material-icons">list</i>
        </button>-->
        <label for="" class="float-right">|</label>
        <a title="Buscar Libretas" href="#" onclick="buscarLibretasBancariasServicioWeb()" class="btn btn-info btn-sm btn-fab float-right">
          <i class="material-icons">search</i>
        </a>
        <label for="" class="float-right">|</label>
        <select class="selectpicker form-control form-control-sm col-sm-1 float-right" name="modal_anio_actual" id="modal_anio_actual" data-style="btn btn-default text-dark" onchange="cambiarValorAnioFechaBuscar()">
          <option disabled value="">--Filtrar Año--</option>
                        <option value="0">Todo</option>
                        <?php 
                        for ($ann=(int)$_SESSION['globalNombreGestion']; $ann >=((int)$_SESSION['globalNombreGestion']-3); $ann--) { 
                          if($ann==(int)$_SESSION['globalNombreGestion']){
                           ?><option value="<?=$ann?>" selected><?=$ann?></option>
                           <?php 
                         }else{
                          ?><option value="<?=$ann?>"><?=$ann?></option>
                           <?php
                         }
                        }
                        ?>
                        
                  </select>
                <select class="selectpicker form-control form-control-sm col-sm-3 float-right" data-size="6" data-live-search="true" name="modal_libretas_select" id="modal_libretas_select" data-style="fondo-boton fondo-boton-active">
                        <option disabled selected="selected" value="">--Filtrar Libreta--</option>
                        <option value="0">Todos</option>
                        <?php 
                        //LIBRETAS BANCARIAS DETALLE CARGAR
                        $stmt = $dbh->prepare("SELECT p.nombre as banco,dc.* FROM libretas_bancarias dc join bancos p on dc.cod_banco=p.codigo WHERE dc.cod_estadoreferencial=1");
                        $stmt->execute();
                        $i=0;
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          $codigoX=$row['codigo'];
                          $bancoX=$row['banco'];
                          $cod_banco=$row['cod_banco'];
                          $cod_cuenta=$row['cod_cuenta'];
                          $cod_contracuenta=$row['cod_contracuenta'];
                          $nombreX=$row['nombre'];
                          $nombreBan=nameBancos($cod_banco);
                        if($nombreBan==""){
                          $nombreBan=$Banco." - ".$nombreX;
                        }else{
                          $nombreBan=$nombreBan." - ".$nombreX;  
                        }
                      ?><option value="<?=$codigoX?>"><?=$nombreBan?></option><?php
    
                      }
                      ?>
                  </select>
               <input type="date" id="modal_buscar_fecha" min="<?=(int)$_SESSION['globalNombreGestion']?>-01-01" max="<?=(int)$_SESSION['globalNombreGestion']?>-12-31" class="form-control col-sm-2 float-right text-right" value=""/>
               
               <input type="text" id="modal_buscar_nombre" class="form-control row col-sm-5 float-right" style="margin-top:-30px !important;" value="" placeholder="Descripción"/>
               <input type="text" id="modal_buscar_monto"  class="form-control row col-sm-2 float-right" style="margin-top:-30px !important;" value="" placeholder="Monto"/>                     
      </div>
      <div class="card-body">
        <input type="hidden" name="cod_solicitudfacturacion" id="cod_solicitudfacturacion" value="0">
        <input type="hidden" name="direccion" id="direccion" value="0">
        <input type="hidden" name="datos" id="datos" value="0">
        <input type="hidden" name="indice" id="indice" value="0">
        <input type="hidden" name="saldo_x" id="saldo_x" value="0">
        <div class="row">

        <div class="">
          <!--<a href="#" class="btn btn-sm fila-button" onclick="ajax_contenedor_tabla_libretaBancariaIndividual(0)">Todas</a>-->
          <?php 
           
            /*$lista=obtenerObtenerLibretaBancaria();
            foreach ($lista->libretas as $v) {
              $CodLibreta=$v->CodLibreta;
              $Nombre=$v->Nombre;
              $Banco=$v->Banco;
              $nombreBan=nameBancos($v->CodBanco);
              if($nombreBan==""){
                $nombreBan=$Nombre;
              }else{
                $nombreBan=$Nombre;  
              }
              
            }*/
          ?>
        </div>          
            <div class="table-responsive" id="contenedor_tabla_libreta_bancaria">
              
            </div>          
        </div>
      </div>
      <div class="modal-footer" id="modal_descripcion_pie">
         <span style="color:  #e59866 ;"><i class="material-icons">check_box</i> Registros Contabilizados</span><br>
         <span style="color:  #85929e;"><i class="material-icons">check_box</i> Registros No Contabilizados</span><br>
      </div>
    </div>
  </div>
</div>

<!-- modal envio de facturas -->
<div class="modal fade" id="modalListaLibretasBancariasDetalle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="background-color:#e2e6e7">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Relacionar Libreta Bancaria - Factura</h4>
      </div>
      <div class="modal-body">
         <table class="table table-condensed table-bordered small">
          <thead>
           <tr class="bg-primary text-white">
             <td>FECHA</td>
             <td>DESCRIPCION</td>
             <td>MONTO</td>
             <td>SALDO</td>
             <td>QUITAR</td>
           </tr>
           </thead>
           <tbody id="datos_libreta_bancaria_detalle">
             
           </tbody>
           <tfoot >
            <tr style="background-color:#768c9c; color: #FFFFFF">
              <td>-</td>
              <td colspan="2">TOTAL SALDO</td>              
              <td><input style="background-color:#768c9c; color: #FFFFFF" type="text" name="total_saldo_lib" id="total_saldo_lib" value="0" readonly="true"></td>
              <td>-</td>
            </tr>
           </tfoot>
           <!--<tfoot>
             <tr class="font-weight-bold">
             <td colspan="2">Total</td>
             <td id="totales_monto_libreta">0</td>
             <td id="totales_saldo_libreta">0</td>
             <td></td>
           </tr>
           </tfoot>-->
         </table>
         <input type="hidden" id="cantidad_filas_libretas" value="0">
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-success" onclick="facturarLibretaBancaria()">FACTURAR</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Adicionar + </button>
      </div>
    </div>
  </div>
</div>


<!-- carga datos -->
<style>
  #libreta_bancaria_reporte_modal_filter{
         display: none !important;
       }      
</style>

<div class="modal fade" id="modalDetalleFacturaManual" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel"><b>Detalle Factura Manual</b></h3>
      </div>
      <div class="modal-body">        
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Cliente</label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="text" name="cliente_facmanual" id="cliente_facmanual" readonly="true" style="background-color:#D8CEF6;" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Numero de Factura: </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="text" name="nro_factura_facmanual" id="nro_factura_facmanual" readonly="true" style="background-color:#D8CEF6;" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Nro de Autorización: </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="text" name="nro_autorizacion_facmanual" id="nro_autorizacion_facmanual" readonly="true" style="background-color:#D8CEF6;" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Nit Cliente </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="number" name="nit_cliente_facmanual" id="nit_cliente_facmanual" readonly="true" style="background-color:#D8CEF6;" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Razón Social </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="text" name="razon_social_facmanual" id="razon_social_facmanual" readonly="true" style="background-color:#D8CEF6;" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Importe</label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="text" name="importe_facmanual" id="importe_facmanual" readonly="true" style="background-color:#D8CEF6;" class="form-control">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-success" id="guardarFacturaManual" name="guardarFacturaManual">Agregar</button> -->
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modal-arriba" id="modal_estadocuenta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-primary card-header-text">
        <div class="card-text">
          <h4>Estados de Cuenta<div id="contenedor_cabecera_estados_cuenta"></div></h4>      
        </div>
        
        <button title="Cerrar" type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
        
        <a href="#" id="boton_libreta_detalle_facturas" title="Estados de Cuenta Seleccionados" onclick="mostrar_listado_facturas_ec()" class="btn btn-primary btn-sm btn-fab float-right">
          <i class="material-icons">L</i><span id="nfacturasEstadosCuenta" class="count bg-warning">0</span>
        </a>

        <!-- <button title="Listar todo" onclick="ajax_listado_libreta_bancaria_filtrar()" type="button" class="btn btn-warning btn-sm btn-fab float-right" >
          <i class="material-icons">list</i>
        </button> -->
      </div>
      <div class="card-body">
        <input type="hidden" name="cod_solicitudfacturacion_ec" id="cod_solicitudfacturacion_ec" value="0">
        <input type="hidden" name="direccion_ec" id="direccion_ec" value="0">
        <input type="hidden" name="datos_ec" id="datos_ec" value="0">
        <input type="hidden" name="indice_ec" id="indice_ec" value="0">
        <input type="hidden" name="saldo_ec" id="saldo_ec" value="0">
        <input type="hidden" name="cod_libreta_ec" id="cod_libreta_ec" value="0">
        <div class="row">
          <div class="table-responsive" id="contenedor_tabla_estados_cuenta">
            
          </div>          
        </div>
      </div>
      <div class="modal-footer">
         <!-- <span style="color:  #e59866 ;"><i class="material-icons">check_box</i> Registros Contabilizados</span><br>
         <span style="color:  #85929e;"><i class="material-icons">check_box</i> Registros No Contabilizados</span><br> -->
      </div>
    </div>
  </div>
</div>

<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>



<div class="modal fade" id="modalListaEstadosCuenta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="background-color:#e2e6e7">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Relacionar Estado Cuenta - Factura</h4>
      </div>
      <div class="modal-body">
         <table class="table table-condensed table-bordered small">
          <thead>
           <tr class="bg-primary text-white">
             <td>FECHA</td>
             <td>TIPO</td>
             <td>DESCRIPCION</td>
             <td>MONTO</td>
             <!-- <td>SALDO</td> -->
             <td>QUITAR</td>
           </tr>
           </thead>
           <tbody id="datos_estados_cuenta_detalle">
             
           </tbody>
           <tfoot >
            <tr style="background-color:#768c9c; color: #FFFFFF">
              <td>-</td>
              <td colspan="2">TOTAL SALDO</td>              
              <td><input style="background-color:#768c9c; color: #FFFFFF" type="text" name="total_saldo_estadoscuenta" id="total_saldo_estadoscuenta" value="0" readonly="true"></td>
              <td>-</td>
            </tr>
           </tfoot>           
         </table>
         <input type="hidden" id="cantidad_filas_estadoscuenta" value="0">
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-success" onclick="facturarEstadosCuenta()">FACTURAR</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Adicionar + </button>
      </div>
    </div>
  </div>
</div>