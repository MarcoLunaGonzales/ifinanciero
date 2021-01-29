<?php

require_once 'conexion.php';
require_once 'conexion_externa.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();


if(isset($_GET['q'])){
  $q=$_GET['q'];
  $s=$_GET['s'];
  $u=$_GET['u'];
  $v=$_GET['v'];
}
if(isset($_GET['cod_sw'])){
  $cod_sw=$_GET['cod_sw']; 
}
$dbhIBNO = new ConexionIBNORCA();

$IdServicio=$IdServicio;
$cod_facturacion=$cod_facturacion;
//$cod_simulacion=$cod_simulacion;
//sacamos datos para la facturacion
$stmtIBNO = $dbhIBNO->prepare("SELECT * from servicios s where IdServicio=$IdServicio");
$stmtIBNO->execute();
$resultServicio = $stmtIBNO->fetch();
$IdTipo = $resultServicio['IdTipo'];
$Codigo_alterno = $resultServicio['Codigo'];

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
    $nombre_simulacion = $resultServicio['Descripcion'];
    $name_cliente=nameCliente($cod_cliente);
    $dias_credito=$result['dias_credito'];
    $correo_contacto=$result['correo_contacto'];

}else {
    $nombre_simulacion = $resultServicio['Descripcion'];
     if(isset($_GET['q'])){
        $cod_personal=$_GET['q'];
    }else{
        $cod_personal =$_SESSION["globalUser"];
    }
    

    $cod_uo = $resultServicio['IdOficina'];
    $cod_area = $resultServicio['IdArea'];
    $cod_cliente = $resultServicio['IdCliente'];    
    $fecha_registro =date('Y-m-d');
    $fecha_solicitudfactura =$fecha_registro;
    $cod_tipoobjeto=211;//por defecto}
    $cod_tipopago = null;
    $name_cliente=nameCliente($cod_cliente);
    $nit=obtenerNitCliente($cod_cliente);
    $razon_social = $name_cliente;    
    $observaciones = $Codigo_alterno." - ".$name_cliente;
    $observaciones_2=null;
    $persona_contacto= null;
    $dias_credito=obtenerValorConfiguracion(58);
    $correo_contacto=obtenerCorreosCliente($cod_cliente);
    $correo_contacto=trim($correo_contacto,",");
    
    // echo "aqui";
}
$name_tipoPago=obtenerNombreTipoPago($cod_tipoobjeto);
$name_uo=nameUnidad($cod_uo);
$name_area=trim(abrevArea($cod_area),'-');
$contadorRegistros=0;
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
                <input type="hidden" name="cod_defecto_deposito_cuenta" id="cod_defecto_deposito_cuenta" value="<?=$cod_defecto_deposito_cuenta?>"/>
                 <input type="hidden" name="cod_defecto_cod_tipo_credito" id="cod_defecto_cod_tipo_credito" value="<?=$cod_defecto_cod_tipo_credito?>"/>
                <input type="hidden" name="Codigo_alterno" id="Codigo_alterno" value="<?=$Codigo_alterno;?>"/>
                <input type="hidden" name="cod_simulacion" id="cod_simulacion" value="<?=$IdServicio;?>"/>
                <input type="hidden" name="cod_facturacion" id="cod_facturacion" value="<?=$cod_facturacion;?>"/>
                <input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">
                <input type="hidden" name="tipo_solicitud" id="tipo_solicitud" value="1">
                <input type="hidden" name="tipo_aux" id="tipo_aux" value="1"><!-- //nos indica de donde va para editar adjuntos -->
                <input type="hidden" name="desde_servicio" id="desde_servicio" value="1">
                <?php 
                if(isset($_GET['q'])){
                    ?><input type="hidden" name="usuario_ibnored" id="usuario_ibnored" value="<?=$q;?>">
                    <input type="hidden" name="usuario_ibnored_s" id="usuario_ibnored_s" value="<?=$s;?>">
                    <input type="hidden" name="usuario_ibnored_u" id="usuario_ibnored_u" value="<?=$u;?>">
                    <input type="hidden" name="usuario_ibnored_v" id="usuario_ibnored_v" value="<?=$v;?>"><?php
                }
                if(isset($_GET['cod_sw'])){
                    ?><input type="hidden" name="cod_sw" id="cod_sw" value="<?=$cod_sw;?>">
                    <?php
                }                
                ?> 

                <!-- para agregar nuevos servicios -->
                <input type="hidden" name="IdTipo" id="IdTipo" value="<?=$IdTipo;?>">
                <div class="card">
                  <div class="card-header <?=$colorCard;?> card-header-text">
                    <div class="card-text">
                      <h4 class="card-title"><?php if ($cod_facturacion == 0) echo "Registrar "; else echo "Editar ";?>Solicitud de Facturación</h4>                      
                    </div>
                    <h4 class="card-title" align="center"><b>Propuesta/Servicio: <?=$Codigo_alterno?> - <?=$nombre_simulacion?></b></h4>
                  </div>
                  <div class="card-body ">
                        <?php require_once 'simulaciones_servicios/cabecera_registro_sol_fac.php';?>
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
                                            <th>#</th>
                                            <th>Item</th>
                                            <th>Cant.</th>
                                            <th width="6%">Precio<br>(BOB)</th>
                                            <th>Desc<br>(%)</th>
                                            <th>Desc<br>(BOB)</th>                                            
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
                                        $cantidad_total_registrado=0;
                                        $cantidad_saldo=0;
                                        $cantidad_inicial=0;
                                        $saldo_real=0;
                                        $items_repetidos=0;

                                        //$queryPr="SELECT s.IdDetServicio,s.IdClaServicio,s.Cantidad,s.PrecioUnitario,1 as tipo_item from ibnorca.serviciopresupuesto s where  s.IdServicio=$IdServicio";
                                        /*$queryPr="SELECT c.IdCotizacion, s.IdDetServicio,s.IdClaServicio,s.Cantidad,s.PrecioUnitario,1 as tipo_item 
                                            from ibnorca.serviciopresupuesto s 
                                            INNER JOIN ibnorca.cotizaciones c ON c.IdCotizacion=s.IdCotizacion
                                            where  s.IdServicio=$IdServicio AND ibnorca.d_clasificador(ibnorca.id_estadoobjeto(196, c.IdCotizacion))='Adjudicada';";*/
                                  $queryPr="SELECT c.IdCotizacion,c.Descuento,s.IdDetServicio,s.IdClaServicio,s.Cantidad,s.PrecioUnitario, 1 AS tipo_item 
                                           FROM
                                               ibnorca.serviciopresupuesto s
                                               INNER JOIN ibnorca.cotizaciones c ON c.IdCotizacion = s.IdCotizacion 
                                           WHERE
                                               s.IdServicio = $IdServicio
                                               AND ibnorca.d_clasificador (
                                               ibnorca.id_estadoobjeto ( 196, c.IdCotizacion ))= 'Adjudicada';";
                                        //echo $queryPr;
                                        if ($cod_facturacion > 0){
                                            $queryPr.=" UNION ";                                            
                                            $queryPr.="SELECT d.codigo,0 as Descuento,d.cod_claservicio,d.cantidad,d.precio,tipo_item from solicitudes_facturaciondetalle d where d.tipo_item=2 and d.cod_solicitudfacturacion=$cod_facturacion";
                                        }
                                        // echo $queryPr;
                                        $stmt = $dbh->prepare($queryPr);
                                        $stmt->execute();
                                        $modal_totalmontopre=0;$modal_totalmontopretotal=0;
                                        while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $tipo_item=$rowPre['tipo_item'];//hace referencia a los items adiciones insertados
                                            $codigoPre=$rowPre['IdDetServicio'];
                                            $codCS=$rowPre['IdClaServicio'];
                                            // $tipoPre=$rowPre['descripcion'];
                                            $cantidadPre=$rowPre['Cantidad'];//cantidad inicial
                                            $descuentoFila=$rowPre['Descuento'];//descuento
                                            $cantidadPre=(double)$cantidadPre;

                                            $cantidad_saldo=$cantidadPre;
                                            $cantidad_inicial=$cantidadPre;
                                            // $cantidadPre=1;
                                            $montoPre=$rowPre['PrecioUnitario'];
                                            $montoPre=number_format($montoPre,2,".","");                                            
                                            $tipoPre=$Codigo_alterno." - ".descripcionClaServicio($codCS);
                                            $montoPreTotal=($montoPre*$cantidadPre);
                                            //$montoPreTotal=($montoPre*$cantidadPre)-((($montoPre*$cantidadPre)*$descuentoFila)/100);
                                            $banderaHab=1;
                                            $codTipoUnidad=0;

                                            $monto_pagar=$montoPre;
                                            $saldo=$montoPre*$cantidadPre;//saldo inicial monto
                                            $saldo_real=$saldo;
                                            $monto_total_pagado=0;
                                            if($banderaHab!=0){
                                                $descuento_porX=0;
                                                $descuento_bobX=0;
                                                $descripcion_alternaX=$tipoPre;                                            
                                                $sw="";

                                                //sacamos el monto total de registros
                                                $sqlControladorTotal="SELECT sfd.precio,sfd.cantidad from solicitudes_facturacion sf,solicitudes_facturaciondetalle sfd where sf.codigo=sfd.cod_solicitudfacturacion and sf.cod_simulacion_servicio=$IdServicio  and sfd.cod_claservicio=$codCS and tipo_solicitud=3 and sf.cod_estadosolicitudfacturacion!=2";
                                                 // echo $sqlControladorTotal;
                                                $stmtControladorTotal = $dbh->prepare($sqlControladorTotal);
                                                $stmtControladorTotal->execute();
                                                $precio_total_x=0;
                                                $cantidad_total_registrado=0;
                                                while ($rowPre = $stmtControladorTotal->fetch(PDO::FETCH_ASSOC)) {
                                                    $precio_total_x+=$rowPre['precio']*$rowPre['cantidad'];
                                                    $cantidad_total_registrado+=$rowPre['cantidad'];
                                                }
                                                /*if($itemServicioAux==$IdServicio&&$codSC==$itemServicioFilaAux){
                                                    $precio_total_x=0;
                                                    $cantidad_total_registrado=0;
                                                }*/
                                                
                                                
                                                // $resultMontoTotal=$stmtControladorTotal->fetch();
                                                if($precio_total_x>0){
                                                    //$saldo=$monto_pagar*$cantidad_saldo-$precio_total_x;
                                                    $saldo=($monto_pagar*$cantidadPre)-$precio_total_x;
                                                    $saldo_real=$saldo;
                                                    if($cod_facturacion==0){
                                                        $cantidad_saldo=$cantidad_inicial-$cantidad_total_registrado;    
                                                    }
                                                    
                                                }
                                                if($precio_total_x==null || $precio_total_x=='' || $precio_total_x==' ' || $precio_total_x==0){
                                                }else $monto_total_pagado=$precio_total_x;

                                                if($cod_facturacion>0){
                                                    //parte del controlador de check //para la parte de editar  
                                                    $sqlControlador="SELECT sfd.cantidad,sfd.precio,sfd.descuento_por,sfd.descuento_bob,sfd.descripcion_alterna from solicitudes_facturacion sf,solicitudes_facturaciondetalle sfd where sf.codigo=sfd.cod_solicitudfacturacion and sf.cod_simulacion_servicio=$IdServicio and sfd.cod_claservicio=$codCS and sf.codigo=$cod_facturacion and tipo_solicitud=3";
                                                    // echo $sqlControlador;
                                                    $stmtControlado = $dbh->prepare($sqlControlador);
                                                    $stmtControlado->execute();
                                                    //if($itemServicioAux==$IdServicio&&$codSC==$itemServicioFilaAux){
                                                    while ($rowPre = $stmtControlado->fetch(PDO::FETCH_ASSOC)) {
                                                        $sw="checked";
                                                        $montoPre=$rowPre['precio'];
                                                        $cantidad_edit=$rowPre['cantidad'];
                                                        $cantidad_saldo=$cantidad_edit;//solo cuando sea edit se reemplaza a esa variable
                                                        $preciox=$rowPre['precio']*$cantidad_edit;
                                                        $descuento_porX=$rowPre['descuento_por'];
                                                        $descuento_bobX=$rowPre['descuento_bob'];
                                                        $descripcion_alternaX=$rowPre['descripcion_alterna'];
                                                        if($tipo_item==2){//registros adicionados con ajax
                                                            $monto_pagar=$montoPre+$descuento_bobX/$cantidad_edit;
                                                        }else{
                                                            $montoPre=$montoPre+$descuento_bobX;
                                                        }
                                                        $cantidad_total_registrado=$cantidad_total_registrado-$cantidad_saldo;
                                                    }
                                                  //}
                                                }
                                                // echo $IdServicio."--".$codCS;
                                                $sw2="";
                                                $monto_servicio=verificar_pago_servicios_tcp_solfac($IdServicio,$codCS);
                                                $monto_servicio=number_format($monto_servicio,2,".","");
                                                ?><script>console.log("MONTO SERVICIO: "+<?=$monto_servicio?>)</script><?php  
                                                if(count(verificarSiHayFacturasAnuladasSol($cod_facturacion))>0){
                                                   $monto_servicio=''; 
                                                }
                                                if($monto_servicio!=0 && $monto_servicio!='' && $monto_servicio!=null){
                                                    //$saldo=$monto_pagar*$cantidad_saldo-$monto_servicio;
                                                    $saldo=($monto_pagar*$cantidadPre)-$monto_servicio;
                                                    $monto_total_pagado=$monto_servicio;                                                    
                                                    if($monto_servicio==$montoPre){
                                                        $sw2="readonly style='background-color:#cec6d6;'";
                                                        $saldo=0;
                                                    }
                                                }

                                                // echo $monto_total_pagado;

                                                //parte del controlador de check// los ya registrados
                                                $sqlControlador2="SELECT sf.codigo as cod_solicitud,sfd.precio,sfd.descuento_por,sfd.descuento_bob,sfd.descripcion_alterna from solicitudes_facturacion sf,solicitudes_facturaciondetalle sfd where sf.codigo=sfd.cod_solicitudfacturacion and sf.cod_simulacion_servicio=$IdServicio and sfd.cod_claservicio=$codCS and tipo_solicitud=3 and sf.cod_estadosolicitudfacturacion!=2"; 
                                                // echo $sqlControlador2;
                                                $stmtControlador2 = $dbh->prepare($sqlControlador2);
                                                $stmtControlador2->execute();
                                                $cont_items_aux=0;
                                                //if($itemServicioAux==$IdServicio&&$codSC==$itemServicioFilaAux){
                                                while ($rowPre = $stmtControlador2->fetch(PDO::FETCH_ASSOC)) {
                                                    $cont_items_aux++;
                                                    if($sw!="checked"){//si el item  no es  editar
                                                        if($montoPreTotal==$monto_total_pagado){
                                                            $sw2="readonly style='background-color:#cec6d6;'";
                                                            $saldo=0;
                                                            $cantidad_saldo=0;
                                                        }
                                                        if($rowPre['descuento_bob']==null || $rowPre['descuento_bob']==0 || $rowPre['descuento_bob']=='' || $rowPre['descuento_bob']==' '){
                                                        }else{
                                                            // $monto_total_pagado-=$rowPre['descuento_bob'];
                                                            // echo $monto_pagar."-".$monto_total_pagados;
                                                            //$saldo=$monto_pagar*$cantidad_saldo-$monto_total_pagado;  
                                                            $descuento_bobX=(($montoPre*$cantidadPre)*$descuentoFila)/100;  
                                                            $saldo=($monto_pagar*$cantidadPre)-$monto_total_pagado-$descuento_bobX;    
                                                            $saldo_real=$saldo;
                                                        }
                                                        // $montoPre=$rowPre['precio']+$rowPre['descuento_bob'];
                                                        $descuento_porX=$rowPre['descuento_por'];
                                                        $descuento_bobX=$rowPre['descuento_bob'];
                                                        $descripcion_alternaX=$rowPre['descripcion_alterna'];
                                                    }else{//editar                                                        
                                                        $monto_total_pagado=$precio_total_x-$preciox;
                                                        $saldo=$preciox;
                                                        $saldo_real=$saldo;
                                                    }
                                                 } 
                                                //}
                                                $itemServicioAux=$IdServicio;
                                                $itemServicioFilaAux=$codSC;

                                                if($descuentoFila>0){
                                                    $descuento_bobX=(($montoPre*$cantidadPre)*$descuentoFila)/100;
                                                    //$descuento_bobX=(($monto_pagar*$cantidad_saldo)*$descuentoFila)/100;
                                                    $descuento_porX=$descuentoFila;
                                                }
                                            
                                                ?>

                                                <!-- guardamos todas las valores en un input -->
                                                <input type="hidden" id="tipo_item<?=$iii?>" name="tipo_item<?=$iii?>" value="<?=$tipo_item?>">
                                                <input type="hidden" id="cod_serv_tiposerv<?=$iii?>" name="cod_serv_tiposerv<?=$iii?>" value="<?=$codigoPre?>">
                                                <input type="hidden" id="servicio<?=$iii?>" name="servicio<?=$iii?>" value="<?=$codCS?>">
                                                 <input type="hidden" id="nombre_servicio<?=$iii?>" name="nombre_servicio<?=$iii?>" value="<?=$tipoPre?>">
                                                <!-- <input type="hidden" id="cantidad<?=$iii?>" name="cantidad<?=$iii?>" value="<?=$cantidad_saldo?>"> -->
                                                <input type="hidden" id="importe<?=$iii?>" name="importe<?=$iii?>" value="<?=$monto_pagar?>">
                                                <input type="hidden" id="saldo_monto<?=$iii?>" name="saldo_monto<?=$iii?>" value="<?=$saldo_real?>">

                                                <!-- aqui se captura los servicios activados con el checkbox -->
                                                <input type="hidden" id="cod_serv_tiposerv_a<?=$iii?>" name="cod_serv_tiposerv_a<?=$iii?>">
                                                <input type="hidden" id="servicio_a<?=$iii?>" name="servicio_a<?=$iii?>">
                                                <input type="hidden" id="cantidad_a<?=$iii?>" name="cantidad_a<?=$iii?>">
                                                <input type="hidden" id="importe_a<?=$iii?>" name="importe_a<?=$iii?>">
                                                <input type="hidden" id="cantidad_real<?=$iii?>" name="cantidad_real<?=$iii?>" value="<?=$cantidadPre?>">
                                                <tr>
                                                    <td><small><?=$iii?></small></td>
                                                    <td class="text-left" ><small><?=$tipoPre?></small></td>
                                                    <!-- <td class="text-right"><small><?=$cantidadPre?></small></td> -->
                                                    <td class="text-right"><input type="number" step="0.01" id="cantidad<?=$iii?>" name="cantidad<?=$iii?>" class="form-control input-sm"  value="<?=$cantidad_saldo?>" onchange="cantidad_por_importe_servicio_sf_2(<?=$iii?>)" <?=$sw2?> ><span style="color: #6ab682;font-size: 8px;">(CT: <?=$cantidad_inicial?></span><br><span style="color: #FF0000;font-size: 8px; ">CF: <?=$cantidad_total_registrado?>)</span></td>

                                                    <td class="text-right"><input type="number" id="monto_precio<?=$iii?>" name="monto_precio<?=$iii?>" class="form-control input-sm"  value="<?=$monto_pagar?>" step="0.01" onkeyup="activarInputMontoFilaServicio2()" <?=$sw2?> readonly></td>
                                                    <!--  descuentos -->
                                                    <td class="text-right"><input type="text" class="form-control input-sm" name="descuento_por<?=$iii?>" id="descuento_por<?=$iii?>" value="<?=$descuento_porX?>" onkeyup="descuento_convertir_a_bolivianos(<?=$iii?>)" <?=$sw2?>></td>                                             
                                                    <td class="text-right"><input type="text" class="form-control input-sm" name="descuento_bob<?=$iii?>" id="descuento_bob<?=$iii?>" value="<?=$descuento_bobX?>" onkeyup="descuento_convertir_a_porcentaje(<?=$iii?>)" <?=$sw2?>></td>                                        
                                                    <!-- total -->
                                                    <td class="text-right"><input type="hidden" name="modal_importe<?=$iii?>" id="modal_importe<?=$iii?>"><input type="text" class="form-control input-sm" name="modal_importe_dos<?=$iii?>" id="modal_importe_dos<?=$iii?>" style ="background-color: #ffffff;" readonly></td>

                                                    <td>
                                                        <input type="hidden" name="modal_importe_pagado_dos_a<?=$iii?>" id="modal_importe_pagado_dos_a<?=$iii?>" value="<?=$monto_total_pagado;?>">
                                                        <input type="text" class="form-control" name="modal_importe_pagado_dos<?=$iii?>" id="modal_importe_pagado_dos<?=$iii?>" readonly value="<?=number_format($monto_total_pagado,2);?>">
                                                    </td>
                                                    <td>
                                                        <input type="number" step="any" id="importe_a_pagar<?=$iii?>" name="importe_a_pagar<?=$iii?>" class="form-control text-primary text-right"  value="<?=$saldo?>" step="any" onkeyup="verificar_item_activo(<?=$iii?>)" <?=$sw2?> min="0.1">
                                                    </td>

                                                                                              
                                                    <td>
                                                        <textarea name="descripcion_alterna<?=$iii?>" id="descripcion_alterna<?=$iii?>" class="form-control" <?=$sw2?>><?=$descripcion_alternaX?></textarea>
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
                                            <?php   $iii++;  }//fin if
                                                                                                                
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
                                <input type="hidden" id="comprobante_auxiliar" name="comprobante_auxiliar">
                                <!-- <div class="row">
                                    <label class="col-sm-5 col-form-label" style="color:#000000">Monto Total</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">                                        
                                            <input style="background:#ffffff" class="form-control" type="text" value="0" name="modal_totalmontoserv" id="modal_totalmontoserv" step="0.01" readonly="true" />                                            
                                        </div>
                                    </div>
                                </div> -->
                                <fieldset id="fiel" style="width:100%;border:0;">
                                    <button title="Agregar Servicios" type="button" id="add_boton" name="add" class="btn btn-warning btn-round btn-fab" onClick="AgregarSeviciosFacturacion2_servicios(this)">
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
                                    <?=$index=1;?>
                                    <div id="div<?=$index;?>">  
                                        <div class="h-divider">
                                        
                                        </div>
                                    </div>
                                    

                                </fieldset>
                                <div class="row">
                                    <label class="col-sm-5 col-form-label" style="color:#000000">Monto Total + Servicios Adicionales</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">                                            
                                            <input style="background:#ffffff" class="form-control"  name="monto_total" id="monto_total"  readonly="readonly" value="0" step="0.01" />
                                            <input  class="form-control" type="hidden" name="monto_total_a" id="monto_total_a"  readonly="readonly" value="0"  />
                                        </div>
                                    </div>
                                </div>                                
                            </div>
                        </div>                    
                  </div>
                  <div class="card-footer ml-auto mr-auto">
                    <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
                    <?php 
                    if(isset($_GET['q'])){
                        ?><a href='<?=$urllistFacturasServicios;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> IR A SF </a><?php
                    }else{?>
                        <a href='<?=$urllistFacturasServicios;?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> IR A SF </a>                       
                        <?php
                    }?>
                        
                    
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
    if(f.elements["monto_total"].value == 0 || f.elements["monto_total"].value < 0 || f.elements["monto_total"].value == '')
    {    
        ok = false;
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