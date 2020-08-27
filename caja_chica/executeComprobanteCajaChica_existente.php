<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';
require_once __DIR__.'/../functionsGeneral.php';
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES
set_time_limit(3000);
session_start();
$cod_cajachica = $_POST["cod_cajachica"];//
$nro_comprobante = $_POST["nro_comprobante"];//
$mes_comprobante = $_POST["mes_comprobante"];//
$cod_tipocomprobante = $_POST["tipo_comprobante"];//
$gestion_cpte = $_POST["gestion"];//
$unidad_cpte = $_POST["unidad"];//
try{
	//comprobamos si el comprobante ya se generó
	// $stmtVerifComprobante = $dbh->prepare("SELECT cod_comprobante from caja_chica where codigo=$cod_cajachica");
 //    $stmtVerifComprobante->execute();
 //    $resultVerifCompro = $stmtVerifComprobante->fetch();
 //    $cod_tipocajachica = $resultVerifCompro['cod_comprobante'];  
 //    if($cod_tipocajachica==null || $cod_tipocajachica==0){//generamos si aun no se registro
    	//Verificamos si las retenciones de tipo credito fiscal iva tienen facturas
  //   	$cod_retencion=obtenerValorConfiguracion(53);
  //   	$sqlVerifRetencion="SELECT cc.nro_documento,(select (sum(f.importe)-sum(f.exento)-sum(f.tasa_cero)-sum(f.ice)) from facturas_detalle_cajachica f where f.cod_cajachicadetalle=cc.codigo) importe_factura,(select sum(g.importe) from detalle_cajachica_gastosdirectos g where g.cod_cajachicadetalle=cc.codigo) as importe_gasto_directo, cc.monto from caja_chicadetalle cc where cc.cod_cajachica=$cod_cajachica and cc.cod_tipodoccajachica=$cod_retencion and cc.cod_estadoreferencial=1;";
  //   	// echo $sqlVerifRetencion;
		// $stmtVerifRetencion = $dbh->prepare($sqlVerifRetencion);
	 //    $stmtVerifRetencion->execute();
	 //    $contadorRentencion=0;
	 //    $stringRetenciones="";
	 //    while($rowVeriRetencion = $stmtVerifRetencion->fetch()) 
	 //    {
	 //    	$nro_documento=$rowVeriRetencion['nro_documento'];
	 //    	$importe_gasto_directo_x=$rowVeriRetencion['importe_gasto_directo'];
	 //    	if($importe_gasto_directo_x==null || $importe_gasto_directo_x=='')$importe_gasto_directo_x=0;
	 //    	$importe_factura_x=$rowVeriRetencion['importe_factura']+$importe_gasto_directo_x;

	 //    	$monto_x=$rowVeriRetencion['monto'];	    	
	 //    	$importe_factura_x=round($importe_factura_x, 2);
	 //    	if($importe_factura_x!=$monto_x){
	 //    		$contadorRentencion++;
	 //    		$stringRetenciones.="Nro. Documento: ".$nro_documento."<br>";
	 //    	}
	 //    }
		$string_validacion_facturas=validacion_facturas_cajachica($cod_cajachica);
    	$array_validacion_facturas=explode('#####@@@@@', $string_validacion_facturas);
		$contadorRentencion=$array_validacion_facturas[0];
		$stringRetenciones=$array_validacion_facturas[1];
	    if($contadorRentencion!=0){//faltan facturas en retenciones tipo cred fiscal iva
	    	echo "4#####".$stringRetenciones;
	    }else{//todo okey
	    	$string_validacion_estadoscuetas=validacion_estadoscuenta_cajachica($cod_cajachica);
	    	$array_validacion_ec=explode('#####@@@@@', $string_validacion_estadoscuetas);
			$contadorEC=$array_validacion_ec[0];
			$stringEC_obs=$array_validacion_ec[1];			
			if($contadorEC!=0){//faltan facturas en retenciones tipo cred fiscal iva
				echo "5#####".$stringEC_obs;
			}
		}
	// }else{
	// 	echo "2#####";//El COMPROBANTE ya fue generado. Actualice el Sistema Por favor!		
	// }

} catch(PDOException $ex){
    // echo "Un error ocurrio".$ex->getMessage();
    echo "0#####";
}
?>


