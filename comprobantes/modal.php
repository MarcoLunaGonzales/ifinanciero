<style>
  #mayores_cuenta_reporte_modal_filter{
         display: none !important;
       }      
</style>
<!-- modal -->
<div class="modal fade modal-arriba" id="modalPegarDatosComprobante" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-primary card-header-text">
        <div class="card-text">
          <h4>Pegar Datos - Excel</h4>      
        </div>
        
        <button title="Cerrar" type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
      </div>
      <div class="card-body">  
        <div class="row">                      
            <label class="col-sm-2 col-form-label" style="color: #4a148c;">Pega los datos del EXCEL aquí</label>
            <div class="col-sm-12">
                <div class="form-group">  
                  <div id="">
                   <textarea class="form-control" style="background-color:#E3CEF6;text-align: left;" rows="10" name="data_excel" id="data_excel"></textarea>                        
                 </div>                                                                                                
               </div>
             </div>
        </div>
      </div>

      <div class="modal-footer">
            <a href="#" class="btn btn-primary btn-round" id="boton_cargar_datos" onclick="cargarComprobanteExcel()">Cargar Datos</a>
            <a href="#" class="btn btn-success btn-round d-none" id="boton_generar_filas" onclick="generarComprobanteExcel()">Generar Filas</a>
            <a href="#" class="btn btn-default btn-round" onclick="limpiarComprobanteExcel()">Limpiar Datos</a>
      </div>
      <hr>
      <div id="div_datos_excel"></div>
    </div>
  </div>
</div>

<!-- modal solicitud recursos -->
<div class="modal fade modal-arriba" id="modal_solicitudes_recursos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-default card-header-text">
        <div class="card-text">
          <h4><span class="material-icons text-dark">view_sidebar</span> Lista de Solicitudes SIS  <div id="numero_solicitud_relacionado"></div></h4>      
        </div>   
        <button title="Cerrar" type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>

      </div>
      <div class="card-body">
        <div class="row">
          <input type="hidden" id="fila_detallesolicitudsis" value="0"> 
        <div class="btn-group float-right">
          <a title="Quitar" href="#"  onclick="quitarSolicitudRecursoDelComprobante()" class="btn btn-rose btn-sm">
          <i class="material-icons">close</i> Quitar Solicitud Recurso <span id="numero_badge_sr"></span>
          </a>
        </div>          
                     
        </div>
        <div class="row" id="div_contenido_solicitudes">
              
        </div> 
      </div>
      <div class="modal-footer">
         <span class="text-success"><i class="material-icons">check_box</i> Habilitado</span><br>
         <span class="text-danger"><i class="material-icons">check_box</i> Deshabilitado</span><br>
      </div>
    </div>
  </div>
</div>

<!-- modal libreta bancaria -->
<div class="modal fade modal-arriba" id="modalListaMayoresCuenta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-primary card-header-text">
        <div class="card-text">
          <h4>Lista de Mayores <div id="monto_debe_total_modal"></div></h4>      
        </div>
        
        <button title="Cerrar" type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
        <a id="boton_libreta_detalle_facturas_comp" title="Facturas" onclick="mostrar_listado_comprobantes()" class="btn btn-primary btn-sm btn-fab float-right" >
          <i class="material-icons">C</i><span id="ncomprobantesdetalles" class="count bg-warning">0</span>
        </a>
      </div>
      <div class="card-body">
        <input type="hidden" name="indice" id="indice" value="0">      
        <div class="row">

        <div class="btn-group">
        </div>          
            <div class="table-responsive" id="contenedor_tabla_mayores_cuenta">
              
            </div>          
        </div>
      </div>
      <div class="modal-footer">
         <span class="text-success"><i class="material-icons">check_box</i> Habilitado</span><br>
         <span class="text-danger"><i class="material-icons">check_box</i> Deshabilitado</span><br>
      </div>
    </div>
  </div>
</div>

