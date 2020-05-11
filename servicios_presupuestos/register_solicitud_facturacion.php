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
    $persona_contacto= $result['persona_contacto'];

    $nombre_simulacion = $resultServicio['Descripcion'];
    $name_cliente=nameCliente($cod_cliente);
}else {
    $nombre_simulacion = $resultServicio['Descripcion'];
    $cod_personal =$_SESSION["globalUser"];

    $cod_uo = $resultServicio['IdOficina'];
    $cod_area = $resultServicio['IdArea'];
    $cod_cliente = $resultServicio['IdCliente'];    
    $fecha_registro =date('Y-m-d');
    $fecha_solicitudfactura =$fecha_registro;
    $cod_tipoobjeto=obtenerValorConfiguracion(34);//por defecto
    $cod_tipopago = null;
    $name_cliente=nameCliente($cod_cliente);
    $razon_social = $name_cliente;
    $nit = 0;
    $observaciones = $Codigo_alterno." - ".$name_cliente;
    $persona_contacto= null;
}
$name_uo=nameUnidad($cod_uo);
$name_area=trim(abrevArea($cod_area),'-');

$contadorRegistros=0;
?>
<script>
  numFilas=<?=$contadorRegistros;?>;
  cantidadItems=<?=$contadorRegistros;?>;
