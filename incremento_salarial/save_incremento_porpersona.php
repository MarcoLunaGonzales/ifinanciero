<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';

$dbh = new Conexion();

session_start();
$tipo=4;
$created_at=date('Y-m-d H:i:s');
$fecha_cambio=date('Y-m-d');
$gestion=date('Y');
$created_by=$_SESSION['globalUser'];

$incremento_smn_g=$_POST['incremento_smn_g'];
$incremento_hb_g=$_POST['incremento_hb_g'];
$incremento_smn_monto=$_POST['incremento_smn_monto'];
$minimo_salarial_anterior=obtenerValorConfiguracionPlanillas(1);
$array_codPesonal=$_POST['codigo_persona'];//array de codigos de personal
$haber_basico_nuevo=$_POST['hbn'];//array de nuevo haber basico
$haber_basico_ant=$_POST['hba'];//array de nuevo haber basico

if(isset($_POST['bandera_edit'])){
  $bandera_nuevo=false;
}else{
  $bandera_nuevo=true;
}

$index=0;
// var_dump($haber_basico_ant);
foreach($array_codPesonal as $key => $cod_personal) {
  $persona_seleccionada=$_POST['personal_seleccionado'.$index];//array de nuevo haber basico
  if($persona_seleccionada==1){
    $haber_basico = $haber_basico_nuevo[$index];
    $haber_basico_anterior = $haber_basico_ant[$index];  
    $descripcion="Inc Salarial ".date('Y').". SMN:".$incremento_smn_g."%, HB:".$incremento_hb_g."%. ".formatNumberDec($haber_basico_anterior)." -> ".formatNumberDec($haber_basico);
    $stmt = $dbh->prepare("UPDATE personal set haber_basico=:haber_basico,haber_basico_anterior=:haber_basico_anterior where codigo = :codigo");
    $stmt->bindParam(':codigo', $cod_personal);
    $stmt->bindParam(':haber_basico', $haber_basico);
    $stmt->bindParam(':haber_basico_anterior', $haber_basico_anterior);
    $flagSuccess=$stmt->execute();
    if($bandera_nuevo){
      //para el historico
      $sql="INSERT into historico_cambios_personal(cod_personal,tipo,descripcion,fecha_cambio,created_by,created_at)
      values(:cod_personal,:tipo,:descripcion,:fecha_cambio,:created_by,:created_at)";
      $stmtInsert = $dbh->prepare($sql);
      $stmtInsert->bindParam(':cod_personal', $cod_personal);
      $stmtInsert->bindParam(':tipo',$tipo);
      $stmtInsert->bindParam(':descripcion',$descripcion);
      $stmtInsert->bindParam(':fecha_cambio',$fecha_cambio);
      $stmtInsert->bindParam(':created_by',$created_by);
      $stmtInsert->bindParam(':created_at',$created_at);
      $flagSuccess=$stmtInsert->execute();  
    }
  }elseif(!$bandera_nuevo){
    $haber_basico = $haber_basico_nuevo[$index];
    $haber_basico_anterior = $haber_basico_ant[$index];  
    $descripcion="Inc Salarial ".date('Y').". SMN:".$incremento_smn_g."%, HB:".$incremento_hb_g."%. ".formatNumberDec($haber_basico)." -> ".formatNumberDec($haber_basico_anterior);
    $stmt = $dbh->prepare("UPDATE personal set haber_basico='$haber_basico_anterior',haber_basico_anterior=null where codigo = $cod_personal");
    $flagSuccess=$stmt->execute();     
    //para el historico
    $sql="INSERT into historico_cambios_personal(cod_personal,tipo,descripcion,fecha_cambio,created_by,created_at)
    values(:cod_personal,:tipo,:descripcion,:fecha_cambio,:created_by,:created_at)";
    $stmtInsert = $dbh->prepare($sql);
    $stmtInsert->bindParam(':cod_personal', $cod_personal);
    $stmtInsert->bindParam(':tipo',$tipo);
    $stmtInsert->bindParam(':descripcion',$descripcion);
    $stmtInsert->bindParam(':fecha_cambio',$fecha_cambio);
    $stmtInsert->bindParam(':created_by',$created_by);
    $stmtInsert->bindParam(':created_at',$created_at);
    $flagSuccess=$stmtInsert->execute();
  }
  $index++;
}

if($bandera_nuevo){  
  $salario_minimo_nacional_nuevo=$incremento_smn_monto;
  $stmtUpdateConf = $dbh->prepare("UPDATE configuraciones_planillas set valor_configuracion='$salario_minimo_nacional_nuevo' where id_configuracion = '1'");//salario minimo nacional nuevo
  $stmtUpdateConf->execute();
  $stmtUpdateConf = $dbh->prepare("UPDATE configuraciones_planillas set valor_configuracion='$gestion' where id_configuracion = '29'");  //gestion procesada
  $stmtUpdateConf->execute();
  $stmtUpdateConf = $dbh->prepare("UPDATE configuraciones_planillas set valor_configuracion='$minimo_salarial_anterior' where id_configuracion = '31'");  //salario minimo nacional anterior
  $stmtUpdateConf->execute();
  $stmtUpdateConf = $dbh->prepare("UPDATE configuraciones_planillas set valor_configuracion='$incremento_smn_g' where id_configuracion = '32'");   //% incremento Salario minimo nacional
  $stmtUpdateConf->execute();
  $stmtUpdateConf = $dbh->prepare("UPDATE configuraciones_planillas set valor_configuracion='$incremento_hb_g' where id_configuracion = '33'");   //% incremento Haber basico
  $stmtUpdateConf->execute();

  //creamos la planilla de retroactivos
  $cod_gestion=codigoGestion($gestion);
  $cod_estadoplanilla=1;
  $sqlPlanillaRetro="INSERT into planillas_retroactivos(cod_gestion,cod_estadoplanilla,created_by,created_at) values(:cod_gestion,:cod_estadoplanilla,:created_by,:created_at)";
  $stmtPlanillaRetro = $dbh->prepare($sqlPlanillaRetro);
  $stmtPlanillaRetro->bindParam(':cod_gestion', $cod_gestion);
  $stmtPlanillaRetro->bindParam(':cod_estadoplanilla',$cod_estadoplanilla);
  $stmtPlanillaRetro->bindParam(':created_by',$created_by);
  $stmtPlanillaRetro->bindParam(':created_at',$created_at);
  $stmtPlanillaRetro->execute();
}
$flagSuccess=true;
showAlertSuccessError($flagSuccess,"../index.php?opcion=planillasRetroactivoPersonal");

?>
