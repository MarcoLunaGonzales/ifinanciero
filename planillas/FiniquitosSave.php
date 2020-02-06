<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'rrhh/configModule.php';
ini_set('display_errors',1);

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {
    $codigo = $_POST["codigo"];
    $cod_personal = $_POST["cod_personal"];
    $anios_trabajados_pagados = $_POST["anios_trabajados_pagados"];
    //$fecha_retiro = $_POST["fecha_retiro"];
    

    // echo $cod_personal."-".$codigo."-".$anios_trabajados_pagados;

    $cod_estadoreferencial =   1;    
    $created_by = 1;//$_POST["created_by"];
    $modified_by = 1;//$_POST["modified_by"];    
    //tipo_retiro
    $stmttipoRetiro = $dbh->prepare("SELECT cod_tiporetiro,fecha_retiro from personal_retiros where cod_personal=$cod_personal");
    $stmttipoRetiro->execute();
    $resultRetiro =  $stmttipoRetiro->fetch();
    $motivo_retiro = $resultRetiro['cod_tiporetiro'];
    $fecha_retiro=$resultRetiro['fecha_retiro'];

    $anio_retiro = date("Y", strtotime($fecha_retiro));
    $mes_retiro = date("m", strtotime($fecha_retiro));
    $dia_retiro = date("d", strtotime($fecha_retiro));
    
    $stmtGestion = $dbh->prepare("SELECT codigo from gestiones where nombre=$anio_retiro");
    $stmtGestion->execute();
    $resultGestion =  $stmtGestion->fetch();
    $cod_gestion = $resultGestion['codigo'];
    //sacar datos del personal
    $stmtPersonal = $dbh->prepare("SELECT ing_contr from personal where codigo=$cod_personal");
    $stmtPersonal->execute();
    $resultPersonal =  $stmtPersonal->fetch();
    $ing_contr_x = $resultPersonal['ing_contr'];

    $anio_ingreso = date("Y", strtotime($ing_contr_x));
    $mes_ingreso = date("m", strtotime($ing_contr_x));
    $dia_ingreso = date("d", strtotime($ing_contr_x));
    //$anios_trabajados_pagados=5;//cambiar

    $anios_aux=$anio_ingreso+$anios_trabajados_pagados;
    $ing_contr = $anios_aux.'/'.$mes_ingreso.'/'.$dia_ingreso;
    // echo $ing_contr."-".$ing_contr_x;
    $anio_ingreso2 = date("Y", strtotime($ing_contr));
    $mes_ingreso2 = date("m", strtotime($ing_contr));
    $dia_ingreso2 = date("d", strtotime($ing_contr));
    //aun no hay datos de planillas
    // $cod_planilla_3_atras=obtener_id_planilla($cod_gestion,($mes_retiro-3));
    // $cod_planilla_2_atras=obtener_id_planilla($cod_gestion,($mes_retiro-2));
    // $cod_planilla_1_atras=obtener_id_planilla($cod_gestion,($mes_retiro-1));
    // $sueldo_3_atras=obtenerSueldomes($codigo_personal,$cod_planilla_3_atras);
    // $sueldo_2_atras=obtenerSueldomes($codigo_personal,$cod_planilla_2_atras);    
    // $sueldo_1_atras=obtenerSueldomes($codigo_personal,$cod_planilla_1_atras);
    $sueldo_3_atras=5843.18;//cambiar
    $sueldo_2_atras=5843.18;//cambiar
    $sueldo_1_atras=5843.18;//cambiar

    $sueldo_promedio=($sueldo_3_atras+$sueldo_2_atras+$sueldo_1_atras)/3;
    //desahucio 3 meses
    $desahucio_3_meses=0;//buscar valor
    //indemnizacion
    $indemnizacion_anios_diferencia=$anio_retiro-$anio_ingreso2;
    if($mes_retiro>$mes_ingreso2) 
        $indemnizacion_meses_diferencia=$mes_retiro-$mes_ingreso2;
    else $indemnizacion_meses_diferencia=$mes_ingreso2-$mes_retiro;
    if($dia_retiro>$dia_ingreso2) 
        $indemnizacion_dias_diferencia=$dia_retiro-$dia_ingreso2;
    else $indemnizacion_dias_diferencia=$dia_ingreso2-$dia_retiro;
    $indemnizacion_anios_monto=$indemnizacion_anios_diferencia*$sueldo_promedio;
    $indemnizacion_meses_monto=$sueldo_promedio/12*$indemnizacion_meses_diferencia;
    $indemnizacion_dias_monto=($sueldo_promedio/12/30)*$indemnizacion_dias_diferencia;//preguntar
    $suma_indemnizacion=$indemnizacion_anios_monto+$indemnizacion_meses_monto+$indemnizacion_dias_monto;
    //aguinaldo
    
    $aguinaldo_meses=$mes_retiro-1;
    $aguinaldo_dias=$dia_retiro;
    $aguinaldo_anios_monto=0;//preguntar
    $aguinaldo_meses_monto=$sueldo_promedio/12*$aguinaldo_meses;
    $aguinaldo_dias_monto=($sueldo_promedio/12/30)*$aguinaldo_dias;
    $suma_aguinaldo=$aguinaldo_meses_monto+$aguinaldo_dias_monto;
    //vacaciones
    $vacaciones_dias=20;//ver datos
    $vacaciones_doudecimas=6.89;//ver datos
    $vacaciones_dias_monto=$sueldo_promedio/30*$vacaciones_dias;
    $vacaciones_duodecimas_monto=$sueldo_promedio/30*$vacaciones_doudecimas;
    $suma_vacaciones=$vacaciones_dias_monto+$vacaciones_duodecimas_monto;
    //desahucio
    $desahucio=0;
    $desahucio_monto=$sueldo_promedio*$desahucio;
    //otros
    $servicios_adicionales=0;
    $subsidios_meses=0;
    $finiquitos_a_cuenta=0;
    $suma_otros=$servicios_adicionales+$subsidios_meses+$finiquitos_a_cuenta;
    //deducciones
    $porcentaje_deducciones_por_vacaciones=obtenerValorConfiguracion(14);
    $deducciones_total=-($suma_vacaciones*$porcentaje_deducciones_por_vacaciones/100);
    //total
    $total_a_pagar=$desahucio_3_meses+$suma_indemnizacion+$suma_aguinaldo+$suma_vacaciones+$desahucio_monto+$suma_otros+$deducciones_total;
    $observaciones="OBS. Fecha de ingreso ".$ing_contr_x.", se pagó indemnización por los primeros ".$anios_trabajados_pagados." años trabajados.";
    // echo "vacaciones_duodecimas_monto:".$vacaciones_duodecimas_monto."<br>";
    // echo "suma_aguinaldo:".$suma_aguinaldo."<br>";
    // echo "suma_vacaciones:".$suma_vacaciones."<br>";
    // echo "deducciones_total:".$deducciones_total."<br>";
    // echo "total_a_pagar:".$total_a_pagar."<br>";

    
    if ($_POST["codigo"] == 0){
        $stmt = $dbh->prepare("INSERT INTO finiquitos(cod_personal,fecha_ingreso,fecha_retiro,cod_tiporetiro,sueldo_promedio,sueldo_3_atras,sueldo_2_atras,sueldo_1_atras,indemnizacion_anios_monto,indemnizacion_meses_monto,indemnizacion_dias_monto,aguinaldo_anios_monto,aguinaldo_meses_monto,aguinaldo_dias_monto,vacaciones_dias_monto,vacaciones_duodecimas_monto,desahucio_monto,servicios_adicionales,subsidios_meses,finiquitos_a_cuenta,deducciones_total,total_a_pagar,observaciones,cod_estadoreferencial,created_by,modified_by) 
        values (:cod_personal,:ing_contr,:fecha_retiro,:motivo_retiro,:sueldo_promedio,:sueldo_3_atras,:sueldo_2_atras,:sueldo_1_atras,:indemnizacion_anios_monto,:indemnizacion_meses_monto,:indemnizacion_dias_monto,:aguinaldo_anios_monto,:aguinaldo_meses_monto,:aguinaldo_dias_monto,:vacaciones_dias_monto,:vacaciones_duodecimas_monto,:desahucio_monto,:servicios_adicionales,:subsidios_meses,:finiquitos_a_cuenta,:deducciones_total,:total_a_pagar,:observaciones,:cod_estadoreferencial,:created_by,:modified_by)");
        //Bind
        $stmt->bindParam(':cod_personal', $cod_personal);
        $stmt->bindParam(':ing_contr',$ing_contr);
        $stmt->bindParam(':fecha_retiro',$fecha_retiro);
        $stmt->bindParam(':motivo_retiro',$motivo_retiro);
        $stmt->bindParam(':sueldo_promedio',$sueldo_promedio);
        $stmt->bindParam(':sueldo_3_atras',$sueldo_3_atras);
        $stmt->bindParam(':sueldo_2_atras',$sueldo_2_atras);
        $stmt->bindParam(':sueldo_1_atras',$sueldo_1_atras);
        $stmt->bindParam(':indemnizacion_anios_monto',$indemnizacion_anios_monto);
        $stmt->bindParam(':indemnizacion_meses_monto',$indemnizacion_meses_monto);
        $stmt->bindParam(':indemnizacion_dias_monto',$indemnizacion_dias_monto);
        $stmt->bindParam(':aguinaldo_anios_monto',$aguinaldo_anios_monto);
        $stmt->bindParam(':aguinaldo_meses_monto',$aguinaldo_meses_monto);
        $stmt->bindParam(':aguinaldo_dias_monto',$aguinaldo_dias_monto);
        $stmt->bindParam(':vacaciones_dias_monto',$vacaciones_dias_monto);
        $stmt->bindParam(':vacaciones_duodecimas_monto',$vacaciones_duodecimas_monto);
        $stmt->bindParam(':desahucio_monto',$desahucio_monto);
        $stmt->bindParam(':servicios_adicionales',$servicios_adicionales);
        $stmt->bindParam(':subsidios_meses',$subsidios_meses);
        $stmt->bindParam(':finiquitos_a_cuenta',$finiquitos_a_cuenta);
        $stmt->bindParam(':deducciones_total',$deducciones_total);
        $stmt->bindParam(':total_a_pagar',$total_a_pagar);
        $stmt->bindParam(':observaciones',$observaciones);
        $stmt->bindParam(':cod_estadoreferencial',$cod_estadoreferencial);
        $stmt->bindParam(':created_by',$created_by);
        $stmt->bindParam(':modified_by',$modified_by);
        $flagSuccess=$stmt->execute();
        $tabla_id = $dbh->lastInsertId();
        showAlertSuccessError($flagSuccess,$urlFiniquitosList);

        //$stmt->debugDumpParams();
    } else {//update

        $stmt = $dbh->prepare("UPDATE finiquitos set cod_personal=$cod_personal,fecha_ingreso='$ing_contr',fecha_retiro='$fecha_retiro',motivo_retiro='$motivo_retiro',sueldo_promedio=$sueldo_promedio,sueldo_3_atras=$sueldo_3_atras,sueldo_2_atras=$sueldo_2_atras,sueldo_1_atras=$sueldo_1_atras,indemnizacion_anios_monto=$indemnizacion_anios_monto,indemnizacion_meses_monto=$indemnizacion_meses_monto,indemnizacion_dias_monto=$indemnizacion_dias_monto,aguinaldo_anios_monto=$aguinaldo_anios_monto,aguinaldo_meses_monto=$aguinaldo_meses_monto,aguinaldo_dias_monto=$aguinaldo_dias_monto,vacaciones_dias_monto=$vacaciones_dias_monto,vacaciones_duodecimas_monto=$vacaciones_duodecimas_monto,desahucio_monto=$desahucio_monto,servicios_adicionales=$servicios_adicionales,subsidios_meses=$subsidios_meses,finiquitos_a_cuenta=$finiquitos_a_cuenta,deducciones_total=$deducciones_total,total_a_pagar=$total_a_pagar,observaciones='$observaciones',modified_by=$modified_by
         where codigo = $codigo");
        $flagSuccess=$stmt->execute();
        
        showAlertSuccessError($flagSuccess,$urlFiniquitosList);

    }//si es insert o update
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>