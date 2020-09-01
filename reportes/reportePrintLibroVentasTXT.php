<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();
//creamos el archivo txt
$fecha=date('Y-m-d');
$nombre_archivo="archivofacilito_ventas-".$fecha.".txt";
//limpiamos en archivo
$arch = fopen ("archivos_txt/".$nombre_archivo, "w+") or die ("nada");
fwrite($arch,"");
fclose($arch);
//archi limpiado
$archivo=fopen("archivos_txt/".$nombre_archivo, "a") or die ("#####0#####");//a de apertura de archivo
//RECIBIMOS LAS VARIABLES
$gestion = $_POST["cod_gestion"];
$cod_mes_x = $_POST["cod_mes"];
$unidad=$_POST["unidad"];

$nombre_gestion=nameGestion($gestion);
$sql="SELECT *,DATE_FORMAT(fecha_factura,'%d/%m/%Y')as fecha_factura_x from facturas_venta where MONTH(fecha_factura)=$cod_mes_x and YEAR(fecha_factura)=$nombre_gestion and cod_unidadorganizacional in ($unidad) ORDER BY nro_factura asc";
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
try {
	$index=1;           
	while ($row = $stmt2->fetch()) {
	$importe=sumatotaldetallefactura($codigo);                             
	switch ($cod_estadofactura) {
        case 1:
          $btnEstado='<span class="badge badge-success">';
        break;
        case 2:
          $btnEstado='<span class="badge badge-danger">';
          $razon_social="ANULADO";
          $importe=0;
          $codigo_control=0;
          $nit=0;
         // $fecha_factura=0;
        break;
        case 3:
          $btnEstado='<span class="badge badge-success">';
          $cod_estadofactura=1;
        break;
        case 4:
          $btnEstado='<span class="badge badge-default">';
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

	//agregamos los items al archivo	
	$texto="3|".$index."|".$fecha_factura."|".$nro_factura."|".$nro_autorizacion."|".$nombre_estado."|".$nit."|".$razon_social."|".number_format($importe,2,'.','')."|".number_format($importe_no_iva,2,'.','')."|".number_format($extento,2,'.','')."|".number_format($ventas_gravadas,2,'.','')."|".number_format($subtotal,2,'.','')."|".number_format($rebajas_sujetos_iva,2,'.','')."|".number_format($importe_debito_fiscal,2,'.','')."|".number_format($debito_fiscal,2,'.','')."|".$codigo_control;
	fwrite($archivo, $texto);
	fwrite($archivo, "".PHP_EOL);
	
	$index++; } 
	fclose($archivo);
	echo "#####1#####".$nombre_archivo;

} catch (Exception $e) {
	echo "#####0#####";
}
?>