<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$codSimulacionServX=$_GET['cod_sim'];
$tipoSolicitud =$_GET['tipo'];

                    $codigoPlantillaXX=obtenerPlantillaCodigoSimulacion($codSimulacionServX);
                    $detalle=obtenerDetalleSolicitudSimulacionCuentaPlantilla($codSimulacionServX,$codigoPlantillaXX);
                    $areaXX=obtenerCodigoAreaPlantillasCosto($codigoPlantillaXX);
                    $unidadXX=obtenerCodigoUnidadPlantillaCosto($codigoPlantillaXX);    
                    $ibnorca=1;
                    $unidadSol=0;
                    $areaSol=0;
						        $idFila=1;
                    $desdePropuestas=1;
                   ?><script>numFilas=0;cantidadItems=0;itemFacturas=[];</script><?php
						       $cuentasCodigos=[];$conta=0;$auxAnio=-1;$detalleAux="";$contAux=0;$totalImportePres=0;$totalImporteSol=0;

						       while ($row = $detalle->fetch(PDO::FETCH_ASSOC)) {
							       //$codigo_fila=explode("###",$row['codigo_detalle']);
							       /*if($codigo_fila[1]=="DET-SIM"){
                          $cod_plantilladetalle=$codigo_fila[0];
                          $cod_plantillauditor="";
                          $solicitudDetalle=obtenerSolicitudRecursosDetallePlantilla(false,$cod_plantilladetalle);
							        }else{
                          $cod_plantilladetalle="";
                          $cod_plantillauditor=$codigo_fila[0];
                          $solicitudDetalle=obtenerSolicitudRecursosDetallePlantillaAud(false,$cod_plantillauditor);
							        }*/

                      $cod_plantilladetalle=$row['codigo_detalle'];
                      $cod_plantillauditor="";
                      $solicitudDetalle=obtenerSolicitudRecursosDetallePlantilla(false,$cod_plantilladetalle);

						        	$codCuentaX=$row['codigo'];
						        	$codDetalleX=0;
						        	$detalleX=$row['glosa'];
						        	$detalleXX=$row['glosa'];
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

                     //
                      $tituloAnio="";
                     /*if($row['cod_anio']<=1&&$areaXX==38){
                       $tituloAnio="Año 1 - Et ".($row['cod_anio']+1);
                     }*/
						      	$nombrePartidaX="<b class='text-warning'>".$row['partida']."</b>";
						      	$nombrePartidaDetalleX="<b class='text-warning'>Cuenta</b> - <b class='text-dark'>".$tituloAnio."</b>";                            
							
						      	$entro=0;

                    $sumaImportePres=0;$sumaImportePropuesta=0;$entro2=0;
                    while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                        $sumaImportePres=$rowDetalles['importe_presupuesto'];
                        $sumaImportePropuesta+=$rowDetalles['importe'];
                        $entro2=1;
                    }
                    if($codCuentaX==obtenerValorConfiguracion(88)){
                       $sumaImportePres=obtenerDatosContratoSolicitudCapacitacion($codSimulacionServX)[0];
                       $proveedorX=obtenerDatosContratoSolicitudCapacitacion($codSimulacionServX)[1];
                       if($proveedorX>0){
                           $conContrato=1;
                           $glosaContrato=obtenerDatosContratoSolicitudCapacitacion($codSimulacionServX)[2]." ".obtenerDatosContratoSolicitudCapacitacion($codSimulacionServX)[3];
                           $detalleX.=" ".$glosaContrato;  
                       }
                    }

                    if($entro2==1){
                      if($sumaImportePropuesta<$sumaImportePres){
                         $importeSolX=$sumaImportePres-$sumaImportePropuesta;
                      }else{
                        $entro=1;
                      }
                    }else{
                      if($codCuentaX==obtenerValorConfiguracion(88)){
                          $importeSolX=obtenerDatosContratoSolicitudCapacitacion($codSimulacionServX)[0];
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
              
            //insertamos datos en el select            
            $glosas=obtenerGlosaSolicitudSimulacionCuentaPlantillaCosto($codSimulacionServX,$codigoPlantillaXX);
            $i=0;  
            while ($rowglosa = $glosas->fetch(PDO::FETCH_ASSOC)) {
              $detalleDes=$rowglosa['glosa'];
               ?>
                  <script>
                     $('#item_detalle_solicitud').append("<option value='<?=$i?>'><?=$detalleDes?></option>");
                     $('.selectpicker').selectpicker("refresh");
                   </script>
             <?php
             $i++;
            }

            //fin de selects
     if($idFila==0){
          ?><script>Swal.fire("Sin Datos!", "No se encontraron registros", "warning");</script><?php  
     }       
  ?>					
<script>
 $("#cantidad_filas").val(<?=($idFila-1)?>);
 cargarArrayAreaDistribucion(<?=$unidadXX?>);
</script>					