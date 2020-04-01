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
$cod_activo=$_GET['cod_activo'];
// $cod_cc=$_GET['cod_cc'];
// $cod_dcc=$_GET['cod_dcc'];
       
$lista= obtenerPaisesServicioIbrnorca();

?>                  
<input class="form-control" type="hidden" name="cod_activo" id="cod_activo" value="<?=$cod_activo?>" />
<!-- <input class="form-control" type="hidden" name="cod_cc" id="cod_cc" value="<?=$cod_cc?>"/>
<input class="form-control" type="hidden" name="cod_dcc" id="cod_dcc" value="<?=$cod_dcc?>"/> -->

                    <center><h4 class="fontweight-bold text-muted">Datos del Proveedor</h4></center>
                    <div class="row">
                       <label class="col-sm-3 col-form-label">Tipo Proveedor *</label>
                       <div class="col-sm-9">
                        <div class="form-group">
                          <select name="tipo_empresa" id="tipo_empresa" class="form-control form-control-sm selectpicker" data-style="btn btn-primary" required="true">
                            <option value="E">EMPRESA</option>
                            <option value="P">PERSONA</option>
                          </select>
                        </div>
                       </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-3 col-form-label">Clase *</label>
                       <div class="col-sm-9">
                        <div class="form-group">
                          <select name="nacional_empresa" id="nacional_empresa" class="form-control form-control-sm selectpicker" data-style="btn btn-primary" required="true">
                            <option value="N">NACIONAL</option>
                            <option value="I">INTERNACIONAL</option>
                          </select>
                        </div>
                       </div>
                      </div>
                     <div class="row">
                       <label class="col-sm-3 col-form-label">Nombre *</label>
                       <div class="col-sm-9">
                        <div class="form-group">
                          <input type="text" class="form-control" name="nombre_empresa" id="nombre_empresa" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        </div>
                        </div>
                      </div>

                      <div class="row">
                       <label class="col-sm-3 col-form-label">NIT *</label>
                       <div class="col-sm-9">
                        <div class="form-group">
                          <input class="form-control" type="number" name="nit_empresa" id="nit_empresa" required="true"/>
                        </div>
                        </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-3 col-form-label">Pais *</label>
                       <div class="col-sm-9">
                        <div class="form-group">
                          <select name="pais_empresa" id="pais_empresa" onchange="seleccionarDepartamentoServicioCajaChica()" class="form-control form-control-sm selectpicker" data-style="btn btn-info" required="true">
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
                       <label class="col-sm-3 col-form-label">Departamento / Estados *</label>
                       <div class="col-sm-9">
                        <div class="form-group">
                          <select name="departamento_empresa" onchange="seleccionarCiudadServicioCajaChica()" id="departamento_empresa" class="form-control form-control-sm selectpicker" data-style="btn btn-info" required="true">
                          </select>
                        </div>
                       </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-3 col-form-label">Ciudad *</label>
                       <div class="col-sm-9">
                        <div class="form-group">
                          <select name="ciudad_empresa" onchange="mostrarOtraCiudadServicio()" id="ciudad_empresa" class="form-control form-control-sm selectpicker" data-style="btn btn-success" >
                          </select>
                        </div>
                       </div>
                      </div>
                      <div class="row d-none" id="otra_ciudad_div">
                       <label class="col-sm-3 col-form-label">Otra ciudad *</label>
                       <div class="col-sm-9">
                        <div class="form-group">
                          <input type="text" class="form-control" name="otra_ciudad" id="otra_ciudad" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        </div>
                       </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-3 col-form-label">Direcci&oacute;n *</label>
                       <div class="col-sm-9">
                        <div class="form-group">
                          <input type="text" class="form-control" name="direccion_empresa" id="direccion_empresa" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        </div>
                       </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-3 col-form-label">Telefono *</label>
                       <div class="col-sm-9">
                        <div class="form-group">
                          <input type="text" class="form-control" name="telefono_empresa" id="telefono_empresa" value="" required="true">
                        </div>
                       </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-3 col-form-label">Correo *</label>
                       <div class="col-sm-9">
                        <div class="form-group">
                          <input type="text" class="form-control" name="correo_empresa" id="correo_empresa" value="" required="true">
                        </div>
                       </div>
                      </div>
                      <center><h4 class="fontweight-bold text-muted">Datos del contacto </h4></center>
                      <div class="row">
                       <label class="col-sm-3 col-form-label">Nombre Contacto *</label>
                       <div class="col-sm-9">
                        <div class="form-group">
                          <input type="text" class="form-control" name="nombre_contacto" id="nombre_contacto" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        </div>
                       </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-3 col-form-label">Apellido Contacto *</label>
                       <div class="col-sm-9">
                        <div class="form-group">
                          <input type="text" class="form-control" name="apellido_contacto" id="apellido_contacto" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        </div>
                       </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-3 col-form-label">Cargo Contacto *</label>
                       <div class="col-sm-9">
                        <div class="form-group">
                          <input type="text" class="form-control" name="cargo_contacto" id="cargo_contacto" value="" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        </div>
                       </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-3 col-form-label">Correo Contacto *</label>
                       <div class="col-sm-9">
                        <div class="form-group">
                          <input type="text" class="form-control" name="correo_contacto" id="correo_contacto" value="" required="true">
                        </div>
                       </div>
                      </div>
                      

