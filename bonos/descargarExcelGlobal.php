<meta charset="utf-8">
<?php
header("Pragma: public");
header("Expires: 0");
$fecha_actual=date('Ymd');
$filename = "Plantilla_Planillas_".$fecha_actual.".xls";
header("Content-type: application/x-msdownload");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
require_once("../conexion.php");
require_once '../functions.php';
$dbh = new Conexion();
$sql="SELECT p.codigo,(select a.nombre from areas a where a.codigo=p.cod_area)as areas,p.identificacion,CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre)as personal,p.cod_unidadorganizacional
  FROM personal p
  where p.cod_estadopersonal=1 and p.cod_estadoreferencial=1
  order by p.cod_unidadorganizacional,2,p.paterno";
$dias_trabajados_mes = obtenerValorConfiguracionPlanillas(22); //dias trabajados del mes
?>
<table class="table table-condensed table-bordered">
  <thead>
    <tr>
      <th><small><b>CODIGO</b></small></th>
      <th><small><b>CI</b></small></th>
      <th><small><b>Apellidos y Nombres</b></small></th>
      <th><small><b>Area</b></small></th>     
      <th><small><b>Dias Trabajados L_V</b></small></th>      
      <?php
      $sqlBonos="SELECT codigo,nombre from bonos where cod_estadoreferencial=1 order by codigo";
      $contador_bonos=0;
      $stmtBono = $dbh->prepare($sqlBonos);
      $stmtBono->execute();
      while ($rowBono = $stmtBono->fetch(PDO::FETCH_ASSOC)) {
        $nombre_bono=$rowBono['nombre']; ?>
        <th><small><b><?=$nombre_bono?></b></small></th><?php   
        $contador_bonos++;
      }      
      $sqldes="SELECT codigo,nombre from descuentos where cod_estadoreferencial=1 order by codigo";
      $contador=0;
      $stmtDes = $dbh->prepare($sqldes);
      $stmtDes->execute();
      while ($row1 = $stmtDes->fetch(PDO::FETCH_ASSOC)) {
        $nombre_descuento=$row1['nombre'];?>
  			<th><small><b><?=$nombre_descuento?></b></small></th><?php   
  			$contador++;
  		} ?>
      <th><small><b>ANTICIPOS</b></small></th>
    </tr>    
  </thead>
  <tbody>    
    <?php
    $stmtDet = $dbh->prepare($sql);
    $stmtDet->execute();
    while ($row = $stmtDet->fetch(PDO::FETCH_ASSOC)) { 
        $codigo=$row['codigo'];
        $areas=$row['areas'];
        $identificacion=$row['identificacion'];
        $personal=$row['personal'];        
        $cod_unidadorganizacional=$row['cod_unidadorganizacional'];        
        ?>
        <tr>
          <td class="text text-left"><small><?=$codigo?></small></td>
          <td class="text text-left"><small><?=$identificacion?></small></td>
          <td class="text text-left"><small><?=$personal?></small></td>
          <td class="text text-left"><small><?=$areas?></small></td>
          <td class="text text-left"><small><?=$dias_trabajados_mes?></small></td>
          <?php
          for ($x=0; $x <$contador_bonos ; $x++) { ?>
            <td class="text text-left"><small></small></td>
          <?php }
          for ($x=0; $x <$contador ; $x++) { ?>
            <td class="text text-left"><small></small></td>
          <?php }
          ?>
          <td class="text text-left"><small></small></td>          
        </tr>
      <?php } ?>
  </tbody>
</table>