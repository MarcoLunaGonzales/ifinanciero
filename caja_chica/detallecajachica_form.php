<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

//$dbh = new Conexion();
$dbh = new Conexion();
//por is es edit
$cod_cc=$cod_cc;
$cod_tcc=$cod_tcc;
$cod_dcc=$codigo;
$i=0;
  echo "<script>var array_cuenta=[],imagen_cuenta=[];</script>";
   $stmtCuenta = $dbh->prepare("SELECT pcc.cod_cuenta,pc.numero,pc.nombre from plan_cuentas_cajachica pcc,plan_cuentas pc where pcc.cod_cuenta=pc.codigo");
   $stmtCuenta->execute();
   while ($rowCuenta = $stmtCuenta->fetch(PDO::FETCH_ASSOC)) {
    $codigoX=$rowCuenta['cod_cuenta'];
    $numeroX=$rowCuenta['numero'];
    $nombreX=$rowCuenta['nombre'];
    ?>
    <script>
     var obtejoLista={
       label:'<?=trim($numeroX)?> - <?=trim($nombreX)?>',
       value:'<?=$codigoX?>'};
       array_cuenta[<?=$i?>]=obtejoLista;
       imagen_cuenta[<?=$i?>]='../assets/img/calc.jpg';
    </script> 
    <?php
    $i=$i+1;  
  }
//sacmos el valor de fechas hacia atrás
$dias_atras=obtenerValorConfiguracion(31);
$cod_proveedores=0;



