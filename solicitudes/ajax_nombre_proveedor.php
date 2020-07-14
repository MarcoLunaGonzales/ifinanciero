<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once 'configModule.php';

//header('Content-Type: application/json');
$dbh = new Conexion();

$codigo = $_GET["codigo"];
$listaCiudad= obtenerDepartamentoServicioIbrnorca(26);
if($codigo=='E'){?><!-- empresa -->
	<div class="row">
		<label class="col-sm-3 col-form-label">Nombre <b class="text-danger">*</b></label>
		<div class="col-sm-9">
		    <div class="form-group" >
		        <input type="text" class="form-control" name="nombre_empresa" id="nombre_empresa" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">  
		    </div>
		</div>
	</div>
	<div class="row">
      	<label class="col-sm-3 col-form-label">NIT <b class="text-danger">*</b></label>
      	<div class="col-sm-9">
	        <div class="form-group">
	          <input class="form-control" type="number" name="identificacion" id="identificacion" required="true"/>
	        </div>
	    </div>
    </div>     
	<input type="hidden" name="nombre_persona" id="nombre_persona">
    <input type="hidden" name="paterno_persona" id="paterno_persona">
    <input type="hidden" name="materno_persona" id="materno_persona">
    <input type="hidden" name="tipo_id" id="tipo_id">
    <input type="hidden" name="tipo_id_otro" id="tipo_id_otro">
    <input type="hidden" name="emision" id="emision">
    <input type="hidden" name="emision_otro" id="emision_otro">
    <input type="hidden" name="numero_celular" id="numero_celular">

<?php }else{?><!-- persona -->
	<input type="hidden" name="nombre_empresa" id="nombre_empresa">
	<div class="row">
		<label class="col-sm-3 col-form-label">Nombre <b class="text-danger">*</b></label>
		<div class="col-sm-9">
		    <div class="form-group" >
		        <input type="text" class="form-control" name="nombre_persona" id="nombre_persona" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"> 
		    </div>
		</div>
	</div>
	<div class="row">
		<label class="col-sm-3 col-form-label">Paterno <b class="text-danger">*</b></label>
		<div class="col-sm-9">
		    <div class="form-group" >
		        <input type="text" class="form-control" name="paterno_persona" id="paterno_persona" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
		    </div>
		</div>
	</div>
	<div class="row">
		<label class="col-sm-3 col-form-label">Materno <b class="text-danger">*</b></label>
		<div class="col-sm-9">
		    <div class="form-group" >
		        <input type="text" class="form-control" name="materno_persona" id="materno_persona" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
		    </div>
		</div>
	</div>
	<div class="row">
		<label class="col-sm-3 col-form-label">Tipo ID <b class="text-danger">*</b></label>
		<div class="col-sm-9">
		    <div class="form-group" >
		        <select class="selectpicker form-control form-control-sm" name="tipo_id" id="tipo_id" data-style="btn btn-info">
                      <?php 
                      $query="SELECT * FROM tipos_identificacion_personal order by nombre";
                      $stmt = $dbh->prepare($query);
                      $stmt->execute();
                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {                        
                        ?><option value="<?=$row['codigo']?>"><?=$row['nombre']?></option>
                       <?php 
                       } ?> 
                </select>
		    </div>
		</div>
	</div>
	<div class="row">
      	<label class="col-sm-3 col-form-label">Identificación <b class="text-danger">*</b></label>
      	<div class="col-sm-9">
	        <div class="form-group">
	          <input class="form-control" type="number" name="identificacion" id="identificacion" required="true"/>
	        </div>
	    </div>
    </div> 
	<div class="row">
		<label class="col-sm-3 col-form-label">ID Otro </label>
		<div class="col-sm-9">
		    <div class="form-group" >
		        <input type="text" class="form-control" name="tipo_id_otro" id="tipo_id_otro" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
		    </div>
		</div>
	</div>
	<div class="row">
		<label class="col-sm-3 col-form-label">Lugar Emisión <b class="text-danger">*</b></label>
		<div class="col-sm-9">
		    <div class="form-group" >
		        <select name="emision" id="emision" class="form-control form-control-sm selectpicker" data-style="btn btn-info" required="true">
		        	<option disabled selected value="">--Seleccione--</option>
                 <?php
                      foreach ($listaCiudad->lista as $listas) {
                          echo "<option value=".$listas->idEstado.">".$listas->estNombre."</opction>";
                      }?>
                  </select>
		    </div>
		</div>
	</div>
	<div class="row">
		<label class="col-sm-3 col-form-label">Emisión Otro </label>
		<div class="col-sm-9">
		    <div class="form-group" >
		        <input type="text" class="form-control" name="emision_otro" id="emision_otro" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
		    </div>
		</div>
	</div>
	
	
<?php }
?>
