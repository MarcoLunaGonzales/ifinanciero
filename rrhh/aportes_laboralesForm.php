<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();

$codigo=$_GET['codigo'];
if ($codigo > 0){
    //EDIT GET1 no guardar, sino obtener
    $codigo=$codigo;
    
    $stmt = $dbh->prepare("SELECT * FROM aportes_laborales where codigo =:codigo");
    //Ejecutamos;
    $stmt->bindParam(':codigo',$codigo);
    $stmt->execute();
    $result = $stmt->fetch();
    $codigo = $result['codigo'];
    $salario_minimo_nacional = $result['salario_minimo_nacional'];
    $cuenta_individual_vejez = $result['cuenta_individual_vejez'];
    $seguro_invalidez = $result['seguro_invalidez'];
    $comision_afp = $result['comision_afp'];
    $provivienda = $result['provivienda'];
    $iva = $result['iva'];
    $asa = $result['asa'];
    $aporte_nac_solidario_13 = $result['aporte_nac_solidario_13'];
    $aporte_nac_solidario_25 = $result['aporte_nac_solidario_25'];
    $aporte_nac_solidario_35 = $result['aporte_nac_solidario_35'];
    $estado = $result['estado'];
    $created_at = $result['created_at'];
    $created_by = $result['created_by'];
    $modified_at = $result['modified_at'];
    $modified_by = $result['modified_by'];

} else {
    $codigo = 0;
    $salario_minimo_nacional = ' ';
    $cuenta_individual_vejez = ' ';
    $seguro_invalidez = ' ';
    $comision_afp = ' ';
    $provivienda = ' ';
    $iva = ' ';
    $asa = ' ';
    $aporte_nac_solidario_13 = ' ';
    $aporte_nac_solidario_25 = ' ';
    $aporte_nac_solidario_35 = ' ';
    $estado = ' ';
    $created_at = ' ';
    $created_by = ' ';
    $modified_at = ' ';
    $modified_by = ' ';
}


//por is es edit



?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveaportes_laborales;?>" method="post">
    
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?>  <?=$nombreSingularaportes_laborales;?></h4>
				</div>
			  </div>
			  <div class="card-body ">
				

          
              <div class="row">
    <label class="col-sm-2 col-form-label">Salario Minimo Nacional</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
        <input class="form-control" type="text" name="salario_minimo_nacional" id="salario_minimo_nacional" required="true" value="<?=$salario_minimo_nacional;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo salario_minimo_nacional -->
<div class="row">
    <label class="col-sm-2 col-form-label">Cuenta Individual de Vejez</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="cuenta_individual_vejez" id="cuenta_individual_vejez" required="true" value="<?=$cuenta_individual_vejez;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo cuenta_individual_vejez -->
<div class="row">
    <label class="col-sm-2 col-form-label">Seguro Invalidez</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="seguro_invalidez" id="seguro_invalidez" required="true" value="<?=$seguro_invalidez;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo seguro_invalidez -->
<div class="row">
    <label class="col-sm-2 col-form-label">Comision AFP</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="comision_afp" id="comision_afp" required="true" value="<?=$comision_afp;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo comision_afp -->
<div class="row">
    <label class="col-sm-2 col-form-label">Pro vivienda</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="provivienda" id="provivienda" required="true" value="<?=$provivienda;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo provivienda -->
<div class="row">
    <label class="col-sm-2 col-form-label">Iva</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="iva" id="iva" required="true" value="<?=$iva;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo iva -->
<div class="row">
    <label class="col-sm-2 col-form-label">Asa</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="asa" id="asa" required="true" value="<?=$asa;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo asa -->
<div class="row">
    <label class="col-sm-2 col-form-label">Aporte Nacional Solidario 13</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="aporte_nac_solidario_13" id="aporte_nac_solidario_13" required="true" value="<?=$aporte_nac_solidario_13;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo aporte_nac_solidario_13 -->
<div class="row">
    <label class="col-sm-2 col-form-label">Aporte Nacional Solidario 25</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="aporte_nac_solidario_25" id="aporte_nac_solidario_25" required="true" value="<?=$aporte_nac_solidario_25;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo aporte_nac_solidario_25 -->
<div class="row">
    <label class="col-sm-2 col-form-label">Aporte Nacional Solidario 35</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="aporte_nac_solidario_35" id="aporte_nac_solidario_35" required="true" value="<?=$aporte_nac_solidario_35;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo aporte_nac_solidario_35 -->

			  </div>





			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<!--<a href="<?=$urlListAreas;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>-->
			  </div>
			</div>
		  </form>
		</div>





	</div>
    
</div>