<?php //ESTADO FINALIZADO

require_once 'conexion.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'rrhh/configModule.php';

$dbh = new Conexion();
$dbhI = new Conexion();
$dbhIPD = new Conexion();

$anio_actual=date('Y');
$mes_actual=date('m');


$stmt = $dbh->prepare("SELECT * from gestiones where nombre=$anio_actual");
$stmt->execute();
$result= $stmt->fetch();
$cod_gestion=$result['codigo'];
$cod_mes=(integer)$mes_actual;
$cod_estadoplanilla=1;
$created_by=1;
$modified_by=1;
// echo "mes ".$mes_actual;
//$fecha_actual=date('Y-m-d');
$cont=0;
$stmtPlanillas = $dbh->prepare("SELECT * from planillas where cod_gestion=$cod_gestion and cod_mes=$cod_mes");
$stmtPlanillas->execute();
$stmtPlanillas->bindColumn('codigo',$codigo_planilla);
//verificamos si exite registro de planilla en este mes
while ($row = $stmtPlanillas->fetch()) 
{
  $cont+=1; 
}
if($cont==0){//insert - cuando no existe planilla
  $sqlInsert="INSERT into planillas (cod_gestion,cod_mes,cod_estadoplanilla,created_by,modified_by) values(:cod_gestion,:cod_mes,:cod_estadoplanilla,:created_by,:modified_by)";
  $stmtInsert = $dbhI->prepare($sqlInsert);
  $stmtInsert->bindParam(':cod_gestion', $cod_gestion);
  $stmtInsert->bindParam(':cod_mes',$cod_mes);
  $stmtInsert->bindParam(':cod_estadoplanilla',$cod_estadoplanilla);
  $stmtInsert->bindParam(':created_by',$created_by);
  $stmtInsert->bindParam(':modified_by',$modified_by);
  $flagSuccess=$stmtInsert->execute();

  //===capturando codigo de planilla registrada
  $sqlAux="SELECT codigo from planillas order by 1 desc";
  $stmtAux = $dbhI->prepare($sqlAux);
  $stmtAux->execute();  
  $resultAux = $stmtAux->fetch();
  $codigo_planilla_actual = $resultAux['codigo'];

  //=========================creando la planilla previa
  $dias_trabajados = 30; //por defecto
  $horas_pagadas = 0; //buscar datos
  $minimo_salarial=0;
  $valor_conf_x65_90=0;
  $valor_conf_x90_120=0;
  $valor_conf_x120_150=0;
  $valor_conf_x150=0;
  $total_bonos=0;
  $total_ganado=0;

  $haber_basico=0;//del personal
  $horas_extra = 0; //buscar datos
  $comisiones=0;//buscar datos
  $monto_bonos=0;
  $monto_descuentos=0;//???

  $otros_descuentos=0;
  $total_descuentos=0;
  $liquido_pagable=0;
  $cod_estadoreferencial=1;
  $created_by=1;
  $modified_by=1;


  $stmtConfiguracion = $dbh->prepare("SELECT * from configuraciones_planillas");
  $stmtConfiguracion->execute();
  $stmtConfiguracion->bindColumn('id_configuracion', $codigo_configuracion);
  $stmtConfiguracion->bindColumn('valor_configuracion',$valor_configuracion);

  //capturando valores de configuracion
  while ($row = $stmtConfiguracion->fetch()) 
  {
    switch ($codigo_configuracion) {
      case 1:
        $minimo_salarial=$valor_configuracion;
        break;
      case 5:
        $valor_conf_x65_90=$valor_configuracion;
        break;
      case 6:
        $valor_conf_x90_120=$valor_configuracion;
        break;
      case 7:
        $valor_conf_x120_150=$valor_configuracion;
        break;
      case 8:
        $valor_conf_x150=$valor_configuracion;
        break;
      
      default:
        
        break;
    }
  }
  // fin de valores de configruacion

  //============select del personal
  $sql = "SELECT codigo,haber_basico,
  (Select pga.porcentaje from personal_grado_academico pga where pga.codigo=cod_grado_academico) as p_grado_academico,
  (Select pga.nombre from personal_grado_academico pga where pga.codigo=cod_grado_academico) as grado_academico_x,
  cod_tipoaporteafp,cod_tipoafp
  from personal where cod_estadoreferencial=1 and codigo in (1,5,7,8,9,12,84)";

  $stmtPersonal = $dbh->prepare($sql);
  $stmtPersonal->execute();
  $stmtPersonal->bindColumn('codigo', $codigo_personal);
  $stmtPersonal->bindColumn('haber_basico', $haber_basico);
  $stmtPersonal->bindColumn('grado_academico_x', $grado_academico_x);
  $stmtPersonal->bindColumn('p_grado_academico', $p_grado_academico);
  $stmtPersonal->bindColumn('cod_tipoafp', $cod_tipoafp);
  $stmtPersonal->bindColumn('cod_tipoaporteafp', $cod_tipoaporteafp);
  while ($row = $stmtPersonal->fetch()) 
  {
    //calculado otros bonos
    if($p_grado_academico==0)$otro_bonos = 0;
    else $otro_bonos = $p_grado_academico/100*$minimo_salarial;
    
    $bono_antiguedad= 233.42 ;//falta hacer
    $otros_b = 0 ;//buscar datos
    
    $total_bonos=$otro_bonos+$bono_antiguedad+$otros_b;

    $total_ganado = ($haber_basico/30*$dias_trabajados)+$otro_bonos+$bono_antiguedad+$otros_b;
    //calculamos descuentoss
    if($cod_tipoafp==1){
      $afp_futuro =obtenerAporteAFPFuturo($cod_tipoaporteafp,$total_ganado);
      $afp_prevision=0;
    }elseif($cod_tipoafp==2){
      $afp_prevision = obtenerAporteAFPFuturo($cod_tipoaporteafp,$total_ganado);
      $afp_futuro=0;
    }else{
      $afp_prevision = 0;
      $afp_futuro=0;
    }
    //aportes volvuntarios
    $aporte_solidario_13000 = obtenerAporteSolidario13000($total_ganado);
    $aporte_solidario_25000 = obtenerAporteSolidario25000($total_ganado);
    $aporte_solidario_35000 = obtenerAporteSolidario35000($total_ganado);
    
    $RC_IVA = obtenerRC_IVA($total_ganado,$afp_futuro,$afp_prevision,$aporte_solidario_13000,$aporte_solidario_25000,$aporte_solidario_35000);

    $atrasos = obtenerAtrasoPersonal($codigo_personal,$haber_basico,$valor_conf_x65_90,$valor_conf_x90_120,$valor_conf_x120_150,$valor_conf_x150);
    $otros_descuentos = obtenerOtrosDescuentos();
    $anticipo = obtenerAnticipo($codigo_personal);    

    $total_descuentos = $afp_futuro+$afp_prevision+$aporte_solidario_13000+$aporte_solidario_25000+$aporte_solidario_35000+$RC_IVA+$atrasos+$otros_descuentos+$anticipo;
    $liquido_pagable=$total_ganado-$total_descuentos;


    // echo "codigo".$codigo_personal.", total ganado ". $total_ganado."<br>";
    // echo "liquido_pagable ".$liquido_pagable;

    //==== insert de panillas de  personal mes
    $sqlInsertPlanillas="INSERT into planillas_personal_mes(cod_planilla,cod_personalcargo,cod_gradoacademico,dias_trabajados,horas_pagadas,
      haber_basico,bono_antiguedad,horas_extra,comisiones,monto_bonos,total_ganado,monto_descuentos,otros_descuentos,
      total_descuentos,liquido_pagable,cod_estadoreferencial,created_by,modified_by)
     values(:cod_planilla,:cod_personal_cargo,:cod_grado_academico,:dias_trabajados,:horas_pagadas,:haber_basico,:bono_antiguedad,
      :horas_extra,:comisiones,:monto_bonos,:total_ganado,:monto_descuentos,:otros_descuentos,:total_descuentos,
      :liquido_pagable,:cod_estadoreferencial,:created_by,:modified_by)";
    $stmtInsertPlanillas = $dbhI->prepare($sqlInsertPlanillas);
    $stmtInsertPlanillas->bindParam(':cod_planilla', $codigo_planilla_actual);
    $stmtInsertPlanillas->bindParam(':cod_personal_cargo',$codigo_personal);
    $stmtInsertPlanillas->bindParam(':cod_grado_academico',$grado_academico_x);
    $stmtInsertPlanillas->bindParam(':dias_trabajados',$dias_trabajados);
    $stmtInsertPlanillas->bindParam(':horas_pagadas',$horas_pagadas);
    $stmtInsertPlanillas->bindParam(':haber_basico',$haber_basico);
    $stmtInsertPlanillas->bindParam(':bono_antiguedad',$bono_antiguedad);
    $stmtInsertPlanillas->bindParam(':horas_extra',$horas_extra);
    $stmtInsertPlanillas->bindParam(':comisiones',$comisiones);
    $stmtInsertPlanillas->bindParam(':monto_bonos',$total_bonos);
    $stmtInsertPlanillas->bindParam(':total_ganado',$total_ganado);
    $stmtInsertPlanillas->bindParam(':monto_descuentos',$monto_descuentos);
    $stmtInsertPlanillas->bindParam(':otros_descuentos',$otros_descuentos);
    $stmtInsertPlanillas->bindParam(':total_descuentos',$total_descuentos);
    $stmtInsertPlanillas->bindParam(':liquido_pagable',$liquido_pagable);
    $stmtInsertPlanillas->bindParam(':cod_estadoreferencial',$cod_estadoreferencial);
    $stmtInsertPlanillas->bindParam(':created_by',$created_by);
    $stmtInsertPlanillas->bindParam(':modified_by',$modified_by);
    $flagSuccessIP=$stmtInsertPlanillas->execute();
    
    

    // echo "codigo_planilla_actual: ".$codigo_planilla_actual."<br>";
    // echo "codigo_personal: ".$codigo_personal."<br>";
    // echo "afp_futuro: ".$afp_futuro."<br>";
    // echo "afp_prevision: ".$afp_prevision."<br>";
    // echo "aporte_solidario_25000: ".$aporte_solidario_13000."<br>";
    // echo "aporte_solidario_25000: ".$aporte_solidario_25000."<br>";
    // echo "aporte_solidario_35000: ".$aporte_solidario_35000."<br>";
    // echo "RC_IVA: ".$RC_IVA."<br>";
    // echo "atrasos: ".$atrasos."<br>";
    // echo "anticipo: ".$anticipo;

    //==== insert de panillas de  personal mes
    $sqlInsertPlanillaDetalle="INSERT into planillas_personal_mes_detalle(cod_planilla,cod_personal_cargo,afp_futuro,afp_prevision,a_solidario_13000,a_solidario_25000,a_solidario_35000,rc_iva,atrasos,anticipo)
    values(:cod_planilla,:cod_personal_cargo,:afp_futuro,:afp_prevision,:a_solidario_13000,:a_solidario_25000,:a_solidario_35000,:rc_iva,:atrasos,:anticipo)";
    $stmtInsertPlanillaDetalle = $dbhIPD->prepare($sqlInsertPlanillaDetalle);
    $stmtInsertPlanillaDetalle->bindParam(':cod_planilla', $codigo_planilla_actual);
    $stmtInsertPlanillaDetalle->bindParam(':cod_personal_cargo',$codigo_personal);
    $stmtInsertPlanillaDetalle->bindParam(':afp_futuro',$afp_futuro);  
    $stmtInsertPlanillaDetalle->bindParam(':afp_prevision',$afp_prevision);
    $stmtInsertPlanillaDetalle->bindParam(':a_solidario_13000',$aporte_solidario_13000);
    $stmtInsertPlanillaDetalle->bindParam(':a_solidario_25000',$aporte_solidario_25000);
    $stmtInsertPlanillaDetalle->bindParam(':a_solidario_35000',$aporte_solidario_35000);
    $stmtInsertPlanillaDetalle->bindParam(':rc_iva',$RC_IVA);
    $stmtInsertPlanillaDetalle->bindParam(':atrasos',$atrasos);
    $stmtInsertPlanillaDetalle->bindParam(':anticipo',$anticipo);
    $flagSuccessIPMD=$stmtInsertPlanillaDetalle->execute();
  }
  //===fin de planilla previa
  if($flagSuccessIP)echo "Planilla Sueldos Personal CORRECTO"."<br>";
  else echo "Planilla Sueldos Personal ERROR"."<br>";
  if($flagSuccessIPMD)echo "Planilla Sueldos Detalle CORRECTO"."<br>";
  else echo "Planilla Sueldos Detalle ERROR"."<br>";

}else{
  $flagSuccess=0;//alerta indicando que ya existe registro
}
showAlertSuccessError3($flagSuccess,$urlPlanillasSueldoList);



?>