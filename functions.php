<?php
require_once 'conexion.php';
require_once 'conexion_externa.php';

date_default_timezone_set('America/La_Paz');

/*function showAlertSuccessError($bandera, $url){
  if($bandera==true){
     echo "<script>
        alerts.showSwal('success-message','$url');
     </script>";
  }else{
     echo "<script>
        alerts.showSwal('error-message','$url');
     </script>";
  }
}*/


function callService($parametros, $url){
  $parametros=json_encode($parametros);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_POST, TRUE);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $remote_server_output = curl_exec ($ch);
  curl_close ($ch);
  
  return $remote_server_output;   
}

function nameMes($month){
  setlocale(LC_TIME, 'es_ES');
  $monthNum  = $month;
  $dateObj   = DateTime::createFromFormat('!m', $monthNum);
  $monthName = strftime('%B', $dateObj->getTimestamp());
  return $monthName;
}

function abrevMes($month){
  if($month==1){    return ("Ene");   }
  if($month==2){    return ("Feb");  }
  if($month==3){    return ("Mar");  }
  if($month==4){    return ("Abr");  }
  if($month==5){    return ("May");  }
  if($month==6){    return ("Jun");  } 
  if($month==7){    return ("Jul");  }
  if($month==8){    return ("Ago");  }
  if($month==9){    return ("Sep");  }
  if($month==10){    return ("Oct");  }         
  if($month==11){    return ("Nov");  }         
  if($month==12){    return ("Dic");  }             
}
function nombreMes($month){
  if($month==1){    return ("Enero");   }
  if($month==2){    return ("Febrero");  }
  if($month==3){    return ("Marzo");  }
  if($month==4){    return ("Abril");  }
  if($month==5){    return ("Mayo");  }
  if($month==6){    return ("Junio");  } 
  if($month==7){    return ("Julio");  }
  if($month==8){    return ("Agosto");  }
  if($month==9){    return ("Septiembre");  }
  if($month==10){    return ("Octubre");  }         
  if($month==11){    return ("Noviembre");  }         
  if($month==12){    return ("Diciembre");  }             
}

function nameGestion($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM gestiones where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}
function nameMoneda($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM monedas where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}
function abrevMoneda($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT abreviatura FROM monedas where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['abreviatura'];
   }
   return($nombreX);
}

function nameCuenta($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM plan_cuentas where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}
function nameCliente($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM clientes where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}

function nameCuentaAux($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM cuentas_auxiliares where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}
function obtieneNumeroCuenta($codigo){
   
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT numero FROM plan_cuentas where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX='';
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['numero'];
   }
   return($nombreX);
}

function obtieneNuevaCuenta($codigo){//ESTA FUNCION TRABAJA CON UNA CUENTA FORMATEADA CON PUNTOS
   $dbh = new Conexion();
   $nivelCuenta=buscarNivelCuenta($codigo);
   $cuentaSinFormato=str_replace(".","",$codigo);
   $nivelCuentaBuscado=$nivelCuenta+1;
   
   //echo "nivel cta: ".$nivelCuentaBuscado; 

   list($nivel1, $nivel2, $nivel3, $nivel4, $nivel5) = explode('.', $codigo);
   
   $stmt = $dbh->prepare("SELECT (max(numero))numero FROM plan_cuentas where cod_padre=:codigo");
   $stmt->bindParam(':codigo',$cuentaSinFormato);
   $stmt->execute();
   $cuentaHijoMaxima="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $cuentaHijoMaxima=$row['numero'];
   }
   //echo "max:".$cuentaHijoMaxima;
   //ACA SACAMOS EL NUMERO DEL NIVEL MAXIMO
   $numeroIncrementar=0;
   if($nivelCuentaBuscado==2){
      $numeroIncrementar=substr($cuentaHijoMaxima, 1,2);
   }
   if($nivelCuentaBuscado==3){
      $numeroIncrementar=substr($cuentaHijoMaxima, 3,2);
   }
   if($nivelCuentaBuscado==4){
      $numeroIncrementar=substr($cuentaHijoMaxima, 5,2);
   }
   if($nivelCuentaBuscado==5){
      $numeroIncrementar=substr($cuentaHijoMaxima, 7,3);
   }
   $numeroIncrementar=($numeroIncrementar*1)+1;

  $nuevaCuenta="";
  if($nivelCuentaBuscado==3){
    $numeroIncremetarConCeros = str_pad($numeroIncrementar, 2, "0", STR_PAD_LEFT);
    $nuevaCuenta=$nivel1.$nivel2.$numeroIncremetarConCeros."00"."000";
  }
  if($nivelCuentaBuscado==4){
    $numeroIncremetarConCeros = str_pad($numeroIncrementar, 2, "0", STR_PAD_LEFT);
    $nuevaCuenta=$nivel1.$nivel2.$nivel3.$numeroIncremetarConCeros."000";
  }
  if($nivelCuentaBuscado==5){
    $numeroIncremetarConCeros = str_pad($numeroIncrementar, 3, "0", STR_PAD_LEFT);
    $nuevaCuenta=$nivel1.$nivel2.$nivel3.$nivel4.$numeroIncremetarConCeros;
  }
  //echo $nuevaCuenta;
  return($nuevaCuenta);
}

function formateaPlanCuenta($cuenta, $nivel){
  $tabs="";
  for($i=1;$i<=$nivel;$i++){
    $tabs.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
  }
  $cuenta=$tabs.$cuenta;
  if($nivel==1){
    $cuenta="<span class='text-left font-weight-bold'>$cuenta</span>";
  }
  if($nivel==5){
    $cuenta="<span class='text-primary small'>$cuenta</span>";
  }
  return($cuenta);
}

function formateaPuntosPlanCuenta($cuenta){
  $nivel1=substr($cuenta, 0, 1);
  $nivel2=substr($cuenta, 1, 2);
  $nivel3=substr($cuenta, 3, 2);
  $nivel4=substr($cuenta, 5, 2);
  $nivel5=substr($cuenta, 7, 3);
  $cuentaNueva=$nivel1.".".$nivel2.".".$nivel3.".".$nivel4.".".$nivel5;
  return($cuentaNueva);
}

function buscarCuentaPadre($cuenta){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM plan_cuentas where numero='$cuenta'");
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}

function obtenerCuentaPadre($cuentaaux){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cod_cuenta FROM cuentas_auxiliares where codigo='$cuentaaux'");
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['cod_cuenta'];
   }
   return($nombreX);
}

function buscarNivelCuenta($cuenta){
  list($nivel1, $nivel2, $nivel3, $nivel4, $nivel5) = explode('.', $cuenta);
  $nivelCuenta=0;
  if($nivel5!="000"){
    $cuentaPadre=$nivel1.$nivel2.$nivel3.$nivel4."000";
    $cuentaBuscar=buscarCuentaPadre($cuentaPadre);
    if($cuentaBuscar!=""){
      $nivelCuenta=5;
    }
  }
  if($nivel5=="000" && $nivel4!="00"){
    $cuentaPadre=$nivel1.$nivel2.$nivel3."00"."000";
    $cuentaBuscar=buscarCuentaPadre($cuentaPadre);
    if($cuentaBuscar!=""){
      $nivelCuenta=4;
    } 
  }
  if($nivel5=="000" && $nivel4=="00" && $nivel3!="00"){
    $cuentaPadre=$nivel1.$nivel2."00"."00"."000";
    $cuentaBuscar=buscarCuentaPadre($cuentaPadre);
    if($cuentaBuscar!=""){
      $nivelCuenta=3;
    } 
  }
  if($nivel5=="000" && $nivel4=="00" && $nivel3=="00" && $nivel2!="00"){
    $cuentaPadre=$nivel1."00"."00"."00"."000";
    $cuentaBuscar=buscarCuentaPadre($cuentaPadre);
    if($cuentaBuscar!=""){
      $nivelCuenta=2;
    } 
  }
  return $nivelCuenta;
}


function obtenerCodigoComprobante(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from comprobantes c");
   $stmt->execute();
   $codigoComprobante=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoComprobante=$row['codigo'];
   }
   return($codigoComprobante);
}
function obtenerCodigoComprobanteExistente($cod_tipocomprobante,$nro_comprobante,$mes_comprobante,$unidad_cpte,$gestion_cpte){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT codigo from comprobantes where cod_tipocomprobante=$cod_tipocomprobante and numero=$nro_comprobante and MONTH(fecha)=$mes_comprobante and cod_unidadorganizacional=$unidad_cpte and cod_gestion=$gestion_cpte");
   $stmt->execute();
   $codigoComprobante=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoComprobante=$row['codigo'];
   }
   return($codigoComprobante);
}
function obtenerCodigoSimulacionServicioDetalle(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from simulaciones_serviciodetalle c");
   $stmt->execute();
   $codigoComprobante=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoComprobante=$row['codigo'];
   }
   return($codigoComprobante);
}
function generarNumeroCeros($n,$num){
 $nr =strlen($num);
 $nnum="";
 for ($i=0; $i < ($n-$nr); $i++) { 
      $nnum.='0';
   }
  return $nnum.$num;   
}
function obtenerCodigoComprobanteDetalle(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from comprobantes_detalle c");
   $stmt->execute();
   $codigoComprobanteDetalle=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoComprobanteDetalle=$row['codigo'];
   }
   return($codigoComprobanteDetalle);
}
function obtenerCodigoPlantillaGrupo(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from plantillas_gruposcosto c");
   $stmt->execute();
   $codigoPlantillaGrupo=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoPlantillaGrupo=$row['codigo'];
   }
   return($codigoPlantillaGrupo);
}
function obtenerCodigoPlantillaGrupoServicio(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from plantillas_gruposervicio c");
   $stmt->execute();
   $codigoPlantillaGrupo=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoPlantillaGrupo=$row['codigo'];
   }
   return($codigoPlantillaGrupo);
}
function obtenerCodigoPlantillaGrupoDetalle(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from plantillas_grupocostodetalle c");
   $stmt->execute();
   $codigoPlantillaGrupo=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoPlantillaGrupo=$row['codigo'];
   }
   return($codigoPlantillaGrupo);
}

function obtenerCodigoPlantillaGrupoDetalleServicio(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from plantillas_gruposerviciodetalle c");
   $stmt->execute();
   $codigoPlantillaGrupo=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoPlantillaGrupo=$row['codigo'];
   }
   return($codigoPlantillaGrupo);
}

function obtenerCodigoFacturaCompra(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from facturas_compra c");
   $stmt->execute();
   $codigoFacturaCompra=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoFacturaCompra=$row['codigo'];
   }
   return($codigoFacturaCompra);
}
function obtenerCodigoEstadosCuenta(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from estados_cuenta c");
   $stmt->execute();
   $codigoFacturaCompra=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoFacturaCompra=$row['codigo'];
   }
   return($codigoFacturaCompra);
}
function nameCargo($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM cargos where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}

function nameProveedor($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM af_proveedores where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}

function existeProveedor($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT codigo FROM af_proveedores where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX++;
   }
   return($nombreX);
}

function nameEntidad($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT abreviatura FROM entidades where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['abreviatura'];
   }
   return($nombreX);
}
function nameBancos($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT abreviatura FROM bancos where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['abreviatura'];
   }
   return($nombreX);
}
function nameLibretas($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM libretas_bancarias where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}
function nameEntidadUO($codigo){
   $dbh = new Conexion();
   $sql="SELECT e.nombre from entidades e, entidades_uo eu where e.codigo=eu.cod_entidad and eu.cod_uo=:codigo";
   //echo $sql;
   $stmt = $dbh->prepare($sql);
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}


function namePartidaPres($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM partidas_presupuestarias where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}

function nameArea($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM areas where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX='';
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}

function abrevArea($codigo){
   $dbh = new Conexion();
   $sql="SELECT abreviatura FROM areas where codigo in ($codigo)";
   $stmt = $dbh->prepare($sql);
   //echo $sql;
   $stmt->execute();
   $cadenaAreas="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $cadenaAreas=$cadenaAreas."-".$row['abreviatura'];
   }
   $cadenaAreas=substr($cadenaAreas, 1);
   return($cadenaAreas);
}
function abrevArea_solo($codigo){
   $dbh = new Conexion();
   $sql="SELECT abreviatura FROM areas where codigo in ($codigo)";
   $stmt = $dbh->prepare($sql);
   //echo $sql;
   $stmt->execute();
   $cadenaAreas="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $cadenaAreas=$row['abreviatura'];
   }
   return($cadenaAreas);
}

function namesArea($codigo){
  $dbh = new Conexion();
  $sqlAreas = "SELECT nombre FROM areas where codigo in ($codigo)";
  $stmtAreas = $dbh->prepare($sqlAreas);
  $stmtAreas->execute();
  $nombreArea="";
  while ($rowAreas = $stmtAreas->fetch(PDO::FETCH_ASSOC)) {
    $nombreArea.=$rowAreas['nombre']." - ";
  }
  return($nombreArea);
}

function namesPersonal($codigo){
  $dbh = new Conexion();
  $sqlPersonal = "SELECT CONCAT_WS(' ',paterno,materno,primer_nombre)as nombre FROM personal where codigo in ($codigo)";
  $stmtPersonal = $dbh->prepare($sqlPersonal);
  $stmtPersonal->execute();
  $nombrePersonal="";
  while ($rowPersonal = $stmtPersonal->fetch(PDO::FETCH_ASSOC)) {
    $nombrePersonal.=$rowPersonal['nombre']." - ";
  }
  return($nombrePersonal);
}

function namesUnidad($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM unidades_organizacionales where codigo in ($codigo)");
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX.=$row['nombre']." - ";
   }
   return($nombreX);
}

function nameUnidad($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM unidades_organizacionales where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}

function namePersonalCompleto($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT CONCAT_WS(' ',primer_nombre,materno,paterno)as nombre FROM personal where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}

function namePersonal($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT CONCAT_WS(' ',paterno,materno,primer_nombre)as nombre FROM personal where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}
function namePersonal_2($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT CONCAT_WS(' ',paterno,primer_nombre)as nombre FROM personal where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}

function namesDepreciacion($codigo){
 $dbh = new Conexion();
 $stmt = $dbh->prepare("SELECT nombre FROM depreciaciones where codigo in ($codigo)");
 $stmt->execute();
 $nombreX="";
 while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
   $nombreX.=$row['nombre']." - ";
 }
 return($nombreX);
}
function nameDepreciacion($codigo){
 $dbh = new Conexion();
 $stmt = $dbh->prepare("SELECT nombre FROM depreciaciones where codigo=$codigo");
 $stmt->execute();
 $nombreX="";
 while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
   $nombreX=$row['nombre'];
 }
 return($nombreX);
}
function abrevDepreciacion($codigo){
 $dbh = new Conexion();
 $stmt = $dbh->prepare("SELECT abreviatura FROM depreciaciones where codigo in ($codigo)");
 $stmt->execute();
 $nombreX="";
 while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
   $nombreX.=$row['abreviatura']." - ";
 }
 return($nombreX);
}
function abrevUnidad($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT abreviatura FROM unidades_organizacionales where codigo in ($codigo)");
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX.=$row['abreviatura']."-";
   }
   $nombreX=substr($nombreX, 0, -1);
   return($nombreX);
}
function abrevUnidad_solo($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT abreviatura FROM unidades_organizacionales where codigo in ($codigo)");
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['abreviatura'];
   }
   return($nombreX);
}

function abrevTipoCurso($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT abreviatura FROM tipos_cursos where codigo in ($codigo)");
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX.=$row['abreviatura']."-";
   }
   $nombreX=substr($nombreX, 0, -1);
   return($nombreX);
}
function abrevTipoCliente($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM clientes where codigo in ($codigo)");
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX.=$row['nombre']."-";
   }
   $nombreX=substr($nombreX, 0, -1);
   return($nombreX);
}

function buscarAreasAdicionales($cod_personal,$tipo){//1 codigos , 2 nombres
  $dbh = new Conexion();
  $sql="SELECT pa.cod_area, (select a.abreviatura from areas a where a.codigo=pa.cod_area)as nombre from personal_areas pa where pa.cod_personal='$cod_personal'";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $cadena="0";
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codAreaAdi=$row['cod_area'];
      $nombreAreaAdi=$row['nombre'];
      if($tipo==1){
        $cadena.=",".$codAreaAdi;
      }
      if($tipo==2){
        $cadena.=",".$nombreAreaAdi;
      }
  }
  return($cadena);  
}

function buscarUnidadesAdicionales($cod_personal,$tipo){//1 codigos , 2 nombres
  $dbh = new Conexion();
  $sql="SELECT pa.cod_unidad, (select a.abreviatura from unidades_organizacionales a where a.codigo=pa.cod_unidad)as nombre from personal_unidadesorganizacionales pa where pa.cod_personal='$cod_personal'";
  //echo $sql;
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $cadena="0";
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codAreaAdi=$row['cod_unidad'];
      $codUnidadHijos=buscarHijosUO($codAreaAdi);
      $nombreAreaAdi=$row['nombre'];
      if($tipo==1){
        $cadena.=",".$codUnidadHijos;
      }
      if($tipo==2){
        $cadena.=",".$nombreAreaAdi;
      }
  }
  return($cadena);  
}

function obtenerUnidadesReport($codigo){
  $dbh = new Conexion();
  $sql="";
  if($codigo=="0"){
    $sql="SELECT u.codigo from unidades_organizacionales u";
  }else{
    $sql="SELECT u.codigo from unidades_organizacionales u where u.codigo in ($codigo)";
  }
  //echo "codigo.".$codigo." ".$sql;
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $cadena="";
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigo=$row['codigo'];
      $cadena.=",".$codigo;
  }
  $cadena=substr($cadena, 1);
  return($cadena);    
}

function obtenerAreasReport($codigo){
  $dbh = new Conexion();
  $sql="";
  if($codigo=="0"){
    $sql="SELECT a.codigo from areas a";
  }else{
    $sql="SELECT a.codigo from areas a where a.codigo in ($codigo)";
  }
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $cadena="";
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigo=$row['codigo'];
      $cadena.=",".$codigo;
  }
  $cadena=substr($cadena, 1);
  return($cadena);    
}

function obtenerUFV($date){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT u.valor from ufvs u where u.fecha='$date'";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $valor="0";
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['valor'];
  }
  return($valor);  
}

function obtenerFondosReport($codigo){
  $dbh = new Conexion();
  $sql="";
  if($codigo=="0"){
    $sql="SELECT p.codigo, p.nombre from po_fondos p";
  }else{
    $sql="SELECT p.codigo, p.nombre from po_fondos p, unidades_organizacionales u where p.cod_unidadorganizacional=u.codigo and u.codigo in ($codigo)";
  }
  //echo $sql;
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $cadena="";
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigo=$row['codigo'];
      $cadena.=",".$codigo;
  }
  $cadena=substr($cadena, 1);
  return($cadena);    
}

function obtenerOrganismosReport($codigo){
  $dbh = new Conexion();
  $sql="";
  if($codigo=="0"){
    $sql="SELECT p.codigo, p.nombre from po_organismos p";
  }else{
    $sql="SELECT p.codigo, p.nombre from po_organismos p, areas a where p.cod_area=a.codigo and a.codigo in ($codigo)";
  }
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $cadena="";
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigo=$row['codigo'];
      $cadena.=",".$codigo;
  }
  $cadena=substr($cadena, 1);
  return($cadena);    
}

function buscarHijosUO($cod_unidad){
  $dbh = new Conexion();
  $sql="select u.cod_unidadorganizacionalhijo from unidadesorganizacionales_hijos u where u.cod_unidadorganizacional='$cod_unidad'";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $cadenaHijos=$cod_unidad;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codUnidadHijo=$row['cod_unidadorganizacionalhijo'];
      $cadenaHijos.=",".$codUnidadHijo;
  }
  return($cadenaHijos);  
}

//funcion nueva obtener moneda
function obtenerMoneda($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT m.codigo,m.nombre,m.cod_estadoreferencial,m.abreviatura from monedas m where m.cod_estadoreferencial=1 and m.codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}

//funcion nueva obtener detalle de comprobante
function obtenerComprobantesDet($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT d.cod_cuentaauxiliar,d.codigo as cod_det,d.cod_area,d.cod_unidadorganizacional,p.codigo,p.numero,p.nombre,d.glosa,d.debe,d.haber,a.abreviatura,p.cuenta_auxiliar,u.abreviatura as unidadAbrev FROM plan_cuentas p join comprobantes_detalle d on p.codigo=d.cod_cuenta join areas a on d.cod_area=a.codigo join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional where d.cod_comprobante=$codigo order by cod_det";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   // bindColumn
   //$stmt->bindColumn('cod_comprobante', $codigoC);
   return $stmt;
}
//funcion nueva obtener detalle de comprobante
function obtenerComprobantesDetImp($codigo){
   $dbh = new Conexion();
   
   $sql="";
//    $sql="SELECT d.codigo as cod_det,d.cod_area,d.cod_unidadorganizacional,p.codigo,p.numero,p.nombre,d.glosa,d.debe,d.haber,a.abreviatura,p.cuenta_auxiliar,u.abreviatura as unidadAbrev,(select 1 from comprobantes_detalle cdd where cdd.debe=0 and d.codigo=cdd.codigo) as haber_order 
// FROM plan_cuentas p join comprobantes_detalle d on p.codigo=d.cod_cuenta join areas a on d.cod_area=a.codigo join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional where d.cod_comprobante=$codigo order by haber_order, d.codigo";
   $sql="SELECT d.cod_cuentaauxiliar,d.codigo as cod_det,d.cod_area,d.cod_unidadorganizacional,p.codigo,p.numero,p.nombre,d.glosa,d.debe,d.haber,a.abreviatura,p.cuenta_auxiliar,u.abreviatura as unidadAbrev,(select 1 from comprobantes_detalle cdd where cdd.debe=0 and d.codigo=cdd.codigo) as haber_order, (select ca.nombre from cuentas_auxiliares ca where ca.codigo=d.cod_cuentaauxiliar)as nombrecuentaauxiliar
FROM plan_cuentas p join comprobantes_detalle d on p.codigo=d.cod_cuenta join areas a on d.cod_area=a.codigo join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional where d.cod_comprobante=$codigo order by d.codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   // bindColumn
   //$stmt->bindColumn('cod_comprobante', $codigoC);
   return $stmt;
}
function obtenerPlantillaCostoDatos($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT  * from plantillas_costo where codigo=$codigo order by codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   // bindColumn
   //$stmt->bindColumn('cod_comprobante', $codigoC);
   return $stmt;
}
function obtenerPlantillaServicioDatos($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT  * from plantillas_servicios where codigo=$codigo order by codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   // bindColumn
   //$stmt->bindColumn('cod_comprobante', $codigoC);
   return $stmt;
}

//funcion nueva obtener detalle de comprobante
function obtenerPlantillaCosto($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT  * from plantillas_gruposcosto where cod_plantillacosto=$codigo order by codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   // bindColumn
   //$stmt->bindColumn('cod_comprobante', $codigoC);
   return $stmt;
}
//funcion nueva obtener detalle de servicio plantilla
function obtenerPlantillaServicio($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT  * from plantillas_gruposervicio where cod_plantillaservicio=$codigo order by codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   // bindColumn
   //$stmt->bindColumn('cod_comprobante', $codigoC);
   return $stmt;
}
function obtenerPlantillaCostoAlumnos($codigo){
  $dbh = new Conexion();
   $valor=0;
   $sql="SELECT cantidad_alumnoslocal from plantillas_costo where codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cantidad_alumnoslocal'];
  }
  return $valor;
}
function obtenerPlantillaCostoUtilidad($codigo){
  $dbh = new Conexion();
   $valor=0;
   $sql="SELECT utilidad_minimalocal from plantillas_costo where codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['utilidad_minimalocal'];
  }
  return $valor;
}
function obtenerPlantillaCostoCursosMes($codigo){
  $dbh = new Conexion();
   $valor=0;
   $sql="SELECT cantidad_cursosmes from plantillas_costo where codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cantidad_cursosmes'];
  }
  return $valor;
}
//funcion nueva obtener tipo cambio monedas
function obtenerTipoCambio($codigo,$fi,$fa){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT id,cod_moneda,fecha,valor from tipo_cambiomonedas where cod_moneda=$codigo and fecha between '$fi' and '$fa' order by fecha";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   // bindColumn
   //$stmt->bindColumn('cod_comprobante', $codigoC);
   return $stmt;
}
//funcion nueva obtener tipo cambio monedas
function obtenerValorTipoCambio($codigo,$fecha){
   $dbh = new Conexion();
   $valor=0;
   $sql="SELECT valor from tipo_cambiomonedas where cod_moneda=$codigo and fecha = '$fecha'";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['valor'];
  }
  return $valor;
}

//funcion contar comprobanteDetalle
 function contarTipoCambio($codigo,$fi,$fa){
   $dbh = new Conexion();
   $sql="";
   $sql="select count(*) as total from tipo_cambiomonedas where cod_moneda=$codigo and fecha between '$fi' and '$fa'";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['total'];
  }
  return($valor);
  }

/*function reemplazarTildesUTF8($texto){
$chars = array("Ã¡", "Ã©", "Ã*","Ã³","Ãº","Ã","Ã‰","Ã","Ã“","Ãš","Ã±","Ã‘","Âº","Âª","Â¿");
$tildes= array("á", "é", "í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","º","ª","¿");
return str_replace($chars, $tildes, $texto);
}*/
//funcion para descargar con dompdf

  
function obtenerComprobante($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT c.codigo,c.cod_gestion,u.abreviatura,c.fecha,c.cod_tipocomprobante,c.numero,c.glosa from comprobantes c join unidades_organizacionales u on c.cod_unidadorganizacional=u.codigo where c.codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
//funcion contar comprobanteDetalle
 function contarComprobantesDetalle($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="select count(*) as total from comprobantes_detalle where cod_comprobante=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
  }
  //funcion contar plantillaCosto
 function contarPlantillaCosto($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="select count(*) as total from plantillas_gruposcosto where cod_plantillacosto=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
  }
   //funcion contar plantillaServicio
 function contarPlantillaServicio($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="select count(*) as total from plantillas_gruposervicio where cod_plantillaservicio=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
  }
    //funcion nueva obtener factura de comprobantesdetalle
function obtenerFacturasCompro($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT codigo as cod_det,nit,nro_factura,fecha,razon_social,importe,exento,nro_autorizacion,codigo_control from facturas_compra  where cod_comprobantedetalle=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerEstadosCuenta($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT codigo as cod_det,cod_plancuenta,monto,cod_proveedor,fecha from estados_cuenta  where cod_comprobantedetalle=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
    //funcion nueva obtener factura de comprobantesdetalle
function obtenerPlantillasDetalle($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT * from plantillas_grupocostodetalle  where cod_plantillagrupocosto=$codigo order by codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerPlantillasDetalleServicio($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT * from plantillas_gruposerviciodetalle  where cod_plantillagruposervicio=$codigo order by codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
  //funcion contar facturas_compra
 function contarFacturasCompra($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="select count(*) as total from facturas_compra where cod_comprobantedetalle=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   $stmt->bindColumn('total', $contador);
   while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    $cont1=$contador;
   }
   return $cont1;
  }
  function contarEstadosCuenta($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="select count(*) as total from estados_cuenta where cod_comprobantedetalle=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   $stmt->bindColumn('total', $contador);
   while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    $cont1=$contador;
   }
   return $cont1;
  } 
   //funcion contar facturas_compra
 function contarPlantillaCostoDetalle($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="select count(*) as total from plantillas_grupocostodetalle where cod_plantillagrupocosto=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   $stmt->bindColumn('total', $contador);
   while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    $cont1=$contador;
   }
   return $cont1;
  } 
  function contarPlantillaServicioDetalle($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="select count(*) as total from plantillas_gruposerviciodetalle where cod_plantillagruposervicio=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   $stmt->bindColumn('total', $contador);
   while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    $cont1=$contador;
   }
   return $cont1;
  } 
//funcion nueva obtener detalle de comprobante
function obtenerPlantilla($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT * FROM plantillas_comprobante where codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
  function editarComprobanteDetalle($codComp,$title,$cant1,$cant2,$stmt,$tabla,$cabecera,$valores,$fact){
    $dbh = new Conexion();
    $i=0;$cab=cantidadF($cabecera);$codigo=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigo=$row['cod_det'];
      if($i<$cant2){
        //update
        $sets="";
        for ($k=0; $k < $cab; $k++) { 
          if($k==($cab-1)){
            $sets.=$cabecera[$k]."='".$valores[$i][$k]."'";
          }else{
            $sets.=$cabecera[$k]."='".$valores[$i][$k]."', ";
          }
        } 
         $query="UPDATE $tabla set $sets where codigo=$codigo";       
      }else{
        //delete
        $query="DELETE from $tabla where codigo=$codigo";
      }
      $crud = $dbh->prepare($query);
      $crud->execute();
      if($fact!=null){
        //recoger valores del array en un array general para enviar los datos
        for($h=0;$h<cantidadF($fact[$i]);$h++){
          $valFac[$h][0]=$fact[$i][$h]->nit;
          $valFac[$h][1]=$fact[$i][$h]->nroFac;

          $fecha=$fact[$i][$h]->fechaFac;
          $porciones = explode("/", $fecha);
          $fechaFac=$porciones[2]."-".$porciones[1]."-".$porciones[0];

          $valFac[$h][2]=$fechaFac;
          $valFac[$h][3]=$fact[$i][$h]->razonFac;
          $valFac[$h][4]=$fact[$i][$h]->impFac;
          $valFac[$h][5]=$fact[$i][$h]->exeFac;
          $valFac[$h][6]=$fact[$i][$h]->autFac;
          $valFac[$h][7]=$fact[$i][$h]->conFac;
          $valFac[$h][8]=$fact[$i][$h]->exeFac;
          $valFac[$h][9]=$fact[$i][$h]->iceFac;
          $valFac[$h][10]=$fact[$i][$h]->tazaFac;
          $valFac[$h][11]=$fact[$i][$h]->tipoFac;
        }
        if(cantidadF($fact[$i])>0){
         $cabeceraFac[0]="nit";$cabeceraFac[1]="nro_factura";$cabeceraFac[2]="fecha";$cabeceraFac[3]="razon_social";$cabeceraFac[4]="importe";$cabeceraFac[5]="exento";$cabeceraFac[6]="nro_autorizacion";$cabeceraFac[7]="codigo_control";
         if($title=="cod_solicitudrecurso"){
            $sql = obtenerFacturasSoli($codigo); 
            editarComprobanteDetalle($codigo,'cod_solicitudrecursodetalle',contarFacturasSoli($codigo),cantidadF($fact[$i]),$sql,'facturas_compra',$cabeceraFac,$valFac,null);
         }else{
           $sql = obtenerFacturasCompro($codigo); 
           editarComprobanteDetalle($codigo,'cod_comprobantedetalle',contarFacturasCompra($codigo),cantidadF($fact[$i]),$sql,'facturas_compra',$cabeceraFac,$valFac,null);
         }  
        }              
      }
      $i++;
    }
    if($cant2>$cant1){
       for ($j=$i; $j < $cant2; $j++) { 
         //insert
        $into=$title.",";$values=$codComp.",";
        for ($l=0; $l < $cab; $l++) { 
          if($l==($cab-1)){
          $into.=$cabecera[$l]."";
          $values.="'".$valores[$j][$l]."'";
          }else{
           $into.=$cabecera[$l].",";
          $values.="'".$valores[$j][$l]."',";
          }          
        }
        if($fact==null){
          $codFacturaCompra=obtenerCodigoFacturaCompra();
          $dbh = new Conexion();
          $query2="INSERT INTO $tabla (codigo, ".$into.") values ($codFacturaCompra, ".$values.")";//
        }else{
          if($title=="cod_solicitudrecurso"){
            $codComprobanteDetalle=obtenerCodigoSolicitudDetalle();
          }else{
            $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
          }    
          $dbh = new Conexion();
          $query2="INSERT INTO $tabla (codigo, ".$into.") values ($codComprobanteDetalle, ".$values.")";

          //recoger valores del array en un array general para enviar los datos
        for($h=0;$h<cantidadF($fact[$j]);$h++){
          $valFac[$h][0]=$fact[$j][$h]->nit;
          $valFac[$h][1]=$fact[$j][$h]->nroFac;

          $fecha=$fact[$j][$h]->fechaFac;
          $porciones = explode("/", $fecha);
          $fechaFac=$porciones[2]."-".$porciones[1]."-".$porciones[0];

          $valFac[$h][2]=$fechaFac;
          $valFac[$h][3]=$fact[$j][$h]->razonFac;
          $valFac[$h][4]=$fact[$j][$h]->impFac;
          $valFac[$h][5]=$fact[$j][$h]->exeFac;
          $valFac[$h][6]=$fact[$j][$h]->autFac;
          $valFac[$h][7]=$fact[$j][$h]->conFac;
          $valFac[$h][8]=$fact[$j][$h]->exeFac;
          $valFac[$h][9]=$fact[$j][$h]->iceFac;
          $valFac[$h][10]=$fact[$j][$h]->tazaFac;
          $valFac[$h][11]=$fact[$j][$h]->tipoFac;
        }
        if(cantidadF($fact[$j])>0){
        $cabeceraFac[0]="nit";$cabeceraFac[1]="nro_factura";$cabeceraFac[2]="fecha";$cabeceraFac[3]="razon_social";$cabeceraFac[4]="importe";$cabeceraFac[5]="exento";$cabeceraFac[6]="nro_autorizacion";$cabeceraFac[7]="codigo_control";
          if($title=="cod_solicitudrecurso"){
           $sql = obtenerFacturasSoli($codComprobanteDetalle); 
           editarComprobanteDetalle($codComprobanteDetalle,'cod_solicitudrecursodetalle',contarFacturasSoli($codComprobanteDetalle),cantidadF($fact[$j]),$sql,'facturas_compra',$cabeceraFac,$valFac,null);
          }else{
           $sql = obtenerFacturasCompro($codComprobanteDetalle); 
           editarComprobanteDetalle($codComprobanteDetalle,'cod_comprobantedetalle',contarFacturasCompra($codComprobanteDetalle),cantidadF($fact[$j]),$sql,'facturas_compra',$cabeceraFac,$valFac,null);
          }    
          
         }
        }
        $crud2 = $dbh->prepare($query2);
        $crud2->execute();
       }
    }
  }
  function editarComprobanteDetalleCompleto($codComp,$title,$cant1,$cant2,$stmt,$tabla,$cabecera,$valores,$fact,$estados){
    $dbh = new Conexion();
    $i=0;$cab=cantidadF($cabecera);$codigo=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigo=$row['cod_det'];
      if($i<$cant2){
        //update
        $sets="";
        for ($k=0; $k < $cab; $k++) { 
          if($k==($cab-1)){
            $sets.=$cabecera[$k]."='".$valores[$i][$k]."'";
          }else{
            $sets.=$cabecera[$k]."='".$valores[$i][$k]."', ";
          }
        } 
         $query="UPDATE $tabla set $sets where codigo=$codigo";       
      }else{
        //delete
        $query="DELETE from $tabla where codigo=$codigo";
        $queryP="DELETE from estados_cuenta where cod_comprobantedetalle=$codigo";
        $crudP = $dbh->prepare($queryP);
        $crudP->execute();
      }
      $crud = $dbh->prepare($query);
      $crud->execute();
      if($fact!=null){
        //recoger valores del array en un array general para enviar los datos
        for($h=0;$h<cantidadF($fact[$i]);$h++){
          $valFac[$h][0]=$fact[$i][$h]->nit;
          $valFac[$h][1]=$fact[$i][$h]->nroFac;

          $fecha=$fact[$i][$h]->fechaFac;
          $porciones = explode("/", $fecha);
          $fechaFac=$porciones[2]."-".$porciones[1]."-".$porciones[0];

          $valFac[$h][2]=$fechaFac;
          $valFac[$h][3]=$fact[$i][$h]->razonFac;
          $valFac[$h][4]=$fact[$i][$h]->impFac;
          $valFac[$h][5]=$fact[$i][$h]->exeFac;
          $valFac[$h][6]=$fact[$i][$h]->autFac;
          $valFac[$h][7]=$fact[$i][$h]->conFac;
        }
        if(cantidadF($fact[$i])>0){
         $cabeceraFac[0]="nit";$cabeceraFac[1]="nro_factura";$cabeceraFac[2]="fecha";$cabeceraFac[3]="razon_social";$cabeceraFac[4]="importe";$cabeceraFac[5]="exento";$cabeceraFac[6]="nro_autorizacion";$cabeceraFac[7]="codigo_control";
         if($title=="cod_solicitudrecurso"){
            $sql = obtenerFacturasSoli($codigo); 
            editarComprobanteDetalleCompleto($codigo,'cod_solicitudrecursodetalle',contarFacturasSoli($codigo),cantidadF($fact[$i]),$sql,'facturas_compra',$cabeceraFac,$valFac,null,null);
         }else{
           $sql = obtenerFacturasCompro($codigo); 
           editarComprobanteDetalleCompleto($codigo,'cod_comprobantedetalle',contarFacturasCompra($codigo),cantidadF($fact[$i]),$sql,'facturas_compra',$cabeceraFac,$valFac,null,null);
         }  
        }
        //estados de cuenta
        if($estados!=null){
         for($h=0;$h<cantidadF($estados[$i]);$h++){
          $fecha=date("Y-m-d H:i:s");
          $valEst[$h][0]=$estados[$i][$h]->cod_plancuenta;
          $valEst[$h][1]=$estados[$i][$h]->monto;
          $valEst[$h][2]=$estados[$i][$h]->cod_proveedor;
          $valEst[$h][3]=$fecha;
          $valEst[$h][4]=$estados[$i][$h]->cod_comprobantedetalle;
         }
           if(cantidadF($estados[$i])>0){
              $cabeceraEst[0]="cod_plancuenta";$cabeceraEst[1]="monto";$cabeceraEst[2]="cod_proveedor";$cabeceraEst[3]="fecha";$cabeceraEst[4]="cod_comprobantedetalleorigen";
              $sql = obtenerEstadosCuenta($codigo); 
              editarComprobanteDetalleCompleto($codigo,'cod_comprobantedetalle',contarEstadosCuenta($codigo),cantidadF($estados[$i]),$sql,'estados_cuenta',$cabeceraEst,$valEst,null,null); 
           }
        }

      }//fin de if ($fact != null)
      $i++;
    }
    if($cant2>$cant1){
       for ($j=$i; $j < $cant2; $j++) { 
         //insert
        $into=$title.",";$values=$codComp.",";
        for ($l=0; $l < $cab; $l++) { 
          if($l==($cab-1)){
          $into.=$cabecera[$l]."";
          $values.="'".$valores[$j][$l]."'";
          }else{
           $into.=$cabecera[$l].",";
          $values.="'".$valores[$j][$l]."',";
          }          
        }
        if($fact==null||$estados==null){
          if($fact==null){
            $codFacturaCompra=obtenerCodigoFacturaCompra();
          }else{
            $codFacturaCompra=obtenerCodigoEstadosCuenta();
          }
            $dbh = new Conexion();
            $query2="INSERT INTO $tabla (codigo, ".$into.") values ($codFacturaCompra, ".$values.")";
        }else{
          if($title=="cod_solicitudrecurso"){
            $codComprobanteDetalle=obtenerCodigoSolicitudDetalle();
          }else{
            $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
          }    
          $dbh = new Conexion();
          $query2="INSERT INTO $tabla (codigo, ".$into.") values ($codComprobanteDetalle, ".$values.")";

          //recoger valores del array en un array general para enviar los datos
        for($h=0;$h<cantidadF($fact[$j]);$h++){
          $valFac[$h][0]=$fact[$j][$h]->nit;
          $valFac[$h][1]=$fact[$j][$h]->nroFac;

          $fecha=$fact[$j][$h]->fechaFac;
          $porciones = explode("/", $fecha);
          $fechaFac=$porciones[2]."-".$porciones[1]."-".$porciones[0];

          $valFac[$h][2]=$fechaFac;
          $valFac[$h][3]=$fact[$j][$h]->razonFac;
          $valFac[$h][4]=$fact[$j][$h]->impFac;
          $valFac[$h][5]=$fact[$j][$h]->exeFac;
          $valFac[$h][6]=$fact[$j][$h]->autFac;
          $valFac[$h][7]=$fact[$j][$h]->conFac;
        }
        if(cantidadF($fact[$j])>0){
        $cabeceraFac[0]="nit";$cabeceraFac[1]="nro_factura";$cabeceraFac[2]="fecha";$cabeceraFac[3]="razon_social";$cabeceraFac[4]="importe";$cabeceraFac[5]="exento";$cabeceraFac[6]="nro_autorizacion";$cabeceraFac[7]="codigo_control";
          if($title=="cod_solicitudrecurso"){
           $sql = obtenerFacturasSoli($codComprobanteDetalle); 
           editarComprobanteDetalle($codComprobanteDetalle,'cod_solicitudrecursodetalle',contarFacturasSoli($codComprobanteDetalle),cantidadF($fact[$j]),$sql,'facturas_compra',$cabeceraFac,$valFac,null);
          }else{
           $sql = obtenerFacturasCompro($codComprobanteDetalle); 
           editarComprobanteDetalle($codComprobanteDetalle,'cod_comprobantedetalle',contarFacturasCompra($codComprobanteDetalle),cantidadF($fact[$j]),$sql,'facturas_compra',$cabeceraFac,$valFac,null);
          }    
         }
         // estados de cuenta items nuevos
         for($h=0;$h<cantidadF($estados[$j]);$h++){
          $fecha=date("Y-m-d H:i:s");
          $valEst[$h][0]=$estados[$j][$h]->cod_plancuenta;
          $valEst[$h][1]=$estados[$j][$h]->monto;
          $valEst[$h][2]=$estados[$j][$h]->cod_proveedor;
          $valEst[$h][3]=$fecha;
          $valEst[$h][4]=$estados[$j][$h]->cod_comprobantedetalle;
         }
           if(cantidadF($estados[$j])>0){
              $cabeceraEst[0]="cod_plancuenta";$cabeceraEst[1]="monto";$cabeceraEst[2]="cod_proveedor";$cabeceraEst[3]="fecha";$cabeceraEst[4]="cod_comprobantedetalleorigen";
              $sql = obtenerEstadosCuenta($codComprobanteDetalle); 
              echo editarComprobanteDetalleCompleto($codComprobanteDetalle,'cod_comprobantedetalle',contarEstadosCuenta($codComprobanteDetalle),cantidadF($estados[$j]),$sql,'estados_cuenta',$cabeceraEst,$valEst,null,null); 
           }
        }
        $crud2 = $dbh->prepare($query2);
        $crud2->execute();
       }
    }
  }
  /**
 * Funcion que muestra la estructura de carpetas a partir de la ruta dada.
 */
