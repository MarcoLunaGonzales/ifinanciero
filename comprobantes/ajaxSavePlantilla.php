<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$data = obtenerComprobante($_GET['codigo']);
// bindColumn
$data->bindColumn('codigo', $codigo);
$data->bindColumn('cod_gestion', $gestion);
$data->bindColumn('abreviatura', $unidad);
$data->bindColumn('fecha', $fechaComprobante);
$data->bindColumn('cod_tipocomprobante', $tipoComprobante);
$data->bindColumn('numero', $nroCorrelativo);
$data->bindColumn('glosa', $glosaComprobante);

if(isset($_GET['codigo'])){
	$globalCode=obtenerComprobantePlantilla();
  $cantidadFilas=$_GET["cantidad_filas"];
  $detalles= json_decode($_GET['det']);
}else{
	$globalCode=0;
  $cantidadFilas=0;
}
  $carpeta="../assets/plantillas/json/json-".$globalCode;
      if(!file_exists($carpeta)){
           mkdir($carpeta,0777,true);
        }

    $nombre_archivo=$carpeta."/comprobantes_detalle_json.json";

//guardar cabevera
$json[0][0]->tipo_comprobante=$_GET['tipo'];
$json[0][0]->glosa=$_GET['glosa'];
//guardar las ediciones
for ($i=0;$i<cantidadF($detalles);$i++){
  $cuenta=$detalles[$i]->cuenta;
  if($cuenta!=0 || $cuenta!=""){

    $json[1][$i]->cuenta=$detalles[$i]->cuenta;
    $json[1][$i]->cuenta_auxiliar=$detalles[$i]->cuenta_auxiliar;
    $json[1][$i]->nom_cuenta=nameCuenta($detalles[$i]->cuenta);
    $json[1][$i]->n_cuenta=obtieneNumeroCuenta($detalles[$i]->cuenta);
    $json[1][$i]->nom_cuenta_auxiliar=nameCuenta($detalles[$i]->cuenta_auxiliar);
    $json[1][$i]->n_cuenta_auxiliar=obtieneNumeroCuenta($detalles[$i]->cuenta_auxiliar);
    $json[1][$i]->unidad=$detalles[$i]->unidad;
    $json[1][$i]->area=$detalles[$i]->area;
    $json[1][$i]->debe=$detalles[$i]->debe;
    $json[1][$i]->haber=$detalles[$i]->haber;
    $json[1][$i]->glosa_detalle=$detalles[$i]->glosa_detalle;
    $json[1][$i]->orden=$detalles[$i]->orden;

  }
}
      $jsonencoded = json_encode($json,JSON_UNESCAPED_UNICODE);
 




    if(file_exists($nombre_archivo)){
        $mensaje = "El Archivo $nombre_archivo se ha modificado";
    }else{
        $mensaje = "El Archivo $nombre_archivo se ha creado";
    }
    if($fh = fopen($nombre_archivo, "a")){
        if($re= fwrite($fh, $jsonencoded)){
            echo "Se ha ejecutado correctamente";
            $dbh = new Conexion();
            $sqlInsert="INSERT INTO plantillas_comprobante ( cod_unidadorganizacional, titulo, descripcion, archivo_json, cod_personal) VALUES ( '$globalUnidad','".$_GET['titulo']."', '".$_GET['des']."', '$jsonencoded', '$globalUser')";
            $stmtInsert = $dbh->prepare($sqlInsert);
            $stmtInsert->execute();
        }else{
            echo "Ha habido un problema al crear el archivo";
        }
        fclose($archivo);
    }


?>
