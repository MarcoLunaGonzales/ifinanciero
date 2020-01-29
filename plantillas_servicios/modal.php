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
<div class="modal fade modal-arriba" id="modalDetalle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice modal-lg">
    <div class="modal-content card">
                <div class="card-header <?=$colorCard;?> card-header-text">
                  <div class="card-text" id="divTituloGrupo">
                    
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <div class="card-body ">
                      <form name="form2">
                     <input class="form-control" type="hidden" name="codGrupo" id="codGrupo"/>
                     <div class="row">
                       <label class="col-sm-2 col-form-label">Partidas</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                                <select class="selectpicker form-control" onchange="mostrarCuentasPartida();" data-style="btn btn-info" data-live-search="true" title="-- Elija una partida --" name="partida_detalle" id="partida_detalle" data-style="select-with-transition" data-actions-box="true" required>
                                  <?php
                           $stmt = $dbh->prepare("SELECT p.codigo, p.nombre, p.observaciones from partidas_presupuestarias p where p.cod_estadoreferencial=1 order by p.codigo");
                         $stmt->execute();
                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          $codigoX=$row['codigo'];
                          $obsX=$row['observaciones'];
                          $nombreX=$row['nombre'];

                          ?>
                         <option value="<?=$codigoX;?>"><?=$nombreX?></option>
                         <?php } 
                         ?>  
                       </select>
                            </div>
                        </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Cuentas</label>
                       <div class="col-sm-10">
                        <div class="form-group" id="combo_cuentas">
                         </div>
                        </div>
                      </div>
                      <br><br>
                      <div id="mensajeDetalle"></div>
                      <div class="form-group float-right">
                        <a href="#" class="btn btn-info btn-round" id="boton_guardardetalle"onclick="savePlantillaDetalleTcp()">Guardar</a>
                      </div>
                    </form>
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
                  
                  <a href="#" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </a>
                </div>
                <div class="card-body">
                   <table class="table table-condensed table-bordered bg-info text-white">
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

<!-- notice modal -->
<div class="modal fade modal-gris modal-arriba" id="modalDetallesPartida" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice modal-lg">
    <div class="modal-content card">
                <div class="card-header card-header-warning card-header-text">
                  <div class="card-text">
                    <h4>DETALLES <b id="titulo_partidadetalle"></b></h4>
                  </div>
                  <a href="#" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </a>
                  <a class="btn btn-success btn-sm btn-fab float-right" href="#" onclick="cambiarModalDetalle()">
                    <i class="material-icons">keyboard_backspace</i>
                  </a>
                </div>
                <div class="card-body">
                  <div class="row" id="lista_detallespartidacuenta">
                    
                  </div>
               </div>   
      </div>
  </div>
</div>
<!-- end notice modal -->