function editarPlantillaCosto($codComp,$title,$cant1,$cant2,$stmt,$tabla,$cabecera,$valores,$fact){
    $dbh = new Conexion();
    $i=0;$cab=cantidadF($cabecera);$codigo=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigo=$row['codigo'];
      if($i<$cant2){
        //update
        $sets="";
        for ($k=0; $k < $cab; $k++) { 
          if($k==($cab-1)){
            $sets.=$cabecera[$k]."='".$valores[$i][$k]."'";
          }else{
            $sets.=$cabecera[$k]."='".$valores[$i][$k]."', ";
          }
        } 
         $query="UPDATE $tabla set $sets where codigo=$codigo";       
      }else{
        //delete
        $query="DELETE from $tabla where codigo=$codigo";
      }
      $crud = $dbh->prepare($query);
      $crud->execute();
      if($fact!=null){
        //recoger valores del array en un array general para enviar los datos
        for($h=0;$h<cantidadF($fact[$i]);$h++){
          $valFac[$h][0]=$fact[$i][$h]->codigo_cuenta;
          $valFac[$h][1]=$fact[$i][$h]->tipo;
          $valFac[$h][2]=$fact[$i][$h]->monto_i;
          $valFac[$h][3]=$fact[$i][$h]->monto_fi;
          $valFac[$h][4]=$fact[$i][$h]->monto_cal;
        }
        $sql = obtenerPlantillasDetalle($codigo);
        $cabeceraFac[0]="cod_partidapresupuestaria";$cabeceraFac[1]="tipo_calculo";$cabeceraFac[2]="monto_local";$cabeceraFac[3]="monto_externo";$cabeceraFac[4]="monto_calculado";
        editarPlantillaCosto($codigo,'cod_plantillagrupocosto',contarPlantillaCostoDetalle($codigo),cantidadF($fact[$i]),$sql,'plantillas_grupocostodetalle',$cabeceraFac,$valFac,null);
      }
      $i++;
    }
    if($cant2>$cant1){
       for ($j=$i; $j < $cant2; $j++) { 
         //insert
        $into=$title.",";$values=$codComp.",";
        for ($l=0; $l < $cab; $l++) { 
          if($l==($cab-1)){
          $into.=$cabecera[$l]."";
          $values.="'".$valores[$j][$l]."'";
          }else{
           $into.=$cabecera[$l].",";
          $values.="'".$valores[$j][$l]."',";
          }          
        }
        if($fact==null){
          $codPlantillaDetalle=obtenerCodigoPlantillaGrupoDetalle();
          $dbh = new Conexion();
          $query2="INSERT INTO $tabla (codigo, ".$into.") values ($codPlantillaDetalle, ".$values.")";
        }else{
          $codPlantillaGrupo=obtenerCodigoPlantillaGrupo();
          $dbh = new Conexion();
          $query2="INSERT INTO $tabla (codigo, ".$into.") values ($codPlantillaGrupo, ".$values.")";
          //recoger valores del array en un array general para enviar los datos
        for($r=0;$r<cantidadF($fact[$j]);$r++){
          $valFac[$r][0]=$fact[$j][$r]->codigo_cuenta;  
          $valFac[$r][1]=$fact[$j][$r]->tipo;
          $valFac[$r][2]=$fact[$j][$r]->monto_i;
          $valFac[$r][3]=$fact[$j][$r]->monto_fi;
          $valFac[$r][4]=$fact[$j][$r]->monto_cal;
        }
        $sql = obtenerPlantillasDetalle($codPlantillaGrupo);
        $cabeceraFac[0]="cod_partidapresupuestaria";$cabeceraFac[1]="tipo_calculo";$cabeceraFac[2]="monto_local";$cabeceraFac[3]="monto_externo";$cabeceraFac[4]="monto_calculado";
        editarPlantillaCosto($codPlantillaGrupo,'cod_plantillagrupocosto',contarPlantillaCostoDetalle($codPlantillaGrupo),cantidadF($fact[$j]),$sql,'plantillas_grupocostodetalle',$cabeceraFac,$valFac,null);
        }
       
        $crud2 = $dbh->prepare($query2);
        $crud2->execute();
       }
    }
  }

//plantillas SERVICIOS
  function editarPlantillaServicio($codComp,$title,$cant1,$cant2,$stmt,$tabla,$cabecera,$valores,$fact){
    $dbh = new Conexion();
    $i=0;$cab=cantidadF($cabecera);$codigo=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigo=$row['codigo'];
      if($i<$cant2){
        //update
        $sets="";
        for ($k=0; $k < $cab; $k++) { 
          if($k==($cab-1)){
            $sets.=$cabecera[$k]."='".$valores[$i][$k]."'";
          }else{
            $sets.=$cabecera[$k]."='".$valores[$i][$k]."', ";
          }
        } 
         $query="UPDATE $tabla set $sets where codigo=$codigo";       
      }else{
        //delete
        $query="DELETE from $tabla where codigo=$codigo";
      }
      $crud = $dbh->prepare($query);
      $crud->execute();
      if($fact!=null){
        //recoger valores del array en un array general para enviar los datos
        for($h=0;$h<cantidadF($fact[$i]);$h++){
          $valFac[$h][0]=$fact[$i][$h]->codigo_cuenta;
          $valFac[$h][1]=$fact[$i][$h]->tipo;
          $valFac[$h][2]=$fact[$i][$h]->monto_i;
          $valFac[$h][3]=$fact[$i][$h]->monto_fi;
          $valFac[$h][4]=$fact[$i][$h]->monto_cal;
        }
        $sql = obtenerPlantillasDetalleServicio($codigo);
        $cabeceraFac[0]="cod_partidapresupuestaria";$cabeceraFac[1]="tipo_calculo";$cabeceraFac[2]="monto_local";$cabeceraFac[3]="monto_externo";$cabeceraFac[4]="monto_calculado";
        editarPlantillaServicio($codigo,'cod_plantillagruposervicio',contarPlantillaServicioDetalle($codigo),cantidadF($fact[$i]),$sql,'plantillas_gruposerviciodetalle',$cabeceraFac,$valFac,null);
      }
      $i++;
    }
    if($cant2>$cant1){
       for ($j=$i; $j < $cant2; $j++) { 
         //insert
        $into=$title.",";$values=$codComp.",";
        for ($l=0; $l < $cab; $l++) { 
          if($l==($cab-1)){
          $into.=$cabecera[$l]."";
          $values.="'".$valores[$j][$l]."'";
          }else{
           $into.=$cabecera[$l].",";
          $values.="'".$valores[$j][$l]."',";
          }          
        }
        if($fact==null){
          $codPlantillaDetalle=obtenerCodigoPlantillaGrupoDetalleServicio();
          $dbh = new Conexion();
          $query2="INSERT INTO $tabla (codigo, ".$into.") values ($codPlantillaDetalle, ".$values.")";
        }else{
          $codPlantillaGrupo=obtenerCodigoPlantillaGrupoServicio();
          $dbh = new Conexion();
          $query2="INSERT INTO $tabla (codigo, ".$into.") values ($codPlantillaGrupo, ".$values.")";
          //recoger valores del array en un array general para enviar los datos
        for($r=0;$r<cantidadF($fact[$j]);$r++){
          $valFac[$r][0]=$fact[$j][$r]->codigo_cuenta;  
          $valFac[$r][1]=$fact[$j][$r]->tipo;
          $valFac[$r][2]=$fact[$j][$r]->monto_i;
          $valFac[$r][3]=$fact[$j][$r]->monto_fi;
          $valFac[$r][4]=$fact[$j][$r]->monto_cal;
        }
        $sql = obtenerPlantillasDetalleServicio($codPlantillaGrupo);
        $cabeceraFac[0]="cod_partidapresupuestaria";$cabeceraFac[1]="tipo_calculo";$cabeceraFac[2]="monto_local";$cabeceraFac[3]="monto_externo";$cabeceraFac[4]="monto_calculado";
        editarPlantillaServicio($codPlantillaGrupo,'cod_plantillagruposervicio',contarPlantillaServicioDetalle($codPlantillaGrupo),cantidadF($fact[$j]),$sql,'plantillas_gruposerviciodetalle',$cabeceraFac,$valFac,null);
        }
       
        $crud2 = $dbh->prepare($query2);
        $crud2->execute();
       }
    }
  }

function obtenerDirectorios($ruta){
    // Se comprueba que realmente sea la ruta de un directorio
    if (is_dir($ruta)){
        // Abre un gestor de directorios para la ruta indicada
        $gestor = opendir($ruta);
        echo "<div>";

        // Recorre todos los elementos del directorio
        while (($archivo = readdir($gestor)) !== false)  {
                
            $ruta_completa = $ruta . "/" . $archivo;

            // Se muestran todos los archivos y carpetas excepto "." y ".."
            if ($archivo != "." && $archivo != "..") {
                // Si es un directorio se recorre recursivamente
                if (is_dir($ruta_completa)) {
                  echo "<div class='btn-group'><a class='btn btn-md btn-warning btn-block' href='".$ruta_completa."' target='_blank'>" . $archivo . "</a><a class='btn btn-md btn-default' href='".$ruta_completa."' download='".$archivo."'><i class='material-icons'>vertical_align_bottom</i></a></div>";
                    obtenerDirectorios($ruta_completa);
                } else {
                  echo "<div class='btn-group'><a class='btn btn-md btn-warning btn-block' href='".$ruta_completa."' target='_blank'>" . $archivo . "</a><a class='btn btn-md btn-default' href='".$ruta_completa."' download='".$archivo."'><i class='material-icons'>vertical_align_bottom</i></a></div>";
                }
            }
        }
        
        // Cierra el gestor de directorios
        closedir($gestor);
        echo "</div>";
    } else {
        echo "No tiene archivos adjuntos";
    }
}
function obtenerDirectoriosSol($ruta){
    // Se comprueba que realmente sea la ruta de un directorio
    if (is_dir($ruta)){
        // Abre un gestor de directorios para la ruta indicada
        $gestor = opendir($ruta);
        echo "<div>";

        // Recorre todos los elementos del directorio
        while (($archivo = readdir($gestor)) !== false)  {
                
            $ruta_completa = $ruta . "/" . $archivo;

            // Se muestran todos los archivos y carpetas excepto "." y ".."
            if ($archivo != "." && $archivo != "..") {
                // Si es un directorio se recorre recursivamente
                if (is_dir($ruta_completa)) {
                 
                } else {
                  $archivoNombre=$archivo;
                  if(strlen($archivo)>15){
                    $archivoNombre=substr($archivo,0,15)."..."; 
                  }
                  echo "<div class='btn-group'><a class='btn btn-sm btn-info btn-block' href='".$ruta_completa."' target='_blank'>" . $archivoNombre . "</a><a class='btn btn-sm btn-default' href='".$ruta_completa."' download='".$archivo."'><i class='material-icons'>vertical_align_bottom</i></a><a class='btn btn-sm btn-primary' href='#' onclick='vistaPreviaArchivoSol(\"".$ruta_completa."\",\"".$archivo."\"); return false;'><i class='material-icons'>remove_red_eye</i></a></div>";
                }
            }
        }
        
        // Cierra el gestor de directorios
        closedir($gestor);
        echo "</div>";
    } else {
        echo "No tiene archivos adjuntos";
    }
}
function obtenerDirectoriosSol_solfac($ruta,$url_archivo){
    $ruta2=$ruta."/";    
    $nombre_archivo_array=explode($ruta2, $url_archivo);    
    $nombre_archivo=$nombre_archivo_array[1];    
    // Se comprueba que realmente sea la ruta de un directorio
    if (is_dir($ruta)){
        // Abre un gestor de directorios para la ruta indicada
        $gestor = opendir($ruta);
        echo "<div>";

        // Recorre todos los elementos del directorio
        while (($archivo = readdir($gestor)) !== false)  {
                
            $ruta_completa = $ruta . "/" . $archivo;
            if($ruta_completa==$url_archivo){
              // Se muestran todos los archivos y carpetas excepto "." y ".."
              if ($archivo != "." && $archivo != "..") {
                  // Si es un directorio se recorre recursivamente
                  if (is_dir($ruta_completa)) {
                   
                  } else {
                    echo "<div class='btn-group'><a class='btn btn-sm btn-info btn-block' href='ifinanciero/".$ruta_completa."' target='_blank'>" . $archivo . "</a><a class='btn btn-sm btn-default' href='ifinanciero/".$ruta_completa."' download='ifinanciero/".$archivo."'><i class='material-icons'>vertical_align_bottom</i></a></div>";
                  }
              }
            }          
        }
        
        // Cierra el gestor de directorios
        closedir($gestor);
        echo "</div>";
    } else {
        echo "No tiene archivos adjuntos";
    }
}

function obtenerCodigoPlanCosto(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from plantillas_costo c");
   $stmt->execute();
   $codigoComprobante=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoComprobante=$row['codigo'];
   }
   return($codigoComprobante);
}
function obtenerCodigoPlanServ(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from plantillas_servicios c");
   $stmt->execute();
   $codigoPlan=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoPlan=$row['codigo'];
   }
   return($codigoPlan);
}
function obtenerCodigoSimCosto(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from simulaciones_costos c");
   $stmt->execute();
   $codigoComprobante=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoComprobante=$row['codigo'];
   }
   return($codigoComprobante);
}
function obtenerCodigoSimServicio(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from simulaciones_servicios c");
   $stmt->execute();
   $codigoComprobante=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoComprobante=$row['codigo'];
   }
   return($codigoComprobante);
}
function obtenerMontoPorCuenta($numero,$unidad,$area,$fecha){
   $fecha_i=$fecha."-01-01";
   $fecha_f=$fecha."-12-31";
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT sum(monto) as monto from po_mayores where cuenta='$numero' and fondo='$unidad' and organismo='$area' and fecha between '$fecha_i' and '$fecha_f'");
   $stmt->bindParam(':monto',$codigo);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['monto'];
   }
   return($nombreX);
}
 //funcion para calcular costos
  function calcularCostosPresupuestarios($id,$unidad,$area,$fecha){
     $sql="SELECT p.cod_partidapresupuestaria,p.cod_cuenta,c.numero FROM partidaspresupuestarias_cuentas p join plan_cuentas c on p.cod_cuenta=c.codigo where p.cod_partidapresupuestaria=$id";

     //echo $sql;

     $dbh = new Conexion(); 
     $stmt = $dbh->prepare($sql);
     $stmt->execute();
     $sum=0;
     $mes=date("m");
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      //$sum=33333;
      $numero=trim($row['numero']);
      $cuenta=$row['cod_cuenta'];
      $tipoSim=obtenerValorConfiguracion(13);
      $tipoPresupuesto=obtenerValorConfiguracion(52);
      if($tipoPresupuesto!=1){
        $unidad=0;
      }
      if($tipoPresupuesto!=1){
       $saux= ejecutadoEgresosMes($unidad, $fecha, 12, $area, 1, $numero);
       $saux=$saux/12;
       $sum+=$saux; 
      }else{
       $sum+= ejecutadoEgresosMes($unidad, $fecha, $mes, $area, 0, $numero);
      }
      
      //$sum+=(float)obtenerMontoPorCuenta($numero,$unidad,$area,$fecha);      
    }
    
      $valor=obtenerValorConfiguracion(6);
      return redondearDecimal($sum/(int)$valor);
  }
  function calcularCostosPresupuestariosValor($id,$unidad,$area,$fecha,$valor){
     $sql="SELECT p.cod_partidapresupuestaria,p.cod_cuenta,c.numero FROM partidaspresupuestarias_cuentas p join plan_cuentas c on p.cod_cuenta=c.codigo where p.cod_partidapresupuestaria=$id";

     //echo $sql;

     $dbh = new Conexion(); 
     $stmt = $dbh->prepare($sql);
     $stmt->execute();
     $sum=0;
     $mes=date("m");
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      //$sum=33333;
      $numero=trim($row['numero']);
      $cuenta=$row['cod_cuenta'];
      $tipoSim=obtenerValorConfiguracion(13);
      $tipoPresupuesto=obtenerValorConfiguracion(52);
      if($tipoPresupuesto!=1){
        $unidad=0;
      }
      if($tipoPresupuesto!=1){
       $saux= ejecutadoEgresosMes($unidad, $fecha, 12, $area, 1, $numero);
       //$saux=$saux/12;
       $sum+=$saux; 
      }else{
       $sum+= ejecutadoEgresosMes($unidad, $fecha, $mes, $area, 0, $numero);
      }
      
      //$sum+=(float)obtenerMontoPorCuenta($numero,$unidad,$area,$fecha);      
    }
    
      return redondearDecimal($sum/(int)$valor);
  }

  function calcularCostosPresupuestariosAuditoria($id,$unidad,$area,$fecha,$valor){
     $sql="SELECT p.cod_partidapresupuestaria,p.cod_cuenta,c.numero FROM partidaspresupuestarias_cuentas p join plan_cuentas c on p.cod_cuenta=c.codigo where p.cod_partidapresupuestaria=$id";
     $dbh = new Conexion(); 
     $stmt = $dbh->prepare($sql);
     $stmt->execute();
     $sum=0;
     $mes=date("m");
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $numero=trim($row['numero']);
      $cuenta=$row['cod_cuenta'];
      $tipoPresupuesto=obtenerValorConfiguracion(52);
      if($tipoPresupuesto!=1){
        $unidad=0;
      }
      $tipoSim=obtenerValorConfiguracion(13);
      if($tipoPresupuesto!=1){
       $saux= ejecutadoEgresosMes($unidad, $fecha, 12, $area, 1, $numero);
       //$saux=$saux/12;
       $sum+=$saux; 
      }else{
       $sum+= ejecutadoEgresosMes($unidad, $fecha, $mes, $area, 0, $numero);
      }     
    }
    $valorD=obtenerValorConfiguracion($valor);
    return redondearDecimal($sum/(int)$valorD);
  }
    function calcularCostosPres($id,$unidad,$area,$fecha){
     $sql="SELECT p.cod_partidapresupuestaria,p.cod_cuenta,c.numero FROM partidaspresupuestarias_cuentas p join plan_cuentas c on p.cod_cuenta=c.codigo where p.cod_partidapresupuestaria=$id";
     $dbh = new Conexion(); 
     $stmt = $dbh->prepare($sql);
     $stmt->execute();
     $sum=0;
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $numero=trim($row['numero']);
      $sum+=(float)obtenerMontoPorCuenta($numero,$unidad,$area,$fecha);      
    }
    return redondearDecimal($sum);
  }
  function costoModulo($monto,$valor){
     return redondearDecimal($monto/(int)$valor);
  }
  function redondearDecimal($valor) {
   $float_redondeado=round($valor * 100) / 100;
   return $float_redondeado;
   }
   function contarPresupuestoCuentas($codigo){
    $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT count(*) as total FROM partidaspresupuestarias_cuentas where cod_partidapresupuestaria=$codigo");
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['total'];
   }
   return($nombreX);
   }

   function obtenerPreciosPlantillaCosto($codigo){
     $dbh = new Conexion();
     $stmt = $dbh->prepare("SELECT * FROM plantillas_costo where codigo=$codigo");
     $stmt->execute();
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $precioLocalX=$row['precio_ventalocal'];
        $precioExternoX=$row['precio_ventaexterno'];
        $alumnosX=$row['cantidad_alumnoslocal'];
        $alumnosExternoX=$row['cantidad_alumnosexterno'];
     }
     return array($precioLocalX,$precioExternoX,$alumnosX,$alumnosExternoX);
   } 

   function obtenerPreciosPorCodigo($codigo){
     $dbh = new Conexion();
     $stmt = $dbh->prepare("SELECT * FROM precios_plantillacosto where codigo=$codigo");
     $stmt->execute();
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $precioLocalX=$row['venta_local'];
        $precioExternoX=$row['venta_externo'];
     }
     return array($precioLocalX,$precioExternoX);
   }  

   //FUNCION DE SIMULACION OBTENER VALORES TOTALES TIPO FIJO O VARIABLE
   function obtenerTotalesPlantilla($codigo,$tipo,$mes){
    $anio=date("Y");
    $dbh = new Conexion();
    $query2="select pgd.cod_plantillagrupocosto,pc.cod_unidadorganizacional,pc.cod_area,pgc.nombre,pgc.cod_tipocosto,sum(pgd.monto_local) as local,sum(pgd.monto_externo) as externo,sum(pgd.monto_calculado) as calculado,pgd.tipo_calculo from plantillas_grupocostodetalle pgd join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo
join plantillas_gruposcosto pgc on pgd.cod_plantillagrupocosto=pgc.codigo
join plantillas_costo pc on pgc.cod_plantillacosto=pc.codigo 
where pc.codigo=$codigo and pgc.cod_tipocosto=$tipo GROUP BY pgd.cod_plantillagrupocosto order by pgd.cod_plantillagrupocosto";


  $stmt = $dbh->prepare($query2);
  $stmt->execute();

  $totalImporte=0;$totalModulo=0;$totalLocal=0;$totalExterno=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codGrupo=$row['cod_plantillagrupocosto'];$grupoUnidad=$row['cod_unidadorganizacional'];$grupoArea=$row['cod_area'];

    $tipoCalculoPadre=$row['tipo_calculo'];
    if($tipoCalculoPadre==1){
      $totalModulo+=$row['calculado'];
      $importe_grupo=(float)$row['calculado']*$mes;
    }else{
      $totalModulo+=$row['local'];
      $importe_grupo=(float)$row['local']*$mes;
    }
    $totalImporte+=$importe_grupo;
    $totalLocal+=$row['local'];
    $totalExterno+=$row['externo'];

  //partidas
    $query_partidas="select pgd.cod_plantillagrupocosto,pp.nombre,pgd.cod_partidapresupuestaria,pgd.tipo_calculo,pgd.monto_local,pgd.monto_externo,pgd.monto_calculado from plantillas_grupocostodetalle pgd join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo join plantillas_gruposcosto pgc on pgd.cod_plantillagrupocosto=pgc.codigo where pgd.cod_plantillagrupocosto=$codGrupo";

     $stmt_partidas = $dbh->prepare($query_partidas);
     $stmt_partidas->execute();

     while ($row_partidas = $stmt_partidas->fetch(PDO::FETCH_ASSOC)) {

         $codPartida=$row_partidas['cod_partidapresupuestaria'];
         $numeroCuentas=contarPresupuestoCuentas($codPartida);

        if($row_partidas['tipo_calculo']!=1){
          $importe_partida=(float)$row_partidas['monto_calculado']*$mes;
        }else{
          $importe_partida=(float)$row_partidas['monto_calculado']*$mes;
        }

        if($row_partidas['tipo_calculo']==1){
            $query_cuentas="SELECT pc.*,pp.cod_partidapresupuestaria FROM plan_cuentas pc join partidaspresupuestarias_cuentas pp on pc.codigo=pp.cod_cuenta where pp.cod_partidapresupuestaria=$codPartida order by pc.codigo";       
            $stmt_cuentas = $dbh->prepare($query_cuentas);
            $stmt_cuentas->execute();
            while ($row_cuentas = $stmt_cuentas->fetch(PDO::FETCH_ASSOC)) {
                $monto=obtenerMontoPorCuenta($row_cuentas['numero'],$grupoUnidad,$grupoArea,((int)$anio-1));
                if($monto==null){$monto=0;}
                $montoCal=costoModulo($monto,$mes);
            }
          }

      }  
    }
  return array($totalImporte,$totalModulo,$totalLocal,$totalExterno);
   }
   
   function obtenerTotalesPlantillaServicio($codigo,$tipo,$mes){
    $anio=date("Y");
    $dbh = new Conexion();
    $query2="select pgd.cod_plantillagruposervicio,pc.cod_unidadorganizacional,pc.cod_area,pgc.nombre,pgc.cod_tiposervicio,sum(pgd.monto_local) as local,sum(pgd.monto_externo) as externo,sum(pgd.monto_calculado) as calculado,pgd.tipo_calculo from plantillas_gruposerviciodetalle pgd join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo
join plantillas_gruposervicio pgc on pgd.cod_plantillagruposervicio=pgc.codigo
join plantillas_servicios pc on pgc.cod_plantillaservicio=pc.codigo 
where pc.codigo=$codigo and pgc.cod_tiposervicio=1 GROUP BY pgd.cod_plantillagruposervicio order by pgd.cod_plantillagruposervicio";


  $stmt = $dbh->prepare($query2);
  $stmt->execute();

  $totalImporte=0;$totalModulo=0;$totalLocal=0;$totalExterno=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codGrupo=$row['cod_plantillagruposervicio'];$grupoUnidad=$row['cod_unidadorganizacional'];$grupoArea=$row['cod_area'];
      
    $tipoCalculoPadre=$row['tipo_calculo'];
    if($tipoCalculoPadre==1){
      $totalModulo+=$row['calculado'];
      $importe_grupo=(float)$row['calculado']*$mes;
    }else{
      $totalModulo+=$row['local'];
      $importe_grupo=(float)$row['local']*$mes;
    }
    $totalImporte+=$importe_grupo;
    $totalLocal+=$row['local'];
    $totalExterno+=$row['externo'];

  //partidas
    $query_partidas="select pgd.cod_plantillagruposervicio,pp.nombre,pgd.cod_partidapresupuestaria,pgd.tipo_calculo,pgd.monto_local,pgd.monto_externo,pgd.monto_calculado from plantillas_gruposerviciodetalle pgd join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo join plantillas_gruposervicio pgc on pgd.cod_plantillagruposervicio=pgc.codigo where pgd.cod_plantillagruposervicio=$codGrupo";

     $stmt_partidas = $dbh->prepare($query_partidas);
     $stmt_partidas->execute();

     while ($row_partidas = $stmt_partidas->fetch(PDO::FETCH_ASSOC)) {

         $codPartida=$row_partidas['cod_partidapresupuestaria'];
         $numeroCuentas=contarPresupuestoCuentas($codPartida);

        if($row_partidas['tipo_calculo']!=1){
          $importe_partida=(float)$row_partidas['monto_calculado']*$mes;
        }else{
          $importe_partida=(float)$row_partidas['monto_calculado']*$mes;
        }

        if($row_partidas['tipo_calculo']==1){
            $query_cuentas="SELECT pc.*,pp.cod_partidapresupuestaria FROM plan_cuentas pc join partidaspresupuestarias_cuentas pp on pc.codigo=pp.cod_cuenta where pp.cod_partidapresupuestaria=$codPartida order by pc.codigo";       
            $stmt_cuentas = $dbh->prepare($query_cuentas);
            $stmt_cuentas->execute();
            while ($row_cuentas = $stmt_cuentas->fetch(PDO::FETCH_ASSOC)) {
                $monto=obtenerMontoPorCuenta($row_cuentas['numero'],$grupoUnidad,$grupoArea,((int)$anio-1));
                if($monto==null){$monto=0;}
                $montoCal=costoModulo($monto,$mes);
            }
          }

      }  
    }
  return array($totalImporte,$totalModulo,$totalLocal,$totalExterno);
   }


  function obtenerValorConfiguracion($id){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT valor_configuracion from configuraciones c where id_configuracion=$id");
   $stmt->execute();
   $codigoComprobante=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoComprobante=$row['valor_configuracion'];
   }
   return($codigoComprobante);
  }

  function obtenerCuentaContableDepre($id){
    $dbh = new Conexion();
     $stmt = $dbh->prepare("SELECT cod_cuentacontable from depreciaciones c where codigo=$id");
     $stmt->execute();
     $codigoComprobante=0;
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $codigoComprobante=$row['cod_cuentacontable'];
     }
     return($codigoComprobante);
  }


  function obtenerConfiguracionValorServicio($id){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cod_claservicio from configuraciones_servicios c where cod_claservicio=$id");
   $stmt->execute();
   $retorno=false;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $retorno=true;
   }
   return $retorno;
  }

  function nameTipoComprobante($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM tipos_comprobante where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}

// obtener nombre de retencion
function nameRetencion($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM configuracion_retenciones where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}

function abrevRetencion($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT abreviatura FROM configuracion_retenciones where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['abreviatura'];
   }
   return($nombreX);
}

// obtener porcentaje de retencion
function porcentRetencion($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT porcentaje_cuentaorigen FROM configuracion_retenciones where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['porcentaje_cuentaorigen'];
   }
   return($nombreX);
}

function porcentRetencionSolicitud($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT SUM(porcentaje) as porcentaje FROM configuracion_retencionesdetalle where cod_configuracionretenciones=:codigo and cod_cuenta!=0");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['porcentaje'];
   }
   return($nombreX);
}

function debeHaberRetencionDetalle($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT debe_haber FROM configuracion_retencionesdetalle where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['debe_haber'];
   }
   return($nombreX);
}
function porcentRetencionDetalle($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT porcentaje FROM configuracion_retencionesdetalle where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['porcentaje'];
   }
   return($nombreX);
}

function verificarAnticipoPersonaMes($codigoPersona, $codMes,$codGestion)
{
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT cod_personal FROM anticipos_personal WHERE cod_mes=$codMes AND cod_gestion=$codGestion");
  $stmt->execute();

  $cont = 0;
  $existe = 0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($row['cod_personal'] == $codigoPersona) {
      $cont++;
    }
  }
  if ($cont == 0) {
    $existe = 0;
  } else {
    $existe = 1;
  }
  return ($existe);
}

function verificarBonoPersonaMes($codigoPersona, $codMes, $codBono)
{
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT cod_personal FROM bonos_personal_mes WHERE cod_mes=$codMes AND cod_bono=$codBono");
  $stmt->execute();

  $cont = 0;
  $existe = 0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($row['cod_personal'] == $codigoPersona) {
      $cont++;
    }
  }
  if ($cont == 0) {
    $existe = 0;
  } else {
    $existe = 1;
  }
  return ($existe);
}


function verificarPersonaMes($codigoPersona, $codMes, $codDescuento)
{
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT cod_personal FROM descuentos_personal_mes WHERE cod_mes=$codMes AND cod_descuento=$codDescuento");
  $stmt->execute();

  $cont = 0;
  $existe = 0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($row['cod_personal'] == $codigoPersona) {
      $cont++;
    }
  }
  if ($cont == 0) {
    $existe = 0;
  } else {
    $existe = 1;
  }
  return ($existe);
}

function formatearNumerosExcel($numero)
{
  //reemplazar comas por puntos
  $num = str_replace(',', '.', $numero);
  return $num;
}

function formatoNombreArchivoExcel()
{
  $fecha = getdate();
  $nombre_v = $fecha["mday"] . "-" . $fecha["mon"] . "-" . $fecha["year"] . "-" . $fecha["hours"] . "-" . $fecha["minutes"] . "-" . $fecha["seconds"] . ".csv";
  return $nombre_v;
}


function verificarExistenciaPersona($codigoPersona)
{
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT codigo FROM personal WHERE codigo=$codigoPersona and cod_estadoreferencial=1");
  $stmt->execute();

  $cont = 0;
  $existe = 0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($row['codigo'] == $codigoPersona) {
      $cont++;
    }
  }
  if ($cont == 0) {
    $existe = false;
  } else {
    $existe = true;
  }
  return ($existe);
}

function nombrePersona($codigoPersona){
  $nombre=null;
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT codigo,paterno,materno,primer_nombre FROM personal WHERE codigo=$codigoPersona and cod_estadoreferencial=1");
  $stmt->execute();
  $stmt->bindParam('paterno',$paterno);
  $stmt->bindParam('materno',$materno);
  $stmt->bindParam('primer_nombre',$primer_nombre);

  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $paternoX=$row['paterno'];
    $maternoX=$row['materno'];
    $primer_nombreX=$row['primer_nombre'];
    $nombre= $primer_nombreX." ".$paternoX." ".$maternoX;
 }
 return($nombre);

}

function calculaIva($monto){
  $calculo=$monto*0.13;
  return ($calculo);
}

function MesAnioEnLetra($fecha){
  $anio = date("Y", strtotime($fecha));
  $mes = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
  $mes = $mes[(date('m', strtotime($fecha))*1)-1];
  return $mes.'/'.$anio;
}


function calculaBonoProfesion($codGrado){
  $dbh = new Conexion();
  //obtener sueldo basico de configuraciones_planillas
  $stmt = $dbh->prepare("SELECT valor_configuracion FROM configuraciones_planillas where id_configuracion=1");
  $stmt->execute();
  $stmt->bindColumn('valor_configuracion', $sueldoBasico);

  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    $sueldoBasicoX= $sueldoBasico;
  }

  //obtener codigo y porcentaje de profesion
  $stmtb = $dbh->prepare("SELECT codigo,porcentaje FROM personal_grado_academico where codigo=$codGrado");
  $stmtb->execute();
  $stmtb->bindColumn('codigo', $codigo);
  $stmtb->bindColumn('porcentaje', $porcentaje);

  while ($row = $stmtb->fetch(PDO::FETCH_BOUND)) {
    $codGAX = $codigo;
    $porcentajeX = $porcentaje;

    if($codGrado==$codGAX){
      $bonoProf = $sueldoBasicoX*($porcentajeX/100);
    }
  }

  return($bonoProf);
}

