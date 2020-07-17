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
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$mesActualConsulta=date("m");
if(isset($_GET["simulacion"])){
 $codigo=$_GET["simulacion"];
 $montoNorma=obtenerMontoNormaSimulacion($codigo);
 $habNorma=obtenerHabilitadoNormaSimulacion($codigo);

 $codPlan=$_GET["plantilla"];
 $tipoCosto=$_GET["tipo"];
 $alumnos=$_GET["al"];
$anio=date("Y");
$mes=obtenerValorConfiguracion(6);
$query1="select pgd.cod_plantillagrupocosto,pc.cod_unidadorganizacional,pc.cod_area,pgc.nombre,pgc.cod_tipocosto,sum(pgd.monto_local) as local,sum(pgd.monto_externo) as externo,sum(pgd.monto_calculado) as calculado from plantillas_grupocostodetalle pgd join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo
join plantillas_gruposcosto pgc on pgd.cod_plantillagrupocosto=pgc.codigo
join plantillas_costo pc on pgc.cod_plantillacosto=pc.codigo 
where pc.codigo=$codPlan";

if($tipoCosto==1){
$query2=$query1." and pgc.cod_tipocosto=1 GROUP BY pgd.cod_plantillagrupocosto order by pgd.cod_plantillagrupocosto";
$bgClase="bg-info";
}else{
  $query2=$query1." and pgc.cod_tipocosto=2 GROUP BY pgd.cod_plantillagrupocosto order by pgd.cod_plantillagrupocosto";
  $bgClase="bg-success";
}
  $stmt = $dbh->prepare($query2);
  $stmt->execute();
  $html='';$montoTotales=0;$montoTotales2=0;$montoTotales2Alumno=0;
?>
       <div class="row">
         
       </div>
       <div class=""><center>
        <?php if($tipoCosto==1){
          $porCre=($_GET['porcentaje_fijo']/100);
          /* DATOS PARA PRECIO EN LUGAR DE CANTIDAD AUDITORIAS*/
          $precioLocalX=obtenerPrecioSimulacionCosto($codigo)*$alumnos;
          $precioRegistrado=obtenerPrecioRegistradoPlantillaCosto($codPlan);
          $nCursos=obtenerCantidadCursosPlantillaCosto($codPlan); 
          
          $porcentPrecios=($precioLocalX*100)/($precioRegistrado);
          $codigoPrecio=obtenerCodigoPrecioSimulacionCosto($codigo);
          $ingresoAlternativo=obtenerPrecioAlternativoDetalle($codigoPrecio);
          if($ingresoAlternativo!=0){
            $precioLocalX=$ingresoAlternativo;
          }

          /* fin de datos */
          $tituloUnidad="NACIONAL";
          if(obtenerValorConfiguracion(51)==1){
            $tituloUnidad=$_GET['unidad_nombre'];
          }
          $codOficina=0;$codAreaX=0;
          $datosPlantilla=obtenerPlantillaCostoDatos($codPlan);
          while ($rowPlantilla = $datosPlantilla->fetch(PDO::FETCH_ASSOC)) {
            $codOficina=$rowPlantilla['cod_unidadorganizacional'];
            $codAreaX=$rowPlantilla['cod_area'];
          }
          $presupuestoMes=obtenerPresupuestoEjecucionPorArea($codOficina,$codAreaX,$globalNombreGestion,$mesActualConsulta)['presupuesto'];
          $porcentPreciosMes=($precioLocalX*100)/($presupuestoMes);
         ?>
          <table class="table table-condensed table-bordered">
            <tr class="text-white <?=$bgClase?>">
              <td colspan="6">DATOS</td>
            </tr>
            <tr>
              <td class="bg-plomo">PRESUPUESTO <?=$_GET['area_nombre']?>, <?=$tituloUnidad?> GESTION</td>
              <td class="text-right"><?=number_format($precioRegistrado, 2, '.', ',')?></td>
              <td class="bg-plomo">Precio</td>
              <td class="text-right"><?=number_format($precioLocalX, 2, '.', ',')?></td>
              <td class="bg-plomo">Porcentaje</td>
              <td class="text-right"><?=number_format($porcentPrecios, 2, '.', ',')?> %</td>
            </tr>
            <tr>
              <td class="bg-plomo">PRESUPUESTO <?=$_GET['area_nombre']?>, <?=$tituloUnidad?> MES (INFORMATIVO)</td>
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
        ?> <td>Ejecutado Gestión</td><?php 
        }?>
        <td>Monto x Modulo</td>
        <?php if($tipoCosto!=1){
        ?> <td>Monto x Alumno</td><?php 
        }
        ?>
        </tr>
<?php
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codGrupo=$row['cod_plantillagrupocosto'];
  $grupoUnidad=$row['cod_unidadorganizacional'];
  $grupoArea=$row['cod_area'];

    
    if($tipoCosto==1){
      if($row['calculado']==$row['local']){
      $montoCalculadoTit=$row['calculado']*($porcentPrecios/100)*$nCursos;
      $montoCalculadoEjecutadoPadre=$row['calculado']*$nCursos;
    }else{
      $montoCalculadoTit=$row['local']*($porcentPrecios/100)*$nCursos;
      $montoCalculadoEjecutadoPadre=$row['local']*$nCursos;
    }
      $montoTotales+=$montoCalculadoTit;

       $html.='<tr class="bg-plomo">'.
                      '<td class="font-weight-bold text-left">'.$row['nombre'].'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($montoCalculadoEjecutadoPadre, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($montoCalculadoTit, 2, '.', ',').'</td>';
      $html.='</tr>';
     }else{
       $html.='<tr class="bg-plomo">'.
                      '<td class="font-weight-bold text-left">'.$row['nombre'].'</td>'.
                      '<td class="text-right font-weight-bold"></td><td></td>';
      $html.='</tr>';
    }
     

     $query_partidas="select pgd.cod_plantillagrupocosto,pp.nombre,pgd.cod_partidapresupuestaria,pgd.tipo_calculo,pgd.monto_local,pgd.monto_externo,pgd.monto_calculado from plantillas_grupocostodetalle pgd join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo join plantillas_gruposcosto pgc on pgd.cod_plantillagrupocosto=pgc.codigo where pgd.cod_plantillagrupocosto=$codGrupo";

     $stmt_partidas = $dbh->prepare($query_partidas);
     $stmt_partidas->execute();

     while ($row_partidas = $stmt_partidas->fetch(PDO::FETCH_ASSOC)) {
       $codPartida=$row_partidas['cod_partidapresupuestaria'];
         $numeroCuentas=contarPresupuestoCuentas($codPartida);

        
        
         if($tipoCosto==1){
          if($row_partidas['tipo_calculo']!=1){
          $numeroCuentas="(Manual)";
          $montoCalculado=$row_partidas['monto_local']*($porcentPrecios/100)*$nCursos;
          $montoCalculadoEjecutado=$row_partidas['monto_local']*$nCursos;
        }else{
          $numeroCuentas="(".$numeroCuentas.")";
          $montoCalculado=$row_partidas['monto_calculado']*($porcentPrecios/100)*$nCursos;
          $montoCalculadoEjecutado=$row_partidas['monto_calculado']*$nCursos;
        }
           $html.='<tr class="bg-info text-white">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp; '.$row_partidas['nombre'].' '.$numeroCuentas.'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($montoCalculadoEjecutado, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($montoCalculado, 2, '.', ',').'</td>';
          $html.='</tr>';
         }else{
           $html.='<tr class="bg-success text-white">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp; '.$row_partidas['nombre'].' '.$numeroCuentas.'</td>'.
                      '<td class="text-right font-weight-bold"></td><td></td>';
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
                $montoCal=$monto*($porcentPrecios/100);
                $html.='<tr class="">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row_cuentas['nombre'].'</td>'.
                      '<td class="text-right text-muted">'.number_format($monto, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>';
                $html.='</tr>';
            }
          }else{ 
            $query_cuentas=obtenerDetalleSimulacionCostosPartida($codigo,$codPartida);
            $montoSimulacion=0;
            while ($row_cuentas = $query_cuentas->fetch(PDO::FETCH_ASSOC)) {
              $montoCal=$row_cuentas['monto_total'];
              $montoSimulacion+=$row_cuentas['monto_total'];
              
              $bandera=$row_cuentas['habilitado'];
              $bgFila="";
              if($bandera==0){
                 $bgFila="text-danger";   
              }else{
                $montoTotales2+=$row_cuentas['monto_total'];
                $montoTotales2Alumno+=$montoCal/$alumnos;
                $html.='<tr class="'.$bgFila.'">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row_cuentas['nombre'].' / '.$row_cuentas['glosa'].'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>';
                      if($tipoCosto!=1){
                        $html.='<td class="text-right text-muted">'.number_format($montoCal/$alumnos, 2, '.', ',').'</td>';
                      }
                      
                $html.='</tr>';
              }

            }
          }  
     }
}
         if($tipoCosto!=1){
            /*if($habNorma==1){
               $html.='<tr class="bg-warning text-dark">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; NORMA </td>'.
                      '<td class="text-right font-weight-bold">'.number_format($montoNorma, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($montoNorma/$alumnos, 2, '.', ',').'</td>';
                $html.='</tr>';
                $montoTotales2+=$montoNorma;
                $montoTotales2Alumno+=($montoNorma/$alumnos); 
            }*/
                
         } 
    if($tipoCosto==1){
           $html.='<tr class="bg-plomo">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Total </td>'.
                      '<td class="font-weight-bold text-left"></td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format($montoTotales, 2, '.', ',').'</td>';
                $html.='</tr>';
         }else{
           $html.='<tr class="bg-plomo">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Total </td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format($montoTotales2, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format($montoTotales2Alumno, 2, '.', ',').'</td>';
                $html.='</tr>';
         } 

echo $html;
?>      
  </table>
  <?php  
   if($tipoCosto!=1){
        ?><div class="row div-center"><h4 class="font-weight-bold"><small>N&uacute;mero de alumnos registrado:</small> <small class="text-success"><?=$alumnos?></small></h4></div><?php 
    }   
}     