<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';


$dbh = new Conexion();


//------------------------------------------- para combos
$querydepre = "select * from depreciaciones"; //los rubros
$statementDepre = $dbh->query($querydepre);

$queryUO = "SELECT u.*, uo.nombre as xuo FROM ubicaciones u, unidades_organizacionales uo WHERE u.cod_unidades_organizacionales = uo.codigo";
$statementUO = $dbh->query($queryUO);//uo

$query = "select * from personal2";
$statementPersonal = $dbh->query($query);
$statementPersonal2 = $dbh->query($query);

$query_prov = "select * from af_proveedores";
$statementprov = $dbh->query($query_prov);

$query_tiposbienes = "select * from tiposbienes";
$statementTIPOSBIENES = $dbh->query($query_tiposbienes);

//------------------------------------------------------------------- principal
if ($codigo > 0){
    $stmt = $dbh->prepare("SELECT * FROM activosfijos where codigo =:codigo");
    //Ejecutamos;
    $stmt->bindParam(':codigo',$codigo);
    $stmt->execute();
    $result = $stmt->fetch();
    $codigo = $result['codigo'];
    $codigoactivo = $result['codigoactivo'];
    $tipoalta = $result['tipoalta'];
    $fechalta = $result['fechalta'];
    $indiceufv = $result['indiceufv'];
    $tipocambio = $result['tipocambio'];
    $moneda = $result['moneda'];
    $valorinicial = $result['valorinicial'];
    $depreciacionacumulada = $result['depreciacionacumulada'];
    $valorresidual = $result['valorresidual'];
    $cod_depreciaciones = $result['cod_depreciaciones'];
    $cod_tiposbienes = $result['cod_tiposbienes'];
    $vidautilmeses = $result['vidautilmeses'];
    $estadobien = $result['estadobien'];
    $otrodato = $result['otrodato'];
    $cod_ubicaciones = $result['cod_ubicaciones'];
    $cod_empresa = $result['cod_empresa'];
    $activo = $result['activo'];
    $cod_responsables_responsable = $result['cod_responsables_responsable'];
    $cod_responsables_autorizadopor = $result['cod_responsables_autorizadopor'];
    $created_at = $result['created_at'];
    $created_by = $result['created_by'];
    $modified_at = $result['modified_at'];
    $modified_by = $result['modified_by'];
    $vidautilmeses_restante = $result['vidautilmeses_restante'];
    $cod_af_proveedores = $result['cod_af_proveedores'];
    $numerofactura = $result['numerofactura'];


        
    //IMAGEN
    $stmtIMG = $dbh->prepare("SELECT * FROM activosfijosimagen where codigo =:codigo");
    //Ejecutamos;
    $stmtIMG->bindParam(':codigo',$codigo);
    $stmtIMG->execute();
    $resultIMG = $stmtIMG->fetch();
    $imagen = $resultIMG['imagen'];
    //$archivo = __DIR__.DIRECTORY_SEPARATOR."imagenes".DIRECTORY_SEPARATOR.$imagen;//sale mal
    $archivo = "activosfijos/imagenes/".$imagen;//sale mal

    //asignaciones
    $query2 = "SELECT * FROM v_activosfijos_asignaciones where codigo = ".$codigo;
    $statement2 = $dbh->query($query2);


} else {
    $codigo = 0;
    $codigo = '';
    $codigoactivo = '';
    $tipoalta = '';
    $fechalta = '';
    $indiceufv = '';
    $tipocambio = '';
    $moneda = '';
    $valorinicial =  '';
    $depreciacionacumulada = '';
    $valorresidual = '';
    $cod_depreciaciones = '';
    $cod_tiposbienes = '';
    $vidautilmeses = '';
    $estadobien  = '';
    $otrodato  = '';
    $cod_ubicaciones  = '';
    $cod_empresa = '';
    $activo = '';
    $cod_responsables_responsable  = '';
    $cod_responsables_autorizadopor  = '';
    $created_at  = '';
    $created_by  = '';
    $modified_at  = '';
    $modified_by  = '';
    $vidautilmeses_restante  = '';
    $cod_af_proveedores  = '';
    $numerofactura = '';

    $archivo = '';
}

