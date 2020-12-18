<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../conexion_externa.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();
$dbhIBNO = new ConexionIBNORCA();
$globalUser=$_SESSION["globalUser"];

set_time_limit(0);
//recibimos las normas seleccionadas a facturar
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $r=$_GET['r'];  
  $s=$_GET['s']; 
  $u=$_GET['u']; 
}
if(isset($_POST['q'])){
  $q=$_POST['q'];
  $r=$_POST['r'];  
  $s=$_POST['s'];  
  $u=$_POST['u'];  
}
if(isset($_GET['cod_sw'])){//para la parte de editar
    $stringCi=$_GET['codigo_ci'];
    $stringCurso=$_GET['IdCurso'];
    $cod_facturacion=$_GET['cod_facturacion'];
}else{
    $total_items = $_POST["total_items"];
    $cod_simulacion=0;
    $cod_facturacion=0; 
    //recibimos datos 
    $array_ci= array();
    $array_curso= array();    
    for ($i=1;$i<=$total_items-1;$i++){
        if($_POST["CiAlumno_a".$i]!=''){
            $ci_alumno=$_POST["CiAlumno_a".$i];
            $id_curso=$_POST["IdCurso_a".$i];            
            $array_ci[$i-1]=trim($ci_alumno," ");
            $array_curso[$i-1]=$id_curso;
        }    
    }    
    $stringCi=implode("','", $array_ci);
    $stringCi="'".trim($stringCi,"','")."'";
    $stringCurso=implode(",", $array_curso);
    $stringCurso=trim($stringCurso,',');
}

//consulta para oficina y area desde itranet
if(isset($_GET['q']) || isset($_POST['q'])){
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
            $sqlUO_x="and codigo=0";    
        }else{
            $sqlUO_x="and codigo ".$codigoUO;  
        }
    }
}else{
    $sqlUO_x="";
    $sqlAreas_x="";
}


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
    $razon_social = $result['razon_social'];
    $nit = $result['nit'];
    $observaciones = $result['observaciones'];
    $observaciones_2 = $result['observaciones_2'];
    $persona_contacto= $result['persona_contacto'];
    $Codigo_alterno=$result['codigo_alterno'];
    $dias_credito=$result['dias_credito'];
    $correo_contacto=$result['correo_contacto'];
}else {
    $globalUnidad=$_SESSION['globalUnidad'];
    $cod_area=13;
    $cod_uo=$globalUnidad;    
    $cod_cliente=null;    
    if(isset($_POST['q'])){
        $cod_personal=$_POST['q'];
    }else{
        $cod_personal = $globalUser;
    }
    $fecha_registro =date('Y-m-d');
    $fecha_solicitudfactura=$fecha_registro;
    $cod_tipoobjeto=212;//por defecto}
    $name_tipoPago=obtenerNombreTipoPago($cod_tipoobjeto);
    $cod_tipopago = null;    
    $razon_social = null;
    $nit=null;
    $observaciones = null;
    $observaciones_2 = null;
    $persona_contacto=null;
    $dias_credito=obtenerValorConfiguracion(58);
    
    $Codigo_alterno=obtenerCodigoExternoCurso($id_curso);
    $correo_contacto="";
}

