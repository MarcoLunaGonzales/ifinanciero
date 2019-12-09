<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
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
?>
<div class="content">
	<div id="contNuevaPlantilla">
		<div class="fondo-imagen2"></div>
	<center><div class="card col-sm-6">
                <div class="card-header card-header-warning card-header-text">
                  <div class="card-text">
                    <h6 class="card-title">Nueva Simulacion</h6>
                  </div>
                </div>
                <div class="card-body ">
                    <div class="row">
                       <label class="col-sm-3 col-form-label">Nombre:</label>
                       <div class="col-sm-9">
                        <div class="form-group">
                          <input class="form-control" type="text" name="nombre" id="nombre" autocomplete="off" autofocus/>
                        </div>
                        </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-3 col-form-label">Plantilla de costos :</label>
                       <div class="col-sm-9">
                        <div class="form-group">
                                <select class="selectpicker form-control" onchange="listarPreciosPlantilla(this.value)" name="plantilla_costo" id="plantilla_costo" data-style="<?=$comboColor;?>"  data-live-search="true" title="-- Elija una plantilla --" data-style="select-with-transition" data-actions-box="true"required>
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
                          <div class="row">
                           <label class="col-sm-3 col-form-label">En IBNORCA:</label>
                           <div class="col-sm-1"> 
                             <div class="form-group">
                                <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" id="ibnorca_check" name="ibnorca_check" value="1">
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                               </div>
                             </div>     
                          </div>
                      <hr>
                      <div class="form-group float-right">
                        <a href="../index.php?opcion=listSimulacionesCostos" class="btn btn-default btn-round">Cerrar</a>
                        <button type="button" class="btn btn-warning btn-round" onclick="guardarSimulacionCosto()">Guardar</button>
                      </div>
                 <div id="mensaje"></div>
      </div>  
    </div></center>
   </div><!--div nueva plantilla-->
</div>

