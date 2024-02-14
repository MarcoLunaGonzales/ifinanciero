<?php
//oficial el dos no se usa
session_start();

require_once '../conexion.php';
require_once '../functions.php';
require_once '../rrhh/configModule.php';
require_once '../functionsGeneral.php';
$dbh = new Conexion();

$codigo = $_POST['cod_planillatrib'];
$codPlan = $_POST['cod_planilla'];

if($codigo==0){
  $codigo_pt=insertarPlanillaTributaria($codPlan);
  $flagsucess=ReprocesarPlanillaTribNuevo($codigo_pt,$codPlan);
  if($flagsucess)
    echo 1;
  else
    echo 0;
}else{
  actualizarPlanillaTributaria($codigo);  
  //procesarPlanillaTributaria($codigo,$codPlan); 
  $flagsucess=ReprocesarPlanillaTribNuevo($codigo,$codPlan);
  if($flagsucess)
    echo 1;
  else
    echo 0;
}
//actualizar la planilla tributaria el modified at
function actualizarPlanillaTributaria($codigo){
  $codigoUser=$_SESSION["globalUser"];
  $fechaActual=date("Y-m-d H:i:s"); 
  $dbhI = new Conexion();
  $sqlUpdate="UPDATE planillas_tributarias SET modified_by='$codigoUser',modified_at='$fechaActual' where codigo=$codigo";
  $stmtUpdate = $dbhI->prepare($sqlUpdate);
  $stmtUpdate->execute();
}
//insertar nueva planilla tributaria
function insertarPlanillaTributaria($codigo){
  $dbh = new Conexion();  
  $stmt = $dbh->prepare("SELECT cod_gestion,cod_mes from planillas where codigo=$codigo");
  $stmt->execute();
  $result= $stmt->fetch();
  $cod_gestion=$result['cod_gestion'];
  $cod_mes=$result['cod_mes'];
  $cod_estadoplanilla=2;    
  //insertar
  $created_by=$_SESSION["globalUser"];
  $modified_by=$_SESSION["globalUser"];
  $dbhI = new Conexion();
  $sqlInsert="INSERT into planillas_tributarias (cod_gestion,cod_mes,cod_estadoplanilla,created_by,modified_by) values(:cod_gestion,:cod_mes,:cod_estadoplanilla,:created_by,:modified_by)";
  $stmtInsert = $dbhI->prepare($sqlInsert);
  $stmtInsert->bindParam(':cod_gestion', $cod_gestion);
  $stmtInsert->bindParam(':cod_mes',$cod_mes);
  $stmtInsert->bindParam(':cod_estadoplanilla',$cod_estadoplanilla);
  $stmtInsert->bindParam(':created_by',$created_by);
  $stmtInsert->bindParam(':modified_by',$modified_by);
  $stmtInsert->execute();
  $ultimo = $dbh->lastInsertId();
  return $ultimo;
}

