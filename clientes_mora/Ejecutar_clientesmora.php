<?php 
require_once '../conexion.php';
require_once '../functions.php';

$dbh = new Conexion();

$cod_cuenta=67;
$periodo=30;//dìas para entrar en mora
$cod_estado=1;
$fecha_actual=date('Y-m-d');

$sql="SELECT ec.codigo,ec.cod_comprobantedetalle, ec.cod_plancuenta,ec.monto,ec.fecha,ec.cod_cuentaaux,ec.glosa_auxiliar
    from comprobantes c join comprobantes_detalle cd on c.codigo=cd.cod_comprobante join estados_cuenta ec on cd.codigo=ec.cod_comprobantedetalle
    where c.cod_estadocomprobante<>2 and ec.cod_plancuenta=67 and ec.cod_comprobantedetalleorigen not in (select ec.codigo
    from comprobantes c join comprobantes_detalle cd on c.codigo=cd.cod_comprobante join estados_cuenta ec on cd.codigo=ec.cod_comprobantedetalle
    where c.cod_estadocomprobante<>2 and ec.cod_comprobantedetalleorigen=0 and ec.cod_plancuenta=$cod_cuenta)"; //ca.nombre, 
//echo $sql;
$stmtUO = $dbh->prepare($sql);
$stmtUO->execute();
$index=1;
while ($row = $stmtUO->fetch()) {
    $codigoEC=$row['codigo'];    
    $cod_comprobantedetalleEC=$row['cod_comprobantedetalle'];
    $cod_plancuentaEC=$row['cod_plancuenta'];
    $montoEC=$row['monto'];
    $fechaEC=$row['fecha'];
    $cod_cuentaauxEC=$row['cod_cuentaaux'];
    $glosa_auxiliarEC=$row['glosa_auxiliar'];
    $fecha_limite=date('Y-m-d',strtotime($fechaEC.'+'.$periodo.' day'));
    if($fecha_limite<$fecha_actual){
        $date1 = new DateTime($fechaEC);
        $date2 = new DateTime($fecha_actual);
        $diff = $date1->diff($date2);        
        $dias_mora=$diff->days;
        // echo  $dias_mora."*";
        $sqlInsert="INSERT INTO clientes_mora(fecha,cod_plancuenta,cod_cuentaauxiliar,dias_mora,monto_mora,cod_estado)
            values ('$fechaEC',$cod_plancuentaEC,'$cod_cuentaauxEC','$dias_mora','$montoEC','$cod_estado')";
        $stmt2 = $dbh->prepare($sqlInsert);
        $flagSuccess=$stmt2->execute();
    }
    $index++;
}                        
?>
