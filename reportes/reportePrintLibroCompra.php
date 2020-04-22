<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';

require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$unidad=$_POST["unidad"];
$stringUnidadesX=implode(",", $unidad);

$porcionesFechaDesde = explode("-", $_POST["fecha_desde"]);
$porcionesFechaHasta = explode("-", $_POST["fecha_hasta"]);
$desde=$porcionesFechaDesde[0]."-".$porcionesFechaDesde[1]."-".$porcionesFechaDesde[2];
$hasta=$porcionesFechaHasta[0]."-".$porcionesFechaHasta[1]."-".$porcionesFechaHasta[2];
$fechaTitulo=strftime('%d/%m/%Y',strtotime($desde))." - ".strftime('%d/%m/%Y',strtotime($hasta));
// echo $areaString;
$stringUnidades="";
foreach ($unidad as $valor ) {    
    $stringUnidades.=" ".abrevUnidad($valor)." ";
}

$stmt2 = $dbh->prepare("SELECT f.fecha,f.nit,f.razon_social,f.nro_factura,f.nro_autorizacion,f.codigo_control,f.importe,f.ice,f.exento from facturas_compra f, comprobantes_detalle c where f.cod_comprobantedetalle=c.codigo and c.cod_unidadorganizacional in ($stringUnidadesX) ORDER BY fecha asc");
// Ejecutamos                        
$stmt2->execute();
//resultado
$stmt2->bindColumn('fecha', $fecha);
$stmt2->bindColumn('nit', $nit);
$stmt2->bindColumn('razon_social', $razon_social);
$stmt2->bindColumn('nro_factura', $nro_factura);
$stmt2->bindColumn('nro_autorizacion', $nro_autorizacion);
$stmt2->bindColumn('codigo_control', $codigo_control);
$stmt2->bindColumn('importe', $importe);
$stmt2->bindColumn('ice', $ice);
$stmt2->bindColumn('exento', $exento);          


?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon bg-blanco">
                    <img class="" width="40" height="40" src="../assets/img/logoibnorca.png">
                  </div>
                  <div class="float-right col-sm-2"><h6 class="card-title">Exportar como:</h6></div>
                  <h4 class="card-title text-center">Libro de Compras IVA</h4>                  
                  <h6 class="card-title">Unidad: <?=$stringUnidades;?></h6>
                  <div class="row">
                     <h6 class="card-title col-sm-3">Fecha: <?=$fechaTitulo?></h6>                     
                  </div> 

                </div>
                <div class="card-body">
                  <div class="table-responsive">
      

                        <table id="libro_diario_rep" class="table table-bordered table-condensed" style="width:100%">
                            <thead>
                              <tr>
                                  <th colspan="6" class="text-left"> Razon : IBNORCA<br>Nit : 1020745020</th>   
                                  <th colspan="6" class="text-left"> Nit<br>Nit</th>   
                              </tr>                                         
                              <tr>
                                  <th class="font-weight-bold">-</th>   
                                  <th class="font-weight-bold">Fecha</th>                                
                                  <th class="font-weight-bold">No de NIT</th>
                                  <th class="font-weight-bold">Razón Social o  Nombre del Proveedor</th>
                                  <th class="font-weight-bold">No de FACTURA</th>
                                  <th class="font-weight-bold">No  de Autorización</th>
                                  <th class="font-weight-bold">Código de Control</th>
                                  <th class="font-weight-bold">Total Factura (A)</th>
                                  <th class="font-weight-bold">Total I.C.E (B)</th>
                                  <th class="font-weight-bold">Importes Exentos (C)</th>
                                  <th class="font-weight-bold">Importe Neto Sujeto a IVA (A-B-C)</th>
                                  <th class="font-weight-bold">Crédito Fiscal Obtenido</th>
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
                              while ($row = $stmt2->fetch()) { 
                                $index++;
                                $importe_sujeto_iva=$importe-$ice-$exento;
                                $iva_obtenido=$importe_sujeto_iva*13/100;

                                $total_importe+=$importe;
                                $total_ice+=$ice;
                                $total_exento+=$exento;
                                $total_importe_sujeto_iva+=$importe_sujeto_iva;
                                $total_iva_obtenido+=$iva_obtenido;

                                ?>
                                <tr>
                                  <td class="text-center small"><?=$index;?></td>
                                  <td class="text-center small"><?=$fecha;?></td>
                                  <td class="text-center small"><?=$nit;?></td>
                                  <td class="text-center small"><?=$razon_social;?></td>
                                  <td class="text-center small"><?=$nro_factura;?></td>
                                  <td class="text-center small"><?=$nro_autorizacion;?></td>
                                  <td class="text-center small"><?=$codigo_control;?></td>
                                  <td class="text-center small"><?=formatNumberDec($importe);?></td>
                                  <td class="text-center small"><?=formatNumberDec($ice);?></td>
                                  <td class="text-center small"><?=formatNumberDec($exento);?></td>
                                  <td class="text-center small"><?=formatNumberDec($importe_sujeto_iva);?></td>
                                  <td class="text-center small"><?=formatNumberDec($iva_obtenido);?></td>                                      
                                </tr>
                                <?php                                  
                              }?>
                              <tr>
                                  <td class="text-center small" colspan="7"><b>SubTotal:</b></td>
                                  
                                  <td class="text-center small"><?=formatNumberDec($total_importe);?></td>
                                  <td class="text-center small"><?=formatNumberDec($total_ice);?></td>
                                  <td class="text-center small"><?=formatNumberDec($total_exento);?></td>
                                  <td class="text-center small"><?=formatNumberDec($total_importe_sujeto_iva);?></td>
                                  <td class="text-center small"><?=formatNumberDec($total_iva_obtenido);?></td>                                      
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

