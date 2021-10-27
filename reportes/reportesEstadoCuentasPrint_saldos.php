<?php //ESTADO FINALIZADO
$proveedoresString=implode(",", $proveedores);
$proveedoresStringAux="and e.cod_cuentaaux in ($proveedoresString)";
if(count($proveedores)==(int)$_POST["numero_proveedores"]){
  $proveedoresStringAux="";
}
$StringCuenta=implode(",", $cuenta);
$StringUnidades=implode(",", $unidad);
$stringGeneraCuentas="";

foreach ($cuenta as $cuentai ) {    
    $stringGeneraCuentas.=nameCuenta($cuentai).",";
}
$stringGeneraUnidades="";
foreach ($unidad as $unidadi ) {    
    $stringGeneraUnidades.=" ".abrevUnidad($unidadi)." ";
}
$stmtG = $dbh->prepare("SELECT * from gestiones WHERE codigo=:codigo");
$stmtG->bindParam(':codigo',$gestion);
$stmtG->execute();
$resultG = $stmtG->fetch();
$NombreGestion = $resultG['nombre'];

$i=0;$saldo=0;
$indice=0;
$totalCredito=0;
$totalDebito=0;

$unidadCosto=$_POST['unidad_costo'];
$areaCosto=$_POST['area_costo'];

$unidadCostoArray=implode(",", $unidadCosto);
$areaCostoArray=implode(",", $areaCosto);
$unidadAbrev=abrevUnidad($unidadCostoArray);
$areaAbrev=abrevArea($areaCostoArray);
$periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($desde))." al ".strftime('%d/%m/%Y',strtotime($hasta));

$string_periodo="30,60,90";
$array_periodo=explode(",", $string_periodo);

require_once 'reportesEstadoCuentasPrint_saldos_detalle.php';
?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-icon">
                        <!--div class="float-right col-sm-2">
                            <h6 class="card-title">Exportar como:</h6>
                        </div-->
                        <h4 class="card-title"> 
                            <img  class="card-img-top" src="../marca.png" style="width:100%; max-width:50px;">
                            Estado de Cuentas
                        </h4>
                      <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
                      <h6 class="card-title">Gestion: <?= $NombreGestion; ?></h6>
                      <h6 class="card-title">Cuenta: <?=$stringGeneraCuentas;?></h6>
                      <h6 class="card-title">Unidad:<?=$stringGeneraUnidades?></h6>             
                      <div class="row">
                        <div class="col-sm-6"><h5 class="card-title"><b>Centro de Costo - Oficina: </b> <small><?=$unidadAbrev?></small></h6></div>
                        <div class="col-sm-6"><h5 class="card-title"><b>Centro de Costo - Area: </b> <small><?=$areaAbrev?></small></h6></div>
                      </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php 
                            echo '<table class="table table-bordered table-condensed" id="tablePaginatorFixedEstadoCuentas">'.
                                '<thead>'.
                                    '<tr class="">'.
                                        '<th class="text-left">-</th>'.
                                        '<th class="text-left">Proveedor/Cliente</th>';
                                        $periodo=0;
                                        $x=0;
                                        foreach ($array_periodo as $periodo) {
                                            echo '<th class="text-right">'.$periodo.' Días</th>';
                                            $monto_periodo[$x]=0;
                                            $x++;
                                        }
                                        $monto_periodo[$x]=0;
                                        echo '<th class="text-right"> > '.$periodo.' Días</th>';
                                    echo '<th class="text-right">Total</th></tr>'.
                                '</thead>'.
                                '<tbody>';
                                foreach ($cuenta as $cuentai ) {
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
                                        FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentai) and e.cod_comprobantedetalleorigen=0 and cc.cod_gestion= '$NombreGestion' $sqlFechaEstadoCuenta and cc.cod_unidadorganizacional in ($StringUnidades) $proveedoresStringAux and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) GROUP BY e.cod_cuentaaux  order by e.fecha"; //ca.nombre, 
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
                                                generarHTMLFacCliente($cuentai,$NombreGestion,$sqlFechaEstadoCuenta,$StringUnidades,$cod_cuentaauxX,$unidadCostoArray,$areaCostoArray,$desde,$hasta,$monto_periodo,$array_periodo);
                                                echo '</tr>'; 
                                            }
                                        $index++;
                                    }    
                                }
                                // $totalSaldo=$totalDebito-$totalCredito;
                                // if($totalSaldo<0){
                                //     $totalSaldo=$totalSaldo*(-1);
                                // }                         
                                // echo '<tr>
                                //     <td style="display: none;"></td>
                                //     <td class="text-right small" colspan="2">Total:</td>
                                //     <td class="text-right small font-weight-bold">'.formatNumberDec($totalDebito).'</td>
                                //     <td class="text-right small font-weight-bold">'.formatNumberDec($totalCredito).'</td>
                                //     <td class="text-right small font-weight-bold">'.formatNumberDec($totalSaldo).'</td>
                                // </tr>';  
                                echo '</tbody>
                            </table>';
                            // echo $html;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</div>

