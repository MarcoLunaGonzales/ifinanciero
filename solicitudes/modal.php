 <?php
 $listaActividad= obtenerActividadesServicioImonitoreo(1); 
 $listaAcc= obtenerAccServicioImonitoreo(1); 
?>
 <select class="selectpicker form-control form-control-sm d-none" name="actividades_detalle" id="actividades_detalle" data-style="btn btn-success">                                  
 <option disabled selected value="">--SELECCIONE ACTIVIDAD--</option>
<?php
    foreach ($listaActividad as $listas) { ?>
      <option value="<?=$listas->codigo?>" class="text-right"><?=$listas->abreviatura?> - <?=substr($listas->nombre, 0, 85)?></option>

<?php }?>
</select>
 <select class="selectpicker form-control form-control-sm d-none" name="acc_detalle" id="acc_detalle" data-style="btn btn-success">                                  
 <option disabled selected value="">--SELECCIONE ACC--</option>
<?php
    foreach ($listaAcc as $listasacc) { ?>
      <option value="<?=$listasacc->codigo?>" class="text-right"><?=$listasacc->abreviatura?> - <?=substr($listasacc->nombre, 0, 85)?></option>

<?php }?>
</select>

<!-- notice modal -->
<div class="modal fade" id="modalActividadesProyecto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
      <div class="card-header card-header-primary card-header-text">
          <div class="card-text">
            <h5>Lista Detalles con Actividades</h5> 
          </div>
          <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
            <i class="material-icons">close</i>
          </button>
      </div>
          <div class="card-body">
            <div class="col-sm-12">
              <div class="row">                      
                   <table class="table table-bordered table-condensed">
                     <thead>
                       <tr class="fondo-boton">
                         <td width="8%">Nro Fila</td>
                         <td width="70%">Actividad</td>
                         <td>Estado</td>
                       </tr>
                     </thead>
                     <tbody id="contenedor_actividadesmodal">
                       
                     </tbody>
                   </table>
                </div>
               <div class="row">                        
              </div>
             </div>                     
             <div class="form-group float-right">
                <button type="button" class="btn btn-warning btn-round" onclick="guardarActividadFilasDetalle()">Asociar Actividades</button>
             </div>
             <p class="text-muted"><small>Las actividades están relacionadas al detalle de la solicitud.</small></p>             
          </div>
    </div>
  </div>
</div>
<!-- end notice modal -->
<!-- notice modal -->
<div class="modal fade" id="modalNuevoCuentaBeneficiario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
      <div class="card-header card-header-success card-header-text">
          <div class="card-text">
            <h5>Nueva Cuenta Beneficiario</h5> 
          </div>
          <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
            <i class="material-icons">close</i>
          </button>
      </div>
      <input type="hidden" name="cod_proveedorbeneficiario" id="cod_proveedorbeneficiario"/>
          <div class="card-body">
            <div class="col-sm-12">
              <div class="row">                      
                    <label class="col-sm-2 col-form-label" style="color: #4a148c;">Proveedor</label>
                    <div class="col-sm-10">
                      <div class="form-group">  
                           <input class="form-control" readonly type="text" name="nombre_proveedorbeneficiario" id="nombre_proveedorbeneficiario" required="true">                                                                                                                       
                       </div>
                    </div>
                </div>
               <div class="row">
                <label class="col-sm-2 col-form-label" style="color: #4a148c;">Banco</label>
                <div class="col-sm-4">
                  <div class="form-group">
                     <select class="selectpicker form-control form-control-sm" name="nuevo_banco" id="nuevo_banco" data-live-search="true" data-size="6"data-style="btn btn-primary">                                  
                        <?php 
                         $stmt3 = $dbh->prepare("SELECT * from bancos where cod_estadoreferencial=1");
                         $stmt3->execute();
                          while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                           $codigoSel=$rowSel['codigo'];
                          $nombreSelX=$rowSel['nombre'];
                          $abrevSelX=$rowSel['abreviaruta'];
                          ?><option value="<?=$codigoSel;?>"><?=$nombreSelX?></option><?php 
                          }
                        ?>
                    </select>
                  </div>
                </div>
                <label class="col-sm-2 col-form-label" style="color: #4a148c;">Cuenta Beneficiario</label>
                <div class="col-sm-4">
                   <div class="form-group" id="">
                        <input class="form-control" type="text" name="nuevo_cuenta_beneficiario" id="nuevo_cuenta_beneficiario" placeholder="123-456-78-90" required="true"/>
                    </div>
                </div>                          
              </div>
               <div class="row">                      
                    <label class="col-sm-2 col-form-label" style="color: #4a148c;">Nombre Completo Beneficiario</label>
                    <div class="col-sm-10">
                      <div class="form-group">  
                           <input class="form-control" type="text" name="nuevo_nombre_beneficiario" id="nuevo_nombre_beneficiario" required="true">                                                                                                                       
                       </div>
                    </div>
                    <!--<label class="col-sm-2 col-form-label" style="color: #4a148c;">Apellido Beneficiario</label>
                    <div class="col-sm-4">
                      <div class="form-group">  -->
                           <input class="form-control" type="hidden" name="nuevo_apellido_beneficiario" id="nuevo_apellido_beneficiario" required="true">                                                                                                                       
                       <!--</div>
                    </div>-->
                </div>
                <div class="mensaje"></div>
             </div>                     
             <div class="form-group float-right">
                <button type="button" class="btn btn-warning btn-round" onclick="guardarNuevoBeneficiario()">Guardar</button>
             </div>         
          </div>
    </div>
  </div>