?>
<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSave6;?>" method="post"  enctype="multipart/form-data">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?> <?=$moduleNameSingular6;?></h4>
				</div>
			  </div>
			  <div class="card-body ">
			
<input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
<div class="row">
    <label class="col-sm-2 col-form-label">Codigo Activo</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" name="codigoactivo" id="codigoactivo" required="true" value="<?=$codigoactivo;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>


</div><!--fin campo codigoactivo -->
<div class="row">
    <label class="col-sm-2 col-form-label">Tipo Alta</label>
    <div class="col-sm-4">
    <div class="form-group">
				<select name="tipoalta" id="tipoalta" class="selectpicker " data-style="btn btn-info">
                <!--<select name="tipoalta" id="tipoalta" class="selectpicker " data-style="select-with-transition">-->
					<option <?php if("NUEVO" == $tipoalta) echo "selected"; ?> value="NUEVO">NUEVO</option>
					<option <?php if("USADO" == $tipoalta) echo "selected"; ?> value="USADO">USADO</option>
				</select>
    </div>
    </div><!--fin campo tipoalta -->

    <label class="col-sm-2 col-form-label">Rubro / Depreciacion</label>
    <div class="col-sm-4">
        <div class="form-group">
		<select name="cod_depreciaciones" id="cod_depreciaciones" class="selectpicker" data-style="btn btn-info">
					<?php while ($row = $statementDepre->fetch()){ ?>
						<option <?php if($cod_depreciaciones == $row["codigo"]) echo "selected"; ?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
					<?php } ?>
		</select>				
        </div>
    </div>
</div><!--fin campo cod_depreciaciones -->


