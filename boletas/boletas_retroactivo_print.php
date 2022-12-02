<?php //ESTADO FINALIZADO
require_once __DIR__.'/../functions.php';
require_once 'boletas_retroactivo_html.php';
//RECIBIMOS LAS VARIABLES
$cod_planilla = $_GET["codigo_planilla"];
$cod_gestion = $_GET["cod_gestion"];
$cod_personal=0;
$gestion=nameGestion($cod_gestion);

$htmlConta1=generarHtmlBoletaRetroactivo($cod_planilla,$cod_gestion,0);  
descargarPDFBoleta("COBOFAR BOLETAS RETROACTIVOS ".$gestion,$htmlConta1);
//echo $htmlConta1;
?>
