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
  $anios=$_GET["anios"];
  $usd=$_GET["usd"];
  $codAreaX=$_GET["cod_area"];
 $codigos=explode("###",$_GET["codigo_filas"]);
 $montos_filas=explode("###",$_GET["monto_filas"]);
 $nroColumnas=(count($codigos)-1);
 ?>
  <table class="table table-condensed table-bordered">
    <tr class="text-white bg-info">
        <td colspan="2"></td>
        <?php 
        for ($i=0; $i < $nroColumnas; $i++) {
        $totalColumnaDetalle[$i]=0;
        $nombreColumna=obtenerNombreDetalleSimulacionVariablesPeriodo($codigos[$i],$anio);
         ?>
         <td class="fondo-boton" colspan="4"><?=$nombreColumna?></td>
         <?php
        }?>
        <td class="fondo-boton" colspan="2">TOTAL</td>
    </tr>
    <tr class="text-white bg-info">
        <td width="13%">Tipo Auditor</td>
        <td width="4%">Cantidad</td>
        <!--<td width="8%">D&iacute;as Aud.</td>-->
        <?php 
        for ($i=0; $i < $nroColumnas; $i++) {
         ?>
         <td class="fondo-boton">D/C</td>
         <td class="bg-success text-white">MON</td>
         <td class="fondo-boton">BOB</td>
         <td class="fondo-boton">USD</td>
         <?php
        }?>
        <td width="6%" class="fondo-boton">BOB</td>
        <td width="6%" class="fondo-boton">USD</td>
    </tr>
    <?php 
    $sql="SELECT s.*,t.nombre as tipo FROM simulaciones_servicios_auditores s join tipos_auditor t on s.cod_tipoauditor=t.codigo where s.cod_simulacionservicio=$codSimulacion and s.cod_anio=$anio and s.habilitado=1";
    $stmt=$dbh->prepare($sql);
    $stmt->execute();
    $iii=1;$totalTabla=0;$totalTablaUnitario=0;
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoTipo=$row['codigo'];
      $nombreTipo=$row['descripcion']; //$row['tipo'];
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
           <select class="form-control selectpicker form-control-sm" data-size="6" data-style="fondo-boton fondo-boton-active" name="modal_cantidad_personal<?=$iii?>" id="modal_cantidad_personal<?=$iii?>" onchange="calcularTotalPersonalServicioAuditor()">
              <?php 
                 for ($hf=0; $hf<=$cantidadTipo; $hf++) {
                   if($hf==$cantidadTipo){
                     ?><option value="<?=$hf?>" selected><?=$hf?></option><?php
                   }else{
                        ?><option value="<?=$hf?>"><?=$hf?></option><?php
                   }      
                }
               ?>
           </select>
           <input type="hidden" id="cantidad_columnas<?=$iii?>" value="<?=$nroColumnas?>">
         </td>
         <!--<td>
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
           
         </td>-->
         <?php
         $totalFilaUnitario=0;$totalFila=0;
         for ($i=0; $i < $nroColumnas; $i++) {

          $codigoCol=$codigos[$i];
          
          
          $montoPres=obtenerMontoSimulacionDetalleAuditorPeriodo($codSimulacion,$codigoCol,$codigoTipo,$anio);
          //montoPres
          //$montoPres=$montos_filas[$i]; //para obtener el monto del modal anterioir

          $diasPres=obtenerDiasEspecificoSimulacionDetalleAuditorPeriodo($codSimulacion,$codigoCol,$codigoTipo,$anio);
          $montoPresext=obtenerMontoSimulacionDetalleAuditorExternoPeriodo($codSimulacion,$codigoCol,$codigoTipo,$anio);
          if($codExtLoc==1){
            $montoPre=$montoPres*$cantPre*$diasPres;
          }else{
            $montoPre=$montoPresext*$cantPre*$diasPres;
          }
          $totalColumnaDetalle[$i]+=$montoPre;
          $totalFilaUnitario+=$montoPre;  
          $ncol=$i+1; 
          $montoPreUSD=number_format($montoPre/$usd,2,".","");
          $montoPre=number_format($montoPre,2,".","");       
         ?>
         <td class="text-right">
          <input type="number" min="0" id="modal_dias_personalItem<?=$ncol?>RRR<?=$iii?>" name="modal_dias_personalItem<?=$ncol?>RRR<?=$iii?>" class="form-control fondo-boton fondo-boton-active text-right" onchange="calcularTotalPersonalServicioAuditor()" onkeyUp="calcularTotalPersonalServicioAuditor()" value="<?=$diasPres?>">
           <!--<select class="form-control selectpicker form-control-sm" data-size="6" data-style="fondo-boton fondo-boton-active" name="modal_dias_personalItem<?=$ncol?>RRR<?=$iii?>" id="modal_dias_personalItem<?=$ncol?>RRR<?=$iii?>" onchange="calcularTotalPersonalServicioAuditor()">
              <?php 
                 for ($hf=0; $hf<=$diasTipo; $hf++) {
                   if($hf==$diasPres){
                     ?><option value="<?=$hf?>" selected><?=$hf?></option><?php
                   }else{
                        ?><option value="<?=$hf?>"><?=$hf?></option><?php
                   }      
                }
               ?>
           </select>-->
         </td> 
         <td class="text-right">
            <input type="number" id="monto<?=$ncol?>RRR<?=$iii?>" step="0.01" value="<?=$montoPres?>" class="form-control text-info text-right" onchange="calcularTotalPersonalServicioAuditor()" onkeyUp="calcularTotalPersonalServicioAuditor()">
          </td>
          <td class="text-right">
            <input type="hidden" id="codigo_columnas<?=$ncol?>RRR<?=$iii?>" value="<?=$codigoCol?>">
            <!--<input type="hidden" id="codigo_ssd_ssa<?=$ncol?>RRR<?=$iii?>" value="<?=$codigoCol?>">-->
            <input type="number" id="monto_mult<?=$ncol?>RRR<?=$iii?>" readonly name="monto_mult<?=$ncol?>RRR<?=$iii?>" class="form-control text-info text-right" onchange="calcularTotalPersonalServicioAuditor()" onkeyUp="calcularTotalPersonalServicioAuditor()" value="<?=$montoPre?>" step="0.01">
            <!--<input type="number" id="monto<?=$ncol?>RRR<?=$iii?>" step="0.01" value="<?=$montoPres?>" class="form-control text-info text-right" onchange="calcularTotalPersonalServicioAuditor()" onkeyUp="calcularTotalPersonalServicioAuditor()">-->
            <input type="hidden" id="montoext<?=$ncol?>RRR<?=$iii?>" value="<?=$montoPresext?>">
          </td>
          <td class="text-right">
            <input type="number" id="monto_multUSD<?=$ncol?>RRR<?=$iii?>" readonly name="monto_multUSD<?=$ncol?>RRR<?=$iii?>" class="form-control text-info text-right" value="<?=$montoPreUSD?>" step="0.01">
          </td>
         <?php

         }
         $totalFila+=$totalFilaUnitario; //*$diasPre*$cantPre
         ?>
         <td class="text-right font-weight-bold fondo-boton" id="total_auditor<?=$iii?>"><?=number_format($totalFila, 2, '.', ',')?></td>
         <td class="text-right font-weight-bold fondo-boton" id="total_auditorUSD<?=$iii?>"><?=number_format($totalFila/$usd, 2, '.', ',')?></td>
       </tr>
       <?php
       $totalTabla+=$totalFila;
       $totalTablaUnitario+=$totalFilaUnitario;
       $iii++;
     }
     $colSpan=($nroColumnas*2)+3;
    ?>
    <tr>
      <td colspan="2" class="font-weight-bold">TOTAL</td>
      <?php 
       for ($i=0; $i < $nroColumnas; $i++) {
        ?>
        <td></td>
        <td></td>
        <td class="text-right font-weight-bold" id="total_item<?=$i+1?>"><?=number_format($totalColumnaDetalle[$i], 2, '.', ',')?></td> 
        <td class="text-right font-weight-bold" id="total_itemUSD<?=$i+1?>"><?=number_format($totalColumnaDetalle[$i]/$usd, 2, '.', ',')?></td> 
        <?php
       }
      ?>
      <!--<td class="text-right font-weight-bold" id="total_unitarioauditor"><?=number_format($totalTablaUnitario, 2, '.', ',')?></td>-->
      <td class="text-right font-weight-bold fondo-boton" id="total_auditor"><?=number_format($totalTabla, 2, '.', ',')?></td>
      <td class="text-right font-weight-bold fondo-boton" id="total_auditorUSD"><?=number_format($totalTabla/$usd, 2, '.', ',')?></td>
    </tr>
  </table>
  <input type="hidden" id="modal_numeropersonalauditor" value="<?=$iii?>">  

  <?php
  $etapas="Año ".$anio;
  if($codAreaX!=39){
    $inicioAnio=0;
    if($anio==0||$anio==1){
     $etapas="Año 1 (ETAPA ".($anio+1).")"; 
    }
  }else{
    $inicioAnio=1;
  }
  ?>
  <div class="row col-sm-12">
    
            <label class="col-sm-2 col-form-label">Copiar de <?=$etapas?> a:</label>
              <div class="col-sm-2">
                  <div class="form-group">
                    <select class="form-control selectpicker" multiple data-style="btn btn-primary btn-round" name="copiar_variables<?=$anio?>[]" id="copiar_variables<?=$anio?>">
                                 <?php
                                for ($kk=$inicioAnio; $kk<=$anios; $kk++) { 
                                    $optionTit="Año ".$kk;
                                     if($codAreaX!=39){
                                       if($kk==0||$kk==1){
                                        $optionTit="Año 1 (ETAPA ".($kk+1).")"; 
                                       }
                                     }
                                    if($kk!=$anio){
                                        ?><option value="<?=$kk?>"><?=$optionTit?></option><?php
                                    }
                                }    
                                  ?>       
                      </select>
                      </div> 
                    </div>
          <div class="col-sm-2">
           <div class="form-group">
             <button onclick="copiarCostosVariables(<?=$anio?>)" class="btn btn-default" >GUARDAR Y COPIAR</button>
            </div> 
         </div> 
         <div class="col-sm-6 text-right">
           <div class="form-group">
             <button class="btn btn-success" id="guardar_cuenta" onclick="guardarCuentasSimulacionAjaxGenericoServicioAuditor('<?=$anio?>',0,0)">Guardar Cambios</button>
           </div> 
          </div>                 
      </div>
 <?php
 }