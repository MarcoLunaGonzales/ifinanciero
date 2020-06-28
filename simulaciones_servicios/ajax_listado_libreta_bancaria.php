<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';
require_once '../styles.php';
require_once '../layouts/librerias.php';

$saldo=$_GET['saldo'];
?>
<?php
                $codigo=0;
                $lista=obtenerObtenerLibretaBancaria($codigo);
                $saldo_x=$saldo;
              ?>
              <style>
                tfoot input {
                  width: 100%;
                  padding: 3px;
                  
                }
              </style> 
                                     
              <table id="libreta_bancaria_reporte_modal" class="table table-condensed table-bordered table-sm" style="width:100% !important;">
                  <thead>
                    <tr style="background:#21618C; color:#fff;">
                      <th class="text-center" width="3%">#</th>
                      <th class="small" width="5%"><small>Fecha</small></th>      
                      <th class="small" width="30%"><small>Descripción</small></th>      
                      <th class="small" width="5%"><small>Monto</small></th>
                      <th class="small" width="3%"><small><small>N° Ref</small></small></th>
                      <th class="small bg-success" width="4%"><small>Fecha Fac.</small></th>
                      <th class="small bg-success" width="4%"><small>N° Fac.</small></th>      
                      <th class="small bg-success"><small>Nit Fac.</small></th>
                      <th class="small bg-success"><small>Razón Social Fac.</small></th>
                      <th class="small bg-success" width="7%"><small>Monto Fac.</small></th>
                      <!-- <th class="text-right bg-success" width="3%"></th> -->
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    // if($lista->estado==1){
                      $j=1;
                        foreach ($lista->libretas as $v) {
                          $Nombre=$v->Nombre;
                          $Banco=$v->Banco;
                          $detalle=$v->detalle;
                          $index=1;?>
                          <tr>
                            <td class="d-none"></td>
                            <td class="d-none"></td>
                            <td class="d-none"></td>
                            <td class="d-none"></td>                        
                            <td class="d-none"></td>
                            <td class="d-none"></td>
                            <td class="d-none"></td>
                            <td class="d-none"></td>
                            <td class="d-none"></td>
                            <!-- <td class="d-none"></td> -->
                            <td align="center" colspan="10" style="background:#e58400; color:#fff;"><button title="Detalles" id="botonLibreta<?=$j?>" style="border:none; background:#e58400; color:#fff;" onclick="activardetalleLibreta(<?=$j?>)"><small><?=$Banco;?> - <?=$Nombre;?></small></button></td>
                          </tr>     
                          <?php
                            foreach ($detalle as $v_detalle) {
                            $CodLibretaDetalle=$v_detalle->CodLibretaDetalle;
                            $Descripcion=$v_detalle->Descripcion;
                            $InformacionComplementaria=" Info: ".$v_detalle->InformacionComplementaria;
                            $Agencia=$v_detalle->Agencia;
                            $NumeroCheque=$v_detalle->NumeroCheque;
                            $NumeroDocumento=$v_detalle->NumeroDocumento;
                            $Fecha=$v_detalle->Fecha;
                            $Hora=$v_detalle->Hora;
                            $FechaHoraCompleta=$v_detalle->FechaHoraCompleta;
                            $monto=$v_detalle->monto;
                            if(isset($v_detalle->CodFactura))$CodFactura=$v_detalle->CodFactura;
                            else $CodFactura=null;
                            if(isset($v_detalle->FechaFactura))$FechaFactura=$v_detalle->FechaFactura;
                            else $FechaFactura=null;
                            if(isset($v_detalle->NumeroFactura))$NumeroFactura=$v_detalle->NumeroFactura;
                            else $NumeroFactura=null;
                            if(isset($v_detalle->NitFactura))$NitFactura=$v_detalle->NitFactura;
                            else $NitFactura=null;
                            if(isset($v_detalle->RSFactura))$RSFactura=$v_detalle->RSFactura;
                            else $RSFactura=null;
                            if(isset($v_detalle->MontoFactura))$MontoFactura=$v_detalle->MontoFactura;
                            else $MontoFactura=null;
                            ?>
                            <tr>
                              <td style="" class="libretaDetalles_<?=$j?> small" align="center"><?=$index;?></td>
                              <td style="" class="libretaDetalles_<?=$j?> text-center small"><span style="padding:0px;border: 0px;"><?=strftime('%d/%m/%Y',strtotime($FechaHoraCompleta))?><br><?=strftime('%H:%M:%S',strtotime($FechaHoraCompleta))?></span></td>           
                              <td style="" class="libretaDetalles_<?=$j?> text-left ">
                                <?php 
                              if($CodFactura==null || $CodFactura==''||$CodFactura==0){
                                ?><small><small><?=$Descripcion." ".$InformacionComplementaria?></small></small><?php
                              }else{
                                ?>
                                <div id="accordion<?=$index;?>" role="tablist">
                                  <div class="card-collapse">
                                    <div class="card-header" role="tab" id="heading<?=$index;?>">
                                      <p class="mb-0">
                                        <small>
                                           <a data-toggle="collapse" href="#collapse<?=$index;?>" aria-expanded="false" aria-controls="collapse<?=$index;?>" class="collapsed">
                                              <small><small><?=$Descripcion." ".$InformacionComplementaria?></small></small>
                                              <i class="material-icons">keyboard_arrow_down</i>
                                           </a>
                                        </small>
                                      </p>
                                    </div>
                                    <div id="collapse<?=$index;?>" class="collapse" role="tabpanel" aria-labelledby="heading<?=$index;?>" data-parent="#accordion<?=$index;?>" style="">
                                      <div class="card-body">
                                        <?php
                                              $sqlDetalleX="SELECT * FROM facturas_venta where cod_libretabancariadetalle=$CodLibretaDetalle";                                   
                                              $stmtDetalleX = $dbh->prepare($sqlDetalleX);
                                              $stmtDetalleX->execute();

                                              $stmtDetalleX->bindColumn('fecha_factura', $fechaDetalle);
                                              $stmtDetalleX->bindColumn('nro_factura', $nroDetalle);
                                              $stmtDetalleX->bindColumn('nit', $nitDetalle);
                                              $stmtDetalleX->bindColumn('razon_social', $rsDetalle);
                                              $stmtDetalleX->bindColumn('observaciones', $obsDetalle);
                                              $stmtDetalleX->bindColumn('importe', $impDetalle);

                                         ?>
                                          <table width="100%">
                                              <tr class="bg-success text-white">
                                                <th>Fecha</th>
                                                <th>Número</th>
                                                <th>Nit</th>
                                                <th>Razón Social</th>
                                                <th>Detalle</th>
                                                <th>Monto</th>
                                                </tr>
                                           <?php
                                            while ($rowDetalleX = $stmtDetalleX->fetch(PDO::FETCH_BOUND)) {
                                            ?>
                                             <tr>
                                                 <td class="text-center small"><?=$fechaDetalle;?></td>
                                                 <td class="text-left small"><?=$nroDetalle;?></td>
                                                 <td class="text-left small"><?=$nitDetalle;?></td>
                                                 <td class="text-left small"><?=$rsDetalle;?></td>
                                                 <td class="text-left small"><?=$obsDetalle;?></td>
                                                 <td class="text-left small"><?=number_format($impDetalle,2,".",",");?></td>
                                             </tr>
                                              <?php    
                                              }
                                              ?>
                                          </table>
                                       </div>
                                     </div>
                                   </div>
                                 </div>
                                 <?php 
                               }          
                                 ?>
                              </td>
                              <td style="" class="libretaDetalles_<?=$j?> text-right small"><?=number_format($monto,2)?></td>
                              <td style="" class="libretaDetalles_<?=$j?> text-left small"><?=$NumeroDocumento?></td>
                              <td style=" color: #ff0000;" class="libretaDetalles_<?=$j?> text-center small"><?=$FechaFactura?></td>
                              <td style=" color: #ff0000;" class="libretaDetalles_<?=$j?> text-right small"><?=$NumeroFactura?></td>            
                              <td style=" color: #ff0000;" class="libretaDetalles_<?=$j?> text-right small"><?=$NitFactura?></td>
                              <td style=" color: #ff0000;" class="libretaDetalles_<?=$j?> text-left"><small><small><?=$RSFactura?></small></small></td>
                              <td style=" color: #ff0000;" class="libretaDetalles_<?=$j?> text-right small"><?=$MontoFactura?> <?php
                                if($CodFactura==null || $CodFactura==''||$CodFactura==0){?>
                                  <a href="#" style="padding: 0;font-size:10px;width:25px;height:25px;" onclick="seleccionar_libretaBancaria(<?=$CodLibretaDetalle?>)" class="btn btn-fab btn-success btn-sm" title="Seleccionar Item"><i class="material-icons">done</i></a>
                                <?php }else{?>
                                  <a href="#" style="padding: 0;font-size:10px;width:25px;height:25px;" onclick="seleccionar_libretaBancaria(<?=$CodLibretaDetalle?>)" class="btn btn-fab btn-warning btn-sm" title="Seleccionar Item"><i class="material-icons">done</i></a>
                                <?php  
                                }?></td>
                              <!-- <td style=" color: #ff0000;" class="libretaDetalles_<?=$j?> td-actions text-right small">
                                
                              </td> -->
                            </tr>
                          <?php
                          $index++;
                          }
                          $j++;
                      }
                    // }
                    ?>
                  </tbody>
                  <tfoot>
                    <tr style="background:#21618C; color:#fff;">
                      <td class="text-center" width="3%">#</td>
                      <th class="small" width="5%"><small>Fecha</small></th>      
                      <th class="small" width="30%"><small>Información Complementaria</small></th>      
                      <th class="small" width="5%"><small>Monto</small></th>
                      <th class="small" width="3%"><small><small>N° Ref</small></small></th>
                      <th class="small bg-success" width="4%"><small>Fecha<br>Fac.</small></th>
                      <th class="small bg-success" width="4%"><small>N° Fac.</small></th>      
                      <th class="small bg-success"><small>Nit Fac.</small></th>
                      <th class="small bg-success"><small>Razón Social Fac.</small></th>
                      <th class="small bg-success" width="7%"><small>Monto Fac.</small></th>
                      <!-- <th class="text-right bg-success" width="3%"></th> -->
                    </tr>
                  </tfoot>
              </table>