<!-- notice modal -->
<div class="modal fade" id="modalEditFac" tabindex="-1" role="dialog" style="z-index:99999"aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice modal-xl">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
              <div class="card ">
                <div class="card-header" id="divTituloCuentaDetalle">
                  <h4 class="card-title">Facturas -
                    <small class="description">Edicion</small>
                  </h4>
                </div>
                <div class="card-body ">
                        <input class="form-control" type="hidden" name="fila_fac" id="fila_fac"/>
                        <input class="form-control" type="hidden" name="indice_fac" id="indice_fac"/>
                        <div style="padding: 20px;">
                          <div class="row">                      
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">NIT</label>
                            <div class="col-sm-3">
                              <div class="form-group">  
                                <div id="">
                                  <input class="form-control" type="number" name="nit_fac_edit" id="nit_fac_edit" required="true">                        
                                </div>                                                                                                
                              </div>

                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Nro. Factura</label>
                            <div class="col-sm-3">
                              <div class="form-group" id="">
                                  <!-- <label for="number" class="bmd-label-floating" style="color: #4a148c;">Nro. Factura</label>      -->
                                  <input class="form-control" type="number" name="nro_fac_edit" id="nro_fac_edit" required="true"/>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Fecha</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="fecha_fac" class="bmd-label-floating" style="color: #4a148c;">Fecha</label>      -->
                                <input type="date" class="form-control" name="fecha_fac_edit" id="fecha_fac_edit" value="<?=$fechaActualModal?>">
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Importe</label>
                            <div class="col-sm-3">
                              <div class="form-group" id="">
                                <input class="form-control" type="number" name="imp_fac_edit" id="imp_fac_edit" required="true"/>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Exento</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="exe_fac" class="bmd-label-floating" style="color: #4a148c;">Extento</label>      -->
                                <input class="form-control" type="text" name="exe_fac_edit" id="exe_fac_edit" required="true" value="0" />
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">ICE</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="ice_fac" class="bmd-label-floating" style="color: #4a148c;">ICE</label>      -->
                                <input class="form-control" type="text" name="ice_fac_edit" id="ice_fac_edit" required="true" value="0" />
                              </div>
                             </div>
                          </div>                                                                  
                          <!--No tiene funcion este campo-->
                          <div class="row">                                            
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Tasa Cero</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="taza_fac" class="bmd-label-floating" style="color: #4a148c;">Taza Cero</label>      -->
                                <input class="form-control" type="text" name="taza_fac_edit" id="taza_fac_edit" required="true" value="0" />
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Autorizaci&oacute;n</label>
                            <div class="col-sm-3">
                              <div class="form-group" id="">
                                <!-- <label for="aut_fac" class="bmd-label-floating" style="color: #4a148c;">Nro. Autorizaci&oacute;n</label>      -->
                                <input class="form-control" type="text" name="aut_fac_edit" id="aut_fac_edit" required="true"/>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Cod. Control</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="con_fac" class="bmd-label-floating" style="color: #4a148c;">Cod. Control</label>      -->
                                <input class="form-control" type="text" name="con_fac_edit" id="con_fac_edit" required="true"/>
                              </div>
                             </div>
                          </div> 
                          <div class="row">
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Tipo</label>
                            <div class="col-sm-2">
                              <div class="form-group">
                                <select class="selectpicker form-control form-control-sm" name="tipo_fac_edit" id="tipo_fac_edit" data-style="btn btn-primary">                                  
                                   <?php
                                         $stmt = $dbh->prepare("SELECT codigo, nombre FROM tipos_compra_facturas where cod_estadoreferencial=1");
                                       $stmt->execute();
                                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $codigoX=$row['codigo'];
                                        $nombreX=$row['nombre'];
                                        ?><option value="<?=$codigoX;?>"><?=$nombreX;?></option><?php
                                         }
                                     ?>
                                </select>
                              </div>
                            </div>                        
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Razón Social</label>
                            <div class="col-sm-8">
                              <div class="form-group" id="">                                
                                <input type="text" class="form-control" name="razon_fac_edit" id="razon_fac_edit">
                                
                              </div>
                            </div>   
                        </div>
                        
                          
                        </div>                     
                        <div class="form-group float-right">
                          <button type="button" class="btn btn-info btn-round" onclick="saveFacturaEdit()">Guardar</button>
                        </div>
                      
                </div>
              </div>
        
        <!--<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eaque ullam autem illum, minima doloribus doloremque adipisci dolorem, repellendus debitis animi laboriosam commodi dolores et sint, quod. Pariatur, repudiandae sequi assumenda.</p>-->
      </div>
      <div class="modal-footer justify-content-center">
        
      </div>
    </div>
  </div>
