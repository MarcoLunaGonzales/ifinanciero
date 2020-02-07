<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$plantilla_costo=$_GET['codigo'];
$codPlanCosto=obtenerCodigoPlanServ();

$plantillaAntigua=obtenerPlantillaServicioDatos($plantilla_costo);
  
  while ($row = $plantillaAntigua->fetch(PDO::FETCH_ASSOC)) {
  	$nombre=$row['nombre']."-copia";
  	$abrev=$row['abreviatura'];
  	$unidad=$row['cod_unidadorganizacional'];
  	$area=$row['cod_area'];

  	$codCliente=$row['cod_cliente'];
  	$codTipoAu=$row['cod_tipoauditoria'];
  	$cantidadPersonal=$row['cantidad_personal'];
  	$productos=$row['productos'];
    $norma=$row['norma'];
    $codServicio=$row['cod_servicio'];
    $fechaAu=$row['fecha_auditoria'];
    $codPersonalR=$row['cod_personal_registro'];
    $fechaReg=$row['fecha_registro'];
    $fechaAp=$row['fecha_aprobacion'];
    $diasAu=$row['dias_auditoria'];
    $codEstadoPlan=$row['cod_estadoplantilla'];
    $codEstadoRef=$row['cod_estadoreferencial'];
    $utilidadMin=$row['utilidad_minima'];

   $dbh = new Conexion();
  $sqlInsert="INSERT INTO plantillas_servicios (codigo, nombre, abreviatura, cod_unidadorganizacional, cod_area,cod_cliente,cod_tipoauditoria,cantidad_personal,productos,norma,cod_servicio,fecha_auditoria,cod_personal_registro,fecha_registro,fecha_aprobacion,dias_auditoria,cod_estadoplantilla,cod_estadoreferencial,utilidad_minima) 
  VALUES ('".$codPlanCosto."','".$nombre."','".$abrev."', '".$unidad."', '".$area."','".$codCliente."','".$codTipoAu."','".$cantidadPersonal."','".$productos."','".$norma."','".$codServicio."','".$fechaAu."','".$codPersonalR."','".$fechaReg."','".$fechaAp."','".$diasAu."','".$codEstadoPlan."','".$codEstadoRef."','".$utilidadMin."')";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $flagSuccess=$stmtInsert->execute();

     
    //INSERTAR plantillas_gruposervicio
   $sqlPrecios="SELECT * FROM plantillas_gruposervicio where cod_plantillaservicio=$plantilla_costo";
   $stmtPrecios = $dbh->prepare($sqlPrecios);
   $stmtPrecios->execute();
    while ($rowPrecios = $stmtPrecios->fetch(PDO::FETCH_ASSOC)) {
      $regCodigo=$rowPrecios['codigo'];
      $regC=obtenerCodigoPlantillaGrupoServicio();
      $reg2=$rowPrecios['cod_tiposervicio'];
      $reg3=$rowPrecios['nombre'];
      $reg4=$rowPrecios['abreviatura'];
      $dbh2 = new Conexion();
      $sqlInsert2="INSERT INTO plantillas_gruposervicio (codigo,cod_tiposervicio,nombre,abreviatura,cod_plantillaservicio) 
      VALUES ('".$regC."','".$reg2."', '".$reg3."', '".$reg4."', '".$codPlanCosto."')";
      $stmtInsert2 = $dbh2->prepare($sqlInsert2);
      $stmtInsert2->execute();

      //INSERTAR plantillas_gruposerviciodetalle
       $sqlSub="SELECT * FROM plantillas_gruposerviciodetalle where cod_plantillagruposervicio=$regCodigo";
       $stmtSub = $dbh->prepare($sqlSub);
       $stmtSub->execute();
       while ($rowSub = $stmtSub->fetch(PDO::FETCH_ASSOC)) {
          $regCS=obtenerCodigoPlantillaGrupoDetalleServicio();
          $reg2S=$rowSub['cod_partidapresupuestaria'];
          $reg3S=$rowSub['tipo_calculo'];
          $reg4S=$rowSub['monto_local'];
          $reg5S=$rowSub['monto_externo'];
          $reg6S=$rowSub['monto_calculado'];
          $dbh2S = new Conexion();
         $sqlSubInsert="INSERT INTO plantillas_gruposerviciodetalle (codigo,cod_plantillagruposervicio,cod_partidapresupuestaria,tipo_calculo,monto_local,monto_externo,monto_calculado) 
         VALUES ('".$regCS."','".$regC."','".$reg2S."', '".$reg3S."', '".$reg4S."', '".$reg5S."', '".$reg6S."')";
         $stmtSubInsert = $dbh2S->prepare($sqlSubInsert);
         $stmtSubInsert->execute();
       }
    }

    //DETALLES PLANTILLA SERVICIOS

      $dbhID = new Conexion();
      $sqlID="INSERT INTO plantillas_servicios_detalle (cod_plantillatcp,cod_plantillacosto, cod_partidapresupuestaria, cod_cuenta,glosa,monto_unitario,cantidad,monto_total,cod_estadoreferencial,habilitado,editado_alumno) 
        SELECT $codPlanCosto as cod_plantillatcp,cod_plantillacosto,cod_partidapresupuestaria,cod_cuenta,glosa,monto_unitario,cantidad,monto_total,cod_estadoreferencial,habilitado,editado_alumno FROM plantillas_servicios_detalle where cod_plantillatcp=$plantilla_costo";
      $stmtID = $dbhID->prepare($sqlID);
      $stmtID->execute();

      //AUDITORES PLANTILLA SERVICIOS

      $dbhID = new Conexion();
      $sqlID="INSERT INTO plantillas_servicios_auditores (cod_plantillaservicio,cod_tipoauditor, cantidad, monto,cod_estadoreferencial) 
        SELECT $codPlanCosto as cod_plantillaservicio,cod_tipoauditor,cantidad,monto,cod_estadoreferencial FROM plantillas_servicios_auditores where cod_plantillaservicio=$plantilla_costo";
      $stmtID = $dbhID->prepare($sqlID);
      $stmtID->execute();

      //SERVICIOS PLANTILLA SERVICIOS

      $dbhID = new Conexion();
      $sqlID="INSERT INTO plantillas_servicios_tiposervicio (cod_plantillaservicio,cod_claservicio,observaciones, cantidad, monto,cod_estadoreferencial) 
        SELECT $codPlanCosto as cod_plantillaservicio,cod_claservicio,observaciones,cantidad,monto,cod_estadoreferencial FROM plantillas_servicios_tiposervicio where cod_plantillaservicio=$plantilla_costo";
      $stmtID = $dbhID->prepare($sqlID);
      $stmtID->execute();   

  }
showAlertSuccessError($flagSuccess,$urlList);
?>