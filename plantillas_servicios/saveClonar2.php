<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$plantilla_costo=$_GET['codigo'];

if(isset($_GET['q'])){
  $q=$_GET['q'];
  $s=$_GET['s'];
  $u=$_GET['u'];
}
$anioGestion=date("Y");
$codPlanCosto=$plantilla_costo;

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
    $codEstadoPlan=1;
    $codEstadoRef=$row['cod_estadoreferencial'];
    $utilidadMin=$row['utilidad_minima'];
    $cantidadAuditorias=$row['cantidad_auditorias'];
    //$ingresoPresupuestado=$row['ingreso_presupuestado'];
    $ingresoPresupuestado=obtenerPresupuestoEjecucionPorArea($unidad,$area,$anioGestion,12)['presupuesto'];
    $anios=$row['anios'];

   $dbh = new Conexion();
  $sqlInsert="UPDATE plantillas_servicios SET ingreso_presupuestado=$ingresoPresupuestado where codigo=$codPlanCosto";
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

      //INSERTAR plantillas_gruposerviciodetalle
       $sqlSub="SELECT * FROM plantillas_gruposerviciodetalle where cod_plantillagruposervicio=$regCodigo";
       $stmtSub = $dbh->prepare($sqlSub);
       $stmtSub->execute();
       while ($rowSub = $stmtSub->fetch(PDO::FETCH_ASSOC)) {
          $codDetalle=$rowSub['codigo'];
          $regCS=obtenerCodigoPlantillaGrupoDetalleServicio();
          $reg2S=$rowSub['cod_partidapresupuestaria'];
          $reg3S=$rowSub['tipo_calculo'];
          $reg4S=$rowSub['monto_local'];
          $reg5S=$rowSub['monto_externo'];
          $reg6S=$rowSub['monto_calculado'];

          if(isset($_GET['pr'])&&$rowSub['tipo_calculo']==1){
              $anio=date("Y");
              $cantidad_cursosmes=17;
              if($area==38){
                $cantidad_cursosmes=18; 
              }
              $montoCalculado = calcularCostosPresupuestariosAuditoria($rowSub['cod_partidapresupuestaria'],$unidad,$area,$anio,$cantidad_cursosmes);
              $reg4S=$montoCalculado;
              $reg5S=$montoCalculado;
              $reg6S=$montoCalculado;
          }
          $dbh2S = new Conexion();
         $sqlSubInsert="UPDATE plantillas_gruposerviciodetalle  SET monto_local=$reg4S,monto_externo=$reg5S,monto_calculado=$reg6S WHERE codigo=$codDetalle";
         $stmtSubInsert = $dbh2S->prepare($sqlSubInsert);
         $stmtSubInsert->execute();
       }
    }


  }
  if(isset($_GET['q'])){
   showAlertSuccessError($flagSuccess,$urlList."&q=".$q."&s=".$s."&u=".$u);
  }else{
   showAlertSuccessError($flagSuccess,$urlList);
  }

?>