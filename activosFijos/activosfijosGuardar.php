<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';
ini_set('display_errors',1);

$dbh = new Conexion();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {
    $codigoactivo=$_POST["codigoactivo"];
    $tipoalta=$_POST["tipoalta"];
    
    //llega en espaniol la fecha
    $d = str_replace('/','-',$_POST["fechalta"]);
    //$fechalta='2019-09-10';//$_POST["fechalta"];
    $fechalta=date('Y-m-d' , strtotime($d));

    $indiceufv=1;//$_POST["indiceufv"];xxxxxxxxxxxxxxxxxxxxxxxxxxxX
    $tipocambio=1;//$_POST["tipocambio"];XXXXXXXXXXXXXXXXXXXXXXXXXX
    $moneda=1;//$_POST["moneda"];XXXXXXXXXXXXXXXXXXXXXXXXXXXXX
   
    $valorinicial=$_POST["valorinicial"];
    $depreciacionacumulada=$_POST["depreciacionacumulada"];
    $valorresidual=$_POST["valorresidual"];
    $cod_depreciaciones=$_POST["cod_depreciaciones"];
    $cod_tiposbienes=$_POST["cod_tiposbienes"];
    $vidautilmeses=$_POST["vidautilmeses"];
    $estadobien=$_POST["estadobien"];
    $otrodato=$_POST["otrodato"];

    $cod_ubicaciones=0;
    if(isset($_POST["cod_ubicaciones"])){
        $cod_ubicaciones=$_POST["cod_ubicaciones"];
    }

    $cod_empresa=1;//$_POST["cod_empresa"];
    $activo=$_POST["activo"];
    //var_dump($_POST);
    $cod_responsables_responsable=0;
    $cod_responsables_autorizadopor=0;
    $reevaluo=0;

    if(isset($_POST["cod_responsables_responsable"])){
        $cod_responsables_responsable=$_POST["cod_responsables_responsable"];
    }
    if(isset($_POST["cod_responsables_autorizadopor"])){
        $cod_responsables_autorizadopor=$_POST["cod_responsables_autorizadopor"];
    }

    //$created_at=$_POST["created_at"];
    //$created_by=$_POST["created_by"];
    //$modified_at=$_POST["modified_at"];
    //$modified_by=$_POST["modified_by"];
    $cod_af_proveedores=$_POST["cod_af_proveedores"];
    if($cod_af_proveedores=='')$cod_af_proveedores=null;
    $numerofactura=$_POST["numerofactura"];
    $bandera_depreciar = 'NO';#LA PRIMERA VEZ LUEGO SE CAMBIA A SI Y DEPRECIA

    $cod_unidadorganizacional = $_POST['cod_unidadorganizacional'];
    $cod_proy_finan = $_POST['cod_proy_finan'];

    // echo "llego: ".$cod_proy_finan;

    $cod_area=0;
    if(isset($_POST['cod_area'])){
        $cod_area = $_POST['cod_area'];
    }
    $cod_tiposactivos=$_POST['cod_tiposactivos'];
    $cod_estadoactivofijo = 1;


    if ($_POST["codigo"] == 0){
        $stmt = $dbh->prepare("INSERT INTO activosfijos(codigoactivo,tipoalta,fechalta,indiceufv,tipocambio,moneda,valorinicial,
        depreciacionacumulada,valorresidual,cod_depreciaciones,cod_tiposbienes,vidautilmeses, vidautilmeses_restante,estadobien,otrodato,cod_ubicaciones,
        cod_empresa,activo,cod_responsables_responsable,cod_responsables_autorizadopor, cod_af_proveedores, numerofactura,
        bandera_depreciar, cod_unidadorganizacional,cod_area, cod_estadoactivofijo,cod_proy_financiacion,reevaluo,tipo_af) values
        (:codigoactivo, :tipoalta, :fechalta, :indiceufv, :tipocambio, :moneda, :valorinicial, :depreciacionacumulada, :valorresidual,
        :cod_depreciaciones, :cod_tiposbienes, :vidautilmeses, :vidautilmeses_restante, :estadobien, :otrodato, :cod_ubicaciones, :cod_empresa, :activo,
        :cod_responsables_responsable, :cod_responsables_autorizadopor, :cod_af_proveedores, :numerofactura,
        :bandera_depreciar, :cod_unidadorganizacional, :cod_area ,:cod_estadoactivofijo,:cod_proy_financiacion,:reevaluo,:cod_tiposactivos)");

        //necesito guardar en una segunda tabla: activofijos_asignaciones

        //$stmt->debugDumpParams();

        //Bind
        //$stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':codigoactivo', $codigoactivo);
        $stmt->bindParam(':tipoalta', $tipoalta);
        $stmt->bindParam(':fechalta', $fechalta);

        $stmt->bindParam(':indiceufv', $indiceufv);//no sirve
        //
        $stmt->bindParam(':tipocambio', $tipocambio);//no sirve
        $stmt->bindParam(':moneda', $moneda);//no sirve

        $stmt->bindParam(':valorinicial', $valorinicial);
        $stmt->bindParam(':depreciacionacumulada', $depreciacionacumulada);
        $stmt->bindParam(':valorresidual', $valorresidual);//resta del anterior
        $stmt->bindParam(':cod_depreciaciones', $cod_depreciaciones);
        $stmt->bindParam(':cod_tiposbienes', $cod_tiposbienes);
        $stmt->bindParam(':vidautilmeses', $vidautilmeses);
        $stmt->bindParam(':vidautilmeses_restante', $vidautilmeses);//mismo valor si es nuevo
        $stmt->bindParam(':estadobien', $estadobien);
        $stmt->bindParam(':otrodato', $otrodato);
        $stmt->bindParam(':cod_ubicaciones', $cod_ubicaciones);
        $stmt->bindParam(':cod_empresa', $cod_empresa);
        $stmt->bindParam(':activo', $activo);
        $stmt->bindParam(':cod_responsables_responsable', $cod_responsables_responsable);
        $stmt->bindParam(':cod_responsables_autorizadopor', $cod_responsables_autorizadopor);

        $stmt->bindParam(':cod_af_proveedores', $cod_af_proveedores);
        $stmt->bindParam(':numerofactura', $numerofactura);
        $stmt->bindParam(':bandera_depreciar', $bandera_depreciar);

        $stmt->bindParam(':cod_unidadorganizacional', $cod_unidadorganizacional);
        $stmt->bindParam(':cod_area', $cod_area);
        $stmt->bindParam(':cod_estadoactivofijo', $cod_estadoactivofijo);
        $stmt->bindParam(':cod_proy_financiacion', $cod_proy_finan);
        $stmt->bindParam(':reevaluo', $reevaluo);
        $stmt->bindParam(':cod_tiposactivos', $cod_tiposactivos);
        
        
        //$stmt->bindParam(':created_at', $created_at);
        //$stmt->bindParam(':created_by', $created_by);
        //$stmt->bindParam(':modified_at', $modified_at);
        //$stmt->bindParam(':modified_by', $modified_by);

        $flagSuccess=$stmt->execute();
        ///////////////////////////////////////////////////////////////////////////////////////////////
        //insertar el segundo: SE CREA UNA ASIGNACION POR DEFECTO
        

        //LA PRIMERA ASIGNACION INGRESA POR ACA.
        $ultimo = $dbh->lastInsertId();
        //echo $ultimo;
        $stmt2 = $dbh->prepare("INSERT INTO activofijos_asignaciones(cod_activosfijos,fechaasignacion,
            cod_ubicaciones,cod_personal, estadobien_asig, cod_unidadorganizacional, cod_area, cod_estadoasignacionaf)
            values (:cod_activosfijos, now(),
            :cod_ubicaciones, :cod_personal, :estadobien_asig, :cod_unidadorganizacional, :cod_area, :cod_estadoasignacionaf)");

        $codEstadoAsignacionAF="1";
        $stmt2->bindParam(':cod_activosfijos', $ultimo);
        //$stmt2->bindParam(':fechaasignacion', $fechalta);
        $stmt2->bindParam(':cod_ubicaciones', $cod_ubicaciones);
        $stmt2->bindParam(':cod_personal', $cod_responsables_responsable);
        $stmt2->bindParam(':estadobien_asig', $estadobien);
        $stmt2->bindParam(':cod_unidadorganizacional', $cod_unidadorganizacional);
        $stmt2->bindParam(':cod_area', $cod_area);
        $stmt2->bindParam(':cod_estadoasignacionaf', $codEstadoAsignacionAF);
        //$stmt2->bindParam(':created_by', 1);
        //$stmt2->bindParam(':modified_by', 1);
        $flagSuccess=$stmt2->execute();
        

        $stmt3 = $dbh->prepare("INSERT INTO activosfijosimagen(codigo,imagen) values (:codigo, :imagen)");
        $stmt3->bindParam(':codigo', $ultimo);
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
            echo "Sin imagen".$_FILES["image"]["error"];//sale error 0

        $flagSuccess=$stmt3->execute();
        //$stmt3->debugDumpParams();

        //$arr = $stmt->errorInfo();
        //print_r($arr);
        //$tabla_id = $dbh->lastInsertId();;
        
        showAlertSuccessError($flagSuccess,$urlList6);

        //$stmt->debugDumpParams();
    } else {
        //UPDATE
        $codigo = $_POST["codigo"];
        //obtener unos datos antes de actualizar...
        $stmtPREVIO = $dbh->prepare("SELECT * FROM activosfijos where codigo =:codigo");
        //Ejecutamos;
        $stmtPREVIO->bindParam(':codigo',$codigo);
        $stmtPREVIO->execute();
        $resultPREVIO = $stmtPREVIO->fetch();
        //$codigo = $result['codigo'];
        $idubicacion222 = $resultPREVIO['cod_ubicaciones'];
        $idresponsable222 = $resultPREVIO['cod_responsables_responsable'];
        $valorinicial222 = $resultPREVIO['valorinicial'];
        //SI EL VALOR DEL ACTIVO CAMBIA... se actualiza el valor bandera_depreciar a NO
        $bandera_depreciar = 'SI';
        if ($valorinicial222 != $valorinicial)
            $bandera_depreciar = 'NO';//SIGNIFICA FLASH
        //preparamos para actualizar

        $stmt = $dbh->prepare("UPDATE activosfijos set codigoactivo=:codigoactivo,tipoalta=:tipoalta,fechalta=:fechalta,
        indiceufv=:indiceufv,tipocambio=:tipocambio,moneda=:moneda,valorinicial=:valorinicial,
        depreciacionacumulada=:depreciacionacumulada,valorresidual=:valorresidual,
        cod_depreciaciones=:cod_depreciaciones,cod_tiposbienes=:cod_tiposbienes,
        vidautilmeses=:vidautilmeses,estadobien=:estadobien,otrodato=:otrodato,cod_empresa=:cod_empresa,activo=:activo,
        vidautilmeses_restante=:vidautilmeses_restante,cod_af_proveedores=:cod_af_proveedores,
        numerofactura=:numerofactura, bandera_depreciar = :bandera_depreciar,cod_proy_financiacion=:cod_proy_financiacion,tipo_af=:cod_tiposactivos where codigo = :codigo");
        //bind
        //created_at=:created_at,created_by=:created_by,modified_at=:modified_at,modified_by=:modified_by,
       
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':codigoactivo', $codigoactivo);
        $stmt->bindParam(':tipoalta', $tipoalta);
        $stmt->bindParam(':fechalta', $fechalta);
        $stmt->bindParam(':indiceufv', $indiceufv);
        $stmt->bindParam(':tipocambio', $tipocambio);
        $stmt->bindParam(':moneda', $moneda);
        $stmt->bindParam(':valorinicial', $valorinicial);
        $stmt->bindParam(':depreciacionacumulada', $depreciacionacumulada);
        $stmt->bindParam(':valorresidual', $valorresidual);
        $stmt->bindParam(':cod_depreciaciones', $cod_depreciaciones);
        $stmt->bindParam(':cod_tiposbienes', $cod_tiposbienes);
        $stmt->bindParam(':vidautilmeses', $vidautilmeses);
        $stmt->bindParam(':estadobien', $estadobien);
        $stmt->bindParam(':otrodato', $otrodato);
        $stmt->bindParam(':cod_empresa', $cod_empresa);
        $stmt->bindParam(':activo', $activo);    
        $stmt->bindParam(':vidautilmeses_restante', $vidautilmeses_restante);
        $stmt->bindParam(':cod_af_proveedores', $cod_af_proveedores);
        $stmt->bindParam(':numerofactura', $numerofactura);
        $stmt->bindParam(':bandera_depreciar', $bandera_depreciar);
        $stmt->bindParam(':cod_proy_financiacion', $cod_proy_finan);
        $stmt->bindParam(':cod_tiposactivos', $cod_tiposactivos);

        $flagSuccess=$stmt->execute();

        //si cambio la imagen reemplazar...

        //1 obtener la imagen anterior
        $stmtANT = $dbh->prepare("SELECT * FROM activosfijosimagen where codigo =:codigo");
        //Ejecutamos;
        $stmtANT->bindParam(':codigo',$codigo);
        $stmtANT->execute();
        $resultANT = $stmtANT->fetch();
        //$codigo = $result['codigo'];
        $imagenANT = $resultANT['imagen'];


        if ($imagenANT != $_FILES['image']['name'] AND strlen($_FILES['image']['name']) > 1){//solo si es diferente actualizar
            //entra
            //echo "reemplazar";
            $results = $dbh->query("SELECT * from activosfijosimagen where codigo = ".$codigo)->fetchAll(PDO::FETCH_ASSOC);
            if(count($results))     
            {
                $stmt3 = $dbh->prepare("UPDATE activosfijosimagen set imagen = :imagen where codigo = :codigo");    
            } else {
                $stmt3 = $dbh->prepare("INSERT into activosfijosimagen (codigo, imagen) values (:codigo, :imagen)");
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

        showAlertSuccessError($flagSuccess,$urlList6);

    }//si es insert o update
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>