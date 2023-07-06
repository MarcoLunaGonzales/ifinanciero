<?php
require_once 'conexion.php';

function totalGanadoDN($gestion, $mes, $unidad){
  $dbh = new Conexion();
  $sql="SELECT sum(pm.total_ganado*(pad.porcentaje/100))as monto 
    from planillas p join planillas_personal_mes pm on p.codigo=pm.cod_planilla join personal_area_distribucion pad on pm.cod_personalcargo=pad.cod_personal and pad.cod_estadoreferencial=1
    where p.cod_gestion='$gestion' and p.cod_mes='$mes' and pad.cod_uo='$unidad'";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $monto=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto=$row['monto'];
    }
    return($monto);
}
function totalGanadoArea($gestion, $mes, $cod_area,$cod_uo=null){
  $sql_add="";
  if($cod_uo!=null){
    $sql_add=" and pad.cod_uo='$cod_uo' ";
  }
  $dbh = new Conexion();
  $sql="SELECT sum(pm.total_ganado*(pad.porcentaje/100))as monto 
    from planillas p join planillas_personal_mes pm on p.codigo=pm.cod_planilla join personal_area_distribucion pad on pm.cod_personalcargo=pad.cod_personal and pad.cod_estadoreferencial=1
    where p.cod_gestion='$gestion' and p.cod_mes='$mes' and pad.cod_area='$cod_area' $sql_add";
    // echo $sql."<br>";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $monto=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto=$row['monto'];
    }
    return($monto);
}

function monto_planillaGeneral($codigo_planilla, $globalUnidadX,$cod_area,$cod_tipo){
  
  switch ($cod_tipo) {
    case 1:
      $sql_cabecera="sum(pm.haber_basico)as monto";
    break;
    case 2:
      $sql_cabecera="sum(pm.bono_antiguedad)as monto";
    break;
    case 3:
      $sql_cabecera="sum(pm.liquido_pagable)as monto";
    break;
    
  }
  if($globalUnidadX==1){
    $sql_add=" and pad.cod_unidadorganizacional=$globalUnidadX";
  }elseif($globalUnidadX==2){
    $sql_add=" and pad.cod_unidadorganizacional=$globalUnidadX and pad.cod_area=$cod_area";
  }elseif($globalUnidadX==-100){
    $sql_add=" ";
  }

  $dbh = new Conexion();
  $sql="SELECT $sql_cabecera
  from planillas_personal_mes pm join personal pad on pm.cod_personalcargo=pad.codigo
  where pm.cod_planilla=$codigo_planilla  $sql_add";
   // echo $sql."<br><br>"; 
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $monto=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto=$row['monto'];
    }
    return($monto);
}
function monto_planillaGeneral_bonos($cod_planilla,$cod_gestion,$cod_mes,$globalUnidad_ofcen,$cod_area,$cod_bono,$cod_tipobono){
  $dbh = new Conexion();
  if($globalUnidad_ofcen==1){
    $sql_add="";
  }else{
    $sql_add=" and p.cod_area=$cod_area";
  }
  if($cod_bono==15){
    $sql_add_bono = "and bpm.cod_bono in (15,16)";
  }else{
    $sql_add_bono = "and bpm.cod_bono in ($cod_bono)";
  }
  if($cod_tipobono==1){
    $sqlBonosOtrs = "SELECT sum(bpm.monto) as monto
    from bonos_personal_mes bpm join planillas_personal_mes ppm on ppm.cod_personalcargo=bpm.cod_personal join personal p on bpm.cod_personal=p.codigo
    where bpm.cod_mes=$cod_mes and bpm.cod_gestion=$cod_gestion $sql_add_bono and bpm.cod_estadoreferencial=1 and ppm.cod_planilla=$cod_planilla and p.cod_unidadorganizacional=$globalUnidad_ofcen $sql_add";
    $stmtBonosOtrs = $dbh->prepare($sqlBonosOtrs);
    $stmtBonosOtrs->execute();
    $resultBonosOtros=$stmtBonosOtrs->fetch();
    $montoX=$resultBonosOtros['monto'];
    return $montoX;
  }else{
    $value=0;
      $sqlBonosOtrs = "SELECT bpm.monto,ppm.dias_trabajados
    from bonos_personal_mes bpm join planillas_personal_mes ppm on ppm.cod_personalcargo=bpm.cod_personal join personal p on bpm.cod_personal=p.codigo
    where bpm.cod_mes=$cod_mes and bpm.cod_gestion=$cod_gestion $sql_add_bono and bpm.cod_estadoreferencial=1 and ppm.cod_planilla=$cod_planilla and p.cod_unidadorganizacional=$globalUnidad_ofcen $sql_add";
    $stmtBonosOtrs = $dbh->prepare($sqlBonosOtrs);
    $stmtBonosOtrs->execute();
    while ($row = $stmtBonosOtrs->fetch(PDO::FETCH_ASSOC)) {
        $monto=$row['monto'];
      $dias_trabajados=$row['dias_trabajados'];
      $porcen_monto=$dias_trabajados*100/30;
      $value+=$porcen_monto*$monto/100;
      }
    return($value);
  }
}

