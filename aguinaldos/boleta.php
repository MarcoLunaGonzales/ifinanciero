<?php 
require_once __DIR__.'/../functions.php';
require_once 'boleta_funciones.php';

/**
 * PLANILLA DE AGUINALDOS
 */

/* Datos de Personal */
$cod_planilla_personal  = $_GET["key"];
$dbh  = new Conexion();
$sqlP = "SELECT pad.cod_planilla, pad.codigo as cod_planilla_personal, pad.cod_personal, p.cod_gestion, 'AGUINALDO' as mes
        FROM planillas_aguinaldos_detalle pad
        LEFT JOIN planillas_aguinaldos p ON p.codigo = pad.cod_planilla
        WHERE pad.codigo = '$cod_planilla_personal'
        LIMIT 1";
        // echo $sqlP;
$stmtP = $dbh->prepare($sqlP);
$stmtP->execute();
$rowP = $stmtP->fetch(PDO::FETCH_ASSOC);

// Asignar los valores a las variables
$cod_planilla          = $rowP['cod_planilla'] ?? "";
$cod_planilla_personal = $rowP['cod_planilla_personal'] ?? "";
$cod_gestion           = $rowP['cod_gestion'] ?? "";
$mes                   = $rowP['mes'] ?? "";
$cod_personal          = $rowP['cod_personal'] ?? "";
$gestion               = nameGestion($cod_gestion);

$htmlConta1            = generarHtmlBoletaSueldosMes($cod_planilla_personal, $cod_planilla, $cod_gestion,$mes,$cod_personal);

descargarPDFBoleta("IBNORCA BOLETA DE SUELDO ".$mes." ".$gestion,$htmlConta1);

//borramos los archivos temporales
$files = glob('../aguinaldos/qr_temp/*.png'); //obtenemos todos los nombres de los ficheros
foreach($files as $file){
    if(is_file($file))
    unlink($file); //elimino el fichero
}
//echo $htmlConta1;
?>
