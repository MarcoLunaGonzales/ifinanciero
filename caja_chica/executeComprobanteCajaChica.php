<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once '../layouts/bodylogin.php';
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES
set_time_limit(3000);
session_start();
$globalUser=$_SESSION["globalUser"];
$globalMes=$_SESSION['globalMes'];
$cod_cajachica = $_GET["cod_cajachica"];//codigoactivofijo
try{
	//comprobamos si el comprobante ya se generó
	$stmtVerifComprobante = $dbh->prepare("SELECT cod_comprobante from caja_chica where codigo=$cod_cajachica");
    $stmtVerifComprobante->execute();
    $resultVerifCompro = $stmtVerifComprobante->fetch();
    $cod_comprobante_x = $resultVerifCompro['cod_comprobante'];
    if($cod_comprobante_x==null || $cod_comprobante_x==0){//generamos si aun no se registro
    	//Verificamos si las retenciones de tipo credito fiscal iva tienen facturas
    	$string_validacion_facturas=validacion_facturas_cajachica($cod_cajachica);
    	$array_validacion_facturas=explode('#####@@@@@', $string_validacion_facturas);
		$contadorRentencion=$array_validacion_facturas[0];
		$stringRetenciones=$array_validacion_facturas[1];
	    if($contadorRentencion>0){//faltan facturas en retenciones tipo cred fiscal iva
	    	?>
	    	<script>
	    		var stringRetenciones="<?=$stringRetenciones?>";
	            Swal.fire("Error!","No se pudo generar el comprobante. <br>\n Error en: "+stringRetenciones+"<br>\n No tiene Factura registrada.", "error");
	        </script>
	    	<?php
	    }else{//todo okey
	    	$string_validacion_estadoscuetas=validacion_estadoscuenta_cajachica($cod_cajachica);
	    	$array_validacion_ec=explode('#####@@@@@', $string_validacion_estadoscuetas);
			$contadorEC=$array_validacion_ec[0];
			$stringEC_obs=$array_validacion_ec[1];
			if($contadorEC!=0){//faltan facturas en retenciones tipo cred fiscal iva
				?>
		    	<script>
		    		var stringRetenciones="<?=$stringEC_obs?>";
		            Swal.fire("Error!","No se pudo generar el comprobante. <br>\n Error en: "+stringRetenciones+"<br>\n No tiene Estado de Cuenta Asociada.", "error");
		        </script>
		    	<?php				
			}else{
				$string_validacion_fechafactura=validacion_fechafactura_comprobante($cod_cajachica,$globalMes);
		    	$array_validacion_ff=explode('#####@@@@@', $string_validacion_fechafactura);
				$contadorff=$array_validacion_ff[0];
				$stringFF_obs=$array_validacion_ff[1];
				if($contadorff!=0){//controlador de facturas con fecha diferente al del comprobante
					?>
			    	<script>
			    		var stringRetenciones="<?=$stringFF_obs?>";
			            Swal.fire("Error!","No se pudo generar el comprobante. <br>\n Error en: "+stringRetenciones+"<br>\n Las Fechas de las Facturas no corresponden al mes en curso.", "error");
			        </script>
			    	<?php				
				}else{
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
					$diaActual=date("d");
					$horaActual=date("H:i:s");
					$fechaActual=$gestionTrabajo."-".$mesTrabajo."-".$diaActual." ".$horaActual;

					$tipoComprobante=3;
					$numeroComprobante=obtenerCorrelativoComprobante($tipoComprobante, $cod_uo_tcc, $gestionTrabajo, $mesTrabajo);
					$concepto_contabilizacion="CONTABILIZACIÓN CAJA CHICA N° ".$numeroCC." DE ".$nombre_uo_tcc;

					$codComprobante=obtenerCodigoComprobante();
					
					// $cod_contra_cuenta=obtenerValorConfiguracion(28);
					$cod_contra_cuenta=obtnercontracuentaUnidad($cod_uo_tcc);
					if($cod_contra_cuenta==0){
						$cod_contra_cuenta=obtenerValorConfiguracion(28);// LA PAZ
					}

					$centroCostosDN=obtenerValorConfiguracion(29);//DN 
					// echo $numeroComprobante;

					$sqlInsertCab="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa,created_at,created_by) values ('$codComprobante','$codEmpresa','$cod_uo_tcc','$codAnio','$codMoneda','$codEstadoComprobante','$tipoComprobante','$fechaActual','$numeroComprobante','$concepto_contabilizacion',NOW(),$globalUser)";
					$stmtInsertCab = $dbh->prepare($sqlInsertCab);
					$flagSuccess=$stmtInsertCab->execute();
					   //listado de todo el detalle de caja chica en curso
				    require_once 'executeComprobanteCajaChica_cuerpo.php';
					//indicamos que ya se realizo el comprbante      
					$stmtUdateCajaChica = $dbh->prepare("UPDATE caja_chica set cod_comprobante=$codComprobante where codigo=$cod_cajachica");
					$stmtUdateCajaChica->execute();
					header('Location: ../comprobantes/imp.php?comp='.$codComprobante.'&mon=1');
				}
			}
	    }
	}else{?>
    	<script>
		    alert('El COMPROBANTE ya fue generado. Actualice el Sistema Por favor!');
		</script><?php 
	}

} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>