function monto_planillaGeneral_afps($cod_planilla){
  $dbh = new Conexion();
  $sql="SELECT sum(pm.afp_1+pm.afp_2+ppmp.a_solidario_13000+ppmp.a_solidario_25000+ppmp.a_solidario_35000)as monto 
  from planillas_personal_mes pm join planillas_personal_mes_patronal ppmp on pm.cod_planilla=ppmp.cod_planilla and pm.cod_personalcargo=ppmp.cod_personal_cargo
  where pm.cod_planilla=$cod_planilla;";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $monto=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto=$row['monto'];
    }
    return($monto);
}

function totalLiquidoPagable($gestion, $mes){
  $dbh = new Conexion();
  $sql="SELECT sum(pm.liquido_pagable)as monto 
    from planillas p, planillas_personal_mes pm where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes'";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $monto=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto=$row['monto'];
    }
    return($monto);
}
function totalPersonalProyectos($proyecto){
  $dbh = new Conexion();
  $sql="SELECT sum(p.monto_subsidio)as monto from personal_proyectosfinanciacionexterna p where p.cod_estado_referencial=1 and p.cod_proyecto='$proyecto'";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $monto=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto=$row['monto'];
    }
    return($monto);
}
function cuentaCorrienteUO($unidad){
  $nroCuenta="";
  if($unidad==10){
    $nroCuenta="56";
  }
  if($unidad==5){
    $nroCuenta="55";
  }
  if($unidad==8){
    $nroCuenta="60";
  }
  if($unidad==9){
    $nroCuenta="57";
  }
  if($unidad==270){
    $nroCuenta="58";
  }
  if($unidad==271){
    $nroCuenta="59";
  }
  return($nroCuenta);
}
function obtenerTotalCPS($gestion, $mes){
  $dbh = new Conexion();
  $sql="SELECT sum(pm.seguro_de_salud)as monto from planillas p, planillas_personal_mes_patronal pm where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes' ";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $monto=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto=$row['monto'];
    }
    return($monto);
}
function obtenerTotalAFP_prev1($gestion, $mes){
  $dbh = new Conexion();
  $sql="SELECT sum((pm.riesgo_profesional)+(pm.a_patronal_sol))as monto from planillas p, planillas_personal_mes_patronal pm,personal per where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes' and pm.cod_personal_cargo=per.codigo and per.cod_tipoafp=3";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $monto1=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto1=$row['monto'];
    }

    $sql2="SELECT sum(pm.afp_1)as monto from planillas p, planillas_personal_mes pm,personal per where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes'  and pm.cod_personalcargo=per.codigo and per.cod_tipoafp=3";

    $stmt2 = $dbh->prepare($sql2);
    $stmt2->execute();
    $monto2=0;
    while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
      $monto2=$row['monto'];
    }

    $montoTotal=$monto1+$monto2;

    return($montoTotal);
}
function obtenerTotalAFP_prev2($gestion, $mes){
  $dbh = new Conexion();
  $sql="SELECT sum((pm.a_solidario_13000)+(pm.a_solidario_25000)+(pm.a_solidario_35000))as monto 
    from planillas p, planillas_personal_mes_patronal pm,personal per
    where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes' and pm.cod_personal_cargo=per.codigo and per.cod_tipoafp=3";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $monto=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto=$row['monto'];
    }
    return($monto);
}
function obtenerTotalAFP_prev4($gestion, $mes){
  $dbh = new Conexion();
  $sql="SELECT sum((pm.a_solidario_13000)+(pm.a_solidario_25000)+(pm.a_solidario_35000))as monto 
    from planillas p, planillas_personal_mes_patronal pm,personal per
    where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes' and pm.cod_personal_cargo=per.codigo and per.cod_tipoafp=2";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $monto=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto=$row['monto'];
    }
    return($monto);
}
function obtenerTotalAFP_prev3($gestion, $mes){
  $dbh = new Conexion();
  $sql="SELECT sum((pm.riesgo_profesional)+(pm.a_patronal_sol))as monto 
  from planillas p, planillas_personal_mes_patronal pm,personal per where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes'  and pm.cod_personal_cargo=per.codigo and per.cod_tipoafp=1";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $monto1=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto1=$row['monto'];
    }

    $sql2="SELECT sum(pm.afp_1)as monto from planillas p, planillas_personal_mes pm,personal per where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes' and pm.cod_personalcargo=per.codigo and per.cod_tipoafp=1";
    $stmt2 = $dbh->prepare($sql2);
    $stmt2->execute();
    $monto2=0;
    while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
      $monto2=$row['monto'];
    }
    $montoTotal=$monto1+$monto2;
    return($montoTotal);
}
function obtenerTotalprovivienda($gestion, $mes){
  $dbh = new Conexion();
  $sql="SELECT sum(pm.provivienda)as monto from planillas p, planillas_personal_mes_patronal pm,personal per where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes' and pm.cod_personal_cargo=per.codigo and per.cod_tipoafp=3";

    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $monto1=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto1=$row['monto'];
    }
    return($monto1);
}
function obtenerTotalprovivienda2($gestion, $mes, $unidad){
  $dbh = new Conexion();
  $sql="SELECT sum(pm.provivienda)as monto from planillas p, planillas_personal_mes_patronal pm,personal per where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes'  and pm.cod_personal_cargo=per.codigo and per.cod_tipoafp=1";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $monto1=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto1=$row['monto'];
    }
    return($monto1);
}
function obtenerTotalOtrosdescuentos($gestion, $mes, $unidad){
  $dbh = new Conexion();
  $sql="SELECT sum(pm.monto_descuentos-pm.afp_1-pm.afp_2-pm.descuentos_otros)as monto 
    from planillas p, planillas_personal_mes pm,personal per where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes' and pm.cod_personalcargo=per.codigo";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $monto1=0;$monto2=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto1=$row['monto'];
    }
    $sql2="SELECT sum(pm.a_solidario_13000+pm.a_solidario_25000+pm.a_solidario_35000+pm.atrasos+pm.rc_iva+pm.anticipo+ifnull(pm.dotaciones,0))as monto 
    from planillas p, planillas_personal_mes_patronal pm,personal per 
    where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes' and pm.cod_personal_cargo=per.codigo";
    //+(pm.rc_iva)
    // echo $sql2."<br>";
    $stmt2 = $dbh->prepare($sql2);
    $stmt2->execute();
    while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
      $monto2=$row['monto'];
    }
    $montoTotal=$monto1-$monto2;
    return($montoTotal);
}


