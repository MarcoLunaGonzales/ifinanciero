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
        $sql = obtenerFacturasCompro($codigo);
        $cabeceraFac[0]="nit";$cabeceraFac[1]="nro_factura";$cabeceraFac[2]="fecha";$cabeceraFac[3]="razon_social";$cabeceraFac[4]="importe";$cabeceraFac[5]="exento";$cabeceraFac[6]="nro_autorizacion";$cabeceraFac[7]="codigo_control";
        editarComprobanteDetalle($codigo,'cod_comprobantedetalle',contarFacturasCompra($codigo),cantidadF($fact[$i]),$sql,'facturas_compra',$cabeceraFac,$valFac,null);
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
          $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
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
        $sql = obtenerFacturasCompro($codComprobanteDetalle);
        $cabeceraFac[0]="nit";$cabeceraFac[1]="nro_factura";$cabeceraFac[2]="fecha";$cabeceraFac[3]="razon_social";$cabeceraFac[4]="importe";$cabeceraFac[5]="exento";$cabeceraFac[6]="nro_autorizacion";$cabeceraFac[7]="codigo_control";
        editarComprobanteDetalle($codComprobanteDetalle,'cod_comprobantedetalle',contarFacturasCompra($codComprobanteDetalle),cantidadF($fact[$j]),$sql,'facturas_compra',$cabeceraFac,$valFac,null);
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
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $numero=trim($row['numero']);
      $sum+=(float)obtenerMontoPorCuenta($numero,$unidad,$area,$fecha);      
    }
    $sql="SELECT * from configuraciones where id_configuracion=6";
    $dbh = new Conexion(); 
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $valor=trim($row['valor_configuracion']);
    }
    return redondearDecimal($sum/(int)$valor);
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
        $alumnosX=$row['cantidad_alumnos'];
     }
     return array($precioLocalX,$precioExternoX,$alumnosX);
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
  return array($totalImporte,$totalModulo,$totalExterno,$totalLocal);
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
?>