function insertarDotacionPersonaMes($codDotacionPersona,$monto,$nro_meses, $fecha,$codGestion)
{
  
  $dbh = new Conexion();
  $montoxMes=$monto/$nro_meses;
  $codGest=$codGestion;

  //$anio = date("Y", strtotime($fecha));
  $mes = (date('m', strtotime($fecha)) * 1);

  for ($i = 1; $i <= $nro_meses; $i++) {
    if ($mes <= 12) {
      //echo $nro_meses . " " . $mes . '/' . $anio . ".....";
      $stmt = $dbh->prepare("INSERT INTO dotaciones_personal_mes(cod_dotacionpersonal,cod_gestion,cod_mes,monto_mes,cod_estadoreferencial)
                           VALUES ($codDotacionPersona,$codGest,$mes,$montoxMes,1) ");
      $stmt->execute();
      $mes++;
    } else {
      $mes = 1;
      //$anio++;
      $codGest=$codGest+1;
      //echo $nro_meses . " " . $mes . '/' . $anio . ".....";
      $stmtb = $dbh->prepare("INSERT INTO dotaciones_personal_mes(cod_dotacionpersonal,cod_gestion,cod_mes,monto_mes,cod_estadoreferencial)
                           VALUES ($codDotacionPersona,$codGest,$mes,$montoxMes,1) ");
      $stmtb->execute();
      $mes++;
    }
  }
}


function obtenerCodigoSolicitudRecursos(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from solicitud_recursos c");
   $stmt->execute();
   $codigoSolicitud=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoSolicitud=$row['codigo'];
   }
   return($codigoSolicitud);
}

function nameSimulacion($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM simulaciones_costos where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}
function nameSimulacionServicio($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM simulaciones_servicios where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}

function nameClienteSimulacionServicio($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT c.nombre FROM clientes c join simulaciones_servicios s on s.cod_cliente=c.codigo where s.codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}

function obtenerDetalleSolicitud($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT * from solicitud_recursosdetalle where cod_solicitudrecurso=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerDetalleSolicitudSimulacion($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT pc.*,pp.nombre as partida, pp.codigo as cod_partida from simulaciones_costos s 
join plantillas_costo p on s.cod_plantillacosto=p.codigo 
join plantillas_gruposcosto pg on p.codigo=pg.cod_plantillacosto 
join plantillas_grupocostodetalle pgd on pgd.cod_plantillagrupocosto=pg.codigo
join partidas_presupuestarias pp on pp.codigo=pgd.cod_partidapresupuestaria 
join partidaspresupuestarias_cuentas ppc on ppc.cod_partidapresupuestaria=pp.codigo 
join plan_cuentas pc on ppc.cod_cuenta=pc.codigo where s.codigo=$codigo and pg.cod_tipocosto=2 order by pp.codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerDetalleSolicitudSimulacionCuenta($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT pc.*,pp.nombre as partida, pp.codigo as cod_partida,sc.monto_local,sc.monto_externo from cuentas_simulacion sc 
join partidas_presupuestarias pp on pp.codigo=sc.cod_partidapresupuestaria 
join plan_cuentas pc on sc.cod_plancuenta=pc.codigo where sc.cod_simulacioncostos=$codigo order by pp.codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}

function obtenerDetalleSolicitudSimulacionCuentaPlantilla($codigo,$codigoPlan){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT tablap.codigo as codigo_detalle,tablap.glosa,tablap.monto_total,tablap.habilitado,tabla_uno.* 
FROM (SELECT pc.codigo,pc.numero,pc.nombre,pp.nombre as partida, pp.codigo as cod_partida,sc.monto_local,sc.monto_externo from cuentas_simulacion sc 
join partidas_presupuestarias pp on pp.codigo=sc.cod_partidapresupuestaria 
join plan_cuentas pc on sc.cod_plancuenta=pc.codigo where sc.cod_simulacioncostos=$codigo order by pp.codigo) tabla_uno,
simulaciones_detalle tablap where tablap.cod_cuenta=tabla_uno.codigo and (tablap.cod_plantillacosto!='' or tablap.cod_plantillacosto!=NULL) and tablap.cod_plantillacosto=$codigoPlan and tablap.cod_simulacioncosto=$codigo and tablap.habilitado=1 and tablap.cod_estadoreferencial=1 order by tabla_uno.codigo;";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}

function obtenerDetalleSolicitudSimulacionCuentaPlantillaServicio($codigo,$codigoPlan){
   $dbh = new Conexion();
   $sql="";
   $sql="(select * from v_propuestas_detalle_variables  where cod_simulacionservicio=$codigo order by cod_detalle)
        UNION
         (select * from v_propuestas_detalle_honorarios  where cod_simulacionservicio=$codigo)
        order by cod_anio limit 20";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}

function obtenerDetalleSolicitudSimulacionCuentaPlantillaServicioCompleto($codigo,$codigoPlan){
   $dbh = new Conexion();
   $sql="";
   $sql="(select * from v_propuestas_detalle_variables  where cod_simulacionservicio=$codigo order by cod_detalle)
        UNION
         (select * from v_propuestas_detalle_honorarios  where cod_simulacionservicio=$codigo)
        order by cod_anio";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}

function obtenerDetalleSolicitudSimulacionCuentaPlantillaServicioCompletoAnios($codigo,$anio){
   $dbh = new Conexion();
   $sql="";
   $sql="(select * from v_propuestas_detalle_variables  where cod_simulacionservicio=$codigo and cod_anio=$anio order by cod_detalle)
        UNION
         (select * from v_propuestas_detalle_honorarios  where cod_simulacionservicio=$codigo and cod_anio=$anio)
        order by cod_anio";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}


function obtenerDetalleSolicitudProveedor($codigo,$fechai,$fechaf,$estado,$codUsuario){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT cs.monto_local,cs.monto_externo, pc.*,pp.nombre as partida, pp.codigo as cod_partida,s.nombre as simulacion,s.codigo as cod_simulacion,s.fecha as fecha_simulacion from simulaciones_costos s 
join plantillas_costo p on s.cod_plantillacosto=p.codigo 
join plantillas_gruposcosto pg on p.codigo=pg.cod_plantillacosto 
join plantillas_grupocostodetalle pgd on pgd.cod_plantillagrupocosto=pg.codigo
join partidas_presupuestarias pp on pp.codigo=pgd.cod_partidapresupuestaria 
join partidaspresupuestarias_cuentas ppc on ppc.cod_partidapresupuestaria=pp.codigo 
join plan_cuentas pc on ppc.cod_cuenta=pc.codigo, cuentas_simulacion cs
where pc.codigo=cs.cod_plancuenta and s.codigo=cs.cod_simulacioncostos and pc.codigo=$codigo and s.cod_estadosimulacion=$estado and s.cod_responsable=$codUsuario and s.fecha BETWEEN '$fechai' and '$fechaf' order by pp.codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerDetalleSolicitudProveedorPlantilla($codigo,$fechai,$fechaf,$estado,$codUsuario){
   $dbh = new Conexion();
   $sql="";
   /*$sql="SELECT tablap.codigo as codigo_detalle,tablap.glosa,tablap.monto_total,tablap.habilitado,tabla_uno.* FROM (SELECT s.cod_plantillacosto,cs.monto_local,cs.monto_externo,pc.codigo, pc.numero,pc.nombre,pp.nombre as partida, pp.codigo as cod_partida,s.nombre as simulacion,s.codigo as cod_simulacion,s.fecha as fecha_simulacion 
from simulaciones_costos s 
join plantillas_costo p on s.cod_plantillacosto=p.codigo 
join plantillas_gruposcosto pg on p.codigo=pg.cod_plantillacosto 
join plantillas_grupocostodetalle pgd on pgd.cod_plantillagrupocosto=pg.codigo
join partidas_presupuestarias pp on pp.codigo=pgd.cod_partidapresupuestaria 
join partidaspresupuestarias_cuentas ppc on ppc.cod_partidapresupuestaria=pp.codigo 
join plan_cuentas pc on ppc.cod_cuenta=pc.codigo, cuentas_simulacion cs
where pc.codigo=cs.cod_plancuenta and s.codigo=cs.cod_simulacioncostos and pc.codigo=$codigo and s.cod_estadosimulacion=$estado and s.cod_responsable=$codUsuario and s.fecha BETWEEN '$fechai' and '$fechaf' order by pp.codigo)
tabla_uno,plantillas_servicios_detalle tablap 
where tablap.cod_cuenta=tabla_uno.codigo and (tablap.cod_plantillacosto!='' or tablap.cod_plantillacosto!=NULL) and tablap.cod_plantillacosto=tabla_uno.cod_plantillacosto and tablap.habilitado=1 and tablap.cod_estadoreferencial=1 order by tabla_uno.codigo;";*/
   $sql="(select * from v_propuestas_detalle_variables  where codigo=$codigo order by cod_detalle)
        UNION
         (select * from v_propuestas_detalle_honorarios  where codigo=$codigo)";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerDetalleSolicitudProveedorPlantillaSec($codigo,$fechai,$fechaf,$codUsuario){
   $dbh = new Conexion();
   $sql="";
   $sql="(SELECT 2 as simu,sec.* from (
SELECT tablap.codigo as codigo_detalle,tablap.glosa,tablap.monto_total,tablap.habilitado,tabla_uno.*,s.nombre as nombre_simulacion,plan.cod_area,plan.cod_unidadorganizacional  
FROM 
(SELECT pc.codigo,pc.numero,pc.nombre,pp.nombre as partida, pp.codigo as cod_partida,sc.monto_local,sc.monto_externo ,sc.cod_simulacioncostos
from cuentas_simulacion sc 
join partidas_presupuestarias pp on pp.codigo=sc.cod_partidapresupuestaria 
join plan_cuentas pc on sc.cod_plancuenta=pc.codigo order by pp.codigo) tabla_uno,
simulaciones_detalle tablap
join simulaciones_costos s on s.codigo=tablap.cod_simulacioncosto
join plantillas_costo plan on plan.codigo=s.cod_plantillacosto
where tablap.cod_cuenta=tabla_uno.codigo and (tablap.cod_plantillacosto!='' or tablap.cod_plantillacosto!=NULL) and tablap.cod_plantillacosto=3 
and tablap.cod_simulacioncosto=tabla_uno.cod_simulacioncostos and tablap.habilitado=1 and tablap.cod_estadoreferencial=1  
and tablap.cod_cuenta=$codigo and s.cod_responsable=$codUsuario and s.fecha BETWEEN '$fechai' and '$fechaf'
order by tabla_uno.codigo) sec )";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerDetalleSolicitudProveedorPlantillaTCPTCS($codigo,$fechai,$fechaf,$codUsuario){
   $dbh = new Conexion();
   $sql="";
   $sql="(SELECT 1 as simu,tcptcs.* FROM ((select v.* ,s.nombre as nombre_simulacion,plan.cod_area,plan.cod_unidadorganizacional from v_propuestas_detalle_variables v join simulaciones_servicios s on s.codigo=v.cod_simulacionservicio join plantillas_servicios plan on plan.codigo=s.cod_plantillaservicio where v.codigo=$codigo and s.cod_responsable=$codUsuario and s.fecha BETWEEN '$fechai' and '$fechaf' order by v.cod_detalle)
        UNION
         (select v.*,s.nombre as nombre_simulacion,plan.cod_area,plan.cod_unidadorganizacional from v_propuestas_detalle_honorarios v join simulaciones_servicios s on s.codigo=v.cod_simulacionservicio join plantillas_servicios plan on plan.codigo=s.cod_plantillaservicio where v.codigo=$codigo and s.cod_responsable=$codUsuario and s.fecha BETWEEN '$fechai' and '$fechaf')) tcptcs         
          )";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerCuentasLista($nivel,$n){
 $dbh = new Conexion();
   $sql="";
   $sqlAux="";
   if($n!=null){
    $sqlAux.="and (";
     for ($i=0; $i < count($n); $i++) {
       if($i==(count($n)-1)){
        $sqlAux.="p.numero like '".$n[$i]."%')";
       }else{
        $sqlAux.="p.numero like '".$n[$i]."%' or ";
       } 
     }
   }
   $sql="SELECT p.codigo, p.numero, p.nombre from plan_cuentas p where p.nivel=$nivel $sqlAux order by p.numero";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerCuentasListaSolicitud(){
 $dbh = new Conexion();
   $sql="";
   $sql="SELECT p.codigo, p.numero, p.nombre from plan_cuentas p join solicitud_recursoscuentas s on p.codigo=s.cod_cuenta order by p.numero";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerCorreosListaPersonal(){
 $dbh = new Conexion();
   $sql="";
   $sql="SELECT p.codigo,p.email_empresa from personal p where p.email_empresa!='' order by p.email_empresa";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function contarSolicitudDetalle($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="select count(*) as total from solicitud_recursosdetalle where cod_solicitudrecurso=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
  }
  function obtenerSolicitudesDet($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT d.codigo as cod_det from solicitud_recursosdetalle d where d.cod_solicitudrecurso=$codigo order by cod_det";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   // bindColumn
   //$stmt->bindColumn('cod_comprobante', $codigoC);
   return $stmt;
}

function obtenerCorrelativoComprobante($cod_tipocomprobante, $unidad_organizacional, $gestion, $mes){
  $dbh = new Conexion(); 
  $sql="SELECT IFNULL(max(c.numero)+1,1)as codigo from comprobantes c where c.cod_tipocomprobante='$cod_tipocomprobante' and c.cod_unidadorganizacional='$unidad_organizacional' and YEAR(c.fecha)='$gestion' and MONTH(c.fecha)='$mes' and c.cod_estadocomprobante<>2";
  //echo $sql;
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $nroCorrelativo=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $nroCorrelativo=$row['codigo'];
  }
  return ($nroCorrelativo);
}

function obtenerCorrelativoComprobante2($cod_tipocomprobante){
  $dbh = new Conexion(); 
  $sql="SELECT IFNULL(max(c.numero)+1,1)as codigo from comprobantes c where c.cod_tipocomprobante=$cod_tipocomprobante and c.fecha>='2020-07-01 00:00:00' and c.cod_estadocomprobante<>2";
  //echo $sql;
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $nroCorrelativo=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $nroCorrelativo=$row['codigo'];
  }
  return ($nroCorrelativo);
}

function obtenerCorrelativoSolicitud(){
  $dbh = new Conexion(); 
  $sql="SELECT IFNULL(max(c.nro_correlativo)+1,7000)as correlativo from solicitudes_facturacion c";
  //echo $sql;
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $nroCorrelativo=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $nroCorrelativo=$row['correlativo'];
  }
  return ($nroCorrelativo);
}

function obtenerFacturasSoli($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT codigo as cod_det,nit,nro_factura,fecha,razon_social,importe,exento,nro_autorizacion,codigo_control from facturas_compra  where cod_solicitudrecursodetalle=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function contarFacturasSoli($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="select count(*) as total from facturas_compra where cod_solicitudrecursodetalle=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   $stmt->bindColumn('total', $contador);
   while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    $cont1=$contador;
   }
   return $cont1;
  }
  function obtenerCodigoSolicitudDetalle(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from solicitud_recursosdetalle c");
   $stmt->execute();
   $codigoComprobanteDetalle=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoComprobanteDetalle=$row['codigo'];
   }
   return($codigoComprobanteDetalle);
}


function obtenerCuentasSimulaciones($nivel,$estado,$codUsuario){
 $dbh = new Conexion();
   $sql="SELECT DISTINCT p.* from plantillas_servicios_detalle d join plan_cuentas p on p.codigo=d.cod_cuenta where d.editado_alumno!=0";
   /*$sql="SELECT DISTINCT pc.* from simulaciones_costos s 
join plantillas_costo p on s.cod_plantillacosto=p.codigo 
join plantillas_gruposcosto pg on p.codigo=pg.cod_plantillacosto 
join plantillas_grupocostodetalle pgd on pgd.cod_plantillagrupocosto=pg.codigo
join partidas_presupuestarias pp on pp.codigo=pgd.cod_partidapresupuestaria 
join partidaspresupuestarias_cuentas ppc on ppc.cod_partidapresupuestaria=pp.codigo 
join plan_cuentas pc on ppc.cod_cuenta=pc.codigo WHERE s.cod_estadosimulacion=$estado and pg.cod_tipocosto=2 and s.cod_responsable=$codUsuario and pc.nivel=$nivel order by pp.codigo";*/
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerCentroSolicitud($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cod_unidadorganizacional,cod_area from solicitud_recursos where codigo=$codigo");
   $stmt->execute();
   $codigoUnidad=0;$codigoArea=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoUnidad=$row['cod_unidadorganizacional'];
      $codigoArea=$row['cod_area'];
   }
   return array($codigoUnidad,$codigoArea);
}
function obtenerSolicitudRecursosDetalleCuenta($codSol,$codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT sd.*,pc.numero,pc.nombre from solicitud_recursosdetalle sd join plan_cuentas pc on sd.cod_plancuenta=pc.codigo where pc.codigo=$codigo and sd.cod_solicitudrecurso=$codSol";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerSolicitudRecursosDetallePlantilla($codSol,$codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT sd.*,pc.numero,pc.nombre from solicitud_recursosdetalle sd join plan_cuentas pc on sd.cod_plancuenta=pc.codigo where sd.cod_detalleplantilla=$codigo"; // and sd.cod_solicitudrecurso=$codSol
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerSolicitudRecursosDetallePlantillaAud($codSol,$codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT sd.*,pc.numero,pc.nombre from solicitud_recursosdetalle sd join plan_cuentas pc on sd.cod_plancuenta=pc.codigo where sd.cod_servicioauditor=$codigo"; //and sd.cod_solicitudrecurso=$codSol
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerSolicitudRecursosDetallePlantillaSol($codSol,$codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT sd.*,pc.numero,pc.nombre from solicitud_recursosdetalle sd join plan_cuentas pc on sd.cod_plancuenta=pc.codigo where sd.cod_detalleplantilla=$codigo and sd.cod_solicitudrecurso=$codSol"; // 
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerSolicitudRecursosDetallePlantillaAudSol($codSol,$codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT sd.*,pc.numero,pc.nombre from solicitud_recursosdetalle sd join plan_cuentas pc on sd.cod_plancuenta=pc.codigo where sd.cod_servicioauditor=$codigo and sd.cod_solicitudrecurso=$codSol"; //
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerSolicitudRecursosDetalle($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT sd.*,pc.numero,pc.nombre from solicitud_recursosdetalle sd join plan_cuentas pc on sd.cod_plancuenta=pc.codigo where sd.cod_solicitudrecurso=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerPartidasPlantillaCostos($codigo,$tipo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT DISTINCT p.cod_partidapresupuestaria,pc.cod_unidadorganizacional,pc.cod_area,pc.codigo as codPlantilla,pd.tipo_calculo,pd.monto_local,pd.monto_externo FROM partidaspresupuestarias_cuentas p join plan_cuentas c on p.cod_cuenta=c.codigo
join plantillas_grupocostodetalle pd on pd.cod_partidapresupuestaria=p.cod_partidapresupuestaria
join plantillas_gruposcosto pg on pg.codigo=pd.cod_plantillagrupocosto
join plantillas_costo pc on pc.codigo=pg.cod_plantillacosto where pc.codigo=$codigo and pg.cod_tipocosto=$tipo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerPartidasPlantillaServicio($codigo,$tipo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT DISTINCT p.cod_partidapresupuestaria,pc.cod_unidadorganizacional,pc.cod_area,pc.codigo as codPlantilla,pd.tipo_calculo,pd.monto_local,pd.monto_externo FROM partidaspresupuestarias_cuentas p join plan_cuentas c on p.cod_cuenta=c.codigo
join plantillas_gruposerviciodetalle pd on pd.cod_partidapresupuestaria=p.cod_partidapresupuestaria
join plantillas_gruposervicio pg on pg.codigo=pd.cod_plantillagruposervicio
join plantillas_servicios pc on pc.codigo=pg.cod_plantillaservicio where pc.codigo=$codigo and pg.cod_tiposervicio=$tipo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerCuentaPlantillaCostos($codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT p.cod_partidapresupuestaria,p.cod_cuenta,c.numero,c.nombre FROM partidaspresupuestarias_cuentas p join plan_cuentas c on p.cod_cuenta=c.codigo where p.cod_partidapresupuestaria=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerDetallePlantillaCostosPartida($plantilla,$codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT c.numero,c.nombre,p.* FROM plantillas_servicios_detalle p join plan_cuentas c on p.cod_cuenta=c.codigo where p.cod_partidapresupuestaria=$codigo and p.cod_plantillacosto=$plantilla";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerDetallePlantillaCostos($plantilla){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT c.numero,c.nombre,p.* FROM plantillas_servicios_detalle p join plan_cuentas c on p.cod_cuenta=c.codigo where p.cod_plantillacosto=$plantilla";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerDetallePlantillaServicioPartida($plantilla,$codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT c.numero,c.nombre,p.* FROM plantillas_servicios_detalle p join plan_cuentas c on p.cod_cuenta=c.codigo where p.cod_partidapresupuestaria=$codigo and p.cod_plantillatcp=$plantilla";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerDetallePlantillaServicioAuditores($plantilla){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT p.* FROM plantillas_servicios_auditores p where p.cod_estadoreferencial=1 and p.cod_plantillaservicio=$plantilla";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerDetallePlantillaServicioTipoServicio($plantilla){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT p.* FROM plantillas_servicios_tiposervicio p where p.cod_estadoreferencial=1 and p.cod_plantillaservicio=$plantilla";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerListaPreciosPlantillaCosto($plantilla,$codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT p.* FROM precios_plantillacosto p where p.cod_plantillacosto=$plantilla and codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}

function obtenerPorcentajesPreciosPlantillaCosto(){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT p.* FROM configuraciones_precioscosto p ";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}

function obtenerDetalleSimulacionCostosPartida($sim,$codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT c.numero,c.nombre,p.* FROM simulaciones_detalle p join plan_cuentas c on p.cod_cuenta=c.codigo where p.cod_partidapresupuestaria=$codigo and p.cod_simulacioncosto=$sim";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerDetalleSimulacionCostosPartidaServicio($sim,$codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT c.numero,c.nombre,p.* FROM simulaciones_serviciodetalle p join plan_cuentas c on p.cod_cuenta=c.codigo where p.cod_partidapresupuestaria=$codigo and p.cod_simulacionservicio=$sim";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}

function obtenerDetalleSimulacionCostosPartidaServicioPeriodo($sim,$codigo,$anio){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT c.numero,c.nombre,p.* FROM simulaciones_serviciodetalle p join plan_cuentas c on p.cod_cuenta=c.codigo where p.cod_partidapresupuestaria=$codigo and p.cod_simulacionservicio=$sim and p.cod_anio=$anio";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerMontosCuentasDetallePlantillaCostosPartida($plantilla,$codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT p.cod_partidapresupuestaria,p.cod_cuenta,c.numero,c.nombre,sum(p.monto_total) as monto FROM plantillas_servicios_detalle p join plan_cuentas c on p.cod_cuenta=c.codigo where p.cod_partidapresupuestaria=$codigo and p.cod_plantillacosto=$plantilla group by cod_cuenta";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerMontosCuentasDetallePlantillaServicioPartida($plantilla,$codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT p.cod_partidapresupuestaria,p.cod_cuenta,c.numero,c.nombre,sum(p.monto_total) as monto,sum(p.monto_totalext) as montoext FROM plantillas_servicios_detalle p join plan_cuentas c on p.cod_cuenta=c.codigo where p.cod_partidapresupuestaria=$codigo and p.cod_plantillatcp=$plantilla group by cod_cuenta";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerMontosCuentasDetallePlantillaCostosPartidaHabilitado($plantilla,$codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT p.cod_partidapresupuestaria,p.cod_cuenta,c.numero,c.nombre,sum(p.monto_total) as monto,p.habilitado FROM plantillas_servicios_detalle p join plan_cuentas c on p.cod_cuenta=c.codigo where p.cod_partidapresupuestaria=$codigo and p.cod_plantillacosto=$plantilla group by cod_cuenta";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerMontosCuentasDetalleSimulacionCostosPartidaHabilitado($sim,$codigo){
  $dbh = new Conexion();
  $sql="";
  //$sql="SELECT p.cod_partidapresupuestaria,p.cod_cuenta,c.numero,c.nombre,sum(p.monto_total) as monto,p.habilitado FROM simulaciones_detalle p join plan_cuentas c on p.cod_cuenta=c.codigo where p.cod_partidapresupuestaria=$codigo and p.cod_simulacioncosto=$sim group by cod_cuenta";
  $sql="SELECT p.cod_partidapresupuestaria,p.cod_cuenta,c.numero,c.nombre,p.monto_total as monto,p.habilitado FROM simulaciones_detalle p join plan_cuentas c on p.cod_cuenta=c.codigo where p.cod_partidapresupuestaria=$codigo and p.cod_simulacioncosto=$sim";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerMontosCuentasDetalleSimulacionServicioPartidaHabilitado($sim,$codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT p.cod_partidapresupuestaria,p.cod_cuenta,c.numero,c.nombre,p.monto_total as monto,p.habilitado FROM simulaciones_serviciodetalle p join plan_cuentas c on p.cod_cuenta=c.codigo where p.cod_partidapresupuestaria=$codigo and p.cod_simulacionservicio=$sim";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerMontosCuentasDetalleSimulacionServicioPartidaHabilitadoPeriodo($sim,$codigo,$anio){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT p.cod_partidapresupuestaria,p.cod_cuenta,c.numero,c.nombre,p.monto_total as monto,p.habilitado FROM simulaciones_serviciodetalle p join plan_cuentas c on p.cod_cuenta=c.codigo where p.cod_partidapresupuestaria=$codigo and p.cod_simulacionservicio=$sim and p.cod_anio=$anio";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerCantidadPreciosPlantilla($codPlantilla){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT count(*) as num FROM precios_plantillacosto where cod_plantillacosto=$codPlantilla";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $num=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
   $num=$row['num'];
  }
  return $num;
}
function obtenerCantidadPlantillaDetallesPartida($codPlantilla,$codPartida){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT count(*) as num FROM plantillas_servicios_detalle where cod_plantillacosto=$codPlantilla and cod_partidapresupuestaria=$codPartida";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $num=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
   $num=$row['num'];
  }
  return $num;
}
function obtenerCantidadPlantillaDetallesPartidaServicio($codPlantilla,$codPartida){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT count(*) as num FROM plantillas_servicios_detalle where cod_plantillatcp=$codPlantilla and cod_partidapresupuestaria=$codPartida";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $num=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
   $num=$row['num'];
  }
  return $num;
}
function obtenerPrecioServiciosSimulacion($codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT * from simulaciones_servicios_tiposervicio where cod_simulacionservicio=$codigo and habilitado!=0";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $num=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
    $cantidad=$row['cantidad_editado'];
    $monto=$row['monto'];
    $num+=$cantidad*$monto;
  }
  return $num;
}
function obtenerPrecioServiciosSimulacionPorAnio($codigo,$anio){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT * from simulaciones_servicios_tiposervicio where cod_simulacionservicio=$codigo and cod_anio=$anio and habilitado!=0";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $num=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
    $cantidad=$row['cantidad_editado'];
    $monto=$row['monto'];
    $num+=$cantidad*$monto;
  }
  return $num;
}
function obtenerNombreClienteSimulacion($codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT c.nombre from simulaciones_servicios s join clientes c on s.cod_cliente=c.codigo where s.codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $valor="";
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
    $valor=$row['nombre'];
  }
  return $valor;
}
function obtenerPrecioServiciosSimulacionPeriodo($codigo,$anio){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT * from simulaciones_servicios_tiposervicio where cod_simulacionservicio=$codigo and habilitado!=0 and cod_anio=$anio";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $num=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
    $cantidad=$row['cantidad_editado'];
    $monto=$row['monto'];
    $num+=$cantidad*$monto;
  }
  return $num;
}

function obtenerCantidadPersonalSimulacion($codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT * from simulaciones_servicios_auditores where cod_simulacionservicio=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $num=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
    $cantidad=$row['cantidad'];
    //$monto=$row['monto'];
    $num+=$cantidad;
  }
  return $num;
}
function obtenerCantidadPersonalSimulacionEditado($codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT * from simulaciones_servicios_auditores where cod_simulacionservicio=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $num=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
    $cantidad=$row['cantidad_editado'];
    //$monto=$row['monto'];
    $num+=$cantidad;
  }
  return $num;
}
function obtenerCantidadTotalPersonalSimulacionEditado($codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT * from simulaciones_servicios_auditores where cod_simulacionservicio=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $num=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
    if($row['dias']>0){
      $cantidad=$row['cantidad_editado']*$row['dias'];
    }else{
      $cantidad=$row['cantidad_editado'];
    }

    $num+=$cantidad;
  }
  return $num;
}
function obtenerCostosPersonalSimulacionEditado($codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT * from simulaciones_servicios_auditores where cod_simulacionservicio=$codigo and habilitado=1";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $num=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
    $cantidad=$row['cantidad_editado'];
    $dias=$row['dias'];
    $extlocal=$row['cod_externolocal'];
    if($extlocal==1){
      $monto=$row['monto'];
    }else{
      $monto=$row['monto_externo'];
    }   
    $num+=$cantidad*$monto*$dias;
  }
  return $num;
}
function obtenerCostosPersonalSimulacionEditadoPeriodo($codigo,$anio){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT * from simulaciones_servicios_auditores where cod_simulacionservicio=$codigo and habilitado=1 and cod_anio=$anio";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $num=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
    $cantidad=$row['cantidad_editado'];
    $dias=$row['dias'];
    $extlocal=$row['cod_externolocal'];
    if($extlocal==1){
      $monto=$row['monto'];
    }else{
      $monto=$row['monto_externo'];
    }   
    $num+=$cantidad*$monto*$dias;
  }
  return $num;
}

function obtenerCostosPersonalSimulacionEditadoBK($codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT * from simulaciones_servicios_auditores where cod_simulacionservicio=$codigo and habilitado=1";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $num=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
    $cantidad=$row['cantidad_editado'];
    $dias=$row['dias'];
    $monto=$row['monto'];
    $num+=$cantidad*$monto*$dias;
  }
  return $num;
}

function obtenerCantidadPersonalSimulacionDetalle($codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT * from simulaciones_serviciodetalle where cod_simulacionservicio=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $num=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
    $cantidad=$row['cantidad'];
    //$monto=$row['monto'];
    $num+=$cantidad;
  }
  return $num;
}

function obtenerCantidadPersonalPlantilla($codPlantilla){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT IFNULL(sum(cantidad),0) as num FROM plantillas_servicios_auditores where cod_plantillaservicio=$codPlantilla";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $num=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
   $num=$row['num'];
  }
  return $num;
}
function obtenerCantidadTotalPersonalPlantilla($codPlantilla){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT sum(cantidad*dias) as num FROM plantillas_servicios_auditores where cod_plantillaservicio=$codPlantilla";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $num=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
   $num=$row['num'];
  }
  return $num;
}
function obtenerCodigoAreaPlantillaServicio($codigo){
   $dbh = new Conexion();
  $sql="";
  $sql="SELECT cod_area FROM plantillas_servicios where codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $num=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
   $num=$row['cod_area'];
  }
  return $num;
}
function obtenerCodigoUnidadPlantillaServicio($codigo){
   $dbh = new Conexion();
  $sql="";
  $sql="SELECT cod_unidadorganizacional FROM plantillas_servicios where codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $num=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
   $num=$row['cod_unidadorganizacional'];
  }
  return $num;
}
function obtenerCodigoUnidadPlantillaCosto($codigo){
   $dbh = new Conexion();
  $sql="";
  $sql="SELECT cod_unidadorganizacional FROM plantillas_costo where codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $num=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
   $num=$row['cod_unidadorganizacional'];
  }
  return $num;
}

//================ ========== PARA  planilla sueldos

function obtenerBonoAntiguedad($minino_salarial,$ing_contr,$anio_actual){  
  // $anio_actual= date('Y');
  // $anio_actual=2019;
  $fechaComoEntero = strtotime($ing_contr);
  $anio_inicio = date("Y", $fechaComoEntero);
  $diferencia_anios=$anio_actual-$anio_inicio;

  $total_bono_antiguedad = 0;
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT * from escalas_antiguedad where cod_estadoreferencial=1");
  $stmt->execute();
  $stmt->bindColumn('anios_inicio', $anios_inicio);
  $stmt->bindColumn('anios_final', $anios_final);
  $stmt->bindColumn('porcentaje', $porcentaje);  
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
    if($anios_inicio<=$diferencia_anios and $diferencia_anios<$anios_final){
        $l3 = $minino_salarial*$porcentaje/100;      
    }else $l3 = 0;

    $total_bono_antiguedad +=$l3;
  }
  // $aporte_laboral_aux=$total_ganado*$aporte_laboral_porcentaje_total/100;
  $total_bono_antiguedad_x=number_format($total_bono_antiguedad,2,'.','');
  // $stmt = null;
  // $dbh = null;
  return $total_bono_antiguedad_x;

}
function obtenerTotalBonos($codigo_personal,$dias_trabajados_asistencia,$dias_trabajados_por_defecto,$cod_gestion,$mes)
{  
  // $mes=date('m');
  // $gestion=date('Y');

  // $mes=11;
  // $gestion=2019;

  $dbh = new Conexion();
  // $sqlGestion = "SELECT codigo from gestiones where nombre=$gestion";
  // $stmtGestion = $dbh->prepare($sqlGestion);
  // $stmtGestion->execute();
  // $resultGestion=$stmtGestion->fetch();
  // $cod_gestion = $resultGestion['codigo'];


  // $sqlBonos = "SELECT SUM(monto) as total_bonos from bonos_personal_mes 
  // where cod_personal = $codigo_personal and cod_gestion=$cod_gestion and cod_mes=$mes and cod_estadoreferencial=1";
  // $stmtBonos = $dbh->prepare($sqlBonos);
  // $stmtBonos->execute();
  // $resultBonos=$stmtBonos->fetch();
  // $total_bonos = $resultBonos['total_bonos'];
  $total_bonos1=0;
  $total_bonos2=0;

  $sqlBonos1 = "SELECT bpm.monto
  from bonos_personal_mes bpm,bonos b
  where bpm.cod_bono=b.codigo and bpm.cod_personal=$codigo_personal and bpm.cod_gestion=$cod_gestion and bpm.cod_mes=$mes and bpm.cod_estadoreferencial=1 and b.cod_tipocalculobono=1";
  $stmtBonos1 = $dbh->prepare($sqlBonos1);
  $stmtBonos1->execute();
  $stmtBonos1->bindColumn('monto',$monto1);
  while ($row = $stmtBonos1->fetch()) 
  {
    $total_bonos1=$total_bonos1+$monto1;
  }
    $sqlBonos2 = "SELECT bpm.monto
  from bonos_personal_mes bpm,bonos b
  where bpm.cod_bono=b.codigo and bpm.cod_personal=$codigo_personal and bpm.cod_gestion=$cod_gestion and bpm.cod_mes=$mes and bpm.cod_estadoreferencial=1 and b.cod_tipocalculobono=2";
  $stmtBonos2 = $dbh->prepare($sqlBonos2);
  $stmtBonos2->execute();
  $stmtBonos2->bindColumn('monto',$monto2);
  while ($row = $stmtBonos2->fetch()) 
  {
    $porcen_monto=$dias_trabajados_asistencia*100/$dias_trabajados_por_defecto;
    $monto2_aux=$porcen_monto*$monto2/100;
    $total_bonos2=$total_bonos2+$monto2_aux;
  }
  $total_bonos=$total_bonos1+$total_bonos2;

  $stmtGestion = null;
  $stmtBonos1 = null;
  $dbh = null;
  return $total_bonos;
}
function obtenerAporteAFP($total_ganado){
  $aporte_laboral_porcentaje_total=0;
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT valor_configuracion from configuraciones_planillas where id_configuracion in (12,13,14,15)");
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
    $valor_configuracion=$row['valor_configuracion'];
    $aporte_laboral_porcentaje_total+=$valor_configuracion;
  } 
  $aporte_laboral_aux=$total_ganado*$aporte_laboral_porcentaje_total/100;
  $aporte_laboral=number_format($aporte_laboral_aux,2,'.','');
  $stmt = null;
  $dbh = null;
  return($aporte_laboral);
}

function obtenerAporteSolidario13000($total_ganado){
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT valor_configuracion from configuraciones_planillas where id_configuracion=2");
  $stmt->execute();
  $result=$stmt->fetch();
  $valor_configuracion=$result['valor_configuracion'];
  if($total_ganado>13000){
    $aporte_solidario_13000_aux=($total_ganado-13000)*$valor_configuracion/100;
  }else $aporte_solidario_13000_aux = 0;
  $aporte_solidario_13000=number_format($aporte_solidario_13000_aux,2,'.','');
  $stmt = null;
  $dbh = null;
  return($aporte_solidario_13000);
}
function obtenerAporteSolidario25000($total_ganado){
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT valor_configuracion from configuraciones_planillas where id_configuracion=3");
  $stmt->execute();
  $result=$stmt->fetch();
  $valor_configuracion=$result['valor_configuracion'];
  if($total_ganado>25000){
    $aporte_solidario_25000_aux=($total_ganado-25000)*$valor_configuracion/100;
  }else $aporte_solidario_25000_aux = 0;
  $aporte_solidario_25000=number_format($aporte_solidario_25000_aux,2,'.','');
  $stmt = null;
  $dbh = null;
  return($aporte_solidario_25000);
}
function obtenerAporteSolidario35000($total_ganado){
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT valor_configuracion from configuraciones_planillas where id_configuracion=4");
  $stmt->execute();
  $result=$stmt->fetch();
  $valor_configuracion=$result['valor_configuracion'];
  if($total_ganado>35000){
    $aporte_solidario_35000_aux=($total_ganado-35000)*$valor_configuracion/100;
  }else $aporte_solidario_35000_aux = 0;
  $aporte_solidario_35000=number_format($aporte_solidario_35000_aux,2,'.','');
  $stmt = null;
  $dbh = null;
  return($aporte_solidario_35000);
}
function obtenerRC_IVA($total_ganado,$apf_f,$afp_p,$ap_sol_13000,$ap_sol_25000,$ap_sol_35000)
{
  $sueldo=$total_ganado-$apf_f-$afp_p-$ap_sol_13000-$ap_sol_25000-$ap_sol_35000;
  $sueldo_neto=number_format($sueldo);
  $minimo_no_imponible=4244; // no definido m10
  $datosf39=10;//no definido

  if($sueldo_neto>$minimo_no_imponible)$sueldo_gravado=$sueldo_neto-$minimo_no_imponible;
  else $sueldo_gravado=0;
  $fisco=$sueldo_gravado*$datosf39/100;
  $t10=400;//no definido
  $datosC40=0;//no definido
  $datosC41=0;//no definido
  $u10=number_format(($t10*$datosC40)/($datosC41-$t10));
  $v10=$t10+$u10;
  $s10=0;//no definido
  $w10 = $v10 + $s10;
  if($fisco>$w10)$aporte_rc_iva = $fisco-$w10;
  else $aporte_rc_iva = 0;
  $aporte_rc_iva_neto=number_format($aporte_rc_iva);

  $stmt = null;
  $dbh = null;
  return ($aporte_rc_iva_neto);
}

