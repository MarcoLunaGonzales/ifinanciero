<?php 
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';

$dbh = new Conexion();
// Preparamos
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaActual=date("Y-m-d");
$anio=date("Y");
 $codigo=$_GET['codigo'];
$query1="select pgd.cod_plantillagrupocosto,pc.cod_unidadorganizacional,pc.cod_area,pgc.nombre,pgc.cod_tipocosto,sum(pgd.monto_local) as local,sum(pgd.monto_externo) as externo,sum(pgd.monto_calculado) as calculado from plantillas_grupocostodetalle pgd join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo
join plantillas_gruposcosto pgc on pgd.cod_plantillagrupocosto=pgc.codigo
join plantillas_costo pc on pgc.cod_plantillacosto=pc.codigo 
where pc.codigo=$codigo";
 $mes=$_GET['mes'];
 $grupoUnidad=$_GET['unidad'];
 $grupoArea=$_GET['area'];

    $html='<table id="libro_mayor_rep" class="table table-bordered table-condensed" style="width:100%">'.
            '<thead class="bg-table-primary text-white">'.
            '<tr class="text-center">'.
              '<th colspan="3" class=""></th>'.
              '<th class="">EN IBNORCA</th>'.
              '<th class="">FUERA IBNORCA</th>'.
            '</tr>'.
            '<tr class="text-center">'.
              '<th class="text-left"><small>DETALLE</small></th>'.
              '<th><small>IMPORTE TOTAL MENSUAL</small></th>'.
              '<th><small>COSTO POR CURSO/MODULO/MES</small></th>'.
              '<th><small>BOB</small></th>'.
              '<th><small>BOB</small></th>'.
            '</tr>'.
           '</thead>'.
           '<tbody>'; 

         $html.='<tr class="bg-table-primary text-white">'.
               '<td>COSTO FIJO</td><td></td><td></td><td></td><td></td>'.
               '</tr>';  
  $query2=$query1." and pgc.cod_tipocosto=1 GROUP BY pgd.cod_plantillagrupocosto order by pgd.cod_plantillagrupocosto";
  $stmt = $dbh->prepare($query2);
  $stmt->execute();
  $totalImporte=0;$totalModulo=0;$totalLocal=0;$totalExterno=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codGrupo=$row['cod_plantillagrupocosto'];$grupoUnidad=$row['cod_unidadorganizacional'];$grupoArea=$row['cod_area'];
    $importe_grupo=(float)$row['calculado']*$mes;
    $totalImporte+=$importe_grupo;;
    $totalModulo+=$row['calculado'];
    $totalLocal+=$row['local'];
    $totalExterno+=$row['externo'];
    
     $html.='<tr class="bg-plomo">'.
                      '<td class="font-weight-bold text-left">'.$row['nombre'].'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($importe_grupo, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($row['calculado'], 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($row['local'], 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($row['externo'], 2, '.', ',').'</td>';
      $html.='</tr>';
     $query_partidas="select pgd.cod_plantillagrupocosto,pp.nombre,pgd.cod_partidapresupuestaria,pgd.tipo_calculo,pgd.monto_local,pgd.monto_externo,pgd.monto_calculado from plantillas_grupocostodetalle pgd join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo join plantillas_gruposcosto pgc on pgd.cod_plantillagrupocosto=pgc.codigo where pgd.cod_plantillagrupocosto=$codGrupo";

     $stmt_partidas = $dbh->prepare($query_partidas);
     $stmt_partidas->execute();

     while ($row_partidas = $stmt_partidas->fetch(PDO::FETCH_ASSOC)) {

         $codPartida=$row_partidas['cod_partidapresupuestaria'];
         $numeroCuentas=contarPresupuestoCuentas($codPartida);
        if($row_partidas['tipo_calculo']!=1){
          $numeroCuentas="(Manual)";
          $signoClass="block";
          $estiloFila="text-muted";
          $funcionOnclick="";
          $importe_partida=(float)$row_partidas['monto_calculado']*$mes;
        }else{
          $numeroCuentas="(".$numeroCuentas.")";
          $signoClass="add_circle";
          $estiloFila="fila-button";
          $funcionOnclick='filasPresupuesto('.$codPartida.')';
          $importe_partida=(float)$row_partidas['monto_calculado']*$mes;
        }
          $html.='<tr onclick="'.$funcionOnclick.'" class="'.$estiloFila.'">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;<i class="material-icons icon-sm simbolo'.$codPartida.'">'.$signoClass.'</i> '.$row_partidas['nombre'].' '.$numeroCuentas.'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($importe_partida, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($row_partidas['monto_calculado'], 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($row_partidas['monto_local'], 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($row_partidas['monto_externo'], 2, '.', ',').'</td>';
          $html.='</tr>';
          
          if($row_partidas['tipo_calculo']==1){
            $query_cuentas="SELECT pc.*,pp.cod_partidapresupuestaria FROM plan_cuentas pc join partidaspresupuestarias_cuentas pp on pc.codigo=pp.cod_cuenta where pp.cod_partidapresupuestaria=$codPartida order by pc.codigo";
            
            $stmt_cuentas = $dbh->prepare($query_cuentas);
            $stmt_cuentas->execute();
            while ($row_cuentas = $stmt_cuentas->fetch(PDO::FETCH_ASSOC)) {
                $monto=obtenerMontoPorCuenta($row_cuentas['numero'],$grupoUnidad,$grupoArea,((int)$anio-1));
                if($monto==null){$monto=0;}
                $montoCal=costoModulo($monto,$mes);
                $html.='<tr class="cuenta'.$codPartida.'" style="display:none">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row_cuentas['nombre'].'</td>'.
                      '<td class="text-right text-muted">'.number_format($monto, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>';
                $html.='</tr>';
            }
          }

     } 
 }