$cod_defecto_deposito_cuenta=obtenerValorConfiguracion(55);
$cod_defecto_cod_tipo_credito=obtenerValorConfiguracion(48);
$name_area=null;
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
            <form id="formSoliFactNormas" class="form-horizontal" action="<?=$urlSave_solicitud_facturacion_costos_grupal_estudiantes;?>" method="post" onsubmit="return valida(this)" enctype="multipart/form-data">
                <?php                   
                if(isset($_POST['q']) || isset($_GET['q'])){?>
                    <input type="hidden" name="q" id="q" value="<?=$q?>">
                    <input type="hidden" name="r" id="r" value="<?=$r?>">
                    <input type="hidden" name="s" id="s" value="<?=$s?>">
                    <input type="hidden" name="u" id="u" value="<?=$u?>">
                <?php }?>
                <input type="hidden" name="cod_defecto_deposito_cuenta" id="cod_defecto_deposito_cuenta" value="<?=$cod_defecto_deposito_cuenta?>"/>
                <input type="hidden" name="cod_defecto_cod_tipo_credito" id="cod_defecto_cod_tipo_credito" value="<?=$cod_defecto_cod_tipo_credito?>"/>
                
                <input type="hidden" name="Codigo_alterno" id="Codigo_alterno" value="<?=$Codigo_alterno;?>"/>  
                <input type="hidden" name="ci_estudiante" id="ci_estudiante" value="<?=$stringCi;?>"/>
                <input type="hidden" name="IdCurso" id="IdCurso" value="<?=$stringCurso;?>">

                <input type="hidden" name="cod_simulacion" id="cod_simulacion" value="<?=$cod_simulacion;?>"/>
                <input type="hidden" name="cod_facturacion" id="cod_facturacion" value="<?=$cod_facturacion;?>"/>
                <input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">
                <input type="hidden" name="tipo_solicitud" id="tipo_solicitud" value="2">
                <input type="hidden" name="tipo_aux" id="tipo_aux" value="2"><!-- //nos indica de donde va para editar adjuntos -->
                

                <div class="card">
                    <div class="card-header <?=$colorCard;?> card-header-text">
                        <div class="card-text">
                          <h4 class="card-title"><?php if ($cod_facturacion == 0) echo "Registrar "; else echo "Editar ";?>Solicitud de Facturación</h4>
                        </div>
                        <h4 class="card-title" align="center"><b>Nombre Curso : Grupal / <?=$Codigo_alterno?></b></h4>
                    </div>
                    <div class="card-body ">    
                        <div class="row">
                          <label class="col-sm-2 col-form-label">Oficina</label>
                          <div class="col-sm-4">
                            <div class="form-group">                                
                                <select name="cod_uo" id="cod_uo"  class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">                                        
                                    <option value=""></option>
                                    <?php 
                                    $queryUO1 = "SELECT codigo,nombre,abreviatura from unidades_organizacionales where cod_estado=1 and centro_costos=1 $sqlUO_x order by nombre";
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
                                            <?php 
                                            $sqlArea="SELECT codigo,nombre,abreviatura from areas where cod_estado=1 and centro_costos=1 $sqlAreas_x order by nombre";

                                            $stmtArea = $dbh->prepare($sqlArea);
                                            $stmtArea->execute();
                                            while ($rowArea = $stmtArea->fetch()){ ?>
                                                 <option <?=($cod_area==$rowArea["codigo"])?"selected":"disabled";?> value="<?=$rowArea["codigo"];?>" data-subtext="(<?=$rowArea['codigo']?>)"><?=$rowArea["abreviatura"];?> - <?=$rowArea["nombre"];?></option><?php 
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

                                    <input class="form-control" type="hidden" name="cod_tipoobjeto" id="cod_tipoobjeto" required="true" value="<?=$cod_tipoobjeto;?>" required="true" readonly/>

                                    <input class="form-control" type="text" required="true" value="<?=$name_tipoPago;?>" required="true" readonly style="background-color:#E3CEF6;text-align: left"/>
                                </div>
                            </div> 
                        </div>
                        <!-- fin fechas -->                        
                        <div class="row" >                            
                            <script>
                                var nfac=[];itemTipoPagos_facturacion.push(nfac);
                                var nfacAreas=[];itemAreas_facturacion.push(nfacAreas);

                            </script>
                            <!-- creamos los objetos de las areas de servicios -->
                            <div class="">
                                <?php 
                                    // añadimos los porcetnajes de distribucion tanto para areas y formas de pago 
                                    require_once '../simulaciones_servicios/objeto_formaspago_areas.php';
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
                                     //unidades
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
                                    <select name="cod_tipopago" id="cod_tipopago" class="selectpicker form-control form-control-sm" data-style="btn btn-info" onChange="ajaxTipoPagoContactoPersonal_normas(this);">
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
                                     <button type="button" class="btn btn-danger btn-round btn-fab btn-sm" data-toggle="modal" data-target="" onclick="agregarDatosModalTipoPagoFacturacionNormas(2)">
                                        <i class="material-icons" title="Forma de Pago Porcentaje">list</i>
                                        <span id="nfac" class="count bg-warning"></span>
                                     </button>
                                     
                                     <button type="button" class="btn btn-primary btn-round btn-fab btn-sm" data-toggle="modal" data-target="" onclick="agregarDatosModalAreasFacturacionNormas(2)">
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
                        <!-- fin tipos pago y objeto                 -->
                        <div class="row dias_credito_x" id="" style="display: none">                            
                            <label class="col-sm-2 col-form-label">Días de Crédito</label>
                            <div class="col-sm-2">
                                <div class="form-group">                                
                                    <input type="number" class="form-control" name="dias_credito" id="dias_credito" value="<?=$dias_credito?>">
                                </div>
                            </div>                            
                        </div>
                        
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Cliente</label>
                            <div class="col-sm-4">
                                <div class="form-group" >                                                            
                                    <select name="cod_cliente" id="cod_cliente" class="selectpicker form-control form-control-sm" data-style="btn btn-info"  required="true" onChange="ajaxClienteContactoNormas(this);" data-live-search="true" >
                                        <option value=""></option>
                                        <?php 
                                        $queryTipoObjeto = "SELECT * from clientes where cod_estadoreferencial=1 order by nombre";
                                        $statementObjeto = $dbh->query($queryTipoObjeto);
                                        while ($row = $statementObjeto->fetch()){ ?>
                                            <option <?=($cod_cliente==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
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
                                          $query="SELECT * FROM clientes_contactos where cod_cliente=$cod_cliente order by nombre";
                                          $stmt = $dbh->prepare($query);
                                          $stmt->execute();
                                          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $codigo_contacto=$row['codigo'];    
                                            $nombre_conatacto=$row['nombre']." ".$row['paterno']." ".$row['materno'];
                                            ?><option <?=($persona_contacto==$codigo_contacto)?"selected":"";?> value="<?=$codigo_contacto?>" class="text-right"><?=$nombre_conatacto?></option>
                                           <?php 
                                           } ?> 
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="form-group" >                                        
                                    <a href="#" class="btn btn-warning btn-round btn-fab btn-sm" onclick="cargarDatosRegistroContactoNormas()">
                                        <i class="material-icons" title="Add Contacto">add</i>
                                    </a>
                                    <a href="#" class="btn btn-success btn-round btn-fab btn-sm" onclick="actualizarRegistroContactoNormas()">
                                       <i class="material-icons" title="Actualizar Clientes & Contactos">update</i>
                                    </a> 
                                </div>
                            </div>                    
                        </div>
                        <!-- fin cliente  -->

                        <div id="contenedor_razon_nit">
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Razón Social</label>
                                <div class="col-sm-5">
                                    <div class="form-group">                                    
                                        <input class="form-control" type="text" name="razon_social" id="razon_social" required="true" value="<?=$razon_social;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>                                        
                                    </div>
                                </div>
                                <label class="col-sm-1 col-form-label">Nit</label>
                                <div class="col-sm-4">
                                    <div class="form-group">                                        
                                            <input class="form-control" type="number" name="nit" id="nit" required="true" value="<?=$nit;?>" required="true"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Correo De Contacto <br>Para Envío De Factura.</label>
                                <div class="col-sm-10">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="correo_contacto" id="correo_contacto" value="<?=$correo_contacto;?>" required />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- fin razon social y nit -->
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
                                    <!-- <input class="form-control" type="text" name="observaciones_2" id="observaciones_2"  value="<?=$observaciones_2;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/> -->
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
                            <!-- <button type="button" onclick="AgregarSeviciosFacturacion()" class="btn btn-success btn-sm btn-fab float-right">
                                 <i class="material-icons" title="Registrar Servicios">edit</i>
                            </button> -->

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
                                        // if($cod_facturacion==0){
                                            $queryPr="SELECT aa.IdModulo, aa.IdCurso, aa.CiAlumno, concat(cpe.clPaterno,' ',cpe.clMaterno,' ',cpe.clNombreRazon)as nombreAlumno, concat(cpe.clNombreRazon,' ',cpe.clPaterno,' ',cpe.clMaterno)as razonsocial, c.Abrev, c.Auxiliar,
                                            pc.Costo, pc.CantidadModulos, m.NroModulo, pc.Nombre, m.IdTema
                                            FROM asignacionalumno aa, dbcliente.cliente_persona_empresa cpe, alumnocurso ac, clasificador c, programas_cursos pc, modulos m 
                                            where cpe.clIdentificacion=aa.CiAlumno 
                                            and ac.IdCurso=aa.IdCurso and ac.CiAlumno=aa.CiAlumno and ac.IdConceptoPago=c.IdClasificador and pc.IdCurso=aa.IdCurso and 
                                            m.IdCurso=pc.IdCurso and m.IdModulo=aa.IdModulo and cpe.clIdentificacion in ($stringCi) and aa.IdCurso in ($stringCurso) ";
                                           // echo $queryPr;
                                            $stmt = $dbhIBNO->prepare($queryPr);
                                            $stmt->execute();
                                        // }else{
                                        //     $queryPr="SELECT d.codigo,d.cod_claservicio,d.descripcion_alterna,d.cantidad,d.precio from solicitudes_facturaciondetalle d where d.tipo_item=1 and d.cod_solicitudfacturacion=$cod_facturacion ORDER BY d.codigo";
                                        //     echo $queryPr;
                                        //      $stmt = $dbh->prepare($queryPr);
                                        //     $stmt->execute();
                                        // }
                                        $modal_totalmontopre=0;$modal_totalmontopretotal=0;
                                        $sw="";
                                        $sw2="";//para registrar nuevos, impedir los ya registrados
                                        while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {                                            
                                                $IdCurso=$rowPre['IdCurso'];
                                                $ci_estudiante=$rowPre['CiAlumno'];
                                                // $nombreAlumno=$rowPre['nombreAlumno'];
                                                $nombreAlumno=obtnerNombreComprimidoEstudiante($ci_estudiante);
                                                $Codigo_alterno=obtenerCodigoExternoCurso($IdCurso);
                                                $codigoPre=$rowPre['IdTema'];
                                                //$codCS=430;//defecto
                                                $codCS=$rowPre['IdModulo'];//guardaremos el id de curso en ves de servicio..
                                                $NroModulo=$rowPre['NroModulo'];
                                                $tipoPre=$Codigo_alterno." - Mod:".$NroModulo." - ".$rowPre['Nombre']." - ".$nombreAlumno;
                                                $CantidadModulos=$rowPre['CantidadModulos'];
                                                $cantidadPre=$CantidadModulos;
                                                $Costo=$rowPre['Costo']/$CantidadModulos;
                                                $Costo = number_format($Costo, 2, '.', '');
                                                $montoPre=$Costo;
                                                $cantidadPre=1;
                                                $Abrev=trim($rowPre['Abrev'],'%');
                                                $descuento_cliente=trim($rowPre['Abrev'],'%');
                                                $descuento_bob_cliente=($Costo*$descuento_cliente/100);
                                                $descripcion_alternaX=$tipoPre;
                                                $monto_pagar=($Costo - ($Costo*$Abrev)/100); //formula para sacar el monto a pagar del estudiante  
                                                $saldo=$monto_pagar;
                                                $banderaHab=1;
                                                $controlador_auxiliar=0;                                            
                                            if($banderaHab!=0){
                                                $descuento_porX=$descuento_cliente;
                                                $descuento_bobX=$descuento_bob_cliente;
                                                // $descripcion_alternaX=$tipoPre;
                                                // $modal_totalmontopre+=$montoPre;                                                
                                                //parte del controlador de check
                                                //para la parte de editar
                                                $sw="";
                                                if($cod_facturacion>0){
                                                    //$sqlControlador="SELECT sfd.precio,sfd.descuento_por,sfd.descuento_bob,sfd.descripcion_alterna from solicitudes_facturacion sf,solicitudes_facturaciondetalle sfd, solicitudes_facturacion_grupal sfg where sf.codigo=sfg.cod_solicitudfacturacion and  sf.codigo=sfd.cod_solicitudfacturacion and sfd.cod_claservicio=$codCS and sf.codigo=$cod_facturacion and sfg.cod_curso=$IdCurso and tipo_solicitud in (2,7)";
                                                    $sqlControlador="SELECT sfd.precio,sfd.descuento_por,sfd.descuento_bob,sfd.descripcion_alterna from solicitudes_facturacion sf,solicitudes_facturaciondetalle sfd where sf.codigo=sfd.cod_solicitudfacturacion and sfd.cod_claservicio=$codCS and sf.codigo=$cod_facturacion and sfd.cod_curso=$IdCurso and sf.tipo_solicitud in (2,7) and sfd.ci_estudiante = '$ci_estudiante'";
                                                    // echo $sqlControlador;
                                                    $stmtControlado = $dbh->prepare($sqlControlador);
                                                   $stmtControlado->execute();                                                   
                                                    while ($rowPre = $stmtControlado->fetch(PDO::FETCH_ASSOC)) {
                                                        $sw="checked";
                                                        $montoPre=$rowPre['precio']+$rowPre['descuento_bob'];
                                                        $preciox=$rowPre['precio'];
                                                        // $monto_pagadoX=$rowPre['monto_pagado'];
                                                        $descuento_porX=$rowPre['descuento_por'];
                                                        $descuento_bobX=$rowPre['descuento_bob'];
                                                        $descripcion_alternaX=$rowPre['descripcion_alterna'];
                                                    }    
                                                }
                                                $sw2="";//para registrar nuevos, impedir los ya registrados
                                                $codigo_externo="";
                                                $montoPagado=0;
                                                $estadoPagado=0;
                                                $cod_modulo=0;
                                                $monto_total_pagado=0;
                                                $lista=verifica_pago_curso($IdCurso,$ci_estudiante);
                                                // var_dump($lista);
                                                if($lista){
                                                    $estado_ws=true;                                                    
                                                    foreach ($lista->lstModulos as $listas) {
                                                        $cod_modulo=$listas->IdModulo;
                                                        $estadoPagado=$listas->EstadoPagado;
                                                        if($cod_modulo==$codCS){
                                                            if($estadoPagado==1){
                                                                $sw2="readonly style='background-color:#cec6d6;'";              
                                                            }
                                                            $codigo_externo=$listas->Codigo;
                                                            $montoPagado=$listas->MontoPagado;
                                                            $monto_total_pagado=$listas->MontoPagado;
                                                            // $monto_x_pagar=$listas->MontoXPagar;
                                                            // echo $monto_total_pagado."---";
                                                            $monto_total_pagado2=$monto_total_pagado;
                                                            $saldo=$listas->Saldo;
                                                            $montoPagado = number_format($montoPagado, 2, '.', '');
                                                            $monto_total_pagado = number_format($monto_total_pagado, 2, '.', '');
                                                            $saldo = number_format($saldo, 2, '.', '');
                                                            break;
                                                        }
                                                    }
                                                    if($estadoPagado!=1){
                                                        // if($cod_facturacion==0){
                                                            //parte del controlador de check//impedir los ya registrados
                                                            $sqlControlador2="SELECT sfd.cod_solicitudfacturacion,sfd.precio,sfd.descuento_por,sfd.descuento_bob,sfd.descripcion_alterna from solicitudes_facturacion sf,solicitudes_facturaciondetalle sfd where sf.codigo=sfd.cod_solicitudfacturacion and sf.cod_simulacion_servicio=$IdCurso and sfd.cod_claservicio=$codCS and sf.ci_estudiante = '$ci_estudiante' and tipo_solicitud in (2,7) and sf.cod_estadosolicitudfacturacion!=2";
                                                             // echo $sqlControlador2;
                                                            $stmtControlador2 = $dbh->prepare($sqlControlador2);
                                                            $stmtControlador2->execute();
                                                            //sacamos el monto total
                                                            $sqlControladorTotal="SELECT SUM(sfd.precio) as precio from solicitudes_facturacion sf,solicitudes_facturaciondetalle sfd where sf.codigo=sfd.cod_solicitudfacturacion and sf.cod_simulacion_servicio=$IdCurso and sfd.cod_claservicio=$codCS and sf.ci_estudiante = '$ci_estudiante' and tipo_solicitud in (2,7) and sf.cod_estadosolicitudfacturacion!=2";
                                                             // echo $sqlControladorTotal;
                                                            $stmtControladorTotal = $dbh->prepare($sqlControladorTotal);
                                                            $stmtControladorTotal->execute();
                                                            $resultMontoTotal=$stmtControladorTotal->fetch();                                                            
                                                            $precio_total_x=$resultMontoTotal['precio'] + $monto_total_pagado;//sumamos el monto de la solicutd y el servicio
                                                            //consulta para los items registrados grupales
                                                            $precio_total_x_grupal=obtenermontoestudianteGrupal($IdCurso,$ci_estudiante,$codCS);
                                                            $precio_total_x=$precio_total_x+$precio_total_x_grupal;
                                                            if($precio_total_x>0){
                                                                $saldo=$monto_pagar-$precio_total_x;
                                                            }

                                                            if($precio_total_x==null || $precio_total_x=='' || $precio_total_x==' ' || $precio_total_x==0){
                                                            }else $monto_total_pagado=$precio_total_x;
                                                            // echo "monto_total_pagado:".$monto_total_pagado;
                                                            $cont_items_aux=0;
                                                            while ($rowPre = $stmtControlador2->fetch(PDO::FETCH_ASSOC)) {
                                                                $cod_solicitudfacturacion_x=$rowPre['cod_solicitudfacturacion'];
                                                                $cont_items_aux++;
                                                                if($sw!="checked"){
                                                                    if($monto_pagar==$monto_total_pagado){
                                                                        $controlador_auxiliar=1;
                                                                        $sw2="readonly style='background-color:#cec6d6;'";
                                                                        $saldo=0;
                                                                    }
                                                                    if($rowPre['descuento_bob']==null || $rowPre['descuento_bob']==0 || $rowPre['descuento_bob']=='' || $rowPre['descuento_bob']==' '){
                                                                    }else{
                                                                        // $monto_total_pagado-=$rowPre['descuento_bob'];
                                                                        // echo $monto_pagar."-".$monto_total_pagado;
                                                                        $saldo=$monto_pagar-$monto_total_pagado;
                                                                    }
                                                                    // $montoPre=$rowPre['precio']+$rowPre['descuento_bob'];
                                                                    $descuento_porX=$rowPre['descuento_por'];
                                                                    $descuento_bobX=$rowPre['descuento_bob'];
                                                                    $descripcion_alternaX=$rowPre['descripcion_alterna'];
                                                                }else{//si el item  es para  editar
                                                                    // $monto_total_pagado=$monto_total_pagado2;
                                                                    $monto_total_pagado=$precio_total_x-$preciox;
                                                                    $saldo=$preciox;
                                                                }
                                                            }
                                                            if($cont_items_aux==0){
                                                                if($sw!="checked"){
                                                                    if($monto_pagar==$monto_total_pagado){
                                                                        $sw2="readonly style='background-color:#cec6d6;'";
                                                                        $controlador_auxiliar=1;
                                                                        $cod_solicitudfacturacion_x=obtnerCodigoSFGrupal($IdCurso,$ci_estudiante,$codCS);
                                                                    }    
                                                                }else{
                                                                    $monto_total_pagado=$precio_total_x-$preciox;
                                                                    $saldo=$preciox;
                                                                }
                                                                
                                                                // $descripcion_alternaX=obtenerDescripcionestudianteGrupal($IdCurso,$ci_estudiante,$codCS);
                                                            }
                                                        // }else{
                                                        //     $monto_total_pagado=$monto_pagadoX;
                                                        // }
                                                        
                                                    }
                                                }else{           
                                                    ?>
                                                    <script>$("#div_mensaje_ws").removeClass('d-none');</script>
                                                    <?php
                                                    $estado_ws=false;
                                                    // break;
                                                }

                                                // $monto_pagar=number_format($monto_pagar,2,".","");
                                                ?>
                                                <!-- guardamos las varialbles en un input -->
                                                <input type="hidden" id="cod_serv_tiposerv<?=$iii?>" name="cod_serv_tiposerv<?=$iii?>" value="<?=$codigoPre?>">
                                                <input type="hidden" id="servicio<?=$iii?>" name="servicio<?=$iii?>" value="<?=$codCS?>">
                                                <input type="hidden" id="nombre_servicio<?=$iii?>" name="nombre_servicio<?=$iii?>" value="<?=$tipoPre?>">
                                                <input type="hidden" id="cantidad<?=$iii?>" name="cantidad<?=$iii?>" value="1">
                                                <input type="hidden" id="importe<?=$iii?>" name="importe<?=$iii?>" value="<?=$Costo?>">

                                                <input type="hidden" id="cod_curso_x<?=$iii?>" name="cod_curso_x<?=$iii?>" value="<?=$IdCurso?>">
                                                <input type="hidden" id="ci_estudiante<?=$iii?>" name="ci_estudiante<?=$iii?>" value="<?=$ci_estudiante?>">

                                                <!-- aqui se captura los servicios activados -->
                                                <input type="hidden" id="cod_serv_tiposerv_a<?=$iii?>" name="cod_serv_tiposerv_a<?=$iii?>">
                                                <input type="hidden" id="servicio_a<?=$iii?>" name="servicio_a<?=$iii?>">
                                                <input type="hidden" id="cantidad_a<?=$iii?>" name="cantidad_a<?=$iii?>">
                                                <input type="hidden" id="importe_a<?=$iii?>" name="importe_a<?=$iii?>">
                                                <tr>
                                                  
                                                    <td class="text-left" width="35%"><textarea name="descripcion_alterna<?=$iii?>" id="descripcion_alterna<?=$iii?>" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();" <?=$sw2?>><?=$descripcion_alternaX?></textarea></td>
                                                    <td class="text-right"><?=$cantidadPre?></td>
                                                    <td class="text-right"><input type="hidden" step="0.01" id="monto_precio<?=$iii?>" name="monto_precio<?=$iii?>" class="form-control text-primary text-right"  value="<?=$Costo?>" step="0.01" <?=$sw2?> readonly="true"><input type="text" step="0.01" id="monto_precio_a<?=$iii?>" name="monto_precio_a<?=$iii?>" class="form-control text-primary text-right"  value="<?=number_format($Costo,2)?>" <?=$sw2?> readonly="true"></td>
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
                                                            <a rel="tooltip" href='../<?=$urlPrintSolicitud;?>?codigo=<?=$cod_solicitudfacturacion_x;?>' target="_blank"><i class="material-icons text-primary" title="Imprimir Solicitud Facturación">print</i></a>
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

                                <!-- <fieldset id="fiel" style="width:100%;border:0;">
                                    <button title="Agregar Servicios" type="button" id="add_boton" name="add" class="btn btn-warning btn-round btn-fab" onClick="AgregarSeviciosFacturacion2(this)">
                                        <i class="material-icons">add</i>
                                    </button><span style="color:#084B8A;"><b> SERVICIOS ADICIONALES</b></span>
                                    
                                        <div class="h-divider">
                                        
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
                  <div class="card-footer fixed-bottom">
                    <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
                        <?php                                    
                            if(isset($_POST['q'])){?>
                                <a href='../<?=$urlListSol?>&q=<?=$q?>&v=<?=$r?>&u=<?=$u?>&s=<?=$s?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> IR A SF</a>                    
                            <?php }else{
                                if(isset($_GET['q'])){?>
                                <a href='../<?=$urlListSol?>&q=<?=$q?>&v=<?=$r?>&u=<?=$u?>&s=<?=$s?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> IR A SF</a>                    
                                <?php }else{?>
                                    <a href='../<?=$urlListSol?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> IR A SF</a>                    
                                <?php }                         
                            }
                            
                        ?>                         
                  </div>
                </div>
                <?php  require_once '../simulaciones_servicios/modal_subir_archivos.php';?>
            </form>                  
            </div>
        </div>
    </div>
</div>


<!-- carga de proveedores -->
<!-- <div class="cargar">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div> -->

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
                        <button type="button" onclick="guardarDatoscontactoNormas()" class="btn btn-info btn-round">Agregar</button>
                </div>
          </div>
      </div>  
    </div>
</div>
<?php  require_once '../simulaciones_servicios/modal_facturacion.php';?>
<script>$('.selectpicker').selectpicker("refresh");</script>
<script>
    $(document).ready(function() {
        var cod_facturacion=document.getElementById("cod_facturacion").value;
        if(cod_facturacion>0){        
            tablaGeneral_tipoPagos_solFac();
            // tablaGeneral_areas_solFac();
            tablaGeneral_areas_solFacNormas();
            $("#nfac").html(itemTipoPagos_facturacion[0].length);
            $("#nfacAreas").html(itemAreas_facturacion[0].length);
        }
    });
</script>
<script type="text/javascript">
    function valida(f) {
        var ok = true;
        var msg = "El monto Total no debe ser '0' o 'negativo', Habilite los Items que desee facturar...\n";    
        if(f.elements["modal_totalmontoserv_costo_a"].value == 0 || f.elements["modal_totalmontoserv_costo_a"].value < 0 || f.elements["modal_totalmontoserv_costo_a"].value == '')
        {  
            ok = false;
        }
        // if(f.elements["monto_total"].value>0)
        // {    
        //     ok = true;
        // }    
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