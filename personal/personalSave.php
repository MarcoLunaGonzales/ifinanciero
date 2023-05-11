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
    $codigo = $_POST["codigo"];  
    $cod_cargo = $_POST["cod_cargo"];
    $cod_unidadorganizacional = $_POST["cod_uo"];
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
    $grado_academico=$_POST['grado_academico'];
    $ing_contr=$_POST['ing_contr'];//esto se manejará para las planillas
    $ing_planilla=$_POST['ing_planilla'];
    $bandera=1;
    $email_empresa=$_POST['email_empresa'];
    $tipo_persona_discapacitado=$_POST['tipo_persona_discapacitado'];
    $nro_carnet_discapacidad=$_POST['nro_carnet_discapacidad'];
    $fecha_nac_persona_dis =$_POST['fecha_nac_persona_dis'];

    $personal_confianza=$_POST['personal_confianza'];
    $cuenta_bancaria=$_POST['cuenta_bancaria'];
    $cod_banco=$_POST['cod_banco'];
    $codigo_dependiente=$_POST['codigo_dependiente'];

    
    $globalUser=$_SESSION['globalUser'];
    //$created_at = $_POST["created_at"];
    $created_by = $globalUser;//$_POST["created_by"];
    //$modified_at = $_POST["modified_at"];
    $modified_by = $globalUser;//$_POST["modified_by"];
    $cod_estadoreferencial=1;
    $porcentaje=100;

    // Nuevo campo Nro_casillero
    $nro_casillero = $_POST['nro_casillero'];

    $stmt = $dbh->prepare("UPDATE personal set jubilado=:jubilado,
    cod_tipopersonal=:cod_tipopersonal,apellido_casada=:apellido_casada,otros_nombres=:otros_nombres,
    nua_cua_asignado=:nua_cua_asignado,ing_contr=:ing_contr,ing_planilla=:ing_planilla,
    cod_tipoafp=:cod_tipoafp,nro_seguro=:nro_seguro,
    cod_estadopersonal=:cod_estadopersonal,persona_contacto=:persona_contacto,cod_tipoaporteafp = :cod_tipoaporteafp,email_empresa=:email_empresa,bandera=:bandera,personal_confianza=:personal_confianza ,modified_by=:modified_by,modified_at=NOW(),cuenta_bancaria=:cuenta_bancaria,cod_banco=:cod_banco,codigo_dependiente=:codigo_dependiente,nro_casillero=:nro_casillero
    where codigo = :codigo");
    //bind
    $stmt->bindParam(':codigo', $codigo);
    // $stmt->bindParam(':cod_cargo', $cod_cargo);
    // $stmt->bindParam(':cod_unidadorganizacional', $cod_unidadorganizacional);
    // $stmt->bindParam(':cod_area', $cod_area);
    $stmt->bindParam(':jubilado', $jubilado);        
    $stmt->bindParam(':cod_tipopersonal', $cod_tipopersonal);
    // $stmt->bindParam(':haber_basico', $haber_basico);        
    $stmt->bindParam(':apellido_casada', $apellido_casada);
    $stmt->bindParam(':otros_nombres', $otros_nombres);
    $stmt->bindParam(':nua_cua_asignado', $nua_cua_asignado);            
    $stmt->bindParam(':cod_tipoafp', $cod_tipoafp);
    $stmt->bindParam(':cod_tipoaporteafp', $cod_tipoaporteafp);        
    $stmt->bindParam(':nro_seguro', $nro_seguro);
    $stmt->bindParam(':cod_estadopersonal', $cod_estadopersonal);        
    $stmt->bindParam(':persona_contacto', $persona_contacto);
    // $stmt->bindParam(':grado_academico', $grado_academico);

    $stmt->bindParam(':ing_contr', $ing_contr);
    $stmt->bindParam(':ing_planilla', $ing_planilla);
    $stmt->bindParam(':email_empresa', $email_empresa);
    $stmt->bindParam(':bandera', $bandera);
    $stmt->bindParam(':personal_confianza', $personal_confianza);
    $stmt->bindParam(':modified_by', $modified_by);
    $stmt->bindParam(':cuenta_bancaria', $cuenta_bancaria);
    $stmt->bindParam(':cod_banco', $cod_banco);
    $stmt->bindParam(':codigo_dependiente', $codigo_dependiente);
    $stmt->bindParam(':nro_casillero', $nro_casillero);
    
    $flagSuccess=$stmt->execute();

    //sacmos el id de area distribucion area distribucion
    $stmtPer = $dbhS->prepare("SELECT codigo 
            from personal_area_distribucion 
            where cod_personal=:cod_personal ORDER BY 1 DESC");
    $stmtPer->bindParam(':cod_personal', $codigo);
    $stmtPer->execute();
    $resultPer=$stmtPer->fetch();
    $codigo_areaDP=$resultPer['codigo'];
        //$flagSuccess=$stmt->execute();
        //para area distribucion
        // $stmtPer = $dbhS->prepare("SELECT codigo,cod_area 
        //         from personal_area_distribucion 
        //         where cod_personal=:cod_personal ORDER BY 1 DESC");
        // $stmtPer->bindParam(':cod_personal', $codigo);
        // $stmtPer->execute();
        // $stmtPer->bindColumn('codigo', $codigo_areaDP);
        // $stmtPer->bindColumn('cod_area', $codigo_areaP);
        // while ($row = $stmtPer->fetch(PDO::FETCH_BOUND)) {
        // }                
        $stmtDistribucion = $dbh->prepare("UPDATE personal_area_distribucion 
            set cod_uo=:cod_uo,cod_area=:cod_area,porcentaje=:porcentaje,monto=:haber_basico where codigo=:codigo_areaDP");
        $stmtDistribucion->bindParam(':codigo_areaDP', $codigo_areaDP);
        $stmtDistribucion->bindParam(':cod_uo', $cod_unidadorganizacional); 
        $stmtDistribucion->bindParam(':cod_area', $cod_area); 
        $stmtDistribucion->bindParam(':porcentaje', $porcentaje);            
        $stmtDistribucion->bindParam(':haber_basico', $haber_basico);   
        $stmtDistribucion->execute();     
        
        //actualizamos la parte de personal discapacitado        
        $stmtDiscapacitado = $dbh->prepare("UPDATE personal_discapacitado set tipo_persona_discapacitado = :tipo_persona_discapacitado,
            nro_carnet_discapacidad=:nro_carnet_discapacidad,fecha_nac_persona_dis=:fecha_nac_persona_dis,cod_estadoreferencial=:cod_estadoreferencial
        where codigo = :codigo");
        //bind
        $stmtDiscapacitado->bindParam(':codigo', $codigo);        
        $stmtDiscapacitado->bindParam(':tipo_persona_discapacitado', $tipo_persona_discapacitado);
        $stmtDiscapacitado->bindParam(':nro_carnet_discapacidad', $nro_carnet_discapacidad);
        $stmtDiscapacitado->bindParam(':fecha_nac_persona_dis', $fecha_nac_persona_dis);
        $stmtDiscapacitado->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);

        
        $flagSuccess=$stmtDiscapacitado->execute();
        
        //parte de imagen
        //imagen anterior
        $stmtANT = $dbh->prepare("SELECT * FROM personalimagen where codigo =:codigo");
        //Ejecutamos;
        $stmtANT->bindParam(':codigo',$codigo);
        $stmtANT->execute();
        $resultANT = $stmtANT->fetch();
        //$codigo = $result['codigo'];
        $imagenANT = $resultANT['imagen'];


        if ($imagenANT != $_FILES['image']['name'] AND strlen($_FILES['image']['name']) > 1){//solo si es diferente actualizar
            //entra
            //echo "reemplazar";
            $results = $dbh->query("SELECT * from personalimagen where codigo = ".$codigo)->fetchAll(PDO::FETCH_ASSOC);
            if(count($results))     
            {
                $stmt3 = $dbh->prepare("UPDATE personalimagen set imagen = :imagen where codigo = :codigo");    
            } else {
                $stmt3 = $dbh->prepare("INSERT into personalimagen (codigo, imagen) values (:codigo, :imagen)");
            }                
            $stmt3->bindParam(':codigo', $codigo);
            $stmt3->bindParam(':imagen', $_FILES['image']['name']);//la url esta poniendo
            //if (move_uploaded_file($_FILES['image']['name'], APP_PATH . DIRECTORY_SEPARATOR ."imagenes".DIRECTORY_SEPARATOR.$_FILES['image']['name']))
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

    
} catch(PDOException $ex){
    //manejar error
    echo "Un error ocurrio".$ex->getMessage();
}
?>