
<?php
require "conexionmysqli.inc";
$codSalida=$_GET['codigo_salida'];
//obtenemos la sucursal de la factura
$sql="SELECT a.cod_ciudad from salida_almacenes s join almacenes a on s.cod_almacen=a.cod_almacen where s.cod_salida_almacenes='$codSalida'";
// echo $sql;
$respq=mysqli_query($enlaceCon,$sql);
$globalSucursal=mysqli_result($respq,0,0);
// $globalSucursal=$_COOKIE['global_agencia'];

?>
<script>
    function enviarFormularioFactura(cod_salida,ex){
        var nit=$("#nitCliente").val();
        var tipo_doc=$("#tipo_documento").val();
        var complemento=$("#complemento").val();
        var url="dFacturaElectronica.php?codigo_salida="+cod_salida+"&r=1&nit="+nit+"&tipo_doc="+tipo_doc+"&complemento="+complemento+"&ex="+ex;
            window.location.href=url;
    }
</script>
<?php

$stringXml="";
$codigoError="";

if(isset($_GET['nit'])&&(int)$_GET['nit']>0){
    $nitOrigen=$_GET['nit'];
    $tipo_docOrigen=$_GET['tipo_doc'];
    $complementoOrigen=$_GET['complemento'];

    $sqlRecep="UPDATE salida_almacenes SET nit='$nitOrigen',siat_codigotipodocumentoidentidad='$tipo_docOrigen',siat_complemento='$complementoOrigen' where cod_salida_almacenes='$codSalida'";
    $respRecep=mysqli_query($enlaceCon,$sqlRecep);

    $sqlRecep="UPDATE facturas_venta SET nit='$nitOrigen' where cod_venta='$codSalida' and cod_sucursal='".$_COOKIE['global_agencia']."'";
    mysqli_query($enlaceCon,$sqlRecep);
}

