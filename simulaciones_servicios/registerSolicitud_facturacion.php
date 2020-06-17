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
}

$cod_simulacion=$cod_s;
$cod_facturacion=$cod_f;
$cod_sw=$cod_sw;
//sacamos datos para la facturacion
$sql="SELECT sc.nombre,sc.anios,sc.cod_responsable,sc.cod_cliente,ps.cod_area,ps.cod_unidadorganizacional,sc.id_tiposervicio
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
    $nombre_simulacion = $resultServicio['nombre'];
    $id_tiposervicio = $resultServicio['id_tiposervicio'];
    $name_cliente=nameCliente($cod_cliente);
}else {
    $nombre_simulacion = $resultServicio['nombre'];
    if(isset($_POST['q'])){
        $cod_personal=$_POST['q'];
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
}
$name_tipoPago=obtenerNombreTipoPago($cod_tipoobjeto);
$name_uo=nameUnidad($cod_uo);
$name_area=trim(abrevArea($cod_area),'-');

$contadorRegistros=0;


$descuento_cliente=obtenerDescuentoCliente($cod_cliente);
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
                
                <input type="hidden" name="Codigo_alterno" id="Codigo_alterno" value="<?=$Codigo_alterno;?>"/>  
                <input type="hidden" name="cod_simulacion" id="cod_simulacion" value="<?=$cod_simulacion;?>"/>
                <input type="hidden" name="cod_facturacion" id="cod_facturacion" value="<?=$cod_facturacion;?>"/>
                <input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">
                <input type="hidden" name="IdTipo" id="IdTipo" value="<?=$id_tiposervicio;?>"><!-- //tipo de servicio -->

                <div class="card">
                  <div class="card-header <?=$colorCard;?> card-header-text">
                    <div class="card-text">
                      <h4 class="card-title"><?php if ($cod_facturacion == 0) echo "Registrar "; else echo "Editar ";?>Solicitud de Facturación</h4>                      
                    </div>
                    <h4 class="card-title" align="center"><b>Propuesta/Servicio: <?=$nombre_simulacion?> - <?=$name_area?> / <?=$Codigo_alterno?></b></h4>
                  </div>
                  <div class="card-body ">
                        <div class="row">
                          <label class="col-sm-2 col-form-label">Oficina</label>
                          <div class="col-sm-4">
                            <div class="form-group">
                                <input class="form-control" type="hidden" name="cod_uo" id="cod_uo" required="true" value="<?=$cod_uo;?>" required="true" readonly/>
                                 <input class="form-control" type="text" required="true" value="<?=$name_uo;?>" required="true" readonly style="background-color:#E3CEF6;text-align: left"/>
                               
                            </div>
                          </div>
                          <label class="col-sm-2 col-form-label">Area</label>
                            <div class="col-sm-4">
                                <div class="form-group" >
                                    <div id="div_contenedor_area_tcc">
                                        <input class="form-control" type="hidden" name="cod_area" id="cod_area" required="true" value="<?=$cod_area;?>" required="true" readonly/>

                                        <input class="form-control" type="text" required="true" value="<?=$name_area;?>" required="true" readonly style="background-color:#E3CEF6;text-align: left"/>
                                       
                                    </div>                    
                                </div>
                            </div>
                        </div>
                            <!--fin ofician y area -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">F. Registro</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="date" name="fecha_registro" id="fecha_registro" required="true" value="<?=$fecha_registro;?>" required="true"/>
                                    <input type="hidden" name="fecha_solicitudfactura" id="fecha_solicitudfactura" value="<?=$fecha_solicitudfactura;?>"/>
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Tipo Objeto</label>
                            <div class="col-sm-4">
                                <div class="form-group" >
                                    <input class="form-control" type="hidden" name="cod_tipoobjeto" id="cod_tipoobjeto" required="true" value="<?=$cod_tipoobjeto;?>" required="true" readonly/>

                                    <input class="form-control" type="text" required="true" value="<?=$name_tipoPago;?>" required="true" readonly style="background-color:#E3CEF6;text-align: left"/>
                                </div>
                            </div>
                        </div>                        
                        <div class="row" >                        
                            <script>var nfac=[];itemTipoPagos_facturacion.push(nfac);var nfacAreas=[];itemAreas_facturacion.push(nfacAreas);</script>
                            <div class="">
                                <?php 
                                    //====ingresamos los objetos con porcentajes
                                    if($cod_facturacion > 0)
                                    {
                                        $queryTipopagoEdit="SELECT cod_tipopago,porcentaje,monto from solicitudes_facturacion_tipospago where cod_solicitudfacturacion=$cod_facturacion";
                                        $stmtTipopagoEdit = $dbh->prepare($queryTipopagoEdit);
                                        $stmtTipopagoEdit->execute();
                                        $ncAreas=0;$contAreas= array();
                                        while ($rowAreas = $stmtTipopagoEdit->fetch(PDO::FETCH_ASSOC)) {
                                            $datoArea = new stdClass();//obejto
                                            $codFila=(int)$rowAreas["cod_tipopago"];
                                            $porcentaje_x=trim($rowAreas['porcentaje']);
                                            $monto_x=trim($rowAreas['monto']);?>
                                            <script>
                                                var tipopago={
                                                    codigo_tipopago: <?=$codFila?>,
                                                    monto_porcentaje: <?=$porcentaje_x?>,
                                                    monto_bob: <?=$monto_x?>
                                                }
                                                itemTipoPagos_facturacion[0].push(tipopago);  
                                            </script>
                                            <?php
                                        }
                                        //para objetos areas                                        
                                        $queryAreasEdit="SELECT cod_area,porcentaje,monto from solicitudes_facturacion_areas where cod_solicitudfacturacion=$cod_facturacion";
                                        $stmtAreasEdit = $dbh->prepare($queryAreasEdit);
                                        $stmtAreasEdit->execute();
                                        $ncAreas=0;$contAreas= array();
                                        while ($row = $stmtAreasEdit->fetch(PDO::FETCH_ASSOC)) {
                                            $datoArea = new stdClass();//obejto
                                            $codFila=(int)$row["cod_area"];
                                            $porcentaje_x=trim($row['porcentaje']);
                                            $monto_x=trim($row['monto']);?>
                                            <script>
                                                var area={
                                                    codigo_areas: <?=$codFila?>,
                                                    monto_porcentaje: <?=$porcentaje_x?>,
                                                    monto_bob: <?=$monto_x?>
                                                }
                                                itemAreas_facturacion[0].push(area);  
                                            </script>
                                            <?php
                                        }
                                    }
                                    //=== termina porcentaje objetos
                                    $queryAreas="SELECT codigo,nombre,abreviatura from areas where areas_ingreso=1 and cod_estado=1 order by nombre";
                                    $stmtAreas = $dbh->prepare($queryAreas);
                                    $stmtAreas->execute();
                                    $ncAreas=0;$contAreas= array();
                                    while ($rowAreas = $stmtAreas->fetch(PDO::FETCH_ASSOC)) { 
                                        //unidades de cada area?>
                                        <script>
                                            var nfacUnidades=[];itemUnidades_facturacion.push(nfacUnidades);
                                        </script>
                                        <?php
                                        //objeto dato donde se guarda las areas de servicios
                                        $datoArea = new stdClass();//obejto
                                        $codFila=(int)$rowAreas["codigo"];
                                        $nombre_x=trim($rowAreas['nombre']);
                                        $abrev_x=trim($rowAreas['abreviatura']);
                                        $datoArea->codigo=($ncAreas+1);
                                        $datoArea->cod_area=$codFila;
                                        $datoArea->nombrex=$nombre_x;
                                        $datoArea->abrevx=$abrev_x;
                                        $datosAreas[0][$ncAreas]=$datoArea;                           
                                        $ncAreas++;
                                    }
                                    $contAreas[0]=$ncAreas;
                                ?>
                                <?php //unidades
                                    $queryUnidades="SELECT codigo,nombre,abreviatura from unidades_organizacionales where cod_estado=1 order by nombre";
                                    $stmtUnidades = $dbh->prepare($queryUnidades);
                                    $stmtUnidades->execute();
                                    $ncUnidades=0;$contUnidades= array();
                                    while ($rowUnidades = $stmtUnidades->fetch(PDO::FETCH_ASSOC)) { 
                                        //objeto dato donde se guarda las areas de servicios
                                        $datoUnidades = new stdClass();//obejto
                                        $codFila=(int)$rowUnidades["codigo"];
                                        $nombre_x=trim($rowUnidades['nombre']);                                        
                                        $datoUnidades->codigo=($ncUnidades+1);
                                        $datoUnidades->cod_unidad=$codFila;
                                        $datoUnidades->nombrex=$nombre_x;                                                
                                        $datosUnidades[0][$ncUnidades]=$datoUnidades;                           
                                        $ncUnidades++;
                                    }
                                    $contUnidades[0]=$ncUnidades;
                                ?>
                            </div>
                            <label class="col-sm-2 col-form-label">Tipo Pago</label>
                            <div class="col-sm-3">
                                <div class="form-group" >
                                    <select name="cod_tipopago" id="cod_tipopago" class="selectpicker form-control form-control-sm" data-style="btn btn-info" onChange="ajaxTipoPagoContactoPersonal(this);">
                                        <?php 
                                        $queryTipoPago = "SELECT codigo,nombre FROM  tipos_pago WHERE cod_estadoreferencial=1 order by nombre";
                                        $statementPAgo = $dbh->query($queryTipoPago);
                                        $nc=0;$cont= array();
                                        while ($row = $statementPAgo->fetch()){ 
                                            //objeto dato donde guarda tipos de pago
                                            $dato = new stdClass();//obejto
                                            $codFila=(int)$row["codigo"];
                                            $nombre_x=trim($row['nombre']);
                                            $dato->codigo=($nc+1);
                                            $dato->cod_tipopago=$codFila;
                                            $dato->nombrex=$nombre_x;                                                
                                            $datos[0][$nc]=$dato;                           
                                            $nc++;
                                            ?>
                                            <option <?=($cod_tipopago==$row["codigo"])?"selected":"";?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                        <?php } 
                                        $cont[0]=$nc;
                                        ?>
                                    </select>                                
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="form-group" >
                                    <button type="button" class="btn btn-danger btn-round btn-fab btn-sm" data-toggle="modal" data-target="" onclick="agregarDatosModalTipoPagoFacturacion()">
                                        <i class="material-icons" title="Tipo Pago Porcentaje">list</i>
                                        <span id="nfac" class="count bg-warning"></span>
                                     </button>
                                     <button type="button" class="btn btn-primary btn-round btn-fab btn-sm" data-toggle="modal" data-target="" onclick="agregarDatosModalAreasFacturacion()">
                                        <i class="material-icons" title="Areas Porcentaje">list</i>
                                        <span id="nfacAreas" class="bg-warning"></span>
                                     </button>                              
                                </div>
                            </div> 
                            <label class="col-sm-2 col-form-label">Responsable</label>
                            <div class="col-sm-4">
                                <div class="form-group">            
                                    <?php  $responsable=namePersonal($cod_personal); ?>                    
                                    <input type="hidden" name="cod_personal" id="cod_personal" value="<?=$cod_personal?>" readonly="true" class="form-control">
                                    <input type="text" value="<?=$responsable?>" readonly="true" class="form-control" style="background-color:#E3CEF6;text-align: left">
                                </div>
                            </div>                            
                        </div>
                        <!-- fin tipos pago y objeto                 -->
                        
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Cliente</label>
                            <div class="col-sm-4">
                                <div class="form-group" >

                                     <input class="form-control" type="hidden" name="cod_cliente" id="cod_cliente" required="true" value="<?=$cod_cliente;?>" required="true" readonly/>

                                        <input class="form-control" type="text" required="true" value="<?=$name_cliente;?>" required="true" readonly style="background-color:#E3CEF6;text-align: left"/>
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Persona Contacto</label>
                            <div class="col-sm-3">                                
                                <div id="div_contenedor_contactos">
                                    <select class="selectpicker form-control form-control-sm" name="persona_contacto" id="persona_contacto" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" title="Seleccione Contacto">
                                        <?php 
                                        $query="SELECT * FROM clientes_contactos where cod_cliente=$cod_cliente order by nombre";
                                        $stmt = $dbh->prepare($query);
                                        $stmt->execute();
                                        while ($rowContacto = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $codigo_contacto=$rowContacto['codigo'];    
                                        $nombre_conatacto=$rowContacto['nombre']." ".$rowContacto['paterno']." ".$rowContacto['materno'];
                                        ?><option <?=($persona_contacto==$codigo_contacto)?"selected":"";?> value="<?=$codigo_contacto?>" class="text-right"><?=$nombre_conatacto?></option>
                                        <?php 
                                        } ?> 
                                    </select>
                                    
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="form-group" >                                        
                                    <a href="#" class="btn btn-warning btn-round btn-fab btn-sm" onclick="cargarDatosRegistroContacto()">
                                        <i class="material-icons" title="Add Contacto">add</i>
                                    </a>
                                    <a href="#" class="btn btn-success btn-round btn-fab btn-sm" onclick="actualizarRegistroContacto()">
                                       <i class="material-icons" title="Actualizar Clientes & Contactos">update</i>
                                    </a> 
                                </div>
                            </div>
                            
                        </div>
                        <!-- fin cliente y contacto -->

                        <div class="row">
                            <label class="col-sm-2 col-form-label">Razón Social</label>
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <div id="contenedor_razonsocial">
                                        <input class="form-control" type="text" name="razon_social" id="razon_social" required="true" value="<?=$razon_social;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>    
                                    </div>
                                </div>
                            </div>
                            <label class="col-sm-1 col-form-label">Nit</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="number" name="nit" id="nit" required="true" value="<?=$nit;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" required="true"/>
                                </div>
                            </div>
                        </div>
                        <!-- fin razon social y nit -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Observaciones * 1</label>
                            <div class="col-sm-10">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="observaciones" id="observaciones"  value="<?=$observaciones;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" requerid/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Observaciones 2</label>
                            <div class="col-sm-10">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="observaciones_2" id="observaciones_2" value="<?=$observaciones_2;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                                </div>
                            </div>
                        </div>
                        <!-- fin observaciones -->

                        <div class="card">
                            <div class="card-header <?=$colorCard;?> card-header-text">
                                <div class="card-text">
                                  <h6 class="card-title">Detalle Solicitud Facturación</h6>
                                </div>
                            </div>
                            <!-- <button type="button" onclick="AgregarSeviciosFacturacion()" class="btn btn-success btn-sm btn-fab float-right">
                                 <i class="material-icons" title="Registrar Servicios">edit</i>
                            </button> -->

                            <div class="card-body ">
                                <table class="table table-bordered table-condensed table-striped table-sm">
                                     <thead>
                                          <tr class="fondo-boton">
                                            <th>#</th>
                                            <th >Año</th>
                                            <th>Item</th>
                                            <th>Cant.</th>
                                            <th>Precio(BOB)</th>
                                            <th>Desc(%)</th>
                                            <th>Desc(BOB)</th>
                                            <th width="10%">Importe(BOB)</th>
                                            <th width="40%">Glosa</th>
                                            <th class="small">H/D</th>  
                                          </tr>
                                      </thead>
                                      <tbody>                                
                                        <?php 
                                        $iii=1;
                                        $queryPr="SELECT s.codigo,s.cod_claservicio,t.Descripcion as nombre_serv,s.cantidad,s.monto,s.habilitado,s.cod_tipounidad,s.cod_anio,s.cantidad_editado FROM simulaciones_servicios_tiposervicio s, cla_servicios t where s.cod_simulacionservicio=$cod_simulacion and s.cod_claservicio=t.IdClaServicio";
                                        if ($cod_facturacion > 0){
                                            $queryPr.=" UNION ";
                                            $queryPr.="SELECT d.codigo,d.cod_claservicio,(select cs.Descripcion from cla_servicios cs where cs.IdClaServicio=d.cod_claservicio) as descripcion,d.cantidad,d.precio,1,1,null,1 from solicitudes_facturaciondetalle d where d.tipo_item=2 and d.cod_solicitudfacturacion=$cod_facturacion ORDER BY nombre_serv";
                                        }
                                       // echo $queryPr;
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
                                            $descuento_bob_cliente=$montoPre*$descuento_cliente; 
                                            // $montoPreTotal=$montoPre*$cantidadEPre;
                                            $banderaHab=$rowPre['habilitado'];
                                            $codTipoUnidad=$rowPre['cod_tipounidad'];
                                            $cod_anio=$rowPre['cod_anio'];                                        
                                            if($banderaHab!=0){
                                                $descuento_porX=0;
                                                $descuento_bobX=0;
                                                $descripcion_alternaX=$tipoPre;
                                                // $modal_totalmontopre+=$montoPre;
                                                $montoPre=number_format($montoPre,2,".","");
                                                //parte del controlador de check
                                                $sqlControlador="SELECT sfd.precio,sfd.descuento_por,sfd.descuento_bob,sfd.descripcion_alterna from solicitudes_facturacion sf,solicitudes_facturaciondetalle sfd where sf.codigo=sfd.cod_solicitudfacturacion and sf.cod_simulacion_servicio=$cod_simulacion and sf.cod_estado=1 and sfd.cod_claservicio=$codCS and sf.codigo=$cod_facturacion";
                                                // echo $sqlControlador;
                                                $stmtControlado = $dbh->prepare($sqlControlador);
                                               $stmtControlado->execute();                                           
                                               $sw="";//para la parte de editar
                                                while ($rowPre = $stmtControlado->fetch(PDO::FETCH_ASSOC)) {
                                                    $sw="checked";
                                                    $montoPre=$rowPre['precio']+$rowPre['descuento_bob'];
                                                    $descuento_porX=$rowPre['descuento_por'];
                                                    $descuento_bobX=$rowPre['descuento_bob'];
                                                    $descripcion_alternaX=$rowPre['descripcion_alterna'];
                                                }
                                                $sqlControlador2="SELECT sfd.precio,sfd.descuento_por,sfd.descuento_bob,sfd.descripcion_alterna from solicitudes_facturacion sf,solicitudes_facturaciondetalle sfd where sf.codigo=sfd.cod_solicitudfacturacion and sf.cod_simulacion_servicio=$cod_simulacion and sf.cod_estado=1 and sfd.cod_claservicio=$codCS";
                                                // echo $sqlControlador2;
                                                $stmtControlador2 = $dbh->prepare($sqlControlador2);
                                                $stmtControlador2->execute();                                           
                                                $sw2="";//para registrar nuevos, impedir los ya registrados
                                            
                                                while ($rowPre = $stmtControlador2->fetch(PDO::FETCH_ASSOC)) {
                                                  if($sw!="checked"){
                                                    $sw2="readonly style='background-color:#cec6d6;'";
                                                    $montoPre=$rowPre['precio']+$rowPre['descuento_bob'];
                                                    $descuento_porX=$rowPre['descuento_por'];
                                                    $descuento_bobX=$rowPre['descuento_bob'];
                                                    $descripcion_alternaX=$rowPre['descripcion_alterna'];
                                                  }
                                                }
                                            
                                                ?>
                                                <!-- guardamos las varialbles en un input -->
                                                <input type="hidden" id="cod_serv_tiposerv<?=$iii?>" name="cod_serv_tiposerv<?=$iii?>" value="<?=$codigoPre?>">
                                                <input type="hidden" id="servicio<?=$iii?>" name="servicio<?=$iii?>" value="<?=$codCS?>">
                                                 <input type="hidden" id="nombre_servicio<?=$iii?>" name="nombre_servicio<?=$iii?>" value="<?=$tipoPre?>">
                                                <input type="hidden" id="cantidad<?=$iii?>" name="cantidad<?=$iii?>" value="<?=$cantidadPre?>">
                                                <input type="hidden" id="importe<?=$iii?>" name="importe<?=$iii?>" value="<?=$montoPre?>">

                                                <!-- aqui se captura los servicios activados -->
                                                <input type="hidden" id="cod_serv_tiposerv_a<?=$iii?>" name="cod_serv_tiposerv_a<?=$iii?>">
                                                <input type="hidden" id="servicio_a<?=$iii?>" name="servicio_a<?=$iii?>">
                                                <input type="hidden" id="cantidad_a<?=$iii?>" name="cantidad_a<?=$iii?>">
                                                <input type="hidden" id="importe_a<?=$iii?>" name="importe_a<?=$iii?>">
                                                <tr>
                                                  <td><?=$iii?></td>
                                                  <td class="text-left"><?=$cod_anio?> </td>
                                                  <td class="text-left"><?=$tipoPre?></td>
                                                  <td class="text-right"><?=$cantidadPre?></td>
                                                  <td class="text-right"><input type="number" step="0.01" id="monto_precio<?=$iii?>" name="monto_precio<?=$iii?>" class="form-control text-primary text-right"  value="<?=$montoPre?>" onkeyup="activarInputMontoFilaServicio2()" <?=$sw2?>></td>
                                                  <!--  descuentos -->
                                                  <td class="text-right"><input type="number" step="0.01" class="form-control" name="descuento_por<?=$iii?>" id="descuento_por<?=$iii?>" value="<?=$descuento_porX?>" min="0" max="<?=$descuento_cliente?>" onkeyup="descuento_convertir_a_bolivianos(<?=$iii?>)" <?=$sw2?>></td>                                             
                                                  <td class="text-right"><input type="number" class="form-control" name="descuento_bob<?=$iii?>" id="descuento_bob<?=$iii?>" value="<?=$descuento_bobX?>" min="0" max="<?=$descuento_bob_cliente?>" onkeyup="descuento_convertir_a_porcentaje(<?=$iii?>)" <?=$sw2?>></td>                                        
                                                  <!-- total -->
                                                  <td class="text-right"><input type="hidden" name="modal_importe<?=$iii?>" id="modal_importe<?=$iii?>"><input type="text" class="form-control" name="modal_importe_dos<?=$iii?>" id="modal_importe_dos<?=$iii?>" style ="background-color: #ffffff;" readonly></td>
                                                                                              
                                                  <td>
                                                    <textarea name="descripcion_alterna<?=$iii?>" id="descripcion_alterna<?=$iii?>" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();" <?=$sw2?>><?=$descripcion_alternaX?></textarea>
                                                     <!-- <input type="text" > -->
                                                  </td>
                                                  <!-- checkbox -->
                                                  <td>
                                                    <?php if($sw2!="readonly style='background-color:#cec6d6;'"){?>
                                                        <div class="togglebutton">
                                                           <label>
                                                             <input type="checkbox"  id="modal_check<?=$iii?>" onchange="activarInputMontoFilaServicio2()" <?=$sw?> >
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
                                            </script>

                                            <?php
                                        
                                        } ?>                        
                                      </tbody>
                                </table>

                                <input type="hidden" id="modal_numeroservicio" name="modal_numeroservicio" value="<?=$iii?>">                    
                                <input type="hidden" id="modal_totalmontos" name="modal_totalmontos">
                                <!-- <script>activarInputMontoFilaServicio2();</script>   -->
                                <input type="hidden" id="comprobante_auxiliar" name="comprobante_auxiliar">
                                <div class="row">
                                    <label class="col-sm-5 col-form-label" style="color:#000000">Monto Total</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">                                        
                                            <input style="background:#ffffff" class="form-control" type="text" value="0" name="modal_totalmontoserv" id="modal_totalmontoserv" step="0.01" readonly="true" />                                            
                                        </div>
                                    </div>
                                </div>
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


<!--    end small modal -->


<!-- carga de proveedores -->
<!-- <div class="cargar">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div> -->

<script type="text/javascript">
    function valida(f) {
      var ok = true;
      var msg = "El monto Total no debe ser '0' o 'negativo', Habilite los Items que desee facturar...\n";  
      if(f.elements["comprobante_auxiliar"].value == 0 || f.elements["comprobante_auxiliar"].value < 0 || f.elements["comprobante_auxiliar"].value == '')
      {    
        ok = false;
      }
      if(f.elements["monto_total"].value>0)
      {    
        ok = true;
      }
      if(ok == false)    
        Swal.fire("Informativo!",msg, "warning");
      return ok;
    }
</script>

<script>$('.selectpicker').selectpicker("refresh");</script>
<?php  require_once 'simulaciones_servicios/modal_facturacion.php';?>
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