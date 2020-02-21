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
   <table class="table table-condensed table-bordered">
         <tr class="text-white <?=$bgClase?>">
        <td>Cuenta / Detalle</td>
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
    if($row['calculado']==$row['local']){
      $montoCalculadoTit=$row['calculado'];
    }else{
      $montoCalculadoTit=$row['local'];
    }
      $montoTotales+=$montoCalculadoTit;
    if($tipoCosto==1){
       $html.='<tr class="bg-plomo">'.
                      '<td class="font-weight-bold text-left">'.$row['nombre'].'</td>'.
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

        if($row_partidas['tipo_calculo']!=1){
          $numeroCuentas="(Manual)";
          $montoCalculado=$row_partidas['monto_local'];
        }else{
          $numeroCuentas="(".$numeroCuentas.")";
          $montoCalculado=$row_partidas['monto_calculado'];
        }
        
         if($tipoCosto==1){
           $html.='<tr class="bg-info text-white">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp; '.$row_partidas['nombre'].' '.$numeroCuentas.'</td>'.
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
               if($tipoSim==1){
                $monto=ejecutadoEgresosMes($grupoUnidad,((int)$anio-1),12,$grupoArea,1,$row_cuentas['numero']);
                $monto=($monto/12);
               }else{
                $monto=ejecutadoEgresosMes($grupoUnidad,((int)$anio-1),$mesActual,$grupoArea,0,$row_cuentas['numero']);
               }
                
                if($monto==null){$monto=0;}
                $montoCal=costoModulo($monto,$mes);
                $html.='<tr class="">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row_cuentas['nombre'].'</td>'.
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
            if($habNorma==1){
               $html.='<tr class="bg-warning text-dark">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; NORMA </td>'.
                      '<td class="text-right font-weight-bold">'.number_format($montoNorma, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($montoNorma/$alumnos, 2, '.', ',').'</td>';
                $html.='</tr>';
                $montoTotales2+=$montoNorma;
                $montoTotales2Alumno+=($montoNorma/$alumnos); 
            }
                
         } 
    if($tipoCosto==1){
           $html.='<tr class="bg-plomo">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Total </td>'.
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