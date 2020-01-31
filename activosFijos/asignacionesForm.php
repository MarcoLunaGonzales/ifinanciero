<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();

$codigo=0;
//por is es edit
if ($codigo > 0){
    //EDIT GET1 no guardar, sino obtener
    $codigo=$codigo;
    $stmt = $dbh->prepare("SELECT * FROM activofijos_asignaciones where codigo =:codigo");
    //Ejecutamos;
    $stmt->bindParam(':codigo',$codigo);
    $stmt->execute();
    $result = $stmt->fetch();
    $codigo = $result['codigo'];
    $cod_activosfijos = $result['cod_activosfijos'];
    $fechaasignacion = $result['fechaasignacion'];
    $cod_ubicaciones = $result['cod_ubicaciones'];
    $cod_personal = $result['cod_personal'];
} else {
  $codigo = 0;
}
?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSave8;?>" method="post">
      <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Registrar <?=$moduleNameSingular8;?></h4>
				</div>
			  </div>
			  <div class="card-body ">
				

              <input type="hidden" name="codigo" id="codigo" value="<?=codigo;?>"/>

<div class="row">
    <label class="col-sm-2 col-form-label">Fecha Asignacion</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="fechaasignacion" id="fechaasignacion" required="true" value="<?=$fechaasignacion;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo fechaasignacion -->
<div class="row">
    <label class="col-sm-2 col-form-label">Ubicaciones</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="cod_ubicaciones" id="cod_ubicaciones" required="true" value="<?=$cod_ubicaciones;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo cod_ubicaciones -->
<div class="row">
    <label class="col-sm-2 col-form-label">Personal</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="cod_personal" id="cod_personal" required="true" value="<?=$cod_personal;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo cod_personal -->






			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlList4;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>