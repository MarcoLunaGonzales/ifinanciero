<script> periodo_mayor='<?=$periodoTitle?>';
          cuenta_mayor='';
          unidad_mayor='';
 </script>
<style>
tfoot input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
</style>
<?php
switch ($filtro) {
  case 0:$tituloFiltros="Ver Todo";break;
  case 1:$tituloFiltros="Ver Solo Registros Relacionados";break;
  case 2:$tituloFiltros="Ver Solo Registros Pendientes de Identificación";break;
  default:$tituloFiltros="Ver Todo";break;
}
 ?>
<div class="card-body">
  <h6 class="card-title">Periodo Libretas: <?=$periodoTitle?></h6>
  <h6 class="card-title">Periodo Facturas y/o Comprobantes: <?=$periodoTitleFac?></h6>
  <h6 class="card-title">Libretas Bancarias: <?=$stringEntidades;?></h6>
  <h6 class="card-title">Filtro: <?=$tituloFiltros;?></h6>
  <div class="col-sm-4 float-right">
    <div class="row">
        <label class="col-sm-4 col-form-label">Total Monto</label>
        <div class="col-sm-8">
          <div class="form-group">
             <input class="form-control" placeholder="Calculando..." readonly value="" id="total_reporte">                   
          </div>  
        </div>     
      </div>  
    </div>
  <div class="table-responsive col-sm-12"> 
    <table id="libreta_bancaria_reporte" class="table table-condensed small" style="width:100% !important;">
      <thead>
        <tr style="background:#21618C; color:#fff;">
          <td>Fecha</td>
          <td>Hora</td>
          <td width="35%">Descripción</td>
          <!--<td>Información C.</td>-->
          <td>Sucursal</td>
          <td>Monto</td>
          <td>Saldo</td>
          <td width="10%">Nro Doc / Nro Ref</td>
          <!--<td width="10%"><a href="#" id="minus_tabla_lib" title="Abrir/Cerrar Facturas" class="text-white float-right"><i class="material-icons">switch_left</i></a>Estado</td>-->
          <td class="bg-success">Fecha</td>
          <td class="bg-success">Numero</td>
          <td class="bg-success">NIT</td>
          <td class="bg-success">Razon Social</td>
          <td width="10%" class="bg-success">Detalle</td>
          <td class="bg-success">Monto</td>
          
        </tr>
      </thead> 
      <?php
      $html='<tbody>';
      $sqlDetalle="SELECT ce.*
      FROM libretas_bancariasdetalle ce join libretas_bancarias lb on lb.codigo=ce.cod_libretabancaria where lb.codigo in ($StringEntidadCodigos) and ce.fecha_hora BETWEEN '$fecha 00:00:00' and '$fechaHasta 23:59:59' and ce.cod_estadoreferencial=1 order by ce.fecha_hora";
      $stmt = $dbh->prepare($sqlDetalle);
      // echo $sqlDetalle;
      // Ejecutamos
      $stmt->execute();
      // bindColumn
      $stmt->bindColumn('codigo', $codigo);
      $stmt->bindColumn('descripcion', $descripcion);
      $stmt->bindColumn('informacion_complementaria', $informacion_complementaria);
      $stmt->bindColumn('agencia', $agencia);
      $stmt->bindColumn('nro_cheque', $nro_cheque);
      $stmt->bindColumn('nro_documento', $nro_documento);
      $stmt->bindColumn('fecha_hora', $fecha);
      $stmt->bindColumn('monto', $monto);
      $stmt->bindColumn('cod_comprobante', $codComprobante);
      $stmt->bindColumn('cod_comprobantedetalle', $codComprobanteDetalle);
      // $stmt->bindColumn('cod_factura', $codFactura);
      // $stmt->bindColumn('monto_fac', $montoFac);
      $index=1;$totalMonto=0;$totalMontoFac=0;$montoMonto=0;
      while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
        $entro=0; 
        $verificar=1;
        //if($filtro==1){          
          //$verificar=verificarCodFactura($codigo);
        //}
        if($verificar==1){          
          // echo $verificar;
          $sqlFacturas="SELECT lf.cod_facturaventa,(SELECT sum((SELECT SUM((fd.cantidad*fd.precio)-fd.descuento_bob) from facturas_ventadetalle fd where fd.cod_facturaventa=f.codigo)) from facturas_venta f where f.codigo=lf.cod_facturaventa and f.cod_estadofactura!=2 $sqlFiltro2)as monto_fac From libretas_bancariasdetalle_facturas lf join facturas_venta f on f.codigo=lf.cod_facturaventa where lf.cod_libretabancariadetalle=$codigo and f.cod_estadofactura<>2 limit 1";
          $stmtFacturas = $dbh->prepare($sqlFacturas);
          // echo $sqlFacturas;
          $stmtFacturas->execute();        
          $codFactura=0;
          $montoFac=0;
          while ($resultFacturas = $stmtFacturas->fetch(PDO::FETCH_ASSOC)) {
              $codFactura=$resultFacturas['cod_facturaventa'];
              $montoFac=$resultFacturas['monto_fac'];
          }
          
          $cant=obtenerCantidadFacturasLibretaBancariaDetalle($codigo,$sqlFiltro2);
          $cant2=obtenerCantidadComprobanteLibretaBancariaDetalle($codigo,$sqlFiltroComp);
          $saldoAux=$monto-$montoFac;
          if($filtro==1){ //solo relacionados
             if($cant>0||$cant2>0){
              $entro=1;
             }   
          }else if($filtro==2){//solo pendientes
             if($cant==0&&$cant2==0){
              $entro=1;
             }
          }else{ //filtro Mostrar TODO
            $entro=1;
          }
          
          $saldo=obtenerSaldoLibretaBancariaDetalleFiltro($codigo,$sqlFiltroSaldo,$monto);
          if($entro==1){
            if($codFactura==""||$codFactura==0||$codFactura==null){
              if(!($codComprobante==""||$codComprobante==0)){
                  $datosDetalle=obtenerDatosComprobanteDetalleFechas($codComprobanteDetalle,$sqlFiltroComp);                    
                  if($datosDetalle[1]!=''){
                      $saldo=0;
                   }
               }
            }   
            $totalMonto+=(float)$saldo;
            $montoMonto+=(float)$monto;
            ?>
            <tr>
              <td class="text-center font-weight-bold"><?=strftime('%d/%m/%Y',strtotime($fecha))?></td>
              <td class="text-center"><?=strftime('%H:%M:%S',strtotime($fecha))?></td>
              <td class="text-left">
                <?=$descripcion?> info: <?=$informacion_complementaria?>
              </td>      
              <td class="text-left"><?=$agencia?></td>
              <td class="text-right"><?=number_format($monto,2,".",",")?></td>
              <td class="text-right"><?=number_format($saldo,2,".",",")?></td>
              <td class="text-right"><?=$nro_documento?></td>
              <?php 
              if($codFactura==""||$codFactura==0||$codFactura==null){
                $facturaFecha="";
                $facturaNumero="";
                $facturaNit="";
                $facturaRazonSocial="";
                $facturaDetalle="";
                $facturaMonto="";
                $totalMontoFac+=0;
                if(!($codComprobante==""||$codComprobante==0)){
                  $datosDetalle=obtenerDatosComprobanteDetalleFechas($codComprobanteDetalle,$sqlFiltroComp);                    
                  /*if($filtro==0){
                    $datosDetalle=obtenerDatosComprobanteDetalle($codComprobanteDetalle);                    
                  }else{
                    $datosDetalle=obtenerDatosComprobanteDetalleFechas($codComprobanteDetalle,$sqlFiltroComp);                    
                  }*/
                  if($datosDetalle[1]!=''){
                     $facturaFecha="<b class='text-success'>".strftime('%d/%m/%Y',strtotime(obtenerFechaComprobante($codComprobante)))."<b>";
                     $facturaNumero="<b class='text-success'>".nombreComprobante($codComprobante)."</b>";
                     $facturaNit="<b class='text-success'>-</b>";
                     $facturaDetalle="<b class='text-success'>".$datosDetalle[0]."</b>";
                     $facturaRazonSocial="<b class='text-success'>".$datosDetalle[2]." [".$datosDetalle[3]."] - ".$datosDetalle[4]."</b>";
                     $facturaMonto="<b class='text-success'>".$datosDetalle[1]."</b>";     
                  }
                  $totalMontoFac+=$datosDetalle[1];
                }
                
               ?>
                <td class="text-right font-weight-bold"><?=$facturaFecha?></td>
                <td class="text-right font-weight-bold"><?=$facturaNumero?></td>
                <td class="text-right font-weight-bold"><?=$facturaNit?></td>
                <td class="text-right font-weight-bold"><?=$facturaRazonSocial?></td>
                <td class="text-right font-weight-bold"><?=$facturaDetalle?></td>
                <td class="text-right font-weight-bold"><?=$facturaMonto?></td> 
               <?php                          
              }else{
                $cadena_facturas=obtnerCadenaFacturas($codigo);
                $sqlDetalleX="SELECT f.fecha_factura,f.nro_factura,f.nit,f.razon_social,f.observaciones,(SELECT SUM((fd.cantidad*fd.precio)-fd.descuento_bob) from facturas_ventadetalle fd where fd.cod_facturaventa=f.codigo)as importe FROM facturas_venta f where f.codigo in ($cadena_facturas) and f.cod_estadofactura!=2 $sqlFiltro2 order by f.codigo desc";
                //$sqlDetalleX="SELECT f.fecha_factura,f.nro_factura,f.nit,f.razon_social,f.observaciones,SUM((fd.cantidad*fd.precio)-fd.descuento_bob) as importe FROM facturas_venta f, facturas_ventadetalle fd where f.codigo=fd.cod_facturaventa and f.codigo in ($cadena_facturas) and f.cod_estadofactura!=2 $sqlFiltro2 order by f.codigo desc";
                /*if($filtro==0){
                  $sqlDetalleX="SELECT * FROM facturas_venta where codigo in ($cadena_facturas) and cod_estadofactura!=2 order by codigo desc";
                }else{
                    $sqlDetalleX="SELECT * FROM facturas_venta where codigo in ($cadena_facturas) and cod_estadofactura!=2 $sqlFiltro2 order by codigo desc";
                }*/
                
                // echo $sqlDetalleX;                                   
                $stmtDetalleX = $dbh->prepare($sqlDetalleX);
                $stmtDetalleX->execute();
                $stmtDetalleX->bindColumn('fecha_factura', $fechaDetalle);
                $stmtDetalleX->bindColumn('nro_factura', $nroDetalle);
                $stmtDetalleX->bindColumn('nit', $nitDetalle);
                $stmtDetalleX->bindColumn('razon_social', $rsDetalle);
                $stmtDetalleX->bindColumn('observaciones', $obsDetalle);
                $stmtDetalleX->bindColumn('importe', $impDetalle);
                $facturaFecha=[];
                $facturaNumero=[];
                $facturaNit=[];
                $facturaRazonSocial=[];
                $facturaDetalle=[];
                $facturaMonto=[];
                $filaFac=0;  
                while ($rowDetalleX = $stmtDetalleX->fetch(PDO::FETCH_BOUND)) {
                  if($nroDetalle!=""){
                  $totalMontoFac+=$impDetalle;
                  $facturaFecha[$filaFac]=strftime('%d/%m/%Y',strtotime($fechaDetalle));
                  $facturaNumero[$filaFac]=$nroDetalle;
                  $facturaNit[$filaFac]=$nitDetalle;
                  $facturaRazonSocial[$filaFac]=$rsDetalle;
                  $facturaDetalle[$filaFac]=$obsDetalle;
                  $facturaMonto[$filaFac]=number_format($impDetalle,2,".",",");
                  $filaFac++;
                  }
                }?>
                <td class="text-right font-weight-bold" style="vertical-align: top;"><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaFecha)?></td>
                <td class="text-right font-weight-bold" style="vertical-align: top;"><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaNumero)?></td>
                <td class="text-right font-weight-bold" style="vertical-align: top;"><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaNit)?></td>
                <td class="text-right font-weight-bold" style="vertical-align: top;"><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaRazonSocial)?></td>
                <td class="text-right font-weight-bold" style="vertical-align: top;"><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaDetalle)?></td>
                <td class="text-right font-weight-bold" style="vertical-align: top;"><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaMonto)?></td> 
                <?php
              }
              ?>
            </tr><?php 
            $index++;
          }
        }
      }?>
      <script>$("#total_reporte").val("<?=number_format($totalMonto,2,'.',',')?>");</script>
      <tr class="font-weight-bold" style="background:#21618C; color:#fff;">
        <td align="center" colspan="4" class="csp">Totales</td>
        <td class="text-right"><?=number_format($montoMonto,2,".",",")?></td>
        <td class="text-right"><?=number_format($totalMonto,2,".",",")?></td>
        <td class="text-left"></td>
        <td class="text-left"></td>
        <td class="text-left"></td>
        <td class="text-left"></td>
        <td class="text-left"></td>
        <td class="text-left"></td>
        <td class="text-left"><?=number_format($totalMontoFac,2,".",",")?></td>
      </tr>
      <?php
        $html.=    '</tbody>';
        echo $html;?>
      <tfoot>
        <tr style="background:#21618C; color:#fff;">
          <th>Fecha</th>
          <th>Hora</th>
          <th>Descripcion</th>
          <th>Sucursal</th>
          <th>Monto</th>
          <th>Saldo</th>
          <th>Nro Documento</th>
          <th class="bg-success">Fecha</th>
          <th class="bg-success">Numero</th>
          <th class="bg-success">NIT</th>
          <th class="bg-success">Razon Social</th>
          <th class="bg-success">Detalle</th>
          <th class="bg-success">Monto</th>
        </tr>
      </tfoot>
    </table>  
  </div>
</div>
            
<style>
.dataTables_filter{
  display: none !important;
}
</style>              