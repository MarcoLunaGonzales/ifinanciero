<?php 


require_once '../conexion.php';
require_once '../functions.php';

$dbh = new Conexion();


$sqlGestion="SELECT cod_gestion FROM gestiones_datosadicionales where cod_estado=1";
$stmtGestion = $dbh->prepare($sqlGestion);
$stmtGestion->execute();
while ($rowGestion = $stmtGestion->fetch(PDO::FETCH_ASSOC)) {
    $codGestionActiva=$rowGestion['cod_gestion'];    
}
$nombreGestion=nameGestion($codGestionActiva);
//eliminacion logica
$sqlDelete="UPDATE clientes_mora set cod_estado=2 where cod_estado=1";
$stmtDelete = $dbh->prepare($sqlDelete);
$stmtDelete->execute();

$cod_cuenta=67;//cod cuenta cliente
$periodo=30;//dìas para entrar en mora
$cod_estado=1;
$fecha_actual=date('Y-m-d');

$sql="SELECT ec.codigo,ec.cod_comprobantedetalle, ec.cod_plancuenta,ec.monto,ec.fecha,ec.cod_cuentaaux,ec.glosa_auxiliar,cd.glosa
    from comprobantes c join comprobantes_detalle cd on c.codigo=cd.cod_comprobante join estados_cuenta ec on cd.codigo=ec.cod_comprobantedetalle
    where c.cod_estadocomprobante<>2 and ec.cod_plancuenta=$cod_cuenta and DATE_FORMAT(ec.fecha,'%Y')=$nombreGestion and ec.cod_comprobantedetalleorigen=0"; //ca.nombre, 
// echo $sql;
$stmtUO = $dbh->prepare($sql);
$stmtUO->execute();

while ($row = $stmtUO->fetch()) {
    $codigoEC=$row['codigo'];    
    $cod_comprobantedetalleEC=$row['cod_comprobantedetalle'];
    $cod_plancuentaEC=$row['cod_plancuenta'];
    $montoEC=$row['monto'];
    $fechaEC=$row['fecha'];
    $cod_cuentaauxEC=$row['cod_cuentaaux'];
    $glosa_auxiliarEC=$row['glosa_auxiliar'];
    $glosaCmp=$row['glosa'];


    //PAGADO
    $sql="SELECT sum(e.monto) as monto
    FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and cc.cod_estadocomprobante<>2 and e.cod_comprobantedetalleorigen=$codigoEC";    
    $stmt_d = $dbh->prepare($sql);
    $stmt_d->execute();
    $monto_ecD=0;
    while ($row_d = $stmt_d->fetch()) {
        $monto_ecD=$row_d['monto'];
    }
    $saldo_X=$montoEC-$monto_ecD;
    if($saldo_X>0){
        $fecha_limite=date('Y-m-d',strtotime($fechaEC.'+'.$periodo.' day'));
        if($fecha_limite<$fecha_actual){
            $date1 = new DateTime($fechaEC);
            $date2 = new DateTime($fecha_actual);
            $diff = $date1->diff($date2);        
            $dias_mora=$diff->days;
            // echo  $dias_mora."*";
            //veriicamos si estado cuenta se encuentra silenciado
            $sw=verificarEstadoClienteMora($codigoEC);
            if($sw==0){
                $sqlInsert="INSERT INTO clientes_mora(fecha,cod_plancuenta,cod_cuentaauxiliar,dias_mora,monto_mora,cod_estado,cod_estadocuenta,glosa)
                values ('$fechaEC',$cod_plancuentaEC,'$cod_cuentaauxEC','$dias_mora','$saldo_X','$cod_estado','$codigoEC','$glosaCmp')";
                $stmt2 = $dbh->prepare($sqlInsert);
                $flagSuccess=$stmt2->execute();
            }
        }
        
    }
}
echo "Fin Proceso...";
?>