function obtenerAtrasoPersonal($id_personal,$haber_basico,$cod_gestion,$mes){
  $dbh = new Conexion();
  set_time_limit(300);
  //capturando fecha
  // $mes=date('m');
  // $gestion=date('Y');
  // $mes=11;
  // $gestion=2019;

   $dbh = new Conexion();
  // $sqlGestion = "SELECT codigo from gestiones where nombre=$gestion";
  // $stmtGestion = $dbh->prepare($sqlGestion);
  // $stmtGestion->execute();
  // $resultGestion=$stmtGestion->fetch();
  // $cod_gestion = $resultGestion['codigo'];

  $porcentaje_diahaber_x=0;
  $stmt = $dbh->prepare("SELECT minutos_retraso from retrasos_personal 
   where cod_personal=$id_personal and cod_mes=$mes and cod_gestion=$cod_gestion");
  $stmt->execute();
  $result=$stmt->fetch();
  $minutos_retraso=$result['minutos_retraso'];

  $stmtPoliticaRetraso = $dbh->prepare("SELECT minutos_inicio,minutos_final,porcentaje_diahaber 
    from politica_descuentoretrasos where cod_estadoreferencial=1");
  $stmtPoliticaRetraso->execute();
  while ($row = $stmtPoliticaRetraso->fetch(PDO::FETCH_ASSOC)) {
      $minutos_inicio=$row['minutos_inicio'];
      $minutos_final=$row['minutos_final'];
      $porcentaje_diahaber=$row['porcentaje_diahaber'];

      if($minutos_inicio<=$minutos_retraso and $minutos_retraso<=$minutos_final )
      {
        $porcentaje_diahaber_x=$porcentaje_diahaber;
      }
  }
  $dia_haber=$haber_basico/30;
  $descuentos_neto=$dia_haber*$porcentaje_diahaber_x/100;
  $stmt = null;
  $dbh = null;
  return ($descuentos_neto);
}
function obtenerOtrosDescuentos($codigo_personal,$cod_gestion,$mes)
{  
  // $mes=date('m');
  // $gestion=date('Y');

  // $mes=11;
  // $gestion=2019;

  $dbh = new Conexion();
  // $sqlGestion = "SELECT codigo from gestiones where nombre=$gestion";
  // $stmtGestion = $dbh->prepare($sqlGestion);
  // $stmtGestion->execute();
  // $resultGestion=$stmtGestion->fetch();
  // $cod_gestion = $resultGestion['codigo'];


  $sqlBonos = "SELECT SUM(monto) as total_descuentos_otros from descuentos_personal_mes 
  where cod_personal = $codigo_personal and cod_gestion=$cod_gestion and cod_mes=$mes and cod_estadoreferencial=1";
  $stmtBonos = $dbh->prepare($sqlBonos);
  $stmtBonos->execute();
  $resultBonos=$stmtBonos->fetch();
  $total_descuentos_otros = $resultBonos['total_descuentos_otros'];
  $stmtGestion = null;
  $stmtBonos = null;
  $dbh = null;
  return $total_descuentos_otros;
}
function obtenerDotaciones($codigo_personal,$cod_gestion_x,$cod_mes_x){
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT SUM(dpm.monto_mes) as monto_mes_dotacion
  from dotaciones_personal_mes dpm, dotaciones_personal dm
  where dpm.cod_gestion=$cod_gestion_x and dpm.cod_mes=$cod_mes_x and dm.cod_personal=$codigo_personal
  and dpm.cod_dotacionpersonal=dm.codigo");
    $stmt->execute();
    $resultado=$stmt->fetch();
    $monto_mes_dotacion=$resultado['monto_mes_dotacion'];
    $monto_mes_dotacion_x=number_format($monto_mes_dotacion,2,'.','');
    return $monto_mes_dotacion_x;
}
function obtenerAnticipo($id_personal,$cod_gestion,$mes)
{
  $anticipo=0;
  
  // $mes=date('m');
  // $gestion=date('Y');
  // $mes=11;
  // $gestion=2019;

  $dbh = new Conexion();
  // $sqlGestion = "SELECT codigo from gestiones where nombre=$gestion";
  // $stmtGestion = $dbh->prepare($sqlGestion);
  // $stmtGestion->execute();
  // $resultGestion=$stmtGestion->fetch();
  // $cod_gestion = $resultGestion['codigo'];

  // $fecha_inicio=$gestion."-".$mes."-01 00:00:00";
  // $fecha_actual=date('Y-m-d G:i:s');

  $dbh = new Conexion();
  $stmtAnticipos = $dbh->prepare("SELECT sum(monto)as total_anticipos
  FROM anticipos_personal
  WHERE cod_personal=$id_personal and cod_gestion = $cod_gestion and cod_mes=$mes");
  $stmtAnticipos->execute();
  $resultAnticipos=$stmtAnticipos->fetch();
  $anticipo=$resultAnticipos['total_anticipos'];
  if($anticipo==null){
    $anticipo=0;
  }
  $stmtGestion = null;
  $stmt = null;
  $dbh = null;  
  return ($anticipo);
}

function obtener_aporte_patronal_general($cod_config_planilla,$total_ganado){  
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT valor_configuracion from configuraciones_planillas where id_configuracion=$cod_config_planilla");
  $stmt->execute();
  $resultado=$stmt->fetch();
  $valor_configuracion=$resultado['valor_configuracion'];

  $aporte_p_seguro_medico=$total_ganado*$valor_configuracion/100;
  $aporte_p_seguro_medico_X=number_format($aporte_p_seguro_medico,2,'.','');

  //cerramos
  $stmt = null;
  $dbh = null;
  return($aporte_p_seguro_medico_X);
}
//planillas aguinaldos
// function Verificar_si_corresponde_Aguinaldo($ing_contr){
//   $anio_actual= date('Y');
//   // $anio_actual=2019;
//   $fechaComoEntero = strtotime($ing_contr);
//   $anio_ingreso = date("Y", $fechaComoEntero);
//   $mes_ingreso = date("m", $fechaComoEntero);
//   $diferencia_anios=$anio_actual-$anio_ingreso;
//   $diferencia_meses=12-$mes_ingreso;
//   if($diferencia_anios>0){
//     $sw=1;
//   }elseif($diferencia_meses>2){
//     $sw=1;
//   }else $sw=0;
//   return $sw;
// }
function obtener_anios_trabajados($ing_contr){
  $anio_actual= date('Y');
  // $anio_actual=2019;
  $fechaComoEntero = strtotime($ing_contr);
  $anio_ingreso = date("Y", $fechaComoEntero);
  $diferencia_anios=$anio_actual-$anio_ingreso;
  return $diferencia_anios;
}
function obtener_meses_trabajados($ing_contr){
  $fechaComoEntero = strtotime($ing_contr);  
  $mes_ingreso = date("m", $fechaComoEntero);
  $diferencia_meses=12-$mes_ingreso;
  // if($diferencia_anios>0){
  //   $sw=1;
  // }elseif($diferencia_meses>2){
  //   $sw=1;
  // }else $sw=0;
  return $diferencia_meses;
}
function obtener_dias_trabajados($ing_contr){
  $fechaComoEntero = strtotime($ing_contr);
  $dia_ingreso = date("d", $fechaComoEntero);  
  $diferencia_dias=30-$dia_ingreso;
  if($diferencia_dias<0){
    $diferencia_dias=0;
  }

  // if($diferencia_anios>0){
  //   $sw=1;
  // }elseif($diferencia_meses>2){
  //   $sw=1;
  // }else $sw=0;
  return $diferencia_dias;
}

function obtener_id_planilla($cod_gestion,$cod_mes){
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT codigo from planillas
  where cod_gestion=$cod_gestion and cod_mes=$cod_mes");
  $stmt->execute();
  $result=$stmt->fetch();
  $codigo=$result['codigo'];
  $dbh = '';
  $stmt = '';
  return ($codigo);
}
function obtenerSueldomes($cod_personal,$cod_planilla){
  $dbh = new Conexion();
  set_time_limit(300);
  $stmt = $dbh->prepare("SELECT liquido_pagable from planillas_personal_mes
  where cod_planilla=$cod_planilla and cod_personalcargo=$cod_personal");
  $stmt->execute();
  $result=$stmt->fetch();
  $liquido_pagable=$result['liquido_pagable'];
  $dbh = '';
  $stmt = '';
  return ($liquido_pagable);
}

//=======
function obtenerMontoPlantillaDetalle($codigoPar,$codigo,$ib){
  $dbh = new Conexion();
  $montoI=0;$montoF=0;
   $stmt = $dbh->prepare("SELECT pd.monto_local,pd.monto_externo FROM plantillas_grupocostodetalle pd 
    join plantillas_gruposcosto pg on pg.codigo=pd.cod_plantillagrupocosto
    join plantillas_costo p on p.codigo=pg.cod_plantillacosto
    where pd.cod_partidapresupuestaria=:codigo and p.codigo=:codPlan");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->bindParam(':codPlan',$codigoPar);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $montoI=$row['monto_local'];
      $montoF=$row['monto_externo'];
   }
   if($ib==1){
     return($montoI);
   }else{
     return($montoF);
   }
}
function obtenerMontoPlantillaDetalleServicio($codigoPar,$codigo,$ib){
  $dbh = new Conexion();
  $montoI=0;$montoF=0;
   $stmt = $dbh->prepare("SELECT pd.monto_local,pd.monto_externo FROM plantillas_gruposerviciodetalle pd 
    join plantillas_gruposervicio pg on pg.codigo=pd.cod_plantillagruposervicio
    join plantillas_servicios p on p.codigo=pg.cod_plantillaservicio
    where pd.cod_partidapresupuestaria=:codigo and p.codigo=:codPlan");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->bindParam(':codPlan',$codigoPar);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $montoI=$row['monto_local'];
      $montoF=$row['monto_externo'];
   }
   if($ib==1){
     return($montoI);
   }else{
     return($montoF);
   }
}
function obtenerMontoSimulacionCuenta($codigo,$codigoPar,$ib){
  $dbh = new Conexion();
  $montoI=0;$montoF=0;
   $stmt = $dbh->prepare("SELECT sum(monto_local) as total_local, sum(monto_externo) as total_externo 
    FROM cuentas_simulacion where cod_simulacioncostos=:codSim and cod_partidapresupuestaria=:codPar");
   $stmt->bindParam(':codSim',$codigo);
   $stmt->bindParam(':codPar',$codigoPar);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $montoI=$row['total_local'];
      $montoF=$row['total_externo'];
   }
   if($ib==1){
     return($montoI);
   }else{
     return($montoF);
   }
}
function obtenerMontoSimulacionCuentaServicio($codigo,$codigoPar,$ib){
  $dbh = new Conexion();
  $montoI=0;$montoF=0;
   $stmt = $dbh->prepare("SELECT sum(monto_local) as total_local, sum(monto_externo) as total_externo 
    FROM cuentas_simulacion where cod_simulacionservicios=:codSim and cod_partidapresupuestaria=:codPar");
   $stmt->bindParam(':codSim',$codigo);
   $stmt->bindParam(':codPar',$codigoPar);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $montoI=$row['total_local'];
      $montoF=$row['total_externo'];
   }
   if($ib==1){
     return($montoI);
   }else{
     return($montoF);
   }
}
function obtenerTotalesSimulacion($codigo){
  $dbh = new Conexion();
    $montoI=1;$montoF=1;
   $stmt = $dbh->prepare("SELECT sum(monto_local) as total_local, sum(monto_externo) as total_externo 
    FROM cuentas_simulacion where cod_simulacioncostos=:codSim");
   $stmt->bindParam(':codSim',$codigo);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $montoI=$row['total_local'];
      $montoF=$row['total_externo'];
   }
  return array(0,0,$montoI,$montoF);
}
function obtenerTotalesSimulacionServicio($codigo){
  $dbh = new Conexion();
    $montoI=1;$montoF=1;
   $stmt = $dbh->prepare("SELECT sum(monto_local) as total_local, sum(monto_externo) as total_externo 
    FROM cuentas_simulacion where cod_simulacionservicios=:codSim");
   $stmt->bindParam(':codSim',$codigo);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $montoI=$row['total_local'];
      $montoF=$row['total_externo'];
   }
  return array(0,0,$montoI,$montoF);
}
function obtenerTotalesSimulacionServicioPeriodo($codigo,$anio){
  $dbh = new Conexion();
    $montoI=1;$montoF=1;
   $stmt = $dbh->prepare("SELECT sum(monto_local) as total_local, sum(monto_externo) as total_externo 
    FROM cuentas_simulacion where cod_simulacionservicios=:codSim and cod_anio=:anio");
   $stmt->bindParam(':codSim',$codigo);
   $stmt->bindParam(':anio',$anio);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $montoI=$row['total_local'];
      $montoF=$row['total_externo'];
   }
  return array(0,0,$montoI,$montoF);
}

function obtenerIbnorcaCheck($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT ibnorca FROM simulaciones_costos where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['ibnorca'];
   }
   return($nombreX);
}

function obtenerValorRefrigerio(){
  //Seleccionar el monto de refrigerio de configuraciones
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT c.valor_configuracion FROM configuraciones c WHERE c.id_configuracion=10");
  $stmt->execute();
  $stmt->bindColumn('valor_configuracion', $valorConfiguracion);

  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    $valorConfiguracionX = $valorConfiguracion;
  }

  return($valorConfiguracionX);
}

function calculaMontoDescuentoRetraso($minutos_retraso, $codigoPersona)
{
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT porcentaje_diahaber from politica_descuentoretrasos
where cod_estadoreferencial=1 and $minutos_retraso between minutos_inicio and minutos_final");
  $stmt->execute();
  $stmt->bindColumn('porcentaje_diahaber', $porcentaje_diahaber);

  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    $porcentaje_diahaberX = $porcentaje_diahaber;
  }
$dias_trabajadosX=null;$haber_basicoX=null;
  $stmtb = $dbh->prepare("SELECT dias_trabajados,haber_basico 
from planillas_personal_mes where cod_personalcargo=$codigoPersona");
  $stmtb->execute();
  $stmtb->bindColumn('dias_trabajados', $dias_trabajados);
  $stmtb->bindColumn('haber_basico', $haber_basico);
  
  while ($row = $stmtb->fetch(PDO::FETCH_BOUND)) {
    $dias_trabajadosX = $dias_trabajados;
    $haber_basicoX = $haber_basico;

    $montoRetraso = ($haber_basicoX / $dias_trabajadosX) * ($porcentaje_diahaberX / 100);
  }

  if($dias_trabajadosX==null && $haber_basicoX==null){
    $montoRetraso=null;
  }
  return ($montoRetraso);
}

function calcularHaberBasicoPorPersona($codigo){
  $haberX=null;
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT haber_basico from personal
where cod_estadoreferencial=1 and codigo='$codigo'");
  $stmt->execute();
  $stmt->bindColumn('haber_basico', $haber);

  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    $haberX = $haber;
  }

  return $haberX;
}

//FUNCIONES DE REPORTE
function obtenerPlanillaSueldosRevision($codigo,$cod_area_x,$cod_uo_x){
  $dbh = new Conexion();
  $sql="SELECT p.codigo,p.cod_area,a.nombre as area, CONCAT(p.primer_nombre,' ', p.otros_nombres) as nombres,CONCAT(p.paterno,' ', p.materno) as apellidos,
  p.identificacion as ci,p.ing_planilla,c.nombre as cargo,pm.haber_basico,
  pm.dias_trabajados,pm.bono_academico,pm.bono_antiguedad,pm.total_ganado,pm.monto_descuentos,pm.liquido_pagable,pm.afp_1,pm.afp_2,pad.porcentaje
  FROM personal p
  join cargos c on p.cod_cargo=c.codigo
  join planillas_personal_mes pm on pm.cod_personalcargo=p.codigo
  join areas a on p.cod_area=a.codigo
  join personal_area_distribucion pad on pm.cod_personalcargo=pad.cod_personal
  
  where pm.cod_planilla=$codigo and pad.cod_uo=$cod_uo_x and pad.cod_area=$cod_area_x order by a.nombre";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  return $stmt;
}
function obtenerPlanillaSueldoRevisionBonos($cod_personalcargo,$cod_gestion,$cod_mes,$dias_trabajados_asistencia,$dias_trabajados){
  $total_bonos1=0;
  $total_bonos2=0;
  $dbh = new Conexion();
  set_time_limit(300);
  $sqlBonos1 = "SELECT bpm.monto
  from bonos_personal_mes bpm,bonos b
  where bpm.cod_bono=b.codigo and bpm.cod_personal=$cod_personalcargo and bpm.cod_gestion=$cod_gestion and bpm.cod_mes=$cod_mes and bpm.cod_estadoreferencial=1 and b.cod_tipocalculobono=1";
  $stmtBonos1 = $dbh->prepare($sqlBonos1);
  $stmtBonos1->execute();
  $stmtBonos1->bindColumn('monto',$monto1);
  while ($row = $stmtBonos1->fetch()) 
  {
    $total_bonos1+=$monto1;
  }
    $sqlBonos2 = "SELECT bpm.monto
  from bonos_personal_mes bpm,bonos b
  where bpm.cod_bono=b.codigo and bpm.cod_personal=$cod_personalcargo and bpm.cod_gestion=$cod_gestion and bpm.cod_mes=$cod_mes and bpm.cod_estadoreferencial=1 and b.cod_tipocalculobono=2";
  $stmtBonos2 = $dbh->prepare($sqlBonos2);
  $stmtBonos2->execute();
  $stmtBonos2->bindColumn('monto',$monto2);
  while ($row = $stmtBonos2->fetch()) 
  {
    $porcen_monto=$dias_trabajados_asistencia*100/$dias_trabajados;
    $monto2_aux=$porcen_monto*$monto2/100;
    $total_bonos2+=$monto2_aux;
  }
  $sumaBono_otros=$total_bonos1+$total_bonos2;
  return $sumaBono_otros;
}
//FUNCIONES DE REPORTE
function obtenerPlanillaTributariaReporte($codigo){
  $dbh = new Conexion();
  $sql="SELECT p.cod_area,a.nombre as area, CONCAT(p.primer_nombre,' ', p.otros_nombres) as nombres,CONCAT(p.paterno,' ', p.materno) as apellidos,
  p.identificacion as ci,p.ing_planilla,c.nombre as cargo,
  pm.*
  FROM personal p
  join cargos c on p.cod_cargo=c.codigo
  join planillas_tributarias_personal_mes pm on pm.cod_personal=p.codigo
  join areas a on p.cod_area=a.codigo where pm.cod_planillatributaria=$codigo order by a.nombre";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  return $stmt;;
}
 //funcion para descargar con dompdf
function descargarPDFHorizontal($nom,$html){
  //aumentamos la memoria  
  ini_set("memory_limit", "128M");
  // Cargamos DOMPDF
  require_once 'assets/libraries/dompdf/dompdf_config.inc.php';
  $mydompdf = new DOMPDF();
  $mydompdf->set_paper('legal', 'landscape');
  ob_clean();
  $mydompdf->load_html($html);
  $mydompdf->render();
  $canvas = $mydompdf->get_canvas();
  $canvas->page_text(730, 25, "Página:    {PAGE_NUM}", Font_Metrics::get_font("sans-serif"), 10, array(0,0,0)); 
  $mydompdf->set_base_path('assets/libraries/plantillaPDF.css');
  $mydompdf->stream($nom.".pdf", array("Attachment" => false));
} 


function descargarPDF($nom,$html){
  //aumentamos la memoria  
  ini_set("memory_limit", "128M");
  // Cargamos DOMPDF
  require_once 'assets/libraries/dompdf/dompdf_config.inc.php';
  $mydompdf = new DOMPDF();
  ob_clean();
  $mydompdf->load_html($html);
  $mydompdf->render();
  $canvas = $mydompdf->get_canvas();
       /* if ( isset($canvas) ) {
          $numero="{PAGE_NUM}";$numeroF="{PAGE_COUNT}"; 
          if ((int)$numero==(int)$numeroF) {
            $font = Font_Metrics::get_font("helvetica", "normal");
                $size = 9;
                $y = 50;
                $x = 100;
                $canvas->page_text($x, $y, "pie de pagina en la ultima hoja".$numero.$numeroF, $font, $size);
            }
        }*/
  $canvas->page_text(500, 25, "Página:            {PAGE_NUM}", Font_Metrics::get_font("sans-serif"), 10, array(0,0,0)); 
  $mydompdf->set_base_path('assets/libraries/plantillaPDF.css');
  $mydompdf->stream($nom.".pdf", array("Attachment" => false));
}
function descargarPDFSolicitudesRecursos($nom,$html){
  //aumentamos la memoria  
  ini_set("memory_limit", "128M");
  // Cargamos DOMPDF
  require_once 'assets/libraries/dompdf/dompdf_config.inc.php';
  $mydompdf = new DOMPDF();
  ob_clean();
  $mydompdf->load_html($html);
  $mydompdf->render();
  $canvas = $mydompdf->get_canvas();
  $canvas->page_text(450, 763, "Página:  {PAGE_NUM} de {PAGE_COUNT}", Font_Metrics::get_font("sans-serif"), 9, array(0,0,0)); 
  $mydompdf->set_base_path('assets/libraries/plantillaPDFSolicitudesRecursos.css');
  $mydompdf->stream($nom.".pdf", array("Attachment" => false));
}

function descargarPDFOfertaPropuesta($nom,$html){
  //aumentamos la memoria  
  ini_set("memory_limit", "128M");
  // Cargamos DOMPDF
  require_once 'assets/libraries/dompdf/dompdf_config.inc.php';
  $mydompdf = new DOMPDF();
  ob_clean();
  $mydompdf->load_html($html);
  $mydompdf->render();
  $canvas = $mydompdf->get_canvas();
  $canvas->page_text(490, 753, "PÁGINA {PAGE_NUM} de {PAGE_COUNT}", Font_Metrics::get_font("helvetica","bold"),7, array(255,255,255)); 
  $mydompdf->set_base_path('assets/libraries/plantillaPDFOfertaPropuesta.css');
  $mydompdf->stream($nom.".pdf", array("Attachment" => false));
}

function descargarPDF1($nom,$html){
  //aumentamos la memoria  
  ini_set("memory_limit", "128M");
  // Cargamos DOMPDF
  require_once 'assets/libraries/dompdf/dompdf_config.inc.php';
  $mydompdf = new DOMPDF();
  ob_clean();
  $mydompdf->load_html($html);
  $mydompdf->render();
  $canvas = $mydompdf->get_canvas();
  $canvas->page_text(500, 25, "", Font_Metrics::get_font("sans-serif"), 10, array(0,0,0)); 
  $mydompdf->set_base_path('assets/libraries/plantillaPDF.css');
  $mydompdf->stream($nom.".pdf", array("Attachment" => false));
}
function descargarPDFCajaChica($nom,$html){
  //aumentamos la memoria  
  ini_set("memory_limit", "128M");
  // Cargamos DOMPDF
  require_once 'assets/libraries/dompdf/dompdf_config.inc.php';
  $mydompdf = new DOMPDF();
  ob_clean();
  $mydompdf->load_html($html);
  $mydompdf->render();
  $canvas = $mydompdf->get_canvas();
  $canvas->page_text(500, 25, "", Font_Metrics::get_font("sans-serif"), 10, array(0,0,0)); 
  $mydompdf->set_base_path('assets/libraries/plantillaPDFCajaChica.css');
  $mydompdf->stream($nom.".pdf", array("Attachment" => false));
}
function descargarPDFFacturas($nom,$html,$codFactura){
  //aumentamos la memoria  
  ini_set("memory_limit", "128M");
  // Cargamos DOMPDF
  require_once 'assets/libraries/dompdf/dompdf_config.inc.php';
  $mydompdf = new DOMPDF();
  ob_clean();
  $mydompdf->load_html($html);
  $mydompdf->set_paper("A4", "portrait");
  $mydompdf->render();
  
  $estado=obtener_estado_facturas($codFactura);
  if($estado==2){ //facturas anuladas MARCA DE AGUA ANULADO
    //marca de agua
    $canvas2 = $mydompdf->get_canvas(); 
    $w = $canvas2->get_width(); 
    $h = $canvas2->get_height(); 
    $font = Font_Metrics::get_font("times"); 
    $text = "ANULADO"; 
    $txtHeight = -100; 
    $textWidth = 250; 
    $canvas2->set_opacity(.2); 
    $x = (($w-$textWidth)/2); 
    $y = (($h-$txtHeight)/2); 
    $canvas2->text($x, $y, $text, $font, 100, $color = array(167, 14, 14), $word_space = 0.0, $char_space = 0.0, $angle = -45);
  //fin marca agua
  } 

  $canvas = $mydompdf->get_canvas();
  $canvas->page_text(500, 25, "", Font_Metrics::get_font("sans-serif"), 10, array(0,0,0));   
  $mydompdf->set_base_path('assets/libraries/plantillaPDFFactura.css');
  $mydompdf->stream($nom.".pdf", array("Attachment" => false));
  //guardar pdf
  // $pdf = $mydompdf->output();
  // file_put_contents("../simulaciones_servicios/facturas/".$nom.".pdf", $pdf);
}
function descargarPDFFacturasCopiaCliente($nom,$html,$codFactura){
  //aumentamos la memoria  
  ini_set("memory_limit", "128M");
  // Cargamos DOMPDF
  require_once 'assets/libraries/dompdf/dompdf_config.inc.php';
  // $mydompdf = new DOMPDF();
  // ob_clean();
  // $mydompdf->load_html($html);
  // $mydompdf->render();
  // $canvas = $mydompdf->get_canvas();
  // $canvas->page_text(500, 25, "", Font_Metrics::get_font("sans-serif"), 10, array(0,0,0)); 
  // $mydompdf->set_base_path('assets/libraries/plantillaPDFFactura.css');
  // $mydompdf->stream($nom.".pdf", array("Attachment" => false));
  // //guardar pdf
  // $pdf = $mydompdf->output();
  // file_put_contents("../simulaciones_servicios/facturas/".$nom.".pdf", $pdf);
    $dompdf = new DOMPDF();
    // $dompdf->set_paper("letter", "portrait");
    $dompdf->set_paper("A4", "portrait");
    $dompdf->load_html($html);    
    $dompdf->render();

    $estado=obtener_estado_facturas($codFactura);
  if($estado==2){ //facturas anuladas MARCA DE AGUA ANULADO
    //marca de agua
    $canvas2 = $dompdf->get_canvas(); 
    $w = $canvas2->get_width(); 
    $h = $canvas2->get_height(); 
    $font = Font_Metrics::get_font("times"); 
    $text = "ANULADO"; 
    $txtHeight = -100; 
    $textWidth = 250; 
    $canvas2->set_opacity(.2); 
    $x = (($w-$textWidth)/2); 
    $y = (($h-$txtHeight)/2); 
    $canvas2->text($x, $y, $text, $font, 100, $color = array(167, 14, 14), $word_space = 0.0, $char_space = 0.0, $angle = -45);
  //fin marca agua
   } 
    $pdf = $dompdf->output();
    file_put_contents("../simulaciones_servicios/facturas/".$nom.".pdf", $pdf);
}

function descargarPDFFiniquito($nom,$html){
  //aumentamos la memoria  
  ini_set("memory_limit", "128M");
  // Cargamos DOMPDF
  require_once 'assets/libraries/dompdf/dompdf_config.inc.php';
  $mydompdf = new DOMPDF();
  ob_clean();
  $mydompdf->load_html($html);
  $mydompdf->render();
  $canvas = $mydompdf->get_canvas();
       /* if ( isset($canvas) ) {
          $numero="{PAGE_NUM}";$numeroF="{PAGE_COUNT}"; 
          if ((int)$numero==(int)$numeroF) {
            $font = Font_Metrics::get_font("helvetica", "normal");
                $size = 9;
                $y = 50;
                $x = 100;
                $canvas->page_text($x, $y, "pie de pagina en la ultima hoja".$numero.$numeroF, $font, $size);
            }
        }*/
  $canvas->page_text(500, 25, "", Font_Metrics::get_font("sans-serif"), 10, array(0,0,0)); 
  $mydompdf->set_base_path('assets/libraries/plantillaPDF.css');
  $mydompdf->stream($nom.".pdf", array("Attachment" => false));
}


function obtenerSueldoMinimo(){
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT * from configuraciones_planillas where id_configuracion=1");
  $stmt->execute();
  $result= $stmt->fetch();
  $monto=$result['valor_configuracion'];
  return $monto;
}
function obtenerValorConfiguracionPlanillas($cod){
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT * from configuraciones_planillas where id_configuracion=$cod");
  $stmt->execute();
  $result= $stmt->fetch();
  $valor=$result['valor_configuracion'];
  return $valor;
}
function obtenerRcIvaPersonal($cod_persona,$cod_mes,$cod_gestion){
  $monto=0;
  $dbh = new Conexion();
  set_time_limit(300);
  $stmt = $dbh->prepare("SELECT monto_iva from rc_ivapersonal where cod_personal='$cod_persona' and cod_mes=$cod_mes and cod_gestion=$cod_gestion");
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $monto = $row['monto_iva'];
  }
  if($monto==null){
    $monto=0;
  }
  return $monto;
}
function obtenerSaldoMesAnteriorTrib($cod_persona,$cod_mes,$cod_gestion){
  $monto=0;
  $dbh = new Conexion();
  set_time_limit(300);
  $stmt = $dbh->prepare("SELECT ptd.total_saldo_favordependiente from planillas_tributarias_personal_mes ptd join planillas_tributarias pt on pt.codigo=ptd.cod_planillatributaria where ptd.cod_personal='$cod_persona' and pt.cod_mes='$cod_mes' and pt.cod_gestion='$cod_gestion'");
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $monto = $row['total_saldo_favordependiente'];
  }
  if($monto==null){
    $monto=0;
  }
  return $monto;
}


function bonosIndefinidos(){
  $mesActual=date("m");
 $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT * from bonos_personal_mes where indefinido=1");
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codigo=$row['codigo'];
    $codBono=$row['cod_bono'];
    $codPersona=$row['cod_personal'];
    $monto=$row['monto'];
    $observaciones=$row['observaciones'];
    $codEstado=$row['cod_estadoreferencial'];
    $indefinido=$row['indefinido'];
    if($row['cod_mes']<12){
      $nuevoMes=$row['cod_mes'];
      $gestion=$row['cod_gestion'];
    }else{
      $nuevoMes=$row['cod_mes'];
      //$gestion=(int)nameGestion($row['cod_gestion']);
      $gestion=$row['cod_gestion']+1;
    }
    if($row['cod_mes']<(int)$mesActual){
        $stmt2 = $dbh->prepare("INSERT INTO bonos_personal_mes (cod_bono, cod_personal,cod_gestion,cod_mes,monto,observaciones,indefinido, cod_estadoreferencial) 
                      VALUES ('$codBono','$codPersona','$gestion','$nuevoMes','$monto','$observaciones',1, '$codEstado')");
        $stmt2->execute();
        $stmt3 = $dbh->prepare("UPDATE bonos_personal_mes SET indefinido=0 where codigo=$codigo");
        $stmt3->execute();  

    }
  }
}

function enviarNotificacionesSistema($tipoContrato){

  $mesActual=date("m");
  $fechaActual=date("Y-m-d");
 $dbh = new Conexion();
  $sql = "SELECT es.*,p.email_empresa,concat(p.primer_nombre,' ',p.otros_nombres,' ',p.paterno,' ',p.materno) as personal,e.nombre,
  pc.fecha_iniciocontrato,pc.fecha_fincontrato,pc.codigo as contrato
  FROM eventos_sistemapersonal es 
  join personal_contratos pc on es.cod_personal=pc.cod_personal
  join personal p on es.cod_personal=p.codigo
  join eventos_sistema e on e.codigo=es.cod_eventosistema ";
  if($tipoContrato==1){
   $dias=obtenerValorConfiguracion(12); 
   $sql.="where pc.cod_tipocontrato=1 and pc.fecha_evaluacioncontrato='$fechaActual' and pc.bandera_notificacion=0";  
  }else{
   $dias=obtenerValorConfiguracion(11);
   $sql.="where pc.cod_tipocontrato!=1 and pc.fecha_evaluacioncontrato='$fechaActual' and pc.bandera_notificacion=0";
  }
  
    $stmt = $dbh->prepare($sql);
  $stmt->execute();

 $i=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
    $codigo=$row['codigo'];
    $correo=$row['email_empresa'];
    $titulo=$row['nombre'];
    $codContrato=$row['contrato'];
    $personal=strtoupper($row['personal']);
    $fechaInicio=strftime('%d/%m/%Y',strtotime($row['fecha_iniciocontrato']));
    if(trim($row['fecha_fincontrato'])!="INDEFINIDO"){
    $fechaFin=strftime('%d/%m/%Y',strtotime($row['fecha_fincontrato']));  
    }else{
      $fechaFin=$row['fecha_fincontrato'];
    }
    
    //contenido del mensaje
    $mensaje="Estimado(a) ".$personal;
    $mensaje.="<br>El presente contrato tiene como fecha de inicio: ".$fechaInicio. ", finaliza en fecha: ".$fechaFin;
    $mensaje.="<br>Saludos.";
  //datos del correo
    $mail_username="noresponse@minkasoftware.com";//Correo electronico saliente ejemplo: tucorreo@gmail.com
    $mail_userpassword="minka@2019";//Tu contraseña de gmail
    $mail_addAddress=$correo;//correo electronico que recibira el mensaje
    $template="notificaciones_sistema/PHPMailer/email_template.html";//Ruta de la plantilla HTML para enviar nuestro mensaje
      
      /*Inicio captura de datos enviados por $_POST para enviar el correo */
     $mail_setFromEmail=$mail_username;
     $mail_setFromName="IBNORCA";
     $txt_message=$mensaje;
     $mail_subject=$titulo; //el subject del mensaje
     $flag=sendemail($mail_username,$mail_userpassword,$mail_setFromEmail,$mail_setFromName,$mail_addAddress,$txt_message,$mail_subject,$template,$i);      
    $i++;
    $dbhUpdate = new Conexion();
     $sqlUpdate="UPDATE personal_contratos SET bandera_notificacion=1 where codigo=$codContrato";
     $stmtUpdate = $dbhUpdate->prepare($sqlUpdate);
     $stmtUpdate->execute();

  }

}

function obtenerPlantillaCodigoSimulacion($codigo){
  $dbh = new Conexion();
   $valor=0;
   $sql="SELECT cod_plantillacosto from simulaciones_costos where codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cod_plantillacosto'];
  }
  return $valor;
}
function obtenerPlantillaCodigoSimulacionServicio($codigo){
  $dbh = new Conexion();
   $valor=0;
   $sql="SELECT cod_plantillaservicio from simulaciones_servicios where codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cod_plantillaservicio'];
  }
  return $valor;
}

function obtenerNombrePersonal($codigo){
    $dbh = new Conexion();
   $valor=0;
   $sql="SELECT concat(p.paterno, ' ',p.materno, ' ', p.primer_nombre)as nombre from personal p where p.codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['nombre'];
  }
  return $valor;
}
function obtenerNombreDetalleSimulacionVariables($codigo){
    $dbh = new Conexion();
   $valor=0;
   $sql="SELECT glosa from simulaciones_serviciodetalle p where p.codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['glosa'];
  }
  return $valor;
}
function obtenerNombreDetalleSimulacionVariablesPeriodo($codigo,$anio){
    $dbh = new Conexion();
   $valor=0;
   $sql="SELECT glosa from simulaciones_serviciodetalle p where p.codigo=$codigo and p.cod_anio='$anio'";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['glosa'];
  }
  return $valor;
}
function obtenerValorConfiguracionEmpresa($codigo){
  $dbh = new Conexion();
  $valor="";
  $stmtConfiguracion = $dbh->prepare("SELECT valor_configuracion from configuraciones_empresa where id_configuracion=$codigo");
  $stmtConfiguracion->execute();
  $resultConfiguracion = $stmtConfiguracion->fetch();
  $valor=$resultConfiguracion['valor_configuracion'];
  return $valor; 
}
function obtenerValorConfiguracionFactura($codigo){
  $dbh = new Conexion();
  $valor="";
  $stmtConfiguracion = $dbh->prepare("SELECT valor from configuracion_facturas where id=$codigo");
  $stmtConfiguracion->execute();
  $resultConfiguracion = $stmtConfiguracion->fetch();
  $valor=$resultConfiguracion['valor'];
  return $valor; 
}
function obtenerTiempoDosFechas($fechaInicio,$fechafin){
  $datetime1=date_create($fechaInicio);
  $datetime2=date_create($fechaFin);
  $intervalo=date_diff($datetime1,$datetime2);
  $tiempo=array();
  foreach ($intervalo as $valor ) {
    $tiempo[]=$valor;
  }
  return $tiempo;
}

