<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();
$cod_simulacion=$cod_s;
$cod_facturacion=$cod_f;
//sacamos datos para la facturacion

$sql="SELECT sc.anios,sc.cod_responsable,sc.cod_cliente,ps.cod_area,ps.cod_unidadorganizacional
from simulaciones_servicios sc,plantillas_servicios ps
where sc.cod_plantillaservicio=ps.codigo and sc.cod_estadoreferencial=1 and sc.codigo=$cod_simulacion order by sc.codigo";
$stmtServicio = $dbh->prepare($sql);
$stmtServicio->execute();
$resultServicio = $stmtServicio->fetch();

if ($cod_facturacion > 0){
    $stmt = $dbh->prepare("SELECT * FROM solicitudes_facturacion where codigo=$cod_facturacion");
    $stmt->execute();
    $result = $stmt->fetch();
    $cod_uo = $result['cod_unidadorganizacional'];
    $cod_area = $result['cod_area'];
    $fecha_registro = $result['fecha_registro'];
    $fecha_solicitudfactura = $result['fecha_solicitudfactura'];
    $cod_tipoobjeto = $result['cod_tipoobjeto'];
    $cod_tipopago = $result['cod_tipopago'];
    $cod_cliente = $result['cod_cliente'];
    $cod_personal = $result['cod_personal'];
    $razon_social = $result['razon_social'];
    $nit = $result['nit'];
    $observaciones = $result['observaciones'];
    $anios_servicio = $resultServicio['anios'];
    $name_cliente=nameCliente($cod_cliente);
}else {
    
    
    $cod_personal = $resultServicio['cod_responsable'];
    $cod_uo = $resultServicio['cod_unidadorganizacional'];
    $cod_area = $resultServicio['cod_area'];
    $cod_cliente = $resultServicio['cod_cliente'];
    $anios_servicio = $resultServicio['anios'];

    $fecha_registro =date('Y-m-d');
    $fecha_solicitudfactura =null;
    $cod_tipoobjeto=obtenerValorConfiguracion(34);//por defecto
    $cod_tipopago = null;
    $name_cliente=nameCliente($cod_cliente);    
    $razon_social = $name_cliente;
    $nit = 0;
    $observaciones = null;
}


$name_uo=nameUnidad($cod_uo);
$name_area=nameArea($cod_area);


?>

