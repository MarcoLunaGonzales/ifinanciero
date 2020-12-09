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

$sqlDocumento="";
if(isset($_POST['documento'])&&$_POST['documento']!=""&&$_POST['documento']!=0){
  $sqlDocumento="and ce.nro_documento like '%".$_POST['documento']."%'";
}

$sqlMonto="";
if(isset($_POST['monto'])&&$_POST['monto']!=""&&$_POST['monto']!=0){
  $sqlMonto="and ce.monto=".$_POST['monto']."";
}

$sqlDesde="";
if(isset($_POST['desde'])&&$_POST['desde']!=""&&$_POST['desde']!=null&&$_POST['hasta']!=""&&$_POST['hasta']!=null){
  $sqlDesde="and ce.fecha_hora between '".$_POST['desde']."' and '".$_POST['hasta']."' ";
}

$sqlDescripcion="";
if(isset($_POST['descripcion'])&&$_POST['descripcion']!=""&&$_POST['descripcion']!=""){
  $sqlDescripcion="and (ce.descripcion like '%".$_POST['descripcion']."%' or ce.informacion_complementaria like '%".$_POST['descripcion']."%')";
}

$codigoLibreta=$_POST['codigo_libreta'];

$dbh = new Conexion();

$saldo_inicial=0;

// Preparamos
$stmt = $dbh->prepare("SELECT ce.*
FROM libretas_bancariasdetalle ce where ce.cod_libretabancaria=$codigoLibreta and  ce.cod_estadoreferencial=1 
$sqlDocumento $sqlMonto $sqlDesde $sqlDescripcion
order by ce.fecha_hora desc");
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
$stmt->bindColumn('saldo', $saldo);
$stmt->bindColumn('cod_estado', $estadoFila);
$stmt->bindColumn('nro_referencia', $nro_referencia);
$stmt->bindColumn('cod_comprobante', $codComprobante);
$stmt->bindColumn('cod_comprobantedetalle', $codComprobanteDetalle);

?>

                <table class="table table-condensed small">
                      <thead>
                        <tr style="background:#21618C; color:#fff;">
                          <td class="text-center">#</td>
                          <td>Fecha<br>Hora</td>                          
                          <td>Descripción</td>
                          <td>Información C.</td>
                          <td>Sucursal</td>
                          <td>Monto</td>
                          <td style="background:#A4E082;">Saldo Acumulado</td>
                          <td style="background:#B91E0B;">Saldo según Banco <br>(Cargado)</td>
                          <td style="background:#B91E0B;">Saldo del Registro</td>
                          <td>Nro Doc / Nro Ref</td>

                          <th class="small bg-success" width="4%"><small>Fecha Fac.</small></th>
                          <th class="small bg-success" width="4%"><small>N° Fac.</small></th>      
                          <th class="small bg-success" width="7%"><small>Nit Fac.</small></th>
                          <th class="small bg-success" width="7%"><small>Razón Social Fac.</small></th>
                          <th class="small bg-success" width="5%"><small>Monto Fac.</small></th>                          
                          <td class="text-right">*</td>
                          <td class="text-right">Acciones</td>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $index=1;
                        $saldo_acumulado=0;
                        //codigo temporal para cuadrar cierto monto  el saldo inicial es de la fecha 1/7/2020
                        $fecha_temporal="2020-07-01 00:00:00";
                        if($codigoLibreta==4){
                          $sw_temporal=true;  
                        }else{
                          $sw_temporal=false;
                        }
                        //termina codigo temporal
                        
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          //codigo temporal para cuadrar cierto monto  el saldo inicial es de la fecha 1/7/2020
                          if($fecha>=$fecha_temporal && $sw_temporal){
                            $sw_temporal=false;
                            $saldo_inicial_temporal=157510.15;
                            $saldo_inicial=$saldo_inicial_temporal;?>
                            <!--<tr style="background:#21618C; color:#fff;"><td></td><td></td><td></td><td></td><td></td><td></td><td><?=$saldo_inicial_temporal?></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>-->

                          <?php }

                          //$saldo_inicial=$saldo_inicial+$monto;
                            //$saldo_inicial=obtenerSaldoLibretaBancariaDetalle($codigo);
                            $saldo_inicial=obtenerSaldoLibretaBancariaDetalleFiltro($codigo,"",$monto);
                            //$saldo_acumulado+=$saldo_inicial;
                            $saldo_acumulado=obtenerSaldoAcumuladoFilaLibretaBancaria($codigo);
                          //==termina el codigom temporal

                          ?>
                          <tr>
                            <td align="center"><?=$index;?></td>
                            <td class="text-center"><?=strftime('%d/%m/%Y',strtotime($fecha))?><br><?=strftime('%H:%M:%S',strtotime($fecha))?></td>
                            <td class="text-left"><?=$descripcion?></td>
                            <td class="text-left"><?=$informacion_complementaria?></td>      
                            <td class="text-left"><?=$agencia?></td>
                            <td class="text-right"><?=number_format($monto,2,".",",")?></td>
                            <td class="text-right" style="background:#A4E082;"><?=number_format($saldo_acumulado,2,".",",")?></td>
                            <td class="text-right" style="background:#F7684F;"><?=number_format($saldo,2,".",",")?></td>
                            <td class="text-right" style="background:#F7684F;"><?=number_format($saldo_inicial,2,".",",")?></td>                                                  
                            <td class="text-left"><?=$nro_documento?></td>

                            <?php
                              
                              // $cont_facturas=contarFacturasLibretaBancaria($codigo);
                              // if($cont_facturas>0){
                                $cadena_facturas=obtnerCadenaFacturas($codigo);
                                // $sqlDetalleX="SELECT * FROM facturas_venta where cod_libretabancariadetalle=$codigo and cod_estadofactura!=2 order by codigo desc";
                                $sqlDetalleX="SELECT * FROM facturas_venta where codigo in ($cadena_facturas) and cod_estadofactura!=2 order by codigo desc";
                                $stmtDetalleX = $dbh->prepare($sqlDetalleX);
                                $stmtDetalleX->execute();
                                $stmtDetalleX->bindColumn('codigo', $codigo_factura);
                                $stmtDetalleX->bindColumn('fecha_factura', $fechaDetalle);
                                $stmtDetalleX->bindColumn('nro_factura', $nroDetalle);
                                $stmtDetalleX->bindColumn('nit', $nitDetalle);
                                $stmtDetalleX->bindColumn('razon_social', $rsDetalle);
                                $stmtDetalleX->bindColumn('observaciones', $obsDetalle);
                                $stmtDetalleX->bindColumn('importe', $impDetalle);
                                $facturaCodigo=[];
                                $facturaFecha=[];
                                $facturaNumero=[];
                                $facturaNit=[];
                                $facturaRazonSocial=[];
                                $facturaDetalle=[];
                                $facturaMonto=[];
                                $filaFac=0;  
                                while ($rowDetalleX = $stmtDetalleX->fetch(PDO::FETCH_BOUND)) {                        
                                  $facturaCodigo[$filaFac]=$codigo_factura;
                                  $facturaFecha[$filaFac]=strftime('%d/%m/%Y',strtotime($fechaDetalle));
                                  $facturaNumero[$filaFac]=$nroDetalle;
                                  $facturaNit[$filaFac]=$nitDetalle;
                                  $facturaRazonSocial[$filaFac]=$rsDetalle;
                                  $facturaDetalle[$filaFac]=$obsDetalle;
                                  $facturaMonto[$filaFac]=number_format($impDetalle,2,".",",");
                                  $filaFac++;
                                }
                                if(!($codComprobante==""||$codComprobante==0)){
                                  $datosDetalle=obtenerDatosComprobanteDetalle($codComprobanteDetalle);

                                  $facturaFecha[$filaFac]="<b class='text-success'>".strftime('%d/%m/%Y',strtotime(obtenerFechaComprobante($codComprobante)))."<b>";
                                  $facturaNumero[$filaFac]="<b class='text-success'>".nombreComprobante($codComprobante)."</b>";
                                  $facturaNit[$filaFac]="<b class='text-success'>-</b>";
                                  $facturaDetalle[$filaFac]="<b class='text-success'>".$datosDetalle[0]."</b>";
                                  $facturaRazonSocial[$filaFac]="<b class='text-success'>".$datosDetalle[2]." [".$datosDetalle[3]."] - ".$datosDetalle[4]."</b>";
                                  $facturaMonto[$filaFac]="<b class='text-success'>".$datosDetalle[1]."</b>";
                                  $facturaRazonSocial[$filaFac].="<br>".$facturaDetalle[$filaFac];
                                }
                                ?>
                                <td class="text-right" style="vertical-align: top;"><small><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaFecha)?></small></td>
                                <td class="text-right" style="vertical-align: top;"><small><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaNumero)?></small></td>
                                <td class="text-right" style="vertical-align: top;"><small><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaNit)?></small></td>
                                <td class="text-left" style="vertical-align: top;"><small><small><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaRazonSocial)?></small></small></td>                      
                                <td class="text-right" style="vertical-align: top;"><small><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaMonto)?></small>
                                </td>
                                <td class="text-right" style="vertical-align: top;"><small>
                                  <?php
                                  for ($i=0;$i<count($facturaCodigo);$i++){?>
                                    <div style='border-bottom:1px solid #26BD3D;'>
                                      <a title="Eliminar relación de Factura" href="#" class="btn btn-danger btn-sm btn-round" style="padding: 0;font-size:8px;width:15px;height:15px;" onclick="eliminarRelacionFactura(<?=$facturaCodigo[$i]?>,<?=$codigo?>)"><i class="material-icons">remove</i></a>
                                    </div>
                                  <?php }                                   
                                  ?>
                                  </small>
                                </td>

                            <td class="td-actions text-right">
                            <?php
                              if($globalAdmin==1){
                              ?>                             
                              <!-- <button type="button"  title="Relacionar Con Factura" class="btn btn-warning" data-toggle="modal" data-target="#modallista_facturas" onclick="relacionar_factura_libreta(<?=$codigo?>)">
                                <i class="material-icons">add_circle_outline</i>
                              </button> -->
                              <a href="#" title="Relacionar Con Factura" onclick="relacionar_factura_libreta(<?=$codigo?>);return false;"  class="btn btn-success">
                                <i class="material-icons">add_circle_outline</i>
                              </a>
                              <?php 
                               //if($estadoFila!=1){
                                ?>
                                  <a href="#" rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteDetalle;?>&codigo=<?=$codigo;?>&c=<?=$codigoLibreta?>')">
                                    <i class="material-icons"><?=$iconDelete;?></i>
                                  </a>
                                <?php
                               //} 
                              ?>
                              
                              <?php
                              }
                              ?>
                              
                            </td>
                          </tr>
                          <?php $index++;
                        } ?>
                      </tbody>
                    </table>