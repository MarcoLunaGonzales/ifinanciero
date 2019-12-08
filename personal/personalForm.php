<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'rrhh/configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();


//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

//por is es edit
if ($codigo > 0){
    $codigo=$codigo;
    $stmt = $dbh->prepare("SELECT * FROM personal_datos where codigo =:codigo");
    //Ejecutamos;
    $stmt->bindParam(':codigo',$codigo);
    $stmt->execute();
    $result = $stmt->fetch();
    $codigo = $result['codigo'];
    $ci = $result['ci'];
    $ci_lugar_emision = $result['ci_lugar_emision'];
    $fecha_nacimiento = $result['fecha_nacimiento'];
    $cod_cargo = $result['cod_cargo'];//cb
    $cod_unidadorganizacional = $result['cod_unidadorganizacional'];//cb
    $cod_area = $result['cod_area'];
    $jubilado = $result['jubilado'];
    $cod_genero = $result['cod_genero'];//cb
    $cod_tipopersonal = $result['cod_tipopersonal'];//cb
    $haber_basico = $result['haber_basico'];
    $paterno = $result['paterno'];
    $materno = $result['materno'];
    $apellido_casada = $result['apellido_casada'];
    $primer_nombre = $result['primer_nombre'];
    $otros_nombres = $result['otros_nombres'];
    $nua_cua_asignado = $result['nua_cua_asignado'];
    $direccion = $result['direccion'];
    $cod_tipoafp = $result['cod_tipoafp'];//
    $cod_tipoaporteafp = $result['cod_tipoaporteafp'];
    $nro_seguro = $result['nro_seguro'];
    $cod_estadopersonal = $result['cod_estadopersonal'];
    $created_at = $result['created_at'];
    $created_by = $result['created_by'];
    $modified_at = $result['modified_at'];
    $modified_by = $result['modified_by'];

    $telefono = $result['telefono'];
    $celular = $result['celular'];
    $email = $result['email'];
    $persona_contacto = $result['persona_contacto'];
} else {//ES NUEVO
  $codigo = 0;
  $ci = ' ';
    $ci_lugar_emision = ' ';
    $fecha_nacimiento = ' ';
    $cod_cargo = ' ';//cb
    $cod_unidadorganizacional = ' ';//cb
    $cod_area = ' ';
    $jubilado = ' ';
    $cod_genero = ' ';//cb
    $cod_tipopersonal = ' ';//cb
    $haber_basico = ' ';
    $paterno = ' ';
    $materno = ' ';
    $apellido_casada = ' ';
    $primer_nombre = ' ';
    $otros_nombres = ' ';
    $nua_cua_asignado = ' ';
    $direccion = ' ';
    $cod_tipoafp = ' ';//
    $cod_tipoaporteafp = ' ';
    $nro_seguro = ' ';
    $cod_estadopersonal = ' ';
    $created_at = ' ';
    $created_by = ' ';
    $modified_at = ' ';
    $modified_by = ' ';

    $telefono = ' ';
    $celular = ' ';
    $email = ' ';
    $persona_contacto = ' ';
}

//COMBOS...
$queryUO = "select * from unidades_organizacionales";
$statementUO = $dbh->query($queryUO);

$queryCargos = "select * from cargos";
$statementCargos = $dbh->query($queryCargos);

$queryTGenero = "select * from tipos_genero";
$statementTgenero = $dbh->query($queryTGenero);

$queryTPersonal = "select * from tipos_personal";
$statementTPersonal = $dbh->query($queryTPersonal);

$querytipos_afp = "select * from tipos_afp";
$statementtipos_afp = $dbh->query($querytipos_afp);

$querytipos_aporteafp = "select * from tipos_aporteafp";
$statementtipos_aporteafp = $dbh->query($querytipos_aporteafp);


