<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

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


if(isset($_GET['codigo'])){
	$codigo=$_GET['codigo'];
}else{
	$codigo=0;
}

?>

<div class="content">
	<div class="container-fluid">


			<div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text">
					  <h4 class="card-title">Vista de Archivos</h4>
					</div>
				</div>
				<div class="card-body ">
					<p>Archivos de respaldo almacenados en el servidor</p>
					<div class="h-divider"></div>
					<div class="row">

						<div class="col-sm-4">
							<?php 
							obtenerDirectorios("../assets/archivos-respaldo/COMP-".$codigo);
							?>
						</div>
						<div class="col-sm-8" id="cont_archivos">
							
						</div>
						
					</div>
					<div class="h-divider"></div>

				</div>
				<div class="card-footer fixed-bottom">
						<a href="../<?=$urlListReg;?>" class="<?=$buttonCancel;?>">Cancelar</a>

				  	</div>
			</div>	
	</div>
</div>