$html.='<tr class="bg-table-primary text-white">'.
                      '<td class="font-weight-bold text-left">TOTAL COSTO FIJO</td>'.
                      '<td class="text-right" id="fijo_importe">'.number_format($totalImporte, 2, '.', ',').'</td>'.
                      '<td class="text-right" id="fijo_modulo">'.number_format($totalModulo, 2, '.', ',').'</td>'.
                      '<td class="text-right" id="fijo_ibnorca">'.number_format($totalLocal, 2, '.', ',').'</td>'.
                      '<td class="text-right" id="fijo_fuera">'.number_format($totalExterno, 2, '.', ',').'</td>';
                    $html.='</tr>';

  $html.='<tr class="bg-table-primary text-white">'.
               '<td>COSTO VARIABLE</td><td></td><td></td><td></td><td></td>'.
               '</tr>';  
  $query2=$query1." and pgc.cod_tipocosto=2 GROUP BY pgd.cod_plantillagrupocosto order by pgd.cod_plantillagrupocosto";
  $stmt = $dbh->prepare($query2);
  $stmt->execute();
  $totalImporte2=0;$totalModulo2=0;$totalLocal2=0;$totalExterno2=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codGrupo=$row['cod_plantillagrupocosto'];$grupoUnidad=$row['cod_unidadorganizacional'];$grupoArea=$row['cod_area'];
    $importe_grupo=(float)$row['calculado']*$mes;
    $totalImporte2+=$importe_grupo;
    $totalModulo2+=$row['calculado'];
    $totalLocal2+=$row['local'];
    $totalExterno2+=$row['externo'];   
     $html.='<tr class="bg-plomo">'.
                      '<td class="font-weight-bold text-left">'.$row['nombre'].'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($importe_grupo, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($row['calculado'], 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($row['local'], 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($row['externo'], 2, '.', ',').'</td>';
      $html.='</tr>';
     $query_partidas="select pgd.cod_plantillagrupocosto,pp.nombre,pgd.cod_partidapresupuestaria,pgd.tipo_calculo,pgd.monto_local,pgd.monto_externo,pgd.monto_calculado from plantillas_grupocostodetalle pgd join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo join plantillas_gruposcosto pgc on pgd.cod_plantillagrupocosto=pgc.codigo where pgd.cod_plantillagrupocosto=$codGrupo";

     $stmt_partidas = $dbh->prepare($query_partidas);
     $stmt_partidas->execute();

     while ($row_partidas = $stmt_partidas->fetch(PDO::FETCH_ASSOC)) {

         $codPartida=$row_partidas['cod_partidapresupuestaria'];
         $numeroCuentas=contarPresupuestoCuentas($codPartida);
        if($row_partidas['tipo_calculo']!=1){
          $numeroCuentas="(Manual)";
          $signoClass="block";
          $estiloFila="text-muted";
          $funcionOnclick="";
          $importe_partida=(float)$row_partidas['monto_calculado']*$mes;
        }else{
          $numeroCuentas="(".$numeroCuentas.")";
          $signoClass="add_circle";
          $estiloFila="fila-button";
          $funcionOnclick='filasPresupuesto('.$codPartida.')';
          $importe_partida=(float)$row_partidas['monto_calculado']*$mes;
        }
          $html.='<tr onclick="'.$funcionOnclick.'" class="'.$estiloFila.'">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;<i class="material-icons icon-sm simbolo'.$codPartida.'">'.$signoClass.'</i> '.$row_partidas['nombre'].' '.$numeroCuentas.'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($importe_partida, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($row_partidas['monto_calculado'], 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($row_partidas['monto_local'], 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($row_partidas['monto_externo'], 2, '.', ',').'</td>';
          $html.='</tr>';
          
          if($row_partidas['tipo_calculo']==1){
            $query_cuentas="SELECT pc.*,pp.cod_partidapresupuestaria FROM plan_cuentas pc join partidaspresupuestarias_cuentas pp on pc.codigo=pp.cod_cuenta where pp.cod_partidapresupuestaria=$codPartida order by pc.codigo";
            
            $stmt_cuentas = $dbh->prepare($query_cuentas);
            $stmt_cuentas->execute();
            while ($row_cuentas = $stmt_cuentas->fetch(PDO::FETCH_ASSOC)) {
                $monto=obtenerMontoPorCuenta($row_cuentas['numero'],$grupoUnidad,$grupoArea,((int)$anio-1));
                if($monto==null){$monto=0;}
                $montoCal=costoModulo($monto,$mes);
                $html.='<tr class="cuenta'.$codPartida.'" style="display:none">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row_cuentas['nombre'].'</td>'.
                      '<td class="text-right text-muted">'.number_format($monto, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>';
                $html.='</tr>';
            }
          }

     } 
 }


