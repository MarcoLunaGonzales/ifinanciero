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
set_time_limit(300);

$codigoPrecioSimulacion=$_GET['codigo'];

                                $iii=1;
                               $queryPr="SELECT * FROM precios_simulacioncostodetalle where cod_preciosimulacion=$codigoPrecioSimulacion order by 1";
                               $stmt = $dbh->prepare($queryPr);
                               $stmt->execute();
                               $totalFilasPrecios=0;
                               while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoPre=$rowPre['codigo'];
                                  $cantidadPre=$rowPre['cantidad'];
                                  $montoPre=$rowPre['monto'];
                                  $montoPreTotal=$montoPre*$cantidadPre;
                                  $totalFilasPrecios+=$montoPreTotal;
                                  $porcentajePre=$rowPre['porcentaje'];
                                  $iconalum="check_circle";
                                  $montoPre=number_format($montoPre,2,".","");
                                  $montoPreTotal=number_format($montoPreTotal,2,".","");
                                   ?>
                                   <tr id="fila_precios<?=$iii?>">
                                     <td class="text-center">
                                      <input type="hidden" id="codigo_alumnosAAA<?=$iii?>" value="<?=$codigoPre?>">
                                       <input type="number" min="1" id="cantidad_alumnosAAA<?=$iii?>" name="cantidad_alumnosAAA<?=$iii?>" class="form-control" style="background-color:#E3CEF6;text-align: right" onchange="calcularPrecioTotal(<?=$iii?>)" onkeyUp="calcularPrecioTotal(<?=$iii?>)" value="<?=$cantidadPre?>">
                                     </td>
                                     <td class="text-center">
                                       <input type="number" id="porcentaje_alumnosAAA<?=$iii?>" name="porcentaje_alumnosAAA<?=$iii?>" class="form-control" style="background-color:#E3CEF6;text-align: right" onchange="calcularPrecioPorcentaje(<?=$iii?>)" onkeyUp="calcularPrecioPorcentaje(<?=$iii?>)" value="<?=$porcentajePre?>" step="0.01">
                                     </td>
                                     <td class="text-center">
                                       <input type="number" id="monto_alumnosAAA<?=$iii?>" name="monto_alumnosAAA<?=$iii?>" class="form-control" style="background-color:#E3CEF6;text-align: right" onchange="calcularPrecioTotal(<?=$iii?>)" onkeyUp="calcularPrecioTotal(<?=$iii?>)" value="<?=$montoPre?>" step="0.01">
                                     </td>  
                                     <td class="text-center">
                                       <input type="number" readonly id="total_alumnosAAA<?=$iii?>" name="total_alumnosAAA<?=$iii?>" class="form-control" style="background-color:#E3CEF6;text-align: right" value="<?=$montoPreTotal?>" step="0.01">
                                     </td>
                                     <td class="text-left">
                                      <a href="#" title="Quitar" class="btn btn-danger btn-round btn-sm btn-fab float-right" onClick="quitarElementoPrecios(<?=$iii?>)"><i class="material-icons">delete_outline</i></a>
                                     </td>
                                   </tr>
                                  <?php
                                  $iii++; 
                                  } ?>
<script>
  $("#total_preciosimulacion").val(<?=$totalFilasPrecios?>);
  $("#cantidad_filasprecios").val(<?=$iii?>);
</script>      