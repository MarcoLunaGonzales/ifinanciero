<?php //ESTADO FINALIZADO


$fecha=date('Y-m-d');
$nombre_archivo="archivoSIAT_ventas-".$fecha.".xls";
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary"); 
// header("Content-disposition: attachment; filename=\"archivofacilito.xls\""); 
header("Content-disposition: attachment; filename=".$nombre_archivo); 

require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once  '../fpdf_html.php';

$dbh = new Conexion();

$bd_siat=obtenerValorConfiguracion(106);

//creamos el archivo txt
echo "<table><td>Nº</td><td>ESPECIFICACION</td><td>FECHA DE LA FACTURA</td><td>N° DE LA FACTURA</td><td>CODIGO DE AUTORIZACION</td><td>NIT / CI CLIENTE</td><td>COMPLEMENTO</td><td>NOMBRE O RAZON SOCIAL</td><td>IMPORTE TOTAL DE LA VENTA</td><td>IMPORTE ICE</td><td>IMPORTE IEHD
</td><td>IMPORTE IPJ</td><td>TASAS</td><td>OTROS NO SUJETOS AL IVA</td><td>EXPORTACIONES Y OPERACIONES EXENTAS</td><td>VENTAS GRAVADAS A TASA CERO</td><td>SUBTOTAL</td><td>DESCUENTOS, BONIFICACIONES Y REBAJAS SUJETAS AL IVA</td><td>IMPORTE GIFT CARD</td><td>IMPORTE BASE PARA DEBITO FISCAL</td><td>DEBITO FISCAL</td><td>ESTADO</td><td>CODIGO DE CONTROL</td><td>TIPO DE VENTA</td>";
//RECIBIMOS LAS VARIABLES
$gestion = $_GET["cod_gestion"];
//$cod_mes_x = $_GET["cod_mes"];
$unidad=$_GET["unidad"];
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

$nombre_gestion=nameGestion($gestion);
$sql="SELECT *,DATE_FORMAT(f.fecha_factura,'%d/%m/%Y')as fecha_factura_x,
(select s.siat_cuf from ".$bd_siat.".salida_almacenes s where s.cod_salida_almacenes=f.idTransaccion_siat)as cuf from facturas_venta f where f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_unidadorganizacional in ($unidad) ORDER BY f.fecha_factura, f.nro_factura asc"; //MONTH(fecha_factura)=$cod_mes_x and YEAR(fecha_factura)=$nombre_gestion
// echo $sql; 
$stmt2 = $dbh->prepare($sql);
$stmt2->execute();
//resultado
$stmt2->bindColumn('codigo', $codigo);
$stmt2->bindColumn('cod_sucursal', $cod_sucursal);
$stmt2->bindColumn('cod_solicitudfacturacion', $cod_solicitudfacturacion);
$stmt2->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmt2->bindColumn('fecha_factura_x', $fecha_factura);
$stmt2->bindColumn('fecha_limite_emision', $fecha_limite_emision);
$stmt2->bindColumn('cod_tipoobjeto', $cod_tipoobjeto);
$stmt2->bindColumn('cod_tipopago', $cod_tipopago);
$stmt2->bindColumn('cod_cliente', $cod_cliente);
$stmt2->bindColumn('cod_personal', $cod_personal);
$stmt2->bindColumn('razon_social', $razon_social);
$stmt2->bindColumn('nit', $nit);
$stmt2->bindColumn('cod_dosificacionfactura', $cod_dosificacionfactura);
$stmt2->bindColumn('nro_factura', $nro_factura);
$stmt2->bindColumn('nro_autorizacion', $nro_autorizacion);
$stmt2->bindColumn('codigo_control', $codigo_control);
$stmt2->bindColumn('importe', $importe);
$stmt2->bindColumn('observaciones', $observaciones);
$stmt2->bindColumn('cod_estadofactura', $cod_estadofactura);
$stmt2->bindColumn('cod_comprobante', $cod_comprobante);
$stmt2->bindColumn('cuf', $cufSiat);

	$index=1;           
	while ($row = $stmt2->fetch()) {
	$importe=sumatotaldetallefactura($codigo);                             
	switch ($cod_estadofactura) {
        case 1:
          // $btnEstado='<span class="badge badge-success">';
        break;
        case 2:
          // $btnEstado='<span class="badge badge-danger">';
          $razon_social="ANULADO";
          $importe=0;
          $codigo_control=0;
          $nit=0;
         // $fecha_factura=0;
        break;
        case 3:
          // $btnEstado='<span class="badge badge-success">';
          $cod_estadofactura=1;
        break;
        case 4:
          // $btnEstado='<span class="badge badge-default">';
          $cod_estadofactura=1;
      }
	$nombre_estado=nameEstadoFactura($cod_estadofactura);
	$importe_no_iva=0;
	$extento=0;
	$ventas_gravadas=0;
	$rebajas_sujetos_iva=0;
	$subtotal=$importe-$importe_no_iva-$extento-$ventas_gravadas;
	$importe_debito_fiscal=$subtotal-$rebajas_sujetos_iva;
	$debito_fiscal=13*$importe_debito_fiscal/100;
	if($nit ==null || $nit==''){
		$nit=0;
	}	
	if($razon_social==null || $razon_social=='' || $razon_social==' '){
		$razon_social="S/N";
	}
	$razon_social=trim($razon_social);
    $nro_autorizacion=trim($nro_autorizacion);
    $codigo_control=trim($codigo_control);

  /*SIAT AUTORIZACION CUF*/
  if($nro_autorizacion==1 && $cufSiat<>""){
    $nro_autorizacion=$cufSiat;
  }


	//agregamos los items al archivo	
	echo "<tr><td>".$index."</td><td>2</td><td>".$fecha_factura."</td><td>".$nro_factura."</td><td>".$nro_autorizacion."</td><td>".$nit."</td><td></td><td>".$razon_social."</td><td>".number_format($importe,2,'.',',')."</td><td>0.00</td><td>0.00</td><td>0.00</td><td>0.00</td><td>0.00</td><td>0.00</td><td>0.00</td><td>".number_format($importe,2,'.',',')."</td><td>0.00</td><td>0.00</td><td>".number_format($importe,2,'.',',')."</td><td>".number_format($debito_fiscal,2,'.',',')."</td><td>".$nombre_estado."</td><td>".$codigo_control."</td><td>0</td></tr>";
	$index++; } 
echo "</table>";
?>