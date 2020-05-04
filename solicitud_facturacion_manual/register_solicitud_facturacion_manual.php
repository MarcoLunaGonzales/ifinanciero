<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';


$globalUser=$_SESSION["globalUser"];; 
//$dbh = new Conexion();
$dbh = new Conexion();
$cod_simulacion=0;
$cod_facturacion=0;
$cod_sw=0;
//sacamos datos para la facturacion
if(isset($_GET['q'])){
  $q=$_GET['q'];
}
if ($cod_facturacion > 0){
    // $stmt = $dbh->prepare("SELECT * FROM solicitudes_facturacion where codigo=$cod_facturacion");
    // $stmt->execute();
    // $result = $stmt->fetch();
    // $cod_uo = $result['cod_unidadorganizacional'];
    // $cod_area = $result['cod_area'];
    // $fecha_registro = $result['fecha_registro'];
    // $fecha_solicitudfactura = $result['fecha_solicitudfactura'];
    // $cod_tipoobjeto = $result['cod_tipoobjeto'];
    // $cod_tipopago = $result['cod_tipopago'];
    // $cod_cliente = $result['cod_cliente'];
    // $cod_personal = $result['cod_personal'];
    // $razon_social = $result['razon_social'];
    // $nit = $result['nit'];
    // $observaciones = $result['observaciones'];
    // $persona_contacto= $result['persona_contacto'];
    // // $anios_servicio = $resultServicio['anios'];
    // $nombre_simulacion = $resultServicio['nombre'];
    
    // $name_cliente=nameCliente($cod_cliente);
}else {
    $nombre_simulacion = null;
    $cod_uo = null; 
    $cod_area = null; 
    $cod_cliente =null;
    $cod_personal= $globalUser;     
    // $anios_servicio = $resultServicio['anios'];

    $id_tiposervicio = null;

    $fecha_registro =date('Y-m-d');
    $fecha_solicitudfactura =$fecha_registro;
    $cod_tipoobjeto=obtenerValorConfiguracion(34);//por defecto
    $cod_tipopago = null;
    $name_cliente=null;    
    $razon_social = $name_cliente;
    $nit = 0;
    $observaciones = null;
    $persona_contacto=null;
}
$name_uo=null;
$name_area=null;
$Codigo_alterno=null;
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
                <?php 
               if(isset($_GET['q'])){
                 ?><input type="hidden" name="id_ibnored" id="id_ibnored" value="<?=$q;?>"/><?php 
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
                      <h4 class="card-title"><?php if ($cod_simulacion == 0) echo "Registrar "; else echo "Editar ";?>Solicitud de Facturación</h4>                      
                    </div>
                    <h4 class="card-title" align="center"><b>Propuesta: Manual</b></h4>
                  </div>
                  <div class="card-body ">
                        <div class="row">
                          <label class="col-sm-2 col-form-label">Oficina</label>
                          <div class="col-sm-4">
                            <div class="form-group">
                                <!-- <input class="form-control" type="hidden" name="cod_uo" id="cod_uo" required="true" value="<?=$cod_uo;?>" required="true" readonly/>
                                 <input class="form-control" type="text" required="true" value="<?=$name_uo;?>" required="true" readonly style="background-color:#E3CEF6;text-align: left"/> -->

                                 <select name="cod_uo" id="cod_uo" onChange="ajaxAFunidadorganizacionalArea(this);" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">                                        
                                    <option value=""></option>
                                    <?php 
                                    $queryUO1 = "SELECT codigo,nombre,abreviatura from unidades_organizacionales where cod_estado=1 order by nombre";
                                    $statementUO1 = $dbh->query($queryUO1);
                                    while ($row = $statementUO1->fetch()){ ?>
                                        <option value="<?=$row["codigo"];?>" data-subtext="(<?=$row['codigo']?>)"><?=$row["abreviatura"];?> - <?=$row["nombre"];?></option>
                                    <?php } ?>
                                </select>
                               
                            </div>
                          </div>
                          <label class="col-sm-2 col-form-label">Area</label>
                            <div class="col-sm-4">
                                <div class="form-group" >
                                    <!-- <div id="div_contenedor_area_tcc"> -->
                                    <div id="div_contenedor_area">
                                        <!-- <input class="form-control" type="hidden" name="cod_area" id="cod_area" required="true" value="<?=$cod_area;?>" required="true" readonly/>

                                        <input class="form-control" type="text" required="true" value="<?=$name_area;?>" required="true" readonly style="background-color:#E3CEF6;text-align: left"/> -->
                                       
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
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Fecha a<br>Facturar</label>
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
                            
                            <label class="col-sm-2 col-form-label">Tipo Pago</label>
                            <div class="col-sm-4">
                                <div class="form-group" >
                                        <select name="cod_tipopago" id="cod_tipopago" class="selectpicker form-control form-control-sm" data-style="btn btn-info">
                                            <!-- <option value=""></option> -->
                                            <?php 
                                            $queryTipoPago = "SELECT codigo,nombre FROM  tipos_pago WHERE cod_estadoreferencial=1 order by nombre";
                                            $statementPAgo = $dbh->query($queryTipoPago);
                                            while ($row = $statementPAgo->fetch()){ ?>
                                                <option <?=($cod_tipopago==$row["codigo"])?"selected":"";?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>                                
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
                                    <select name="cod_cliente" id="cod_cliente" class="selectpicker form-control form-control-sm" data-style="btn btn-info"  required="true" onChange="ajaxClienteContacto(this);">
                                        <option value=""></option>
                                        <?php 
                                        $queryTipoObjeto = "SELECT * from clientes where cod_estadoreferencial=1 order by nombre";
                                        $statementObjeto = $dbh->query($queryTipoObjeto);
                                        while ($row = $statementObjeto->fetch()){ ?>
                                            <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                        <?php } ?>
                                    </select>  
                                        
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Persona Contacto</label>
                            <div class="col-sm-3">
                                <div class="form-group" >
                                    <div id="div_contenedor_contactos">
                                        <select class="selectpicker form-control form-control-sm" name="persona_contacto" id="persona_contacto" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" title="Seleccione Contacto" required="true">
                                          <option value=""></option>
                                          <?php 
                                          $query="SELECT * FROM clientes_contactos where cod_cliente=$cod_cliente order by nombre";
                                          $stmt = $dbh->prepare($query);
                                          $stmt->execute();
                                          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $codigo=$row['codigo'];    
                                            $nombre_conatacto=$row['nombre']." ".$row['paterno']." ".$row['materno'];
                                            ?><option value="<?=$codigo?>" class="text-right"><?=$nombre_conatacto?></option>
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
                                       <i class="material-icons" title="Actualizar Contacto">update</i>
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
                                <label class="col-sm-2 col-form-label">Nit</label>
                                <div class="col-sm-4">
                                    <div class="form-group">                                        
                                            <input class="form-control" type="number" name="nit" id="nit" required="true" value="<?=$nit;?>" required="true"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- fin razon social y nit -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Observaciones</label>
                            <div class="col-sm-7">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="observaciones" id="observaciones"  value="<?=$observaciones;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
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
                               
                                <fieldset id="fiel" style="width:100%;border:0;">
                                    <button title="Agregar Servicios" type="button" id="add_boton" name="add" class="btn btn-warning btn-round btn-fab" onClick="AgregarSeviciosFacturacion_soli_manual(this)">
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
                                    <label class="col-sm-5 col-form-label" style="color:#000000">Monto Total <!-- + Servicios Adicionales --></label>
                                    <div class="col-sm-4">
                                        <div class="form-group">                                            
                                            <input style="background:#ffffff" class="form-control"  name="monto_total" id="monto_total"  readonly="readonly" value="0" step="0.01" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                    
                  </div>
                  <div class="card-footer ml-auto mr-auto">
                    <button type="submit" class="<?=$buttonNormal;?>">Guardar</button><?php
                    if(isset($_GET['q'])){
                    if($cod_sw==1){?>
                        <a href='<?=$urlSolicitudfactura;?>&cod=<?=$cod_simulacion;?>&q=<?=$q?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
                    <?php }else{?>
                        <a href='<?=$urlListSimulacionesServ?>&q=<?=$q?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
                    <?php }
                    }else{
                      if($cod_sw==1){?>
                        <a href='<?=$urlSolicitudfactura;?>&cod=<?=$cod_simulacion;?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
                    <?php }else{?>
                        <a href='<?=$urlListSimulacionesServ?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
                    <?php }   
                    }

                    ?>
                    
                  </div>
                </div>
              </form>                  
            </div>
        </div>
    </div>
</div>
<!-- small modal -->
<div class="modal fade modal-primary" id="modalAgregarServicioFacturacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card">
           <div class="card-header card-header-success card-header-text">
              <div class="card-text">
                <h4>Agregar Servicio</h4>
              </div>
              <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                <i class="material-icons">close</i>
              </button>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-condensed table-striped table-sm">
                    <thead>
                        <tr class="fondo-boton">
                            <td width="30%">Descripci&oacute;n</td>
                            <td>Cantidad</td>                            
                            <td>Importe</td>                            
                            <td>Action</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-plomo">                        
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
                            <td class="text-right">
                               <input type="number" id="modal_montoserv" name="modal_montoserv" class="form-control text-primary text-right"  value="0" step="0.01">
                            </td>
                            
                          <td>
                            <div class="btn-group">                            
                               <button id="add_boton" name="add" class="btn btn-primary btn-sm" onClick="agregarNuevoServicioSimulacion2(this); return false;">
                                 Agregar
                               </button>
                             </div>
                          </td>
                        </tr>
                    </tbody>
               </table>
                  
                  
                <!-- <hr>
                <div class="form-group float-right">
                    <button type="button" id="boton_guardarsim" class="btn btn-default" onclick="guardarDatosSimulacion(this.id)">Guardar</button>
                </div>  -->
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