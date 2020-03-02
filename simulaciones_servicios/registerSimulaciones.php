<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

setlocale(LC_TIME, "Spanish");
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $numeroServicio=(int)obtenerCantidadSimulacionServicio($q)+1;
  $nombreServicioIbnorca="SERVICIO DE PRUEBA COD : ".$q;
  $nombreInputPropuesta=$nombreServicioIbnorca." - PROPUESTA(".$numeroServicio.")";
}else{
  $nombreInputPropuesta="";
}

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
?>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold">Procesando Datos</h4>
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
                    echo "<center><h5><b>".$nombreServicioIbnorca."</b></h5></center>";
                    ?><input class="form-control col-sm-4" type="hidden" name="codigo_servicioibnorca" id="codigo_servicioibnorca" value="<?=$q?>"/><?php
                  }
              ?>

                 <div class="row">
                       <label class="col-sm-2 col-form-label">Nombre:</label>
                       <div class="col-sm-7">
                        <div class="form-group">
                          <input class="form-control" type="text" name="nombre" id="nombre" value="<?=$nombreInputPropuesta?>" autocomplete="off" autofocus/>
                        </div>
                        </div>
                      </div>
                  <div class="row">
                       <label class="col-sm-2 col-form-label">Plantilla de Servicios :</label>
                       <div class="col-sm-7">
                        <div class="form-group">
                                <select class="selectpicker form-control" name="plantilla_servicio" onchange="listarDatosPlantillaSim(this.value)" id="plantilla_servicio" data-style="<?=$comboColor;?>"  data-live-search="true" title="-- Elija una plantilla --" data-style="select-with-transition" data-actions-box="true"required>
                                <?php
                                 $stmt = $dbh->prepare("SELECT p.*, u.abreviatura as unidad,a.abreviatura as area from plantillas_servicios p,unidades_organizacionales u, areas a where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and p.cod_estadoreferencial!=2 and p.cod_estadoplantilla=3 order by codigo");
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

                      <div class="row">
                       <label class="col-sm-2 col-form-label">Cliente</label>
                       <div class="col-sm-7">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="form-group">
                                <select class="selectpicker form-control" name="cliente" id="cliente" data-style="btn btn-info"  required>
          
                                <!--<option disabled selected="selected" value="">Cliente</option>-->
                                <?php
                                 $stmt = $dbh->prepare("SELECT c.codigo, c.nombre, t.nombre as tipo FROM clientes c join tipos_clientes t on c.cod_tipocliente=t.codigo where c.cod_estadoreferencial=1 order by 2");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  $tipoX=$row['tipo'];
                                  //$abrevX=$row['abreviatura'];
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$nombreX;?> - <?=$tipoX?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>
                              </div>
                          </div> 
                        </div>
                       </div>
                      </div><!--row-->

                      <div class="row">
                       <label class="col-sm-2 col-form-label">Productos (producto1,producto2)</label>
                       <div class="col-sm-7">
                        <div class="form-group" style="border-bottom: 1px solid #CACFD2">
                          <input type="text" value="" class="form-control tagsinput" name="productos" id="productos" data-role="tagsinput" required data-color="primary">
                        </div>
                        </div>
                      </div>
                      <!--<div class="row">
                       <label class="col-sm-2 col-form-label">Norma</label>
                       <div class="col-sm-7">
                        <div class="form-group">-->
                          <input class="form-control" type="hidden" name="norma" id="norma" required value="NINGUNA" autocomplete="off"/>
                        <!--</div>
                        </div>
                      </div>-->
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Regi&oacute;n</label>
                       <div class="col-sm-7">
                         <div class="form-group">
                           <select class="form-control selectpicker" data-style="btn btn-info" name="local_extranjero" id="local_extranjero">
                                 <option value="1" selected>BOLIVIA</option>
                                 <option value="0">EXTRANJERO</option>        
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
            ?><button type="button" class="<?=$buttonNormal;?>" onclick="guardarSimulacionServicio()">Guardar</button>
              <a href="<?=$urlList?>&q=<?=$q?>" class="<?=$buttonCancel;?>">Volver</a><?php
          }else{
            ?><button type="button" class="<?=$buttonNormal;?>" onclick="guardarSimulacionServicio()">Guardar</button>
              <a href="<?=$urlList?>" class="<?=$buttonCancel;?>">Volver</a><?php
          }
        ?>
        </div>
      </div>
    </div>
      
  </div>
</div> 