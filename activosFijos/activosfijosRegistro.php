<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';


$dbh = new Conexion();


$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$globalAdmin=$_SESSION["globalAdmin"];

//------------------------------------------- para combos
$querydepre = "select * from depreciaciones where cod_estado=1 order by 3"; //los rubros
$statementDepre = $dbh->query($querydepre);

$queryUO = "SELECT u.*, uo.nombre as xuo FROM ubicaciones u, unidades_organizacionales uo WHERE u.cod_unidades_organizacionales = uo.codigo
order by 3";
$statementUO = $dbh->query($queryUO);//uo

$query = "select * from personal2 order by 2";
$statementPersonal = $dbh->query($query);
$statementPersonal2 = $dbh->query($query);

$query_prov = "select * from af_proveedores order by 2";
$statementprov = $dbh->query($query_prov);

$query_tiposbienes = "select * from tiposbienes order by 2";
$statementTIPOSBIENES = $dbh->query($query_tiposbienes);

$query_unidadesOrganizacionales = "select * from unidades_organizacionales";
$statementUNIDADESORGANIZACIONALES = $dbh->query($query_unidadesOrganizacionales);

$query_areas = "select * from areas order by 2";
$statementAREAS = $dbh->query($query_areas);


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

    $cod_unidadorganizacional = $result['cod_unidadorganizacional'];
    $cod_area = $result['cod_area'];


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

    $variableDisabled="true";
} else {
    //consulta para sacar el codigo del ultimo activoFijo
    $stmt = $dbh->prepare("SELECT max(codigo) AS id FROM activosfijos");
    //Ejecutamos;
    $stmt->execute();
    $result = $stmt->fetch();
    $codigo = 0;

    $codigo_aux = $result['id']+1; 
    
    //$codigoactivo = "AF-".$codigo_aux;
    $codigoactivo = "-";

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

    $cod_unidadorganizacional = '';
    $cod_area = '';
    $variableDisabled="false";
}
//echo $variableDisabled;
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
            <div id="divCodigoAF">
                <input type="text"  readonly="readonly" style="padding-left:20px" class="form-control" name="codigoactivo" id="codigoactivo" required="true"  value="<?=$codigoactivo;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
            </div>
        </div>
    </div>
    <div class='col-sm-6'>
            <?php
                require 'assets/phpqrcode/qrlib.php';
                $dir = 'qr_temp/';
                if(!file_exists($dir)){
                    mkdir ($dir);}
                $fileName = $dir.'test.png';
                $tamanio = 6; //tamaño de imagen que se creará
                $level = 'Q'; //tipo de precicion Baja L, mediana M, alta Q, maxima H
                $frameSize = 1; //marco de qr
                $contenido = $codigoactivo;
                QRcode::png($contenido, $fileName, $level,$tamanio,$frameSize);
                echo '<img src="'.$fileName.'"/>';
            ?>
    </div>

</div><!--fin campo codigoactivo -->

<div class="row">
    <label class="col-sm-2 col-form-label">Ubicacion</label>
    <div class="col-sm-4">
    <div class="form-group">
        
        <select name="cod_ubicaciones" id="cod_ubicaciones" class="selectpicker" data-style="btn btn-primary" onChange="ajaxAFunidadorganizacional(this);" >
            <option value=""></option>
            <?php while ($row = $statementUO->fetch()){ ?>
                <option <?=($cod_ubicaciones==$row["codigo"])?"selected":"";?> <?=($codigo>0)?"disabled":"";?> value="<?=$row["codigo"];?>"><?=$row["edificio"];?> <?=$row["oficina"];?></option>
            <?php } ?>
        </select>
    </div>
    </div>
