<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';
$dbh = new Conexion();
setlocale(LC_TIME, "Spanish");

$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$sqlAreas="";
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $s=$_GET['s'];
  $u=$_GET['u'];
  if(isset($_GET['u'])){
    $u=$_GET['u'];
    ?><input type="hidden" name="idPerfil" id="idPerfil" value="<?=$u?>"/><?php
  }else{
    $q=$globalUser;
  }
}else{
  $q=$globalUser;
}

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$contadorRegistros=0;
?>
<script>
  numFilas=<?=$contadorRegistros;?>;
  cantidadItems=<?=$contadorRegistros;?>;
</script>

<?php
$fechaActual=date("Y-m-d");
$fechaActualInput=date("Y-m-d");
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
        
                  <?php 
                  if(isset($_GET['q'])){
                    ?><input class="form-control col-sm-4" type="hidden" name="codigo_servicioibnorca" id="codigo_servicioibnorca" value="<?=$q?>"/>
                    <input type="hidden" name="codigo_servicioibnorca_s" id="codigo_servicioibnorca_s" value="<?=$s?>"/>
                    <input type="hidden" name="codigo_servicioibnorca_u" id="codigo_servicioibnorca_u" value="<?=$u?>"/><?php
                  }
              ?>

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
                                  <option value="<?=$codigoX;?>"><?=$nombreX;?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>
                              </div>
                        </div>
                      </div>

                      <div class="row">
                       <label class="col-sm-2 col-form-label">Personal</label>
                       <div class="col-sm-6">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="form-group" id="lista_personal">
                                <select class="selectpicker form-control" data-size="4" data-live-search-placeholder="Seleccionar usuario que registra..." data-live-search="true" name="codigo_personal" id="codigo_personal" data-style="btn btn-info"  required>
                                <option value="0">-- -- --</option>
                                <?php
                                 $u=0;
                                 if(isset($u)){
                                    $u=$_GET['u'];
                                 }
                                 $stmt = $dbh->prepare("SELECT p.codigo, concat(p.paterno,' ',p.materno,' ',p.primer_nombre)as nombrepersona FROM personal p where p.cod_estadopersonal in (1,2,3) order by 2");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombrepersona'];
                                   ?>
                                  <option value="<?=$codigoX;?>" <?=($codigoX==$u)?"selected":""?> ><?=$nombreX;?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>
                              </div>
                          </div> 
                        </div>
                       </div>
                    </div><!--row-->  

                      <div class="row" id="lista_precios">
                      </div>
                      <br>
                      <div class="row">
                           <label class="col-sm-2 col-form-label">Cantidad de M&oacute;dulos:</label>
                           <div class="col-sm-2"> 
                             <div class="form-group">
                                 <input class="form-control" type="number" id="cantidad_modulos" readonly name="cantidad_modulos" min="1" max="12" value="1">
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
                       <div class="col-sm-2">
                         <div class="form-group">
                            <input class="form-control" type="date" id="fecha_estimada" name="fecha_estimada" value="<?=$fechaActualInput?>">  
                         </div>
                        </div>
                        <label class="col-sm-2 col-form-label">Fecha Solicitud Cliente:</label>
                        <div class="col-sm-2">
                         <div class="form-group">
                            <input class="form-control" type="date" id="fecha_solicitud_cliente" name="fecha_solicitud_cliente" value="<?=$fechaActualInput?>">  
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
                       <label class="col-sm-2 col-form-label">Cliente</label>
                       <div class="col-sm-6">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="form-group" id="lista_clientes">
                                <select class="selectpicker form-control" data-size="4" data-live-search-placeholder="Buscar cliente..." data-live-search="true" name="codigo_cliente" id="codigo_cliente" data-style="btn btn-info"  required>
          
                                <<option value="0">-- --</option>
                                <?php
                                 $stmt = $dbh->prepare("SELECT c.codigo, c.nombre FROM clientes c where c.cod_estadoreferencial=1 order by 2");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  //$tipoX=$row['tipo'];
                                  //$abrevX=$row['abreviatura'];
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$nombreX;?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>
                              </div>
                          </div> 
                        </div>
                       </div>
                       <div class="col-sm-1">
                        <div class="row">
                          <div class="form-group">
                            <a href="#" class="btn btn-warning btn-round btn-fab" onclick="actualizarRegistroSoloClientes()"> <!---->
                             <i class="material-icons" title="Actualizar Clientes">update</i>
                            </a>
                         </div>
                        </div>
                       </div>
                  </div><!--row-->   

                  <div class="row">
                   <label class="col-sm-2 col-form-label">Normas Nacionales:</label>
                   <div class="col-sm-7">
                    <div class="form-group">
                            <select class="selectpicker form-control" name="normas[]" id="normas" multiple data-style="btn btn-warning" data-actions-box="true" data-live-search="true" data-size="6" required>
                            <?php
                             $stmt = $dbh->prepare("SELECT vn.codigo, vn.abreviatura, vn.nombre, 'L' as tipo from v_normas vn where vn.cod_estado=1 order by 4,2");
                             $stmt->execute();
                              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                              $codigoX=$row['codigo'];
                              $nombreX=$row['nombre'];
                              $tipoX=$row['tipo'];
                              $abrevX=$row['abreviatura']." (".$tipoX.")";
                              $nombreX=substr($nombreX, 0, 70);
                               ?>
                              <option value="<?=$codigoX;?>" data-subtext="<?=$nombreX;?>"><?=$abrevX;?></option> 
                              <?php
                                }
                                ?>
                            </select>
                          </div>
                    </div>
                  </div>
                  <div class="row">
                   <label class="col-sm-2 col-form-label">Normas Internacionales:</label>
                   <div class="col-sm-7">
                    <div class="form-group">
                            <select class="selectpicker form-control" name="normas_int[]" id="normas_int" multiple data-style="btn btn-warning" data-actions-box="true" data-live-search="true" data-size="6" required>
                            <?php
                             $stmt = $dbh->prepare("SELECT vi.codigo, vi.abreviatura, vi.nombre, 'I' as tipo from v_normas_int vi where vi.cod_estado=1 order by 4,2");
                             $stmt->execute();
                              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                              $codigoX=$row['codigo'];
                              $nombreX=$row['nombre'];
                              $tipoX=$row['tipo'];
                              $abrevX=$row['abreviatura']." (".$tipoX.")";
                              $nombreX=substr($nombreX, 0, 70);
                               ?>
                              <option value="<?=$codigoX;?>" data-subtext="<?=$nombreX;?>"><?=$abrevX;?></option> 
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
          <?php 
          if(isset($_GET['q'])){
            ?><button type="button" class="<?=$buttonNormal;?>" onclick="guardarSimulacionCosto()">Guardar</button>
              <!--<a href="<?=$urlList?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>" class="<?=$buttonCancel;?>">Volver</a>--><?php
          }else{
            ?><button type="button" class="<?=$buttonNormal;?>" onclick="guardarSimulacionCosto()">Guardar</button>
              <a href="<?=$urlList?>" class="<?=$buttonCancel;?>">Volver</a><?php
          }
        ?>
        </div>
      </div>
    </div>
      
  </div>
</div> 