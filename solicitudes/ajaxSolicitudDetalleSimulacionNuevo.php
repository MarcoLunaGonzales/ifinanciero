<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$codSimulacionServX=$_GET['cod_sim'];
$tipoSolicitud =$_GET['tipo'];
                    $codigoPlantillaXX=obtenerPlantillaCodigoSimulacionServicio($codSimulacionServX);
                    $detalle=obtenerDetalleSolicitudSimulacionCuentaPlantillaServicio($codSimulacionServX,$codigoPlantillaXX);
                    $areaXX=obtenerCodigoAreaPlantillasServicios($codigoPlantillaXX);
                    $unidadXX=obtenerCodigoUnidadPlantillaServicio($codigoPlantillaXX);    
                        $ibnorca=1;
                        $unidadSol=0;
                        $areaSol=0;
						$idFila=1;
?><script>numFilas=0;cantidadItems=0;itemFacturas=[];</script><?php
						$cuentasCodigos=[];$conta=0;$auxAnio=-1;$detalleAux="";$contAux=0;$totalImportePres=0;$totalImporteSol=0;

						while ($row = $detalle->fetch(PDO::FETCH_ASSOC)) {
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
							$detalleXX=$row['glosa'];
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
              $tituloAnio="Año ".$row['cod_anio'];
              if($row['cod_anio']<=1){
                $tituloAnio="Año 1 - Et ".($row['cod_anio']+1);
              }
							$nombrePartidaX="<b class='text-warning'>".$row['partida']."</b>";
							$nombrePartidaDetalleX="<b class='text-warning'>Cuenta</b> - <b class='text-dark'>".$tituloAnio."</b>";
                            
							
							$entro=0;
                            while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                            	$entro=1;						   
                            }
							$numeroCuentaX=trim($row['numero']);
							$nombreCuentaX=trim($row['nombre']);							
							if((int)$row['cod_anio']!=$auxAnio){
								$anioSelect=(int)$row['cod_anio'];
                                 ?>
                                 <script>
                                    $('#anio_solicitud').append("<option value='<?=$anioSelect?>' ><?=$tituloAnio?></option>");
                                    $('.selectpicker').selectpicker("refresh");
                                  </script>
						       <?php  				 			
						    }
							$auxAnio=(int)$row['cod_anio'];

							if($detalleXX!=$detalleAux){
								if($entro==0){
								$listaDetalles[$contAux]=$detalleXX;
								$contAux++;			
								}
							}
                         		
							$detalleAux=$detalleXX;

                         	if($entro==0){
                         		?><script>var nfac=[];itemFacturas.push(nfac);</script><?php
                         		include "addFila.php";
                         		$idFila=$idFila+1;
                         	}		 
						 	 
						 
						}
                        
                         
	if(isset($listaDetalles)){
           $resultado = array_unique($listaDetalles);						
           for ($i=0; $i < count($resultado); $i++) { 
                       ?>
                         <script>
                            $('#item_detalle_solicitud').append("<option value='<?=$i?>'><?=$resultado[$i]?></option>");
                            $('.selectpicker').selectpicker("refresh");
                          </script>
						 <?php  	
          }
     }else{
     	?><script>Swal.fire("Sin Datos!", "No se encontraron registros", "warning");</script><?php
     }       
  ?>					
	<script>$("#cantidad_filas").val(<?=($idFila-1)?>)</script>					