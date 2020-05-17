<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$usuario=$_SESSION['globalUser'];
$globalUnidad=$_SESSION["globalUnidad"];
?><script>
  itemDistArea=[];
  </script><?php
if(isset($_GET['unidad'])){
 $distribucionArea=obtenerDistribucionCentroCostosAreaActivo($_GET['unidad']); //null para todas las iniciales del numero de 
   while ($rowArea = $distribucionArea->fetch(PDO::FETCH_ASSOC)) {
    $codigoD=$rowArea['codigo'];
    $codDistD=$rowArea['cod_distribucionarea'];
    $codAreaD=$rowArea['cod_area'];
    $porcentajeD=$rowArea['porcentaje'];
    $nombreD=$rowArea['nombre'];
     ?>
      <script>
        var distri = {
          codigo:<?=$codigoD?>,
          cod_dis:<?=$codDistD?>,
          area:<?=$codAreaD?>,
          nombre:'<?=$nombreD?>',
          porcentaje:<?=$porcentajeD?>
        }
        itemDistArea.push(distri);
      </script>  
      <?php
   }
}
  ?>
  