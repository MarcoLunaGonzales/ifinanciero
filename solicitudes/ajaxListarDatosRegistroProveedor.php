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
<center><h4 class="font-weight-bold text-muted">Datos del Proveedor</h4></center>
<div class="row">
      <label class="col-sm-3 col-form-label">Buscar</label>
      <div class="col-sm-7">
        <div class="form-group" >
            <input type="text" class="form-control" name="nombre_empresa" id="nombre_empresa" value="" required="true" placeholder="Ingrese Nombre Completo Nit ó Identificación...">
        </div>
      </div>
        <a href="#" id="boton_actualizar_lista" class="btn btn-info btn-sm btn-fab d-none" onclick="actualizarRegistroProveedor()" title="Actualizar Lista Proveedores"><i class="material-icons">find_replace</i></a>
        <a href="#" id="boton_cambiar_a_proveedor" class="btn btn-danger btn-sm btn-fab d-none" onclick="convertirARegistroProveedor()" title="Convertir a Proveedor"><i class="material-icons">outbond</i></a>
    </div>
<div class="row">
   <label class="col-sm-3 col-form-label">Tipo Proveedor <b class="text-danger">*</b></label>
   <div class="col-sm-8">
    <div class="form-group">
      <select name="tipo_empresa" id="tipo_empresa" class="form-control selectpicker" data-style="btn btn-warning" required="true" onchange="ajaxTipoProveedorPersonaSol()">
        <option value="E">EMPRESA</option>
        <option value="P">PERSONA</option>
      </select>
    </div>
   </div>
  </div>
  <div class="row">
   <label class="col-sm-3 col-form-label">Clase <b class="text-danger">*</b></label>
   <div class="col-sm-8">
    <div class="form-group">
      <select name="nacional_empresa" id="nacional_empresa" class="form-control  selectpicker" data-style="btn btn-warning" required="true" onchange="ajaxNacionalEmpresaPersonaSol()">
        <option value="N">NACIONAL</option>
        <option value="I">INTERNACIONAL</option>
      </select>
    </div>
   </div>
  </div>
    <div class="row">
      <label class="col-sm-3 col-form-label">Nombre <b class="text-danger">*</b></label>
      <div class="col-sm-8">
        <div class="form-group">
            <input type="text" class="form-control" name="nombre_empresa_persona" id="nombre_empresa_persona" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
            <input type="hidden" id="cod_proveedor_encontrado" name="cod_proveedor_encontrado" value="0">
        </div>
      </div>
    </div> 

  <div id="div_persona_proveedor" class="d-none">
    <div class="row">
      <label class="col-sm-3 col-form-label">Paterno <b class="text-danger">*</b></label>
      <div class="col-sm-8">
        <div class="form-group" >
            <input type="text" class="form-control" name="paterno_persona" id="paterno_persona" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
        </div>
      </div>
    </div>
    <div class="row">
      <label class="col-sm-3 col-form-label">Materno</label>
      <div class="col-sm-8">
        <div class="form-group" >
            <input type="text" class="form-control" name="materno_persona" id="materno_persona" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
        </div>
      </div>
    </div>
    <div class="row">
      <label class="col-sm-3 col-form-label">CI/DNI <b class="text-danger" id="identificacion_required">*</b></label>
      <div class="col-sm-8">
        <div class="form-group" >
            <input type="text" class="form-control" name="identificacion" id="identificacion" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
        </div>
      </div>
    </div> 
  </div> 
    <div class="row">
      <label class="col-sm-3 col-form-label">NIT <b class="text-danger" id="nit_empresa_required">*</b></label>
      <div class="col-sm-8">
        <div class="form-group">
          <input class="form-control" type="number" name="nit_empresa" id="nit_empresa" required="true"/>
        </div>
      </div>
    </div>                        
  

  
  <div class="row">
   <label class="col-sm-3 col-form-label">Pais <b class="text-danger">*</b></label>
   <div class="col-sm-8">
    <div class="form-group">
      <select name="pais_empresa" id="pais_empresa" onchange="seleccionarDepartamentoServicio()" class="form-control  selectpicker" data-style="btn btn-warning" required="true">
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
   <div class="col-sm-8">
    <div class="form-group">
      <select name="departamento_empresa" onchange="seleccionarCiudadServicio()" id="departamento_empresa" class="form-control  selectpicker" data-style="btn btn-warning" required="true">
      </select>
    </div>
   </div>
  </div>
  <div class="row">
   <label class="col-sm-3 col-form-label">Ciudad</label>
   <div class="col-sm-8">
    <div class="form-group">
      <select name="ciudad_empresa" onchange="mostrarOtraCiudadServicio()" id="ciudad_empresa" class="form-control  selectpicker" data-style="btn btn-warning" >
      </select>
    </div>
   </div>
  </div>
  <div class="row d-none" id="otra_ciudad_div">
   <label class="col-sm-3 col-form-label">Otra ciudad</label>
   <div class="col-sm-8">
    <div class="form-group">
      <input type="text" class="form-control" name="otra_ciudad" id="otra_ciudad" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
    </div>
   </div>
  </div>
  <div class="row">
   <label class="col-sm-3 col-form-label">Direcci&oacute;n</label>
   <div class="col-sm-8">
    <div class="form-group">
      <input type="text" class="form-control" name="direccion_empresa" id="direccion_empresa" value="" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="(Opcional)">
    </div>
   </div>
  </div>
  <div class="row">
   <label class="col-sm-3 col-form-label">Teléfono</label>
   <div class="col-sm-8">
    <div class="form-group">
      <input type="text" class="form-control" name="telefono_empresa" id="telefono_empresa" value="" placeholder="(Opcional)">
    </div>
   </div>
  </div>
  <div id="div_datos_add_proveedor">                        
    <div class="row">
      <label class="col-sm-3 col-form-label">Correo</label>
      <div class="col-sm-8">
        <div class="form-group">
          <input type="email" class="form-control" name="correo_empresa" id="correo_empresa" value="" placeholder="(Opcional)">
        </div>
      </div>
    </div>
    <hr class="hr bg-warning">
    <div>
      <center><h4 class="font-weight-bold text-muted">Datos del contacto</h4></center>
      <div class="row">
       <label class="col-sm-3 col-form-label">Nombre Contacto</label>
       <div class="col-sm-8">
        <div class="form-group">
          <input type="text" class="form-control" name="nombre_contacto" id="nombre_contacto" value="" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="(Opcional)">
        </div>
       </div>
      </div>
      <div class="row">
       <label class="col-sm-3 col-form-label">Apellido Contacto</label>
       <div class="col-sm-8">
        <div class="form-group">
          <input type="text" class="form-control" name="apellido_contacto" id="apellido_contacto" value="" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="(Opcional)">
        </div>
       </div>
      </div>
      <div class="row">
       <label class="col-sm-3 col-form-label">Cargo Contacto</label>
       <div class="col-sm-8">
        <div class="form-group">
          <input type="text" class="form-control" name="cargo_contacto" id="cargo_contacto" value="" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="(Opcional)">
        </div>
       </div>
      </div>
      <div class="row">
       <label class="col-sm-3 col-form-label">Correo Contacto</label>
       <div class="col-sm-8">
        <div class="form-group">
          <input type="text" class="form-control" name="correo_contacto" id="correo_contacto" value="" placeholder="(Opcional)">
        </div>
       </div>
      </div>
     </div> 
  </div>
  <script>
$('#nombre_empresa').on('input', function() {
    autocompletarAJAXComplemento("nombre_empresa","autocompletar_datos_proveedores.php");
     });
   </script>
                      
                      

