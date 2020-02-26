<?php
session_start();

require_once '../conexion.php';
require_once '../functions.php';
require_once '../rrhh/configModule.php';
require_once '../functionsGeneral.php';
$dbh = new Conexion();

$codigo = $_POST['cod_planillatrib'];
$codPlan = $_POST['cod_planilla'];
if($codigo==0){
  insertarPlanillaTributaria($codPlan);
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
  $stmt = $dbh->prepare("SELECT * from planillas where codigo=$codigo");
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
}

function procesarPlanillaTributaria($codigo,$codPlan){
  $dbh = new Conexion();

  //BORRAR detalle planilla tributaria
  $sqlDelete="DELETE FROM planillas_tributarias_personal_mes where cod_planillatributaria=$codigo";
  $stmtDelete = $dbh->prepare($sqlDelete);
  $stmtDelete->execute();

  //insertamos los datos
  $planillas="SELECT pl.*,p.cod_mes,p.cod_gestion,(select monto_iva from rc_ivapersonal where cod_personal=pl.cod_personalcargo) as monto_iva FROM planillas_personal_mes pl,planillas p where pl.cod_planilla=p.codigo and pl.cod_planilla=$codPlan";
  $stmtPlanillas=$dbh->prepare($planillas);
  $stmtPlanillas->execute();
  //datos estaticos
  $minimo_no_imponible=obtenerSueldoMinimo()*2;
  $impuesto_sueldo_gravado=obtenerValorConfiguracionPlanillas(21);
   
  while ($row = $stmtPlanillas->fetch(PDO::FETCH_ASSOC)) {
   	$cod_personal=$row['cod_personalcargo'];
   	$cod_mes=$row['cod_mes'];
   	$cod_gestion=$row['cod_gestion'];

    $monto_iva=$row['monto_iva'];
    if($monto_iva==null||$monto_iva==""){
      $monto_iva=0;
    }
   	//valores constantes
    $importe_cotizable=$row['liquido_pagable'];
    $prima=0;
    $otros_ingresos=0;
    // valores de plantilla
    $total_ganado=$importe_cotizable+$prima+$otros_ingresos;
    //suedo gravado
    if($total_ganado>$minimo_no_imponible){
      $sueldo_gravado=$total_ganado-$minimo_no_imponible;
    }else{
      $sueldo_gravado=0;     	
    }

    //********************************estan sin redondear*****************************
    //sueldo grabado porcentaje
    $porcentaje_sueldogravado=$sueldo_gravado*($impuesto_sueldo_gravado/100);
    //13% del form110
    
    $porcentaje_formulario110=obtenerRcIvaPersonal($cod_personal,$cod_mes,$cod_gestion);
    // echo "pers".$cod_personal."-".$porcentaje_formulario110."<br>";
    $porcentaje_formulario110=$monto_iva;
    //porcentajeSueldoMinimo
    $porcentaje_minimonoimponible=$minimo_no_imponible*(obtenerValorConfiguracionPlanillas(21)/100);
    //fisco (no se debe redondear)
    if($porcentaje_sueldogravado>($porcentaje_formulario110+$porcentaje_minimonoimponible)){
      $fisco=$porcentaje_sueldogravado-$porcentaje_formulario110-$porcentaje_minimonoimponible;	
    }else{
      $fisco=0;
    }
    //dependiente (si pide redondear)
    if($porcentaje_sueldogravado<($porcentaje_formulario110+$porcentaje_minimonoimponible)){
      $dependiente=$porcentaje_minimonoimponible+$porcentaje_formulario110-$porcentaje_sueldogravado;
    }else{
      $dependiente=0;
    }

    //saldo anterior pendiente
         //////////////////////si es del mes de enero
        if((int)$cod_mes==1){
          $cod_gestion_ant=((int)$cod_gestion-1);
          $cod_mes_ant=12;
        }else{
          $cod_gestion_ant=(int)$cod_gestion;
          $cod_mes_ant=(int)$cod_mes-1;
        }
        /////////////////////////
    $saldo_mes_anterior=obtenerSaldoMesAnteriorTrib($cod_personal,$cod_mes_ant,$cod_gestion_ant);
    $saldo_mes_anterior_actualizado=$saldo_mes_anterior;

    /*********************************************************************************************/
     
     //total saldo
     $total_saldo=($saldo_mes_anterior+$saldo_mes_anterior_actualizado);
     
     //total saldo favor 
     $total_saldo_favordependiente=$dependiente+$total_saldo;
     
     //saldo utilizado
     if($fisco<$total_saldo_favordependiente){
      $saldo_utilizado=$fisco;
     }else{
      $saldo_utilizado=$total_saldo_favordependiente;
     }

     //importe retenido (redondear)
     if($fisco>$total_saldo_favordependiente){
     	$importe_retenido=$fisco-$total_saldo_favordependiente;
     }else{
     	$importe_retenido=0;
     }
     $dbhInstert = new Conexion();
     $sqlInsert="INSERT INTO planillas_tributarias_personal_mes (cod_planillatributaria,cod_personal,importe_cotizable,prima,otros_ingresos,total_ganado,minimo_no_imponible,sueldo_gravado,porcentaje_sueldogravado,porcentaje_formulario110,porcentaje_minimonoimponible,fisco,dependiente,saldo_mes_anterior,saldo_mes_anterior_actualizado,total_saldo,total_saldo_favordependiente,saldo_utilizado,importe_retenido,cod_estadoreferencial) 
     VALUES (
      '$codigo',
      '$cod_personal',
      '$importe_cotizable',
      '$prima',
      '$otros_ingresos',
      '$total_ganado',
      '$minimo_no_imponible',
      '$sueldo_gravado',
      '$porcentaje_sueldogravado',
      '$porcentaje_formulario110',
      '$porcentaje_minimonoimponible',
      '$fisco',
      '$dependiente',
      '$saldo_mes_anterior',
      '$saldo_mes_anterior_actualizado',
      '$total_saldo',
      '$total_saldo_favordependiente',
      '$saldo_utilizado',
      '$importe_retenido',
      '1'
     	)";
     $stmtInsert = $dbhInstert->prepare($sqlInsert);
     $stmtInsert->execute();     	
   } //while plantillas
}