</div>
<!-- end notice modal -->

<!-- small modal -->
<div class="modal fade modal-mini modal-primary" id="modalDist" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-small">
    <div id="modalAlertStyle" class="modal-content">
      <div class="modal-header">
      	Distribucion de Gastos!
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="material-icons">clear</i></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="distFila" value="">
        <input type="hidden" id="tipoDistribucion" value="">
        <div id="mensajeDist"></div>
      </div>
      <div class="modal-footer">
        <a class="btn btn-success text-white" data-dismiss="modal" onclick="nuevaDistribucion();">Aceptar
        </a>
      </div>
    </div>
  </div>
</div>
<!--    end small modal -->
<!-- small modal -->
<div class="modal fade modal-mini modal-primary" id="modalCopy" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-small">
    <div class="modal-content bg-info text-white">
      <div class="modal-header">
      	<i class="material-icons" data-notify="icon"><?=$iconCopy?></i>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="material-icons">clear</i></button>
      </div>
      <div class="modal-body">
        <p>¿Desea copiar la glosa a todos los detalles?.</p> 
      </div>
      <div class="modal-footer">
      	<button type="button" class="btn btn-link" data-dismiss="modal"> <-- Volver </button>
        <button type="button" onclick="copiarGlosa()" class="btn btn-white btn-link" data-dismiss="modal">Aceptar
          <div class="ripple-container"></div>
        </button>
      </div>
    </div>
  </div>
</div>
<!--    end small modal -->
<!-- small modal -->
<div class="modal fade modal-primary" id="modalAbrirPlantilla" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content bg-secondary text-white">
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
              <div class="card ">
              	<div class="card-header">
                  <h4 class="card-title">Plantilla -
                    <small class="description">Abrir :</small>
                  </h4>
                </div>
                <div class="card-body ">
                 <div id="listaPlan"></div>
                 <div id="mensaje"></div>
                </div>
              </div>
      </div>	
    </div>
  </div>
</div>
<!--    end small modal -->

<!-- small modal -->
<div class="modal fade modal-mini modal-primary" id="modalCopySel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-small">
    <div class="modal-content">
      <div class="modal-header">
      	<i class="material-icons" data-notify="icon"><?=$iconCopy?></i>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="material-icons">clear</i></button>
      </div>
      <div class="modal-body">
        <p>Seleccione las opciones que desea cambiar. (Los cambios se aplicaran a todas las cuentas)</p>
        <div class="row">
        	<div class="col-sm-6">
        	    <div class="form-group">
	              <select class="selectpicker form-control form-control-sm" name="unidad" id="unidad" data-style="<?=$comboColor;?>">
			  	      <option disabled selected="selected" value="">Oficina</option>
			  	                <?php
			  	                $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 2");
				                $stmt->execute();
				                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				                	$codigoX=$row['codigo'];
				                	$nombreX=$row['nombre'];
				                	$abrevX=$row['abreviatura'];
				                ?>
				        <option value="<?=$codigoX;?>"><?=$abrevX;?></option>	
				               <?php
			  	               }
			  	               ?>
			       </select>
			    </div>
      	      </div>
      	      <div class="col-sm-6">
        	       <div class="form-group">
	                    <select class="selectpicker form-control form-control-sm" name="area" id="area" data-style="<?=$comboColor;?>">
			  	                 <option disabled selected value="">Area</option>
			  	                 <?php
			  	                 $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 2");
				                 $stmt->execute();
				                 while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					                 $codigoX=$row['codigo'];
					                 $nombreX=$row['nombre'];
					                  $abrevX=$row['abreviatura'];
				                  ?>
				                  <option value="<?=$codigoX;?>"><?=$abrevX;?></option>	
				                  <?php
			  	                  }
			  	                  ?>
			              </select>
			         </div>
      	      </div>
        </div>
        <div id="copiar_sel_msg"></div>
      </div>
      <div class="modal-footer">
      	<button type="button" class="btn btn-link" data-dismiss="modal">Cerrar</button>
        <button type="button" onclick="copiarSelect()" class="btn btn-primary btn-link">Copiar
          <div class="ripple-container"></div>
        </button>
      </div>
    </div>
  </div>
