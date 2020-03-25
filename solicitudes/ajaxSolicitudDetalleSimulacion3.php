<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$codigo=$_GET['cod_solicitud'];
$codigoSolicitud=$_GET['cod_solicitud'];
$codSimulacionServX=$_GET['cod_sim'];

$anio=$_GET['anio'];
$item_detalle=$_GET['item_detalle'];

$codUnidadX=$_GET['unidad'];
$codAreaX=$_GET['area'];

$codigo_detalle=$_GET['codigo_detalle'];

						//$detalle=obtenerDetalleSolicitudSimulacion($codSimulacionServX);
                        $codigoPlantillaXX=obtenerPlantillaCodigoSimulacionServicio($codSimulacionServX);
                        if($anio=="all"){

                        }
                        $detalle=obtenerDetalleSolicitudSimulacionCuentaPlantillaServicioFiltro($codSimulacionServX,$codigoPlantillaXX,$anio,$item_detalle,$codigo_detalle);
                        
                        $ibnorca=1;
                        $unidadSol=$codUnidadX;
                        $areaSol=$codAreaX;
						$idFila=1;
?><script>numFilas=0;cantidadItems=0;itemFacturas=[];</script><?php
						$cuentasCodigos=[];$conta=0;$auxAnio=0;$detalleAux="";$contAux=0;

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
							$nombrePartidaX="<b class='text-warning'>".$row['partida']."</b>";
							$nombrePartidaDetalleX="<b class='text-warning'>Cuenta</b> - <b class='text-dark'>Año ".$row['cod_anio']."</b>";
                            
							
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
							$nombrePartidaDetalleX="<b class='text-success'>Cuenta</b> - <b class='text-primary'>Año ".$row['cod_anio']."</b>";
                              							   
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