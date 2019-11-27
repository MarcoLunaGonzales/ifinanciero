<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$idFila=$_GET['idFila'];
?>
<div id="comp_row" class="col-md-12">
	<div class="row">

		<div class="col-sm-1">
        	<div class="form-group">
	        <select class="selectpicker form-control form-control-sm" name="unidad<?=$idFila;?>" id="unidad<?=$idFila;?>" data-style="<?=$comboColor;?>" >
			  	
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

		<div class="col-sm-1">
        	<div class="form-group">
	        <select class="selectpicker form-control form-control-sm" name="area<?=$idFila;?>" id="area<?=$idFila;?>" data-style="<?=$comboColor;?>">
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

      	<div class="col-sm-3">
      		<input type="hidden" name="cuenta<?=$idFila;?>" id="cuenta<?=$idFila;?>">
    		<input type="hidden" name="cuenta_auxiliar<?=$idFila;?>" id="cuenta_auxiliar<?=$idFila;?>">
    		<div class="row">	
    			<div class="col-sm-9">
    				<div class="form-group" id="divCuentaDetalle<?=$idFila;?>">
    			
        	        </div>
    			</div>
    			<div class="col-sm-3">
    				 <a title="Distribucion - shift+d " href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucion<?=$idFila?>" onclick="nuevaDistribucionPonerFila(<?=$idFila;?>);" class="btn btn-sm btn-default btn-fab"><span class="material-icons">scatter_plot</span></a>	  
    			</div>
    		</div>
      	</div>

		<div class="col-sm-2">
            <div class="form-group">
            	<label for="debe<?=$idFila;?>" class="bmd-label-floating">Debe</label>			
          		<input class="form-control" type="number" name="debe<?=$idFila;?>" id="debe<?=$idFila;?>" onChange="calcularTotalesComprobante(this.id,event);" OnKeyUp="calcularTotalesComprobante(this.id,event);" step="0.01">	
			</div>
      	</div>

		<div class="col-sm-2">
            <div class="form-group">
            	<label for="haber<?=$idFila;?>" class="bmd-label-floating">Haber</label>			
          		<input class="form-control" type="number" name="haber<?=$idFila;?>" id="haber<?=$idFila;?>" onChange="calcularTotalesComprobante(this.id,event);" OnKeyUp="calcularTotalesComprobante(this.id,event);" step="0.01"> 	
			</div>
      	</div>
      	<div class="col-sm-2">
		    <div class="form-group">
          		<label for="glosa_detalle<?=$idFila;?>" class="bmd-label-static">GlosaDetalle</label>
				<textarea rows="1" class="form-control" name="glosa_detalle<?=$idFila;?>" id="glosa_detalle<?=$idFila;?>" value=""></textarea>
			</div>
		</div>
		<div class="col-sm-1">
		  <div class="btn-group">
		  	<a href="#" id="boton_fac<?=$idFila;?>" onclick="listFac(<?=$idFila;?>);" class="btn btn-just-icon btn-info btn-link">
               <i class="material-icons">featured_play_list</i><span id="nfac<?=$idFila;?>" class="count bg-warning">0</span>
             </a>
			<a rel="tooltip" href="#" class="btn btn-just-icon btn-danger btn-link" id="boton_remove<?=$idFila;?>" onclick="minusCuentaContable('<?=$idFila;?>');">
            	<i class="material-icons">remove_circle</i>
	        </a>
	      </div>  
		</div>

	</div>
</div>

<div class="h-divider"></div>