function obtenerPaisesServicioIbrnorca(){
  $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
  $sIde = "ifinanciero";
  $sKey = "ce94a8dabdf0b112eafa27a5aa475751";
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "TipoLista"=>"paises"); //Lista todos los paises
  $parametros=json_encode($parametros);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$direccion."clasificador/ws-paises.php"); // PRUEBA
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    curl_close ($ch);
    return json_decode($remote_server_output);
}
function obtenerDepartamentoServicioIbrnorca($cod){
  $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
  $sIde = "ifinanciero";
  $sKey = "ce94a8dabdf0b112eafa27a5aa475751";
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "TipoLista"=>"estados", "IdPais"=>$cod);
  $parametros=json_encode($parametros);
    $ch = curl_init();
    //curl_setopt($ch, CURLOPT_URL,$direccion."clasificador/ws-paises.php"); // OFICIAL
    curl_setopt($ch, CURLOPT_URL,$direccion."clasificador/ws-paises.php"); // PRUEBA
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    curl_close ($ch);
    return json_decode($remote_server_output);
}
function obtenerCiudadServicioIbrnorca($cod){
  $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
  $sIde = "ifinanciero";
  $sKey = "ce94a8dabdf0b112eafa27a5aa475751";
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "TipoLista"=>"ciudades", "IdEstado"=>$cod);
  $parametros=json_encode($parametros);
    $ch = curl_init();
    //curl_setopt($ch, CURLOPT_URL,$direccion."clasificador/ws-paises.php"); // OFICIAL
    curl_setopt($ch, CURLOPT_URL,$direccion."clasificador/ws-paises.php"); // PRUEBA
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    curl_close ($ch);
    return json_decode($remote_server_output);
}
function obtenerListaProveedoresDelServicio(){
  $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
  $sIde = "irrhh";
  $sKey = "c066ffc2a049cf11f9ee159496089a15";
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ListarProveedor"); 
    $parametros=json_encode($parametros);
    // abrimos la sesión cURL
    $ch = curl_init();
    // definimos la URL a la que hacemos la petición
    curl_setopt($ch, CURLOPT_URL,$direccion."rrhh/ws-personal-listas.php"); 
    // indicamos el tipo de petición: POST
    curl_setopt($ch, CURLOPT_POST, TRUE);
    // definimos cada uno de los parámetros
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    // recibimos la respuesta y la guardamos en una variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    // cerramos la sesión cURL
    curl_close ($ch);  
    return json_decode($remote_server_output);       
}
function obtenerListaProveedoresTipoPersona(){
  $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
  $sIde = "ifinanciero";
  $sKey = "ce94a8dabdf0b112eafa27a5aa475751";
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ListarPersonaxAtributo", "IdAtributo"=>2530); 
    $parametros=json_encode($parametros);
    // abrimos la sesión cURL
    $ch = curl_init();
    // definimos la URL a la que hacemos la petición
    curl_setopt($ch, CURLOPT_URL,$direccion."rrhh/ws-personal-listas.php"); 
    // indicamos el tipo de petición: POST
    curl_setopt($ch, CURLOPT_POST, TRUE);
    // definimos cada uno de los parámetros
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    // recibimos la respuesta y la guardamos en una variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    // cerramos la sesión cURL
    curl_close ($ch);  
    return json_decode($remote_server_output);       
}
function obtenerListaPersonalDocenteServicio(){
  $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
  // $sIde = "irrhh";
  // $sKey = "c066ffc2a049cf11f9ee159496089a15";  
  $sIde = "ifinanciero";
  $sKey = "ce94a8dabdf0b112eafa27a5aa475751";
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ListarPersonaxAtributo", "IdAtributo"=>354);
  $parametros=json_encode($parametros);
  // abrimos la sesión cURL
  $ch = curl_init();
  // definimos la URL a la que hacemos la petición
  curl_setopt($ch, CURLOPT_URL,$direccion."rrhh/ws-personal-listas.php"); 

  // indicamos el tipo de petición: POST
  curl_setopt($ch, CURLOPT_POST, TRUE);
  // definimos cada uno de los parámetros
  curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
  // recibimos la respuesta y la guardamos en una variable
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $remote_server_output = curl_exec ($ch);
  // cerramos la sesión cURL
  curl_close ($ch);  
  return json_decode($remote_server_output);
}
function obtenerListaPersonalAuditorServicio(){
  $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
  // $sIde = "irrhh";
  // $sKey = "c066ffc2a049cf11f9ee159496089a15";  
  $sIde = "ifinanciero";
  $sKey = "ce94a8dabdf0b112eafa27a5aa475751";
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ListarPersonaxAtributo", "IdAtributo"=>1621);
  $parametros=json_encode($parametros);
  // abrimos la sesión cURL
  $ch = curl_init();
  // definimos la URL a la que hacemos la petición
  curl_setopt($ch, CURLOPT_URL,$direccion."rrhh/ws-personal-listas.php"); 

  // indicamos el tipo de petición: POST
  curl_setopt($ch, CURLOPT_POST, TRUE);
  // definimos cada uno de los parámetros
  curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
  // recibimos la respuesta y la guardamos en una variable
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $remote_server_output = curl_exec ($ch);
  // cerramos la sesión cURL
  curl_close ($ch);  
  return json_decode($remote_server_output);
}
function obtenerListaPersonalConsultorServicio(){
  $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
  // $sIde = "irrhh";
  // $sKey = "c066ffc2a049cf11f9ee159496089a15";  
  $sIde = "ifinanciero";
  $sKey = "ce94a8dabdf0b112eafa27a5aa475751";
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ListarPersonaxAtributo", "IdAtributo"=>1622);
  $parametros=json_encode($parametros);
  // abrimos la sesión cURL
  $ch = curl_init();
  // definimos la URL a la que hacemos la petición
  curl_setopt($ch, CURLOPT_URL,$direccion."rrhh/ws-personal-listas.php"); 

  // indicamos el tipo de petición: POST
  curl_setopt($ch, CURLOPT_POST, TRUE);
  // definimos cada uno de los parámetros
  curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
  // recibimos la respuesta y la guardamos en una variable
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $remote_server_output = curl_exec ($ch);
  // cerramos la sesión cURL
  curl_close ($ch);  
  return json_decode($remote_server_output);
}
function obtenerTipodeCalculoRegistradoDetalle($codPlan,$codPar,$tp){
   $dbh = new Conexion();
  if($tp==1){
     $stmt = $dbh->prepare("SELECT tipo_registro from plantillas_servicios_detalle where cod_plantillacosto=$codPlan and cod_partidapresupuestaria=$codPar limit 1");
  }else{
     $stmt = $dbh->prepare("SELECT tipo_registro from plantillas_servicios_detalle where cod_plantillatcp=$codPlan and cod_partidapresupuestaria=$codPar limit 1");  
  }  
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['tipo_registro'];
   }
   return($valor);

}
function obtenerCostoTipoClienteSello($codSello,$codTipoC,$nacional){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT monto from costos_servicios_tipocliente where cod_configuracionserviciosello=$codSello and cod_tipocliente=$codTipoC and cod_tipoclientenacionalidad=$nacional");  
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['monto'];
   }
   return($valor);
}
function obtenerTipoCliente($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cod_tipocliente from clientes where codigo=$codigo");  
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cod_tipocliente'];
   }
   return($valor);
}
function obtenerTipoNacionalCliente($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cod_tipoclientenacionalidad from clientes where codigo=$codigo");  
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cod_tipoclientenacionalidad'];
   }
   return($valor);
}
function obtenerCodigoClienteSimulacion($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cod_cliente from simulaciones_servicios where codigo=$codigo");  
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cod_cliente'];
   }
   return($valor);
}
function obtenerDetallePlantillaServicioAuditoresCantidad($plantilla){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT p.* FROM plantillas_servicios_auditores p where p.cod_estadoreferencial=1 and p.cod_plantillaservicio=$plantilla");  
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor++;
   }
   return($valor);
}
function obtenerCantidadAuditoriasPlantilla($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cantidad_auditorias from plantillas_servicios where codigo=$codigo");  
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cantidad_auditorias'];
   }
   return($valor);
}
function obtenerPrecioRegistradoPlantilla($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT ingreso_presupuestado from plantillas_servicios where codigo=$codigo");  
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['ingreso_presupuestado'];
   }
   return($valor);
}

function obtenerPrecioRegistradoPropuestaTCPTCS($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT ingreso_presupuestado from simulaciones_servicios where codigo=$codigo");  
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['ingreso_presupuestado'];
   }
   return($valor);
}

function obtenerCantidadSimulacionDetalleAuditor($codigo,$codAuditor){
    $dbh = new Conexion();
   $valor=0;
   $sql="SELECT cantidad from simulaciones_ssd_ssa p where p.cod_simulacionservicio=$codigo and cod_simulacionservicioauditor=$codAuditor limit 1";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cantidad'];
  }
  return $valor;
}
function obtenerCantidadSimulacionDetalleAuditorPeriodo($codigo,$codAuditor,$anio){
    $dbh = new Conexion();
   $valor=0;
   $sql="SELECT cantidad from simulaciones_ssd_ssa p where p.cod_simulacionservicio=$codigo and p.cod_simulacionservicioauditor=$codAuditor and p.cod_anio=$anio limit 1";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cantidad'];
  }
  return $valor;
}
function obtenerDiasSimulacionDetalleAuditor($codigo,$codAuditor){
    $dbh = new Conexion();
   $valor=0;
   $sql="SELECT dias from simulaciones_ssd_ssa p where p.cod_simulacionservicio=$codigo and cod_simulacionservicioauditor=$codAuditor limit 1";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['dias'];
  }
  return $valor;
}
function obtenerDiasSimulacionDetalleAuditorPeriodo($codigo,$codAuditor,$anio){
    $dbh = new Conexion();
   $valor=0;
   $sql="SELECT dias from simulaciones_ssd_ssa p where p.cod_simulacionservicio=$codigo and p.cod_simulacionservicioauditor=$codAuditor and p.cod_anio=$anio limit 1";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['dias'];
  }
  return $valor;
}
function obtenerMontoSimulacionDetalleAuditor($codigo,$tipo,$codAuditor){
    $dbh = new Conexion();
   $valor=0;
   $sql="SELECT monto from simulaciones_ssd_ssa p where p.cod_simulacionservicio=$codigo and cod_simulacionserviciodetalle=$tipo and cod_simulacionservicioauditor=$codAuditor";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['monto'];
  }
  return $valor;
}
function obtenerMontoSimulacionDetalleAuditorPeriodo($codigo,$tipo,$codAuditor,$anio){
    $dbh = new Conexion();
   $valor=0;
   $sql="SELECT monto from simulaciones_ssd_ssa p where p.cod_simulacionservicio=$codigo and p.cod_simulacionserviciodetalle=$tipo and p.cod_simulacionservicioauditor=$codAuditor and p.cod_anio=$anio";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['monto'];
  }
  return $valor;
}
function obtenerDiasEspecificoSimulacionDetalleAuditorPeriodo($codigo,$tipo,$codAuditor,$anio){
    $dbh = new Conexion();
   $valor=0;
   $sql="SELECT dias from simulaciones_ssd_ssa p where p.cod_simulacionservicio=$codigo and p.cod_simulacionserviciodetalle=$tipo and p.cod_simulacionservicioauditor=$codAuditor and p.cod_anio=$anio";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['dias'];
  }
  return $valor;
}

function obtenerCodigoEspecificoSimulacionDetalleAuditorPeriodo($codigo,$tipo,$codAuditor,$anio){
    $dbh = new Conexion();
   $valor=0;
   $sql="SELECT codigo from simulaciones_ssd_ssa p where p.cod_simulacionservicio=$codigo and p.cod_simulacionserviciodetalle=$tipo and p.cod_simulacionservicioauditor=$codAuditor and p.cod_anio=$anio";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['codigo'];
  }
  return $valor;
}


function obtenerMontoSimulacionDetalleAuditorExterno($codigo,$tipo,$codAuditor){
    $dbh = new Conexion();
   $valor=0;
   $sql="SELECT monto_externo from simulaciones_ssd_ssa p where p.cod_simulacionservicio=$codigo and cod_simulacionserviciodetalle=$tipo and cod_simulacionservicioauditor=$codAuditor";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['monto_externo'];
  }
  return $valor;
}
function obtenerMontoSimulacionDetalleAuditorExternoPeriodo($codigo,$tipo,$codAuditor,$anio){
    $dbh = new Conexion();
   $valor=0;
   $sql="SELECT monto_externo from simulaciones_ssd_ssa p where p.cod_simulacionservicio=$codigo and p.cod_simulacionserviciodetalle=$tipo and p.cod_simulacionservicioauditor=$codAuditor and p.cod_anio=$anio";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['monto_externo'];
  }
  return $valor;
}
function obtenerCantidadTotalSimulacionesServiciosDetalleAuditor($codigo,$tipo){
    $dbh = new Conexion();
   $valor=0;
   $sql="SELECT cantidad,dias from simulaciones_ssd_ssa p where p.cod_simulacionservicio=$codigo and cod_simulacionserviciodetalle=$tipo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor+=$row['cantidad']*$row['dias'];
  }
  return $valor;
}
function obtenerCantidadTotalSimulacionesServiciosDetalleAuditorPeriodo($codigo,$tipo,$anio){
    $dbh = new Conexion();
   $valor=0;
   $sql="SELECT p.codigo,p.cantidad,p.dias,p.monto from simulaciones_ssd_ssa p, (select codigo from simulaciones_servicios_auditores where cod_simulacionservicio=$codigo and cod_anio=$anio and habilitado=1) q 
where p.cod_simulacionservicio=$codigo and p.cod_simulacionserviciodetalle=$tipo and p.cod_anio=$anio and p.cod_simulacionservicioauditor=q.codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor+=($row['cantidad']*$row['dias']);
  }
  return $valor;
}
function obtenerMontoTotalSimulacionesServiciosDetalleAuditor($codigo,$tipo){
    $dbh = new Conexion();
   $valor=0;
   $sql="SELECT p.cantidad,p.dias,p.monto,s.cod_externolocal,p.monto_externo from simulaciones_ssd_ssa p join simulaciones_servicios_auditores s on p.cod_simulacionservicioauditor=s.cod_tipoauditor where p.cod_simulacionservicio=$codigo and s.cod_simulacionservicio=$codigo and cod_simulacionserviciodetalle=$tipo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      if($row['cod_externolocal']==1){
        $valor+=($row['cantidad']*$row['dias']*$row['monto']);
      }else{
        $valor+=($row['cantidad']*$row['dias']*$row['monto_externo']);
      }     
  }
  return $valor;
}
function obtenerMontoTotalSimulacionesServiciosDetalleAuditorPeriodo($codigo,$tipo,$anio){
    $dbh = new Conexion();
   $valor=0;
   $sql="SELECT p.cantidad,p.dias,p.monto,s.cod_externolocal,p.monto_externo from simulaciones_ssd_ssa p join simulaciones_servicios_auditores s on p.cod_simulacionservicioauditor=s.codigo where p.cod_simulacionservicio=$codigo and s.cod_simulacionservicio=$codigo and p.cod_simulacionserviciodetalle=$tipo and p.cod_anio=$anio and s.cod_anio=$anio and s.habilitado=1";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      if($row['cod_externolocal']==1){
        $valor+=($row['cantidad']*$row['dias']*$row['monto']);
      }else{
        $valor+=($row['cantidad']*$row['dias']*$row['monto_externo']);
      }     
  }
  return $valor;
}

function obtenerMontoNormaSimulacion($codigo){
    $dbh = new Conexion();
   $valor=0;
   $sql="SELECT monto_norma from simulaciones_costos p where p.codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['monto_norma'];
  }
  return $valor;
}
function obtenerHabilitadoNormaSimulacion($codigo){
    $dbh = new Conexion();
   $valor=0;
   $sql="SELECT habilitado_norma from simulaciones_costos p where p.codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['habilitado_norma'];
  }
  return $valor;
}
function obtenerCantidadSimulacionServicio($q){
  $dbh = new Conexion();
   $valor=0;
   $sql="SELECT idServicio from simulaciones_servicios p where p.idServicio=$q";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor++;
  }
  return $valor;
}
function obtenerAnioPlantillaServicio($codigo){
   $dbh = new Conexion();
   $valor=0;
   $sql="SELECT anios from plantillas_servicios p where p.codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['anios'];
  }
  return $valor;
}

function obtenerCodigoSolicitudRecursosSimulacion($claseSim,$codigo){
  $dbh = new Conexion();
   $valor=0;
   if($claseSim==1){
    $sql="SELECT codigo from solicitud_recursos p where p.cod_simulacion=$codigo and p.cod_simulacionservicio=0 and p.cod_proveedor=0";
   }else{
    $sql="SELECT codigo from solicitud_recursos p where p.cod_simulacionservicio=$codigo and p.cod_simulacion=0 and p.cod_proveedor=0";
   }
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['codigo'];
  }
  return $valor;
}

function obtenerMontoTarifarioSelloEmpresa($cod_sello,$cod_empresa,$cod_nacionalidad){
   $dbh = new Conexion();
   $valor=0;$codigo=0;
   $sql="SELECT codigo,monto from costos_servicios_tipocliente p where p.cod_configuracionserviciosello=$cod_sello and p.cod_tipocliente=$cod_empresa and p.cod_tipoclientenacionalidad=$cod_nacionalidad";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['monto'];
      $codigo=$row['codigo'];
  }
  return array($valor,$codigo);
}


function obtener_diashsbiles_atras($dias_atras,$fecha)
{
  $cont=1;
  $i=0;
  $fecha_dias_atras='';
  while ( $i< $dias_atras) {
    $fecha_dias_atras=date("Y-m-d",strtotime($fecha."- ".($cont)." days"));
    if(date("w",strtotime($fecha_dias_atras)) != 0 && date("w",strtotime($fecha_dias_atras)) != 6 )
    {
      $i++;
    }
    $cont++;  
  }
  return $fecha_dias_atras;
}

function VerificarProyFinanciacion($codigo_UO){
   $dbh = new Conexion();
   $valor=null;
   $sql="SELECT p.codigo from proyectos_financiacionexterna p where p.cod_unidadorganizacional=$codigo_UO";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['codigo'];
  }
  return $valor;
}

function obtenerActividadesServicioImonitoreo($codigo_proyecto){
  $sIde = "";
  $sKey = "";
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ListarComponentes","codigo_proyecto"=>$codigo_proyecto);
  //Lista todos los componentes
  $parametros=json_encode($parametros);
    $ch = curl_init();
    // definimos la URL a la que hacemos la petición    
    //curl_setopt($ch, CURLOPT_URL,"http://localhost/imonitoreo/componentesSIS/compartir_servicio.php");//prueba
    curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/ifinanciero/wsifin/ws_actividadesproyectos.php");//prueba    
    // indicamos el tipo de petición: POST
    curl_setopt($ch, CURLOPT_POST, TRUE);
    // definimos cada uno de los parámetros
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    // recibimos la respuesta y la guardamos en una variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    curl_close ($ch);
    
    // imprimir en formato JSON  
    //print_r($remote_server_output);
    $obj= json_decode($remote_server_output);
    $detalle=$obj->lstComponentes;
    return $detalle;
    // foreach ($detalle as $objDet){
    //   echo $objDet->codigo."<br>";
    // }
  }

  function obtenerCodigoActividadesServicioImonitoreo($cod_actividad){
  $sIde = "";
  $sKey = "";
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"DatosActividadProyecto","codigo"=>$cod_actividad);
  //Lista todos los componentes
  $parametros=json_encode($parametros);
    $ch = curl_init();
    // definimos la URL a la que hacemos la petición    
    //curl_setopt($ch, CURLOPT_URL,"http://localhost/imonitoreo/componentesSIS/compartir_servicio.php");//prueba
    curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/ifinanciero/wsifin/ws_actividadesproyectos.php");//prueba    
    // indicamos el tipo de petición: POST
    curl_setopt($ch, CURLOPT_POST, TRUE);
    // definimos cada uno de los parámetros
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    // recibimos la respuesta y la guardamos en una variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    curl_close ($ch);
    
    // imprimir en formato JSON  
    //print_r($remote_server_output);
    $obj= json_decode($remote_server_output);
    $detalle=$obj->lstComponentes;
    $abreviatura="";
    foreach ($detalle as $listas) { 
     $abreviatura="Actividad: ".$listas->abreviatura;
    }
    return $abreviatura;
  }

  function obtenerDetalleSolicitudSimulacionCuentaPlantillaServicioFiltro($codigo,$codigoPlan,$anio,$item_detalle,$codigo_detalle){
   $dbh = new Conexion();
   if($anio!="all"){
   $anioSQL1="cod_anio=$anio and";  
    if($anio==1){
        if(obtenerCodigoAreaPlantillasServicios($codigoPlan)==38){
          $an=$anio-1;
          $anioSQL1="(cod_anio=$an or cod_anio=$anio) and";
        }
    }
   }else{
    $anioSQL1="";    
   }
   
   if($codigo_detalle!="all"){
    $item_detalleSQL1="glosa='$item_detalle' and";
   }else{
    $item_detalleSQL1="";
   }

   $sql="";
   $sql="(select * from v_propuestas_detalle_variables  where $anioSQL1 $item_detalleSQL1 cod_simulacionservicio=$codigo  order by cod_detalle)
UNION
(select * from v_propuestas_detalle_honorarios  where $anioSQL1 $item_detalleSQL1 cod_simulacionservicio=$codigo)
order by cod_anio";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}


function listaObligacionesPagoDetalleSolicitudRecursos(){
  $dbh = new Conexion();
  $sql="SELECT p.nombre as proveedor,u.nombre as nombre_unidad,u.abreviatura as unidad,a.nombre as nombre_area,a.abreviatura as area,
s.cod_personal,s.cod_unidadorganizacional,s.cod_area,s.fecha,s.numero,s.cod_estadosolicitudrecurso,sd.* 
  from solicitud_recursosdetalle sd join solicitud_recursos s on sd.cod_solicitudrecurso=s.codigo  
join unidades_organizacionales u on s.cod_unidadorganizacional=u.codigo
join areas a on s.cod_area=a.codigo
join af_proveedores p on sd.cod_proveedor=p.codigo where s.cod_estadosolicitudrecurso=3";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  return $stmt;
}
function listaObligacionesPagoDetalleSolicitudRecursosSolicitud($codigo){
  $dbh = new Conexion();
  $sql="SELECT s.cod_comprobante,p.nombre as proveedor,u.nombre as nombre_unidad,u.abreviatura as unidad,a.nombre as nombre_area,a.abreviatura as area,
s.cod_personal,s.cod_unidadorganizacional,s.cod_area,s.fecha,s.numero,s.cod_estadosolicitudrecurso,sd.* 
  from solicitud_recursosdetalle sd join solicitud_recursos s on sd.cod_solicitudrecurso=s.codigo  
join unidades_organizacionales u on s.cod_unidadorganizacional=u.codigo
join areas a on s.cod_area=a.codigo
join af_proveedores p on sd.cod_proveedor=p.codigo where s.cod_estadosolicitudrecurso=3 and s.codigo=$codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  return $stmt;
}


function listaDiasCreditoProveedores(){
  $dbh = new Conexion();
  $sql="SELECT p.nombre,dc.* 
FROM dias_credito dc join af_proveedores p on dc.cod_proveedor=p.codigo
WHERE dc.cod_estadoreferencial=1;";
$stmt = $dbh->prepare($sql);
  $stmt->execute();
  return $stmt;
}
function obtenerCantidadDiasCredito($proveedor){
   $dbh = new Conexion();
   $valor=0;
   $sql="SELECT p.cantidad_dias from dias_credito p where p.cod_proveedor=$proveedor";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cantidad_dias'];
  }
  return $valor;
}
function obtenerSaldoPagoProveedorDetallePorSolicitudRecurso($codigo){
  $dbh = new Conexion();
   $valor=0;
   $sql="SELECT p.monto from pagos_proveedoresdetalle p where p.cod_solicitudrecursos=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor+=$row['monto'];
  }
  return $valor;
}
function obtenerCodigoPagoProveedorDetallePorSolicitudRecurso($codigo){
  $dbh = new Conexion();
   $valor=0;
   $sql="SELECT p.cod_pagoproveedor from pagos_proveedoresdetalle p where p.cod_solicitudrecursos=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cod_pagoproveedor'];
  }
  return $valor;
}
function obtenerCodigoPagoProveedor(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from pagos_proveedores c");
   $stmt->execute();
   $codigo=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigo=$row['codigo'];
   }
   return($codigo);
}

function listaChequesPago(){
  $dbh = new Conexion();
  $sql="SELECT p.nombre,dc.* 
FROM cheques dc join bancos p on dc.cod_banco=p.codigo
WHERE dc.cod_estadoreferencial=1;";
$stmt = $dbh->prepare($sql);
  $stmt->execute();
  return $stmt;
}
function listaInstanciasEnvio(){
  $dbh = new Conexion();
  $sql="SELECT i.descripcion as instancia,CONCAT (p.primer_nombre,' ',p.otros_nombres,' ',p.paterno,' ',p.materno) as personal,p.email_empresa as email,dc.* 
FROM instancias_envios_correos_detalle dc join personal p on dc.cod_personal=p.codigo join instancias_envios_correos i on i.codigo=dc.cod_instancia_envio
WHERE dc.cod_estadoreferencial=1 order by i.descripcion;";
$stmt = $dbh->prepare($sql);
  $stmt->execute();
  return $stmt;
}

function obtenerMontoPagadoDetalleSolicitud($codSol,$codDetalle){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT c.monto from pagos_proveedoresdetalle c where c.cod_solicitudrecursos=$codSol and c.cod_solicitudrecursosdetalle=$codDetalle");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor+=$row['monto'];
   }
   return($valor);
}
function obtenerCodigoPagoProveedorDetalle(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from pagos_proveedoresdetalle c");
   $stmt->execute();
   $codigo=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigo=$row['codigo'];
   }
   return($codigo);
}

function obtenerProveedorCuentaAux($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT p.nombre from cuentas_auxiliares c join af_proveedores p on c.cod_proveedorcliente=p.codigo 
    where c.codigo=$codigo and c.cod_tipoauxiliar=1");
   $stmt->execute();
   $valor="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['nombre'];
   }
   return($valor);
}

function obtenerCodigoProveedorCuentaAux($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT c.cod_proveedorcliente from cuentas_auxiliares c where c.codigo=$codigo");
   $stmt->execute();
   $valor="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cod_proveedorcliente'];
  }
  return($valor);
}  

function obtenerDetalleSolicitudParaComprobante($codigo){
  $dbh = new Conexion();
  $sql="SELECT cod_unidadorganizacional,cod_area,codigo,cod_plancuenta,importe as monto,cod_proveedor,detalle as glosa,cod_confretencion from solicitud_recursosdetalle where cod_solicitudrecurso=$codigo order by cod_proveedor";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  return $stmt;
}

function numeroCorrelativoComprobante($codGestion,$unidad,$tipoComprobante,$codMes){
  $dbh = new Conexion();
  $mesActivo=$codMes;

  $sql1="SELECT g.nombre from gestiones g where codigo='$codGestion'";
  $stmt1 = $dbh->prepare($sql1);
  $stmt1->execute();
  $anio=$_SESSION["globalNombreGestion"];
  while ($row1= $stmt1->fetch(PDO::FETCH_ASSOC)) {
    $anio=$row1['nombre'];
  }

  $fechaInicio=$anio."-".$mesActivo."-01";
  $fechaFin=date('Y-m-d',strtotime($fechaInicio.'+1 month'));
  $fechaFin=date('Y-m-d',strtotime($fechaFin.'-1 day'));

  $sql="SELECT IFNULL(max(c.numero)+1,1)as codigo from comprobantes c where c.cod_tipocomprobante='$tipoComprobante' and c.cod_unidadorganizacional=$unidad and c.fecha between '$fechaInicio 00:00:00' and '$fechaFin 23:59:59' and c.cod_estadocomprobante<>2";
  //echo $sql;
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $nroCorrelativo=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $nroCorrelativo=$row['codigo'];
  }

  return $nroCorrelativo;
 }

 function obtenerPersonalSolicitanteRecursos($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cod_personal from solicitud_recursos where codigo=$codigo");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cod_personal'];
   }
   return($valor);
}
function obtenerUnidadSolicitanteRecursos($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cod_unidadorganizacional from solicitud_recursos where codigo=$codigo");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cod_unidadorganizacional'];
   }
   return($valor);
}
function obtenerAreaSolicitanteRecursos($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cod_area from solicitud_recursos where codigo=$codigo");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cod_area'];
   }
   return($valor);
}

function obtenerUnidadAreaCentrosdeCostos($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cod_unidadorganizacional,cod_grupocuentas,fijo,cod_area FROM configuracion_centrocostoscomprobantes where cod_grupocuentas='$codigo'");
   $stmt->execute();
   $valor1=0;$valor2=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor1=$row['cod_unidadorganizacional'];
      $valor2=$row['cod_area'];
   }
   return array($valor1,$valor2);
}

function listaObligacionesPagoDetalleSolicitudRecursosProveedor($codigo){
  $dbh = new Conexion();
  $sql="SELECT s.cod_comprobante,p.nombre as proveedor,u.nombre as nombre_unidad,u.abreviatura as unidad,a.nombre as nombre_area,a.abreviatura as area,
s.cod_personal,s.cod_unidadorganizacional,s.cod_area,s.fecha,s.numero,s.cod_estadosolicitudrecurso,sd.* 
  from solicitud_recursosdetalle sd join solicitud_recursos s on sd.cod_solicitudrecurso=s.codigo  
join unidades_organizacionales u on s.cod_unidadorganizacional=u.codigo
join areas a on s.cod_area=a.codigo
join af_proveedores p on sd.cod_proveedor=p.codigo where s.cod_estadosolicitudrecurso=3 and sd.cod_proveedor=$codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  return $stmt;
}
function listaObligacionesPagoDetalleSolicitudRecursosProveedorPagos($codigo,$codigoPago){
  $dbh = new Conexion();
  $sql="SELECT pd.codigo as cod_detallepago,pd.monto as monto_pagado,pd.cod_tipopagoproveedor as tipo_pagado,pd.fecha as fecha_pagado,s.cod_comprobante,p.nombre as proveedor,u.nombre as nombre_unidad,u.abreviatura as unidad,a.nombre as nombre_area,a.abreviatura as area,
s.cod_personal,s.cod_unidadorganizacional,s.cod_area,s.fecha,s.numero,s.cod_estadosolicitudrecurso,sd.* 
  from solicitud_recursosdetalle sd join solicitud_recursos s on sd.cod_solicitudrecurso=s.codigo  
join unidades_organizacionales u on s.cod_unidadorganizacional=u.codigo
join areas a on s.cod_area=a.codigo
join af_proveedores p on sd.cod_proveedor=p.codigo 
join pagos_proveedoresdetalle pd on pd.cod_proveedor=sd.cod_proveedor
where s.cod_estadosolicitudrecurso=3 and sd.cod_proveedor=$codigo and pd.cod_pagoproveedor=$codigoPago";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  return $stmt;
}

function listaObligacionesPagoDetalleSolicitudRecursosProveedorPagosLotes($codigo,$codigoPagoLote){
  $dbh = new Conexion();
  $sql="SELECT pd.codigo as cod_detallepago,pd.monto as monto_pagado,pd.cod_tipopagoproveedor as tipo_pagado,pd.fecha as fecha_pagado,s.cod_comprobante,p.nombre as proveedor,u.nombre as nombre_unidad,u.abreviatura as unidad,a.nombre as nombre_area,a.abreviatura as area,
s.cod_personal,s.cod_unidadorganizacional,s.cod_area,s.fecha,s.numero,s.cod_estadosolicitudrecurso,sd.* 
  from solicitud_recursosdetalle sd join solicitud_recursos s on sd.cod_solicitudrecurso=s.codigo  
join unidades_organizacionales u on s.cod_unidadorganizacional=u.codigo
join areas a on s.cod_area=a.codigo
join af_proveedores p on sd.cod_proveedor=p.codigo 
join pagos_proveedoresdetalle pd on pd.cod_proveedor=sd.cod_proveedor
join pagos_proveedores pp on pp.codigo=pd.cod_pagoproveedor
where s.cod_estadosolicitudrecurso=3 and sd.cod_proveedor in ($codigo) and pp.cod_pagolote=$codigoPagoLote";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  return $stmt;
}
function nameTipoCuentaAuxiliar($codigo){
  $nombreTipoAuxiliar="";
  if($codigo==1){
    $nombreTipoAuxiliar="Proveedor";
  }else{
    $nombreTipoAuxiliar="Cliente";
  }
  return($nombreTipoAuxiliar);
}

function nameProveedorCliente($codTipo, $codigo){
  $name="";
  if($codTipo==1){
    $name=nameProveedor($codigo);
  }else{
    $name=nameCliente($codigo);
  }
  return($name);
}

function ObtenerMontoTotalEstadoCuentas_hijos($codCuenta,$codigo_compDe)
{ 
  $saldo=0;
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT e.monto FROM estados_cuenta e where e.cod_plancuenta=$codCuenta and cod_comprobantedetalleorigen=$codigo_compDe");
   $stmt->execute();   
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $saldo=$saldo+$row['monto'];
   }

  return($saldo);
}

function obtenerFechaEnLetra($fecha){
    // $dia= date("d", strtotime($fecha));
    $num = date("j", strtotime($fecha));
    $anno = date("Y", strtotime($fecha));
    $mes = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
    $mes = $mes[(date('m', strtotime($fecha))*1)-1];
    return $num.' de '.$mes.' del '.$anno;
}

function buscarCuentaAnterior($cuenta){
   $dbh = new Conexion();
   $sqlX="SELECT codigo FROM plan_cuentas where numero='$cuenta'";
   //echo $sqlX;
   $stmt = $dbh->prepare($sqlX);
   $stmt->execute();
   $codigoX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoX=$row['codigo'];
   }
   return($codigoX);
}

function buscarCuentaAuxiliarAnterior($cuentaaux){
   $dbh = new Conexion();
   $sqlX="SELECT codigo FROM cuentas_auxiliares where codigo_anterior='$cuentaaux'";
   //echo $sqlX;
   $stmt = $dbh->prepare($sqlX);
   $stmt->execute();
   $codigoX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoX=$row['codigo'];
   }
   return($codigoX);
}

function listaCuentasAuxiliaresRelacionadasProveedoresClientes(){
  $dbh = new Conexion();
  $sql="SELECT p.*,a.nombre as nombre_proveedorcliente FROM cuentas_auxiliares p JOIN af_proveedores a on a.codigo=p.cod_proveedorcliente where p.cod_tipoauxiliar=1
UNION 
SELECT p.*,a.nombre as nombre_proveedorcliente FROM cuentas_auxiliares p JOIN clientes a on a.codigo=p.cod_proveedorcliente where p.cod_tipoauxiliar=2
order by nombre_proveedorcliente";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  return $stmt;
}

function abrevTipoComprobante($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT abreviatura FROM tipos_comprobante where codigo in ($codigo)");
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['abreviatura'];
   }
   return($nombreX);
}


function obtenerClienteCuentaAux($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT p.nombre from cuentas_auxiliares c join clientes p on c.cod_proveedorcliente=p.codigo 
    where c.codigo=$codigo and c.cod_tipoauxiliar=2");
   $stmt->execute();
   $valor="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['nombre'];
   }
   return($valor);
}

function obtenerCodigoComprobanteDetalleSolicitudRecursosDetalle($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT p.cod_comprobantedetalle from solicitud_recursosdetalle p where p.codigo=$codigo");
   $stmt->execute();
   $valor="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cod_comprobantedetalle'];
   }
   return($valor);
}

function obtenerCuentasComprobantesDet($codigo){
   $dbh = new Conexion();
  $sql="select p.codigo,p.nombre,p.numero,count(*) as cantidad from comprobantes_detalle d join plan_cuentas p on d.cod_cuenta=p.codigo WHERE cod_comprobante=$codigo group by cod_cuenta";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  return $stmt;
}
function obtenerComprobantesDetCuenta($codigo,$cuenta){
   $dbh = new Conexion();
   $sql="";
   $sql2="";
   for ($i=0; $i < count($cuenta); $i++) {
      if($i==0){
        $sql2.="and (";
      }
      if($i==(count($cuenta)-1)){
        $sql2.="d.cod_cuenta='".$cuenta[$i]."')";
       }else{
        $sql2.="d.cod_cuenta='".$cuenta[$i]."' or ";
       }  
   }

   $sql="SELECT d.cod_cuentaauxiliar,d.codigo as cod_det,d.cod_area,d.cod_unidadorganizacional,p.codigo,p.numero,p.nombre,d.glosa,d.debe,d.haber,a.abreviatura,p.cuenta_auxiliar,u.abreviatura as unidadAbrev FROM plan_cuentas p join comprobantes_detalle d on p.codigo=d.cod_cuenta join areas a on d.cod_area=a.codigo join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional where d.cod_comprobante=$codigo ".$sql2." order by cod_det";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   // bindColumn
   //$stmt->bindColumn('cod_comprobante', $codigoC);
   return $stmt;
}

 function contarComprobantesDetalleCuenta($codigo,$cuenta){
   $dbh = new Conexion();
   $sql="";
   $sql2="";
   for ($i=0; $i < count($cuenta); $i++) {
      if($i==0){
        $sql2.="and (";
      }
      if($i==(count($cuenta)-1)){
        $sql2.="cod_cuenta='".$cuenta[$i]."')";
       }else{
        $sql2.="cod_cuenta='".$cuenta[$i]."' or ";
       }  
   }
   
   $sql="select count(*) as total from comprobantes_detalle where cod_comprobante=$codigo ".$sql2;
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
  }

  function obtenerTotalesDebeHaberComprobante($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT sum(debe) as total_debe,sum(haber) as total_haber FROM comprobantes_detalle WHERE cod_comprobante=$codigo");
   $stmt->execute();
   $valor=0;$valor2=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['total_debe'];
      $valor2=$row['total_haber'];
   }
   return array($valor,$valor2);
}
 

 function obtenerCodigoCuentaAuxiliarProveedorCliente($tipo,$codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT c.codigo from cuentas_auxiliares c where c.cod_proveedorcliente=$codigo and c.cod_tipoauxiliar=$tipo");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['codigo'];
   }
   return($valor);
}

 function listaSumaMontosDebeHaberComprobantesDetalle($fechaFinal,$tipoBusqueda,$arrayUnidades,$arrayAreas,$padre,$gestion,$fechaInicio){
    $dbh = new Conexion();
    $sql="";
    $sqlAreas="";
    $sqlUnidades="";
    $fechaFinalMod=explode("/", $fechaFinal);
    //formateando fecha
    if($fechaInicio=="none"){
      $fi=$fechaFinalMod[2]."-01-01";
    }else{
      $fechaFinalModIni=explode("/", $fechaInicio);
      $fi=$fechaFinalModIni[2]."-".$fechaFinalModIni[1]."-".$fechaFinalModIni[0];
    }
  
    $fa=$fechaFinalMod[2]."-".$fechaFinalMod[1]."-".$fechaFinalMod[0];
    //$fi=$fechaFinalMod[2]."-01-01";
    for ($i=0; $i < count($arrayAreas); $i++) {
      if($i==0){
        $sqlAreas.="and (";
      }
      if($i==(count($arrayAreas)-1)){
        $sqlAreas.="d.cod_area='".$arrayAreas[$i]."')";
      }else{
        $sqlAreas.="d.cod_area='".$arrayAreas[$i]."' or ";
      }  
    }
    //busqueda de unidades
    for ($i=0; $i < count($arrayUnidades); $i++) {
      if($i==0){
        $sqlUnidades.="and (";
      }
      if($i==(count($arrayUnidades)-1)){
        $sqlUnidades.="d.cod_unidadorganizacional='".$arrayUnidades[$i]."')";
       }else{
        $sqlUnidades.="d.cod_unidadorganizacional='".$arrayUnidades[$i]."' or ";
       }  
    }
    $sql="SELECT cuentas_monto.*,p.nombre,p.numero,p.nivel,p.cod_padre from plan_cuentas p join 
           (select d.cod_cuenta,sum(debe) as total_debe,sum(haber) as total_haber 
            from comprobantes_detalle d join comprobantes c on c.codigo=d.cod_comprobante 
            where (c.fecha between '$fi' and '$fa') $sqlUnidades and c.cod_estadocomprobante<>'2' and c.cod_gestion='$gestion' group by (d.cod_cuenta) order by d.cod_cuenta) cuentas_monto
        on p.codigo=cuentas_monto.cod_cuenta where p.cod_padre=$padre order by p.numero";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return $stmt;
}

