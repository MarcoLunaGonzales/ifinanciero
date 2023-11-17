<?php

// require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

$globalUser= 90;
$codigoSR = 11541;


$detalle_extra = obtieneDetalleSRCorreo($codigoSR);
// exit;
$datosSolicitud=obtenerDatosSolicitudRecursos($codigoSR);
$correoPersonal='roalmirandadark@gmail.com';
$descripcionEstado=obtenerNombreEstadoSol(5);
if($correoPersonal!=""){
    $envioCorreoPersonal=enviarCorreoSimple($correoPersonal,'CAMBIO DE ESTADO - SOLICITUD DE RECURSOS, Nº : '.$datosSolicitud['numero'],'Estimado(a) '.$datosSolicitud['solicitante'].', el sistema IFINANCIERO le notifica que su Solicitud de Recursos cambio del estado <b>'.$datosSolicitud['estado'].'</b> a <b>'.$descripcionEstado.'</b>. <br> Personal que realizo el cambio:'.namePersonalCompleto($globalUser)."<br>Numero de Solicitud:".$datosSolicitud['numero']."<br>Estado Anterior: <b>".$datosSolicitud['estado']."</b><br>Estado Actual: <b>".$descripcionEstado."</b>$detalle_extra<br><br>Saludos - IFINANCIERO");  
}

echo "holiwis";