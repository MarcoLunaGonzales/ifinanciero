<!-- small modal -->
<div class="modal fade modal-primary" id="modalAreas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">settings_applications</i>
                  </div>
                  <h4 class="card-title">Areas Registradas</h4>
                </div>
                <div class="card-body">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                  <i class="material-icons">close</i>
                </button>
                  <table class="table table-condensed">
                    <thead>
                      <tr class="text-dark bg-plomo">
                      <th>#</th>
                      <th>Nombre Area</th>
                      <th>Nombre Area Padre</th>
                      <th>Cargos</th>  
                      </tr>
                    </thead>
                    <tbody id="tablasA_registradas">
                      
                    </tbody>
                  </table>
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->

<!-- small modal -->
<div class="modal fade modal-primary" id="modalCargos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
                <div class="card-header card-header-danger card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">list</i>
                  </div>
                  <h4 class="card-title">Cargos Registrados por Area <label id="tutulo_cargosarea"></label></h4>
                </div>
                <div class="card-body">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                  <i class="material-icons">close</i>
                </button>
                <div class="row">
                   <label class="col-sm-2 col-form-label">Cargo</label>
                   <div class="col-sm-7">
                     <div class="form-group">
                       <select class="selectpicker form-control form-control-sm" data-style="select-with-transition" data-live-search="true" title="-- Elija un cargo --" name="cargo_areaorg" id="cargo_areaorg" data-style="<?=$comboColor;?>" required="true">
                          <option disabled selected value="">Cargos</option>
                             <?php
                             $stmt = $dbh->prepare("SELECT codigo,nombre FROM cargos order by nombre");
                             $stmt->execute();
                             while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                               $codigoX=$row['codigo'];
                               $nombreX=$row['nombre'];
                               ?>
                               <option value="<?=$codigoX;?>"><?=$nombreX;?></option> 
                            <?php
                              }
                              ?>
                        </select>
                       <input class="form-control" type="hidden" name="areaorganizacion_id" id="areaorganizacion_id"/>
                     </div>
                   </div>
                   <button onclick="agregarCargoAreaOrganizacion()" class="btn btn-info btn-sm col-sm-2">Agregar</button>
                 </div>
                  <div id="mensajeRealizado"></div>
                  <hr>
                  <table class="table table-condensed">
                    <thead>
                      <tr class="text-dark bg-danger">
                      <th>#</th>
                      <th>Nombre Cargo</th>
                      <th>Nombre Area Padre</th>
                      <th>Quitar</th>  
                      </tr>
                    </thead>
                    <tbody id="tablasCargos_registrados">
                      
                    </tbody>
                  </table>
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->