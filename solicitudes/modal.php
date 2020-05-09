<!-- notice modal -->
<div class="modal fade" id="modalDistribucionSol" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
      <div class="card-header card-header-success card-header-text">
          <div class="card-text">
            <h5>Distribución de Gastos <b id="titulo_distribucion"></b> </h5> 
          </div>
          <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
            <i class="material-icons">close</i>
          </button>
      </div>
          <div class="card-body">
            <div class="row col-sm-12">
              <div class="col-sm-6">                                                              
                  <table class="table table-condensed table-bordered">
                    <thead>
                      <tr class="bg-principal text-white">
                        <th>#</th>
                        <th>Oficina</th>
                        <th width="10%">%</th>
                      </tr>
                    </thead>
                    <tbody id="cuerpo_tabladistofi">
                      
                    </tbody>
                  </table>
              </div>
              <div class="col-sm-6">                                                              
                  <table class="table table-condensed table-bordered">
                    <thead>
                      <tr class="bg-principal text-white">
                        <th>#</th>
                        <th>Area</th>
                        <th width="10%">%</th>
                      </tr>
                    </thead>
                    <tbody id="cuerpo_tabladistarea">
                      
                    </tbody>
                  </table>
              </div> 
             </div>                     
             <div class="form-group float-right">
                <button type="button" class="btn btn-success btn-round" onclick="guardarDistribucionSolicitudRecurso()">Guardar</button>
             </div>         
          </div>
    </div>
  </div>
</div>
<!-- end notice modal -->


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
                                <input type="text" class="form-control datepicker" name="fecha_fac_edit" id="fecha_fac_edit" value="<?=$fechaActualModal?>">
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
<div class="modal fade modal-primary" id="modalFileDet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <i class="material-icons" data-notify="icon"><?=$iconFile?></i>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="material-icons">clear</i></button>
      </div>
      <div class="modal-body">
        <p>Cargar archivos de respaldo.</p> 
        <input type="hidden" id="codigo_fila" value=""/>
           <div class="fileinput fileinput-new col-md-12" data-provides="fileinput">
            <div class="row">
              <div class="col-md-9">
                <div class="border" id="lista_archivosdetalle">Ningun archivo seleccionado</div>
              </div>
              <div class="col-md-3">
                <span class="btn btn-info btn-round btn-file">
                      <span class="fileinput-new">Buscar</span>
                      <span class="fileinput-exists">Cambiar</span>
                      <input type="file" name="archivosDetalle[]" id="archivosDetalle" multiple="multiple"/>
                   </span>
                <a href="#" id="boton_quitararchivos" class="btn btn-success btn-round fileinput-exists" onclick="archivosPreviewDetalle(1)" data-dismiss="fileinput"><i class="material-icons">clear</i> Quitar</a>
              </div>
            </div>
           </div>
           <p class="text-danger">Lista de archivos</p>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="" class="btn btn-link" data-dismiss="modal">Aceptar
          <div class="ripple-container"></div>
        </button>
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

<!-- notice modal -->
<div class="modal fade modal-arriba" id="modalFac" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                      <form name="form2">
                        <input class="form-control" type="hidden" name="codCuenta" id="codCuenta"/>
                        <div style="padding: 20px;">
                          <div class="row">                      
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">NIT</label>
                            <div class="col-sm-3">
                              <div class="form-group">  
                                <div id="divNitFacturaDetalle">
                                  <input class="form-control" type="number" name="nit_fac" id="nit_fac" required="true">                        
                                </div>                                
                                <div id="divNit2FacturaDetalle">
                                  
                                </div>                                
                                  
                              </div>

                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Nro. Factura</label>
                            <div class="col-sm-3">
                              <div class="form-group" id="divNroFacFacturaDetalle">
                                  <!-- <label for="number" class="bmd-label-floating" style="color: #4a148c;">Nro. Factura</label>      -->
                                  <input class="form-control" type="number" name="nro_fac" id="nro_fac" required="true"/>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Fecha</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="fecha_fac" class="bmd-label-floating" style="color: #4a148c;">Fecha</label>      -->
                                <input type="text" class="form-control datepicker" name="fecha_fac" id="fecha_fac" value="<?=$fechaActualModal?>">
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Importe</label>
                            <div class="col-sm-3">
                              <div class="form-group" id="divImporteFacturaDetalle">
                                <input class="form-control" type="number" name="imp_fac" id="imp_fac" required="true"/>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Exento</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="exe_fac" class="bmd-label-floating" style="color: #4a148c;">Extento</label>      -->
                                <input class="form-control" type="text" name="exe_fac" id="exe_fac" required="true" value="0" />
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">ICE</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="ice_fac" class="bmd-label-floating" style="color: #4a148c;">ICE</label>      -->
                                <input class="form-control" type="text" name="ice_fac" id="ice_fac" required="true" value="0" />
                              </div>
                             </div>
                          </div>                                                                  
                          <!--No tiene funcion este campo-->
                          <div class="row">                                            
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Tasa Cero</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="taza_fac" class="bmd-label-floating" style="color: #4a148c;">Taza Cero</label>      -->
                                <input class="form-control" type="text" name="taza_fac" id="taza_fac" required="true" value="0" />
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Autorizaci&oacute;n</label>
                            <div class="col-sm-3">
                              <div class="form-group" id="divNroAutoFacturaDetalle">
                                <!-- <label for="aut_fac" class="bmd-label-floating" style="color: #4a148c;">Nro. Autorizaci&oacute;n</label>      -->
                                <input class="form-control" type="text" name="aut_fac" id="aut_fac" required="true"/>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Cod. Control</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="con_fac" class="bmd-label-floating" style="color: #4a148c;">Cod. Control</label>      -->
                                <input class="form-control" type="text" name="con_fac" id="con_fac" required="true"/>
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
                                <input type="text" class="form-control" name="razon_fac" id="razon_fac">
                                
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
                          <input class="form-control" type="number" readonly step="0.001" name="retencion_montoimporte" id="retencion_montoimporte"/>
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
                              <input class="form-check-input" type="radio" id="retencion<?=$codigoX?>" name="retenciones" <?=($contRetencion==0)?"checked":"";?> value="<?=$codigoX?>@<?=$nombreX?>">
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
                        <button type="button" class="btn btn-info btn-round" onclick="agregarRetencionSolicitud()">Agregar</button>
                  </div>
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->
<!-- small modal -->
<div class="modal fade modal-arriba modal-primary" id="modalAgregarProveedor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
            <div class="card-header card-header-warning card-header-icon">
                <div class="card-icon">
                    <i class="material-icons text-dark">ballot</i>
                 </div>
                  <h4 class="card-title">Proveedor</h4>
            </div>
            <div class="card-body">
                 <div id="datosProveedorNuevo">
                   
                 </div> 
                <div class="form-group float-right">
                        <button type="button" onclick="guardarDatosProveedor()" class="btn btn-info btn-round">Agregar</button>
                </div>
          </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->
<script>$('.selectpicker').selectpicker("refresh");</script>