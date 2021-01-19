<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
//$dbh = new Conexion();
$dbh = new Conexion();
if(isset($_GET['q'])){
    $q=$_GET['q'];
    $s=$_GET['s'];
    $u=$_GET['u'];
    $v=$_GET['v'];
    $id_servicio=$v;
}else{
    $id_servicio=0;
}


$cod_simulacion=$cod_s;
$cod_facturacion=$cod_f;
$cod_sw=$cod_sw;
//sacamos datos para la facturacion
$sql="SELECT sc.nombre,sc.anios,sc.cod_responsable,sc.cod_cliente,ps.cod_area,ps.cod_unidadorganizacional,sc.id_tiposervicio,sc.idServicio
from simulaciones_servicios sc,plantillas_servicios ps
where sc.cod_plantillaservicio=ps.codigo and sc.cod_estadoreferencial=1 and sc.codigo=$cod_simulacion order by sc.codigo";
// echo $sql;
$stmtServicio = $dbh->prepare($sql);
$stmtServicio->execute();
$Codigo_alterno=obtenerCodigoServicioPorPropuestaTCPTCS($cod_simulacion);
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
    $observaciones_2 = $result['observaciones_2'];
    $persona_contacto= $result['persona_contacto'];
    // $anios_servicio = $resultServicio['anios'];
    $nombre_simulacion = $resultServicio['nombre'];//de afuera
    $id_tiposervicio = $resultServicio['id_tiposervicio'];//de afuera
    $name_cliente=nameCliente($cod_cliente);    
    $dias_credito=$result['dias_credito'];
    $correo_contacto=$result['correo_contacto'];
}else {
    $nombre_simulacion = $resultServicio['nombre'];
    if(isset($_GET['q'])){
        $cod_personal=$_GET['q'];
    }else{
        $cod_personal = $resultServicio['cod_responsable'];
    }
    $cod_uo = $resultServicio['cod_unidadorganizacional'];
    $cod_area = $resultServicio['cod_area'];
    $cod_cliente = $resultServicio['cod_cliente'];
    // $anios_servicio = $resultServicio['anios'];
    $id_tiposervicio = $resultServicio['id_tiposervicio'];
    $fecha_registro =date('Y-m-d');
    $fecha_solicitudfactura =$fecha_registro;
    $cod_tipoobjeto=211;//por defecto}    
    $cod_tipopago = null;
    $name_cliente=nameCliente($cod_cliente);    
    $razon_social = $name_cliente;
    $observaciones_2 = null;
    $nit=obtenerNitCliente($cod_cliente);    
    $observaciones = $Codigo_alterno." - ".$name_cliente;
    $persona_contacto=null;
    $dias_credito=obtenerValorConfiguracion(58);
    $correo_contacto=obtenerCorreosCliente($cod_cliente);
    $correo_contacto=trim($correo_contacto,",");
}
$name_tipoPago=obtenerNombreTipoPago($cod_tipoobjeto);
$name_uo=nameUnidad($cod_uo);
$name_area=trim(abrevArea($cod_area),'-');

$contadorRegistros=0;


$descuento_cliente=obtenerDescuentoCliente($cod_cliente);
$cod_defecto_deposito_cuenta=obtenerValorConfiguracion(55);
$cod_defecto_cod_tipo_credito=obtenerValorConfiguracion(48);
?>
<script>
  numFilas=<?=$contadorRegistros;?>;
  cantidadItems=<?=$contadorRegistros;?>;
