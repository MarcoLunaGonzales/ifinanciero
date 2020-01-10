<?php
require_once '../conexion.php';
require_once 'configModule.php'; //configuraciones
require_once '../styles.php';
require_once '../functionsGeneral.php';

$codigo = trim($_GET['codigo']); 
$nombreArea = $_GET['nombreArea'];

//cargar lista de cargos
      $dbhB = new Conexion();
      $sqlB="SELECT ca.*,c.nombre FROM cargos_areasorganizacion ca join cargos c on ca.cod_cargo=c.codigo where ca.cod_areaorganizacion=$codigo order by c.nombre";
      $stmtB = $dbhB->prepare($sqlB);
      $stmtB->execute();
      $fc=0;
      while ($rowB = $stmtB->fetch(PDO::FETCH_ASSOC)) {
        $codigoX=$rowB['codigo'];
        $cod_cargoX=$rowB['cod_cargo'];
        $nombreX=$rowB['nombre'];
        $cod_areaorganizacionX=$codigo;                        
        $fc++;
        ?>
         <tr>
          <td><?=$fc?></td>
          <td><?=$nombreX?></td>
          <td><?=$nombreArea?></td>
          <td>
              <a class="btn btn-sm btn-fab btn-danger" href="#" onclick="borrarCargoAreaOrganizacion(<?=$codigoX?>)">
                <i class="material-icons"><?=$iconDelete;?></i>
              </a>
          </td>
        </tr>
        <?php
      }
      if($fc==0){
      ?><tr><td colspan="4">Ningun dato registrado</tr><?php
      }
?>