</div>
<!-- end notice modal -->

<!-- notice modal -->
<div class="modal fade" id="modalTipoPagoSolicitud" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
      <div class="card-header card-header-primary card-header-text">
          <div class="card-text">
            <h5>Forma de Pago</h5> 
          </div>
          <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
            <i class="material-icons">close</i>
          </button>
      </div>
      <input type="hidden" name="fila_pago" id="fila_pago"/>
          <div class="card-body">
            <div class="col-sm-12">
              <div class="row">
                <label class="col-sm-2 col-form-label" style="color: #4a148c;">Tipo Pago (<b class="text-danger">*</b>)</label>
                <div class="col-sm-4">
                  <div class="form-group">
                     <select class="selectpicker form-control form-control-sm" name="tipo_pagoproveedor" id="tipo_pagoproveedor" data-style="btn btn-primary">                                  
                        <?php 
                         $stmt3 = $dbh->prepare("SELECT * from tipos_pagoproveedor where cod_estadoreferencial=1");
                         $stmt3->execute();
                          while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                           $codigoSel=$rowSel['codigo'];
                          $nombreSelX=$rowSel['nombre'];
                          $abrevSelX=$rowSel['abreviaruta'];
                          ?><option value="<?=$codigoSel;?>"><?=$nombreSelX?></option><?php 
                          }
                        ?>
                    </select>
                  </div>
                </div>                       
              </div>
               <div class="row">
                <label class="col-sm-2 col-form-label" style="color: #4a148c;">Cuentas Bancarias</label>
                <div class="col-sm-4">
                  <div class="form-group">
                     <select class="selectpicker form-control form-control-sm" onchange="cargarDatosCuentaBancariaProveedor()" name="cuenta_bancaria" id="cuenta_bancaria" data-size="6" data-live-search="true" data-style="btn btn-primary">
                       
                    </select>
                  </div>
                </div>
                <div class="col-sm-1 float-left">
                      <div class="form-group">                                
                              <a href="#" style="background-color: #0489B1" class="btn btn-round btn-fab btn-sm" onclick="registrarNuevoBeneficiario()">
                                <i class="material-icons" title="Add Proveedor">add</i>
                              </a>
                      </div>
                </div>
                <label class="col-sm-2 col-form-label" style="color: #4a148c;">Cuenta Beneficiario</label>
                <div class="col-sm-3">
                   <div class="form-group" id="">
                        <input class="form-control" type="text" readonly name="cuenta_beneficiario" id="cuenta_beneficiario" required="true"/>
                    </div>
                </div>                          
              </div>
               <div class="row">                      
                    <label class="col-sm-2 col-form-label" style="color: #4a148c;">Nombre Completo Beneficiario (<b class="text-danger">*</b>)</label>
                    <div class="col-sm-10">
                      <div class="form-group">  
                           <input class="form-control" type="text" name="nombre_beneficiario" id="nombre_beneficiario" required="true">                                                                                                                       
                       </div>
                    </div>
                    <div class="col-sm-1"></div>
                    <!--<label class="col-sm-2 col-form-label" style="color: #4a148c;">Apellido Beneficiario (<b class="text-danger">*</b>)</label>
                    <div class="col-sm-3">
                      <div class="form-group"> --> 
                           <input class="form-control" type="hidden" name="apellido_beneficiario" id="apellido_beneficiario" required="true">                                                                                                                       
                       <!--</div>
                    </div>-->
                </div>
                <div class="mensaje"></div>
             </div>                     
             <div class="form-group float-right">
                <button type="button" class="btn btn-warning btn-round" onclick="guardarFormaPagoSolicitud()">Guardar</button>
             </div>         
          </div>
    </div>
  </div>
