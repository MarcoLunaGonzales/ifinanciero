<?php

$fila=$cantidadProveedores;
$lista=listaObligacionesPagoDetalleSolicitudRecursosProveedorPagosLotes($codigo,$codPagoLote);
$totalPagadoX=0;

                 $index=1;$cont=0;$totalImporte=0;

                        while ($row = $lista->fetch(PDO::FETCH_ASSOC)) {
                          $codDetalle=$row['codigo'];
                          $unidad=$row['unidad'];
                          $area=$row['area'];
                          $solicitante=namePersonal($row['cod_personal']);
                          $fecha=$row['fecha'];
                          $numero=$row['numero'];
                          $detalle=$row['detalle'];
                          $importe=$row['importe'];
                          $proveedor=$row['proveedor'];
                          $codProveedor=$row['cod_proveedor'];
                          $codPlancuenta=$row['cod_plancuenta'];
                          $codSol=$row['cod_solicitudrecurso'];
                          $monto_pagado=$row['monto_pagado'];
                          $codSolDet=$codDetalle;

                          $dias=obtenerCantidadDiasCredito($codProveedor);
                          $pagadoFila=obtenerMontoPagadoDetalleSolicitud($codSol,$codDetalle);
                          if($dias==0){
                            $tituloDias="Sin Registro";
                          }else{
                            $tituloDias="".$dias;
                          }
                          $totalImporte+=$importe;
                          $saldoImporte=abs($pagadoFila-$importe);
                          $saldoImporte=$saldoImporte+$monto_pagado;
                          $pagado=$importe-$saldoImporte;
                          
                          $numeroComprobante=nombreComprobante($row['cod_comprobante']);
                          $codTipoPago=$row['cod_tipopagoproveedor'];
                          $nomBen=$row['nombre_beneficiario'];
                          $apellBen=$row['apellido_beneficiario'];
                          $fechaPagado=strftime('%d/%m/%Y',strtotime($row['fecha_pagado']));
                          $tipoPagado=$row['tipo_pagado'];
                          $cod_detallepago=$row['cod_detallepago'];
?>
                        <tr class="fila_proveedor<?=$fila?>">
                          <td class="text-left">
                            
                            <input type="hidden" value="<?=$codigo?>" id="codigo_proveedor_modal<?=$fila?>" name="codigo_proveedor_modal<?=$fila?>">
                            <?php 
                            if($index==1){
                              ?><input type="hidden" value="" id="cantidad_filas<?=$fila?>" name="cantidad_filas<?=$fila?>"><?php
                            }
                            ?>
                            <input type="hidden" value="<?=$cod_detallepago?>" id="codigo_detallepago<?=$index?>PPPP<?=$fila?>" name="codigo_detallepago<?=$index?>PPPP<?=$fila?>">
                            <input type="hidden" value="<?=$detalle?>" id="glosa_detalle<?=$index?>PPPP<?=$fila?>" name="glosa_detalle<?=$index?>PPPP<?=$fila?>">
                            <input type="hidden" value="<?=$codProveedor?>" id="codigo_proveedor<?=$index?>PPPP<?=$fila?>" name="codigo_proveedor<?=$index?>PPPP<?=$fila?>">
                            <input type="hidden" value="<?=$codSol?>" id="codigo_solicitud<?=$index?>PPPP<?=$fila?>" name="codigo_solicitud<?=$index?>PPPP<?=$fila?>">
                            <input type="hidden" value="<?=$codSolDet?>" id="codigo_solicitudDetalle<?=$index?>PPPP<?=$fila?>" name="codigo_solicitudDetalle<?=$index?>PPPP<?=$fila?>">
                            <input type="hidden" value="<?=$codPlancuenta?>" id="codigo_plancuenta<?=$index?>PPPP<?=$fila?>" name="codigo_plancuenta<?=$index?>PPPP<?=$fila?>">
                            <?=$proveedor;?></td>
                          <td class="text-left"><?=$detalle;?></td>
                          <td class="text-left"><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>  
                          <td class=""><?=$numero;?></td>
                          <td><?=$numeroComprobante?></td>
                          <td><?=$unidad?></td>
                          <td class="bg-warning text-dark text-right font-weight-bold"><?=number_format($importe,2,".","")?></td>
                          <td class="text-right font-weight-bold" style="background:#07B46D; color:#F7FF5A;"><?=number_format($pagado,2,".","")?></td>
                          <td id="saldo_pago<?=$index?>PPPP<?=$fila?>" class="text-right font-weight-bold"><?=number_format($importe-$pagado,2,".","")?></td>
                          <td class="text-right">
                            <?php 
                            if(($importe-$pagado)>0){
                              ?>
                              <input type="number" step="0.01" class="form-control text-right text-success" value="<?=$monto_pagado?>" id="monto_pago<?=$index?>PPPP<?=$fila?>" name="monto_pago<?=$index?>PPPP<?=$fila?>">
                              
                              <?php
                            }else{
                              ?>
                              <input type="number" step="0.01" class="form-control text-right text-success" readonly value="<?=$monto_pagado?>" id="monto_pago<?=$index?>PPPP<?=$fila?>" name="monto_pago<?=$index?>PPPP<?=$fila?>">
                              <?php
                            } 
                            ?>
                            
                          </td>
                          <td><input type="text" class="form-control datepicker" value="<?=$fechaPagado?>" id="fecha_pago<?=$index?>PPPP<?=$fila?>" name="fecha_pago<?=$index?>PPPP<?=$fila?>"></td>
                          <td>
                            <div class="form-group">
                               <select class="selectpicker form-control form-control-sm" onchange="mostrarDatosChequeDetalle('<?=$index?>PPPP<?=$fila?>')" data-live-search="true" name="tipo_pago<?=$index?>PPPP<?=$fila?>" id="tipo_pago<?=$index?>PPPP<?=$fila?>" data-style="btn btn-danger">
                                    <option disabled value="">--TIPO--</option>
                                    <?php 
                                     $stmt3 = $dbh->prepare("SELECT * from tipos_pagoproveedor where codigo=2 and cod_estadoreferencial=1");
                                     $stmt3->execute();
                                     while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                                      $codigoSel=$rowSel['codigo'];
                                      $nombreSelX=$rowSel['nombre'];
                                      $abrevSelX=$rowSel['abreviatura'];
                                      if($tipoPagado==$codigoSel){
                                         ?><option selected value="<?=$codigoSel;?>"><?=$abrevSelX?></option><?php 
                                      } 
                                     }
                                    ?>
                                  </select>
                             </div>
                          </td>
                          <td>
                            <div class="d-none" id="div_cheques<?=$index?>PPPP<?=$fila?>">                    
                                <div class="form-group">
                                     <select class="selectpicker form-control form-control-sm" onchange="cargarChequesPagoDetalle('<?=$index?>PPPP<?=$fila?>')" data-live-search="true" name="banco_pago<?=$index?>PPPP<?=$fila?>" id="banco_pago<?=$index?>PPPP<?=$fila?>" data-style="btn btn-danger">
                                    <option disabled selected="selected" value="">--BANCOS--</option>
                                    <?php 
                                     $stmt3 = $dbh->prepare("SELECT * from bancos where cod_estadoreferencial=1");
                                     $stmt3->execute();
                                     while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                                      $codigoSel=$rowSel['codigo'];
                                      $nombreSelX=$rowSel['nombre'];
                                      $abrevSelX=$rowSel['abreviatura'];
                                      //if($codBanco==$codigoSel){
                                       
                                      //}else{
                                       ?><option value="<?=$codigoSel;?>"><?=$abrevSelX?></option><?php 
                                      //}
                                     }
                                    ?>
                                      </select>
                                  </div>
                             </div>
                          </td>
                          <td>
                            <div id="div_chequesemitidos<?=$index?>PPPP<?=$fila?>">                    
                             </div>
                          </td>
                          <td>
                            <input type="number" readonly class="form-control text-right" readonly value="0" id="numero_cheque<?=$index?>PPPP<?=$fila?>" name="numero_cheque<?=$index?>PPPP<?=$fila?>">
                          </td>
                          <td>
                            <input type="text" readonly class="form-control" readonly value="<?=$nomBen?> <?=$apellBen?>" id="beneficiario<?=$index?>PPPP<?=$fila?>" name="beneficiario<?=$index?>PPPP<?=$fila?>">
                          </td>
                        </tr>
                        <script>mostrarDatosChequeDetalle('<?=$index?>PPPP<?=$fila?>');</script>
<?php
              $index++;
                      }

                      if($index>1){
                        $proveedor_nombre=$proveedorNombre;
                        ?><script>$(document).ready(function() { 
                          var html='<tr id="f_proveedor<?=$fila?>"><td class="text-left"><?=$proveedor_nombre?></td><td><div class="btn-group"><button class="btn btn-sm btn-fab btn-danger" title="Eliminar" onclick="removeListaPago(<?=$fila?>);"><i class="material-icons">delete</i></button></div></td></tr>';
                          $("#tabla_proveedor").append(html);$("#cantidad_filas<?=$fila?>").val(<?=$index-1?>);
                      });</script><?php
                      }
        
?>             