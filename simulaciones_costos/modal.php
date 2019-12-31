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
<div class="modal fade modal-primary" id="modalSimulacionCuentas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                <div class="row col-sm-12">
                    <div class="form-group col-sm-12">
                       <label class="bmd-label-static">Seleccione una Partida Presupuestaria</label>
                       <select class="selectpicker form-control" onchange="cargarCuentasSimulacion(<?=$codigo?>,<?=$ibnorcaC?>)" name="partida_presupuestaria" id="partida_presupuestaria" data-style="btn btn-danger" title="-- Elija una partida --">
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
                </div>
                 <div class="card" id="cuentas_simulacion">
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
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Simulaci&oacute;n</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                             <select class="selectpicker form-control" name="modal_tiposim" id="modal_tiposim" data-style="btn btn-success">
                               <option value="1">IBNORCA</option>
                               <option value="2">FUERA DE IBNORCA</option> 
                             </select>
                         </div>
                        </div>
                      </div>
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
                          <label class="col-sm-2 col-form-label">UT Min. Iborca %</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">
                               <input type="number" step="0.01" class="form-control" name="modal_utibnorca" id="modal_utibnorca" value="">
                             </div>
                           </div>  

                          <label class="col-sm-2 col-form-label">UT Min. Fuera %</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">
                               <input type="number" step="0.01" class="form-control" name="modal_utifuera" id="modal_utifuera" value="">
                             </div>
                           </div>  
                      </div>
                      <div class="row">
                          <label class="col-sm-2 col-form-label">Alumnos Ibnorca</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">
                               <input type="number" class="form-control" min="1" name="modal_alibnorca" id="modal_alibnorca" value="">
                             </div>
                           </div>  

                          <label class="col-sm-2 col-form-label">Alumnos Fuera</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">
                               <input type="number" class="form-control" min="1" name="modal_alfuera" id="modal_alfuera" value="">
                             </div>
                           </div>  
                      </div> 
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Importe</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                             <select class="selectpicker form-control" name="modal_importeplan" id="modal_importeplan" data-style="btn btn-info">
                               <?php 
                               $queryPr="SELECT * FROM precios_plantillacosto where cod_plantillacosto=$codigoPX order by codigo";
                               $stmt = $dbh->prepare($queryPr);
                               $stmt->execute();
                               while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoPrecio=$row['codigo'];
                                  $precioLocal=number_format($row['venta_local'], 2, '.', ',');
                                  $precioExterno=number_format($row['venta_externo'], 2, '.', ',');
                                   ?><option value="<?=$codigoPrecio?>" class="text-right">Ibnorca: <?=$precioLocal?>, Fuera: <?=$precioExterno?></option>
                                  <?php 
                                  } ?> 
                             </select>
                         </div>
                        </div>
                      </div>
                      <hr>
                      <a href="#" onclick="modificarMontos()" class="btn btn-sm btn-danger"><i class="material-icons">edit</i> Editar COSTOS VARIABLES</a>
                      <div class="form-group float-right">
                        <button type="button" id="boton_guardarplan" class="btn btn-default" onclick="guardarDatosPlantilla(this.id)">Guardar</button>
                      </div> 
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->
