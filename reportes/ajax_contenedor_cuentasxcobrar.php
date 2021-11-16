<?php //ESTADO FINALIZADO
require_once __DIR__.'/../conexion.php';

require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';

$dbh = new Conexion();


$cuentas_auxiliares=$_GET['cuentas_auxiliares'];

$proveedoresString=implode(",", $cuentas_auxiliares);
$proveedoresStringAux="and e.cod_cuentaaux in ($proveedoresString)";

$StringUnidades=$_GET['unidades'];
$gestion=$_GET['gestion'];
$desde=$_GET['desde'];
$hasta=$_GET['hasta'];

$cuentaString=$_GET['cuentai'];
$cuenta=explode(',', $cuentaString);

// if(count($proveedores)==(int)$_POST["numero_proveedores"]){
//   $proveedoresStringAux="";
// }
// $StringCuenta=implode(",", $cuenta);
//$StringUnidades=implode(",", $unidad);


$stmtG = $dbh->prepare("SELECT * from gestiones WHERE codigo=:codigo");
$stmtG->bindParam(':codigo',$gestion);
$stmtG->execute();
$resultG = $stmtG->fetch();
$NombreGestion = $resultG['nombre'];

$i=0;$saldo=0;
$indice=0;
$totalCredito=0;
$totalDebito=0;

$unidadCosto=$_GET['unidad_costo'];
$areaCosto=$_GET['area_costo'];

$unidadCostoArray=implode(",", $unidadCosto);
$areaCostoArray=implode(",", $areaCosto);

// $unidadAbrev=abrevUnidad($unidadCostoArray);
// $areaAbrev=abrevArea($areaCostoArray);

$periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($desde))." al ".strftime('%d/%m/%Y',strtotime($hasta));

$string_periodo="30,60,90";
$array_periodo=explode(",", $string_periodo);
$totales_array=[];
require_once 'reportesEstadoCuentasPrint_saldos_detalle.php';
  

echo '<table class="table table-bordered table-condensed" id="tablePaginatorFixedEstadoCuentas">'.
  '<thead>'.
      '<tr class="">'.
          '<th class="text-left">-</th>'.
          '<th class="text-left">Cliente</th>';
          $periodo=0;
          $x=0;

          foreach ($array_periodo as $periodo) {
              echo '<th class="text-right">'.$periodo.' Días</th>';
              $monto_periodo[$x]=0;
              $totales_array[$x]=0;
              $x++;
          }
          $monto_periodo[$x]=0;
          $totales_array[$x]=0;
          $totales_array[$x+1]=0;//para el total
          echo '<th class="text-right"> > '.$periodo.' Días</th>';
      echo '<th class="text-right">Total</th></tr>'.
  '</thead>'.
  '<tbody>';
  foreach ($cuenta as $cuentai) {
      $nombreCuenta=nameCuenta($cuentai);//nombre de cuenta
      echo '<tr style="background-color:#9F81F7;">
          <td style="display: none;"></td>
          <td class="text-left small" colspan="2">CUENTA</td>
          <td class="text-left small" colspan="5">'.$nombreCuenta.'</td>
          <td style="display: none;"></td>
          <td style="display: none;"></td>                                        
          <td style="display: none;"></td>
      </tr>';

      $sqlFechaEstadoCuenta="and e.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59'";
      if(isset($_POST['cierre_anterior'])){
        $sqlFechaEstadoCuenta="and e.fecha<='$hasta 23:59:59'";  
      }
      $sql="SELECT e.fecha,e.cod_cuentaaux,ca.nombre,(SELECT c.tipo from configuracion_estadocuentas c where c.cod_plancuenta=d.cod_cuenta)as tipoDebeHaber
          FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentai) and e.cod_comprobantedetalleorigen=0 and cc.cod_gestion= '$NombreGestion' $sqlFechaEstadoCuenta and cc.cod_unidadorganizacional in ($StringUnidades) $proveedoresStringAux and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) GROUP BY e.cod_cuentaaux  order by ca.nombre"; //ca.nombre, 
      // echo $sql;
      $stmtUO = $dbh->prepare($sql);
      $stmtUO->execute();
      $index=1;
      
      while ($row = $stmtUO->fetch()) {
          // $fechaX=$row['fecha'];
          // $monto_ecX=$row['monto_ec'];
          $cod_cuentaauxX=$row['cod_cuentaaux'];
          $nombreX=$row['nombre'];
          $tipoDebeHaberX=$row['tipoDebeHaber'];   
          // $periodo=0;
          if($tipoDebeHaberX==2){//proveedor
              // $totalCredito=$totalCredito+$monto_ecX;
              // $totalDebito=$totalDebito+$monto_ecD;
              // $saldo_X=$monto_ecX-$monto_ecD;
              // echo '<tr class="bg-white" >
              //     <td class="text-center small">'.$index.'</td>
              //     <td class="text-left small">'.$nombreX.'</td>
              //     <td class="text-right small">'.formatNumberDec($saldo_X).'</td>
              // </tr>'; 
          }else{//cliente
              echo '<tr class="bg-white" >
                  <td class="text-center small">'.$index.'</td>
                  <td class="text-left small">'.$nombreX.'</td>';
                  // include "reportesEstadoCuentasPrint_saldos_detalle.php";
                  $array_totales=generarHTMLFacCliente($cuentai,$NombreGestion,$sqlFechaEstadoCuenta,$StringUnidades,$cod_cuentaauxX,$unidadCostoArray,$areaCostoArray,$desde,$hasta,$monto_periodo,$array_periodo);
                  echo '</tr>';
                  //para los totales **                                                
              $x_total=0;
              foreach ($array_totales as $monto_x) {          
                  $totales_array[$x_total]+=$monto_x;
                  $x_total++;
              }
              //totales fin
              }
          $index++;
      }    
  }                                

  echo '<tr>
      <td style="display: none;"></td>
      <td class="text-right small" colspan="2">Total:</td>';
    foreach ($totales_array as $monto_total) {
        echo '<th class="text-right">'.formatNumberDec($monto_total).'</th>'; 
    }    
  echo '</tr>';  

  

  // echo '<tr>
  //     <td style="display: none;"></td>
  //     <td class="text-right small" colspan="2">Total:</td>
  //     <td class="text-right small font-weight-bold">'.formatNumberDec($totalDebito).'</td>
  //     <td class="text-right small font-weight-bold">'.formatNumberDec($totalCredito).'</td>
  //     <td class="text-right small font-weight-bold">'.formatNumberDec($totalSaldo).'</td>
  // </tr>';  
  echo '</tbody>
</table>';