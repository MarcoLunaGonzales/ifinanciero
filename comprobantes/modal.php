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
  <div class="modal-dialog modal-lg">
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
        <p>Seleccione las opciones que desea cambiar. (Los cambios seran para todos los comprobantes)</p>
        <div class="row">
        	<div class="col-sm-6">
        	    <div class="form-group">
	              <select class="selectpicker form-control form-control-sm" name="unidad" id="unidad" data-style="<?=$comboColor;?>">
			  	      <option disabled selected="selected" value="">Unidad</option>
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
                <input type="hidden" id="nro_cuenta_id" name="nro_cuenta_id">
            		<input type="number" class="form-control" id="nro_cuenta" name="nro_cuenta" onkeypress="buscarCuentaList('numero'); pulsar(event);" onkeyDown="buscarCuentaList('numero');" onkeyUp="buscarCuentaList('numero');" autofocus>
          		</div>
          		<div class="form-group col-sm-3">
            		<label for="cuenta" class="bmd-label-floating">Cuenta:</label>
                <input type="hidden" id="cuenta_id" name="cuenta_id">
            		<input type="text" class="form-control" id="cuenta" name="cuenta" onkeypress="buscarCuentaList('nombre');pulsar(event)" onkeyDown="buscarCuentaList('nombre');" onkeyUp="buscarCuentaList('nombre');">
          		</div>
              <div class="form-group col-sm-3">
                <label for="cuenta" class="bmd-label-floating">Cuenta Auxiliar:</label>
                <input type="hidden" id="cuenta_id_auxiliar" name="cuenta_id_auxiliar">
                <input type="text" class="form-control" id="cuenta_auxiliar_modal" name="cuenta_auxiliar_modal" onkeypress="buscarCuentaListAux('nombre');pulsar(event)" onkeyDown="buscarCuentaListAux('nombre');" onkeyUp="buscarCuentaListAux('nombre');">
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
    	      	<div class="form-group col-sm-1">
      		        <button type="button" class="btn btn-just-icon btn-danger btn-link" onclick="buscarCuenta(form1);">
      		        	<i class="material-icons">search</i>
      		        </button>
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
			                <div class="form-group float-right">
			                	<button type="button" class="btn btn-danger btn-round" onclick="guardarPlantilla()">Guardar</button>
			                </div>
                         </form>
                 <div id="mensaje"></div>
                </div>
              </div>
      </div>	
    </div>
  </div>
</div>
<!--    end small modal -->
<!-- notice modal -->
<div class="modal fade" id="modalFac" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice modal-lg">
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
                      <form name="form2">
			                     <input class="form-control" type="hidden" name="codCuenta" id="codCuenta"/>
                      <div class="row">
			                 <label class="col-sm-2 col-form-label">NIT</label>
			                 <div class="col-sm-4">
			                	<div class="form-group">
			                	  <input class="form-control" type="text" name="nit_fac" id="nit_fac" required="true"/>
			                	</div>
			                  </div>
                        <label class="col-sm-2 col-form-label">Nro. Factura</label>
                       <div class="col-sm-4">
                        <div class="form-group">
                          <input class="form-control" type="number" name="nro_fac" id="nro_fac" required="true"/>
                        </div>
                        </div>
			                </div>
			                <div class="row">
			                 <label class="col-sm-2 col-form-label">Fecha</label>
			                 <div class="col-sm-4">
			                	<div class="form-group">
			                	  <input type="text" class="form-control datepicker" name="fecha_fac" id="fecha_fac" value="<?=$fechaActualModal?>">
			                	</div>
                        </div>
                        <label class="col-sm-2 col-form-label">Importe</label>
                       <div class="col-sm-4">
                        <div class="form-group">
                          <input class="form-control" type="number" name="imp_fac" id="imp_fac" required="true"/>
                        </div>
                        </div>
			                </div>
                      <!-- Exento oculto-->
                      <input class="form-control" type="hidden" name="exe_fac" id="exe_fac" required="true"/>
                      <!--No tiene funcion este campo-->
			                <div class="row">
			                 <label class="col-sm-2 col-form-label">Nro. Autorizaci&oacute;n</label>
			                 <div class="col-sm-4">
			                	<div class="form-group">
			                	  <input class="form-control" type="text" name="aut_fac" id="aut_fac" required="true"/>
			                	</div>
			                  </div>
                        <label class="col-sm-2 col-form-label">Cod. Control</label>
                       <div class="col-sm-4">
                        <div class="form-group">
                          <input class="form-control" type="text" name="con_fac" id="con_fac" required="true"/>
                        </div>
                       </div>
			                </div>
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Razon Social</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          <textarea class="form-control" name="razon_fac" id="razon_fac" value=""></textarea>
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
                          <td class="text-left"><?=$nombreX;?></td>
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