<div class="row">
    <label class="col-sm-2 col-form-label">Tipo Bien</label>
    <div class="col-sm-4">
    <div class="form-group">
        <div id="cod_tiposbienes_containers">
		<select name="cod_tiposbienes" id="cod_tiposbienes" class="selectpicker" data-style="btn btn-info">
			<?php while ($row = $statementTIPOSBIENES->fetch()){ ?>
				<option <?php if($cod_tiposbienes == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["tipo_bien"];?></option>
			<?php } ?>
		</select>
        </div>
    </div>
    </div>

    <label class="col-sm-2 col-form-label">Fecha Alta</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="date" name="fechalta" id="fechalta" required="true" value="<?=$fechalta;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div><!--fin campo fechalta -->
</div>
<div class="row">
    <label class="col-sm-2 col-form-label">Valor Inicial</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="number" step="0.01" name="valorinicial" id="valorinicial" required="true" value="<?=$valorinicial;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
<!--fin campo valorinicial -->
    <label class="col-sm-2 col-form-label">Depr. Acumulada (Nuevo es 0)</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" name="depreciacionacumulada" id="depreciacionacumulada" required="true" value="<?=$depreciacionacumulada;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo depreciacionacumulada --> 
<div class="row">
    <label class="col-sm-2 col-form-label">Valor Residual</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" readonly type="text" name="valorresidual" id="valorresidual" required="true" value="<?=$valorresidual;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div><!--fin campo valorresidual -->

    <label class="col-sm-2 col-form-label">Vida Util Meses</label><!-- sel automaticamente -->
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" name="vidautilmeses" id="vidautilmeses" required="true" value="<?=$vidautilmeses;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo vidautilmeses -->
<div class="row">
    <label class="col-sm-2 col-form-label">Estado Bien</label>
    <div class="col-sm-4">
    <div class="form-group">
				<select name="estadobien" name="estadobien" class="selectpicker" data-style="btn btn-info">
					<option <?php if("NUEVO" == $estadobien) echo "selected"; ?> value="NUEVO">NUEVO</option>
					<option <?php if("BUENO" == $estadobien) echo "selected"; ?> value="BUENO">BUENO</option>
					<option <?php if("REGULAR" == $estadobien) echo "selected"; ?> value="REGULAR">REGULAR</option>
					<option <?php if("MALO" == $estadobien) echo "selected"; ?> value="MALO">MALO</option>
					<option <?php if("OBSOLETO" == $estadobien) echo "selected"; ?> value="OBSOLETO">OBSOLETO</option>
					<option <?php if("PESIMO" == $estadobien) echo "selected"; ?> value="PESIMO">PESIMO</option>
				</select>
    </div>
    </div><!--fin campo estadobien -->

    <label class="col-sm-2 col-form-label">Ubicacion</label>
    <div class="col-sm-4">
    <div class="form-group">
		
		<select name="cod_ubicaciones" name="cod_ubicaciones"  class="selectpicker" data-style="btn btn-info">
					<?php while ($row = $statementUO->fetch()){ ?>
						<option <?php if($cod_ubicaciones == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["xuo"];?> <?=$row["edificio"];?> <?=$row["oficina"];?></option>
					<?php } ?>
					</select>		
    </div>
    </div>
</div><!--fin campo cod_ubicaciones -->

<div class="row">
    <label class="col-sm-2 col-form-label">Descripcion del Activo</label>
    <div class="col-sm-7">
    <div class="form-group">
        <textarea rows="2" class="form-control" name="activo" id="activo" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"><?=$activo;?></textarea>
    </div>
    </div>
</div><!--fin campo activo -->
<div class="row">
    <label class="col-sm-2 col-form-label">Informacion o Descripcion</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="otrodato" id="otrodato" required="true" value="<?=$otrodato;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo otrodato -->
<div class="row">
    <label class="col-sm-2 col-form-label">Responsable</label>
    <div class="col-sm-4">
    <div class="form-group">
        <select id="cod_responsables_responsable" name="cod_responsables_responsable" class="selectpicker" data-style="btn btn-info">
            <?php while ($row = $statementPersonal->fetch()){ ?>
                <option <?php if($cod_responsables_responsable == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
            <?php } ?>
        </select>
    </div>
    </div><!--fin campo cod_responsables_responsable -->

    <label class="col-sm-2 col-form-label">Autorizado Por</label>
    <div class="col-sm-4">
    <div class="form-group">
        <select id="cod_responsables_autorizadopor" name="cod_responsables_autorizadopor" class="selectpicker" data-style="btn btn-info">
            <?php while ($row = $statementPersonal2->fetch()){ ?>
                <option <?php if($cod_responsables_autorizadopor == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
            <?php } ?>
        </select>
    </div>
    </div>
</div><!--fin campo cod_responsables_autorizadopor -->
<div class="row">
    <label class="col-sm-2 col-form-label">Proveedor</label>
    <div class="col-sm-4">
    <div class="form-group">
        <select id="cod_af_proveedores" name="cod_af_proveedores" class="selectpicker" data-style="btn btn-info">
            <?php while ($row = $statementprov->fetch()){ ?>
                <option <?php if($cod_af_proveedores == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
            <?php } ?>
        </select>
    </div>
    </div><!--fin campo cod_af_proveedores  -->

    <label class="col-sm-2 col-form-label">Factura o Numero de Documento</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" name="numerofactura" id="numerofactura" required="true" value="<?=$numerofactura;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin numero factura -->

<div class="row">
    <div class="col-md-12">
        <div class="fileinput fileinput-new text-center" data-provides="fileinput">
        <div class="fileinput-new img-raised">
            <img src="<?=$archivo;?>" alt="..." style="width:250px;">
        </div>
        <div class="fileinput-preview fileinput-exists thumbnail img-raised"></div>
        <div>
            <span class="btn btn-raised btn-round <?=$buttonNormal;?> btn-file">
            <span class="fileinput-new">Seleccionar Imagen</span>
            <span class="fileinput-exists">Cambiar</span>
            <input type="file" name="image" /><!-- ARCHHIVO -->
            </span>
                <a href="#" class="btn <?=$buttonNormal;?> btn-round fileinput-exists" data-dismiss="fileinput">
                <i class="fa fa-times"></i> Quitar</a>
        </div>
        </div>
    </div>
  </div>


			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="?opcion=activosfijosLista" class="<?=$buttonCancel;?>">Cancelar</a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>




<?php if ($codigo > 0){ ?>



<!-- tabla -->
<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title"><?=$moduleNamePlural8?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table">
                    <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Estado</th>
                     
                      
                        <th>Personal</th>
                     
                      
                        <th>Edificio</th>
                        <th>Oficina</th>
                        <th>UO</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php $index=1;
                            while ($row = $statement2->fetch()) { ?>
                           <tr>
                                <td><?=$row["fechaasignacion"];?></td>
                                <td><?=$row["estadobien_asig"];?></td>
                     
                                <td><?=$row["nombre_personal"];?></td>
                           
                                
                                <td><?=$row["edificio"];?></td>
                                <td><?=$row["oficina"];?></td>
                                <td><?=$row["nombre_uo"];?></td>
                                <td><a href='<?=$printAlta;?>?codigo=<?=$row["activofijosasignaciones_codigo"];?>' target="_blank" rel="tooltip" class="<?=$buttonEdit;?>">
                                    <i class="material-icons">how_to_reg</i>
                                    </a>
                                </td>
                            </tr>
                        <?php $index++; } ?>
                        </tbody>

                    
                    </table>
                  </div>
                </div>
              </div>
              <?php
              if($globalAdmin==1){
              ?>
      				<div class="card-footer ml-auto mr-auto">
                    <!--<button class="<?=$buttonNormal;?>" onClick="location.href='index.php?opcion=registerUbicacion'">Registrar</button>-->
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlEdit8;?>'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>
<?php } ?>








<script>
$("#valorinicial").keyup(function(){
  $("#valorresidual").val($("#valorinicial").val() - $("#depreciacionacumulada").val());
});
$("#depreciacionacumulada").keyup(function(){
  $("#valorresidual").val($("#valorinicial").val() - $("#depreciacionacumulada").val());
});             
 
$('#cod_depreciaciones').on('change', function() {
  //alert( this.value );
    //cod_tiposbienes
    $.post("activosFijos/tiposbienesAjax2.php", "cod_depreciaciones="+$('#cod_depreciaciones').val(), function (data) {
        $('#cod_tiposbienes').remove();
        
        $("#cod_tiposbienes_containers").empty();
        $('#cod_tiposbienes_containers').append(data);
        $('.selectpicker').selectpicker();
        /*
        //remover child cod_tiposbienes_containers
        $("#cod_tiposbienes_containers").empty();
        //$('#cod_tiposbienes_containers').append('<select name="cod_tiposbienes" id="cod_tiposbienes" class="selectpicker" data-style="btn btn-info"></select>');
        $('#cod_tiposbienes_containers').append('<select name="cod_tiposbienes" id="cod_tiposbienes"></select>');
        
        //cod_tiposbienes
        var select = $('#cod_tiposbienes').empty();
        //$('#cod_tiposbienes').removeAttr("data-style");
        //$('#cod_tiposbienes').removeClass("selectpicker");
        
        //class="selectpicker " data-style="select-with-transition"
        //var data = $.parseJSON(data);
        $.each(data, function (i, item) {
            //console.log(item.tipo_bien);
            select.append('<option value="' + item.codigo + '">' + item.tipo_bien + '</option>');
        });
        //agregar el atributo
        select.attr("data-style","btn btn-info");
        select.addClass("selectpicker");

        //id="cod_tiposbienes" class="selectpicker" data-style="btn btn-info"
        */
    });

    //pero ademas obtener el valor, vida util meses
    $.post("activosFijos/depreciacionesAjax.php", "cod_depreciaciones="+$('#cod_depreciaciones').val(), function (data) {
        //var data = JSON.parse(data);
        $('#vidautilmeses').val(data[0].vida_util);//es una fila, el elemento 0
    });
    
});


$('.datetimepicker').datetimepicker({
    icons: {
        time: "fa fa-clock-o",
        date: "fa fa-calendar",
        up: "fa fa-chevron-up",
        down: "fa fa-chevron-down",
        previous: 'fa fa-chevron-left',
        next: 'fa fa-chevron-right',
        today: 'fa fa-screenshot',
        clear: 'fa fa-trash',
        close: 'fa fa-remove'
    }
});
</script>