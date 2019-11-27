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

$fechaActual=date("Y-m-d");
$dbh = new Conexion();
if(isset($_GET['cod'])){
	$codigo=$_GET['cod'];
}else{
	$codigo=0;
}
$stmt1 = $dbh->prepare("SELECT sc.*,es.nombre as estado from simulaciones_costos sc join estados_simulaciones es on sc.cod_estadosimulacion=es.codigo where sc.cod_estadoreferencial=1 and sc.codigo='$codigo'");
			$stmt1->execute();
			$stmt1->bindColumn('codigo', $codigoX);
            $stmt1->bindColumn('nombre', $nombreX);
            $stmt1->bindColumn('fecha', $fechaX);
            $stmt1->bindColumn('cod_responsable', $codResponsableX);
            $stmt1->bindColumn('estado', $estadoX);
            $stmt1->bindColumn('cod_plantillacosto', $codigoPlan);

      while ($row1 = $stmt1->fetch(PDO::FETCH_BOUND)) {
         //plantilla datos      
			$stmt = $dbh->prepare("SELECT p.*, u.abreviatura as unidad,a.abreviatura as area from plantillas_costo p,unidades_organizacionales u, areas a where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and p.codigo='$codigoPlan' order by codigo");
			$stmt->execute();
			$stmt->bindColumn('codigo', $codigoPX);
            $stmt->bindColumn('nombre', $nombreX);
            $stmt->bindColumn('abreviatura', $abreviaturaX);
            $stmt->bindColumn('cod_unidadorganizacional', $codUnidadX);
            $stmt->bindColumn('precio_ventalocal', $precioLocalX);
            $stmt->bindColumn('precio_ventaexterno', $precioExternoX);
            $stmt->bindColumn('cantidad_alumnos', $alumnosX);
            $stmt->bindColumn('cod_area', $codAreaX);
            $stmt->bindColumn('area', $areaX);
            $stmt->bindColumn('unidad', $unidadX);

      }
     
