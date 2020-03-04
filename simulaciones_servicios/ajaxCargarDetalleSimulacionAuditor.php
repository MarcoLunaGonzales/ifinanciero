<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functionsPOSIS.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

if(isset($_GET["cod_simulacion"])){
  $codSimulacion=$_GET["cod_simulacion"];
  $anio=$_GET["anio"];
  $usd=$_GET["usd"];
 $codigos=explode("###",$_GET["codigo_filas"]);
 $nroColumnas=(count($codigos)-1);
 ?>
  <table class="table table-condensed table-bordered">
    <tr class="text-white bg-info">
        <td width="25%">Tipo Auditor</td>
        <!--<td width="12%">Regi&oacute;n</td>-->
        <td width="8%">Cantidad</td>
        <td width="8%">D&iacute;as Aud.</td>
        <?php 
        for ($i=0; $i < $nroColumnas; $i++) {
        $nombreColumna=obtenerNombreDetalleSimulacionVariablesPeriodo($codigos[$i],$anio);
         ?>
         <td class="fondo-boton"><?=$nombreColumna?></td>
         <?php
        }?>
        <!--<td width="15%" class="fondo-boton">TOTAL UNIT.</td>-->
        <td width="8%" class="fondo-boton">TOTAL BOB</td>
        <td width="8%" class="fondo-boton">TOTAL USD</td>
    </tr>
    <?php 
    $sql="SELECT s.*,t.nombre as tipo FROM simulaciones_servicios_auditores s join tipos_auditor t on s.cod_tipoauditor=t.codigo where s.cod_simulacionservicio=$codSimulacion and s.cod_anio=$anio";
    $stmt=$dbh->prepare($sql);
    $stmt->execute();
    $iii=1;$totalTabla=0;$totalTablaUnitario=0;
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoTipo=$row['cod_tipoauditor'];
      $nombreTipo=$row['tipo'];
      $cantidadTipo=$row['cantidad_editado'];
      $diasTipo=$row['dias'];
      $codExtLoc=$row['cod_externolocal'];
      $cantPre=obtenerCantidadSimulacionDetalleAuditorPeriodo($codSimulacion,$codigoTipo,$anio);
      $diasPre=obtenerDiasSimulacionDetalleAuditorPeriodo($codSimulacion,$codigoTipo,$anio);
      if($cantidadTipo<$cantPre){
        $cantPre=$cantidadTipo;
      }
      if($diasTipo<$diasPre){
        $diasPre=$diasTipo;
      }
       ?>
       <tr>
         <td class="text-left small"><input type="hidden" id="modal_local_extranjero<?=$iii?>" value="<?=$codExtLoc?>"><input type="hidden" id="codigo_filaauditor<?=$iii?>" value="<?=$codigoTipo?>"><?=$nombreTipo?></td>
         <td>
           <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="modal_cantidad_personal<?=$iii?>" id="modal_cantidad_personal<?=$iii?>" onchange="calcularTotalPersonalServicioAuditor()">
              <?php 
                 for ($hf=1; $hf<=$cantidadTipo; $hf++) {
                   if($hf==$cantPre){
                     ?><option value="<?=$hf?>" selected><?=$hf?></option><?php
                   }else{
                        ?><option value="<?=$hf?>"><?=$hf?></option><?php
                   }      
                }
               ?>
           </select>
         </td>
         <td>
           <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="modal_dias_personal<?=$iii?>" id="modal_dias_personal<?=$iii?>" onchange="calcularTotalPersonalServicioAuditor()">
              <?php 
                 for ($hf=1; $hf<=$diasTipo; $hf++) {
                   if($hf==$diasPre){
                     ?><option value="<?=$hf?>" selected><?=$hf?></option><?php
                   }else{
                        ?><option value="<?=$hf?>"><?=$hf?></option><?php
                   }      
                }
               ?>
           </select>
           <input type="hidden" id="cantidad_columnas<?=$iii?>" value="<?=$nroColumnas?>">
         </td>
         <?php
         $totalFilaUnitario=0;$totalFila=0;
         for ($i=0; $i < $nroColumnas; $i++) {
          $codigoCol=$codigos[$i];
          $ncol=$i+1;

          $montoPres=obtenerMontoSimulacionDetalleAuditorPeriodo($codSimulacion,$codigoCol,$codigoTipo,$anio);
          $montoPresext=obtenerMontoSimulacionDetalleAuditorExternoPeriodo($codSimulacion,$codigoCol,$codigoTipo,$anio);
          if($codExtLoc==1){
            $montoPre=$montoPres*$cantPre*$diasPre;
          }else{
            $montoPre=$montoPresext*$cantPre*$diasPre;
          }

          $totalFilaUnitario+=$montoPre;          
         ?>
          <td class="text-right">
            <input type="hidden" id="codigo_columnas<?=$ncol?>RRR<?=$iii?>" value="<?=$codigoCol?>">

            <input type="number" id="monto_mult<?=$ncol?>RRR<?=$iii?>" readonly name="monto_mult<?=$ncol?>RRR<?=$iii?>" class="form-control text-info text-right" onchange="calcularTotalPersonalServicioAuditor()" onkeyUp="calcularTotalPersonalServicioAuditor()" value="<?=$montoPre?>" step="0.01">
            <input type="hidden" id="monto<?=$ncol?>RRR<?=$iii?>" value="<?=$montoPres?>">
            <input type="hidden" id="montoext<?=$ncol?>RRR<?=$iii?>" value="<?=$montoPresext?>">
          </td>
         <?php

         }
         $totalFila+=$totalFilaUnitario; //*$diasPre*$cantPre
         ?>
         <!--<td class="text-right font-weight-bold" id="total_unitarioauditor<?=$iii?>"><?=number_format($totalFilaUnitario, 2, '.', ',')?></td>-->
         <td class="text-right font-weight-bold fondo-boton" id="total_auditor<?=$iii?>"><?=number_format($totalFila, 2, '.', ',')?></td>
         <td class="text-right font-weight-bold fondo-boton" id="total_auditorUSD<?=$iii?>"><?=number_format($totalFila/$usd, 2, '.', ',')?></td>
       </tr>
       <?php
       $totalTabla+=$totalFila;
       $totalTablaUnitario+=$totalFilaUnitario;
       $iii++;
     }
     $colSpan=$nroColumnas+3;
    ?>
    <tr>
      <td colspan="<?=$colSpan?>" class="font-weight-bold">SUMA TOTAL</td>
      <!--<td class="text-right font-weight-bold" id="total_unitarioauditor"><?=number_format($totalTablaUnitario, 2, '.', ',')?></td>-->
      <td class="text-right font-weight-bold fondo-boton" id="total_auditor"><?=number_format($totalTabla, 2, '.', ',')?></td>
      <td class="text-right font-weight-bold fondo-boton" id="total_auditorUSD"><?=number_format($totalTabla/$usd, 2, '.', ',')?></td>
    </tr>
  </table>
  <input type="hidden" id="modal_numeropersonalauditor" value="<?=$iii?>">  
<div class="form-group float-right">
    <button class="btn btn-default" id="guardar_cuenta" onclick="guardarCuentasSimulacionAjaxGenericoServicioAuditor('<?=$anio?>')">Guardar</button>
  </div>
 <?php
 }