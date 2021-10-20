<?php

function generarHTMLFacCliente($cuentai,$NombreGestion,$sqlFechaEstadoCuenta,$StringUnidades,$cod_cuentaauxX,$unidadCostoArray,$areaCostoArray,$desde,$hasta,$monto_periodo,$array_periodo){
    require_once __DIR__.'/../conexion.php';            
    require_once __DIR__.'/../functionsGeneral.php';
    $dbh = new Conexion();

$saldo_X=0;
$sql="SELECT e.fecha,e.monto
    FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentai) and e.cod_comprobantedetalleorigen=0 and cc.cod_gestion= '$NombreGestion' $sqlFechaEstadoCuenta and cc.cod_unidadorganizacional in ($StringUnidades) and e.cod_cuentaaux in ($cod_cuentaauxX) and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) order by e.fecha"; //ca.nombre, 
 // echo $sql;
$stmtUO = $dbh->prepare($sql);
$stmtUO->execute();

while ($row = $stmtUO->fetch()) {
    $fechaDet=$row['fecha'];
    $monto_ecX=$row['monto'];
    //PAGADO
    $sql="SELECT e.monto
    FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentai) and e.cod_comprobantedetalleorigen>0 and cc.cod_gestion= '$NombreGestion' $sqlFechaEstadoCuenta and cc.cod_unidadorganizacional in ($StringUnidades) and e.cod_cuentaaux in ($cod_cuentaauxX) and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) GROUP BY e.cod_cuentaaux  order by e.fecha";
    $stmt_d = $dbh->prepare($sql);
    $stmt_d->execute();
    $monto_ecD=0;
    while ($row_d = $stmt_d->fetch()) {
        $monto_ecD=$row_d['monto'];
    }        
    $saldo_X=$monto_ecX-$monto_ecD;
    $periodo=0;
    $fechai=$desde;
    $i=0;
    foreach ($array_periodo as $periodo) {
        $fechaf=date('Y-m-d',strtotime($fechai.'+'.$periodo.' day'));
        if($fechai<=$fechaDet and $fechaDet<=$fechaf){
            $monto_periodo[$i]+=$monto_ecX;
        }
        $i++;
        $fechai=$fechaf;        
    }
    if($fechaDet>$fechaf){//si es mayor a 90 dias
        $monto_periodo[$i]+=$monto_ecX;
    }
}
$j=0;

$sumaTotalCliente=0;

foreach ($array_periodo as $periodo) {    
    echo '<td class="text-right small">'.formatNumberDec($monto_periodo[$j]).'</td>';
    $sumaTotalCliente+=$monto_periodo[$j];
    $j++;    
}    
echo '<td class="text-right small">'.formatNumberDec($monto_periodo[$j]).'</td>';
$sumaTotalCliente+=$monto_periodo[$j];

echo '<td class="text-right small font-weight-bold">'.formatNumberDec($sumaTotalCliente).'</td>';

}

?>