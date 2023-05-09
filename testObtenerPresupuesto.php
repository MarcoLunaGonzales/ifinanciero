<?php
    require_once 'conexion.php';
    require_once 'functions.php';
    date_default_timezone_set('America/La_Paz');

    // obtenerPresupuestoEjecucionPorAreaAcumulado($oficina,$area,$anio,$mes,$acumulado);
    // var_dump(obtenerPresupuestoEjecucionPorAreaAcumulado(5,38,2023,5,1));



    /***************************************************************/
    /*    TEST FUNCION - OBTENER PRESUPUESTO POR AREA ACUMULADA    */
    /***************************************************************/
    // Parametros de Entrada Inicial
    $area      = 38;
    $oficina   = 5;
    $anio      = 2023;
    $mes       = 4;
    $acumulado = 1;
    // Rutas y credenciales de acceso
    $direccion = "http://192.168.0.129:8090/imonitoreo/";
    $sIde      = "monitoreo"; 
    $sKey      = "101010"; 

    $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "oficina"=>$oficina, "area"=>$area, "anio"=>$anio, "mes"=>$mes, "accion"=>"listar","acumulado"=>$acumulado);
    $parametros=json_encode($parametros);
    // Preparación de Consumo de Servicio
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$direccion."ws/wsPresupuestoIngresosTotal.php");
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    curl_close ($ch);
    $datos=json_decode($remote_server_output);
    // Respuesta
    
    header('Content-type: application/json');
    // print_r($datos);   
    var_dump($datos);
    // print("Servicio");