function sumaMontosDebeHaberComprobantesDetalleNivel($fechaFinal,$tipoBusqueda,$arrayUnidades,$padre){
  $dbh = new Conexion();
  $sql="";
  $sqlAreas="";
  $sqlUnidades="";
    //formateando fecha
  $fechaFinalMod=explode("/", $fechaFinal);
  $fa=$fechaFinalMod[2]."-".$fechaFinalMod[1]."-".$fechaFinalMod[0];
  $fi=$fechaFinalMod[2]."-01-01";
  //busqueda de unidades
  for ($i=0; $i < count($arrayUnidades); $i++) {
    if($i==0){
      $sqlUnidades.="and (";
    }
    if($i==(count($arrayUnidades)-1)){
      $sqlUnidades.="d.cod_unidadorganizacional='".$arrayUnidades[$i]."')";
    }else{
      $sqlUnidades.="d.cod_unidadorganizacional='".$arrayUnidades[$i]."' or ";
    }  
  }
  $sql="SELECT cuentas_monto.*,p.nombre,p.numero,p.nivel,p.cod_padre from plan_cuentas p join 
           (select d.cod_cuenta,sum(debe) as total_debe,sum(haber) as total_haber 
            from comprobantes_detalle d join comprobantes c on c.codigo=d.cod_comprobante and c.cod_estadocomprobante<>'2' 
            where (c.fecha between '$fi' and '$fa') $sqlUnidades group by (d.cod_cuenta) order by d.cod_cuenta) cuentas_monto
        on p.codigo=cuentas_monto.cod_cuenta where p.cod_padre=$padre order by p.numero";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  return $stmt;
}

function formatoNumeroCuenta($numero){
 return $numero[0].".".$numero[1].$numero[2].".".$numero[3].$numero[4].".".$numero[5].$numero[6].".".$numero[7].$numero[8].$numero[9]; 
}

function nameCuentaAuxiliar($cuentaaux){
   $dbh = new Conexion();
   $sqlX="SELECT nombre FROM cuentas_auxiliares where codigo='$cuentaaux'";
   $stmt = $dbh->prepare($sqlX);
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}
function nameTipoAsignacion($valor){
   $dbh = new Conexion();
   $nombreX=0;
   $sqlX="SELECT nombre FROM estados_asignacionaf where codigo='$valor'";
   $stmt = $dbh->prepare($sqlX);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];      
   }
   return($nombreX);
}

function nameTipoAuditor($valor){
   $dbh = new Conexion();
   $nombreX=0;
   $sqlX="SELECT nombre FROM tipos_auditor where codigo='$valor'";
   $stmt = $dbh->prepare($sqlX);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];      
   }
   return($nombreX);
}
function obtenerCodigoAreaPlantillasCosto($codigo){
   $dbh = new Conexion();
   $sqlX="SELECT cod_area FROM plantillas_costo where codigo='$codigo'";
   $stmt = $dbh->prepare($sqlX);
   $stmt->execute();
   $nombreX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['cod_area'];
   }
   return($nombreX);
}

function obtenerCodigoAreaPlantillasServicios($codigo){
   $dbh = new Conexion();
   $sqlX="SELECT cod_area FROM plantillas_servicios where codigo='$codigo'";
   $stmt = $dbh->prepare($sqlX);
   $stmt->execute();
   $nombreX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['cod_area'];
   }
   return($nombreX);
}

function obtenerCodigoUnidadPlantillasServicios($codigo){
   $dbh = new Conexion();
   $sqlX="SELECT cod_unidadorganizacional FROM plantillas_servicios where codigo='$codigo'";
   $stmt = $dbh->prepare($sqlX);
   $stmt->execute();
   $nombreX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['cod_unidadorganizacional'];
   }
   return($nombreX);
}

function obtenerCodigoSimulacionServicioAtributo(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from simulaciones_servicios_atributos c");
   $stmt->execute();
   $codigoComprobante=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoComprobante=$row['codigo'];
   }
   return($codigoComprobante);
}
function obtenerCodigoSimulacionServicioTipoServicio(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from simulaciones_servicios_tiposervicio c");
   $stmt->execute();
   $codigoComprobante=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoComprobante=$row['codigo'];
   }
   return($codigoComprobante);
}

function obtenerCodigoSimulacionServicioAuditor(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from simulaciones_servicios_auditores c");
   $stmt->execute();
   $codigoComprobante=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoComprobante=$row['codigo'];
   }
   return($codigoComprobante);
}

function verificarTipoAuditorMontosVariables($codSim,$codigo,$codDet,$anio){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("select * from simulaciones_ssd_ssa where cod_simulacionservicio=$codSim and cod_simulacionservicioauditor=$codigo and cod_simulacionserviciodetalle=$codDet and cod_anio=$anio");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor++;
   }
   return $valor;
}

function obtenerNombreCliente($codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT s.nombre from clientes s where s.codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $valor="";
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
    $valor=$row['nombre'];
  }
  return $valor;
}

function obtenerNumeroClienteSimulacion($codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT c.nombre from simulaciones_servicios s join clientes c on s.cod_cliente=c.codigo where c.codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $valor=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
    $valor++;
  }
  return $valor;
}

function obtenerCodigoSimulacionServicioAuditorTipoAuditor($simulacion,$tipo,$anio){
    $dbh = new Conexion();
   $valor=0;
   $sql="SELECT p.codigo from simulaciones_servicios_auditores p where p.cod_simulacionservicio=$simulacion and p.cod_tipoauditor=$tipo and p.cod_anio=$anio";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['codigo'];
  }
  return $valor;
}

function listarSimulacionServicioAuditorParaCopiar($simulacion,$tipo,$anio,$glosa){
   $dbh = new Conexion();
  $sql="SELECT DISTINCT a.codigo,b.cod_simulacionservicioauditor from simulaciones_serviciodetalle a,(select q.* from simulaciones_ssd_ssa q, (select s.* from simulaciones_servicios_auditores s,(select cod_tipoauditor from simulaciones_servicios_auditores where codigo=$tipo) p
where s.cod_tipoauditor=p.cod_tipoauditor and s.cod_simulacionservicio=$simulacion and s.cod_anio=$anio) r
where q.cod_simulacionservicioauditor=r.codigo and q.cod_anio=$anio and q.cod_simulacionservicio=$simulacion) b
where a.cod_simulacionservicio=$simulacion and a.cod_anio=$anio and a.glosa='$glosa'";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  return $stmt;
}
  
  function obtenerGlosaSimulacionServicioDetalle($codigo){
    $dbh = new Conexion();
   $valor="";
   $sql="SELECT glosa FROM simulaciones_serviciodetalle where codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['glosa'];
  }
  return $valor;
  }

function obtenerCodigoSimulacionServicioDetalleGlosa($simulacion,$tipo,$anio){
    $dbh = new Conexion();
   $valor="";
   $sql="SELECT d.* from simulaciones_serviciodetalle d,(SELECT glosa FROM simulaciones_serviciodetalle where codigo=$tipo) e
where d.glosa=e.glosa and d.cod_anio=$anio and d.cod_simulacionservicio=$simulacion";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['codigo'];
  }
  return $valor;
  }

  function obtenerCodigoCuentasSimulacionServicio($simulacion,$anio){
    $dbh = new Conexion();
   $valor="";
   $sql="SELECT codigo from cuentas_simulacion where cod_simulacionservicios=$simulacion and cod_anio=$anio";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['codigo'];
  }
  return $valor;
  }


  function obtenerRolPersonaIbnorca($codPersona){
    $dbh = new ConexionIBNORCA();
   $valor=0;
   $sql="select personarol($codPersona) as rol";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['rol'];
    
   }
   return $valor;
 }

  function verificarCuentaEstadosCuenta($cuenta){      
    $dbh = new Conexion();
    $valor=0;
    $sql="SELECT count(*)as contador from configuracion_estadocuentas c where c.cod_plancuenta='$cuenta'";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['contador'];
    }
    return $valor;
  }
  function verificarTipoEstadoCuenta($cuenta){      
    $dbh = new Conexion();
    $valor=0;
    $sql="SELECT c.tipo from configuracion_estadocuentas c where c.cod_plancuenta='$cuenta'";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['tipo'];
    }
    return $valor;
  }

  function actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$user,$objeto,$fechaHoraActual,$obs){
    $dbh = new ConexionIBNORCA();
    //enviar propuestas para la actualizacion de ibnorca
    $sqlUpdateIbnorca="INSERT INTO estadoobjeto (idtipoobjeto,idestado,idresponsable,idobjeto,fechaestado,observaciones)
   VALUES ('$idTipoObjeto','$idObjeto','$user','$objeto','$fechaHoraActual','$obs')";
   $stmtUpdateIbnorca = $dbh->prepare($sqlUpdateIbnorca);
   $stmtUpdateIbnorca->execute();
  }

  function obtenerEstadoIfinancieroPropuestas($estado){
   switch ($estado) {
     case 2715:
       return 1;
       break;
     case 2716:
       return 4;
       break;
     case 2717:
       return 3;
       break;
     case 2718:
       return 5;
       break;
     case 2719:
       return 2;
       break;      
     default:
       return 1;
       break;
   }
  }
  function obtenerEstadoIfinancieroSolicitudes($estado){
   switch ($estado) {
     case 2721:
       return 1;
       break;
     case 2722:
       return 4;
       break;
     case 2723:
       return 3;
       break;
     case 2724:
       return 2;
       break;
     case 2725:
       return 5;
       break;
     case 2822:
       return 6;
       break;       
     default:
       return 1;
       break;
   }
  }
  function obtenerEstadoIfinancieroSolicitudesFac($estado){
   switch ($estado) {
     case 2726:
       return 1;
       break;
     case 2727:
       return 4;
       break;
     case 2728:
       return 3;
       break;
     case 2729:
       return 5;
       break;
     case 2730:
       return 2;
       break;
     case 2823:
       return 6;
       break;       
     default:
       return 1;
       break;
   }
  }
  function obtenerEstadoIfinancieroPlantillas($estado){
   switch ($estado) {
     case 2710:
       return 1;
       break;
     case 2713:
       return 2;
       break;
     case 2712:
       return 3;
       break;     
     default:
       return 1;
       break;
   }
  }
  function obtenerRolPersonaIbnorcaSesion($codPersona){
    $dbh = new ConexionIBNORCA();
   $valor=0;
   $sql="SELECT idrol FROM personarol WHERE idpersona=$codPersona and pordefecto=1";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['idrol'];
    }
    return $valor;
  }    

  function obtenerTipoEstadosCuenta($cuenta){      
    $dbh = new Conexion();
    $valor=0;
    $sql="select c.cod_tipoestadocuenta from configuracion_estadocuentas c where c.cod_plancuenta='$cuenta'";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cod_tipoestadocuenta'];
    }
    return $valor;
  }

  function obtenerTodoPagoSolicitud($codigo){
    $dbh = new Conexion();
   $valor=9000;
   $sql="select (SELECT sum(importe) as solicitado from solicitud_recursosdetalle where cod_solicitudrecurso=$codigo)-(select ifnull(sum(monto),0) as pagado from pagos_proveedoresdetalle where cod_solicitudrecursos=$codigo) as saldo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['saldo'];
    }
    return $valor;
  }
  function obtenerCodigoProveedorClienteEC($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT c.cod_proveedorcliente from cuentas_auxiliares c where c.codigo='$codigo'");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cod_proveedorcliente'];
   }
   return($valor);
}

function obtenerCodigoSimServicioTCPTCS($cod){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(CAST(c.nombre AS UNSIGNED))+1,1)as codigo from simulaciones_servicios c, plantillas_servicios p where p.cod_area=$cod and c.cod_plantillaservicio=p.codigo");
   $stmt->execute();
   $codigoComprobante=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoComprobante=$row['codigo'];
   }
   return($codigoComprobante);
}

function obtenerCodigoEstadoCuentaSolicitudRecursosDetalle($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT p.cod_estadocuenta from solicitud_recursosdetalle p where p.codigo=$codigo");
   $stmt->execute();
   $valor="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cod_estadocuenta'];
   }
   return($valor);
}

function obtenerDatosCompletosPorSimulacionServicios($codigo){
  $dbh = new Conexion();
  $sql="SELECT p.cod_area,p.cod_unidadorganizacional,s.id_tiposervicio,s.cod_cliente,s.cod_responsable,s.cod_objetoservicio,s.descripcion_servicio 
  from simulaciones_servicios s join plantillas_servicios p on p.codigo=s.cod_plantillaservicio where s.codigo=$codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  return $stmt;
}

function obtenerCodigoServicioIbnorca(){
   $dbh = new ConexionIBNORCA();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.idServicio)+1,1)as codigo from servicios c");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['codigo'];
   }
   return($valor);
}
function obtenerEstadoCuentaSaldoComprobante($codigo){
   $dbh = new Conexion();
   $sql="SELECT count(*) as num from comprobantes_detalle cd join comprobantes c on c.codigo=cd.cod_comprobante join estados_cuenta e on e.cod_comprobantedetalle=cd.codigo where c.codigo=$codigo and c.cod_estadocomprobante<>2 and e.cod_comprobantedetalleorigen=0";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      //$valor=$row['num'];
   }
   return($valor);
}

function obtenerUnidadAreaPorSimulacionServicio($codigo){
     $dbh = new Conexion();
     $stmt = $dbh->prepare("SELECT p.cod_area,p.cod_unidadorganizacional FROM simulaciones_servicios s join plantillas_servicios p on p.codigo=s.cod_plantillaservicio where s.codigo=$codigo");
     $stmt->execute();
     $areaX="";
     $unidadX="";
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $areaX=$row['cod_area'];
        $unidadX=$row['cod_unidadorganizacional'];
     }
     return array($areaX,$unidadX);
} 

function obtenerUnidadAreaPorSimulacionCosto($codigo){
     $dbh = new Conexion();
     $stmt = $dbh->prepare("SELECT p.cod_area,p.cod_unidadorganizacional FROM simulaciones_costos s join plantillas_costo p on p.codigo=s.cod_plantillacosto where s.codigo=$codigo");
     $stmt->execute();
     $areaX="";
     $unidadX="";
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $areaX=$row['cod_area'];
        $unidadX=$row['cod_unidadorganizacional'];
     }
     return array($areaX,$unidadX);
}

function obtenerServiciosClaServicioTipo($id,$valor){
  $dbh = new Conexion();
  $sql="";
  if($valor==1){
    $sql="SELECT p.* FROM cla_servicios p join configuraciones_serviciosestado c on p.IdClaServicio=c.IdClaServicio where p.vigente=1 and p.codigo_n2=$id";
  }else{
    $sql="SELECT p.* FROM cla_servicios p where p.vigente=1 and p.codigo_n2=$id";
  }  
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}

function buscarFechasMinMaxComprobante($tipoComprobante, $nroCorrelativo, $UO, $anioTrabajo, $mesTrabajo){
  $dbh = new Conexion();   

  $fechaDefaultMin=$anioTrabajo."-".$mesTrabajo."-01";
  $fechaDefaultMin=date("Y-m-d",strtotime($fechaDefaultMin));
  $fechaDefaultMax=date("Y-m-d",strtotime($fechaDefaultMin."+ 1 month"));
  $fechaDefaultMax=date("Y-m-d",strtotime($fechaDefaultMax."- 1 days")); 

  $numeroMenor=$nroCorrelativo-1;
  $numeroMayor=$nroCorrelativo+1;

  $sql="SELECT max(fecha)as fecha from comprobantes where cod_tipocomprobante=$tipoComprobante and cod_unidadorganizacional='$UO' and YEAR(fecha)='$anioTrabajo' and MONTH(fecha)='$mesTrabajo' and numero='$numeroMenor'";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $fechaMenor="";
  while ($row = $stmt->fetch()){
    $fechaMenor=$row['fecha'];
  }
  if($fechaMenor=="" || $fechaMenor==null){
    $fechaMenor=$fechaDefaultMin;  
  }

  $sql="SELECT max(fecha)as fecha from comprobantes where cod_tipocomprobante=$tipoComprobante and cod_unidadorganizacional='$UO' and YEAR(fecha)='$anioTrabajo' and MONTH(fecha)='$mesTrabajo' and numero='$numeroMayor'";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $fechaMayor="";
  while ($row = $stmt->fetch()){
    $fechaMayor=$row['fecha'];
  }
  if($fechaMayor=="" || $fechaMayor==null){
    $fechaMayor=$fechaDefaultMax;  
  }
  $fechaMenor=date("Y-m-d",strtotime($fechaMenor));
  $fechaMayor=date("Y-m-d",strtotime($fechaMayor));

  return array($fechaMenor,$fechaMayor);  
}


function obtenerCodigoServicioPorPropuestaTCPTCS($idPropuesta){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("select IFNULL(se.Codigo,' - ')as codigo  from simulaciones_servicios  s, ibnorca.servicios se where s.idServicio=se.IdServicio and s.codigo=$idPropuesta");
   $stmt->execute();
   $valor="-";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['codigo'];
   }
   return($valor);
}

function obtenerCodigoServicioPorIdServicio($idServicio){
   $dbh = new ConexionIBNORCA();
   $stmt = $dbh->prepare("SELECT codigo FROM servicios where idServicio=$idServicio");
   $stmt->execute();
   $valor="-";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['codigo'];
   }
   return($valor);
}

function obtenerServicioPorPropuesta($simulacion){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT idServicio FROM simulaciones_servicios where codigo=$simulacion");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['idServicio'];
   }
   return($valor);
}

function descripcionClaServicio($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT Descripcion FROM cla_servicios where IdClaServicio=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $descripcionX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $descripcionX=$row['Descripcion'];
   }
   return($descripcionX);
}


function obtenerListaContactosEmpresaDelServicio($cod_cliente){
  $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
  $sIde = "ifinanciero";
  $sKey = "ce94a8dabdf0b112eafa27a5aa475751"; 
  /*Datos de Clientes Empresa*/
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"DatosClienteEmpresa", "IdCliente"=>$cod_cliente); //
    $parametros=json_encode($parametros);
    // abrimos la sesión cURL
    $ch = curl_init();
    // definimos la URL a la que hacemos la petición
    curl_setopt($ch, CURLOPT_URL,$direccion."cliente/ws-cliente-listas.php");     
    // indicamos el tipo de petición: POST
    curl_setopt($ch, CURLOPT_POST, TRUE);
    // definimos cada uno de los parámetros
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    // recibimos la respuesta y la guardamos en una variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    // cerramos la sesión cURL
    curl_close ($ch);  
    return json_decode($remote_server_output);       
}
function obtenerListaContactosClientesDelServicio($cod_cliente){
  $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
  $sIde = "ifinanciero";
  $sKey = "ce94a8dabdf0b112eafa27a5aa475751"; 
  /*Datos de Clientes Empresa*/
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ContactosCliente", "IdCliente"=>$cod_cliente); //
    $parametros=json_encode($parametros);
    // abrimos la sesión cURL
    $ch = curl_init();
    // definimos la URL a la que hacemos la petición
    curl_setopt($ch, CURLOPT_URL,$direccion."cliente/ws-cliente-listas.php");     
    // indicamos el tipo de petición: POST
    curl_setopt($ch, CURLOPT_POST, TRUE);
    // definimos cada uno de los parámetros
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    // recibimos la respuesta y la guardamos en una variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    // cerramos la sesión cURL
    curl_close ($ch);  
    return json_decode($remote_server_output);       
}

function obtenerListaClientes(){
  $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
  //LLAVES DE ACCESO AL WS
  $sIde = "ifinanciero";
  $sKey = "ce94a8dabdf0b112eafa27a5aa475751";   
  /*Datos de Clientes Empresa*/
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "lista"=>"Clientes"); 
  $parametros=json_encode($parametros);
    // abrimos la sesión cURL
    $ch = curl_init();
    // definimos la URL a la que hacemos la petición
    curl_setopt($ch, CURLOPT_URL,$direccion."cliente/ws-cliente-listas.php");     
    // indicamos el tipo de petición: POST
    curl_setopt($ch, CURLOPT_POST, TRUE);
    // definimos cada uno de los parámetros
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    // recibimos la respuesta y la guardamos en una variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    // cerramos la sesión cURL
    curl_close ($ch);  
    return json_decode($remote_server_output);   
}
function obtenerNitCliente($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT identificacion FROM clientes where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $valor="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['identificacion'];
   }
   return($valor);
}
function obtenerDescuentoCliente($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT descuento FROM clientes where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $valor="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['descuento'];
   }
   return($valor);
}

function obtenerNombreConcatenadoProveedorDetalleSolicitudRecurso($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT DISTINCT p.nombre from solicitud_recursosdetalle d, af_proveedores p where d.cod_solicitudrecurso=$codigo and d.cod_proveedor=p.codigo");
   $stmt->execute();
   $valor="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor.=$row['nombre'].",";
   }
   if (strlen($valor)>22){
    $valor= substr($valor, 0, 22)."..."; 
   }
   
   return($valor);
}
function obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT DISTINCT p.nombre from solicitud_recursosdetalle d, plan_cuentas p where d.cod_solicitudrecurso=$codigo and d.cod_plancuenta=p.codigo");
   $stmt->execute();
   $valor="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor.=$row['nombre'].",";
   }
   if (strlen($valor)>22){
    $valor= substr($valor, 0, 22)."..."; 
   }
   
   return($valor);
}

function obtenerPersonaCambioEstado($tipo,$objeto,$estado){
   $dbh = new ConexionIBNORCA();
   $stmt = $dbh->prepare("SELECT * FROM estadoobjeto where IdTipoObjeto=$tipo and IdObjeto = $objeto and IdEstado=$estado");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['idResponsable'];
   }
   return($valor);
}
function obtenerFechaCambioEstado($tipo,$objeto,$estado){
   $dbh = new ConexionIBNORCA();
   $stmt = $dbh->prepare("SELECT DATE_FORMAT(FechaEstado,'%d/%m/%Y %H:%i:%s')as fecha_registro_x FROM estadoobjeto where IdTipoObjeto=$tipo and IdObjeto = $objeto and IdEstado=$estado");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['fecha_registro_x'];
   }
   return($valor);
}

function obtenerIdPropuestaServicioIbnorca($idServicio){
   $dbh = new ConexionIBNORCA();
   $stmt = $dbh->prepare("SELECT idPropuesta FROM servicios where idServicio=$idServicio");
   $stmt->execute();
   $valor="NONE";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['idPropuesta'];
   }
   if($valor==null||$valor==0){
    $valor="NONE";
   }
   return($valor);
}

 function obtenerIdAreaServicioIbnorca($idServicio){
   $dbh = new ConexionIBNORCA();
   $stmt = $dbh->prepare("SELECT IdArea FROM servicios where idServicio=$idServicio");
   $stmt->execute();
   $valor="NONE";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['IdArea'];
   }
   return($valor);
}
function obtenerIdUnidadServicioIbnorca($idServicio){
   $dbh = new ConexionIBNORCA();
   $stmt = $dbh->prepare("SELECT IdOficina FROM servicios where idServicio=$idServicio");
   $stmt->execute();
   $valor="NONE";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['IdOficina'];
   }
   return($valor);
}
function obtenerCuentaPasivaSolicitudesRecursos($cuenta){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cod_cuentapasivo FROM solicitud_recursoscuentas where cod_cuenta=$cuenta");
   $stmt->execute();
   $valor=obtenerValorConfiguracion(36);
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cod_cuentapasivo'];
   }
   if($valor==0){
    $valor=obtenerValorConfiguracion(36);
   }
   return($valor);
}
function obtenerNumeroFacturaSolicitudRecursos($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT * FROM solicitud_recursosdetalle where cod_solicitudrecurso=$codigo");
   $stmt->execute();
   $titulo="";$index=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      if($index==0){
       $valor=obtenerNumeroFacturaSolicitudRecursoDetalle($row['codigo']);
       $titulo=$valor;
      }else{
      $titulo=$valor." - ".obtenerNumeroFacturaSolicitudRecursoDetalle($row['codigo']);  
      } 
      $index++;      
   }
   return($titulo);
}

function obtenerNumeroFacturaSolicitudRecursoDetalle($codigo){
  $facturas=obtenerFacturasSoli($codigo);
            $numeroFac="";
    while ($rowFac = $facturas->fetch(PDO::FETCH_ASSOC)) {
                $numeroFac=$rowFac['nro_factura'];          
    }
    return $numeroFac;
}

function obtenerProveedorSolicitudRecursos($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT * FROM solicitud_recursosdetalle where cod_solicitudrecurso=$codigo");
   $stmt->execute();
   $titulo="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $titulo=nameProveedor($row['cod_proveedor']);        
   }
   return($titulo);
}

function obtenerPresupuestoEjecucionDelServicio($oficina,$area,$anio,$mes,$cuenta){
  $direccion=obtenerValorConfiguracion(45);//direccion del Server del Servicio
  $sIde = "monitoreo"; 
  $sKey="101010"; 
/*PARAMETROS PARA LA OBTENCION DE LISTAS DE PERSONAL*/
  /*
  $oficina="5";
  $area="38";
  $anio="2020";
  $mes="4";
  $cuenta="5020101001";
  */

  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "oficina"=>$oficina, "area"=>$area, "anio"=>$anio, "mes"=>$mes, "cuenta"=>$cuenta, "accion"=>"listar"); //

  $parametros=json_encode($parametros);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$direccion."ws/wsPresupuestoGastosCuenta.php");
  curl_setopt($ch, CURLOPT_POST, TRUE);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $remote_server_output = curl_exec ($ch);
    // cerramos la sesión cURL
    curl_close ($ch);  
    return json_decode($remote_server_output);       
  }


function nameNorma($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT abreviatura FROM normas where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $valor="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['abreviatura'];
   }
   return($valor);
}

function obtenerListaClientesWS(){
  $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
  $sIde = "ifinanciero";
  $sKey = "ce94a8dabdf0b112eafa27a5aa475751";
  // $sIde = "monitoreo"; 
  // $sKey = "837b8d9aa8bb73d773f5ef3d160c9b17";

  /*Lista de Clientes Empresa*/
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "lista"=>"Clientes");
    $parametros=json_encode($parametros);
    // abrimos la sesión cURL
    $ch = curl_init();
    // definimos la URL a la que hacemos la petición
    curl_setopt($ch, CURLOPT_URL,$direccion."cliente/ws-cliente-listas.php");     
    // indicamos el tipo de petición: POST
    curl_setopt($ch, CURLOPT_POST, TRUE);
    // definimos cada uno de los parámetros
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    // recibimos la respuesta y la guardamos en una variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    // cerramos la sesión cURL
    curl_close ($ch);  
    return json_decode($remote_server_output);       
}
function nameContacto($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre,paterno,materno FROM clientes_contactos where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $valor=null;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['nombre']." ".$row['paterno']." ".$row['materno'];
   }
   return($valor);
}

function obtenerCiudadDeUnidad($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM ciudades where cod_unidad=:codigo limit 1");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $valor=null;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['nombre'];
   }
   return($valor); 
}

function obtenerServiciosClaServicioTipoNombre($id){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT DISTINCT p.descripcion_n2 AS nombre FROM cla_servicios p where p.vigente=1 and p.codigo_n2=$id";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $valor="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['nombre'];
   }
   return($valor);  
}

function obtenerServiciosTipoObjetoNombre($id){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT nombre FROM objeto_servicio where codigo=$id";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $valor="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['nombre'];
   }
   return($valor);  
}

function obtenerTipoServicioPorIdServicio($idServicio){
   $dbh = new ConexionIBNORCA();
   $stmt = $dbh->prepare("SELECT IdTipo FROM servicios where idServicio=$idServicio");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['IdTipo'];
   }
   return($valor);
}

function obtenerCodigoObjetoServicioPorIdSimulacion($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cod_objetoservicio from simulaciones_servicios where codigo=$codigo");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cod_objetoservicio'];
   }
   return($valor);
}

function obtenerIdServicioPorIdSimulacion($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT idServicio from simulaciones_servicios where codigo=$codigo");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['idServicio'];
   }
   return($valor);
}

function obtenerGlosaSolicitudSimulacionCuentaPlantillaServicio($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT DISTINCT glo.glosa from (
       (select * from v_propuestas_detalle_variables  where cod_simulacionservicio=$codigo order by cod_detalle)
        UNION
         (select * from v_propuestas_detalle_honorarios  where cod_simulacionservicio=$codigo)
        )  as glo ";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}

function obtenerAnioSimulacionServicio($codigo){
   $dbh = new Conexion();
   $valor=0;
   $sql="SELECT anios from simulaciones_servicios p where p.codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['anios'];
  }
  return $valor;
}

function sumaMontosDebeHaberComprobantesDetalleResultados($fechaFinal,$tipoBusqueda,$arrayUnidades,$arrayAreas,$gestion,$fechaInicio)
 {
   $dbh = new Conexion();
   $sql="";
   $sqlAreas="";
   $sqlUnidades="";
  $fechaFinalMod=explode("/", $fechaFinal);
  //formateando fecha
   if($fechaInicio=="none"){
    $fi=$fechaFinalMod[2]."-01-01";
   }else{
     $fechaFinalModIni=explode("/", $fechaInicio);
     $fi=$fechaFinalModIni[2]."-".$fechaFinalModIni[1]."-".$fechaFinalModIni[0];
   }
  
  $fa=$fechaFinalMod[2]."-".$fechaFinalMod[1]."-".$fechaFinalMod[0];
  //$fi=$fechaFinalMod[2]."-01-01";

   for ($i=0; $i < count($arrayAreas); $i++) {
      if($i==0){
        $sqlAreas.="and (";
      }
      if($i==(count($arrayAreas)-1)){
        $sqlAreas.="d.cod_area='".$arrayAreas[$i]."')";
       }else{
        $sqlAreas.="d.cod_area='".$arrayAreas[$i]."' or ";
       }  
   }
   //busqueda de unidades
   for ($i=0; $i < count($arrayUnidades); $i++) {
      if($i==0){
        $sqlUnidades.="and (";
      }
      if($i==(count($arrayUnidades)-1)){
        $sqlUnidades.="d.cod_unidadorganizacional='".$arrayUnidades[$i]."')";
       }else{
        $sqlUnidades.="d.cod_unidadorganizacional='".$arrayUnidades[$i]."' or ";
       }  
   }
   
   $sql="(SELECT sum(total_debe) as t_debe,sum(total_haber) as t_haber,1 as tipo from plan_cuentas p join 
           (select d.cod_cuenta,sum(debe) as total_debe,sum(haber) as total_haber 
            from comprobantes_detalle d join comprobantes c on c.codigo=d.cod_comprobante and c.cod_estadocomprobante<>2 
            where (c.fecha between '$fi' and '$fa') $sqlUnidades and c.cod_gestion='$gestion' group by (d.cod_cuenta) order by d.cod_cuenta) cuentas_monto
        on p.codigo=cuentas_monto.cod_cuenta where p.numero like '4%' and p.nivel=5 order by p.numero)
         UNION
         (SELECT sum(total_debe) as t_debe,sum(total_haber) as t_haber,2 as tipo from plan_cuentas p join 
           (select d.cod_cuenta,sum(debe) as total_debe,sum(haber) as total_haber 
            from comprobantes_detalle d join comprobantes c on c.codigo=d.cod_comprobante and c.cod_estadocomprobante<>2
            where (c.fecha between '$fi' and '$fa') $sqlUnidades and c.cod_gestion='$gestion' group by (d.cod_cuenta) order by d.cod_cuenta) cuentas_monto
        on p.codigo=cuentas_monto.cod_cuenta where p.numero like '5%' and p.nivel=5 order by p.numero)";
    
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtieneCuentaPadre($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cod_padre FROM plan_cuentas where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['cod_padre'];
   }
   return($nombreX);
}

function obtieneCuentaPadreAux($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT c.cod_cuenta FROM plan_cuentas p,cuentas_auxiliares c where c.cod_cuenta=p.codigo and c.codigo=$codigo");
   $stmt->execute();
   $nombreX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['cod_cuenta'];
   }
   return($nombreX);
}

function obtenerDistribucionCentroCostosUnidadActivo(){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT dd.*,u.nombre FROM distribucion_gastosporcentaje_detalle dd join distribucion_gastosporcentaje d on d.codigo=dd.cod_distribucion_gastos 
join unidades_organizacionales u on u.codigo=dd.cod_unidadorganizacional  
where estado=1";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}

function obtenerDistribucionCentroCostosAreaActivo($unidad){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT dd.*,u.nombre FROM distribucion_gastosarea_detalle dd join distribucion_gastosarea d on d.codigo=dd.cod_distribucionarea 
  join areas u on u.codigo=dd.cod_area  
  where estado=1 and d.cod_uo=$unidad";  
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerPorcentajeDistribucionGastoSolicitud($antValor,$tipo,$of_area,$codigoSolicitud){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT porcentaje FROM distribucion_gastos_solicitud_recursos where tipo_distribucion=$tipo and oficina_area=$of_area and cod_solicitudrecurso=$codigoSolicitud");
   $stmt->execute();
   $valor=$antValor;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['porcentaje'];
   }
   return($valor);
}
function obtenerSiDistribucionSolicitudRecurso($codigoSolicitud){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT DISTINCT tipo_distribucion FROM distribucion_gastos_solicitud_recursos where cod_solicitudrecurso=$codigoSolicitud");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor+=$row['tipo_distribucion']; //obtener el valor 1:oficina, 2:area, 3:ambos
   }
   return($valor);
}

function obtenerComprobantePlantilla(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from plantillas_comprobante c");
   $stmt->execute();
   $codigoComprobante=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoComprobante=$row['codigo'];
   }
   return($codigoComprobante);
}

function obtenerListaCuentaBancoProveedorWS($codClienteProv){
  $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
  $sIde = "ifinanciero"; 
  $sKey = "ce94a8dabdf0b112eafa27a5aa475751";
  /*Lista de Clientes Empresa*/
    $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ListaCuentaBancoxCliente","IdCliente" => $codClienteProv); 
    $parametros=json_encode($parametros);
    // abrimos la sesión cURL
    $ch = curl_init();
    // definimos la URL a la que hacemos la petición
    curl_setopt($ch, CURLOPT_URL,$direccion."lista/ws-lst-cuentabanco.php");     
    // indicamos el tipo de petición: POST
    curl_setopt($ch, CURLOPT_POST, TRUE);
    // definimos cada uno de los parámetros
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    // recibimos la respuesta y la guardamos en una variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    // cerramos la sesión cURL
    curl_close ($ch);  
    return json_decode($remote_server_output);     
}
function obtenerDatosCuentaBancoProveedorWS($codClienteProv,$cuenta){
  $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
  $sIde = "ifinanciero"; 
  $sKey = "ce94a8dabdf0b112eafa27a5aa475751";
  /*Lista de Clientes Empresa*/
    $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"DatosCuentaBanco","IdCuentaBanco" => $cuenta,"IdCliente" => $codClienteProv); 
    $parametros=json_encode($parametros);
    // abrimos la sesión cURL
    $ch = curl_init();
    // definimos la URL a la que hacemos la petición
    curl_setopt($ch, CURLOPT_URL,$direccion."lista/ws-lst-cuentabanco.php");     
    // indicamos el tipo de petición: POST
    curl_setopt($ch, CURLOPT_POST, TRUE);
    // definimos cada uno de los parámetros
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    // recibimos la respuesta y la guardamos en una variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    // cerramos la sesión cURL
    curl_close ($ch);  
    return json_decode($remote_server_output);     
}

function nombreComprobante($codigo){
  $dbh = new Conexion();
  $sql="SELECT c.cod_tipocomprobante, (select tc.abreviatura from tipos_comprobante tc where tc.codigo=c.cod_tipocomprobante)as tipoComprobante, MONTH(c.fecha)as mes, c.numero from comprobantes c where c.codigo='$codigo'";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $nombreComprobante="";
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codTipoComprobanteX=$row['cod_tipocomprobante'];
    $tipoComprobanteX=$row['tipoComprobante'];
    $mesComprobanteX=str_pad($row['mes'], 2, "0", STR_PAD_LEFT);
    $numeroX=str_pad($row['numero'], 5, "0", STR_PAD_LEFT);;

    if($codTipoComprobanteX<>4){
      $nombreComprobante=$tipoComprobanteX.$mesComprobanteX."-".$numeroX;
    }else{
      $nombreComprobante=$tipoComprobanteX."-".$numeroX;
    }
  }
  return($nombreComprobante);  
}