</div>
<!--    end small modal -->
<!-- small modal -->
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
<!--    end small modal -->
<!-- Classic Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-notice" style="max-width: 90% !important;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Buscar Cuenta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<form name="form1">
          <div class="row">
            <div class="form-group col-sm-3">
                <label for="nro_cuenta" class="bmd-label-floating">Nro. Cuenta:</label>
              </div>
            <div class="form-group col-sm-3">
                <label for="cuenta" class="bmd-label-floating">Nombre Cuenta:</label>
              </div>
            <div class="form-group col-sm-3">
                <label for="cuenta" class="bmd-label-floating">Cuenta Auxiliar:</label>
                
              </div>
            <div class="form-group col-sm-2">
            </div>
              <div class="form-group col-sm-1">
              </div>  
        </div>
	  		<div class="row">
    	      	<div class="form-group col-sm-3">
            		<!--<label for="nro_cuenta" class="bmd-label-floating">Nro. Cuenta:</label>-->
                <input type="hidden" id="nro_cuenta_id" name="nro_cuenta_id">
            		<input type="number" class="form-control" style="background-color:#E3CEF6;text-align: left" id="nro_cuenta" name="nro_cuenta" onkeypress=" pulsar(event);" onkeyDown="pulsar(event);" onkeyUp="pulsar(event);" autofocus><!--onkeypress="buscarCuentaList('numero'); pulsar(event);" onkeyDown="buscarCuentaList('numero');" onkeyUp="buscarCuentaList('numero');"-->
          		</div>
          		<div class="form-group col-sm-3">
            		<!--<label for="cuenta" class="bmd-label-floating">Cuenta:</label>-->
                <input type="hidden" id="cuenta_id" name="cuenta_id">
            		<input type="text" class="form-control" id="cuenta" style="background-color:#E3CEF6;text-align: left" name="cuenta" onkeypress="pulsar(event)" onkeyDown="pulsar(event);" onkeyUp="pulsar(event);"><!--onkeypress="buscarCuentaList('nombre');pulsar(event)" onkeyDown="buscarCuentaList('nombre');" onkeyUp="buscarCuentaList('nombre');"-->
          		</div>
              <div class="form-group col-sm-2">
                <!--<label for="cuenta" class="bmd-label-floating">Cuenta Auxiliar:</label>-->
                <input type="hidden" id="cuenta_id_auxiliar" name="cuenta_id_auxiliar">
                <input type="text" class="form-control" style="background-color:#E3CEF6;text-align: left" id="cuenta_auxiliar_modal" name="cuenta_auxiliar_modal" onkeypress="buscarCuentaList('nombre');pulsar(event)" onkeyDown="buscarCuentaList('nombre');" onkeyUp="buscarCuentaList('nombre');"><!--onkeypress="buscarCuentaList('nombre');pulsar(event)" onkeyDown="buscarCuentaList('nombre');" onkeyUp="buscarCuentaList('nombre');"-->
              </div>
              <div class="form-group col-sm-2">
                  <button type="button" class="btn btn-danger btn-sm" onclick="buscarCuenta(form1);">
                    <i class="material-icons">search</i> Buscar
                  </button>
              </div>
          		<div class="form-group col-sm-2">
	              <select class="selectpicker form-control form-control-sm" name="padre" id="padre" data-style="<?=$comboColor;?>" onchange="buscarCuenta(form1);">
			  	        <option selected="selected" value="">Todas</option>                
			  	         <?php
			  	               $stmt = $dbh->prepare("SELECT codigo, nombre, SUBSTRING(numero, 1, 1) primero  FROM plan_cuentas where nivel=1");
				               $stmt->execute();
				              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				              	$primeroX=$row['primero'];
				              	$nombreX=$row['nombre'];
				              	?><option value="<?=$primeroX;?>"><?=$nombreX;?></option><?php
			  	               }
			  	           ?>
			       </select>
			    </div>
    	      	
          	</div>
          	<div class="row" id="divResultadoBusqueda">
              <?php 
              //include "pruebaBusqueda2.php"; 
               include "pruebaBusqueda.php";
              ?>    
    	      	<div class="form-group col-sm-8">
	          		Resultados de la Búsqueda
                    		
          		</div>
          	</div>
        </form>
      </div>
      <div class="modal-footer">     	
        <!--button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button-->
      </div>
    </div>
  </div>