</div>
<div class="row">
    <label class="col-sm-2 col-form-label">Unidad</label>
    <div class="col-sm-4">
    <div class="form-group">
        <div id="div_contenedor_UO">
            <?php
            $sqlUO="SELECT uo.codigo, uo.abreviatura, uo.nombre from ubicaciones u, unidades_organizacionales uo 
            where u.cod_unidades_organizacionales=uo.codigo and uo.cod_estado=1 and u.codigo='$cod_ubicaciones'";
            
            //echo $sqlUO;
            
            $stmt = $dbh->prepare($sqlUO);
            $stmt->execute();
            ?>
            <select name="cod_unidadorganizacional" id="cod_unidadorganizacional" class="selectpicker" data-style="btn btn-primary">
            <?php 
                while ($row = $stmt->fetch()){ 
            ?>
                 <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
             <?php 
                } 
            ?>
         </select>
        </div>
    </div>
    </div><!--fin campo area -->

    <label class="col-sm-2 col-form-label">Área</label>
    <div class="col-sm-4">
        <div class="form-group">
        <select name="cod_area" id="cod_area" class="selectpicker" data-style="btn btn-primary">
                    <?php while ($row = $statementAREAS->fetch()){ ?>
                        <option <?=($cod_area==$row["codigo"])?"selected":"";?>  <?=($codigo>0)?"disabled":"";?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                    <?php } ?>
        </select>               
        </div>
    </div>
</div><!--fin campo unidad -->

<div class="row">
    <label class="col-sm-2 col-form-label">Tipo Alta</label>
    <div class="col-sm-4">
    <div class="form-group">
				<select name="tipoalta" id="tipoalta" class="selectpicker" data-style="btn btn-primary">
                <!--<select name="tipoalta" id="tipoalta" class="selectpicker " data-style="select-with-transition">-->
					<option <?php if("NUEVO" == $tipoalta) echo "selected"; ?> value="NUEVO">NUEVO</option>
					<option <?php if("USADO" == $tipoalta) echo "selected"; ?> value="USADO">USADO</option>
				</select>
    </div>
    </div><!--fin campo tipoalta -->

    <label class="col-sm-2 col-form-label">Rubro</label>
    <div class="col-sm-4">
        <div class="form-group">
		<select name="cod_depreciaciones" id="cod_depreciaciones" class="selectpicker" data-style="btn btn-primary" onchange="ajaxCodigoActivo(this);">
			<option disabled selected value="">-</option>
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
		<select name="cod_tiposbienes" id="cod_tiposbienes" class="selectpicker" data-style="btn btn-primary">
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
        <input class="form-control" type="number" step="0.01" name="valorinicial" id="valorinicial" required="true" value="<?=$valorinicial;?>"/>
    </div>
    </div>
<!--fin campo valorinicial -->
    <label class="col-sm-2 col-form-label">Depr. Acumulada (Nuevo es 0)</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" name="depreciacionacumulada" id="depreciacionacumulada" required="true" value="<?=($row["codigo"]==0)?"0":$depreciacionacumulada;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
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
        <input class="form-control" type="text" name="vidautilmeses" id="vidautilmeses" required="true" value="<?=$vidautilmeses;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" readonly="true"/>
    </div>
    </div>
</div><!--fin campo vidautilmeses -->
<div class="row">
    <label class="col-sm-2 col-form-label">Estado Bien</label>
    <div class="col-sm-4">
    <div class="form-group">
				<select name="estadobien" name="estadobien" class="selectpicker" data-style="btn btn-primary">
					<option <?php if("NUEVO" == $estadobien) echo "selected"; ?> value="NUEVO">NUEVO</option>
					<option <?php if("BUENO" == $estadobien) echo "selected"; ?> value="BUENO">BUENO</option>
					<option <?php if("REGULAR" == $estadobien) echo "selected"; ?> value="REGULAR">REGULAR</option>
					<option <?php if("MALO" == $estadobien) echo "selected"; ?> value="MALO">MALO</option>
					<option <?php if("OBSOLETO" == $estadobien) echo "selected"; ?> value="OBSOLETO">OBSOLETO</option>
					<option <?php if("PESIMO" == $estadobien) echo "selected"; ?> value="PESIMO">PESIMO</option>
				</select>
    </div>
    </div><!--fin campo estadobien -->

    
