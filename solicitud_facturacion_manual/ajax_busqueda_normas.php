<?php

require_once '../conexion.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

session_start();
// $globalAdmin=$_SESSION["globalAdmin"];
// $globalGestion=$_SESSION["globalGestion"];
// $globalUnidad=$_SESSION["globalUnidad"];
// $globalArea=$_SESSION["globalArea"];

$glosa_cliente=$_GET['glosa_cliente'];
$fechaI=$_GET['fechaI'];
$fechaF=$_GET['fechaF'];
$normas=$_GET['normas'];

// $unidadOrgString=implode(",", $cod_uo);



$sql="SELECT *,DATE_FORMAT(Fecha,'%d/%m/%Y')as Fecha_x from ibnorca.ventanormas where (idSolicitudfactura=0 or idSolicitudfactura is null)";  

if($glosa_cliente!=""){
  $sql.=" and NombreCliente like '%$glosa_cliente%'";
}
if($fechaI!="" && $fechaF!=""){
  $sql.=" and Fecha BETWEEN '$fechaI' and '$fechaF'"; 
}
if($normas!=""){
  $sql.=" and idNorma in ($normas)";
}
$sql.=" order by Fecha desc;";

//echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute(); 
?>
<table class="table table-bordered table-condensed  table-sm">
     <thead>
          <tr class="fondo-boton">
            <th>#</th>
            <!-- <th >Año</th> -->
            <th>Oficina</th>
            <th>Fecha</th>
            <th width="20%">Cliente</th>
            <th>Código<br> Norma</th>
            <th>Nombre Norma</th>
            <th>Cantidad</th>
            <th width="10%">Importe<br>(BOB)</th>                                            
            <th class="small">H/D</th>  
          </tr>
      </thead>
      <tbody>                                
        <?php 
        $iii=1;
        while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $idVentaNormas=$rowPre['IdVentaNormas'];
            $idOficina=$rowPre['idOficina'];
            $nombre_oficina=trim(abrevUnidad($idOficina),'-');
            $NombreCliente=$rowPre['NombreCliente'];
            $Fecha=$rowPre['Fecha_x'];
            $idNorma=$rowPre['idNorma'];
            $Catalogo=$rowPre['Catalogo'];
            $Norma_descripcion=abrevNorma($idNorma,$Catalogo);
            $Norma=nameNorma($idNorma,$Catalogo);
            $Cantidad=$rowPre['Cantidad'];

            if($Cantidad==null || $Cantidad=='' || $Cantidad==' '){
              $Cantidad=1;
            }
            $Precio=$rowPre['Precio'];
           ?>
            <!-- guardamos todos los items en inputs -->
            <input type="hidden" id="idVentaNormas<?=$iii?>" name="idVentaNormas<?=$iii?>" value="<?=$idVentaNormas?>">
            <!-- aqui se captura los items activados -->
            <input type="hidden" id="idVentaNormas_a<?=$iii?>" name="idVentaNormas_a<?=$iii?>">
            <tr>
              <td><?=$iii?></td>
              <!-- <td class="text-left"><?=$cod_anio?> </td> -->
              <td class="text-left small"><?=$nombre_oficina?></td>
              <td class="text-righ small"><?=$Fecha?></td>
              <td class="text-left small"><?=$NombreCliente?></td>
              <td class="text-left small"><?=$Norma?></td>
              <td class="text-left small"><?=$Norma_descripcion?></td>
              <td class="text-righ smallt"><?=$Cantidad?></td>
              <td class="text-righ smallt"><?=number_format($Precio,2,".","")?></td>
              <!-- checkbox -->
              <td>
                <div class="togglebutton">
                  <label>
                    <input type="checkbox"  id="modal_check<?=$iii?>" onchange="itemsSeleccionados_ventaNormas()">
                    <span class="toggle"></span>
                  </label>
                </div>                                                    
              </td><!-- fin checkbox -->
            </tr>
            <?php   
            $iii++;
            } ?> 
            <input type="hidden" id="total_items" name="total_items" value="<?=$iii?>">
            <input type="hidden" id="comprobante_auxiliar" name="comprobante_auxiliar"><!-- contador de items seleccioados -->                     
      </tbody>
</table>     