if ($codigo > 0){
    $stmt = $dbh->prepare("SELECT codigo,cod_cuenta,fecha,cod_tipodoccajachica,nro_documento,cod_personal,monto,observaciones,nro_recibo,cod_area,cod_uo,cod_proveedores,cod_actividad_sw,
        (select c.nombre from plan_cuentas c where c.codigo=cod_cuenta) as nombre_cuenta,
        (select c.numero from plan_cuentas c where c.codigo=cod_cuenta) as nro_cuenta
    from caja_chicadetalle
    where codigo =:codigo");
    $stmt->bindParam(':codigo',$cod_dcc);
    $stmt->execute();
    $result = $stmt->fetch();
    $cod_cuenta = $result['cod_cuenta'];
    $fecha = $result['fecha'];
    $cod_retencion = $result['cod_tipodoccajachica'];    
    $nro_documento = $result['nro_documento'];    
    $cod_personal = $result['cod_personal'];    
    $observaciones = $result['observaciones'];    
    $monto = $result['monto'];
    $nombre_cuenta = $result['nombre_cuenta'];    
    $nro_cuenta = $result['nro_cuenta']; 
    $nro_recibo= $result['nro_recibo'];
    $cod_area= $result['cod_area'];
    $cod_uo= $result['cod_uo'];
    $cod_proveedores= $result['cod_proveedores'];
    $cod_actividad_sw= $result['cod_actividad_sw'];

    $cuenta_aux=$nro_cuenta." - ".$nombre_cuenta;   

    // sacamos datos del comprobante
    $stmtComprobante = $dbh->prepare("SELECT cod_comprobantedetalle from estados_cuenta where cod_cajachicadetalle=$cod_dcc order by codigo asc LIMIT 1");
    $stmtComprobante->execute();
    $resultComprobante = $stmtComprobante->fetch();
    $cod_comprobante = $resultComprobante['cod_comprobantedetalle'];    
    
} else {
    //para el numero correlativo
    $stmtCC = $dbh->prepare("SELECT nro_documento,nro_recibo from caja_chicadetalle where cod_estadoreferencial=1 and cod_cajachica=$cod_cc order by codigo desc limit 1");
    $stmtCC->execute();
    $resultCC = $stmtCC->fetch();
    $numero_caja_chica_aux = $resultCC['nro_documento'];
    $numero_recibo_aux = $resultCC['nro_recibo'];
    if($numero_caja_chica_aux==null){
        $numero_caja_chica_aux=0;
        $numero_recibo_aux=0;
    }

    $codigo=0;
    // $cod_cuenta = 0;
    $cod_uo=0;
    $cod_area=0;
    
    $fecha = date('Y-m-d');
    $cod_retencion = null;
    $nro_documento = $numero_caja_chica_aux+1;
    $nro_recibo=$numero_recibo_aux+1;
    $cod_personal = null;    
    $observaciones = null;    
    $monto = 0;    
    $cod_estado = 1;

    $cuenta_aux="";
    
    $cod_comprobante=null;
    // $cod_contra_cuenta_aux=null;

    $cuenta_aux="";
    $cod_cuenta=0;
    $cod_actividad_sw=null;


// require_once 'modal.php';

}

$fecha_dias_atras=obtener_diashsbiles_atras($dias_atras,$fecha);

?>

<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveDetalleCajaChica;?>" method="post" onsubmit="return valida(this)"> 
        <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
        <input type="hidden" name="cod_cc" id="cod_cc" value="<?=$cod_cc;?>"/>
        <input type="hidden" name="cod_tcc" id="cod_tcc" value="<?=$cod_tcc;?>"/>
  			<div class="card">
  			  <div class="card-header <?=$colorCard;?> card-header-text">
    				<div class="card-text">
    				  <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar Nuevo"; else echo "Editar";?>  Detalle</h4>
    				</div>
  			  </div>
  			  <div class="card-body ">			           
            <div class="row">
              <label class="col-sm-2 col-form-label">Monto</label>
                <div class="col-sm-4">
                    <div class="form-group">
                        <input class="form-control" type="text" step="any" name="monto" id="monto" value="<?=$monto;?>" required/>
                    </div>
                </div>
                
                    <input class="form-control" type="hidden" name="numero" id="numero" value="<?=$nro_documento;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" readonly="readonly"/>
                <!-- </div>
                </div> -->
                <label class="col-sm-2 col-form-label">Nro. Recibo</label>
                <div class="col-sm-4">
                <div class="form-group">
                    <input class="form-control" type="number" name="nro_recibo" id="nro_recibo" value="<?=$nro_recibo;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" required/>
                </div>
                </div>
            </div> 
            <div class="row">
                <label class="col-sm-2 col-form-label">Tipo Retención</label>
                <div class="col-sm-4">
                    <div class="form-group">
                        <select name="tipo_retencion" id="tipo_retencion" class="selectpicker form-control form-control-sm" data-style="btn btn-info" required>                                    
                          <option value="" disabled selected="selected">-Retenciones-</option>
                            <?php                                     
                            $stmtTipoRet = $dbh->query("SELECT * from configuracion_retenciones where cod_estadoreferencial=1 order by 2");
                            while ($row = $stmtTipoRet->fetch()){ ?>
                                <option <?=($cod_retencion==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                            <?php } ?>
                        </select>                                  
                    </div>
                </div>

                <label class="col-sm-2 col-form-label">Fecha</label>
                <div class="col-sm-4">
                    <div class="form-group">
                        
                        <input class="form-control" name="fecha" id="fecha" type="date" min="<?=$fecha_dias_atras?>" max="<?=$fecha?>" required="true" value="<?=$fecha?>" />
                        </select>
                       
                    </div>
                </div>
            </div><!-- monto y fecha -->
            <div class="row">
              <label class="col-sm-2 col-form-label">Cuenta</label>
              <div class="col-sm-8">
                <div class="form-group">
                    <input class="form-control" type="text" name="cuenta_auto" id="cuenta_auto" value="<?=$cuenta_aux?>" placeholder="[numero] y nombre de cuenta" required />
                    <input class="form-control" type="hidden" name="cuenta_auto_id" id="cuenta_auto_id" value="<?=$cod_cuenta?>" required/>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="form-group">                                
                  <div class="retencion_sin_gastos" style="display: none">
                   <!--  <a href="#" class="btn btn-warning btn-round btn-fab btn-sm" onclick="AgregarContraCuentaCajaChica()">
                      <i class="material-icons" title="Estado de Cuentas">add</i>
                    </a> -->

                    <a  href="#" onclick="verEstadosCuentas_cajachica(1,0);" class="btn btn-danger btn-fab btn-round btn-sm">
                      <i class="material-icons text-dark" title="Estado de Cuentas">ballot</i>
                    </a>
                  </div>
                </div>
                </div>
            </div><!-- cuenta-->

            <input type="hidden" name="cuenta1" id="cuenta1" value="<?=$cod_contra_cuenta?>">
            <input type="hidden" name="cuenta_auxiliar1" id="cuenta_auxiliar1" value="<?=$cod_contra_cuenta_aux?>">

            <input type="hidden" name="comprobante" id="comprobante" value="<?=$cod_comprobante?>">
            <input type="hidden" id="tipo_estadocuentas1" value="3">        
           <!--  <div class="contenedor_contra_cuenta" style="display: none">
              <div class="row">
                <label class="col-sm-2 col-form-label"><small>Contra cuenta</small></label>
                <div class="col-sm-8">
                  <div class="form-group">
                    <div class="form-group" id="divContraCuentaDetalle">
                    <?php if($codigo>0){
                      $stmtContraCuenta = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre from plan_cuentas p where p.codigo=$cod_contra_cuenta");
                      $stmtContraCuenta->execute();
                      $result = $stmtContraCuenta->fetch();
                      $numero_contracuenta = $result['numero'];
                      $nombre_contracuenta = $result['nombre'];
                      $stmtAuxiliar = $dbh->prepare("SELECT codigo, nombre FROM cuentas_auxiliares where cod_cuenta=$cod_contra_cuenta_aux");
                      $stmtAuxiliar->execute();
                      $result = $stmtAuxiliar->fetch();
                      $numero_contracuenta_aux = $result['numero'];
                      $nombre_contracuenta_aux = $result['nombre'];
                      ?>
                      <span class="text-danger font-weight-bold">[<?=$numero_contracuenta?>]-<?=$nombre_contracuenta?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-danger font-weight-bold">Monto: <?=$monto?></span><br>
                      <span class="text-primary font-weight-bold small"> <?=$nombre_contracuenta_aux?></span>
                    <?php }?>
                    </div>
                  </div>
                </div>
              </div>
            </div> -->

            <div class="row">
              <label class="col-sm-2 col-form-label">Personal</label>
              <div class="col-sm-8">
                <div class="form-group">
                    <select name="cod_personal" id="cod_personal" class="selectpicker form-control form-control-sm" data-style="btn btn-info"  data-show-subtext="true" data-live-search="true" onChange="ajaxCajaCPersonalUO(this);">
                        <option value=""></option>
                        <?php 
                        $querypersonal = "SELECT codigo,CONCAT_WS(' ',paterno,materno,primer_nombre)AS nombre from personal where cod_estadoreferencial=1 order by nombre";
                        $stmtPersonal = $dbh->query($querypersonal);
                        while ($row = $stmtPersonal->fetch()){ ?>
                            <option <?=($cod_personal==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>"><?=strtoupper($row["nombre"]);?></option>
                        <?php } ?>
                    </select>
                </div>
              </div>
            </div>
            <div class="row">
              <label class="col-sm-2 col-form-label">Oficina</label>
              <div class="col-sm-8">
                <div class="form-group">
                    <div id="div_contenedor_uo">                                        
                      <?php
                          $sqlUO="SELECT codigo,nombre,abreviatura from unidades_organizacionales where cod_estado=1";
                          $stmt = $dbh->prepare($sqlUO);
                          $stmt->execute();
                          ?>
                          <select name="cod_uo" id="cod_uo" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true" onChange="ajaxAreaUOCAJACHICA(this);" title="Elija una opción">
                              <?php 
                                  while ($row = $stmt->fetch()){ 
                              ?>
                                   <option <?=($cod_uo==$row["codigo"])?"selected":"";?> data-subtext="<?=$row["codigo"];?>" value="<?=$row["codigo"];?>"><?=$row["nombre"];?>(<?=$row["abreviatura"];?>)</option>
                               <?php 
                                  } 
                              ?>
                           </select>                                              
                    </div>                                                        
                </div>
              </div>
            </div>
            <div class="row">
              <label class="col-sm-2 col-form-label">Area</label>
              <div class="col-sm-8">
                <div class="form-group">
                    <div id="div_contenedor_area">                                        
                        <?php
                        if($codigo>0){
                            $sqlUO="SELECT cod_area,(select a.nombre from areas a where a.codigo=cod_area )as nombre_areas,(select a.abreviatura from areas a where a.codigo=cod_area)as abrev_area from areas_organizacion where cod_estadoreferencial=1 and cod_unidad=$cod_uo order by nombre_areas";
                            $stmt = $dbh->prepare($sqlUO);
                            $stmt->execute();
                            ?>
                            <select name="cod_area" id="cod_area" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true" >
                                <?php 
                                    while ($row = $stmt->fetch()){ 
                                ?>
                                     <option value="<?=$row["cod_area"];?>" data-subtext="<?=$row["cod_area"];?>" <?=($cod_area==$row["cod_area"])?"selected":"";?> ><?=$row["nombre_areas"];?>(<?=$row["abrev_area"];?>)</option>
                                 <?php 
                                    } 
                                ?>
                             </select>
                       <?php }else{?>

                        <input type="hidden" name="cod_area" id="cod_area" value="0">                                        
                        <?php }
                             ?>
                    </div>
                </div>
              </div>
            </div>
            <div class="row">
              <label class="col-sm-2 col-form-label">Actividad</label>
              <div class="col-sm-8">
                <div class="form-group">
                  
                    <div id="div_contenedor_actividad">
                      <?php
                      $cod_uo_proy_fin=VerificarProyFinanciacion($cod_uo);//verificamos si el codigo pertenece a algun proyecto, de ser asi obtenemos el codigo                              
                      if($cod_uo_proy_fin!=null){
                        $lista= obtenerActividadesServicioImonitoreo($cod_uo_proy_fin); ?>

                        <select name="cod_actividad" id="cod_actividad" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true">
                        <option disabled selected value="">--SELECCIONE--</option>
                         <?php
                              foreach ($lista as $listas) { ?>
                                <option <?=($cod_actividad_sw==$listas->codigo)?"selected":"";?> value="<?=$listas->codigo?>" class="text-right"><?=substr($listas->nombre, 0, 85)?></option>

                              <?php }?>
                        </select>        
                      <?php }else{ ?>
                          <input type="hidden" name="cod_actividad" id="cod_actividad" value="<?=$cod_actividad_sw?>">

                      <?php } ?>                                                                               
                    </div>
                </div>
              </div>
            </div>
            <!-- proveedor -->
            <div class="row">
              <label class="col-sm-2 col-form-label">Proveedor :</label>
              <div class="col-sm-8">
                <div class="form-group">                        
                  <div id="div_contenedor_proveedor">
                    <select class="selectpicker form-control form-control-sm" name="proveedores" id="proveedores" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" title="Seleccione Proveedor">
                      <option value=""></option>
                      <?php 
                      $query="SELECT * FROM af_proveedores order by nombre";
                      $stmt = $dbh->prepare($query);
                      $stmt->execute();
                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $codigoProv=$row['codigo'];    
                        ?><option <?=($cod_proveedores==$codigoProv)?"selected":"";?> value="<?=$codigoProv?>" class="text-right"><?=$row['nombre']?></option>
                       <?php 
                       } ?> 
                    </select>
                  </div>
                </div>
              </div>      
              <div class="col-sm-2">
                  <div class="form-group">                                
                      <a href="#" class="btn btn-warning btn-round btn-fab btn-sm" onclick="cargarDatosRegistroProveedorCajaChica(<?=$cod_tcc?>,<?=$cod_cc?>,<?=$cod_dcc?>)">
                        <i class="material-icons" title="Add Proveedor">add</i>
                      </a>
                      <a href="#" class="btn btn-success btn-round btn-fab btn-sm" onclick="actualizarRegistroProveedorCajaChica(<?=$cod_tcc?>,<?=$cod_cc?>,<?=$cod_dcc?>)">
                        <i class="material-icons" title="Actualizar Proveedor">update</i>
                      </a> 
                  </div>
              </div>                          
            </div>
            <div class="row">
                <label class="col-sm-2 col-form-label">Detalle</label>
                <div class="col-sm-7">
                <div class="form-group">
                    <input class="form-control rounded-0" name="observaciones" id="observaciones" rows="3" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?=$observaciones;?>" required/>

                    <!-- <input class="form-control" type="text" name="observaciones" id="observaciones" required="true" value="<?=$observaciones;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/> -->
                </div>
                </div>
            </div>              
  			  </div>
  			  <div class="card-footer ml-auto mr-auto">
  				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
  				<a href="<?=$urlListDetalleCajaChica;?>&codigo=<?=$cod_cc;?>&cod_tcc=<?=$cod_tcc?>" class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
  			  </div>
  			</div>
		  </form>
		</div>
	
	</div>
</div>

<!-- carga de proveedores -->
<div class="cargar">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
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
                  <h4 class="card-title">Proveedor</h4>
            </div>
            <div class="card-body">
                 <div id="datosProveedorNuevo">
                   
                 </div> 
                <div class="form-group float-right">
                        <button type="button" onclick="guardarDatosProveedorCajaChica()" class="btn btn-info btn-round">Agregar</button>
                </div>
          </div>
      </div>  
    </div>
  </div>

<script>
//  var codigo_detalle = <?=$codigo?>;
  // if(codigo_detalle>0){ //para edit
  //   var nro_cuenta = document.getElementById("cuenta_auto").value; 
  //   if(nro_cuenta.substr(0,1)==2){
  //     $(".retencion_sin_gastos").show();
  //     $(".contenedor_contra_cuenta").show();
  //   }else{
  //     $(".retencion_sin_gastos").hide();
  //     $(".contenedor_contra_cuenta").hide();
  //   }
  // }
  //el numero de cuenta comporbamos si empieza con 2
  $( "#cuenta_auto" ).blur(function() {
      var nro_cuenta = document.getElementById("cuenta_auto").value; 
      
      if(nro_cuenta.substr(0,1)==2){//comprobamos el primer digito de la cuenta 
        // alert("es 5");
        $(".retencion_sin_gastos").show();
        // $(".contenedor_contra_cuenta").show();
      }else{
        $(".retencion_sin_gastos").hide();
        // $(".contenedor_contra_cuenta").hide();
      }
    });  
  
</script>
  <!--    end small modal -->
<script>$('.selectpicker').selectpicker("refresh");</script>
<!-- verifica que no vea campos vacios -->
<script type="text/javascript">
  function valida(f) {
    var nro_cuenta2 = document.getElementById("cuenta_auto").value; 
    var ok = true;
    if(nro_cuenta2.substr(0,1)!=5){//comprobamos el primer digito de la cuenta 
      if(f.elements["cod_personal"].value == "" && f.elements["proveedores"].value == "")
      {
        var msg = "Rellene el campo 'personal' o 'proveedor'\n";
        ok = false;
      }else{
        if(f.elements["monto"].value == 0)
        {    
              var msg = "El Monto no debe ser menor a '0'...\n";
              ok = false;
        }
      }
    }else{
      if(f.elements["monto"].value == 0)
        {    
              var msg = "El Monto no debe ser menor a '0'...\n";
              ok = false;
        }
    }
    if(ok == false)
      alert(msg);
    return ok;
  }
</script>

<?php 
  require_once 'modal.php';
?>
