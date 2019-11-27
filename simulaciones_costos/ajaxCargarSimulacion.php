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

$codigoPlan=$_GET['plantilla_costo'];
$mes=$_GET['mes'];

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
while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {

}


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