function montoCuentaRangoFechas($unidadArray, $unidadCostoArray, $areaCostoArray, $desde, $hasta, $cuenta, $gestion){
  $dbh = new Conexion();
  $sql="SELECT sum(d.debe)as debe, sum(d.haber)as haber
      FROM plan_cuentas p 
      join comprobantes_detalle d on p.codigo=d.cod_cuenta 
      join areas a on d.cod_area=a.codigo 
      join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional 
      join comprobantes c on d.cod_comprobante=c.codigo
      where c.cod_gestion=$gestion and p.codigo=$cuenta and c.cod_estadocomprobante<>2 and c.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) and c.cod_unidadorganizacional in ($unidadArray)";
  //echo $sql;
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $debeX=0; $haberX=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $debeX=$row['debe'];
    $haberX=$row['haber'];
  }
  $variableMontos=array($debeX,$haberX);
  return($variableMontos); 
}
function verificarListaDistribucionGastoSolicitudRecurso($codigoSolicitud){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT * FROM distribucion_gastos_solicitud_recursos where cod_solicitudrecurso=$codigoSolicitud");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor++;
   }
   return($valor);
}
function verificarHayAmbasDistribucionesSolicitudRecurso($codigoSolicitud){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT DISTINCT tipo_distribucion FROM distribucion_gastos_solicitud_recursos where cod_solicitudrecurso=$codigoSolicitud");
   $stmt->execute();
   $valor=0;$val2=0;$distribucion=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['tipo_distribucion'];
      $val2++;
   }
   switch ($val2) {
     case 1:
     if($valor==1){
      $distribucion=1;
     }else{
      $distribucion=2;
     }
     break;
     case 2:
       $distribucion=3;
     break;
     default:
       $distribucion=0;
     break;
   }
   return($distribucion);
}
function obtenerDistribucionGastoSolicitudRecurso($codigo,$tipo,$monto){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT codigo,oficina_area,porcentaje,(porcentaje/100)*$monto as monto_porcentaje,cod_solicitudrecurso from distribucion_gastos_solicitud_recursos where cod_solicitudrecurso=$codigo and tipo_distribucion=$tipo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function costoVariablesHonorariosSimulacionServicio($sim,$anio){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT sum(monto) as monto FROM simulaciones_servicios_auditores where cod_simulacionservicio=$sim and cod_anio=$anio and habilitado=1 and cantidad=1");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['monto'];
   }
   return($valor);
}
function eliminar_acentos($cadena){
    
    //Reemplazamos la A y a
    $cadena = str_replace(
    array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
    array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
    $cadena
    );
 
    //Reemplazamos la E y e
    $cadena = str_replace(
    array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
    array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
    $cadena );
 
    //Reemplazamos la I y i
    $cadena = str_replace(
    array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
    array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
    $cadena );
 
    //Reemplazamos la O y o
    $cadena = str_replace(
    array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
    array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
    $cadena );
 
    //Reemplazamos la U y u
    $cadena = str_replace(
    array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
    array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
    $cadena );
 
    //Reemplazamos la N, n, C y c
    $cadena = str_replace(
    array('Ñ', 'ñ', 'Ç', 'ç'),
    array('N', 'n', 'C', 'c'),
    $cadena
    );
    
    return $cadena;
  }

  function datosPDFFacturasVenta($html,$codFactura){
  //aumentamos la memoria  
  ini_set("memory_limit", "128M");
  require_once 'assets/libraries/dompdf/dompdf_config.inc.php';
    $dompdf = new DOMPDF();
    $dompdf->set_paper("letter", "portrait");
    $dompdf->load_html($html);    
    $dompdf->render();

    $estado=obtener_estado_facturas($codFactura);
  if($estado==2){ //facturas anuladas MARCA DE AGUA ANULADO
    //marca de agua
    $canvas2 = $dompdf->get_canvas(); 
    $w = $canvas2->get_width(); 
    $h = $canvas2->get_height(); 
    $font = Font_Metrics::get_font("times"); 
    $text = "ANULADO"; 
    $txtHeight = -100; 
    $textWidth = 250; 
    $canvas2->set_opacity(.2); 
    $x = (($w-$textWidth)/2); 
    $y = (($h-$txtHeight)/2); 
    $canvas2->text($x, $y, $text, $font, 100, $color = array(167, 14, 14), $word_space = 0.0, $char_space = 0.0, $angle = -45);
  //fin marca agua
   } 

    $pdf = $dompdf->output();
    return array('archivo' => $pdf,'base64'=>base64_encode($pdf));
}

  function obtenerNumeroComprobante($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT numero from comprobantes where codigo=$codigo");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['numero'];
   }
   return($valor);
}
function obtenerGlosaComprobante($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT glosa from comprobantes where codigo=$codigo");
   $stmt->execute();
   $valor="SIN COMPROBANTE";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['glosa'];
   }
   return($valor);
}

function nameTipoPago($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM tipos_pagoproveedor where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}

function comprobarCuentasPasivasDeSolicitudRecursos($codigo){
   $cuentaExclusiva=451; //CUENTA DE OTROS PAGOS
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT * from solicitud_recursoscuentas where cod_cuenta in (SELECT cod_plancuenta from solicitud_recursosdetalle where cod_solicitudrecurso=$codigo) and (cod_cuentapasivo=0 or cod_cuentapasivo=null) and cod_cuenta!=$cuentaExclusiva");
   $stmt->execute();
   $nombreX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX++;
   }
   return($nombreX);
}

function comprobarCuentasOtrosPagosDeSolicitudRecursos($codigo){
   $cuentaExclusiva=451; //CUENTA DE OTROS PAGOS
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT * from solicitud_recursoscuentas where cod_cuenta in (SELECT cod_plancuenta from solicitud_recursosdetalle where cod_solicitudrecurso=$codigo) and cod_cuenta=$cuentaExclusiva");
   $stmt->execute();
   $nombreX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX++;
   }
   return($nombreX);
}

function obtenerTipoPagoSolicitudRecursoDetalle($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT * FROM solicitud_recursosdetalle where cod_solicitudrecurso=$codigo and cod_tipopagoproveedor!=1");
   $stmt->execute();
   $nombreX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX++;
   }
   return($nombreX);
}
function obtenerDatosProveedoresPagoDetalle($codigo){
  include 'solicitudes/configModule.php';
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT pd.*,sd.detalle,s.fecha as fecha_sol,s.numero,s.cod_unidadorganizacional,s.codigo as cod_sol from pagos_proveedoresdetalle pd join solicitud_recursosdetalle sd on sd.codigo=pd.cod_solicitudrecursosdetalle join solicitud_recursos s on pd.cod_solicitudrecursos=s.codigo  where pd.cod_pagoproveedor=$codigo");
   $stmt->execute();
   $proveedores=[];
   $detalles=[];
   $fechaSol=[];
   $numero=[];
   $numeroSolo=[];
   $oficina=[];
   $index=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $proveedores[$index]=nameProveedor($row['cod_proveedor']);
      $detalles[$index]=$row['detalle'];
      if(($index % 2) == 0){
        $estiloBoton="btn-warning";
      }else{
        $estiloBoton="btn-danger";
      }
      $fechaSol[$index]=strftime('%d/%m/%Y',strtotime($row['fecha_sol']));
      $numero[$index]="<a title='ver Archivos Adjuntos' href='".$urlVer."?cod=".$row['cod_sol']."' target='_blank' class=''>".$row['numero']."</a>";
      $numeroSolo[$index]=$row['numero'];
      $oficina[$index]=abrevUnidad_solo($row['cod_unidadorganizacional']);
      $index++;
   }
   return array(implode(",",array_unique($proveedores)),implode(",",array_unique($detalles)),implode(",",array_unique($fechaSol)),implode(", ",array_unique($numero)),implode(",",array_unique($oficina)),implode("-",array_unique($numeroSolo)));
}

function obtenerDatosProveedoresPagoDetalleLote($codigo){
  include 'solicitudes/configModule.php';
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT pd.*,sd.detalle,s.fecha as fecha_sol,s.numero,s.cod_unidadorganizacional,s.codigo as cod_sol from pagos_proveedoresdetalle pd join solicitud_recursosdetalle sd on sd.codigo=pd.cod_solicitudrecursosdetalle join solicitud_recursos s on pd.cod_solicitudrecursos=s.codigo join pagos_proveedores p on p.codigo=pd.cod_pagoproveedor where p.cod_pagolote=$codigo");
   $stmt->execute();
   $proveedores=[];
   $detalles=[];
   $fechaSol=[];
   $numero=[];
   $numeroSolo=[];
   $oficina=[];
   $index=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $proveedores[$index]=nameProveedor($row['cod_proveedor']);
      $detalles[$index]=$row['detalle'];
      if(($index % 2) == 0){
        $estiloBoton="btn-warning";
      }else{
        $estiloBoton="btn-danger";
      }
      $fechaSol[$index]=strftime('%d/%m/%Y',strtotime($row['fecha_sol']));
      $numero[$index]="<a title='ver Archivos Adjuntos' href='".$urlVer."?cod=".$row['cod_sol']."' target='_blank' class=''>".$row['numero']."</a>";
      $numeroSolo[$index]=$row['numero'];
      $oficina[$index]=abrevUnidad_solo($row['cod_unidadorganizacional']);
      $index++;
   }
   return array(implode(", ",array_unique($proveedores)),implode(",",array_unique($detalles)),implode(",",array_unique($fechaSol)),implode(", ",array_unique($numero)),implode(",",array_unique($oficina)),implode("-",array_unique($numeroSolo)));
}

function listaDetallePagosProveedores($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT pd.*,sd.detalle,sd.cod_plancuenta from pagos_proveedoresdetalle pd join solicitud_recursosdetalle sd on pd.cod_solicitudrecursosdetalle=sd.codigo where pd.cod_pagoproveedor=$codigo");
   $stmt->execute();
   return $stmt;
}

function listaDetallePagosProveedoresLote($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT pd.*,sd.detalle,sd.cod_plancuenta from pagos_proveedoresdetalle pd join solicitud_recursosdetalle sd on pd.cod_solicitudrecursosdetalle=sd.codigo join pagos_proveedores p on p.codigo=pd.cod_pagoproveedor where p.cod_pagolote=$codigo");
   $stmt->execute();
   return $stmt;
}

function obtenerGlosaSolicitudRecursoDetalle($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT glosa_comprobantedetalle from solicitud_recursosdetalle where codigo=$codigo");
   $stmt->execute();
   $valor="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['glosa_comprobantedetalle'];
    }
   return($valor);
}

function obtenerCodUnidadSucursal($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT codigo FROM unidades_organizacionales where cod_sucursal=:codigo limit 1");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $codigoX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoX=$row['codigo'];
   }
   return($codigoX);
}
function obtenerSucursalCodUnidad($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cod_sucursal FROM unidades_organizacionales where codigo=:codigo limit 1");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $codigoX=null;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoX=$row['cod_sucursal'];
   }
   return($codigoX);
}
function obtenerNombreTipoPago($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre from tipos_objetofacturacion where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['nombre'];
   }
   return($valor);
}

function insertarFacturaSolicitudAComprobante($cod_detallesol,$cod_detallecomp){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("INSERT INTO facturas_compra
    SELECT $cod_detallecomp as cod_comprobantedetalle,null as cod_solicitudrecursodetalle,nit,nro_factura,fecha,razon_social,importe,exento,nro_autorizacion,codigo_control,ice,tasa_cero,tipo_compra from facturas_compra where cod_solicitudrecursodetalle=$cod_detallesol");
   $flaf=$stmt->execute();
   return $flag;
}

function obtenerCodigoPrecioCosto(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from precios_simulacioncosto c");
   $stmt->execute();
   $codigo=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigo=$row['codigo'];
   }
   return($codigo);
}

function obtenerPresupuestoAreaGestion($gestion,$codArea){
  $valor = obtenerValorConfiguracion(52);
  $sqlUnidad="";
  if($valor==1){
   $codOficina=$_SESSION["globalUnidad"];
   $sqlUnidad=" and d.cod_unidadorganizacional=$codOficina";
  }
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT sum(d.debe-d.haber) as presupuesto from comprobantes_detalle d, comprobantes c where d.cod_comprobante=c.codigo and c.cod_gestion=$gestion and d.cod_area=$codArea $sqlUnidad");
   $stmt->execute();
   $codigo=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigo=$row['presupuesto'];
   }
   return($codigo);
}

function obtenerPrecioRegistradoPlantillaCosto($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT ingreso_presupuestado from plantillas_costo where codigo=$codigo");  
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['ingreso_presupuestado'];
   }
   return($valor);
}

function nameTipoCurso($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM tipos_cursos where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}

function obtenerPresupuestoEjecucionPorArea($oficina,$area,$anio,$mes){
  $direccion=obtenerValorConfiguracion(45);//direccion del Server del Servicio
  $sIde = "monitoreo"; 
  $sKey="101010"; 

/*PARAMETROS PARA LA OBTENCION DE LISTAS DE PERSONAL*/
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "oficina"=>$oficina, "area"=>$area, "anio"=>$anio, "mes"=>$mes, "accion"=>"listar"); //

  $parametros=json_encode($parametros);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$direccion."ws/wsPresupuestoIngresosTotal.php");
  curl_setopt($ch, CURLOPT_POST, TRUE);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $remote_server_output = curl_exec ($ch);
  curl_close ($ch);
  $datos=json_decode($remote_server_output);
    return array('presupuesto' => $datos->presupuesto, 'ejecutado' => $datos->ejecutado);       
  }

function obtenerPrecioSimulacionCosto($codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT pa.venta_local from simulaciones_costos sc join precios_simulacioncosto pa on sc.cod_precioplantilla=pa.codigo where sc.codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $num=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
    $num=$row['venta_local'];
  }
  return $num;
}
function obtenerPrecioSimulacionCostoGeneral($codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT pa.venta_local,sc.cantidad_alumnoslocal from simulaciones_costos sc join precios_simulacioncosto pa on sc.cod_precioplantilla=pa.codigo where sc.codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute(); 
   $num=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
  {
    $num=$row['venta_local']*$row['cantidad_alumnoslocal'];
  }
  return $num;
}
function obtenerCantidadCursosPlantillaCosto($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cantidad_cursosmes from plantillas_costo where codigo=$codigo");  
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cantidad_cursosmes'];
   }
   return($valor);
}
function obtenerPrecioAlternativoDetalle($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT * from precios_simulacioncostodetalle where cod_preciosimulacion=$codigo");  
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor+=$row['cantidad']*$row['monto'];
   }
   return($valor);
}

function obtenerGlosaSolicitudSimulacionCuentaPlantillaCosto($codigo,$codigoPlan){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT DISTINCT tablap.glosa
FROM (SELECT pc.codigo,pc.numero,pc.nombre,pp.nombre as partida, pp.codigo as cod_partida,sc.monto_local,sc.monto_externo from cuentas_simulacion sc 
join partidas_presupuestarias pp on pp.codigo=sc.cod_partidapresupuestaria 
join plan_cuentas pc on sc.cod_plancuenta=pc.codigo where sc.cod_simulacioncostos=$codigo order by pp.codigo) tabla_uno,
simulaciones_detalle tablap where tablap.cod_cuenta=tabla_uno.codigo and (tablap.cod_plantillacosto!='' or tablap.cod_plantillacosto!=NULL) and tablap.cod_plantillacosto=$codigoPlan and tablap.cod_simulacioncosto=$codigo and tablap.habilitado=1 and tablap.cod_estadoreferencial=1 order by tabla_uno.codigo;";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}

function obtenerDetalleSolicitudSimulacionCuentaPlantillaServicioFiltroSec($codigo,$codigoPlan,$anio,$item_detalle,$codigo_detalle){
   $dbh = new Conexion();
   if($codigo_detalle!="all"){
    $item_detalleSQL1="tablap.glosa='$item_detalle' and";
   }else{
    $item_detalleSQL1="";
   }
   $sql="";
   $sql="SELECT tablap.codigo as codigo_detalle,tablap.glosa,tablap.monto_total,tablap.habilitado,tabla_uno.* 
FROM (SELECT pc.codigo,pc.numero,pc.nombre,pp.nombre as partida, pp.codigo as cod_partida,sc.monto_local,sc.monto_externo from cuentas_simulacion sc 
join partidas_presupuestarias pp on pp.codigo=sc.cod_partidapresupuestaria 
join plan_cuentas pc on sc.cod_plancuenta=pc.codigo 
where sc.cod_simulacioncostos=$codigo order by pp.codigo) tabla_uno,simulaciones_detalle tablap 
where $item_detalleSQL1 tablap.cod_cuenta=tabla_uno.codigo and (tablap.cod_plantillacosto!='' or tablap.cod_plantillacosto!=NULL) and tablap.cod_plantillacosto=$codigoPlan and tablap.cod_simulacioncosto=$codigo and tablap.habilitado=1 and tablap.cod_estadoreferencial=1 order by tabla_uno.codigo;";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}

function ejecutadoEgresosMes($oficina, $anio, $mes, $area, $acumulado, $cuenta){
  $direccion=obtenerValorConfiguracion(45);//direccion del Server del Servicio
  $sIde = "monitoreo"; 
  $sKey="101010"; 
  if($oficina==0){
    $oficina=0;
  }
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "oficina"=>$oficina, "area"=>$area, "anio"=>$anio, "mes"=>$mes, "cuenta"=>$cuenta, "accion"=>"listar"); //

  $parametros=json_encode($parametros);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$direccion."ws/wsPresupuestoGastosCuenta.php");
  curl_setopt($ch, CURLOPT_POST, TRUE);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $remote_server_output = curl_exec ($ch);
  curl_close ($ch);
  $datos=json_decode($remote_server_output);
    return $datos->presupuesto; 
}
function presupuestadoEgresosMes($oficina, $anio, $mes, $area, $acumulado, $cuenta){
  $direccion=obtenerValorConfiguracion(45);//direccion del Server del Servicio
  $sIde = "monitoreo"; 
  $sKey="101010"; 
  if($oficina==0){
    $oficina=0;
  }
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "oficina"=>$oficina, "area"=>$area, "anio"=>$anio, "mes"=>$mes, "cuenta"=>$cuenta, "accion"=>"listar"); //

  $parametros=json_encode($parametros);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$direccion."ws/wsPresupuestoGastosCuenta.php");
  curl_setopt($ch, CURLOPT_POST, TRUE);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $remote_server_output = curl_exec ($ch);
  curl_close ($ch);
  $datos=json_decode($remote_server_output);
    return $datos->presupuesto; 
}
function obtenerCodigoPrecioSimulacionCosto($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cod_precioplantilla from simulaciones_costos where codigo=$codigo");  
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cod_precioplantilla'];
   }
   return($valor);
}

function nameEstadoFactura($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT abreviatura from estados_factura  where codigo=$codigo";   
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['abreviatura'];
   }
   return($valor);
}


function verifica_pago_curso($IdCurso,$ci_estudiante){
  $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
  $sIde = "ifinanciero";
  $sKey = "ce94a8dabdf0b112eafa27a5aa475751";  
  // OBTENER MODULOS PAGADOS x CURSO
  // $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
  //         "accion"=>"ObtenerModulosPagados", 
  //         "Identificacion"=>$ci_estudiante, //7666922 ci del alumno
  //         "IdCurso"=>$IdCurso); //1565 

  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
          "accion"=>"ObtenerModuloxPagarPagadoySaldo", 
          "Identificacion"=>$ci_estudiante, //7666922 ci del alumno
          "IdCurso"=>$IdCurso); //1565
  $parametros=json_encode($parametros);
  $ch = curl_init();
  // curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibnob/capacitacion/ws-inscribiralumno.php"); //PRUEBA
  curl_setopt($ch, CURLOPT_URL,$direccion."capacitacion/ws-inscribiralumno.php");
  curl_setopt($ch, CURLOPT_POST, TRUE);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $remote_server_output = curl_exec ($ch);
  curl_close ($ch);
  return json_decode($remote_server_output);  
}

function resgistrar_pago_curso($ci_estudiante,$IdCurso,$Idmodulo,$monto,$cod_solfac){
  $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
  $sIde = "ifinanciero";
  $sKey = "ce94a8dabdf0b112eafa27a5aa475751";  
  //REGISTRAR CONTROL PAGOS 
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
          "accion"=>"RegistrarControlPago", 
          "Identificacion"=>$ci_estudiante, //ci del alumno
          "IdCurso"=>$IdCurso,
          "IdModulo"=>$Idmodulo, 
          "MontoPago"=> $monto, 
          "IdSolicitudFactura"=>$cod_solfac,
          "Plataforma"=>13 // 13=Sistema Financiero
          );
  $parametros=json_encode($parametros);
  $ch = curl_init();
  // curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibnob/capacitacion/ws-inscribiralumno.php"); //PRUEBA
  curl_setopt($ch, CURLOPT_URL,$direccion."capacitacion/ws-inscribiralumno.php");
  curl_setopt($ch, CURLOPT_POST, TRUE);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $remote_server_output = curl_exec ($ch);
  curl_close ($ch);
  return json_decode($remote_server_output, true);
}

function nro_correlativo_facturas($cod_sucursal){
  $fecha_actual=date('Y-m-d');
  $dbh = new Conexion();   
   $sql="SELECT codigo from dosificaciones_facturas where cod_estado=1 and cod_sucursal=$cod_sucursal and fecha_limite_emision>='$fecha_actual'";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   $cod_dosificacion=0; $nroCorrelativo=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $cod_dosificacion=$row['codigo'];
  }
  if($cod_dosificacion>0){
    $sqlFac="SELECT IFNULL(f.nro_factura+1,1)as correlativo from facturas_venta f where f.cod_sucursal='$cod_sucursal' and f.cod_estadofactura<>4 and cod_dosificacionfactura=$cod_dosificacion order by f.codigo desc LIMIT 1";
    $stmtFac = $dbh->prepare($sqlFac);
    $stmtFac->execute();
    $nroCorrelativo==null;
    while ($row = $stmtFac->fetch(PDO::FETCH_ASSOC)) {    
     $nroCorrelativo=$row['correlativo'];     
    }
    if($nroCorrelativo==null || $nroCorrelativo=='')$nroCorrelativo=1; 
  }
  return($nroCorrelativo);
}
function verifica_modulosPagados($IdCurso,$ci_estudiante){
  $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
  $sIde = "ifinanciero";
  $sKey = "ce94a8dabdf0b112eafa27a5aa475751";  
  // OBTENER MODULOS PAGADOS x CURSO
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
          "accion"=>"ObtenerModulosPagados", 
          "Identificacion"=>$ci_estudiante, //7666922 ci del alumno
          "IdCurso"=>$IdCurso); //1565
  $parametros=json_encode($parametros);
  $ch = curl_init();
  // curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibnob/capacitacion/ws-inscribiralumno.php"); //PRUEBA
  curl_setopt($ch, CURLOPT_URL,$direccion."capacitacion/ws-inscribiralumno.php");
  curl_setopt($ch, CURLOPT_POST, TRUE);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $remote_server_output = curl_exec ($ch);
  curl_close ($ch);
  return json_decode($remote_server_output);  
}

function obtenerListaCuentasPlantillasCostoFijo($codigo){
   $dbh = new Conexion();
   $sql="(SELECT DISTINCT 1 as tipo,p.cod_cuenta,pl.numero,pl.nombre, p.cod_partidapresupuestaria from partidaspresupuestarias_cuentas p join plan_cuentas pl on p.cod_cuenta=pl.codigo where p.cod_partidapresupuestaria in (
                     (select DISTINCT pgcd.cod_partidapresupuestaria from plantillas_grupocostodetalle pgcd 
                     join plantillas_gruposcosto pgc on pgcd.cod_plantillagrupocosto=pgc.codigo 
                     join plantillas_costo pc on pgc.cod_plantillacosto=pc.codigo
                     where pc.codigo=$codigo and pgc.cod_tipocosto=1 and pgcd.tipo_calculo=1)) order by p.cod_partidapresupuestaria,p.cod_cuenta)                  
UNION                        
(SELECT DISTINCT 2 as tipo,p.cod_cuenta,pl.numero,pl.nombre, p.cod_partidapresupuestaria from partidaspresupuestarias_cuentas p join plan_cuentas pl on p.cod_cuenta=pl.codigo where p.cod_partidapresupuestaria in (
                     (select DISTINCT pgcd.cod_partidapresupuestaria from plantillas_grupocostodetalle pgcd 
                     join plantillas_gruposcosto pgc on pgcd.cod_plantillagrupocosto=pgc.codigo 
                     join plantillas_costo pc on pgc.cod_plantillacosto=pc.codigo
                     where pc.codigo=$codigo and pgc.cod_tipocosto=1 and pgcd.tipo_calculo=2)) order by p.cod_partidapresupuestaria,p.cod_cuenta)";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerListaCuentasPlantillasCostoFijoManual($cuenta,$partida,$codigo){
   $dbh = new Conexion();
   $sql="SELECT cod_partidapresupuestaria,cod_cuenta,monto_unitario FROM `plantillas_servicios_detalle` WHERE cod_partidapresupuestaria=$partida and cod_plantillacosto=$codigo and cod_cuenta=$cuenta;";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['monto_unitario'];
  }
  return $valor;
}
function obtenerListaCuentasPlantillasCostoFijoServicioManual($cuenta,$partida,$codigo){
   $dbh = new Conexion();
   $sql="SELECT cod_partidapresupuestaria,cod_cuenta,monto_unitario FROM `plantillas_servicios_detalle` WHERE cod_partidapresupuestaria=$partida and cod_plantillatcp=$codigo and cod_cuenta=$cuenta;";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['monto_unitario'];
  }
  return $valor;
}

function obtenerListaCuentasPlantillasCostoFijoServicio($codigo){
   $dbh = new Conexion();
   $sql="(SELECT DISTINCT 1 as tipo,p.cod_cuenta,pl.numero,pl.nombre, p.cod_partidapresupuestaria from partidaspresupuestarias_cuentas p join plan_cuentas pl on p.cod_cuenta=pl.codigo where p.cod_partidapresupuestaria in (
                     (select DISTINCT pgcd.cod_partidapresupuestaria from plantillas_gruposerviciodetalle pgcd 
                     join plantillas_gruposervicio pgc on pgcd.cod_plantillagruposervicio=pgc.codigo 
                     join plantillas_servicios pc on pgc.cod_plantillaservicio=pc.codigo
                     where pc.codigo=3 and pgc.cod_tiposervicio=1 and pgcd.tipo_calculo=1)) order by p.cod_partidapresupuestaria,p.cod_cuenta)                     
UNION
(SELECT DISTINCT 2 as tipo,p.cod_cuenta,pl.numero,pl.nombre, p.cod_partidapresupuestaria from partidaspresupuestarias_cuentas p join plan_cuentas pl on p.cod_cuenta=pl.codigo where p.cod_partidapresupuestaria in (
                     (select DISTINCT pgcd.cod_partidapresupuestaria from plantillas_gruposerviciodetalle pgcd 
                     join plantillas_gruposervicio pgc on pgcd.cod_plantillagruposervicio=pgc.codigo 
                     join plantillas_servicios pc on pgc.cod_plantillaservicio=pc.codigo
                     where pc.codigo=$codigo and pgc.cod_tiposervicio=1 and pgcd.tipo_calculo=2)) order by p.cod_partidapresupuestaria,p.cod_cuenta) 
";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerFechaSimulacionCosto($codigo){
   $dbh = new Conexion();
   $valor=0;
   $sql="SELECT fecha from simulaciones_costos p where p.codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['fecha'];
  }
  return $valor;
}
function verificar_codComprobante_cajaChica($codigo_cobt,$codigo_detalle){
  $dbh = new Conexion();  
  $sw=false;
  $sql="SELECT codigo from caja_chicareembolsos where cod_comprobante=$codigo_cobt and cod_comprobante_detalle=$codigo_detalle and cod_estadoreferencial=1";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sw=true;
  }
  return $sw;
}

function verificarArchivoAdjuntoExistente($tipo,$padre,$objeto,$codArchivo){
  $sqlObjeto="";
  if($objeto>0){
    $sqlObjeto="and cod_padre=$objeto";
  }
   $dbh = new Conexion();
   $sql="SELECT * FROM archivos_adjuntos WHERE cod_tipoarchivo=$codArchivo and cod_tipopadre=$tipo and cod_objeto=$padre $sqlObjeto";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   $valor=0;$descripcion="";$url="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor++;
      $descripcion=$row['descripcion'];
      $url=$row['direccion_archivo'];
  }
  return array($valor,$descripcion,$url);
}
function obtenerCodCuentaTipoPago($codigo){  
  $dbh = new Conexion();
  $sql="SELECT cod_cuenta from tipos_pago_contabilizacion where cod_tipopago=$codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $valor=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {     
    $valor=$row['cod_cuenta'];      
  }
  return $valor;
}
function obtenerCodCuentaArea($codigo){
  $dbh = new Conexion();
   $sql="SELECT cod_cuenta_ingreso from areas where codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {     
      $valor=$row['cod_cuenta_ingreso'];      
  }
  return $valor; 
}
function obtenerDiasAuditorSimulacionServicio($codigo){
  $dbh = new Conexion();
  $valor=0;
  $sql="SELECT dias from simulaciones_servicios_auditores p where p.codigo=$codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $valor=$row['dias'];
  }
  return (float)$valor;
}
function obtenerEntradaSimulacionServicio($codigo){
  $dbh = new Conexion();
  $valor=0;
  $sql="SELECT entrada from simulaciones_servicios p where p.codigo=$codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $valor=$row['entrada'];
  }
  return $valor;
}

function obtenerAuditoresSimulacionPorAnio($codigo,$anio){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT * from simulaciones_servicios_auditores where cod_simulacionservicio=$codigo and cod_anio=$anio";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function obtenerListaNormasIbnorca(){
  $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
  $sIde = "monitoreo"; // De acuerdo al sistema
  $sKey = "837b8d9aa8bb73d773f5ef3d160c9b17"; // llave de acuerdo al sistema
  /*Datos de Normas*/
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "TipoLista"=>"Todos"); //Lista todas las normas
    $parametros=json_encode($parametros);
    // abrimos la sesión cURL
    $ch = curl_init();
    // definimos la URL a la que hacemos la petición
    curl_setopt($ch, CURLOPT_URL,$direccion."catalogo/ws-catalogo-nal.php");     
    // indicamos el tipo de petición: POST
    curl_setopt($ch, CURLOPT_POST, TRUE);
    // definimos cada uno de los parámetros
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    // recibimos la respuesta y la guardamos en una variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    // cerramos la sesión cURL
    curl_close ($ch);  
    return json_decode($remote_server_output);       
}
function obtenerCodigoNorma(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from normas c");
   $stmt->execute();
   $codigoNorma=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoNorma=$row['codigo'];
   }
   return($codigoNorma);
}

function verificarMontoPresupuestadoSolicitadoSR($codigo){
  $dbh = new Conexion();
   $valor=0;
   $sql="SELECT sum(importe_presupuesto) as presupuesto,sum(importe) as solicitado FROM solicitud_recursosdetalle where cod_solicitudrecurso=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if($row['presupuesto']<$row['solicitado']){
      $valor=1;
    }
  }
  return $valor;
}

function obtenerPagoProveedorDetalle($codigo){
   $dbh = new Conexion();
   $sql="";
   $sql="SELECT sd.*,pc.numero,pc.nombre,pp.monto as pago,tp.nombre as tipo_pago 
from solicitud_recursosdetalle sd 
join plan_cuentas pc on sd.cod_plancuenta=pc.codigo 
join pagos_proveedoresdetalle pp on pp.cod_solicitudrecursosdetalle=sd.codigo
join tipos_pagoproveedor tp on tp.codigo=pp.cod_tipopagoproveedor
where pp.cod_pagoproveedor=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   return $stmt;
}
function listaLibretasBancarias(){
  $dbh = new Conexion();
  $sql="SELECT p.nombre as banco,dc.* 
FROM libretas_bancarias dc join bancos p on dc.cod_banco=p.codigo
WHERE dc.cod_estadoreferencial=1;";
$stmt = $dbh->prepare($sql);
  $stmt->execute();
  return $stmt;
}

function obtenerCodigoRegistroLibreta(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from libretas_bancariasregistro c");
   $stmt->execute();
   $codigo=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigo=$row['codigo'];
   }
   return($codigo);
}

function insertarCabeceraComprobante($codComprobante,$codEmpresa,$cod_uo_solicitud,$codAnio,$codMoneda,$codEstadoComprobante,$tipoComprobante,$fechaActual,$numeroComprobante,$concepto_contabilizacion,$globalUser){
   $dbh = new Conexion();
   $sqlInsertCabecera="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa,created_by,modified_by) values ('$codComprobante','$codEmpresa','$cod_uo_solicitud','$codAnio','$codMoneda','$codEstadoComprobante','$tipoComprobante','$fechaActual','$numeroComprobante','$concepto_contabilizacion','$globalUser','$globalUser')";
    $stmtInsertCab = $dbh->prepare($sqlInsertCabecera);
    $flagSuccess=$stmtInsertCab->execute();
   return $flagSuccess;
}
function insertarDetalleComprobante($codComprobante,$cod_cuenta,$cod_cuentaauxiliar,$cod_uo_solicitud,$cod_area_solicitud,$monto_debe,$monto_haber,$descripcion,$ordenDetalle){
  $dbh = new Conexion();
  $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','$cod_cuentaauxiliar','$cod_uo_solicitud','$cod_area_solicitud','$monto_debe','$monto_haber','$descripcion','$ordenDetalle')";
  $stmtInsertDet = $dbh->prepare($sqlInsertDet);
  $flagSuccessDet=$stmtInsertDet->execute();
  return $flagSuccessDet;
}
function verificamos_cuentas_tipos_pagos(){
  $dbh = new Conexion();
  $stmtVerif_tipopago = $dbh->prepare("SELECT (select c.cod_cuenta from tipos_pago_contabilizacion c where c.cod_tipopago=t.codigo) as cuenta from tipos_pago t where t.cod_estadoreferencial=1");
  $stmtVerif_tipopago->execute();
  $cont_tipopago=0;
  while ($row = $stmtVerif_tipopago->fetch())     
  {
    $cod_cuenta=$row['cuenta'];
    if($cod_cuenta==null){
        $cont_tipopago++;
    }
  }
  return $cont_tipopago;
}
function verificamos_cuentas_areas(){
  $dbh = new Conexion();
  $stmtVerif_area = $dbh->prepare("SELECT cod_cuenta_ingreso from areas a where a.cod_estado=1 and areas_ingreso=1");
  $stmtVerif_area->execute();
  $cont_areas=0;
  while ($row = $stmtVerif_area->fetch())    
  {
      $cod_cuenta=$row['cod_cuenta_ingreso'];
      if($cod_cuenta==null){
          $cont_areas++;
      }
  }
  return $cont_areas;
}
function verificamosFacturaDuplicada($codigo){
  $dbh = new Conexion();
  $stmtVerif = $dbh->prepare("SELECT codigo FROM facturas_venta where cod_solicitudfacturacion=$codigo and cod_estadofactura=1");
  $stmtVerif->execute();
  $resultVerif = $stmtVerif->fetch();    
  $codigo_facturacion = $resultVerif['codigo'];
  return $codigo_facturacion;
}
function verificamosFacturaGenerada($codigo){
  $dbh = new Conexion();
  $stmtVerif = $dbh->prepare("SELECT codigo FROM facturas_venta where cod_solicitudfacturacion=$codigo");
  $stmtVerif->execute();
  $valor=0;
  while ($row = $stmtVerif->fetch())    
  {
      $valor=$row['codigo'];      
  }
  return $valor;  
}


function obtener_dato_dosificacion($cod_dosificacion){
  $dbh = new Conexion();
  $stmtVerif = $dbh->prepare("SELECT leyenda from dosificaciones_facturas where codigo=$cod_dosificacion");
  $stmtVerif->execute();
  $resultVerif = $stmtVerif->fetch();    
  $valor = $resultVerif['leyenda'];
  return $valor;
}

function obtenerObtenerLibretaBancaria(){
  $codigo=0;
  //$direccion='http://127.0.0.1/ifinanciero/wsifin/';
  // $direccion='http://200.105.199.164:8008/ifinanciero/wsifin/';
  $direccion=obtenerValorConfiguracion(56);//direccion del servicio web ifinanciero
  $sIde = "libBan";
  $sKey = "89i6u32v7xda12jf96jgi30lh";
  //PARAMETROS PARA LA OBTENCION DE ARRAY LIBRETA
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ObtenerLibretaBancaria","idLibreta"=>$codigo); 
  $parametros=json_encode($parametros);
  // abrimos la sesión cURL
  $ch = curl_init();
  // definimos la URL a la que hacemos la petición
  curl_setopt($ch, CURLOPT_URL,$direccion."ws_obtener_libreta_bancaria.php"); 
  // indicamos el tipo de petición: POST
  curl_setopt($ch, CURLOPT_POST, TRUE);
  // definimos cada uno de los parámetros
  curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
  // recibimos la respuesta y la guardamos en una variable
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $remote_server_output = curl_exec ($ch);
  // cerramos la sesión cURL
  curl_close ($ch);
  return json_decode($remote_server_output);
  // imprimir en formato JSON
  // header('Content-type: application/json');   
  // print_r($remote_server_output);
}

function verificarFechaMaxDetalleLibreta($fecha,$codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT * FROM libretas_bancariasdetalle where cod_libretabancaria=:codigo and fecha_hora >= :fecha and cod_estadoreferencial!=2");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->bindParam(':fecha',$fecha);
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor++;
   }
   return($valor);
}


function obtenerDatosFacturaVenta($codigo){
  $dbh = new Conexion();
  $stmtVerif = $dbh->prepare("SELECT * FROM facturas_venta where codigo=$codigo");
  $stmtVerif->execute();
  $resultVerif = $stmtVerif->fetch();    
  $fecha = $resultVerif['fecha_factura'];
  $numero = $resultVerif['nro_factura'];
  $nit = $resultVerif['nit'];
  $razon_social = $resultVerif['razon_social'];
  $detalle = $resultVerif['observaciones'];
  $monto = $resultVerif['importe'];
  return array($fecha,$numero,$nit,$razon_social,$detalle,$monto);
  }

function nameTipoPagoSolFac($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM tipos_pago where codigo=$codigo");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['nombre'];
   }
   return($valor);
}

function obtenerCodigoPagoLote(){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from pagos_lotes c");
   $stmt->execute();
   $codigo=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigo=$row['codigo'];
   }
   return($codigo);
}

function obtenerDetalleSolicitudFacturacion($codigo){
  $stmtDetalleSol = $dbh->prepare("SELECT cantidad,precio,descripcion_alterna from solicitudes_facturaciondetalle where cod_solicitudfacturacion=$codigo_facturacion");
  $stmtDetalleSol->execute();
  $stmtDetalleSol->bindColumn('cantidad', $cantidad);  
  $stmtDetalleSol->bindColumn('precio', $precio);     
  $stmtDetalleSol->bindColumn('descripcion_alterna', $descripcion_alterna);  
  while ($row_det = $stmtDetalleSol->fetch()){
    $precio_natural=$precio/$cantidad;
    $concepto_contabilizacion.=$descripcion_alterna." / ".trim($cadenaFacturas,',').",".trim($cadenaFacturasM,",")." / ".$razon_social."<br>\n";
    $concepto_contabilizacion.="Cantidad: ".$cantidad." * ".formatNumberDec($precio_natural)." = ".formatNumberDec($precio)."<br>\n";
  }
  return $concepto_contabilizacion;
}
function obtenerDetalleFactura($codigo){
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT nombre FROM tipos_pago where codigo=$codigo");
  $stmt->execute();
  $valor=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $valor=$row['nombre'];
  }
  return($valor); 
}

