
<?php

require_once 'conexion.php';
require_once 'styles.php';

$dbh = new Conexion();
//IMAGEN
$stmtIMG = $dbh->prepare("SELECT * FROM activosfijosimagen where codigo =$codigo");
$stmtIMG->execute();
$resultIMG = $stmtIMG->fetch();
if(isset($resultIMG['imagen'])){
	$imagen = $resultIMG['imagen'];    
}else{
	$imagen = "";
}

$archivo = "activosfijos/imagenes/".$imagen;

?>
<div class="content">
	<div class="container-fluid">
        <div style="overflow-y:scroll;">
            <div class="col-md-12">
                <form id="form1" class="form-horizontal" action="?opcion=activofijoCargarImagen_save" method="post"  enctype="multipart/form-data">
        			<div class="card">
                        <div class="card-header <?=$colorCard;?> card-header-text">
                            <div class="card-text">
                              <h4 class="card-title">Cargar Imagen Activo Fijo</h4>
                            </div>
                        </div>
                        <div class="card-body ">
                            <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                                        <div class="fileinput-new img-raised">
                                            <img src="<?=$archivo;?>" alt="..." style="width:250px;">
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail img-raised">
                                        </div>
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
                        <div class="card-footer fixed-bottom">
                            <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
                            <a href="?opcion=activosfijosLista" class="<?=$buttonCancel;?>">Volver</a>
                        </div>
        			</div>
                </form>
    		</div>
        </div>
	
	</div>
</div>

