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
       
$lista= obtenerPaisesServicioIbrnorca();



?>                  

<center><h4 class="fontweight-bold text-muted">Datos del Proveedor</h4></center>
<div class="row">
   <label class="col-sm-3 col-form-label">Tipo Proveedor <b class="text-danger">*</b></label>
   <div class="col-sm-9">
    <div class="form-group">
      <select name="tipo_empresa" id="tipo_empresa" class="form-control form-control-sm selectpicker" data-style="btn btn-primary" required="true" onchange="ajaxTipoProveedorPersonaSol(this)">
        <option value="E">EMPRESA</option>
        <option value="P">PERSONA</option>
      </select>
    </div>
   </div>
  </div>
  <div class="row">
   <label class="col-sm-3 col-form-label">Clase <b class="text-danger">*</b></label>
   <div class="col-sm-9">
    <div class="form-group">
      <select name="nacional_empresa" id="nacional_empresa" class="form-control form-control-sm selectpicker" data-style="btn btn-primary" required="true">
        <option value="N">NACIONAL</option>
        <option value="I">INTERNACIONAL</option>
      </select>
    </div>
   </div>
  </div>
  <div id="div_nombre_proveedor">
    <div class="row">
      <label class="col-sm-3 col-form-label">Nombre <b class="text-danger">*</b></label>
      <div class="col-sm-9">
        <div class="form-group" >
            <input type="text" class="form-control" name="nombre_empresa" id="nombre_empresa" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
            <input type="hidden" name="nombre_persona" id="nombre_persona">
            <input type="hidden" name="paterno_persona" id="paterno_persona">
            <input type="hidden" name="materno_persona" id="materno_persona"> 
            <input type="hidden" name="tipo_id" id="tipo_id">
            <input type="hidden" name="tipo_id_otro" id="tipo_id_otro">
            <input type="hidden" name="emision" id="emision">
            <input type="hidden" name="emision_otro" id="emision_otro">
            <input type="hidden" name="numero_celular" id="numero_celular">
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
  </div>

  
  <div class="row">
   <label class="col-sm-3 col-form-label">Pais <b class="text-danger">*</b></label>
   <div class="col-sm-9">
    <div class="form-group">
      <select name="pais_empresa" id="pais_empresa" onchange="seleccionarDepartamentoServicio()" class="form-control form-control-sm selectpicker" data-style="btn btn-info" required="true">
        <option disabled selected value="">--SELECCIONE--</option>
         <?php
              foreach ($lista->lista as $listas) {
                  echo "<option value=".$listas->idPais.">".$listas->paisNombre."</opction>";
              }?>
              
      </select>
    </div>
   </div>
  </div>
  <div class="row">
   <label class="col-sm-3 col-form-label">Departamento / Estados <b class="text-danger">*</b></label>
   <div class="col-sm-9">
    <div class="form-group">
      <select name="departamento_empresa" onchange="seleccionarCiudadServicio()" id="departamento_empresa" class="form-control form-control-sm selectpicker" data-style="btn btn-info" required="true">
      </select>
    </div>
   </div>
  </div>
  <div class="row">
   <label class="col-sm-3 col-form-label">Ciudad <b class="text-danger">*</b></label>
   <div class="col-sm-9">
    <div class="form-group">
      <select name="ciudad_empresa" onchange="mostrarOtraCiudadServicio()" id="ciudad_empresa" class="form-control form-control-sm selectpicker" data-style="btn btn-success" >
      </select>
    </div>
   </div>
  </div>
  <div class="row d-none" id="otra_ciudad_div">
   <label class="col-sm-3 col-form-label">Otra ciudad <b class="text-danger">*</b></label>
   <div class="col-sm-9">
    <div class="form-group">
      <input type="text" class="form-control" name="otra_ciudad" id="otra_ciudad" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
    </div>
   </div>
  </div>
  <div class="row">
   <label class="col-sm-3 col-form-label">Direcci&oacute;n <b class="text-danger">*</b></label>
   <div class="col-sm-9">
    <div class="form-group">
      <input type="text" class="form-control" name="direccion_empresa" id="direccion_empresa" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
    </div>
   </div>
  </div>
  <div class="row">
   <label class="col-sm-3 col-form-label">Telefono <b class="text-danger">*</b></label>
   <div class="col-sm-9">
    <div class="form-group">
      <input type="text" class="form-control" name="telefono_empresa" id="telefono_empresa" value="" required="true">
    </div>
   </div>
  </div>
  <div id="div_datos_add_proveedor">                        
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
  </div>
                      
                      

