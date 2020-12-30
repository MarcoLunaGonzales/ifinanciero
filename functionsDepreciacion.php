<?php
require_once 'conexion.php';
require_once 'conexion_externa.php';
require_once 'functions.php';

date_default_timezone_set('America/La_Paz');

function diferenciaMeses($fecha1, $fecha2){

$inicio="$fecha1 00:00:00";
$fin="$fecha2 00:00:00";
 
$datetime1=new DateTime($inicio);
$datetime2=new DateTime($fin);
 
$interval=$datetime2->diff($datetime1);
 
$intervalMeses=$interval->format("%m");

return($intervalMeses);
}

function correrDepreciacion($codActivo,$fechaInicioDepreciacion,$fechaFinalDepreciacion,$valorInicial,$depreciacionAcum,$numeroMesesDepreciacion,$vidautil,$ultimoIdInsertado,$vidautilmeses_restante,$cod_depreciaciones,$fecha_actual,$sw_nuevo){
    $dbh = new Conexion();
    $numeroMesesDepreciacion++;
    $ufvInicio=obtenerUFV($fechaInicioDepreciacion);
    $ufvFinal=obtenerUFV($fechaFinalDepreciacion);
    //echo $fechaInicioDepreciacion."__".$fechaFinalDepreciacion."<br>";
    $valorUFVActualizacion=0;
    if($ufvInicio>0){
        $valorUFVActualizacion=($ufvFinal/$ufvInicio);//cuando no està registrado la ufv
    }
    //diferente proceso para terrenos
    $cod_depreciaciones_configuracion=17;//codigo por defecto de terrenos; 
    if($cod_depreciaciones==$cod_depreciaciones_configuracion){//rubro terreno tiene un proceso diferente
        $valorResidual_2=$valorInicial;
        // echo $fechaInicioDepreciacion."$$".$fecha_actual;
        $ufvInicio=obtenerUFV($fechaInicioDepreciacion);
        $ufvFinal=obtenerUFV($fecha_actual);
        $valorUFVActualizacion=($ufvFinal/$ufvInicio);

        $factorActualizacion_3=$valorInicial*($valorUFVActualizacion-1);
        $valorActivoActualizado_4=$factorActualizacion_3+$valorInicial;
        $valorIncrementoPorcentual_5=$factorActualizacion_3;
        $depreciacionAcumulada_6=0;
        $incrementoDepreciacionAcumulada_7=0;
        $depreciacionPeriodo_8=0;
        $depreciacionActualAcumulada_9=0;        
    }else{
        $valorResidual_2=$valorInicial;
        $factorActualizacion_3=$valorUFVActualizacion;
        $valorActivoActualizado_4=$valorInicial*$valorUFVActualizacion;
        $valorIncrementoPorcentual_5=$valorActivoActualizado_4-$valorInicial;
        $depreciacionAcumulada_6=$depreciacionAcum;
        $incrementoDepreciacionAcumulada_7=$depreciacionAcumulada_6*($valorUFVActualizacion-1);
    }
    if($sw_nuevo==1){//es nuevo
        $d2_valorresidual=0;
    }

    if($vidautilmeses_restante>=$numeroMesesDepreciacion){//vida util mayor a la cantidad de meses a depreciar
        if($vidautil>0){
            $depreciacionPeriodo_8=($valorActivoActualizado_4/$vidautil)*$numeroMesesDepreciacion;
        }else{
            $depreciacionPeriodo_8=0;
        }
        $vida_util_restante=$vidautilmeses_restante-$numeroMesesDepreciacion;
    }else{//vida restante menor a la cantidad de meses a depreciara
        if($vidautil>0){
            $depreciacionPeriodo_8=($valorActivoActualizado_4/$vidautil)*$vidautilmeses_restante;//depreciar con los meses restantes
        }else{
            $depreciacionPeriodo_8=0;
        }
        $vida_util_restante=0;
        $valorNetoActivo_10=1;
    }
    //rubro terreno tiene diferente proceso
    if($cod_depreciaciones!=$cod_depreciaciones_configuracion){
        $depreciacionActualAcumulada_9=$depreciacionAcumulada_6+$incrementoDepreciacionAcumulada_7+$depreciacionPeriodo_8;
        $valorNetoActivo_10=$valorActivoActualizado_4-$depreciacionActualAcumulada_9; 
        if($vidautilmeses_restante<=0){
            $valorNetoActivo_10=1;
            $depreciacionActualAcumulada_9=$depreciacionAcum;
            $valorActivoActualizado_4=$valorInicial;
            $depreciacionPeriodo_8=0;
            $incrementoDepreciacionAcumulada_7=0;
            $valorIncrementoPorcentual_5 = 0;
        }
    }else{
        $valorNetoActivo_10=$valorActivoActualizado_4;
    }
    $sqlInsertDet="INSERT INTO mesdepreciaciones_detalle (cod_mesdepreciaciones, cod_activosfijos, d2_valorresidual, d3_factoractualizacion, d4_valoractualizado, d5_incrementoporcentual, d6_depreciacionacumuladaanterior, d7_incrementodepreciacionacumulada, d8_depreciacionperiodo, d9_depreciacionacumuladaactual, d10_valornetobs, fecha_inicio, fecha_fin,d11_vidarestante) values ('$ultimoIdInsertado', '$codActivo', '$valorResidual_2', '$factorActualizacion_3', '$valorActivoActualizado_4', '$valorIncrementoPorcentual_5', '$depreciacionAcumulada_6', '$incrementoDepreciacionAcumulada_7', '$depreciacionPeriodo_8', '$depreciacionActualAcumulada_9','$valorNetoActivo_10', '$fechaInicioDepreciacion', '$fechaFinalDepreciacion','$vida_util_restante')";
    $stmtInsertDet = $dbh->prepare($sqlInsertDet);
    $stmtInsertDet -> execute();    
    return(1);
}



function verificarContabilizacion($codigo){
     $dbh = new Conexion();
     $stmt = $dbh->prepare("SELECT count(*) as cont from comprobantes_depreciaciones where cod_depreciacion=$codigo");     
     $stmt->execute();
     $valor=0;
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $valor=$row['cont'];
     }
     return($valor);
}
function obtener_cantidad_meses_depreciacion($codigo)
{
    $dbh = new Conexion();
    $stmt = $dbh->prepare("SELECT cantidad_meses_depreciacion from depreciaciones where codigo=$codigo");     
    $stmt->execute();
    $valor=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $valor=$row['cantidad_meses_depreciacion'];
    }
    return($valor);
}
function verificar_si_nuevo($codigo){
    $dbh= new Conexion();
    $stmt=$dbh->prepare("SELECT bandera_depreciar from activosfijos where codigo =$codigo");
    $stmt->execute();
    $valor=0;
    while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
        $valor_aux=$row['bandera_depreciar'];
        if($valor_aux=='NO'){
            $valor=1;
            $stmtUpdate=$dbh->prepare("UPDATE activosfijos set bandera_depreciar='SI' where codigo=$codigo");
            $stmtUpdate->execute();
        }
    }
    return $valor;

}



?>