<div class="content">
    <div class="container-fluid">
        <div style="overflow-y:scroll;">
            <div class="col-md-12">
              <form id="form1" class="form-horizontal" action="<?=$urlSaveSolicitudfactura;?>" method="post" onsubmit="return valida(this)">
                <input type="hidden" name="cod_simulacion" id="cod_simulacion" value="<?=$cod_simulacion;?>"/>
                <input type="hidden" name="cod_facturacion" id="cod_facturacion" value="<?=$cod_facturacion;?>"/>
                <div class="card">
                  <div class="card-header <?=$colorCard;?> card-header-text">
                    <div class="card-text">
                      <h4 class="card-title"><?php if ($cod_simulacion == 0) echo "Registrar"; else echo "Editar";?> Solicutd de Facturación</h4>
                    </div>
                  </div>
                  <div class="card-body ">
                        <div class="row">
                          <label class="col-sm-2 col-form-label">Oficina</label>
                          <div class="col-sm-4">
                            <div class="form-group">
                                <input class="form-control" type="hidden" name="cod_uo" id="cod_uo" required="true" value="<?=$cod_uo;?>" required="true" readonly/>
                                 <input class="form-control" type="text" required="true" value="<?=$name_uo;?>" required="true" readonly/>

                                <!-- <select name="cod_uo" id="cod_uo" class="selectpicker form-control form-control-sm" data-style="btn btn-info" required="true">
                                    <option value=""></option>
                                    <?php 
                                    $queryUO = "SELECT codigo,nombre from unidades_organizacionales where cod_estado=1 order by nombre";
                                    $statementUO = $dbh->query($queryUO);
                                    while ($row = $statementUO->fetch()){ ?>
                                        <option <?=($cod_uo==$row["codigo"])?"selected":"";?><?=($cod_uo!=$row["codigo"])?"disabled":"";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                    <?php } ?>
                                </select> -->
                            </div>
                          </div>
                          <label class="col-sm-2 col-form-label">Area</label>
                            <div class="col-sm-4">
                                <div class="form-group" >
                                    <div id="div_contenedor_area_tcc">
                                        <input class="form-control" type="hidden" name="cod_area" id="cod_area" required="true" value="<?=$cod_area;?>" required="true" readonly/>

                                        <input class="form-control" type="text" required="true" value="<?=$name_area;?>" required="true" readonly/>
                                        <!-- <select name="cod_area" id="cod_area" class="selectpicker form-control form-control-sm" data-style="btn btn-info" >
                                            <option value=""></option>
                                            <?php 
                                            $queryArea = "SELECT codigo,nombre FROM  areas WHERE cod_estado=1 order by nombre";
                                            $statementArea = $dbh->query($queryArea);
                                            while ($row = $statementArea->fetch()){ ?>
                                                <option <?=($cod_area==$row["codigo"])?"selected":"";?><?=($cod_area!=$row["codigo"])?"disabled":"";?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select> -->
                                    </div>                    
                                </div>
                            </div>
                        </div>
                            <!--fin ofician y area -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Fecha R.</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="date" name="fecha_registro" id="fecha_registro" required="true" value="<?=$fecha_registro;?>" required="true"/>
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Fecha Soliciutd</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="date" name="fecha_solicitudfactura" id="fecha_solicitudfactura" required="true" value="<?=$fecha_solicitudfactura;?>" required="true"/>
                                </div>
                            </div>

                        </div>
                        <!-- fin fechas -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Tipo Objeto</label>
                            <div class="col-sm-4">
                                <div class="form-group" >
                                        <select name="cod_tipoobjeto" id="cod_tipoobjeto" class="selectpicker form-control form-control-sm" data-style="btn btn-info" >
                                            <option value=""></option>
                                            <?php 
                                            $queryTipoObjeto = "SELECT codigo,nombre FROM  tipos_objetofacturacion WHERE cod_estadoreferencial=1 order by nombre";
                                            $statementObjeto = $dbh->query($queryTipoObjeto);
                                            while ($row = $statementObjeto->fetch()){ ?>
                                                <option <?=($cod_tipoobjeto==$row["codigo"])?"selected":"";?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>                                
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Tipo Pago</label>
                            <div class="col-sm-4">
                                <div class="form-group" >
                                        <select name="cod_tipopago" id="cod_tipopago" class="selectpicker form-control form-control-sm" data-style="btn btn-info"
                                            <option value=""></option>
                                            <?php 
                                            $queryTipoPago = "SELECT codigo,nombre FROM  tipos_pago WHERE cod_estadoreferencial=1 order by nombre";
                                            $statementPAgo = $dbh->query($queryTipoPago);
                                            while ($row = $statementPAgo->fetch()){ ?>
                                                <option <?=($cod_tipopago==$row["codigo"])?"selected":"";?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>                                
                                </div>
                            </div>
                        </div>
                        <!-- fin tipos pago y objeto                 -->
                        
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Cliente</label>
                            <div class="col-sm-4">
                                <div class="form-group" >

                                     <input class="form-control" type="hidden" name="cod_cliente" id="cod_cliente" required="true" value="<?=$cod_cliente;?>" required="true" readonly/>

                                        <input class="form-control" type="text" required="true" value="<?=$name_cliente;?>" required="true" readonly/>
                                        <!-- <select name="cod_cliente" id="cod_cliente" class="selectpicker form-control form-control-sm" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" onchange="ajax_Cliente_razonsocial(this)" required="true">
                                            <option value=""></option>
                                            <?php 
                                            $querycliente = "SELECT codigo,nombre FROM  clientes WHERE cod_estadoreferencial=1 order by nombre";
                                            $statementCliente = $dbh->query($querycliente);
                                            while ($row = $statementCliente->fetch()){ ?>
                                                <option <?=($cod_cliente==$row["codigo"])?"selected":"";?><?=($cod_cliente!=$row["codigo"])?"disabled":"";?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>                                 -->
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Responsable</label>
                            <div class="col-sm-4">
                                <div class="form-group">            
                                    <?php  $responsable=namePersonal($cod_personal); ?>                    
                                    <input type="hidden" name="cod_personal" id="cod_personal" value="<?=$cod_personal?>" readonly="true" class="form-control">
                                    <input type="text" value="<?=$responsable?>" readonly="true" class="form-control">
                                </div>
                            </div>
                        </div>
                        <!-- fin cliente y responsable -->

                        <div class="row">
                            <label class="col-sm-2 col-form-label">Razón Social</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <div id="contenedor_razonsocial">
                                        <input class="form-control" type="text" name="razon_social" id="razon_social" required="true" value="<?=$razon_social;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>    
                                    </div>
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Nit</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="number" name="nit" id="nit" required="true" value="<?=$nit;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" required="true"/>
                                </div>
                            </div>
                        </div>
                        <!-- fin razon social y nit -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Observaciones</label>
                            <div class="col-sm-7">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="observaciones" id="observaciones" required="true" value="<?=$observaciones;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                                </div>
                            </div>
                        </div>
                        <!-- fin observaciones -->

                        <div class="card">
                            <div class="card-header <?=$colorCard;?> card-header-text">
                                <div class="card-text">
                                  <h6 class="card-title">Detalle Soliciutd Facturación</h6>
                                </div>
                            </div>
                            <div class="card-body ">
                                <table class="table table-bordered table-condensed table-striped table-sm">
                                     <thead>
                                          <tr class="fondo-boton">
                                            <th>#</th>
                                            <th >Año</th>
                                            <th>Servicio</th>
                                            <th>Cantidad</th>
                                            <th>Importe</th>
                                            <th>Total</th>                                    
                                            <th>Descripcion</th>  
                                            <th class="small">Habilitar/Deshabilitar</th>
                                          </tr>
                                      </thead>
                                      <tbody>                                
                                        <?php 
                                        $iii=1;
                                       $queryPr="SELECT s.*,t.descripcion as nombre_serv FROM simulaciones_servicios_tiposervicio s, cla_servicios t where s.cod_simulacionservicio=$cod_simulacion and s.cod_claservicio=t.idclaservicio order by s.codigo";
                                       $stmt = $dbh->prepare($queryPr);
                                       $stmt->execute();
                                       $modal_totalmontopre=0;$modal_totalmontopretotal=0;
                                       while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                          $codigoPre=$rowPre['codigo'];
                                          $codCS=$rowPre['cod_claservicio'];
                                          $tipoPre=$rowPre['nombre_serv'];
                                          $cantidadPre=$rowPre['cantidad'];
                                          $cantidadEPre=$rowPre['cantidad_editado'];
                                          $montoPre=$rowPre['monto'];
                                          // $montoPreTotal=$montoPre*$cantidadEPre;
                                          $banderaHab=$rowPre['habilitado'];
                                          $codTipoUnidad=$rowPre['cod_tipounidad'];
                                          $cod_anio=$rowPre['cod_anio'];

                                          if($banderaHab!=0){
                                            // $modal_totalmontopre+=$montoPre;
                                            $montoPre=number_format($montoPre,2,".","");
                                            // $modal_totalmontopretotal+=$montoPreTotal;
                                            ?>
                                            <!-- guardamos las varialbles en un input -->
                                            <input type="hidden" id="servicio<?=$iii?>" name="servicio<?=$iii?>" value="<?=$codCS?>">
                                            <input type="hidden" id="cantidad<?=$iii?>" name="cantidad<?=$iii?>" value="<?=$cantidadPre?>">
                                            <input type="hidden" id="importe<?=$iii?>" name="importe<?=$iii?>" value="<?=$montoPre?>">
                                            <input type="hidden" id="servicio_a<?=$iii?>" name="servicio_a<?=$iii?>">
                                            <input type="hidden" id="cantidad_a<?=$iii?>" name="cantidad_a<?=$iii?>">
                                            <input type="hidden" id="importe_a<?=$iii?>" name="importe_a<?=$iii?>">
                                            <tr>
                                             <td><?=$iii?></td>
                                             <td class="text-left"><?=$cod_anio?> </td>
                                             <td class="text-right"><?=$tipoPre?></td>
                                             <td class="text-right"><?=$cantidadPre?></td>
                                             <td id="modal_importe<?=$iii?>" class="text-right"><?=$montoPre?></td>
                                             <td class="text-right"><?=$montoPre?></td>
                                             
                                             <td>
                                               <div class="togglebutton">
                                                   <label>
                                                     <input type="checkbox"  id="modal_check<?=$iii?>" onchange="activarInputMontoFilaServicio2()">
                                                     <span class="toggle"></span>
                                                   </label>
                                               </div>
                                             </td>
                                             <td>
                                                 <input type="text" name="descripcion_alterna<?=$iii?>" id="descripcion_alterna<?=$iii?>" class="form-control">
                                             </td>
                                           </tr>

                                          <?php   $iii++;  }
                                                                                                                    
                                          // $montoPreTotal=number_format($montoPreTotal,2,".","");
                                           ?>
                                           

                                           
                                          <?php
                                        
                                          } ?>
                                          <tr>
                                             <td colspan="4" class="text-center font-weight-bold">Total</td>
                                             <td></td>
                                             <td id="modal_totalmontoserv" class="text-right"><?=number_format($modal_totalmontopre,2, ',', '')?></td>
                                             <td></td>
                                           </tr>
                                      </tbody>
                                </table>
                                <input type="hidden" id="modal_numeroservicio" name="modal_numeroservicio" value="<?=$iii?>">                    
                                <input type="hidden" id="modal_totalmontos" name="modal_totalmontos">
                                <!-- <script>activarInputMontoFilaServicio2();</script>   -->
                                <input type="hidden" id="comprobante_auxiliar" name="comprobante_auxiliar">
      
                            </div>
                        </div>                    
                  </div>
                  <div class="card-footer ml-auto mr-auto">
                    <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
                    <a href='<?=$urlSolicitudfactura;?>&cod=<?=$cod_simulacion;?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
                  </div>
                </div>
              </form>
              <div class="card">
                            <div class="card-header <?=$colorCard;?> card-header-text">
                                <div class="card-text">
                                  <h6 class="card-title">Agregar Servicios</h6>
                                </div>
                            </div>
                            <div class="card-body ">
                                <table class="table table-bordered table-condensed table-striped table-sm">
                                 <thead>
                                      <tr class="fondo-boton">
                                        <td colspan="4"></td>
                                        <td colspan="2">MONTO</td>
                                        <td colspan="2">TOTAL</td>
                                        <td></td>
                                      </tr>
                                      <tr class="fondo-boton">
                                        <td>#</td>
                                        <td width="4%">Año</td>
                                        <td width="30%">Descripci&oacute;n</td>
                                        <td>Cantidad</td>
                                        <td width="15%">Unidad</td>
                                        <td>BOB</td>
                                        <td>USD</td>
                                        <td>BOB</td>
                                        <td>USD</td>
                                        <td class="small">Habilitar/Deshabilitar</td>
                                      </tr>
                                  </thead>
                                  <tbody>
                                    <tr class="bg-plomo">
                                      <td>N</td>
                                      <td>
                                        <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="anio_servicio" id="anio_servicio">
                                            <option disabled selected="selected" value="">Año</option>
                                              <?php 
                                                  for ($i=1; $i <=$anios_servicio ; $i++) {
                                                    ?><option value="<?=$i?>"><?=$i?></option><?php    
                                                  }
                                              ?>
                                        </select>
                                      </td>
                                      <td><?php 
                                      if($cod_area==39){
                                        $codigoAreaServ=108;
                                      }else{
                                        if($cod_area==38){
                                          $codigoAreaServ=109;
                                        }else{
                                          $codigoAreaServ=0;
                                        }
                                      }
                                    ?>
                                      <select class="selectpicker form-control form-control-sm" data-live-search="true" name="modal_editservicio" id="modal_editservicio" data-style="fondo-boton">
                                        <option disabled selected="selected" value="">--SERVICIOS--</option>
                                        <?php 
                                         $stmt3 = $dbh->prepare("SELECT idclaservicio,descripcion,codigo from cla_servicios where (codigo_n1=108 or codigo_n1=109) and vigente=1 and codigo_n1=$codigoAreaServ");
                                         $stmt3->execute();
                                         while ($rowServ = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                                          $codigoServX=$rowServ['idclaservicio'];
                                          $nombreServX=$rowServ['descripcion'];
                                          $abrevServX=$rowServ['codigo'];
                                          ?><option value="<?=$codigoServX;?>"><?=$abrevServX?> - <?=$nombreServX?></option><?php 
                                         }
                                        ?>
                                      </select>
                                      </td>
                                      <td class="text-right">
                                           <input type="number" min="1" id="cantidad_servicios" name="cantidad_servicios" class="form-control text-primary text-right" value="1">
                                      </td>
                                      <td>
                                          <select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" name="unidad_servicios" id="unidad_servicios">
                                              <?php 
                                                  $queryUnidad="SELECT * FROM tipos_unidad where cod_estadoreferencial=1 order by codigo";
                                                  $stmtUnidad = $dbh->prepare($queryUnidad);
                                                  $stmtUnidad->execute();
                                                  while ($rowUnidad = $stmtUnidad->fetch(PDO::FETCH_ASSOC)) {
                                                    $codigoUnidad=$rowUnidad['codigo'];
                                                    $nomUnidad=$rowUnidad['nombre'];
                                                    ?><option value="<?=$codigoUnidad?>"><?=$nomUnidad?></option><?php    
                                                  }
                                              ?>
                                          </select>
                                         </td>
                                        <td class="text-right">
                                           <input type="number" id="modal_montoserv" name="modal_montoserv" class="form-control text-primary text-right"  value="0" step="0.01">
                                        </td>
                                        <td class="text-right">
                                           <input type="number" id="modal_montoservUSD" name="modal_montoservUSD" class="form-control text-primary text-right"  value="0" step="0.01">
                                        </td>
                                         <td class="text-right">
                                           <input type="number" id="modal_montoservtotal" name="modal_montoservtotal" class="form-control text-primary text-right"  value="0" step="0.01">
                                         </td>
                                         
                                         <td class="text-right">
                                           <input type="number" id="modal_montoservtotalUSD" name="modal_montoservtotalUSD" class="form-control text-primary text-right" value="0" step="0.01">
                                         </td>
                                      <td>
                                        <div class="btn-group">
                                           <a href="#" class="btn btn-primary btn-sm" id="boton_modalnuevoservicio<?=$an?>" onclick="agregarNuevoServicioSimulacion2(<?=$cod_simulacion?>,<?=$cod_area?>); return false;">
                                             Agregar
                                           </a>
                                         </div>
                                      </td>
                                    </tr>
                                    
                                      <!-- <tr>
                                         <td colspan="4" class="text-center font-weight-bold">Total</td>
                                         <td id="modal_totalmontoserv<?=$an?>" class="text-right"><?=number_format($modal_totalmontopre,2, ',', '')?></td>
                                         <td id="modal_totalmontoservUSD<?=$an?>" class="text-right"><?=number_format($modal_totalmontopre/$usd,2,', ','')?></td>
                                         <td id="modal_totalmontoservtotal<?=$an?>" class="text-right font-weight-bold"><?=number_format($modal_totalmontopretotal,2, ',', '')?></td>
                                         <td id="modal_totalmontoservtotalUSD<?=$an?>" class="text-right font-weight-bold"><?=number_format($modal_totalmontopretotal/$usd,2, ',', '')?></td>
                                         <td></td>
                                       </tr>
                                  </tbody> -->
                               </table>



                            </div>
                        </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
function valida(f) {
  var ok = true;
  var msg = "Habilite los servicios a facturar...\n";
  if(f.elements["comprobante_auxiliar"].value == 0 || f.elements["comprobante_auxiliar"].value == '' )
  {    
    ok = false;
  }

  if(ok == false)
    alert(msg);
  return ok;
}
</script>