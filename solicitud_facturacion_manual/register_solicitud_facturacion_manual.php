<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';
$globalUser=$_SESSION["globalUser"];
//$dbh = new Conexion();
$dbh = new Conexion();

if(isset($_GET['cod_f'])){
    $cod_facturacion=$_GET['cod_f'];
    $cod_simulacion=0;
}else{
    $cod_simulacion=0;
    $cod_facturacion=0;    
}
if(isset($_GET['q'])){
    $q=$_GET['q'];
    $v=$_GET['v'];
    $s=$_GET['s'];
    $u=$_GET['u'];
}
//consulta para oficina y area desde itranet
if(isset($_GET['q'])){
    //para uo
    $arraySqlUO=explode("IdOficina",$s);
    $string_aux='0';  
    if(isset($arraySqlUO[1])){
        $string_aux=trim($arraySqlUO[1]);
        $arraySqlArea=explode("and IdArea",$string_aux);
        $codigoArea='0';  
        $codigoUO='0';  
        if(isset($arraySqlArea[1])){
            $codigoArea=trim($arraySqlArea[1]);
            $codigoUO=trim($arraySqlArea[0]);
        }else{
            $codigoUO=$string_aux;
        }
        if($codigoArea=='0'){    
            $sqlAreas_x="and codigo=0";    
        }else{
            $sqlAreas_x="and codigo ".$codigoArea;  
        }
        if($codigoUO=='0'){    
            $sqlUO_x="and uo.codigo=0";    
        }else{
            $sqlUO_x="and uo.codigo ".$codigoUO;  
        }
    }
}else{
    $sqlUO_x="";
    $sqlAreas_x="";
}

