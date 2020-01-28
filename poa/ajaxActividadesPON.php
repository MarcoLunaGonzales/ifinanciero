<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

session_start();
$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$codigo=$_GET['codigo'];
$codigoIndicador=$_GET['cod_indicador'];
$codUnidad=$_GET['cod_unidad'];

$codUnidadHijosX=buscarHijosUO($codUnidad);


?>
<div class="col-md-12">
	<div class="row">

		<div class="col-sm-3">
	        <div class="form-group">
	        <input type="hidden" name="codigo<?=$codigo;?>" id="codigo<?=$codigo;?>" value="0">
	        <select class="selectpicker form-control" name="comite<?=$codigo;?>" id="comite<?=$codigo;?>" data-style="<?=$comboColor;?>" data-live-search="true">
		  		<option value="">Comite</option>
			  	<?php
			  	$stmt = $dbh->prepare("SELECT codigo, nombre FROM sectores where cod_estado=1 order by 2");
				$stmt->execute();
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$codigoX=$row['codigo'];
					$nombreX=$row['nombre'];
				?>
				<optgroup label="<?=$nombreX;?>">
				<?php
				  	$stmtY = $dbh->prepare("SELECT c.codigo, c.nombre, c.abreviatura FROM comites c where c.cod_sector='$codigoX' and cod_estado=1 order by 2");
					$stmtY->execute();
					while ($rowY = $stmtY->fetch(PDO::FETCH_ASSOC)) {
						$codigoY=$rowY['codigo'];
						$nombreY=$rowY['nombre'];
						$nombreY=cutString($nombreY,80);
						$abreviaturaY=$rowY['abreviatura'];

				?>
						<option value="<?=$codigoY;?>" data-subtext="<?=$abreviaturaY?>" ><?=$nombreY;?></option>	
				<?php
					}
				?>
				</optgroup>
				<?php	
				}
			  	?>
			</select>
			</div>
	    </div>

	    <div class="col-sm-3">
	    	<div class="form-group">
	        <select class="selectpicker form-control" name="norma<?=$codigo;?>" id="norma<?=$codigo;?>" data-style="<?=$comboColor;?>" data-live-search="true">
			  	<option value="">Norma Referencia</option>
			  	<?php
			  	$stmt = $dbh->prepare("SELECT codigo, nombre FROM sectores where cod_estado=1 order by 2");
				$stmt->execute();
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$codigoX=$row['codigo'];
					$nombreX=$row['nombre'];
				?>
				<optgroup label="<?=$nombreX;?>">
				<?php
				  	$stmtY = $dbh->prepare("SELECT n.codigo, n.nombre, n.abreviatura FROM normas n where n.cod_sector='$codigoX' and n.cod_estado=1 order by 2");
					$stmtY->execute();
					while ($rowY = $stmtY->fetch(PDO::FETCH_ASSOC)) {
						$codigoY=$rowY['codigo'];
						$nombreY=$rowY['nombre'];
						$nombreY=cutString($nombreY,80);
						$abreviaturaY=$rowY['abreviatura'];

				?>
						<option value="<?=$codigoY;?>" data-subtext="<?=$nombreY?>" ><?=$abreviaturaY;?></option>	
				<?php
					}
				?>
				</optgroup>
				<?php	
				}
			  	?>
			</select>
			</div>
	  	</div>

	    <div class="col-sm-2">
	    	<div class="form-group">
	        <select class="selectpicker form-control" name="modogeneracion<?=$codigo;?>" id="modogeneracion<?=$codigo;?>" data-style="<?=$comboColor;?>" data-live-search="true">
			  	<option value="">Modo Generacion</option>
			  	<?php
			  	$stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM modos_generacionpon where cod_estado=1 order by 2");
				$stmt->execute();
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$codigoX=$row['codigo'];
					$nombreX=$row['nombre'];
					$abreviaturaX=$row['abreviatura'];
				?>
					<option value="<?=$codigoX;?>" data-subtext="<?=$abreviaturaX?>" ><?=$nombreX;?></option>	
				<?php
					}
				?>
			</select>
			</div>
	  	</div>

	  	<div class="col-sm-4">
            <div class="form-group">
	            <label for="actividad<?=$codigo;?>" class="bmd-label-floating">Actividad</label>
    	      	<textarea class="form-control" type="text" name="actividad<?=$codigo;?>" id="actividad<?=$codigo;?>" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"></textarea>	
			</div>
      	</div>

	</div>
</div>

<div class="col-md-12">
	<div class="row">
		
        <div class="col-sm-3">
        	<div class="form-group">
	        <select class="selectpicker form-control" name="tipo_solicitante<?=$codigo;?>" id="tipo_solicitante<?=$codigo;?>" data-style="<?=$comboColor;?>">
			  	<option value="">Tipo Solicitante</option>
			  	<?php
			  	$stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM tipos_solicitante where cod_estado=1 order by 3");
				$stmt->execute();
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$codigoX=$row['codigo'];
					$nombreX=$row['nombre'];
					$abreviaturaX=$row['abreviatura'];
				?>
					<option value="<?=$codigoX;?>" data-subtext="<?=$abreviaturaX?>" ><?=$nombreX;?></option>	
				<?php
					}
				?>
			</select>
			</div>
      	</div>

      	<div class="col-sm-4">
        	<div class="form-group">
            	<label for="solicitante<?=$codigo;?>" class="bmd-label-floating">Solicitante</label>
            	<input type="text" class="form-control" name="solicitante<?=$codigo;?>" id="solicitante<?=$codigo;?>" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">	
			</div>
      	</div>

        <div class="col-sm-4">
        	<div class="form-group">
	        <select class="selectpicker" name="personal<?=$codigo;?>" id="personal<?=$codigo;?>" data-style="<?=$comboColor;?>">
			  	<option value="">Responsable</option>
			  	<?php
			  	$stmt = $dbh->prepare("SELECT distinct(p.codigo) as codigo, CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre)as nombre FROM personal p, personal_datosadicionales pd where p.codigo=pd.cod_personal and pd.cod_estado=1 order by 2");
				$stmt->execute();
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$codigoX=$row['codigo'];
					$nombreX=$row['nombre'];
				?>
					<option value="<?=$codigoX;?>"><?=$nombreX;?></option>	
				<?php
					}
				?>
			</select>
			</div>
      	</div>

		<div class="col-sm-1">
			<button rel="tooltip" class="btn btn-just-icon btn-danger btn-link" onclick="minusActividad('<?=$codigo;?>');">
	                              <i class="material-icons">remove_circle</i>
	        </button>
		</div>
	</div>
</div>
<div class="h-divider">
