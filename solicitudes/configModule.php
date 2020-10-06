<?php
//AQUI SE DEFINEN LAS VARIABLES PARA EL ABM
$table="solicitud_recursos";
$moduleNameSingular="Solicitud de Recursos";
$moduleNamePlural="Solicitudes de Recursos";

//$moduleNameSingular="Comprobantes";
$urlList2Auxiliar="index.php?opcion=listSolicitudRecursosAdmin"; 
//al editar un estado $urlList2 Cambia constantemente segun el origen de la lista de solicitudes (Esta variable debe ser fija)

$urlList="index.php?opcion=listSolicitudRecursos";
$urlList2="index.php?opcion=listSolicitudRecursosAdmin";
$urlList3="index.php?opcion=listSolicitudRecursosAdminReg";
$urlList4="index.php?opcion=listSolicitudRecursosAdminConta";
$urlList5="index.php?opcion=listSolicitudRecursosAdminSis";
$urlList6="index.php?opcion=listSolicitudRecursosAdminContaHistorico";
$urlList7="index.php?opcion=listSolicitudRecursosAdminContaMenores";
$urlList8="index.php?opcion=listSolicitudRecursosAdminRegHistorico";

$urlImp="solicitudes/imp.php";
$urlImpComp="comprobantes/imp.php";
$urlRegister="solicitudes/registerSolicitud.php";
$urlRegister3="solicitudes/registerSolicitudDetalle.php";

$urlVer="solicitudes/verSolicitudRecursos.php";
$urlRegister2="solicitudes/register.php";
$urlEdit="index.php?opcion=editSolicitudRecursos";
$urlEdit2="solicitudes/edit.php";
$urlDelete="index.php?opcion=deleteSolicitudRecursos";

$urlSaveDelete="";
$urlSavePago="solicitudes/savePago.php";

$urlVerificarSolicitud="solicitudes/comprobarSolicitud.php";
$urlPagos="index.php?opcion=listSolicitudPagosProveedores";
$urlRegisterSS="index.php?opcion=registerPlanCuentaSS&codigo=0";
$urlListCC2="index.php?opcion=listPlanCuentasSolicitudesRecursos";

$urlDeleteRestart="index.php?opcion=deleteSolicitudRecursosRestart";
$urlCambiarPasivo="index.php?opcion=cambiarPasivoCuentaSol";
$urlSavePasivo="solicitudes/savePasivo.php";
$urlCambiarDivision="solicitudes/saveDivision.php";
$urlConta="solicitudes/contabilizar.php";
$urlEncargado="solicitudes/encargado.php";
$urlRegisterCompro="comprobantes/register.php";

?>