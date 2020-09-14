<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';

require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$gestion = $_POST["gestiones"];
$cod_mes_x = $_POST["cod_mes_x"];

$estado=$_POST["estado"];
$stringEstadoX=implode(",", $estado);

$nombre_gestion=nameGestion($gestion);
$nombre_mes=nombreMes($cod_mes_x);

// echo $areaString;
$sql="SELECT f.fecha,DATE_FORMAT(f.fecha,'%d/%m/%Y')as fecha_x,f.nit,f.razon_social,f.nro_factura,f.nro_autorizacion,f.codigo_control,f.importe,f.ice,f.exento,f.tipo_compra 
from facturas_compra f 
join solicitud_recursosdetalle sd on sd.codigo=f.cod_solicitudrecursodetalle
join solicitud_recursos s on s.codigo=sd.cod_solicitudrecurso
where s.cod_estadosolicitudrecurso in ($stringEstadoX) and s.cod_estadoreferencial<>2 and (sd.cod_area=1235 or sd.cod_unidadorganizacional=3000) and MONTH(f.fecha)=$cod_mes_x and YEAR(f.fecha)=$nombre_gestion ORDER BY f.fecha asc";

//echo $sql;
$stmt2 = $dbh->prepare($sql);
// echo $sql;
// Ejecutamos                        
$stmt2->execute();
//resultado
$stmt2->bindColumn('fecha_x', $fecha);
$stmt2->bindColumn('nit', $nit);
$stmt2->bindColumn('razon_social', $razon_social);
$stmt2->bindColumn('nro_factura', $nro_factura);
$stmt2->bindColumn('nro_autorizacion', $nro_autorizacion);
$stmt2->bindColumn('codigo_control', $codigo_control);
$stmt2->bindColumn('importe', $importe);
$stmt2->bindColumn('ice', $ice);
$stmt2->bindColumn('exento', $exento);          
$stmt2->bindColumn('tipo_compra', $tipo_compra);  

//datos de la factura
$stmtPersonal = $dbh->prepare("SELECT * from titulos_oficinas where cod_uo in (5)");
$stmtPersonal->execute();
$result=$stmtPersonal->fetch();
$sucursal=$result['sucursal'];
$direccion=$result['direccion'];
$nit=$result['nit'];
$razon_social=$result['razon_social'];

?>
 <script> 
          gestion_reporte='<?=$nombre_gestion;?>';
          mes_reporte='<?=$nombre_mes;?>';
 </script>
