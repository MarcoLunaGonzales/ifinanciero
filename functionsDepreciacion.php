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
function obtenerContraCuentaDepreciacion($codigo)
{
    $dbh = new Conexion();
    $stmt = $dbh->prepare("SELECT cod_cuentacontable2 from depreciaciones where codigo=$codigo");     
    $stmt->execute();
    $valor=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $valor=$row['cod_cuentacontable2'];
    }
    return($valor);
}
function correrDepreciacion($codActivo,$fechaInicioDepreciacion,$fechaFinalDepreciacion,$valorInicial,$depreciacionAcum,$numeroMesesDepreciacion,$vidautil,$ultimoIdInsertado,$vidautilmeses_restante,$cod_depreciaciones,$fecha_actual,$sw_nuevo){
    $dbh = new Conexion();
    $numeroMesesDepreciacion++;
    $ufvInicio=obtenerUFV($fechaInicioDepreciacion);
    $ufvFinal=obtenerUFV($fechaFinalDepreciacion);
    //echo $fechaInicioDepreciacion."__".$fechaFinalDepreciacion."<br>";
    //echo "i".$ufvInicio."-".$ufvFinal."<br>";
    $valorUFVActualizacion=0;
    if($ufvInicio>0 && $ufvFinal>0){
        $valorUFVActualizacion=($ufvFinal/$ufvInicio);//cuando no està registrado la ufv
        //diferente proceso para terrenos
        $cod_depreciaciones_configuracion=17;//codigo por defecto de terrenos; 
        if($cod_depreciaciones==$cod_depreciaciones_configuracion){//rubro terreno tiene un proceso diferente        
            $valorResidual_2=$valorInicial;
             //echo $fechaInicioDepreciacion."$$".$fecha_actual;
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
            $valorNetoActivo_10=$valorActivoActualizado_4;
            $vida_util_restante=0;
        }else{

            $valorResidual_2=$valorInicial;
            $factorActualizacion_3=$valorUFVActualizacion;
            $valorActivoActualizado_4=$valorInicial*$valorUFVActualizacion;
            //echo "<br><br>".$valorUFVActualizacion."<br><br>";
            $valorIncrementoPorcentual_5=$valorActivoActualizado_4-$valorInicial;


            $depreciacionAcumulada_6=$depreciacionAcum;
            $incrementoDepreciacionAcumulada_7=$depreciacionAcumulada_6*($valorUFVActualizacion-1);
        }    
        if($sw_nuevo==1){//es nuevo
            $valorResidual_2=0;
        }
        //rubro terreno tiene diferente proceso
        //echo $vidautilmeses_restante."--".$numeroMesesDepreciacion;
        if($cod_depreciaciones!=$cod_depreciaciones_configuracion){
            if($vidautilmeses_restante>$numeroMesesDepreciacion){//vida util mayor a la cantidad de meses a depreciar                    
                if($vidautil>0){
                    $depreciacionPeriodo_8=($valorActivoActualizado_4/$vidautil)*$numeroMesesDepreciacion;
                }else{
                    $depreciacionPeriodo_8=0;
                }
                $vida_util_restante=$vidautilmeses_restante-$numeroMesesDepreciacion;
            }else{//vida restante menor a la cantidad de meses a depreciara
                $depreciacionPeriodo_8=$valorActivoActualizado_4-$depreciacionAcum-$incrementoDepreciacionAcumulada_7-1; //depreciar con los meses restantes
                $vida_util_restante=0;
                //$valorNetoActivo_10=1;
            }
            $depreciacionActualAcumulada_9=$depreciacionAcumulada_6+$incrementoDepreciacionAcumulada_7+$depreciacionPeriodo_8;
            
            $valorNetoActivo_10=$valorActivoActualizado_4-$depreciacionActualAcumulada_9; 
            if($vidautilmeses_restante<=0){
                $valorActivoActualizado_4=$valorInicial;
                $valorIncrementoPorcentual_5 = 0;
                $incrementoDepreciacionAcumulada_7=0;
                $depreciacionPeriodo_8=0;
                $depreciacionActualAcumulada_9=$depreciacionAcum;
                $valorNetoActivo_10=1;
            }
            if($vidautilmeses_restante<$numeroMesesDepreciacion+1 ){
                $vida_util_restante=0;
                //$valorNetoActivo_10=1;
            }
            // if($sw_nuevo==1795){//caso especial af a.codigo=1795 llegará en variable $sw_nuevo solo para iniciar el proceso de depreciacion.
            //     $valorInicial=59.16;
            //     $valorResidual_2=$valorInicial;
            //     $depreciacionAcumulada_6=58.16;
            //     $valorActivoActualizado_4=59.16;            
            //     $depreciacionActualAcumulada_9=58.16;
            // }
        }
        //echo "ValorAnt:".$valorResidual_2."depreAcum:".$depreciacionAcumulada_6."-incremeDepreAcumu:".$incrementoDepreciacionAcumulada_7."-DeprePeriodo".$depreciacionPeriodo_8."=DepreActualAcum:".$depreciacionActualAcumulada_9." NEto:".$valorNetoActivo_10."actualizacion:".$valorIncrementoPorcentual_5."<br>";
        $sqlInsertDet="INSERT INTO mesdepreciaciones_detalle (cod_mesdepreciaciones, cod_activosfijos, d2_valorresidual, d3_factoractualizacion, d4_valoractualizado, d5_incrementoporcentual, d6_depreciacionacumuladaanterior, d7_incrementodepreciacionacumulada, d8_depreciacionperiodo, d9_depreciacionacumuladaactual, d10_valornetobs, fecha_inicio, fecha_fin,d11_vidarestante) values ('$ultimoIdInsertado', '$codActivo', '$valorResidual_2', '$factorActualizacion_3', '$valorActivoActualizado_4', '$valorIncrementoPorcentual_5', '$depreciacionAcumulada_6', '$incrementoDepreciacionAcumulada_7', '$depreciacionPeriodo_8', '$depreciacionActualAcumulada_9','$valorNetoActivo_10', '$fechaInicioDepreciacion', '$fechaFinalDepreciacion','$vida_util_restante')";
        $stmtInsertDet = $dbh->prepare($sqlInsertDet);
        $stmtInsertDet -> execute();
        return(1);
    }else{
        return(2);
    }
    
    
}

