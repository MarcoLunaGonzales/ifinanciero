<!-- small modal -->
<div class="modal fade modal-primary" id="modalNuevoPersonal" style="z-index: 100000 !important;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
               <div class="card-header card-header-primary card-header-text">
                  <div class="card-text">
                    <h4>AGREGAR PERSONAL <label id="titulo_modal_honorarios"></label></h4>      
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <div class="card-body">
                    <input type="hidden" class="form-control" name="anio_personal" id="anio_personal" value="-1">
                    <input type="hidden" class="form-control" name="valor_personal" id="valor_personal" value="0">
                      <div class="row">
                          <label class="col-sm-2 col-form-label">Personal</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">
                               <select class="selectpicker form-control form-control-sm" data-size="6" data-live-search="true" name="modal_personalhonorario" id="modal_personalhonorario" data-style="fondo-boton fondo-boton-active">
                                    <option disabled selected="selected" value="">--PERSONAL--</option>
                                    <?php 
                                     $stmt3 = $dbh->prepare("SELECT codigo,nombre,abreviatura from tipos_auditor where cod_estadoreferencial=1 order by nro_orden");
                                     $stmt3->execute();
                                     while ($rowServ = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                                      $codigoServX=$rowServ['codigo'];
                                      $nombreServX=$rowServ['nombre'];
                                      $abrevServX=$rowServ['abreviatura'];
                                      ?><option value="<?=$codigoServX;?>"><?=$nombreServX?></option><?php 
                                     }
                                    ?>
                                  </select>
                             </div>
                           </div>
                           <label class="col-sm-2 col-form-label">D&iacute;as</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">
                                <input type="number" min="0" class="form-control" name="modal_diaspersonalhonorario" id="modal_diaspersonalhonorario" value="1">
                             </div>
                           </div>           
                      </div>
                    
                      <hr>
                      <div class="form-group float-right">
                        <button type="button"  class="btn btn-default" onclick="agregarNuevoPersonalSimulacionModal(<?=$inicioAnio?>,<?=$ibnorcaC?>)">Guardar</button>
                      </div> 
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->


