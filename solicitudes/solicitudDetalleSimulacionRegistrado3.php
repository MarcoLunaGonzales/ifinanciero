<?php
						//$detalle=obtenerDetalleSolicitudSimulacion($codSimulacionServX);
                        $codigoPlantillaXX=obtenerPlantillaCodigoSimulacionServicio($codSimulacionServX);
                        $detalle=obtenerDetalleSolicitudSimulacionCuentaPlantillaServicio($codSimulacionServX,$codigoPlantillaXX);
                        $ibnorca=1;
                        $unidadSol=$codUnidadX;
                        $areaSol=$codAreaX;
                        $unidadXX=$codUnidadX;
                        $areaXX=$codAreaX;
						$idFila=1;
						$cuentasCodigos=[];$conta=0;$auxAnio=0;$detalleAux="";$contAux=0;$listaDetalles=[];
						$desdePropuestas=1;
					?><div id="detalles_solicitud"><?php
						while ($row = $detalle->fetch(PDO::FETCH_ASSOC)) {
							$unidadXX=$codUnidadX;
                        	$areaXX=$codAreaX;
							//$cod_plantilladetalle=$row['codigo_detalle'];
							$codigo_fila=explode("###",$row['codigo_detalle']);
							if($codigo_fila[1]=="DET-SIM"){
                             $cod_plantilladetalle=$codigo_fila[0];
                             $cod_plantillauditor="";
                             $solicitudDetalle=obtenerSolicitudRecursosDetallePlantillaSol($codigo,$cod_plantilladetalle);
							}else{
                             $cod_plantilladetalle="";
                             $cod_plantillauditor=$codigo_fila[0];
                             $solicitudDetalle=obtenerSolicitudRecursosDetallePlantillaAudSol($codigo,$cod_plantillauditor);
							}
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
							if((int)$row['cod_anio']!=$auxAnio){
								$anioSelect=(int)$row['cod_anio']; 				 
						    }
							$auxAnio=(int)$row['cod_anio'];

							if($detalleX!=$detalleAux){
								$listaDetalles[$contAux]=$detalleX;
								$contAux++;
							}
                         		
							$detalleAux=$detalleX;
							
                            while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                            	?><script>var nfac=[];itemFacturas.push(nfac);console.log("Se creo la factura con datos"+ cantidadItems);</script><?php
                               $unidadXX=$rowDetalles['cod_unidadorganizacional'];
							   $areaXX=$rowDetalles['cod_area'];
							   $codCuentaX=$rowDetalles['cod_plancuenta'];
                               $cuentasCodigos[$conta]=$rowDetalles["codigo"];	
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
							 $conta++;
							 $nombrePartidaX="<b class='text-success'>".$row['partida']."</b>";
							$nombrePartidaDetalleX="<b class='text-success'>Cuenta</b> - <b class='text-primary'>Año ".$row['cod_anio']."</b>";
                              include "addFila.php";
                         			 
						      $idFila=$idFila+1;							   
                            }	 
						 
						}
                        
                        
                       $solicitudDetalle=obtenerSolicitudRecursosDetalle($codigo);
                       while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                       	?><script>console.log("Entro fila vacia");</script><?php 
                          $tituloImporte="Importe";
                          $codCuentaX=$rowDetalles['cod_plancuenta'];
                          $cod_plantilladetalle=$rowDetalles['cod_detalleplantilla'];
                          $codigoDetX=$rowDetalles["codigo"];
                          $encontrar=0;
                          for ($i=0; $i < count($cuentasCodigos); $i++) { 
                            if($codigoDetX==$cuentasCodigos[$i]){
                              $encontrar++;
                            }	  
						  }
						 if($encontrar==0){
						 	?><script>console.log("ENCONTRÓ");</script><?php 
						 	?><script>var nfac=[];itemFacturas.push(nfac);console.log("Se creo la factura vacia");</script><?php 
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
	<script>$("#cantidad_filas").val(<?=($idFila-1)?>);console.log(<?=($idFila-1)?>);</script>					