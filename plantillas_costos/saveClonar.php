<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$plantilla_costo=$_GET['codigo'];

$codPlanCosto=obtenerCodigoPlanCosto();
$anioGestion=date("Y");
$plantillaAntigua=obtenerPlantillaCostoDatos($plantilla_costo);
  
  while ($row = $plantillaAntigua->fetch(PDO::FETCH_ASSOC)) {
  	$nombre=$row['nombre']."-copia";
  	$abrev=$row['abreviatura'];
  	$unidad=$row['cod_unidadorganizacional'];
  	$area=$row['cod_area'];
  	$utilidadLocal=$row['utilidad_minimalocal'];
  	$utilidadExterno=$row['utilidad_minimaexterno'];
  	$alumnosLocal=$row['cantidad_alumnoslocal'];
  	$alumnosExterno=$row['cantidad_alumnosexterno'];
    $cantidad_cursosmes=$row['cantidad_cursosmes'];
    $ingresoPresupuestado=obtenerPresupuestoEjecucionPorArea($unidad,$area,$anioGestion,12)['presupuesto'];
   $dbh = new Conexion();
  $sqlInsert="INSERT INTO plantillas_costo (codigo, nombre, abreviatura, cod_unidadorganizacional, cod_area,utilidad_minimalocal,utilidad_minimaexterno,cantidad_alumnoslocal,cantidad_alumnosexterno,cantidad_cursosmes,ingreso_presupuestado) 
  VALUES ('".$codPlanCosto."','".$nombre."','".$abrev."', '".$unidad."', '".$area."','".$utilidadLocal."','".$utilidadExterno."','".$alumnosLocal."','".$alumnosExterno."','".$cantidad_cursosmes."','".$ingresoPresupuestado."')";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $flagSuccess=$stmtInsert->execute();
   //INSERTAR precios
   $sqlPrecios="SELECT * FROM precios_plantillacosto where cod_plantillacosto=$plantilla_costo";
   $stmtPrecios = $dbh->prepare($sqlPrecios);
   $stmtPrecios->execute();
    while ($rowPrecios = $stmtPrecios->fetch(PDO::FETCH_ASSOC)) {
      $precioLocal=$rowPrecios['venta_local'];
      $precioExterno=$rowPrecios['venta_externo'];	
      $dbh2 = new Conexion();
      $sqlInsert2="INSERT INTO precios_plantillacosto (venta_local, venta_externo, cod_plantillacosto) 
      VALUES ('".$precioLocal."','".$precioExterno."', '".$codPlanCosto."')";
      $stmtInsert2 = $dbh2->prepare($sqlInsert2);
      $stmtInsert2->execute();
    }
     
    //INSERTAR plantillas_gruposcosto
   $sqlPrecios="SELECT * FROM plantillas_gruposcosto where cod_plantillacosto=$plantilla_costo";
   $stmtPrecios = $dbh->prepare($sqlPrecios);
   $stmtPrecios->execute();
    while ($rowPrecios = $stmtPrecios->fetch(PDO::FETCH_ASSOC)) {
      $regCodigo=$rowPrecios['codigo'];
      $regC=obtenerCodigoPlantillaGrupo();
      $reg2=$rowPrecios['cod_tipocosto'];
      $reg3=$rowPrecios['nombre'];
      $reg4=$rowPrecios['abreviatura'];
      $dbh2 = new Conexion();
      $sqlInsert2="INSERT INTO plantillas_gruposcosto (codigo,cod_tipocosto,nombre,abreviatura,cod_plantillacosto) 
      VALUES ('".$regC."','".$reg2."', '".$reg3."', '".$reg4."', '".$codPlanCosto."')";
      $stmtInsert2 = $dbh2->prepare($sqlInsert2);
      $stmtInsert2->execute();

      //INSERTAR plantillas_grupocostodetalle
       $sqlSub="SELECT * FROM plantillas_grupocostodetalle where cod_plantillagrupocosto=$regCodigo";
       $stmtSub = $dbh->prepare($sqlSub);
       $stmtSub->execute();
       while ($rowSub = $stmtSub->fetch(PDO::FETCH_ASSOC)) {            
        //cargar Costos con Presupuesto Actual
          $regCS=obtenerCodigoPlantillaGrupoDetalle();
          $reg2S=$rowSub['cod_partidapresupuestaria'];
          $reg3S=$rowSub['tipo_calculo'];
          $reg4S=$rowSub['monto_local'];
          $reg5S=$rowSub['monto_externo'];
          $reg6S=$rowSub['monto_calculado'];
          if(isset($_GET['pr'])&&$rowSub['tipo_calculo']==1){
              $anio=date("Y");
              $montoCalculado = calcularCostosPresupuestariosValor($rowSub['cod_partidapresupuestaria'],$unidad,$area,$anio-1,$cantidad_cursosmes);
              $reg4S=$montoCalculado;
              $reg5S=$montoCalculado;
              $reg6S=$montoCalculado;
          } 
          $dbh2S = new Conexion();
         $sqlSubInsert="INSERT INTO plantillas_grupocostodetalle (codigo,cod_plantillagrupocosto,cod_partidapresupuestaria,tipo_calculo,monto_local,monto_externo,monto_calculado) 
         VALUES ('".$regCS."','".$regC."','".$reg2S."', '".$reg3S."', '".$reg4S."', '".$reg5S."', '".$reg6S."')";
         $stmtSubInsert = $dbh2S->prepare($sqlSubInsert);
         $stmtSubInsert->execute();
       }
    }

    //DETALLES PLANTILLA COSTOS

      $dbhID = new Conexion();
      $sqlID="INSERT INTO plantillas_servicios_detalle (cod_plantillatcp,cod_plantillacosto, cod_partidapresupuestaria, cod_cuenta,glosa,monto_unitario,cantidad,monto_total,cod_estadoreferencial,habilitado,editado_alumno) 
        SELECT cod_plantillatcp,$codPlanCosto as cod_plantillacosto,cod_partidapresupuestaria,cod_cuenta,glosa,monto_unitario,cantidad,monto_total,cod_estadoreferencial,habilitado,editado_alumno FROM plantillas_servicios_detalle where cod_plantillacosto=$plantilla_costo";
      $stmtID = $dbhID->prepare($sqlID);
      $stmtID->execute();   

  }
showAlertSuccessError($flagSuccess,$urlList);
?>