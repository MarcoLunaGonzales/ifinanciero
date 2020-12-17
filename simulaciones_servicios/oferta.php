<?php
session_start();
set_time_limit(0);
error_reporting(-1);
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

$fechaActual=date("Y-m-d");
$dbh = new Conexion();
if(isset($_GET['cod'])){
	$codigo=$_GET['cod'];
  $codigoSimulacionSuper=$_GET['cod'];
}else{
	$codigo=0;
}
$codAreaX=obtenerCodigoAreaPlantillasServicios(obtenerPlantillaCodigoSimulacionServicio($codigo));
$areaX=abrevArea_solo($codAreaX);

?>

<div class="cargar">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<div class="content">
	<div id="contListaGrupos" class="container-fluid">
			<input type="hidden" name="cod_simulacion" id="cod_simulacion" value="<?=$codigo?>">
      <div class="row">
        <div class="card col-sm-12">
				<div class="card-header card-header-warning card-header-text">
					<div class="card-text">
					  <h4 class="card-title text-dark">FORMULARIO DE IMPRESIÓN, OFERTA: <b><?=nameSimulacionServicio($codigo)?> - <?=$areaX?></b></h4>
					</div>
				</div>
				<div class="card-body ">
					<div class="row col-sm-12">
              <?php
              $codOferta=obtenerOfertaActiva($_GET["cod"]);//$_GET["cod_oferta"];
              $default=0;
              if($codOferta==0){
               $default=1;
               if($_GET["cod_area"]==39){
                $codOferta=1;
               }else{
                //tipo de oferta TCS A B C
                if(isset($_GET["of"])){
                  switch ($_GET["of"]) {
                    case 'a':
                      $codOferta=2;
                      break;
                    case 'b':
                      $codOferta=3;
                      break;
                    case 'c':
                      $codOferta=4;
                      break;  
                    default:
                      $codOferta=2;
                      break;
                  }
                }else{
                  $codOferta=2;
                }     
               }  
              }

             if($_GET["cod_area"]==39){
              require 'oferta_html.php';
             }else{
              require 'oferta_html.php';
             }
              ?>
          </div>
				</div>
			</div>
		   </div>
  </div> 
</div>        
          