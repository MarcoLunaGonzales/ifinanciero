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

        $stmtuo = $dbh->prepare("SELECT nombre from unidades_organizacionales where codigo = $cod_uo");        
        $stmtuo->execute();
        $resultuo=$stmtuo->fetch();
        $nombre_uo=$resultuo['nombre'];
        $stmtarea = $dbh->prepare("SELECT nombre from areas where codigo = $cod_area");        
        $stmtarea->execute();
        $resultarea=$stmtarea->fetch();
        $nombre_area=$resultarea['nombre'];
    
        $descripcion=$nombre_uo."/".$nombre_area;
        $tipo="Oficina/Area";

        $stmt = $dbh->prepare("UPDATE personal set cod_unidadorganizacional=:cod_uo,cod_area=:cod_area where codigo=:codigo");
        $stmt->bindParam(':codigo', $codigo_personal);
        $stmt->bindParam(':cod_uo', $cod_uo);
        $stmt->bindParam(':cod_area', $cod_area);    
        $flagSuccess=$stmt->execute();
        //para el historico
        $sql="INSERT into historico_cambios_personal(cod_personal,tipo,descripcion,fecha_cambio,created_by,modified_by)
        values(:cod_personal,:tipo,:descripcion,:fecha_cambio,:created_by,:modified_by)";
        $stmtInsert = $dbh->prepare($sql);
        $stmtInsert->bindParam(':cod_personal', $codigo_personal);
        $stmtInsert->bindParam(':tipo',$tipo);
        $stmtInsert->bindParam(':descripcion',$descripcion);
        $stmtInsert->bindParam(':fecha_cambio',$fecha_cambio);
        $stmtInsert->bindParam(':created_by',$created_by);
        $stmtInsert->bindParam(':modified_by',$modified_by);
        $flagSuccess=$stmtInsert->execute();       
        showAlertSuccessError($flagSuccess,$urlListPersonal);
    }elseif($codigo_item==2){//cargo
        $codigo_personal = $_POST["codigo_personal"];
        $cod_cargo = $_POST["cod_cargo"]; 
        $fecha_cambio=$_POST["fecha_cambio"];

        $stmt = $dbh->prepare("SELECT nombre from cargos where codigo = $cod_cargo");        
        $stmt->execute();
        $result=$stmt->fetch();
        $nombre=$result['nombre'];
        
        $descripcion=$nombre;
        $tipo="Cargo";

        $stmt = $dbh->prepare("UPDATE personal set cod_cargo=:cod_cargo where codigo = :codigo");
        $stmt->bindParam(':codigo', $codigo_personal);
        $stmt->bindParam(':cod_cargo', $cod_cargo);
        $flagSuccess=$stmt->execute();   
        //para el historico
        $sql="INSERT into historico_cambios_personal(cod_personal,tipo,descripcion,fecha_cambio,created_by,modified_by)
        values(:cod_personal,:tipo,:descripcion,:fecha_cambio,:created_by,:modified_by)";
        $stmtInsert = $dbh->prepare($sql);
        $stmtInsert->bindParam(':cod_personal', $codigo_personal);
        $stmtInsert->bindParam(':tipo',$tipo);
        $stmtInsert->bindParam(':descripcion',$descripcion);
        $stmtInsert->bindParam(':fecha_cambio',$fecha_cambio);
        $stmtInsert->bindParam(':created_by',$created_by);
        $stmtInsert->bindParam(':modified_by',$modified_by);
        $flagSuccess=$stmtInsert->execute();
        showAlertSuccessError($flagSuccess,$urlListPersonal);
    }elseif($codigo_item==3){//grado acad
        $codigo_personal = $_POST["codigo_personal"];
        $cod_grado_academico = $_POST["grado_academico"];  
        $fecha_cambio=$_POST["fecha_cambio"];   

        $stmt = $dbh->prepare("SELECT nombre from personal_grado_academico where codigo = $cod_grado_academico");        
        $stmt->execute();
        $result=$stmt->fetch();
        $nombre=$result['nombre'];
        $descripcion=$nombre;
        $tipo="Grado Académico";

        $stmt = $dbh->prepare("UPDATE personal set cod_grado_academico=:grado_academico where codigo = :codigo");
        $stmt->bindParam(':codigo', $codigo_personal);
        $stmt->bindParam(':grado_academico', $cod_grado_academico);
        $flagSuccess=$stmt->execute();
        //para el historico
        $sql="INSERT into historico_cambios_personal(cod_personal,tipo,descripcion,fecha_cambio,created_by,modified_by)
        values(:cod_personal,:tipo,:descripcion,:fecha_cambio,:created_by,:modified_by)";
        $stmtInsert = $dbh->prepare($sql);
        $stmtInsert->bindParam(':cod_personal', $codigo_personal);
        $stmtInsert->bindParam(':tipo',$tipo);
        $stmtInsert->bindParam(':descripcion',$descripcion);
        $stmtInsert->bindParam(':fecha_cambio',$fecha_cambio);
        $stmtInsert->bindParam(':created_by',$created_by);
        $stmtInsert->bindParam(':modified_by',$modified_by);
        $flagSuccess=$stmtInsert->execute();    
        showAlertSuccessError($flagSuccess,$urlListPersonal);
    }elseif($codigo_item==4){//haber basico
        $codigo_personal = $_POST["codigo_personal"];
        $haber_basico = $_POST["haber_basico"];
        $fecha_cambio=$_POST["fecha_cambio"];  

        $descripcion=$haber_basico;
        $tipo="Haber Básico";

        $stmt = $dbh->prepare("UPDATE personal set haber_basico=:haber_basico where codigo = :codigo");
        $stmt->bindParam(':codigo', $codigo_personal);
        $stmt->bindParam(':haber_basico', $haber_basico);
        $flagSuccess=$stmt->execute();     
        //para el historico
        $sql="INSERT into historico_cambios_personal(cod_personal,tipo,descripcion,fecha_cambio,created_by,modified_by)
        values(:cod_personal,:tipo,:descripcion,:fecha_cambio,:created_by,:modified_by)";
        $stmtInsert = $dbh->prepare($sql);
        $stmtInsert->bindParam(':cod_personal', $codigo_personal);
        $stmtInsert->bindParam(':tipo',$tipo);
        $stmtInsert->bindParam(':descripcion',$descripcion);
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