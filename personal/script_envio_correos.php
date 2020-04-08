<?php 
require_once '../conexion.php';
require_once '../functions.php';

require_once '../notificaciones_sistema/PHPMailer/send.php';
$dbh = new Conexion();
//envio de correo automatico para indicar el vencimiento de contratos del personal
// $fecha_actual=date('Y-m-d'); //cambiar por este
$fecha_actual="2020-09-27";

//lista de contrado con evaluacion a la fecha o 5 dias antes
$sqlContratos="SELECT ep.codigo as cod_evento,pc.codigo,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=pc.cod_personal) as personal,pc.fecha_fincontrato,pc.fecha_evaluacioncontrato from eventos_contratospersonal ep, personal_contratos pc where ep.cod_personalcontrato=pc.codigo and pc.cod_estadoreferencial=1 and pc.cod_estadocontrato=1 and pc.alerta_enviada=0 and pc.fecha_evaluacioncontrato BETWEEN DATE_SUB('$fecha_actual',INTERVAL 5 DAY) and '$fecha_actual'";
$stmtContratosFecha = $dbh->prepare($sqlContratos);
$stmtContratosFecha->execute();
$stmtContratosFecha->bindColumn('cod_evento', $cod_evento);
$stmtContratosFecha->bindColumn('codigo', $codigo_C);
$stmtContratosFecha->bindColumn('personal', $personal);
$stmtContratosFecha->bindColumn('fecha_fincontrato', $fecha_fincontrato);
$stmtContratosFecha->bindColumn('fecha_evaluacioncontrato', $fecha_evaluacioncontrato);
$cont=0;
$MessgAdjunto="";
$arrayCodContrato=array();
$arrayEvento=array();
while ($rowContratos = $stmtContratosFecha->fetch(PDO::FETCH_BOUND)) { 
  $MessgAdjunto.=$personal.", F. Evaluación: ".$fecha_evaluacioncontrato.", F. Fin Contrato: ".$fecha_fincontrato.".<br>\n";
  array_push($arrayCodContrato,$codigo_C);
  array_push($arrayEvento,$cod_evento);
  $cont++;  
}
// var_dump($arrayCodContrato);
$stringCodCotrato=implode(",", $arrayCodContrato);
$stringEventos=implode(",", $arrayEvento);
if($cont>0){
  //enviando correos a responasbles
  $sqlX="SELECT cod_responsable from eventos_contratospersonal_detalle where codigo in ($stringEventos)";
  // echo $sqlX;
  $stmtResponsables = $dbh->prepare("SELECT cod_responsable from eventos_contratospersonal_detalle where cod_evento_contratopersonal in ($stringEventos)");
  $stmtResponsables->execute();
  $stmtResponsables->bindColumn('cod_responsable', $cod_responsable);
  while ($rowRespo = $stmtResponsables->fetch(PDO::FETCH_BOUND)) { 
    $stmtEnvioCorreo = $dbh->prepare("SELECT email,CONCAT_WS(' ',primer_nombre,paterno) as nombre_encargado from personal where codigo=$cod_responsable");
    $stmtEnvioCorreo->execute();
    $resultEC = $stmtEnvioCorreo->fetch();
    $nombre_encargado = $resultEC['nombre_encargado'];
    // $email_respo = $resultEC['email'];  
    $email_respo="bsullcamani@gmail.com";//correo de prueba

    //preparamos para el envio de correo
    $texto_cuerpo="Estimad@ ".$nombre_encargado.",<br>\n<br>\n queremos recordarle que el contrato del personal que se encuentra en la siguente lista, finalizará en la fecha adjunta:<br>\n<br>\n".$MessgAdjunto."<br>Saludos.";
    $asunto="FIN CONTRATO PERSONAL ".date('Y-m-d');
    $mail_username="noresponse@minkasoftware.com";//Correo electronico emisor
    $mail_userpassword="minka@2019";// contraseña correo emisor
    $mail_addAddress=$email_respo;//correo electronico destino
    $template="../notificaciones_sistema/PHPMailer/email_template.html";//Ruta de la plantilla HTML para enviar nuestro mensaje
    /*Inicio captura de datos enviados por $_POST para enviar el correo */
    $mail_setFromEmail=$mail_username;
    $mail_setFromName="IBNORCA";
    $txt_message=$texto_cuerpo;
    $mail_subject=$asunto; //el subject del mensaje

    $flag=sendemail($mail_username,$mail_userpassword,$mail_setFromEmail,$mail_setFromName,$mail_addAddress,$txt_message,$mail_subject,$template,0);
    if($flag!=0){//se envio correctamente
      print "CORREO ENVIADO";
      $sqlContratos="UPDATE personal_contratos set alerta_enviada='1' where codigo in($stringCodCotrato)";
      $stmtUpdate = $dbh->prepare($sqlContratos);
      $stmtUpdate->execute();
    }else{
      print "ERROR AL ENVIAR CORREO";
    }
  }
  
}else{
  print "SIN EVENTOS";
}


?>