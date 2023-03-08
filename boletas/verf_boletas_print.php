<?php //ESTADO FINALIZADO
require_once __DIR__.'/../functions.php';
require_once 'verf_boletas_retroactivo.php';

/* Datos de Personal */
$key  = $_GET["key"];
$dbh  = new Conexion();
$sqlP = "SELECT ppm.codigo, p.cod_gestion, p.cod_mes
        FROM planillas_personal_mes ppm
        LEFT JOIN planillas p ON p.codigo = ppm.cod_planilla
        WHERE ppm.codigo = '$key' 
        GROUP BY ppm.codigo";
$stmtP = $dbh->prepare($sqlP);
$stmtP->execute();

//RECIBIMOS LAS VARIABLES
$cod_planilla   = "";
$cod_gestion    = "";
$cod_mes        = "";
$cod_planilla_mes = $key;
while ($rowP = $stmtP->fetch(PDO::FETCH_ASSOC)) {
    $cod_planilla = $rowP['codigo'];
    $cod_gestion  = $rowP['cod_gestion'];
    $cod_mes      = $rowP['cod_mes'];
}

// $cod_personal=90;
$cod_personal=0;
$gestion=nameGestion($cod_gestion);
$mes=strtoupper(nombreMes($cod_mes));

// $htmlConta1=generarHtmlBoletaRetroactivo($cod_planilla,$cod_gestion,0);  
$htmlConta1=generarHtmlBoletaSueldosMes($cod_planilla,$cod_gestion,$cod_mes,$cod_personal, $cod_planilla_mes);
// echo $htmlConta1;
descargarPDFBoleta("IBNORCA BOLETA DE SUELDO ".$mes." ".$gestion,$htmlConta1);

//borramos los archivos temporales
$files = glob('../boletas/qr_temp/*.png'); //obtenemos todos los nombres de los ficheros
foreach($files as $file){
    if(is_file($file))
    unlink($file); //elimino el fichero
}
//echo $htmlConta1;
?>
