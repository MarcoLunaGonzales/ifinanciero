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
$anio_fila=$_GET['anio_fila'];
$iteracion=$_GET['iteracion'];

if($_GET['tcs']==0){
  $tipo_atributo=1;
  $sqlUpdatePlantilla="UPDATE simulaciones_servicios SET  utilidad_minima='$ut_i',dias_auditoria='$dia',productos='$productos' where codigo=$codSimulacion";
}else{
  $tipo_atributo=2;
  $atributosDias= json_decode($_GET['sitios_dias']);
  $sqlUpdatePlantilla="UPDATE simulaciones_servicios SET  utilidad_minima='$ut_i',dias_auditoria='$dia',sitios='$productos' where codigo=$codSimulacion";
}

$stmtUpdatePlantilla = $dbh->prepare($sqlUpdatePlantilla);
$stmtUpdatePlantilla->execute();

if($cantidad<1){
	$cantidad=1;
}

//SITIOS 0 PRODUCTOS
$sqlDetAt="SELECT * FROM simulaciones_servicios_atributos where cod_simulacionservicio=$codSimulacion";
$stmtDetAt = $dbh->prepare($sqlDetAt);
$stmtDetAt->execute();

  while ($rowPreAt = $stmtDetAt->fetch(PDO::FETCH_ASSOC)) {
    $codigoDetAt=$rowPreAt['codigo'];
    $dbhA = new Conexion();
    $sqlDel="DELETE FROM simulaciones_servicios_atributosdias where cod_simulacionservicioatributo=$codigoDetAt";
    $stmtDel = $dbhA->prepare($sqlDel);
    $stmtDel->execute();
  }
   $dbhA = new Conexion();
  $sqlA="DELETE FROM simulaciones_servicios_atributos where cod_simulacionservicio=$codSimulacion";
  $stmtA = $dbhA->prepare($sqlA);
  $stmtA->execute();
  
  //simulaciones_serviciosauditores
          $nC=cantidadF($atributos);
          for($att=0;$att<$nC;$att++){
              $codigoAtributo=$atributos[$att]->codigo;
              $nombreAtributo=$atributos[$att]->nombre;
              $direccionAtributo=$atributos[$att]->direccion;
              $marcaAtributo=$atributos[$att]->marca;
              $normaAtributo=$atributos[$att]->norma;
              $selloAtributo=$atributos[$att]->sello;

              $codSimulacionServicioAtributo=obtenerCodigoSimulacionServicioAtributo();
              $sqlDetalleAtributos="INSERT INTO simulaciones_servicios_atributos (codigo,cod_simulacionservicio, nombre, direccion, cod_tipoatributo,marca,norma,nro_sello) 
              VALUES ('$codSimulacionServicioAtributo','$codSimulacion', '$nombreAtributo', '$direccionAtributo', '$tipo_atributo','$marcaAtributo','$normaAtributo','$selloAtributo')";
              $stmtDetalleAtributos = $dbh->prepare($sqlDetalleAtributos);
              $stmtDetalleAtributos->execute();

            if($_GET['tcs']==0){
                //$direccionAtributo="";
              }else{
                 $nCDias=cantidadF($atributosDias);
                    for($jj=0;$jj<$nCDias;$jj++){
                       $codigoAtributoDias=$atributosDias[$jj]->codigo_atributo;
                       $anioAtributoDias=$atributosDias[$jj]->anio;
                       $diasAtributoDias=$atributosDias[$jj]->dias;
                       if($codigoAtributoDias==$codigoAtributo){
                        $sqlDetalleAtributos="INSERT INTO simulaciones_servicios_atributosdias (cod_simulacionservicioatributo, dias, cod_anio) 
                        VALUES ('$codSimulacionServicioAtributo', '$diasAtributoDias', '$anioAtributoDias')";
                        $stmtDetalleAtributos = $dbh->prepare($sqlDetalleAtributos);
                        $stmtDetalleAtributos->execute();
                       }           
                    }
              }         
              
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
$sqlDetalles="UPDATE simulaciones_servicios_tiposervicio SET cantidad_editado=$cantidad,monto=$monto,habilitado=$habilitado,cod_tipounidad=$unidad,cod_anio=$anio_fila where codigo=$codigo";
$stmtDetalles = $dbh->prepare($sqlDetalles);
$stmtDetalles->execute();
echo $anio."WWW".$iteracion;
?>
