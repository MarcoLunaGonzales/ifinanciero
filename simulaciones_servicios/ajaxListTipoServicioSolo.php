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
$anio_fila=$_GET['anio_fila'];
$usd=$_GET['usd'];
$codigo=$_GET['codigo'];
$anioGeneral=$_GET['anio_general'];
?>
                                <?php 
                                $iii=$_GET['cantidad_filas'];
                               $queryPr="SELECT s.*,t.descripcion as nombre_serv FROM simulaciones_servicios_tiposervicio s, cla_servicios t where s.cod_simulacionservicio=$codigoSimulacionSuper and s.cod_claservicio=t.idclaservicio and s.cod_anio=$anio_fila and s.codigo=$codigo";
                               $stmt = $dbh->prepare($queryPr);
                               $stmt->execute();
                               $modal_totalmontopre=0;$modal_totalmontopretotal=0;
                               while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoPre=$rowPre['codigo'];
                                  $codCS=$rowPre['cod_claservicio'];
                                  $tipoPre=$rowPre['nombre_serv'];
                                  $tipoPreEdit=$rowPre['observaciones'];
                                  $cantidadPre=$rowPre['cantidad'];
                                  $cantidadEPre=$rowPre['cantidad_editado'];
                                  $montoPre=$rowPre['monto'];
                                  $montoPreTotal=$montoPre*$cantidadEPre;
                                  $banderaHab=$rowPre['habilitado'];
                                  $codTipoUnidad=$rowPre['cod_tipounidad'];
                                  $codAnioPre=$rowPre['cod_anio'];
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
                                      <td>
                                        <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="anio<?=$anio?>SSS<?=$iii?>" id="anio<?=$anio?>SSS<?=$iii?>">
                                          <?php 
                                          $inicio=0;
                                          if($codAreaX==39){
                                            $inicio=1;
                                          }
                                          for ($i=$inicio; $i <= $anioGeneral; $i++) { 
                                             if($codAreaX!=39){
                                            $etapas="Seg ".($i-1);

                                              if($codAreaX!=39){
                                               if($i==0||$i==1){
                                                if($i==1){
                                                 $etapas="Et ".($i+1)." / REN";  
                                                }else{
                                                 $etapas="Et ".($i+1)."";   
                                                }
                                                
                                               }
                                              }
                                              
                                              }else{
                                               $etapas="Año ".$i; 
                                              } 
                                             if($i==$codAnioPre){
                                                  ?><option value="<?=$i?>" selected><?=$etapas?></option><?php
                                                }else{
                                                  ?><option value="<?=$i?>"><?=$etapas?></option><?php
                                                }
                                          }
                                          ?>
                                      </select>
                                     </td>
                                     <td class="text-left"><i class="material-icons text-warning"><?=$iconServ?></i><input type="hidden" id="precio_fijo<?=$anio?>SSS<?=$iii?>" value="<?=$iconServ?>"> <?=$tipoPre?></td>
                                     <td class="text-right">
                                       <input type="text" id="descripcion_servicios<?=$anio?>SSS<?=$iii?>" name="descripcion_servicios<?=$anio?>SSS<?=$iii?>" class="form-control text-info text-right" value="<?=$tipoPreEdit?>">
                                     </td>
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
                                       <input type="<?=$claseDeshabilitado?>" id="modal_montoserv<?=$anio?>SSS<?=$iii?>" name="modal_montoserv<?=$anio?>SSS<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalFilaServicio(<?=$anio?>,2)" onkeyUp="calcularTotalFilaServicio(<?=$anio?>,2)" value="<?=$montoPre?>" step="0.01">
                                       <input type="<?=$claseDeshabilitadoOFF?>" id="modal_montoservOFF<?=$anio?>SSS<?=$iii?>" name="modal_montoservOFF<?=$anio?>SSS<?=$iii?>" readonly class="form-control text-info text-right" value="0" step="0.01">
                                     </td>
                                     <td class="text-right">
                                       <input type="<?=$claseDeshabilitado?>" id="modal_montoservUSD<?=$anio?>SSS<?=$iii?>" name="modal_montoservUSD<?=$anio?>SSS<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalFilaServicio(<?=$anio?>,4)" onkeyUp="calcularTotalFilaServicio(<?=$anio?>,4)" value="<?=$montoPreUSD?>" step="0.01">
                                       <input type="<?=$claseDeshabilitadoOFF?>" id="modal_montoservUSDOFF<?=$anio?>SSS<?=$iii?>" name="modal_montoservUSDOFF<?=$anio?>SSS<?=$iii?>" readonly class="form-control text-info text-right" value="0" step="0.01">
                                     </td>
                                     <td class="text-right">
                                       <input type="hidden" id="modal_codigoservicio<?=$anio?>SSS<?=$iii?>" value="<?=$codigoPre?>">
                                       <input type="<?=$claseDeshabilitado?>" id="modal_montoservtotal<?=$anio?>SSS<?=$iii?>" name="modal_montoservtotal<?=$anio?>SSS<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalFilaServicio(<?=$anio?>,1)" onkeyUp="calcularTotalFilaServicio(<?=$anio?>,1)" value="<?=$montoPreTotal?>" step="0.01">
                                       <input type="<?=$claseDeshabilitadoOFF?>" id="modal_montoservtotalOFF<?=$anio?>SSS<?=$iii?>" name="modal_montoservtotalOFF<?=$anio?>SSS<?=$iii?>" readonly class="form-control text-info text-right" value="0" step="0.01">
                                     </td>        
                                     <td class="text-right">
                                       <input type="<?=$claseDeshabilitado?>" id="modal_montoservtotalUSD<?=$anio?>SSS<?=$iii?>" name="modal_montoservtotalUSD<?=$anio?>SSS<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalFilaServicio(<?=$anio?>,3)" onkeyUp="calcularTotalFilaServicio(<?=$anio?>,3)" value="<?=$montoPreTotalUSD?>" step="0.01">
                                       <input type="<?=$claseDeshabilitadoOFF?>" id="modal_montoservtotalUSDOFF<?=$anio?>SSS<?=$iii?>" name="modal_montoservtotalUSDOFF<?=$anio?>SSS<?=$iii?>" readonly class="form-control text-info text-right" value="0" step="0.01">
                                     </td>
                                     <td>
                                       <div class="togglebutton">
                                               <label>
                                                 <input type="checkbox" <?=($banderaHab==1)?"checked":"";?> id="modal_checkserv<?=$anio?>SSS<?=$iii?>" onchange="activarInputMontoFilaServicio(<?=$anio?>,'<?=$iii?>')">
                                                 <span class="toggle"></span>
                                               </label>
                                       </div>
                                     </td>
                                   </tr>
                                  <?php
                                  $iii++; 
                                  } ?>
                                <script>$("#modal_numeroservicio"+<?=$anio?>).val(<?=$iii?>)</script>  