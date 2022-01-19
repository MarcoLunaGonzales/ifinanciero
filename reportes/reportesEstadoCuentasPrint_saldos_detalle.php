<?php

function generarHTMLFacCliente($cuentai,$NombreGestion,$sqlFechaEstadoCuenta,$StringUnidades,$cod_cuentaauxX,$unidadCostoArray,$areaCostoArray,$desde,$hasta,$monto_periodo,$array_periodo,$nombreX,$index){
    require_once __DIR__.'/../conexion.php';            
    require_once __DIR__.'/../functionsGeneral.php';
    $dbh = new Conexion();

    $saldo_X=0;
    $fecha_actual=date('Y-m-d');
    $sql="SELECT e.codigo,e.fecha,e.monto
        FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentai) and e.cod_comprobantedetalleorigen=0 and cc.cod_gestion= '$NombreGestion' $sqlFechaEstadoCuenta and cc.cod_unidadorganizacional in ($StringUnidades) and e.cod_cuentaaux in ($cod_cuentaauxX) and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) order by e.fecha"; //ca.nombre, 
    //echo $sql."***<br>";
    $stmtUO = $dbh->prepare($sql);
    $stmtUO->execute();
    while ($row = $stmtUO->fetch()) {
        $codigo_ec=$row['codigo'];
        $fechaDet=$row['fecha'];
        $monto_ecX=$row['monto'];
        //PAGADO
        $sql="SELECT sum(e.monto) as monto
        FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and cc.cod_estadocomprobante<>2 and e.cod_comprobantedetalleorigen=$codigo_ec";
        // echo $sql."***<br>";
        $stmt_d = $dbh->prepare($sql);
        $stmt_d->execute();
        $monto_ecD=0;
        while ($row_d = $stmt_d->fetch()) {
            $monto_ecD=$row_d['monto'];
        }
        $saldo_X=$monto_ecX-$monto_ecD;
        

        $fechai=$desde;
        $i=0;
        $date1 = new DateTime($fechaDet);
        $date2 = new DateTime($fecha_actual);
        $diff = $date1->diff($date2);        
        $dias_mora=$diff->days;
        // echo $dias_mora."-";
        $periodo=0;
        $periodo1=0;
        foreach ($array_periodo as $periodo) {
            // echo $periodo1."<".$dias_mora." ".$dias_mora."<=".$periodo."<br>";
            if($periodo1<$dias_mora and $dias_mora<=$periodo){
                // echo "si<br>";
                $monto_periodo[$i]+=$saldo_X;
            }
            $periodo1=$periodo;

            $i++;
            //$fechai=$fechaf;
        }
        if($dias_mora>$periodo){//si es mayor a 90 dias
            $monto_periodo[$i]+=$saldo_X;
        }
    }
    $suma_proveedor=0;
    $j=0;
    $sumaTotalCliente=0;
    $array_periodo_total=[];
    $periodo1=0;
    foreach ($monto_periodo as $monto) {
        $suma_proveedor+=$monto;
    }

    if($suma_proveedor>0){

        echo '<tr class="bg-white" >
                <td class="text-center small">'.$index.'</td>
                <td class="text-left small">'.$nombreX.'</td>';

        foreach ($array_periodo as $periodo) {
            echo '<td class="text-right small"><a href="reportesEstadoCuentasPrint_saldos_detalle_xperiodo.php?cuentai='.$cuentai.'&NombreGestion='.$NombreGestion.'&StringUnidades='.$StringUnidades.'&cod_cuentaauxX='.$cod_cuentaauxX.'&unidadCostoArray='.$unidadCostoArray.'&areaCostoArray='.$areaCostoArray.'&desde='.$desde.'&hasta='.$hasta.'&periodo1='.$periodo1.'&periodo2='.$periodo.'" target="_blank">'.formatNumberDec($monto_periodo[$j]).'</a></td>';
            $sumaTotalCliente+=$monto_periodo[$j];
            $array_periodo_total[$j]=$monto_periodo[$j];
            if($j==0){
                $periodo1++;
            }
            $j++;
            $periodo1+=30;
        }    
        $periodo++;
        echo '<td class="text-right small"><a href="reportesEstadoCuentasPrint_saldos_detalle_xperiodo.php?cuentai='.$cuentai.'&NombreGestion='.$NombreGestion.'&StringUnidades='.$StringUnidades.'&cod_cuentaauxX='.$cod_cuentaauxX.'&unidadCostoArray='.$unidadCostoArray.'&areaCostoArray='.$areaCostoArray.'&desde='.$desde.'&hasta='.$hasta.'&periodo1='.$periodo.'&periodo2=1000000" target="_blank">'.formatNumberDec($monto_periodo[$j]).'</a></td>';
        $array_periodo_total[$j]=$monto_periodo[$j];
        $sumaTotalCliente+=$monto_periodo[$j];
        echo '<td class="text-right small font-weight-bold">'.formatNumberDec($sumaTotalCliente).'</td>';
        $array_periodo_total[$j+1]=$sumaTotalCliente;

        echo '</tr>';
        // echo "**1";
        //  var_dump($array_periodo_total);
    }

    
    
    return $array_periodo_total;
}

?>