function totalRefrigerioArea($gestion, $mes, $cod_area,$cod_uo=null){
  $sql_add="";
  if($cod_uo!=null){
    $sql_add=" and pad.cod_uo='$cod_uo' ";
  }
  $dbh = new Conexion();


    $sql="SELECT sum(rd.monto*rd.dias_asistidos*(pad.porcentaje/100))as monto 
    from refrigerios r join refrigerios_detalle rd on r.codigo=rd.cod_refrigerio join personal_area_distribucion pad on rd.cod_personal=pad.cod_personal and pad.cod_estadoreferencial=1
    where r.cod_gestion='$gestion' and r.cod_mes='$mes' and pad.cod_area='$cod_area'  and rd.cod_estadoreferencial=1 $sql_add";
    // echo $sql."<br>";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $monto=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto=$row['monto'];
    }
    return($monto);
}

function totalRefrigerioMes($gestion, $mes){
  $dbh = new Conexion();
  $sql="SELECT sum(pm.monto*pm.dias_asistidos)as monto 
  from refrigerios p join refrigerios_detalle pm on p.codigo=pm.cod_refrigerio 
  where  p.cod_gestion='$gestion' and p.cod_mes='$mes'";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $monto=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto=$row['monto'];
    }
    return($monto);
}

//funcniones para contabilizacion de retroactivos
function totalGanadoAreaRetro($gestion, $mes, $cod_area,$cod_uo=null){
  $sql_add="";
  if($cod_uo!=null){
    $sql_add=" and pad.cod_uo='$cod_uo' ";
  }
  switch ($mes) {
    case 1:
      $sql_cabecera="sum((pm.retroactivo_enero+pm.antiguedad_enero+pm.bonoacademico_enero)*(pad.porcentaje/100))as monto";
    break;
    case 2:
      $sql_cabecera="sum((pm.retroactivo_febrero+pm.antiguedad_febrero+pm.bonoacademico_febrero)*(pad.porcentaje/100))as monto";
    break;
    case 3:
      $sql_cabecera="sum((pm.retroactivo_marzo+pm.antiguedad_marzo+pm.bonoacademico_marzo)*(pad.porcentaje/100))as monto";
    break;
    case 4:
      $sql_cabecera="sum((pm.retroactivo_abril+pm.antiguedad_abril+pm.bonoacademico_abril)*(pad.porcentaje/100))as monto";
    break;
  }
  $dbh = new Conexion();
  //Obtenemos el codigo de planilla para la distribucion de areas
  $sqlCodPlanilla="SELECT codigo from planillas p where p.cod_gestion='$gestion' and p.cod_mes='$mes'";
  $stmtCodPlanilla = $dbh->prepare($sqlCodPlanilla);
  $stmtCodPlanilla->execute();
  $codigoPlanillaX=0;
  while ($rowCodPlanilla = $stmtCodPlanilla->fetch(PDO::FETCH_ASSOC)) {
    $codigoPlanillaX=$rowCodPlanilla['codigo'];
  }
  //Fin obtener codigo planilla

  $sql="SELECT $sql_cabecera
  from planillas_retroactivos p join planillas_retroactivos_detalle pm on p.codigo=pm.cod_planilla join personal_area_distribucion_planilla pad on pm.cod_personal=pad.cod_personal and pad.cod_planilla='$codigoPlanillaX' 
  where p.cod_gestion='$gestion' and pad.cod_area='$cod_area' $sql_add";
    
    //echo $sql."<br>";
    
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $monto=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto=$row['monto'];
    }
    return($monto);
}

