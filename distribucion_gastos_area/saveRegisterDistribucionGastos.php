<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_POST['codigo'];
$nombre_d=$_POST['nombre_d'];
$unidadX=$_POST['unidad'];

$estado=0;
$cod_estadoreferencial=1;
$total=0;

if($codigo==0){    
    $porcentaje=$_POST['porcentaje'];
    $total=$_POST['total'];
    $cod_area=$_POST['cod_area'];

    $sqlDist="INSERT INTO distribucion_gastosarea(estado,nombre,cod_estadoreferencial,cod_uo) values('$estado','$nombre_d','$cod_estadoreferencial','$unidadX')";

    echo $sqlDist;

    $stmt = $dbh->prepare($sqlDist);
    $flagSuccess=$stmt->execute();
    foreach( $cod_area as $key => $n ) {
     //echo "El Id es ".$n.", detalle es ".$detalle[$key].", cod_descuento es ".$codDescPerMes[$key];
      //echo "n:".$n."- porcentaje".$porcentaje[$key]."<br>";
      if($flagSuccess){
        $stmtSelect = $dbh->prepare("SELECT codigo from distribucion_gastosarea order by codigo desc LIMIT 1");
        $stmtSelect->execute();
        $result=$stmtSelect->fetch();
        $codigo_distGasto=$result['codigo'];

        $stmt = $dbh->prepare("INSERT into  distribucion_gastosarea_detalle(cod_distribucionarea,cod_area,porcentaje) values('$codigo_distGasto','$n','$porcentaje[$key]')");
        $flagSuccess=$stmt->execute();
      }
    }
}
else{
   $stmt = $dbh->prepare("UPDATE distribucion_gastosarea set nombre='$nombre_d', cod_uo='$unidadX' where codigo=$codigo");
    $flagSuccess=$stmt->execute();
}

showAlertSuccessError($flagSuccess,"../".$urlList);

?>
