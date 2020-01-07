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
  procesarPlanillaTributaria($codigo,$codPlan);	
}

echo 1;

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
   $planillas="SELECT pl.*,p.cod_mes,p.cod_gestion FROM planillas_personal_mes pl,planillas p where pl.cod_planilla=p.codigo and pl.cod_planilla=$codPlan";
   $stmtPlanillas=$dbh->prepare($planillas);
   $stmtPlanillas->execute();
   
   while ($row = $stmtPlanillas->fetch(PDO::FETCH_ASSOC)) {
   	$cod_personal=$row['cod_personalcargo'];
   	$cod_mes=$row['cod_mes'];
   	$cod_gestion=$row['cod_gestion'];

   	//valores constantes
    $importe_cotizable=0;
    $prima=30;
    $otros_ingresos=0;
    // valores de plantilla

    $total_ganado=$row['total_ganado'];

    
    $minimo_no_imponible=obtenerSueldoMinimo()*2;
    
    //suedo gravado
    if($total_ganado>$minimo_no_imponible){
      $sueldo_gravado=$total_ganado-$minimo_no_imponible;
    }else{
      $sueldo_gravado=0;     	
    }

    //********************************estan sin redondear*****************************
    //sueldo grabado porcentaje
    $porcentaje_sueldogravado=$sueldo_gravado*(obtenerValorConfiguracionPlanillas(21)/100);
    
    //13% del form110
    $porcentaje_formulario110=obtenerRcIvaPersonal($cod_personal,$cod_mes,$cod_gestion);

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
    $saldo_mes_anterior=0;
    $saldo_mes_anterior_actualizado=0;

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
     $stmtInsert = $dbh->prepare($sqlInsert);
     $stmtInsert->execute();     	
   } //while plantillas
}


