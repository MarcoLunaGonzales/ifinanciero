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
                <!--<div class="row col-sm-12">
                    <div class="form-group col-sm-12">
                       <label class="bmd-label-static">Seleccione una Partida Presupuestaria</label>
                       <select class="selectpicker form-control" onchange="cargarCuentasSimulacion(<?=$codigo?>,<?=$ibnorcaC?>)" name="partida_presupuestaria" id="partida_presupuestaria" data-style="btn btn-danger" title="-- Elija una partida --">
                                <option value="0" selected>Todas Las Partidas</option> 
                                <?php
                                 $stmt = $dbh->prepare("SELECT distinct c.cod_partidapresupuestaria as codPartida, p.nombre from cuentas_simulacion c,partidas_presupuestarias p where p.codigo=c.cod_partidapresupuestaria and c.cod_simulacioncostos=$codigo");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codPartida'];
                                  $nombreX=$row['nombre'];
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$nombreX;?></option> 
                                  <?php
                                    }
                                    ?>
                        </select>
                     </div>
                      <div class="col-sm-6" id="lista_precios">
                       </div>
                </div>-->
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
<div class="modal fade modal-primary" id="modalEditPlantilla" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                          <label class="col-sm-3 col-form-label">Utilidad M&iacute;nima %</label>
                           <div class="col-sm-8">                     
                             <div class="form-group">
                               <input type="number" step="0.01" class="form-control" name="modal_utibnorca" id="modal_utibnorca" value="">
                             </div>
                           </div>  

                          <!--<label class="col-sm-2 col-form-label">UT Min. Fuera %</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">-->
                               <input type="hidden" step="0.01" class="form-control" name="modal_utifuera" id="modal_utifuera" value="">
                             <!--</div>
                           </div>--> 
                      </div>
                      <div class="row">
                          <label class="col-sm-3 col-form-label">N&uacute;mero de Personal</label>
                           <div class="col-sm-8">                     
                             <div class="form-group">
                               <input type="number" class="form-control" min="1" readonly name="modal_alibnorca" id="modal_alibnorca" value="">
                             </div>
                           </div>  

                          <!--<label class="col-sm-2 col-form-label">Personal Fuera</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">-->
                               <input type="hidden" class="form-control" min="1" name="modal_alfuera" id="modal_alfuera" value="">
                             <!--</div>
                           </div>--> 
                      </div> 
                      <div class="row">
                        <table class="table table-bordered table-condensed table-striped table-sm">
                             <thead>
                                  <tr class="fondo-boton">
                                    <th>#</th>
                                    <th>Tipo de Personal</th>
                                    <th class="text-center">Cantidad</th>
                                    <th>Cantidad</th>
                                  </tr>
                              </thead>
                              <tbody>
                                <?php 
                                $iii=1;
                               $queryPr="SELECT s.*,t.nombre as tipo_personal FROM simulaciones_servicios_auditores s, tipos_auditor t where s.cod_simulacionservicio=$codigoSimulacionSuper and s.cod_tipoauditor=t.codigo order by s.codigo";
                               $stmt = $dbh->prepare($queryPr);
                               $stmt->execute();
                               while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoPre=$rowPre['codigo'];
                                  $tipoPre=$rowPre['tipo_personal'];
                                  $cantidadPre=$rowPre['cantidad'];
                                  $cantidadEPre=$rowPre['cantidad_editado'];
                                   ?>
                                   <tr>
                                     <td><?=$iii?></td>
                                     <td><?=$tipoPre?></td>
                                     <td class="text-center"><?=$cantidadEPre?> / <?=$cantidadPre?></td>
                                     <td>
                                      <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="cantidad_personal<?=$iii?>" id="cantidad_personal<?=$iii?>">
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
                                     </td>
                                   </tr>
                                  <?php
                                  $iii++; 
                                  } ?>
                              </tbody>
                           </table>
                      </div>
                      <hr>
                     
                      <div class="form-group float-right">
                        <button type="button" id="boton_guardarplan" class="btn btn-default" onclick="guardarDatosPlantilla(this.id)">Guardar</button>
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