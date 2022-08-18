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
  <div class="modal-dialog modal-xl col-sm-12">
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
                      <div class="row">
                          <label class="col-sm-2 col-form-label">Fecha Curso</label>
                           <div class="col-sm-10">                     
                             <div class="form-group">
                               <input type="text" class="form-control datepicker" name="modal_fechacurso" id="modal_fechacurso" value="">
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
                    <h4>Editar Propuesta</h4>
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <div class="card-body">
                    <div class="row">
                          <label class="col-sm-3 col-form-label">Cantidad Módulos</label>
                           <div class="col-sm-8">                     
                             <div class="form-group">
                               <input type="number" class="form-control" min="1" name="modal_modulo" id="modal_modulo" value="<?=$cantidadModuloX?>" style="background:#7BCDF0;color:#fff;">
                             </div>
                           </div>  
                      </div>
                      <div class="row">
                          <label class="col-sm-3 col-form-label">D&iacute;as Curso</label>
                           <div class="col-sm-8">                     
                             <div class="form-group">
                               <input type="number" class="form-control" min="1" name="modal_diascurso" id="modal_diascurso" value="">
                             </div>
                           </div>  
                      </div> 
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
                          <label class="col-sm-3 col-form-label">N&uacute;mero de Estudiantes</label>
                           <div class="col-sm-8">                     
                             <div class="form-group">
                               <input type="number" class="form-control" min="1" name="modal_alibnorca" id="modal_alibnorca" value="">
                             </div>
                           </div>  

                          <!--<label class="col-sm-2 col-form-label">Alumnos Fuera</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">-->
                               <input type="hidden" class="form-control" min="1" name="modal_alfuera" id="modal_alfuera" value="">
                             <!--</div>
                           </div>--> 
                      </div> 

                      <div class="row">
                       <label class="col-sm-3 col-form-label">Normas:</label>
                       <div class="col-sm-8">
                        <div class="form-group">
                                <select class="selectpicker form-control" name="normas[]" id="normas" multiple data-style="btn btn-warning" data-actions-box="true" data-live-search="true" data-size="6" required>
                                <?php
                                 $stmt = $dbh->prepare("SELECT * from normas where cod_estado=1 order by abreviatura");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  $abrevX=$row['abreviatura'];

                                  $stmtNormasEdit = $dbh->prepare("SELECT count(*)as contador from simulaciones_costosnormas where cod_simulacion='$codigo' and  cod_norma='$codigoX'");
                                  $stmtNormasEdit->execute();
                                  $cantidadFilasNormasEdit=0;
                                      while($rowNormasEdit = $stmtNormasEdit->fetch(PDO::FETCH_ASSOC)) {
                                          $cantidadFilasNormasEdit=$rowNormasEdit['contador'];
                                      }  
                                  ?>
                                      <option value="<?=$codigoX;?>" <?=($cantidadFilasNormasEdit>0)?'selected':'';?> ><?=$abrevX;?></option> 
                                  <?php
                                  }
                                  ?>
                                </select>
                              </div>
                        </div>
                      </div>

                      <div class="row">
                       <label class="col-sm-3 col-form-label">Precio</label>
                       <div class="col-sm-8">
                        <div class="form-group">
                             <select class="selectpicker form-control form-control-sm" name="modal_importeplan" id="modal_importeplan" onchange="cambiarPrecioPlantilla()" data-style="btn btn-info">
                               <?php 
                               $queryPr="SELECT * FROM precios_simulacioncosto where cod_simulacioncosto=$codigo order by codigo";
                               $stmt = $dbh->prepare($queryPr);
                               $stmt->execute();
                               while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoPrecio=$row['codigo'];
                                  $precioLocal=number_format($row['venta_local'], 2, '.', ',');
                                  $precioExterno=number_format($row['venta_externo'], 2, '.', ',');
                                   ?><option value="<?=$codigoPrecio?>" class="text-right"><?=$precioLocal?></option>
                                  <?php 
                                  } ?> 
                             </select>
                         </div>
                        </div>

                      </div>
                      <div class="row">
                        <label class="col-sm-3 col-form-label">BOB</label>
                       <div class="col-sm-4">
                        <div class="form-group">
                             <input type="number" readonly class="form-control" id="modal_importeplanedit" name="modal_importeplanedit">
                         </div>
                        </div>
                        <a href="#" title="Editar Precio" class="btn btn-warning text-dark btn-sm btn-fab" onclick="editarPrecioSimulacionCostos();return false;"><i class="material-icons">edit</i></a>
                        <label class="col-sm-1 col-form-label">TOTAL</label>
                       <div class="col-sm-3">
                        <div class="form-group">
                             <input type="number" readonly class="form-control" id="total_preciosimulacion" name="total_preciosimulacion">
                         </div>
                        </div>
                      </div>

                      

                      <br>    
                       <!--INICIO DE alumICIOS-->
                      <h4 class="font-weight-bold"><center>DETALLE PRECIO</center></h4>
                      <div class="row" id="modal_contenidoalumicios">
                        <table class="table table-bordered table-condensed table-striped table-sm">
                             <thead>
                                  <tr class="fondo-boton">
                                    <td><a href="#" title="Nueva Fila" class="btn btn-primary btn-round btn-sm btn-fab float-left" onClick="agregarFilaPreciosSimulacionCabecera()"><i class="material-icons">add</i></a> Estudiantes</td>
                                    <td>% Descuento</td>
                                    <td>Monto</td>
                                    <td>Total</td>
                                    <td>Action</td>
                                  </tr>
                              </thead>
                              <tbody id="modal_body_tabla_alumnos">
                                <?php 
                                $iii=1;
                               $queryPr="SELECT * FROM precios_simulacioncostodetalle where cod_preciosimulacion=$codigoPrecioSimulacion order by 1";
                               $stmt = $dbh->prepare($queryPr);
                               $stmt->execute();
                               $totalFilasPrecios=0;
                               while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoPre=$rowPre['codigo'];
                                  $cantidadPre=$rowPre['cantidad'];
                                  $montoPre=$rowPre['monto'];
                                  $montoPreTotal=$montoPre*$cantidadPre;
                                  $totalFilasPrecios+=$montoPreTotal;
                                  $porcentajePre=$rowPre['porcentaje'];
                                  $iconalum="check_circle";
                                  $montoPre=number_format($montoPre,2,".","");
                                  $montoPreTotal=number_format($montoPreTotal,2,".","");
                                   ?>
                                   <tr id="fila_precios<?=$iii?>">
                                     <td class="text-center">
                                      <input type="hidden" id="codigo_alumnosAAA<?=$iii?>" value="<?=$codigoPre?>">
                                       <input type="number" min="1" id="cantidad_alumnosAAA<?=$iii?>" name="cantidad_alumnosAAA<?=$iii?>" class="form-control" style="background-color:#E3CEF6;text-align: right" onchange="calcularPrecioTotal(<?=$iii?>)" onkeyUp="calcularPrecioTotal(<?=$iii?>)" value="<?=$cantidadPre?>">
                                     </td>
                                     <td class="text-center">
                                       <input type="number" id="porcentaje_alumnosAAA<?=$iii?>" name="porcentaje_alumnosAAA<?=$iii?>" class="form-control" style="background-color:#E3CEF6;text-align: right" onchange="calcularPrecioPorcentaje(<?=$iii?>)" onkeyUp="calcularPrecioPorcentaje(<?=$iii?>)" value="<?=$porcentajePre?>" step="0.01">
                                     </td>
                                     <td class="text-center">
                                       <input type="number" id="monto_alumnosAAA<?=$iii?>" name="monto_alumnosAAA<?=$iii?>" class="form-control" style="background-color:#E3CEF6;text-align: right" onchange="calcularPrecioTotal(<?=$iii?>)" onkeyUp="calcularPrecioTotal(<?=$iii?>)" value="<?=$montoPre?>" step="0.01">
                                     </td>  
                                     <td class="text-center">
                                       <input type="number" readonly id="total_alumnosAAA<?=$iii?>" name="total_alumnosAAA<?=$iii?>" class="form-control" style="background-color:#E3CEF6;text-align: right" value="<?=$montoPreTotal?>" step="0.01">
                                     </td>
                                     <td class="text-left">
                                      <a href="#" title="Quitar" class="btn btn-danger btn-round btn-sm btn-fab float-right" onClick="quitarElementoPrecios(<?=$iii?>)"><i class="material-icons">delete_outline</i></a>
                                     </td>
                                   </tr>
                                  <?php
                                  $iii++; 
                                  } ?>
                              </tbody>
                           </table>
                           <script>$("#total_preciosimulacion").val(<?=$totalFilasPrecios?>);</script>
                           <input type="hidden" id="cantidad_filasprecios" value="<?=$iii?>">        
                      </div>
                    <!--FIN DE alumICIOS-->

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