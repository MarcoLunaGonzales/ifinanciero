<?php
						//$detalle=obtenerDetalleSolicitudSimulacion($codSimulacionServX);
                        $codigoPlantillaXX=obtenerPlantillaCodigoSimulacionServicio($codSimulacionServX);
                        $detalle=obtenerDetalleSolicitudSimulacionCuentaPlantillaServicio($codSimulacionServX,$codigoPlantillaXX);
                        $ibnorca=1;
                        $unidadSol=$codUnidadX;
                        $areaSol=$codAreaX;
						$idFila=1;
						$cuentasCodigos=[];$conta=0;$auxAnio=0;$detalleAux="";$contAux=0;$listaDetalles=[];
					?><div id="detalles_solicitud"><?php
							             
                       $solicitudDetalle=obtenerSolicitudRecursosDetalle($codigo);
                       while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                        $cod_plantillauditor="";
                          $tituloImporte="Importe";
                          $codCuentaX=$rowDetalles['cod_plancuenta'];
                          $codigoDetX=$rowDetalles["codigo"];
                          $cod_plantilladetalle=$rowDetalles['cod_detalleplantilla'];
                          $encontrar=0;
                          for ($i=0; $i < count($cuentasCodigos); $i++) { 
                            if($codigoDetX==$cuentasCodigos[$i]){
                              $encontrar++;
                            }	  
						  }
						 if($encontrar==0){
						 	?><script>var nfac=[];itemFacturas.push(nfac);</script><?php
						 	   $unidadXX=$rowDetalles["cod_unidadorganizacional"];	
                               $areaXX=$rowDetalles["cod_area"];	 
                               $codDetalleX=$rowDetalles["codigo"];	
                               $detalleX=$rowDetalles["detalle"];
                               $importeX=$rowDetalles["importe_presupuesto"];
							   $importeSolX=$rowDetalles["importe"];
							   $proveedorX=$rowDetalles["cod_proveedor"];
							   $retencionX=$rowDetalles["cod_confretencion"];
							   $codCuentaBancaria=$rowDetalles["cod_cuentabancaria"];
							   $codTipoPago=$rowDetalles["cod_tipopagoproveedor"];
                               $nombreBen=$rowDetalles["nombre_beneficiario"];
                               $apellidoBen=$rowDetalles["apellido_beneficiario"];
                               $cuentaBen=$rowDetalles["nro_cuenta_beneficiario"];
							   if($retencionX!=0){
							   	$tituloImporte="Importe - ".nameRetencion($retencionX);
							   	if(strlen($tituloImporte)>13){
                                   $tituloImporte=substr($tituloImporte,0,13)."...";
                                 }
							   }
							   $numeroCuentaX=trim($rowDetalles['numero']);
							   $nombreCuentaX=trim($rowDetalles['nombre']);
							   $nombrePartidaX="PARTIDA PRESUPUESTARIA";
							   $nombrePartidaDetalleX="Cuenta";
							   include "addFila.php";
                         			 
						       $idFila=$idFila+1;
						 } 
                       }
						

?>
                   </div>
	<script>$("#cantidad_filas").val(<?=($idFila-1)?>)</script>					