</div><!--fin campo cod_ubicaciones -->

<div class="row">
    <label class="col-sm-2 col-form-label">Nombre Activo</label>
    <div class="col-sm-7">
    <div class="form-group">
        <textarea rows="2" class="form-control" name="activo" id="activo" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"><?=$activo;?></textarea>
    </div>
    </div>
</div><!--fin campo activo -->

<div class="row">
    <label class="col-sm-2 col-form-label">Datos Complementarios</label>
    <div class="col-sm-7">
    <div class="form-group">
        <textarea rows="2" class="form-control" name="otrodato" id="otrodato" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"><?=$otrodato;?></textarea>
    </div>
    </div>
</div><!--fin campo activo -->

<div class="row">
    <label class="col-sm-2 col-form-label">Responsable</label>
    <div class="col-sm-4">
    <div class="form-group">
        <div id="div_personal_UO">
            <?php
            $stmt = $dbh->prepare("SELECT p.codigo, p.nombre from personal2 p, ubicaciones u, unidades_organizacionales uo where u.cod_unidades_organizacionales=uo.codigo and uo.codigo=p.cod_unidad and u.codigo='$cod_ubicaciones' order by 2");
            $stmt->execute();
            ?>
            <select id="cod_responsables_responsable" name="cod_responsables_responsable" class="form-control" 
            data-style="btn btn-info" data-size="5">
                <?php 
                    while ($row = $stmt->fetch()){ 
                        $codPersonal=$row['codigo'];
                   ?>
                   <option value="<?=$row["codigo"];?>" <?=($codPersonal==$cod_responsables_responsable)?"selected":"";?> <?=($codigo>0)?"disabled":"";?> >
                        <?=$row["nombre"];?>
                    </option>
                    <?php 
                    } ?>
            </select>

        </div>
    </div>
    </div><!--fin campo cod_responsables_responsable -->

    <label class="col-sm-2 col-form-label">Autorizado Por</label>
    <div class="col-sm-4">
    <div class="form-group">
        <select id="cod_responsables_autorizadopor" name="cod_responsables_autorizadopor" class="form-control" data-style="btn btn-primary">
            <?php while ($row = $statementPersonal2->fetch()){ ?>
                <option <?=($cod_responsables_autorizadopor==$row["codigo"])?"selected":"";?>  <?=($codigo>0)?"disabled":"";?> value="<?=$row["codigo"];?>">
                    <?=$row["nombre"];?>
                </option>
            <?php } ?>
        </select>
    </div>
    </div>
</div><!--fin campo cod_responsables_autorizadopor -->
<div class="row">
    <label class="col-sm-2 col-form-label">Proveedor</label>
    <div class="col-sm-4">
    <div class="form-group">
        <select id="cod_af_proveedores" name="cod_af_proveedores" class="selectpicker" data-style="btn btn-primary">
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
        //$('#cod_tiposbienes_containers').append('<select name="cod_tiposbienes" id="cod_tiposbienes" class="selectpicker" data-style="btn btn-primary"></select>');
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
        select.attr("data-style","btn btn-primary");
        select.addClass("selectpicker");

        //id="cod_tiposbienes" class="selectpicker" data-style="btn btn-primary"
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

function ajaxCodigoActivo(combo){
    var codRubro=combo.value;
    console.log("rubro: "+codRubro);
    var contenedor = document.getElementById('divCodigoAF');
    ajax=nuevoAjax();
    ajax.open('GET', 'activosFijos/ajaxCodigoActivoFijo.php?codigo='+codRubro,true);
    ajax.onreadystatechange=function() {
        if (ajax.readyState==4) {
            contenedor.innerHTML = ajax.responseText;
        }
    }
    ajax.send(null)
}
</script>