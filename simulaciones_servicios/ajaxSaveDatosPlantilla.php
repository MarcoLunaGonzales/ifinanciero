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

$extlocal=$_GET['extlocal'];
if($extlocal==1){
  $monto=$_GET['monto'];
  $montoe=$_GET['montoe'];
}else{
  $monto=$_GET['montol'];
  $montoe=$_GET['monto'];
}

$cantidad=$_GET['cantidad'];
$cantidadT=$_GET['cantidadT'];
$dias_aud=$_GET['dias'];
$habilitado=$_GET['habilitado'];
$productos="";
$atributos= json_decode($_GET['productos']);
/*if($dia<$dias_aud){
  $dias_aud=$dia;
}*/
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


$sqlDetalles="UPDATE simulaciones_servicios_auditores SET cantidad_editado=$cantidad,monto=$monto,habilitado=$habilitado,dias=$dias_aud,monto_externo='$montoe',cod_externolocal='$extlocal' where codigo=$codigo";
$stmtDetalles = $dbh->prepare($sqlDetalles);
$stmtDetalles->execute();

$sqlDet="SELECT * FROM simulaciones_serviciodetalle where cod_simulacionservicio=$codSimulacion";
$stmtDet = $dbh->prepare($sqlDet);
$stmtDet->execute();

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

    $sqlDel="DELETE FROM simulaciones_servicios_atributosnormas where cod_simulacionservicioatributo=$codigoDetAt";
    $stmtDel = $dbhA->prepare($sqlDel);
    $stmtDel->execute();
  }
  //normas

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
              $normaCodAtributo=$atributos[$att]->norma_cod;
              $selloAtributo=$atributos[$att]->sello;

              $paisAtributo=$atributos[$att]->pais;
              $estadoAtributo=$atributos[$att]->estado;
              $ciudadAtributo=$atributos[$att]->ciudad;

              $codSimulacionServicioAtributo=obtenerCodigoSimulacionServicioAtributo();
              $sqlDetalleAtributos="INSERT INTO simulaciones_servicios_atributos (codigo,cod_simulacionservicio, nombre, direccion, cod_tipoatributo,marca,norma,nro_sello,cod_pais,cod_estado,cod_ciudad) 
              VALUES ('$codSimulacionServicioAtributo','$codSimulacion', '$nombreAtributo', '$direccionAtributo', '$tipo_atributo','$marcaAtributo','$normaAtributo','$selloAtributo','$paisAtributo','$estadoAtributo','$ciudadAtributo')";
              $stmtDetalleAtributos = $dbh->prepare($sqlDetalleAtributos);
              $stmtDetalleAtributos->execute();

            if($_GET['tcs']==0){
                //$direccionAtributo="";
              $normasFila=explode(",",$normaCodAtributo);
                for ($ni=0; $ni < count($normasFila); $ni++) { 
                 $codNorma=$normasFila[$ni];
                  $sqlDetalleAtributosNormas="INSERT INTO simulaciones_servicios_atributosnormas (cod_simulacionservicioatributo, cod_norma, precio,cantidad) 
                 VALUES ('$codSimulacionServicioAtributo', '$codNorma', '10',1)";
                 $stmtDetalleAtributosNormas = $dbh->prepare($sqlDetalleAtributosNormas);
                 $stmtDetalleAtributosNormas->execute();
               }
              }else{
                 $nCDias=cantidadF($atributosDias);
                    for($jj=0;$jj<$nCDias;$jj++){
                       $codigoAtributoDias=$atributosDias[$jj]->codigo_atributo;
                       $anioAtributoDias=$atributosDias[$jj]->anio;
                       $diasAtributoDias=$atributosDias[$jj]->dias;
                       if($codigoAtributoDias==$codigoAtributo){
                        $sqlDetalleAtributosDias="INSERT INTO simulaciones_servicios_atributosdias (cod_simulacionservicioatributo, dias, cod_anio) 
                        VALUES ('$codSimulacionServicioAtributo', '$diasAtributoDias', '$anioAtributoDias')";
                        $stmtDetalleAtributosDias = $dbh->prepare($sqlDetalleAtributosDias);
                        $stmtDetalleAtributosDias->execute();
                       }           
                    }
              }         
              
         }
         //FIN simulaciones_serviciosauditores



/* while ($rowPre = $stmtDet->fetch(PDO::FETCH_ASSOC)) {
 	$cantidadDet=$rowPre['cantidad'];
 	$codigoDet=$rowPre['codigo'];
 	$montoDet=$rowPre['editado_personal'];
 	$partidaDet=$rowPre['cod_partidapresupuestaria'];
 	$cuenta=$rowPre['cod_cuenta'];
 	if($cantidadDet>($cantidadT*$dias_aud)){
 		$montoTotalDet=$montoDet*$cantidadT*$dias_aud;
 		$sqlDetalles="UPDATE simulaciones_serviciodetalle SET cantidad=$cantidadT,monto_total=$montoTotalDet,monto_unitario=$montoTotalDet where codigo=$codigoDet";
        $stmtDetalles = $dbh->prepare($sqlDetalles);
        $stmtDetalles->execute();
        $dbhC = new Conexion();
        $sqlCuentas="SELECT * FROM cuentas_simulacion where cod_simulacionservicios=$codSimulacion";
        $stmtCuentas = $dbhC->prepare($sqlCuentas);
        $stmtCuentas->execute();
       while ($rowCuentas = $stmtCuentas->fetch(PDO::FETCH_ASSOC)) {
          $simulacion=$rowCuentas['codigo'];  
          $montoTotal=0;
           $detallesMontos=obtenerMontosCuentasDetalleSimulacionServicioPartidaHabilitado($codSimulacion,$partidaDet);
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
}*/

echo "OK";
?>
