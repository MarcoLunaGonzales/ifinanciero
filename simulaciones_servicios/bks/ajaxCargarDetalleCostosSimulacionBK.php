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
 $codPlan=$_GET["plantilla"];
 $codArea=obtenerCodigoAreaPlantillaServicio($codPlan);
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
  $html='';$montoTotales=0;$montoTotales2=0;$montoTotales2Alumno=0;
?>
       <div class="row">
         
       </div>
   <table class="table table-condensed table-bordered">
         <tr class="text-white <?=$bgClase?>">
        <td>Cuenta / Detalle</td>
        <td>Monto x Servicio</td>
        <?php if($tipoCosto!=1){
        ?> <td>Monto x Persona</td><td>Cantidad</td><?php 
        }
        ?>
        </tr>
<?php
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codGrupo=$row['cod_plantillagruposervicio'];
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
                      '<td class="text-right font-weight-bold"></td><td></td><td></td>';
      $html.='</tr>';
    }
     

     $query_partidas="select pgd.cod_plantillagruposervicio,pp.nombre,pgd.cod_partidapresupuestaria,pgd.tipo_calculo,pgd.monto_local,pgd.monto_externo,pgd.monto_calculado from plantillas_gruposerviciodetalle pgd join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo join plantillas_gruposervicio pgc on pgd.cod_plantillagruposervicio=pgc.codigo where pgd.cod_plantillagruposervicio=$codGrupo";

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
                      '<td class="text-right font-weight-bold"></td><td></td><td></td>';
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
            $query_cuentas=obtenerDetalleSimulacionCostosPartidaServicio($codigo,$codPartida);
            $montoSimulacion=0;
            while ($row_cuentas = $query_cuentas->fetch(PDO::FETCH_ASSOC)) {
              $montoCal=$row_cuentas['monto_total'];
              $montoSimulacion+=$row_cuentas['monto_total'];
              
              $bandera=$row_cuentas['habilitado'];
              $cantidadDetalle=$row_cuentas['cantidad'];
              $bgFila="";
              if($bandera==0){
                 $bgFila="text-danger";   
                $html.='<tr class="'.$bgFila.'">'.
                      '<td class="font-weight-bold text-left"><strike>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row_cuentas['nombre'].' / '.$row_cuentas['glosa'].'</strike></td>'.
                      '<td class="text-right text-muted">'.number_format(0, 2, '.', ',').'</td>';
                      if($tipoCosto!=1){
                        $html.='<td class="text-right text-muted">'.number_format(0, 2, '.', ',').'</td><td>'.$cantidadDetalle.'</td>';
                      }
                $html.='</tr>';
              }else{
                $montoTotales2+=$row_cuentas['monto_total'];
                $montoTotales2Alumno+=$montoCal/$cantidadDetalle;
                $html.='<tr class="'.$bgFila.'">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row_cuentas['nombre'].' / '.$row_cuentas['glosa'].'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>';
                      if($tipoCosto!=1){
                        $html.='<td class="text-right text-muted">'.number_format($montoCal/$cantidadDetalle, 2, '.', ',').'</td><td class="text-right text-muted">'.$cantidadDetalle.'</td>';
                      }
                      
                $html.='</tr>';
              }

            }
          }  
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
                      '<td class="text-right text-muted font-weight-bold">'.number_format($montoTotales2Alumno, 2, '.', ',').'</td><td class="text-right text-muted font-weight-bold"></td>';
                $html.='</tr>';
         } 

echo $html;
?>      
  </table>
  <?php  
   if($tipoCosto!=1){
        ?><div class="row div-center"><h4 class="font-weight-bold"><small>N&uacute;mero de personal registrado:</small> <small class="text-success"><?=$alumnos?></small></h4></div><?php 
    }   
}     
