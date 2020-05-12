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


$cod_simulacion=0;
$cod_facturacion=null;
$cod_uo=null;
$cod_area=null;
$cod_cliente=null;
$globalUser=$_SESSION["globalUser"];
$cod_personal=$globalUser;
$fecha_registro =date('Y-m-d');
$fecha_solicitudfactura =$fecha_registro;
$cod_tipoobjeto=obtenerValorConfiguracion(34);//por defecto
$cod_tipopago = null;
$name_cliente=null;
$razon_social = $name_cliente;
$nit=null;
$observaciones = null;
$observaciones_2 = null;
$persona_contacto=null;
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
              <form id="formSoliFactTcp" class="form-horizontal" action="<?=$urlSaveSolicitudfactura_normas;?>" method="post" onsubmit="return valida(this)">
                <?php 
      if(isset($_GET['q'])){
        ?><input type="hidden" name="usuario_ibnored" id="usuario_ibnored" value="<?=$q;?>">
        <input type="hidden" name="usuario_ibnored_s" id="usuario_ibnored_s" value="<?=$s;?>">
        <input type="hidden" name="usuario_ibnored_u" id="usuario_ibnored_u" value="<?=$u;?>">
        <input type="hidden" name="usuario_ibnored_v" id="usuario_ibnored_v" value="<?=$v;?>"><?php
      }
      ?> 
                
                
                <input type="hidden" name="cod_simulacion" id="cod_simulacion" value="<?=$cod_simulacion;?>"/>
                <input type="hidden" name="cod_facturacion" id="cod_facturacion" value="<?=$cod_facturacion;?>"/>
                <input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">                

                <div class="card">
                  <div class="card-header <?=$colorCard;?> card-header-text">
                    <div class="card-text">
                      <h4 class="card-title"><?php if ($cod_simulacion == 0) echo "Registrar "; else echo "Editar ";?>Solicitud de Facturación</h4>                      
                    </div>
                    <h4 class="card-title" align="center"><b>Venta de Normas</b></h4>
                  </div>
                  <div class="card-body ">
                        <div class="row">
                          <label class="col-sm-2 col-form-label">Oficina</label>
                          <div class="col-sm-4">
                            <div class="form-group">                               
                                 <select name="cod_uo" id="cod_uo" onChange="ajaxUnidadorganizacionalAreaNormas(this);" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">                                        
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
                                    <div id="div_contenedor_area">                                        
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
                                                <!-- <?php 
                                                $queryTipoObjeto = "SELECT codigo,nombre FROM  tipos_objetofacturacion WHERE cod_estadoreferencial=1 order by nombre";
                                                $statementObjeto = $dbh->query($queryTipoObjeto);
                                                while ($row = $statementObjeto->fetch()){ ?>
                                                    <option <?=($cod_tipoobjeto==$row["codigo"])?"selected":"";?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                                <?php } ?> -->
                                            </select>                                
                                    </div>
                                </div>    
                            </div>
                            <script>var nfac=[];itemTipoPagos_facturacion.push(nfac);var nfacAreas=[];itemAreas_facturacion.push(nfacAreas);</script>
                            <!-- creamos los objetos de las areas de servicios -->
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
                                    <!-- <button type="button" class="btn btn-danger btn-round btn-fab btn-sm" data-toggle="modal" data-target="" onclick="agregarDatosModalTipoPagoFacturacion()">
                                        <i class="material-icons" title="Tipo Pago Porcentaje">list</i>
                                        <span id="nfac" class="count bg-warning"></span>
                                     </button> -->
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
                                    <select name="cod_cliente" id="cod_cliente" class="selectpicker form-control form-control-sm" data-style="btn btn-info"  required="true" onChange="ajaxClienteContacto(this);" data-live-search="true" >
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
                            <label class="col-sm-3 col-form-label">Observaciones * 1</label>
                            <div class="col-sm-9">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="observaciones" id="observaciones"  value="<?=$observaciones;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" requerid/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-3 col-form-label">Observaciones 2</label>
                            <div class="col-sm-9">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="observaciones_2" id="observaciones_2" onkeyup="javascript:this.value=this.value.toUpperCase();" required="true" />
                                </div>
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
                                            <th>#</th>                                            
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
                                        //recibimos las normas seleccionadas a facturar
                                        $total_items = $_POST["total_items"];
                                        $ids_normas= array();
                                        for ($i=1;$i<=$total_items-1;$i++){
                                            if($_POST["idVentaNormas_a".$i]!=''){
                                                $idVentaNormas=$_POST["idVentaNormas_a".$i];
                                                $ids_normas[$i-1]=$idVentaNormas;
                                            }
                                        }
                                        $stringNormas=implode(",", $ids_normas);
                                        $stringNormas=trim($stringNormas,',');

                                        $queryPr="SELECT * from ibnorca.ventanormas where IdVentaNormas in ($stringNormas)";
                                       // echo $queryPr;
                                        $stmt = $dbh->prepare($queryPr);
                                        $stmt->execute();
                                        $modal_totalmontopre=0;$modal_totalmontopretotal=0;
                                        while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $codigoPre=$rowPre['IdVentaNormas'];
                                            $codCS=533;//por defecto
                                            $tipoPre=descripcionClaServicio($codCS);
                                            $cantidadPre=$rowPre['Cantidad'];                                            
                                            $montoPre=$rowPre['Precio'];
                                            $descuento_bob_cliente=0; //descuento maximo
                                            $banderaHab=1;                                            
                                            $cod_anio=1;                                        
                                            if($banderaHab!=0){
                                                $descuento_porX=0;
                                                $descuento_bobX=0;                                                
                                                $idNorma=$rowPre['idNorma'];   
                                                $descripcion_alternaX=nameNorma($idNorma);                                                
                                                $montoPre=number_format($montoPre,2,".","");
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
                                                  <td class="text-left"><?=$tipoPre?></td>
                                                  <td class="text-right"><?=$cantidadPre?></td>
                                                  <td class="text-right"><input type="number" step="0.01" id="monto_precio<?=$iii?>" name="monto_precio<?=$iii?>" class="form-control text-primary text-right"  value="<?=$montoPre?>" step="0.01" onkeyup="activarInputMontoFilaServicio2()"></td>
                                                  <!--  descuentos -->
                                                  <td class="text-right"><input type="number" step="0.01" class="form-control" name="descuento_por<?=$iii?>" id="descuento_por<?=$iii?>" value="<?=$descuento_porX?>" min="0" max="<?=$descuento_cliente?>" onkeyup="descuento_convertir_a_bolivianos(<?=$iii?>)"></td>                                             
                                                  <td class="text-right"><input type="number" class="form-control" name="descuento_bob<?=$iii?>" id="descuento_bob<?=$iii?>" value="<?=$descuento_bobX?>" min="0" max="<?=$descuento_bob_cliente?>" onkeyup="descuento_convertir_a_porcentaje(<?=$iii?>)"></td>                                        
                                                  <!-- total -->
                                                  <td class="text-right"><input type="hidden" name="modal_importe<?=$iii?>" id="modal_importe<?=$iii?>"><input type="text" class="form-control" name="modal_importe_dos<?=$iii?>" id="modal_importe_dos<?=$iii?>" style ="background-color: #ffffff;" readonly></td>
                                                                                              
                                                  <td>
                                                    <textarea name="descripcion_alterna<?=$iii?>" id="descripcion_alterna<?=$iii?>" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();"><?=$descripcion_alternaX?></textarea>
                                                     <!-- <input type="text" > -->
                                                  </td>
                                                  <!-- checkbox -->
                                                  <td>                                                    
                                                        <div class="togglebutton">
                                                           <label>
                                                             <input type="checkbox"  id="modal_check<?=$iii?>" onchange="activarInputMontoFilaServicio2()" checked>
                                                             <span class="toggle"></span>
                                                           </label>
                                                       </div>                                                    
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
                                <div class="row">
                                    <!-- <label class="col-sm-5 col-form-label" style="color:#000000">Monto Total + Servicios Adicionales</label> -->

                                    <div class="col-sm-4">
                                        <div class="form-group">                                            
                                            <input style="background:#ffffff" class="form-control" type="hidden" name="monto_total" id="monto_total"  readonly="readonly" value="0" step="0.01" />
                                            <input  type="hidden"  name="monto_total_a" id="monto_total_a" value="0" step="0.01" />
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
                        <a href='<?=$urlSolicitudfactura;?>&cod=<?=$cod_simulacion;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
                    <?php }else{?>
                        <a href='<?=$urlListSimulacionesServ?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
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
                    detalle_areas.push({codigo:<?=$datosAreas[$i][$j]->codigo?>,cod_area:<?=$datosAreas[$i][$j]->cod_area?>,nombrex:'<?=$datosAreas[$i][$j]->nombrex?>'});
                </script>

              <?php         
              }          
            }
        ?><script>itemAreas_facturacion_aux.push(detalle_areas);</script><?php                    
    }
?>