function correrDepreciacion2($codActivo,$fechaInicioDepreciacion,$fechaFinalDepreciacion,$valorInicial,$depreciacionAcum,$numeroMesesDepreciacion,$vidautil,$ultimoIdInsertado,$vidautilmeses_restante,$cod_depreciaciones,$fecha_actual,$sw_nuevo){
    $dbh = new Conexion();
    $numeroMesesDepreciacion++;
    $ufvInicio=obtenerUFV($fechaInicioDepreciacion);
    $ufvFinal=obtenerUFV($fechaFinalDepreciacion);
    echo $fechaInicioDepreciacion."__".$fechaFinalDepreciacion."<br>";
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
        if($vidautilmeses_restante==0){
            $valorResidual_2=$valorInicial;
            $factorActualizacion_3=$valorUFVActualizacion;
            $valorActivoActualizado_4=$valorInicial;
            $valorIncrementoPorcentual_5 = 0;
            $depreciacionAcumulada_6=$depreciacionAcum;
            $incrementoDepreciacionAcumulada_7=0;
        }else{
            $valorResidual_2=$valorInicial;
            $factorActualizacion_3=$valorUFVActualizacion;
            $valorActivoActualizado_4=$valorInicial*$valorUFVActualizacion;
            $valorIncrementoPorcentual_5=$valorActivoActualizado_4-$valorInicial;
            $depreciacionAcumulada_6=$depreciacionAcum;
            $incrementoDepreciacionAcumulada_7=$depreciacionAcumulada_6*($valorUFVActualizacion-1);
        }
        
    }
    if($sw_nuevo==1){//es nuevo        
        $valorResidual_2=0;
    }
    //echo "__".$vidautilmeses_restante."__".$numeroMesesDepreciacion;

    if($vidautilmeses_restante>=$numeroMesesDepreciacion){//vida util mayor a la cantidad de meses a depreciar
        $numeroMesesDepreciacion_aux=$numeroMesesDepreciacion;
        $vida_util_restante=$vidautilmeses_restante-$numeroMesesDepreciacion;
    }else{//vida restante menor a la cantidad de meses a depreciara        
        $numeroMesesDepreciacion_aux=$vidautilmeses_restante;
        $vida_util_restante=0;        
    }
    if($vidautil>0){
        $depreciacionPeriodo_8=($valorActivoActualizado_4/$vidautil)*$numeroMesesDepreciacion_aux;
    }else{
        $depreciacionPeriodo_8=0;
    }
    //rubro terreno tiene diferente proceso
    if($cod_depreciaciones!=$cod_depreciaciones_configuracion){
        if($vidautilmeses_restante==0){
            $depreciacionActualAcumulada_9=$depreciacionAcum;
            $valorNetoActivo_10=1;
            $depreciacionPeriodo_8=0;            
        }else{
            $depreciacionActualAcumulada_9=$depreciacionAcumulada_6+$incrementoDepreciacionAcumulada_7+$depreciacionPeriodo_8;
            $valorNetoActivo_10=$valorActivoActualizado_4-$depreciacionActualAcumulada_9; 
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
    $stmt=$dbh->prepare("SELECT bandera_depreciar,tipoalta from activosfijos where codigo =$codigo");
    $stmt->execute();
    $valor=0;
    while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
        $valor_aux=$row['bandera_depreciar'];        
        $tipoalta=$row['tipoalta'];
        if($valor_aux=='NO'){
            // if($tipoalta=='NUEVO'){
                $valor=1;
            // }
            $stmtUpdate=$dbh->prepare("UPDATE activosfijos set bandera_depreciar='SI' where codigo=$codigo");
            $stmtUpdate->execute();
        }
    }
    return $valor;
}

function obtenerComprobanteDepreciacion($codigo){
     $dbh = new Conexion();
     $stmt = $dbh->prepare("SELECT cod_comprobante from comprobantes_depreciaciones where cod_depreciacion=$codigo");     
     $stmt->execute();
     $valor=0;
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $valor=$row['cod_comprobante'];
     }
     return($valor);
}


?>