if(isset($_GET['r'])){
        $sqlRecep="select siat_codigoRecepcion,fecha,nit,siat_codigotipodocumentoidentidad,siat_complemento from salida_almacenes where cod_salida_almacenes='$codSalida'";
        $respRecep=mysqli_query($enlaceCon,$sqlRecep);
        $recepcion=mysqli_result($respRecep,0,0);
        $fecha=mysqli_result($respRecep,0,1);
        $nitCliente=mysqli_result($respRecep,0,2);
        $tipoDoc=mysqli_result($respRecep,0,3);
        $complemento=mysqli_result($respRecep,0,4);

        $errorFacturaXml=0; $mens="";
        if($recepcion==""){
            $anio=date("Y");
            $sqlCuis="select cuis FROM siat_cuis where cod_ciudad='$globalSucursal' and estado=1 and cod_gestion='$anio' LIMIT 1";
            $respCuis=mysqli_query($enlaceCon,$sqlCuis);
            $cuis=mysqli_result($respCuis,0,0);

            $sqlCufd="select codigo,cufd,codigo_control FROM siat_cufd where cod_ciudad='$globalSucursal' and estado=1 and fecha='$fecha' and cuis='$cuis' LIMIT 1"; 
            //echo $sqlCufd;
            $respCufd=mysqli_query($enlaceCon,$sqlCufd);
            $cufd=mysqli_result($respCufd,0,1);
            $controlCodigo=mysqli_result($respCufd,0,2);
            $codigoCufd=mysqli_result($respCufd,0,0);

            require_once "siat_folder/funciones_siat.php"; 
            $errorConexion=verificarConexion()[0];
            if(isset($_GET['ex'])&&$_GET['ex']==1){
                $facturaImpuestos=generarFacturaVentaImpuestos($codSalida,true,$errorConexion);
            }else{
                $facturaImpuestos=generarFacturaVentaImpuestos($codSalida,false,$errorConexion);
            }            
            //print_r($facturaImpuestos);
            $fechaEmision=$facturaImpuestos[1];
            $cuf=$facturaImpuestos[2];            

            $codigoEstadoDevol=0;
            if(isset($facturaImpuestos[0]->RespuestaServicioFacturacion->codigoEstado)){
                $codigoEstadoDevol=$facturaImpuestos[0]->RespuestaServicioFacturacion->codigoEstado;
            }

            
            if(isset($facturaImpuestos[0]->RespuestaServicioFacturacion->codigoRecepcion)){
                $sqlExcepcionAlmacenes="";
                if(isset($_GET['ex'])&&$_GET['ex']==1){
                    $sqlExcepcionAlmacenes=" ,siat_excepcion=1 ";
                }                
                $codigoRecepcion=$facturaImpuestos[0]->RespuestaServicioFacturacion->codigoRecepcion;
                $sqlUpdMonto="update salida_almacenes set siat_fechaemision='$fechaEmision',siat_estado_facturacion='1',siat_codigoRecepcion='$codigoRecepcion',siat_cuf='$cuf',siat_codigocufd='$codigoCufd',siat_codigotipoemision='1',siat_codigoEstado='$codigoEstadoDevol' $sqlExcepcionAlmacenes 
                        where cod_salida_almacenes='$codSalida' ";
                $respUpdMonto=mysqli_query($enlaceCon,$sqlUpdMonto);

                 // AGREGAR UN TEMP DEL XML
                // $archivo = fopen("temp_xml/$cuf.xml","w+b");
                // fwrite($archivo, $facturaImpuestos[3]);
                // fclose($archivo);


                ?><script type="text/javascript">window.location.href='formatoFacturaOnLine.php?codVenta=<?=$codSalida?>'</script><?php

            }else{
                $codigoError=$facturaImpuestos[0]->RespuestaServicioFacturacion->mensajesList->codigo;
                $mens="<b>(".$facturaImpuestos[0]->RespuestaServicioFacturacion->mensajesList->codigo.") ".$facturaImpuestos[0]->RespuestaServicioFacturacion->mensajesList->descripcion."</b>";
                $stringXml=$facturaImpuestos[3];
                $sqlUpdMonto="update salida_almacenes set siat_codigotipoemision=2,siat_codigocufd='$codigoCufd',siat_estado_facturacion='3',siat_codigoEstado='$codigoEstadoDevol'
                        where cod_salida_almacenes='$codSalida' "; //,siat_fechaemision='$fechaEmision',
                $respUpdMonto=mysqli_query($enlaceCon,$sqlUpdMonto);
                $errorFacturaXml=1;
            }           
        }
    $stringEstado="";
}else{
    require_once "siat_folder/funciones_siat.php";     
    try {
      $resEstado=verificarEstadoFactura($codSalida,$globalSucursal);
    } catch (Exception $e) {
        //asignar alguna alerta
    }
    
    $estiloTitulo='';
    // var_dump($resEstado);
    if($resEstado->RespuestaServicioFacturacion->codigoEstado==690){
        $estiloTitulo='style="color:green"';
    }elseif($resEstado->RespuestaServicioFacturacion->codigoEstado==691){
        $estiloTitulo='style="color:red"';
    }
    $stringEstado='<center><span style="font-weight:bold">ESTADO SIAT</span><h2 '.$estiloTitulo.'> FACTURA '.$resEstado->RespuestaServicioFacturacion->codigoDescripcion.'</h2></center>';
    //print_r($resEstado);
}


$sqlEst="select siat_estado_facturacion from salida_almacenes where cod_salida_almacenes='$codSalida'";
$respEst=mysqli_query($enlaceCon,$sqlEst);
// $estadoFacturacion=mysqli_result($respEst,0,0);
$datConf=mysqli_fetch_array($respEst);
$estadoFacturacion=$datConf[0];

?>
<style type="text/css">
    #wrap {
  width: 750px;
  height: 1200px;
  padding: 0;
  overflow: hidden;
}

#scaled-frame {
  width: 1000px;
  height: 2000px;
  border: 0px;
}

#scaled-frame {
  zoom: 0.75;
  -moz-transform: scale(0.75);
  -moz-transform-origin: 0 0;
  -o-transform: scale(0.75);
  -o-transform-origin: 0 0;
  -webkit-transform: scale(0.75);
  -webkit-transform-origin: 0 0;
}