<!-- small modal -->
<div class="modal fade modal-primary" id="modal_atributo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
               <div class="card-header card-header-primary card-header-text">
                  <div class="card-text">
                    <?php if($codAreaX==39){
                       ?><h4 id="titulo_modal_atributo">PRODUCTOS</h4><?php
                    }else{
                      ?><h4 id="titulo_modal_atributo">SITIOS</h4><?php
                    }
                    ?>
                    
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <div class="card-body">
                       <input type="hidden" class="form-control" name="modal_fila" id="modal_fila" value="-1">
                      <div class="row">
                          <label class="col-sm-2 col-form-label" id="lbl_nombre_atributo">Nombre</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">
                               <input type="text" class="form-control" name="modal_nombre" id="modal_nombre" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                             </div>
                           </div>
                           <?php if($codAreaX==39){
                                    ?>
                           <div class="row col-sm-6" id="div_marca">
                             <label class="col-sm-2 col-form-label">Marca</label>
                             <div class="col-sm-10">                     
                              <div class="form-group">
                               <input type="text" class="form-control" name="modal_marca" id="modal_marca" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                               </div>
                             </div>  
                           </div> 
                            <?php
                                 }else{
                                  ?><div class="row col-sm-6 d-none" id="div_marca"></div><?php
                                 }
                            ?>           
                      </div>
                      <?php if($codAreaX==39){
                                    ?>
                      <div id="div_norma">
                        <div class="row">
                           <label class="col-sm-2 col-form-label">Nº Sello</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">
                               <input type="number" class="form-control" name="modal_sello" id="modal_sello" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                             </div>
                           </div>  
                      </div>
                      <div class="row">
                          <label class="col-sm-2 col-form-label">Norma</label>
                           <div class="col-sm-9">                     
                             <div class="form-group"><!--style="border-bottom: 1px solid #CACFD2"-->          
                               <!--<input type="text" class="form-control tagsinput" data-role="tagsinput" data-color="info" name="modal_norma" id="modal_norma" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">-->
                               <select class="selectpicker form-control form-control-sm" name="normas[]" id="normas" multiple data-style="btn btn-warning" data-live-search="true" data-size="6" data-actions-box="true" required>
                                <?php
                                 $stmt = $dbh->prepare("SELECT * from normas order by abreviatura");
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
                       <div class="row">
                          <label class="col-sm-2 col-form-label">Otra Norma</label>
                           <div class="col-sm-9">                     
                             <div class="form-group" style="border-bottom: 1px solid #CACFD2">       
                                <input type="text" class="form-control tagsinput" data-role="tagsinput" data-color="info" name="modal_norma" id="modal_norma" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                             </div>
                           </div>  
                      </div>    
                      </div>
                      <div class="row col-sm-12" id="div_pais">
                          <div class="row col-sm-12">
                       <label class="col-sm-2 col-form-label">Pais</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          <select name="pais_empresa" id="pais_empresa" data-size="6" data-live-search="true" onchange="seleccionarDepartamentoServicioSitioModal()" class="form-control form-control-sm selectpicker" data-style="btn btn-info">
                            <option disabled selected value="">--SELECCIONE--</option>
                             <?php
                                  foreach ($lista->lista as $listas) {
                                      echo "<option value='".$listas->idPais."####".strtoupper($listas->paisNombre)."'>".$listas->paisNombre."</opction>";
                                  }?>
                          </select>
                        </div>
                       </div>
                      </div>
                      <div class="row col-sm-12">
                       <label class="col-sm-2 col-form-label">Dep / Est</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          <select name="departamento_empresa"  data-size="6" data-live-search="true" onchange="seleccionarCiudadServicioSitioModal()" id="departamento_empresa" class="form-control form-control-sm selectpicker" data-style="btn btn-info">
                          </select>
                        </div>
                       </div>
                      </div>
                      <div class="row col-sm-12">
                       <label class="col-sm-2 col-form-label">Ciudad</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          <select name="ciudad_empresa" onchange="" data-size="6" data-live-search="true" id="ciudad_empresa" class="form-control form-control-sm selectpicker" data-style="btn btn-success">
                          </select>
                        </div>
                       </div>
                      </div>  
                      </div>  
                            <?php
                                 }else{
                              ?>
                             <div class="row col-sm-12" id="div_pais">
                          <div class="row col-sm-12">
                       <label class="col-sm-2 col-form-label">Pais</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          <select name="pais_empresa" id="pais_empresa" data-size="6" onchange="seleccionarDepartamentoServicioSitioModal()" class="form-control form-control-sm selectpicker" data-style="btn btn-info">
                            <option disabled selected value="">--SELECCIONE--</option>
                             <?php
                                  foreach ($lista->lista as $listas) {
                                      echo "<option value='".$listas->idPais."####".strtoupper($listas->paisNombre)."'>".$listas->paisNombre."</opction>";
                                  }?>
                          </select>
                        </div>
                       </div>
                      </div>
                      <div class="row col-sm-12">
                       <label class="col-sm-2 col-form-label">Dep / Est</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          <select name="departamento_empresa"  data-size="6" onchange="seleccionarCiudadServicioSitioModal()" id="departamento_empresa" class="form-control form-control-sm selectpicker" data-style="btn btn-info">
                          </select>
                        </div>
                       </div>
                      </div>
                      <div class="row col-sm-12">
                       <label class="col-sm-2 col-form-label">Ciudad</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          <select name="ciudad_empresa" onchange="" data-size="6" id="ciudad_empresa" class="form-control form-control-sm selectpicker" data-style="btn btn-success">
                          </select>
                        </div>
                       </div>
                      </div>  
                      </div>  
                              <?php    
                                 }
                            ?> 
                      <div class="row" id="div_direccion">
                          <label class="col-sm-2 col-form-label">Direcci&oacute;n</label>
                           <div class="col-sm-10">                     
                             <div class="form-group">
                               <input type="text" class="form-control" name="modal_direccion" id="modal_direccion" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                             </div>
                           </div>  
                      </div>
                     <?php 
                      if($codAreaX!=39){
                     ?>
                     <div class="row" id="div_sitios_dias">
                      <h4 class="font-weight-bold div-center"><center>DIAS - SITIOS</center></h4>
                       <table class="table table-bordered table-condensed table-striped table-sm">
                             <thead>
                                  <tr class="fondo-boton">
                                   <?php
                                   for ($an=$inicioAnio; $an<=$anioGeneral; $an++) { 
                                          $active="";
                                          $etapas="Año ".$an;

                                          if($codAreaX!=39){
                                            $etapas="Año ".$an."(SEGUIMIENTO ".($an-1).")";
                                            if($an==0||$an==1){
                                              if($an==1){
                                                $etapas="Año 1 (ETAPA ".($an+1)." / RENOVACIÓN)"; 
                                              }else{
                                                $etapas="Año 1 (ETAPA ".($an+1).")"; 
                                              }
                                             
                                            }
                                          }
                                        ?>
                                      <td><?=$etapas?></td>
                                      <?php
                                        }
                                     ?> 
                                  </tr>
                              </thead>
                              <tbody id="body_sitios_dias">
                                  <tr class="">
                                   <?php
                                   for ($an=$inicioAnio; $an<=$anioGeneral; $an++) { 
                                        ?>
                                      <td><input type="number" id="modal_dias_sitio<?=$an?>" name="modal_dias_sitio<?=$an?>" class="form-control text-primary text-right"></td>
                                      <?php
                                        }
                                     ?> 
                                  </tr>
                              </tbody>
                           </table>
                     </div>  
                       <?php
                      }
                     ?>
                      <hr>
                      <div class="form-group float-right">
                        <button type="button" id="boton_guardarsim" class="btn btn-default" onclick="guardarAtributoItem()">Guardar</button>
                      </div> 
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->

<!-- small modal -->
<div class="modal fade modal-mini modal-primary" id="modalAlert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-small">
    <div id="modalAlertStyle" class="modal-content">
      <div class="modal-header">
        <i class="material-icons" data-notify="icon">notifications_active</i>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="material-icons">clear</i></button>
      </div>
      <div class="modal-body">
        <div id="msgError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-link" data-dismiss="modal">Aceptar
          <div class="ripple-container"></div>
        </button>
      </div>
    </div>
  </div>
