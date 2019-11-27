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

		<div class="col-sm-3">
        	<div class="form-group">
                                <select class="selectpicker show-menu-arrow form-control form-control-sm" data-style="<?=$comboColor;?>" data-live-search="true" title="-- Elija una cuenta --" name="cuenta<?=$idFila?>" id="cuenta<?=$idFila?>" data-style="select-with-transition" data-actions-box="true" required>
                                  <?php
                           $stmt = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre from plan_cuentas p where p.nivel=5 order by p.numero");
                         $stmt->execute();
                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          $codigoX=$row['codigo'];
                          $numeroX=$row['numero'];
                          $nombreX=$row['nombre'];
                          $sqlCuentasAux="SELECT codigo,nro_cuenta, nombre FROM cuentas_auxiliares where cod_cuenta='$codigoX' order by 2";
                                       $stmtAux = $dbh->prepare($sqlCuentasAux);
                                       $stmtAux->execute();
                                       $stmtAux->bindColumn('codigo', $codigoCuentaAux);
                                       $stmtAux->bindColumn('nro_cuenta', $numeroCuentaAux);
                                       $stmtAux->bindColumn('nombre', $nombreCuentaAux);
                                       $nombreAux=" ";
                                       ?>
                         <option value="<?=$codigoX;?>@normal"><?=$numeroX;?> <?=$nombreX?></option>  
                         <?php
                          while ($rowAux = $stmtAux->fetch(PDO::FETCH_BOUND)) {
                                        ?><option value="<?=$codigoCuentaAux?>@aux">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$nombreCuentaAux?> @auxiliar</option><?php
                                      }
                       
                           }
                           ?>
                       </select>
                 </div>
      	</div>
		<div class="col-sm-5">
            <div class="form-group">
	        <select class="selectpicker form-control form-control-sm" name="tipo_dato<?=$idFila;?>" id="tipo_dato<?=$idFila;?>" data-style="<?=$comboColor;?>" >
			  	
			  	<option disabled selected="selected" value="">Tipo</option>
				<option value="1">Mensual</option>
				<option value="2">Manual</option>	
			</select>
			</div>
      	</div>

		<div class="col-sm-2">
            <div class="form-group">
            	<label for="monto_ibnorca<?=$idFila;?>" class="bmd-label-floating">Monto en IBNORCA</label>			
          		<input class="form-control" type="text" name="monto_ibnorca<?=$idFila;?>" id="monto_ibnorca<?=$idFila;?>"> 	
			</div>
      	</div>
      	<div class="col-sm-2">
            <div class="form-group">
            	<label for="monto_f_ibnorca<?=$idFila;?>" class="bmd-label-floating">Monto fuera de IBNORCA</label>			
          		<input class="form-control" type="text" name="monto_f_ibnorca<?=$idFila;?>" id="monto_f_ibnorca<?=$idFila;?>"> 	
			</div>
      	</div>
		<div class="col-sm-2">
		  <div class="btn-group">
			<a rel="tooltip" href="#" class="btn btn-just-icon btn-danger btn-link" id="boton_remove<?=$idFila;?>" onclick="minusGrupoPlantillaDet('<?=$idFila;?>');">
            	<i class="material-icons">remove_circle</i>
	        </a>
	      </div>  
		</div>

	</div>
</div>

<div class="h-divider"></div>