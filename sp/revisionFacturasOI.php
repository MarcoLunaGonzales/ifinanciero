<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-icon">
            <div class="card-icon">
            </div>
            <h4 class="card-title">Revision OI</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="tablePaginator" class="table table-condensed">
                <thead>
                  <tr>
                    <th class="text-left">Codigo</th>
                    <th class="text-center">NroFactura</th>
                    <th class="text-center">FechaFactura</th>
                    <th class="text-center">nit</th>
                    <th class="text-center">Razon Social</th>
                    <th class="text-center">CodDetalle</th>
                    <th class="text-center">codClaSservicio</th>
                    <th class="text-center">cantidad</th>
                    <th class="text-center">Precio</th>
                    <th class="text-center">CodSF</th>
                    <th class="text-center">CodSFDetalle</th>
                    <th class="text-center">DescAlterna</th>
                    <th class="text-center">idServicio</th>
                  </tr>
                </thead>
                <tbody>
<?php
require_once '../conexion.php';
require_once '../functions.php';

$dbh = new Conexion();

$sql="SELECT f.codigo, f.nro_factura, f.fecha_factura, f.nit, f.razon_social, fd.codigo as codigo_detalle, fd.cod_claservicio, fd.cantidad, fd.precio, fd.id_servicio, f.cod_solicitudfacturacion from facturas_venta f, facturas_ventadetalle fd where f.codigo=fd.cod_facturaventa and f.cod_estadofactura<>2 
and f.fecha_factura>='2023-01-01' and f.cod_area=11 and f.cod_estadofactura<>2";
//echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codigoFactura=$row['codigo'];
  $nroFactura=$row['nro_factura'];  
  $fechaFactura=$row['fecha_factura'];
  $nit=$row['nit'];
  $razonSocial=$row['razon_social'];
  $codigoDetalle=$row['codigo_detalle'];
  $codClaServicio=$row['cod_claservicio'];
  $cantidad=$row['cantidad'];
  $precio=$row['precio'];
  $idServicio=$row['id_servicio'];
  $codSolicitudFacturacion=$row['cod_solicitudfacturacion'];

    $sqlSF="SELECT sfd.codigo, sfd.cod_claservicio, sfd.cantidad, sfd.precio, sfd.descripcion_alterna, sfd.id_servicio from solicitudes_facturaciondetalle sfd where sfd.cod_solicitudfacturacion='$codSolicitudFacturacion' and sfd.cod_claservicio='$codClaServicio' and sfd.cantidad='$cantidad' and sfd.precio='$precio' limit 0,1";
    $stmtSF = $dbh->prepare($sqlSF);
    $stmtSF -> execute();
    while ($rowSF = $stmtSF->fetch(PDO::FETCH_ASSOC)) {
        $codSFDetalle=$rowSF['codigo'];
        $descripcionSF=$rowSF['descripcion_alterna'];
        $idServicio=$rowSF['id_servicio'];


        $sqlUpd="update facturas_ventadetalle set id_servicio='$idServicio' where codigo='$codigoDetalle'";
        $stmtUpd = $dbh->prepare($sqlUpd);
        $stmtUpd -> execute();
    

        ?>
          <tr>
            <td class="text-center"><?= $codigoFactura; ?></td>
            <td class="text-center"><?= $nroFactura; ?></td>
            <td class="text-center"><?= $fechaFactura; ?></td>
            <td class="text-center"><?= $nit; ?></td>
            <td class="text-center"><?= $razonSocial; ?></td>
            <td class="text-center"><?= $codigoDetalle; ?></td>
            <td class="text-center"><?= $codClaServicio; ?></td>
            <td class="text-center"><?= $cantidad; ?></td>
            <td class="text-center"><?= $precio; ?></td>
            <td class="text-center"><?= $idServicio; ?></td>
            <td class="text-center"><?= $codSolicitudFacturacion; ?></td>
            <td class="text-center"><?= $codSFDetalle; ?></td>
            <td class="text-center"><?= $descripcionSF; ?></td>
            <td class="text-center"><?= $idServicio; ?></td>
          </tr>
        <?php

    }

  
  /*$sqlAreaCorrecta="select cod_area from personal p where p.codigo='$codResponsable'";
  $stmtArea = $dbh->prepare($sqlAreaCorrecta);
  $stmtArea->execute();
  while ($rowArea = $stmtArea->fetch(PDO::FETCH_ASSOC)) {
    $codAreaCorrecta=$rowArea['cod_area'];
  }
  if($codAreaCorrecta!=$area){
    //echo "$codigoInterno $codigoActivo $nombreActivo $estadoActivo $area $nombreResponsable $codAreaCorrecta<br>";
    echo "update activosfijos set cod_area='$codAreaCorrecta' where codigo='$codigoInterno';<br>";
  } 
  */ 
  //echo "update facturas_venta set codigo_control='$codigoControl' where codigo='$codigoX';<br>";
}

?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>