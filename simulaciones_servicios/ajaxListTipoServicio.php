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


$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$codAreaX=$_GET['cod_area'];
$codigoSimulacionSuper=$_GET['cod_sim'];
$anio=$_GET['anio'];
$usd=$_GET['usd'];
?>

                       <table class="table table-bordered table-condensed table-striped table-sm">
                             <thead>
                                  <tr class="fondo-boton">
                                    <td colspan="4"></td>
                                    <td colspan="2">MONTO</td>
                                    <td colspan="2">TOTAL</td>
                                    <td></td>
                                  </tr>
                                  <tr class="fondo-boton">
                                    <td>#</td>
                                    <td width="30%">Descripci&oacute;n</td>
                                    <td>Cantidad</td>
                                    <td width="17%">Unidad</td>
                                    <td>BOB</td>
                                    <td>USD</td>
                                    <td>BOB</td>
                                    <td>USD</td>
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
                                  <select class="selectpicker form-control form-control-sm" data-live-search="true" name="modal_editservicio<?=$anio?>" id="modal_editservicio<?=$anio?>" data-style="fondo-boton">
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
                                       <input type="number" min="1" id="cantidad_servicios<?=$anio?>SSS0" name="cantidad_servicios<?=$anio?>SSS0" class="form-control text-primary text-right" onchange="calcularTotalFilaServicioNuevo(<?=$anio?>,2)" onkeyUp="calcularTotalFilaServicioNuevo(<?=$anio?>,2)" value="1">
                                  </td>
                                  <td>
                                      <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="unidad_servicios<?=$anio?>SSS0" id="unidad_servicios<?=$anio?>SSS0" onchange="calcularTotalFilaServicioNuevo(<?=$anio?>,2)">
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
                                       <input type="number" id="modal_montoserv<?=$anio?>SSS0" name="modal_montoserv<?=$anio?>SSS0" class="form-control text-primary text-right" onchange="calcularTotalFilaServicioNuevo(<?=$anio?>,2)" onkeyUp="calcularTotalFilaServicioNuevo(<?=$anio?>,2)" value="0" step="0.01">
                                    </td>
                                    <td class="text-right">
                                       <input type="number" id="modal_montoservUSD<?=$anio?>SSS0" name="modal_montoservUSD<?=$anio?>SSS0" class="form-control text-primary text-right" onchange="calcularTotalFilaServicioNuevo(<?=$anio?>,4)" onkeyUp="calcularTotalFilaServicioNuevo(<?=$anio?>,4)" value="0" step="0.01">
                                    </td>
                                     <td class="text-right">
                                       <input type="number" id="modal_montoservtotal<?=$anio?>SSS0" name="modal_montoservtotal<?=$anio?>SSS0" class="form-control text-primary text-right" onchange="calcularTotalFilaServicioNuevo(<?=$anio?>,1)" onkeyUp="calcularTotalFilaServicioNuevo(<?=$anio?>,1)" value="0" step="0.01">
                                     </td>
                                     
                                     <td class="text-right">
                                       <input type="number" id="modal_montoservtotalUSD<?=$anio?>SSS0" name="modal_montoservtotalUSD<?=$anio?>SSS0" class="form-control text-primary text-right" onchange="calcularTotalFilaServicioNuevo(<?=$anio?>,3)" onkeyUp="calcularTotalFilaServicioNuevo(<?=$anio?>,3)" value="0" step="0.01">
                                     </td>
                                  <td>
                                    <div class="btn-group">
                                       <a href="#" class="btn btn-primary btn-sm" id="boton_modalnuevoservicio<?=$anio?>" onclick="agregarNuevoServicioSimulacion(<?=$anio?>,<?=$codigoSimulacionSuper?>,<?=$codAreaX?>); return false;">
                                         Agregar
                                       </a>
                                     </div>
                                  </td>
                                </tr>
                                <?php 
                                $iii=1;
                               $queryPr="SELECT s.*,t.descripcion as nombre_serv FROM simulaciones_servicios_tiposervicio s, cla_servicios t where s.cod_simulacionservicio=$codigoSimulacionSuper and s.cod_claservicio=t.idclaservicio and s.cod_anio=$anio order by s.codigo";
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
                                  if($banderaHab!=0){
                                    $modal_totalmontopre+=$montoPre;
                                    $modal_totalmontopretotal+=$montoPreTotal;
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
                                     <td class="text-left"><i class="material-icons text-warning"><?=$iconServ?></i><input type="hidden" id="precio_fijo<?=$anio?>SSS<?=$iii?>" value="<?=$iconServ?>"> <?=$tipoPre?></td>
                                     <td class="text-right">
                                       <input type="number" min="1" id="cantidad_servicios<?=$anio?>SSS<?=$iii?>" name="cantidad_servicios<?=$anio?>SSS<?=$iii?>" class="form-control text-info text-right" onchange="calcularTotalFilaServicio(<?=$anio?>,2)" onkeyUp="calcularTotalFilaServicio(<?=$anio?>,2)" value="<?=$cantidadEPre?>">
                                     </td>
                                     <td>
                                      <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="unidad_servicios<?=$anio?>SSS<?=$iii?>" id="unidad_servicios<?=$anio?>SSS<?=$iii?>" onchange="calcularTotalFilaServicio(<?=$anio?>,2)">
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
                                       <input type="number" id="modal_montoserv<?=$anio?>SSS<?=$iii?>" name="modal_montoserv<?=$anio?>SSS<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalFilaServicio(<?=$anio?>,2)" onkeyUp="calcularTotalFilaServicio(<?=$anio?>,2)" value="<?=$montoPre?>" step="0.01">
                                     </td>
                                     <td class="text-right">
                                       <input type="number" id="modal_montoservUSD<?=$anio?>SSS<?=$iii?>" name="modal_montoservUSD<?=$anio?>SSS<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalFilaServicio(<?=$anio?>,4)" onkeyUp="calcularTotalFilaServicio(<?=$anio?>,4)" value="<?=$montoPreUSD?>" step="0.01">
                                     </td>
                                     <td class="text-right">
                                       <input type="hidden" id="modal_codigoservicio<?=$anio?>SSS<?=$iii?>" value="<?=$codigoPre?>">
                                       <input type="number" id="modal_montoservtotal<?=$anio?>SSS<?=$iii?>" name="modal_montoservtotal<?=$anio?>SSS<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalFilaServicio(<?=$anio?>,1)" onkeyUp="calcularTotalFilaServicio(<?=$anio?>,1)" value="<?=$montoPreTotal?>" step="0.01">
                                     </td>        
                                     <td class="text-right">
                                       <input type="number" id="modal_montoservtotalUSD<?=$anio?>SSS<?=$iii?>" name="modal_montoservtotalUSD<?=$anio?>SSS<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalFilaServicio(<?=$anio?>,3)" onkeyUp="calcularTotalFilaServicio(<?=$anio?>,3)" value="<?=$montoPreTotalUSD?>" step="0.01">
                                     </td>
                                     <td>
                                       <div class="togglebutton">
                                               <label>
                                                 <input type="checkbox" <?=($banderaHab==1)?"checked":"";?> onchange="activarInputMontoFilaServicio(<?=$anio?>,'<?=$iii?>')">
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
                                     <td id="modal_totalmontoserv<?=$anio?>" class="text-right"><?=number_format($modal_totalmontopre,2, ',', '')?></td>
                                     <td id="modal_totalmontoservUSD<?=$anio?>" class="text-right"><?=number_format($modal_totalmontopre/$usd,2,', ','')?></td>
                                     <td id="modal_totalmontoservtotal<?=$anio?>" class="text-right font-weight-bold"><?=number_format($modal_totalmontopretotal,2, ',', '')?></td>
                                     <td id="modal_totalmontoservtotalUSD<?=$anio?>" class="text-right font-weight-bold"><?=number_format($modal_totalmontopretotal/$usd,2, ',', '')?></td>
                                     <td></td>
                                   </tr>
                              </tbody>
                           </table>
                           <input type="hidden" id="modal_numeroservicio<?=$anioio?>" value="<?=$iii?>">