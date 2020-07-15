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

$nombre_gestion=nameGestion($gestion);
$nombre_mes=nombreMes($cod_mes_x);

$stmt2 = $dbh->prepare("SELECT *,DATE_FORMAT(fecha_factura,'%d/%m/%Y')as fecha_factura_x from facturas_venta where MONTH(fecha_factura)=$cod_mes_x and YEAR(fecha_factura)=$nombre_gestion");
$stmt2->execute();
//resultado
$stmt2->bindColumn('codigo', $codigo);
$stmt2->bindColumn('cod_sucursal', $cod_sucursal);
$stmt2->bindColumn('cod_solicitudfacturacion', $cod_solicitudfacturacion);
$stmt2->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmt2->bindColumn('fecha_factura_x', $fecha_factura);
$stmt2->bindColumn('fecha_limite_emision', $fecha_limite_emision);
$stmt2->bindColumn('cod_tipoobjeto', $cod_tipoobjeto);
$stmt2->bindColumn('cod_tipopago', $cod_tipopago);
$stmt2->bindColumn('cod_cliente', $cod_cliente);
$stmt2->bindColumn('cod_personal', $cod_personal);
$stmt2->bindColumn('razon_social', $razon_social);
$stmt2->bindColumn('nit', $nit);
$stmt2->bindColumn('cod_dosificacionfactura', $cod_dosificacionfactura);
$stmt2->bindColumn('nro_factura', $nro_factura);
$stmt2->bindColumn('nro_autorizacion', $nro_autorizacion);
$stmt2->bindColumn('codigo_control', $codigo_control);
$stmt2->bindColumn('importe', $importe);
$stmt2->bindColumn('observaciones', $observaciones);
$stmt2->bindColumn('cod_estadofactura', $cod_estadofactura);
$stmt2->bindColumn('cod_comprobante', $cod_comprobante);
?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">                  
                  <h4 class="card-title">
                    <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:250px;">
                      Libro de Ventas
                  </h4>
                  <!-- <h4 class="card-title text-center">Reporte De Activos Fijos Por Unidad</h4> -->
                  <h6 class="card-title">
                    Gestión: <?=$nombre_gestion;?><br>
                    Mes: <?=$nombre_mes;?><br>
                  </h6>                  
                </div>
                <div class="card-body">
                  <div class="table-responsive">                  
                    <table id="ventas_rep" class="table table-bordered table-condensed" style="width:100%">
                        <thead>                          
                          <tr style="background-color:#D8D8D8;">
                            <td><small><b>Esp.</b></small></td>
                            <td><small><b>Nro.</b></small></td>                            
                            <td><small><b>Fecha<br>Factura</b></small></td>
                            <td><small><b>Nro.<br>Factura</b></small></td>
                            <td><small><b>Estado</b></small></td>
                            <td><small><b>Nit/CI<br>Cliente</b></small></td>
                            <td><small><b>Nombre o<br>Razón Social</b></small></td>
                            <td><small><b>Importe Total<br>Venta A</b></small></td>
                            <td><small><b>Importe<br> otros no sujetos a iva B</b></small></td>
                            <td><small><b>Exportanciones<br> y Operaciones Extentas C</b></small></td>
                            <td><small><b>Ventas Gravadas<br> a tasa Cero D</b></small></td>
                            <td><small><b>Subtotal E=A-B-C-D</b></small></td>
                            <td><small><b>Descuentos,<br> Bonificaciones y<br> Rebajas sujetos al IVA F</b></small></td>
                            <td><small><b>Importe Débito <br>Fiscal G=E-F</b></small></td>
                            <td><small><b>Débito Fiscal<br> H=G*13%</b></small></td>
                            <td><small><b>Código Control</b></small></td>
                            <td><small><b>Nro. Autorización</b></small></td>

                          </tr>                                
                        </thead>
                        <tbody>
                          <?php                          
                          $index=1;
                          $total_importe=0;
                          $total_importe_no_iva=0;
                          $total_extento=0;
                          $total_ventas_gravadas=0;
                          $total_subtotal=0;
                          $total_rebajas_sujetos_iva=0;
                          $total_importe_debito_fiscal=0;
                          $total_debito_fiscal=0;
                          while ($row = $stmt2->fetch()) {                             
                            switch ($cod_estadofactura) {
                              case 1:
                                $btnEstado='<span class="badge badge-success">';
                              break;
                              case 2:
                                $btnEstado='<span class="badge badge-danger">';
                                $razon_social="ANULADO";
                                $importe=0;
                                $codigo_control=0;
                                $nit=0;
                                $fecha_factura=0;
                              break;
                              case 3:
                                $btnEstado='<span class="badge badge-success">';
                                $cod_estadofactura=1;
                              break;
                              case 4:
                                $btnEstado='<span class="badge badge-default">';
                                $cod_estadofactura=1;
                            }
                            $nombre_estado=nameEstadoFactura($cod_estadofactura);


                            $importe_no_iva=0;
                            $extento=0;
                            $ventas_gravadas=0;
                            $rebajas_sujetos_iva=0;
                            $subtotal=$importe-$importe_no_iva-$extento-$ventas_gravadas;
                            $importe_debito_fiscal=$subtotal-$rebajas_sujetos_iva;
                            $debito_fiscal=13*$importe_debito_fiscal/100;

                            $total_importe+=$importe;
                            $total_importe_no_iva+=$importe_no_iva;
                            $total_extento+=$extento;
                            $total_ventas_gravadas+=$ventas_gravadas;
                            $total_subtotal+=$subtotal;
                            $total_rebajas_sujetos_iva+=$rebajas_sujetos_iva;
                            $total_importe_debito_fiscal+=$importe_debito_fiscal;
                            $total_debito_fiscal+=$debito_fiscal;

                            ?>
                              <!-- el ultimo no sale -->
                            <tr>                                
                                <td class="text-center small">3</td>
                                <td class="text-center small"><?=$index?></td>
                                <td class="text-center small"><?=$fecha_factura; ?></td>
                                <td class="text-center small"><?=$nro_factura ?></td>
                                <td class="text-center small"><?=$btnEstado.$nombre_estado."</span>";?></td>
                                <td class="text-center small"><?=$nit?></td>
                                <td class="text-left small"><small><?=mb_strtoupper($razon_social,'utf-8');?></small></td>
                                <td class="text-center small"><?=formatNumberDec($importe); ?></td>
                                <td class="text-center small"><?=formatNumberDec($importe_no_iva); ?></td>
                                <td class="text-center small"><?=formatNumberDec($extento); ?></td>
                                <td class="text-center small"><?=formatNumberDec($ventas_gravadas); ?></td>
                                <td class="text-center small"><?=formatNumberDec($subtotal); ?></td>
                                <td class="text-center small"><?=formatNumberDec($rebajas_sujetos_iva); ?></td>
                                <td class="text-center small"><?=formatNumberDec($importe_debito_fiscal); ?></td>
                                <td class="text-center small"><?=formatNumberDec($debito_fiscal); ?></td>
                                <td class="text-center small"><?=$codigo_control?></td>
                                <td class="text-center small"><?=$nro_autorizacion?></td>
                            </tr>
                          <?php $index++; } ?>

                            <tr class="bg-dark text-white">
                                <td class="text-center small" colspan="7">TOTAL</td>                                
                                <td class="text-center small"><?=formatNumberDec($total_importe); ?></td>
                                <td class="text-center small"><?=formatNumberDec($total_importe_no_iva); ?></td>
                                <td class="text-center small"><?=formatNumberDec($total_extento); ?></td>
                                <td class="text-center small"><?=formatNumberDec($total_ventas_gravadas); ?></td>
                                <td class="text-center small"><?=formatNumberDec($total_subtotal); ?></td>
                                <td class="text-center small"><?=formatNumberDec($total_rebajas_sujetos_iva); ?></td>
                                <td class="text-center small"><?=formatNumberDec($total_importe_debito_fiscal); ?></td>
                                <td class="text-center small"><?=formatNumberDec($total_debito_fiscal); ?></td>
                                <td class="text-center small"> - </td>
                                <td class="text-center small"> - </td>
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

