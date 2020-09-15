<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
// require_once '../layouts/bodylogin2.php';

require_once __DIR__.'/../conexion.php';
$dbh = new Conexion();
//creamos el archivo txt
$gestion = $_POST["cod_gestion"];
$cod_mes_x = $_POST["cod_mes"];
$stringEstadoX=$_POST["estado"];
//$stringEstadoX=implode(",", $estado);

$fecha=date('Y-m-d');
$nombre_archivo="archivofacilito_compras-".$fecha.".txt";
//limpiamos en archivo
$arch = fopen ("archivos_txt_compras/".$nombre_archivo, "w+") or die ("nada");
fwrite($arch,"");
fclose($arch);
//archivo limpiado
$archivo=fopen("archivos_txt_compras/".$nombre_archivo, "a") or die ("#####0#####");//a de apertura de archivo
//RECIBIMOS LAS VARIABLES
$nombre_gestion=nameGestion($gestion);
$nombre_mes=nombreMes($cod_mes_x);


$sql="SELECT f.fecha,DATE_FORMAT(f.fecha,'%d/%m/%Y')as fecha_x,f.nit,f.razon_social,f.nro_factura,f.nro_autorizacion,f.codigo_control,f.importe,f.ice,f.exento,f.tipo_compra 
from facturas_compra f 
join solicitud_recursosdetalle sd on sd.codigo=f.cod_solicitudrecursodetalle
join solicitud_recursos s on s.codigo=sd.cod_solicitudrecurso

where s.cod_estadosolicitudrecurso in ($stringEstadoX) and s.cod_estadoreferencial<>2 and (sd.cod_area=1235 or sd.cod_unidadorganizacional=3000) and MONTH(f.fecha)=$cod_mes_x and YEAR(f.fecha)=$nombre_gestion ORDER BY f.fecha asc";
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
		//$importe_no_iva=$ice+$exento;
		//$subtotal=$importe-$importe_no_iva;
		//$rebajas_sujetos_iva=0;		

		// $subtotal=$importe-$importe_no_iva-$extento-$ventas_gravadas;

		$importe_credito_fiscal=$subtotal-$rebajas_sujetos_iva;
		$credito_fiscal=13*$importe_credito_fiscal/100;
		if($nit ==null || $nit==''){
			$nit=0;
		}
		if($razon_social==null || $razon_social=='' || $razon_social==' '){
			$razon_social="S/N";
		}
		if(trim($codigo_control)==""){
             $codigo_control="0";
        }

        $sumadeimporte=$importe;
        $subtotal=$sumadeimporte-$sumadeimporte;
        $descuento=0;
        $importeBaseCF=$subtotal-$descuento;
        $credito_fiscal=$importeBaseCF*0.13;

        $razon_social=trim($razon_social);
        $nro_autorizacion=trim($nro_autorizacion);
        $codigo_control=trim($codigo_control);
        $tipo_compra=2;
		//agregamos los items al archivo	
		$texto="1|".$index."|".$fecha_factura."|".$nit."|".$razon_social."|".$nro_factura."|0|".$nro_autorizacion."|".number_format($sumadeimporte,2,'.','')."|".number_format($sumadeimporte,2,'.','')."|".number_format($subtotal,2,'.','')."|".number_format($descuento,2,'.','')."|".number_format($importeBaseCF,2,'.','')."|".number_format($credito_fiscal,2,'.','')."|".$codigo_control."|".$tipo_compra;
		fwrite($archivo, $texto);
		fwrite($archivo, "".PHP_EOL);

		
		$index++; 
	}
	fclose($archivo);
	echo "#####1#####".$nombre_archivo;


} catch (Exception $e) {
	echo "#####0#####";
}
?>