function obtenerTotalAFP_prev2_retroactivos($gestion, $mes){
  $dbh = new Conexion();
  switch ($mes) {
    case 1:
      $sql_cabecera="sum(pm.a_solidario_13_25_35_enero) as monto";
    break;
    case 2:
      $sql_cabecera="sum(pm.a_solidario_13_25_35_febrero) as monto";
    break;
    case 3:
      $sql_cabecera="sum(pm.a_solidario_13_25_35_marzo) as monto";
    break;
    case 4:
      $sql_cabecera="sum(pm.a_solidario_13_25_35_abril) as monto";
    break;
  }
  $sql="SELECT $sql_cabecera
  from planillas_retroactivos p join planillas_retroactivos_detalle pm on p.codigo=pm.cod_planilla 
  where p.cod_gestion='$gestion'";
    // echo $sql."<br>";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $monto=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto=$row['monto'];
    }
    return($monto);
}


function obtenerTotalAFPRetroactivos($gestion, $mes){
  $dbh = new Conexion();
  switch ($mes) {
    case 1:
      $sql_cabecera="pm.cod_personal, sum(pm.retroactivo_enero+pm.antiguedad_enero+pm.bonoacademico_enero) as monto";
    break;
    case 2:
      $sql_cabecera="pm.cod_personal, sum(pm.retroactivo_febrero+pm.antiguedad_febrero+pm.bonoacademico_febrero) as monto";
    break;
    case 3:
      $sql_cabecera="pm.cod_personal, sum(pm.retroactivo_marzo+pm.antiguedad_marzo+pm.bonoacademico_marzo) as monto";
    break;
    case 4:
      $sql_cabecera="pm.cod_personal, sum(pm.retroactivo_abril+pm.antiguedad_abril+pm.bonoacademico_abril) as monto";
    break;
  }
  $sql="SELECT $sql_cabecera
  from planillas_retroactivos p join planillas_retroactivos_detalle pm on p.codigo=pm.cod_planilla
  where p.cod_gestion='$gestion' group by pm.cod_personal";
    
    echo $sql."<br>";
    
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $monto=0;
    $montoAFPPivote=0;
    $montoAFPTotal=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoPersonal=$row['cod_personal'];
      $monto=$row['monto'];
      $montoAFPPivote=$monto*0.1271;
      if($codigoPersonal==84){
        $montoAFPPivote = $monto*0.0271; 
      }
      $montoAFPTotal+=$montoAFPPivote;
    }
    return($montoAFPTotal);

}


?>