</div>
<!--    end small modal -->
<!-- small modal -->
<div class="modal fade modal-mini modal-primary" id="modalGuardar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-small">
    <div id="modalAlertStyle" class="modal-content">
      <div class="modal-header">
        <i class="material-icons" data-notify="icon">notifications_active</i>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="material-icons">clear</i></button>
      </div>
      <div class="modal-body">
        <div id="msgError2"></div>
      </div>
      <div class="modal-footer">
        <button onclick="guardarSimulacionAjax()" class="btn btn-default" data-dismiss="modal">Guardar
          <div class="ripple-container"></div>
        </button>
      </div>
    </div>
  </div>
</div>
<!--    end small modal -->

<div class="modal fade modal-mini modal-primary" id="modalGuardarSend" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-small">
    <div id="modalAlertStyle" class="modal-content">
      <div class="modal-header">
        <i class="material-icons" data-notify="icon">notifications_active</i>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="material-icons">clear</i></button>
      </div>
      <div class="modal-body">
        <div id="msgError3"></div>
      </div>
      <div class="modal-footer">
        <a href="../<?=$urlList;?>" class="btn btn-default">Ir a la lista
          <div class="ripple-container"></div>
        </a>
      </div>
    </div>
  </div>
</div>
<!--    end small modal -->

<div class="modal fade modal-mini modal-primary" id="modalSend" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-small">
    <div id="modalAlertStyle" class="modal-content bg-warning text-dark">
      <div class="modal-header">
        <i class="material-icons" data-notify="icon">notifications_active</i>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="material-icons">clear</i></button>
      </div>
      <div class="modal-body">
        <div id="msgError4"></div>
      </div>
      <div class="modal-footer">
        <a href="../<?=$urlList;?>" class="btn btn-default">Ir a la lista
          <div class="ripple-container"></div>
        </a>
      </div>
    </div>
  </div>
</div>
<!--    end small modal -->


<!-- small modal -->
<div class="modal fade modal-arriba modal-primary" id="modalSimulacionCuentas0" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice" style="max-width: 80% !important;">
    <div class="modal-content card">
                <div class="card-header card-header-success card-header-text">
                  <div class="card-text">
                    <h4>Costos Variables - Honorarios</h4>
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <div class="card-body">
                 <div class="card" id="cuentas_simulacion0">
                  <div class="form-group float-right">
                     <button class="btn btn-success" id="guardar_cuenta0" onclick="cargarDetallesCostosVariablesTodosLosAnios(<?=$inicioAnio?>,<?=$ibnorcaC?>)">Editar Detalle por Persona</button>
                   </div>
                  <?php
                   for ($ann=$inicioAnio; $ann <=$anioGeneral ; $ann++) { 
                     $tituloItem="Año ".$ann;
                      if($codAreaX!=39){
                        $tituloItem="Año ".$ann."(SEGUIMIENTO ".($ann-1).")";
                        if($ann==0||$ann==1){
                          if($ann==1){
                            $tituloItem="Año 1 (ETAPA ".($ann+1)."/ RENOVACIÓN)"; 
                          }else{
                            $tituloItem="Año 1 (ETAPA ".($ann+1).")"; 
                          }   
                        }
                      }
                   include "cargarDetallePlantillaPartida.php";   
                    }
                    ?>  
                   <!--<div class="form-group float-right">
                     <button class="btn btn-success" id="guardar_cuenta0" onclick="cargarDetallesCostosVariablesTodosLosAnios(<?=$inicioAnio?>,<?=$ibnorcaC?>)">Editar Detalle por Persona</button>
                   </div>--> 
                 </div>   
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->
  


<!-- small modal -->
<div class="modal fade modal-arriba modal-primary" style="overflow-y:auto !important;" id="modalSimulacionCuentasPersonal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width: 100% !important;">
    <div class="modal-content card">
       
                <div class="card-header card-header-primary card-header-text">
                  <div class="card-text">
                    <h4>Costos Variables - Honorarios <small>Detalle</small> <b id="titulo_modaldetalleslista"></b></h4>
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                  <a class="btn btn-success btn-sm btn-fab float-right" href="#" onclick="cambiarModalDetalleVariable()">
                    <i class="material-icons">keyboard_backspace</i>
                  </a>
                  <?php 
                   if($codAreaX==38){
                    ?><a href="../<?=$urlDatosSitios?>?cod=<?=$codigoSimulacionSuper?>" class="btn btn-warning text-dark btn-sm float-right" target="_blank"><i class="material-icons">link</i> Ver Sitios</a><?php
                   }
                  ?>
                  
                </div>
                <style>
               #cuentas_simulacionpersonal input:read-only {
                background-color: #BDC3C7  !important;
                }
               </style>
                <div class="card-body">
                 <div class="card" id="cuentas_simulacionpersonal">
                 </div>
                 <div class="col-sm-6 div-center">
                 <table class="table table-bordered table-striped table-condensed">
                  <tr class="fondo-boton">
                    <td colspan="2" width="25%">TOTAL HONORARIOS</td>
                    <td colspan="2" width="25%">TOTAL COSTOS VARIABLES</td>
                    <td colspan="2" width="50%">TOTAL C. VARIABLES + HONORARIOS</td>
                  </tr>
                  <tr class="fondo-boton fondo-boton-active">
                    <td>BOB</td>
                    <td>USD</td>
                    <td>BOB</td>
                    <td>USD</td>
                    <td>BOB</td>
                    <td>USD</td>
                  </tr>
                   <tr>
                     <td class="text-right" id="total_honorarios_modal"></td>
                     <td class="text-right" id="total_honorarios_modalUSD"></td>
                     <td class="text-right" id="total_variables_modal"></td>
                     <td class="text-right" id="total_variables_modalUSD"></td>
                     <td class="text-right font-weight-bold" id="total_variables_hon_modal"></td>
                     <td class="text-right font-weight-bold" id="total_variables_hon_modalUSD"></td>
                   </tr>
                 </table>
                   
                 </div>
                 <div class="col-sm-12 text-right">
                    <div class="form-group">
                        <button class="btn btn-success" id="guardar_cuenta" onclick="guardarCuentasSimulacionAjaxGenericoServicioAuditorTodos(<?=$inicioAnio?>);">Guardar Cambios</button>
                    </div> 
                 </div>
                 <p class="text-muted"><small>USD: Dólar, BOB: Bolivianos, D: Días, C: Cantidad, T BOB: Total en Bolivianos, T USD: Total en Dólares, Hab/Des: Habilitado/Deshabilitado.</small></p>    
                </div>    
      </div>  
    </div>
  </div>
