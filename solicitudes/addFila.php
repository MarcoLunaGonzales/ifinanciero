<?php
$cod_actividadproyecto=obtenerCodigoActividadProyecto($codDetalleX);
$cod_accproyecto=obtenerCodigoAccProyecto($codDetalleX);
$des_actividadproyecto="";
?>
<div class="form-group d-none" id="divNitFactura<?=$idFila;?>">  
  <input class="form-control" type="number" name="nit_fac" id="nit_fac" onkeyup="llenarFacturaAutomaticamente(this.value,'<?=$idFila;?>',<?=$importeSolX?>);">
</div>
 <div id="div<?=$idFila?>">               	         
                             <div class="col-md-12">
                             	<div class="row">
                               <div class="col-sm-1 btn-group" style="padding-left:0 !important;padding-right:0 !important;">
                                 <div class="form-group" style="width:100% !important;">
                                  <span style="position:absolute;left:-25px; font-size:20px;font-weight:600; color:#F1C40F;" id="fila_index<?=$idFila?>"><?=$idFila?></span>
                                    <select class="selectpicker form-control form-control-sm col-sm-12" onchange="listarProyectosSisdeUnidades()" name="unidad_fila<?=$idFila;?>" id="unidad_fila<?=$idFila;?>" data-style="btn btn-primary">
                                      <?php
                                   $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 2");
                                   $stmt->execute();
                                   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $codigoX=$row['codigo'];
                                    $nombreX=$row['nombre'];
                                    $abrevX=$row['abreviatura'];
                                    if($codigoX==$unidadXX){
                                       ?><option selected value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
                                    }else{
                                      if($tipoSolicitud==4){
                                        ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php 
                                      }
                                    }
                                  }
                                    ?>
                                   </select>
                                   </div>
                                 
                                       <div class="form-group" style="width:100% !important;">
                                       <select class="selectpicker form-control form-control-sm col-sm-12" name="area_fila<?=$idFila;?>" id="area_fila<?=$idFila;?>" data-style="btn btn-rose">
                                               <!--<option value="" disabled selected>Area</option>-->
                                     <?php
                                                             
                                           $stmt = $dbh->prepare("SELECT a.codigo, a.nombre, a.abreviatura FROM areas a join areas_activas aa on aa.cod_area=a.codigo where a.cod_estado=1 order by 2");
                                         $stmt->execute();
                                         $cont=0;
                                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                           $codigoX=$row['codigo'];
                                           $nombreX=$row['nombre'];
                                           $abrevX=$row['abreviatura'];
                                           if($codigoX==$areaXX){
                                            ?><option selected value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
                                           }else{
                                             if($tipoSolicitud==4){
                                               ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php 
                                             }
                                            }
                                         } 
                                         ?>
                                        </select>
                                      </div>
                                 </div>
		                          <div class="row col-sm-3">
                                    <div class="form-group col-sm-2 d-none">
                                       <div class="row">
                                         <div class="col-sm-12">
          		                           <div class="form-check">
                                              <label class="form-check-label">
                                              <input class="form-check-input" onchange="habilitarFila(<?=$idFila;?>)" type="checkbox" id="habilitar<?=$idFila;?>" name="habilitar<?=$idFila;?>" checked value="1">
                                                 <span class="form-check-sign">
                                                         <span class="check"></span>
                                                  </span>
                                               </label>
                                             </div>
                                           </div>
                                           
                                        </div>   	
			                               </div>
                                     <div class="form-group col-sm-12">
                                             <select class="selectpicker form-control form-control-sm"  data-live-search="true" data-size="6" name="partida_cuenta_id<?=$idFila?>" id="partida_cuenta_id<?=$idFila?>" required data-style="btn btn-warning">
                                                  <option disabled selected value="">CUENTAS</option>
                                                <?php
                                                $cuentaLista=obtenerCuentasListaSolicitud(); //null para todas las iniciales del numero de cuenta obtenerCuentasLista(5,[5,4]);
                                              while ($rowCuenta = $cuentaLista->fetch(PDO::FETCH_ASSOC)) {
                                                $codigoX=$rowCuenta['codigo'];
                                                $numeroX=$rowCuenta['numero'];
                                                $nombreX=$rowCuenta['nombre'];
                                              ?>
                                              <option value="<?=$codigoX;?>" <?=($codigoX==$codCuentaX)?"selected":"";?> >[<?=$numeroX?>] <?=$nombreX;?></option>  
                                              <?php
                                                }
                                                ?>
                                            </select>
                                            <!--<label for="partida_cuenta<?=$idFila;?>" class="bmd-label-floating"><small><?=$nombrePartidaX?> / <?=$nombrePartidaDetalleX?></small></label>
                                              <input class="form-control" type="hidden" name="partida_cuenta_id<?=$idFila?>" id="partida_cuenta_id<?=$idFila?>" value="<?=$codCuentaX?>"/>-->        
                                            <input class="form-control" type="hidden" name="partida_cuenta<?=$idFila;?>" id="partida_cuenta<?=$idFila;?>" value="[<?=$numeroCuentaX?>] - <?=$nombreCuentaX?>" <?=($tipoSolicitud!=4)?"readonly":"";?>> 
                                          </div>
                                   </div>
                               <input type="hidden" id="unidad<?=$idFila;?>" name="unidad<?=$idFila;?>" value="<?=$unidadSol?>">
                               <input type="hidden" id="area<?=$idFila;?>" name="area<?=$idFila;?>" value="<?=$areaSol?>">
                               <input type="hidden" id="cod_detalleplantilla<?=$idFila;?>" name="cod_detalleplantilla<?=$idFila;?>" value="<?=$cod_plantilladetalle?>">
                               <input type="hidden" id="cod_servicioauditor<?=$idFila;?>" name="cod_servicioauditor<?=$idFila;?>" value="<?=$cod_plantillauditor?>">    
                               <!--COD ACTIVIDADES-->
		                           <input type="hidden" id="cod_actividadproyecto<?=$idFila;?>" name="cod_actividadproyecto<?=$idFila;?>" value="<?=$cod_actividadproyecto?>">
                               <input type="hidden" id="cod_accproyecto<?=$idFila;?>" name="cod_accproyecto<?=$idFila;?>" value="<?=$cod_accproyecto?>">
                               <input type="hidden" id="des_actividadproyecto<?=$idFila;?>" name="des_actividadproyecto<?=$idFila;?>" value="<?=$des_actividadproyecto?>">    
      	                              <!--<div class="col-sm-4">
                                          
      	                              </div>-->
                                  <div class="col-sm-3">
		                                  <div class="form-group">
                                        		<!--<label for="detalle_detalle<?=$idFila;?>" class="bmd-label-static">Detalle</label>-->
		                              		<textarea rows="3" class="form-control" name="detalle_detalle<?=$idFila;?>" required id="detalle_detalle<?=$idFila;?>" value=""><?=$detalleX?></textarea>
		                              	</div>
		                              </div>
                                  <div class="col-sm-1">
                                          <div class="form-group">
                                            <!--<label for="importe_presupuesto<?=$idFila;?>" class="bmd-label-floating">Imp Pres</label>      -->
                                            <input class="form-control" type="number" name="importe_presupuesto<?=$idFila;?>" id="importe_presupuesto<?=$idFila;?>" value="<?=$importeX?>" step="0.001" required readonly>  
                                    </div>
                                  </div>
		                              <div class="col-sm-1">
                                          <div class="form-group">
                                          	<label for="importe<?=$idFila;?>" class="bmd-label-floating d-none" id="importe_label<?=$idFila;?>"><?=$tituloImporte?></label>
                                        		<input class="form-control" type="number" name="importe<?=$idFila;?>" id="importe<?=$idFila;?>" value="<?=$importeSolX?>" step="0.001" onChange="calcularTotalesSolicitud();" OnKeyUp="calcularTotalesSolicitud();" required>	
		                              	</div>
      	                          </div>
      	                              <div class="col-sm-2">
                                          <div class="form-group">
                                              <select class="selectpicker form-control form-control-sm" onchange="quitarFormaPagoProveedor(<?=$idFila?>)" data-live-search="true" data-size="6" name="proveedor<?=$idFila?>" data-live-search="true" id="proveedor<?=$idFila?>" required data-style="<?=$comboColor;?>">
                                                  <option disabled selected value="">Proveedor</option>
                                                   <?php
                                                   $stmt = $dbh->prepare("SELECT * FROM af_proveedores order by nombre");
                                                 $stmt->execute();
                                                 while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                   $codigoX=$row['codigo'];
                                                   $nombreX=$row['nombre'];
                                                   if($codigoX==$proveedorX){
                                                   	?>
                                                 <option value="<?=$codigoX;?>" selected><?=$nombreX;?></option>  
                                                 <?php
                                                   }else{
                                                   	?>
                                                 <option value="<?=$codigoX;?>"><?=$nombreX;?></option>  
                                                 <?php
                                                   }
                                                 
                                                   }
                                                   ?>
                                               </select>
                                          </div>
                                       </div> 	
		                               <div class="col-sm-1">
		                                 <div class="btn-group">
                                      <input type="hidden" name="cod_cuentaBancaria<?=$idFila?>" id="cod_cuentaBancaria<?=$idFila?>" value="<?=$codCuentaBancaria?>"/>
                                      <input type="hidden" name="cod_tipopago<?=$idFila?>" id="cod_tipopago<?=$idFila?>" value="<?=$codTipoPago?>"/>
                                       <input type="hidden" name="nombre_beneficiario<?=$idFila?>" id="nombre_beneficiario<?=$idFila?>" value="<?=$nombreBen?>"/>
                                       <input type="hidden" name="apellido_beneficiario<?=$idFila?>" id="apellido_beneficiario<?=$idFila?>" value="<?=$apellidoBen?>"/>
                                       <input type="hidden" name="cuenta_beneficiario<?=$idFila?>" id="cuenta_beneficiario<?=$idFila?>" value="<?=$cuentaBen?>"/>
                                       
                                       <span id="archivos_fila<?=$idFila?>" class="d-none">
                                       <?php
                                       //archivos adjuntos detalle
                                        $stmtArchivo = $dbh->prepare("SELECT * from ibnorca.vw_plantillaDocumentos where idTipoServicio=2708"); //2708 //2708 localhost
                                        $stmtArchivo->execute();
                                        $filaA=0;
                                        while ($rowArchivo = $stmtArchivo->fetch(PDO::FETCH_ASSOC)) {
                                           $filaA++;
                                           $codigoX=$rowArchivo['idClaDocumento'];
                                           $nombreX=$rowArchivo['Documento'];
                                           $ObligatorioX=$rowArchivo['Obligatorio'];
                                        ?>
                                        <input type="hidden" name="codigo_archivodetalle<?=$filaA?>FFFF<?=$idFila?>" id="codigo_archivodetalle<?=$filaA?>FFFF<?=$idFila?>" value="<?=$codigoX?>">
                                        <input type="hidden" name="nombre_archivodetalle<?=$filaA?>FFFF<?=$idFila?>" id="nombre_archivodetalle<?=$filaA?>FFFF<?=$idFila?>" value="<?=$nombreX?>">
                                        <input type="file" class="archivo" name="documentos_detalle<?=$filaA?>FFFF<?=$idFila?>" id="documentos_detalle<?=$filaA?>FFFF<?=$idFila?>"/>
                                       <?php
                                        }
                                        ?>
                                      </span>  
                                      <input type="hidden" value="<?=$filaA?>" id="cantidad_archivosadjuntosdetalle<?=$idFila?>" name="cantidad_archivosadjuntosdetalle<?=$idFila?>">
                                        <?php
                                     //fin archivos adjuntos detalle  

                                       if($codTipoPago!=0){
                                        $estadoBen="estado";
                                       }else{
                                        $estadoBen="";
                                       }  
                                       if($retencionX==0||$retencionX==""||$retencionX==null){
                                         $estadoRet="";
                                       }else{
                                         $estadoRet="estado";
                                       }
                                       ?>
                                       <a  title="Forma de Pago" href="#" class="btn btn-success btn-sm btn-fab" id="boton_formapago<?=$idFila;?>" onclick="agregarTipoPagoProveedorDetalle(<?=$idFila;?>)">
                                             <i class="material-icons">money</i><span id="nben<?=$idFila?>" class="bg-danger <?=$estadoBen?>"></span>
                                       </a>
		                                 	<input type="hidden" name="cod_retencion<?=$idFila?>" id="cod_retencion<?=$idFila?>" value="<?=$retencionX?>"/>
		                                 	<a href="#" title="Retenciones" id="boton_ret<?=$idFila;?>" onclick="listRetencion(<?=$idFila;?>);" class="btn btn-warning text-dark btn-sm btn-fab">
                                              <i class="material-icons">ballot</i><span id="nret<?=$idFila?>" class="bg-danger <?=$estadoRet?>"></span>
                                            </a>
		                                 	<a href="#" title="Facturas" id="boton_fac<?=$idFila;?>" onclick="listFac(<?=$idFila;?>);" class="btn btn-info btn-sm btn-fab">
                                              <i class="material-icons">featured_play_list</i><span id="nfac<?=$idFila;?>" class="count bg-warning">0</span>
                                            </a>
                                            <!--<span id="archivos_fila<?=$idFila?>" class="d-none"><input type="file" name="archivos<?=$idFila?>[]" id="archivos<?=$idFila?>" multiple="multiple"/></span>-->
                                            <a href="#" title="Archivos" id="boton_archivos<?=$idFila;?>" onclick="addArchivos(<?=$idFila;?>);" class="btn btn-default btn-sm btn-fab d-none">
                                              <i class="material-icons"><?=$iconFile?></i><span id="narch<?=$idFila?>" class="bg-warning"></span>
                                            </a>
		                               	  <a  title="Eliminar (alt + q)" href="#" class="btn btn-danger btn-sm btn-fab" id="boton_remove<?=$idFila;?>" onclick="minusDetalleSolicitud('<?=$idFila;?>');">
                                           	<i class="material-icons">remove_circle</i>
	                                       </a>
	                                     </div>  
		                               </div>

	                          </div>
                            </div>
                           <div class="h-divider"></div>
                         </div>
                         <script>numFilas++;
                               cantidadItems++;
                               //autocompletar("partida_cuenta"+<?=$idFila;?>,"partida_cuenta_id"+<?=$idFila;?>,array_cuenta);
                            </script>
             <?php  
                  $stmtFacturas = $dbh->prepare("SELECT * FROM facturas_compra where cod_solicitudrecursodetalle=$codDetalleX");
                      $stmtFacturas->execute();
                      while ($rowFacturas = $stmtFacturas->fetch(PDO::FETCH_ASSOC)) {
                            $nit=$rowFacturas['nit'];
                            $factura=$rowFacturas['nro_factura'];
                            $fechaFac=$rowFacturas['fecha'];
                            $razon=$rowFacturas['razon_social'];
                            $importe=$rowFacturas['importe'];
                            $exento=$rowFacturas['exento'];
                            $tipoFac=$rowFacturas['tipo_compra'];
                            $iceFac=$rowFacturas['ice'];
                            $tasaFac=$rowFacturas['tasa_cero'];
                            $autorizacion=$rowFacturas['nro_autorizacion'];
                            $control=$rowFacturas['codigo_control'];
                            ?><script>abrirFactura(<?=$idFila?>,'<?=$nit?>',<?=$factura?>,'<?=$fechaFac?>','<?=$razon?>',<?=$importe?>,<?=$exento?>,'<?=$autorizacion?>','<?=$control?>','<?=$iceFac?>','<?=$tipoFac?>','<?=$tasaFac?>');</script><?php
                        } ?>
                       