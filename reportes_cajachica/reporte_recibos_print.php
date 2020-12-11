<?php //ESTADO FINALIZADO

ini_set('memory_limit', '-1');

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once 'reporte_recibos_html.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$caja_chica=$_POST["cod_caja_chica"];
$stringCajaChicaX=$caja_chica;

$porcionesFechaDesde = explode("-", $_POST["fecha_desde"]);
$porcionesFechaHasta = explode("-", $_POST["fecha_hasta"]);
$desde=$porcionesFechaDesde[0]."-".$porcionesFechaDesde[1]."-".$porcionesFechaDesde[2];
$hasta=$porcionesFechaHasta[0]."-".$porcionesFechaHasta[1]."-".$porcionesFechaHasta[2];
$fechaTitulo="De ".strftime('%d/%m/%Y',strtotime($desde))." a ".strftime('%d/%m/%Y',strtotime($hasta));

if($_POST["numero_rango"]!="" || $_POST["numero_rango"]!=0){
	$porcionesnumero_rango = explode("-", $_POST["numero_rango"]);
	$numero_inicio=$porcionesnumero_rango[0];
	$numero_fin=$porcionesnumero_rango[1];
	$sql_rangonumero=" and cd.nro_recibo >= $numero_inicio and cd.nro_recibo <= $numero_fin";
}else{
	$sql_rangonumero="";
}

$sql="SELECT cd.codigo,cd.cod_estadoreferencial FROM caja_chicadetalle cd 
WHERE cd.cod_cajachica in ($stringCajaChicaX) and cd.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and cd.cod_estadoreferencial not in (2) $sql_rangonumero ORDER BY cd.nro_recibo asc";
// echo $sql;
$stmt2 = $dbh->prepare($sql);
$stmt2->execute();
$stmt2->bindColumn('codigo', $codigo_recibo);
$stmt2->bindColumn('cod_estadoreferencial', $estado_referencial);

$admin=1;
$auxiliar=1;
$index=1;
$html="";
$sw=0;
while ($row = $stmt2->fetch()) {
	$htmlConta1=generarHTMLReciboCajaChica($codigo_recibo,$auxiliar,$index,$estado_referencial);
	$array_html=explode('@@@@@@', $htmlConta1);
	$html.=$array_html[0];
	$index++;
}

if($html!='ERROR'){
	$html.='</body></html>';  	
	descargarPDFRecibo_reporte("IBNORCA-REPORTE_RECIBOS",$html);

}else{
	echo "hubo un error al generar la factura";
}

?>


