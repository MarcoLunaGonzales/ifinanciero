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
        <button type="button" class="btn btn-white btn-link" data-dismiss="modal">Aceptar
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

<!-- Classic Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
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
              <div class="form-group col-sm-4">
                <label for="nro_cuenta" class="bmd-label-floating">Nro. Cuenta:</label>
                <input type="number" class="form-control" id="nro_cuenta" name="nro_cuenta" onkeypress="pulsar(event)" autofocus>
              </div>
              <div class="form-group col-sm-4">
                <label for="cuenta" class="bmd-label-floating">Cuenta:</label>
                <input type="text" class="form-control" id="cuenta" name="cuenta" onkeypress="pulsar(event)">
              </div>
              <div class="form-group col-sm-3">
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
<!--  End Modal -->

<!-- notice modal -->
<div class="modal fade" id="modalDet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice modal-lg">
    <div class="modal-content card">
                <div class="card-header <?=$colorCard;?> card-header-text">
                  <div class="card-text" id="divTituloGrupo">
                    
                  </div>
                </div>
                <div class="card-body ">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                  <ul class="nav nav-pills nav-pills-warning" role="tablist">
                    <li class="nav-item">
                          <a id="nav_boton1"class="nav-link active" data-toggle="tab" href="#link111" role="tablist">
                            <span class="material-icons">add</span> Nuevo
                          </a>
                        </li>
                        <li class="nav-item">
                          <a id="nav_boton2"class="nav-link" data-toggle="tab" href="#link110" role="tablist">
                           <span class="material-icons">view_list</span> Lista 
                          </a>
                        </li>
                  </ul>
                  <div class="tab-content tab-space">
                    <div class="tab-pane" id="link110">
                      <div id="divResultadoListaDet">
            
                       </div>
                    </div>
                    <div class="tab-pane active" id="link111">
                      <form name="form2">
                     <input class="form-control" type="hidden" name="codGrupo" id="codGrupo"/>
                           <div class="row">
                       <label class="col-sm-2 col-form-label">Partidas</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                                <select class="selectpicker form-control" onchange="calcularMontos();" data-style="<?=$comboColor;?>" data-live-search="true" title="-- Elija una partida --" name="cuenta_detalle" id="cuenta_detalle" data-style="select-with-transition" data-actions-box="true" required>
                                  <?php
                           $stmt = $dbh->prepare("SELECT p.codigo, p.nombre, p.observaciones from partidas_presupuestarias p where p.cod_estadoreferencial=1 order by p.codigo");
                         $stmt->execute();
                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          $codigoX=$row['codigo'];
                          $obsX=$row['observaciones'];
                          $nombreX=$row['nombre'];

                          ?>
                         <option value="<?=$codigoX;?>@<?=$nombreX?>"><?=$nombreX?></option>
                         <?php } 
                         ?>  
                       </select>
                            </div>
                        </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Tipo</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                             <select class="selectpicker form-control" name="tipo_dato" id="tipo_dato" data-style="btn btn-info" onchange="limpiarMontos()">
                               <option value="1">Mensual</option>
                               <option value="2">Manual</option> 
                             </select>
                         </div>
                        </div>
                      </div>
                      <br><br>
                      <div class="row">
                        <div class="col-sm-4">                     
                         <div class="form-group">
                          <label class="bmd-label-static">Monto GENERADO</label>
                          <input type="number" class="form-control" name="monto_ibnorca" id="monto_ibnorca" value="0" step="0.01" readonly>
                         </div> 
                        </div>
                        <div class="col-sm-4">
                         <div class="form-group">
                          <label class="bmd-label-static">Monto MODULO</label>
                          <input type="number" class="form-control" name="monto_f_ibnorca" id="monto_f_ibnorca" value="0" step="0.01" readonly>
                         </div>
                        </div>
                        <div class="col-sm-4">
                         <div class="form-group">
                          <label class="bmd-label-static">Monto ALUMNO</label>
                          <input type="number" class="form-control" name="monto_alumno" id="monto_alumno" value="0" step="0.01" readonly>
                          <input type="hidden" class="form-control" name="monto_calculado" id="monto_calculado" value="0" step="0.001" readonly>
                         </div>
                        </div>
                      </div>
                      <div class="row d-none" id="montos_editables">
                        <div class="col-sm-4">
                        <a href="#" class="btn btn-danger btn-sm btn-round" onclick="mostrarInputMonto('monto_ibnorca1')"> Editar</a>                     
                         <div class="form-group d-none" id="monto_ibnorca1">
                          <label class="bmd-label-static">Monto GENERADO</label>
                          <input type="number" class="form-control" name="monto_ibnorca_edit" id="monto_ibnorca_edit" value="0" step="0.01">
                         </div> 
                        </div>
                        <div class="col-sm-4">
                          <a href="#" class="btn btn-danger btn-sm btn-round" onclick="mostrarInputMonto('monto_ibnorca2')"> Editar</a> 
                         <div class="form-group d-none" id="monto_ibnorca2">
                          <label class="bmd-label-static">Monto MODULO</label>
                          <input type="number" class="form-control" name="monto_f_ibnorca_edit" id="monto_f_ibnorca_edit" value="0" step="0.01">
                         </div>
                        </div>
                        <div class="col-sm-4 d-none" id="columna_edit_alumno">
                          <a href="#" class="btn btn-danger btn-sm btn-round" onclick="mostrarInputMonto('monto_ibnorca3')"> Editar</a> 
                         <div class="form-group d-none" id="monto_ibnorca3">
                          <label class="bmd-label-static">Monto ALUMNO</label>
                          <input type="number" class="form-control" name="monto_alumno_edit" id="monto_alumno_edit" value="0" step="0.01"> 
                         </div>
                        </div>
                      </div>

                      <div id="mensajeDetalle"></div>
                      <div class="form-group float-right">
                        <button type="button" class="btn btn-info btn-round" onclick="savePlantillaDetalle()">Guardar</button>
                      </div>
                         </form>
                    </div>
                  </div>
                </div>
    </div>
  </div>
</div>
<!-- end notice modal -->

<!-- notice modal -->
<div class="modal fade" id="modalPrecio" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice modal-lg">
    <div class="modal-content card">
                <div class="card-header card-header-success card-header-text">
                  <div class="card-text">
                    <h4>Lista de Precios</h4>
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <div class="card-body">
                   <table class="table table-condensed table-bordered bg-info text-dark">
                     <thead>
                       <tr>
                         <th>Nº</th>
                         <th>EN IBNORCA</th>
                         <th>FUERA DE IBNORCA</th>
                         <th>Actions</th>
                       </tr>
                     </thead>
                     <tbody id="lista_preciosplantilla">
                       
                     </tbody>
                   </table>

               </div>   
      </div>
  </div>
</div>
<!-- end notice modal -->