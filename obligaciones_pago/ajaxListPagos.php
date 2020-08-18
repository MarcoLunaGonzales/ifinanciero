<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
$codigo=$_GET['proveedor'];


$lista=listaObligacionesPagoDetalleSolicitudRecursosProveedor($codigo);
$totalPagadoX=0;

?>
                <div class="col-sm-12 table-responsive">
                  <center><h4 class="text-success"><u>Lista de Pagos Pendientes</u></h4></center>
                    <table id="tablePaginatorHeaderFooter" class="table table-condensed small">
                      <thead>
                        <tr style="background:#21618C; color:#fff;">
                          <th width="20%">Proveedor</th>
                          <th width="20%">Detalle</th>
                          <th>F. Sol</th>     
                          <th>Nº Sol</th>
                          <th>Nº Comp</th>
                          <th>Oficina</th>
                          <th>Importe</th>
                          <th>Pagado</th>
                          <th>Saldo</th>
                          <th width="10%">Monto</th>
                          <th width="10%">Fecha Pago</th>
                          <th width="10%">Tipo</th>
                          <th width="10%">Bancos</th>
                          <th width="10%">Cheques</th>
                          <th width="10%">Nº Cheque</th>
                          <th width="10%">Beneficiario</th>
                        </tr>
                      </thead>
                      <tbody>
                      	<?php
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
                          $pagado=$importe-$saldoImporte;
                          
                          $numeroComprobante=nombreComprobante($row['cod_comprobante']);
                          $codTipoPago=$row['cod_tipopagoproveedor'];
                          $nomBen=$row['nombre_beneficiario'];
                          $apellBen=$row['apellido_beneficiario'];
                          $tituloDetalle=$detalle;
                          if(strlen($detalle)>15){
                            $tituloDetalle='<a href="#" data-toggle="collapse" data-target="#demo'.$index.'">'.substr($detalle,0,15).' ver más...</a>';
                          }
                          if($importe-$pagado>0){
                            ?>
                        <tr>
                          <td class="text-left">
                            <input type="hidden" value="<?=$detalle?>" id="glosa_detalle<?=$index?>" name="glosa_detalle<?=$index?>">
                            <input type="hidden" value="<?=$codProveedor?>" id="codigo_proveedor<?=$index?>" name="codigo_proveedor<?=$index?>">
                            <input type="hidden" value="<?=$codSol?>" id="codigo_solicitud<?=$index?>" name="codigo_solicitud<?=$index?>">
                            <input type="hidden" value="<?=$codSolDet?>" id="codigo_solicitudDetalle<?=$index?>" name="codigo_solicitudDetalle<?=$index?>">
                            <input type="hidden" value="<?=$codPlancuenta?>" id="codigo_plancuenta<?=$index?>" name="codigo_plancuenta<?=$index?>">
                            <?=$proveedor;?></td>
                          <!--<td>                        
                            <?php 
                            if(($importe-$pagado)>0){
                             ?><img src="assets/img/progresa.jpg" alt="" width="80px" height="35px"><?php
                            }else{
                              ?><img src="assets/img/cancelado.png" alt="" width="80px" height="35px"><?php 
                            }?> 
                          </td>-->
                          <td class="text-left">
                            <?php echo $tituloDetalle;?>
                              <?php 
                             if(strlen($detalle)>15){
                              ?><div id="demo<?=$index?>" class="collapse">
                                  <?=$detalle;?>
                               </div><?php
                             } 
                              ?>   
                          <td class="text-left"><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>  
                          <td class=""><?=$numero;?></td>
                          <td><?=$numeroComprobante?></td>
                          <td><?=$unidad?></td>
                          <td class="bg-warning text-dark text-right font-weight-bold"><?=number_format($importe,2,".","")?></td>
                          <td class="text-right font-weight-bold" style="background:#07B46D; color:#F7FF5A;"><?=number_format($pagado,2,".","")?></td>
                          <td id="saldo_pago<?=$index?>" class="text-right font-weight-bold"><?=number_format($importe-$pagado,2,".","")?></td>
                          <td class="text-right">
                            <?php 
                            if(($importe-$pagado)>0){
                              ?>
                              <input type="number" step="any" min="1000" required class="form-control text-right text-success" value="0" id="monto_pago<?=$index?>" name="monto_pago<?=$index?>">
                              
                              <?php
                            }else{
                              ?>
                              <input type="number" step="any" min="1000" required class="form-control text-right text-success" readonly value="0" id="monto_pago<?=$index?>" name="monto_pago<?=$index?>">
                              <?php
                            } 
                            ?>
                            
                          </td>
                          <td><input type="text" class="form-control datepicker" value="<?=date('d/m/Y')?>" id="fecha_pago<?=$index?>" name="fecha_pago<?=$index?>"></td>
                          <td>
                            <div class="form-group">
                               <select class="selectpicker form-control form-control-sm" onchange="mostrarDatosChequeDetalle(<?=$index?>)" data-live-search="true" name="tipo_pago<?=$index?>" id="tipo_pago<?=$index?>" data-style="btn btn-danger" required>
                                    <option disabled selected="selected" value="">--TIPO--</option>
                                    <?php 
                                     $stmt3 = $dbh->prepare("SELECT * from tipos_pagoproveedor where cod_estadoreferencial=1");
                                     $stmt3->execute();
                                     while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                                      $codigoSel=$rowSel['codigo'];
                                      $nombreSelX=$rowSel['nombre'];
                                      $abrevSelX=$rowSel['abreviatura'];
                                      if($codTipoPago==$codigoSel){
                                         ?><option selected value="<?=$codigoSel;?>"><?=$abrevSelX?></option><?php 
                                      }else{
                                         ?><option value="<?=$codigoSel;?>"><?=$abrevSelX?></option><?php 
                                      } 
                                     }
                                    ?>
                                  </select>
                             </div>
                          </td>
                          <td>
                            <div class="d-none" id="div_cheques<?=$index?>">                    
                                <div class="form-group">
                                     <select class="selectpicker form-control form-control-sm" onchange="cargarChequesPagoDetalle(<?=$index?>)" data-live-search="true" data-size="5" name="banco_pago<?=$index?>" id="banco_pago<?=$index?>" data-style="btn btn-danger">
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
                            <div id="div_chequesemitidos<?=$index?>">                    
                             </div>
                          </td>
                          <td>
                            <input type="number" readonly class="form-control text-right" readonly value="0" id="numero_cheque<?=$index?>" name="numero_cheque<?=$index?>">
                          </td>
                          <td>
                            <input type="text" readonly class="form-control" readonly value="<?=$nomBen?> <?=$apellBen?>" id="beneficiario<?=$index?>" name="beneficiario<?=$index?>">
                          </td>
                        </tr>
                        <script>mostrarDatosChequeDetalle(<?=$index?>);</script>
