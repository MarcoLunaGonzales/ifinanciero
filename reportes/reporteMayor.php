<?php
session_start();
set_time_limit(0);
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';

$dbh = new Conexion();
// Preparamos
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaActual=date("Y-m-d");
$desdeInicioAnio="";
if($_POST["fecha_desde"]==""){
  $y=$globalNombreGestion;
  $desde=$y."-01-01";
  $hasta=$y."-12-31";
  $desdeInicioAnio=$y."-01-01";
}else{
  $porcionesFechaDesde = explode("-", $_POST["fecha_desde"]);
  $porcionesFechaHasta = explode("-", $_POST["fecha_hasta"]);

  $desdeInicioAnio=$porcionesFechaDesde[0]."-01-01";
  $desde=$porcionesFechaDesde[0]."-".$porcionesFechaDesde[1]."-".$porcionesFechaDesde[2];
  $hasta=$porcionesFechaHasta[0]."-".$porcionesFechaHasta[1]."-".$porcionesFechaHasta[2];
  //$desde=strftime('%Y-%m-%d',strtotime($_POST["fecha_desde"]));
  //$hasta=strftime('%Y-%m-%d',strtotime($_POST["fecha_hasta"]));
}

$moneda=$_POST["moneda"];

$codcuenta=$_POST["cuenta"];
$codcuentaMayor=$_POST["cuenta"];
$nombreMoneda=nameMoneda($moneda);
$unidadCosto=$_POST['unidad_costo'];
$areaCosto=$_POST['area_costo'];
$unidad=$_POST['unidad'];

//echo "VARIABLES: ".$unidadCosto." ".$areaCosto." ".$unidad;


$gestion= $_POST["gestion"];
$entidad = $_POST["entidad"];

//PONEMOS LAS VARIABLES PARA CUANDO LLAMEMOS AL REPORTE DESDE LOS MAYORES
if($gestion==null){
  $gestion=$globalGestion;
  $unidadCosto=explode(",",obtenerUnidadesReport(0));
  $unidad=explode(",",obtenerUnidadesReport(0));
  $areaCosto=explode(",",obtenerAreasReport(0));
}
$NombreGestion = nameGestion($gestion);
$unidadCostoArray=implode(",", $unidadCosto);
$areaCostoArray=implode(",", $areaCosto);
$unidadArray=implode(",", $unidad);
if(isset($_POST['glosa_len'])){
 $glosaLen=1; 
}else{
  $glosaLen=0;
}
if(isset($_POST['cuentas_auxiliares'])){
 $cuentas_auxiliares=1; 
}else{
  $cuentas_auxiliares=0;
}

if(isset($_POST['cuenta_especifica'])){
  $codcuenta=[];
  $codcuenta[0]=$_POST['cuenta_especifica']."@normal";
}

$codcuenta=listarNivelesCuentaPadre($codcuenta);
$unidadGeneral="";$unidadAbrev="";$areaAbrev="";

$unidadGeneral=abrevUnidad($unidadArray);
$unidadAbrev=abrevUnidad($unidadCostoArray);
$areaAbrev=abrevArea($areaCostoArray);

$nombreCuentaTitle="";
for ($jj=0; $jj < cantidadF($codcuenta); $jj++) { 
    $porciones1 = explode("@", $codcuenta[$jj]);
    $cuenta=$porciones1[0];
    if($porciones1[1]=="aux"){
      $nombreCuentaTitle.=trim(nameCuentaAux($cuenta)).", ";
    }else{
      $nombreCuentaTitle.="[".trim(obtieneNumeroCuenta($cuenta))."] ".trim(nameCuenta($cuenta)).", ";
    }
}
$periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($desde))." al ".strftime('%d/%m/%Y',strtotime($hasta));

     if(strlen($nombreCuentaTitle)>190){
        $nombreCuentaTitle=substr($nombreCuentaTitle,0,190)."...";
      }

?><div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon bg-blanco">
                    <img class="" width="40" height="40" src="../assets/img/logoibnorca.png">
                  </div><?php
if(isset($_POST['reporte_datos'])){
   include "reporteMayorCuentaDatos.php";
}else{
  

?>
<!--<style>
.dt-buttons{
position: absolute !important;
right :50px !important;
top:-20px !important;
z-index: 20;
}
</style>-->
 
                <?php
                if($cuentas_auxiliares==0){
                 ?><div class="float-right col-sm-2"><h6 class="card-title">Exportar como:</h6></div><?php
                 }
                 ?>          
                   <h4 class="card-title text-center">Reporte Libro Mayor</h4>
                </div>
                <?php
                $nombreCuentaTitleAux=$nombreCuentaTitle;
                 if($cuentas_auxiliares==0){
                    include "reporteMayorCuenta.php";
                 }else{
                   //include "reporteMayorCuenta.php";
                   for ($cta=0; $cta < cantidadF($codcuentaMayor); $cta++) { 
                     $porcionesCuenta = explode("@", $codcuentaMayor[$cta]);
                     $cuentaCta=$porcionesCuenta[0];
                     $sql="SELECT * from cuentas_auxiliares where cod_cuenta=$cuentaCta and cod_estadoreferencial=1";
                     $stmtAux = $dbh->prepare($sql);
                     $stmtAux->execute();
                     while ($rowAux = $stmtAux->fetch(PDO::FETCH_ASSOC)) {
                       $valorAux=0;
                       $codigoAux=$rowAux['codigo'];
                       $sqlNum="SELECT count(*) as numero
                    FROM cuentas_auxiliares p 
                    join comprobantes_detalle d on p.codigo=d.cod_cuentaauxiliar 
                    join areas a on d.cod_area=a.codigo 
                    join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional 
                    join comprobantes c on d.cod_comprobante=c.codigo
                    where c.cod_gestion=$NombreGestion and p.codigo=$codigoAux and c.cod_estadocomprobante<>2 and c.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) and c.cod_unidadorganizacional in ($unidadArray)";
                       $stmtNum = $dbh->prepare($sqlNum);
                       $stmtNum->execute();
                       while ($rowNum = $stmtNum->fetch(PDO::FETCH_ASSOC)) {
                        $valorAux=$rowNum['numero'];
                       }

                      if($valorAux!=0){
                       $codcuenta=[];
                       $nombreCuentaTitle=strtoupper($nombreCuentaTitleAux."  Cuenta Auxiliar: ".$rowAux['nombre']);
                       $codcuenta[0]=$codigoAux."@aux";
                       include "reporteMayorCuenta.php";
                       ?><br></br><hr><br><?php
                      } 
                       
                     }
                   }
                   
                 }    
                 ?>
              
<?php
}
?>
              </div>
            </div>
          </div>  
        </div>
    </div>