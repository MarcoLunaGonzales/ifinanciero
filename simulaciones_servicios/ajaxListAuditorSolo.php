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
$codigo=$_GET['codigo'];
?>

                      
                                <?php 
                                $diasSimulacion=$_GET['dias_simulacion'];
                                $iii=$_GET['cantidad_filas'];
                                $sumaCantidadPre=$_GET['cantidad_personal'];
                               $queryPr="SELECT s.*,t.nombre as tipo_personal FROM simulaciones_servicios_auditores s, tipos_auditor t where s.cod_simulacionservicio=$codigoSimulacionSuper and s.cod_tipoauditor=t.codigo and s.cod_anio=$anio and s.codigo=$codigo";
                               $stmt = $dbh->prepare($queryPr);
                               $stmt->execute();
                               $modal_totalmontopre=0;$modal_totalmontopretotal=0;
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
                                     <td class="small"><?=$tipoPre?><input type="hidden" id="local_extranjero<?=$anio?>FFF<?=$iii?>" value="<?=$codExtLoc?>"></td>
                                     <td>
                                      <input type="number" readonly id="cantidad_personal<?=$anio?>FFF<?=$iii?>" name="cantidad_personal<?=$anio?>FFF<?=$iii?>" class="form-control text-primary text-right" onchange="" onkeyUp="" value="<?=$cantidadEPre?>">
                                      <?php 
                                       $sumaCantidadPre+=$cantidadPre;
                                      ?>
                                      <!--<select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="cantidad_personal<?=$anio?>FFF<?=$iii?>" id="cantidad_personal<?=$anio?>FFF<?=$iii?>" onchange="calcularTotalPersonalServicio('<?=$anio?>',2)">
                                          <?php 
                                             for ($hf=0; $hf<=$cantidadPre; $hf++) {
                                              if($hf==$cantidadEPre){
                                                $sumaCantidadPre+=$cantidadPre;
                                                ?><option value="<?=$hf?>" selected><?=$hf?></option><?php
                                              }else{
                                                  ?><option value="<?=$hf?>"><?=$hf?></option><?php
                                              }      
                                             }
                                          ?>
                                      </select>-->
                                     </td>
                                     <td class="text-center">

                                       <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="dias_personal<?=$anio?>FFF<?=$iii?>" id="dias_personal<?=$anio?>FFF<?=$iii?>" onchange="calcularTotalPersonalServicio('<?=$anio?>',2)">
                                          <?php 
                                             for ($hf=0; $hf<=$diasSimulacion; $hf++) {
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
                                       <input type="<?=$claseDeshabilitado?>" id="modal_montopre<?=$anio?>FFF<?=$iii?>" name="modal_montopre<?=$anio?>FFF<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalPersonalServicio('<?=$anio?>',2)" onkeyUp="calcularTotalPersonalServicio('<?=$anio?>',2)" value="<?=$montoPreSi?>" step="0.01">
                                       <input type="<?=$claseDeshabilitadoOFF?>" id="modal_montopreOFF<?=$anio?>FFF<?=$iii?>" name="modal_montopreOFF<?=$anio?>FFF<?=$iii?>" readonly class="form-control text-info text-right" value="0" step="0.01">
                                       <input type="hidden" id="modal_montopreext<?=$anio?>FFF<?=$iii?>" value="<?=$montoPreext?>">
                                       <input type="hidden" id="modal_montopreloc<?=$anio?>FFF<?=$iii?>" value="<?=$montoPre?>">
                                     </td>
                                     <td class="text-right">
                                       <input type="<?=$claseDeshabilitado?>" id="modal_montopreUSD<?=$anio?>FFF<?=$iii?>" name="modal_montopreUSD<?=$anio?>FFF<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalPersonalServicio('<?=$anio?>',4)" onkeyUp="calcularTotalPersonalServicio('<?=$anio?>',4)" value="<?=$montoPreSiUSD?>" step="0.01">
                                       <input type="<?=$claseDeshabilitadoOFF?>" id="modal_montopreUSDOFF<?=$anio?>FFF<?=$iii?>" name="modal_montopreUSDOFF<?=$anio?>FFF<?=$iii?>" readonly class="form-control text-info text-right" value="0" step="0.01">
                                     </td>
                                     <td class="text-right">
                                       <input type="hidden" id="modal_codigopersonal<?=$anio?>FFF<?=$iii?>" value="<?=$codigoPre?>">
                                       <input type="<?=$claseDeshabilitado?>" id="modal_montopretotal<?=$anio?>FFF<?=$iii?>" name="modal_montopretotal<?=$anio?>FFF<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" value="<?=$montoPreTotal?>" step="0.01">
                                       <input type="<?=$claseDeshabilitadoOFF?>" id="modal_montopretotalOFF<?=$anio?>FFF<?=$iii?>" name="modal_montopretotalOFF<?=$anio?>FFF<?=$iii?>" readonly class="form-control text-info text-right" value="0" step="0.01">
                                     </td>   
                                     <td class="text-right">
                                       <input type="<?=$claseDeshabilitado?>" id="modal_montopretotalUSD<?=$anio?>FFF<?=$iii?>" name="modal_montopretotalUSD<?=$anio?>FFF<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" value="<?=$montoPreTotalUSD?>" step="0.01">
                                       <input type="<?=$claseDeshabilitadoOFF?>" id="modal_montopretotalUSDOFF<?=$anio?>FFF<?=$iii?>" name="modal_montopretotalUSDOFF<?=$anio?>FFF<?=$iii?>" readonly class="form-control text-info text-right" value="0" step="0.01">
                                     </td>
                                     <td>
                                       <div class="togglebutton">
                                               <label>
                                                 <input type="checkbox" <?=($banderaHab==1)?"checked":"";?> id="modal_checkpre<?=$anio?>FFF<?=$iii?>" onchange="activarInputMontoPersonalServicio('<?=$anio?>','<?=$iii?>')">
                                                 <span class="toggle"></span>
                                               </label>
                                       </div>
                                     </td>
                                   </tr>
                                   <?php
                                  $iii++; 
                                  } ?>
                                <script>$("#modal_numeropersonal"+<?=$anio?>).val(<?=$iii?>)</script>
                                <script>$("#modal_cantidadpersonal"+<?=$anio?>).val(<?=$sumaCantidadPre?>)</script>  