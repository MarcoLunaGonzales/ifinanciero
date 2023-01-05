<?php

require_once 'conexion.php';
require_once 'conexion_externa.php';
require_once 'styles.php';
require_once 'configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();
$cod_empresa=$codigo;
$cod_simulacion=$cod_simulacion;//agarramos el id del curso
$cod_facturacion=$cod_facturacion;
$IdCurso=$IdCurso;
$globalUser=$_SESSION["globalUser"];
$globalUnidad=$_SESSION['globalUnidad'];
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $r=$_GET['r'];
  $s=$_GET['s'];
  $u=$_GET['u'];
}
if(isset($_GET['cod_sw'])){
  $cod_sw=$_GET['cod_sw'];  
}

$dbhIBNO = new ConexionIBNORCA();

//nombre del curso de ibnoca
$stmtIBNOCurso = $dbhIBNO->prepare("SELECT * from programas_cursos pc where pc.idEmpresa<>0 and IdCurso=$IdCurso;");//poner el codigo de curso a buscar
$stmtIBNOCurso->execute();
$resultNombreCurso = $stmtIBNOCurso->fetch();
$nombre_curso = $resultNombreCurso['Nombre'];
$cod_uo = $resultNombreCurso['IdOficina'];
$cod_area=13;
$cantidadModulos = $resultNombreCurso['CantidadModulos'];
$Costo = $resultNombreCurso['Costo']/$cantidadModulos;
$Costo = number_format($Costo, 2, '.', '');
// $monto_modulos=$Costo/$cantidadModulos;
$nombre_cliente=nameCliente($cod_empresa);
$Codigo_alterno=obtenerCodigoExternoCurso($IdCurso);
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
// echo $sqlUO_x;

// $sqlUO_x="";
// $sqlAreas_x="";
if($cod_facturacion>0){//editar
    $sqlFac="SELECT sf.*,sfd.precio,sfd.descuento_por,sfd.descuento_bob from solicitudes_facturacion sf,solicitudes_facturaciondetalle sfd where sfd.cod_solicitudfacturacion=sf.codigo and sf.codigo=$cod_facturacion";
    // echo $sqlFac;
    $stmtSimuFact = $dbh->prepare($sqlFac);
    $stmtSimuFact->execute();
    $resultSimuFact = $stmtSimuFact->fetch();
    $fecha_registro = $resultSimuFact['fecha_registro'];
    $fecha_solicitudfactura = $resultSimuFact['fecha_solicitudfactura'];
    $razon_social = $resultSimuFact['razon_social'];
    $nit = $resultSimuFact['nit'];
    $observaciones = $resultSimuFact['observaciones'];
    $observaciones_2 = $resultSimuFact['observaciones_2'];
    $cod_tipopago=$resultSimuFact['cod_tipopago'];
    $cod_tipoobjeto=$resultSimuFact['cod_tipoobjeto'];
    $cod_personal= $resultSimuFact['cod_personal'];
    $monto_pagar=$resultSimuFact['precio'];
    $descuento_por=$resultSimuFact['descuento_por'];
    $descuento_bob=$resultSimuFact['descuento_bob'];
    $persona_contacto= $resultSimuFact['persona_contacto'];
    $dias_credito=$resultSimuFact['dias_credito'];
    $cod_uo= $resultSimuFact['cod_unidadorganizacional'];
    $cod_area= $resultSimuFact['cod_area'];
    $correo_contacto=$resultSimuFact['correo_contacto'];


    $codigo_identificacion=$resultSimuFact['siat_tipoidentificacion'];
    $complemento=$resultSimuFact['siat_complemento'];
    $fecha_facturacion=$resultSimuFact['fecha_facturacion'];
    $nro_tarjeta=$resultSimuFact['nro_tarjeta'];
}else{//registrat
    $fecha_registro = date('Y-m-d');
    $fecha_solicitudfactura = date('Y-m-d');
    $razon_social= $nombre_cliente;
    $nit = obtenerNitCliente($cod_empresa);;
    $observaciones = $Codigo_alterno." - ".$nombre_cliente;
    $observaciones_2 = null;
    $cod_tipopago=null;
    $persona_contacto= null;
    $cod_tipoobjeto=212;//por defecto}
    if(isset($_GET['q'])){
        $cod_personal=$_GET['q'];
    }else{
        $cod_personal= $globalUser;
    }
    $descuento_por=0;
    $descuento_bob=0;
    $dias_credito=obtenerValorConfiguracion(58);
    $correo_contacto=obtenerCorreoEstudiante($nit);
    $correo_contacto=trim($correo_contacto,",");

    $codigo_identificacion=null;//por defecto ci
    $complemento="";
    $fecha_facturacion=date('Y-m-d');
    $nro_tarjeta="";
}
$name_tipoPago=obtenerNombreTipoPago($cod_tipoobjeto);
$cod_defecto_deposito_cuenta=obtenerValorConfiguracion(55);
$cod_defecto_cod_tipo_credito=obtenerValorConfiguracion(48);
// $name_uo=nameUnidad($cod_uo);
// $name_area=trim(abrevArea($cod_area),'-');
$contadorRegistros=0;

