<?php
require_once '../conexion.php';
require_once 'configModule.php';

//header('Content-Type: application/json');

$codigo = $_GET["codigo"];
if($codigo=='E'){?><!-- empresa -->	
    <div class="row">
      <label class="col-sm-3 col-form-label">Correo <b class="text-danger">*</b></label>
      <div class="col-sm-9">
        <div class="form-group">
          <input type="text" class="form-control" name="correo_empresa" id="correo_empresa" value="" required="true">
        </div>
      </div>
    </div>
     <center><h4 class="fontweight-bold text-muted">Datos del contacto </h4></center>
    <div class="row">
       <label class="col-sm-3 col-form-label">Nombre Contacto <b class="text-danger">*</b></label>
       <div class="col-sm-9">
        <div class="form-group">
          <input type="text" class="form-control" name="nombre_contacto" id="nombre_contacto" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
        </div>
       </div>
    </div>
    <div class="row">
       <label class="col-sm-3 col-form-label">Apellido Contacto <b class="text-danger">*</b></label>
       <div class="col-sm-9">
        <div class="form-group">
          <input type="text" class="form-control" name="apellido_contacto" id="apellido_contacto" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
        </div>
       </div>
    </div>
    <div class="row">
       <label class="col-sm-3 col-form-label">Cargo Contacto <b class="text-danger">*</b></label>
       <div class="col-sm-9">
        <div class="form-group">
          <input type="text" class="form-control" name="cargo_contacto" id="cargo_contacto" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
        </div>
       </div>
    </div>
    <div class="row">
       <label class="col-sm-3 col-form-label">Correo Contacto <b class="text-danger">*</b></label>
       <div class="col-sm-9">
        <div class="form-group">
          <input type="text" class="form-control" name="correo_contacto" id="correo_contacto" value="" required="true">
        </div>
       </div>
    </div>                      
	<input type="hidden" name="numero_celular" id="numero_celular">

<?php }else{?><!-- persona -->
	<input type="hidden" name="nombre_contacto" id="nombre_contacto">
	<input type="hidden" name="apellido_contacto" id="apellido_contacto">
	<input type="hidden" name="cargo_contacto" id="cargo_contacto">
	<input type="hidden" name="correo_contacto" id="correo_contacto">

	<div class="row">
      <label class="col-sm-3 col-form-label">Num. Celular </label>
      <div class="col-sm-9">
        <div class="form-group">
          <input type="text" class="form-control" name="numero_celular" id="numero_celular" value="" required="true">
        </div>
      </div>
    </div>
	<div class="row">
      <label class="col-sm-3 col-form-label">Correo <b class="text-danger">*</b></label>
      <div class="col-sm-9">
        <div class="form-group">
          <input type="text" class="form-control" name="correo_empresa" id="correo_empresa" value="" required="true">
        </div>
      </div>
    </div>
	
	
<?php }
?>