<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon bg-blanco">
                    <img class="" width="60" height="60" src="../assets/img/logo_ibnorca_origen.png">
                  </div>                  
                  <h3 class="card-title text-center" ><b>Revisión Libro de Compras - Proyecto</b>
                    <span><br><h6>
                    Del Período: <?=$nombre_mes;?>/<?=$nombre_gestion;?><br>
                    Expresado En Bolivianos</h6></span></h3>                  
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                        <table id="libro_compras_rep_2" class="table table-bordered table-condensed" style="width:100%">
                            <thead>
                              <tr style="border:2px solid;">
                                  <th colspan="6" class="text-left"><small> Razón Social : <?=$razon_social?><br>Sucursal : <?=$sucursal?></small></th>   
                                  <th colspan="6" class="text-left"><small> Nit : <?=$nit?><br>Dirección : <?=$direccion?></small></th>   
                              </tr>
                              <tr >
                                  <th width="2%" style="border:2px solid;"><small><b>-</b></small></th>   
                                  <th style="border:2px solid;" width="6%"><small><small><b>Fecha</b></small></small></th>                                
                                  <th style="border:2px solid;" width="6%"><small><small><b>NIT</b></small></small></th>
                                  <th style="border:2px solid;" width="20%"><small><small><b>Razón Social </b></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><b>Nro. FACTURA</b></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><b>Nro de Autorización</b></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><b>Código de Control</b></small></small></th>                                  
                                  <th style="border:2px solid;" width="6%"><small><small><b>Total Factura (A)</b></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><small><b>Importe N.S.C.F. (B)</b></small></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><small><b>Subtotal (C=A-B)</b></small></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><small><b>Desc, Bon, Reb (D)</b></small></small></small></th>
                                  <th style="border:2px solid;" width="10%"><small><small><small><b>Importe Base para Crédito Fiscal (E=C-D)</b></small></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><small><b>Crédito Fiscal (F=E*13%)</b></small></small></small></th>
                              </tr>                                  
                            </thead>
                            <tbody>
                              <?php
                              $index=0; 
                              $total_importe=0;
                              $total_ice=0;
                              $total_exento=0;
                              $total_importe_sujeto_iva=0;
                              $total_iva_obtenido=0;
                              $total_subtotal=0;
                              $total_descuento=0;
                              $total_base=0;
                              $total_credito_fiscal=0;
                              while ($row = $stmt2->fetch()) { 
                                $index++;
                                // $importe_sujeto_iva=$importe-$ice-$exento;
                                $importe_sujeto_iva=$importe-$ice-$exento;;
                                $iva_obtenido=$importe_sujeto_iva*13/100;
                                $caracter=substr($codigo_control, -1);
                                if($caracter=='-'){
                                  $codigo_control=trim($codigo_control, '-');
                                }
                                

                                $total_importe+=$importe;
                                $total_ice+=$ice;
                                $total_exento+=$exento;
                                $total_importe_sujeto_iva+=$importe_sujeto_iva;
                                $total_iva_obtenido+=$iva_obtenido;

                                // $sumadeimporte=$importe+$ice+$exento;
                                $sumadeimporte=$importe;
                                if(trim($codigo_control)==""){
                                  $codigo_control="0";
                                }

                                $subtotal=$sumadeimporte-$sumadeimporte;
                                $descuento=0;
                                $importeBaseCF=$subtotal-$descuento;
                                $credito_fiscal=$importeBaseCF*0.13;

                                $total_subtotal+=$subtotal;
                                $total_descuento+=$descuento;
                                $total_base+=$importeBaseCF;
                                $total_credito_fiscal+=$credito_fiscal;

                                ?>
                                <tr>
                                  <td class="text-center small"><?=$index;?></td>
                                  <td class="text-center small"><?=$fecha;?></td>
                                  <td class="text-right small"><?=$nit;?></td>
                                  <td class="text-left small"><span style="padding-left: 15px;"><?=strtoupper($razon_social);?></span></td>
                                  <td class="text-right small"><?=$nro_factura;?></td>
                                  <td class="text-right small"><?=$nro_autorizacion;?></td>
                                  <td class="text-center small"><?=$codigo_control;?></td><!--
                                  <td class="text-right small"><?=formatNumberDec($sumadeimporte);?></td>
                                  <td class="text-right small"><?=formatNumberDec($ice);?></td>
                                  <td class="text-right small"><?=formatNumberDec($exento);?></td>
                                  <td class="text-right small"><?=formatNumberDec($importe_sujeto_iva);?></td>
                                  <td class="text-right small"><?=formatNumberDec($iva_obtenido);?></td>-->
                                  <td class="text-right small"><?=formatNumberDec($sumadeimporte);?></td>
                                  <td class="text-right small"><?=formatNumberDec($sumadeimporte);?></td>
                                  <td class="text-right small"><?=formatNumberDec($subtotal);?></td>
                                  <td class="text-right small"><?=formatNumberDec($descuento);?></td>
                                  <td class="text-right small"><?=formatNumberDec($importeBaseCF);?></td> 
                                  <td class="text-right small"><?=formatNumberDec($credito_fiscal);?></td>                                         
                                </tr>
                                <?php                                  
                              }?>
                              <tr style="border:2px solid;">                               
                                 <td class="text-left small" colspan="3" style="border:2px solid;">CI:</td>
                                  <td class="text-left small" colspan="3" style="border:2px solid;">Nombre del Responsable:</td>
                                  <td class="text-center small"><b>SubTotal:</b></td>                                  
                                  <td class="text-right small"><?=formatNumberDec($total_importe);?></td>
                                  <td class="text-right small"><?=formatNumberDec($total_importe);?></td>
                                  <td class="text-right small"><?=formatNumberDec($total_subtotal);?></td>
                                  <td class="text-right small"><?=formatNumberDec($total_descuento);?></td>
                                  <td class="text-right small"><?=formatNumberDec($total_base);?></td>
                                  <td class="text-right small"><?=formatNumberDec($total_credito_fiscal);?></td>                                      
                                </tr>
                            </tbody>
                        </table>

                    

                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>

