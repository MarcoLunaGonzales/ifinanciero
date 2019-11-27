<?php 
	require_once '../conexion.php';
	require_once '../functions.php';
	require_once '../styles.php';

	$dbh = new Conexion();

	session_start();
	
	$nombre=$_GET["nombre"];
	$id=$_GET["id"];
	$divContenedor=$_GET["divContenedor"];

	$globalUsuario=$_SESSION["globalUser"];
	$urlServer=$_SESSION["globalServerArchivos"];

?>
<input type="hidden" name="id" id="id" value="<?=$id;?>">
<input type="hidden" name="divContenedor" id="divContenedor" value="<?=$divContenedor;?>">
<input type="hidden" name="url_server" id="url_server" value="<?=$urlServer;?>">


<input type="hidden" name="idD" id="idD" value="13">
<input type="hidden" name="idR" id="idR" value="<?=$id;?>">
<input type="hidden" name="idusr" id="idusr" value="<?=$globalUsuario;?>">
<input type="hidden" name="Tipodoc" id="Tipodoc" value="176">
<input type="hidden" name="descripcion" id="descripcion" value="archivosis">
<input type="hidden" name="codigo" id="codigo" value="<?=$idSIS;?>">
<input type="hidden" name="observacion" id="observacion" value="-">
<input type="hidden" name="r" id="r" value="http://www.google.com">
<input type="hidden" name="v" id="v" value="true">

<div class="col-sm-12">
  <div class="card">
    <div class="card-header card-header-text <?=$colorCardDetail?>">
      <div class="card-text">
      	<h4><?=$nombre;?></h4>
      </div>
	  	
      	<div class="row">
	        <div class="col-md-12 col-sm-12">
	          <div class="fileinput fileinput-new text-center" data-provides="fileinput">
	            <div class="fileinput-new thumbnail">
	              <img src="assets/img/upload3.png" alt="...">
	            </div>
	            <div class="fileinput-preview fileinput-exists thumbnail text-center text-primary font-weigth-bold"></div>
	            <div>
	              <span class="btn btn-rose btn-sm btn-file">
	                <span class="fileinput-new">Cargar Archivo</span>
	                <span class="fileinput-exists">Cambiar</span>
	                <input type="file" name="archivito" required="true" />
	              </span>
	              <a href="#pablo" class="btn btn-danger btn-sm fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i>Remover</a>
	            </div>
	          </div>
	        </div>
	    </div>
	    <div id="mensaje"></div>
    </div>
  </div>
</div>