<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();
$codSimulacionServX=$_GET['cod_sim'];

$anio=$_GET['anio'];
$item_detalle=$_GET['item_detalle'];

$codUnidadX=0;
$codAreaX=0;
$tipoSolicitud =$_GET['tipo'];
$codigo_detalle=$_GET['codigo_detalle'];

		//$detalle=obtenerDetalleSolicitudSimulacion($codSimulacionServX);
                    $codigoPlantillaXX=obtenerPlantillaCodigoSimulacionServicio($codSimulacionServX);
                    $areaXX=obtenerCodigoAreaPlantillasServicios($codigoPlantillaXX);
                    $unidadXX=obtenerCodigoUnidadPlantillaServicio($codigoPlantillaXX);  
                        if($anio=="all"){

                        }
                        $detalle=obtenerDetalleSolicitudSimulacionCuentaPlantillaServicioFiltro($codSimulacionServX,$codigoPlantillaXX,$anio,$item_detalle,$codigo_detalle);
                        
                        $ibnorca=1;
                        $unidadSol=$codUnidadX;
                        $areaSol=$codAreaX;
						$idFila=1;
                        $desdePropuestas=1;
                       ?><script>numFilas=0;cantidadItems=0;itemFacturas=[];</script><?php
						$cuentasCodigos=[];$conta=0;$auxAnio=0;$detalleAux="";$contAux=0;

						while ($row = $detalle->fetch(PDO::FETCH_ASSOC)) {
							//$cod_plantilladetalle=$row['codigo_detalle'];
							$codigo_fila=explode("###",$row['codigo_detalle']);
							if($codigo_fila[1]=="DET-SIM"){
                             $cod_plantilladetalle=$codigo_fila[0];
                             $cod_plantillauditor="";
                             $solicitudDetalle=obtenerSolicitudRecursosDetallePlantilla(false,$cod_plantilladetalle);
							}else{
                             $cod_plantilladetalle="";
                             $cod_plantillauditor=$codigo_fila[0];
                             $solicitudDetalle=obtenerSolicitudRecursosDetallePlantillaAud(false,$cod_plantillauditor);
							}
							$codCuentaX=$row['codigo'];
							$codDetalleX=0;
							$detalleX=$row['glosa_completa'];
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
							$tituloAnio="Año ".$row['cod_anio'];
                            if($row['cod_anio']<=1){
                              $tituloAnio="Año 1 - Et ".($row['cod_anio']+1);
                            }
							$nombrePartidaX="<b class='text-warning'>".$row['partida']."</b>";
							$nombrePartidaDetalleX="<b class='text-warning'>Cuenta</b> - <b class='text-dark'>".$tituloAnio."</b>";
                            
							
							$entro=0;
                            $sumaImportePres=0;$sumaImportePropuesta=0;$entro2=0;
                            while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                                $sumaImportePres=$rowDetalles['importe_presupuesto'];
                                $sumaImportePropuesta+=$rowDetalles['importe'];
                                $entro2=1;
                            }


                            /*********************  REVISAR ESTO  *******************************/
                            //$entro2 Cambiamos a 0 para que todo entre a la SR y no se restrinja nada
                            $entro2=0;
                            /*********************** FIN REVISAR *******************************/


                            if($entro2==1){
                              if($sumaImportePropuesta<$sumaImportePres){
                                 $importeSolX=$sumaImportePres-$sumaImportePropuesta;
                              }else{
                                $entro=1;
                              }
                            }
							$numeroCuentaX=trim($row['numero']);
							$nombreCuentaX=trim($row['nombre']);
							
							
                         	if($entro==0){
                         		?><script>var nfac=[];itemFacturas.push(nfac);</script><?php
                         		include "addFila.php";
                         		$idFila=$idFila+1;
                         	}		 
						 	 
						 
						}
                        
						

?>
	<script>$("#cantidad_filas").val(<?=($idFila-1)?>);</script>					