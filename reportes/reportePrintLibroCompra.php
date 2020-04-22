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
$fechaTitulo="De ".strftime('%d/%m/%Y',strtotime($desde))." a ".strftime('%d/%m/%Y',strtotime($hasta));
// echo $areaString;
$stringUnidades="";
foreach ($unidad as $valor ) {    
    $stringUnidades.=" ".abrevUnidad($valor)." ";
}

$stmt2 = $dbh->prepare("SELECT f.fecha,f.nit,f.razon_social,f.nro_factura,f.nro_autorizacion,f.codigo_control,f.importe,f.ice,f.exento,f.tipo_compra from facturas_compra f, comprobantes_detalle c where f.cod_comprobantedetalle=c.codigo and c.cod_unidadorganizacional in ($stringUnidadesX) ORDER BY fecha asc");
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
$stmt2->bindColumn('tipo_compra', $tipo_compra);  

//datos de la factura
$stmtPersonal = $dbh->prepare("SELECT * from titulos_oficinas where cod_uo in ($stringUnidadesX)");
$stmtPersonal->execute();
$result=$stmtPersonal->fetch();
$sucursal=$result['sucursal'];
$direccion=$result['direccion'];
$nit=$result['nit'];
$razon_social=$result['razon_social'];

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
                     <h6 class="card-title col-sm-3"><?=$fechaTitulo?></h6>                     
                  </div> 

                </div>
                <div class="card-body">
                  <div class="table-responsive">
                        <table id="libro_diario_rep" class="table table-bordered table-condensed" style="width:100%">
                            <thead>
                              <tr>
                                  <th colspan="6" class="text-left"><small><b> Razón Social : <?=$razon_social?><br>Sucursal : <?=$sucursal?></b></small></th>   
                                  <th colspan="6" class="text-left"><small><b> Nit : <?=$nit?><br>Dirección : <?=$direccion?></b></small></th>   
                              </tr>                                         
                              <tr>
                                  <th><small><b>-</b></small></th>   
                                  <th><small><b>Fecha</b></small></th>                                
                                  <th><small><b>NIT</b></small></th>
                                  <th><small><b>Razón Social </b></small></th>
                                  <th><small><b>No de FACTURA</b></small></th>
                                  <th><small><b>No  de Autorización</b></small></th>
                                  <th><small><b>Código de Control</b></small></th>                                  
                                  <th><small><b>Total Factura (A)</b></small></th>
                                  <th><small><b>Total I.C.E (B)</b></small></th>
                                  <th><small><b>Importes Exentos (C)</b></small></th>
                                  <th><small><b>Importe Neto Sujeto a IVA (A-B-C)</b></small></th>
                                  <th><small><b>Crédito Fiscal Obtenido</b></small></th>
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
                                $caracter=substr($codigo_control, -1);
                                if($caracter=='-'){
                                  $codigo_control=trim($codigo_control, '-');
                                }
                                

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
                                  <td class="text-left small"><?=$razon_social;?></td>
                                  <td class="text-center small"><?=$nro_factura;?></td>
                                  <td class="text-center small"><?=$nro_autorizacion;?></td>
                                  <td class="text-left small"><?=$codigo_control;?></td>
                                  <td class="text-right small"><?=formatNumberDec($importe);?></td>
                                  <td class="text-right small"><?=formatNumberDec($ice);?></td>
                                  <td class="text-right small"><?=formatNumberDec($exento);?></td>
                                  <td class="text-right small"><?=formatNumberDec($importe_sujeto_iva);?></td>
                                  <td class="text-right small"><?=formatNumberDec($iva_obtenido);?></td>                                      
                                </tr>
                                <?php                                  
                              }?>
                              <tr>
                                  <td class="text-center small" colspan="7"><b>SubTotal:</b></td>
                                  
                                  <td class="text-right small"><?=formatNumberDec($total_importe);?></td>
                                  <td class="text-right small"><?=formatNumberDec($total_ice);?></td>
                                  <td class="text-right small"><?=formatNumberDec($total_exento);?></td>
                                  <td class="text-right small"><?=formatNumberDec($total_importe_sujeto_iva);?></td>
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

