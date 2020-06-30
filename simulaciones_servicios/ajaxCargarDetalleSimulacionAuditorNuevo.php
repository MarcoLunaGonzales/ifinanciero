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
$data=json_decode($_GET['data']);
$codAreaReal=$_GET["cod_area"];
$aniosReal=$_GET["anios"];
$inicioAnioReal=1;
if($codAreaReal==38){
 $inicioAnioReal=0;
}
for ($nanio=$inicioAnioReal; $nanio <= $aniosReal; $nanio++) { 
  if(isset($data[$nanio]->cod_simulacion)){
  $codSimulacion=$data[$nanio]->cod_simulacion;

  $anio=$data[$nanio]->anio;
  $anios=$data[$nanio]->anios;
  $usd=$data[$nanio]->usd;
  $codAreaX=$data[$nanio]->cod_area;
 $codigos=explode("###",$data[$nanio]->codigo_filas);
 $montos_filas=explode("###",$data[$nanio]->monto_filas);
 $nroColumnas=(count($codigos)-1);
 $tituloItem="Año ".$anio;
 if(($anio==0||$anio==1)&&$codAreaX==38){
  if($anio==0){
    $tituloItem="Año 1 (Etapa".($anio+1).")";
  }else{
    $tituloItem="Año 1 (Etapa".($anio+1)." / Renovación)";
  }
 }
 if(($anio>1)&&$codAreaX==38){
   $tituloItem="Año ".$anio." (Seguimiento".($anio-1).")";
 }
 ?>
  

 <h5 class="font-weight-bold"><center><?=$tituloItem?></center></h5>
  <table class="table table-condensed table-bordered">
    <tr class="text-white bg-info">
        <td colspan="2"><a href="#" onclick="mostrarNuevoPersonalModal(<?=$anio?>,'<?=$tituloItem?>')" class="btn btn-sm btn-default"><i class="material-icons">add</i> AGREGAR</a></td>
        <td colspan="5">HONORARIOS</td>
        <?php 
        for ($i=0; $i < $nroColumnas; $i++) {
        $totalColumnaDetalle[$i]=0;
        $nombreColumna=obtenerNombreDetalleSimulacionVariablesPeriodo($codigos[$i],$anio);
         ?>
         <td class="fondo-boton" colspan="5"><?=$nombreColumna?></td>
         <?php
        }?>
        <td class="fondo-boton" colspan="2">TOTAL</td>
    </tr>
    <tr class="text-white bg-info">
        <td width="13%">Tipo Auditor</td>
        <td width="4%">Hab/Des</td>
        <td class="fondo-boton" width="3%">D</td>
        <td class="bg-success text-white" width="4%">BOB</td>
        <td class="bg-success text-white" width="4%">USD</td>
        <td width="4%" class="bg-principal">T BOB</td>
        <td width="4%" class="bg-principal">T USD</td>
        <!--<td width="8%">D&iacute;as Aud.</td>-->
        <?php 
        for ($i=0; $i < $nroColumnas; $i++) {
         ?>
         <td class="fondo-boton">D/C</td>
         <td class="bg-success text-white">BOB</td>
         <td class="bg-success text-white">USD</td>
         <td class="bg-principal">T BOB</td>
         <td class="bg-principal">T USD</td>
         <?php
        }?>
        <td width="6%" class="fondo-boton">T BOB</td>
        <td width="6%" class="fondo-boton">T USD</td>
    </tr>
    <?php 
    $sql="SELECT s.*,t.nombre as tipo FROM simulaciones_servicios_auditores s join tipos_auditor t on s.cod_tipoauditor=t.codigo where s.cod_simulacionservicio=$codSimulacion and s.cod_anio=$anio order by t.nro_orden"; /*and s.habilitado=1*/
    $stmt=$dbh->prepare($sql);
    $stmt->execute();
    $iii=1;$totalTabla=0;$totalTablaUnitario=0;$sumaAuditorTotal=0;
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoTipo=$row['codigo'];
      $nombreTipo=$row['descripcion']; //$row['tipo'];
      $cantidadTipo=$row['cantidad_editado'];
      $diasTipo=$row['dias'];
      $codExtLoc=$row['cod_externolocal'];
      $montoAuditorIndUSD=number_format($row['monto']/$usd,2,".","");
      $montoAuditorInd=number_format($row['monto'],2,".","");
      $montoAuditor=$row['monto']*$diasTipo;
      
      $montoAuditorUSD=number_format($montoAuditor/$usd,2,".","");
      $montoAuditor=number_format($montoAuditor,2,".","");  

      $cantPre=obtenerCantidadSimulacionDetalleAuditorPeriodo($codSimulacion,$codigoTipo,$anio);
      $diasPre=obtenerDiasSimulacionDetalleAuditorPeriodo($codSimulacion,$codigoTipo,$anio);
      if($cantidadTipo<$cantPre){
        $cantPre=$cantidadTipo;
      }
      if($diasTipo<$diasPre){
        $diasPre=$diasTipo;
      }


      $banderaHab=$row['habilitado'];
      $claseDeshabilitado="hidden"; //number por defecto
      $claseDeshabilitadoOFF="number";
      $claseDeshabilitadocol="hidden";
      $claseDeshabilitadocolOFF="number";
      if($banderaHab!=0){ 
        if($row['cod_tipoauditor']!=-100){
         $sumaAuditorTotal+=$montoAuditor;
        //$modal_totalmontopre+=$montoPreSi;
        //$modal_totalmontopretotal+=$montoPreTotal;
         $claseDeshabilitado="number"; //number por defecto
         $claseDeshabilitadoOFF="hidden";
         $claseDeshabilitadocol="number";
         $claseDeshabilitadocolOFF="hidden";
        }
      }

      if($row['cod_tipoauditor']==-100){
         $nombreTipo="<b class='text-danger'>".$nombreTipo." (CV)</b>";
      }
      $estiloFilaTextoAud="";
      $existeCostoVariableSolAu=obtenerCostoVariableSolicitadoPropuestaTCPTCS($codSimulacion,$codigoTipo,2);
      if($existeCostoVariableSolAu==1){  //HONORARIOS 1, COSTOS VARIABLES 2
           $estiloFilaTextoAud='rel="tooltip" title="SOLICITUD DE RECURSOS" disabled';
       }
       ?>
       <tr>
         <td class="text-left small"><input type="hidden" id="modal_local_extranjero<?=$anio?>CCCC<?=$iii?>" value="<?=$codExtLoc?>"><input type="hidden" id="codigo_filaauditor<?=$anio?>CCCC<?=$iii?>" value="<?=$codigoTipo?>"><input type="hidden" id="codigo_filatipoauditor<?=$anio?>CCCC<?=$iii?>" value="<?=$row["cod_tipoauditor"]?>"><?=$nombreTipo?></td>
         <td id="solicitado_item<?=$anio?>CCCC<?=$iii?>">
          <div class="togglebutton">
               <label>
                 <input type="checkbox" <?=($banderaHab==1)?"checked":"";?> id="modal_checkpre<?=$anio?>CCCC<?=$iii?>" onchange="activarInputsCostosVariables(<?=$anio?>,<?=$iii?>)">
                  <span class="toggle"></span>
                </label>
          </div>
           <input type="hidden" id="modal_cantidad_personal<?=$anio?>CCCC<?=$iii?>" value="<?=$cantidadTipo?>">
           <input type="hidden" id="cantidad_columnas<?=$anio?>CCCC<?=$iii?>" value="<?=$nroColumnas?>">
         </td>
         <td class="text-left small">
            <input class="form-control fondo-boton fondo-boton-active text-right" step="0.5" <?=$estiloFilaTextoAud?> type="<?=$claseDeshabilitado?>" <?=($banderaHab==0)?"readonly":"";?> id="dias_honorario<?=$anio?>CCCC<?=$iii?>" value="<?=$diasTipo?>" onchange="calcularTotalPersonalServicioAuditorHonorarios(<?=$anio?>)" onkeyup="calcularTotalPersonalServicioAuditorHonorarios(<?=$anio?>)">
            <input type="<?=$claseDeshabilitadoOFF?>" id="dias_honorarioOFF<?=$anio?>CCCC<?=$iii?>" readonly name="dias_honorarioOFF<?=$anio?>CCCC<?=$iii?>" class="form-control" value="0">
         </td>
         <td class="text-left small">
           <input class="form-control text-info text-right" type="<?=$claseDeshabilitado?>" <?=$estiloFilaTextoAud?> <?=($banderaHab==0)?"readonly":"";?> id="monto_honorario<?=$anio?>CCCC<?=$iii?>" value="<?=$montoAuditorInd?>" onchange="calcularTotalPersonalServicioAuditorHonorariosSingle(<?=$anio?>,<?=$iii?>,1)" onkeyup="calcularTotalPersonalServicioAuditorHonorariosSingle(<?=$anio?>,<?=$iii?>,1)">
           <input type="<?=$claseDeshabilitadoOFF?>" id="monto_honorarioOFF<?=$anio?>CCCC<?=$iii?>" readonly name="monto_honorarioOFF<?=$anio?>CCCC<?=$iii?>" class="form-control" value="0">
         </td>
         <td class="text-left small">
           <input class="form-control text-info text-right" type="<?=$claseDeshabilitado?>" <?=$estiloFilaTextoAud?> <?=($banderaHab==0)?"readonly":"";?> id="monto_honorarioUSD<?=$anio?>CCCC<?=$iii?>" value="<?=$montoAuditorIndUSD?>" onchange="calcularTotalPersonalServicioAuditorHonorariosSingle(<?=$anio?>,<?=$iii?>,2)" onkeyup="calcularTotalPersonalServicioAuditorHonorariosSingle(<?=$anio?>,<?=$iii?>,2)">
           <input type="<?=$claseDeshabilitadoOFF?>" id="monto_honorarioUSDOFF<?=$anio?>CCCC<?=$iii?>" readonly name="monto_honorarioUSDOFF<?=$anio?>CCCC<?=$iii?>" class="form-control" value="0">
         </td>
         <td class="text-left small bg-principal">
          <input class="form-control text-right" readonly type="<?=$claseDeshabilitado?>" id="monto_honorarioTotal<?=$anio?>CCCC<?=$iii?>" value="<?=$montoAuditor?>">
          <input type="<?=$claseDeshabilitadoOFF?>" id="monto_honorarioTotalOFF<?=$anio?>CCCC<?=$iii?>" readonly name="monto_honorarioTotalOFF<?=$anio?>CCCC<?=$iii?>" class="form-control" value="0">
        </td>
         <td class="text-left small bg-principal">
          <input class="form-control text-right" readonly type="<?=$claseDeshabilitado?>" id="monto_honorarioTotalUSD<?=$anio?>CCCC<?=$iii?>" value="<?=$montoAuditorUSD?>">
          <input type="<?=$claseDeshabilitadoOFF?>" id="monto_honorarioTotalUSDOFF<?=$anio?>CCCC<?=$iii?>" readonly name="monto_honorarioTotalUSDOFF<?=$anio?>CCCC<?=$iii?>" class="form-control" value="0">
         </td>
         <!--<td>
           <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="modal_dias_personal<?=$iii?>" id="modal_dias_personal<?=$iii?>" onchange="calcularTotalPersonalServicioAuditor(<?=$anio?>)">
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
          $codigoPres=obtenerCodigoEspecificoSimulacionDetalleAuditorPeriodo($codSimulacion,$codigoCol,$codigoTipo,$anio);
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

          if($banderaHab!=0){ 
             $claseDeshabilitado="number"; //number por defecto
             $claseDeshabilitadoOFF="hidden";
             $claseDeshabilitadocol="number";
             $claseDeshabilitadocolOFF="hidden";
          }

          if($diasPres!=0){      
            $claseDeshabilitadocol="number"; //number por defecto
            $claseDeshabilitadocolOFF="hidden";
          }else{
            $claseDeshabilitadocol="hidden"; //number por defecto
            $claseDeshabilitadocolOFF="number";
          }
          $montoPresUSD=number_format($montoPres/$usd,2,".","");
          $montoPres=number_format($montoPres,2,".","");
          
          $estiloFilaTexto='';
          $existeCostoVariableSol=obtenerCostoVariableSolicitadoPropuestaTCPTCS($codSimulacion,$codigoPres,1);
          if($existeCostoVariableSol==1){  //HONORARIOS 1, COSTOS VARIABLES 2
           $estiloFilaTexto='rel="tooltip" title="SOLICITUD DE RECURSOS" disabled';
           ?><script>$("#solicitado_item"+'<?=$anio?>CCCC<?=$iii?>').html('<i class="material-icons text-danger">not_interested</i>');</script><?php
          }
          //if($existeCostoVariableSol!=1){ 
         ?>
         <td class="text-right">
          <input type="<?=$claseDeshabilitado?>" min="0" <?=($banderaHab==0)?"readonly":"";?> step="0.5" <?=$estiloFilaTexto?> id="modal_dias_personalItem<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" name="modal_dias_personalItem<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" class="form-control fondo-boton fondo-boton-active text-right" onchange="calcularTotalPersonalServicioAuditor(<?=$anio?>)" onkeyUp="calcularTotalPersonalServicioAuditor(<?=$anio?>)" value="<?=$diasPres?>">
          <input type="<?=$claseDeshabilitadoOFF?>" id="modal_dias_personalItemOFF<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" readonly name="modal_dias_personalItemOFF<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" class="form-control" value="0">
           <!--<select class="form-control selectpicker form-control-sm" data-size="6" data-style="fondo-boton fondo-boton-active" name="modal_dias_personalItem<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" id="modal_dias_personalItem<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" onchange="calcularTotalPersonalServicioAuditor(<?=$anio?>)">
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
            <input type="<?=$claseDeshabilitadocol?>" id="monto<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" step="0.01" <?=$estiloFilaTexto?> value="<?=$montoPres?>" class="form-control text-right" onchange="calcularTotalPersonalServicioAuditor(<?=$anio?>)" onkeyUp="calcularTotalPersonalServicioAuditor(<?=$anio?>)">
            <input type="<?=$claseDeshabilitadocolOFF?>" id="montoOFF<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" readonly name="montoOFF<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" class="form-control" value="0">
          </td>
          <td class="text-right">
            <input type="<?=$claseDeshabilitadocol?>" id="montoUSD<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" step="0.01" <?=$estiloFilaTexto?> value="<?=$montoPresUSD?>" class="form-control text-right" onchange="calcularTotalPersonalServicioAuditorDolar(<?=$anio?>,<?=$ncol?>,<?=$iii?>)" onkeyUp="calcularTotalPersonalServicioAuditorDolar(<?=$anio?>,<?=$ncol?>,<?=$iii?>)">
            <input type="<?=$claseDeshabilitadocolOFF?>" id="montoUSDOFF<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" readonly name="montoUSDOFF<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" class="form-control" value="0">
          </td>

          <td class="text-right bg-principal">
            <input type="hidden" id="codigo_columnas<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" value="<?=$codigoCol?>">
            <!--<input type="hidden" id="codigo_ssd_ssa<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" value="<?=$codigoCol?>">-->

            <input type="<?=$claseDeshabilitadocol?>" id="monto_mult<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" readonly name="monto_mult<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" class="form-control text-right" onchange="calcularTotalPersonalServicioAuditor(<?=$anio?>)" onkeyUp="calcularTotalPersonalServicioAuditor(<?=$anio?>)" value="<?=$montoPre?>" step="0.01">
            <input type="<?=$claseDeshabilitadocolOFF?>" id="monto_multOFF<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" readonly name="monto_multOFF<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" class="form-control" value="0">
            <!--<input type="number" id="monto<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" step="0.01" value="<?=$montoPres?>" class="form-control text-info text-right" onchange="calcularTotalPersonalServicioAuditor(<?=$anio?>)" onkeyUp="calcularTotalPersonalServicioAuditor(<?=$anio?>)">-->
            <input type="hidden" id="montoext<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" value="<?=$montoPresext?>">
          </td>
          <td class="text-right bg-principal">
            <input type="<?=$claseDeshabilitadocol?>" id="monto_multUSD<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" readonly name="monto_multUSD<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" class="form-control text-right" value="<?=$montoPreUSD?>" step="0.01">
            <input type="<?=$claseDeshabilitadocolOFF?>" id="monto_multUSDOFF<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" readonly name="monto_multUSDOFF<?=$anio?>CCCC<?=$ncol?>RRR<?=$iii?>" class="form-control" value="0">
          </td>
         <?php
           /*}else{
            ?>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <?php  
           }*/
         }
         $totalFila+=$totalFilaUnitario+$montoAuditor; //*$diasPre*$cantPre
         ?>
         <td class="text-right font-weight-bold fondo-boton" id="total_auditor<?=$anio?>CCCC<?=$iii?>"><?=number_format($totalFila, 2, '.', ',')?></td>
         <td class="text-right font-weight-bold fondo-boton" id="total_auditorUSD<?=$anio?>CCCC<?=$iii?>"><?=number_format($totalFila/$usd, 2, '.', ',')?></td>
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
      <!--TOTALES DEL HONORARIO-->
        <td></td>
        <td></td>
        <td></td>
        <td class="text-right font-weight-bold" id="total_auditorvariable<?=$anio?>"><?=number_format($sumaAuditorTotal,2,".","");?></td>
        <td class="text-right font-weight-bold" id="total_auditorvariableUSD<?=$anio?>"><?=number_format($sumaAuditorTotal/$usd,2,".","");?></td>
        <!--FIN TOTALES HONORARIO-->
      <?php 
       for ($i=0; $i < $nroColumnas; $i++) {
        ?>
        <td></td>
        <td></td>
        <td></td>
        <td class="text-right font-weight-bold" id="total_item<?=$anio?>CCCC<?=$i+1?>"><?=number_format($totalColumnaDetalle[$i], 2, '.', ',')?></td> 
        <td class="text-right font-weight-bold" id="total_itemUSD<?=$anio?>CCCC<?=$i+1?>"><?=number_format($totalColumnaDetalle[$i]/$usd, 2, '.', ',')?></td> 
        <?php
       }
      ?>
      <!--<td class="text-right font-weight-bold" id="total_unitarioauditor"><?=number_format($totalTablaUnitario, 2, '.', ',')?></td>-->
      <td class="text-right font-weight-bold fondo-boton" id="total_auditor<?=$anio?>"><?=number_format($totalTabla, 2, '.', '')?></td>
      <td class="text-right font-weight-bold fondo-boton" id="total_auditorUSD<?=$anio?>"><?=number_format($totalTabla/$usd, 2, '.', '')?></td>
    </tr>
  </table>
  <input type="hidden" id="modal_numeropersonalauditor<?=$anio?>" value="<?=$iii?>">  
  <script>ponerCantidadTotalesVariablesModal(<?=$inicioAnioReal?>,<?=$anio?>);
         $('[data-toggle="tooltip"]').tooltip({'trigger':'focus', 'title': 'Password tooltip'});
  </script>
 <?php
 }


}