$descuento_cliente=0;
?>
<script>
  numFilas=<?=$contadorRegistros;?>;
  cantidadItems=<?=$contadorRegistros;?>;
</script>
<div class="content">
    <div class="container-fluid">
        <div style="overflow-y:scroll;">
            <div class="col-md-12">
            <form id="formSoliFactTcp" class="form-horizontal" action="<?=$urlSave_solicitud_facturacion_costos_empresa;?>" method="post" onsubmit="return valida(this)" enctype="multipart/form-data">
                <?php
                if(isset($_GET['q'])){?>
                    <input type="hidden" name="q" id="q" value="<?=$q?>">
                    <input type="hidden" name="s" id="s" value="<?=$s?>">
                    <input type="hidden" name="u" id="u" value="<?=$u?>">
                    <input type="hidden" name="r" id="r" value="<?=$r?>">
                <?php }
                if(isset($_GET['cod_sw'])){?>
                    <input type="hidden" name="cod_sw" id="cod_sw" value="<?=$cod_sw?>">                    
                <?php }
                ?> 
                <input type="hidden" name="cod_defecto_deposito_cuenta" id="cod_defecto_deposito_cuenta" value="<?=$cod_defecto_deposito_cuenta?>"/>
                <input type="hidden" name="cod_defecto_cod_tipo_credito" id="cod_defecto_cod_tipo_credito" value="<?=$cod_defecto_cod_tipo_credito?>"/>
                <input type="hidden" name="Codigo_alterno" id="Codigo_alterno" value="<?=$Codigo_alterno;?>"/>  
                <input type="hidden" name="cod_empresa" id="cod_empresa" value="<?=$cod_empresa;?>"/>
                <input type="hidden" name="cod_simulacion" id="cod_simulacion" value="<?=$cod_simulacion;?>"/>
                <input type="hidden" name="cod_facturacion" id="cod_facturacion" value="<?=$cod_facturacion;?>"/>
                <input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">
                <input type="hidden" name="IdCurso" id="IdCurso" value="<?=$IdCurso;?>">
                <input type="hidden" name="tipo_solicitud" id="tipo_solicitud" value="2">
                <input type="hidden" name="tipo_aux" id="tipo_aux" value="1"><!-- //nos indica de donde va para editar adjuntos -->
                <div class="card">
                    <div class="card-header <?=$colorCard;?> card-header-text">
                        <div class="card-text">
                          <h4 class="card-title"><?php if ($cod_facturacion == 0) echo "Registrar "; else echo "Editar ";?>Solicitud de Facturación</h4>                      
                        </div>
                        <h4 class="card-title" align="center"><b>Nombre Curso : <?=$Codigo_alterno?> - <?=$nombre_curso?></b></h4>
                        <!-- <h4 class="card-title" align="center"><b>Módulo : <?=$NroModulo?></b></h4> -->
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
                                    <!-- <div id="div_contenedor_area"> -->
                                        <select name="cod_area" id="cod_area" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true"> 
                                            <?php 
                                            $sqlArea="SELECT codigo,nombre,abreviatura from areas where cod_estado=1 and centro_costos=1 $sqlAreas_x order by nombre";
                                            $stmtArea = $dbh->prepare($sqlArea);                                            
                                            $stmtArea->execute();
                                            while ($rowArea = $stmtArea->fetch()){ ?>
                                                 <option <?=($cod_area==$rowArea["codigo"])?"selected":"disabled";?> value="<?=$rowArea["codigo"];?>" data-subtext="(<?=$rowArea['codigo']?>)"><?=$rowArea["abreviatura"];?> - <?=$rowArea["nombre"];?></option><?php 
                                            } ?>
                                        </select>                                   
                                    <!-- </div>   -->                  
                                </div>
                            </div>
                        </div>
                        <!-- unidad  / area -->                       
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

                        <div class="row">
                            <label class="col-sm-2 col-form-label">Fecha<br>Facturación</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="date" name="fecha_facturacion" id="fecha_facturacion" required="true" value="<?=$fecha_solicitudfactura;?>"/>
                                </div>
                            </div>
                            <label class="col-sm-2 d-none col-form-label" id="div_nrotarjeta1" >Número Tarjeta</label>
                            <div class="col-sm-4 d-none" id="div_nrotarjeta2" >
                                <div class="form-group">
                                    <input class="form-control" type="text" name="nro_tarjeta" id="nro_tarjeta" value="<?=$nro_tarjeta;?>"  style='height:40px;font-size:25px;width:80%;background:#D7B3D8 !important; float:left; margin-top:4px; color:#4C079A;'/>
                                </div>
                            </div>
                        </div>

                        <div class="row">          
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
                                            <option <?=($cod_tipopago==$row["codigo"])?"selected":(($cod_facturacion>0)?"disabled":"");?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                        <?php } 
                                        $cont[0]=$nc;
                                        ?>
                                    </select>                                     
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group" >    
                                    <button type="button" class="btn btn-danger btn-round btn-fab btn-sm" data-toggle="modal" data-target="" onclick="agregarDatosModalTipoPagoFacturacion(2)">
                                        <i class="material-icons" title="Forma de Pago Porcentaje">list</i>
                                        <span id="nfac" class="count bg-warning"></span>
                                     </button>
                                     <button type="button" class="btn btn-primary btn-round btn-fab btn-sm" data-toggle="modal" data-target="" onclick="agregarDatosModalAreasFacturacion(2)">
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
                        <!-- fin tipos pago y objeto  -->                                                 
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Empresa</label>
                            <div class="col-sm-4">
                                <div class="form-group" >
                                    <input class="form-control" type="hidden" name="cod_cliente" id="cod_cliente" required="true" value="<?=$cod_empresa;?>" required="true" readonly/>                                  
                                    <input class="form-control" type="text" id="nombreAlumno" name="nombreAlumno" value="<?=$nombre_cliente;?>" required="true" readonly style="background-color:#E3CEF6;text-align: left"/>
                                        
                                </div>
                            </div> 
                            <label class="col-sm-2 col-form-label">Persona Contacto</label>
                            <div class="col-sm-3">                                
                                <div id="div_contenedor_contactos">
                                    <select class="selectpicker form-control form-control-sm" name="persona_contacto" id="persona_contacto" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" title="Seleccione Contacto">
                                        <?php 
                                        $query="SELECT * FROM clientes_contactos where cod_cliente=$cod_empresa order by nombre";
                                        $stmt = $dbh->prepare($query);
                                        $stmt->execute();
                                        while ($rowContacto = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $codigo_contacto=$rowContacto['codigo'];    
                                        $nombre_conatacto=$rowContacto['nombre']." ".$rowContacto['paterno'];
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
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <div id="contenedor_razonsocial">
                                        <input class="form-control" type="text" name="razon_social" id="razon_social" required="true" value="<?=$razon_social;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>    
                                    </div>
                                </div>
                            </div>
                            <!-- <label class="col-sm-1 col-form-label">Nit</label> -->
                            <div class="col-sm-2" >
                                <select class="selectpicker form-control form-control-sm" name="tipo_documento" id="tipo_documento" data-style="btn btn-danger" title="Tipo de documento" onChange='mostrarComplemento();' required="true">
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
                                    <input class="form-control" type="number" name="nit" id="nit" required="true" value="<?=$nit;?>" required="true"/>
                                </div>
                            </div>
                             <div class="col-sm-1">
                                <div class="form-group">
                                        <input class="form-control" type='hidden' name="complemento" id="complemento" placeholder="Complemento" value="<?=$complemento;?>" style="position:absolute;width:100px !important;background:#D2FFE8;" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                                </div>
                            </div>
                        </div>
                        <!-- fin razon social y nit -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Correo De Contacto <br>Para Envío De Factura.</label>
                            <div class="col-sm-10">
                                <div class="form-group">
                                    <!-- <input class="form-control" type="email" name="correo_contacto" id="correo_contacto" value="<?=$correo_contacto;?>" required/> -->
                                    <input type="text" name="correo_contacto" id="correo_contacto" value="<?=$correo_contacto;?>" class="form-control tagsinput" data-role="tagsinput" data-color="info"  > 
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
                            <label class="col-sm-2 col-form-label">Concepto para Facturación (Solo casos especiales)</label>
                            <div class="col-sm-10">
                                <div class="form-group">
                                    <!-- <input class="form-control" type="text" name="observaciones_2" id="observaciones_2" value="<?=$observaciones_2;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/> -->
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
                        <div class="card">
                            <div class="card-header <?=$colorCard;?> card-header-text">
                                <div class="card-text">
                                  <h6 class="card-title">Detalle Solicitud Facturación</h6>
                                </div>
                            </div>
                            <div class="card-body ">
                                <table class="table table-bordered table-condensed table-sm">
                                    <thead>
                                        <tr class="fondo-boton">
                                            <th>Item</th>
                                            <th width="3%">Cant.</th>
                                            <th width="6%">Precio<br>(BOB)</th>
                                            <th width="4%">Desc<br>(%)</th>
                                            <th width="4%">Desc<br>(BOB)</th>
                                            <th width="6%">Importe<br>(BOB)</th>
                                            <th width="6%">Importe<br>Pagado</th>
                                            <th width="6%">Importe<br>a pagar</th>  
                                            <th width="4%" class="small">H/D</th>
                                        </tr>
                                    </thead>
                                    <tbody>                                
                                        <?php 
                                        $iii=1;
                                        $queryPr="SELECT m.*,(select d_clasificador(m.IdTema))as nombre_tema from modulos m where m.IdCurso=$IdCurso;";
                                        // echo $queryPr;
                                        $stmt = $dbhIBNO->prepare($queryPr);
                                        $stmt->execute();
                                        $modal_totalmontopre=0;$modal_totalmontopretotal=0;
                                        while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $codigoPre=$rowPre['IdTema'];
                                            //$codCS=430;//defecto
                                            $codCS=$rowPre['IdModulo'];
                                            $NroModulo=$rowPre['NroModulo'];
                                            $nombre_tema=$rowPre['nombre_tema'];
                                            if($nombre_tema==null || $nombre_tema=='')$nombre_tema=$rowPre['Nombre'];
                                            $tipoPre=$Codigo_alterno." - Mod:".$NroModulo." - ".$nombre_tema;
                                            $cantidadPre=1;                                            
                                            $saldo=$Costo;
                                            $monto_total_pagado=0;
                                            $descuento_bob_cliente=$Costo*$descuento_cliente; 
                                            $banderaHab=1;                                            
                                            $codTipoUnidad=1;
                                            $cod_anio=1;   
                                            $controlador_auxiliar=0;//para habilitar la impresión de sf                                   
                                            if($banderaHab!=0){
                                                $descuento_porX=0;
                                                $descuento_bobX=0;
                                                $descripcion_alternaX=$tipoPre;
                                                $sw="";//para la parte de editar
                                                
                                                // $montoPre=number_format($montoPre,2,".","");
                                                if($cod_facturacion>0){                                                    
                                                    //parte del controlador de check
                                                    $sqlControlador="SELECT sfd.precio,sfd.descuento_por,sfd.descuento_bob,sfd.descripcion_alterna from solicitudes_facturacion sf,solicitudes_facturaciondetalle sfd where sf.codigo=sfd.cod_solicitudfacturacion and sf.cod_simulacion_servicio=$IdCurso  and sfd.cod_claservicio=$codCS and sf.codigo=$cod_facturacion and tipo_solicitud=6";
                                                    // echo $sqlControlador;
                                                    $stmtControlado = $dbh->prepare($sqlControlador);
                                                   $stmtControlado->execute();                                           
                                                   
                                                    while ($rowPre = $stmtControlado->fetch(PDO::FETCH_ASSOC)) {
                                                        $sw="checked";
                                                        $montoPre=$rowPre['precio']+$rowPre['descuento_bob'];
                                                        $preciox=$rowPre['precio'];                                                        
                                                        $descripcion_alternaX=$rowPre['descripcion_alterna'];
                                                        // $monto_pagadoX=$rowPre['monto_pagado'];
                                                    }
                                                    // $monto_total_pagado=$preciox;                                                    
                                                }
                                                    // echo $IdCurso."-".$cod_empresa."-".$codCS."<br>";                                                    
                                                    $sw2="";
                                                    //parte del controlador de check//impedir los ya registrados
                                                    $sqlControlador2="SELECT sfd.precio,sfd.cod_solicitudfacturacion,sfd.descuento_por,sfd.descuento_bob,sfd.descripcion_alterna from solicitudes_facturacion sf,solicitudes_facturaciondetalle sfd where sf.codigo=sfd.cod_solicitudfacturacion and sf.cod_simulacion_servicio=$IdCurso  and sfd.cod_claservicio=$codCS and sf.cod_cliente=$cod_empresa and tipo_solicitud=6 and sf.cod_estadosolicitudfacturacion!=2";
                                                     // echo $sqlControlador2;
                                                    $stmtControlador2 = $dbh->prepare($sqlControlador2);
                                                    $stmtControlador2->execute();
                                                    //sacamos el monto total
                                                    $sqlControladorTotal="SELECT SUM(sfd.precio) as precio from solicitudes_facturacion sf,solicitudes_facturaciondetalle sfd where sf.codigo=sfd.cod_solicitudfacturacion and sf.cod_simulacion_servicio=$IdCurso  and sfd.cod_claservicio=$codCS and sf.cod_cliente=$cod_empresa and tipo_solicitud=6 and sf.cod_estadosolicitudfacturacion!=2";
                                                     // echo $sqlControladorTotal;
                                                    $stmtControladorTotal = $dbh->prepare($sqlControladorTotal);
                                                    $stmtControladorTotal->execute();
                                                    $resultMontoTotal=$stmtControladorTotal->fetch();
                                                    $precio_total_x=$resultMontoTotal['precio'];
                                                    while ($rowPre = $stmtControlador2->fetch(PDO::FETCH_ASSOC)) {
                                                        $cod_solicitudfacturacion_x=$rowPre['cod_solicitudfacturacion'];
                                                        if($sw!="checked"){//si el item  es diferente de editar
                                                            $monto_total_pagado=$precio_total_x;
                                                            $saldo=$Costo-$monto_total_pagado;
                                                            if($Costo==$precio_total_x){
                                                                $controlador_auxiliar=1;
                                                                $sw2="readonly style='background-color:#cec6d6;'";
                                                                $saldo=0;
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

                                                // 
                                                
                                                
                                    
                                                ?>
                                                <!-- guardamos las varialbles en un input -->
                                                <input type="hidden" id="cod_serv_tiposerv<?=$iii?>" name="cod_serv_tiposerv<?=$iii?>" value="<?=$codigoPre?>">
                                                <input type="hidden" id="servicio<?=$iii?>" name="servicio<?=$iii?>" value="<?=$codCS?>">
                                                <input type="hidden" id="nombre_servicio<?=$iii?>" name="nombre_servicio<?=$iii?>" value="<?=$tipoPre?>">
                                                <input type="hidden" id="cantidad<?=$iii?>" name="cantidad<?=$iii?>" value="<?=$cantidadPre?>">
                                                <input type="hidden" id="importe<?=$iii?>" name="importe<?=$iii?>" value="<?=$Costo?>">

                                                <!-- aqui se captura los servicios activados -->
                                                <input type="hidden" id="cod_serv_tiposerv_a<?=$iii?>" name="cod_serv_tiposerv_a<?=$iii?>">
                                                <input type="hidden" id="servicio_a<?=$iii?>" name="servicio_a<?=$iii?>">
                                                <input type="hidden" id="cantidad_a<?=$iii?>" name="cantidad_a<?=$iii?>">
                                                <input type="hidden" id="importe_a<?=$iii?>" name="importe_a<?=$iii?>">
                                                <tr>
                                                  <!-- <td class="text-left"><?=$cod_anio?> </td> -->
                                                    <td class="text-left" width="35%"><textarea name="descripcion_alterna<?=$iii?>" id="descripcion_alterna<?=$iii?>" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();" <?=$sw2?>><?=$descripcion_alternaX?></textarea></td>
                                                    <td class="text-right"><?=$cantidadPre?></td>
                                                    <td class="text-right"><input type="hidden" step="0.01" id="monto_precio<?=$iii?>" name="monto_precio<?=$iii?>" class="form-control text-primary text-right"  value="<?=$Costo?>" step="0.01" <?=$sw2?> readonly="true">
                                                        <input type="text" class="form-control" name="monto_precio_a<?=$iii?>" id="monto_precio_a<?=$iii?>" style ="background-color: #ffffff;" readonly value="<?=number_format($Costo,2);?>">
                                                    </td>
                                                    <!--  descuentos -->
                                                    <td class="text-right"><input type="number" step="0.01" class="form-control" name="descuento_por<?=$iii?>" id="descuento_por<?=$iii?>" value="<?=$descuento_porX?>" min="0" max="<?=$descuento_cliente?>" onkeyup="descuento_convertir_a_bolivianos(<?=$iii?>)" <?=$sw2?> readonly></td>                                             
                                                    <td class="text-right"><input type="number" class="form-control" name="descuento_bob<?=$iii?>" id="descuento_bob<?=$iii?>" value="<?=$descuento_bobX?>" min="0" max="<?=$descuento_bob_cliente?>" onkeyup="descuento_convertir_a_porcentaje(<?=$iii?>)" <?=$sw2?> readonly></td>                                        
                                                    <!-- total -->
                                                    <td class="text-right"><input type="hidden" name="modal_importe<?=$iii?>" id="modal_importe<?=$iii?>"><input type="text" class="form-control" name="modal_importe_dos<?=$iii?>" id="modal_importe_dos<?=$iii?>" readonly></td>
                                                    <td>
                                                        <input type="hidden" name="modal_importe_pagado_dos_a<?=$iii?>" id="modal_importe_pagado_dos_a<?=$iii?>" value="<?=$monto_total_pagado;?>">
                                                        <input type="text" class="form-control" name="modal_importe_pagado_dos<?=$iii?>" id="modal_importe_pagado_dos<?=$iii?>" readonly value="<?=number_format($monto_total_pagado,2);?>">
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" id="importe_a_pagar<?=$iii?>" name="importe_a_pagar<?=$iii?>" class="form-control text-primary text-right"  value="<?=$saldo?>" step="0.01" onkeyup="verificar_item_activo(<?=$iii?>)" <?=$sw2?>>
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
                                                    <?php }else{
                                                        if($controlador_auxiliar==1){?>
                                                                <a rel="tooltip" href='<?=$urlPrintSolicitud;?>?codigo=<?=$cod_solicitudfacturacion_x;?>' target="_blank"><i class="material-icons text-primary" title="Imprimir Solicitud Facturación">print</i></a>
                                                            <?php }
                                                        ?>
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
                                            <td colspan="5">Monto Total</td>
                                            <td><input style="background:#ffffff" class="form-control" type="text" value="0" name="modal_totalmontoserv" id="modal_totalmontoserv" readonly="true" /></td>
                                            <td><input style="background:#ffffff" class="form-control" type="text" value="0" name="modal_totalmontoserv_pagado" id="modal_totalmontoserv_pagado" readonly="true" /></td>
                                            <td>
                                                <input type="hidden" value="0" name="modal_totalmontoserv_costo_a" id="modal_totalmontoserv_costo_a"/>
                                                <input style="background:#ffffff" class="form-control" type="text" value="0" name="modal_totalmontoserv_costo" id="modal_totalmontoserv_costo" readonly="true" /></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <input type="hidden" id="modal_numeroservicio" name="modal_numeroservicio" value="<?=$iii?>">                    
                                <input type="hidden" id="modal_totalmontos" name="modal_totalmontos">
                                <!-- <script>activarInputMontoFilaServicio2();</script>   -->
                                <input type="hidden" id="comprobante_auxiliar" name="comprobante_auxiliar">
                              <!--   <div class="row">
                                    <label class="col-sm-5 col-form-label" style="color:#000000">Monto Total</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">                                        
                                            <input style="background:#ffffff" class="form-control" type="text" value="0" name="modal_totalmontoserv" id="modal_totalmontoserv" readonly="true" />                                            
                                        </div>
                                    </div>
                                        
                                </div> -->
                                <!-- <fieldset id="fiel" style="width:100%;border:0;">
                                    <button title="Agregar Servicios" type="button" id="add_boton" name="add" class="btn btn-warning btn-round btn-fab" onClick="AgregarSeviciosFacturacion2(this)">
                                        <i class="material-icons">add</i>
                                    </button><span style="color:#084B8A;"><b> SERVICIOS ADICIONALES</b></span>
                                    <div id="div<?=$index;?>">  
                                        <div class="h-divider">
                                        
                                        </div>
                                    </div>
                                    

                                </fieldset> -->
                                <!-- <div class="row">
                                    <label class="col-sm-5 col-form-label" style="color:#000000">Monto Total + Servicios Adicionales</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">                                            
                                            <input style="background:#ffffff" class="form-control"  name="monto_total" id="monto_total"  readonly="readonly" value="0" />-->
                                            <input  class="form-control" type="hidden" name="monto_total_a" id="monto_total_a"  readonly="readonly" value="0"  />
                                        <!--</div>
                                    </div>
                                </div> -->
                            </div>
                        </div>                 
                    </div>
                    <div class="card-footer ml-auto mr-auto">
                        <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
                        <?php                     

                            if(isset($_GET['q'])){?>
                                <a href='<?=$urlListSol?>&q=<?=$q?>&v=<?=$r?>&u=<?=$u?>&s=<?=$s?>' class="<?=$buttonCancel;?>" title="Ir a Solicitudes de Facturación"> IR A SF </a>
                                <!-- <a href='<?=$urlListFacturasServicios_costos_empresas?>&q=<?=$q?>&r=<?=$r?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a> -->
                                <?php }else{?>
                            
                                <a href='<?=$urlListSol?>' class="<?=$buttonCancel;?>" title="Ir a Solicitudes de Facturación"> IR A SF </a>                    
                                <!-- <a href='<?=$urlListFacturasServicios_costos_empresas?>&cod_empresa=<?=$cod_ee?>&glosa=<?=valor_glosa?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a> -->
                            <?php }                     
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
<?php  require_once 'simulaciones_servicios/modal_facturacion.php';?>
<script>
    $(document).ready(function() {
        var cod_facturacion=document.getElementById("cod_facturacion").value;
        if(cod_facturacion>0){        
            tablaGeneral_tipoPagos_solFac();
            tablaGeneral_areas_solFac();
            $("#nfac").html(itemTipoPagos_facturacion[0].length);
            $("#nfacAreas").html(itemAreas_facturacion[0].length);
            // tablaGeneral_areas_solFacNormas();
        }
    });
</script>
<!-- verifica que esté seleccionado al menos un item -->
<script type="text/javascript">
    function valida(f) {
        var ok = true;
        var msg = "El monto Total no debe ser '0' o 'negativo', Habilite los Items que desee facturar...\n";  
        if(f.elements["modal_totalmontoserv_costo"].value == 0 || f.elements["modal_totalmontoserv_costo"].value < 0 || f.elements["modal_totalmontoserv_costo"].value == '')
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