</div>
<!-- <script >
  function ver_cuentasAuxiliares(index){ 
    var label=index;
    if($(".det-cuenta-"+label).is(":visible")){
      $(".det-cuenta-"+label).hide();
    }else{
      $(".det-cuenta-"+label).show();
    }
  }
</script> -->

<!--  End Modal -->
<!-- small modal -->
<div class="modal fade modal-primary" id="modalPlantilla" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content bg-danger text-white">
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
              <div class="card ">
              	<div class="card-header">
                  <h4 class="card-title">Plantillas -
                    <small class="description">Guardar :</small>
                  </h4>
                </div>
                <div class="card-body ">
                      <form name="form2">
                           <div class="row">
			                 <label class="col-sm-2 col-form-label">T&iacute;tulo :</label>
			                 <div class="col-sm-10">
			                	<div class="form-group">
			                	  <input class="form-control" type="text" name="titulo" id="titulo" required="true"/>
			                	</div>
			                  </div>
			                </div>
			                <div class="row">
			                 <label class="col-sm-2 col-form-label">Descripci&oacute;n :</label>
			                 <div class="col-sm-10">
			                	<div class="form-group">
			                	  <input class="form-control" type="text" name="descrip_plan" id="descrip_plan"/>
			                	</div>
			                  </div>
			                </div>
			                
                         </form>
                         <div class="form-group float-right">
                        <a href="#" type="button" class="btn btn-danger btn-round" onclick="guardarPlantilla()">Guardar</a>
                      </div>
                 <div id="mensaje"></div>
                </div>
              </div>
      </div>	
    </div>
  </div>
</div>
<!--    end small modal -->
<!-- notice modal -->
<?php
  $valorNoValido="Valor no Válido.";
