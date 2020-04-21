<?php
require_once '../conexion.php';
require_once 'configModule.php';

//header('Content-Type: application/json');

$codigo = $_GET["codigo"];
if($codigo=='E'){?><!-- empresa -->
	<div class="row">
		<label class="col-sm-3 col-form-label">Nombre *</label>
		<div class="col-sm-9">
		    <div class="form-group" >
		        <input type="text" class="form-control" name="nombre_empresa" id="nombre_empresa" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">  
		    </div>
		</div>
	</div>
	<input type="hidden" name="nombre_persona" id="nombre_persona">
    <input type="hidden" name="paterno_persona" id="paterno_persona">
    <input type="hidden" name="materno_persona" id="materno_persona">
    <input type="hidden" name="tipo_id" id="tipo_id">
    <input type="hidden" name="tipo_id_otro" id="tipo_id_otro">

<?php }else{?><!-- persona -->
	<input type="hidden" name="nombre_empresa" id="nombre_empresa">
	<div class="row">
		<label class="col-sm-3 col-form-label">Nombre *</label>
		<div class="col-sm-9">
		    <div class="form-group" >
		        <input type="text" class="form-control" name="nombre_persona" id="nombre_persona" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"> 
		    </div>
		</div>
	</div>
	<div class="row">
		<label class="col-sm-3 col-form-label">Paterno *</label>
		<div class="col-sm-9">
		    <div class="form-group" >
		        <input type="text" class="form-control" name="paterno_persona" id="paterno_persona" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
		    </div>
		</div>
	</div>
	<div class="row">
		<label class="col-sm-3 col-form-label">Materno *</label>
		<div class="col-sm-9">
		    <div class="form-group" >
		        <input type="text" class="form-control" name="materno_persona" id="materno_persona" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
		    </div>
		</div>
	</div>
	<div class="row">
		<label class="col-sm-3 col-form-label">Tipo ID *</label>
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
		<label class="col-sm-3 col-form-label">Tipo ID *</label>
		<div class="col-sm-9">
		    <div class="form-group" >
		        <input type="text" class="form-control" name="tipo_id_otro" id="tipo_id_otro" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
		    </div>
		</div>
	</div>
	
	
<?php }
?>
