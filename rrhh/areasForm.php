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
    $stmt = $dbh->prepare("SELECT * FROM AREAS where codigo =:codigo");
    //Ejecutamos;
    $stmt->bindParam(':codigo',$codigo);
    $stmt->execute();
    $result = $stmt->fetch();
    $codigo = $result['codigo'];
    $nombre = $result['nombre'];
    $abreviatura = $result['abreviatura'];
    $observaciones = $result['observaciones'];
    $cod_estadoreferencial = $result['cod_estadoreferencial'];
    $created_at = $result['created_at'];
    $created_by = $result['created_by'];
    $modified_at = $result['modified_at'];
    $modified_by = $result['modified_by'];
} else {
  $codigo = 0;
    $nombre = ' ';
    $abreviatura = ' ';
    $observaciones = ' ';
    $cod_estadoreferencial = ' ';
    $created_at = ' ';
    $created_by = ' ';
    $modified_at = ' ';
    $modified_by = ' ';
}
?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveAreas;?>" method="post">
      <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?>  <?=$nombreSingularArea;?></h4>
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
    <label class="col-sm-2 col-form-label">Observaciones</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="observaciones" id="observaciones" required="true" value="<?=$observaciones;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo observaciones -->
<div class="row">
    <label class="col-sm-2 col-form-label">Estado Referencial</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="cod_estadoreferencial" id="cod_estadoreferencial" required="true" value="<?=$cod_estadoreferencial;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo cod_estadoreferencial -->






			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlListAreas;?>" class="<?=$buttonCancel;?>">Cancelar</a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>