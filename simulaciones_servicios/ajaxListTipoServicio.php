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

?>

                       <table class="table table-bordered table-condensed table-striped table-sm">
                             <thead>
                                  <tr class="fondo-boton">
                                    <td>#</td>
                                    <td width="30%">Descripci&oacute;n</td>
                                    <td>Cantidad</td>
                                    <td width="17%">Unidad</td>
                                    <td>Monto</td>
                                    <td>Total</td>
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
                                  <select class="selectpicker form-control form-control-sm" data-live-search="true" name="modal_editservicio" id="modal_editservicio" data-style="fondo-boton">
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
                                       <input type="number" min="1" id="cantidad_servicios0" name="cantidad_servicios0" class="form-control text-primary text-right" onchange="calcularTotalFilaServicioNuevo(2)" onkeyUp="calcularTotalFilaServicioNuevo(2)" value="1">
                                  </td>
                                  <td>
                                      <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="unidad_servicios0" id="unidad_servicios0" onchange="calcularTotalFilaServicioNuevo(2)">
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
                                       <input type="number" id="modal_montoserv0" name="modal_montoserv0" class="form-control text-primary text-right" onchange="calcularTotalFilaServicioNuevo(2)" onkeyUp="calcularTotalFilaServicioNuevo(2)" value="0" step="0.01">
                                    </td>
                                     <td class="text-right">
                                       <input type="number" id="modal_montoservtotal0" name="modal_montoservtotal0" class="form-control text-primary text-right" onchange="calcularTotalFilaServicioNuevo(1)" onkeyUp="calcularTotalFilaServicioNuevo(1)" value="0" step="0.01">
                                     </td>
                                  <td>
                                    <div class="btn-group">
                                       <a href="#" class="btn btn-primary btn-sm" id="boton_modalnuevoservicio" onclick="agregarNuevoServicioSimulacion(<?=$codigoSimulacionSuper?>); return false;">
                                         Agregar
                                       </a>
                                     </div>
                                  </td>
                                </tr>
                                <?php 
                                $iii=1;
                               $queryPr="SELECT s.*,t.descripcion as nombre_serv FROM simulaciones_servicios_tiposervicio s, cla_servicios t where s.cod_simulacionservicio=$codigoSimulacionSuper and s.cod_claservicio=t.idclaservicio order by s.codigo";
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
                                   ?>
                                   <tr>
                                     <td><?=$iii?></td>
                                     <td class="text-left"><i class="material-icons text-warning"><?=$iconServ?></i><input type="hidden" id="precio_fijo<?=$iii?>" value="<?=$iconServ?>"> <?=$tipoPre?></td>
                                     <td class="text-right">
                                       <input type="number" min="1" id="cantidad_servicios<?=$iii?>" name="cantidad_servicios<?=$iii?>" class="form-control text-info text-right" onchange="calcularTotalFilaServicio(2)" onkeyUp="calcularTotalFilaServicio(2)" value="<?=$cantidadEPre?>">
                                     </td>
                                     <!--<td>
                                      <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="cantidad_servicios<?=$iii?>" id="cantidad_servicios<?=$iii?>" onchange="calcularTotalFilaServicio(2)">
                                          <?php 
                                             for ($hf=1; $hf<=$cantidadPre; $hf++) {
                                              if($hf==$cantidadEPre){
                                                ?><option value="<?=$hf?>" selected><?=$hf?></option><?php
                                              }else{
                                                  ?><option value="<?=$hf?>"><?=$hf?></option><?php
                                              }      
                                             }
                                          ?>
                                      </select>
                                     </td>-->
                                     <td>
                                      <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="unidad_servicios<?=$iii?>" id="unidad_servicios<?=$iii?>" onchange="calcularTotalFilaServicio(2)">
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
                                       <input type="number" id="modal_montoserv<?=$iii?>" name="modal_montoserv<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalFilaServicio(2)" onkeyUp="calcularTotalFilaServicio(2)" value="<?=$montoPre?>" step="0.01">
                                     </td>
                                     <td class="text-right">
                                       <input type="hidden" id="modal_codigoservicio<?=$iii?>" value="<?=$codigoPre?>">
                                       <input type="number" id="modal_montoservtotal<?=$iii?>" name="modal_montoservtotal<?=$iii?>" <?=($banderaHab==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalFilaServicio(1)" onkeyUp="calcularTotalFilaServicio(1)" value="<?=$montoPreTotal?>" step="0.01">
                                     </td>
                                     <td>
                                       <div class="togglebutton">
                                               <label>
                                                 <input type="checkbox" <?=($banderaHab==1)?"checked":"";?> onchange="activarInputMontoFilaServicio('<?=$iii?>')">
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
                                     <td id="modal_totalmontoserv" class="text-right"><?=$modal_totalmontopre?></td>
                                     <td id="modal_totalmontoservtotal" class="text-right font-weight-bold"><?=$modal_totalmontopretotal?></td>
                                     <td></td>
                                   </tr>
                              </tbody>
                           </table>
                           <input type="hidden" id="modal_numeroservicio" value="<?=$iii?>">