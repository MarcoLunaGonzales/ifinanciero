<?php
session_start();
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../functions.php';
require_once '../styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalMesActivo=$_SESSION['globalMes'];
$userAdmin=obtenerValorConfiguracion(74);

$codigoLibreta=$_POST['codigo_libreta'];

$dbh = new Conexion();
?>
<table id="libreta_bancaria_reporte_modal" class="table table-condensed small">          
            <thead>
              <tr style="background:#DAF7A6; color:#000;">
                <th>#</th>
                <th>Fecha</th>
                <th>N°</th>            
                <th>Razón Social</th> 
                <th>Nit</th>
                <th>Importe</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $stmt = $dbh->prepare("SELECT codigo,fecha_factura,date_format(fecha_factura,'%d/%m/%Y') as fecha_x,razon_social,nit,nro_factura,importe,cod_libretabancariadetalle from facturas_venta where cod_estadofactura!=2 order by codigo desc");
              $stmt->execute();
              $stmt->bindColumn('codigo', $codigo_x);
              $stmt->bindColumn('fecha_x', $fecha_factura_x);
              $stmt->bindColumn('razon_social', $razon_social_x);
              $stmt->bindColumn('nit', $nit_x);
              $stmt->bindColumn('nro_factura', $nro_factura_x);
              $stmt->bindColumn('importe', $importe_x);
              // $stmt->bindColumn('cod_libretabancariadetalle', $cod_libretabancariadetalle_x);
              $index=1;
              while ($rowTC = $stmt->fetch(PDO::FETCH_BOUND)) {
                $cod_libretabancariadetalle_x=verificar_cod_libretadetalle($codigo_x);
                $color_tr="";$label="btn btn-fab btn-success btn-sm";
                if($cod_libretabancariadetalle_x>0){$color_tr="background-color:#f6ddcc;";$label="btn btn-fab btn-warning btn-sm";}
                ?>
                <tr style="<?=$color_tr?>">
                  <td align="text-center small"><?=$index;?></td>
                  <td align="text-center small"><?=$fecha_factura_x;?></td>
                  <td align="text-right small"><?=$nro_factura_x;?></td>
                  <td align="text-left small"><?=$razon_social_x;?></td>
                  <td align="text-right small"><?=$nit_x;?></td>
                  <td align="text-right small"><?=number_format($importe_x,2);?></td>
                  <td class="td-actions text-right"><a href="#" style="padding: 0;font-size:10px;width:25px;height:25px;" onclick="seleccionar_Factura_relacion(<?=$codigo_x?>)" class="<?=$label?>" title="Seleccionar Factura"><i class="material-icons">done</i></a></td>
                </tr>
              <?php $index++;} ?>
            </tbody>
            <tfoot>
              <tr style="background:#DAF7A6; color:#000;">
                <td>#</td>
                <th class="small">Fecha</th>
                <th class="small">N°</th>            
                <th class="small">Razón Social</th> 
                <th class="small">Nit</th>
                <th class="small">Importe</th>
                <th class="small"></th>
              </tr>
             </tfoot>
          </table>
              