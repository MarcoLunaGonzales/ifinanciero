<?php
						//$detalle=obtenerDetalleSolicitudSimulacion($codSimulacionServX);
                        $codigoPlantillaXX=obtenerPlantillaCodigoSimulacionServicio($codSimulacionServX);
                        $detalle=obtenerDetalleSolicitudSimulacionCuentaPlantillaServicio($codSimulacionServX,$codigoPlantillaXX);
                        $ibnorca=1;
                        $unidadSol=$codUnidadX;
                        $areaSol=$codAreaX;
						$idFila=1;
						$cuentasCodigos=[];$conta=0;
						while ($row = $detalle->fetch(PDO::FETCH_ASSOC)) {
							//$cod_plantilladetalle=$row['codigo_detalle'];
							$codigo_fila=explode("###",$row['codigo_detalle']);
							if($codigo_fila[1]=="DET-SIM"){
                             $cod_plantilladetalle=$codigo_fila[0];
                             $cod_plantillauditor="";
                             $solicitudDetalle=obtenerSolicitudRecursosDetallePlantilla($codigo,$cod_plantilladetalle);
							}else{
                             $cod_plantilladetalle="";
                             $cod_plantillauditor=$codigo_fila[0];
                             $solicitudDetalle=obtenerSolicitudRecursosDetallePlantillaAud($codigo,$cod_plantillauditor);
							}
							$codCuentaX=$row['codigo'];
							$codDetalleX=0;
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
							   	if(strlen($tituloImporte)>13){
                                   $tituloImporte=substr($tituloImporte,0,13)."...";
                                 }
							   }
							 $conta++;  							   
                            }
							$numeroCuentaX=trim($row['numero']);
							$nombreCuentaX=trim($row['nombre']);
							$nombrePartidaX="<b class='text-success'>".$row['partida']."</b>";
							$nombrePartidaDetalleX="<b class='text-success'>Cuenta</b> - <b class='text-primary'>Año ".$row['cod_anio']."</b>";
                            
							include "addFila.php";
                         			 
						 $idFila=$idFila+1;
						}


                       $solicitudDetalle=obtenerSolicitudRecursosDetalle($codigo);
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