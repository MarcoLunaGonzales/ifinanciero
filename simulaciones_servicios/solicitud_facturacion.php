<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$codigo_simulacion=$cod;//codigo de simulacion
// $dbh = new Conexion();
// $globalAdmin=$_SESSION["globalAdmin"];
// //obtenemos datos de la simulacion en curso
// $sql="SELECT sc.nombre,ps.cod_area,ps.cod_unidadorganizacional
// from simulaciones_servicios sc,plantillas_servicios ps
// where sc.cod_plantillaservicio=ps.codigo and sc.cod_estadoreferencial=1 and sc.codigo=$codigo_simulacion";
// $stmtSimu = $dbh->prepare($sql);
// $stmtSimu->execute();
// $resultSimu = $stmtSimu->fetch();
// $nombre_simulacion = $resultSimu['nombre'];
// $cod_area_simulacion = $resultSimu['cod_area'];
// $name_area_simulacion=trim(abrevArea($cod_area_simulacion),'-');
// //obtenemos la cantidad de datos registrados de la simulacion en curso
// $stmtCantidad = $dbh->prepare("SELECT count(codigo) as cantidad FROM solicitudes_facturacion where cod_simulacion_servicio=$codigo_simulacion ");//and cod_estado=1
// $stmtCantidad->execute();
// $resutCanitdad = $stmtCantidad->fetch();
// $cantidad_items = $resutCanitdad['cantidad'];

if(isset($_GET['q'])){
  $q=$_GET['q'];
  $v=$_GET['v'];
  $s=$_GET['s'];
  $u=$_GET['u'];
}
 if(isset($_GET['q'])){
    ?>
      <script type="text/javascript">
        location = "<?=$urlRegisterSolicitudfactura;?>&cod_s=<?=$codigo_simulacion?>&cod_f=0&cod_sw=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>"
      </script>
   <?php
  }else{
   ?>
      <script type="text/javascript">
        location = "<?=$urlRegisterSolicitudfactura;?>&cod_s=<?=$codigo_simulacion?>&cod_f=0&cod_sw=0"
      </script>
   <?php
  }
?>