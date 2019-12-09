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
if(isset($_GET['admin'])){
	$urlList=$urlList2;
}
$stmt1 = $dbh->prepare("SELECT sc.*,es.nombre as estado,pa.venta_local,pa.venta_externo from simulaciones_costos sc join estados_simulaciones es on sc.cod_estadosimulacion=es.codigo join precios_plantillacosto pa on sc.cod_precioplantilla=pa.codigo where sc.cod_estadoreferencial=1 and sc.codigo='$codigo'");
			$stmt1->execute();
			$stmt1->bindColumn('codigo', $codigoX);
            $stmt1->bindColumn('nombre', $nombreX);
            $stmt1->bindColumn('fecha', $fechaX);
            $stmt1->bindColumn('cod_responsable', $codResponsableX);
            $stmt1->bindColumn('estado', $estadoX);
            $stmt1->bindColumn('cod_plantillacosto', $codigoPlan);
            $stmt1->bindColumn('venta_local', $precioLocalX);
            $stmt1->bindColumn('venta_externo', $precioExternoX);
            $stmt1->bindColumn('cod_precioplantilla', $codPrecioPlan);
            $stmt1->bindColumn('ibnorca', $ibnorcaC);

      while ($row1 = $stmt1->fetch(PDO::FETCH_BOUND)) {
         //plantilla datos      
			$stmt = $dbh->prepare("SELECT p.*, u.abreviatura as unidad,a.abreviatura as area from plantillas_costo p,unidades_organizacionales u, areas a where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and p.codigo='$codigoPlan' order by codigo");
			$stmt->execute();
			$stmt->bindColumn('codigo', $codigoPX);
            $stmt->bindColumn('nombre', $nombreX);
            $stmt->bindColumn('abreviatura', $abreviaturaX);
            $stmt->bindColumn('cod_unidadorganizacional', $codUnidadX);        
            $stmt->bindColumn('cantidad_alumnoslocal', $alumnosX);
            $stmt->bindColumn('cantidad_alumnosexterno', $alumnosExternoX);
            $stmt->bindColumn('cod_area', $codAreaX);
            $stmt->bindColumn('area', $areaX);
            $stmt->bindColumn('unidad', $unidadX);
            $stmt->bindColumn('utilidad_minimalocal', $utilidadIbnorcaX);
            $stmt->bindColumn('utilidad_minimaexterno', $utilidadFueraX);

      }
     while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {

     }
     if($ibnorcaC==1){
  	  $simulacionEn="IBNORCA";
     }else{
  	  $simulacionEn="FUERA DE IBNORCA";
     }
