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
   $stmtCuenta = $dbh->prepare("SELECT pcc.cod_cuenta,pc.numero,pc.nombre from plan_cuentas_cajachica pcc,plan_cuentas pc 
where pcc.cod_cuenta=pc.codigo");
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


$cod_proveedores=0;



if ($codigo > 0){
    
    $stmt = $dbh->prepare("SELECT codigo,cod_cuenta,fecha,cod_tipodoccajachica,nro_documento,cod_personal,monto,observaciones,nro_recibo,cod_area,cod_uo,cod_proveedores,
        (select c.nombre from plan_cuentas c where c.codigo=cod_cuenta) as nombre_cuenta,
        (select c.numero from plan_cuentas c where c.codigo=cod_cuenta) as nro_cuenta
    from caja_chicadetalle
    where codigo =:codigo");
    
    $stmt->bindParam(':codigo',$cod_dcc);
    $stmt->execute();
    $result = $stmt->fetch();

    $cod_cuenta = $result['cod_cuenta'];
    $fecha = $result['fecha'];
    $cod_tipodoccajachica = $result['cod_tipodoccajachica'];    
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

    $cuenta_aux=$nro_cuenta." - ".$nombre_cuenta;   
    
} else {
    //para el numero correlativo
    $stmtCC = $dbh->prepare("SELECT nro_documento from caja_chicadetalle where cod_estadoreferencial=1 and cod_cajachica=$cod_cc order by codigo desc");
    $stmtCC->execute();
    $resultCC = $stmtCC->fetch();
    $numero_caja_chica_aux = $resultCC['nro_documento'];
    if($numero_caja_chica_aux==null){
        $numero_caja_chica_aux=0;
    }

    $codigo=0;
    // $cod_cuenta = 0;
    $cod_uo=0;
    $cod_area=0;
    
    $fecha = date('Y-m-d');
    $cod_tipodoccajachica = 0;
    $nro_documento = $numero_caja_chica_aux+1;    
    $cod_personal = 0;    
    $observaciones = "";    
    $monto = 0;    
    $cod_estado = 1;

    $cuenta_aux="";
    $nro_recibo=0;
    $cod_cuenta=0;

}
?>

<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveDetalleCajaChica;?>" method="post">
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
                      <label class="col-sm-2 col-form-label">Cuenta</label>
                      <div class="col-sm-8">
                        <div class="form-group">

                            <input class="form-control" type="text" name="cuenta_auto" id="cuenta_auto" value="<?=$cuenta_aux?>" placeholder="[numero] y nombre de cuenta" required/>
                            <input class="form-control" type="hidden" name="cuenta_auto_id" id="cuenta_auto_id" value="<?=$cod_cuenta?>" required/>
                            
                        </div>
                      </div>
                    </div><!-- cuenta-->

                    <div class="row">
                        <label class="col-sm-2 col-form-label">Tipo Doc.</label>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <select name="tipo_documento" id="tipo_documento" class="selectpicker form-control form-control-sm" data-style="btn btn-info" required>                                    
                                    <?php                                     
                                    $stmtTipoDoc = $dbh->query("SELECT td.codigo,td.nombre from tipos_documentocajachica td where td.tipo=1 order by nombre");
                                    while ($row = $stmtTipoDoc->fetch()){ ?>
                                        <option <?=($cod_tipodoccajachica==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                    <?php } ?>
                                </select>                                  
                            </div>
                        </div>
                        <!-- <label class="col-sm-2 col-form-label">Nro. Doc.</label> -->
                        <!-- <div class="col-sm-4">
                        <div class="form-group"> -->
                            <input class="form-control" type="hidden" name="numero" id="numero" value="<?=$nro_documento;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" readonly="readonly"/>
                        <!-- </div>
                        </div> -->
                        <label class="col-sm-2 col-form-label">Nro. Recibo</label>
                        <div class="col-sm-4">
                        <div class="form-group">
                            <input class="form-control" type="number" name="nro_recibo" id="nro_recibo" value="<?=$nro_recibo;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" required/>
                        </div>
                        </div>
                    </div> <!--fin campo fecha numero-->
                    <div class="row">
                        <label class="col-sm-2 col-form-label">Monto</label>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input class="form-control" type="text" step="any" name="monto" id="monto" value="<?=$monto;?>" required/>
                            </div>
                        </div>
                        <label class="col-sm-2 col-form-label">Fecha</label>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input class="form-control" type="date" name="fecha" id="fecha" readonly="true" value="<?=$fecha;?>" required/>
                            </div>
                        </div>
                    </div><!--monto inicio y reembolso-->
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
                              

                                  $sqlUO="SELECT codigo,nombre from unidades_organizacionales where cod_estado=1";
                                  $stmt = $dbh->prepare($sqlUO);
                                  $stmt->execute();
                                  ?>
                                  <select name="cod_uo" id="cod_uo" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true" onChange="ajaxAreaUOCAJACHICA(this);" title="Elija una opción">
                                      <?php 
                                          while ($row = $stmt->fetch()){ 
                                      ?>
                                           <option <?=($cod_uo==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
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
                                            $sqlUO="SELECT cod_area,(select a.nombre from areas a where a.codigo=cod_area )as nombre_areas from areas_organizacion where cod_estadoreferencial=1 and cod_unidad=$cod_uo order by nombre_areas";
                                            $stmt = $dbh->prepare($sqlUO);
                                            $stmt->execute();
                                            ?>
                                            <select name="cod_area" id="cod_area" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true" >
                                                <?php 
                                                    while ($row = $stmt->fetch()){ 
                                                ?>
                                                     <option <?=($cod_area==$row["cod_area"])?"selected":"";?> value="<?=$row["cod_area"];?>"><?=$row["nombre_areas"];?></option>
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

                    <!-- proveedor -->
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Proveedor :</label>
                       <div class="col-sm-8">
                         <div class="form-group">                        
                              <select class="selectpicker form-control form-control-sm" name="proveedores" id="proveedores" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" title="Seleccione Proveedor">
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
                        <div class="col-sm-2">
                            <div class="form-group">                                
                                <a href="#" class="btn btn-warning btn-round btn-fab btn-sm" onclick="cargarDatosRegistroProveedorCajaChica(<?=$cod_tcc?>,<?=$cod_cc?>,<?=$cod_dcc?>)">
                                  <i class="material-icons" title="Add Proveedor">add</i>
                                </a>
                                <a href="#" class="btn btn-success btn-round btn-fab btn-sm" onclick="actualizarRegistroProveedorCajaChica(<?=$cod_tcc?>,<?=$cod_cc?>,<?=$cod_dcc?>)">
                                  <i class="material-icons" title="Actualizar">update</i>
                                </a> 
                            </div>
                        </div>                        
                    </div>

                    <div class="row">
                        <label class="col-sm-2 col-form-label">Detalle</label>
                        <div class="col-sm-7">
                        <div class="form-group">
                            <input class="form-control rounded-0" name="observaciones" id="observaciones" rows="3" required onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?=$observaciones;?>" required/>

                            <!-- <input class="form-control" type="text" name="observaciones" id="observaciones" required="true" value="<?=$observaciones;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/> -->
                        </div>
                        </div>
                    </div><!--fin campo nombre -->              
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
  <!--    end small modal -->
<script>$('.selectpicker').selectpicker("refresh");</script>