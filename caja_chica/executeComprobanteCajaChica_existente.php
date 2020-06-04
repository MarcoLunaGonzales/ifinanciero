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
try{
	//comprobamos si el comprobante ya se generó
	$stmtVerifComprobante = $dbh->prepare("SELECT cod_comprobante from caja_chica where codigo=$cod_cajachica");
    $stmtVerifComprobante->execute();
    $resultVerifCompro = $stmtVerifComprobante->fetch();
    $cod_tipocajachica = $resultVerifCompro['cod_comprobante'];  
    if($cod_tipocajachica==null || $cod_tipocajachica==0){//generamos si aun no se registro
    	//Verificamos si las retenciones de tipo credito fiscal iva tienen facturas
    	$cod_retencion=obtenerValorConfiguracion(53);
		$stmtVerifRetencion = $dbh->prepare("SELECT cc.nro_documento,(select f.codigo from facturas_detalle_cajachica f where f.cod_cajachicadetalle=cc.codigo and f.importe=cc.monto) as cod_factura from caja_chicadetalle cc where cc.cod_cajachica=$cod_cajachica and cc.cod_tipodoccajachica=$cod_retencion and cc.cod_estadoreferencial=1");
	    $stmtVerifRetencion->execute();
	    $contadorRentencion=0;
	    $stringRetenciones="";
	    while($rowVeriRetencion = $stmtVerifRetencion->fetch()) 
	    {
	    	$nro_documento=$rowVeriRetencion['nro_documento'];
	    	$cod_factura=$rowVeriRetencion['cod_factura'];
	    	if($cod_factura==null || $cod_factura=='' || $cod_factura==' '){
	    		$contadorRentencion++;
	    		$stringRetenciones.="Nro Doc. :".$nro_documento."<br>";
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

			$codComprobante=obtenerCodigoComprobanteExistente($cod_tipocomprobante,$nro_comprobante,$mes_comprobante);
			if($codComprobante==0){
				echo "3#####";//no se encontró el comprobante
			}else{

				$cod_contra_cuenta=obtenerValorConfiguracion(28);
				$centroCostosDN=obtenerValorConfiguracion(29);//DN 
				// echo $numeroComprobante;
				$sqlInsertCab="UPDATE comprobantes set glosa='$concepto_contabilizacion' where codigo='$codComprobante'";

				$stmtInsertCab = $dbh->prepare($sqlInsertCab);
				$flagSuccess=$stmtInsertCab->execute();
				//borramos el detalle del comprobante
				$sqlDeleteDetalle="DELETE FROM comprobantes_detalle where cod_comprobante=$codComprobante";
				$stmtDeleteDetalle = $dbh->prepare($sqlDeleteDetalle);
				$stmtDeleteDetalle->execute();
				 //listado de todo el detalle de caja chica en curso
			    $stmtCajaChicaDet = $dbh->prepare("SELECT codigo,cod_tipodoccajachica,observaciones,monto,cod_uo,cod_area,cod_cuenta,(select p.nombre from af_proveedores p where p.codigo=cod_proveedores)as proveedor,(select c.nombre from plan_cuentas c where c.codigo=cod_cuenta)nombre_cuenta,
			    (select c2.numero from plan_cuentas c2 where c2.codigo=cod_cuenta)numero_cuenta,
			    (select CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno) from personal p where p.codigo=cod_personal)as personal,
			    (select u.abreviatura from unidades_organizacionales u where u.codigo=cod_uo)as nombre_uo,
			    (select a.abreviatura from areas a where a.codigo=cod_area)as nombre_area
			    from caja_chicadetalle where cod_estadoreferencial=1 and cod_cajachica=$cod_cajachica ORDER BY 1");
			    $stmtCajaChicaDet->execute();
			    $stmtCajaChicaDet->bindColumn('codigo', $codigo_ccdetalle);
			    $stmtCajaChicaDet->bindColumn('cod_cuenta', $cod_cuenta);

			    $stmtCajaChicaDet->bindColumn('nombre_cuenta', $nombre_cuenta);
			    $stmtCajaChicaDet->bindColumn('numero_cuenta', $numero_cuenta);    
			    $stmtCajaChicaDet->bindColumn('personal', $personal);
			    $stmtCajaChicaDet->bindColumn('proveedor', $proveedor);
			    $stmtCajaChicaDet->bindColumn('cod_uo', $cod_uo);
			    $stmtCajaChicaDet->bindColumn('cod_area', $cod_area);
			    $stmtCajaChicaDet->bindColumn('nombre_uo', $nombre_uo);
			    $stmtCajaChicaDet->bindColumn('nombre_area', $nombre_area);
			    $stmtCajaChicaDet->bindColumn('cod_tipodoccajachica', $cod_retencioncajachica);
			    $stmtCajaChicaDet->bindColumn('observaciones', $observaciones_dcc);
			    $stmtCajaChicaDet->bindColumn('monto', $monto_dcc);
			    $ordenDetalle=1;//<--
			    while ($rowCajaChicaDet = $stmtCajaChicaDet->fetch()) 
			    {
			    	//el porcentaje origen de tipo de retencion
			        $stmtRetencionOrigen = $dbh->prepare("SELECT porcentaje_cuentaorigen from configuracion_retenciones where codigo=$cod_retencioncajachica");
			        $stmtRetencionOrigen->execute();
			        $resultRetencionOrgine = $stmtRetencionOrigen->fetch();
			        $porcentaje_cuentaorigen = $resultRetencionOrgine['porcentaje_cuentaorigen']; 
			        // verificamos si el porcentaje es mayor a 100%
			        if($porcentaje_cuentaorigen>100){
			        	$monto_recalculado=$monto_dcc*$porcentaje_cuentaorigen/100;
			        }else{
			        	$monto_recalculado=$monto_dcc;
			        }
			        //Listamos los gastos que tengan factura y los contabilizamos
			        $sw_facturas=0;//contador de facturas
			        $stmtFacturas = $dbh->prepare("SELECT nro_factura,razon_social,importe from facturas_detalle_cajachica where cod_cajachicadetalle=$codigo_ccdetalle");
			        $stmtFacturas->execute();
			        $stmtFacturas->bindColumn('nro_factura', $nro_factura);
			        $stmtFacturas->bindColumn('razon_social', $razon_social);
			        $stmtFacturas->bindColumn('importe', $importe);
			        while ($rowFac = $stmtFacturas->fetch()) 
			        {
			            //buscamos el tipo de retencion
			            $stmtRetenciones = $dbh->prepare("SELECT cod_cuenta,porcentaje,debe_haber from configuracion_retencionesdetalle where cod_configuracionretenciones=$cod_retencioncajachica");
			            $stmtRetenciones->execute();
			            $stmtRetenciones->bindColumn('cod_cuenta', $cod_cuenta_retencion);
			            $stmtRetenciones->bindColumn('porcentaje', $porcentaje_retencion);
			            $stmtRetenciones->bindColumn('debe_haber', $debe_haber);
			            while ($rowFac = $stmtRetenciones->fetch()) 
			            {
			            	$descripcion=$nombre_uo.' F/'.$nro_factura.' '.$personal.', '.$observaciones_dcc;
			                $monto=$monto_recalculado*$porcentaje_retencion/100;
			                if($cod_cuenta_retencion>0){
			                    // $nro_cuenta_retencion=obtieneNumeroCuenta($cod_cuenta_retencion);
			                    // $nombre_cuenta_retencion=nameCuenta($cod_cuenta_retencion);
			                    if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
			                    	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_retencion','0','$cod_uo','$cod_area','$monto','0','$descripcion','$ordenDetalle')";
						            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
						            $flagSuccessDet=$stmtInsertDet->execute();
						            $ordenDetalle++;
			                    }else{
			                    	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_retencion','0','$cod_uo','$cod_area','0','$monto','$descripcion','$ordenDetalle')";
						            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
						            $flagSuccessDet=$stmtInsertDet->execute();
						            $ordenDetalle++;
			                    }
			                }else{             

			                	if($porcentaje_cuentaorigen>100){
						        	$monto_restante=$monto_recalculado;
						        }else{
						        	$monto_restante=$monto_recalculado*$porcentaje_cuentaorigen/100;       
						        }
			                	
			                    $cod_uo_config=obtenerValorConfiguracion(15);
			                    if($cod_uo==$cod_uo_config){
			                        //desde aqui repartimos la contabilizacion a las oficinas si es DN
			                        $stmtOficina = $dbh->prepare("SELECT dgd.cod_unidadorganizacional,dgd.porcentaje,
			                           (SELECT uo.abreviatura FROM unidades_organizacionales uo WHERE uo.codigo=dgd.cod_unidadorganizacional) as oficina
			                        from distribucion_gastosporcentaje_detalle dgd,distribucion_gastosporcentaje dg
			                        where dgd.cod_distribucion_gastos=dg.codigo and dg.estado=1 and porcentaje>0");
			                        $stmtOficina->execute();
			                        $stmtOficina->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
			                        $stmtOficina->bindColumn('porcentaje', $porcentaje);
			                        $stmtOficina->bindColumn('oficina', $oficinaFac);
			                        while ($rowOf = $stmtOficina->fetch()) 
			                        {                                    
			                            $descripcion_of=$oficinaFac.'/'.$centroCostosDN.' F/'.$nro_factura.' '.$personal.', '.$observaciones_dcc;
			                            $monto_of=$monto_restante*$porcentaje/100;
			                            if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
			                            	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_unidadorganizacional','$centroCostosDN','0','$monto_of','$descripcion_of','$ordenDetalle')";
								            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
								            $flagSuccessDet=$stmtInsertDet->execute();
								            $ordenDetalle++;
			                            }else{
			                            	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_unidadorganizacional','$centroCostosDN','$monto_of','0','$descripcion_of','$ordenDetalle')";
								            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
								            $flagSuccessDet=$stmtInsertDet->execute();
								            $ordenDetalle++;
			                            }                                     
			                        }
			                    }else{
			                        $descripcion_of=$nombre_uo.'/'.$nombre_area.' F/'.$nro_factura.' '.$personal.', '.$observaciones_dcc;	
			                        if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
			                    		$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_uo','$cod_area','0','$monto_restante','$descripcion_of','$ordenDetalle')";
							            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
							            $flagSuccessDet=$stmtInsertDet->execute();
							            $ordenDetalle++;
			                        }else{
			                        	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_uo','$cod_area','$monto_restante','0','$descripcion_of','$ordenDetalle')";
							            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
							            $flagSuccessDet=$stmtInsertDet->execute();
							            $ordenDetalle++;
			                        }       
			                    }
			                    // aqui la contra cuenta
			                    $descripcion_contra_cuenta='CONTABILIZACIÓN CAJA CHICA. '.$personal.', '.$observaciones_dcc;
			                    $monto_contracuenta=$monto_recalculado*$porcentaje_retencion/100;
			                    if($debe_haber==1){//si es debe, pondremos en haber
			                    	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_contra_cuenta','0', '$cod_uo_tcc','$cod_area_tcc','$monto_contracuenta','0','$descripcion_contra_cuenta','$ordenDetalle')";
						            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
						            $flagSuccessDet=$stmtInsertDet->execute();
						            $ordenDetalle++;
			                    }else{
			                    	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_contra_cuenta','0','$cod_uo_tcc','$cod_area_tcc','0','$monto_contracuenta','$descripcion_contra_cuenta','$ordenDetalle')";
						            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
						            $flagSuccessDet=$stmtInsertDet->execute();
						            $ordenDetalle++;
			                    }
			                }
			                $sw_facturas++;//contador de facturas incrementa
			            }
			        }
			        if($sw_facturas==0){//compra no tiene factura registrada                        
				        
				        //buscamos el tipo de retencion
				        $stmtRetenciones = $dbh->prepare("SELECT cod_cuenta,porcentaje,debe_haber from configuracion_retencionesdetalle where cod_configuracionretenciones=$cod_retencioncajachica");
				        $stmtRetenciones->execute();
				        $stmtRetenciones->bindColumn('cod_cuenta', $cod_cuenta_retencion);
				        $stmtRetenciones->bindColumn('porcentaje', $porcentaje_retencion);
				        $stmtRetenciones->bindColumn('debe_haber', $debe_haber);
				        while ($rowFac = $stmtRetenciones->fetch()) 
				        {                            
				            //recalculando monto
				            $descripcionIT=$nombre_uo.' SF '.$personal.', '.$observaciones_dcc;                                
				            $monto_it=$monto_recalculado*$porcentaje_retencion/100;            
				            //las retenciones
				            if($cod_cuenta_retencion>0){
				                
				                if($debe_haber==1){ //preguntamos si pertenece a la columna debe o haber
				                	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_retencion','0','$cod_uo','$cod_area','$monto_it','0','$descripcionIT','$ordenDetalle')";
						            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
						            $flagSuccessDet=$stmtInsertDet->execute();
						            $ordenDetalle++;
				                }else{
				                	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_retencion','0','$cod_uo','$cod_area','0','$monto_it','$descripcionIT','$ordenDetalle')";
						            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
						            $flagSuccessDet=$stmtInsertDet->execute();
						            $ordenDetalle++;
				                }
				            }else{                
				                $monto_restante=$monto_recalculado;//completo
				                //buscamos si tiene alguna contra cuenta registrada en estados_cuenta
				                $stmtContraCuenta = $dbh->prepare("SELECT cod_plancuenta from estados_cuenta where cod_cajachicadetalle=$codigo_ccdetalle");
				                $stmtContraCuenta->execute();
				                $resultContraCuenta = $stmtContraCuenta->fetch();
				                $cod_plancuenta = $resultContraCuenta['cod_plancuenta']; 
				                // echo "llego: ".$cod_plancuenta;
				                if($cod_plancuenta>0){
				                    //buscamos el nombre y el numero de la contra cuenta                    
				                    $descripcionIT=$nombre_uo.'/'.$nombre_area.' '.$proveedor.' SF, '.$observaciones_dcc;
				                    if($debe_haber==1){ //debe=1
				                    	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_plancuenta','0','$cod_uo','$cod_area','0','$monto_restante','$descripcionIT','$ordenDetalle')";
							            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
							            $flagSuccessDet=$stmtInsertDet->execute();
							            $ordenDetalle++;
				                    }else{//haber=2
				                    	
							            $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_plancuenta','0','$cod_uo','$cod_area','$monto_restante','0','$descripcionIT','$ordenDetalle')";
							            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
							            $flagSuccessDet=$stmtInsertDet->execute();
							            $ordenDetalle++;
				                    }
				                    
				                    //sacamos el codigo del comprobante detalle 
				                    $stmtEstadoCuenta = $dbh->prepare("SELECT codigo from comprobantes_detalle where cod_comprobante=$codComprobante order by codigo desc LIMIT 1");
					                $stmtEstadoCuenta->execute();
					                $resultEstadoCuenta = $stmtEstadoCuenta->fetch();
					                $cod_compro_det = $resultEstadoCuenta['codigo'];
					                //actualizamos el campo comporbante del estado_cuentas 
					                $stmtUpdateEstadoCuenta = $dbh->prepare("UPDATE estados_cuenta set cod_comprobantedetalle=$cod_compro_det where cod_cajachicadetalle=$codigo_ccdetalle");
					                $stmtUpdateEstadoCuenta->execute();

				                }else{		                	
			                    	if($porcentaje_cuentaorigen>100){
							        	$monto_restante=$monto_recalculado;
							        }else{
							        	// $monto_restante=$monto_recalculado*$porcentaje_cuentaorigen/100;       
							        	$monto_restante=$monto_recalculado;
							        }
				                    $cod_uo_config=obtenerValorConfiguracion(15);
				                    if($cod_uo==$cod_uo_config){
				                        //desde aqui repartimos la contabilizacion a las oficinas 
				                        $stmtOficina = $dbh->prepare("SELECT dgd.cod_unidadorganizacional,dgd.porcentaje,
				                           (SELECT uo.abreviatura FROM unidades_organizacionales uo WHERE uo.codigo=dgd.cod_unidadorganizacional) as oficina
				                        from distribucion_gastosporcentaje_detalle dgd,distribucion_gastosporcentaje dg
				                        where dgd.cod_distribucion_gastos=dg.codigo and dg.estado=1 and porcentaje>0");
				                        $stmtOficina->execute();
				                        $stmtOficina->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
				                        $stmtOficina->bindColumn('porcentaje', $porcentaje);
				                        $stmtOficina->bindColumn('oficina', $oficinaFac);
				                        while ($rowOf = $stmtOficina->fetch()) 
				                        {                                                                            
				                            $descripcion_of=$oficinaFac.'/'.$nombre_uo.' SF '.$personal.', '.$observaciones_dcc;
				                            $monto_of=$monto_restante*$porcentaje/100;

				                            if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
				                            	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_unidadorganizacional','$centroCostosDN','0','$monto_of','$descripcion_of','$ordenDetalle')";
									            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
									            $flagSuccessDet=$stmtInsertDet->execute();
									            $ordenDetalle++;
				                            }else{
				                                $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_unidadorganizacional','$centroCostosDN','$monto_of','0','$descripcion_of','$ordenDetalle')";
									            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
									            $flagSuccessDet=$stmtInsertDet->execute();
									            $ordenDetalle++;                               
				                            }
				                        }
				                    }else{
				                        $descripcion_of=$nombre_uo.'/'.$nombre_area.' SF '.$personal.', '.$observaciones_dcc;
				                        if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
				                        	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_uo','$cod_area','0','$monto_restante','$descripcion_of','$ordenDetalle')";
								            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
								            $flagSuccessDet=$stmtInsertDet->execute();
								            $ordenDetalle++;
				                        }else{
				                            $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_uo','$cod_area','$monto_restante','0','$descripcion_of','$ordenDetalle')";
								            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
								            $flagSuccessDet=$stmtInsertDet->execute();
								            $ordenDetalle++;
				                        }
				                    }
				                }
				                //contra cuenta
				                $descripcion_contra_cuenta='CONTABILIZACIÓN CAJA CHICA.'.$personal.'/'.$proveedor.', '.$observaciones_dcc;
				                $monto_contracuenta=$monto_recalculado*$porcentaje_retencion/100;
				                if($debe_haber==1){//si es debe, pondremos en haber
				                	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_contra_cuenta','0','$cod_uo_tcc','$cod_area_tcc','$monto_contracuenta','0','$descripcion_contra_cuenta','$ordenDetalle')";
						            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
						            $flagSuccessDet=$stmtInsertDet->execute();
						            $ordenDetalle++;
				                }else{
				                	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_contra_cuenta','0','$cod_uo_tcc','$cod_area_tcc','0','$monto_contracuenta','$descripcion_contra_cuenta','$ordenDetalle')";
						            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
						            $flagSuccessDet=$stmtInsertDet->execute();
						            $ordenDetalle++;
				                }
				            }            
				        }
			        
			    	}
			    }
				//indicamos que ya se realizo el comprobante      
				$stmtUdateCajaChica = $dbh->prepare("UPDATE caja_chica set cod_comprobante=$codComprobante where codigo=$cod_cajachica");
				$stmtUdateCajaChica->execute();
				//header('Location: ../comprobantes/imp.php?comp='.$codComprobante.'&mon=1');
				echo "1#####";
			}
		}
	}else{
		echo "2#####";//El COMPROBANTE ya fue generado. Actualice el Sistema Por favor!		
	}

} catch(PDOException $ex){
    // echo "Un error ocurrio".$ex->getMessage();
    echo "0#####";
}
?>


