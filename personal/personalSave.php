<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'rrhh/configModule.php';
ini_set('display_errors',1);

$dbh = new Conexion();
$dbhS = new Conexion();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {
    // $codigo = $_POST["codigo"];
    
    $cod_cargo = $_POST["cod_cargo"];
    $cod_unidadorganizacional = $_POST["cod_unidadorganizacional"];
    $cod_area = $_POST["cod_area"];
    $jubilado = $_POST["jubilado"];
    
    $cod_tipopersonal = $_POST["cod_tipopersonal"];
    $haber_basico = $_POST["haber_basico"];
    
    $apellido_casada = $_POST["apellido_casada"];
    
    $otros_nombres = $_POST["otros_nombres"];
    $nua_cua_asignado = $_POST["nua_cua_asignado"];    
    
    $cod_tipoafp = $_POST["cod_tipoafp"];
    $cod_tipoaporteafp = $_POST["cod_tipoaporteafp"];
    
    $nro_seguro = $_POST["nro_seguro"];
    $cod_estadopersonal = $_POST["cod_estadopersonal"];
    
    $persona_contacto = $_POST["persona_contacto"];

    $discapacitado=$_POST['cod_discapacidad'];
    $tutordiscapacidad=$_POST['cod_tutordiscapacidad'];
    $parentescotutor=$_POST['parentescotutor'];
    $celularTutor=$_POST['celularTutor'];
    $grado_academico=$_POST['grado_academico'];
    $ing_contr=$_POST['ing_contr'];
    $ing_planilla=$_POST['ing_planilla'];
    
    //$created_at = $_POST["created_at"];
    $created_by = 1;//$_POST["created_by"];
    //$modified_at = $_POST["modified_at"];
    $modified_by = 1;//$_POST["modified_by"];
    $cod_estadoreferencial=1;
    $porcentaje=100;

        $codigo = $_POST["codigo"];                
        $stmt = $dbh->prepare("UPDATE personal set cod_cargo=:cod_cargo,cod_unidadorganizacional=:cod_unidadorganizacional,cod_area=:cod_area,jubilado=:jubilado,
        cod_tipopersonal=:cod_tipopersonal,haber_basico=:haber_basico,apellido_casada=:apellido_casada,otros_nombres=:otros_nombres,
        nua_cua_asignado=:nua_cua_asignado,ing_contr=:ing_contr,ing_planilla=:ing_planilla,
        cod_tipoafp=:cod_tipoafp,nro_seguro=:nro_seguro,cod_grado_academico=:grado_academico,
        cod_estadopersonal=:cod_estadopersonal,persona_contacto=:persona_contacto,cod_tipoaporteafp = :cod_tipoaporteafp  
        where codigo = :codigo");
        //bind
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':cod_cargo', $cod_cargo);
        $stmt->bindParam(':cod_unidadorganizacional', $cod_unidadorganizacional);
        $stmt->bindParam(':cod_area', $cod_area);
        $stmt->bindParam(':jubilado', $jubilado);        
        $stmt->bindParam(':cod_tipopersonal', $cod_tipopersonal);
        $stmt->bindParam(':haber_basico', $haber_basico);        
        $stmt->bindParam(':apellido_casada', $apellido_casada);
        $stmt->bindParam(':otros_nombres', $otros_nombres);
        $stmt->bindParam(':nua_cua_asignado', $nua_cua_asignado);            
        $stmt->bindParam(':cod_tipoafp', $cod_tipoafp);
        $stmt->bindParam(':cod_tipoaporteafp', $cod_tipoaporteafp);        
        $stmt->bindParam(':nro_seguro', $nro_seguro);
        $stmt->bindParam(':cod_estadopersonal', $cod_estadopersonal);        
        $stmt->bindParam(':persona_contacto', $persona_contacto);
        $stmt->bindParam(':grado_academico', $grado_academico);

        $stmt->bindParam(':ing_contr', $ing_contr);
        $stmt->bindParam(':ing_planilla', $ing_planilla);
        $flagSuccess=$stmt->execute();

        //para area distribucion
        $stmtPer = $dbhS->prepare("SELECT codigo,cod_area 
                from personal_area_distribucion 
                where cod_personal=:cod_personal ORDER BY 1 DESC");
        $stmtPer->bindParam(':cod_personal', $codigo);
        $stmtPer->execute();
        $stmtPer->bindColumn('codigo', $codigo_areaDP);
        $stmtPer->bindColumn('cod_area', $codigo_areaP);
        while ($row = $stmtPer->fetch(PDO::FETCH_BOUND)) {

            $flagSuccess=$stmt->execute();

            //para area distribucion
            $stmtPer = $dbhS->prepare("SELECT codigo,cod_area 
                    from personal_area_distribucion 
                    where cod_personal=:cod_personal ORDER BY 1 DESC");
            $stmtPer->bindParam(':cod_personal', $codigo);
            $stmtPer->execute();
            $stmtPer->bindColumn('codigo', $codigo_areaDP);
            $stmtPer->bindColumn('cod_area', $codigo_areaP);
            while ($row = $stmtPer->fetch(PDO::FETCH_BOUND)) {
            }
            if($codigo_areaP==0){//actualizamos los datos de area distribuion si el cod_area es 0
                $stmtDistribucion = $dbh->prepare("UPDATE personal_area_distribucion 
                    set cod_area=:cod_area,porcentaje=:porcentaje where codigo=:codigo_areaDP");
                //Bind
                $stmtDistribucion->bindParam(':codigo_areaDP', $codigo_areaDP);
                $stmtDistribucion->bindParam(':cod_area', $cod_area);    
                $stmtDistribucion->bindParam(':porcentaje', $porcentaje);            
                $stmtDistribucion->execute();     
            }
            //actualizamos la parte de personal discapacitado        
            $stmtDiscapacitado = $dbh->prepare("UPDATE personal_discapacitado set discapacitado = :discapacitado,
                tutor_discapacitado=:tutordiscapacidad,parentesco=:parentescotutor,celular_tutor=:celularTutor
            where codigo = :codigo");
            //bind
            $stmtDiscapacitado->bindParam(':codigo', $codigo);
            $stmtDiscapacitado->bindParam(':discapacitado', $discapacitado);
            $stmtDiscapacitado->bindParam(':tutordiscapacidad', $tutordiscapacidad);
            $stmtDiscapacitado->bindParam(':parentescotutor', $parentescotutor);
            $stmtDiscapacitado->bindParam(':celularTutor', $celularTutor);

            
            $flagSuccess=$stmtDiscapacitado->execute();
            
            //parte de imagen
            //imagen anterior
            $stmtANT = $dbh->prepare("SELECT * FROM personalimagen where codigo =:codigo");
            $stmtANT->bindParam(':codigo',$codigo);
            $stmtANT->execute();
            $resultANT = $stmtANT->fetch();
            $imagenANT = $resultANT['imagen'];

            // echo "ver: ".$_FILES['image']['name'];
            // echo "ver: ".$imagenANT;

            // echo "ver2: ".strlen($_FILES['image']['name']);

            if ($imagenANT != $_FILES['image']['name'] AND strlen($_FILES['image']['name']) > 1){//solo si es diferente actualizar            
                
                $results = $dbh->query("SELECT * from personalimagen where codigo = ".$codigo)->fetchAll(PDO::FETCH_ASSOC);
                
                if(count($results)) 
                {                

                    $stmt3 = $dbh->prepare("UPDATE personalimagen set imagen = :imagen where codigo = :codigo");
                }else {
                    $stmt3 = $dbh->prepare("INSERT into personalimagen (codigo, imagen) values (:codigo, :imagen)");
                }
                            
                $stmt3->bindParam(':codigo', $codigo);
                $stmt3->bindParam(':imagen', $_FILES['image']['name']);//la url esta poniendo        
                $archivo = __DIR__.DIRECTORY_SEPARATOR."imagenes".DIRECTORY_SEPARATOR.$_FILES['image']['name'];
                //esta guardando en activosfijos\imagenes
                $stmt3->execute();

                //echo $archivo;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $archivo))
                    echo "correcto";
                else
                    echo "error".$_FILES["image"]["error"];//sale error 0
            }

            showAlertSuccessError($flagSuccess,$urlListPersonal);

        }//si es insert o update
    
} catch(PDOException $ex){
    //manejar error
    echo "Un error ocurrio".$ex->getMessage();
}
?>