$queryestados_personal = "select * from estados_personal";
$statementestados_personal = $dbh->query($queryestados_personal);
?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSavePersonal;?>" method="post"  enctype="multipart/form-data">
            <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?>  <?=$nombreSingularPersonal;?></h4>
				</div>
			  </div>
			  <div class="card-body ">
				

              <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>

<div class="row">
    <label class="col-sm-2 col-form-label">Imagen</label>
    <div class="col-sm-7">
        <div class="form-group">
           
        <div class="fileinput fileinput-new text-center" data-provides="fileinput">
        <div class="fileinput-new img-raised">
            <img src="rrhh/imagenes/<?=$codigo;?>.jpg" alt="..." style="width:250px;">
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

 
  
</div><!--fin campo ci_lugar_emision -->
<div class="row">
    <label class="col-sm-2 col-form-label">Ci</label>
    <div class="col-sm-4">
        <div class="form-group">
            <input class="form-control" type="text" name="ci" id="ci" required="true" value="<?=$ci;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
        </div>
    </div>

    <label class="col-sm-2 col-form-label">Lugar Emision</label>
    <div class="col-sm-4">
    <div class="form-group">
    <select name="ci_lugar_emision"  class="selectpicker " data-style="btn btn-info">
        <option <?php if($ci_lugar_emision == 'LP') echo "selected"; ?> value="LP">LA PAZ</option>
        <option <?php if($ci_lugar_emision == 'CH') echo "selected"; ?> value="CH">CHUQUISACA</option>
        <option <?php if($ci_lugar_emision == 'OR') echo "selected"; ?> value="OR">ORURO</option>
        <option <?php if($ci_lugar_emision == 'SC') echo "selected"; ?> value="SC">SANTA CRUZ</option>
        <option <?php if($ci_lugar_emision == 'CO') echo "selected"; ?> value="CO">COCHABAMBAS</option>


	</select>			
				
       
    </div>
    </div>