$html.='<tr class="bg-table-primary text-white">'.
                      '<td class="font-weight-bold text-left">TOTAL COSTO VARIABLE</td>'.
                      '<td class="text-right" id="fijo_importe">'.number_format($totalImporte2, 2, '.', ',').'</td>'.
                      '<td class="text-right" id="fijo_modulo">'.number_format($totalModulo2, 2, '.', ',').'</td>'.
                      '<td class="text-right" id="fijo_ibnorca">'.number_format($totalLocal2, 2, '.', ',').'</td>'.
                      '<td class="text-right" id="fijo_fuera">'.number_format($totalExterno2, 2, '.', ',').'</td>';
                    $html.='</tr>';
$totalIbnorca=$totalLocal+$totalLocal2;
$totalFuera=$totalExterno+$totalExterno2;
$html.='<tr class="bg-table-total">'.
                      '<td class="font-weight-bold text-left">COSTO TOTAL</td>'.
                      '<td></td>'.
                      '<td></td>'.
                      '<td class="text-right font-weight-bold" id="ibnorca">'.number_format($totalIbnorca, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold" id="fuera">'.number_format($totalFuera, 2, '.', ',').'</td>';
                    $html.='</tr>';
$html.='</tbody>';
$html.=    '</table>';
echo $html;

?>