</script>
<div class="content">
    <div class="container-fluid">
        <div style="overflow-y:scroll;">
            <div class="col-md-12">
            <form id="formSoliFactTcp" class="form-horizontal" action="<?=$urlSaveSolicitudfactura;?>" method="post" onsubmit="return valida(this)" enctype="multipart/form-data">
                <?php 
                  if(isset($_GET['q'])){
                    ?><input type="hidden" name="usuario_ibnored" id="usuario_ibnored" value="<?=$q;?>">
                    <input type="hidden" name="usuario_ibnored_s" id="usuario_ibnored_s" value="<?=$s;?>">
                    <input type="hidden" name="usuario_ibnored_u" id="usuario_ibnored_u" value="<?=$u;?>">
                    <input type="hidden" name="usuario_ibnored_v" id="usuario_ibnored_v" value="<?=$v;?>"><?php
                  }
                  ?> 
                <input type="hidden" name="cod_defecto_deposito_cuenta" id="cod_defecto_deposito_cuenta" value="<?=$cod_defecto_deposito_cuenta?>"/>
                <input type="hidden" name="cod_defecto_cod_tipo_credito" id="cod_defecto_cod_tipo_credito" value="<?=$cod_defecto_cod_tipo_credito?>"/>
                <input type="hidden" name="Codigo_alterno" id="Codigo_alterno" value="<?=$Codigo_alterno;?>"/>  
                <input type="hidden" name="cod_simulacion" id="cod_simulacion" value="<?=$cod_simulacion;?>"/>
                <input type="hidden" name="cod_facturacion" id="cod_facturacion" value="<?=$cod_facturacion;?>"/>
                <input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">
                <input type="hidden" name="IdTipo" id="IdTipo" value="<?=$id_tiposervicio;?>"><!-- //tipo de servicio -->
                <input type="hidden" name="tipo_solicitud" id="tipo_solicitud" value="1">
                <input type="hidden" name="tipo_aux" id="tipo_aux" value="1"><!-- //nos indica de donde va para editar adjuntos -->

                <div class="card">
                    <div class="card-header <?=$colorCard;?> card-header-text">
                        <div class="card-text">
                          <h4 class="card-title"><?php if ($cod_facturacion == 0) echo "Registrar "; else echo "Editar ";?>Solicitud de Facturación</h4>                      
                        </div>
                        <h4 class="card-title" align="center"><b>Propuesta/Servicio: <?=$Codigo_alterno?> - <?=$nombre_simulacion?> - <?=$name_area?></b></h4>
                    </div>
                    <div class="card-body ">
                        <?php require_once 'cabecera_registro_sol_fac.php';?>
                        <!-- archivos -->
                        <div class="row">
                            <div class="col-sm-12">
                                <center>
                                    <div class="btn-group">
                                        <a title="Subir Archivos Respaldo (shift+r)" href="#modalFile" data-toggle="modal" data-target="#modalFile" class="btn btn-default btn-sm">Archivos 
                                            <i class="material-icons"><?=$iconFile?></i><span id="narch" class="bg-warning"></span>
                                        </a>
                                    </div> 
                                </center>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header <?=$colorCard;?> card-header-text">
                                <div class="card-text">
                                  <h6 class="card-title">Detalle Solicitud Facturación</h6>
                                </div>
                            </div>
                            <div class="card-body ">
                                <table class="table table-bordered table-condensed table-striped table-sm">
                                     <thead>
                                          <tr class="fondo-boton">
                                            <!-- <th>#</th> -->
                                            <th >Año</th>
                                            <th>Item</th>
                                            <th>Cant.</th>
                                            <th>Precio(BOB)</th>
                                            <th>Desc(%)</th>
                                            <th>Desc(BOB)</th>                                            
                                            <th width="6%">Importe<br>(BOB)</th>
                                            <th width="6%">Importe<br>Pagado</th>
                                            <th width="6%">Importe<br>a pagar</th>  
                                            <th width="40%">Glosa</th>
                                            <th class="small">H/D</th>  
                                          </tr>
                                      </thead>
                                      <tbody>                                
                                        <?php 
                                        $iii=1;
                                        $queryPr="SELECT s.codigo,s.cod_claservicio,t.Descripcion as nombre_serv,s.cantidad,s.monto,s.habilitado,s.cod_tipounidad,s.cod_anio,s.cantidad_editado,1 as tipo_item FROM simulaciones_servicios_tiposervicio s, cla_servicios t where s.cod_simulacionservicio=$cod_simulacion and s.cod_claservicio=t.IdClaServicio";
                                        if ($cod_facturacion > 0){
                                            $queryPr.=" UNION ";
                                            $queryPr.="SELECT d.codigo,d.cod_claservicio,(select cs.Descripcion from cla_servicios cs where cs.IdClaServicio=d.cod_claservicio) as descripcion,d.cantidad,d.precio,1,1,null,d.cantidad,tipo_item as cantidad_editado from solicitudes_facturaciondetalle d where d.tipo_item=2 and d.cod_solicitudfacturacion=$cod_facturacion ORDER BY nombre_serv";
                                        }
                                       // echo $queryPr;
                                        $stmt = $dbh->prepare($queryPr);
                                        $stmt->execute();
                                        $modal_totalmontopre=0;$modal_totalmontopretotal=0;
                                        while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $tipo_item=$rowPre['tipo_item'];//hace referencia a los items adiciones insertados
                                            $codigoPre=$rowPre['codigo'];
                                            $codCS=$rowPre['cod_claservicio'];
                                            $tipoPre=$Codigo_alterno." - ".$rowPre['nombre_serv'];
                                            $cantidadPre=$rowPre['cantidad_editado'];                                            

                                            // echo $cantidadPre."-";
                                            // $cantidadPre=1;
                                            // $cantidadPre_x=$rowPre['cantidad_editado'];
                                            $montoPre=$rowPre['monto'];
                                            $montoPre=number_format($montoPre,2,".","");
                                            $descuento_bob_cliente=$montoPre*$descuento_cliente; 
                                            // $montoPreTotal=$montoPre*$cantidadEPre;
                                            $banderaHab=$rowPre['habilitado'];
                                            $codTipoUnidad=$rowPre['cod_tipounidad'];
                                            $cod_anio=$rowPre['cod_anio'];

                                            $monto_pagar=$montoPre;
                                            $saldo=$montoPre*$cantidadPre;
                                            $monto_total_pagado=0;
                                            if($banderaHab!=0){
                                                $descuento_porX=0;
                                                $descuento_bobX=0;
                                                $descripcion_alternaX=$tipoPre;
                                                // $modal_totalmontopre+=$montoPre;
                                                $sw="";
                                                if($cod_facturacion>0){
                                                    //parte del controlador de check
                                                    //para la parte de editar
                                                    $sqlControlador="SELECT sfd.precio,sfd.descuento_por,sfd.descuento_bob,sfd.descripcion_alterna from solicitudes_facturacion sf,solicitudes_facturaciondetalle sfd where sf.codigo=sfd.cod_solicitudfacturacion and sf.cod_simulacion_servicio=$cod_simulacion and sfd.cod_claservicio=$codCS and sf.codigo=$cod_facturacion and tipo_solicitud=1";

                                                    // echo $sqlControlador;
                                                    $stmtControlado = $dbh->prepare($sqlControlador);
                                                    $stmtControlado->execute();
                                                    while ($rowPre = $stmtControlado->fetch(PDO::FETCH_ASSOC)) {

                                                        $sw="checked";
                                                        $montoPre=$rowPre['precio'];
                                                        $preciox=$rowPre['precio']*$cantidadPre;
                                                        $descuento_porX=$rowPre['descuento_por'];
                                                        $descuento_bobX=$rowPre['descuento_bob'];
                                                        $descripcion_alternaX=$rowPre['descripcion_alterna'];
                                                        if($tipo_item==2){
                                                            $monto_pagar=$montoPre+$descuento_bobX/$cantidadPre;
                                                            
                                                        }else{
                                                            $montoPre=$montoPre+$descuento_bobX;
                                                        }
                                                    }
                                                }

                                                $sw2="";//para registrar nuevos, impedir los ya registrados
                                                $monto_servicio=verificar_pago_servicios_tcp_solfac($id_servicio,$codCS);
                                                ?><script>console.log("MONTO SERVICIO: "+<?=$monto_servicio?>+"CLA:"+<?=$codCS?>+"-S:"+<?=$id_servicio?>);</script><?php
                                                $monto_servicio=number_format($monto_servicio,2,".","");
                                                if($monto_servicio!=0){
                                                    $saldo=$monto_pagar*$cantidadPre-$monto_servicio;
                                                    $monto_total_pagado=$monto_servicio;    
                                                    if(number_format($monto_servicio,2,".","")==number_format($montoPre,2,".","")){

                                                        $sw2="readonly style='background-color:#cec6d6;'";
                                                        $saldo=0;
                                                    }
                                                }
                                                //impedir ya registrados
                                                $sqlControlador2="SELECT sfd.precio,sfd.descuento_por,sfd.descuento_bob,sfd.descripcion_alterna from solicitudes_facturacion sf,solicitudes_facturaciondetalle sfd where sf.codigo=sfd.cod_solicitudfacturacion and sf.cod_simulacion_servicio=$cod_simulacion and sfd.cod_claservicio=$codCS and tipo_solicitud=1 and sf.cod_estadosolicitudfacturacion!=2";
                                                // echo $sqlControlador2;
                                                $stmtControlador2 = $dbh->prepare($sqlControlador2);
                                                $stmtControlador2->execute();                                           
                                                //sacamos el monto total
                                                $sqlControladorTotal="SELECT SUM(sfd.precio) as precio from solicitudes_facturacion sf,solicitudes_facturaciondetalle sfd where sf.codigo=sfd.cod_solicitudfacturacion and sf.cod_simulacion_servicio=$cod_simulacion  and sfd.cod_claservicio=$codCS and tipo_solicitud=1 and sf.cod_estadosolicitudfacturacion!=2";
                                                 // echo $sqlControladorTotal;
                                                $stmtControladorTotal = $dbh->prepare($sqlControladorTotal);
                                                $stmtControladorTotal->execute();
                                                $resultMontoTotal=$stmtControladorTotal->fetch();
                                                $precio_total_x=$resultMontoTotal['precio']*$cantidadPre;
                                                if($precio_total_x>0){
                                                    $saldo=$monto_pagar*$cantidadPre-$precio_total_x;
                                                }
                                                if($precio_total_x==null || $precio_total_x=='' || $precio_total_x==' ' || $precio_total_x==0){
                                                }else $monto_total_pagado=$precio_total_x;
                                                $cont_items_aux=0;
                                                while ($rowPre = $stmtControlador2->fetch(PDO::FETCH_ASSOC)) {
                                                    // if($sw!="checked"){
                                                    //     $sw2="readonly style='background-color:#cec6d6;'";
                                                    //     $montoPre=$rowPre['precio']+$rowPre['descuento_bob'];
                                                    //     $descuento_porX=$rowPre['descuento_por'];
                                                    //     $descuento_bobX=$rowPre['descuento_bob'];
                                                    //     $descripcion_alternaX=$rowPre['descripcion_alterna'];
                                                    // }

                                                     ?><script>console.log("precio: " + "<?=$precio_total_x?>");</script><?php
                                                    $cont_items_aux++;
                                                    if($sw!="checked"){//si el item  es para  editar
                                                         ?><script>console.log("ch: " + "checked - montopagar:"+<?=$monto_pagar?>+"_montopagado:"+<?=$monto_total_pagado?>);</script><?php
                                                        if(($monto_pagar*$cantidadPre)==$monto_total_pagado){

                                                            $sw2="readonly style='background-color:#cec6d6;'";
                                                            $saldo=0;
                                                        }
                                                        if($rowPre['descuento_bob']==null || $rowPre['descuento_bob']==0 || $rowPre['descuento_bob']=='' || $rowPre['descuento_bob']==' '){
                                                        }else{
                                                            // $monto_total_pagado-=$rowPre['descuento_bob'];
                                                            // echo $monto_pagar."-".$monto_total_pagados;
                                                            $saldo=$monto_pagar*$cantidadPre-$monto_total_pagado;
                                                        }
                                                        // $montoPre=$rowPre['precio']+$rowPre['descuento_bob'];
                                                        $descuento_porX=$rowPre['descuento_por'];
                                                        $descuento_bobX=$rowPre['descuento_bob'];
                                                        $descripcion_alternaX=$rowPre['descripcion_alterna'];
                                                    }else{                                                      
                                                        $monto_total_pagado=$precio_total_x-$preciox;
                                                        $saldo=$preciox;
                                                    }
                                                }
                                                ?><script>console.log("sw: " + "<?=$sw2?>");</script><?php
                                                ?>
                                                <!-- guardamos las varialbles en un input -->
                                                <input type="hidden" id="cod_serv_tiposerv<?=$iii?>" name="cod_serv_tiposerv<?=$iii?>" value="<?=$codigoPre?>">
                                                <input type="hidden" id="servicio<?=$iii?>" name="servicio<?=$iii?>" value="<?=$codCS?>">
                                                 <input type="hidden" id="nombre_servicio<?=$iii?>" name="nombre_servicio<?=$iii?>" value="<?=$tipoPre?>">
                                                <input type="hidden" id="cantidad<?=$iii?>" name="cantidad<?=$iii?>" value="<?=$cantidadPre?>">
                                                <input type="hidden" id="importe<?=$iii?>" name="importe<?=$iii?>" value="<?=$monto_pagar?>">
                                                <input type="hidden" id="tipo_item<?=$iii?>" name="tipo_item<?=$iii?>" value="<?=$tipo_item?>">

                                                <!-- aqui se captura los servicios activados -->
                                                <input type="hidden" id="cod_serv_tiposerv_a<?=$iii?>" name="cod_serv_tiposerv_a<?=$iii?>">
                                                <input type="hidden" id="servicio_a<?=$iii?>" name="servicio_a<?=$iii?>">
                                                <input type="hidden" id="cantidad_a<?=$iii?>" name="cantidad_a<?=$iii?>">
                                                <input type="hidden" id="importe_a<?=$iii?>" name="importe_a<?=$iii?>">
                                                <tr>
                                                    <!-- <td><?=$iii?></td> -->
                                                    <td class="text-left"><?=$cod_anio?> </td>
                                                    <td class="text-left"><?=$tipoPre?></td>
                                                    <td class="text-right"><?=$cantidadPre?></td>
                                                    <td class="text-right"><input type="number" step="any" id="monto_precio<?=$iii?>" name="monto_precio<?=$iii?>" class="form-control"  value="<?=$monto_pagar?>" onkeyup="activarInputMontoFilaServicio2()" <?=$sw2?> readonly="true"></td>
                                                    <!--  descuentos -->
                                                    <td class="text-right"><input type="number" step="any" class="form-control" name="descuento_por<?=$iii?>" id="descuento_por<?=$iii?>" value="<?=$descuento_porX?>" min="0" max="<?=$descuento_cliente?>" onkeyup="descuento_convertir_a_bolivianos(<?=$iii?>)" <?=$sw2?>></td>                                             
                                                    <td class="text-right"><input type="number" class="form-control" name="descuento_bob<?=$iii?>" id="descuento_bob<?=$iii?>" value="<?=$descuento_bobX?>" min="0" max="<?=$descuento_bob_cliente?>" onkeyup="descuento_convertir_a_porcentaje(<?=$iii?>)" <?=$sw2?>></td>                                        
                                                    <!-- total -->
                                                    <td class="text-right"><input type="hidden" name="modal_importe<?=$iii?>" id="modal_importe<?=$iii?>"><input type="text" class="form-control" name="modal_importe_dos<?=$iii?>" id="modal_importe_dos<?=$iii?>" style ="background-color: #ffffff;" readonly></td>
                                                    <td>
                                                        <input type="hidden" name="modal_importe_pagado_dos_a<?=$iii?>" id="modal_importe_pagado_dos_a<?=$iii?>" value="<?=$monto_total_pagado;?>">
                                                        <input type="text" class="form-control" name="modal_importe_pagado_dos<?=$iii?>" id="modal_importe_pagado_dos<?=$iii?>" readonly value="<?=number_format($monto_total_pagado,2);?>">
                                                    </td>
                                                    <td>
                                                        <input type="number" step="any" id="importe_a_pagar<?=$iii?>" name="importe_a_pagar<?=$iii?>" class="form-control text-primary text-right"  value="<?=$saldo?>" step="any" onkeyup="verificar_item_activo(<?=$iii?>)" <?=$sw2?>>
                                                    </td>


                                                                                              
                                                  <td>
                                                    <textarea name="descripcion_alterna<?=$iii?>" id="descripcion_alterna<?=$iii?>" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();" <?=$sw2?>><?=$descripcion_alternaX?></textarea>
                                                     <!-- <input type="text" > -->
                                                  </td>
                                                  <!-- checkbox -->
                                                  <td>
                                                    <?php if($sw2!="readonly style='background-color:#cec6d6;'"){?>
                                                        <div class="togglebutton">
                                                           <label>
                                                             <input type="checkbox"  id="modal_check<?=$iii?>" onchange="calcularTotalFilaServicio2Costos()" <?=$sw?> >
                                                             <span class="toggle"></span>
                                                           </label>
                                                       </div>
                                                    <?php }else{?>
                                                      <div class="togglebutton d-none">
                                                           <label>
                                                             <input type="checkbox"  id="modal_check<?=$iii?>" >
                                                             <span class="toggle"></span>
                                                           </label>
                                                       </div>                                                
                                                    <?php }?>
                                                  </td><!-- fin checkbox -->
                                               </tr>

                                            <?php   $iii++;  }
                                                                                                                    
                                            // $montoPreTotal=number_format($montoPreTotal,2,".","");
                                            ?>
                                            <script>
                                                window.onload = activarInputMontoFilaServicio2;
                                                window.onload = calcularTotalFilaServicio2Costos;
                                            </script>

                                            <?php
                                        
                                        } ?>  
                                        <tr>
                                            <td colspan="6">Monto Total</td>
                                            <td><input style="background:#ffffff" class="form-control" type="text" value="0" name="modal_totalmontoserv" id="modal_totalmontoserv" readonly="true" /></td>
                                            <td><input style="background:#ffffff" class="form-control" type="text" value="0" name="modal_totalmontoserv_pagado" id="modal_totalmontoserv_pagado" readonly="true" /></td>
                                            <td><input type="hidden" value="0" name="modal_totalmontoserv_costo_a" id="modal_totalmontoserv_costo_a"/><input style="background:#ffffff" class="form-control" type="text" value="0" name="modal_totalmontoserv_costo" id="modal_totalmontoserv_costo" readonly="true" /></td>
                                            <td></td>
                                        </tr>                      
                                      </tbody>
                                </table>

                                <input type="hidden" id="modal_numeroservicio" name="modal_numeroservicio" value="<?=$iii?>">                    
                                <input type="hidden" id="modal_totalmontos" name="modal_totalmontos" value="0">
                                <!-- <script>activarInputMontoFilaServicio2();</script>   -->
                                <input type="hidden" id="comprobante_auxiliar" name="comprobante_auxiliar" value="0">
                               <!--  <div class="row">
                                    <label class="col-sm-5 col-form-label" style="color:#000000">Monto Total</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">                                        
                                            <input style="background:#ffffff" class="form-control" type="text" value="0" name="modal_totalmontoserv" id="modal_totalmontoserv" step="any" readonly="true" />                                            
                                        </div>
                                    </div>
                                </div> -->
                                <fieldset id="fiel" style="width:100%;border:0;">
                                    <button title="Agregar Servicios" type="button" id="add_boton" name="add" class="btn btn-warning btn-round btn-fab" onClick="AgregarSeviciosFacturacion2(this)">
                                        <i class="material-icons">add</i>
                                    </button><span style="color:#084B8A;"><b> SERVICIOS ADICIONALES</b></span>
                                    <div class="row" style="background-color:#1a2748">
                                        <th><label class="col-sm-4 col-form-label" style="color:#ff9c14">Servicios</label>
                                        <label class="col-sm-1 col-form-label" style="color:#ff9c14">Cant</label>
                                        <label class="col-sm-1 col-form-label" style="color:#ff9c14">Precio(BOB)</label>
                                        <label class="col-sm-1 col-form-label" style="color:#ff9c14">Desc(%)</label>
                                        <label class="col-sm-1 col-form-label" style="color:#ff9c14">Desc(BOB)</label>
                                        <label class="col-sm-1 col-form-label" style="color:#ff9c14">Importe(BOB)</label>
                                        <label class="col-sm-2 col-form-label" style="color:#ff9c14">Glosa</label>
                                        <label class="col-sm-1 col-form-label" style="color:#ff9c14">Eliminar</label>
                                    </div>
                                    

                                    <div id="div">  
                                        <div class="h-divider">
                                        
                                        </div>
                                    </div>
                                    

                                </fieldset>
                                <div class="row">
                                    <label class="col-sm-5 col-form-label" style="color:#000000">Monto Total + Servicios Adicionales</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">                                            
                                            <input style="background:#ffffff" class="form-control"  name="monto_total" id="monto_total"  readonly="readonly" value="0" step="any" />
                                            <input  class="form-control" type="hidden" name="monto_total_a" id="monto_total_a"  readonly="readonly" value="0"  />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                    
                  </div>
                  <div class="card-footer ml-auto mr-auto">
                    <button type="submit" class="<?=$buttonNormal;?>">Guardar</button><?php
                    if(isset($_GET['q'])){//desde intranet ?>
                            <a href='<?=$urlListSol?>&q=<?=$q?>&v=<?=$v?>&s=<?=$s?>&u=<?=$u?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> IR A SF </a>
                        <?php 
                    }else{//desde ifinanciero
                        ?><a href='<?=$urlListSol?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> IR A SF </a><?php
                    }

                    ?>
                    
                  </div>
                </div>

                <?php  require_once 'simulaciones_servicios/modal_subir_archivos.php';?>
            </form>                  
            </div>
        </div>
    </div>
