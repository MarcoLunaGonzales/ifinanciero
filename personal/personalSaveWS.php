<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'rrhh/configModule.php';
	$dbh = new Conexion();
	$data = json_decode(file_get_contents("http://localhost/ifinanciero/assets/plantillas/json/json-personal/personal_ws.json"),true);//json decodificara el contenido
	//print_r($data);

	for ($i=0; $i <count($data) ; $i++) {
		//======recuperamos datos del json
		$codigo = $data[$i]["codigo"];
		$ci = $data[$i]["ci"];
		$fecha_nacimiento = $data[$i]["fecha_nacimiento"];
		$haber_basico = $data[$i]["haber_basico"];
		$paterno = $data[$i]["paterno"];
		$materno = $data[$i]["materno"];
		$primer_nombre = $data[$i]["nombres"];
		$direccion = $data[$i]["direccion"];
		$telefono = $data[$i]["telefono"];
		$celular = $data[$i]["celular"];
		$email = $data[$i]["email"];
		$created_by = 1;//$_POST["created_by"];
	    //$modified_at = $_POST["modified_at"];
	    $modified_by = 1;//$_POST["modified_by"];
	    $cod_estadoreferencial=1;
	   
	    $codigoPersonalAD=$codigo;
	    $codigoPersonalD=$codigo;
	    $ci_aux=$ci;
	    $ci_lugar_emision = '';	    
	    $cod_cargo = '';
	    $cod_unidadorganizacional = '';
	    $cod_area = '';
	    $jubilado = '0';
	    $cod_genero = '';
	    $cod_tipopersonal = '';	    
	    $apellido_casada = '';	    
	    $otros_nombres = '';
	    $nua_cua_asignado = '';	    
	    $cod_tipoafp = '';
	    $cod_tipoaporteafp = '';	   
	    $nro_seguro = '';
	    $cod_estadopersonal = '';	   
	    $persona_contacto = '';
	    $discapacitado='0';
	    $tutordiscapacidad='0';
	    $parentescotutor='';
	    $celularTutor='';
	    //$created_at = $_POST["created_at"];
	    $created_by = 1;//$_POST["created_by"];
	    //$modified_at = $_POST["modified_at"];
	    $modified_by = 1;//$_POST["modified_by"];
	    $cod_estadoreferencial=1;
	    $porcentaje=100;


	    //===hacemos un listado de todo el personal para buscar si hay existe codigo registrado de personal
	    $stmtBusquedaPersonal = $dbh->prepare("SELECT codigo from personal where codigo=:codigo");
	    $stmtBusquedaPersonal->bindParam(':codigo',$codigo);
	    $stmtBusquedaPersonal->execute();	    
	    $resultP=$stmtBusquedaPersonal->fetch();
	    $codigoPersonal2=$resultP['codigo'];
	    

	    if($codigo==$codigoPersonal2){//UPDATE
	    	$stmt = $dbh->prepare("UPDATE personal set ci=:ci,fecha_nacimiento=:fecha_nacimiento,
	        haber_basico=:haber_basico,paterno=:paterno,
	        materno=:materno,primer_nombre=:primer_nombre,
	        direccion=:direccion,
	        telefono=:telefono,celular=:celular,email=:email,
	        where codigo = :codigo");
	        //bind
	        $stmt->bindParam(':codigo', $codigo);
	        $stmt->bindParam(':ci', $ci);	        
	        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);	        
	        $stmt->bindParam(':haber_basico', $haber_basico);
	        $stmt->bindParam(':paterno', $paterno);
	        $stmt->bindParam(':materno', $materno);	        
	        $stmt->bindParam(':primer_nombre', $primer_nombre);	        
	        $stmt->bindParam(':direccion', $direccion);	        	        
	        $stmt->bindParam(':telefono', $telefono);
	        $stmt->bindParam(':celular', $celular);
	        $stmt->bindParam(':email', $email);	        	        
	        $stmt->execute();

	        $stmtDiscapacitado = $dbh->prepare("UPDATE personal_discapacitado set discapacitado = :discapacitado,
	            tutor_discapacitado=:tutordiscapacidad,parentesco=:parentescotutor,celular_tutor=:celularTutor
	        where codigo = :codigo");
	        //bind
	        $stmtDiscapacitado->bindParam(':codigo', $codigoPersonalD);
	        $stmtDiscapacitado->bindParam(':discapacitado', $discapacitado);
	        $stmtDiscapacitado->bindParam(':tutordiscapacidad', $tutordiscapacidad);
	        $stmtDiscapacitado->bindParam(':parentescotutor', $parentescotutor);
	        $stmtDiscapacitado->bindParam(':celularTutor', $celularTutor);

	        $flagSuccess=$stmtDiscapacitado->execute();
	    }else{//INSERT
	    	$stmt = $dbh->prepare("INSERT INTO personal(codigo,ci,ci_lugar_emision,fecha_nacimiento,cod_cargo,cod_unidadorganizacional,cod_area,
	        jubilado,cod_genero,cod_tipopersonal,haber_basico,paterno,materno,apellido_casada,primer_nombre,otros_nombres,nua_cua_asignado,
	        direccion,cod_tipoafp,nro_seguro,cod_estadopersonal,created_by,modified_by,telefono,celular,email,persona_contacto, cod_tipoaporteafp,cod_estadoreferencial) 
	        values (:codigo,:ci, :ci_lugar_emision, :fecha_nacimiento, 
	        :cod_cargo, :cod_unidadorganizacional, :cod_area, :jubilado, :cod_genero, :cod_tipopersonal, :haber_basico, :paterno, 
	        :materno, :apellido_casada, :primer_nombre, :otros_nombres, :nua_cua_asignado, :direccion, :cod_tipoafp, :nro_seguro, 
	        :cod_estadopersonal, :created_by, :modified_by, :telefono, :celular, :email, :persona_contacto, :cod_tipoaporteafp,:cod_estadoreferencial)");
	        //Bind
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
	        $stmt->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
	        $flagSuccess=$stmt->execute(); 
			//======insertamos area distribucion
	        $stmtDistribucion = $dbh->prepare("INSERT INTO personal_area_distribucion(cod_personal,cod_area,porcentaje,cod_estadoreferencial,created_by,modified_by) 
	        values (:cod_personal,:cod_area,:porcentaje,:cod_estadoreferencial,:created_by,:modified_by)");
	        //Bind
	        $stmtDistribucion->bindParam(':cod_personal', $codigoPersonalAD);
	        $stmtDistribucion->bindParam(':cod_area', $cod_area);    
	        $stmtDistribucion->bindParam(':porcentaje', $porcentaje);
	        $stmtDistribucion->bindParam(':created_by', $created_by);
	        $stmtDistribucion->bindParam(':modified_by', $modified_by);
	        $stmtDistribucion->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
	        $stmtDistribucion->execute(); 
	        //insertamos personal discapacitado
	        $stmtDiscapacitado = $dbh->prepare("INSERT INTO personal_discapacitado (codigo,discapacitado,tutor_discapacitado,celular_tutor,parentesco)
	                                            values(:codigo,:discapacitado,:tutordiscapacidad,:celularTutor,:parentescotutor)");       
	        $stmtDiscapacitado->bindParam(':codigo', $codigoPersonalD);
	        $stmtDiscapacitado->bindParam(':discapacitado', $discapacitado);
	        $stmtDiscapacitado->bindParam(':tutordiscapacidad', $tutordiscapacidad);
	        $stmtDiscapacitado->bindParam(':parentescotutor', $parentescotutor);
	        $stmtDiscapacitado->bindParam(':celularTutor', $celularTutor);        
	        $flagSuccess=$stmtDiscapacitado->execute();
	    }


	}	    
    //volvemos al listado de personal
    showAlertSuccessError($flagSuccess,$urlListPersonal);



?>