?>
<div id="logo_carga" class="logo-carga" style="display:none;"></div>
<div class="content">
	<div id="contListaGrupos" class="container-fluid">
			<input type="hidden" name="cod_simulacion" id="cod_simulacion" value="<?=$codigo?>">
            <div class="row"><div class="card col-sm-6">
				<div class="card-header card-header-warning card-header-text">
					<div class="card-text">
					  <h4 class="card-title">Datos de la Simulacion</h4>
					</div>
				</div>
				<div class="card-body ">
					<div class="row">
					<?php
                    $responsable=namePersonal($codResponsableX);
						?>
						<div class="col-sm-4">
							<div class="form-group">
						  		<label class="bmd-label-static">Nombre</label>
					  			<input class="form-control" type="text" name="nombre" value="<?=$nombreX?>" id="nombre"/>
							</div>
						</div>

						<div class="col-sm-4">
							<div class="form-group">
						  		<label class="bmd-label-static">Responsable</label>
						  		<input class="form-control" type="text" name="responsable" readonly value="<?=$responsable?>" id="responsable"/>
							</div>
						</div>

						<div class="col-sm-2">
							<div class="form-group">
						  		<label class="bmd-label-static">Fecha</label>
						  		<input class="form-control" type="text" name="fecha" value="<?=$fechaX?>" id="fecha" readonly/>
							</div>
						</div>

						<div class="col-sm-2">
				        	<div class="form-group">
						  		<label class="bmd-label-static">Estado</label>
						  		<input class="form-control" type="text" name="estado" value="<?=$estadoX?>" id="estado" readonly/>
							</div>
				      	</div>
					</div>
				</div>
			</div>
			<div class="card col-sm-6">
				<div class="card-header card-header-info card-header-text">
					<div class="card-text">
					  <h4 class="card-title">Datos de la Plantilla</h4>
					</div>
				</div>
				<div class="card-body ">
                     <div class="row">
					<?php while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {?>
					<input type="hidden" name="cod_plantilla" id="cod_plantilla" value="<?=$codigoPX?>">
						<div class="col-sm-4">
							<div class="form-group">
						  		<label class="bmd-label-static">Nombre Plantilla</label>
					  			<input class="form-control" type="text" name="nombre_plan" value="<?=$nombreX?>" id="nombre_plan" READONLY />
							</div>
						</div>

						<div class="col-sm-4">
							<div class="form-group">
						  		<label class="bmd-label-static">Abreviatura</label>
						  		<input class="form-control" type="text" name="abreviatura_plan" value="<?=$abreviaturaX?>" READONLY id="abreviatura_plan"/>
							</div>
						</div>

						<div class="col-sm-2">
							<div class="form-group">
						  		<label class="bmd-label-static">Unidad</label>
						  		<input class="form-control" type="text" name="unidad_plan" value="<?=$unidadX?>" id="unidad_plan" readonly/>
							</div>
						</div>

						<div class="col-sm-2">
				        	<div class="form-group">
						  		<label class="bmd-label-static">Area</label>
						  		<input class="form-control" type="text" name="area_plan" value="<?=$areaX?>" id="area_plan" readonly/>
							</div>
				      	</div>
				      	<?php } ?>
					</div>
                    <div class="row">
                    	<div class="col-sm-8">
                    		 <div class="form-group">
                                <select class="selectpicker form-control" onchange="presioneBoton()" name="plantilla_costo" id="plantilla_costo" data-style="<?=$comboColor;?>"  data-live-search="true" title="-- Elija una plantilla --" data-style="select-with-transition" data-actions-box="true"required>
                                <?php
                                 $stmt = $dbh->prepare("SELECT p.*, u.abreviatura as unidad,a.abreviatura as area from plantillas_costo p,unidades_organizacionales u, areas a where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and p.precio_ventalocal!=0 and p.precio_ventaexterno!=0 and p.cantidad_alumnos!=0 order by codigo");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  $abrevX=$row['abreviatura'];
                                  $unidadX=$row['unidad'];
                                  $areaX=$row['area'];
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$nombreX;?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; #<?=$unidadX?> @<?=$areaX?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>
                              </div>
                              <div id="mensaje"></div>
                    	</div>
                    	<div class="col-sm-4">
                    	  <div class="form-group">	
                    		<a href="#" onclick="cargarPlantillaSimulacion(18)" class="btn btn-warning text-dark btn-block">Simular Plantilla</a>
                    	  </div>
                    	</div>
                    </div>
				</div>
			</div>
		   </div>
           <div class="row">
             <div class="col-sm-8 div-center">
			  <div class="card">
				<div class="card-header card-header-deafult card-header-text text-center">
					<div class="card-text">
					  <h4 class="card-title"><b>SIMULACION</b></h4>
					</div>
				</div>
				<div class="card-body">
					<div class="table-responsive" id="div_simulacion">
				<?php
				//IVA y IT
				$iva=obtenerValorConfiguracion(1);
				$it=obtenerValorConfiguracion(2);

				//valores de la simulacion


                 $totalFijo=obtenerTotalesPlantilla($codigoPX,1,18); //tipo de costo 1:fijo,2:variable
                 $totalVariable=obtenerTotalesPlantilla($codigoPX,2,18);

                 //ingreso por ventas
                 $ingresoLocal=$precioLocalX*$alumnosX;
                 $ingresoExterno=$precioExternoX*$alumnosX;
                 //porcentajes costo servicio
                 $costoLocal=($totalVariable[2]*$alumnosX)/($ingresoLocal);
                 $pCostoLocal=$costoLocal*100;
                 $costoExterno=($totalVariable[3]*$alumnosX)/($ingresoExterno);
                 $pCostoExterno=$costoExterno*100;

                 //gastos operativos
                 //TOTAL DE COSTO FIJO
                 $costoOperLocal=$totalFijo[2]*0.87;
                 $pCostoOperLocal=($costoOperLocal/$ingresoLocal)*100;

                 $costoOperExterno=$totalFijo[3]*0.87;
                 $pCostoOperExterno=($costoOperExterno/$ingresoLocal)*100;

                 //utilidad antes de impuestos
                 $utilidadLocal=$ingresoLocal-($totalVariable[2]*$alumnosX)-$costoOperLocal;
                 $utilidadExterno=$ingresoExterno-($totalVariable[3]*$alumnosX)-$costoOperExterno;


                 // impuesto iva
                 $costoTotalLocal=$totalFijo[2]+($totalVariable[2]*$alumnosX);
                 $costoTotalExterno=$totalFijo[3]+($totalVariable[3]*$alumnosX);

                 $impuestoIvaLocal=$costoTotalLocal*($iva/100);
                 $impuestoIvaExterno=$costoTotalExterno*($iva/100);
                      //porcentaje iva
                 $pImpLocal=($impuestoIvaLocal/$ingresoLocal)*100;
                 $pImpExterno=($impuestoIvaExterno/$ingresoExterno)*100;


                 //impuesto transacciones
                 $impuestoITLocal=($ingresoLocal/0.87)*($it/100);
                 $impuestoITExterno=($ingresoExterno/0.87)*($it/100);
                 $pImpItLocal=($impuestoITLocal/$ingresoLocal)*100;
                 $pImpItExterno=($impuestoITExterno/$ingresoExterno)*100;
                  
                 //UTILIDAD NETA 
                 $utilidadNetaLocal=$utilidadLocal-$impuestoIvaLocal-$impuestoITLocal;
                 $utilidadNetaExterno=$utilidadExterno-$impuestoIvaExterno-$impuestoITExterno;
                 $pUtilidadLocal=($utilidadNetaLocal/$ingresoLocal)*100;
                 $pUtilidadExterno=($utilidadNetaExterno/$ingresoExterno)*100;

				?>		
						<table class="table table-bordered table-condensed">
							<thead>
								<tr class="">
									<th>-</th>
									<th colspan="2" class="bg-table-primary text-white">EN IBNORCA</th>
									<th colspan="2" class="bg-table-total text-white">FUERA DE IBNORCA</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="text-left">INGRESOS POR VENTAS</td><td class="text-right font-weight-bold"><?=number_format($ingresoLocal, 2, '.', ',')?></td><td class="text-right font-weight-bold">100 %</td><td class="text-right font-weight-bold"><?=number_format($ingresoExterno, 2, '.', ',')?></td><td class="text-right font-weight-bold">100 %</td>
								</tr>
								<tr>
									<td class="text-left">COSTO DEL SERVICIO</td><td class="text-right font-weight-bold"><?=number_format(($totalVariable[2]*$alumnosX), 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($pCostoLocal, 2, '.', ',')?> %</td><td class="text-right font-weight-bold"><?=number_format(($totalVariable[3]*$alumnosX), 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($pCostoExterno, 2, '.', ',')?> %</td>
								</tr>
								<tr>
									<td class="text-left">GASTOS OPERATIVOS</td><td class="text-right font-weight-bold"><?=number_format($costoOperLocal, 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($pCostoOperLocal, 2, '.', ',')?> %</td><td class="text-right font-weight-bold"><?=number_format($costoOperExterno, 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($pCostoOperExterno, 2, '.', ',')?> %</td>
								</tr>
								<tr>
									<td class="text-left">UTILIDAD ANTES DE IMPUESTOS</td><td class="text-right font-weight-bold"><?=number_format($utilidadLocal, 2, '.', ',')?></td><td class="text-right font-weight-bold"></td><td class="text-right font-weight-bold"><?=number_format($utilidadExterno, 2, '.', ',')?></td><td class="text-right font-weight-bold"></td>
								</tr>
								<tr>
									<td class="text-left">PAGO IMPUESTOS (IVA  <?=$iva?> %)</td><td class="text-right font-weight-bold"><?=number_format($impuestoIvaLocal, 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($pImpLocal, 2, '.', ',')?> %</td><td class="text-right font-weight-bold"><?=number_format($impuestoIvaExterno, 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($pImpExterno, 2, '.', ',')?> %</td>
								</tr>
								<tr>
									<td class="text-left">PAGO IMPUESTOS (IT <?=$it?> %)</td><td class="text-right font-weight-bold"><?=number_format($impuestoITLocal, 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($pImpItLocal, 2, '.', ',')?> %</td><td class="text-right font-weight-bold"><?=number_format($impuestoITExterno, 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($pImpItExterno, 2, '.', ',')?> %</td>
								</tr>
								<tr>
									<td class="text-left">UTILIDAD NETA</td><td class="text-right font-weight-bold"><?=number_format($utilidadNetaLocal, 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($pUtilidadLocal, 2, '.', ',')?> %</td><td class="text-right font-weight-bold"><?=number_format($utilidadNetaExterno, 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($pUtilidadExterno, 2, '.', ',')?> %</td>
								</tr>
							</tbody>
						</table>
					</div>
				  	<div class="card-footer fixed-bottom">
						<a onclick="guardarSimulacion()" class="btn btn-info">Guardar</a>
						<a onclick="guardarSimulacion('enviar')" class="btn btn-warning text-dark">Enviar Simulacion</a>
						<a href="../<?=$urlList;?>" class="btn btn-default">Cancelar</a>

				  	</div>
				 </div>
			    </div><!--div end card-->			
               </div>
            </div>
	</div>
</div>

<?php
require_once 'modal.php';
?>