</div><!--fin campo ci_lugar_emision -->
<div class="row">
    <label class="col-sm-2 col-form-label">Fecha Nacimiento</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="date" name="fecha_nacimiento" id="fecha_nacimiento" required="true" value="<?=$fecha_nacimiento;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>

    <label class="col-sm-2 col-form-label">Cargo</label>
    <div class="col-sm-4">
    <div class="form-group">
    <select name="cod_cargo"  class="selectpicker " data-style="btn btn-info">
					<?php while ($row = $statementCargos->fetch()) { ?>
						<option <?php if($cod_cargo == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
					<?php } ?>
					</select>
       
    </div>
    </div>
</div><!--fin campo cod_cargo -->
<div class="row">
    <label class="col-sm-2 col-form-label">Unidad Organizacional</label>
    <div class="col-sm-4">
    <div class="form-group">
        <select name="cod_unidadorganizacional"  class="selectpicker " id="cod_unidadorganizacional" data-style="btn btn-info">
					<?php while ($row = $statementUO->fetch()) { ?>
						<option <?php if($cod_unidadorganizacional == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
					<?php } ?>
					</select>
       
    </div>
    </div>

    <label class="col-sm-2 col-form-label">Area</label>
    <div class="col-sm-4">
    <div class="form-group">
        <div id="cod_area_containers">
            <select name="cod_area"  class="selectpicker " id="cod_area" data-style="btn btn-info">
            </select>
            <input type="hidden" name="cod_area2" id="cod_area2" value="<?=$cod_area;?>"/>

        </div>
        </div>
    </div>
</div><!--fin campo cod_area -->
<div class="row">
    <label class="col-sm-2 col-form-label">Jubilado</label>
    <div class="col-sm-4">
    <div class="form-group">
    <select name="jubilado"  class="selectpicker " data-style="btn btn-info">
        <option <?php if($ci_lugar_emision == '1') echo "selected"; ?> value="1">SI</option>
        <option <?php if($ci_lugar_emision == '0') echo "selected"; ?> value="0">NO</option>
	</select>			

    </div>
    </div>

    <label class="col-sm-2 col-form-label">Genero</label>
    <div class="col-sm-4">
    <div class="form-group">
    <select name="cod_genero"  class="selectpicker " data-style="btn btn-info">
					<?php while ($row = $statementTgenero->fetch()) { ?>
						<option <?php if($cod_genero == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
					<?php } ?>
					</select>
        
    </div>
    </div>
</div><!--fin campo cod_genero -->
<div class="row">
    <label class="col-sm-2 col-form-label">Tipo Personal</label>
    <div class="col-sm-4">
    <div class="form-group">
    <select name="cod_tipopersonal"  class="selectpicker " data-style="btn btn-info">
					<?php while ($row = $statementTPersonal->fetch()) { ?>
						<option <?php if($cod_tipopersonal == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
					<?php } ?>
					</select>
    </div>
    </div>

    <label class="col-sm-2 col-form-label">Haber Basico</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" name="haber_basico" id="haber_basico" required="true" value="<?=$haber_basico;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo haber_basico -->
<div class="row">
    <label class="col-sm-2 col-form-label">Paterno</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" name="paterno" id="paterno" required="true" value="<?=$paterno;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>

    <label class="col-sm-2 col-form-label">Materno</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" name="materno" id="materno" required="true" value="<?=$materno;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo materno -->
<div class="row">
    <label class="col-sm-2 col-form-label">Apellido Casada</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" name="apellido_casada" id="apellido_casada" value="<?=$apellido_casada;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo apellido_casada -->
<div class="row">
    <label class="col-sm-2 col-form-label">Primer Nombre</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" name="primer_nombre" id="primer_nombre" required="true" value="<?=$primer_nombre;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>

    <label class="col-sm-2 col-form-label">Otros Nombres</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" name="otros_nombres" id="otros_nombres" value="<?=$otros_nombres;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo otros_nombres -->
<div class="row">
    <label class="col-sm-2 col-form-label">Nua / Cua Asignado</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" name="nua_cua_asignado" id="nua_cua_asignado" required="true" value="<?=$nua_cua_asignado;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo nua_cua_asignado -->
<div class="row">
    <label class="col-sm-2 col-form-label">Direccion</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="direccion" id="direccion" required="true" value="<?=$direccion;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo direccion -->
<div class="row">
    <label class="col-sm-2 col-form-label">Afp</label>
    <div class="col-sm-4">
    <div class="form-group">
        <select name="cod_tipoafp" id="cod_tipoafp"  class="selectpicker " data-style="btn btn-info">
					<?php while ($row = $statementtipos_afp->fetch()) { ?>
						<option <?php if($cod_tipoafp == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
					<?php } ?>
					</select>
    </div>
    </div>

    <label class="col-sm-2 col-form-label">Tipo de Aporte AFP</label>
    <div class="col-sm-4">
    <div class="form-group">
        <select name="cod_tipoaporteafp" id="cod_tipoaporteafp"  class="selectpicker " data-style="btn btn-info">
					<?php while ($row = $statementtipos_aporteafp->fetch()) { ?>
						<option <?php if($cod_tipoaporteafp == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
					<?php } ?>
					</select>
    </div>
    </div>
</div><!--fin campo cod_tipoaporteafp -->
<div class="row">
    <label class="col-sm-2 col-form-label">Nro seguro</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" name="nro_seguro" id="nro_seguro" required="true" value="<?=$nro_seguro;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>

    <label class="col-sm-2 col-form-label">Estado</label>
    <div class="col-sm-4">
    <div class="form-group">
    <select name="cod_estadopersonal"  class="selectpicker " data-style="btn btn-info">
					<?php while ($row = $statementestados_personal->fetch()) { ?>
						<option <?php if($cod_estadopersonal == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
					<?php } ?>
					</select>
      
    </div>
    </div>
</div><!--fin campo cod_estadopersonal -->
<div class="row">
    <label class="col-sm-2 col-form-label">Telefono</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" name="telefono" id="telefono" required="true" value="<?=$telefono;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>

    <label class="col-sm-2 col-form-label">Celular</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" name="celular" id="celular" required="true" value="<?=$celular;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo celular -->
<div class="row">
    <label class="col-sm-2 col-form-label">Email</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="email" id="email" required="true" value="<?=$email;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo email -->
<div class="row">
    <label class="col-sm-2 col-form-label">Persona Contacto</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="persona_contacto" id="persona_contacto" required="true" value="<?=$persona_contacto;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo persona_contacto -->




			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlListPersonal;?>" class="<?=$buttonCancel;?>">Cancelar</a>
			  </div>
			</div>
		  </form>


<!-- SOLO MUESTRO SI ES EN ESTADO EDICION... -->
<?php if ($codigo > 0){ ?>

    <!-- tabs -->
    <div class="card card-nav-tabs card-plain">
        <div class="card-header <?=$colorCard;?>">
            <!-- colors: "header-primary", "header-info", "header-success", "header-warning", "header-danger" -->
            <div class="nav-tabs-navigation">
                <div class="nav-tabs-wrapper">
                    <ul class="nav nav-tabs" data-tabs="tabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="#uno" data-toggle="tab">Distribucion Salarial</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#dos" data-toggle="tab">Historico Cargos</a>
                        </li>
                        <!--
                        <li class="nav-item">
                            <a class="nav-link" href="#history" data-toggle="tab">History</a>
                        </li>
                        -->
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body ">
            <div class="tab-content text-center">
                <div class="tab-pane active" id="uno">
                    <p>I think that&#x2019;s a responsibility that I have, to push possibilities, to show people, this is the level that things could be at. So when you get something that has the name Kanye West on it, it&#x2019;s supposed to be pushing the furthest possibilities. I will be the leader of a company that ends up being worth billions of dollars, because I got the answers. I understand culture. I am the nucleus.</p>
                </div><!-- fin tab uno -->
                <div class="tab-pane" id="dos">
                    <p> I will be the leader of a company that ends up being worth billions of dollars, because I got the answers. I understand culture. I am the nucleus. I think that&#x2019;s a responsibility that I have, to push possibilities, to show people, this is the level that things could be at. I think that&#x2019;s a responsibility that I have, to push possibilities, to show people, this is the level that things could be at. </p>
                </div><!-- fin tab dos -->
                <!--
                <div class="tab-pane" id="history">
                    <p> I think that&#x2019;s a responsibility that I have, to push possibilities, to show people, this is the level that things could be at. I will be the leader of a company that ends up being worth billions of dollars, because I got the answers. I understand culture. I am the nucleus. I think that&#x2019;s a responsibility that I have, to push possibilities, to show people, this is the level that things could be at.</p>
                </div>
                -->
            </div>
        </div>
    </div>

    <!-- fin tabs-->
<?php } ?>






		</div>
	   
	</div>
</div>

<script>
 function cargarAreas(codigo){
    $.post("rrhh/areas_organizacionAjax.php", "cod_unidadorganizacional="+codigo, function (data) {
        //console.log("llega0");
        $('#cod_area').remove();//elimino totalmente el combo
        //console.log("llega1");
        $("#cod_area_containers").empty();//vacio el padre
        //console.log("llega2");
        $('#cod_area_containers').append(data);//inserto el html+data q me llego
        //console.log("llega3");
        $('.selectpicker').selectpicker();//le doy estilo
        //console.log("llega4");

        //---------------------------------------------------------------------
        //intentar seleccionar si es q hay un valor x defecto

    });
 }

 $('#cod_unidadorganizacional').on('change', function() {
  //alert( this.value );
    //cod_tiposbienes
    //console.log("llega-1");
    cargarAreas($('#cod_unidadorganizacional').val());
});

cargarAreas($('#cod_unidadorganizacional').val());
</script>