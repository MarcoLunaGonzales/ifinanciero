<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
$cod_cliente=$_GET['cod_cliente'];
$cod_personal=$_GET['cod_personal'];
// $cod_cc=$_GET['cod_cc'];
// $cod_dcc=$_GET['cod_dcc'];
       
$lista= obtenerDepartamentoServicioIbrnorca(26);

?>                  
<input class="form-control" type="hidden" name="cod_cliente" id="cod_cliente" value="<?=$cod_cliente?>" />
<input class="form-control" type="hidden" name="cod_personal" id="cod_personal" value="<?=$cod_personal?>" />

<center><h4 class="fontweight-bold text-muted">Datos del contacto</h4></center>    
<div class="row">
 <label class="col-sm-3 col-form-label">Nombre Contacto *</label>
 <div class="col-sm-9">
  <div class="form-group">
    <input type="text" class="form-control" name="nombre_contacto" id="nombre_contacto" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
  </div>
 </div>
</div>
<div class="row">
 <label class="col-sm-3 col-form-label">Paterno *</label>
 <div class="col-sm-9">
  <div class="form-group">
    <input type="text" class="form-control" name="paterno_contacto" id="paterno_contacto" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
  </div>
 </div>
</div>
<div class="row">
 <label class="col-sm-3 col-form-label">Materno *</label>
 <div class="col-sm-9">
  <div class="form-group">
    <input type="text" class="form-control" name="materno_contacto" id="materno_contacto" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
  </div>
 </div>
</div>
<div class="row">
 <label class="col-sm-3 col-form-label">CI o Identificación </label>
 <div class="col-sm-9">
  <div class="form-group">
    <input type="number" class="form-control" name="identificacion_contacto" id="identificacion_contacto" value="" required="true">
  </div>
 </div>
</div>



<div class="row">
   <label class="col-sm-3 col-form-label">Lugar Emisión </label>
   <div class="col-sm-9">
    <div class="form-group">
      <select name="departamento_contacto" id="departamento_contacto"  class="form-control form-control-sm selectpicker" data-style="btn btn-info" required="true">
        <option disabled selected value="">--Seleccione--</option>
           <?php
                foreach ($lista->lista as $listas) {
                    echo "<option value=".$listas->idEstado.">".$listas->estNombre."</opction>";
                }?>

      </select>
    </div>
   </div>
  </div>
  




<!-- <div class="row"> -->
  <input type="hidden" name="pais_contacto" id="pais_contacto" value="26" ><!-- 26 para bolivia -->
  
<!-- </div> -->
<div class="row">
 <label class="col-sm-3 col-form-label">Cargo Contacto *</label>
 <div class="col-sm-9">
  <div class="form-group">
    <input type="text" class="form-control" name="cargo_contacto" id="cargo_contacto" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
  </div>
 </div>
</div>
<div class="row">
 <label class="col-sm-3 col-form-label">Teléfono o Celular </label>
 <div class="col-sm-9">
  <div class="form-group">
    <input type="number" class="form-control" name="telefono_contacto" id="telefono_contacto" value="" required="true">
  </div>
 </div>
</div>
<div class="row">
 <label class="col-sm-3 col-form-label">Correo Contacto </label>
 <div class="col-sm-9">
  <div class="form-group">
    <input type="text" class="form-control" name="correo_contacto" id="correo_contacto" value="" required="true">
  </div>
 </div>
</div>
                
                      


                      