?>
<div id="logo_carga" class="logo-carga" style="display:none;"></div>
<div class="content">
	<div id="contListaGrupos" class="container-fluid">
			<input type="hidden" name="cod_simulacion" id="cod_simulacion" value="<?=$codigo?>">
           <div class="row">
             <div class="col-sm-12">
			  <div class="card">
				<div class="card-header card-header-deafult card-header-text text-center">
					<div class="card-text">
					  <h4 class="card-title"><b>SIMULACION</b></h4>
					</div>
				</div>
				<div class="card-body">
				<?php
				//IVA y IT
				$iva=obtenerValorConfiguracion(1);
				$it=obtenerValorConfiguracion(2);

				//valores de la simulacion


                 $totalFijo=obtenerTotalesPlantilla($codigoPX,1,18); //tipo de costo 1:fijo,2:variable
                 $totalVariable=obtenerTotalesPlantilla($codigoPX,2,18);

                 //calcular cantidad alumnos si no esta registrado
                 if($alumnosX==0||$alumnosX==""||$alumnosX==null||$alumnosExternoX==0||$alumnosExternoX==""||$alumnosExternoX==null){
                 	$porcentajeFinalLocal=0;$alumnosX=0;$alumnosExternoX=0;$porcentajeFinalExterno=0;
                 	while ($porcentajeFinalLocal < $utilidadIbnorcaX || $porcentajeFinalExterno<$utilidadFueraX) {
                 		$alumnosX++;
                 		include "calculoSimulacion.php";
                        $porcentajeFinalLocal=$pUtilidadLocal;
                        $porcentajeFinalExterno=$pUtilidadExterno;

                 	}              
                   //$alumnosX=round((100*($totalFijo[2]*(0.87+($iva/100))))/((100*(($precioLocalX*(1-($it/100)))-($totalVariable[2]*(1+($iva/100)))))-($utilidadIbnorcaX*$precioLocalX)));  
                }else{
                	include "calculoSimulacion.php";
                }
 
                 if($ibnorcaC==1){
                 	$utilidadReferencial=$utilidadIbnorcaX;
                 	$ibnorca_title="EN IBNORCA";
                 }else{
                 	$utilidadReferencial=$utilidadFueraX;
                 	$ibnorca_title="FUERA DE IBNORCA";
                 }
                 $codEstadoSimulacion=4; 
                 if($pUtilidadLocal>=$utilidadIbnorcaX&&$pUtilidadExterno>=$utilidadFueraX){
                    $estiloUtilidad="bg-success text-white";
                    $mensajeText="La simulación SI CUMPLE con la UTILIDAD NETA REQUERIDA DEL ".$utilidadReferencial." % ".$ibnorca_title;
                    $estiloMensaje="text-success font-weight-bold";
                    $codEstadoSimulacion=3;  
                 }else{
                    if($pUtilidadLocal>=$utilidadIbnorcaX){
                        $estiloUtilidadIbnorca="bg-success text-white";
                        if($ibnorcaC==1){
                         $mensajeText="La simulación SI CUMPLE con la UTILIDAD NETA REQUERIDA DEL ".$utilidadReferencial." % ".$ibnorca_title;
                         $estiloMensaje="text-success font-weight-bold";
                         $codEstadoSimulacion=3;
                        }                 
                    }else{
                        $estiloUtilidadIbnorca="bg-danger text-white";
                        if($ibnorcaC==1){
                         $mensajeText="La simulación NO CUMPLE con la UTILIDAD NETA REQUERIDA DEL ".$utilidadReferencial." % ".$ibnorca_title;
                         $estiloMensaje="text-danger font-weight-bold";
                        }                      
                    }
                    if($pUtilidadExterno>=$utilidadFueraX){
                        $estiloUtilidadFuera="bg-success text-white";
                        if($ibnorcaC!=1){
                         $mensajeText="La simulación SI CUMPLE con la UTILIDAD NETA REQUERIDA DEL ".$utilidadReferencial." % ".$ibnorca_title;
                         $estiloMensaje="text-success font-weight-bold";
                         $codEstadoSimulacion=3;
                        }
                    }else{
                        $estiloUtilidadFuera="bg-danger text-white";
                        if($ibnorcaC!=1){
                         $mensajeText="La simulación NO CUMPLE con la UTILIDAD NETA REQUERIDA DEL ".$utilidadReferencial." % ".$ibnorca_title;
                         $estiloMensaje="text-danger font-weight-bold";
                        }                      
                    }
                 }
				?>		
					<div class="row"> 	
					<div class="col-sm-4">	
						<table class="table table-bordered table-condensed">
							<thead>
								<tr class="">
									<th  style="font-size:12px !important;">-</th>
									<th class="bg-table-primary text-white" style="font-size:12px !important;">EN IBNORCA</th>
									<th class="bg-table-total text-white" style="font-size:12px !important;">FUERA DE IBNORCA</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="text-left small bg-table-primary text-white">TOTAL COSTO FIJO+OTROS GASTOS</td><td class="text-right font-weight-bold"><?=number_format($totalFijo[2], 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($totalFijo[3], 2, '.', ',')?></td>
								</tr>
								<tr>
									<td class="text-left small bg-table-primary text-white">COSTO VARIABLE UNITARIO</td><td class="text-right font-weight-bold"><?=number_format($totalVariable[2], 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($totalVariable[3], 2, '.', ',')?></td>
								</tr>
								<tr>
									<td class="text-left small bg-table-primary text-white">COSTO VARIABLE TOTAL</td><td class="text-right font-weight-bold"><?=number_format(($totalVariable[2]*$alumnosX), 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format(($totalVariable[3]*$alumnosExternoX), 2, '.', ',')?></td>
								</tr>
								<tr class="bg-secondary text-white">
									<td class="text-left small">COSTO TOTAL</td><td class="text-right font-weight-bold"><?=number_format($costoTotalLocal, 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($costoTotalExterno, 2, '.', ',')?></td>
								</tr>
								<tr>
									<td class="text-left small bg-table-primary text-white">UTILIDAD MINIMA</td><td class="text-right font-weight-bold"><?=number_format($utilidadIbnorcaX, 2, '.', ',')?> %</td><td class="text-right font-weight-bold"><?=number_format($utilidadFueraX, 2, '.', ',')?> %</td>
								</tr>
								<tr class="bg-secondary text-white">
									<td class="text-left small">PRECIO DE VENTA (IMPORTE)</td><td class="text-right font-weight-bold"><?=number_format($precioLocalX, 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($precioExternoX, 2, '.', ',')?></td>
								</tr>
								<tr class="bg-warning text-dark">
									<td class="text-left small">ALUMNOS</td><td class="text-right font-weight-bold"><?=$alumnosX?></td><td class="text-right font-weight-bold"><?=$alumnosExternoX?></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="col-sm-8">
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
									<td class="text-left small bg-table-primary text-white">INGRESOS POR VENTAS</td><td class="text-right font-weight-bold"><?=number_format($ingresoLocal, 2, '.', ',')?></td><td class="text-right font-weight-bold">100 %</td><td class="text-right font-weight-bold"><?=number_format($ingresoExterno, 2, '.', ',')?></td><td class="text-right font-weight-bold">100 %</td>
								</tr>
								<tr>
									<td class="text-left small bg-table-primary text-white">COSTO DEL SERVICIO</td><td class="text-right font-weight-bold"><?=number_format(($totalVariable[2]*$alumnosX), 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($pCostoLocal, 2, '.', ',')?> %</td><td class="text-right font-weight-bold"><?=number_format(($totalVariable[3]*$alumnosExternoX), 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($pCostoExterno, 2, '.', ',')?> %</td>
								</tr>
								<tr>
									<td class="text-left small bg-table-primary text-white">GASTOS OPERATIVOS</td><td class="text-right font-weight-bold"><?=number_format($costoOperLocal, 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($pCostoOperLocal, 2, '.', ',')?> %</td><td class="text-right font-weight-bold"><?=number_format($costoOperExterno, 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($pCostoOperExterno, 2, '.', ',')?> %</td>
								</tr>
								<tr>
									<td class="text-left small bg-table-primary text-white">UTILIDAD ANTES DE IMPUESTOS</td><td class="text-right font-weight-bold"><?=number_format($utilidadLocal, 2, '.', ',')?></td><td class="text-right font-weight-bold"></td><td class="text-right font-weight-bold"><?=number_format($utilidadExterno, 2, '.', ',')?></td><td class="text-right font-weight-bold"></td>
								</tr>
								<tr>
									<td class="text-left small bg-table-primary text-white">PAGO IMPUESTOS (IVA  <?=$iva?> %)</td><td class="text-right font-weight-bold"><?=number_format($impuestoIvaLocal, 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($pImpLocal, 2, '.', ',')?> %</td><td class="text-right font-weight-bold"><?=number_format($impuestoIvaExterno, 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($pImpExterno, 2, '.', ',')?> %</td>
								</tr>
								<tr>
									<td class="text-left small bg-table-primary text-white">PAGO IMPUESTOS (IT <?=$it?> %)</td><td class="text-right font-weight-bold"><?=number_format($impuestoITLocal, 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($pImpItLocal, 2, '.', ',')?> %</td><td class="text-right font-weight-bold"><?=number_format($impuestoITExterno, 2, '.', ',')?></td><td class="text-right font-weight-bold"><?=number_format($pImpItExterno, 2, '.', ',')?> %</td>
								</tr>
								<tr class="<?=$estiloUtilidad?>">
									<td class="text-left small bg-table-primary text-white">UTILIDAD NETA</td><td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=number_format($utilidadNetaLocal, 2, '.', ',')?></td><td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=number_format($pUtilidadLocal, 2, '.', ',')?> %</td><td class="text-right font-weight-bold <?=$estiloUtilidadFuera?>"><?=number_format($utilidadNetaExterno, 2, '.', ',')?></td><td class="text-right font-weight-bold <?=$estiloUtilidadFuera?>"><?=number_format($pUtilidadExterno, 2, '.', ',')?> %</td>
								</tr>
							</tbody>
						</table>
					<div class="row div-center">
						<h5><p class="<?=$estiloMensaje?>"><?=$mensajeText?></p></h5>
					</div>	
					</div>

				  	<div class="card-footer fixed-bottom">
						<a href="../<?=$urlList;?>" class="btn btn-default">Cancelar</a>

				  	</div>
				 </div>
			    </div><!--div end card-->			
               </div>
            </div>
	</div>
</div>