@media screen and (-webkit-min-device-pixel-ratio:0) {
  #scaled-frame {
    zoom: 1;
  }
}
</style>
<script type="text/javascript">
  
  function anular_salida_siat(codReg){
    var parametros={"codigo":codReg};
    $.ajax({
          type: "GET",
          dataType: 'html',
          url: "programas/salidas/frmConfirmarCodigoSalida_siat.php",
          data: parametros,
          success:  function (resp) { 
              $("#datos_anular").html(resp);
              $("#codigo_salida").val(codReg);
              $("#contrasena_admin").val("");
              $("#modalAnularFactura").modal("show");           
        }
    }); 
  }

  function confirmarCodigo(){ 
    document.getElementById('boton_anular').style.visibility='hidden';
   // var cod_sucursal=document.getElementById("cod_sucursal").value;  
   // var cod_personal=document.getElementById("cod_personal").value;  
   
  var cad1=$("input#idtxtcodigo").val();
  var cad2=$("input#idtxtclave").val(); 
  var per=$("#rpt_personal").val(); 

  var rpt_tipoanulacion=$("#rpt_tipoanulacion").val(); 
  // var glosa_anulacion=$("input#glosa_anulacion").val(); 

  var enviar_correo=$("input#enviar_correo").val();
  var correo_destino=$("input#correo_destino").val();

  var parametros={"codigo":cad1,"clave":cad2,"per":per};
  $.ajax({
        type: "GET",
        dataType: 'html',
        url: "programas/salidas/validacionCodigoConfirmar_siat.php",
        data: parametros,
        success:  function (resp) { 
            if(resp==1) {
                location.href='anular_venta_siat.php?codigo_registro='+$("#codigo_salida").val()+'&id_caja='+per+'&enviar_correo='+enviar_correo+'&correo_destino='+correo_destino+'&rpt_tipoanulacion='+rpt_tipoanulacion;
            }else{
               Swal.fire("Error!","El codigo que ingreso es incorrecto","error");
               $("#modalAnularFactura").modal("hide");    
            }
      }
 }); 
}
</script>
<div class="card">
    <div class="card-header card-header-primary">
        <h4>Detalle de Factura Electronica SIAT</h4>
    </div>        
    <div class="card-body">
        <?=$stringEstado?>
        <!-- <a href="#" class="btn btn-danger" onclick="window.close()">Cerrar</a> -->
        <?php 
    if($estadoFacturacion==1){
        ?>
        <p>Puede obtener la factura con los siguientes formatos:</p>
        <div class="row">
        <div class="col-lg-3 col-md-8 mb-5 mb-lg-0 mx-auto">
         <a href="#" onclick="window.open('descargarFacturaXml.php?codVenta=<?=$codSalida?>','detalle_factura'); return false;" class="after-loop-item card border-0 card-tercero shadow-lg" style="background: #F0C70E;color:#000;" onclick="return false;">
            <div class="card-body d-flex align-items-center flex-column">
               <h4><i class="material-icons">description</i> <b>DESCARGAR XML</b></h4>
               <p>FACTURA COMPUTARIZADA</p>
            </div>
         </a>
        </div>

        <div class="col-lg-3 col-md-8 mb-5 mb-lg-0 mx-auto">
         <a href='#' class="after-loop-item card border-0 card-tercero shadow-lg" style="background:#12C1A9;color:#fff;" onclick="window.open('formatoFacturaOnLine.php?codVenta=<?=$codSalida?>','detalle_factura'); return false;">
            <div class="card-body d-flex align-items-center flex-column">
               <h4><i class="material-icons">print</i> <b>IMPRIMIR FACTURA</b></h4>
               <p>FACTURA COMPUTARIZADA</p>
            </div>
         </a>
        </div>
        <div class="col-lg-3 col-md-8 mb-5 mb-lg-0 mx-auto">
         <a href="#" onclick="window.open('descargarFacturaPDF.php?codigo_salida=<?=$codSalida?>&ds=1','detalle_factura'); return false;" class="after-loop-item card border-0 card-tercero shadow-lg" style="background: #EE0808;color:#fff;" onclick="return false;">
            <div class="card-body d-flex align-items-center flex-column">
               <h4><i class="material-icons">description</i> <b>DESCARGAR PDF</b></h4>
               <p>FACTURA COMPUTARIZADA</p>
            </div>
         </a>
        </div>
        <div class="col-lg-4 col-md-8 mb-5 mb-lg-0 mx-auto">
         <a href='#' class="after-loop-item card border-0 card-tercero shadow-lg" style="background:#4D0778;color:#fff;" onclick="window.open('enviar_correo/index.php?datos=<?=$codSalida?>','detalle_factura'); return false;">
            <div class="card-body d-flex align-items-center flex-column">
               <h4><i class="material-icons">mail</i> <b>ENVIAR FACTURA POR CORREO</b></h4>
               <p>FACTURA COMPUTARIZADA</p>
            </div>
         </a>
        </div>
        <div class="col-lg-4 col-md-8 mb-5 mb-lg-0 mx-auto">
         <button class="after-loop-item card border-0 card-tercero shadow-lg" style="background:#FF5733;color:#fff;" onclick='anular_salida_siat(<?=$codigo_salida?>)'>
            <div class="card-body d-flex align-items-center flex-column">
              <center>
               <h4><i class="material-icons">delete</i> <b>ANULAR FACTURA</b></h4>
               </center>
               <p>FACTURA COMPUTARIZADA</p>
            </div>
         </button>
         
        </div>
        </div>
        <center>
        <div id="wrap" style="width:100% !important">
            <hr>
            <!-- <h4><b>DETALLE DE LA FACTURA</b></h4>
            <iframe id="scaled-frame" style="width:1700px !important;" src="dFactura.php?codigo_salida=<?=$codSalida?>"></iframe> -->
        </div></center>
        
        <?php
    }else{
        ?>
        <script type="text/javascript">
            function mostrarComplemento(){
                var tipo=$("#tipo_documento").val();
                if(tipo==1){
                    $("#complemento").attr("type","text");
                    $("#nitCliente").attr("placeholder","INGRESE EL CARNET");
                }else{
                    $("#complemento").attr("type","hidden");
                    $("#nitCliente").attr("placeholder","INGRESE EL NIT");
                }
            }
            $(document).ready(function (){
                 mostrarComplemento();
            });
        </script>
        <center><p style="font-size: 25;">No se generó la factura en el Sistema de Impuestos.</p>
        <p style="font-size: 20;"><?=$mens?></p>
        <?php
        //$codigoError="1037";
        if($codigoError=="1037"){
            ?>
        <div class="col-sm-6">
            <div class="row">
                <div id='divNIT' class="col-sm-7">
                    <input type='number' value='<?php echo $nitCliente; ?>' name='nitCliente' id='nitCliente'  min="1" class="form-control" required placeholder="INGRESE EL NIT / CI">
                </div>
                <div class="col-sm-4">
                <select name='tipo_documento' class='selectpicker form-control' data-live-search="true" id='tipo_documento' onChange='mostrarComplemento();' required data-style="btn btn-rose">
        <?php
        $sql2="SELECT codigoClasificador,descripcion FROM siat_sincronizarparametricatipodocumentoidentidad;";
        $resp2=mysqli_query($enlaceCon,$sql2);

        while($dat2=mysqli_fetch_array($resp2)){
           $codCliente=$dat2[0];
            $nombreCliente=$dat2[1]." ".$dat2[2];
            if($tipoDoc==$codCliente){
                    ?><option value='<?php echo $codCliente?>' selected><?php echo $nombreCliente?></option><?php
            }else{
                ?><option value='<?php echo $codCliente?>'><?php echo $nombreCliente?></option><?php
            }
        
        }
        ?>
            </select>
            </div>                      
          </div>
          <input type='hidden' name='complemento' id='complemento' value='' class="form-control" placeholder="COMPLEMENTO" style="text-transform:uppercase;position:absolute;width:160px !important;background:#D2FFE8;" onkeyup="javascript:this.value=this.value.toUpperCase();" value='<?=$complemento?>'> 
        </div>
        <div class="col-sm-12">
                <br><br>
                <a href="#" onclick="enviarFormularioFactura(<?=$codSalida?>,1);return false;" class="btn btn-info" style="height: 60px !important;font-size: 20px;"><i class="material-icons" style="font-size: 20px;">outbox</i> FACTURAR CON  CODIGO DE EXCEPCION</a>
            </div>
        </center>
    <?php } ?>
         <div class="row">       
        <?php
        $sqlSalida="SELECT siat_codigotipoemision,cod_tipo_doc FROM salida_almacenes where cod_salida_almacenes='$codSalida' and siat_codigotipoemision=2 and (siat_codigoRecepcion=''||siat_codigoRecepcion is null) ;";        
        $respSalida=mysqli_query($enlaceCon,$sqlSalida);

        while($datSalida=mysqli_fetch_array($respSalida)){
            ?>
            
        <div class="col-lg-3 col-md-8 mb-5 mb-lg-0 mx-auto">
         <a href="#" onclick="window.open('descargarFacturaXml.php?codVenta=<?=$codSalida?>','detalle_factura'); return false;" class="after-loop-item card border-0 card-tercero shadow-lg" style="background: #909090;color:#000;" onclick="return false;">
            <div class="card-body d-flex align-items-center flex-column">
               <h4><i class="material-icons">description</i> <b>DESCARGAR XML</b></h4>
               <p>FACTURA COMPUTARIZADA OFFLINE</p>
            </div>
         </a>
        </div>  
        <div class="col-lg-3 col-md-8 mb-5 mb-lg-0 mx-auto">  
        <a href='#' class="after-loop-item card border-0 card-tercero shadow-lg" style="background:#909090;color:#fff;" onclick="window.open('formatoFacturaOnLine.php?codVenta=<?=$codSalida?>','detalle_factura'); return false;">
            <div class="card-body d-flex align-items-center flex-column">
               <h4><i class="material-icons">print</i> <b>IMPRIMIR FACTURA</b></h4>
               <p>FACTURA COMPUTARIZADA OFFLINE</p>
            </div>
         </a>
         </div>
        <div class="col-lg-3 col-md-8 mb-5 mb-lg-0 mx-auto">
         <a href="#" onclick="window.open('descargarFacturaPDF.php?codigo_salida=<?=$codSalida?>&ds=1','detalle_factura'); return false;" class="after-loop-item card border-0 card-tercero shadow-lg" style="background: #909090;color:#fff;" onclick="return false;">
            <div class="card-body d-flex align-items-center flex-column">
               <h4><i class="material-icons">description</i> <b>DESCARGAR PDF</b></h4>
               <p>FACTURA COMPUTARIZADA OFFLINE</p>
            </div>
         </a>
        </div>
        <div class="col-lg-4 col-md-8 mb-5 mb-lg-0 mx-auto">
         <a href='#' class="after-loop-item card border-0 card-tercero shadow-lg" style="background:#1074A6;color:#fff;" onclick="window.open('enviar_correo/index.php?datos=<?=$codSalida?>','detalle_factura'); return false;">
            <div class="card-body d-flex align-items-center flex-column">
               <h4><i class="material-icons">mail</i> <b>ENVIAR FACTURA POR CORREO</b></h4>
               <p>FACTURA COMPUTARIZADA OFFLINE</p>
            </div>
         </a>
        </div>  
        <?php
        }

        $anio=date("Y");
        $sqlCuisSalida="select IFNULL(siat_cuis,0) FROM salida_almacenes where cod_salida_almacenes='$codSalida'";
        //echo $sqlCuisSalida;
        $respSalida=mysqli_query($enlaceCon,$sqlCuisSalida);
        $cuisSalida=mysqli_result($respSalida,0,0);
        if($cuisSalida!=""){
            ?>
        <div class="col-lg-3 col-md-8 mb-5 mb-lg-0 mx-auto">
         <!-- <a href="#" onclick="enviarFormularioFactura(<?=$codSalida?>,0);return false;" class="after-loop-item card border-0 card-tercero shadow-lg" style="background: #C70039;color:#fff;">
            <div class="card-body d-flex align-items-center flex-column">
               <h4><i class="material-icons">outbox</i> <b> VOLVER A INTENTAR</b></h4>
            </div>
         </a> -->
        </div> 
            <?php
        }else{
            ?><h5 class="text-danger"><b>*  La factura no fue registrada con la nueva modalidad de facturación.</b></h5>            
            <!-- <div style="width:100%"><center><h4 style="color:#891E7F; font-weight: bold;">DETALLE DE LA FACTURA</h4><iframe src="dFactura.php?codigo_salida=<?=$codSalida?>" style="border: none;width:100%;height:680px"></iframe></center></div> --><?php
        }
        ?>               
        </div>
        <div class="row">
            <!-- <center><div class="col-sm-8"><textarea cols="130" rows="30" style="background:#F94876;color:#000"><?=$stringXml?></textarea></div></center> -->
        </div>
        <?php
    }
        ?>         
    </div>
    <!--div class="card-footer">
        <p>Departamento de Sistemas - COBOFAR </p>
    </div-->
</div>

<!-- small modal -->
<div class="modal fade modal-primary" id="modalAnularFactura" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="background-color: rgba(0,0,0, 0.5) !important;">
  <div class="modal-dialog modal-lg" >
    <div class="modal-content card">
        <div class="card-header card-header-danger card-header-icon">
          <div class="card-icon">
            <i class="material-icons">delete</i>
          </div>
          <h4 class="card-title text-danger font-weight-bold">Anulación de Facturas</h4>
          <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true" style="position:absolute;top:0px;right:0;">
            <i class="material-icons">close</i>
          </button>
        </div>
        <input type="hidden" name="codigo_salida" id="codigo_salida" value="0">
        <div class="card-body" id="datos_anular">
           
        </div>
        <div class="card-footer" >
           <button id="boton_anular" name="boton_anular" class="btn btn-default" onclick="confirmarCodigo()">ANULAR</button>
        </div>
    </div>  
    </div>
</div>
<!--    end small modal -->