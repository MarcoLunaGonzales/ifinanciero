<?php
error_reporting(0);

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
if(isset($_GET['cod'])){
	$codigo=$_GET['cod'];
}else{
	$codigo=0;
}
			$stmt = $dbh->prepare("SELECT p.*,e.nombre as estado_plantilla, u.abreviatura as unidad,a.abreviatura as area from plantillas_costo p,unidades_organizacionales u, areas a,estados_plantillascosto e
  where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and e.codigo=p.cod_estadoplantilla and p.codigo='$codigo' order by codigo");
			$stmt->execute();
			$stmt->bindColumn('codigo', $codigoX);
            $stmt->bindColumn('nombre', $nombreX);
            $stmt->bindColumn('abreviatura', $abreviaturaX);
            $stmt->bindColumn('cod_unidadorganizacional', $codUnidadX);
            $stmt->bindColumn('cod_area', $codAreaX);
            $stmt->bindColumn('area', $areaX);
            $stmt->bindColumn('unidad', $unidadX);
            $stmt->bindColumn('estado_plantilla', $estadoX);
            $stmt->bindColumn('utilidad_minimalocal', $utilidadIbnorcaX);
            $stmt->bindColumn('utilidad_minimaexterno', $utilidadFueraX);
            $stmt->bindColumn('cantidad_alumnoslocal', $alumnosLocalX);
            $stmt->bindColumn('cantidad_alumnosexterno', $alumnosExternoX);
            $stmt->bindColumn('cantidad_cursosmes', $cantidadCursosMesX);
?>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<form id="formRegDet" class="form-horizontal" action="saveEdit.php" method="post" enctype="multipart/form-data">
<div class="content">
	<div id="contListaGrupos" class="container-fluid">
			<input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">
			<input type="hidden" name="cod_plantilla" id="cod_plantilla" value="<?=$codigo?>">
            
			<div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text">
					  <h4 class="card-title">Registrar <?=$moduleNameSingular;?></h4>
					</div>
				</div>
				<div class="card-body ">
                     <div class="row">
					<?php while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {?>
					<input type="hidden" name="cod_mescurso" id="cod_mescurso" value="<?=$cantidadCursosMesX?>">
					<input class="form-control" type="hidden" name="cod_unidad" value="<?=$codUnidadX?>" id="cod_unidad" readonly/>
					<input class="form-control" type="hidden" name="cod_area" value="<?=$codAreaX?>" id="cod_area" readonly/>
						<div class="col-sm-2">
							<div class="form-group">
						  		<label class="bmd-label-static">Nombre</label>
					  			<input class="form-control" type="text" name="nombre" value="<?=$nombreX?>" id="nombre"/>
							</div>
						</div>

						<div class="col-sm-1">
							<div class="form-group">
						  		<label class="bmd-label-static">Abreviatura</label>
						  		<input class="form-control" type="text" name="abreviatura" value="<?=$abreviaturaX?>" id="abreviatura"/>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group has-success">
						  		<label class="bmd-label-static">Utilidad M&iacute;nima</label>
						  		<input class="form-control" type="number" step="0.001" name="utilidad_ibnorca" value="<?=$utilidadIbnorcaX?>" id="utilidad_ibnorca"/>
						  		<span class="form-control-feedback">%</span>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
						  		<label class="bmd-label-static">N&uacute;mero de Alumnos</label>
						  		<input class="form-control" type="number" name="alumnos_ibnorca" value="<?=$alumnosLocalX?>" id="alumnos_ibnorca"/>
						  		
							</div>
						</div>
						<!--<div class="col-sm-2">
							<div class="form-group has-success">
						  		<label class="bmd-label-static">Utilidad Min FI</label>-->
						  		<input class="form-control" type="hidden" step="0.001" name="utilidad_fuera" value="<?=$utilidadFueraX?>" id="utilidad_fuera"/>
						  		<!--<span class="form-control-feedback">%</span>
							</div>
						</div>-->
                        <div class="col-sm-2">
							<div class="form-group">
						  		<label class="bmd-label-static">Estado</label>
						  		<input class="form-control" type="text" readonly name="estado_plan" value="<?=$estadoX?>" id="estado_plan"/>
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
						  		<label class="bmd-label-static">Unidad</label>
						  		<input class="form-control" type="text" name="unidad" value="<?=$unidadX?>" id="unidad" readonly/>
							</div>
						</div>

						<div class="col-sm-2">
				        	<div class="form-group">
						  		<label class="bmd-label-static">Area</label>
						  		<input class="form-control" type="text" name="area" value="<?=$areaX?>" id="area" readonly/>
							</div>
				      	</div>
				      	<?php 
				      	if($codAreaX==13){
				      		$codOficina=0;
				      		if(obtenerValorConfiguracion(52)!=1){
				      			$codOficina=$codUnidadX;
				      		}
                           $precioPresupuestado=number_format(obtenerPresupuestoEjecucionPorAreaAcumulado($codOficina,$codAreaX,$globalNombreGestion,12,1)['presupuesto'], 0, '.', '');
                           $precioPresupuestadoTabla=number_format(obtenerPresupuestoEjecucionPorAreaAcumulado($codOficina,$codAreaX,$globalNombreGestion,12,1)['presupuesto'], 0, '.', ',');
                         }else{
                          $precioPresupuestado=0;	
                          $precioPresupuestadoTabla=0;
                         }
				      	?>
				      </div>
				      <div class="row">
				      	
						<!--<div class="col-sm-2">
							<div class="form-group">
						  		<label class="bmd-label-static">Alumnos FUERA IBNORCA</label>-->
						  		<input class="form-control" type="hidden" name="alumnos_fuera" value="<?=$alumnosExternoX?>" id="alumnos_fuera"/>
						  	<input class="form-control" type="hidden" name="presupuestado_plan" value="<?=$precioPresupuestado?>" id="presupuestado_plan"/>	 
							<!--</div>
						</div>-->
						<div class="col-sm-2 float-right">
							<div class="">
						  		<button type="button" class="btn btn-success" onclick="mostrarPreciosPlantilla()"><span class="material-icons">attach_money</span> Lista de Precios</button>
							</div>
						</div>
						<div class="col-sm-1 float-right">
							<div class="">
						  		<a href="#" title="Ayuda" class="btn btn-default btn-fab btn-round" onclick="ayudaPlantilla()"><span class="material-icons">help_outline</span></a>
							</div>
						</div>
						
				      	<?php } ?>
					</div>

				</div>
			</div>
           <div class="row">
             <div class="col-sm-8">
			  <div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text">
					  <h6 class="card-title">Grupos</h6>
					</div>
				</div>
				<div class="card-body">
					<fieldset id="fiel" style="width:100%;border:0;">
							<button title="Agregar (shift+n)" type="button" name="add" class="btn btn-warning btn-round btn-fab" onClick="addGrupoPlantilla(this)">
                      		  <i class="material-icons">add</i>
		                    </button>  
						<div id="div">	
							
							<div class="h-divider">
	        				</div>
		 					
	 					</div>
	 					<?php
                       $stmt = $dbh->prepare("SELECT p.codigo, p.cod_tipocosto, p.nombre,p.abreviatura,p.cod_plantillacosto from plantillas_gruposcosto p where p.cod_plantillacosto=$codigo order by p.codigo");
                         $stmt->execute();
                         $idFila=1;
                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          $codigoCostoX=$row['codigo'];
                          $codTipoCostoX=$row['cod_tipocosto'];
                          $nombreCostoX=$row['nombre'];
                          $abreviaturaCostoX=$row['abreviatura'];
                          $codPlantillaCostoX=$row['cod_plantillacosto'];
                          ?>
                          <script>numFilas++;cantidadItems++;</script>
                          <script>var ndet=[];itemDetalle.push(ndet);</script>
						 <?php
						      $stmt2 = $dbh->prepare("SELECT * FROM plantillas_grupocostodetalle where cod_plantillagrupocosto=$codigoCostoX");
				              $stmt2->execute();
				              $idFilas=0;
				           
						?>
                          <div id="div<?=$idFila?>" class="col-md-12">
	<div class="row">

		<div class="col-sm-3">
        	<div class="form-group">
	        <select class="selectpicker form-control form-control-sm" name="tipo_costo<?=$idFila;?>" id="tipo_costo<?=$idFila;?>" data-style="<?=$comboColor;?>" required>
	        	                <option disabled value="">Tipo</option>
			  	              <?php        
                                if($codTipoCostoX==1){
                                ?><option value="1" selected>Fijo</option>
                                  <option value="2">Variable</option>
                                  <option value="3">Manual</option>
                                <?php
                                }else{
                                	if($codTipoCostoX==2){
                                 ?><option value="1">Fijo</option>
                                  <option value="2" selected>Variable</option>
                                  <option value="3">Manual</option>
                                <?php
                                  }else{
                                  	 ?><option value="1">Fijo</option>
                                      <option value="2">Variable</option>
                                      <option value="3" selected>Manual</option>
                                    <?php
                                  }
                                }
			  	              ?>
					
			</select>
			</div>
      	</div>
		<div class="col-sm-5">
            <div class="form-group">
            	<label for="nombre_grupo<?=$idFila;?>" class="bmd-label-floating">Nombre de grupo</label>			
          		<input class="form-control" type="text" value="<?=$nombreCostoX?>" name="nombre_grupo<?=$idFila;?>" id="nombre_grupo<?=$idFila;?>" onkeyup="mostrarDetalle(<?=$idFila;?>);" required>	
			</div>
      	</div>

		<div class="col-sm-2">
            <div class="form-group">
            	<label for="abreviatura_grupo<?=$idFila;?>" class="bmd-label-floating">Abreviatura</label>			
          		<input class="form-control" type="text" value="<?=$abreviaturaCostoX?>" name="abreviatura_grupo<?=$idFila;?>" id="abreviatura_grupo<?=$idFila;?>" required> 	
			</div>
      	</div>
		<div class="col-sm-2">
		  <div class="btn-group">
		  	<a href="#" id="boton_det<?=$idFila;?>" onclick="listDetalle(<?=$idFila;?>);" class="btn btn-just-icon btn-primary btn-link">
               <i class="material-icons">view_list</i><span id="ndet<?=$idFila;?>" class="count bg-warning">0</span>
             </a>
			<a rel="tooltip" href="#" class="btn btn-just-icon btn-danger btn-link" id="boton_remove<?=$idFila;?>" onclick="minusGrupoPlantilla('<?=$idFila;?>');">
            	<i class="material-icons">remove_circle</i>
	        </a>
	        <a rel="tooltip" id="boton_det_list<?=$idFila;?>" href="#" class="btn btn-just-icon btn-info btn-link" onclick="mostrarDetalle('<?=$idFila;?>');">
            	<i class="material-icons">remove_red_eye</i>
	        </a>
	      </div>  
		</div>

	</div>
	<div class="h-divider"></div>
</div>


                          <?php
                    while ($rowDet = $stmt2->fetch(PDO::FETCH_ASSOC)) {
				                    $codigo_partida=$rowDet['cod_partidapresupuestaria'];

				                    $nombrePartida=namePartidaPres($rowDet['cod_partidapresupuestaria']);         

				                    $tipo=$rowDet['tipo_calculo'];
				                    $monto_i=$rowDet['monto_local'];
				                    $monto_fi=$rowDet['monto_externo'];
				                    $monto_cal=$rowDet['monto_calculado'];
				                    $idFilas=$idFilas+1;
				                    ?><script>abrirDetalleCosto(<?=$idFila?>,'<?=$codigo_partida?>','<?=$nombrePartida?>','<?=$tipo?>',<?=$monto_i?>,<?=$monto_fi?>,<?=$monto_cal?>);</script><?php
			  	              }
			  	      ?><script>$("#cantidad_filas").val(<?=$idFila?>);</script><?php              
                          $idFila++;
                      }
	 					?>
		            </fieldset>

				  	<div class="card-footer fixed-bottom">
						<button type="submit" class="<?=$buttonMorado;?>">Guardar</button>
						<a href="../<?=$urlList;?>" class="<?=$buttonCancel;?>">Volver</a>

				  	</div>
				 </div>
			    </div>			
               </div>
               <div class="col-sm-4">
            	<div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text" id="cabezadetalle">
					  <h6 class="card-title">Detalle</h6>
					</div>
				</div>
				<div class="card-body">
					<table class="table table-condensed table-striped text-small">
						<thead>
							<tr>
								<th>Partida</th>
								<th>Tipo</th>
								<th class="text-right">M x Mes</th>
								<th class="text-right">M x Modulo</th>
								<th class="text-right">M x Persona</th>
							</tr>
						</thead>
						<tbody id="cuerpoDetalle">
						</tbody>

					</table>
				 </div>
			    </div>	
               </div>
            </div>
	</div>
</div>
</form>

<?php
require_once 'modal.php';
?>
