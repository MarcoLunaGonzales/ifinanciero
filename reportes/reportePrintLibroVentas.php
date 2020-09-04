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
$unidad=$_POST["unidad"];
$stringUnidadesX=implode(",", $unidad);
$nombre_gestion=nameGestion($gestion);
$nombre_mes=nombreMes($cod_mes_x);
//para la razon social
if (isset($_POST["check_rs_librocompras"])) {
  $check_rs_librocompras=$_POST["check_rs_librocompras"]; 
  if($check_rs_librocompras){
    $razon_social=$_POST["razon_social"]; 
    $sql_rs=" and razon_social like '%$razon_social%'";
  }else{
    $sql_rs="";
  }
}else{
  $sql_rs="";
}

$stmt2 = $dbh->prepare("SELECT *,DATE_FORMAT(fecha_factura,'%d/%m/%Y')as fecha_factura_x from facturas_venta where MONTH(fecha_factura)=$cod_mes_x and YEAR(fecha_factura)=$nombre_gestion and cod_unidadorganizacional in ($stringUnidadesX) $sql_rs ORDER BY nro_factura asc");
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
$stmt2->bindColumn('observaciones', $observaciones);
$stmt2->bindColumn('cod_estadofactura', $cod_estadofactura);
$stmt2->bindColumn('cod_comprobante', $cod_comprobante);

//datos de la factura
$stmtPersonal = $dbh->prepare("SELECT * from titulos_oficinas where cod_uo in (5)");
$stmtPersonal->execute();
$result=$stmtPersonal->fetch();
$sucursal=$result['sucursal'];
$direccion=$result['direccion'];
$nit=$result['nit'];
$razon_social_titulo=$result['razon_social'];
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
                  <h3 class="card-title text-center" ><b>Libro de Ventas</b>
                    <span><br><h6>
                    Del Período: <?=$nombre_mes;?>/<?=$nombre_gestion;?><br>
                    Expresado En Bolivianos</h6></span></h3>                  
                  <!-- <h6 class="card-title">Unidad: <?=$stringUnidades;?></h6> -->
                </div>
                <div class="card-body">
                  <div class="table-responsive">                  
                    <table id="libro_ventas_rep_2" class="table table-bordered table-condensed" style="width:100%">
                        <thead>  
                          <tr style="border:2px solid;">
                            <th colspan="9" class="text-left"><small> Razón Social : <?=$razon_social_titulo?><br>Sucursal : <?=$sucursal?></small></th>   
                            <th colspan="8" class="text-left"><small> Nit : <?=$nit?><br>Dirección : <?=$direccion?></small></th>   
                          </tr>                        
                          <tr>
                            <td style="border:2px solid;"><small><b>Esp.</b></small></td>
                            <td style="border:2px solid;"><small><b>-</b></small></td>                            
                            <td style="border:2px solid;"><small><b>Fecha</b></small></td>
                            <td style="border:2px solid;"><small><b>Nro.<br>Factura</b></small></td>
                            <td style="border:2px solid;"><small><b>Estado</b></small></td>
                            <td style="border:2px solid;"><small><b>Nit/CI<br>Cliente</b></small></td>
                            <td style="border:2px solid;"><small><b>Nombre o<br>Razón Social</b></small></td>
                            <td style="border:2px solid;"><small><b>Código Control</b></small></td>
                            <td style="border:2px solid;"><small><b>Nro. Autorización</b></small></td>
                            <td style="border:2px solid;" width="6%"><small><b>Importe Total<br> Venta (A)</b></small></td>
                            <td style="border:2px solid;" width="6%"><small><b>Importe<br> otros no sujetos a iva (B)</b></small></td>
                            <td style="border:2px solid;" width="6%"><small><b>Export.<br> y Operac. Extentas (C)</b></small></td>
                            <td style="border:2px solid;" width="6%"><small><b>Ventas Gravadas<br> a tasa Cero (D)</b></small></td>
                            <td style="border:2px solid;"><small><b>Subtotal <br>E=A-B-C-D</b></small></td>
                            <td style="border:2px solid;" width="6%"><small><b>Desc., Bonif. y<br> Rebajas sujetos al IVA <br>(F)</b></small></td>
                            <td style="border:2px solid;" width="5%"><small><b>Importe Débito <br>Fiscal (G=E-F)</b></small></td>
                            <td style="border:2px solid;" width="5%"><small><b>Débito Fiscal<br> (H=G*13%)</b></small></td>
                            

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
                            $importe=sumatotaldetallefactura($codigo);
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
                               // $fecha_factura=0;
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
                                <td class="text-right small"><?=$nro_factura ?></td>
                                <td class="text-center small"><?=$nombre_estado;?></td>
                                <td class="text-right small"><?=$nit?></td>                                
                                <td class="text-left small"><small><span style="padding-left: 15px;"><?=mb_strtoupper($razon_social,'utf-8');?></small></span></td>
                                <td class="text-center small"><?=$codigo_control?></td>
                                <td class="text-right small"><?=$nro_autorizacion?></td>
                                <td class="text-right small"><?=formatNumberDec($importe); ?></td>
                                <td class="text-right small"><?=formatNumberDec($importe_no_iva); ?></td>
                                <td class="text-right small"><?=formatNumberDec($extento); ?></td>
                                <td class="text-right small"><?=formatNumberDec($ventas_gravadas); ?></td>
                                <td class="text-right small"><?=formatNumberDec($subtotal); ?></td>
                                <td class="text-right small"><?=formatNumberDec($rebajas_sujetos_iva); ?></td>
                                <td class="text-right small"><?=formatNumberDec($importe_debito_fiscal); ?></td>
                                <td class="text-right small"><?=formatNumberDec($debito_fiscal); ?></td>
                                
                            </tr>
                          <?php $index++; } ?>

                            <tr style="border:2px solid;">
                                <!-- <td class="d-none"></td>
                                <td class="d-none"></td>
                                <td class="d-none"></td>
                                <td class="d-none"></td>
                                <td class="d-none"></td>
                                <td class="d-none"></td>
                                <td class="d-none"></td>
                                <td class="d-none"></td> -->
                                <td class="text-left small" colspan="4" style="border:2px solid;">CI:</td>
                                <td class="text-left small" colspan="4" style="border:2px solid;">Nombre Responsable:</td>
                                <td class="text-center small">TOTAL</td>                                
                                <td class="text-right small"><?=formatNumberDec($total_importe); ?></td>
                                <td class="text-right small"><?=formatNumberDec($total_importe_no_iva); ?></td>
                                <td class="text-right small"><?=formatNumberDec($total_extento); ?></td>
                                <td class="text-right small"><?=formatNumberDec($total_ventas_gravadas); ?></td>
                                <td class="text-right small"><?=formatNumberDec($total_subtotal); ?></td>
                                <td class="text-right small"><?=formatNumberDec($total_rebajas_sujetos_iva); ?></td>
                                <td class="text-right small"><?=formatNumberDec($total_importe_debito_fiscal); ?></td>
                                <td class="text-right small"><?=formatNumberDec($total_debito_fiscal); ?></td>
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

