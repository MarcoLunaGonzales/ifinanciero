<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
// require_once '../layouts/bodylogin2.php';

require_once __DIR__.'/../conexion.php';
$dbh = new Conexion();
//creamos el archivo txt
$fecha=date('Y-m-d');
$nombre_archivo="archivofacilito_compras-".$fecha.".txt";
//limpiamos en archivo
$arch = fopen ("archivos_txt/".$nombre_archivo, "w+") or die ("nada");
fwrite($arch,"");
fclose($arch);
//archi limpiado
$archivo=fopen("archivos_txt_compras/".$nombre_archivo, "a") or die ("#####0#####");//a de apertura de archivo
//RECIBIMOS LAS VARIABLES

$unidad=$_POST["unidad"];
// $stringUnidadesX=implode(",", $unidad);

$porcionesFechaDesde = explode("-", $_POST["fecha_desde"]);
$porcionesFechaHasta = explode("-", $_POST["fecha_hasta"]);
$desde=$porcionesFechaDesde[0]."-".$porcionesFechaDesde[1]."-".$porcionesFechaDesde[2];
$hasta=$porcionesFechaHasta[0]."-".$porcionesFechaHasta[1]."-".$porcionesFechaHasta[2];


$sql="SELECT f.fecha,DATE_FORMAT(f.fecha,'%d/%m/%Y')as fecha_x,f.nit,f.razon_social,f.nro_factura,f.nro_autorizacion,f.codigo_control,f.importe,f.ice,f.exento,f.tipo_compra from facturas_compra f, comprobantes_detalle c, comprobantes cc where cc.codigo=c.cod_comprobante and f.cod_comprobantedetalle=c.codigo and cc.cod_unidadorganizacional in ($unidad) and f.fecha BETWEEN '$desde' and '$hasta' and cc.cod_estadocomprobante<>2 ORDER BY fecha asc";
$stmt2 = $dbh->prepare($sql);
// echo $sql;
// Ejecutamos                        
$stmt2->execute();
//resultado
$stmt2->bindColumn('fecha_x', $fecha_factura);
$stmt2->bindColumn('nit', $nit);
$stmt2->bindColumn('razon_social', $razon_social);
$stmt2->bindColumn('nro_factura', $nro_factura);
$stmt2->bindColumn('nro_autorizacion', $nro_autorizacion);
$stmt2->bindColumn('codigo_control', $codigo_control);
$stmt2->bindColumn('importe', $importe);
$stmt2->bindColumn('ice', $ice);
$stmt2->bindColumn('exento', $exento);          
$stmt2->bindColumn('tipo_compra', $tipo_compra);  

try {
	$index=1;           
	while ($row = $stmt2->fetch()) {                             
	
		// $nombre_estado=nameEstadoFactura($cod_estadofactura);
		$importe_no_iva=$ice+$exento;
		$subtotal=$importe-$importe_no_iva;
		$rebajas_sujetos_iva=0;		

		// $subtotal=$importe-$importe_no_iva-$extento-$ventas_gravadas;

		$importe_credito_fiscal=$subtotal-$rebajas_sujetos_iva;
		$credito_fiscal=13*$importe_credito_fiscal/100;
		if($nit ==null || $nit==''){
			$nit=0;
		}
		if($razon_social==null || $razon_social=='' || $razon_social==' '){
			$razon_social="S/N";
		}
		//agregamos los items al archivo	
		$texto="1|".$index."|".$fecha_factura."|".$nit."|".$razon_social."|".$nro_factura."|0|".$nro_autorizacion."|".$importe."|".$importe_no_iva."|".$subtotal."|".$rebajas_sujetos_iva."|".$importe_credito_fiscal."|".$credito_fiscal."|".$codigo_control."|".$tipo_compra;
		fwrite($archivo, $texto);
		fwrite($archivo, "\n");
		
		$index++; 
	} 
	fclose($archivo);
	echo "#####1#####".$nombre_archivo;


} catch (Exception $e) {
	echo "#####0#####";
}
?>