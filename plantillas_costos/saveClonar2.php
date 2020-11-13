<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$plantilla_costo=$_GET['codigo'];

$codPlanCosto=$plantilla_costo;
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
   $sqlInsert="UPDATE plantillas_costo SET ingreso_presupuestado=$ingresoPresupuestado where codigo=$codPlanCosto";
   $stmtInsert = $dbh->prepare($sqlInsert);
   $flagSuccess=$stmtInsert->execute();
     
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


      //INSERTAR plantillas_grupocostodetalle
       $sqlSub="SELECT * FROM plantillas_grupocostodetalle where cod_plantillagrupocosto=$regCodigo";
       $stmtSub = $dbh->prepare($sqlSub);
       $stmtSub->execute();
       while ($rowSub = $stmtSub->fetch(PDO::FETCH_ASSOC)) {            
        //cargar Costos con Presupuesto Actual
          $regCodigo=$rowSub['codigo'];
          $regCS=obtenerCodigoPlantillaGrupoDetalle();
          $reg2S=$rowSub['cod_partidapresupuestaria'];
          $reg3S=$rowSub['tipo_calculo'];
          $reg4S=$rowSub['monto_local'];
          $reg5S=$rowSub['monto_externo'];
          $reg6S=$rowSub['monto_calculado'];
          if(isset($_GET['pr'])&&$rowSub['tipo_calculo']==1){
              $anio=date("Y");
              $montoCalculado = calcularCostosPresupuestariosValor($rowSub['cod_partidapresupuestaria'],$unidad,$area,$anio,$cantidad_cursosmes);
              $reg4S=$montoCalculado;
              $reg5S=$montoCalculado;
              $reg6S=$montoCalculado;
          } 
          $dbh2S = new Conexion();
         $sqlSubInsert="UPDATE plantillas_grupocostodetalle SET monto_local=$reg4S,monto_externo=$reg5S,monto_calculado=$reg6S where codigo=$regCodigo";
         $stmtSubInsert = $dbh2S->prepare($sqlSubInsert);
         $stmtSubInsert->execute();
       }
    }

  }
showAlertSuccessError($flagSuccess,$urlList);
?>