<?php
set_time_limit(0);
error_reporting(-1);

require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../styles.php';

$dbh = new Conexion();

?>


<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon">
              <i class="material-icons">assignment</i>
            </div>
            <h4 class="card-title">Cargado Inicial de Comprobantes</h4>
          </div>
          <div class="card-body">
                  
<?php

echo "<h6>Hora Inicio Proceso: " . date("Y-m-d H:i:s")."</h6>";

//conexion modificado IBNORCA- INGE
//$dsn = "conta";
//$usuario = "sa";
//$clave = "minka@2018";
  //debe ser de sistema no de usuario
  //realizamos la conexion mediante odbc
//end modificado

  //CONEXION EXTERNA TIPO 2
  $dsn = "conta"; 
  $usuario = "consultadb";
  $clave="consultaibno1$";
 
  //FIN CONEXION EXTERNA TIPO 2


  $conexión=odbc_connect($dsn, $usuario, $clave);


if (!$conexión) { 
  exit( "Error al conectar: " . $conexión);
}else{
    echo "CONEXION ESTABLECIDA!!!!";
    //maximo codigo tabla po_mayores
    $flagSuccess=TRUE;
    $flagSuccess2=TRUE;

    $sqlInserta="";

    $sql = "SELECT forma.fondo, forma.clase, forma.numero, forma.fecha, forma.moneda, forma.glosa, forma.estado 
    FROM ibnorca2020.dbo.forma where forma.clase not in ('I-ADM', 'POA', 'POA99', 'POE', 'POE99', 'PPC', '4') 
    and forma.fondo not in (2000,2001) and MONTH(forma.fecha) in (1) order by forma.fecha,
         forma.clase, forma.numero;";
    // end modificado ,2,3,4,5,6

    $rs = odbc_exec( $conexión, $sql );
    if ( !$rs ) { 
      echo $sql."<br>";
      exit( "Error en la consulta SQL" ); 
    }
    
    //$indiceCodigo=$indiceMax+1;

$sqlDetalle="SELECT cd.codigo from comprobantes_detalle cd join comprobantes c on c.codigo=cd.cod_comprobante
where year(c.fecha)=2020 and month(c.fecha) in (1) and c.cod_unidadorganizacional!=3000
order by cd.cod_comprobante,cd.codigo";
// echo $sqlDetalle;
$stmtFCCD = $dbh->prepare($sqlDetalle);
$stmtFCCD->execute();
$arrayCodigos=[];$ii=0;
while ($row = $stmtFCCD->fetch(PDO::FETCH_ASSOC)) {
 $arrayCodigos[$ii]=$row['codigo'];                                       
 $ii++;
}

$indexCodigo=0;
    while(odbc_fetch_row($rs)){ 
      $fondo=odbc_result($rs,"fondo");
      $clase=odbc_result($rs,"clase");
      $numero=odbc_result($rs,"numero");
      $fecha=odbc_result($rs,"fecha");
      $moneda=odbc_result($rs,"moneda");
      $glosa=odbc_result($rs,"glosa");
      $estado=odbc_result($rs,"estado");

      $glosa=utf8_encode($glosa);
      $glosa=clean_string($glosa);
      //$glosa=string_sanitize($glosa);      
      $buscar=array(chr(13).chr(10), "\r\n", "\n", "\r");
      $reemplazar=array("", "", "", "");
      $glosa=str_ireplace($buscar,$reemplazar,$glosa);      
      $glosa=addslashes($glosa);

      //echo $fondo." ".$clase." ".$numero." ".$fecha." ".$moneda." ".$glosa." ".$estado."<br>";
      $fondo=intval($fondo);
      $unidadInsertar=0;
      if($fondo==1010){
        $unidadInsertar=829;
      }elseif ($fondo==1011) {
        $unidadInsertar=5;
      }elseif ($fondo==1020) {
        $unidadInsertar=10;
      }elseif ($fondo==1030) {
        $unidadInsertar=9;
      }elseif ($fondo==1040) {
        $unidadInsertar=270;
      }elseif ($fondo==1050) {
        $unidadInsertar=271;
      }elseif ($fondo==1060) {
        $unidadInsertar=8;
      }elseif ($fondo==1012) {
        $unidadInsertar=2692;
      }elseif ($fondo==1070) {
        $unidadInsertar=272;
      }elseif ($fondo==1070) {
        $unidadInsertar=272;
      }elseif ($fondo==2001) {
        $unidadInsertar=3000;
      }else{
        $unidadInsertar=$fondo;
      }


      $pos = strpos($clase,'-');
      if(count(explode('-', $clase))==2) {
        list($tipoComprobante, $mesComprobante) = explode('-',$clase);
      }else{
        if(trim($clase)=="FAC"){
          $tipoComprobante="FAC";
          $tipoComprobanteInsertar=4;
        }
        echo "COMPROBANTE DE FACTURA<br>"; 
      }
        
        

        if($tipoComprobante=="T"){
        $tipoComprobanteInsertar=3;
      }elseif ($tipoComprobante=="I") {
        $tipoComprobanteInsertar=1;
      }elseif ($tipoComprobante=="E") {
        $tipoComprobanteInsertar=2;
      }elseif ($tipoComprobante=="FAC"){
        $tipoComprobanteInsertar=4;
      }

      $numeroComprobante=intval($numero);

      //INSERTAMOS LA CABECERA DEL COMPROBANTE
      $codEmpresa=1;
      $codMoneda=1;
      $codEstadoComprobante=1;
      $codComprobante=obtenerCodigoComprobante();
      if($flagSuccess==FALSE){
        exit("ERROR EN LA INSERCION ".$sqlInsertCab);
      }
      
      //

      $sqlDetalle="SELECT detalle.cuenta, cuenta.nombre AS nombre_cuenta, detalle.partida, detalle.debebs, detalle.haberbs, detalle.glosa AS glosa_detalle, detalle.organismo, detalle.categoria AS ml_partida, detalle.auxiliar FROM ibnorca2020.dbo.detalle, ibnorca2020.dbo.cuenta WHERE detalle.entidad = '10' AND detalle.fondo = '$fondo' AND detalle.clase = '$clase' AND detalle.numero = '$numero' AND cuenta.codigo = detalle.cuenta";
      $rsDetalle = odbc_exec($conexión, $sqlDetalle);

      if ( !$rsDetalle ) { 
        echo $sqlDetalle."<br>";
        exit( "Error en la consulta SQL" ); 
      }      

      echo "CODIGO: ".$codComprobante." ".$clase." ".$fondo."<br>";
  
      $ordenDetalle=1;
      $insert_str="";
      while(odbc_fetch_row($rsDetalle)){
          //echo "entro al detalle";
          //echo $sqlDetalle."<br>";
          $cuentaDetalle=odbc_result($rsDetalle, "cuenta");
          $nombreCuentaDetalle=odbc_result($rsDetalle, "nombre_cuenta");
          $partida=odbc_result($rsDetalle, "partida");
          $debebs=odbc_result($rsDetalle, "debebs");
          $haberbs=odbc_result($rsDetalle, "haberbs");
          $glosaDetalle=odbc_result($rsDetalle, "glosa_detalle");
          
          $glosaDetalle=utf8_encode($glosaDetalle);
          $glosaDetalle=clean_string($glosaDetalle);
          $buscar=array(chr(13).chr(10), "\r\n", "\n", "\r");
          $reemplazar=array("", "", "", "");
          $glosaDetalle=str_ireplace($buscar,$reemplazar,$glosaDetalle);      
          $glosaDetalle=addslashes($glosaDetalle);
          
          $organismoDetalle=odbc_result($rsDetalle, "organismo");
          $mlPartidaDetalle=odbc_result($rsDetalle, "ml_partida");
          $cuentaAuxiliar=odbc_result($rsDetalle, "auxiliar");
          
          //echo "entro acad 2";

          $cuentaInsertar=buscarCuentaAnterior($cuentaDetalle);
          $cuentaAuxiliarInsertar=buscarCuentaAuxiliarAnterior($cuentaAuxiliar,$cuentaInsertar);

          //INSERTAR CODIGO PARA VALIDAR SOLO LAS CUENTAS DE ESTADOS DE CUENTA

          //echo "entro acad 3";

          $organismoDetalle=intval($organismoDetalle);
          if($organismoDetalle==501){
            $organismoInsert=847;
          }elseif ($organismoDetalle==502) {
            $organismoInsert=502;
          }elseif ($organismoDetalle==503) {
            $organismoInsert=11;
          }elseif ($organismoDetalle==504) {
            $organismoInsert=0;
          }elseif ($organismoDetalle==505) {
            $organismoInsert=38;
          }elseif ($organismoDetalle==506) {
            $organismoInsert=39;
          }elseif ($organismoDetalle==507) {
            $organismoInsert=40;
          }elseif ($organismoDetalle==508) {
            $organismoInsert=13;
          }elseif ($organismoDetalle==509) {
            $organismoInsert=-1;
          }elseif ($organismoDetalle==510) {
            $organismoInsert=12;
          }elseif ($organismoDetalle==511) {
            $organismoInsert=-2;
          }elseif ($organismoDetalle==512) {
            $organismoInsert=-3;
          }elseif ($organismoDetalle==513) {
            $organismoInsert=-4;
          }elseif ($organismoDetalle==710) {
            $organismoInsert=1235;
          }else{
            $organismoInsert=502;
          }
          //echo $organismoInsert."<br>";
           $codigoDetalleComprobante=$arrayCodigos[$indexCodigo];
            $sqlInsertDet="UPDATE comprobantes_detalle SET cod_cuentaauxiliar=$cuentaAuxiliarInsertar where codigo=$codigoDetalleComprobante";
            $stmtInsertDet=$dbh->prepare($sqlInsertDet);
            $flagSuccess2=$stmtInsertDet->execute();
           $flagSuccess2=true;
            $indexCodigo++;
          $ordenDetalle++;
          echo "CODIGO:".$codigoDetalleComprobante.", auxiliar:".$cuentaAuxiliarInsertar;
      }

       if($flagSuccess2==FALSE){
        exit("ERROR EN LA INSERCION DETALLE ".$sqlInsertDet);
       }
      
      
      

    }
}//FIN RECORRIDO GESTION

odbc_close($conexión);

echo "<h6>HORA FIN PROCESO CARGADO INICIAL COMPROBANTES: " . date("Y-m-d H:i:s")."    ----------- Index: ".($indexCodigo-1).", Codigos:".count($arrayCodigos)."</h6>";
?>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>