?>
<div class="modal fade" id="modalFac" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice modal-xl">
    <div class="modal-content">
      <div class="modal-body">
      	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
              <div class="card ">
              	<div class="card-header" id="divTituloCuentaDetalle">
                  <h4 class="card-title">Facturas -
                    <small class="description">Cuenta :</small>
                  </h4>
                </div>
                <div class="card-body ">
                  <ul class="nav nav-pills nav-pills-warning" role="tablist">
                  	<li class="nav-item">
                      <a id="nav_boton1"class="nav-link active" data-toggle="tab" href="#link110" role="tablist">
                          <span class="material-icons">view_list</span> Lista
                      </a>
                    </li>
                    <li class="nav-item">
                      <a id="nav_boton2"class="nav-link" data-toggle="tab" href="#link111" role="tablist">
                        <span class="material-icons">add</span> Nuevo
                      </a>
                    </li>
                    <li class="nav-item">
                      <a id="nav_boton3" class="nav-link" data-toggle="tab" href="#link112" role="tablist">
                        <span class="material-icons">filter_center_focus</span> QR quincho
                      </a>
                    </li>
                  </ul>
                  <div class="tab-content tab-space">
                    <div class="tab-pane active" id="link110" style="background: #e0e0e0">
                      <div id="divResultadoListaFac">
        	  
                       </div>
                    </div>
                    <div class="tab-pane" id="link111" style="background: #e0e0e0">
                      <form name="form_facturas" id="form_facturas">
  			                <input class="form-control" type="hidden" name="codCuenta" id="codCuenta"/>
                        <div style="padding: 20px;">
                          <div class="row">                      
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">NIT</label>
                            <div class="col-sm-3">
                              <div class="form-group">  
                                <div id="divNitFacturaDetalle">
                                  <input class="form-control" type="number" name="nit_fac" id="nit_fac" required="true">
                                  <div class="invalid-feedback"><?=$valorNoValido;?></div>                        
                                </div>
                                <div id="divNit2FacturaDetalle">                                  
                                </div>                                
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Nro. Factura</label>
                            <div class="col-sm-3">
                              <div class="form-group" id="divNroFacFacturaDetalle">
                                  <input class="form-control" type="number" name="nro_fac" id="nro_fac" required="true"/>
                                  <div class="invalid-feedback"><?=$valorNoValido;?></div>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Fecha</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <input type="date" class="form-control" name="fecha_fac" id="fecha_fac" value="<?=$fechaActualModal?>" required="true">
                                <div class="invalid-feedback"><?=$valorNoValido;?></div>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Importe</label>
                            <div class="col-sm-3">
                              <div class="form-group" id="divImporteFacturaDetalle">
                                <input class="form-control" type="number" step="0.01" name="imp_fac" id="imp_fac" required="true"/>
                                <div class="invalid-feedback"><?=$valorNoValido;?></div>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Exento</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <input class="form-control" type="number" step="0.01" name="exe_fac" id="exe_fac" required="true" value="0" />
                                <div class="invalid-feedback"><?=$valorNoValido;?></div>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">ICE</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <input class="form-control" type="number" step="0.01" name="ice_fac" id="ice_fac" required="true" value="0" />
                                <div class="invalid-feedback"><?=$valorNoValido;?></div>
                              </div>
                             </div>
                          </div>                                                                  
                          <!--No tiene funcion este campo-->
                          <div class="row">                                            
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Tasa Cero</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="taza_fac" class="bmd-label-floating" style="color: #4a148c;">Taza Cero</label>      -->
                                <input class="form-control" type="number" step="0.01" name="taza_fac" id="taza_fac" required="true" value="0" />
                                <div class="invalid-feedback"><?=$valorNoValido;?></div>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Autorizaci&oacute;n</label>
                            <div class="col-sm-3">
                              <div class="form-group" id="divNroAutoFacturaDetalle">
                                <input class="form-control" type="text" name="aut_fac" id="aut_fac" required="true"/>
                                <div class="invalid-feedback"><?=$valorNoValido;?></div>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Cod. Control</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <input class="form-control" type="text" name="con_fac" id="con_fac" required="true"/>
                                <div class="invalid-feedback"><?=$valorNoValido;?></div>
                              </div>
                             </div>
                          </div> 
                          <div class="row">
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Tipo</label>
                            <div class="col-sm-2">
                              <div class="form-group">
                                <select class="selectpicker form-control form-control-sm" name="tipo_fac" id="tipo_fac" data-style="btn btn-primary">                                  
                                   <?php
                                         $stmt = $dbh->prepare("SELECT codigo, nombre FROM tipos_compra_facturas where cod_estadoreferencial=1");
                                       $stmt->execute();
                                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $codigoX=$row['codigo'];
                                        $nombreX=$row['nombre'];
                                        ?><option value="<?=$codigoX;?>"><?=$nombreX;?></option><?php
                                         }
                                     ?>
                                </select>
                              </div>
                            </div>                        
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Razón Social</label>
                            <div class="col-sm-8">
                              <div class="form-group" id="divRazonFacturaDetalle">                                
                                <input type="text" class="form-control" name="razon_fac" id="razon_fac" required="true">
                                <div class="invalid-feedback"><?=$valorNoValido;?></div>
                              </div>
                            </div>   
                        </div>
                        
                          
                        </div>                     
  			                <div class="form-group float-right">
  			                	<button type="button" class="btn btn-info btn-round" onclick="saveFactura()">Guardar</button>
  			                </div>
                      </form>
                    </div>

                    <div class="tab-pane" id="link112">
                     <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                          <div class="fileinput-preview fileinput-exists thumbnail"></div>
                         <div>
                         <span class="btn btn-rose btn-round btn-file">
                           <span class="fileinput-new">Subir archivo .txt</span>
                           <span class="fileinput-exists">Subir archivo .txt</span>
                           <input type="file" name="qrquincho" id="qrquincho" accept=".txt"/>
                         </span>
                
                        </div>
                       </div>
                       <p>Los archivos cargados se adjuntaran a la lista de facturas existente</p>
                    </div>
                  </div>
                </div>
              </div>
        
        <!--<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eaque ullam autem illum, minima doloribus doloremque adipisci dolorem, repellendus debitis animi laboriosam commodi dolores et sint, quod. Pariatur, repudiandae sequi assumenda.</p>-->
      </div>
      <div class="modal-footer justify-content-center">
        
      </div>
    </div>
  </div>
