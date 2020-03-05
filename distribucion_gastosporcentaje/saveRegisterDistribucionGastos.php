<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_POST['codigo'];
$nombre_d=$_POST['nombre_d'];

$estado=0;
$cod_estadoreferencial=1;
if($codigo==0){
  if($total==100){
    
    $porcentaje=$_POST['porcentaje'];
    $total=$_POST['total'];
    $cod_unidad=$_POST['cod_unidad'];

    $stmt = $dbh->prepare("INSERT into  distribucion_gastosporcentaje(estado,nombre,cod_estadoreferencial) values('$estado','$nombre_d','$cod_estadoreferencial') ");
    $flagSuccess=$stmt->execute();
    foreach( $cod_unidad as $key => $n ) {
     //echo "El Id es ".$n.", detalle es ".$detalle[$key].", cod_descuento es ".$codDescPerMes[$key];
      //echo "n:".$n."- porcentaje".$porcentaje[$key]."<br>";
      if($flagSuccess){
        $stmtSelect = $dbh->prepare("SELECT codigo from distribucion_gastosporcentaje where cod_estadoreferencial=1 order by codigo desc LIMIT 1 ");
        $stmtSelect->execute();
        $result=$stmtSelect->fetch();
        $codigo_distGasto=$result['codigo'];


        $stmt = $dbh->prepare("INSERT into  distribucion_gastosporcentaje_detalle(cod_distribucion_gastos,cod_unidadorganizacional,porcentaje) values('$codigo_distGasto','$n','$porcentaje[$key]')");
        $flagSuccess=$stmt->execute();
      }

     }
  }
}
else{
   $stmt = $dbh->prepare("UPDATE distribucion_gastosporcentaje set nombre='$nombre_d'
   where codigo=$codigo");
    $flagSuccess=$stmt->execute();
}

showAlertSuccessError($flagSuccess,"../".$urlList);

?>
