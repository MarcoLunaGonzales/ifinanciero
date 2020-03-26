<?php
						//$detalle=obtenerDetalleSolicitudSimulacion($codSimulacionX);
                        $codigoPlantillaXX=obtenerPlantillaCodigoSimulacion($codSimulacionX);
                        $detalle=obtenerDetalleSolicitudSimulacionCuentaPlantilla($codSimulacionX,$codigoPlantillaXX);
                        $ibnorca=obtenerIbnorcaCheck($codSimulacionX);
                        $unidadSol=$codUnidadX;
                        $areaSol=$codAreaX;
						$idFila=1;
						$cuentasCodigos=[];$conta=0;
						while ($row = $detalle->fetch(PDO::FETCH_ASSOC)) {
							$cod_plantilladetalle=$row['codigo_detalle'];
							$cod_plantillauditor="";
							$codCuentaX=$row['codigo'];
							$codDetalleX=0;
							$solicitudDetalle=obtenerSolicitudRecursosDetallePlantilla($codigoSolicitud,$cod_plantilladetalle);
							$detalleX=$row['glosa'];
							$proveedorX="";
							$retencionX="";
							$tituloImporte="Importe";
							if($ibnorca==1){
                              $importeX=$row['monto_total'];
                              $importeSolX=$row['monto_total'];
							}else{
                              $importeX=$row['monto_externo'];
                              $importeSolX=$row['monto_externo'];
							}
							$nombrePartidaX="<b class='text-warning'>".$row['partida']."</b>";
							$nombrePartidaDetalleX="<b class='text-warning'>Cuenta</b>";
							
							$entro=0;
                            while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                            	$entro=1;
                               $cuentasCodigos[$conta]=$rowDetalles["codigo"];	
                               $codDetalleX=$rowDetalles["codigo"];	
                               $detalleX=$rowDetalles["detalle"];
                               $importeX=$rowDetalles["importe_presupuesto"];
							   $importeSolX=$rowDetalles["importe"];
							   $proveedorX=$rowDetalles["cod_proveedor"];
							   $retencionX=$rowDetalles["cod_confretencion"];
							   if($retencionX!=0){
							   	$tituloImporte="Importe - ".nameRetencion($retencionX);
							   	if(strlen($tituloImporte)>13){
                                   $tituloImporte=substr($tituloImporte,0,13)."...";
                                 }
							   }
							 $conta++;  
							 $nombrePartidaX="<b class='text-success'>".$row['partida']."</b>";
							 $nombrePartidaDetalleX="<b class='text-success'>Cuenta</b>";							   
                            }
							$numeroCuentaX=trim($row['numero']);
							$nombreCuentaX=trim($row['nombre']);
                            
							if($entro==0){
								?><script>var nfac=[];itemFacturas.push(nfac);</script><?php
                         		include "addFila.php";
                         		$idFila=$idFila+1;
                         	}	
						}


                       $solicitudDetalle=obtenerSolicitudRecursosDetalle($codigoSolicitud);
                       while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                          $tituloImporte="Importe";
                          $codCuentaX=$rowDetalles['cod_plancuenta'];
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
	<script>$("#cantidad_filas").val(<?=($idFila-1)?>)</script>					