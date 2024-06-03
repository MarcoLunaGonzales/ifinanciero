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
$globalUser=$_SESSION["globalUser"];
$cod_cajachica = $_POST["cod_cajachica"];//
$nro_comprobante = $_POST["nro_comprobante"];//
$mes_comprobante = $_POST["mes_comprobante"];//
$cod_tipocomprobante = $_POST["tipo_comprobante"];//
$gestion_cpte = $_POST["gestion"];//
$unidad_cpte = $_POST["unidad"];//
try{

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
		if($contadorEC!=0){//faltan estados de cuenta
			echo "5#####".$stringEC_obs;
		}else{
			$string_validacion_fechafactura=validacion_fechafactura_comprobante($cod_cajachica,$mes_comprobante,$gestion_cpte);
	    	$array_validacion_ff=explode('#####@@@@@', $string_validacion_fechafactura);
			$contadorff=$array_validacion_ff[0];
			$stringFF_obs=$array_validacion_ff[1];
			if($contadorff!=0){//controlador de facturas con fecha diferente al del comprobante
				echo "6#####".$stringFF_obs;
			}else{

				$sqlCajaChica="SELECT *,(select CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno) from personal p where p.codigo=cod_personal) as name_personal,
		        (select tc.nombre from tipos_caja_chica tc where tc.codigo=cod_tipocajachica) as name_tipocc,
		        (select (select uo.nombre from unidades_organizacionales uo where uo.codigo= tc2.cod_uo) from tipos_caja_chica tc2 where tc2.codigo=cod_tipocajachica)as nombre_uo_tcc,
		        (select  tc3.cod_uo from tipos_caja_chica tc3 where tc3.codigo=cod_tipocajachica)as cod_uo_tcc,
		        (select  tc4.cod_area from tipos_caja_chica tc4 where tc4.codigo=cod_tipocajachica)as cod_area_tcc,
				(select  tc4.cod_cuenta from tipos_caja_chica tc4 where tc4.codigo=cod_tipocajachica)as cod_cuenta
		        FROM caja_chica where codigo=$cod_cajachica";

				$stmtCajaChica = $dbh->prepare($sqlCajaChica);
			    
			    //echo $sqlCajaChica;

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
				$cod_contra_cuenta = $resultCCD['cod_cuenta']; // ! Nuevo campo: Para contabilización de caja chica

				//datos para el comprbante
				$mesTrabajo=$_SESSION['globalMes'];
				$gestionTrabajo=$_SESSION['globalNombreGestion'];
				$codEmpresa=1;
				$codAnio=$_SESSION["globalNombreGestion"];
				$codMoneda=1;
				$codEstadoComprobante=1;
				// $fechaActual=date("Y-m-d H:i:s");
				$diaActual=date("d");
				$horaActual=date("H:i:s");
				$fechaActual=$gestionTrabajo."-".$mesTrabajo."-".$diaActual." ".$horaActual;
				$tipoComprobante=3;
				// $numeroComprobante=obtenerCorrelativoComprobante($tipoComprobante, $cod_uo_tcc, $gestionTrabajo, $mesTrabajo);
				$numeroComprobante=$nro_comprobante;
				$concepto_contabilizacion="CONTABILIZACIÓN ".obtenerNombreInstanciaCajaChica($cod_cajachica).", CAJA CHICA N° ".$numeroCC." DE ".$nombre_uo_tcc;

				$codComprobante=obtenerCodigoComprobanteExistente($cod_tipocomprobante,$nro_comprobante,$mes_comprobante,$unidad_cpte,$gestion_cpte);
				
				if($codComprobante==0){
					echo "3#####";//no se encontró el comprobante
				}else{
					// $cod_contra_cuenta=obtnercontracuentaUnidad($cod_uo_tcc); // ! FORMA ANTERIOR DE CONTABILIZACIÓN 
					if($cod_contra_cuenta==0){
						$cod_contra_cuenta=obtenerValorConfiguracion(28);// LA PAZ
					}


					$centroCostosDN=obtenerValorConfiguracion(29);//DN 
					// echo $numeroComprobante;,fecha='$fechaActual'
					$sqlInsertCab="UPDATE comprobantes set glosa='$concepto_contabilizacion',modified_by=$globalUser,modified_at=NOW() where codigo='$codComprobante'";
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
		}
	}
} catch(PDOException $ex){
    // echo "Un error ocurrio".$ex->getMessage();
    echo "0#####".$ex->getMessage();
}
?>