</script>
<div class="content">
    <div class="container-fluid">
        <div style="overflow-y:scroll;">
            <div class="col-md-12">
              <form id="form1" class="form-horizontal" action="<?=$urlSaveSolicitudfactura;?>" method="post" onsubmit="return valida(this)">
                <input type="hidden" name="Codigo_alterno" id="Codigo_alterno" value="<?=$Codigo_alterno;?>"/>
                <input type="hidden" name="cod_simulacion" id="cod_simulacion" value="<?=$IdServicio;?>"/>
                <input type="hidden" name="cod_facturacion" id="cod_facturacion" value="<?=$cod_facturacion;?>"/>
                <input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">
                <?php 
      if(isset($_GET['q'])){
        ?><input type="hidden" name="usuario_ibnored" id="usuario_ibnored" value="<?=$q;?>">
        <input type="hidden" name="usuario_ibnored_s" id="usuario_ibnored_s" value="<?=$s;?>">
        <input type="hidden" name="usuario_ibnored_u" id="usuario_ibnored_u" value="<?=$u;?>">
        <input type="hidden" name="usuario_ibnored_v" id="usuario_ibnored_v" value="<?=$v;?>"><?php
      }
      ?> 
                <!-- para agregar nuevos servicios -->
                <input type="hidden" name="IdTipo" id="IdTipo" value="<?=$IdTipo;?>">
                <div class="card">
                  <div class="card-header <?=$colorCard;?> card-header-text">
                    <div class="card-text">
                      <h4 class="card-title"><?php if ($cod_facturacion == 0) echo "Registrar "; else echo "Editar ";?>Solicitud de Facturación</h4>                      
                    </div>
                    <h4 class="card-title" align="center"><b>Propuesta: <?=$nombre_simulacion?>  / <?=$Codigo_alterno?></b></h4>
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
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">F. A Facturar</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="date" name="fecha_solicitudfactura" id="fecha_solicitudfactura" required="true" value="<?=$fecha_solicitudfactura;?>" required="true"/>
                                </div>
                            </div>
                        </div>
                        <!-- fin fechas -->
                        <div class="row" >
                            <div class="d-none">
                                <label class="col-sm-2 col-form-label">Tipo Objeto</label>
                                <div class="col-sm-4">
                                    <div class="form-group" >
                                            <select name="cod_tipoobjeto" id="cod_tipoobjeto" class="selectpicker form-control form-control-sm" data-style="btn btn-info" >
                                                <!-- <option value=""></option> -->
                                                <?php 
                                                $queryTipoObjeto = "SELECT codigo,nombre FROM  tipos_objetofacturacion WHERE cod_estadoreferencial=1 order by nombre";
                                                $statementObjeto = $dbh->query($queryTipoObjeto);
                                                while ($row = $statementObjeto->fetch()){ ?>
                                                    <option <?=($cod_tipoobjeto==$row["codigo"])?"selected":"";?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                                <?php } ?>
                                            </select>                                
                                    </div>
                                </div>    
                            </div>
                            <script>var nfac=[];itemTipoPagos_facturacion.push(nfac);var nfacAreas=[];itemAreas_facturacion.push(nfacAreas);</script>
                            <div class="">
                                <?php 
                                    $queryAreas="SELECT codigo,nombre,abreviatura from areas where areas_ingreso=1 and cod_estado=1";
                                    $stmtAreas = $dbh->prepare($queryAreas);
                                    $stmtAreas->execute();
                                    $ncAreas=0;$contAreas= array();
                                    while ($rowAreas = $stmtAreas->fetch(PDO::FETCH_ASSOC)) { 
                                        //objeto dato donde se guarda las areas de servicios
                                        $datoArea = new stdClass();//obejto
                                        $codFila=(int)$rowAreas["codigo"];
                                        $nombre_x=trim($rowAreas['nombre']);                                        
                                        $datoArea->codigo=($ncAreas+1);
                                        $datoArea->cod_area=$codFila;
                                        $datoArea->nombrex=$nombre_x;                                                
                                        $datosAreas[0][$ncAreas]=$datoArea;                           
                                        $ncAreas++;
                                    }
                                    $contAreas[0]=$ncAreas;
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
                                        <span id="nfacAreas" class="count bg-warning"></span>
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
                                <div class="form-group" >
                                        <!-- <input type="text" name="persona_contacto" id="persona_contacto" value="<?=$persona_contacto?>" class="form-control" required="true"> -->
                                        <div id="div_contenedor_contactos">
                                            <select class="selectpicker form-control form-control-sm" name="persona_contacto" id="persona_contacto" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" title="Seleccione Contacto">
                                              <option value=""></option>
                                              <?php 
                                              $query="SELECT * FROM clientes_contactos where cod_cliente=$cod_cliente order by nombre";
                                              $stmt = $dbh->prepare($query);
                                              $stmt->execute();
                                              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                $codigo=$row['codigo'];    
                                                $nombre_conatacto=$row['nombre']." ".$row['paterno']." ".$row['materno'];
                                                ?><option <?=($persona_contacto==$row["codigo"])?"selected":"";?> value="<?=$codigo?>" class="text-right"><?=$nombre_conatacto?></option>
                                               <?php 
                                               } ?> 
                                            </select>
                                        </div>
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
                            <label class="col-sm-3 col-form-label">Observaciones <small style="color: #f45454">*(Información para la contabilización)</small></label>
                            <div class="col-sm-9">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="observaciones" id="observaciones"  value="<?=$observaciones;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" requerid/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-3 col-form-label">Observaciones</label>
                            <div class="col-sm-9">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="observaciones_2" id="observaciones_2" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
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
                                            <th width="35%">Item</th>
                                            <th>Cant.</th>
                                            <th>Precio(BOB)</th>
                                            <th>Desc(%)</th>
                                            <th>Desc(BOB)</th>
                                            <th width="10%">Importe(BOB)</th>
                                            <th width="35%">Glosa</th>
                                            <th class="small">H/D</th>  
                                          </tr>
                                      </thead>
                                      <tbody>                                
                                        <?php 
                                        $iii=1;                                    
                                        $queryPr="SELECT s.IdDetServicio,s.IdClaServicio,s.Cantidad,s.PrecioUnitario from ibnorca.serviciopresupuesto s where  s.IdServicio=$IdServicio";
                                        // echo $queryPr;
                                        if ($cod_facturacion > 0){
                                            $queryPr.=" UNION ";
                                            //,(select cs.descripcion from cla_servicios cs where cs.IdClaServicio=d.cod_claservicio) as descripcion
                                            $queryPr.="SELECT d.codigo,d.cod_claservicio,d.cantidad,d.precio from solicitudes_facturaciondetalle d where d.tipo_item=2 and d.cod_solicitudfacturacion=$cod_facturacion";

                                        }
                                        // echo $queryPr;
                                        $stmt = $dbh->prepare($queryPr);
                                        $stmt->execute();
                                        $modal_totalmontopre=0;$modal_totalmontopretotal=0;
                                        while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $codigoPre=$rowPre['IdDetServicio'];
                                            $codCS=$rowPre['IdClaServicio'];
                                            // $tipoPre=$rowPre['descripcion'];
                                            $cantidadPre=$rowPre['Cantidad'];                                          
                                            $montoPre=$rowPre['PrecioUnitario'];
                                            // echo "---cod  :".$codCS;
                                            $tipoPre=descripcionClaServicio($codCS);
                                            // $montoPreTotal=$montoPre*$cantidadEPre;
                                            $banderaHab=1;
                                            $codTipoUnidad=0;
                                            // $cod_anio=$rowPre['cod_anio'];
                                            if($banderaHab!=0){ 
                                                $descuento_porX=0;
                                                $descuento_bobX=0;
                                                $descripcion_alternaX=$tipoPre;                     
                                                $montoPre=number_format($montoPre,2,".","");
                                                //parte del controlador de check //para la parte de editar  
                                                $sqlControlador="SELECT sfd.precio,sfd.descuento_por,sfd.descuento_bob,sfd.descripcion_alterna from solicitudes_facturacion sf,solicitudes_facturaciondetalle sfd where sf.codigo=sfd.cod_solicitudfacturacion and sf.cod_estado=1 and sf.cod_simulacion_servicio=$IdServicio and sfd.cod_claservicio=$codCS and sf.codigo=$cod_facturacion";
                                                // echo $sqlControlador;
                                                $stmtControlado = $dbh->prepare($sqlControlador);
                                                $stmtControlado->execute();                                           
                                                $sw="";                                         
                                                while ($rowPre = $stmtControlado->fetch(PDO::FETCH_ASSOC)) {
                                                      $sw="checked";
                                                      $montoPre=$rowPre['precio']+$rowPre['descuento_bob'];
                                                      $descuento_porX=$rowPre['descuento_por'];
                                                      $descuento_bobX=$rowPre['descuento_bob'];
                                                      $descripcion_alternaX=$rowPre['descripcion_alterna'];
                                                }
                                                //parte del controlador de check//impedir los ya registrados
                                                $sqlControlador2="SELECT sfd.precio,sfd.descuento_por,sfd.descuento_bob,sfd.descripcion_alterna from solicitudes_facturacion sf,solicitudes_facturaciondetalle sfd where sf.codigo=sfd.cod_solicitudfacturacion and sf.cod_estado=1 and sf.cod_simulacion_servicio=$IdServicio and sfd.cod_claservicio=$codCS"; 
                                                // echo $sqlControlador2;
                                                $stmtControlador2 = $dbh->prepare($sqlControlador2);
                                                $stmtControlador2->execute();                                           
                                                $sw2="";
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
                                                <!-- guardamos todas las valores en un input -->
                                                <input type="hidden" id="cod_serv_tiposerv<?=$iii?>" name="cod_serv_tiposerv<?=$iii?>" value="<?=$codigoPre?>">
                                                <input type="hidden" id="servicio<?=$iii?>" name="servicio<?=$iii?>" value="<?=$codCS?>">
                                                 <input type="hidden" id="nombre_servicio<?=$iii?>" name="nombre_servicio<?=$iii?>" value="<?=$tipoPre?>">
                                                <input type="hidden" id="cantidad<?=$iii?>" name="cantidad<?=$iii?>" value="<?=$cantidadPre?>">
                                                <input type="hidden" id="importe<?=$iii?>" name="importe<?=$iii?>" value="<?=$montoPre?>">

                                                <!-- aqui se captura los servicios activados con el checkbox -->
                                                <input type="hidden" id="cod_serv_tiposerv_a<?=$iii?>" name="cod_serv_tiposerv_a<?=$iii?>">
                                                <input type="hidden" id="servicio_a<?=$iii?>" name="servicio_a<?=$iii?>">
                                                <input type="hidden" id="cantidad_a<?=$iii?>" name="cantidad_a<?=$iii?>">
                                                <input type="hidden" id="importe_a<?=$iii?>" name="importe_a<?=$iii?>">
                                                <tr>
                                                    <td><?=$iii?></td>                                              
                                                    <td class="text-left" ><?=$tipoPre?></td>
                                                    <td class="text-right"><?=$cantidadPre?></td>
                                                    <td class="text-right"><input type="number" id="monto_precio<?=$iii?>" name="monto_precio<?=$iii?>" class="form-control text-primary text-right"  value="<?=$montoPre?>" step="0.01" onkeyup="activarInputMontoFilaServicio2()" <?=$sw2?>></td>
                                                    <!--  descuentos -->
                                                    <td class="text-right"><input type="text" class="form-control" name="descuento_por<?=$iii?>" id="descuento_por<?=$iii?>" value="<?=$descuento_porX?>" onkeyup="descuento_convertir_a_bolivianos(<?=$iii?>)" <?=$sw2?>></td>                                             
                                                    <td class="text-right"><input type="text" class="form-control" name="descuento_bob<?=$iii?>" id="descuento_bob<?=$iii?>" value="<?=$descuento_bobX?>" onkeyup="descuento_convertir_a_porcentaje(<?=$iii?>)" <?=$sw2?>></td>                                        
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
                                            <?php   $iii++;  }//fin if
                                                                                                                
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
                        ?><a href='<?=$urllistFacturasServicios;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a><?php
                    }else{
                        ?><a href='<?=$url_list_Solicitudfactura;?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a><?php
                    }?>
                        
                    
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



<script type="text/javascript">
function valida(f) {
    var ok = true;
    var msg = "El monto Total no debe ser '0' o 'negativo', Habilite los Items que desee facturar...\n";  
    if(f.elements["monto_total"].value == 0 || f.elements["monto_total"].value < 0 || f.elements["monto_total"].value == '')
    {    
        ok = false;
    }

  if(ok == false)    
    Swal.fire("Informativo!",msg, "warning");
  return ok;
}
</script>
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
                    detalle_areas.push({codigo:<?=$datosAreas[$i][$j]->codigo?>,cod_area:<?=$datosAreas[$i][$j]->cod_area?>,nombrex:'<?=$datosAreas[$i][$j]->nombrex?>'});
                </script>

              <?php         
              }          
            }
        ?><script>itemAreas_facturacion_aux.push(detalle_areas);</script><?php                    
    }
?>