</div>
<!-- end notice modal -->

<!-- small modal -->
<div class="modal fade modal-primary" id="modalRetencion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons text-dark">ballot</i>
                  </div>
                  <h4 class="card-title">Retenciones</h4>
                </div>
                <div class="card-body">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                  <i class="material-icons">close</i>
                </button>
                <input class="form-control" type="hidden" name="retencion_codcuenta" id="retencion_codcuenta"/>
                <input class="form-control" type="hidden" name="retFila" id="retFila"/>
                <div class="row" id="retencion_cuenta">
                  </div>
                  <div class="row">
                       <label class="col-sm-2 col-form-label">Importe</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          <input class="form-control" type="number" step="0.001" name="retencion_montoimporte" id="retencion_montoimporte"/>
                        </div>
                        </div>
                  </div>
                  <div class="card-title"><center><h6>Retenciones</h6></center></div>
                 <table class="table table-condensed table-striped">
                   <thead>
                     <tr>
                       <th>Opcion</th>
                       <th class="text-left">Descripci&oacute;n</th>
                     </tr>
                   </thead>
                   <tbody>
                     <?php 
                        $stmtRetencion = $dbh->prepare("SELECT * from configuracion_retenciones where cod_estadoreferencial=1 order BY nombre");
                        $stmtRetencion->execute();
                        $contRetencion=0;
                        while ($row = $stmtRetencion->fetch(PDO::FETCH_ASSOC)) {
                           $nombreX=$row['nombre'];
                           $abrevX=$row['abreviatura'];
                           $codigoX=$row['codigo'];
?>
                        <tr>
                          <td align="center" width="20%">
                          <div class="form-check">
                            <label class="form-check-label">
                              <input class="form-check-input" type="radio" id="retencion<?=$codigoX?>" name="retenciones" <?=($contRetencion==0)?"checked":"";?> value="<?=$codigoX?>">
                              <span class="form-check-sign">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div>
                          </td>
                          <td class="text-left"><?=$nombreX;?> - <?=$abrevX?></td>
                        </tr>

                      <?php
                      $contRetencion++;
                        }
                     ?>
                   </tbody>  
                 </table>
                 <div id="mensaje_retencion"></div>
                 <div class="form-group float-right">
                        <button type="button" class="btn btn-info btn-round" onclick="agregarRetencion()">Agregar</button>
                  </div>
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->

