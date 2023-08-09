<?php
    require_once 'functions.php';
    session_start();
    date_default_timezone_set('America/La_Paz');
    $idCliente   = 123;
    $razonSocial = 'Test';
    $nit         = '12345678';
    $idTipoDocumento = '5';
    $idSolicitud     = 123;
    $response = wsClienteContacto($idCliente,$razonSocial,$nit,$idTipoDocumento,$idSolicitud);
    var_dump($response);