</div>
<!-- end notice modal -->

<!-- notice modal -->
<div class="modal fade" id="modalDistribucionSolGeneral" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
      <div class="card-header card-header-success card-header-text">
          <div class="card-text">
            <h5>Distribución de Gastos <b id="titulo_distribucion_b"></b> </h5> 
          </div>
          <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
            <i class="material-icons">close</i>
          </button>
      </div>
          <div class="card-body">
            <div class="row col-sm-12">
              <div class="col-sm-12">                                                              
                  <table class="table table-condensed table-bordered">
                    <thead>
                      <tr class="bg-principal text-white">
                        <th>#</th>
                        <th width="45%">Areas</th>
                        <th width="10%">%</th>
                        <th class="bg-info" width="23%">Oficina</th>
                        <th class="bg-info" width="10%">%</th>
                        <th class="bg-info" width="7%">%</th>
                      </tr>
                    </thead>
                    <tbody id="cuerpo_tabladistarea_general">
                      
                    </tbody>
                  </table>
              </div>
              <!--<div class="col-sm-6">                                                              
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
              </div>--> 
             </div>                     
             <div class="form-group float-right">
                <button type="button" class="btn btn-success btn-round" onclick="guardarDistribucionSolicitudRecursoGeneral()">Guardar</button>
             </div>         
          </div>
    </div>
  </div>
