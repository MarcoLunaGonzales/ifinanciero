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
    $codigo_contrato = $_POST["codigo_contrato"];
    $cod_tiporetiro = $_POST["cod_tiporetiro"];

    $vacaciones_pagar = $_POST["vacaciones_pagar"];
    $duodecimas = $_POST["duodecimas"];
    $otros_pagar = $_POST["otros"];
    
    
    //$fecha_retiro = $_POST["fecha_retiro"];
    

    // echo $cod_personal."-".$codigo."-".$anios_trabajados_pagados;

    $cod_estadoreferencial =   1;    
    $created_by = 1;//$_POST["created_by"];
    $modified_by = 1;//$_POST["modified_by"];    
    //tipo_retiro
    $stmttipoRetiro = $dbh->prepare("SELECT fecha_iniciocontrato,fecha_fincontrato From personal_contratos where codigo=$codigo_contrato");
    $stmttipoRetiro->execute();
    $resultRetiro =  $stmttipoRetiro->fetch();
    $motivo_retiro = $cod_tiporetiro;
    $fecha_retiro=$resultRetiro['fecha_fincontrato'];
    $ing_contr_x = $resultRetiro['fecha_iniciocontrato'];

    $anio_retiro = date("Y", strtotime($fecha_retiro));
    $mes_retiro = date("m", strtotime($fecha_retiro));
    $dia_retiro = date("d", strtotime($fecha_retiro));
    
    $anio_ingreso = date("Y", strtotime($ing_contr_x));
    $mes_ingreso = date("m", strtotime($ing_contr_x));
    $dia_ingreso = date("d", strtotime($ing_contr_x));

    $stmtGestion = $dbh->prepare("SELECT codigo from gestiones where nombre=$anio_retiro");
    $stmtGestion->execute();
    $resultGestion =  $stmtGestion->fetch();
    $cod_gestion = $resultGestion['codigo'];
 

    $anios_aux=$anio_ingreso+$anios_trabajados_pagados;
    $ing_contr = $anios_aux.'/'.$mes_ingreso.'/'.$dia_ingreso;
    // echo $ing_contr."-".$ing_contr_x;
    $anio_ingreso2 = $anios_aux;
    $mes_ingreso2 = $mes_ingreso;
    $dia_ingreso2 = $dia_ingreso;
    //aun no hay datos de planillas
    $cod_planilla_3_atras=obtener_id_planilla($cod_gestion,($mes_retiro-3));
    $cod_planilla_2_atras=obtener_id_planilla($cod_gestion,($mes_retiro-2));
    $cod_planilla_1_atras=obtener_id_planilla($cod_gestion,($mes_retiro-1));
    if($cod_planilla_3_atras==0 || $cod_planilla_3_atras=='')
        $sueldo_3_atras=0;
    else
        $sueldo_3_atras=obtenerSueldomes($cod_personal,$cod_planilla_3_atras);   
    if($cod_planilla_2_atras==0 || $cod_planilla_2_atras=='')
        $sueldo_2_atras=0;
    else
        $sueldo_2_atras=obtenerSueldomes($cod_personal,$cod_planilla_2_atras);    
    if($cod_planilla_1_atras==0 || $cod_planilla_1_atras=='')
        $sueldo_1_atras=0;
    else
        $sueldo_1_atras=obtenerSueldomes($cod_personal,$cod_planilla_1_atras);


    // $sueldo_3_atras=5843.18;//cambiar
    // $sueldo_2_atras=5843.18;//cambiar
    // $sueldo_1_atras=5843.18;//cambiar

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
    $aguinaldo_anios_monto=0;//Buscar datos

    $aguinaldo_meses_monto=$sueldo_promedio/12*$aguinaldo_meses;
    $aguinaldo_dias_monto=($sueldo_promedio/12/30)*$aguinaldo_dias;
    $suma_aguinaldo=$aguinaldo_meses_monto+$aguinaldo_dias_monto;
    //vacaciones
    $vacaciones_dias=$vacaciones_pagar;
    $vacaciones_doudecimas=$duodecimas;

    $vacaciones_dias_monto=$sueldo_promedio/30*$vacaciones_dias;
    $vacaciones_duodecimas_monto=$sueldo_promedio/30*$vacaciones_doudecimas;
    $suma_vacaciones=$vacaciones_dias_monto+$vacaciones_duodecimas_monto;
    //desahucio
    $desahucio=0;//buscar datos
    $desahucio_monto=$sueldo_promedio*$desahucio;
    //otros
    $servicios_adicionales=0;//buscar datos
    $subsidios_meses=0;//buscar datos
    $finiquitos_a_cuenta=0;//buscar datos

    // $suma_otros=$servicios_adicionales+$subsidios_meses+$finiquitos_a_cuenta;
    $suma_otros=$otros_pagar;
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
        $stmt = $dbh->prepare("INSERT INTO finiquitos(cod_personal,fecha_ingreso,fecha_retiro,cod_tiporetiro,sueldo_promedio,sueldo_3_atras,sueldo_2_atras,sueldo_1_atras,indemnizacion_anios_monto,indemnizacion_meses_monto,indemnizacion_dias_monto,aguinaldo_anios_monto,aguinaldo_meses_monto,aguinaldo_dias_monto,vacaciones_dias_monto,vacaciones_duodecimas_monto,desahucio_monto,servicios_adicionales,subsidios_meses,finiquitos_a_cuenta,deducciones_total,total_a_pagar,observaciones,cod_estadoreferencial,created_by,modified_by,cod_contrato,anios_pagados,dias_vacaciones_pagar,duodecimas,otros_pagar) 
        values (:cod_personal,:ing_contr,:fecha_retiro,:motivo_retiro,:sueldo_promedio,:sueldo_3_atras,:sueldo_2_atras,:sueldo_1_atras,:indemnizacion_anios_monto,:indemnizacion_meses_monto,:indemnizacion_dias_monto,:aguinaldo_anios_monto,:aguinaldo_meses_monto,:aguinaldo_dias_monto,:vacaciones_dias_monto,:vacaciones_duodecimas_monto,:desahucio_monto,:servicios_adicionales,:subsidios_meses,:finiquitos_a_cuenta,:deducciones_total,:total_a_pagar,:observaciones,:cod_estadoreferencial,:created_by,:modified_by,:codigo_contrato, :anios_pagados, :dias_vacaciones_pagar, :duodecimas,:otros_pagar)");
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
        $stmt->bindParam(':codigo_contrato',$codigo_contrato);

        $stmt->bindParam(':anios_pagados',$anios_trabajados_pagados);
        $stmt->bindParam(':dias_vacaciones_pagar',$vacaciones_pagar);
        $stmt->bindParam(':duodecimas',$duodecimas);
        $stmt->bindParam(':otros_pagar',$otros_pagar);

        $flagSuccess=$stmt->execute();
        //sacamos el codigo de finiquito;
        $stmtCodFiniquito = $dbh->prepare("SELECT codigo from finiquitos where cod_contrato=$codigo_contrato");
        $stmtCodFiniquito->execute();
        $resultFiniquitoCod =  $stmtCodFiniquito->fetch();
        $cod_finiquito_x = $resultFiniquitoCod['codigo'];
        $stmtUpdateContrato = $dbh->prepare("UPDATE personal_contratos set cod_finiquito=$cod_finiquito_x where codigo=$codigo_contrato");
        $stmtUpdateContrato->execute();
        
        showAlertSuccessError($flagSuccess,$urlFiniquitosList);

        //$stmt->debugDumpParams();
    } else {//update
        $stmt = $dbh->prepare("UPDATE finiquitos set cod_personal=$cod_personal,fecha_ingreso='$ing_contr',fecha_retiro='$fecha_retiro',cod_tiporetiro='$motivo_retiro',sueldo_promedio='$sueldo_promedio',sueldo_3_atras='$sueldo_3_atras',sueldo_2_atras='$sueldo_2_atras',sueldo_1_atras='$sueldo_1_atras',indemnizacion_anios_monto='$indemnizacion_anios_monto',indemnizacion_meses_monto='$indemnizacion_meses_monto',indemnizacion_dias_monto='$indemnizacion_dias_monto',aguinaldo_anios_monto='$aguinaldo_anios_monto',aguinaldo_meses_monto='$aguinaldo_meses_monto',aguinaldo_dias_monto='$aguinaldo_dias_monto',vacaciones_dias_monto='$vacaciones_dias_monto',vacaciones_duodecimas_monto='$vacaciones_duodecimas_monto',desahucio_monto='$desahucio_monto',servicios_adicionales='$servicios_adicionales',subsidios_meses='$subsidios_meses',finiquitos_a_cuenta='$finiquitos_a_cuenta',deducciones_total='$deducciones_total',total_a_pagar='$total_a_pagar',observaciones='$observaciones',modified_by=$modified_by,anios_pagados='$anios_trabajados_pagados',dias_vacaciones_pagar='$vacaciones_pagar',duodecimas='$duodecimas',otros_pagar='$otros_pagar'
         where codigo = $codigo");
        $flagSuccess=$stmt->execute();
        
        showAlertSuccessError($flagSuccess,$urlFiniquitosList);

    }//si es insert o update
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>