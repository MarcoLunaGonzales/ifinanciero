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
$codigo_activo = $_GET["codigo_activo"];//codigoactivofijo
try{
	//comprobamos si el comprobante ya se generó
	$stmtVerifComprobante = $dbh->prepare("SELECT cod_comprobante from activosfijos where codigo=$codigo_activo");
    $stmtVerifComprobante->execute();
    $resultVerifCompro = $stmtVerifComprobante->fetch();
    $cod_comprobante = $resultVerifCompro['cod_comprobante'];  
    if($cod_comprobante==null){//generamos si aun no se registro    	
	    $stmtCajaChica = $dbh->prepare("SELECT af.codigo,af.codigoactivo,af.activo,af.fechalta, d.codigo as cod_depre,d.abreviatura as dep_nombre, tb.tipo_bien tb_tipo,af.contabilizado,af.cod_unidadorganizacional,af.cod_area,af.cod_responsables_responsable,af.numerofactura,af.valorinicial,
		(select pr.abreviatura from proyectos_financiacionexterna pr where pr.codigo=af.cod_proy_financiacion)as proy_financiacion,
		 (select uo.abreviatura from unidades_organizacionales uo where uo.codigo=af.cod_unidadorganizacional)as nombre_unidad, 
		 (select a.abreviatura from areas a where a.codigo=af.cod_area)as nombre_area,
		 (select concat_ws(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=af.cod_responsables_responsable)as nombre_responsable
		from activosfijos af, depreciaciones d, tiposbienes tb 
		where af.cod_depreciaciones = d.codigo and af.cod_tiposbienes = tb.codigo and af.codigo=$codigo_activo");
	    $stmtCajaChica->execute();
	    $resultCCD = $stmtCajaChica->fetch();
	    $activo = $resultCCD['activo'];    
	    $fecha_alta = $resultCCD['fecha_alta'];
	    $dep_nombre = $resultCCD['dep_nombre'];
	    $cod_responsables_responsable = $resultCCD['cod_responsables_responsable'];
	    $cod_area = $resultCCD['cod_area'];
	    $cod_unidadorganizacional = $resultCCD['cod_unidadorganizacional'];
	    $nombre_unidad = $resultCCD['nombre_unidad'];
	    $nombre_area = $resultCCD['nombre_area'];
	    $numerofactura = $resultCCD['numerofactura'];
	    $monto_af = $resultCCD['valorinicial'];
	    $nombre_responsable = $resultCCD['nombre_responsable'];
	    $cod_depre = $resultCCD['cod_depre'];
	    

		//datos para el comprbant
		$mesTrabajo=$_SESSION['globalMes'];
		$gestionTrabajo=$_SESSION['globalNombreGestion'];
		$codEmpresa=1;
		$codAnio=$_SESSION["globalNombreGestion"];
		$codMoneda=1;
		$codEstadoComprobante=1;
		$fechaActual=date("Y-m-d H:i:s");
		$tipoComprobante=3;

		$numeroComprobante=obtenerCorrelativoComprobante($tipoComprobante, $cod_unidadorganizacional, $gestionTrabajo, $mesTrabajo);
		$concepto_contabilizacion="REGISTRO DE F/".$numerofactura." COMPRA DE ".$activo.".";

		$codComprobante=obtenerCodigoComprobante();
		// $cod_contra_cuenta=obtenerValorConfiguracion(28);
		// $centroCostosDN=obtenerValorConfiguracion(29);//DN 
		$cod_cuenta_depreciacion=obtenerCuentaContableDepre($cod_depre);
		$cod_cuenta_iva=obtenerValorConfiguracion(3);
		$cod_cuenta_otrosAnticipos=obtenerValorConfiguracion(39);
		
		$procentaje_iva=obtenerValorConfiguracion(1);
		$procentaje_depreciaciones=100-$procentaje_iva;


		// echo $numeroComprobante;
		$sqlInsertCab="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa) values ('$codComprobante','$codEmpresa','$cod_unidadorganizacional','$codAnio','$codMoneda','$codEstadoComprobante','$tipoComprobante','$fechaActual','$numeroComprobante','$concepto_contabilizacion')";
		$stmtInsertCab = $dbh->prepare($sqlInsertCab);
		$flagSuccess=$stmtInsertCab->execute();
		if($flagSuccess){
			$ordenDetalle=1;//<--
	    	$descripcion=$nombre_unidad.'/'.$nombre_area.' F/'.$numerofactura.' '.$nombre_responsable.', '.$activo;
	        $monto_depre=$monto_af*$procentaje_depreciaciones/100;
	        $monto_iva=$monto_af*$procentaje_iva/100;
		                    
	    	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_depreciacion','0','$cod_unidadorganizacional','$cod_area','$monto_depre','0','$descripcion','$ordenDetalle')";
	        $stmtInsertDet = $dbh->prepare($sqlInsertDet);
	        $stmtInsertDet->execute();
	        $ordenDetalle++;
	        //INSERTAMOS LE DETALLE
	    	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_iva','0','$cod_unidadorganizacional','$cod_area','$monto_iva','0','$descripcion','$ordenDetalle')";
	        $stmtInsertDet = $dbh->prepare($sqlInsertDet);
	        $stmtInsertDet->execute();
	        $ordenDetalle++;
	        $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_otrosAnticipos','0','$cod_unidadorganizacional','$cod_area','0','$monto_af','$descripcion','$ordenDetalle')";
	        $stmtInsertDet = $dbh->prepare($sqlInsertDet);
	        $flagSuccessDet=$stmtInsertDet->execute();
	        $ordenDetalle++;
	        if($flagSuccessDet){
	        	//indicamos que ya se realizo el comprbante      
				$stmtUdateAF = $dbh->prepare("UPDATE activosfijos set contabilizado=1, cod_comprobante=$codComprobante where codigo=$codigo_activo");
				$stmtUdateAF->execute();	
	        }
			header('Location: ../comprobantes/impActivosFijos.php?comp='.$codComprobante.'&mon=1');
		}else{
			echo "Hubo un error en el proceso...";
		}

	}else{?>
    	<script>
		    alert('El COMPROBANTE ya fue generado. Actualice el Sistema Por favor!');
		    header('Location: ../comprobantes/impActivosFijos.php?comp='.$codComprobante.'&mon=1');
		</script><?php 
	}

} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>


