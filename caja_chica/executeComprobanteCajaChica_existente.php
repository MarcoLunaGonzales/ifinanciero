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
    	$cod_retencion=obtenerValorConfiguracion(53);
    	$sqlVerifRetencion="SELECT cc.nro_documento,(select (sum(f.importe)+sum(f.exento)+sum(f.tasa_cero)+sum(f.ice)) from facturas_detalle_cajachica f where f.cod_cajachicadetalle=cc.codigo) importe_factura,(select sum(g.importe) from detalle_cajachica_gastosdirectos g where g.cod_cajachicadetalle=cc.codigo) as importe_gasto_directo, cc.monto from caja_chicadetalle cc where cc.cod_cajachica=$cod_cajachica and cc.cod_tipodoccajachica=$cod_retencion and cc.cod_estadoreferencial=1;";
    	// echo $sqlVerifRetencion;
		$stmtVerifRetencion = $dbh->prepare($sqlVerifRetencion);
	    $stmtVerifRetencion->execute();
	    $contadorRentencion=0;
	    $stringRetenciones="";
	    while($rowVeriRetencion = $stmtVerifRetencion->fetch()) 
	    {
	    	$nro_documento=$rowVeriRetencion['nro_documento'];
	    	$importe_gasto_directo_x=$rowVeriRetencion['importe_gasto_directo'];
	    	if($importe_gasto_directo_x==null || $importe_gasto_directo_x=='')$importe_gasto_directo_x=0;
	    	$importe_factura_x=$rowVeriRetencion['importe_factura']+$importe_gasto_directo_x;

	    	$monto_x=$rowVeriRetencion['monto'];	    	
	    	if($importe_factura_x!=$monto_x){
	    		$contadorRentencion++;
	    		$stringRetenciones.="Nro. Documento: ".$nro_documento."<br>";
	    	}
	    }

	    if($contadorRentencion!=0){//faltan facturas en retenciones tipo cred fiscal iva
	    	echo "4#####".$stringRetenciones;
	    }else{//todo okey
	    	//Informacion caja chica en curso
		    $stmtCajaChica = $dbh->prepare("SELECT *,(select CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno) from personal p where p.codigo=cod_personal) as name_personal,
		        (select tc.nombre from tipos_caja_chica tc where tc.codigo=cod_tipocajachica) as name_tipocc,
		        (select (select uo.nombre from unidades_organizacionales uo where uo.codigo= tc2.cod_uo) from tipos_caja_chica tc2 where tc2.codigo=cod_tipocajachica)as nombre_uo_tcc,
		        (select  tc3.cod_uo from tipos_caja_chica tc3 where tc3.codigo=cod_tipocajachica)as cod_uo_tcc,
		        (select  tc4.cod_area from tipos_caja_chica tc4 where tc4.codigo=cod_tipocajachica)as cod_area_tcc
		        FROM caja_chica where codigo=$cod_cajachica");
		    $stmtCajaChica->execute();
		    $resultCCD = $stmtCajaChica->fetch();
		    $cod_tipocajachica = $resultCCD['cod_tipocajachica'];    
		    $numeroCC = $resultCCD['numero'];
		    $monto_inicio = $resultCCD['monto_inicio'];
		    $monto_reembolso = $resultCCD['monto_reembolso'];
		    $observacionesCC = $resultCCD['observaciones'];
		    $cod_personalCCD = $resultCCD['cod_personal'];
		    $name_personalCC = $resultCCD['name_personal'];
		    $name_tipoccCC = $resultCCD['name_tipocc'];
		    $nombre_uo_tcc = $resultCCD['nombre_uo_tcc'];
		    $cod_uo_tcc = $resultCCD['cod_uo_tcc'];
		    $cod_area_tcc = $resultCCD['cod_area_tcc'];
		    $fecha_inicio_cc = $resultCCD['fecha'];
		    $fecha_cierre_cc = $resultCCD['fecha_cierre'];

			//datos para el comprbant
			$mesTrabajo=$_SESSION['globalMes'];
			$gestionTrabajo=$_SESSION['globalNombreGestion'];
			$codEmpresa=1;
			$codAnio=$_SESSION["globalNombreGestion"];
			$codMoneda=1;
			$codEstadoComprobante=1;
			$fechaActual=date("Y-m-d H:i:s");
			$tipoComprobante=3;

			// $numeroComprobante=obtenerCorrelativoComprobante($tipoComprobante, $cod_uo_tcc, $gestionTrabajo, $mesTrabajo);
			$numeroComprobante=$nro_comprobante;
			$concepto_contabilizacion="CONTABILIZACIÓN CAJA CHICA N° ".$numeroCC." DE ".$nombre_uo_tcc;

			$codComprobante=obtenerCodigoComprobanteExistente($cod_tipocomprobante,$nro_comprobante,$mes_comprobante,$unidad_cpte,$gestion_cpte);
			
			if($codComprobante==0){
				echo "3#####";//no se encontró el comprobante
			}else{
				$cod_contra_cuenta=obtnercontracuentaUnidad($cod_uo_tcc);
				if($cod_contra_cuenta==0){
					$cod_contra_cuenta=obtenerValorConfiguracion(28);// LA PAZ
				}
				$centroCostosDN=obtenerValorConfiguracion(29);//DN 
				// echo $numeroComprobante;
				$sqlInsertCab="UPDATE comprobantes set glosa='$concepto_contabilizacion' where codigo='$codComprobante'";
				$stmtInsertCab = $dbh->prepare($sqlInsertCab);
				$flagSuccess=$stmtInsertCab->execute();
				//necesitamos el codigo del comprobante detalle para borrar las facturas registradas en facturas_Compra
				$stmtCmptDet = $dbh->prepare("SELECT codigo FROM comprobantes_detalle where cod_comprobante=$codComprobante");
			    $stmtCmptDet->execute();			  
			    $stmtCmptDet->bindColumn('codigo', $codigo_detalecpte);
			    while ($row = $stmtCmptDet->fetch()) 
			    {
			    	//BORRAMOS LAS FACTURAS INSERTADAS SI LA HUBIERA
					$sqlDeleteDetalleFactComp="DELETE FROM facturas_compra where cod_comprobantedetalle=$codigo_detalecpte";
					$stmtDeleteDetalleFactComp = $dbh->prepare($sqlDeleteDetalleFactComp);
					$stmtDeleteDetalleFactComp->execute();
			    }
				//borramos el detalle del comprobante
				$sqlDeleteDetalle="DELETE FROM comprobantes_detalle where cod_comprobante=$codComprobante";
				$stmtDeleteDetalle = $dbh->prepare($sqlDeleteDetalle);
				$stmtDeleteDetalle->execute();
				//procesamos con el cuerpo del comprobante
			    require_once 'executeComprobanteCajaChica_cuerpo.php';
				//indicamos que ya se realizo el comprobante      
				$stmtUdateCajaChica = $dbh->prepare("UPDATE caja_chica set cod_comprobante=$codComprobante where codigo=$cod_cajachica");
				$stmtUdateCajaChica->execute();
				//header('Location: ../comprobantes/imp.php?comp='.$codComprobante.'&mon=1');
				echo "1#####";
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


