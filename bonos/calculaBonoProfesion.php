<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codBono=$codigo_bono;
$codMes=$codigo_mes;
$codGestionActiva=$_SESSION['globalGestion'];

$flagSuccess=false;
// Preparamos
$stmt = $dbh->prepare("select bpm.cod_personal as cod_persona, bpm.codigo as codigo ,
(select pga.codigo from personal p, personal_grado_academico pga where p.cod_grado_academico=pga.codigo and p.codigo=bpm.cod_personal)as grado_academico,
bpm.monto as detalle from bonos_personal_mes bpm where bpm.cod_bono=$codBono and bpm.cod_mes=$codMes and bpm.cod_gestion=$codGestionActiva and bpm.cod_estadoreferencial=1");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('cod_persona', $codPersona);
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('grado_academico', $codGradoAcademico);
$stmt->bindColumn('detalle', $detalle);
while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    $codigoX= $codigo;
    $codPersonaX=$codPersona;
    $codGradoAcademicoX=$codGradoAcademico;

    if($codGradoAcademicoX!=null){
    $montoGrado=calculaBonoProfesion($codGradoAcademicoX);
    $sql = $dbh->prepare("UPDATE bonos_personal_mes set monto=$montoGrado where codigo=$codigoX and cod_bono=$codBono
    and cod_personal=$codPersonaX and cod_gestion=$codGestionActiva and cod_mes=$codMes ");

    $flagSuccess=$sql->execute();
    }else{
      $sql = $dbh->prepare("UPDATE bonos_personal_mes set monto=0 where codigo=$codigoX and cod_bono=$codBono
    and cod_personal=$codPersonaX and cod_gestion=$codGestionActiva and cod_mes=$codMes ");

    $flagSuccess=$sql->execute();
      
    }


  }

  showAlertSuccessError($flagSuccess,$urlListMesPersona."&cod_bono=".$codBono."&cod_mes=".$codMes);

?>