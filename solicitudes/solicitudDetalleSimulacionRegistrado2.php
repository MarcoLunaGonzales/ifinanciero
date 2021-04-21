<?php
						//$detalle=obtenerDetalleSolicitudSimulacion($codSimulacionServX);
                        $codigoPlantillaXX=obtenerPlantillaCodigoSimulacion($codSimulacionServX);
                        $detalle=obtenerDetalleSolicitudSimulacionCuentaPlantilla($codSimulacionServX,$codigoPlantillaXX);
                        $ibnorca=1;
                        $unidadSol=$codUnidadX;
                        $areaSol=$codAreaX;
                        $unidadXX=$codUnidadX;
                        $areaXX=$codAreaX;

                        //echo $areaXX;
						$idFila=1;
						$cuentasCodigos=[];$conta=0;$auxAnio=0;$detalleAux="";$contAux=0;$listaDetalles=[];
						$desdePropuestas=1;
					?><div id="detalles_solicitud"><?php
						while ($row = $detalle->fetch(PDO::FETCH_ASSOC)) {
							  
							$cod_plantillauditor="";
							//$cod_plantilladetalle=$row['codigo_detalle'];
							/*$codigo_fila=explode("###",$row['codigo_detalle']);
							if($codigo_fila[1]=="DET-SIM"){
                             $cod_plantilladetalle=$codigo_fila[0];
                             $cod_plantillauditor="";
                             $solicitudDetalle=obtenerSolicitudRecursosDetallePlantillaSol($codigo,$cod_plantilladetalle);
							}else{
                             $cod_plantilladetalle="";
                             $cod_plantillauditor=$codigo_fila[0];
                             $solicitudDetalle=obtenerSolicitudRecursosDetallePlantillaAudSol($codigo,$cod_plantillauditor);
							}*/
							$cod_plantilladetalle=$row['codigo_detalle'];
							$codCuentaX=$row['codigo'];
							$codDetalleX=0;
							$detalleX=$row['glosa'];
							$proveedorX="";
							$retencionX="";
                            $codTipoPago=0;
                            $codCuentaBancaria=0;
                            $nombreBen="";
                            $apellidoBen="";
                            $cuentaBen="";
							$tituloImporte="Importe";
							if($ibnorca==1){
                              $importeX=$row['monto_total'];
                              $importeSolX=$row['monto_total'];
							}else{
                              $importeX=$row['monto_externo'];
                              $importeSolX=$row['monto_externo'];
							}
							$nombrePartidaX="<b class='text-warning'>".$row['partida']."</b>";
							$nombrePartidaDetalleX="<b class='text-warning'>Cuenta</b> - <b class='text-dark'>Año ".$row['cod_anio']."</b>";
                            
                            $numeroCuentaX=trim($row['numero']);
							$nombreCuentaX=trim($row['nombre']);
							/*if((int)$row['cod_anio']!=$auxAnio){
								$anioSelect=(int)$row['cod_anio']; 				 
						    }
							$auxAnio=(int)$row['cod_anio'];*/

							if($detalleX!=$detalleAux){
								$listaDetalles[$contAux]=$detalleX;
								$contAux++;
							}
                         		
							$detalleAux=$detalleX;
							
                            while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                              ?><script>var nfac=[];itemFacturas.push(nfac);</script><?php
                               $cuentasCodigos[$conta]=$rowDetalles["codigo"];	
                               $codDetalleX=$rowDetalles["codigo"];
                               $codCuentaX=$rowDetalles['cod_plancuenta'];	
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

							      if($codCuentaX==obtenerValorConfiguracion(88)){                      
                    $proveedorNuevoX=obtenerDatosContratoSolicitudCapacitacion($codSimulacionX)[1];
                    if($proveedorNuevoX>0){
                      $conContrato=1;  
                    }                        
                  }
							 $conta++;
							 $nombrePartidaX="<b class='text-success'>".$row['partida']."</b>";
							$nombrePartidaDetalleX="<b class='text-success'>Cuenta</b> - <b class='text-primary'>Año ".$row['cod_anio']."</b>";
                              include "addFila.php";
                         			 
						      $idFila=$idFila+1;							   
                            }	 
						 
						}
                        
                         
                       $solicitudDetalle=obtenerSolicitudRecursosDetalle($codigo);
                       while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
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
                 if($codCuentaX==obtenerValorConfiguracion(88)){                      
                    $proveedorNuevoX=obtenerDatosContratoSolicitudCapacitacion($codSimulacionX)[1];
                    if($proveedorNuevoX>0){
                      $conContrato=1;  
                    }                        
                  }

							   include "addFila.php";
                         			 
						       $idFila=$idFila+1;
						       
						 } 
                       }
						

$resultado = array_unique($listaDetalles);						
for ($i=0; $i < count($resultado); $i++) { 
                       ?>
                         <script>
                            $('#item_detalle_solicitud').append("<option value='<?=$i?>'><?=$resultado[$i]?></option>");
                            $('.selectpicker').selectpicker("refresh");
                          </script>
						 <?php  	
}
?>
                   </div>
	<script>$("#cantidad_filas").val(<?=($idFila-1)?>)</script>					