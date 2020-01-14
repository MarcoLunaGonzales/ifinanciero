<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$monto=$_POST["monto"];
$codPersona=$_POST["personal"];
$codBono=$_POST["codBono"];
$desde=$_POST["desde"];
$hasta=$_POST["hasta"];
$codGestion=$_POST["codGestion"];
$obs=$_POST["obs"];
$codEstado="1";
if($hasta!=0){
if($desde<$hasta){
 for ($i=$desde; $i <= $hasta; $i++) { 
// Prepare
  $codMes=$i;
  $stmt = $dbh->prepare("INSERT INTO bonos_personal_mes (cod_bono, cod_personal,cod_gestion,cod_mes,monto,observaciones, cod_estadoreferencial) 
                        VALUES (:cod_bono,:codPersona,:codGestion,:codMes,:monto,:observaciones, :cod_estado)");
// Bind
  $stmt->bindParam(':monto', $monto);
  $stmt->bindParam(':cod_estado', $codEstado);
  $stmt->bindParam(':cod_bono',$codBono);
  $stmt->bindParam(':codPersona',$codPersona);
  $stmt->bindParam(':codGestion',$codGestion);
  $stmt->bindParam(':codMes',$codMes);
  $stmt->bindParam(':observaciones',$obs);
  $flagSuccess=$stmt->execute();	
 }	
}else{
   for ($i=$desde; $i <= 12; $i++) { 
// Prepare
  $codMes=$i;
  $stmt = $dbh->prepare("INSERT INTO bonos_personal_mes (cod_bono, cod_personal,cod_gestion,cod_mes,monto,observaciones, cod_estadoreferencial) 
                        VALUES (:cod_bono,:codPersona,:codGestion,:codMes,:monto,:observaciones, :cod_estado)");
// Bind
  $stmt->bindParam(':monto', $monto);
  $stmt->bindParam(':cod_estado', $codEstado);
  $stmt->bindParam(':cod_bono',$codBono);
  $stmt->bindParam(':codPersona',$codPersona);
  $stmt->bindParam(':codGestion',$codGestion);
  $stmt->bindParam(':codMes',$codMes);
  $stmt->bindParam(':observaciones',$obs);
  $flagSuccess=$stmt->execute();	
 }
   for ($i=1; $i <= $hasta; $i++) { 
// Prepare
  $codMes=$i;
  $stmt = $dbh->prepare("INSERT INTO bonos_personal_mes (cod_bono, cod_personal,cod_gestion,cod_mes,monto,observaciones, cod_estadoreferencial) 
                        VALUES (:cod_bono,:codPersona,:codGestion,:codMes,:monto,:observaciones, :cod_estado)");
// Bind
  $stmt->bindParam(':monto', $monto);
  $stmt->bindParam(':cod_estado', $codEstado);
  $stmt->bindParam(':cod_bono',$codBono);
  $stmt->bindParam(':codPersona',$codPersona);
  $stmt->bindParam(':codGestion',$codGestion);
  $stmt->bindParam(':codMes',$codMes);
  $stmt->bindParam(':observaciones',$obs);
  $flagSuccess=$stmt->execute();	
 }	
}
showAlertSuccessError($flagSuccess,"../".$urlListMes."&codigo=".$codBono);
	
}else{
  
  $mesActual=date("m");
  if($desde<(int)$mesActual){
    for ($i=$desde; $i <= (int)$mesActual; $i++) { 
      if((int)$mesActual==$i){
// Prepare
  $codMes=$i;
  $stmt = $dbh->prepare("INSERT INTO bonos_personal_mes (cod_bono, cod_personal,cod_gestion,cod_mes,monto,observaciones,indefinido, cod_estadoreferencial) 
                        VALUES (:cod_bono,:codPersona,:codGestion,:codMes,:monto,:observaciones,:indefinido, :cod_estado)");
$indefinido=1;
// Bind
  $stmt->bindParam(':monto', $monto);
  $stmt->bindParam(':cod_estado', $codEstado);
  $stmt->bindParam(':cod_bono',$codBono);
  $stmt->bindParam(':codPersona',$codPersona);
  $stmt->bindParam(':codGestion',$codGestion);
  $stmt->bindParam(':codMes',$codMes);
  $stmt->bindParam(':observaciones',$obs);
  $stmt->bindParam(':indefinido',$indefinido);
  $flagSuccess=$stmt->execute();  

      }else{
        // Prepare
  $codMes=$i;
  $stmt = $dbh->prepare("INSERT INTO bonos_personal_mes (cod_bono, cod_personal,cod_gestion,cod_mes,monto,observaciones,indefinido, cod_estadoreferencial) 
                        VALUES (:cod_bono,:codPersona,:codGestion,:codMes,:monto,:observaciones,:indefinido, :cod_estado)");
$indefinido=0;
// Bind
  $stmt->bindParam(':monto', $monto);
  $stmt->bindParam(':cod_estado', $codEstado);
  $stmt->bindParam(':cod_bono',$codBono);
  $stmt->bindParam(':codPersona',$codPersona);
  $stmt->bindParam(':codGestion',$codGestion);
  $stmt->bindParam(':codMes',$codMes);
  $stmt->bindParam(':observaciones',$obs);
  $stmt->bindParam(':indefinido',$indefinido);
  $flagSuccess=$stmt->execute();  
      }
 }
    
  }else{
    $codMes=$desde;
  $stmt = $dbh->prepare("INSERT INTO bonos_personal_mes (cod_bono, cod_personal,cod_gestion,cod_mes,monto,observaciones,indefinido, cod_estadoreferencial) 
                        VALUES (:cod_bono,:codPersona,:codGestion,:codMes,:monto,:observaciones,:indefinido, :cod_estado)");
$indefinido=1;
// Bind
  $stmt->bindParam(':monto', $monto);
  $stmt->bindParam(':cod_estado', $codEstado);
  $stmt->bindParam(':cod_bono',$codBono);
  $stmt->bindParam(':codPersona',$codPersona);
  $stmt->bindParam(':codGestion',$codGestion);
  $stmt->bindParam(':codMes',$codMes);
  $stmt->bindParam(':observaciones',$obs);
   $stmt->bindParam(':indefinido',$indefinido);
  $flagSuccess=$stmt->execute(); 
  }

showAlertSuccessError($flagSuccess,"../".$urlListMes."&codigo=".$codBono);
}//if hasta !=0

?>
