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

function correrDepreciacion($codActivo,$fechaInicioDepreciacion,$fechaFinalDepreciacion,$valorInicial,$depreciacionAcum,$numeroMesesDepreciacion,$vidautil,$ultimoIdInsertado,$vidautilmeses_restante,$cod_depreciaciones){

    $dbh = new Conexion();
    $numeroMesesDepreciacion++;

    $ufvInicio=obtenerUFV($fechaInicioDepreciacion);
    $ufvFinal=obtenerUFV($fechaFinalDepreciacion);
    //echo $fechaInicioDepreciacion."__".$fechaFinalDepreciacion."<br>";
    if($ufvInicio==0 || $ufvFinal==0){
        $banderaUFVError=1;
    }
    $valorUFVActualizacion=0;
    if($ufvInicio>0){
        // $valorUFVActualizacion=($ufvFinal/$ufvInicio);
        $valorUFVActualizacion=(2.34086/2.33187);

        

    }

    //echo "valorActualizacion: ".$valorUFVActualizacion;
    // $valorResidual_2=$valorInicial;
    // $factorActualizacion_3=$valorUFVActualizacion;
    // $valorActivoActualizado_4=$valorInicial*$valorUFVActualizacion;
    // $valorIncrementoPorcentual_5=$valorActivoActualizado_4-$valorInicial;
    // $depreciacionAcumulada_6=$depreciacionAcum;
    // $incrementoDepreciacionAcumulada_7=$depreciacionAcumulada_6*($valorUFVActualizacion-1);

    //diferente proceso para terrenos
    $cod_depreciaciones_configuracion=17;//codigo por defecto de terrenos; 
    if($cod_depreciaciones==$cod_depreciaciones_configuracion){
        $valorResidual_2=$valorInicial;
        // $factorActualizacion_3=$valorUFVActualizacion;
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


    // $depreciacionActualAcumulada_9=$depreciacionAcumulada_6+$incrementoDepreciacionAcumulada_7+$depreciacionPeriodo_8;
    // $valorNetoActivo_10=$valorActivoActualizado_4-$depreciacionActualAcumulada_9;


    //echo $valorActivoActualizado_4." --  ".$valorIncrementoPorcentual_5." -- ".$depreciacionAcumulada_6." -- ".$incrementoDepreciacionAcumulada_7." -- ".$depreciacionPeriodo_8." -- ".$valorNetoActivo_9."<br>";

    if($vidautilmeses_restante>=$numeroMesesDepreciacion){
        if($vidautil>0){
            $depreciacionPeriodo_8=($valorActivoActualizado_4/$vidautil)*$numeroMesesDepreciacion;
        }else{
            $depreciacionPeriodo_8=0;
        }
        $vida_util_restante=$vidautilmeses_restante-$numeroMesesDepreciacion;
    }else{
        if($vidautil>0){
            $depreciacionPeriodo_8=($valorActivoActualizado_4/$vidautil)*$vidautilmeses_restante;
        }else{
            $depreciacionPeriodo_8=0;
        }
        // echo "vidautil".$vidautilmeses_restante."-".$depreciacionPeriodo_8;
        $vida_util_restante=0;
        $valorNetoActivo_10=1;
    }
    //si es terreno tiene diferente proceso
    if($cod_depreciaciones!=$cod_depreciaciones_configuracion){
        $depreciacionActualAcumulada_9=$depreciacionAcumulada_6+$incrementoDepreciacionAcumulada_7+$depreciacionPeriodo_8;
        $valorNetoActivo_10=$valorActivoActualizado_4-$depreciacionActualAcumulada_9; 
        if($vidautilmeses_restante==0){
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

?>

