<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'rrhh/configModule.php';
ini_set('display_errors',1);

$dbh = new Conexion();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {
    $codigo_item = $_POST["codigo_item"];
    $created_by=1;
    $modified_by=1;

    if($codigo_item==1){//uo_area
        $codigo_personal = $_POST["codigo_personal"];
        $cod_uo = $_POST["cod_uo"];
        $cod_area = $_POST["cod_area"];
        $fecha_cambio=$_POST["fecha_cambio"];

        $stmt = $dbh->prepare("UPDATE personal set cod_unidadorganizacional=:cod_uo,cod_area=:cod_area where codigo=:codigo");
        $stmt->bindParam(':codigo', $codigo_personal);
        $stmt->bindParam(':cod_uo', $cod_uo);
        $stmt->bindParam(':cod_area', $cod_area);    
        $flagSuccess=$stmt->execute();
        //para el historico
        $sql="INSERT into historico_uo_area(cod_personal,cod_uo,cod_area,fecha_cambio,created_by,modified_by)
        values(:cod_personal,:cod_uo,:cod_area,:fecha_cambio,:created_by,:modified_by)";
        $stmtInsert = $dbh->prepare($sql);
        $stmtInsert->bindParam(':cod_personal', $codigo_personal);
        $stmtInsert->bindParam(':cod_uo',$cod_uo);
        $stmtInsert->bindParam(':cod_area',$cod_area);
        $stmtInsert->bindParam(':fecha_cambio',$fecha_cambio);
        $stmtInsert->bindParam(':created_by',$created_by);
        $stmtInsert->bindParam(':modified_by',$modified_by);
        $flagSuccess=$stmtInsert->execute();       
        showAlertSuccessError($flagSuccess,$urlListPersonal);
    }elseif($codigo_item==2){//cargo
        $codigo_personal = $_POST["codigo_personal"];
        $cod_cargo = $_POST["cod_cargo"]; 
        $fecha_cambio=$_POST["fecha_cambio"];

        $stmt = $dbh->prepare("UPDATE personal set cod_cargo=:cod_cargo where codigo = :codigo");
        $stmt->bindParam(':codigo', $codigo_personal);
        $stmt->bindParam(':cod_cargo', $cod_cargo);
        $flagSuccess=$stmt->execute();   
        //para el historico
        $sql="INSERT into historico_cargos(cod_personal,cod_cargo,fecha_cambio,created_by,modified_by)
        values(:cod_personal,:cod_cargo,:fecha_cambio,:created_by,:modified_by)";
        $stmtInsert = $dbh->prepare($sql);
        $stmtInsert->bindParam(':cod_personal', $codigo_personal);
        $stmtInsert->bindParam(':cod_cargo',$cod_cargo);
        $stmtInsert->bindParam(':fecha_cambio',$fecha_cambio);
        $stmtInsert->bindParam(':created_by',$created_by);
        $stmtInsert->bindParam(':modified_by',$modified_by);
        $flagSuccess=$stmtInsert->execute();
        showAlertSuccessError($flagSuccess,$urlListPersonal);
    }elseif($codigo_item==3){//grado acad
        $codigo_personal = $_POST["codigo_personal"];
        $cod_grado_academico = $_POST["grado_academico"];  
        $fecha_cambio=$_POST["fecha_cambio"];      
        $stmt = $dbh->prepare("UPDATE personal set cod_grado_academico=:grado_academico where codigo = :codigo");
        $stmt->bindParam(':codigo', $codigo_personal);
        $stmt->bindParam(':grado_academico', $grado_academico);
        $flagSuccess=$stmt->execute();
        //para el historico
        $sql="INSERT into historico_grado_acad(cod_personal,cod_grado_academico,fecha_cambio,created_by,modified_by)
        values(:cod_personal,:cod_grado_academico,:fecha_cambio,:created_by,:modified_by)";
        $stmtInsert = $dbh->prepare($sql);
        $stmtInsert->bindParam(':cod_personal', $codigo_personal);
        $stmtInsert->bindParam(':cod_grado_academico',$cod_grado_academico);
        $stmtInsert->bindParam(':fecha_cambio',$fecha_cambio);
        $stmtInsert->bindParam(':created_by',$created_by);
        $stmtInsert->bindParam(':modified_by',$modified_by);
        $flagSuccess=$stmtInsert->execute();    
        showAlertSuccessError($flagSuccess,$urlListPersonal);
    }elseif($codigo_item==4){
        $codigo_personal = $_POST["codigo_personal"];
        $haber_basico = $_POST["haber_basico"];
        $fecha_cambio=$_POST["fecha_cambio"];     
        $stmt = $dbh->prepare("UPDATE personal set haber_basico=:haber_basico where codigo = :codigo");
        $stmt->bindParam(':codigo', $codigo_personal);
        $stmt->bindParam(':haber_basico', $haber_basico);
        $flagSuccess=$stmt->execute();     
        //para el historico
        $sql="INSERT into historico_haber_basico(cod_personal,haber_basico,fecha_cambio,created_by,modified_by)
        values(:cod_personal,:haber_basico,:fecha_cambio,:created_by,:modified_by)";
        $stmtInsert = $dbh->prepare($sql);
        $stmtInsert->bindParam(':cod_personal', $codigo_personal);
        $stmtInsert->bindParam(':haber_basico',$haber_basico);
        $stmtInsert->bindParam(':fecha_cambio',$fecha_cambio);
        $stmtInsert->bindParam(':created_by',$created_by);
        $stmtInsert->bindParam(':modified_by',$modified_by);
        $flagSuccess=$stmtInsert->execute();       
        showAlertSuccessError($flagSuccess,$urlListPersonal);
    }
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>