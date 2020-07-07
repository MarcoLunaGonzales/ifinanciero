<?php
set_time_limit(0);
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$mesActualConsulta=date("m");
if(isset($_GET["simulacion"])){
 $codigo=$_GET["simulacion"];
 $codPlan=$_GET["plantilla"];
 $usd=(float)$_GET["usd"];
 $cod_anio=$_GET["anio"];
 $codArea=obtenerCodigoAreaPlantillaServicio($codPlan);

 if($codArea==39){
   $inicioAnio=1;
 }else{
   $inicioAnio=0;
 }

 ?>
   <ul class="nav nav-pills nav-pills-warning" role="tablist">
    <?php
      for ($an=$inicioAnio; $an<=$cod_anio; $an++) { 
        $active="";
        $tituloItem="Año ".$an;
        if($codArea!=39){
          $tituloItem="Año ".$an."(SEGUIMIENTO ".($an-1).")";
           if($an==0||$an==1){
           $tituloItem="Año 1 (ETAPA ".($an+1).")"; 
          }
        }

        if($an==1){
          $active="active";
        }
            ?>
      <li class="nav-item">
        <a class="nav-link <?=$active?>" data-toggle="tab" href="#link_detalle<?=$an?>" role="tablist">
           <?=$tituloItem?>
         </a>
       </li>
    <?php
    }
    ?>
    </ul>
    <div class="tab-content tab-space">
 <?php
 for ($yyyy=$inicioAnio; $yyyy <=$cod_anio; $yyyy++) { 
    $active="";
    $tituloItem="AÑO ".$yyyy;
    if($codArea!=39){
      $tituloItem="Año ".$yyyy."(SEGUIMIENTO ".($yyyy-1).")";
       if($yyyy==0||$yyyy==1){
       $tituloItem="Año 1 (ETAPA ".($yyyy+1).")"; 
      }
    }
    if($yyyy==1){
      $active="active";
    }
  ?>
   <div class="tab-pane <?=$active?>" id="link_detalle<?=$yyyy?>">
    <h4 class="font-weight-bold"><center>COSTOS <?=$tituloItem?> </center></h4>
  <?php
 
 if($codArea==39){
   $mes=obtenerCantidadAuditoriasPlantilla($codPlan);
 }else{
   $mes=obtenerCantidadAuditoriasPlantilla($codPlan);
 }
 $tipoCosto=$_GET["tipo"];
 $alumnos=obtenerCantidadPersonalSimulacionEditado($codigo);
$anio=date("Y");

$query1="select pgd.cod_plantillagruposervicio,pc.cod_unidadorganizacional,pc.cod_area,pgc.nombre,pgc.cod_tiposervicio,sum(pgd.monto_local) as local,sum(pgd.monto_externo) as externo,sum(pgd.monto_calculado) as calculado from plantillas_gruposerviciodetalle pgd join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo
join plantillas_gruposervicio pgc on pgd.cod_plantillagruposervicio=pgc.codigo
join plantillas_servicios pc on pgc.cod_plantillaservicio=pc.codigo 
where pc.codigo=$codPlan";

if($tipoCosto==1){
$query2=$query1." and pgc.cod_tiposervicio=1 GROUP BY pgd.cod_plantillagruposervicio order by pgd.cod_plantillagruposervicio";
$bgClase="bg-info";
}else{
  
  $query2=$query1." and pgc.cod_tiposervicio=2 GROUP BY pgd.cod_plantillagruposervicio order by pgd.cod_plantillagruposervicio";
  $bgClase="bg-success";
}
  $stmt = $dbh->prepare($query2);
  $stmt->execute();
  $html='';$montoTotales=0;$montoTotales2=0;$montoTotales2Alumno=0;$montoTotalesPresupuesto=0;
  $precioLocalX=obtenerPrecioServiciosSimulacionPorAnio($codigo,$yyyy);
?>
       <div class=""><center>
        <?php if($tipoCosto==1){
          $porCre=($_GET['porcentaje_fijo']/100)*($yyyy-1);
          /* DATOS PARA PRECIO EN LUGAR DE CANTIDAD AUDITORIAS*/
          $precioLocalX=obtenerPrecioServiciosSimulacionPorAnio($codigo,$yyyy);
          $precioRegistrado=obtenerPrecioRegistradoPropuestaTCPTCS($codigo);
          if($precioRegistrado==0){
             $precioRegistrado=1;
          }
          $sumaPrecioRegistrado=0;
          if($yyyy>1){
           //$precioLocalX=($precioLocalX*$porCre)+$precioLocalX;
           $sumaPrecioRegistrado=$precioRegistrado*$porCre;
          }
          $nAuditorias=obtenerCantidadAuditoriasPlantilla($codPlan); 
          
          $porcentPrecios=($precioLocalX*100)/($precioRegistrado+$sumaPrecioRegistrado);  

          $codOficina=0;$codAreaX=0;
          $datosPlantilla=obtenerPlantillaServicioDatos($codPlan);
          while ($rowPlantilla = $datosPlantilla->fetch(PDO::FETCH_ASSOC)) {
            $codOficina=$rowPlantilla['cod_unidadorganizacional'];
            $codAreaX=$rowPlantilla['cod_area'];
          }

          $presupuestoMes=obtenerPresupuestoEjecucionPorArea($codOficina,$codAreaX,$globalNombreGestion,$mesActualConsulta)['presupuesto'];
          if($presupuestoMes>0){
            $porcentPreciosMes=($precioLocalX*100)/($presupuestoMes);
          }else{
            $porcentPreciosMes=0;
          }
         
         $valorConfiguracionTCPTCS=obtenerValorConfiguracion(52);
         $tituloPorpuestaTCPTCS="NACIONAL";
          if($valorConfiguracionTCPTCS==1){
            $tituloPorpuestaTCPTCS=$_GET['unidad_nombre'];
          }
          /* fin de datos */
         ?>
          <table class="table table-condensed table-bordered">
            <tr class="text-white <?=$bgClase?>">
              <td colspan="6">DATOS</td>
            </tr>
            <tr>
              <td class="bg-plomo">PRESUPUESTO <?=$_GET['area_nombre']?>, <?=$tituloPorpuestaTCPTCS?> GESTION</td>
              <td class="text-right"><?=number_format($precioRegistrado+$sumaPrecioRegistrado, 2, '.', ',')?></td>
              <td class="bg-plomo">Precio</td>
              <td class="text-right"><?=number_format($precioLocalX, 2, '.', ',')?></td>
              <td class="bg-plomo">Porcentaje</td>
              <td class="text-right"><?=number_format($porcentPrecios, 2, '.', ',')?> %</td>
            </tr>
            <tr>
              <td class="bg-plomo">PRESUPUESTO <?=$_GET['area_nombre']?>, <?=$tituloPorpuestaTCPTCS?> MES (INFORMATIVO)</td>
              <td class="text-right"><?=number_format($presupuestoMes, 2, '.', ',')?></td>
              <td class="bg-plomo">Precio</td>
              <td class="text-right"><?=number_format($precioLocalX, 2, '.', ',')?></td>
              <td class="bg-plomo">Porcentaje</td>
              <td class="text-right"><?=number_format($porcentPreciosMes, 2, '.', ',')?> %</td>
            </tr>
          </table>
       <?php
        }?>
       </center></div>
   <table class="table table-condensed table-bordered">
         <tr class="text-white <?=$bgClase?>">
        <td>Cuenta / Detalle</td>
        <?php if($tipoCosto==1){
        ?> <td>Presupuesto BOB</td><?php 
        }
        ?>
        <td>Monto x Servicio BOB</td>
        <td>Monto x Servicio USD</td>
        <?php if($tipoCosto!=1){
        ?> <td>Monto x Persona BOB</td><td>Monto x Persona USD</td><td>Cantidad</td><?php 
        }
        ?>
        </tr>
<?php
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codGrupo=$row['cod_plantillagruposervicio'];
  $grupoUnidad=$row['cod_unidadorganizacional'];
  $grupoArea=$row['cod_area'];
    
    if($tipoCosto==1){
      if($row['calculado']==$row['local']){
      $montoPresupuestoFila=$row['calculado']*$nAuditorias;
      $montoCalculadoTit=($row['calculado']*$nAuditorias)*($porcentPrecios/100);
    }else{
      $montoPresupuestoFila=$row['calculado']*$nAuditorias;
      $montoCalculadoTit=($row['local']*$nAuditorias)*($porcentPrecios/100);
    }
      $montoTotales+=$montoCalculadoTit;
      $montoTotalesPresupuesto+=$montoPresupuestoFila;
       $html.='<tr class="bg-plomo">'.
                      '<td class="font-weight-bold text-left">'.$row['nombre'].'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($montoPresupuestoFila, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($montoCalculadoTit, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($montoCalculadoTit/$usd, 2, '.', ',').'</td>';
      $html.='</tr>';
     }else{
       $html.='<tr class="bg-plomo">'.
                      '<td class="font-weight-bold text-left">'.$row['nombre'].'</td>'.
                      '<td class="text-right font-weight-bold"></td><td></td><td></td><td></td><td></td>';
      $html.='</tr>';
    }
     

     $query_partidas="select pgd.cod_plantillagruposervicio,pp.nombre,pgd.cod_partidapresupuestaria,pgd.tipo_calculo,pgd.monto_local,pgd.monto_externo,pgd.monto_calculado from plantillas_gruposerviciodetalle pgd join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo join plantillas_gruposervicio pgc on pgd.cod_plantillagruposervicio=pgc.codigo where pgd.cod_plantillagruposervicio=$codGrupo";

     $stmt_partidas = $dbh->prepare($query_partidas);
     $stmt_partidas->execute();

     while ($row_partidas = $stmt_partidas->fetch(PDO::FETCH_ASSOC)) {
       $codPartida=$row_partidas['cod_partidapresupuestaria'];
         $numeroCuentas=contarPresupuestoCuentas($codPartida);

        
         if($tipoCosto==1){
        if($row_partidas['tipo_calculo']!=1){
          $numeroCuentas="(Manual)";
          $montoPresupuestoFila2=$row_partidas['monto_local']*$nAuditorias;
          $montoCalculado=($row_partidas['monto_local']*$nAuditorias)*($porcentPrecios/100);
        }else{
          $numeroCuentas="(".$numeroCuentas.")";
          $montoPresupuestoFila2=$row_partidas['monto_local']*$nAuditorias;
          $montoCalculado=($row_partidas['monto_calculado']*$nAuditorias)*($porcentPrecios/100);
        }
           $html.='<tr class="bg-info text-white">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp; '.$row_partidas['nombre'].' '.$numeroCuentas.'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($montoPresupuestoFila2, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($montoCalculado, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($montoCalculado/$usd, 2, '.', ',').'</td>';
          $html.='</tr>';
         }else{
           $html.='<tr class="bg-success text-white">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp; '.$row_partidas['nombre'].' '.$numeroCuentas.'</td>'.
                      '<td class="text-right font-weight-bold"></td><td></td><td></td><td></td><td></td>';
          $html.='</tr>';
         } 
        if($row_partidas['tipo_calculo']==1){
            $query_cuentas="SELECT pc.*,pp.cod_partidapresupuestaria FROM plan_cuentas pc join partidaspresupuestarias_cuentas pp on pc.codigo=pp.cod_cuenta where pp.cod_partidapresupuestaria=$codPartida order by pc.codigo";
            
            $stmt_cuentas = $dbh->prepare($query_cuentas);
            $stmt_cuentas->execute();
            while ($row_cuentas = $stmt_cuentas->fetch(PDO::FETCH_ASSOC)) {
               $tipoSim=obtenerValorConfiguracion(13);
               $mesActual=date("m");
                $valorConfiguracionTCPTCS=obtenerValorConfiguracion(52);
               if($valorConfiguracionTCPTCS!=1){
                $monto=ejecutadoEgresosMes(0,$anio,12,$grupoArea,0,$row_cuentas['numero']);
                //$monto=($monto/12);
               }else{
                $monto=ejecutadoEgresosMes($grupoUnidad,$anio,$mesActual,$grupoArea,1,$row_cuentas['numero']);
                //$monto=ejecutadoEgresosMes($grupoUnidad,((int)$anio-1),$mesActual,$grupoArea,0,$row_cuentas['numero']);
               }
                
                if($monto==null){$monto=0;}
                //$montoCal=costoModulo($monto,$mes);
                $montoCal=$monto*($porcentPrecios/100);
                $html.='<tr class="">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row_cuentas['nombre'].'</td>';
                    if($tipoCosto==1){
                      $html.='<td class="text-right text-muted">'.number_format($monto, 2, '.', ',').'</td>';
                    }  
                $html.='<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal/$usd, 2, '.', ',').'</td>';
                $html.='</tr>';
            }
          }else{
            
          if(!isset($_GET['anio'])){
            
            $query_cuentas=obtenerDetalleSimulacionCostosPartidaServicio($codigo,$codPartida);
          }else{
           $query_cuentas=obtenerDetalleSimulacionCostosPartidaServicioPeriodo($codigo,$codPartida,$yyyy); 
          } 
            
            $montoSimulacion=0;
            while ($row_cuentas = $query_cuentas->fetch(PDO::FETCH_ASSOC)) {
              $montoCal=$row_cuentas['monto_total'];
              $montoSimulacion+=$row_cuentas['monto_total'];
              
              $bandera=$row_cuentas['habilitado'];
              $cantidadDetalle=$row_cuentas['cantidad'];

              //obtener la cantidad real 
              $bgFila="";
              if($bandera==0){
                 $bgFila="text-danger";   
                
              }else{
                $montoTotales2+=$row_cuentas['monto_total'];
                if($cantidadDetalle==0){
                  $cantidadDetalle=1;
                }
                
                $montoTotales2Alumno+=$montoCal/$cantidadDetalle;
                $html.='<tr class="'.$bgFila.'">'.
                      '<td class="font-weight-bold text-left small">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row_cuentas['nombre'].' / '.$row_cuentas['glosa'].' ('.$tituloItem.')</td>';
               if($tipoCosto==1){
                 $html.='<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>';
                }         

               $html.='<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal/$usd, 2, '.', ',').'</td>';
                      if($tipoCosto!=1){
                        $html.='<td class="text-right text-muted">'.number_format($montoCal/$cantidadDetalle, 2, '.', ',').'</td><td class="text-right text-muted">'.number_format(($montoCal/$cantidadDetalle)/$usd, 2, '.', ',').'</td><td class="text-right text-muted">'.$cantidadDetalle.'</td>';
                      }
                      
                $html.='</tr>';
              }

            }
          }  
     }
}
    if($tipoCosto==1){
           $html.='<tr class="bg-plomo">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Total Fijos</td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format($montoTotalesPresupuesto, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format($montoTotales, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format($montoTotales/$usd, 2, '.', ',').'</td>';
                $html.='</tr>';     
         }else{
           $html.='<tr class="bg-plomo">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Total Variables</td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format($montoTotales2, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format($montoTotales2/$usd, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format($montoTotales2Alumno, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format($montoTotales2Alumno/$usd, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted font-weight-bold"></td>';
                $html.='</tr>';
          /* $html.='<tr class="bg-plomo">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Total Honorarios</td>'.
                      '<td class="text-right text-muted font-weight-bold"></td>'.
                      '<td class="text-right text-muted font-weight-bold"></td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format(costoVariablesHonorariosSimulacionServicio($codigo,$yyyy), 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format(costoVariablesHonorariosSimulacionServicio($codigo,$yyyy)/$usd, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted font-weight-bold"></td>';
                $html.='</tr>'; */    
         } 

echo $html;
?>      
  </table>
   
   <?php 
    if(isset($_GET['verSim'])){
      ?>
     <h4 class="font-weight-bold"><center>HONORARIOS <?=$tituloItem?> </center></h4>  
     <table class="table table-condensed table-bordered">
         <tr class="text-white <?=$bgClase?>">
            <td width="50%">Descripción</td>
            <td>Monto x Persona BOB</td>
            <td>Monto x Persona USD</td>
         </tr>
      <?php  
    $sql="SELECT s.*,t.nombre as tipo FROM simulaciones_servicios_auditores s join tipos_auditor t on s.cod_tipoauditor=t.codigo where s.cod_simulacionservicio=$codigo and s.cod_anio=$yyyy and s.habilitado=1 order by t.nro_orden,s.descripcion";
    $stmt=$dbh->prepare($sql);
    $stmt->execute();
    $iii=1;$totalAuditor=0;$totalAuditorUSD=0;
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
      $totalAuditor+=$montoAuditor;
      $totalAuditorUSD+=($montoAuditor/$usd);
      $cantPre=obtenerCantidadSimulacionDetalleAuditorPeriodo($codigo,$codigoTipo,$anio);
      $diasPre=obtenerDiasSimulacionDetalleAuditorPeriodo($codigo,$codigoTipo,$anio);
      if($cantidadTipo<$cantPre){
        $cantPre=$cantidadTipo;
      }
      if($diasTipo<$diasPre){
        $diasPre=$diasTipo;
      }

      if($row['cod_tipoauditor']==-100){
         $nombreTipo="<b class='text-danger'>".$nombreTipo."</b>";
      }
      $estiloFilaTextoAud="";
      $existeCostoVariableSolAu=obtenerCostoVariableSolicitadoPropuestaTCPTCS($codigo,$codigoTipo,2);
       ?>
       <tr>
         <td class="text-left"><?=$nombreTipo?></td>      
         <td class="text-right small"><?=$montoAuditor?></td>
         <td class="text-right small"><?=$montoAuditorUSD?></td>
        <tr> 
      <?php }
      ?>    
      <tr>
         <td class="text-left bg-plomo font-weight-bold">Totales</td>      
         <td class="text-right small bg-plomo font-weight-bold"><?=number_format($totalAuditor,2,".","")?></td>
         <td class="text-right small bg-plomo font-weight-bold"><?=number_format($totalAuditorUSD,2,".","")?></td>
        <tr>
     </table>    
      <?php
    }  
   if($tipoCosto!=1){
        ?><!--<div class="row div-center"><h4 class="font-weight-bold"><small>N&uacute;mero de personal registrado:</small> <small class="text-success"><?=$alumnos?></small></h4></div>--><?php 
    }
   ?></div><?php    
 }     

}