<?php
              $index++;
                          }
                        
                      }

          // $saldoX=$totalImporte-$totalPagadoX;
           //$saldoXInput=number_format($saldoX,2,".","");           
?>
                      </tbody>
                      <!--<tfoot>
                        <tr style="background:#21618C; color:#fff;">
                          <th class="small">Proveedor</th>
                          <th class="small">Detalle</th>
                          <th class="small">F. Sol</th>     
                          <th class="small">Nº Sol</th>
                          <th class="small">Nº Comp</th>
                          <th class="small">Oficina</th>
                          <th class="small">Importe</th>
                          <th class="small">Pagado</th>
                          <th class="small">Saldo</th>
                          <th class="small">Monto</th>
                          <th class="small">Fecha Pago</th>
                          <td class="small">Tipo</td>
                          <td class="small">Bancos</td>
                          <td class="small">Cheques</td>
                          <td class="small">Nº Cheque</td>
                          <td class="small">Beneficiario</td>       
                         </tr>
                      </tfoot>-->
                    </table>
                  </div>
                  <input type="hidden" value="<?=$index-1?>" id="cantidad_filas" name="cantidad_filas">
   <script type="text/javascript">
        $(document).ready(function(e) {
           if(!($("body").hasClass("sidebar-mini"))){
           	 $("#minimizeSidebar").click()
           } 
        });
    </script>                