</div>
<!-- end notice modal -->


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
                  <table class="table table-condensed table-bordered table-striped">
                    <thead>
                      <tr class="bg-info text-white">
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
<div class="modal fade modal-primary" id="modalFileDet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
      <div class="card-header card-header-info card-header-text">
                  <div class="card-text">
                    <h5>DOCUMENTOS DE RESPALDO</h5>      
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
      </div>

      <div class="card-body">
        <p class="text-muted"><small>Los archivos se subir&aacute;n al servidor cuando se GUARDE la Solicitud de Recurso</small></p> 
        <input type="hidden" id="codigo_fila" value=""/>
        <div class="row col-sm-11 div-center">
              <table class="table table-warning table-bordered table-condensed">
                <thead>
                  <tr>
                    <th class="small" width="30%">Tipo de Documento <a href="#" title="Otro Documento" class="btn btn-primary btn-round btn-sm btn-fab float-left" onClick="agregarFilaArchivosAdjuntosDetalle()"><i class="material-icons">add</i></a></th>
                    <th class="small">Obligatorio</th>
                    <th class="small" width="35%">Archivo</th>
                    <th class="small">Descripción</th>                  
                  </tr>
                </thead>
                <tbody id="tabla_archivosdetalle">
                  <?php
                  $stmtArchivo = $dbh->prepare("SELECT * from ibnorca.vw_plantillaDocumentos where idTipoServicio=2708"); //2708 //2708 localhost
                  $stmtArchivo->execute();
                  $filaA=0;
                  while ($rowArchivo = $stmtArchivo->fetch(PDO::FETCH_ASSOC)) {
                     $filaA++;
                     $codigoX=$rowArchivo['idClaDocumento'];
                     $nombreX=$rowArchivo['Documento'];
                     $ObligatorioX=$rowArchivo['Obligatorio'];
                     $Obli='<i class="material-icons text-danger">clear</i> NO';
                     if($ObligatorioX==1){
                      $Obli='<i class="material-icons text-success">done</i> SI';
                     }
                  ?>
                  <tr>
                    <td class="text-left"><input type="hidden" name="codigo_archivodetalle<?=$filaA?>" id="codigo_archivodetalle<?=$filaA?>" value="<?=$codigoX;?>"><input type="hidden" name="nombre_archivodetalle<?=$filaA?>" id="nombre_archivodetalle<?=$filaA?>" value="<?=$nombreX;?>"><?=$nombreX;?></td>
                    <td class="text-center"><?=$Obli?></td>
                    <td class="text-right">
                      <small id="label_txt_documentos_detalle<?=$filaA?>"></small> 
                      <span class="input-archivo">
                        <input type="file" class="archivo" name="documentos_detalle<?=$filaA?>" id="documentos_detalle<?=$filaA?>"/>
                      </span>
                      <label title="Ningún archivo" for="documentos_detalle<?=$filaA?>" id="label_documentos_detalle<?=$filaA?>" class="label-archivo btn btn-warning btn-sm"><i class="material-icons">publish</i> Subir Archivo
                      </label>
                    </td>    
                    <td><?=$nombreX;?></td>
                  </tr> 
                  <?php
                   }
                  ?>       
                </tbody>
              </table>
              <input type="hidden" value="<?=$filaA?>" id="cantidad_archivosadjuntosdetalle" name="cantidad_archivosadjuntosdetalle">
              <input type="hidden" value="<?=$filaA?>" id="cantidad_archivosadjuntosdetalleFijos" name="cantidad_archivosadjuntosdetalleFijos">
            </div>

     <!--<div class="fileinput fileinput-new col-md-12" data-provides="fileinput">
           <div class="row">
              <div class="col-md-12">
                <div class="border" id="lista_archivosdetalle">Ningun archivo seleccionado</div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <span class="btn btn-info btn-file btn-sm">
                      <span class="fileinput-new">Buscar</span>
                      <span class="fileinput-exists">Cambiar</span>
                      <input type="file" name="archivosDetalle[]" id="archivosDetalle" multiple="multiple"/>
                   </span>
                <a href="#" id="boton_quitararchivos" class="btn btn-danger btn-sm fileinput-exists" onclick="archivosPreviewDetalle(1)" data-dismiss="fileinput"><i class="material-icons">clear</i> Quitar</a>
              </div>
            </div>
           </div>-->
      </div>
      <div class="modal-footer">
        <button type="button" onclick="guardarArchivosDetalleSolicitud()" class="btn btn-success" data-dismiss="modal">Guardar
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
                      <form name="form_facturas" id="form_facturas">
                        <input class="form-control" type="hidden" name="codCuenta" id="codCuenta"/>
                        <div style="padding: 20px;">
                          <div class="row">                      
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">NIT</label>
                            <div class="col-sm-3">
                              <div class="form-group">  
                                <div id="divNitFacturaDetalle">
                                  <input class="form-control" type="text" name="nit_fac" id="nit_fac" required="true">
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
                    <!--<tr>
                          <td align="center" width="20%">
                          <div class="form-check">
                            <label class="form-check-label">
                              <input class="form-check-input" type="radio" id="retencion0" name="retenciones" checked value="0@NINGUNA">
                              <span class="form-check-sign">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div>
                          </td>
                          <td class="text-left">NINGUNA</td>
                        </tr>-->
                     <?php 
                        $stmtRetencion = $dbh->prepare("SELECT * from configuracion_retenciones where cod_estadoreferencial=1 order BY nombre");
                        $stmtRetencion->execute();
                        $contRetencion=0;
                        while ($row = $stmtRetencion->fetch(PDO::FETCH_ASSOC)) {
                           $abrevX=$row['abreviatura'];
                           $nombreX=$row['nombre'];
                           $codigoX=$row['codigo'];
                           $estiloRetencion="";
                           $checkRetencion="";
                           if($codigoX==6){
                             $estiloRetencion="bg-primary text-white";
                             $checkRetencion="checked";
                           }
?>
                        <tr class="<?=$estiloRetencion?>">
                          <td align="center" width="20%">
                          <div class="form-check">
                            <label class="form-check-label">
                              <input class="form-check-input" type="radio" id="retencion<?=$codigoX?>" <?=$checkRetencion?> name="retenciones" value="<?=$codigoX?>@<?=$nombreX?>">
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
                        <button type="button" class="btn btn-info btn-round" onclick="agregarRetencionSolicitud()">GUARDAR</button>
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