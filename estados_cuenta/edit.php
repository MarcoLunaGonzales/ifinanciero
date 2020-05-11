<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$codigo=$codigo;

$dbh = new Conexion();

$sql="SELECT c.codigo, c.cod_plancuenta,  (select p.nombre from plan_cuentas p where p.codigo=c.cod_plancuenta) as nombrecuenta, (select p.numero from plan_cuentas p where p.codigo=c.cod_plancuenta) as numerocuenta, c.tipo, c.cod_tipoestadocuenta from configuracion_estadocuentas c where c.codigo='$codigo'";
$stmt = $dbh->prepare($sql);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$codigo=$row['codigo'];
	$codigoXZ=$row['cod_plancuenta'];
	$nombreXZ=$row['nombrecuenta'];
	$numeroXZ=$row['numerocuenta'];
	$codPlanCuentaXZ=$row['cod_plancuenta'];
	$codTipoXZ=$row['tipo'];
	$codTipoEstadoCuentaXZ=$row['cod_tipoestadocuenta'];
}


  $i=0;
  echo "<script>var array_cuenta=[],imagen_cuenta=[];</script>";
   $stmtCuenta = $dbh->prepare("SELECT CONCAT(p.codigo,'$$','NNN') as codigo, p.numero, p.nombre from plan_cuentas p where p.nivel=5");
   $stmtCuenta->execute();
   while ($rowCuenta = $stmtCuenta->fetch(PDO::FETCH_ASSOC)) {
    $codigoX=$rowCuenta['codigo'];
    $numeroX=$rowCuenta['numero'];
    $nombreX=$rowCuenta['nombre'];

    ?>
    <script>
     var obtejoLista={
       label:'<?=trim($numeroX)?> - <?=trim($nombreX)?>',
       value:'<?=$codigoX?>'};
       array_cuenta[<?=$i?>]=obtejoLista;
       imagen_cuenta[<?=$i?>]='../assets/img/calc.jpg';
    </script> 
    <?php
    $i=$i+1;  
  }
?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveEdit;?>" method="post">
			<div class="card">
			  <div class="card-header card-header-info card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Editar <?=$moduleNameSingular;?></h4>
				</div>
			  </div>
			  <div class="card-body ">
				<div class="row">
				  <label class="col-sm-2 col-form-label">Cuenta</label>
				  <div class="col-sm-7">
					<div class="form-group">
						<input class="form-control" type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
					  	<input class="form-control" type="text" name="cuenta_auto" id="cuenta_auto" placeholder="[numero] y nombre de cuenta" value="<?=$numeroXZ." - ".$nombreXZ;?>" readonly="true"/>
                      	<input class="form-control" type="hidden" name="cuenta_auto_id" id="cuenta_auto_id" value="<?=$codigoXZ;?>"/>
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Tipo de Estado de Cuenta</label>
				  <div class="col-sm-7">
					<div class="form-group">
					 <select class="selectpicker form-control" name="tipo" id="tipo" data-style="<?=$comboColor;?>" required>
                       <option value=""></option>
					 	<?php
					 	$stmt = $dbh->prepare("select codigo,nombre from tipos_estado_cuenta order by codigo");
								$stmt->execute();
								while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
									$codigoX=$row['codigo'];
									$nombreX=$row['nombre'];
								?>
								<option value="<?=$codigoX;?>" <?=($codigoX==$codTipoEstadoCuentaXZ)?"selected":""?> ><?=$nombreX;?></option>	
								<?php
							  	}
							  	?>
					 </select>
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Debe/Haber</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <select class="selectpicker form-control" name="credito" id="credito" data-style="<?=$comboColor;?>" required>
                           <option value=""></option>
                           <option value="1" <?=($codTipoXZ==1)?"selected":"";?> > Debe </option>
                           <option value="2" <?=($codTipoXZ==2)?"selected":"";?> > Haber </option> 
                       </select>
					</div>
				  </div>
				</div>
				
			  </div>
			  <div  class="card-footer fixed-bottom">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlList2;?>" class="<?=$buttonCancel;?>">Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>