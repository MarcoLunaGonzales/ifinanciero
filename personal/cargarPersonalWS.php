<?php
set_time_limit(0);


require_once 'conexion.php';
require_once 'functions.php';
require_once 'rrhh/configModule.php';
$dbh = new Conexion();

$sIde = "monitoreo"; 
$sKey = "837b8d9aa8bb73d773f5ef3d160c9b17";



//SERVICIOS TLQ
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ListarPersonal");
$url="http://ibnored.ibnorca.org/wsibno/rrhh/ws-personal-listas.php";


$json=callService($parametros, $url);
$obj=json_decode($json);//decodificando json

$detalle=$obj->lstPersonal;
foreach ($detalle as $objDet){
	$codigo = $objDet->IdCliente;
	$primer_nombre = $objDet->NombreRazon;
	$paterno = $objDet->Paterno;
	$materno = $objDet->Materno;
	$cod_tipoIdentificacion = $objDet->IdTipoIdentificacion;
	$TipoIdentificacionOtro = $objDet->TipoIdentificacionOtro;
	$identificacion = $objDet->Identificacion;
	$cod_lugar_emision = $objDet->IdLugarEmision;
	$LugarEmisionOtro = $objDet->LugarEmisionOtro;
	$cod_nacionalidad = $objDet->IdNacionalidad;
	$fecha_nacimiento = $objDet->FechaNacimiento;
	$cod_genero = $objDet->IdGenero;
	$cod_estadoCivil = $objDet->IdEstadoCivil;
	$cod_pais = $objDet->IdPais;
	$cod_departamento = $objDet->IdDepartamento;
	$cod_ciudad = $objDet->IdCiudad;
	$CiudadOtro = $objDet->CiudadOtro;
	$direccion = $objDet->Direccion;
	$email = $objDet->Correo;
	$telefono = $objDet->Telefono;
	$celular = $objDet->Movil;


	//=====otros datos de web Service
	// $TipoIdentificacion = $objDet->TipoIdentificacion;		
	// $LugarEmision = $objDet->LugarEmision;
	// $genero = $objDet->Genero;	
	// $Nacionalidad = $objDet->Nacionalidad;
	// $EstadoCivil = $objDet->EstadoCivil;
	// $Pais = $objDet->Pais;	
	// $Departamento = $objDet->Departamento;	
	// $Ciudad = $objDet->Ciudad;	
	// $Vigencia = $objDet->Vigencia;

	//=0datos de nuestra BD que faltan

	$created_by = 1;//$_POST["created_by"];
    $modified_by = 1;//$_POST["modified_by"];
    $cod_estadoreferencial=1;

    $codigoPersonalAD=$codigo;
    $codigoPersonalD=$codigo;
    $ci_aux=$identificacion;

    $haber_basico = '';	    
    $cod_cargo = '';
    $cod_unidadorganizacional = '';
    $cod_area = '';
    $jubilado = '0';    
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
    $created_by = 1;//$_POST["created_by"];
    $modified_by = 1;//$_POST["modified_by"];
    $cod_estadoreferencial=1;
    $porcentaje=100;
    //consulta para verificar si personal esta en la bd
    $stmtBusquedaPersonal = $dbh->prepare("SELECT codigo from personal where codigo=:codigo");
    $stmtBusquedaPersonal->bindParam(':codigo',$codigo);
    $stmtBusquedaPersonal->execute();	    
    $resultP=$stmtBusquedaPersonal->fetch();
    $codigoPersonal2=$resultP['codigo'];
    if($codigo==$codigoPersonal2){//UPDATE
		$stmtPersonalU = $dbh->prepare("UPDATE personal set cod_tipoIdentificacion=:cod_tipoIdentificacion,tipo_identificacionOtro=:tipo_identificacionOtro,
			identificacion=:identificacion,cod_lugar_emision=:cod_lugar_emision,lugar_emisionOtro=:lugar_emisionOtro,fecha_nacimiento=:fecha_nacimiento,
			cod_genero=:cod_genero,paterno=:paterno,materno=:materno,primer_nombre=:primer_nombre,
        direccion=:direccion,telefono=:telefono,celular=:celular,email=:email,cod_nacionalidad=:cod_nacionalidad,cod_estadoCivil=:cod_estadoCivil,
        cod_pais=:cod_pais,cod_departamento=:cod_departamento,cod_ciudad=:cod_ciudad,ciudadOtro=:ciudadOtro
        where codigo = :codigo");
        //bind
        $stmtPersonalU->bindParam(':codigo', $codigo);
        $stmtPersonalU->bindParam(':cod_tipoIdentificacion', $cod_tipoIdentificacion);
        $stmtPersonalU->bindParam(':tipo_identificacionOtro', $TipoIdentificacionOtro);
        $stmtPersonalU->bindParam(':identificacion', $identificacion);
        $stmtPersonalU->bindParam(':cod_lugar_emision', $cod_lugar_emision);
        $stmtPersonalU->bindParam(':lugar_emisionOtro', $LugarEmisionOtro);
        $stmtPersonalU->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmtPersonalU->bindParam(':cod_genero', $cod_genero);
        $stmtPersonalU->bindParam(':paterno', $paterno);
        $stmtPersonalU->bindParam(':materno', $materno);
        $stmtPersonalU->bindParam(':primer_nombre', $primer_nombre);
        $stmtPersonalU->bindParam(':direccion', $direccion);
        $stmtPersonalU->bindParam(':telefono', $telefono);
        $stmtPersonalU->bindParam(':celular', $celular);
        $stmtPersonalU->bindParam(':email', $email);        
        $stmtPersonalU->bindParam(':cod_nacionalidad', $cod_nacionalidad);
        $stmtPersonalU->bindParam(':cod_estadoCivil', $cod_estadoCivil);
        $stmtPersonalU->bindParam(':cod_pais', $cod_pais);
        $stmtPersonalU->bindParam(':cod_departamento', $cod_departamento);
        $stmtPersonalU->bindParam(':cod_ciudad', $cod_ciudad);
        $stmtPersonalU->bindParam(':ciudadOtro', $CiudadOtro);        
        $stmtPersonalU->execute(); 
		

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
    	$stmt = $dbh->prepare("INSERT INTO personal(codigo,cod_tipoIdentificacion,tipo_identificacionOtro,identificacion,cod_lugar_emision,lugar_emisionOtro,fecha_nacimiento,cod_cargo,cod_unidadorganizacional,cod_area,
        jubilado,cod_genero,cod_tipopersonal,haber_basico,paterno,materno,apellido_casada,primer_nombre,otros_nombres,nua_cua_asignado,
        direccion,cod_tipoafp,cod_tipoaporteafp,nro_seguro,cod_estadopersonal,telefono,celular,email,persona_contacto,created_by,modified_by,cod_estadoreferencial,cod_nacionalidad,cod_estadoCivil,
        cod_pais,cod_departamento,cod_ciudad,CiudadOtro) 
        values (:codigo,:cod_tipoIdentificacion,:tipo_identificacionOtro,:identificacion,:cod_lugar_emision,:lugar_emisionOtro,:fecha_nacimiento,
        	:cod_cargo,:cod_unidadorganizacional,:cod_area,:jubilado,:cod_genero,:cod_tipopersonal,:haber_basico,:paterno,
        	:materno,:apellido_casada,:primer_nombre,:otros_nombres,:nua_cua_asignado,
        :direccion,:cod_tipoafp,:cod_tipoaporteafp,:nro_seguro,:cod_estadopersonal,:telefono,:celular,:email,:persona_contacto,:created_by,:modified_by,
        :cod_estadoreferencial,:cod_nacionalidad,:cod_estadoCivil,
        :cod_pais,:cod_departamento,:cod_ciudad,:ciudadOtro)");
        //Bind
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':cod_tipoIdentificacion', $cod_tipoIdentificacion);
        $stmt->bindParam(':tipo_identificacionOtro', $TipoIdentificacionOtro);
        $stmt->bindParam(':identificacion', $identificacion);
        $stmt->bindParam(':cod_lugar_emision', $cod_lugar_emision);
        $stmt->bindParam(':lugar_emisionOtro', $LugarEmisionOtro);
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
        $stmt->bindParam(':cod_nacionalidad', $cod_nacionalidad);
        $stmt->bindParam(':cod_estadoCivil', $cod_estadoCivil);
        $stmt->bindParam(':cod_pais', $cod_pais);
        $stmt->bindParam(':cod_departamento', $cod_departamento);
        $stmt->bindParam(':cod_ciudad', $cod_ciudad);
        $stmt->bindParam(':ciudadOtro', $CiudadOtro);        
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
