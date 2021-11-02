<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

ini_set('display_errors',1);

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

echo "<br><br><br><br>";
try {
    $cod_responable1=$_POST["cod_responable1"];    
    $nuevo_cod_responable1=$_POST["nuevo_cod_responable1"];        
    $query = "SELECT codigo,estadobien,cod_unidadorganizacional,cod_area from activosfijos where cod_responsables_responsable=$cod_responable1  and cod_estadoactivofijo=1 order by codigoactivo";
    // echo $query;
    $stmt = $dbh->query($query);
    $cod_ubicaciones="";
    $codEstadoAsignacionAF="1";
    $flagSuccess=false;
    while ($row = $stmt->fetch()){ 
        $codigo_af=$row["codigo"];
        $estadobien=$row["estadobien"];
        $cod_unidadorganizacional=$row["cod_unidadorganizacional"];
        $cod_area=$row["cod_area"];
        //actualizamos personal        
        $sql="INSERT INTO activofijos_asignaciones(cod_activosfijos,fechaasignacion,
        cod_ubicaciones,cod_personal, estadobien_asig, cod_unidadorganizacional, cod_area, cod_estadoasignacionaf)
        values (:cod_activosfijos, now(),
        :cod_ubicaciones, :cod_personal, :estadobien_asig, :cod_unidadorganizacional, :cod_area, :cod_estadoasignacionaf)";
        $stmt2 = $dbh->prepare($sql);
        
        $stmt2->bindParam(':cod_activosfijos', $codigo_af);
        //$stmt2->bindParam(':fechaasignacion', $fechalta);
        $stmt2->bindParam(':cod_ubicaciones', $cod_ubicaciones);
        $stmt2->bindParam(':cod_personal', $nuevo_cod_responable1);            
        $stmt2->bindParam(':estadobien_asig', $estadobien);
        $stmt2->bindParam(':cod_unidadorganizacional', $cod_unidadorganizacional);
        $stmt2->bindParam(':cod_area', $cod_area);
        $stmt2->bindParam(':cod_estadoasignacionaf', $codEstadoAsignacionAF);            
        $flagSuccess=$stmt2->execute();
    }
    showAlertSuccessError($flagSuccess,$urlcambiar_respo);
} catch(PDOException $ex){
    //manejar error
    echo "Un error ocurrio".$ex->getMessage();
}
?>