<?php //ESTADO FINALIZADO
require_once __DIR__.'/../functions.php';
require_once 'boletas_retroactivo_html.php';
//RECIBIMOS LAS VARIABLES
$cod_planilla = $_GET["codigo_planilla"];
$cod_gestion = $_GET["cod_gestion"];
$cod_mes = $_GET["cod_mes"];//

// $cod_personal=90;
$cod_personal=0;
$gestion=nameGestion($cod_gestion);
$mes=strtoupper(nombreMes($cod_mes));

// $htmlConta1=generarHtmlBoletaRetroactivo($cod_planilla,$cod_gestion,0);  
$htmlConta1=generarHtmlBoletaSueldosMes($cod_planilla,$cod_gestion,$cod_mes,$cod_personal);
// echo $htmlConta1;
descargarPDFBoleta("IBNORCA BOLETAS DE SUELDO ".$mes." ".$gestion,$htmlConta1);

//borramos los archivos temporales
$files = glob('../boletas/qr_temp/*.png'); //obtenemos todos los nombres de los ficheros
foreach($files as $file){
    if(is_file($file))
    unlink($file); //elimino el fichero
}
//echo $htmlConta1;
?>
