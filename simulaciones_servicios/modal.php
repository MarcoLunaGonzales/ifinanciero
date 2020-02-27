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
<div class="modal fade modal-arriba modal-primary" id="modalSimulacionCuentas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg col-sm-12">
    <div class="modal-content card">
                <div class="card-header card-header-danger card-header-text">
                  <div class="card-text">
                    <h4>Costos Variables</h4>
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <div class="card-body">
                 <div class="card" id="cuentas_simulacion">
                   <?php 
                    include "cargarDetallePlantillaPartida.php";
                   ?>
                 </div>   
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->

<!-- small modal -->
<div class="modal fade modal-arriba modal-primary" id="modalSimulacionCuentasPersonal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
                <div class="card-header card-header-warning card-header-text">
                  <div class="card-text">
                    <h4>Costos Variables Detalle</h4>
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                  <a class="btn btn-success btn-sm btn-fab float-right" href="#" onclick="cambiarModalDetalleVariable()">
                    <i class="material-icons">keyboard_backspace</i>
                  </a>
                </div>
                <div class="card-body">
                 <div class="card" id="cuentas_simulacionpersonal">
                 </div>   
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
                    <h4>Editar Simulaci&oacute;n</h4>
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
                       <label class="col-sm-2 col-form-label">Simulaci&oacute;n</label>
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
<div class="modal fade modal-primary modal-arriba" id="modalEditPlantilla" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
               <div class="card-header card-header-info card-header-text">
                  <div class="card-text">
                    <h4>Editar Plantilla</h4>
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
                           <label class="col-sm-2 col-form-label">D&iacute;as Auditoria</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">
                               <input type="number" min="1" class="form-control" name="modal_diasauditoria" id="modal_diasauditoria" value="">
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
                        $cantidadProductos=explode(",",$productosX);
                      ?>
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Productos <small class="text-muted">(<?=count($cantidadProductos)?>)</small></label>
                       <div class="col-sm-10">
                        <div class="form-group" style="border-bottom: 1px solid #CACFD2">
                          <input type="text" value="" class="form-control tagsinput" name="modal_productos" id="modal_productos" data-role="tagsinput" data-color="warning">                          
                        </div>
                        </div>
                        <!--<label for="">La cantidad m&iacute;nima de productos es 1</label>-->
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
                      <h4 class="font-weight-bold"><center>SERVICIOS <b id="num_tituloservicios"></b></center></h4>
                      <div class="row">
                        <table class="table table-bordered table-condensed table-striped table-sm">
                             <thead>
                                  <tr class="fondo-boton">
                                    <td>#</td>
                                    <td width="30%">Descipci&oacute;n</td>
                                    <td>Cantidad</td>
                                    <td width="17%">Unidad</td>
                                    <td>Monto</td>
                                    <td>Total</td>
                                    <td class="small">Habilitar/Deshabilitar</td>
                                  </tr>
                              </thead>
                              <tbody>
                                <?php 
                                $iii=1;
                               $queryPr="SELECT s.*,t.descripcion as nombre_serv FROM simulaciones_servicios_tiposervicio s, cla_servicios t where s.cod_simulacionservicio=$codigoSimulacionSuper and s.cod_claservicio=t.idclaservicio order by s.codigo";
                               $stmt = $dbh->prepare($queryPr);
                               $stmt->execute();
                               $modal_totalmontopre=0;$modal_totalmontopretotal=0;
                               while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoPre=$rowPre['codigo'];
                                  $codCS=$rowPre['cod_claservicio'];
                                  $tipoPre=$rowPre['nombre_serv'];
                                  $cantidadPre=$rowPre['cantidad'];
                                  $cantidadEPre=$rowPre['cantidad_editado'];
                                  $montoPre=$rowPre['monto'];
                                  $montoPreTotal=$montoPre*$cantidadEPre;
                                  $banderaHab=$rowPre['habilitado'];
                                  $codTipoUnidad=$rowPre['cod_tipounidad'];
                                  if($banderaHab!=0){
                                    $modal_totalmontopre+=$montoPre;
                                    $modal_totalmontopretotal+=$montoPreTotal;
                                  }
                                  $iconServ="";
                                  if(obtenerConfiguracionValorServicio($codCS)==true){
                                    $iconServ="check_circle";
                                  }
                                   ?>
                                   <tr>
                                     <td><?=$iii?></td>
                                     <td class="text-left"><i class="material-icons text-warning"><?=$iconServ?></i><input type="hidden" id="precio_fijo<?=$iii?>" value="<?=$iconServ?>"> <?=$tipoPre?></td>
                                     <td class="text-right">
                                       <input type="number" min="1" id="cantidad_servicios<?=$iii?>" name="cantidad_servicios<?=$iii?>" class="form-control text-info text-right" onchange="calcularTotalFilaServicio(2)" onkeyUp="calcularTotalFilaServicio(2)" value="<?=$cantidadEPre?>">
                                     </td>
                                     <!--<td>
                                      <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="cantidad_servicios<?=$iii?>" id="cantidad_servicios<?=$iii?>" onchange="calcularTotalFilaServicio(2)">
                                          <?php 
                                             for ($hf=1; $hf<=$cantidadPre; $hf++) {
                                              if($hf==$cantidadEPre){
                                                ?><option value="<?=$hf?>" selected><?=$hf?></option><?php
                                              }else{
                                                  ?><option value="<?=$hf?>"><?=$hf?></option><?php
                                              }      
                                             }
                                          ?>
                                      </select>
                                     </td>-->
                                     <td>
                                      <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="unidad_servicios<?=$iii?>" id="unidad_servicios<?=$iii?>" onchange="calcularTotalFilaServicio(2)">
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
                                       <input type="number" id="modal_montoserv<?=$iii?>" name="modal_montoserv<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalFilaServicio(2)" onkeyUp="calcularTotalFilaServicio(2)" value="<?=$montoPre?>" step="0.01">
                                     </td>
                                     <td class="text-right">
                                       <input type="hidden" id="modal_codigoservicio<?=$iii?>" value="<?=$codigoPre?>">
                                       <input type="number" id="modal_montoservtotal<?=$iii?>" name="modal_montoservtotal<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalFilaServicio(1)" onkeyUp="calcularTotalFilaServicio(1)" value="<?=$montoPreTotal?>" step="0.01">
                                     </td>
                                     <td>
                                       <div class="togglebutton">
                                               <label>
                                                 <input type="checkbox" <?=($banderaHab==1)?"checked":"";?> onchange="activarInputMontoFilaServicio('<?=$iii?>')">
                                                 <span class="toggle"></span>
                                               </label>
                                       </div>
                                     </td>
                                   </tr>
                                  <?php
                                  $iii++; 
                                  } ?>
                                  <tr>
                                     <td colspan="4" class="text-center font-weight-bold">Total</td>
                                     <td id="modal_totalmontoserv" class="text-right"><?=$modal_totalmontopre?></td>
                                     <td id="modal_totalmontoservtotal" class="text-right font-weight-bold"><?=$modal_totalmontopretotal?></td>
                                     <td></td>
                                   </tr>
                              </tbody>
                           </table>
                           <input type="hidden" id="modal_numeroservicio" value="<?=$iii?>">
                      </div>
                      <h4 class="font-weight-bold"><center>HONORARIOS PERSONAL <b id="num_titulopersonal"></b></center></h4>
                      <div class="row">
                        <table class="table table-bordered table-condensed table-striped table-sm">
                             <thead>
                                  <tr class="fondo-boton">
                                    <td>#</td>
                                    <td width="25%">Tipo de Personal</td>
                                    <!--<td width="14%" class="text-center">Regi&oacute;n</td>-->
                                    <td width="8%" class="text-center">Cantidad</td>                                   
                                    <td width="8%">D&iacute;as Aud.</td>
                                    <td>Monto</td>
                                    <td>Total</td>
                                    <td width="10%" class="small">Hab/Des</td>
                                  </tr>
                              </thead>
                              <tbody>
                                <?php 
                                $iii=1;
                               $queryPr="SELECT s.*,t.nombre as tipo_personal FROM simulaciones_servicios_auditores s, tipos_auditor t where s.cod_simulacionservicio=$codigoSimulacionSuper and s.cod_tipoauditor=t.codigo order by s.codigo";
                               $stmt = $dbh->prepare($queryPr);
                               $stmt->execute();
                               $modal_totalmontopre=0;$modal_totalmontopretotal=0;$sumaCantidadPre=0;
                               $modal_totalmontopreext=0;$modal_totalmontopretotalext=0;
                               while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoPre=$rowPre['codigo'];
                                  $tipoPre=$rowPre['tipo_personal'];
                                  $cantidadPre=$rowPre['cantidad'];
                                  $diasPre=$rowPre['dias'];
                                  $cantidadEPre=$rowPre['cantidad_editado'];
                                  $montoPre=$rowPre['monto'];
                                  $montoPreext=$rowPre['monto_externo'];

                                  $codExtLoc=$rowPre['cod_externolocal'];
                                  if($codExtLoc==1){
                                    $montoPreSi=$montoPre;
                                  }else{
                                    $montoPreSi=$montoPreext;
                                  }

                                  $montoPreTotal=$montoPreSi*$cantidadEPre*$diasPre;
                                  //$montoPreTotalext=$montoPreext*$cantidadEPre*$diasPre;
                                  $banderaHab=$rowPre['habilitado'];
                                  if($banderaHab!=0){
                                    $modal_totalmontopre+=$montoPreSi;
                                    $modal_totalmontopretotal+=$montoPreTotal;
                                    //$modal_totalmontopreext+=$montoPreext;
                                    //$modal_totalmontopretotalext+=$montoPreTotalext;
                                  }
                                   ?>
                                   <tr>
                                     <td><?=$iii?></td>
                                     <td class="small"><?=$tipoPre?><input type="hidden" id="local_extranjero<?=$iii?>" value="<?=$codExtLoc?>"></td>
                                     <!--<td>
                                      <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="local_extranjero<?=$iii?>" id="local_extranjero<?=$iii?>" onchange="montarMontoLocalExternoTabla(<?=$iii?>)">
                                          <?php 
                                              if($codExtLoc==1){                  
                                                ?><option value="1" selected>BOLIVIA</option>
                                                  <option value="0">EXTRANJERO</option>
                                                <?php
                                              }else{
                                                ?><option value="1">BOLIVIA</option>
                                                  <option value="0" selected>EXTRANJERO</option>
                                                <?php
                                              }
                                          ?>
                                      </select>
                                     </td>-->
                                     <td>
                                      <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="cantidad_personal<?=$iii?>" id="cantidad_personal<?=$iii?>" onchange="calcularTotalPersonalServicio(2)">
                                          <?php 
                                             for ($hf=1; $hf<=$cantidadPre; $hf++) {
                                              if($hf==$cantidadEPre){
                                                $sumaCantidadPre+=$cantidadPre;
                                                ?><option value="<?=$hf?>" selected><?=$hf?></option><?php
                                              }else{
                                                  ?><option value="<?=$hf?>"><?=$hf?></option><?php
                                              }      
                                             }
                                          ?>
                                      </select>
                                     </td>
                                     <td class="text-center">
                                       <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="dias_personal<?=$iii?>" id="dias_personal<?=$iii?>" onchange="calcularTotalPersonalServicio(2)">
                                          <?php 
                                             for ($hf=1; $hf<=$diasSimulacion; $hf++) {
                                              if($hf==$diasPre){
                                                ?><option value="<?=$hf?>" selected><?=$hf?></option><?php
                                              }else{
                                                  ?><option value="<?=$hf?>"><?=$hf?></option><?php
                                              }      
                                             }
                                          ?>
                                      </select>
                                     </td>
                                     <td class="text-right">
                                       <input type="number" id="modal_montopre<?=$iii?>" name="modal_montopre<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalPersonalServicio(2)" onkeyUp="calcularTotalPersonalServicio(2)" value="<?=$montoPreSi?>" step="0.01">
                                       <input type="hidden" id="modal_montopreext<?=$iii?>" value="<?=$montoPreext?>">
                                       <input type="hidden" id="modal_montopreloc<?=$iii?>" value="<?=$montoPre?>">
                                     </td>
                                     <td class="text-right">
                                       <input type="hidden" id="modal_codigopersonal<?=$iii?>" value="<?=$codigoPre?>">
                                       <input type="number" id="modal_montopretotal<?=$iii?>" name="modal_montopretotal<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalPersonalServicio(1)" onkeyUp="calcularTotalPersonalServicio(1)" value="<?=$montoPreTotal?>" step="0.01">
                                     </td>
                                     <td>
                                       <div class="togglebutton">
                                               <label>
                                                 <input type="checkbox" <?=($banderaHab==1)?"checked":"";?> onchange="activarInputMontoPersonalServicio('<?=$iii?>')">
                                                 <span class="toggle"></span>
                                               </label>
                                       </div>
                                     </td>
                                   </tr>
                                  <?php
                                  $iii++; 
                                  } ?>
                                  <tr>
                                     <td colspan="4" class="text-center font-weight-bold">Total</td>
                                     <td id="modal_totalmontopre" class="text-right"><?=$modal_totalmontopre?></td>
                                     <td id="modal_totalmontopretotal" class="text-right font-weight-bold"><?=$modal_totalmontopretotal?></td>
                                     <!--<td id="modal_totalmontopreext" class="text-right"><?=$modal_totalmontopreext?></td>
                                     <td id="modal_totalmontopretotalext" class="text-right font-weight-bold"><?=$modal_totalmontopretotalext?></td>-->
                                     <td></td>
                                   </tr>
                              </tbody>
                           </table>
                           <input type="hidden" id="modal_numeropersonal" value="<?=$iii?>">
                           <input type="hidden" id="modal_cantidadpersonal" value="<?=$sumaCantidadPre?>">
                      </div>
                      <hr>
                       
                      <div class="form-group float-right">
                        <button type="button" id="boton_guardarplan" class="btn btn-default" onclick="guardarDatosPlantillaServicio(this.id)">Guardar</button>
                      </div> 
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->

<!-- small modal -->
<div class="modal fade modal-arriba modal-primary" id="modalCargarDetalleCosto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg col-sm-12">
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