function obtenerCorreosInstanciaEnvio($codigo){
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT p.email_empresa,concat(p.primer_nombre,' ',p.otros_nombres,' ',p.paterno,' ',p.materno) as personal from instancias_envios_correos_detalle i join personal p on p.codigo=i.cod_personal where i.cod_instancia_envio=$codigo and p.email_empresa IS NOT NULL and i.cod_estadoreferencial=1");
  $stmt->execute();
  $datos=[];$datos2=[];
  $index=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $datos[$index]=$row['email_empresa'];
    $datos2[$index]=$row['personal'];
    $index++;
  }

  return array($datos,$datos2); 
}

function nameLotesPago($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM pagos_lotes where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre'];
   }
   return($nombreX);
}

function obtenerCodigoExternoCurso($codigo){
  $dbh = new ConexionIBNORCA();
  $stmt = $dbh->prepare("SELECT codigo_curso($codigo) as codigo");
  $stmt->execute();
  $valor=" - ";
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $valor=$row['codigo'];
  }
  return($valor);    
}

function obtenermontoestudianteGrupal($IdCurso,$ci_estudiante,$codCS){
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT SUM(sfd.precio) as precio from solicitudes_facturaciondetalle sfd, solicitudes_facturacion sf where sf.codigo=sfd.cod_solicitudfacturacion and sf.cod_estadosolicitudfacturacion<>5 and sfd.cod_curso=$IdCurso and sfd.ci_estudiante=$ci_estudiante and sfd.cod_claservicio=$codCS and sf.cod_estadosolicitudfacturacion!=2");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['precio'];
   }
   return($valor);
}
function obtenerDescripcionestudianteGrupal($IdCurso,$ci_estudiante,$codCS){
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT sfd.descripcion_alterna from solicitudes_facturaciondetalle sfd where sfd.cod_curso=$IdCurso and sfd.ci_estudiante=$ci_estudiante and sfd.cod_claservicio=$codCS");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['descripcion_alterna'];
   }
   return($valor);
}
function obtenerEstadoLibretaBancaria($cod_libreta){
  $dbh = new Conexion();
  $valor="";
  $stmtLibretasDet = $dbh->prepare("SELECT lbd.cod_estado from  libretas_bancariasdetalle lbd where  lbd.codigo=$cod_libreta");
  $stmtLibretasDet->execute();
  $resultLibretaDet = $stmtLibretasDet->fetch();   
  $valor = $resultLibretaDet['cod_estado'];    
  return($valor);
}
function obtenerCuentaLibretaBancaria($cod_libreta){
  $dbh = new Conexion();
  $valor="";
  $stmtLibretasDet = $dbh->prepare("SELECT lb.cod_cuenta from  libretas_bancariasdetalle lbd,libretas_bancarias lb where lb.codigo=lbd.cod_libretabancaria and lbd.codigo=$cod_libreta");
  $stmtLibretasDet->execute();
  $resultLibretaDet = $stmtLibretasDet->fetch();
  $valor= $resultLibretaDet['cod_cuenta'];
  return($valor);
}
function obtenerContraCuentaLibretaBancaria($cod_libreta){
  $dbh = new Conexion();
  $valor="";
  $stmtLibretasDet = $dbh->prepare("SELECT lb.cod_contracuenta from  libretas_bancariasdetalle lbd,libretas_bancarias lb where lb.codigo=lbd.cod_libretabancaria and lbd.codigo=$cod_libreta");
  $stmtLibretasDet->execute();
  $resultLibretaDet = $stmtLibretasDet->fetch();  
  $valor= $resultLibretaDet['cod_contracuenta'];
  return($valor);
}
function contarFacturasLibretaBancaria($codigo){
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT codigo From libretas_bancariasdetalle_facturas lbf, facturas_venta f where lbf.cod_facturaventa=f.codigo and f.cod_estadofactura!=2 and lbf.cod_libretabancariadetalle=$codigo");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor++;
   }
   return($valor);
}

function cuentaLibreta($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cod_cuenta FROM libretas_bancarias where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['cod_cuenta'];
   }
   return($nombreX);
}
function contraCuentaLibreta($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cod_contracuenta FROM libretas_bancarias where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['cod_contracuenta'];
   }
   return($nombreX);
}

function obtenerCodigoDetallePorpuestaServicio($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cod_simulacionserviciodetalle from simulaciones_ssd_ssa where codigo=$codigo");
   $stmt->execute();
   $nombreX=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['cod_simulacionserviciodetalle'];
   }
   return($nombreX);
}

function obtenerCostoVariableHonorariosSolicitadoPropuestaTCPTCS($codSimulacionServX,$anio,$codDetalle){
   $existeItem=0;
   $detalle=obtenerDetalleSolicitudSimulacionCuentaPlantillaServicioCompletoAnios($codSimulacionServX,$anio);
   while ($row = $detalle->fetch(PDO::FETCH_ASSOC)) {
    $codigo_fila=explode("###",$row['codigo_detalle']);
    $cod_plantilladetalle=$codigo_fila[0];
    if($codigo_fila[1]=="DET-SIM"){
         $solicitudDetalle=obtenerSolicitudRecursosDetallePlantilla(false,$cod_plantilladetalle);
    }else{
         $solicitudDetalle=null;
    }
    if($solicitudDetalle!=null){
      $existe=0;
     while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
      $existe++;
     } 
     if($existe>0){
      if(obtenerCodigoDetallePorpuestaServicio($cod_plantilladetalle)==$codDetalle){
        $existeItem=1; 
      }
     }
    }
  }
  return $existeItem;
}

function obtenerCostoVariableSolicitadoPropuestaTCPTCS($codSimulacionServX,$codigoItemCons,$valorItem){
   $existeItem=0;$codigoItem=0;
   $detalle=obtenerDetalleSolicitudSimulacionCuentaPlantillaServicioCompleto($codSimulacionServX,0);
   while ($row = $detalle->fetch(PDO::FETCH_ASSOC)) {
    $codigo_fila=explode("###",$row['codigo_detalle']);
    $cod_plantilladetalle=$codigo_fila[0];
    if($valorItem==1){
       if($codigo_fila[1]=="DET-SIM"){
         $solicitudDetalle=obtenerSolicitudRecursosDetallePlantilla(false,$cod_plantilladetalle);
       }else{
         $solicitudDetalle=null;
       }
    }else{
       if($codigo_fila[1]=="DET-SIM"){
         $solicitudDetalle=null;
       }else{
        $solicitudDetalle=obtenerSolicitudRecursosDetallePlantillaAud(false,$cod_plantilladetalle);
       }
    }

    $entro=0;
    if($solicitudDetalle!=null){
     while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
      $entro=1;
      $codigoItem=$cod_plantilladetalle;
     } 
    }

    if($entro==1){
      if($codigoItemCons==$codigoItem){
        $existeItem=1;
      }
     }
  }
  return $existeItem;
}

function obtenerServicioSolicitadoPropuestaTCPTCS($codigo,$detalle){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT sd.* from solicitudes_facturaciondetalle sd join solicitudes_facturacion s on s.codigo=sd.cod_solicitudfacturacion  where s.cod_simulacion_servicio=$codigo and sd.cod_claservicio=$detalle");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor++;
   }
   return($valor);
}

function obtenerNombreDepositoNoFacturado($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT l.nombre,b.abreviatura as banco from libretas_bancarias l join bancos b on b.codigo=l.cod_banco where l.cod_estadoreferencial=1 and l.codigo=$codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX=$row['nombre']." - ".$row['banco'];
   }
   return($nombreX);
}

function obtenerDatosContactoSolFac($codigo){
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT CONCAT_WS(' ',nombre,paterno,materno)as nombre_cliente, telefono,correo from clientes_contactos where codigo=$codigo");
  $stmt->execute();
  $valor='';
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $valor=$row['nombre_cliente'].'#####'.$row['telefono'].'#####'.$row['correo'];
  }
  return($valor);
}
function obtenerCorreosCliente($cod_cliente){
  $dbh = new Conexion();
  $sqlCorreo="SELECT correo from clientes_contactos where correo!='null' and cod_cliente=$cod_cliente";
  // echo $sqlCorreo;
  $stmtCorreos = $dbh->prepare($sqlCorreo);
  $stmtCorreos->execute();
  $stmtCorreos->bindColumn('correo', $correo);
  $correos_string= '';                            
  while ($row = $stmtCorreos->fetch(PDO::FETCH_BOUND)) {
    if($correos_string!=null || $correos_string!='' || $correos_string!=' '){
      $correos_string.=$correo.',';
    }
  }
  return($correos_string);
}
function obtenerCorreoEstudiante($nit){
  $dbh = new Conexion();
  $sqlCorreo2="SELECT c.clCorreo as correo from dbcliente.cliente_persona_empresa c where c.clCorreo IS NOT NULL and c.clIdentificacion='$nit'";
  $stmtCorreos2 = $dbh->prepare($sqlCorreo2);
  $stmtCorreos2->execute();
  $stmtCorreos2->bindColumn('correo', $correo2);

  $sqlCorreo="SELECT c.clCorreo as correo from dbcliente.cliente_persona_empresa c where c.clCorreo IS NOT NULL and c.clNit='$nit'";
  $stmtCorreos = $dbh->prepare($sqlCorreo);
  $stmtCorreos->execute();
  $stmtCorreos->bindColumn('correo', $correo);
  $correos_string="";                           
  while ($row = $stmtCorreos->fetch(PDO::FETCH_BOUND)) {
    if($correo!=null || $correo!='' || $correo!=' '){
      $correos_string=$correo;
    }
  }
  if($correos_string==""){
   while ($row2 = $stmtCorreos2->fetch(PDO::FETCH_BOUND)) {
    if($correo2!=null || $correo2!='' || $correo2!=' '){
      $correos_string=$correo2;
    }
   }
  }
  return $correos_string;
}
function obtenerTipoSolicitud($codigo){
  $dbh = new Conexion();
  $sql="SELECT s.tipo_solicitud from solicitudes_facturacion s where s.codigo=$codigo";
  // echo $sql;
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $stmt->bindColumn('tipo_solicitud', $tipo_solicitud);
  $valor= '';
  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    $valor=$tipo_solicitud;
  }
  return($valor);
}
function obtenerNroFactura($codigo){
  $dbh = new Conexion();
  $sql="SELECT s.nro_factura from facturas_venta s where s.codigo=$codigo";
  // echo $sql;
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $stmt->bindColumn('nro_factura', $nro_factura);
  $valor= '';
  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    $valor=$nro_factura;
  }
  return($valor);
}
function obtnerFormasPago($codigo){
  $dbh = new Conexion();
  $sql="SELECT cod_tipopago from solicitudes_facturacion_tipospago where cod_solicitudfacturacion=$codigo";
  // echo $sql;
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $stmt->bindColumn('cod_tipopago', $cod_tipopago);
  $correos_string= '';                            
  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
      $nombre_tipopago=nameTipoPagoSolFac($cod_tipopago);
      $correos_string.=$nombre_tipopago.',<br>';
  }
  $correos_string=trim($correos_string,',<br>');
  return($correos_string);
}
function obtnerFormasPago_factura($codigo){
  $dbh = new Conexion();
  $sql="SELECT cod_tipopago from solicitudes_facturacion_tipospago where cod_solicitudfacturacion=$codigo";
  // echo $sql;
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $stmt->bindColumn('cod_tipopago', $cod_tipopago);
  $correos_string= '';                            
  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
      $nombre_tipopago=nameTipoPagoSolFac($cod_tipopago);
      $correos_string.=$nombre_tipopago.',';
  }
  $correos_string=trim($correos_string,',');
  return($correos_string);
}

function obtnerFormasPago_codigo($cod_tipopago_aux,$codigo_facturacion){
  $dbh = new Conexion();
  $sql="SELECT cod_tipopago from solicitudes_facturacion_tipospago where cod_solicitudfacturacion=$codigo_facturacion and cod_tipopago=$cod_tipopago_aux";
  // echo $sql;
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $stmt->bindColumn('cod_tipopago', $cod_tipopago);
  $valor=0;
  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {    
      $valor=$cod_tipopago_aux;
  }  
  return($valor);
}

function obtenerMontoporcentaje_formapago($cod_tipopago_dep,$codigo_facturacion){
  $dbh = new Conexion();
  $sql="SELECT monto from solicitudes_facturacion_tipospago where cod_solicitudfacturacion=$codigo_facturacion and cod_tipopago=$cod_tipopago_dep";
  // echo $sql;
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $stmt->bindColumn('monto', $monto);
  $valor=0;
  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {    
      $valor=$monto;
  }  
  return($valor);
}
function obtenerNombreEstudiante($ci_estudiante){

  $dbhIBNO = new ConexionIBNORCA();
  //datos del estudiante y el curso que se encuentra
  $sqlIBNORCA="SELECT concat(cpe.clPaterno,' ',cpe.clMaterno,' ',cpe.clNombreRazon)as nombreAlumno 
  FROM dbcliente.cliente_persona_empresa cpe
  where cpe.clIdentificacion=$ci_estudiante";
  // echo $sqlIBNORCA;
  $stmtIbno = $dbhIBNO->prepare($sqlIBNORCA);
  $stmtIbno->execute();
  $resultSimu = $stmtIbno->fetch();
  $valor = $resultSimu['nombreAlumno'];
  return($valor);
}
function obtener_codigo_modulo_IBnorca($cod_modulo){
  $dbhIBNO = new ConexionIBNORCA();
  //datos del estudiante y el curso que se encuentra
  $sqlIBNORCA="SELECT concat(c1.abrev,'-',c.abrev,'-',c2.abrev,'-','G',p.grupo,'-','M',NroModulo,'-',c3.abrev) as programa 
  from modulos m
  inner join programas_cursos p on m.idcurso=p.`IdCurso`
  inner join clasificador c on p.idprograma=c.idclasificador
  inner join  clasificador c1 on p.`IdOficina`=c1.`IdClasificador`
  inner join  clasificador c2 on p.`IdTipo`=c2.`IdClasificador`
  inner join  clasificador c3 on p.`IdGestion`=c3.`IdClasificador` 
  where m.idmodulo=$cod_modulo";
  // echo $sqlIBNORCA;
  $stmtIbno = $dbhIBNO->prepare($sqlIBNORCA);
  $stmtIbno->execute();
  $resultSimu = $stmtIbno->fetch();
  $valor = $resultSimu['programa'];
  return($valor);
}
function obtener_nombreestado_factura($codigo){
  $dbh = new Conexion();
  $sql="SELECT t.nombre from estados_factura t where t.codigo=$codigo";  
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $stmt->bindColumn('nombre', $nombre);
  $valor=0;
  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {    
      $valor=$nombre;
  }  
  return($valor);
}
function obtener_observacion_factura($codigo){
  $dbh = new Conexion();
  $sql="SELECT obs_devolucion from solicitudes_facturacion where codigo=$codigo";  
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $stmt->bindColumn('obs_devolucion', $obs_devolucion);
  $valor=0;
  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {    
      $valor=$obs_devolucion;
  }  
  return($valor);
}
function obtener_string_observaciones($obs_devolucion,$observaciones,$observaciones_2){
  $string_obs="";
  if($obs_devolucion!=null){
    $string_obs.="<span style='color:#ff0000;'>".$obs_devolucion."</span>,";}
  if($observaciones!=null) {$string_obs.='<small>'.$observaciones.'</small>';}
  if($observaciones_2!=null) {$string_obs.=",<span style='color:#431490;'><small>".$observaciones_2."</small></span>";}
  return $string_obs;
}

function sumatotaldetallefactura($cod_factura){
  $dbh = new Conexion();
  $sql="SELECT sf.precio,sf.descuento_bob,sf.cantidad from facturas_ventadetalle sf where sf.cod_facturaventa=$cod_factura";  
  $stmt = $dbh->prepare($sql);
  $stmt->execute();  
  $stmt->bindColumn('precio', $precio);
  $stmt->bindColumn('descuento_bob', $descuento_bob);
  $stmt->bindColumn('cantidad', $cantidad);
  $suma_total=0;
  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    $precio=$precio*$cantidad-$descuento_bob;    
    $suma_total+=$precio;
  }  
  return($suma_total);
}
 function obtenerCodigoDetalleSolFac($codigo){
    $dbh = new Conexion();
    $sql="SELECT codigo FROM solicitudes_facturaciondetalle where cod_solicitudfacturacion=$codigo";  
    $stmt = $dbh->prepare($sql);
    $stmt->execute();  
    $stmt->bindColumn('codigo', $codigo);  
    $array_cod = array();
    $contador=0;
    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
        $array_cod[$contador]=$codigo;
        $contador++;
    }  
    return($array_cod);
  }
  function verificar_pago_servicios_tcp_solfac($idServicio,$idClaServicio){
    $idTipoObjeto=211;
    $dbhIBNO = new ConexionIBNORCA();
    $sql="SELECT sp.idServicio, sp.idclaServicio, sp.cantidad, (( sp.PrecioUnitario )-(( sp.PrecioUnitario )*( co.descuento / 100 ))) AS preciounitario,
    cs.Descripcion,f.cantidad_f, f.monto_f 
    FROM
      `serviciopresupuesto` sp
      LEFT JOIN (
        SELECT sf.`IdDetalleServicioCurso` AS IdClaServicio,
          SUM( sf.cantidad ) AS cantidad_f,
          sum( sf.cantidad * sf.precio ) AS monto_f 
        FROM
          `vw_listasolfacturacion` sf 
        WHERE
          sf.idTipoObjeto = $idTipoObjeto 
          AND sf.idObjeto = $idServicio
          AND (ifnull( d_auxclasificador ( id_estadoobjeto ( 264, sf.idsolicitudfactura )), '' ) NOT IN ( 'N' )) 
        GROUP BY
          1 ) f ON sp.`IdClaServicio` = f.IdClaServicio
      INNER JOIN cotizaciones co ON sp.`IdCotizacion` = `co`.`IdCotizacion` 
      AND `id_estadoobjeto` ( 196, co.idCotizacion )= 198
      INNER JOIN claservicios cs ON sp.IdClaservicio = cs.idClaServicio 
    WHERE
      `sp`.`IdServicio` = $idServicio and sp.idClaServicio=$idClaServicio";    
    // echo $sql."<br><br>";
    $stmtIbno = $dbhIBNO->prepare($sql);
    $stmtIbno->execute();
    $stmtIbno->bindColumn('cantidad', $cantidad);  
    $stmtIbno->bindColumn('preciounitario', $preciounitario);
    $stmtIbno->bindColumn('cantidad_f', $cantidad_f);
    $stmtIbno->bindColumn('monto_f', $monto_f);
    $valor=0;
    while ($row = $stmtIbno->fetch(PDO::FETCH_BOUND)) {      
      // echo "aqou";
        $valor=$monto_f;
    }  
    return($valor);
  }
  function obtnerNombreComprimidoEstudiante($ci_estudiante){
    $dbh = new Conexion();
    $sql="SELECT concat(cpe.clPaterno,' ',cpe.clNombreRazon)as nombreAlumno
    FROM dbcliente.cliente_persona_empresa cpe 
    where cpe.clIdentificacion=$ci_estudiante";  
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $stmt->bindColumn('nombreAlumno', $nombreAlumno);
    $valor='';
    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {    
        $valor=$nombreAlumno;
    }  
    return($valor);

  }

  function obtenerCorreosEnviadosFactura($codigo){
    $dbh = new Conexion();
    $sql="SELECT correo from log_instancias_envios_correo where cod_factura=$codigo";  
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $stmt->bindColumn('correo', $correo);
    $valor=[];
    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
       array_push($valor,$correo);   
    }  
    return implode("\n *", $valor);
  }
  function obtenerCodUOSolFac($cod_solicitudfacturacion,$auxiliar){
    $dbh = new Conexion();
    $sql="SELECT $auxiliar from solicitudes_facturacion where codigo=$cod_solicitudfacturacion";  
    $stmt = $dbh->prepare($sql);
    $stmt->execute();    
    $result = $stmt->fetch();    
    $cod_libretabancariadetalle = $result[$auxiliar];
    return $cod_libretabancariadetalle;
  }

  function obtenerLibretaBancariaFacturaVenta($codigo){
    $dbh = new Conexion();
    $sql="SELECT cod_libretabancariadetalle from libretas_bancariasdetalle_facturas where cod_facturaventa in ($codigo) group by cod_libretabancariadetalle";
    // $sql="SELECT cod_libretabancariadetalle FROM facturas_venta where codigo=$codigo";
    $stmtVerif = $dbh->prepare($sql);
    $stmtVerif->execute();
    $codigos_libreta="";
    while ($row = $stmtVerif->fetch(PDO::FETCH_BOUND)) {
      $cod_libreta=$row['cod_libretabancariadetalle'];
      $codigos_libreta.=$cod_libreta.",";
    }
    $codigos_libreta=trim($codigos_libreta,",");
    return $codigos_libreta;
  }
  function obtenerGlosaLibretaBancariaDetalle($codigo){
    $dbh = new Conexion();
    $stmtVerif = $dbh->prepare("SELECT informacion_complementaria from libretas_bancariasdetalle where codigo in ($codigo)");
    $stmtVerif->execute();
    $informacion="";
    while ($row = $stmtVerif->fetch(PDO::FETCH_BOUND)) {
      $valor=$row['informacion_complementaria'];
      $informacion.=$valor.", ";
    }
    $informacion=trim($informacion,", ");
    return $informacion; 
  }
  function obtenerIdRolDeIbnorca($codigo){
    $dbh = new Conexion();
    $stmt = $dbh->prepare("SELECT IdRol FROM ibnorca.personarol WHERE IdPersona = '$codigo' and pordefecto=1 "); //and ibnorca.PersonaRol(IdPersona)>4
    $stmt->execute();
    $valor=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {    
        $valor=$row['IdRol'];
    }  
    return($valor);
  }

  function obtenerCantidadFacturasLibretaBancariaDetalle($codigo,$sqlFiltro2){
    $dbh = new Conexion();
    $stmtVerif = $dbh->prepare("SELECT (SELECT f.codigo from facturas_venta f where f.codigo=lf.cod_facturaventa and f.cod_estadofactura!=2 $sqlFiltro2)as codigo From libretas_bancariasdetalle_facturas lf where lf.cod_libretabancariadetalle=$codigo");
    $stmtVerif->execute();
    $contador=0;
    while ($row = $stmtVerif->fetch(PDO::FETCH_ASSOC)) {    
        $valor=$row['codigo'];
        if($valor!=null || $valor!=0 || $valor!=''){
          $contador++;
        }
    }
    return $contador;
  }
  function obtnerCadenaFacturas($codigo){
    $dbh = new Conexion();
    $sql="SELECT ld.cod_facturaventa from libretas_bancariasdetalle_facturas  ld where ld.cod_libretabancariadetalle=$codigo";    
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $cadena="";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {      
      $cadena.=$row['cod_facturaventa'].",";
    }     
    $cadena=trim($cadena,','); 

    return($cadena);
  }
  function verificarCodFactura($codigo){
    $dbh = new Conexion();
    $sql="SELECT ld.cod_facturaventa from libretas_bancariasdetalle_facturas  ld where ld.cod_libretabancariadetalle=$codigo";    
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $valor=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {      
      $valor=1;
    }         
    return($valor);
  }
  function verificar_cod_libretadetalle($codigo){
    $dbh = new Conexion();
    $sql="SELECT ld.cod_libretabancariadetalle from libretas_bancariasdetalle_facturas  ld where ld.cod_facturaventa=$codigo";    
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $valor=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {     
      $cod_x=$row['cod_libretabancariadetalle'];
      if($cod_x!=null || $cod_x!= '' || $cod_x!=0){
        $valor=$cod_x;
      }else $valor=0;
    }         
    return($valor);
  }

  function obtenerCodigoLibretaDetalleComprobante($codigo){
    $dbh = new Conexion();
    $stmt = $dbh->prepare("SELECT codigo FROM libretas_bancariasdetalle WHERE cod_comprobantedetalle = '$codigo'");
    $stmt->execute();
    $valor=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {    
        $valor=$row['codigo'];
    }  
    return($valor);
  }
  function obtenerDescripcionLibretaDetalleComprobante($codigo){
    $dbh = new Conexion();
    $stmt = $dbh->prepare("SELECT fecha_hora,CONCAT(descripcion,' Info: ',informacion_complementaria) as descripcion FROM libretas_bancariasdetalle WHERE cod_comprobantedetalle = '$codigo'");
    $stmt->execute();
    $valor="";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {    
        $valor=strftime('%d/%m/%Y',strtotime($row['fecha_hora']))." - ".$row['descripcion'];
    }  
    return($valor);
  }


function obtenerCodigoActividadProyecto($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cod_actividadproyecto from solicitud_recursosdetalle where codigo=$codigo");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cod_actividadproyecto'];
    }
   return($valor);
  }

  function obtnercontracuentaUnidad($codigo_uo){
    $dbh = new Conexion();
    $stmt = $dbh->prepare("SELECT cod_cuenta from  configuraciones_uo_cuenta where unidad=$codigo_uo");
    $stmt->execute();
    $valor=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {    
      $valor=$row['cod_cuenta'];
    }  
    return($valor);
  }

  function contador_facturas_cajachica($codigo){
    $dbh = new Conexion();
    $sql="SELECT codigo from facturas_detalle_cajachica where cod_cajachicadetalle=$codigo";    
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $valor=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {      
      $valor++;
    }         
    return($valor);
  }
  function cadena_facturas_cajachica($codigo){
    $dbh = new Conexion();
    $sql="SELECT nro_factura from facturas_detalle_cajachica where cod_cajachicadetalle=$codigo";    
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $cadena="";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {      
      $cadena.="F/".$row['nro_factura'].",";
    }     
    $cadena=trim($cadena,',');
    return($cadena);
  }
  function importe_total_facturas($codigo){
    $dbh = new Conexion();
    $sql="SELECT importe from facturas_detalle_cajachica where cod_cajachicadetalle=$codigo";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $valor=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {      
      $valor+=$row['importe'];
    }         
    return($valor);
  }
  function importe_total_gastos_directos($codigo){
    $dbh = new Conexion();
    $sql=" SELECT importe from detalle_cajachica_gastosdirectos where cod_cajachicadetalle=$codigo";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $valor=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {      
      $valor+=$row['importe'];
    }         
    return($valor);
  }
  function obtenerDistribucionCajachicaDetalle($codigo,$tipo){
    $dbh = new Conexion();    
    if($tipo==1){
      $sql="SELECT u.nombre,d.porcentaje from distribucion_gastos_caja_chica d  join unidades_organizacionales u on u.codigo=d.oficina_area where d.cod_cajachica_detalle=$codigo and d.tipo_distribucion=$tipo";
    }else{
      $sql="SELECT u.nombre,d.porcentaje from distribucion_gastos_caja_chica d  join areas u on u.codigo=d.oficina_area where d.cod_cajachica_detalle=$codigo and d.tipo_distribucion=$tipo";
    }
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return $stmt;
  }

  function ontener_porcentaje_distribucion_cajachica($codigo,$cod_uo_area,$tipo){
    $dbh = new Conexion();        
    $sql="SELECT d.porcentaje from distribucion_gastos_caja_chica d  where d.cod_cajachica_detalle=$codigo and d.tipo_distribucion=$tipo and d.oficina_area=$cod_uo_area";
    // echo $sql; 
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $valor=-1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['porcentaje'];
    }         
    return($valor);
  }
  function verificamos_distribucion_cajachica($codigo){
    $dbh = new Conexion();        
    $sql="SELECT tipo_distribucion from distribucion_gastos_caja_chica where cod_cajachica_detalle=$codigo GROUP BY tipo_distribucion";    
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $valor='0';
    $cont=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $cont++;
      $tipo_distribucion=$row['tipo_distribucion'];
      if($tipo_distribucion==1){  
        $valor="x OFICINA";
      }else{
        $valor="x AREA";
      }
    }         
    if($cont>1){
      $valor="x OFICINA y AREA";
    }
    return($valor);
  }
  function obtenerNombreEstadoSolFac($cod_estado){
    $dbh = new Conexion();        
    $sql="SELECT nombre from estados_solicitudfacturacion where codigo=$cod_estado";    
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $valor="-";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['nombre'];
    }         
    return($valor);
  }
  function obtenemosformaPagoSolfact($codigo){
    $dbh = new Conexion();        
    $sql="SELECT cod_tipopago from solicitudes_facturacion_tipospago where cod_solicitudfacturacion=$codigo limit 1";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $valor="-";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['cod_tipopago'];
    }         
    return($valor);
  }

function obtenerDatosDistribucionSolicitudFacturacion($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT sf.codigo,sf.cod_unidadorganizacional,sf.cod_area,
(SELECT porcentaje FROM solicitudes_facturacion_areas where cod_solicitudfacturacion=sf.codigo and cod_area=sf.cod_area) as porcentaje_area_origen,
(SELECT group_concat( distinct CONCAT (cod_area,'P',porcentaje)) FROM solicitudes_facturacion_areas where cod_solicitudfacturacion=sf.codigo group by cod_solicitudfacturacion) as areas_distribuidas,
(SELECT group_concat( distinct CONCAT (a.abreviatura,':',ROUND((SELECT SUM((sd.cantidad*sd.precio)-sd.descuento_bob) as importe_solicitado from solicitudes_facturaciondetalle sd where sd.cod_solicitudfacturacion=sf.codigo)*(sa.porcentaje/100),2),' Bs. (',sa.porcentaje,' %)')) FROM solicitudes_facturacion_areas sa join areas a on a.codigo=sa.cod_area where sa.cod_solicitudfacturacion=sf.codigo group by sa.cod_solicitudfacturacion) as areas_distribuidas_texto
 from solicitudes_facturacion sf  where sf.codigo=$codigo");
   $stmt->execute();
   $datos[0]="";$datos[1]="";$datos[2]="";$datos[3]="";$datos[4]="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $datos[0]=$row['cod_unidadorganizacional'];
      $datos[1]=$row['cod_area'];
      $datos[2]=$row['porcentaje_area_origen'];
      $datos[3]=$row['areas_distribuidas'];
      $datos[4]=$row['areas_distribuidas_texto'];
   }
   return($datos);
}

function insertar_facturas_compra($codComprobante,$ordenDetalle,$codigo_ccdetalle){
  $ordenDetalle--;
  //sacamos el codigo del detalle de comprobante insertado
  $dbh = new Conexion();
  $sqlDetCpte="SELECT codigo from comprobantes_detalle where cod_comprobante=$codComprobante and orden=$ordenDetalle";
  $stmtDetCpte = $dbh->prepare($sqlDetCpte);
  $stmtDetCpte->execute();
  $valor=0;
  while ($row = $stmtDetCpte->fetch(PDO::FETCH_ASSOC)) {      
    $codigoDetalle=$row['codigo'];
  }         
  //listamos todas las factuas de la caja chica detalle
  $dbh = new Conexion();
  $sql="SELECT nit,nro_factura,fecha,razon_social,importe,exento,nro_autorizacion,codigo_control,ice,tasa_cero from facturas_detalle_cajachica where cod_cajachicadetalle=$codigo_ccdetalle";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();    
  $stmt->bindColumn('nit', $nit);
  $stmt->bindColumn('nro_factura', $nro_factura);
  $stmt->bindColumn('fecha', $fecha);
  $stmt->bindColumn('razon_social', $razon_social);
  $stmt->bindColumn('importe', $importe);
  $stmt->bindColumn('exento', $exento);
  $stmt->bindColumn('nro_autorizacion', $nro_autorizacion);
  $stmt->bindColumn('codigo_control', $codigo_control);
  $stmt->bindColumn('ice', $ice);
  $stmt->bindColumn('tasa_cero', $tasa_cero);
  while ($rowCajachica = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sqlInsertDet="INSERT into facturas_compra(cod_comprobantedetalle,nit,nro_factura,fecha,razon_social,importe,exento,nro_autorizacion,codigo_control,ice,tasa_cero) values('$codigoDetalle','$nit','$nro_factura','$fecha','$razon_social','$importe','$exento','$nro_autorizacion','$codigo_control','$ice','$tasa_cero')";
    $stmtInsertDet = $dbh->prepare($sqlInsertDet);
    $flagSuccessDet=$stmtInsertDet->execute();
  }  
}

function obtener_estado_facturas($codigo){
  $dbh = new Conexion();        
  $sql="SELECT cod_estadofactura from facturas_venta where codigo=$codigo";    
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $cod_estado="";
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $cod_estado=$row['cod_estadofactura'];
  }         
  return($cod_estado);
}

function ordinalSuffix( $n ){
  $ends = array('','er','do','er','to','to','to','mo','vo','no','mo');
    if ((($n % 100) >= 11) && (($n%100) <= 13))
        return $n. 'mo';
    else
        return $n. $ends[$n % 10];
}

function obtenerValorConfiguracionCajachicaCuenta($codigo){
  $dbh = new Conexion();        
  $sql="SELECT cod_cuenta from configuraciones_cuentas_cajachica where cod_unidad=$codigo";    
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $valor=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $valor=$row['cod_cuenta'];
  }         
  return($valor);
}
function obtenerCodigoCuentaCajaChica($codigo){
  $dbh = new Conexion();        
  $sql="SELECT cod_cuenta from configuraciones_cuentas_cajachica where cod_tipo_cajachica=$codigo";    
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $valor=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $valor=$row['cod_cuenta'];
  }         
  return($valor);
}

function obtenerFechaComprobante($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT fecha FROM comprobantes where codigo=$codigo");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=$row['fecha'];
   }
   return($valor);
}

function obtenerDatosComprobanteDetalle($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT cd.glosa,cd.debe,cd.haber,(cd.debe+cd.haber) as monto,cd.cod_area,cd.cod_unidadorganizacional,p.nombre,p.numero,(SELECT nombre FROM cuentas_auxiliares where codigo=cd.cod_cuentaauxiliar) as nombre_auxiliar from comprobantes_detalle cd join plan_cuentas p on p.codigo=cd.cod_cuenta where cd.codigo=$codigo");
   $stmt->execute();
   $valor=array('','','','','');
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=array($row['glosa'],$row['monto'],$row['nombre'],$row['numero'],$row['nombre_auxiliar']);
   }
   return($valor);
}

function obtenerStringFacturas($codigo){
  $dbh = new Conexion(); 
  $stmtFActuras = $dbh->prepare("SELECT nro_factura from facturas_venta where cod_solicitudfacturacion=$codigo");
  $stmtFActuras->execute(); 
  // $stmtFActuras->bindColumn('codigo', $codigo_x);
  $stmtFActuras->bindColumn('nro_factura', $nro_factura_x);
  $cadenaFacturas="";  
  while ($row = $stmtFActuras->fetch()) {
    $cadenaFacturas.="F ".$nro_factura_x.", ";
    // $codigos_facturas.=$codigo_x.",";
  }
  $cadenaFacturas=trim($cadenaFacturas,", ");//
  return $cadenaFacturas;
}
function obtenerStringCodigoFacturas($codigo){
  $dbh = new Conexion(); 
  $stmtFActuras = $dbh->prepare("SELECT codigo from facturas_venta where cod_solicitudfacturacion=$codigo");
  $stmtFActuras->execute(); 
  $stmtFActuras->bindColumn('codigo', $codigo_x);
  // $stmtFActuras->bindColumn('nro_factura', $nro_factura_x);
  $cadenaFacturas="";  
  while ($row = $stmtFActuras->fetch()) {
    // $cadenaFacturas.="F ".$nro_factura_x.", ";
    $cadenaFacturas.=$codigo_x.",";
  }
  $cadenaFacturas=trim($cadenaFacturas,", ");//
  return $cadenaFacturas;
}
function obtenerTotalFacturasLibreta($codigo){
  $dbh = new Conexion(); 
  $stmtFActuras = $dbh->prepare("SELECT * from libretas_bancariasdetalle_facturas where cod_libretabancariadetalle =$codigo");
  $stmtFActuras->execute(); 
  $stmtFActuras->bindColumn('cod_facturaventa', $cod_facturaventa);  
  $total_facturas=0;  
  while ($row = $stmtFActuras->fetch()) {
    $monto_factura=sumatotaldetallefactura($cod_facturaventa);
    $total_facturas=$total_facturas+$monto_factura;
  }  
  return $total_facturas;
}

function verificarLibretaDetalle($codigo){
  $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT * FROM (select ld.cod_libretabancaria,ld.codigo,ld.cod_comprobantedetalle,(select count(*) from libretas_bancariasdetalle_facturas where cod_libretabancariadetalle=ld.codigo) as facturas from libretas_bancariasdetalle ld) vd
   where (vd.cod_comprobantedetalle>0 or vd.facturas>0) and vd.codigo=$codigo");
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor++;
   }
   return($valor);
}
function obtenerSumaTotal_solicitudFacturacion($codigo){
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT sf.cantidad,sf.precio,sf.descuento_bob,sf.descuento_por from solicitudes_facturaciondetalle sf where sf.cod_solicitudfacturacion=$codigo");
  $stmt->execute();
  $valor=0;
  $sumaTotalMonto=0;
  $sumaTotalDescuento_por=0;
  $sumaTotalDescuento_bob=0;
  while ($row2 = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $cantidadX=trim($row2['cantidad']);
    $precioX=(trim($row2['precio'])*$cantidadX)+trim($row2['descuento_bob']);
    $descuento_porX=trim($row2['descuento_por']);
    $descuento_bobX=trim($row2['descuento_bob']);

    $sumaTotalMonto+=$precioX;
    $sumaTotalDescuento_por+=$descuento_porX;
    $sumaTotalDescuento_bob+=$descuento_bobX;
  }
  $sumaTotalImporte=$sumaTotalMonto-$sumaTotalDescuento_bob;
  return($sumaTotalImporte);
}

function verificarExisteArchivoSolicitud($tipo,$descripcion,$tipoPadre,$codSolicitud){
  $dbh = new Conexion();
  $sql="SELECT codigo,direccion_archivo FROM archivos_adjuntos 
    where cod_tipoarchivo=$tipo and descripcion='$descripcion' and cod_tipopadre=$tipoPadre and cod_padre=0 and cod_objeto=$codSolicitud";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   $codigo=0;$direccion="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigo=$row["codigo"];
      $direccion=$row["direccion_archivo"];
   }
   return array($codigo,$direccion); 
}

function obtenerLinkDirectoArchivoAdjunto($codigo){
  $dbh = new Conexion();
  $sql="SELECT direccion_archivo FROM archivos_adjuntos 
    where codigo=$codigo";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   $direccion="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $direccion=$row["direccion_archivo"];
   }
   return $direccion; 
}
?>