//sacamos datos para la facturacion
if ($cod_facturacion > 0){
    $sql="SELECT * FROM solicitudes_facturacion where codigo=$cod_facturacion";
    // echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch();
    $nombre_simulacion = null;
    $cod_uo = $result['cod_unidadorganizacional'];
    $cod_area = $result['cod_area'];
    $cod_cliente = $result['cod_cliente'];
    $cod_personal = $result['cod_personal'];
    $id_tiposervicio = null;

    $fecha_registro = $result['fecha_registro'];
    $fecha_solicitudfactura = $result['fecha_solicitudfactura'];
    $cod_tipoobjeto = $result['cod_tipoobjeto'];
    $name_tipoPago=obtenerNombreTipoPago($cod_tipoobjeto);
    $cod_tipopago = $result['cod_tipopago'];
    $name_cliente=nameCliente($cod_cliente);
    $razon_social = $result['razon_social'];
    $nit = $result['nit'];
    $observaciones = $result['observaciones'];
    $observaciones_2 = $result['observaciones_2'];
    $persona_contacto= $result['persona_contacto'];
    $Codigo_alterno=null;
    $dias_credito=$result['dias_credito'];
    $correo_contacto=$result['correo_contacto'];

    $codigo_identificacion=$result['siat_tipoidentificacion'];
    $complemento=$result['siat_complemento'];

    $fecha_facturacion=$result['fecha_facturacion'];
    $nro_tarjeta=$result['nro_tarjeta'];
    
}else {
    $nombre_simulacion = null;
    $cod_uo = null; 
    $cod_area = null; 
    $cod_cliente =null;    
    if(isset($_GET['q'])){
        $cod_personal=$_GET['q'];
    }else{
        $cod_personal = $globalUser;
    }
    $id_tiposervicio = null;
    $fecha_registro =date('Y-m-d');
    $fecha_solicitudfactura =$fecha_registro;
    $cod_tipoobjeto=211;//por defecto}
    $name_tipoPago=obtenerNombreTipoPago($cod_tipoobjeto);
    $cod_tipopago = null;
    $name_cliente=null;    
    $razon_social = $name_cliente;
    $nit = null;
    $observaciones = null;
    $observaciones_2 = null;
    $persona_contacto=null;
    $Codigo_alterno=null;
    $dias_credito=obtenerValorConfiguracion(58);
    $correo_contacto="";
    $codigo_identificacion=1;//por defecto ci
    $complemento=null;
    $fecha_facturacion=date('Y-m-d');
    $nro_tarjeta=null;
}
$name_uo=null;
$name_area=null;
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
                    if(isset($_GET['q'])){?>
                        <input type="hidden" name="usuario_ibnored" id="usuario_ibnored" value="<?=$q;?>">
                        <input type="hidden" name="usuario_ibnored_s" id="usuario_ibnored_s" value="<?=$s;?>">
                        <input type="hidden" name="usuario_ibnored_u" id="usuario_ibnored_u" value="<?=$u;?>">
                        <input type="hidden" name="usuario_ibnored_v" id="usuario_ibnored_v" value="<?=$v;?>">
                        <?php 
                    }
                    ?>
                    <input type="hidden" value="-100" id="tipo_documento_otro" name="tipo_documento_otro">
                    <input type="hidden" name="cod_defecto_deposito_cuenta" id="cod_defecto_deposito_cuenta" value="<?=$cod_defecto_deposito_cuenta?>"/>
                    <input type="hidden" name="cod_defecto_cod_tipo_credito" id="cod_defecto_cod_tipo_credito" value="<?=$cod_defecto_cod_tipo_credito?>"/>
                    <!-- si hubiese bancarizacion -->
                    <!-- <input type="hidden" name="nro_contrato" id="nro_contrato" value="0"/>
                    <input type="hidden" name="nro_cuenta_doc" id="nro_cuenta_doc" value="0"/>
                    <input type="hidden" name="nit_entidad_financiera" id="nit_entidad_financiera" value="0"/>
                    <input type="hidden" name="nro_transaccion" id="nro_transaccion" value="0">
                    <input type="hidden" name="tipo_doc_pago" id="tipo_doc_pago" value="0">
                    <input type="hidden" name="fecha_doc_pago" id="fecha_doc_pago" value="0">
                    
 -->
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
                          <h4 class="card-title"><?php if ($cod_facturacion == 0) echo "Registrar "; else echo "Editar ";?>Solicitud de Facturación Manual</h4>                      
                        </div>
                        <!-- <h4 class="card-title" align="center"><b>Propuesta: Manual</b></h4> -->
                    </div>
                    <div class="card-body ">
                        <div class="row">
                          <label class="col-sm-2 col-form-label">Oficina</label>
                          <div class="col-sm-4">
                            <div class="form-group">                                
                                <select name="cod_uo" id="cod_uo" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">                                        
                                    <option value=""></option>
                                    <?php 
                                    $queryUO1 = "SELECT uo.codigo,uo.nombre,uo.abreviatura from entidades_uo e, unidades_organizacionales uo where e.cod_uo=uo.codigo and uo.cod_estado=1 and uo.centro_costos=1 $sqlUO_x order by nombre";
                                    $statementUO1 = $dbh->query($queryUO1);
                                    while ($row = $statementUO1->fetch()){ ?>
                                        <option <?=($cod_uo==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>" data-subtext="(<?=$row['codigo']?>)"><?=$row["abreviatura"];?> - <?=$row["nombre"];?></option>
                                    <?php } ?>
                                </select>
                               
                            </div>
                          </div>
                          <label class="col-sm-2 col-form-label">Area</label>
                            <div class="col-sm-4">
                                <div class="form-group" >                                    
                                    <div id="div_contenedor_area">
                                        <select name="cod_area" id="cod_area" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">
                                            <option value=""></option>

                                            <?php 
                                            $sqlArea="SELECT codigo,nombre,abreviatura from areas where cod_estado=1 and centro_costos=1 $sqlAreas_x order by nombre";
                                            $stmtArea = $dbh->prepare($sqlArea);
                                            $stmtArea->execute();
                                            while ($rowArea = $stmtArea->fetch()){ ?>
                                                 <option <?=($cod_area==$rowArea["codigo"])?"selected":"";?> value="<?=$rowArea["codigo"];?>" data-subtext="(<?=$rowArea['codigo']?>)"><?=$rowArea["abreviatura"];?> - <?=$rowArea["nombre"];?></option><?php 
                                            } ?>
                                        </select>
                                    </div>                    
                                </div>
                            </div>
                        </div>
                        <!--fin ofician y area -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Fecha<br>Registro</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="date" name="fecha_registro" id="fecha_registro" required="true" value="<?=$fecha_registro;?>" required="true"/>
                                    <input type="hidden" name="fecha_solicitudfactura" id="fecha_solicitudfactura" value="<?=$fecha_solicitudfactura;?>"/>
                                </div>
                            </div>                                                        
                            <label class="col-sm-2 col-form-label">Tipo Objeto</label>
                            <div class="col-sm-4">
                                <div class="form-group" >
                                    <select name="cod_tipoobjeto" id="cod_tipoobjeto" class="selectpicker form-control form-control-sm" data-style="btn btn-info">
                                        <?php 
                                        $queryTipoObjeto = "SELECT codigo,nombre FROM  tipos_objetofacturacion WHERE codigo in (214,840) order by nombre";
                                        $statementObjeto = $dbh->prepare($queryTipoObjeto);
                                        $statementObjeto -> execute();
                                        $nc=0;$cont= array();
                                        while ($row = $statementObjeto->fetch()){
                                            ?>
                                            <option <?=($cod_tipoobjeto==$row["codigo"])?"selected":"";?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                            <?php 
                                        }
                                        ?>
                                    </select>                                      
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Fecha<br>Facturación</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="date" name="fecha_facturacion" id="fecha_facturacion" required="true" value="<?=$fecha_facturacion;?>"/>
                                </div>
                            </div>
                            <label class="col-sm-2 d-none col-form-label" id="div_nrotarjeta1" >Número Tarjeta</label>
                            <div class="col-sm-4 d-none" id="div_nrotarjeta2" >
                                <div class="form-group">
                                    <input class="form-control" type="text" name="nro_tarjeta" id="nro_tarjeta" value="<?=$nro_tarjeta;?>"  style='height:40px;font-size:25px;width:80%;background:#D7B3D8 !important; float:left; margin-top:4px; color:#4C079A;'/>
                                </div>
                            </div>
                        </div>
                        <!-- fin fechas -->
                        <div class="row" >
                            <script>var nfac=[];itemTipoPagos_facturacion.push(nfac);var nfacAreas=[];itemAreas_facturacion.push(nfacAreas);</script>
                            <div class="">
                                <?php
                                    // añadimos los porcetnajes de distribucion tanto para areas y formas de pago 
                                    require_once 'simulaciones_servicios/objeto_formaspago_areas.php';
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
                            <label class="col-sm-2 col-form-label">Forma de Pago</label>
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
                                            <option <?=($cod_tipopago==$row["codigo"])?"selected":(($cod_facturacion>0)?"disabled":"");?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                        <?php } 
                                        $cont[0]=$nc;
                                        ?>
                                    </select>                                
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group" >
                                    <button type="button" class="btn btn-danger btn-round btn-fab btn-sm" data-toggle="modal" data-target="" onclick="agregarDatosModalTipoPagoFacturacion(1)">
                                        <i class="material-icons" title="Forma de Pago Porcentaje">list</i>
                                        <span id="nfac" class="count bg-warning"></span>
                                     </button>
                                     <button type="button" class="btn btn-primary btn-round btn-fab btn-sm" data-toggle="modal" data-target="" onclick="agregarDatosModalAreasFacturacion(1)">
                                        <i class="material-icons" title="Areas Porcentaje">list</i>
                                        <span id="nfacAreas" class="count bg-warning"></span>
                                     </button>                              
                                </div>
                            </div>                            
                            <label class="col-sm-1 col-form-label"><small>Responsable</small></label>
                            <div class="col-sm-4">
                                <div class="form-group">            
                                    <?php  $responsable=namePersonal($cod_personal); ?>
                                    <input type="hidden" name="cod_personal" id="cod_personal" value="<?=$cod_personal?>" readonly="true" class="form-control">
                                    <input type="text" value="<?=$responsable?>" readonly="true" class="form-control" style="background-color:#E3CEF6;text-align: left">
                                </div>
                            </div>
                        </div>
                         <div class="row dias_credito_x" id="" style="display: none">                            
                            <label class="col-sm-2 col-form-label">Días de Crédito</label>
                            <div class="col-sm-2">
                                <div class="form-group">                                
                                    <input type="number" class="form-control" name="dias_credito" id="dias_credito" value="<?=$dias_credito?>">
                                </div>
                            </div>                            
                        </div>
                        <!-- fin tipos pago y objeto                 -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Cliente</label>
                            <div class="col-sm-4">
                                <div class="form-group" >                                                            
                                    <select name="cod_cliente" id="cod_cliente" class="selectpicker form-control form-control-sm" data-style="btn btn-info"  required="true" onChange="ajaxClienteContacto(this);" data-live-search="true">
                                        <option value=""></option>
                                        <?php 
                                        $queryTipoObjeto = "SELECT codigo,nombre from clientes where cod_estadoreferencial=1 order by nombre";
                                        $statementObjeto = $dbh->query($queryTipoObjeto);
                                        while ($row = $statementObjeto->fetch()){ 
                                            $nombreClienteSelect=$row["nombre"];
                                            $nombreClienteSelect=substr($nombreClienteSelect, 0, 100);
                                        ?>
                                            <option <?=($cod_cliente==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>"><?=$nombreClienteSelect;?></option>
                                        <?php } ?>
                                    </select>  
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Persona Contacto</label>
                            <div class="col-sm-3">
                                <div class="form-group" >
                                    <div id="div_contenedor_contactos">
                                        <select class="selectpicker form-control form-control-sm" name="persona_contacto" id="persona_contacto" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" title="Seleccione Contacto">
                                          <?php 
                                          $query="SELECT codigo,nombre,paterno FROM clientes_contactos where cod_cliente='$cod_cliente' order by nombre";
                                          $stmt = $dbh->prepare($query);
                                          $stmt->execute();
                                          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $codigo_contacto=$row['codigo'];    
                                            $nombre_conatacto=$row['nombre']." ".$row['paterno'];
                                            ?><option <?=($persona_contacto==$codigo_contacto)?"selected":"";?> value="<?=$codigo_contacto?>" class="text-right"><?=$nombre_conatacto?></option>
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
                        <!-- fin cliente  -->
                        <div id="contenedor_razon_nit">
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Razón Social</label>
                                <div class="col-sm-4">
                                    <div class="form-group">                
                                            <input class="form-control" type="text" name="razon_social" id="razon_social" required="true" value="<?=$razon_social;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>                                        
                                    </div>
                                </div>
                                <!-- <label class="col-sm-1 col-form-label">Nit</label> -->
                                <div class="col-sm-2" >
                                    <select class="selectpicker form-control form-control-sm" name="tipo_documento" id="tipo_documento" data-style="btn btn-success" data-show-subtext="true" data-live-search="true" title="Seleccione Tipo de documento" onChange='mostrarComplemento();'>
                                    <?php
                                    $sql2="SELECT codigo,nombre from siat_tipos_documentoidentidad where cod_estadoreferencial=1";
                                    $stmtTipoIdentificacion = $dbh->prepare($sql2);
                                    $stmtTipoIdentificacion->execute();
                                    while ($rowTipoIden = $stmtTipoIdentificacion->fetch(PDO::FETCH_ASSOC)) {
                                        $codigo_identificacionx=$rowTipoIden['codigo'];    
                                        $nombre_identificacionx=$rowTipoIden['nombre'];
                                        ?><option <?=($codigo_identificacion==$codigo_identificacionx)?"selected":"";?> value="<?=$codigo_identificacionx?>" class="text-right"><?=$nombre_identificacionx?></option>
                                       <?php 
                                    } ?> 
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="nit" id="nit" value="<?=$nit;?>" required="true"/>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                            <input class="form-control" type='hidden' name="complemento" id="complemento" placeholder="Complemento" value="<?=$complemento;?>" style="position:absolute;width:100px !important;background:#D2FFE8;" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                                    </div>
                                </div>
                            </div>
                        <!--     <div class="row">
                              <div class="col-sm-12" style="background-color:#FFFFFF">
                                <div class="form-group" >
                                  <input type="text" name="correo_destino" id="correo_destino" class="form-control tagsinput" data-role="tagsinput" data-color="info" required="true" >  
                                </div>
                              </div>
                            </div> -->
                        </div>
                        <!-- fin razon social y nit -->

                            <div class="row">
                                <label class="col-sm-2 col-form-label">Correo De Contacto <br>Para Envío De Factura.</label>
                                <div class="col-sm-10">
                                    <div class="form-group">
                                        <input type="text" name="correo_contacto" id="correo_contacto" value="<?=$correo_contacto;?>" class="form-control tagsinput" data-role="tagsinput" data-color="info"> 
                                    </div>
                                </div>
                            </div>
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Observaciones * 1</label>
                            <div class="col-sm-10">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="observaciones" id="observaciones"  value="<?=$observaciones;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" required/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Concepto para facturación Especial</label>
                            <div class="col-sm-10">
                                <div class="form-group">
                                    <textarea class="form-control" type="text" name="observaciones_2" id="observaciones_2" rows="4" placeholder="Solo a requerimiento del cliente, coordinar con Administración para la impresión"><?=$observaciones_2;?></textarea>
                                </div>
                            </div>
                        </div>
                        <!-- fin observaciones -->
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
                        <!-- detalle de sol fac -->
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
                                <?php 
                                if($cod_facturacion>0){?>
                                    <table class="table table-bordered table-condensed table-striped table-sm">
                                         <thead>
                                              <tr class="fondo-boton">
                                                <th>#</th>
                                                <!-- <th >Año</th> -->
                                                <th width="15%">Item</th>
                                                <th>Cant.</th>
                                                <th width="8%">Precio<br>(BOB)</th>
                                                <th width="5%">Desc(%)</th>
                                                <th width="5%">Desc<br>(BOB)</th>
                                                <th width="8%">Importe<br>(BOB)</th>
                                                <th width="40%">Glosa</th>
                                                <th class="small">H/D</th>  
                                              </tr>
                                          </thead>
                                          <tbody>                                
                                            <?php 
                                            $iii=1;                                            
                                            if ($cod_facturacion > 0){                                                
                                                $queryPr="SELECT d.codigo,d.cod_claservicio,(select cs.Descripcion from cla_servicios cs where cs.IdClaServicio=d.cod_claservicio) as nombre_serv,d.cantidad,d.precio as monto,tipo_item from solicitudes_facturaciondetalle d where d.tipo_item=2 and d.cod_solicitudfacturacion=$cod_facturacion";
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
                                                $cantidadEPre=$cantidadPre;
                                                $montoPre=$rowPre['monto'];
                                                $tipo_item=$rowPre['tipo_item'];
                                                $descuento_bob_cliente=$montoPre*$descuento_cliente; 
                                                // $montoPreTotal=$montoPre*$cantidadEPre;
                                                $banderaHab=1;                                                
                                                $cod_anio=1;
                                                if($banderaHab!=0){
                                                    $descuento_porX=0;
                                                    $descuento_bobX=0;
                                                    $descripcion_alternaX=$tipoPre;
                                                    // $modal_totalmontopre+=$montoPre;
                                                    $montoPre=number_format($montoPre,2,".","");
                                                    // $montoPre=round($montoPre,2);
                                                    //parte del controlador de check
                                                    $sw="";//para la parte de editar
                                                    $sw2="";//para registrar nuevos, impedir los ya registrados
                                                    if($cod_facturacion>0){//edit
                                                        $sqlControlador="SELECT sfd.precio,sfd.descuento_por,sfd.descuento_bob,sfd.descripcion_alterna from solicitudes_facturacion sf,solicitudes_facturaciondetalle sfd where sf.codigo=sfd.cod_solicitudfacturacion and sf.cod_simulacion_servicio=$cod_simulacion and sfd.cod_claservicio=$codCS and sf.codigo=$cod_facturacion and sf.tipo_solicitud=4";
                                                        // echo $sqlControlador;
                                                        $stmtControlado = $dbh->prepare($sqlControlador);
                                                       $stmtControlado->execute();                                           
                                                        while ($rowPre = $stmtControlado->fetch(PDO::FETCH_ASSOC)) {
                                                            $sw="checked";
                                                            $montoPre=$rowPre['precio'];
                                                            $descuento_porX=$rowPre['descuento_por'];
                                                            $descuento_bobX=$rowPre['descuento_bob'];
                                                            $descripcion_alternaX=$rowPre['descripcion_alterna'];
                                                            if($tipo_item==2){
                                                                $montoPre=$montoPre+$descuento_bobX/$cantidadPre;
                                                            }
                                                            $montoPre=round($montoPre,2);
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
                                                      <!-- <td class="text-left"><?=$cod_anio?> </td> -->
                                                      <td class="text-left"><small><?=$tipoPre?></small></td>
                                                      <td class="text-right"><small><?=$cantidadPre?></small></td>
                                                      <td class="text-right"><input type="number" step="0.01" id="monto_precio<?=$iii?>" name="monto_precio<?=$iii?>" class="form-control text-primary text-right"  value="<?=$montoPre?>" onkeyup="activarInputMontoFilaServicio_manual()" <?=$sw2?>></td>
                                                      <!--  descuentos -->
                                                      <td class="text-right"><input type="number" step="0.01" class="form-control" name="descuento_por<?=$iii?>" id="descuento_por<?=$iii?>" value="<?=$descuento_porX?>" min="0" onkeyup="descuento_convertir_a_bolivianos_manual(<?=$iii?>)" <?=$sw2?>></td>                                             
                                                      <td class="text-right"><input type="number" class="form-control" name="descuento_bob<?=$iii?>" id="descuento_bob<?=$iii?>" value="<?=$descuento_bobX?>" min="0" onkeyup="descuento_convertir_a_porcentaje_manual(<?=$iii?>)" <?=$sw2?>></td>                                        
                                                      <!-- total -->
                                                      <td class="text-right"><input type="hidden" name="modal_importe<?=$iii?>" id="modal_importe<?=$iii?>"><input type="text" class="form-control" name="modal_importe_dos<?=$iii?>" id="modal_importe_dos<?=$iii?>" style ="background-color: #ffffff;" readonly></td>
                                                                                                  
                                                      <td>
                                                        <input type="hidden" id="importe_a_pagar<?=$iii?>" name="importe_a_pagar<?=$iii?>" class="form-control text-primary text-right"  value="0" >

                                                        <textarea name="descripcion_alterna<?=$iii?>" id="descripcion_alterna<?=$iii?>" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();" <?=$sw2?>><?=$descripcion_alternaX?></textarea>
                                                         <!-- <input type="text" > -->
                                                      </td>
                                                      <!-- checkbox -->
                                                      <td>
                                                        <?php if($sw2!="readonly style='background-color:#cec6d6;'"){?>
                                                            <div class="togglebutton">
                                                               <label>
                                                                 <input type="checkbox"  id="modal_check<?=$iii?>" onchange="activarInputMontoFilaServicio_manual()" <?=$sw?> >
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
                                                    // window.onload = activarInputMontoFilaServicio2;
                                                    window.onload = activarInputMontoFilaServicio_manual;
                                                </script>

                                                <?php
                                            
                                            } ?>                        
                                          </tbody>
                                    </table>                                    
                                    <input type="hidden" value="0" name="modal_totalmontoserv_costo_a" id="modal_totalmontoserv_costo_a"/>


                                    <input type="hidden" id="modal_numeroservicio" name="modal_numeroservicio" value="<?=$iii?>">                    
                                    <input type="hidden" id="modal_totalmontos" name="modal_totalmontos">
                                    <!-- <script>activarInputMontoFilaServicio2();</script>   -->
                                    <input type="hidden" id="comprobante_auxiliar" name="comprobante_auxiliar" value="0">
                                    <div class="row">
                                        <label class="col-sm-5 col-form-label" style="color:#000000">Monto Total</label>
                                        <div class="col-sm-4">
                                            <div class="form-group">                                        
                                                <input style="background:#ffffff" class="form-control" type="text" value="0" name="modal_totalmontoserv" id="modal_totalmontoserv" step="0.01" readonly="true" />                                            
                                            </div>
                                        </div>
                                    </div><?php 
                                }?>


                                <fieldset id="fiel" style="width:100%;border:0;">
                                    <button title="Agregar Servicios" type="button" id="add_boton" name="add" class="btn btn-warning btn-round btn-fab" onClick="AgregarSeviciosFacturacion_soli_manual(this)">
                                        <i class="material-icons">add</i>
                                    </button><span style="color:#084B8A;"><b> SERVICIOS ADICIONALES</b></span>
                                    <div class="row" style="background-color:#1a2748">
                                        <label class="col-sm-3 col-form-label" style="color:#ff9c14; text-align: center">Servicios</label>
                                        <label class="col-sm-1 col-form-label" style="color:#ff9c14; text-align: center">Cant</label>
                                        <label class="col-sm-1 col-form-label" style="color:#ff9c14; text-align: center">Precio<br>(BOB)</label>
                                        <label class="col-sm-1 col-form-label" style="color:#ff9c14; text-align: center">Desc<br>(%)</label>
                                        <label class="col-sm-1 col-form-label" style="color:#ff9c14; text-align: center">Desc<br>(BOB)</label>
                                        <label class="col-sm-1 col-form-label" style="color:#ff9c14; text-align: center">Importe<br>(BOB)</label>
                                        <label class="col-sm-3 col-form-label" style="color:#ff9c14;text-align: center;">Glosa</label>
                                        <label class="col-sm-1 col-form-label" style="color:#ff9c14;text-align: center">Eliminar</label>
                                    </div>
                                    <div id="div">  
                                        <div class="h-divider">
                                        
                                        </div>
                                    </div>
                                    

                                </fieldset>
                                <div class="row">
                                    <label class="col-sm-5 col-form-label" style="color:#000000">Monto Total  + Servicios Adicionales </label>
                                    <div class="col-sm-4">
                                        <div class="form-group">                                            
                                            <input style="background:#ffffff" class="form-control"  name="monto_total" id="monto_total"  readonly="readonly" value="0" step="0.01" />
                                            <input  type="hidden"  name="monto_total_a" id="monto_total_a" value="0" step="0.01" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer fixed-bottom">
                        <button type="submit" class="<?=$buttonNormal;?>">Guardar</button><?php
                        if(isset($_GET['q'])){  ?>
                            <a href='<?=$urlSolicitudfactura;?>&q=<?=$q?>&v=<?=$v?>&s=<?=$s?>&u=<?=$u?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> IR A SF </a>
                        <?php }else{?>
                            <a href='<?=$urlSolicitudfactura?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> IR A SF </a>
                        <?php }
                        ?>
                    </div>
                    <?php  require_once 'simulaciones_servicios/modal_subir_archivos.php';?>
                    
                </form>                  
            </div>
        </div>
    </div>
</div>

<!--    end small modal -->
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
/**
 * Validación - Porcentaje de Distribución del Ingreso por Area 
 */
function valida(f) {
    //verificamos monto mayor a 50
    //var nro_cuenta_doc=$("#nro_cuenta_doc").val();//monto total de items
    // alert(cod_tipopago+"-"+cod_defecto_deposito_cuenta);
    // if(f.elements["monto_total_a"].value>=50000 && nro_cuenta_doc==0)
    // {   
    //     var msg = "El monto total supera el límite de bancarización. Por favor debe llenar el siguiente formulario...\n";
    //     $('#modalBancarizacion').modal('show');
    //     ok = false;
    // }else{
    //     var ok = true;
    //     var msg = "El monto Total no debe ser '0' o 'negativo', Habilite los Items que desee facturar...\n";  
    //     if(f.elements["monto_total"].value<=0)
    //     {    
    //         ok = false;
    //     }
    // }

    var ok = true;
    var msg = "El monto Total no debe ser '0' o 'negativo', Habilite los Items que desee facturar...\n";  
    if(f.elements["monto_total"].value<=0)
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

    //validamos correos
    var correo_contacto=f.elements["correo_contacto"].value;
        if(correo_contacto=="" || correo_contacto==null || correo_contacto==" "){
            var msg = "Correo Electronico no válido.";        
            ok = false;
        }else{
            var parts = correo_contacto.split(',');
            //Creo esta variable para detener el ciclo siguiente cuando no es un correo válido    
            //Recorro la lista de los correos
            for(var i in parts){
                /*Verifico que sea una dirección de correo correcta, es la misma
                  expresión regular que usa esta librería para la regla email */
                if(/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test(parts[i].trim()) === false){
                    //Si falla la verificación pongo a true esta variable y salgo del ciclo          
                    var msg = "Correo Electronico no válido. -> "+parts[i];        
                    ok = false;
                  break;
                }
            }    
        }

    if(ok == false)Swal.fire("Informativo!",msg, "warning");
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