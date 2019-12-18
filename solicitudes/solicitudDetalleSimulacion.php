<?php
						$detalle=obtenerDetalleSolicitudSimulacion($codSimulacionX);
                        $unidadSol=$codUnidadX;
                        $areaSol=$codAreaX;
						$idFila=1;
						$cuentasCodigos=[];$conta=0;
						while ($row = $detalle->fetch(PDO::FETCH_ASSOC)) {
							$codCuentaX=$row['codigo'];
							$codDetalleX=0;
							$solicitudDetalle=obtenerSolicitudRecursosDetalleCuenta($codigo,$codCuentaX);
							$detalleX="";
							$importeX="";
							$proveedorX="";
							$retencionX="";
							$tituloImporte="Importe";
							?><script>var nfac=[];itemFacturas.push(nfac);</script><?php
                            while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                               $cuentasCodigos[$conta]=$rowDetalles["codigo"];	
                               $codDetalleX=$rowDetalles["codigo"];	
                               $detalleX=$rowDetalles["detalle"];
							   $importeX=$rowDetalles["importe"];
							   $proveedorX=$rowDetalles["cod_proveedor"];
							   $retencionX=$rowDetalles["cod_confretencion"];
							   if($retencionX!=0){
							   	$tituloImporte="Importe - ".nameRetencion($retencionX);
							   }
							 $conta++;  							   
                            }
							$numeroCuentaX=trim($row['numero']);
							$nombreCuentaX=trim($row['nombre']);
							$nombrePartidaX=$row['partida'];

							include "addFila.php";
                         			 
						 $idFila=$idFila+1;
						}

						/*for ($i=0; $i < count($cuentasCodigos); $i++) { 
						  $solicitudDetalle=obtenerSolicitudRecursosDetalle($codigo,$cuentasCodigos[$i]);
						}*/
						?>
	<script>$("#cantidad_filas").val(<?=($idFila-1)?>)</script>					