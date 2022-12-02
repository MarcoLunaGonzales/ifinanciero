<meta charset="utf-8" />

<?php


require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo_string = $_GET["cod_planilla"];//
// $cod_personal = $_GET["cod_personal"];//

// echo $codigo_string;

$codigo_array=explode(',', $codigo_string);
$cod_planilla=$codigo_array[0];
$cod_gestion = $codigo_array[1];
set_time_limit(0);
// $mes=strtoupper(nombreMes($cod_mes));
$gestion=nameGestion($cod_gestion);

  $sql="SELECT p.codigo, p.primer_nombre as nombres,CONCAT(p.paterno,' ', p.materno) as apellidos,(select c.nombre from cargos c where c.codigo=p.cod_cargo) as cargo,p.identificacion,p.ing_contr,pad.total_aguinaldo,pad.dias_360,pad.promedio_ganado
		from planillas_aguinaldos_detalle pad join personal p on pad.cod_personal=p.codigo
		join areas a on p.cod_area=a.codigo
		where pad.cod_planilla=$cod_planilla
		order by p.cod_unidadorganizacional,a.nombre,p.paterno";
    //echo $sql;
    $stmt = $dbh->prepare($sql);
    //Ejecutamos
    $stmt->execute();
    // $result = $stmt->fetch();
    

$html = '';
$html.='<html>'.
         '<head>'.
             '<!-- CSS Files -->'.
             '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
             '<link href="../assets/libraries/plantillaPDFFActura.css" rel="stylesheet" />'.
           '</head>';
$html.='<body>'.
        '<script type="text/php">'.
      'if ( isset($pdf) ) {'. 
        '$font = Font_Metrics::get_font("helvetica", "normal");'.
        '$size = 9;'.
        '$y = $pdf->get_height() - 24;'.
        '$x = $pdf->get_width() - 15 - Font_Metrics::get_text_width("1/1", $font, $size);'.
        '$pdf->page_text($x, $y, "{PAGE_NUM}/{PAGE_COUNT}", $font, $size);'.
      '}'.
    '</script>';
    $index_planilla=1;
while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$nombres=$result['nombres'];
	$apellidos=$result['apellidos'];
	$cargo=$result['cargo'];
	$identificacion=$result['identificacion'];
	$apellidos=$result['apellidos'];
	$total_aguinaldo=$result['total_aguinaldo'];
	$dias_360=$result['dias_360'];
	$promedio_ganado=$result['promedio_ganado'];
	
$suma_ingresos=$total_aguinaldo;
$suma_egresos=0;
    $liquido_pagable=$suma_ingresos-$suma_egresos;

		require 'boletas_aguinaldos_html.php';
		$html.='<hr>';
		require 'boletas_aguinaldos_html.php';	
        $html.='<div style="page-break-after: always"></div>';
	$index_planilla++;
}
$html.='</body>'.
'</html>';

$stmt=null;
$dbh=null;

echo $html;
// descargarPDFBoleta("COBOFAR - ",$html);

?>

<script type="text/javascript">
    window.print();
</script>
