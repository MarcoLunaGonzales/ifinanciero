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
    $stmt = $dbh->prepare("select * from aportes_patronales where codigo =:codigo");
    //Ejecutamos;
    $stmt->bindParam(':codigo',$codigo);
    $stmt->execute();
    $result = $stmt->fetch();
    $codigo = $result['codigo'];

    $seguro_riesgo_profesional = $result['seguro_riesgo_profesional'];
    $provivienda = $result['provivienda'];
    $infocal = $result['infocal'];
    $cns = $result['cns'];
    $aporte_patronal_solidario = $result['aporte_patronal_solidario'];
    $estado = $result['estado'];
    $created_at = $result['created_at'];
    $created_by = $result['created_by'];
    $modified_at = $result['modified_at'];
    $modified_by = $result['modified_by'];
} else {
    $codigo = 0;
    $seguro_riesgo_profesional = ' ';
    $provivienda = ' ';
    $infocal = ' ';
    $cns = ' ';
    $aporte_patronal_solidario = ' ';
    $estado = ' ';
    $created_at = ' ';
    $created_by = ' ';
    $modified_at = ' ';
    $modified_by = ' ';
}


?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveaportes_patronales;?>" method="post">

			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?>  <?=$nombreSingularaportes_patronales;?></h4>
				</div>
			  </div>
			  <div class="card-body ">
				

              <div class="row">
                <label class="col-sm-2 col-form-label">Seguro Riesgo Profesional</label>
                <div class="col-sm-7">
                <div class="form-group">
                    <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
                    <input class="form-control" type="text" name="seguro_riesgo_profesional" id="seguro_riesgo_profesional" required="true" value="<?=$seguro_riesgo_profesional;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>

                </div>
                </div>
            </div><!--fin campo seguro_riesgo_profesional -->
            <div class="row">
                <label class="col-sm-2 col-form-label">Provivienda</label>
                <div class="col-sm-7">
                <div class="form-group">
                    <input class="form-control" type="text" name="provivienda" id="provivienda" required="true" value="<?=$provivienda;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                </div>
                </div>
            </div><!--fin campo provivienda -->
            <div class="row">
                <label class="col-sm-2 col-form-label">Infocal</label>
                <div class="col-sm-7">
                <div class="form-group">
                    <input class="form-control" type="text" name="infocal" id="infocal" required="true" value="<?=$infocal;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                </div>
                </div>
            </div><!--fin campo infocal -->
            <div class="row">
                <label class="col-sm-2 col-form-label">Cns</label>
                <div class="col-sm-7">
                <div class="form-group">
                    <input class="form-control" type="text" name="cns" id="cns" required="true" value="<?=$cns;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                </div>
                </div>
            </div><!--fin campo cns -->
            <div class="row">
                <label class="col-sm-2 col-form-label">Aporte Patronal Solidario</label>
                <div class="col-sm-7">
                <div class="form-group">
                    <input class="form-control" type="text" name="aporte_patronal_solidario" id="aporte_patronal_solidario" required="true" value="<?=$aporte_patronal_solidario;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                </div>
                </div>
            </div><!--fin campo aporte_patronal_solidario -->






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