<!-- small modal -->
<div class="modal fade modal-primary" id="modalEstadosCuentas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice" style="max-width: 90% !important;">
    <div class="modal-content card">
                <div class="card-header card-header-danger card-header-text">
                  <div class="card-text">
                    <h4>ESTADOS DE CUENTA</h4>
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                
                <div class="modal-body">
                  
                <!--<input class="form-control" type="text" name="est_codcuenta" id="est_codcuenta"/>
                <input class="form-control" type="text" name="est_codcuentaaux" id="est_codcuentaaux"/>-->
                <input class="form-control" type="hidden" name="estFila" id="estFila"/>
                <div class="card-title"><center><!--h6>Datos de la nueva transaccion</h6--></center></div>
                 <div class="row">
                       <label class="col-sm-2 col-form-label">Monto</label>
                       <div class="col-sm-3">
                        <div class="form-group">
                          <input class="form-control" type="number" step="0.001" readonly name="monto_estadocuenta" id="monto_estadocuenta"/>
                        </div>
                        </div>
                  </div>
                  <div class="card-title"><center><div id="tituloCuentaModal"></div></center></div>
                  <br>
                 <div id="div_estadocuentas"></div>
                 <div id="mensaje_estadoscuenta"></div>
                 <div class="form-group float-right">
                        <!--button type="button" class="btn btn-info btn-round" onclick="agregarEstadoCuenta()">Agregar</button-->
                        <button type="button" class="btn btn-danger btn-round" onclick="quitarEstadoCuenta()">Quitar Estado de Cuenta</button>
                  </div>
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->


<div class="modal fade" id="modalRegisterCuentasAux" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
      <div class="card-header card-header-success card-header-text">
        <div class="card-text">
          <h5>Nueva Cuenta Auxiliar</h5> 
        </div>
        <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
      </div>
      <!-- <input type="hidden" name="cod_cuenta_x" id="cod_cuenta_x"/> -->
      <div class="card-body">
        <div class="row">
          <label class="col-sm-2 col-form-label">Cuenta</label>
          <div class="col-sm-4">
            <div class="form-group">                  
              <select name="cod_cuenta" id="cod_cuenta" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true" >
                <option value="">SELECCIONAR UNA OPCION</option><?php 
                $sql="SELECT codigo,numero,nombre from plan_cuentas where cuenta_auxiliar=1 order by nombre";
                $stmt = $dbh->prepare($sql);
                $stmt->bindParam(':codigo', $codigo);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':numero', $numero);
                $stmt->execute();
                while ($row = $stmt->fetch()){ ?>
                  <option value="<?=$row["codigo"];?>"><?=$row["numero"];?> - <?=$row["nombre"];?></option><?php 
                } 
                ?>
             </select>
            </div>
          </div>
        </div>

        <div class="row">
          <label class="col-sm-2 col-form-label">Nombre</label>
          <div class="col-sm-7">
          <div class="form-group">
            <input class="form-control" type="text" name="nombre_x" id="nombre_x" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
          </div>
          </div>
        </div>

        <div class="row">
          <label class="col-sm-2 col-form-label">Tipo</label>
            <div class="col-sm-4">
                  <div class="form-group">
                  <select class="selectpicker form-control form-control-sm" name="tipo_x" id="tipo_x" data-style="<?=$comboColor;?>" required="true" onChange="ajaxTipoProveedorCliente_comprobante(this);">
                  <option disabled selected value="">Seleccionar una opcion</option>
                <option value="1">Proveedor</option>  
                <option value="2">Cliente</option>  
              </select>
              </div>
                </div>
        </div>

        <div class="row">
          <label class="col-sm-2 col-form-label">Proveedor/Cliente</label>
          <div class="col-sm-7">
          <div class="form-group" id="divProveedorCliente">
            
          </div>
          </div>
        </div>


        <div class="form-group float-right">
            <button type="button" class="btn btn-warning btn-round" onclick="guardarNuevaCuentaAuxi()">Guardar</button>
        </div>         
      </div>
    </div>
  </div>
</div>
<!-- edit -->