function ReprocesarPlanillaTribNuevo($codigo,$codPlan){
  $dbh = new Conexion();
  // session_start();
  
  $globalGestion=$_SESSION['globalGestion'];
  //BORRAR detalle planilla tributaria
  $sqlDelete="DELETE FROM planillas_tributarias_personal_mes_2 where cod_planillatributaria=$codigo";
  $stmtDelete = $dbh->prepare($sqlDelete);
  $stmtDelete->execute();

  //modificacion Para el retroactivo de gestion, solo apliuca a mes de mayo
  $sqlPlanillaS="SELECT cod_mes from planillas where codigo=$codPlan";
  $stmtPlanillaS=$dbh->prepare($sqlPlanillaS);
  $stmtPlanillaS->execute();
  $resultPlanillaS=$stmtPlanillaS->fetch();
  $cod_mes_planillaS=$resultPlanillaS['cod_mes'];
  

  //datos estaticos
  $salario_minimo_no_imponible=round(obtenerSueldoMinimo()*2,0);
  $impuesto_sueldo_gravado=obtenerValorConfiguracionPlanillas(21);
  //insertamos los datos
  $planillas="SELECT pl.cod_personalcargo,pl.afp_1,pl.afp_2,pl.total_ganado,p.cod_mes,p.cod_gestion,(select nombre from gestiones where codigo=p.cod_gestion)as gestion,(select rc.monto_iva from rc_ivapersonal rc where rc.cod_personal=pl.cod_personalcargo and rc.cod_mes=p.cod_mes and rc.cod_gestion=p.cod_gestion and rc.cod_estadoreferencial=1) as monto_iva
    FROM planillas_personal_mes pl,planillas p where pl.cod_planilla=p.codigo and pl.cod_planilla=$codPlan";
  //and pl.cod_personalcargo in (84,93,183,195,286,32,176,96,68,16,97)
  $stmtPlanillas=$dbh->prepare($planillas);
  $stmtPlanillas->execute();
  while ($row = $stmtPlanillas->fetch(PDO::FETCH_ASSOC)) {
    $cod_personal=$row['cod_personalcargo'];
    $cod_mes=$row['cod_mes'];
    $cod_gestion=$row['cod_gestion'];
    $mes=str_pad($cod_mes, 2, "0", STR_PAD_LEFT);
    $gestion=$row['gestion'];
    $afp_1=$row['afp_1'];
    $afp_2=$row['afp_2'];
    $total_ganado=$row['total_ganado'];
    
    //Solo en mayo se adiciona el retroactivo
    $liquido_pagableRetroactivo=0;
    if($cod_mes_planillaS==5){ 
      $sqlPlanillaRetro="SELECT pd.liquido_pagable from planillas_retroactivos p join planillas_retroactivos_detalle pd on p.codigo=pd.cod_planilla
      where p.cod_gestion=$globalGestion and pd.cod_personal=$cod_personal";
      $stmtPlanillaRetro=$dbh->prepare($sqlPlanillaRetro);
      $stmtPlanillaRetro->execute();
      $resultPlanillaRetro=$stmtPlanillaRetro->fetch();
      $liquido_pagableRetroactivo=$resultPlanillaRetro['liquido_pagable'];
      if($liquido_pagableRetroactivo>0){
        $total_ganado+=$liquido_pagableRetroactivo;
      }
    } //Fin retroactivos

    /**
     * Solo en Diciembre | Aguinaldo
     */
    if($cod_mes_planillaS==12){ 
        $liquido_pagableRetroactivo=0;
        $sqlAguinaldo="	SELECT pad.total_aguinaldo
                            FROM planillas_aguinaldos pa
                            LEFT JOIN planillas_aguinaldos_detalle pad ON pad.cod_planilla = pa.codigo
                            WHERE pa.cod_gestion = '$globalGestion'
                            AND pad.cod_personal = '$cod_personal'";
        $stmtAguinaldo=$dbh->prepare($sqlAguinaldo);
        $stmtAguinaldo->execute();
        $resultAguinaldo = $stmtAguinaldo->fetch();
        $liquido_pagableRetroactivo = $resultAguinaldo['total_aguinaldo'];
        if($liquido_pagableRetroactivo > 0){
            $total_ganado += $liquido_pagableRetroactivo;
        }
    } //Fin retroactivos

    /***********************
     * Adiciona Refrigerio *
     ***********************/
    $liquido_pagableRefrigerio=0;
    $sqlRefrigerio="SELECT 
                        rd.codigo as cod_ref_detalle,
                        CONCAT(p.paterno, ' ', p.materno, ' ', p.primer_nombre) as nombrepersonal,
                        rd.dias_asistidos as dias_asistencia,
                        rd.monto as monto_refrigerio,
                        (rd.dias_asistidos * rd.monto) AS total_mensual  
                    FROM refrigerios_detalle rd 
                        LEFT JOIN refrigerios r ON r.codigo = rd.cod_refrigerio
                        LEFT JOIN personal p ON p.codigo = rd.cod_personal
                    WHERE rd.cod_estadoreferencial = 1
                    AND r.cod_gestion = '$cod_gestion'
                    AND r.cod_mes = '$cod_mes'
                    AND rd.cod_personal = '$cod_personal'";
    // echo $sqlRefrigerio;
    $stmtRefrigerio=$dbh->prepare($sqlRefrigerio);
    $stmtRefrigerio->execute();
    $resultRefrigerio = $stmtRefrigerio->fetch();
    if($resultRefrigerio){
        $liquido_pagableRefrigerio = $resultRefrigerio['total_mensual'];
        $total_ganado += $liquido_pagableRefrigerio;
    }
    //Fin Refrigerio
    /********************
     * Adiciona Viático *
     ********************/
    $liquido_pagableViatico=0;
    $sqlViatico="SELECT
                    cd.fecha, 
                    cd.monto, 
                    cd.cod_proveedores, 
                    UPPER(af.nombre) as nombre_proveedor, 
                    SUM(cd.monto) as total
                FROM 
                    caja_chica c
                    INNER JOIN caja_chicadetalle cd ON c.codigo = cd.cod_cajachica
                    LEFT JOIN af_proveedores af ON af.codigo = cd.cod_proveedores
                    LEFT JOIN personal p ON p.cod_proveedor = af.codigo
                WHERE c.codigo = cd.cod_cajachica
                AND cd.cod_cuenta = 469
                AND YEAR(cd.fecha) = '$gestion'
                AND MONTH(cd.fecha) = '$cod_mes'
                AND c.cod_tipocajachica = 34
                AND p.codigo = '$cod_personal'
                AND p.codigo IS NOT NULL
                AND c.cod_estadoreferencial <> 2
                AND cd.cod_estadoreferencial <> 2
                GROUP BY cd.cod_proveedores";
    // echo $sqlViatico;
    $stmtViatico=$dbh->prepare($sqlViatico);
    $stmtViatico->execute();
    $resultViatico = $stmtViatico->fetch();
    if($resultViatico){
        $liquido_pagableViatico = $resultViatico['total'];
        $total_ganado += $liquido_pagableViatico;
    }
    //Fin Viático
    /**********************************
     * Adiciona Solicitud de Recursos *
     **********************************/
    $importe_neto_sr=0;
    $sqlSR="SELECT s.codigo as codigo, 
                    s.numero,
                    DATE_FORMAT(s.fecha,'%d-%m-%Y') as fecha,
                    sd.glosa_comprobantedetalle as observaciones, 
                    SUM(sd.importe) as monto
                FROM solicitud_recursos s, solicitud_recursosdetalle sd
                LEFT JOIN personal p ON p.cod_proveedor = sd.cod_proveedor
                WHERE s.codigo = sd.cod_solicitudrecurso 
                AND sd.cod_plancuenta = 469 
                AND p.codigo = '$cod_personal'
                AND YEAR(s.fecha) = '$gestion'
                AND MONTH(s.fecha) = '$cod_mes'
                AND s.cod_estadosolicitudrecurso = 5
                ORDER BY s.fecha DESC";
    // echo $sqlSR;
    $stmtSR=$dbh->prepare($sqlSR);
    $stmtSR->execute();
    $resultSR = $stmtSR->fetch();
    if($resultSR){
        $importe_neto_sr = $resultSR['monto'];
        $total_ganado += $importe_neto_sr;
    }
    $liquido_pagableViatico += $importe_neto_sr;
    //Fin Solicitud de Recursos
        
    $monto_iva=$row['monto_iva'];
    if($monto_iva==null||$monto_iva==""){
      $monto_iva=0;
    }
    $sqlPatronal="SELECT a_solidario_13000,a_solidario_25000,a_solidario_35000 from planillas_personal_mes_patronal where cod_personal_cargo=$cod_personal and cod_planilla=$codPlan";
    $stmtPatronal=$dbh->prepare($sqlPatronal);
    $stmtPatronal->execute();
    $resultPatronal=$stmtPatronal->fetch();
    $a_solidario_13000=$resultPatronal['a_solidario_13000'];
    $a_solidario_25000=$resultPatronal['a_solidario_25000'];
    $a_solidario_35000=$resultPatronal['a_solidario_35000'];

    $dato_auxiliar1=$afp_1+$afp_2+$a_solidario_13000+$a_solidario_25000+$a_solidario_35000;
    $monto_de_ingreso_neto=round($total_ganado-$dato_auxiliar1,2);//
    
    if($monto_de_ingreso_neto>$salario_minimo_no_imponible) 
      $importe_sujeto_a_impuesto_I=round($monto_de_ingreso_neto-$salario_minimo_no_imponible,0);
    else
      $importe_sujeto_a_impuesto_I=0;//redondear 

    $impuesto_rc_iva=round($importe_sujeto_a_impuesto_I*$impuesto_sueldo_gravado/100,0);//redondear
    if($importe_sujeto_a_impuesto_I>0)
      $salarios_minimos_nacionales_13=round($salario_minimo_no_imponible*$impuesto_sueldo_gravado/100,0);
    else
      $salarios_minimos_nacionales_13=0;
    if($impuesto_rc_iva>$salarios_minimos_nacionales_13)
      $impuesto_neto_rc_iva= round($impuesto_rc_iva-$salarios_minimos_nacionales_13,0);
    else
      $impuesto_neto_rc_iva=0;
    //13% del form110
    $porcentaje_formulario110=round($monto_iva,0);
    //SALDO A FAVOR DEL FISCO
    //fisco (no se debe redondear)
    if($impuesto_neto_rc_iva>$porcentaje_formulario110)
      $saldo_favor_fisico=round($impuesto_neto_rc_iva-$porcentaje_formulario110,0);
    else
      $saldo_favor_fisico=0;
    //SALDO A FAVOR DEL DEPENDIENTE
    if($porcentaje_formulario110>$impuesto_neto_rc_iva)
      $saldo_favor_del_dependiente=round($porcentaje_formulario110-$impuesto_neto_rc_iva,0);
    else
      $saldo_favor_del_dependiente=0;
    //SALDO A FAVOR DEL DEPENDIENTE PERIODO ANTERIOR
         //////////////////////si es del mes de enero
        if((int)$cod_mes==1){
          $cod_gestion_ant=((int)$cod_gestion-1);
          $cod_mes_ant=12;
        }else{
          $cod_gestion_ant=(int)$cod_gestion;
          $cod_mes_ant=(int)$cod_mes-1;
        }
        /////////////////////////
    $saldo_mes_anterior=round(obtenerSaldoMesAnteriorTrib($cod_personal,$cod_mes_ant,$cod_gestion_ant),0);
    // $saldo_mes_anterior= 6543;
    //MANTENIMIENTO DE VALOR DEL SALDO A FAVOR DEL DEPENDIENTE DEL PERIODO ANTERIOR
    
    $fecha_inicio=date($gestion."-".$mes."-01");
    //UFV Anterior
    $fecha_anterior=date('Y-m-t',strtotime($fecha_inicio." - 1 days"));
    $fecha_fin=date('Y-m-t',strtotime($fecha_inicio));
    // echo $fecha_inicio."***".$fecha_anterior."***".$fecha_fin;
    $ufv_anterior=obtenerUFV($fecha_anterior);
    $ufv_actual=obtenerUFV($fecha_fin);

    // echo $ufv_anterior."-".$ufv_actual."<br>";
      $mantenimiento_saldo_mes_anterior=round(($saldo_mes_anterior*($ufv_actual/$ufv_anterior)-$saldo_mes_anterior),0);
      //SALDO DEL PERIODO ANTERIOR ACTUALIZADO
      $saldo_mes_anterior_actualizado=round($saldo_mes_anterior+$mantenimiento_saldo_mes_anterior,0);
      //SALDO UTILIZADO
      if($saldo_mes_anterior_actualizado<=$saldo_favor_fisico)
        $saldo_utilizado=round($saldo_mes_anterior_actualizado,0);
      else
      {
        if($saldo_favor_fisico<$saldo_mes_anterior_actualizado)
          $saldo_utilizado=$saldo_favor_fisico;
        else
          $saldo_utilizado=0;
      }
      //IMPUESTO RC-IVA RETENIDO
      if($saldo_favor_fisico>$saldo_utilizado)
        $impuesto_rc_iva_retenido=round($saldo_favor_fisico-$saldo_utilizado,0);
      else
        $impuesto_rc_iva_retenido=0;
      //SALDO DE CRÉDITO FISCAL A FAVOR DEL DEPENDIENTE PARA EL MES SIGUIENTE
      $saldo_credito_fiscal_siguiente=round($saldo_favor_del_dependiente+$saldo_mes_anterior_actualizado-$saldo_utilizado,0);

      $dbhInstert = new Conexion();
      $sqlInsert="INSERT INTO planillas_tributarias_personal_mes_2 (cod_planillatributaria,cod_personal,monto_ingreso_neto,minimo_no_imponble,importe_sujeto_impuesto_i,impuesto_rc_iva,minimo_13,impuesto_neto_rc_iva,formulario_110_13,saldo_favor_fisico,saldo_favor_dependiente,saldo_mes_anterior,mantenimiento_saldo_mes_anterior,saldo_anterior_actualizado,saldo_utilizado,impuesto_rc_iva_retenido,saldo_credito_fiscal_mes_siguiente,monto_retroactivo,monto_refrigerio,monto_viatico) 
     VALUES (
      '$codigo',
      '$cod_personal',
      '$monto_de_ingreso_neto',
      '$salario_minimo_no_imponible',
      '$importe_sujeto_a_impuesto_I',
      '$impuesto_rc_iva',
      '$salarios_minimos_nacionales_13',
      '$impuesto_neto_rc_iva',
      '$porcentaje_formulario110',
      '$saldo_favor_fisico',
      '$saldo_favor_del_dependiente',
      '$saldo_mes_anterior',
      '$mantenimiento_saldo_mes_anterior',
      '$saldo_mes_anterior_actualizado',
      '$saldo_utilizado',
      '$impuesto_rc_iva_retenido',
      '$saldo_credito_fiscal_siguiente',
      '$liquido_pagableRetroactivo',
      '$liquido_pagableRefrigerio',
      '$liquido_pagableViatico'
      )";
     $stmtInsert = $dbhInstert->prepare($sqlInsert);
     $flagsuccess=$stmtInsert->execute();
  }
  return $flagsuccess;

}


