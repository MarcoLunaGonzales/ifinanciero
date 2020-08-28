<?php //ESTADO FINALIZADO

ini_set('memory_limit', '-1');

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once 'reporte_comprobantes_html.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$unidad=$_POST["unidad"];
$stringUnidadesX=implode(",", $unidad);

$tipos_comprobante=$_POST["tipos_comprobante"];
$stringtiposComprobante=implode(",", $tipos_comprobante);

$porcionesFechaDesde = explode("-", $_POST["fecha_desde"]);
$porcionesFechaHasta = explode("-", $_POST["fecha_hasta"]);
$desde=$porcionesFechaDesde[0]."-".$porcionesFechaDesde[1]."-".$porcionesFechaDesde[2];
$hasta=$porcionesFechaHasta[0]."-".$porcionesFechaHasta[1]."-".$porcionesFechaHasta[2];
// $fechaTitulo="De ".strftime('%d/%m/%Y',strtotime($desde))." a ".strftime('%d/%m/%Y',strtotime($hasta));

if($_POST["numero_rango"]!="" || $_POST["numero_rango"]!=0){
	$porcionesnumero_rango = explode("-", $_POST["numero_rango"]);
	$numero_inicio=$porcionesnumero_rango[0];
	$numero_fin=$porcionesnumero_rango[1];
	$sql_rangonumero=" and c.numero BETWEEN $numero_inicio and $numero_fin";
}else{
	$sql_rangonumero="";
}

// $sql="SELECT f.codigo,f.cod_estadofactura
// FROM facturas_venta f 
// WHERE f.cod_unidadorganizacional in ($stringUnidadesX) and f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_estadofactura not in (2,4) $sql_rangonumero  ORDER BY f.nro_factura asc";
$sql="SELECT c.codigo,c.cod_estadocomprobante 
FROM comprobantes c
WHERE c.cod_unidadorganizacional in ($stringUnidadesX) and c.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and c.cod_tipocomprobante in ($stringtiposComprobante) and c.cod_estadocomprobante=1 $sql_rangonumero order by c.numero asc";//and c.codigo in (19783,19784,1978)
// echo $sql;
$stmt2 = $dbh->prepare($sql);
$stmt2->execute();
$stmt2->bindColumn('codigo', $codigo_comprobante);
$stmt2->bindColumn('cod_estadocomprobante', $estado_comprobante);

$admin=1;
$auxiliar=1;
$index=1;
$html="";
$sw=0;

$moneda=1;
$indice_aux=1;
while ($row = $stmt2->fetch()) {
	if($sw==0){
		$htmlConta1=generarHTMLComprobante($codigo_comprobante,$moneda,$indice_aux);
	    $array_html=explode('@@@@@@', $htmlConta1);
	    $html.=$array_html[0];
	    $sw=2;	    
	}else{
		$htmlConta1=generarHTMLComprobante($codigo_comprobante,$moneda,$indice_aux);
	    $array_html=explode('@@@@@@', $htmlConta1);
	    $html.=$array_html[0];
	}
	$index++;
}

if($html!='ERROR'){
	$html.='</body></html>';
	descargarPDF("IBNORCA_REPORTE_COMPROBANTES",$html);

	// descargarPDFFacturas_reporte("IBNORCA-REPORTE_FACTURAS",$html);
}else{
	echo "hubo un error al generar la factura";
}

?>


