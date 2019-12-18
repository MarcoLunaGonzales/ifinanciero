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
$unidad=$_SESSION['globalUnidad'];
$sql="SELECT IFNULL(max(c.codigo)+1,1)as codigo from solicitud_recursos c where c.cod_unidadorganizacional=$unidad";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$nroCorrelativo=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $nroCorrelativo=$row['codigo'];
}
?>
<div class="content">
	<div id="contNuevaPlantilla">
		<div class="fondo-imagen2"></div>
	<center><div class="card col-sm-6">
                <div class="card-header card-header-danger card-header-text">
                  <div class="card-text">
                    <h6 class="card-title">Nueva Solicitud</h6>
                  </div>
                </div>
                <div class="card-body ">
                    <div class="row">
                       <label class="col-sm-3 col-form-label">Numero:</label>
                       <div class="col-sm-3">
                        <div class="form-group">
                          <input class="form-control" type="text" readonly name="numero" id="numero" value="<?=$nroCorrelativo?>"/>
                        </div>
                        </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-3 col-form-label">Tipo :</label>
                       <div class="col-sm-9">
                        <div class="form-group">
                                <select class="selectpicker form-control" onchange="listarTipoSolicitud(this.value)" name="tipo_solicitud" id="tipo_solicitud" data-style="<?=$comboColor;?>" title="-- Elija un tipo --" data-style="select-with-transition" data-actions-box="true"required>
                                  <option value="1">POR SIMULACION</option> 
                                  <option value="2">POR PROVEEDOR</option> 
                                </select>
                              </div>
                        </div>
                      </div>
                      <div class="row" id="lista_tipo">
                      </div>
                      <hr>
                      <div class="form-group float-right">
                        <a href="../index.php?opcion=listSolicitudSimulacion" class="btn btn-default btn-round">Cerrar</a>
                        <button type="button" class="btn btn-warning btn-round" onclick="guardarSolicitudRecursos()">Guardar</button>
                      </div>
                 <div id="mensaje"></div>
      </div>  
    </div></center>
   </div><!--div nueva plantilla-->
</div>

