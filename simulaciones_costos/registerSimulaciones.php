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
$fechaActualInput=date("d/m/Y");
$dbh = new Conexion();
?>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<div class="content">
  <div class="container-fluid">

    <div class="col-md-12">
      <div class="card">
        <div class="card-header <?=$colorCard;?> card-header-text">
        <div class="card-text">
          <h4 class="card-title">Registrar Propuesta</h4>
        </div>
        </div>
        <div class="card-body ">
        

                 <div class="row">
                       <label class="col-sm-2 col-form-label">Nombre:</label>
                       <div class="col-sm-7">
                        <div class="form-group">
                          <input class="form-control" type="text" name="nombre" id="nombre" autocomplete="off" autofocus/>
                        </div>
                        </div>
                      </div>
                  <div class="row">
                       <label class="col-sm-2 col-form-label">Plantilla de costos :</label>
                       <div class="col-sm-7">
                        <div class="form-group">
                                <select class="selectpicker form-control" onchange="listarPreciosPlantillaSim(this.value)" name="plantilla_costo" id="plantilla_costo" data-style="<?=$comboColor;?>"  data-live-search="true" title="-- Elija una plantilla --" data-style="select-with-transition" data-actions-box="true"required>
                                <?php
                                 $stmt = $dbh->prepare("SELECT p.*, u.abreviatura as unidad,a.abreviatura as area from plantillas_costo p,unidades_organizacionales u, areas a where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and p.cod_estadoreferencial!=2 and p.cod_estadoplantilla=3 order by codigo");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  $abrevX=$row['abreviatura'];
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$nombreX;?> @<?=$abrevX?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>
                              </div>
                        </div>
                      </div>
                      <div class="row" id="lista_precios">
                      </div>
                      <br>
                      <div class="row">
                           <label class="col-sm-2 col-form-label">Cantidad de M&oacute;dulos:</label>
                           <div class="col-sm-2"> 
                             <div class="form-group">
                                 <input class="form-control" type="number" id="cantidad_modulos" name="cantidad_modulos" min="1" max="12" value="1">
                               </div>
                             </div>
                             <label class="col-sm-2 col-form-label">Cantidad de d&iacute;as:</label>
                           <div class="col-sm-3"> 
                             <div class="form-group">
                                 <input class="form-control" type="number" id="cantidad_dias" name="cantidad_dias" min="1" value="1">
                               </div>
                             </div>     
                      </div>
                      <!--<div class="row">
                           <label class="col-sm-2 col-form-label">Monto Norma:</label>
                           <div class="col-sm-7"> 
                             <div class="form-group">-->
                                 <input class="form-control" type="hidden" id="monto_norma" name="monto_norma" value="10">
                               <!--</div>
                             </div>     
                      </div>-->
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Fecha Estimada:</label>
                       <div class="col-sm-7">
                         <div class="form-group">
                            <input class="form-control datepicker" type="text" id="fecha_estimada" name="fecha_estimada" value="<?=$fechaActualInput?>">  
                         </div>
                        </div>
                      </div>
                     <div class="row">
                       <label class="col-sm-2 col-form-label">Tipo de Curso:</label>
                       <div class="col-sm-7">
                        <div class="form-group">
                                <select class="selectpicker form-control" name="tipo_curso" id="tipo_curso" data-style="btn btn-warning" required>
                                <?php
                                 $stmt = $dbh->prepare("SELECT * from tipos_cursos order by 1");
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
                      </div>
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Normas:</label>
                       <div class="col-sm-7">
                        <div class="form-group">
                                <select class="selectpicker form-control" name="normas[]" id="normas" multiple data-style="btn btn-warning" data-live-search="true" data-size="6" required>
                                <?php
                                 $stmt = $dbh->prepare("SELECT * from normas where cod_estado=1 order by abreviatura");
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
                      </div>
        
        </div>
        <br>
        <div id="mensaje"></div>
        <div class="card-footer  ml-auto mr-auto">
        <button type="button" class="<?=$buttonNormal;?>" onclick="guardarSimulacionCosto()">Guardar</button>
        <a href="<?=$urlList?>" class="<?=$buttonCancel;?>">Volver</a>
        </div>
      </div>
    </div>
      
  </div>
</div> 