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

$codigo=$_GET['plantilla'];
$diasAuditoriaX=$_GET['dias_auditoria'];
?>
<table class="table table-bordered table-condensed">
                                    <thead>
                                        <tr class="fondo-boton">
                                            <th>#</th>
                                            <th>Descripci&oacute;n</th>
                                            <th>Cantidad</th>
                                            <th>D&iacute;as</th>
                                            <th>Monto Bolivia</th>
                                            <th>Total Bolivia</th>
                                            <th>Monto Extranjero</th>
                                            <th>Total Extranjero</th>
                                            <th>Quitar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $index11=1;$total11=0;$sumaCantidad=0;$sumaCantidad2=0;$total11Ext=0;
                                         $stmt3 = $dbh->prepare("SELECT codigo,nombre,abreviatura from tipos_auditor where cod_estadoreferencial=1");
                                          $stmt3->execute();
                                          while ($rowServ = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                                          $codigo11=$rowServ['codigo'];     
                                          $descripcion11=$rowServ['nombre'];

                                        $sql11="SELECT s.*,c.nombre,c.codigo as auditor_cod from plantillas_servicios_auditores s,tipos_auditor c where s.cod_plantillaservicio=$codigo and s.cod_tipoauditor=c.codigo and c.codigo=$codigo11";
                                        $stmt11 = $dbh->prepare($sql11);
                                        $stmt11->execute();
                                        
                                        $cantidad11=0;$dias11=$diasAuditoriaX;$monto11=0;
                                        $bgFila="";$idRemove=0;$monto11Ext=0;
                                       while ($rowServ11 = $stmt11->fetch(PDO::FETCH_ASSOC)) {
                                             $idRemove=$rowServ11['codigo'];
                                             $cantidad11=$rowServ11['cantidad'];
                                             $dias11=$rowServ11['dias'];
                                             $monto11=$rowServ11['monto'];
                                             $monto11Ext=$rowServ11['monto_externo'];
                                             $bgFila="bg-warning";
                                             $sumaCantidad+=$cantidad11;
                                             $sumaCantidad2+=$cantidad11*$dias11;

                                       }
                                          $montoTotal11=$cantidad11*$monto11*$dias11;
                                          $montoTotal11Ext=$cantidad11*$monto11Ext*$dias11;
                                          $total11+=$montoTotal11;
                                          $total11Ext+=$montoTotal11Ext;
                                          ?>
                                       <tr class="<?=$bgFila?>">
                                         <td><input type="hidden" id="codigo_personal<?=$index11?>" value="<?=$codigo11?>"><?=$index11?></td>
                                         <td><?=$descripcion11?></td>                                         
                                         <td class="text-right"><input type="number" min="0" id="cantidad_personal<?=$index11?>" class="form-control text-right" value="<?=$cantidad11?>" onkeyup="calcularMontoFilaPersonalServicio(<?=$index11?>)" onkeydown="calcularMontoFilaPersonalServicio(<?=$index11?>)"></td>
                                         <td class="text-right"><input type="number" min="0" max="<?=$diasAuditoriaX?>" id="dias_personal<?=$index11?>" class="form-control text-right" value="<?=$dias11?>" onkeyup="calcularMontoFilaPersonalServicio(<?=$index11?>)" onkeydown="calcularMontoFilaPersonalServicio(<?=$index11?>)"></td>
                                         <td class="text-right"><input type="number" step="0.01" min="0" id="monto_personal<?=$index11?>" class="form-control text-right" value="<?=number_format($monto11, 2, '.', '');?>" onkeyup="calcularMontoFilaPersonalServicio(<?=$index11?>)" onkeydown="calcularMontoFilaPersonalServicio(<?=$index11?>)"></td>
                                         <td class="text-right"><input type="number" step="0.01" readonly min="0" id="total_personal<?=$index11?>" class="form-control text-right" value="<?=number_format($montoTotal11, 2, '.', '');?>"></td>
                                         <td class="text-right"><input type="number" step="0.01" min="0" id="monto_personalext<?=$index11?>" class="form-control text-right" value="<?=number_format($monto11Ext, 2, '.', '');?>" onkeyup="calcularMontoFilaPersonalServicio(<?=$index11?>)" onkeydown="calcularMontoFilaPersonalServicio(<?=$index11?>)" onchange="calcularMontoFilaPersonalServicio(<?=$index11?>)"></td>
                                         <td class="text-right"><input type="number" step="0.01" readonly min="0" id="total_personalext<?=$index11?>" class="form-control text-right" value="<?=number_format($montoTotal11Ext, 2, '.', '');?>"></td>
                                         <td>
                                            <?php 
                                           if($idRemove!=0){
                                             ?>
                                            <a href="#" class="<?=$buttonDelete;?> btn-link btn-sm" onclick="removeAuditorPlantilla(<?=$idRemove?>); return false;">
                                                                    <i class="material-icons"><?=$iconDelete;?></i>
                                              </a>
                                             <?php
                                           }
                                           ?>   
                                          </td>
                                        </tr>
                                          <?php
                                          $index11++;
                                      }?>
                                      <tr class="font-weight-bold">
                                         <td colspan="5" class="text-center">TOTAL</td>
                                         <td class="text-right" id="total_personalservicio"><?=number_format($total11, 2, '.', ',');?></td>
                                         <td></td>
                                         <td class="text-right" id="total_personalservicioext"><?=number_format($total11Ext, 2, '.', ',');?></td>
                                         <td></td>
                                       </tr>
                                    </tbody>
                                </table>
                                <input type="hidden" id="cantidad_filaspersonal" value="<?=$index11?>">

<script>$("#alumnos_ibnorca").val(<?=$sumaCantidad2?>);$("#cantidad_personal").text(<?=$sumaCantidad?>);</script><?php