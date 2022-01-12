<meta charset="utf-8">
<?php //ESTADO FINALIZADO

$fecha=date('Y-m-d');
$nombre_archivo="archivoSIAT_compras-".$fecha.".xls";
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary"); 
// header("Content-disposition: attachment; filename=\"archivofacilito.xls\""); 
header("Content-disposition: attachment; filename=".$nombre_archivo); 

require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once  '../fpdf_html.php';

$dbh = new Conexion();
//creamos el archivo txt
$gestion = $_GET["cod_gestion"];
//$cod_mes_x = $_GET["cod_mes"];
$unidad=$_GET["unidad"];
// $unidad_x = str_replace(",", "_", $unidad);
//RECIBIMOS LAS VARIABLES
$nombre_gestion=nameGestion($gestion);
//$nombre_mes=nombreMes($cod_mes_x);
$desdeInicioAnio="";
if($_GET["fecha_desde"]==""){
  $y=$globalNombreGestion;
  $desde=$y."-01-01";
  $hasta=$y."-12-31";
  $desdeInicioAnio=$y."-01-01";
}else{
  $porcionesFechaDesde = explode("-", $_GET["fecha_desde"]);
  $porcionesFechaHasta = explode("-", $_GET["fecha_hasta"]);

  $desdeInicioAnio=$porcionesFechaDesde[0]."-01-01";
  $desde=$porcionesFechaDesde[0]."-".$porcionesFechaDesde[1]."-".$porcionesFechaDesde[2];
  $hasta=$porcionesFechaHasta[0]."-".$porcionesFechaHasta[1]."-".$porcionesFechaHasta[2];
}

$sql="SELECT f.fecha,DATE_FORMAT(f.fecha,'%d/%m/%Y')as fecha_x,f.nit,f.razon_social,f.nro_factura,f.nro_autorizacion,f.codigo_control,f.importe,f.ice,f.exento,f.tipo_compra from facturas_compra f, comprobantes_detalle c, comprobantes cc where cc.codigo=c.cod_comprobante and f.cod_comprobantedetalle=c.codigo  and cc.cod_estadocomprobante<>2 and cc.cod_unidadorganizacional in ($unidad) and cc.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' ORDER BY f.fecha asc, f.nit, f.nro_factura";

//echo $sql;

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
	echo "<table><tr>
	<td>Nº</td>
	<td>ESPECIFICACION</td>
	<td>NIT PROVEEDOR</td>
	<td>RAZON SOCIAL PROVEEDOR</td>
	<td>CODIGO DE AUTORIZACION</td>
	<td>NUMERO FACTURA</td>
	<td>NUMERO DUI/DIM</td>
	<td>FECHA DE FACTURA/DUI/DIM</td>
	<td>IMPORTE TOTAL COMPRA</td>
	<td>IMPORTE ICE</td>
	<td>IMPORTE IEHD</td>
	<td>IMPORTE IPJ</td>
	<td>TASAS</td>
	<td>OTRO NO SUJETO A CREDITO FISCAL</td>
	<td>IMPORTES EXENTOS</td>
	<td>IMPORTE COMPRAS GRAVADAS A TASA CERO</td>
	<td>SUBTOTAL</td>
	<td>DESCUENTOS/BONIFICACIONES /REBAJAS SUJETAS AL IVA</td>
	<td>IMPORTE GIFT CARD</td>
	<td>IMPORTE BASE CF</td>
	<td>CREDITO FISCAL</td>
	<td>TIPO COMPRA</td>
	<td>CODIGO DE CONTROL</td>
	</tr>";
	$index=1;           
	while ($row = $stmt2->fetch()) {                             

		$importe_iehd=0;
		$importe_ipj=0;
		$importe_tasa=0;
		$importe_tasa_cero=0;
		$otros_no_iva=0;

		// $nombre_estado=nameEstadoFactura($cod_estadofactura);
		$importe_no_iva=$ice+$importe_iehd+$importe_ipj+$importe_tasa+$importe_tasa_cero+$exento+$otros_no_iva;
		$subtotal=$importe-$importe_no_iva;
		$rebajas_sujetos_iva=0;		
		$importe_credito_fiscal=$subtotal-$rebajas_sujetos_iva;

		$importe_gift_card=0;

		$credito_fiscal=13*$importe_credito_fiscal/100;
		if($nit ==null || $nit==''){
			$nit=0;
		}
		if($razon_social==null || $razon_social=='' || $razon_social==' '){
			$razon_social="S/N";
		}	
		$razon_social=trim($razon_social);
		$caracter=substr($codigo_control, -1);
        if($caracter=='-'){
          $codigo_control=trim($codigo_control, '-');
        }
        if($codigo_control==null || $codigo_control=="")
          $codigo_control=0;

      	if($tipo_compra=="" || $tipo_compra==null || $tipo_compra==0){
      		$tipo_compra=1;
      	}
      	$nro_autorizacion=trim($nro_autorizacion);
		//agregamos los items al archivo	
		//$texto="1|".$index."|".$fecha_factura."|".$nit."|".$razon_social."|".$nro_factura."|0|".$nro_autorizacion."|".number_format($importe,2,'.','')."|".number_format($importe_no_iva,2,'.','')."|".number_format($subtotal,2,'.','')."|".number_format($rebajas_sujetos_iva,2,'.','')."|".number_format($importe_credito_fiscal,2,'.','')."|".number_format($credito_fiscal,2,'.','')."|".$codigo_control."|".$tipo_compra;
		
		echo "<tr><td>".$index."</td><td>1</td><td>".$nit."</td><td>".$razon_social."</td><td>".$nro_autorizacion."&nbsp;</td><td>".$nro_factura."&nbsp;</td><td>0</td><td>".$fecha_factura."</td><td>".number_format($importe,2,'.',',')."</td><td>".$ice."</td><td>".$importe_iehd."</td><td>".$importe_ipj."</td><td>".$importe_tasa."</td><td>".$otros_no_iva."</td><td>".number_format($exento,2,'.',',')."</td><td>".$importe_tasa_cero."</td><td>".number_format($subtotal,2,'.',',')."</td><td>".$rebajas_sujetos_iva."</td><td>".$importe_gift_card."</td><td>".number_format($importe_credito_fiscal,2,'.',',')."</td><td>".number_format($credito_fiscal,2,'.',',')."</td><td>".$tipo_compra."</td><td>".$codigo_control."</td></tr>";
		$index++; 
	} 
	echo "</table>";



?>