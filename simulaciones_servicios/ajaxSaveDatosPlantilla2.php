<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';
set_time_limit (0);
$dbh = new Conexion();

$codigo=$_POST["codigo"];
session_start();

$codPlantillaCosto=$_POST["plantilla"];
$codSimulacion=$_POST["simulacion"];
$ut_i=$_POST['utilidad'];
$dia=$_POST['dia'];


$monto=$_POST['monto'];
$cantidad=$_POST['cantidad'];
$obs=$_POST['descripcion'];

$habilitado=$_POST['habilitado'];
$unidad=$_POST['unidad'];
$fijo=$_POST['precio_fijo'];
$productos="";
$atributos= json_decode($_POST['productos']);
$anio=$_POST['anio'];
$anio_fila=$_POST['anio_fila'];
$iteracion=$_POST['iteracion'];

$sqlDetallesAuditores="UPDATE simulaciones_servicios_auditores SET dias=0 where cod_simulacionservicio=$codSimulacion";
$stmtDetallesAuditores = $dbh->prepare($sqlDetallesAuditores);
$stmtDetallesAuditores->execute();

if($_POST['tcs']==0){
  $tipo_atributo=1;
  $sqlUpdatePlantilla="UPDATE simulaciones_servicios SET  utilidad_minima='$ut_i',dias_auditoria='$dia',productos='$productos' where codigo=$codSimulacion";
}else{
  $tipo_atributo=2;
  $atributosDias= json_decode($_POST['sitios_dias']);
  $sqlUpdatePlantilla="UPDATE simulaciones_servicios SET  utilidad_minima='$ut_i',dias_auditoria='$dia',sitios='$productos' where codigo=$codSimulacion";
}

$stmtUpdatePlantilla = $dbh->prepare($sqlUpdatePlantilla);
$stmtUpdatePlantilla->execute();

if($cantidad==0){
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

              $paisAtributo=$atributos[$att]->pais;
              $estadoAtributo=$atributos[$att]->estado;
              $ciudadAtributo=$atributos[$att]->ciudad;

              $codSimulacionServicioAtributo=obtenerCodigoSimulacionServicioAtributo();
              $sqlDetalleAtributos="INSERT INTO simulaciones_servicios_atributos (codigo,cod_simulacionservicio, nombre, direccion, cod_tipoatributo,marca,norma,nro_sello,cod_pais,cod_estado,cod_ciudad) 
              VALUES ('$codSimulacionServicioAtributo','$codSimulacion', '$nombreAtributo', '$direccionAtributo', '$tipo_atributo','$marcaAtributo','$normaAtributo','$selloAtributo','$paisAtributo','$estadoAtributo','$ciudadAtributo')";
              $stmtDetalleAtributos = $dbh->prepare($sqlDetalleAtributos);
              $stmtDetalleAtributos->execute();
            if($_POST['tcs']==0){
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
                $auditoresDias=json_decode($_POST['auditoresDias']);               
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
                        $sqlDetalleAu="UPDATE simulaciones_servicios_atributosauditores SET estado=0 where cod_simulacionservicioatributo=$codSimulacionServicioAtributo and cod_anio=$anioAtributoDias";
                        $stmtDetalleAu = $dbh->prepare($sqlDetalleAu);
                        $stmtDetalleAu->execute();
                        if(obtenerEntradaSimulacionServicio($codSimulacion)==1){
                         //aumentar dias a los auditores
                         for ($al=0; $al < count($auditoresDias[$jj]); $al++) { 
                          $codigoAuditor=$auditoresDias[$jj][$al];
                          $cantidadDiasAnterior=obtenerDiasAuditorSimulacionServicio($codigoAuditor);
                          $cantidadDiasNuevo=$cantidadDiasAnterior+$diasAtributoDias;
                          $sqlDetallesAuditores="UPDATE simulaciones_servicios_auditores SET dias=$cantidadDiasNuevo where codigo=$codigoAuditor";
                          $stmtDetallesAuditores = $dbh->prepare($sqlDetallesAuditores);
                          $stmtDetallesAuditores->execute();

                          $sqlDetalleAu="UPDATE simulaciones_servicios_atributosauditores SET estado=1 where cod_simulacionservicioatributo=$codSimulacionServicioAtributo and cod_anio=$anioAtributoDias and cod_auditor=$codigoAuditor";
                          $stmtDetalleAu = $dbh->prepare($sqlDetalleAu);
                          $stmtDetalleAu->execute();
                          echo $sqlDetallesAuditores;
                         }
                        }

                  
                       }           
                    }
              }         
              
         }
         //FIN simulaciones_serviciosauditores


/*if($fijo!=""){
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
}*/

$sqlDetallesAuditores="UPDATE simulaciones_servicios_auditores SET dias=1 where cod_simulacionservicio=$codSimulacion and dias=0";
$stmtDetallesAuditores = $dbh->prepare($sqlDetallesAuditores);
$stmtDetallesAuditores->execute();

$sqlDetalles="UPDATE simulaciones_servicios_tiposervicio SET observaciones='$obs',cantidad_editado=$cantidad,monto=$monto,habilitado=$habilitado,cod_tipounidad=$unidad,cod_anio=$anio_fila where codigo=$codigo";
$stmtDetalles = $dbh->prepare($sqlDetalles);
$stmtDetalles->execute();

      
/*
      $sqlDelete="DELETE FROM simulaciones_cf where cod_simulacionservicio=$codSimulacion and cod_anio=$anio_fila";  
      $stmtDelete = $dbh->prepare($sqlDelete);
      $stmtDelete->execute();

  //costos Fijos en tabla
      $cuentasFijas=obtenerListaCuentasPlantillasCostoFijoServicio($codPlantillaCosto);
      while ($rowFijo = $cuentasFijas->fetch(PDO::FETCH_ASSOC)) {
         $nombreCuentaFijo=$rowFijo['nombre'];
         $numeroCuentaFijo=$rowFijo['numero'];
         $codCuentaFijo=$rowFijo['cod_cuenta'];
         $codPartidaFijo=$rowFijo['cod_partidapresupuestaria'];
         $tipoFijo=$rowFijo['tipo'];

         $precioLocalX=obtenerPrecioServiciosSimulacionPorAnio($codSimulacion,$anio_fila);
         $precioRegistrado=obtenerPrecioRegistradoPlantilla($codPlantillaCosto);
         $nCursos=obtenerCantidadAuditoriasPlantilla($codPlantillaCosto); 
         $porcentPrecios=($precioLocalX)/($precioRegistrado);
         if($tipoFijo==1){ 
         $anioSim= date("Y");  
         $monto=ejecutadoEgresosMes($globalUnidad,((int)$anioSim-1),12,$areaGeneralPlantilla,1,$numeroCuentaFijo);          
         }else{
          $monto=obtenerListaCuentasPlantillasCostoFijoServicioManual($codCuentaFijo,$codPartidaFijo,$codPlantillaCosto);
         }
         $montoUnidad=$monto*$porcentPrecios; 
         $dbh = new Conexion();
         $sqlFijos="INSERT INTO simulaciones_cf (cod_simulacionservicio, cod_simulacioncosto,cod_partidapresupuestaria,cod_cuenta,monto,cantidad,monto_total,cod_anio) 
         VALUES ('".$codSimulacion."',0,'".$codPartidaFijo."','".$codCuentaFijo."','".$montoUnidad."',1,'".$montoUnidad."','".$anio_fila."')";
         $stmtFijos = $dbh->prepare($sqlFijos);
         $stmtFijos->execute();
      } 
      */
echo $anio."WWW".$iteracion;
?>
