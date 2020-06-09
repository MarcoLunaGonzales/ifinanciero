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

        $stmt = $dbh->prepare("SELECT * FROM plantillas_comprobante where cod_estadoreferencial=1 and cod_unidadorganizacional=$globalUnidad");
        $stmt->execute();
        ?>
         <table class="table">
          <tr>
            <th width="3%">Nro</th>
            <th width="2%">Cargar</th>
            <th width="4%">Unidad</th>
            <th width="30%">Titulo</th>
            <th>Descripci&oacute;n</th>
            <th width="15%">Creado por</th>
            <th width="2%">Options</th>
          </tr>
        <?php
        $j=0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $arch=json_decode($row['archivo_json']);
          $nro=cantidadF($arch[1]);
          $ntipo=$arch[0][0]->tipo_comprobante;
          $glosa=$arch[0][0]->glosa;
          $cod=$row['codigo'];
          $nombre_unidad=abrevUnidad($row['cod_unidadorganizacional']);
          $nombre_personal=namePersonal($row['cod_personal']);
          ?>
           <tr>
             <td><?=($j+1)?></td>
             <td><a href="#" title="Cargar Plantilla" onclick="abrirPlantilla('<?=$cod?>',<?=$nro?>,'<?=$glosa?>',<?=$ntipo?>)" class="btn btn-warning btn-fab btn-link"><i class="material-icons">favorite</i></a>
            </td>
             <td><?=$nombre_unidad?></td>
             <td><small><?=$row['titulo']?></small></td>
             <td><small><?=$row['descripcion']?></small></td>
             <td><small><?=$nombre_personal?></small></td>
             <td><button title="Borrar Plantilla" class="btn btn-danger btn-link" onclick="removePlantillaComprobantes('<?=$cod?>');"><i class="material-icons">remove_circle</i></button></td>
           </tr>
          <?php
          $j++;
         }
         ?>
         </table>