function ReprocesarPlanillaTribNuevo($codigo,$codPlan){
  $dbh = new Conexion();

  //BORRAR detalle planilla tributaria
  $sqlDelete="DELETE FROM planillas_tributarias_personal_mes_2 where cod_planillatributaria=$codigo";
  $stmtDelete = $dbh->prepare($sqlDelete);
  $stmtDelete->execute();
  //datos estaticos
  $salario_minimo_no_imponible=obtenerSueldoMinimo()*2;
  $impuesto_sueldo_gravado=obtenerValorConfiguracionPlanillas(21);
  //insertamos los datos
  $planillas="SELECT pl.*,p.cod_mes,p.cod_gestion,(select monto_iva from rc_ivapersonal where cod_personal=pl.cod_personalcargo) as monto_iva FROM planillas_personal_mes pl,planillas p where pl.cod_planilla=p.codigo and pl.cod_planilla=$codPlan";
  //and pl.cod_personalcargo in (84,93,183,195,286,32,176,96,68,16,97)
  $stmtPlanillas=$dbh->prepare($planillas);
  $stmtPlanillas->execute();
  while ($row = $stmtPlanillas->fetch(PDO::FETCH_ASSOC)) {
    $cod_personal=$row['cod_personalcargo'];
    $cod_mes=$row['cod_mes'];
    $cod_gestion=$row['cod_gestion'];
    
    $afp_1=$row['afp_1'];
    $afp_2=$row['afp_2'];
    $total_ganado=$row['total_ganado'];
    // $afp_1=0;
    // $afp_2=1340.41;
    // $total_ganado=10546.07;


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
    // $monto_iva=0;
    // $a_solidario_13000=0;
    // $a_solidario_25000=0;
    // $a_solidario_35000=0;



    $dato_auxiliar1=$afp_1+$afp_2+$a_solidario_13000+$a_solidario_25000+$a_solidario_35000;
    $monto_de_ingreso_neto=$total_ganado-$dato_auxiliar1;//
    
    if($monto_de_ingreso_neto>$salario_minimo_no_imponible) 
      $importe_sujeto_a_impuesto_I=$monto_de_ingreso_neto-$salario_minimo_no_imponible;
    else
      $importe_sujeto_a_impuesto_I=0;//redondear 

    $impuesto_rc_iva=$importe_sujeto_a_impuesto_I*$impuesto_sueldo_gravado/100;//redondear
    if($importe_sujeto_a_impuesto_I>0)
      $salarios_minimos_nacionales_13=$salario_minimo_no_imponible*$impuesto_sueldo_gravado/100;
    else
      $salarios_minimos_nacionales_13=0;
    if($impuesto_rc_iva>$salarios_minimos_nacionales_13)
      $impuesto_neto_rc_iva= $impuesto_rc_iva-$salarios_minimos_nacionales_13;
    else
      $impuesto_neto_rc_iva=0;
    //13% del form110
    $porcentaje_formulario110=$monto_iva;
    //SALDO A FAVOR DEL FISCO
    //fisco (no se debe redondear)
    if($impuesto_neto_rc_iva>$porcentaje_formulario110)
      $saldo_favor_fisico=$impuesto_neto_rc_iva-$porcentaje_formulario110;
    else
      $saldo_favor_fisico=0;
    //SALDO A FAVOR DEL DEPENDIENTE
    if($porcentaje_formulario110>$impuesto_neto_rc_iva)
      $saldo_favor_del_dependiente=$porcentaje_formulario110-$impuesto_neto_rc_iva;
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
    $saldo_mes_anterior=obtenerSaldoMesAnteriorTrib($cod_personal,$cod_mes_ant,$cod_gestion_ant);
    // $saldo_mes_anterior= 6543;
    //MANTENIMIENTO DE VALOR DEL SALDO A FAVOR DEL DEPENDIENTE DEL PERIODO ANTERIOR
    

      //UFV Anterior
      $fecha_inicio=date("Y-m-01");
      $fecha_actual=date("Y-m-d");

      $ufv_anterior=obtenerUFV($fecha_inicio);
      $ufv_actual=obtenerUFV($fecha_actual);

       

      $mantenimiento_saldo_mes_anterior=($saldo_mes_anterior*$ufv_actual/$ufv_anterior-$saldo_mes_anterior);
      //SALDO DEL PERIODO ANTERIOR ACTUALIZADO
      $saldo_mes_anterior_actualizado=$saldo_mes_anterior+$mantenimiento_saldo_mes_anterior;
      //SALDO UTILIZADO
      if($saldo_mes_anterior_actualizado<=$saldo_favor_fisico)
        $saldo_utilizado=$saldo_mes_anterior_actualizado;
      else
      {
        if($saldo_favor_fisico<$saldo_mes_anterior_actualizado)
          $saldo_utilizado=$saldo_favor_fisico;
        else
          $saldo_utilizado=0;
      }
      //IMPUESTO RC-IVA RETENIDO
      if($saldo_favor_fisico>$saldo_utilizado)
        $impuesto_rc_iva_retenido=$saldo_favor_fisico-$saldo_utilizado;
      else
        $impuesto_rc_iva_retenido=0;
      //SALDO DE CRÉDITO FISCAL A FAVOR DEL DEPENDIENTE PARA EL MES SIGUIENTE
      $saldo_credito_fiscal_siguiente=$saldo_favor_del_dependiente+$saldo_mes_anterior_actualizado-$saldo_utilizado;
      $dbhInstert = new Conexion();
      $sqlInsert="INSERT INTO planillas_tributarias_personal_mes_2 (cod_planillatributaria,cod_personal,monto_ingreso_neto,minimo_no_imponble,importe_sujeto_impuesto_i,impuesto_rc_iva,minimo_13,impuesto_neto_rc_iva,formulario_110_13,saldo_favor_fisico,saldo_favor_dependiente,saldo_mes_anterior,mantenimiento_saldo_mes_anterior,saldo_anterior_actualizado,saldo_utilizado,impuesto_rc_iva_retenido,saldo_credito_fiscal_mes_siguiente) 
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
      '$saldo_credito_fiscal_siguiente'
      )";
     $stmtInsert = $dbhInstert->prepare($sqlInsert);
     $flagsuccess=$stmtInsert->execute();
  }
  return $flagsuccess;

}


