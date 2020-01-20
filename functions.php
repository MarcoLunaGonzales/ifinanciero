<?php
require_once 'conexion.php';

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

function nameGestion($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM gestiones where codigo=:codigo");
   $stmt->bindParam(':codigo',$codigo);
   $stmt->execute();
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
  $sqlPersonal = "SELECT nombre FROM personal2 where codigo in ($codigo)";
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

function namePersonal($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT nombre FROM personal2 where codigo=:codigo");
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

function abrevUnidad($codigo){
   $dbh = new Conexion();
   $stmt = $dbh->prepare("SELECT abreviatura FROM unidades_organizacionales where codigo in ($codigo)");
   $stmt->execute();
   $nombreX="";
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombreX.=$row['abreviatura']." - ";
   }
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
   $sql="SELECT d.codigo as cod_det,d.cod_area,d.cod_unidadorganizacional,p.codigo,p.numero,p.nombre,d.glosa,d.debe,d.haber,a.abreviatura,p.cuenta_auxiliar,u.abreviatura as unidadAbrev FROM plan_cuentas p join comprobantes_detalle d on p.codigo=d.cod_cuenta join areas a on d.cod_area=a.codigo join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional where d.cod_comprobante=$codigo order by cod_det";
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
   $sql="SELECT d.codigo as cod_det,d.cod_area,d.cod_unidadorganizacional,p.codigo,p.numero,p.nombre,d.glosa,d.debe,d.haber,a.abreviatura,p.cuenta_auxiliar,u.abreviatura as unidadAbrev,(select 1 from comprobantes_detalle cdd where cdd.debe=0 and d.codigo=cdd.codigo) as haber_order 
FROM plan_cuentas p join comprobantes_detalle d on p.codigo=d.cod_cuenta join areas a on d.cod_area=a.codigo join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional where d.cod_comprobante=$codigo order by haber_order, d.codigo";
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

//funcion para descargar con dompdf
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
     $dbh = new Conexion(); 
     $stmt = $dbh->prepare($sql);
     $stmt->execute();
     $sum=0;
     $mes=date("m");
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $numero=trim($row['numero']);
      $cuenta=$row['cod_cuenta'];
      $tipoSim=obtenerValorConfiguracion(13);
      if($tipoSim==1){
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
    //return $sum;
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
    $query2="select pgd.cod_plantillagrupocosto,pc.cod_unidadorganizacional,pc.cod_area,pgc.nombre,pgc.cod_tipocosto,sum(pgd.monto_local) as local,sum(pgd.monto_externo) as externo,sum(pgd.monto_calculado) as calculado from plantillas_grupocostodetalle pgd join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo
join plantillas_gruposcosto pgc on pgd.cod_plantillagrupocosto=pgc.codigo
join plantillas_costo pc on pgc.cod_plantillacosto=pc.codigo 
where pc.codigo=$codigo and pgc.cod_tipocosto=$tipo GROUP BY pgd.cod_plantillagrupocosto order by pgd.cod_plantillagrupocosto";


  $stmt = $dbh->prepare($query2);
  $stmt->execute();

  $totalImporte=0;$totalModulo=0;$totalLocal=0;$totalExterno=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codGrupo=$row['cod_plantillagrupocosto'];$grupoUnidad=$row['cod_unidadorganizacional'];$grupoArea=$row['cod_area'];
    $importe_grupo=(float)$row['calculado']*$mes;
    $totalImporte+=$importe_grupo;;
    $totalModulo+=$row['calculado'];
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
  $sql="SELECT IFNULL(max(c.numero)+1,1)as codigo from comprobantes c where c.cod_tipocomprobante='$cod_tipocomprobante' and c.cod_unidadorganizacional='$unidad_organizacional' and YEAR(c.fecha)='$gestion' and MONTH(c.fecha)='$mes'";
  //echo $sql;
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $nroCorrelativo=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $nroCorrelativo=$row['codigo'];
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
   $sql="";
   $sql="SELECT DISTINCT pc.* from simulaciones_costos s 
join plantillas_costo p on s.cod_plantillacosto=p.codigo 
join plantillas_gruposcosto pg on p.codigo=pg.cod_plantillacosto 
join plantillas_grupocostodetalle pgd on pgd.cod_plantillagrupocosto=pg.codigo
join partidas_presupuestarias pp on pp.codigo=pgd.cod_partidapresupuestaria 
join partidaspresupuestarias_cuentas ppc on ppc.cod_partidapresupuestaria=pp.codigo 
join plan_cuentas pc on ppc.cod_cuenta=pc.codigo WHERE s.cod_estadosimulacion=$estado and pg.cod_tipocosto=2 and s.cod_responsable=$codUsuario and pc.nivel=$nivel order by pp.codigo";
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
function obtenerCuentaPlantillaCostos($codigo){
  $dbh = new Conexion();
  $sql="";
  $sql="SELECT p.cod_partidapresupuestaria,p.cod_cuenta,c.numero FROM partidaspresupuestarias_cuentas p join plan_cuentas c on p.cod_cuenta=c.codigo where p.cod_partidapresupuestaria=$codigo";
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

//================ ========== PARA  planilla sueldos

function obtenerBonoAntiguedad($minino_salarial,$ing_contr){  
  $anio_actual= date('Y');
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
function obtenerTotalBonos($codigo_personal,$dias_trabajados_asistencia,$dias_trabajados_por_defecto)
{  
  $mes=date('m');
  $gestion=date('Y');

  // $mes=11;
  // $gestion=2019;

  $dbh = new Conexion();
  $sqlGestion = "SELECT codigo from gestiones where nombre=$gestion";
  $stmtGestion = $dbh->prepare($sqlGestion);
  $stmtGestion->execute();
  $resultGestion=$stmtGestion->fetch();
  $cod_gestion = $resultGestion['codigo'];


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

function obtenerAtrasoPersonal($id_personal,$haber_basico){
  $dbh = new Conexion();
  set_time_limit(300);
  //capturando fecha
  $mes=date('m');
  $gestion=date('Y');
  // $mes=11;
  // $gestion=2019;

  $dbh = new Conexion();
  $sqlGestion = "SELECT codigo from gestiones where nombre=$gestion";
  $stmtGestion = $dbh->prepare($sqlGestion);
  $stmtGestion->execute();
  $resultGestion=$stmtGestion->fetch();
  $cod_gestion = $resultGestion['codigo'];

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
function obtenerOtrosDescuentos($codigo_personal)
{  
  $mes=date('m');
  $gestion=date('Y');

  // $mes=11;
  // $gestion=2019;

  $dbh = new Conexion();
  $sqlGestion = "SELECT codigo from gestiones where nombre=$gestion";
  $stmtGestion = $dbh->prepare($sqlGestion);
  $stmtGestion->execute();
  $resultGestion=$stmtGestion->fetch();
  $cod_gestion = $resultGestion['codigo'];


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
function obtenerAnticipo($id_personal)
{
  $anticipo=0;
  
  $mes=date('m');
  $gestion=date('Y');
  // $mes=11;
  // $gestion=2019;

  $dbh = new Conexion();
  $sqlGestion = "SELECT codigo from gestiones where nombre=$gestion";
  $stmtGestion = $dbh->prepare($sqlGestion);
  $stmtGestion->execute();
  $resultGestion=$stmtGestion->fetch();
  $cod_gestion = $resultGestion['codigo'];

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
function Verificar_si_corresponde_Aguinaldo($ing_contr){
  $anio_actual= date('Y');
  // $anio_actual=2019;
  $fechaComoEntero = strtotime($ing_contr);
  $anio_ingreso = date("Y", $fechaComoEntero);
  $mes_ingreso = date("m", $fechaComoEntero);
  $diferencia_anios=$anio_actual-$anio_ingreso;
  $diferencia_meses=12-$mes_ingreso;
  if($diferencia_anios>0){
    $sw=1;
  }elseif($diferencia_meses>2){
    $sw=1;
  }else $sw=0;
}
function obtner_id_planilla($cod_gestion,$cod_mes){
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
function obtnerSueldomes($cod_personal,$cod_planilla){
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
function obtenerTotalesSimulacion($codigo){
  $dbh = new Conexion();
    $montoI=0;$montoF=0;
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
 $dbh = new Conexion();
  $sql = "SELECT es.*,p.email_empresa,concat(p.primer_nombre,' ',p.otros_nombres,' ',p.paterno,' ',p.materno) as personal,e.nombre,
  pc.fecha_iniciocontrato,pc.fecha_fincontrato
  FROM eventos_sistemapersonal es 
  join personal_contratos pc on es.cod_personal=pc.cod_personal
  join personal p on es.cod_personal=p.codigo
  join eventos_sistema e on e.codigo=es.cod_eventosistema ";
  if($tipoContrato==1){
   $dias=obtenerValorConfiguracion(12); 
   $sql.="where pc.cod_tipocontrato=1";  
  }else{
   $dias=obtenerValorConfiguracion(11);
   $sql.="where pc.cod_tipocontrato!=1";
  }
  
    $stmt = $dbh->prepare($sql);
  $stmt->execute();

 $i=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
    $codigo=$row['codigo'];
    $correo=$row['email_empresa'];
    $titulo=$row['nombre'];
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
  }

}
?>


