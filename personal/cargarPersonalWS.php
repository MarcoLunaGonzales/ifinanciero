<?php
set_time_limit(0);


require_once 'conexion.php';
require_once 'functions.php';
require_once 'rrhh/configModule.php';

require_once 'cargarDatosWS.php';//tipos identificacion personal
require_once 'cargarGeneroWS.php';
require_once 'cargarEstadoCivilWS.php';
$dbh = new Conexion();

$sIde = "monitoreo"; 
$sKey = "837b8d9aa8bb73d773f5ef3d160c9b17";

//SERVICIOS TLQ
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ListarPersonal");
$url="http://ibnored.ibnorca.org/wsibno/rrhh/ws-personal-listas.php";

$json=callService($parametros, $url);
$obj=json_decode($json);//decodificando json
$i=0;
$j=0;
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
    $cod_grado_academico=0;
    $ing_contr='0000-00-00';
    $ing_planilla = '0000-00-00';
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
        
        $flagSuccess=$stmtPersonalU->execute();

        // $stmtDiscapacitado = $dbh->prepare("UPDATE personal_discapacitado set discapacitado = :discapacitado,
        //     tutor_discapacitado=:tutordiscapacidad,parentesco=:parentescotutor,celular_tutor=:celularTutor
        // where codigo = :codigo");
        // //bind
        // $stmtDiscapacitado->bindParam(':codigo', $codigoPersonalD);
        // $stmtDiscapacitado->bindParam(':discapacitado', $discapacitado);
        // $stmtDiscapacitado->bindParam(':tutordiscapacidad', $tutordiscapacidad);
        // $stmtDiscapacitado->bindParam(':parentescotutor', $parentescotutor);
        // $stmtDiscapacitado->bindParam(':celularTutor', $celularTutor);

        // $flagSuccess=$stmtDiscapacitado->execute();


        if($flagSuccess==false){
            $i++;            
        }
    	
    }else{//INSERT
    	$stmtI = $dbh->prepare("INSERT INTO personal(codigo,cod_tipo_identificacion,tipo_identificacion_otro,identificacion,cod_lugar_emision,lugar_emision_otro,fecha_nacimiento,cod_cargo,cod_unidadorganizacional,cod_area,
        jubilado,cod_genero,cod_tipopersonal,haber_basico,paterno,materno,apellido_casada,primer_nombre,otros_nombres,nua_cua_asignado,
        direccion,cod_tipoafp,cod_tipoaporteafp,nro_seguro,cod_estadopersonal,telefono,celular,email,persona_contacto,created_by,modified_by,cod_estadoreferencial,cod_nacionalidad,cod_estadoCivil,
        cod_pais,cod_departamento,cod_ciudad,Ciudad_otro,cod_grado_academico,ing_contr,ing_planilla) 
        values (:codigo,:cod_tipoIdentificacion,:tipo_identificacionOtro,:identificacion,:cod_lugar_emision,:lugar_emisionOtro,:fecha_nacimiento,
        	:cod_cargo,:cod_unidadorganizacional,:cod_area,:jubilado,:cod_genero,:cod_tipopersonal,:haber_basico,:paterno,
        	:materno,:apellido_casada,:primer_nombre,:otros_nombres,:nua_cua_asignado,
        :direccion,:cod_tipoafp,:cod_tipoaporteafp,:nro_seguro,:cod_estadopersonal,:telefono,:celular,:email,:persona_contacto,:created_by,:modified_by,
        :cod_estadoreferencial,:cod_nacionalidad,:cod_estadoCivil,
        :cod_pais,:cod_departamento,:cod_ciudad,:ciudadOtro,:cod_grado_academico,:ing_contr,:ing_planilla)");
        //Bind
        $stmtI->bindParam(':codigo', $codigo);
        $stmtI->bindParam(':cod_tipoIdentificacion', $cod_tipoIdentificacion);
        $stmtI->bindParam(':tipo_identificacionOtro', $TipoIdentificacionOtro);
        $stmtI->bindParam(':identificacion', $identificacion);
        $stmtI->bindParam(':cod_lugar_emision', $cod_lugar_emision);
        $stmtI->bindParam(':lugar_emisionOtro', $LugarEmisionOtro);
        $stmtI->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmtI->bindParam(':cod_cargo', $cod_cargo);
        $stmtI->bindParam(':cod_unidadorganizacional', $cod_unidadorganizacional);
        $stmtI->bindParam(':cod_area', $cod_area);
        $stmtI->bindParam(':jubilado', $jubilado);
        $stmtI->bindParam(':cod_genero', $cod_genero);
        $stmtI->bindParam(':cod_tipopersonal', $cod_tipopersonal);
        $stmtI->bindParam(':haber_basico', $haber_basico);
        $stmtI->bindParam(':paterno', $paterno);
        $stmtI->bindParam(':materno', $materno);
        $stmtI->bindParam(':apellido_casada', $apellido_casada);
        $stmtI->bindParam(':primer_nombre', $primer_nombre);
        $stmtI->bindParam(':otros_nombres', $otros_nombres);
        $stmtI->bindParam(':nua_cua_asignado', $nua_cua_asignado);
        $stmtI->bindParam(':direccion', $direccion);
        $stmtI->bindParam(':cod_tipoafp', $cod_tipoafp);
        $stmtI->bindParam(':cod_tipoaporteafp', $cod_tipoaporteafp);
        $stmtI->bindParam(':nro_seguro', $nro_seguro);
        $stmtI->bindParam(':cod_estadopersonal', $cod_estadopersonal);
        $stmtI->bindParam(':telefono', $telefono);
        $stmtI->bindParam(':celular', $celular);
        $stmtI->bindParam(':email', $email);
        $stmtI->bindParam(':persona_contacto', $persona_contacto);
        //$stmtI->bindParam(':created_at', $created_at);
        $stmtI->bindParam(':created_by', $created_by);
        //$stmtI->bindParam(':modified_at', $modified_at);
        $stmtI->bindParam(':modified_by', $modified_by);
        $stmtI->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
        $stmtI->bindParam(':cod_nacionalidad', $cod_nacionalidad);
        $stmtI->bindParam(':cod_estadoCivil', $cod_estadoCivil);
        $stmtI->bindParam(':cod_pais', $cod_pais);
        $stmtI->bindParam(':cod_departamento', $cod_departamento);
        $stmtI->bindParam(':cod_ciudad', $cod_ciudad);
        $stmtI->bindParam(':ciudadOtro', $CiudadOtro);
        $stmtI->bindParam(':cod_grado_academico', $cod_grado_academico);
        $stmtI->bindParam(':ing_contr', $ing_contr); 
        $stmtI->bindParam(':ing_planilla', $ing_planilla); 

        $flagSuccess=$stmtI->execute();
        if($flagSuccess==false){
            $j++;            
        }
		//======insertamos area distribucion
        $stmtDistribucion = $dbh->prepare("INSERT INTO personal_area_distribucion(cod_personal,cod_uo,cod_area,porcentaje,cod_estadoreferencial,created_by,modified_by) 
        values (:cod_personal,:cod_uo,:cod_area,:porcentaje,:cod_estadoreferencial,:created_by,:modified_by)");
        //Bind
        $stmtDistribucion->bindParam(':cod_personal', $codigoPersonalAD);
        $stmtDistribucion->bindParam(':cod_uo', $cod_uo);    
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
echo "<br>";
echo  $j." ERROR AL REGISTRAR PERSONAL <br>";
echo  $i." ERROR AL ACTUALIZAR PERSONAL <br>";

//volvemos al listado de personal
showAlertSuccessError($flagSuccess,$urlListPersonal);


?>
