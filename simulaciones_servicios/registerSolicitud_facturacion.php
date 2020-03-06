<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();
$cod_simulacion=$cod_s;
$cod_facturacion=$cod_f;
//sacamos datos para la facturacion

//
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
}else {
    $sql="SELECT sc.cod_responsable,sc.cod_cliente,ps.cod_area,ps.cod_unidadorganizacional
    from simulaciones_servicios sc,plantillas_servicios ps
    where sc.cod_plantillaservicio=ps.codigo and sc.cod_estadoreferencial=1 and sc.codigo=$cod_simulacion order by sc.codigo";
    $stmtServicio = $dbh->prepare($sql);
    $stmtServicio->execute();
    $resultServicio = $stmtServicio->fetch();
    $cod_personal = $resultServicio['cod_responsable'];
    $cod_uo = $resultServicio['cod_unidadorganizacional'];
    $cod_area = $resultServicio['cod_area'];
    $cod_cliente = $resultServicio['cod_cliente'];

    $fecha_registro =date('Y-m-d');
    $fecha_solicitudfactura =null;
    $cod_tipoobjeto=obtenerValorConfiguracion(34);//por defecto
    $cod_tipopago = null;    
    $razon_social = null;
    $nit = 0;
    $observaciones = null;
}

?>

<div class="content">
    <div class="container-fluid">
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
                            <select name="cod_uo" id="cod_uo" class="selectpicker form-control form-control-sm" data-style="btn btn-info" required="true">
                                <option value=""></option>
                                <?php 
                                $queryUO = "SELECT codigo,nombre from unidades_organizacionales where cod_estado=1 order by nombre";
                                $statementUO = $dbh->query($queryUO);
                                while ($row = $statementUO->fetch()){ ?>
                                    <option <?=($cod_uo==$row["codigo"])?"selected":"";?><?=($cod_uo!=$row["codigo"])?"disabled":"";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                <?php } ?>
                            </select>
                        </div>
                      </div>
                      <label class="col-sm-2 col-form-label">Area</label>
                        <div class="col-sm-4">
                            <div class="form-group" >
                                <div id="div_contenedor_area_tcc">
                                    <select name="cod_area" id="cod_area" class="selectpicker form-control form-control-sm" data-style="btn btn-info" >
                                        <option value=""></option>
                                        <?php 
                                        $queryArea = "SELECT codigo,nombre FROM  areas WHERE cod_estado=1 order by nombre";
                                        $statementArea = $dbh->query($queryArea);
                                        while ($row = $statementArea->fetch()){ ?>
                                            <option <?=($cod_area==$row["codigo"])?"selected":"";?><?=($cod_area!=$row["codigo"])?"disabled":"";?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                        <?php } ?>
                                    </select>
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
                                    <select name="cod_cliente" id="cod_cliente" class="selectpicker form-control form-control-sm" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" onchange="ajax_Cliente_razonsocial(this)" required="true">
                                        <option value=""></option>
                                        <?php 
                                        $querycliente = "SELECT codigo,nombre FROM  clientes WHERE cod_estadoreferencial=1 order by nombre";
                                        $statementCliente = $dbh->query($querycliente);
                                        while ($row = $statementCliente->fetch()){ ?>
                                            <option <?=($cod_cliente==$row["codigo"])?"selected":"";?><?=($cod_cliente!=$row["codigo"])?"disabled":"";?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                        <?php } ?>
                                    </select>                                
                            </div>
                        </div>
                        <label class="col-sm-2 col-form-label">Responsable</label>
                        <div class="col-sm-4">
                            <div class="form-group">            
                                <?php  $responsable=namePersonal($cod_personal); ?>                    
                                <input type="text" name="cod_personal" id="cod_personal" value="<?=$responsable?>" readonly="true" class="form-control">
                            </div>
                        </div>
                    </div>
                    <!-- fin cliente y responsable -->

                    <div class="row">
                        <label class="col-sm-2 col-form-label">Razón Social</label>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div id="contenedor_razonsocial">
                                    <input class="form-control" type="text" name="razon_social" id="razon_social" required="true" value="<?=$razon_social;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" required="true"/>    
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
                                        $modal_totalmontopre+=$montoPre;
                                        // $modal_totalmontopretotal+=$montoPreTotal;
                                      }
                                      // $iconServ="";
                                      // if(obtenerConfiguracionValorServicio($codCS)==true){
                                      //   $iconServ="check_circle";
                                      // } 
                                      


                                      $montoPre=number_format($montoPre,2,".","");
                                      // $montoPreTotal=number_format($montoPreTotal,2,".","");
                                       ?>
                                       <!-- guardamos las varialbles en un input -->
                                        <input type="hidden" id="servicio<?=$iii?>" name="servicio<?=$iii?>" value="<?=$codCS?>">
                                        <input type="hidden" id="cantidad<?=$iii?>" name="cantidad<?=$iii?>" value="<?=$cantidadPre?>">
                                        <input type="hidden" id="importe<?=$iii?>" name="importe<?=$iii?>" value="<?=$montoPre?>">
                                        <input type="hidden" id="servicio_a<?=$iii?>" name="servicio_a<?=$iii?>">
                                        <input type="hidden" id="cantidad_a<?=$iii?>" name="cantidad_a<?=$iii?>">
                                        <input type="hidden" id="importe_a<?=$iii?>" name="importe_a<?=$iii?>">
                                        <input type="hidden" id="comprobante_auxiliar" name="comprobante_auxiliar" value="<?=$modal_totalmontopre?>">
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
                                                 <input type="checkbox"  <?=($banderaHab==1)?"checked":"";?> id="modal_check<?=$iii?>" onchange="activarInputMontoFilaServicio2('<?=$iii?>')">
                                                 <span class="toggle"></span>
                                               </label>
                                           </div>
                                         </td>
                                       </tr>
                                      <?php
                                      $iii++; 
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
  
                        </div>
                    </div>
              </div>
              <div class="card-footer ml-auto mr-auto">
                <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
                <a href='<?=$urlSolicitudfactura;?>&cod=<?=$cod_simulacion;?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
              </div>
            </div>
          </form>
        </div>
    
    </div>
</div>
<script type="text/javascript">
function valida(f) {
  var ok = true;
  var msg = "Habilite los servicios a facturar\n";
  if(f.elements["comprobante_auxiliar"].value == 0 )
  {    
    ok = false;
  }

  if(ok == false)
    alert(msg);
  return ok;
}
</script>