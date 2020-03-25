<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';
set_time_limit (0);
$dbh = new Conexion();

$codigo=$_GET["codigo"];
session_start();

$codPlantillaCosto=$_GET["plantilla"];
$codSimulacion=$_GET["simulacion"];
$ut_i=$_GET['utilidad'];
$dia=$_GET['dia'];


$monto=$_GET['monto'];
$cantidad=$_GET['cantidad'];

$habilitado=$_GET['habilitado'];
$unidad=$_GET['unidad'];
$fijo=$_GET['precio_fijo'];
$productos="";
$atributos= json_decode($_GET['productos']);
$anio=$_GET['anio'];
$iteracion=$_GET['iteracion'];

if($_GET['tcs']==0){
  $sqlUpdatePlantilla="UPDATE simulaciones_servicios SET  utilidad_minima='$ut_i',dias_auditoria='$dia',productos='$productos' where codigo=$codSimulacion";
}else{
  $sqlUpdatePlantilla="UPDATE simulaciones_servicios SET  utilidad_minima='$ut_i',dias_auditoria='$dia',sitios='$productos' where codigo=$codSimulacion";
}

$stmtUpdatePlantilla = $dbh->prepare($sqlUpdatePlantilla);
$stmtUpdatePlantilla->execute();

if($cantidad<1){
	$cantidad=1;
}

//SITIOS 0 PRODUCTOS
   $dbhA = new Conexion();
  $sqlA="DELETE FROM simulaciones_servicios_atributos where cod_simulacionservicio=$codSimulacion";
  $stmtA = $dbhA->prepare($sqlA);
  $stmtA->execute();
  
  //simulaciones_serviciosauditores
          $nC=cantidadF($atributos);
          for($att=0;$att<$nC;$att++){
              $nombreAtributo=$atributos[$att]->nombre;
            if($_GET['tcs']==0){
                $direccionAtributo="";
              }else{
                $direccionAtributo=$atributos[$att]->direccion;
              }         
              $sqlDetalleAtributos="INSERT INTO simulaciones_servicios_atributos (cod_simulacionservicio, nombre, direccion, cod_tipoatributo) 
              VALUES ('$codSimulacion', '$nombreAtributo', '$direccionAtributo', '$tipo_atributo')";
              $stmtDetalleAtributos = $dbh->prepare($sqlDetalleAtributos);
              $stmtDetalleAtributos->execute();
         }
         //FIN simulaciones_serviciosauditores


if($fijo!=""){
	$cliente=obtenerCodigoClienteSimulacion($codSimulacion);
	//$productosLista=explode(",", $productos);
        $codTC=obtenerTipoCliente($cliente);
        $nacional=obtenerTipoNacionalCliente($cliente);
        $suma=0;
        for ($i=0; $i < count($atributos); $i++) {
          $aux=obtenerCostoTipoClienteSello(($i+1),$codTC,$nacional);
           if($aux==0){
            $aux=$aux2;
           }else{            
            $aux2=$aux;
           }
           $suma+=$aux;          
        }
       $cantidad=1;
       $monto=$suma; 
}
$sqlDetalles="UPDATE simulaciones_servicios_tiposervicio SET cantidad_editado=$cantidad,monto=$monto,habilitado=$habilitado,cod_tipounidad=$unidad where codigo=$codigo";
$stmtDetalles = $dbh->prepare($sqlDetalles);
$stmtDetalles->execute();
echo $anio."WWW".$iteracion;
?>