<!--    end small modal -->

<!-- small modal -->
<div class="modal fade modal-primary" id="modalEditSimulacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
               <div class="card-header card-header-success card-header-text">
                  <div class="card-text">
                    <h4>Editar Propuesta</h4>
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <div class="card-body">

                      <div class="row">
                          <label class="col-sm-2 col-form-label">Nombre</label>
                           <div class="col-sm-10">                     
                             <div class="form-group">
                               <input type="text" class="form-control" name="modal_nombresim" id="modal_nombresim" value="">
                             </div>
                           </div>  
                      </div> 
                      <!--<div class="row">
                       <label class="col-sm-2 col-form-label">Propuesta</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                             <select class="selectpicker form-control" name="modal_tiposim" id="modal_tiposim" data-style="btn btn-success">
                               <option value="1">IBNORCA</option>
                               <option value="2">FUERA DE IBNORCA</option> 
                             </select>
                         </div>
                        </div>
                      </div>-->
                      <hr>
                      <div class="form-group float-right">
                        <button type="button" id="boton_guardarsim" class="btn btn-default" onclick="guardarDatosSimulacion(this.id)">Guardar</button>
                      </div> 
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->

<!-- small modal -->
<div class="modal fade modal-primary" id="modalEditPlantilla" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice" style="max-width: 90% !important;">
    <div class="modal-content card">
               <div class="card-header card-header-info card-header-text">
                  <div class="card-text">
                    <h4>Editar Propuesta</h4>
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <div class="card-body">

                      <div class="row">
                          <label class="col-sm-2 col-form-label">Utilidad M&iacute;nima %</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">
                               <input type="number" step="0.01" class="form-control" name="modal_utibnorca" id="modal_utibnorca" value="">
                             </div>
                           </div>  
                           <label class="col-sm-2 col-form-label">Area</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">
                               <input type="text" readonly class="form-control" name="modal_area" id="modal_area" value="<?=$areaX?>">
                             </div>
                           </div>

                          <!--<label class="col-sm-2 col-form-label">UT Min. Fuera %</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">-->
                               <input type="hidden" step="0.01" class="form-control" name="modal_utifuera" id="modal_utifuera" value="">
                             <!--</div>
                           </div>--> 
                      </div>
                      <?php 
                        if($codAreaX==39){
                          $cantidadProductos=explode(",",$productosX);
                         ?>
                     <div class="row">
                       <label class="col-sm-2 col-form-label">Productos <!--<small class="text-muted">(<?=count($cantidadProductos)?>)</small>--></label>
                       <div class="col-sm-8">
                        <div class="form-group" style="border-bottom: 1px solid #CACFD2">
                          <input type="hidden" value="" class="form-control tagsinput" name="modal_productos" id="modal_productos" data-role="tagsinput" data-color="warning">
                          <div id="productos_div" class=""></div>
                          <div id="divResultadoListaAtributosProd">
                            <div class="">
                              <center><h4><b>SIN REGISTROS</b></h4></center>
                            </div>
                          </div>                          
                        </div>
                        </div>
                        <div class="col-sm-2">
                           <button title="Agregar Sitio" type="button" name="add" class="btn btn-warning btn-round btn-fab" onClick="agregarAtributoAjax()"><i class="material-icons">add</i>
                            </button>
                        </div>
                      </div>
                         <?php
                        }else{
                            if($codAreaX==38){
                              $cantidadSitios=explode(",",$sitiosX);
                              
                         ?>
                     <div class="row">
                       <!--<label class="col-sm-2 col-form-label">Sitios <small class="text-muted">(<?=count($cantidadSitios)?>)</small></label>-->
                       <div class="col-sm-12">
                          <div class="btn-group  float-right">
                            <button title="Agregar Sitio" type="button" name="add" class="btn btn-sm btn-warning btn-round btn-fab" onClick="agregarAtributoAjax()"><i class="material-icons">add</i>
                            </button>
                              <button title="Agregar Auditor" type="button" class="btn btn-sm btn-primary btn-round dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">add</i> EA
                              </button>
                              <div class="dropdown-menu">
                                <?php 
                                for ($i=$inicioAnio; $i <= $anioGeneral; $i++) {
                                  $etapas="Seg ".($i-1);
                                  if($i==0||$i==1){
                                        if($i==1){
                                          $etapas="Et ".($i+1)." / REN"; 
                                        }else{
                                             $etapas="Et ".($i+1)."";                                                  
                                        }
                                  }
                                  ?>
                                  <a href="#" class="dropdown-item" onClick="mostrarNuevoPersonalModal(<?=$i?>,'<?=$etapas?>',1)">
                                    <?=$etapas?>
                                 </a>
                                  <?php
                                } ?>
                               </div>
                           
                        </div>
                        <div class="" style="border-bottom: 1px solid #CACFD2">
                          <div id="productos_div" class="d-none"></div>
                          <input type="hidden" value="" class="form-control tagsinput" name="modal_sitios" id="modal_sitios" data-role="tagsinput" data-color="warning">                          
                          <div id="divResultadoListaAtributos">
                            <div class="">
                              <center><h4><b>SIN REGISTROS</b></h4></center>
                            </div>
                          </div>
                        </div>
                        </div>
                        
                      </div>
                         <?php
                            }else{
                             //otro servicio
                            }
                          }
                        ?>
                      
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Cliente</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          <input type="text" readonly value="<?=$nombreClienteX?>" class="form-control" name="modal_nombrecliente" id="modal_nombrecliente">                          
                        </div>
                        </div>
                      </div>
                      <!--<div class="row">
                          <label class="col-sm-3 col-form-label">N&uacute;mero de Personal</label>
                           <div class="col-sm-8">                     
                             <div class="form-group">-->
                               <input type="hidden" class="form-control" min="1" readonly name="modal_alibnorca" id="modal_alibnorca" value="">
                             <!--</div>
                           </div> --> 

                          <!--<label class="col-sm-2 col-form-label">Personal Fuera</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">-->
                               <input type="hidden" class="form-control" min="1" name="modal_alfuera" id="modal_alfuera" value="">
                             <!--</div>
                           </div> 
                      </div> -->

                      
                      <?php 
                       /*Aqui poner los servicios*/
                       
                      ?>
                      
                      <div>
                        <hr>
                        <br>
                         <div class="content">
                          <div class="">
                    <?php $an=0; $totalesAuditores=0;?>
                    <!--INICIO DE SERVICIOS-->
                    <!--<h4 class="font-weight-bold"><center><?=$etapas?> SERVICIOS</center></h4>-->
                      <div class="row" id="modal_contenidoservicios<?=$an?>">
                        <table class="table table-bordered table-condensed table-striped table-sm">
                             <thead>
                                  <tr class="fondo-boton">
                                    <td>#</td>
                                    <td width="6%">Año</td>
                                    <td width="20%"> SERVICIOS</td>
                                    <td width="20%"> DESCRIPCIÓN</td>
                                    <td width="5%">Cantidad</td>
                                    <td width="5%">Unidad</td>
                                    <td>Monto BOB</td>
                                    <td>Monto USD</td>
                                    <td>Total BOB</td>         
                                    <td>Total USD</td>
                                    <td class="small">Habilitar/Deshabilitar</td>
                                  </tr>
                              </thead>
                              <tbody id="modal_body_tabla_servicios<?=$an?>">
                                <tr class="bg-plomo">
                                  <td>N</td>
                                  <td>
                                    <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="anio<?=$an?>SSS0" id="anio<?=$an?>SSS0">
                                          <?php 
                                          for ($i=$inicioAnio; $i <= $anioGeneral; $i++) { 
                                             if($codAreaX!=39){
                                            $etapas="Seg ".($i-1);

                                              if($codAreaX!=39){
                                               if($i==0||$i==1){
                                                if($i==1){
                                                $etapas="Et ".($i+1)." / REN"; 

                                                }else{
                                                   $etapas="Et ".($i+1)."";                                                  
                                                }
                                               }
                                              }
                                              
                                              }else{
                                               $etapas="Año ".$i; 
                                              } 
                                             if($i==$codAnioPre){
                                                  ?><option value="<?=$i?>" selected><?=$etapas?></option><?php
                                                }else{
                                                  ?><option value="<?=$i?>"><?=$etapas?></option><?php
                                                }
                                          }
                                          ?>
                                      </select>
                                  </td>
                                  <td><?php 
                                  if($codAreaX==39){
                                    $codigoAreaServ=108;
                                    $idTipoServ=309;
                                  }else{
                                    if($codAreaX==38){
                                      $codigoAreaServ=109; //codigo 109 tcp
                                      $idTipoServ=310;
                                    }else{
                                      $codigoAreaServ=0;
                                      $idTipoServ=309;
                                    }
                                  }
                                ?>
                                  <select class="selectpicker form-control form-control-sm" data-size="6" data-live-search="true" onchange="ponerDescripcionServicio(<?=$an?>)" name="modal_editservicio<?=$an?>" id="modal_editservicio<?=$an?>" data-style="fondo-boton">
                                    <option disabled selected="selected" value="">--SERVICIOS--</option>
                                    <?php 
                                     $stmt3 = $dbh->prepare("SELECT IdClaServicio,Descripcion,Codigo from cla_servicios where (codigo_n1=108 or codigo_n1=109) and vigente=1 and codigo_n1=$codigoAreaServ and idTipo=$idTipoServGlobal order by 2");
                                     $stmt3->execute();
                                     while ($rowServ = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                                      $codigoServX=$rowServ['IdClaServicio'];
                                      $nombreServX=$rowServ['Descripcion'];
                                      $abrevServX=$rowServ['Codigo'];
                                      ?><option value="<?=$codigoServX;?>"><?=$nombreServX?></option><?php 
                                     }
                                    ?>
                                  </select>
                                  </td>
                                  <td class="text-right">
                                       <input type="text" id="descripcion_servicios<?=$an?>SSS0" name="descripcion_servicios<?=$an?>SSS0" class="form-control text-primary text-right" value="">
                                  </td>
                                  <td class="text-right">
                                       <input type="number" min="1" id="cantidad_servicios<?=$an?>SSS0" name="cantidad_servicios<?=$an?>SSS0" class="form-control text-primary text-right" onchange="calcularTotalFilaServicioNuevo(<?=$an?>,2)" onkeyUp="calcularTotalFilaServicioNuevo(<?=$an?>,2)" value="1">
                                  </td>
                                  <td>
                                      <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="unidad_servicios<?=$an?>SSS0" id="unidad_servicios<?=$an?>SSS0" onchange="calcularTotalFilaServicioNuevo(<?=$an?>,2)">
                                          <?php 
                                              $queryUnidad="SELECT * FROM tipos_unidad where cod_estadoreferencial=1 order by codigo";
                                              $stmtUnidad = $dbh->prepare($queryUnidad);
                                              $stmtUnidad->execute();
                                              while ($rowUnidad = $stmtUnidad->fetch(PDO::FETCH_ASSOC)) {
                                                $codigoUnidad=$rowUnidad['codigo'];
                                                $nomUnidad=$rowUnidad['nombre'];
                                                ?><option value="<?=$codigoUnidad?>"><?=$nomUnidad?></option><?php    
                                              }
                                          ?>
                                      </select>
                                     </td>
                                    <td class="text-right">
                                       <input type="number" id="modal_montoserv<?=$an?>SSS0" name="modal_montoserv<?=$an?>SSS0" class="form-control text-primary text-right" onchange="calcularTotalFilaServicioNuevo(<?=$an?>,2)" onkeyUp="calcularTotalFilaServicioNuevo(<?=$an?>,2)" value="0" step="0.01">
                                    </td>
                                    <td class="text-right">
                                       <input type="number" id="modal_montoservUSD<?=$an?>SSS0" name="modal_montoservUSD<?=$an?>SSS0" class="form-control text-primary text-right" onchange="calcularTotalFilaServicioNuevo(<?=$an?>,4)" onkeyUp="calcularTotalFilaServicioNuevo(<?=$an?>,4)" value="0" step="0.01">
                                    </td>
                                     <td class="text-right">
                                       <input type="number" id="modal_montoservtotal<?=$an?>SSS0" name="modal_montoservtotal<?=$an?>SSS0" class="form-control text-primary text-right"  value="0" step="0.01">
                                     </td>
                                     
                                     <td class="text-right">
                                       <input type="number" id="modal_montoservtotalUSD<?=$an?>SSS0" name="modal_montoservtotalUSD<?=$an?>SSS0" class="form-control text-primary text-right" value="0" step="0.01">
                                     </td>
                                  <td>
                                    <div class="btn-group">
                                       <a href="#" class="btn btn-primary btn-sm" id="boton_modalnuevoservicio<?=$an?>" onclick="agregarNuevoServicioSimulacion(<?=$an?>,<?=$codigoSimulacionSuper?>,<?=$codAreaX?>); return false;">
                                         Agregar
                                       </a>
                                     </div>
                                  </td>
                                </tr>
                                <?php 
                                $iii=1;
                               $queryPr="SELECT s.*,t.Descripcion as nombre_serv FROM simulaciones_servicios_tiposervicio s, cla_servicios t where s.cod_simulacionservicio=$codigoSimulacionSuper and s.cod_claservicio=t.IdClaServicio order by t.nro_orden";
                               $stmt = $dbh->prepare($queryPr);
                               $stmt->execute();
                               $modal_totalmontopre=0;$modal_totalmontopretotal=0;
                               while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoPre=$rowPre['codigo'];
                                  $codCS=$rowPre['cod_claservicio'];
                                  $tipoPre=$rowPre['nombre_serv'];
                                  $tipoPreEdit=$rowPre['observaciones'];
                                  $cantidadPre=$rowPre['cantidad'];
                                  $cantidadEPre=$rowPre['cantidad_editado'];
                                  $montoPre=$rowPre['monto'];
                                  $montoPreTotal=$montoPre*$cantidadEPre;
                                  $banderaHab=$rowPre['habilitado'];
                                  $codTipoUnidad=$rowPre['cod_tipounidad'];
                                  $codAnioPre=$rowPre['cod_anio'];
                                  $claseDeshabilitado="hidden";
                                  $claseDeshabilitadoOFF="number";
                                  if($banderaHab!=0){
                                    $modal_totalmontopre+=$montoPre;
                                    $modal_totalmontopretotal+=$montoPreTotal;
                                    $claseDeshabilitado="number";
                                    $claseDeshabilitadoOFF="hidden";
                                  }
                                  $iconServ="";
                                  if(obtenerConfiguracionValorServicio($codCS)==true){
                                    $iconServ="check_circle";
                                  }
                                  $montoPreUSD=number_format($montoPre/$usd,2,".","");
                                  $montoPreTotalUSD=number_format($montoPreTotal/$usd,2,".","");
                                  $montoPre=number_format($montoPre,2,".","");
                                  $montoPreTotal=number_format($montoPreTotal,2,".","");
                                   ?>
                                   <tr>
                                     <td><?=$iii?></td>
                                     <td>
                                        <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="anio<?=$an?>SSS<?=$iii?>" id="anio<?=$an?>SSS<?=$iii?>">
                                          <?php 
                                          for ($i=$inicioAnio; $i <= $anioGeneral; $i++) {
                                          if($codAreaX!=39){
                                            $etapas="Seg ".($i-1);

                                              if($codAreaX!=39){
                                               if($i==0||$i==1){
                                                if($i==1){
                                                $etapas="Et ".($i+1)." / REN"; 

                                                }else{
                                                   $etapas="Et ".($i+1)."";                                                  
                                                }
                                               }
                                              }
                                              
                                              }else{
                                               $etapas="Año ".$i; 
                                              } 
                                             if($i==$codAnioPre){
                                                  ?><option value="<?=$i?>" selected><?=$etapas?></option><?php
                                                }else{
                                                  ?><option value="<?=$i?>"><?=$etapas?></option><?php
                                                }
                                          }
                                          ?>
                                      </select>
                                     </td>
                                     <td class="text-left"><i class="material-icons text-warning"><?=$iconServ?></i><input type="hidden" id="precio_fijo<?=$an?>SSS<?=$iii?>" value="<?=$iconServ?>"> <?=$tipoPre?></td>
                                     <td class="text-right">
                                       <input type="text" id="descripcion_servicios<?=$an?>SSS<?=$iii?>" name="descripcion_servicios<?=$an?>SSS<?=$iii?>" class="form-control text-info text-right" value="<?=$tipoPreEdit?>">
                                     </td>
                                     <td class="text-right">
                                       <input type="number" min="1" id="cantidad_servicios<?=$an?>SSS<?=$iii?>" name="cantidad_servicios<?=$an?>SSS<?=$iii?>" class="form-control text-info text-right" onchange="calcularTotalFilaServicio(<?=$an?>,2)" onkeyUp="calcularTotalFilaServicio(<?=$an?>,2)" value="<?=$cantidadEPre?>">
                                     </td>
                                     <td>
                                      <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="unidad_servicios<?=$an?>SSS<?=$iii?>" id="unidad_servicios<?=$an?>SSS<?=$iii?>" onchange="calcularTotalFilaServicio(<?=$an?>,2)">
                                          <?php 
                                              $queryUnidad="SELECT * FROM tipos_unidad where cod_estadoreferencial=1 order by codigo";
                                              $stmtUnidad = $dbh->prepare($queryUnidad);
                                              $stmtUnidad->execute();
                                              while ($rowUnidad = $stmtUnidad->fetch(PDO::FETCH_ASSOC)) {
                                                $codigoUnidad=$rowUnidad['codigo'];
                                                $nomUnidad=$rowUnidad['nombre'];
                                                if($codigoUnidad==$codTipoUnidad){
                                                  ?><option value="<?=$codigoUnidad?>" selected><?=$nomUnidad?></option><?php
                                                }else{
                                                  ?><option value="<?=$codigoUnidad?>"><?=$nomUnidad?></option><?php
                                                }    
                                              }
                                          ?>
                                      </select>
                                     </td>
                                     <td class="text-right">
                                       <input type="<?=$claseDeshabilitado?>" id="modal_montoserv<?=$an?>SSS<?=$iii?>" name="modal_montoserv<?=$an?>SSS<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalFilaServicio(<?=$an?>,2)" onkeyUp="calcularTotalFilaServicio(<?=$an?>,2)" value="<?=$montoPre?>" step="0.01">
                                       <input type="<?=$claseDeshabilitadoOFF?>" id="modal_montoservOFF<?=$an?>SSS<?=$iii?>" name="modal_montoservOFF<?=$an?>SSS<?=$iii?>" readonly class="form-control text-info text-right" value="0" step="0.01">
                                     </td>
                                     <td class="text-right">
                                       <input type="<?=$claseDeshabilitado?>" id="modal_montoservUSD<?=$an?>SSS<?=$iii?>" name="modal_montoservUSD<?=$an?>SSS<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalFilaServicio(<?=$an?>,4)" onkeyUp="calcularTotalFilaServicio(<?=$an?>,4)" value="<?=$montoPreUSD?>" step="0.01">
                                       <input type="<?=$claseDeshabilitadoOFF?>" id="modal_montoservUSDOFF<?=$an?>SSS<?=$iii?>" name="modal_montoservUSDOFF<?=$an?>SSS<?=$iii?>" readonly class="form-control text-info text-right" value="0" step="0.01">
                                     </td>
                                     <td class="text-right">
                                       <input type="hidden" id="modal_codigoservicio<?=$an?>SSS<?=$iii?>" value="<?=$codigoPre?>">
                                       <input type="<?=$claseDeshabilitado?>" id="modal_montoservtotal<?=$an?>SSS<?=$iii?>" name="modal_montoservtotal<?=$an?>SSS<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right"  value="<?=$montoPreTotal?>" step="0.01"> <!-- onchange="calcularTotalFilaServicio(<?=$an?>,1)" onkeyUp="calcularTotalFilaServicio(<?=$an?>,1)"-->
                                       <input type="<?=$claseDeshabilitadoOFF?>" id="modal_montoservtotalOFF<?=$an?>SSS<?=$iii?>" name="modal_montoservtotalOFF<?=$an?>SSS<?=$iii?>" readonly class="form-control text-info text-right" value="0" step="0.01">
                                     </td>        
                                     <td class="text-right">
                                       <input type="<?=$claseDeshabilitado?>" id="modal_montoservtotalUSD<?=$an?>SSS<?=$iii?>" name="modal_montoservtotalUSD<?=$an?>SSS<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" value="<?=$montoPreTotalUSD?>" step="0.01"> <!--onchange="calcularTotalFilaServicio(<?=$an?>,3)" onkeyUp="calcularTotalFilaServicio(<?=$an?>,3)" -->
                                       <input type="<?=$claseDeshabilitadoOFF?>" id="modal_montoservtotalUSDOFF<?=$an?>SSS<?=$iii?>" name="modal_montoservtotalUSDOFF<?=$an?>SSS<?=$iii?>" readonly class="form-control text-info text-right" value="0" step="0.01">
                                     </td>
                                     <td>
                                       <div class="togglebutton">
                                               <label>
                                                 <input type="checkbox" <?=($banderaHab==1)?"checked":"";?> id="modal_checkserv<?=$an?>SSS<?=$iii?>" onchange="activarInputMontoFilaServicio(<?=$an?>,'<?=$iii?>')">
                                                 <span class="toggle"></span>
                                               </label>
                                       </div>
                                     </td>
                                   </tr>
                                  <?php
                                  $iii++; 
                                  } ?>
                                  
                              </tbody>
                                  <tr>
                                     <td colspan="6" class="text-center font-weight-bold">Total</td>
                                     <td id="modal_totalmontoserv<?=$an?>" class="text-right"><?=number_format($modal_totalmontopre,2, ',', '')?></td>
                                     <td id="modal_totalmontoservUSD<?=$an?>" class="text-right"><?=number_format($modal_totalmontopre/$usd,2,', ','')?></td>
                                     <td id="modal_totalmontoservtotal<?=$an?>" class="text-right font-weight-bold"><?=number_format($modal_totalmontopretotal,2, ',', '')?></td>    
                                     <td id="modal_totalmontoservtotalUSD<?=$an?>" class="text-right font-weight-bold"><?=number_format($modal_totalmontopretotal/$usd,2, ',', '')?></td>
                                     <td></td>
                                   </tr>
                           </table>
                           <input type="hidden" id="modal_numeroservicio<?=$an?>" value="<?=$iii?>">        
                      </div>
                    <!--FIN DE SERVICIOS-->

                   
                          </div>

                      
                         <?php   
                          for ($an=$inicioAnio; $an<=$anioGeneral; $an++) { 
                            if($codAreaX!=39){
                             $etapas="Seguimiento ".($an-1);

                            if($codAreaX!=39){
                              if($an==0||$an==1){
                               $etapas="Etapa ".($an+1).""; 
                              }
                            }
                            }else{
                                $etapas="Año ".$an; 
                            } 
                            $active="";
                            
                            if($an==1){
                              $active="active";
                            }
                                ?>
                          <div class="">

                    

                   
                          </div>
                        <?php
                        $totalesAuditores+=$modal_totalmontopretotal;
                            }

                        ?>  
                        <!--<table class="table table-bordered table-condensed table-striped table-sm">
                             <tr>
                                     <td width="80%" class="text-center font-weight-bold">TOTAL HONORARIOS</td>
                                     <td width="10%" id="suma_totalpre" class="text-right font-weight-bold"><?=number_format($totalesAuditores,2, ',', '')?> Bs.</td>
                                     <td width="10%" id="suma_totalpreUSD" class="text-right font-weight-bold"><?=number_format($totalesAuditores/$usd,2, ',', '')?> USD.</td>
                                   </tr>
                           </table>-->
                         </div>
                      </div>
                      <hr>
                       
                      <div class="form-group float-right">
                        <button type="button" id="boton_guardarplan" class="btn btn-default" onclick="guardarDatosPlantillaServicio(this.id)">Guardar</button>
                      </div> 
                <p class="text-muted"><small>USD: Dolar, BOB: Bolivianos, EA: Equipo Auditor.</small></p> 
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->

<!-- small modal -->
<div class="modal fade modal-arriba modal-primary" id="modalCargarDetalleCosto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice" style="max-width: 90% !important;">
    <div class="modal-content card">
                <div class="card-header card-header-info card-header-text">
                  <div class="card-text">
                    <h4>LISTA DE DETALLES</h4>
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <div class="card-body">
                 <div class="card" id="lista_detallecosto">
                 </div>   
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->