</div>

<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<div class="modal fade modal-arriba modal-primary" id="modalAgregarProveedor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
            <div class="card-header card-header-warning card-header-icon">
                <div class="card-icon">
                    <i class="material-icons text-dark">ballot</i>
                 </div>
                  <h4 class="card-title">Contacto</h4>
            </div>
            <div class="card-body">
                 <div id="datosProveedorNuevo">
                   
                 </div> 
                <div class="form-group float-right">
                        <button type="button" onclick="guardarDatoscontacto()" class="btn btn-info btn-round">Agregar</button>
                </div>
          </div>
      </div>  
    </div>
</div>
<script>$('.selectpicker').selectpicker("refresh");</script>
<?php  require_once 'simulaciones_servicios/modal_facturacion.php';?>
<script>
    $(document).ready(function() {
        var cod_facturacion=document.getElementById("cod_facturacion").value;
        if(cod_facturacion>0){        
            tablaGeneral_tipoPagos_solFac();
            tablaGeneral_areas_solFac();
            // tablaGeneral_areas_solFacNormas();
            $("#nfac").html(itemTipoPagos_facturacion[0].length);
            $("#nfacAreas").html(itemAreas_facturacion[0].length);
        }
    });
</script>
<script type="text/javascript">
    function valida(f) {
        var ok = true;
        var msg = "El monto Total no debe ser '0' o 'negativo', Habilite los Items que desee facturar...\n";  
        var coprobante_auxiliar = f.elements["monto_total_a"].value;
        
        if(f.elements["comprobante_auxiliar"].value == 0 || f.elements["comprobante_auxiliar"].value < 0 || f.elements["comprobante_auxiliar"].value == '')
        {                
            ok = false;
        }
        if(f.elements["monto_total_a"].value>0)
        {            
            ok = true;
        }
        var cod_tipopago=f.elements["cod_tipopago"].value;
        var cod_defecto_deposito_cuenta=$("#cod_defecto_deposito_cuenta").val();
        if(cod_tipopago==cod_defecto_deposito_cuenta){
            if(f.elements["cantidad_archivosadjuntos"].value==0){
                 var msg = "Por favor agregue Archivo Adjunto.";        
                ok = false;
            }
        }
        if(ok == false)    
            Swal.fire("Informativo!",msg, "warning");
        return ok;
    }
