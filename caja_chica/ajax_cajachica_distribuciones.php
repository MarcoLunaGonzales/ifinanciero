<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../styles.php';
$dbh = new Conexion();
$codigo=$_GET['codigo'];
?>
<?php  
 //distribuciones
  $index=1;
  $distribucionOfi=obtenerDistribucionCajachicaDetalle($codigo,1);
  $total_porcetnaje=0;
?>
<table class="table table-condensed table-bordered">
  <thead>
    <tr class="bg-principal text-white">
      <th>#</th>
      <th>Oficina</th>
      <th width="10%">%</th>
    </tr>
  </thead>
  <tbody>
    <?php
    while ($rowOfi = $distribucionOfi->fetch(PDO::FETCH_ASSOC)) {    
      $porcentajeD=$rowOfi['porcentaje'];
      $nombre=$rowOfi['nombre'];
      $total_porcetnaje+=$porcentajeD;
      ?>
      <tr>
        <td><?=$index?></td>
        <td class="font-weight-bold text-left"><?=$nombre?></td>
        <td><?=$porcentajeD?></td>
      </tr>
    <?php
    $index++;
    } 
    ?>
    <tr>
      <td></td>
      <td class="text-left font-weight-bold">TOTAL</td>
      <td class="text-left font-weight-bold"><?=$total_porcetnaje?></td>
    </tr>    
  </tbody>
                    
</table>
