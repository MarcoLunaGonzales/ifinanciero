<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

setlocale(LC_TIME, "Spanish");
$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$contadorRegistros=0;
?>
<script>
	numFilas=<?=$contadorRegistros;?>;
	cantidadItems=<?=$contadorRegistros;?>;
</script>

<?php
$fechaActual=date("Y-m-d");
$dbh = new Conexion();
 $stmt = $dbh->prepare("SELECT * FROM configuraciones_cursos where codigo=1");
 $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $utilidadMinLocal=$row['utilidad_minlocal'];
    $utilidadMinExterno=$row['utilidad_minexterno'];
    $alumnosLocal=$row['alumnos_local'];
    $alumnosExterno=$row['alumnos_externo'];
    $precioVentaLocal=$row['precio_ventalocal'];
    $precioVentaExterno=$row['precio_ventaexterno'];
    }
?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="formRegComp" class="form-horizontal" action="save.php" method="post" enctype="multipart/form-data">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Registrar <?=$moduleNameSingular?></h4>
				</div>
			  </div>
			  <div class="card-body ">
				

				<div class="row">
                       <label class="col-sm-2 col-form-label">Nombre de plantilla:</label>
                       <div class="col-sm-7">
                        <div class="form-group">
                          <input class="form-control" type="text" name="nombre" id="nombre" autocomplete="off" autofocus/>
                        </div>
                        </div>
                 </div>
                 <div class="row">
                       <label class="col-sm-2 col-form-label">Abreviatura</label>
                       <div class="col-sm-7">
                        <div class="form-group">
                          <input class="form-control" type="text" name="abreviatura" id="abreviatura" autocomplete="off"/>
                        </div>
                        </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Empresa</label>
                       <div class="col-sm-7">
                        <div class="row">
                          <div class="col-sm-5">
                            <div class="form-group">
                                <select class="selectpicker form-control" name="empresa" id="empresa" data-style="btn btn-info"  required>
          
                                <option disabled selected="selected" value="">Empresa</option>
                                <?php
                                 $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM empresas where cod_estadoreferencial=1 order by 2");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  $abrevX=$row['abreviatura'];
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$nombreX;?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>
                              </div>
                          </div>
                          <div class="col-sm-7">
                            <div class="row">
                            <label class="col-sm-6 col-form-label">Planta de Producci&oacute;n</label>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <input class="form-control" type="text" name="planta_produccion" id="planta_produccion" value=""/>
                              </div>
                            </div>
                           </div><!--row--> 
                          </div> 
                        </div>
                       </div>
                      </div><!--row-->
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Tipo de Auditoria</label>
                       <div class="col-sm-7">
                        <div class="row">
                          <div class="col-sm-5">
                            <div class="form-group">
                               <select class="selectpicker form-control" name="tipo_auditoria" id="tipo_auditoria" data-style="btn btn-info"  required>
                                <option disabled selected="selected" value="">Auditoria</option>
                                <?php
                                 $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM tipos_auditoria where cod_estadoreferencial=1 order by 1");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  $abrevX=$row['abreviatura'];
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$nombreX;?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>                               
                              </div>
                          </div>
                          <div class="col-sm-7">
                            <div class="row">
                            <label class="col-sm-6 col-form-label">Productos</label>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <input class="form-control" type="text" name="productos" id="productos" value=""/>
                               
                              </div>
                            </div>
                           </div><!--row--> 
                          </div> 
                        </div>
                       </div>
                      </div><!--row-->
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Norma</label>
                       <div class="col-sm-7">
                        <div class="row">
                          <div class="col-sm-4">
                            <div class="form-group">
                                <input class="form-control" type="text" name="norma" id="norma" value=""/>
                                
                              </div>
                          </div>
                          <div class="col-sm-8">
                            <div class="row">
                            <label class="col-sm-7 col-form-label">Fecha</label>
                            <div class="col-sm-5">
                              <div class="form-group">
                                <input class="form-control datepicker" type="text" name="fecha_auditoria" id="fecha_auditoria" value="<?=date('d/m/Y')?>"/>
                                
                              </div>
                            </div>
                           </div><!--row--> 
                          </div> 
                        </div>
                       </div>
                      </div><!--row-->
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Unidad</label>
                       <div class="col-sm-7">
                        <div class="row">
                          <div class="col-sm-5">
                            <div class="form-group">
                                <select class="selectpicker form-control" name="unidad" id="unidad" data-style="btn btn-info"  required>
          
                                <option disabled selected="selected" value="">Unidad</option>
                                <?php
                                 $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 2");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  $abrevX=$row['abreviatura'];
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$abrevX;?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>
                              </div>
                          </div>
                          <div class="col-sm-7">
                            <div class="row">
                            <label class="col-sm-2 col-form-label">Area</label>
                            <div class="col-sm-10">
                              <div class="form-group">
                                <select class="selectpicker form-control" name="area" id="area" data-style="btn btn-info"  required>
          
                                <option disabled selected="selected" value="">Area</option>
                                <?php
                                 $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 2");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  $abrevX=$row['abreviatura'];
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$abrevX;?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>
                              </div>
                            </div>
                           </div><!--row--> 
                          </div> 
                        </div>
                       </div>
                      </div><!--row-->
				
			  </div>
			  <br>
			  <div id="mensaje"></div>
			  <div class="card-footer  ml-auto mr-auto">
				<button type="button" class="<?=$buttonNormal;?>" onclick="guardarPlantillaCosto()">Guardar</button>
				<a href="<?=$urlList?>" class="<?=$buttonCancel;?>">Volver</a>
			  </div>
			</div>
		  </form>
		</div>
	    
	</div>
</div>