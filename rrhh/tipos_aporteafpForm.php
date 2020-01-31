<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();


//por is es edit
if ($codigo > 0){
    //EDIT GET1 no guardar, sino obtener
    $codigo=$codigo;    
    $stmt = $dbh->prepare("SELECT * FROM tipos_aporteafp  where codigo =:codigo");
    //Ejecutamos;
    $stmt->bindParam(':codigo',$codigo);
    $stmt->execute();
    $result = $stmt->fetch();
    $codigo = $result['codigo'];
    $nombre = $result['nombre'];
    $abreviatura = $result['abreviatura'];
    $cod_estadoreferencial = $result['cod_estadoreferencial'];
    $porcentaje_aporte = $result['porcentaje_aporte'];
    $porcentaje_riesgoprofesional = $result['porcentaje_riesgoprofesional'];
    $porcentaje_provivienda = $result['porcentaje_provivienda'];

} else {
  $codigo = 0;
  $nombre = ' ';
    $abreviatura = ' ';
    $cod_estadoreferencial = ' ';
    $porcentaje_aporte = ' ';
    $porcentaje_riesgoprofesional = ' ';
    $porcentaje_provivienda = ' ';
}
?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveTipos_aporteafp;?>" method="post">
  
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?>  <?=$nombreSingularTipos_aporteafp;?></h4>
				</div>
			  </div>
			  <div class="card-body ">
				

              <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>

<div class="row">
    <label class="col-sm-2 col-form-label">Nombre</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="nombre" id="nombre" required="true" value="<?=$nombre;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo nombre -->
<div class="row">
    <label class="col-sm-2 col-form-label">Abreviatura</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="abreviatura" id="abreviatura" required="true" value="<?=$abreviatura;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo abreviatura -->
<div class="row">
    <label class="col-sm-2 col-form-label">Porcentaje Aporte</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="porcentaje_aporte" id="porcentaje_aporte" required="true" value="<?=$porcentaje_aporte;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo porcentaje_aporte -->
<div class="row">
    <label class="col-sm-2 col-form-label">Porcentaje Riesgo Profesional</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="porcentaje_riesgoprofesional" id="porcentaje_riesgoprofesional" required="true" value="<?=$porcentaje_riesgoprofesional;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo porcentaje_riesgoprofesional -->
<div class="row">
    <label class="col-sm-2 col-form-label">Porcentaje Provivienda</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="porcentaje_provivienda" id="porcentaje_provivienda" required="true" value="<?=$porcentaje_provivienda;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo porcentaje_provivienda -->





			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlListTipos_aporteafp;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>