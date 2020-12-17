<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_GET["codigo"];
session_start();

$codPlantillaCosto=$_GET["codigo"];
$codSimulacion=$_GET["cod_sim"];
$ut_i=$_GET['ut_i'];
$ut_f=$_GET['ut_f'];
$al_i=$_GET['al_i'];
$al_f=$_GET['al_f'];
$precio_p=$_GET['precio_p'];
$dias_curso=$_GET['dias_curso'];
$precio_pedit=$_GET['precio_pedit'];
$modal_modulos=$_GET['modal_modulos'];

$sqlUpdate="UPDATE precios_simulacioncosto SET  venta_local='$precio_pedit' where codigo=$precio_p";
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();

$sqlUpdatePlantilla="UPDATE simulaciones_costos SET  dias_curso=$dias_curso,cod_precioplantilla='$precio_p',utilidad_minimalocal='$ut_i',cantidad_alumnoslocal='$al_i',cantidad_modulos=$modal_modulos where codigo=$codSimulacion";
$stmtUpdatePlantilla = $dbh->prepare($sqlUpdatePlantilla);
$stmtUpdatePlantilla->execute();


$sqlDetalles="SELECT * FROM simulaciones_detalle where cod_simulacioncosto=$codSimulacion and editado_alumno!=0";
$stmtDetalles = $dbh->prepare($sqlDetalles);
$stmtDetalles->execute();

 while ($rowDet = $stmtDetalles->fetch(PDO::FETCH_ASSOC)) {
    $codDet=$rowDet['codigo'];
    $partida=$rowDet['cod_partidapresupuestaria'];
    $codTipo=$rowDet['cod_tipo'];
    $cantidadAlumnos=$rowDet['cantidad'];
    if($codTipo==1||$codTipo==4){
     $cantidadAlumnos=$al_i;
    }
    
    $montoDet=$rowDet['editado_alumno']*$cantidadAlumnos;
    if($codTipo==4){
      $montoDet=$rowDet['editado_alumno']*$cantidadAlumnos*$dias_curso;
    } 
    $cuenta=$rowDet['cod_cuenta'];
    $dbhDet = new Conexion();
    /*$sqlUpdateDetalle="UPDATE simulaciones_detalle SET  monto_unitario='$montoDet',monto_total='$montoDet' where codigo=$codDet";
    $stmtUpdateDetalle = $dbhDet->prepare($sqlUpdateDetalle);
    $stmtUpdateDetalle->execute();*/

    $sqlUpdateDetalle2="UPDATE simulaciones_detalle SET  cantidad='$cantidadAlumnos',monto_unitario='$montoDet',monto_total='$montoDet' where codigo=$codDet";
    $stmtUpdateDetalle2 = $dbhDet->prepare($sqlUpdateDetalle2);
    $stmtUpdateDetalle2->execute();

//insertar en cuentas_simulacion
    $dbhC = new Conexion();
$sqlCuentas="SELECT * FROM cuentas_simulacion where cod_simulacioncostos=$codSimulacion";
$stmtCuentas = $dbhC->prepare($sqlCuentas);
$stmtCuentas->execute();
 while ($rowCuentas = $stmtCuentas->fetch(PDO::FETCH_ASSOC)) {
    $simulacion=$rowCuentas['codigo'];  
    $montoTotal=0;
   $detallesMontos=obtenerMontosCuentasDetalleSimulacionCostosPartidaHabilitado($codSimulacion,$partida);
while ($row = $detallesMontos->fetch(PDO::FETCH_ASSOC)) {
	if($row['cod_cuenta']==$cuenta){
    if($row['habilitado']==0){
      $montoTotal+=0;
    }else{
		$montoTotal+=$row['monto'];    
    }
  }
 }
  $dbh2 = new Conexion();
    $sqlUpdate="UPDATE cuentas_simulacion SET  monto_local='$montoTotal' where codigo=$simulacion and cod_plancuenta=$cuenta";  
      $stmtUpdate = $dbh2->prepare($sqlUpdate);
      $stmtUpdate->execute();
 }
}

 //costos Fijos en tabla
      $sqlDelete="DELETE FROM simulaciones_cf where cod_simulacioncosto=$codSimulacion";  
      $stmtDelete = $dbh->prepare($sqlDelete);
      $stmtDelete->execute();

      $cuentasFijas=obtenerListaCuentasPlantillasCostoFijo($codPlantillaCosto);
      while ($rowFijo = $cuentasFijas->fetch(PDO::FETCH_ASSOC)) {
         $nombreCuentaFijo=$rowFijo['nombre'];
         $numeroCuentaFijo=$rowFijo['numero'];
         $codCuentaFijo=$rowFijo['cod_cuenta'];
         $codPartidaFijo=$rowFijo['cod_partidapresupuestaria'];
         $tipoFijo=$rowFijo['tipo'];

         $precioLocalX=obtenerPrecioSimulacionCostoGeneral($codSimulacion);
         $precioRegistrado=obtenerPrecioRegistradoPlantillaCosto($codPlantillaCosto);
         $nCursos=obtenerCantidadCursosPlantillaCosto($codPlantillaCosto); 
         $porcentPrecios=($precioLocalX)/($precioRegistrado);
         if($tipoFijo==1){ 
         $anioSim= date("Y");  
         $monto=ejecutadoEgresosMes($globalUnidad,((int)$anioSim-1),12,13,1,$numeroCuentaFijo);          
         }else{
          $monto=obtenerListaCuentasPlantillasCostoFijoManual($codCuentaFijo,$codPartidaFijo,$codPlantillaCosto);
         }
         $montoUnidad=$monto*$porcentPrecios; 
         $dbh = new Conexion();
         $sqlFijos="INSERT INTO simulaciones_cf (cod_simulacionservicio, cod_simulacioncosto,cod_partidapresupuestaria,cod_cuenta,monto,cantidad,monto_total) 
         VALUES (0,'".$codSimulacion."','".$codPartidaFijo."','".$codCuentaFijo."','".$montoUnidad."',1,'".$montoUnidad."')";
         $stmtFijos = $dbh->prepare($sqlFijos);
         $stmtFijos->execute();
      }    


$precios=obtenerPreciosPorCodigo($precio_p);
echo $precios[0]."$$$".$precios[1];
?>
