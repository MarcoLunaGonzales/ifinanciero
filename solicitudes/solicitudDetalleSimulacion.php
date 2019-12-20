<?php
						//$detalle=obtenerDetalleSolicitudSimulacion($codSimulacionX);
                        $detalle=obtenerDetalleSolicitudSimulacionCuenta($codSimulacionX);
                        $ibnorca=obtenerIbnorcaCheck($codSimulacionX);
                        $unidadSol=$codUnidadX;
                        $areaSol=$codAreaX;
						$idFila=1;
						$cuentasCodigos=[];$conta=0;
						while ($row = $detalle->fetch(PDO::FETCH_ASSOC)) {
							$codCuentaX=$row['codigo'];
							$codDetalleX=0;
							$solicitudDetalle=obtenerSolicitudRecursosDetalleCuenta($codigo,$codCuentaX);
							$detalleX="";
							$proveedorX="";
							$retencionX="";
							$tituloImporte="Importe";
							if($ibnorca==1){
                              $importeX=$row['monto_local'];
                              $importeSolX=$row['monto_local'];
							}else{
                              $importeX=$row['monto_externo'];
                              $importeSolX=$row['monto_externo'];
							}
							?><script>var nfac=[];itemFacturas.push(nfac);</script><?php
                            while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                               $cuentasCodigos[$conta]=$rowDetalles["codigo"];	
                               $codDetalleX=$rowDetalles["codigo"];	
                               $detalleX=$rowDetalles["detalle"];
                               $importeX=$rowDetalles["importe_presupuesto"];
							   $importeSolX=$rowDetalles["importe"];
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
							$nombrePartidaDetalleX="Cuenta";
                            
							include "addFila.php";
                         			 
						 $idFila=$idFila+1;
						}


                       $solicitudDetalle=obtenerSolicitudRecursosDetalle($codigo);
                       while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                          $codigoDetX=$rowDetalles["codigo"];
                          $encontrar=0;
                          for ($i=0; $i < count($cuentasCodigos); $i++) { 
                            if($codigoDetX==$cuentasCodigos[$i]){
                              $encontrar++;
                            }	  
						  }
						 if($encontrar==0){
                               $codDetalleX=$rowDetalles["codigo"];	
                               $detalleX=$rowDetalles["detalle"];
                               $importeX=$rowDetalles["importe_presupuesto"];
							   $importeSolX=$rowDetalles["importe"];
							   $proveedorX=$rowDetalles["cod_proveedor"];
							   $retencionX=$rowDetalles["cod_confretencion"];
							   if($retencionX!=0){
							   	$tituloImporte="Importe - ".nameRetencion($retencionX);
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
	<script>$("#cantidad_filas").val(<?=($idFila-1)?>)</script>					