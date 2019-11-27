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

        $stmt = $dbh->prepare("SELECT * FROM plantillas_comprobante where cod_unidadorganizacional=$globalUnidad");
        $stmt->execute();
        ?>
         <table class="table">
          <tr>
            <th>Nro</th>
            <th>Cargar</th>
            <th>Unidad</th>
            <th>Titulo</th>
            <th>Descripci&oacute;n</th>
            <th>Creado por</th>
          </tr>
        <?php
        $j=0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $arch=json_decode($row['archivo_json']);
          $nro=cantidadF($arch[1]);
          $ntipo=$arch[0][0]->tipo_comprobante;
          $glosa=$arch[0][0]->glosa;
          $cod=$row['codigo'];
          ?>
           <tr>
             <td><?=($j+1)?></td>
             <td><a href="#" onclick="abrirPlantilla('<?=$cod?>',<?=$nro?>,'<?=$glosa?>',<?=$ntipo?>)" class="btn btn-warning btn-fab btn-link"><i class="material-icons">favorite</i></a>
            </td>
             <td><?=$row['cod_unidadorganizacional']?></td>
             <td><?=$row['titulo']?></td>
             <td><?=$row['descripcion']?></td>
             <td><?=$row['cod_personal']?></td>
           </tr>
          <?php
          $j++;
         }
         ?>
         </table>
        <?php
?>
