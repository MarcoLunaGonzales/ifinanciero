<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$codigoLibreta=$_GET['codigo'];
//echo "test cod bono: ".$codigoLibreta;

$globalAdmin=$_SESSION["globalAdmin"];
$nombreGestion=$_SESSION['globalNombreGestion'];
$codGestionActiva=$_SESSION['globalGestion'];

$dbh = new Conexion();
//sacamos el saldo inicial p menor
// $stmtSaldoInicial = $dbh->prepare("SELECT ce.monto
// FROM libretas_bancariasdetalle ce where ce.cod_libretabancaria=$codigoLibreta and  ce.cod_estadoreferencial=1 limit 1");
// $stmtSaldoInicial->execute();
// $resultSaldoInicial=$stmtSaldoInicial->fetch();
$saldo_inicial=0;


// Preparamos
$stmt = $dbh->prepare("SELECT ce.*
FROM libretas_bancariasdetalle ce where ce.cod_libretabancaria=$codigoLibreta and  ce.cod_estadoreferencial=1 order by ce.fecha_hora desc limit 50");
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


//Mostrar tipo bono
$stmtb = $dbh->prepare("SELECT p.nombre as banco,c.* FROM $table c join bancos p on c.cod_banco=p.codigo WHERE c.codigo=$codigoLibreta");
// Ejecutamos
$stmtb->execute();
// bindColumn
$stmtb->bindColumn('banco', $nombreBanco);
$stmtb->bindColumn('nro_cuenta', $cuenta);
$stmtb->bindColumn('nombre', $nombre);

?>
<style>
.menu-fixed {
    top:-100px !important;
}
</style>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Subiendo Archivo Excel</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <input type="hidden" name="codigo_libreta" id="codigo_libreta" value="<?=$codigoLibreta?>">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title"><b style="color:#732590;"><?=$moduleNamePluralDetalle?></b>
                     
                  </h4>

                  <?php
                  while ($row = $stmtb->fetch(PDO::FETCH_BOUND)) {
                    ?>
                  <h4 class="card-title" align="center"><?=$nombreBanco?> <b>NRO. CUENTA: <?=$cuenta?></b> / <?=$nombre?></b>
                      <a href="#" class="btn btn-warning btn-round btn-fab btn-sm" data-toggle="modal" data-target="#modalBuscarDetalleLibretas">
                        <i class="material-icons" title="Buscador Avanzado">search</i>
                      </a>
                  </h4>
                  <?php
                  }
                  ?>
                   
                   <script>
                   $(document).ready(function() {
                      var table = $('#tablePaginatorLibretas').DataTable( {
                          "pageLength": 100,
                          "language": {
                              "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                          },
                          fixedHeader: {
                            header: true,
                            footer: true
                          },
                          "ordering": false
                      } );
                      $('#min').on('change keyup', function() {
                          table.draw();
                      });
                      $('#max').on('change keyup', function() {
                          table.draw();
                      }); 
                  });
                   $.fn.dataTable.ext.search.push(
                     function(oSettings, aData, iDataIndex) {
                         var dateIni = $('#min').val();
                         var dateFin = $('#max').val();
                         var indexCol = 1;
                         dateIni = dateIni.replace(/-/g, "");
                         dateFin= dateFin.replace(/-/g, "");
                         var dateCol = aData[indexCol].replace(/-/g, "");
                         if (dateIni === "" && dateFin === "")
                         {
                             return true;
                         }
                         if(dateIni === "")
                         {
                             return dateCol <= dateFin;
                         }
                         if(dateFin === "")
                         {
                             return dateCol >= dateIni;
                         }
                          return dateCol >= dateIni && dateCol <= dateFin;
                      }
                  );
                   </script>
                </div>
                <div class="card-body">
                  <div class="row d-none"> <!-- ocultar porque ya hay un buscador-->
                     <div class="input-group mb-3">
                       <div class="input-group-prepend">
                         <span class="input-group-text text-muted" id="basic-addon1"><small>Buscar Fecha Desde</small></span>
                       </div>
                       <input type="text" class="form-control" id="min" name="min" placeholder="dd/mm/aaaa" aria-label="dd/mm/aaaa" style="background-color:#E3CEF6;text-align: left;" aria-describedby="basic-addon1">
                     </div>
                     <div class="input-group mb-3">
                       <div class="input-group-prepend">
                         <span class="input-group-text text-muted" id="basic-addon1"><small>Hasta</small></span>
                       </div>
                       <input type="text" class="form-control" id="max" name="max" placeholder="dd/mm/aaaa" aria-label="dd/mm/aaaa" style="background-color:#E3CEF6;text-align: left;" aria-describedby="basic-addon1">
                     </div>
                  </div>
                  <hr>
                  <div class="table-responsive" id="contenedor_libretas_detalle">
                    <table class="table table-condensed small table-bordered">
                      <thead>
                        <tr style="background:#21618C; color:#fff;">
                          <td class="text-center">#</td>
                          <td>Fecha<br>Hora</td>                          
                          <td>Descripción</td>
                          <td>Información C.</td>
                          <td>Sucursal</td>
                          <td>Monto</td>
                          <td style="background:#B91E0B;">Saldo según Banco</td>
                          <td style="background:#B91E0B;">Saldo</td>
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
                            $saldo_inicial=obtenerSaldoLibretaBancariaDetalle($codigo);

                          //==termina el codigom temporal

                          ?>
                          <tr>
                            <td align="center"><?=$index;?></td>
                            <td class="text-center"><?=strftime('%d/%m/%Y',strtotime($fecha))?><br><?=strftime('%H:%M:%S',strtotime($fecha))?></td>
                            <td class="text-left"><?=$descripcion?></td>
                            <td class="text-left"><?=$informacion_complementaria?></td>      
                            <td class="text-left"><?=$agencia?></td>
                            <td class="text-right"><?=number_format($monto,2,".",",")?></td>
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
                  </div>
                </div>
              </div>

              <?php
              if($globalAdmin==1){
              ?>
      				<div class="card-footer fixed-bottom">
                <button class="<?=$buttonCancel;?>" onClick="location.href='<?=$urlList;?>'">Volver</button>
                <div class="btn-group dropdown">
                      <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Cargar Libreta desde Excel
                      </button>
                      <div class="dropdown-menu menu-fixed">
                        <a href="#" onclick="subirArchivoExcelLibretaBancaria(1,'Formato BISA'); return false;"  class="dropdown-item">
                                   <i class="material-icons">keyboard_arrow_right</i>Formato BISA
                        </a>
                        <a href="#" onclick="subirArchivoExcelLibretaBancaria(2,'Formato UNION'); return false;"  class="dropdown-item">
                                   <i class="material-icons">keyboard_arrow_right</i>Formato UNION
                        </a>
                      </div>
                  </div>
                  
                  <!--<button class="btn btn-info" onClick="#">Historial</button>-->
              </div>
              
              <?php
              }
              ?>
		  
            </div>
          </div>

        </div>
    </div>

    <!-- small modal -->  
<div class="modal fade modal-arriba modal-primary" id="modalSubirArchivoExcel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice" style="max-width: 80% !important;">
    <div class="modal-content card">
      <div class="card-header card-header-default card-header-text">
        <div class="card-text">
          <h4 id="formato_texto"></h4>
        </div>
        <button type="button" class="btn btn-success btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
      </div>
      <div class="card-body">
        <form action="<?=$urlSaveImport?>" method="post" name="formLibretaBancaria" id="formLibretaBancaria" enctype="multipart/form-data">
          <input type="hidden" name="tipo_formato" id="tipo_formato">
          <div class="row">
            <label class="col-sm-3 col-form-label" style="color:#000000; ">Archivo Excel:</label>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="hidden" class="form-control" name="codigo" id="codigo" value="<?=$codigoLibreta?>">
                <small id="label_txt_documentos_excel"></small> 
                <span class="input-archivo">
                  <input type="file" class="archivo" accept=".xls,.xlsx" name="documentos_excel" id="documentos_excel"/>
                </span>
                <label title="Ningún archivo" for="documentos_excel" id="label_documentos_excel" class="label-archivo btn btn-default btn-sm"><i class="material-icons">publish</i> Subir Archivo
                </label>
              </div>
            </div>
          </div>
          <div class="row">     
            <label class="col-sm-3 col-form-label" style="color:#000000; ">Observaciones:</label>
            <div class="col-sm-6">
              <div class="form-group">
                 <textarea type="text" class="form-control" name="observaciones" id="observaciones" value="" style="background-color:#E3CEF6;text-align: left" ></textarea>
              </div>
            </div> 
          </div>
          <div class="row">     
            <label class="col-sm-3 col-form-label" style="color:#000000; ">Tipo de Cargado:</label>
            <div class="col-sm-6">
              <div class="form-group">
                <select class="selectpicker form-control" name="tipo_cargado" id="tipo_cargado" data-style="btn btn-default">
                <?php
                   $stmt = $dbh->prepare("SELECT p.codigo,p.nombre FROM tipos_libretabancariacargado p where p.cod_estadoreferencial=1 order by p.codigo desc");
                   $stmt->execute();
                   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                     $codigoX=$row['codigo'];
                     $nombreX=$row['nombre'];
                     if($codigoX==1){
                      ?>
                     <option value="<?=$codigoX;?>" selected><?=$nombreX;?></option>  
                      <?php
                     }else{
                      ?>
                     <option value="<?=$codigoX;?>"><?=$nombreX;?></option>  
                      <?php
                     }
                   
                     }
                     ?> 
                </select>
              </div>
            </div> 
          </div>
          <br><br>
          <center><h4 id="tipo_formato_titulo2" class="font-weight-bold"></h4></center>
          <div id="tabla_muestra_formato_a">
            <table class="table table-bordered small table-condensed">
              <thead>
               <tr style="background:#F9D820; color:#262C7B;">
                <th>Fecha</th>
                <th>Hora</th>
                <th>Nro Cheque</th>
                <th>Descripción</th>
                <th>Monto</th>
                <th>Saldo</th>
                <th>Información C.</th>
                <th>Sucursal</th>
                <th>Canal</th>
                <th>Nro Referencia</th> 
                <th>Codigo</th>
               </tr>
              </thead>
              <tbody>
               <tr style="background:#262C7B; color:#fff;">
                <td>dd-mm-aaaa</td>
                <td>HH:mm:ss</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
               </tr>
              </tbody>
            </table>  
          </div>
          <div id="tabla_muestra_formato_b" class="d-none">
            <table class="table table-bordered table-condensed">
               <thead>
                 <tr style="background:#223BC8; color:#F3F300;">
                  <th>Fecha</th>
                  <th>Agencia</th>
                  <th>Descripción</th>
                  <th>Nro Documento</th>
                  <th>Monto</th>
                  <th>Saldo</th>
                 </tr>
               </thead>
               <tbody>
                 <tr style="background:#F37200; color:#fff;">
                   <td>dd-mm-aaaa</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                 </tr>
               </tbody>
            </table>  
          </div>
          <hr>
          <div class="float-right">
            <button type="submit" id="submit" name="import" class="btn btn-success" onclick="iniciarCargaAjax();">Importar Registros</button>
          </div>
        </form>
      </div>  
    </div>
  </div>
</div>
<!--    end small modal -->

<div class="modal fade modal-arriba modal-primary" id="modallista_facturas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice" style="max-width: 80% !important;">
    <div class="modal-content card">
      <div class="card-header card-header-warning card-header-icon">
        <div class="card-icon">
          <i class="material-icons">settings_applications</i>
        </div>
        <h4 class="card-title">Facturas</h4>
      </div>
      <div class="card-body">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
        <input type="hidden" name="cod_libretabancariadetalle" id="cod_libretabancariadetalle" value="0">
        <div class="table-responsive">
          <table id="tablePaginator50" class="table table-condensed small">          
            <thead>
              <tr style="background:#21618C; color:#fff;">
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
          </table>
        </div>
      </div>
      <div class="modal-footer">
         <span style="color:   #ec7063 ;"><i class="material-icons">check_box</i> Facturas Relacionadas</span><br>
         <!-- <span ><i class="material-icons">check_box</i> Facturas No Relacionadas</span><br> -->
      </div>
    </div>  
  </div>
</div>

<!-- modal devolver solicitud -->
<div class="modal fade" id="modalBuscarDetalleLibretas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" id="cabecera_conta" style="background:#732590; !important;color:#fff;">
        <h4 class="modal-title" id="titulo_conta">Buscar registro Libreta Bancaria</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
      </div>
      <div class="modal-body">        
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id=""><small>Doc/Ref</small></span></label>
          <div class="col-sm-5">
            <div class="form-group" >
              <input type="text" class="form-control" name="buscar_nro_documento" id="buscar_nro_documento" style="background-color:#e2d2e0;">              
            </div>
          </div>
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id=""><small >Monto</small></span></label>
          <div class="col-sm-5">
            <div class="form-group" >  
                <input type="number" step="any" class="form-control" name="buscar_monto" id="buscar_monto" style="background-color:#e2d2e0;">                          
            </div>
          </div>
        </div> 
        <div class="row">
                    <div class="col-sm-6">
                      <div class="row">
                       <label class="col-sm-2 col-form-label" style="color:#7e7e7e"><small>Desde</small></label>
                       <div class="col-sm-10">
                           <div class="form-group">
                            <input type="date" class="form-control" name="buscar_fecha_desde" id="buscar_fecha_desde" style="background-color:#e2d2e0">                                                     
                           </div>
                        </div>
                   </div>
                     </div>
                    <div class="col-sm-6">
                      <div class="row">
                       <label class="col-sm-2 col-form-label" style="color:#7e7e7e"><small>Hasta</small></label>
                       <div class="col-sm-10">
                        <div class="form-group">
                              <input type="date" class="form-control" name="buscar_fecha_hasta" id="buscar_fecha_hasta" style="background-color:#e2d2e0">                                                     
                            </div>
                        </div>
                    </div>
              </div>
                  </div><!--div row-->
                
        <div class="row">
          <label class="col-sm-12 col-form-label" style="color:#7e7e7e"><small>Descripción / Informacion C.</small></label>
        </div>
        <div class="row">
          <div class="col-sm-12" style="background-color:#f9edf7">
            <div class="form-group" >              
              <textarea class="form-control" name="buscar_descripcion" id="buscar_descripcion" style="background-color:#e2d2e0"></textarea>
            </div>
          </div>
        </div>        
      </div>
      <br>  
      <div class="modal-footer">
        <a href="#" class="btn btn-success" style="background:#732590 !important;" onclick="buscarDetallesLibretasBancarias()"><i class="material-icons">search</i> BUSCAR</a>
        <!--<button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>-->
      </div>
    </div>
  </div>
</div>