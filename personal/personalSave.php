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
    $ci = $_POST["ci"];
    $ci_aux=$ci;
    $ci_lugar_emision = $_POST["ci_lugar_emision"];
    $fecha_nacimiento = $_POST["fecha_nacimiento"];
    $cod_cargo = $_POST["cod_cargo"];
    $cod_unidadorganizacional = $_POST["cod_unidadorganizacional"];
    $cod_area = $_POST["cod_area"];
    $jubilado = $_POST["jubilado"];
    $cod_genero = $_POST["cod_genero"];
    $cod_tipopersonal = $_POST["cod_tipopersonal"];
    $haber_basico = $_POST["haber_basico"];
    $paterno = $_POST["paterno"];
    $materno = $_POST["materno"];
    $apellido_casada = $_POST["apellido_casada"];
    $primer_nombre = $_POST["primer_nombre"];
    $otros_nombres = $_POST["otros_nombres"];
    $nua_cua_asignado = $_POST["nua_cua_asignado"];
    $direccion = $_POST["direccion"];
    
    $cod_tipoafp = $_POST["cod_tipoafp"];
    $cod_tipoaporteafp = $_POST["cod_tipoaporteafp"];
    
    $nro_seguro = $_POST["nro_seguro"];
    $cod_estadopersonal = $_POST["cod_estadopersonal"];
    $telefono = $_POST["telefono"];
    $celular = $_POST["celular"];
    $email = $_POST["email"];
    $persona_contacto = $_POST["persona_contacto"];

    $discapacitado=$_POST['cod_discapacidad'];
    $tutordiscapacidad=$_POST['cod_tutordiscapacidad'];
    $parentescotutor=$_POST['parentescotutor'];
    $celularTutor=$_POST['celularTutor'];
    $grado_academico=$_POST['grado_academico'];
    
    //$created_at = $_POST["created_at"];
    $created_by = 1;//$_POST["created_by"];
    //$modified_at = $_POST["modified_at"];
    $modified_by = 1;//$_POST["modified_by"];
    $cod_estadoreferencial=1;
    $porcentaje=100;
    if ($_POST["codigo"] == 0){
        //buscamos el ultimo registro de personal
        $stmtPerAux = $dbhS->prepare("SELECT codigo from personal");
        $stmtPerAux->execute();
        $stmtPerAux->bindColumn('codigo', $codigoPAux);
        $codigoPAux=$codigoPAux+1;
        while ($row = $stmtPerAux->fetch(PDO::FETCH_BOUND)) {
        }

        $stmt = $dbh->prepare("INSERT INTO personal(ci,ci_lugar_emision,fecha_nacimiento,cod_cargo,cod_unidadorganizacional,cod_area,
        jubilado,cod_genero,cod_tipopersonal,haber_basico,paterno,materno,apellido_casada,primer_nombre,otros_nombres,nua_cua_asignado,
        direccion,cod_tipoafp,nro_seguro,cod_estadopersonal,created_by,modified_by,telefono,celular,email,persona_contacto, cod_tipoaporteafp,cod_estadoreferencial) 
        values (:ci, :ci_lugar_emision, :fecha_nacimiento, 
        :cod_cargo, :cod_unidadorganizacional, :cod_area, :jubilado, :cod_genero, :cod_tipopersonal, :haber_basico, :paterno, 
        :materno, :apellido_casada, :primer_nombre, :otros_nombres, :nua_cua_asignado, :direccion, :cod_tipoafp, :nro_seguro, 
        :cod_estadopersonal, :created_by, :modified_by, :telefono, :celular, :email, :persona_contacto, :cod_tipoaporteafp,:cod_estadoreferencial)");
        //Bind
        $stmt->bindParam(':codigoPAux', $codigoPAux);
        $stmt->bindParam(':ci', $ci);
        $stmt->bindParam(':ci_lugar_emision', $ci_lugar_emision);
        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmt->bindParam(':cod_cargo', $cod_cargo);
        $stmt->bindParam(':cod_unidadorganizacional', $cod_unidadorganizacional);
        $stmt->bindParam(':cod_area', $cod_area);
        $stmt->bindParam(':jubilado', $jubilado);
        $stmt->bindParam(':cod_genero', $cod_genero);
        $stmt->bindParam(':cod_tipopersonal', $cod_tipopersonal);
        $stmt->bindParam(':haber_basico', $haber_basico);
        $stmt->bindParam(':paterno', $paterno);
        $stmt->bindParam(':materno', $materno);
        $stmt->bindParam(':apellido_casada', $apellido_casada);
        $stmt->bindParam(':primer_nombre', $primer_nombre);
        $stmt->bindParam(':otros_nombres', $otros_nombres);
        $stmt->bindParam(':nua_cua_asignado', $nua_cua_asignado);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':cod_tipoafp', $cod_tipoafp);
        $stmt->bindParam(':cod_tipoaporteafp', $cod_tipoaporteafp);
        $stmt->bindParam(':nro_seguro', $nro_seguro);
        $stmt->bindParam(':cod_estadopersonal', $cod_estadopersonal);

        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':celular', $celular);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':persona_contacto', $persona_contacto);
        //$stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':created_by', $created_by);
        //$stmt->bindParam(':modified_at', $modified_at);
        $stmt->bindParam(':modified_by', $modified_by);
        $stmt->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
        $flagSuccess=$stmt->execute(); 

        $tabla_id = $dbh->lastInsertId();

        //para area distribucion
        $stmtPer = $dbhS->prepare("SELECT codigo from personal where ci=$ci_aux");
        $stmtPer->execute();
        $stmtPer->bindColumn('codigo', $codigoP);
        while ($row = $stmtPer->fetch(PDO::FETCH_BOUND)) {
        }
        $stmtDistribucion = $dbh->prepare("INSERT INTO personal_area_distribucion(cod_personal,cod_area,porcentaje,cod_estadoreferencial,created_by,modified_by) 
        values (:cod_personal,:cod_area,:porcentaje,:cod_estadoreferencial,:created_by,:modified_by)");
        //Bind
        $stmtDistribucion->bindParam(':cod_personal', $codigoP);
        $stmtDistribucion->bindParam(':cod_area', $cod_area);    
        $stmtDistribucion->bindParam(':porcentaje', $porcentaje);
        $stmtDistribucion->bindParam(':created_by', $created_by);
        $stmtDistribucion->bindParam(':modified_by', $modified_by);
        $stmtDistribucion->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
        $stmtDistribucion->execute(); 

        $stmtDiscapacitado = $dbh->prepare("INSERT INTO personal_discapacitado (codigo,discapacitado,tutor_discapacitado,celular_tutor,parentesco)
                                            values(:codigo,:discapacitado,:tutordiscapacidad,:celularTutor,:parentescotutor)");
        //bind
        $stmtDiscapacitado->bindParam(':codigo', $codigo);
        $stmtDiscapacitado->bindParam(':discapacitado', $discapacitado);
        $stmtDiscapacitado->bindParam(':tutordiscapacidad', $tutordiscapacidad);
        $stmtDiscapacitado->bindParam(':parentescotutor', $parentescotutor);
        $stmtDiscapacitado->bindParam(':celularTutor', $celularTutor);
        $stmtDiscapacitado->execute();

        if ($flagSuccess){
            $stmt3 = $dbh->prepare("INSERT INTO personalimagen(codigo,imagen) values (:codigo, :imagen)");
            $stmt3->bindParam(':codigo', $tabla_id);
            $stmt3->bindParam(':imagen', $_FILES['image']['name']);//la url esta poniendo
            //sif (move_uploaded_file($_FILES['image']['name'], APP_PATH . DIRECTORY_SEPARATOR ."imagenes".DIRECTORY_SEPARATOR.$_FILES['image']['name']))
            //echo $_FILES['image']['name']."...  ";
            //$archivo = imagenes".DIRECTORY_SEPARATOR.$_FILES['image']['name'];
            //$archivo = "d:\\UwAmp\\www\\ibno\\imagenes\\".$_FILES['image']['name'];//funciona
            $archivo = __DIR__.DIRECTORY_SEPARATOR."imagenes".DIRECTORY_SEPARATOR.$_FILES['image']['name'];
            //esta guardando en activosfijos\imagenes

            //echo $archivo;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $archivo))
                echo "correcto";
            else
                echo "error".$_FILES["image"]["error"];//sale error 0

            $flagSuccess=$stmt3->execute();
        }
        
        showAlertSuccessError($flagSuccess,$urlListPersonal);

        //$stmt->debugDumpParams();
    } else {//update
        $codigo = $_POST["codigo"];
        $stmt = $dbh->prepare("UPDATE personal set ci=:ci,ci_lugar_emision=:ci_lugar_emision,fecha_nacimiento=:fecha_nacimiento,
        cod_cargo=:cod_cargo,cod_unidadorganizacional=:cod_unidadorganizacional,cod_area=:cod_area,jubilado=:jubilado,
        cod_genero=:cod_genero,cod_tipopersonal=:cod_tipopersonal,haber_basico=:haber_basico,paterno=:paterno,
        materno=:materno,apellido_casada=:apellido_casada,primer_nombre=:primer_nombre,otros_nombres=:otros_nombres,
        nua_cua_asignado=:nua_cua_asignado,direccion=:direccion,
        cod_tipoafp=:cod_tipoafp,
        nro_seguro=:nro_seguro,
        cod_estadopersonal=:cod_estadopersonal,created_by=:created_by,
        modified_by=:modified_by, 
        telefono=:telefono,celular=:celular,email=:email,persona_contacto=:persona_contacto
        , cod_tipoaporteafp = :cod_tipoaporteafp  
        where codigo = :codigo");
        //bind
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':ci', $ci);
        $stmt->bindParam(':ci_lugar_emision', $ci_lugar_emision);
        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmt->bindParam(':cod_cargo', $cod_cargo);
        $stmt->bindParam(':cod_unidadorganizacional', $cod_unidadorganizacional);
        $stmt->bindParam(':cod_area', $cod_area);
        $stmt->bindParam(':jubilado', $jubilado);
        $stmt->bindParam(':cod_genero', $cod_genero);
        $stmt->bindParam(':cod_tipopersonal', $cod_tipopersonal);
        $stmt->bindParam(':haber_basico', $haber_basico);
        $stmt->bindParam(':paterno', $paterno);
        $stmt->bindParam(':materno', $materno);
        $stmt->bindParam(':apellido_casada', $apellido_casada);
        $stmt->bindParam(':primer_nombre', $primer_nombre);
        $stmt->bindParam(':otros_nombres', $otros_nombres);
        $stmt->bindParam(':nua_cua_asignado', $nua_cua_asignado);
        $stmt->bindParam(':direccion', $direccion);
        
        $stmt->bindParam(':cod_tipoafp', $cod_tipoafp);
        $stmt->bindParam(':cod_tipoaporteafp', $cod_tipoaporteafp);
        
        $stmt->bindParam(':nro_seguro', $nro_seguro);
        $stmt->bindParam(':cod_estadopersonal', $cod_estadopersonal);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':celular', $celular);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':persona_contacto', $persona_contacto);
        //$stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':created_by', $created_by);
        //$stmt->bindParam(':modified_at', $modified_at);
        $stmt->bindParam(':modified_by', $modified_by);
        
<<<<<<< HEAD
    $stmt = $dbh->prepare("UPDATE personal set cod_cargo=:cod_cargo,cod_unidadorganizacional=:cod_unidadorganizacional,cod_area=:cod_area,jubilado=:jubilado,
    cod_tipopersonal=:cod_tipopersonal,haber_basico=:haber_basico,apellido_casada=:apellido_casada,otros_nombres=:otros_nombres,
    nua_cua_asignado=:nua_cua_asignado,
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
=======
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
>>>>>>> 9665608161fbd74baa97b51d1230f7cda83c0916
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