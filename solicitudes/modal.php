
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
                       <div class="col-sm-10">
                        <div class="form-group">
                          <input class="form-control" type="text" name="nit_fac" id="nit_fac" required="true"/>
                        </div>
                        </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Nro. Factura</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          <input class="form-control" type="number" name="nro_fac" id="nro_fac" required="true"/>
                        </div>
                        </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Fecha</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          <?php $fechaActualMod=date("d/m/Y")?>
                          <input type="text" class="form-control datepicker" name="fecha_fac" id="fecha_fac" value="<?=$fechaActualMod?>">
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
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Importe</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          <input class="form-control" type="number" name="imp_fac" id="imp_fac" required="true"/>
                        </div>
                        </div>
                      </div>
                      <!--<div class="row">
                       <label class="col-sm-2 col-form-label">Exentos</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          --><input class="form-control" type="hidden" name="exe_fac" id="exe_fac" required="true"/>
                        <!--</div>
                        </div>
                      </div>-->
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Nro. Autorizaci&oacute;n</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          <input class="form-control" type="text" name="aut_fac" id="aut_fac" required="true"/>
                        </div>
                        </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Cod. Control</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          <input class="form-control" type="text" name="con_fac" id="con_fac" required="true"/>
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