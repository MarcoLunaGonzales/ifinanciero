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

<?php
for ($ann=0; $ann <=$anioGeneral ; $ann++) { 
  ?>
<!-- small modal -->
<div class="modal fade modal-arriba modal-primary" id="modalSimulacionCuentas<?=$ann?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice" style="max-width: 80% !important;">
    <div class="modal-content card">
                <div class="card-header card-header-danger card-header-text">
                  <div class="card-text">
                    <h4>Costos Variables A&ntilde;o <?=$ann?></h4>
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <div class="card-body">
                 <div class="card" id="cuentas_simulacion<?=$ann?>">
                   <?php 
                    include "cargarDetallePlantillaPartida.php";
                   ?>
                 </div>   
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->
  <?php
}
?>


<!-- small modal -->
<div class="modal fade modal-arriba modal-primary" id="modalSimulacionCuentasPersonal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice" style="max-width: 100% !important;">
    <div class="modal-content card">
                <div class="card-header card-header-warning card-header-text">
                  <div class="card-text">
                    <h4>Costos Variables Detalle AÑO <b id="titulo_modaldetalleslista"></b></h4>
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
                        if($codAreaX==39){
                          $cantidadProductos=explode(",",$productosX);
                         ?>
                     <div class="row">
                       <label class="col-sm-2 col-form-label">Productos <small class="text-muted">(<?=count($cantidadProductos)?>)</small></label>
                       <div class="col-sm-10">
                        <div class="form-group" style="border-bottom: 1px solid #CACFD2">
                          <input type="text" value="" class="form-control tagsinput" name="modal_productos" id="modal_productos" data-role="tagsinput" data-color="warning">                          
                        </div>
                        </div>
                      </div>
                         <?php
                        }else{
                            if($codAreaX==38){
                              $cantidadSitios=explode(",",$sitiosX);
                         ?>
                     <div class="row">
                       <label class="col-sm-2 col-form-label">Sitios <small class="text-muted">(<?=count($cantidadSitios)?>)</small></label>
                       <div class="col-sm-10">
                        <div class="form-group" style="border-bottom: 1px solid #CACFD2">
                          <input type="text" value="" class="form-control tagsinput" name="modal_sitios" id="modal_sitios" data-role="tagsinput" data-color="warning">                          
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
                        <ul class="nav nav-pills nav-pills-warning" role="tablist">
                        <?php
                          for ($an=1; $an<=$anioGeneral; $an++) { 
                            $active="";
                            if($an==1){
                              $active="active";
                            }
                                ?>
                          <li class="nav-item">
                            <a class="nav-link <?=$active?>" data-toggle="tab" href="#link<?=$an?>" onclick="cambiarTituloPersonalModal(<?=$an?>)" role="tablist">
                              Año <?=$an?>
                            </a>
                          </li>
                        <?php
                            }
                        ?>
                         </ul>
                         <div class="tab-content tab-space">
                         <?php
                          for ($an=1; $an<=$anioGeneral; $an++) { 
                            $active="";
                            if($an==1){
                              $active="active";
                            }
                                ?>
                          <div class="tab-pane <?=$active?>" id="link<?=$an?>">

                    <!--INICIO DE SERVICIOS-->
                    <h4 class="font-weight-bold"><center>AÑO <?=$an?> SERVICIOS</center></h4>
                      <div class="row" id="modal_contenidoservicios<?=$an?>">
                        <table class="table table-bordered table-condensed table-striped table-sm">
                             <thead>
                                  <tr class="fondo-boton">
                                    <td>#</td>
                                    <td width="30%">Descripci&oacute;n</td>
                                    <td>Cantidad</td>
                                    <td width="17%">Unidad</td>
                                    <td>Monto BOB</td>
                                    <td>Total BOB</td>
                                    <td>Monto USD</td>
                                    <td>Total USD</td>
                                    <td class="small">Habilitar/Deshabilitar</td>
                                  </tr>
                              </thead>
                              <tbody>
                                <tr class="bg-plomo">
                                  <td>N</td>
                                  <td><?php 
                                  if($codAreaX==39){
                                    $codigoAreaServ=108;
                                  }else{
                                    if($codAreaX==38){
                                      $codigoAreaServ=109;
                                    }else{
                                      $codigoAreaServ=0;
                                    }
                                  }
                                ?>
                                  <select class="selectpicker form-control form-control-sm" data-live-search="true" name="modal_editservicio<?=$an?>" id="modal_editservicio<?=$an?>" data-style="fondo-boton">
                                    <option disabled selected="selected" value="">--SERVICIOS--</option>
                                    <?php 
                                     $stmt3 = $dbh->prepare("SELECT idclaservicio,descripcion,codigo from cla_servicios where (codigo_n1=108 or codigo_n1=109) and vigente=1 and codigo_n1=$codigoAreaServ");
                                     $stmt3->execute();
                                     while ($rowServ = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                                      $codigoServX=$rowServ['idclaservicio'];
                                      $nombreServX=$rowServ['descripcion'];
                                      $abrevServX=$rowServ['codigo'];
                                      ?><option value="<?=$codigoServX;?>"><?=$abrevServX?> - <?=$nombreServX?></option><?php 
                                     }
                                    ?>
                                  </select>
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
                                       <input type="number" id="modal_montoservtotal<?=$an?>SSS0" name="modal_montoservtotal<?=$an?>SSS0" class="form-control text-primary text-right" onchange="calcularTotalFilaServicioNuevo(<?=$an?>,1)" onkeyUp="calcularTotalFilaServicioNuevo(<?=$an?>,1)" value="0" step="0.01">
                                     </td>
                                     <td class="text-right">
                                       <input type="number" id="modal_montoservUSD<?=$an?>SSS0" name="modal_montoservUSD<?=$an?>SSS0" class="form-control text-primary text-right" onchange="calcularTotalFilaServicioNuevo(<?=$an?>,4)" onkeyUp="calcularTotalFilaServicioNuevo(<?=$an?>,4)" value="0" step="0.01">
                                    </td>
                                     <td class="text-right">
                                       <input type="number" id="modal_montoservtotalUSD<?=$an?>SSS0" name="modal_montoservtotalUSD<?=$an?>SSS0" class="form-control text-primary text-right" onchange="calcularTotalFilaServicioNuevo(<?=$an?>,3)" onkeyUp="calcularTotalFilaServicioNuevo(<?=$an?>,3)" value="0" step="0.01">
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
                               $queryPr="SELECT s.*,t.descripcion as nombre_serv FROM simulaciones_servicios_tiposervicio s, cla_servicios t where s.cod_simulacionservicio=$codigoSimulacionSuper and s.cod_claservicio=t.idclaservicio and s.cod_anio=$an order by s.codigo";
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
                                     <td class="text-left"><i class="material-icons text-warning"><?=$iconServ?></i><input type="hidden" id="precio_fijo<?=$an?>SSS<?=$iii?>" value="<?=$iconServ?>"> <?=$tipoPre?></td>
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
                                       <input type="<?=$claseDeshabilitado?>" id="modal_montoservtotal<?=$an?>SSS<?=$iii?>" name="modal_montoservtotal<?=$an?>SSS<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalFilaServicio(<?=$an?>,1)" onkeyUp="calcularTotalFilaServicio(<?=$an?>,1)" value="<?=$montoPreTotal?>" step="0.01">
                                       <input type="<?=$claseDeshabilitadoOFF?>" id="modal_montoservtotalOFF<?=$an?>SSS<?=$iii?>" name="modal_montoservtotalOFF<?=$an?>SSS<?=$iii?>" readonly class="form-control text-info text-right" value="0" step="0.01">
                                     </td>        
                                     <td class="text-right">
                                       <input type="<?=$claseDeshabilitado?>" id="modal_montoservtotalUSD<?=$an?>SSS<?=$iii?>" name="modal_montoservtotalUSD<?=$an?>SSS<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalFilaServicio(<?=$an?>,3)" onkeyUp="calcularTotalFilaServicio(<?=$an?>,3)" value="<?=$montoPreTotalUSD?>" step="0.01">
                                       <input type="<?=$claseDeshabilitadoOFF?>" id="modal_montoservtotalUSDOFF<?=$an?>SSS<?=$iii?>" name="modal_montoservtotalUSDOFF<?=$an?>SSS<?=$iii?>" readonly class="form-control text-info text-right" value="0" step="0.01">
                                     </td>
                                     <td>
                                       <div class="togglebutton">
                                               <label>
                                                 <input type="checkbox" <?=($banderaHab==1)?"checked":"";?> onchange="activarInputMontoFilaServicio(<?=$an?>,'<?=$iii?>')">
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
                                     <td id="modal_totalmontoserv<?=$an?>" class="text-right"><?=number_format($modal_totalmontopre,2, ',', '')?></td>
                                     <td id="modal_totalmontoservtotal<?=$an?>" class="text-right font-weight-bold"><?=number_format($modal_totalmontopretotal,2, ',', '')?></td>
                                     <td id="modal_totalmontoservUSD<?=$an?>" class="text-right"><?=number_format($modal_totalmontopre/$usd,2,', ','')?></td>
                                     <td id="modal_totalmontoservtotalUSD<?=$an?>" class="text-right font-weight-bold"><?=number_format($modal_totalmontopretotal/$usd,2, ',', '')?></td>
                                     <td></td>
                                   </tr>
                              </tbody>
                           </table>
                           <input type="hidden" id="modal_numeroservicio<?=$an?>" value="<?=$iii?>">
                      </div>
                    <!--FIN DE SERVICIOS-->

                   <!--INICION DE PERSONAL-->         
                      <h4 class="font-weight-bold"><center>AÑO <?=$an?> HONORARIOS PERSONAL</center></h4>
                      <div class="row">
                        <table class="table table-bordered table-condensed table-striped table-sm">
                             <thead>
                                  <tr class="fondo-boton">
                                    <td>#</td>
                                    <td width="25%">Tipo de Personal</td>
                                    <td width="8%" class="text-center">Cantidad</td>                                   
                                    <td width="8%">D&iacute;as Aud.</td>
                                    <td>Monto BOB</td>
                                    <td>Total BOB</td>
                                    <td>Monto USD</td>
                                    <td>Total USD</td>
                                    <td width="10%" class="small">Hab/Des</td>
                                  </tr>
                              </thead>
                              <tbody>
                                <?php 
                                $iii=1;
                               $queryPr="SELECT s.*,t.nombre as tipo_personal FROM simulaciones_servicios_auditores s, tipos_auditor t where s.cod_simulacionservicio=$codigoSimulacionSuper and s.cod_tipoauditor=t.codigo and s.cod_anio=$an order by s.codigo";
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
                                  $claseDeshabilitado="hidden";
                                  $claseDeshabilitadoOFF="number";
                                  $banderaHab=$rowPre['habilitado'];
                                  if($banderaHab!=0){
                                    $modal_totalmontopre+=$montoPreSi;
                                    $modal_totalmontopretotal+=$montoPreTotal;
                                    //$modal_totalmontopreext+=$montoPreext;
                                    //$modal_totalmontopretotalext+=$montoPreTotalext;
                                    $claseDeshabilitado="number";
                                    $claseDeshabilitadoOFF="hidden";
                                  }
                                  $montoPreSiUSD=number_format($montoPreSi/$usd,2,".","");
                                  $montoPreTotalUSD=number_format($montoPreTotal/$usd,2,".","");
                                  $montoPreSi=number_format($montoPreSi,2,".","");
                                  $montoPreTotal=number_format($montoPreTotal,2,".","");
                                   ?>
                                   <tr>
                                     <td><?=$iii?></td>
                                     <td class="small"><?=$tipoPre?><input type="hidden" id="local_extranjero<?=$an?>FFF<?=$iii?>" value="<?=$codExtLoc?>"></td>
                                     <td>
                                      <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="cantidad_personal<?=$an?>FFF<?=$iii?>" id="cantidad_personal<?=$an?>FFF<?=$iii?>" onchange="calcularTotalPersonalServicio('<?=$an?>',2)">
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
                                       <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="dias_personal<?=$an?>FFF<?=$iii?>" id="dias_personal<?=$an?>FFF<?=$iii?>" onchange="calcularTotalPersonalServicio('<?=$an?>',2)">
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
                                       <input type="<?=$claseDeshabilitado?>" id="modal_montopre<?=$an?>FFF<?=$iii?>" name="modal_montopre<?=$an?>FFF<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalPersonalServicio('<?=$an?>',2)" onkeyUp="calcularTotalPersonalServicio('<?=$an?>',2)" value="<?=$montoPreSi?>" step="0.01">
                                       <input type="<?=$claseDeshabilitadoOFF?>" id="modal_montopreOFF<?=$an?>FFF<?=$iii?>" name="modal_montopreOFF<?=$an?>FFF<?=$iii?>" readonly class="form-control text-info text-right" value="0" step="0.01">
                                       <input type="hidden" id="modal_montopreext<?=$an?>FFF<?=$iii?>" value="<?=$montoPreext?>">
                                       <input type="hidden" id="modal_montopreloc<?=$an?>FFF<?=$iii?>" value="<?=$montoPre?>">
                                     </td>
                                     <td class="text-right">
                                       <input type="<?=$claseDeshabilitado?>" id="modal_montopreUSD<?=$an?>FFF<?=$iii?>" name="modal_montopreUSD<?=$an?>FFF<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalPersonalServicio('<?=$an?>',4)" onkeyUp="calcularTotalPersonalServicio('<?=$an?>',4)" value="<?=$montoPreSiUSD?>" step="0.01">
                                       <input type="<?=$claseDeshabilitadoOFF?>" id="modal_montopreUSDOFF<?=$an?>FFF<?=$iii?>" name="modal_montopreUSDOFF<?=$an?>FFF<?=$iii?>" readonly class="form-control text-info text-right" value="0" step="0.01">
                                     </td>
                                     <td class="text-right">
                                       <input type="hidden" id="modal_codigopersonal<?=$an?>FFF<?=$iii?>" value="<?=$codigoPre?>">
                                       <input type="<?=$claseDeshabilitado?>" id="modal_montopretotal<?=$an?>FFF<?=$iii?>" name="modal_montopretotal<?=$an?>FFF<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalPersonalServicio('<?=$an?>',1)" onkeyUp="calcularTotalPersonalServicio('<?=$an?>',1)" value="<?=$montoPreTotal?>" step="0.01">
                                       <input type="<?=$claseDeshabilitadoOFF?>" id="modal_montopretotalOFF<?=$an?>FFF<?=$iii?>" name="modal_montopretotalOFF<?=$an?>FFF<?=$iii?>" readonly class="form-control text-info text-right" value="0" step="0.01">
                                     </td>   
                                     <td class="text-right">
                                       <input type="<?=$claseDeshabilitado?>" id="modal_montopretotalUSD<?=$an?>FFF<?=$iii?>" name="modal_montopretotalUSD<?=$an?>FFF<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalPersonalServicio('<?=$an?>',3)" onkeyUp="calcularTotalPersonalServicio('<?=$an?>',3)" value="<?=$montoPreTotalUSD?>" step="0.01">
                                       <input type="<?=$claseDeshabilitadoOFF?>" id="modal_montopretotalUSDOFF<?=$an?>FFF<?=$iii?>" name="modal_montopretotalUSDOFF<?=$an?>FFF<?=$iii?>" readonly class="form-control text-info text-right" value="0" step="0.01">
                                     </td>
                                     <td>
                                       <div class="togglebutton">
                                               <label>
                                                 <input type="checkbox" <?=($banderaHab==1)?"checked":"";?> onchange="activarInputMontoPersonalServicio('<?=$an?>','<?=$iii?>')">
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
                                     <td id="modal_totalmontopre<?=$an?>" class="text-right"><?=number_format($modal_totalmontopre,2, ',', '')?></td>
                                     <td id="modal_totalmontopretotal<?=$an?>" class="text-right font-weight-bold"><?=number_format($modal_totalmontopretotal,2, ',', '')?></td>
                                     <td id="modal_totalmontopreUSD<?=$an?>" class="text-right"><?=number_format($modal_totalmontopre/$usd,2, ',', '')?></td>
                                     <td id="modal_totalmontopretotalUSD<?=$an?>" class="text-right font-weight-bold"><?=number_format($modal_totalmontopretotal/$usd,2, ',', '')?></td>
                                     <td></td>
                                   </tr>
                              </tbody>
                           </table>
                           <input type="hidden" id="modal_numeropersonal<?=$an?>" value="<?=$iii?>">
                           <input type="hidden" id="modal_cantidadpersonal<?=$an?>" value="<?=$sumaCantidadPre?>">
                      </div>
                       <!--FIN DE PERSONAL-->
                          </div>
                        <?php
                            }
                        ?>  

                         </div>
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