</script>
<!-- objeto tipo de pago -->
<?php 
    $lan=sizeof($cont);//filas si lo hubiese         
    for ($i=0; $i < $lan; $i++) {
      ?>
      <script>var detalle_tipopago=[];</script>
      <?php      
        for ($j=0; $j < $cont[$i]; $j++) {
             if($cont[$i]>0){?>
                <script>
                    detalle_tipopago.push({codigo:<?=$datos[$i][$j]->codigo?>,cod_tipopago:<?=$datos[$i][$j]->cod_tipopago?>,nombrex:'<?=$datos[$i][$j]->nombrex?>'});

                </script>

              <?php
              }          
            }
        ?><script>itemTipoPagos_facturacion_aux.push(detalle_tipopago);</script><?php                    
    }
?>
<!-- objeto Areas servicio -->
<?php 
    $lanAreas=sizeof($contAreas);
    for ($i=0; $i < $lanAreas; $i++) {
      ?>
      <script>var detalle_areas=[];</script>
      <?php
        for ($j=0; $j < $contAreas[$i]; $j++) {            
             if($contAreas[$i]>0){?>
                <script>
                    detalle_areas.push({codigo:<?=$datosAreas[$i][$j]->codigo?>,cod_area:<?=$datosAreas[$i][$j]->cod_area?>,nombrex:'<?=$datosAreas[$i][$j]->nombrex?>',abrevx:'<?=$datosAreas[$i][$j]->abrevx?>'});
                </script>

              <?php         
              }          
            }
        ?><script>itemAreas_facturacion_aux.push(detalle_areas);</script><?php                    
    }
?>
<!-- objeto unidades servicio -->
<?php 
    $lanUnidades=sizeof($contUnidades);
    for ($i=0; $i < $lanUnidades; $i++) {
      ?>
      <script>var detalle_unidades=[];</script>
      <?php
        for ($j=0; $j < $contUnidades[$i]; $j++) {            
             if($contUnidades[$i]>0){?>
                <script>
                    detalle_unidades.push({codigo:<?=$datosUnidades[$i][$j]->codigo?>,cod_unidad:<?=$datosUnidades[$i][$j]->cod_unidad?>,nombrex:'<?=$datosUnidades[$i][$j]->nombrex?>'});
                </script>

              <?php         
              }          
            }
        ?><script>itemUnidades_facturacion_aux.push(detalle_unidades);</script><?php                    
    }
?>