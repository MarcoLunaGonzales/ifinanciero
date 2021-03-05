<?php
require_once '../../conexion.php';
require_once '../../conexion_externa.php';

$dbh = new ConexionIBNORCA();
//sql
/*$sql="INSERT INTO estadoobjeto (idtipoobjeto,idestado,idresponsable,idobjeto,fechaestado,observaciones)
     VALUES ('2708','2722','58','837','2020-10-22 14:18:36','En aprobacion');
INSERT INTO estadoobjeto (idtipoobjeto,idestado,idresponsable,idobjeto,fechaestado,observaciones)
     VALUES ('2708','2722','58','840','2020-10-22 14:18:07','En aprobacion');
INSERT INTO estadoobjeto (idtipoobjeto,idestado,idresponsable,idobjeto,fechaestado,observaciones)
     VALUES ('2708','2722','58','841','2020-10-22 14:17:51','En aprobacion');
INSERT INTO estadoobjeto (idtipoobjeto,idestado,idresponsable,idobjeto,fechaestado,observaciones)
     VALUES ('2708','2722','58','842','2020-10-22 14:48:18','En aprobacion');";*/
$servicio=14791;
    echo "IDSERVICIO:".$servicio."<br><br><br>"; 
$sql="SELECT c.IdCotizacion,c.Descuento,s.IdDetServicio,s.IdClaServicio,s.Cantidad,s.PrecioUnitario, 1 AS tipo_item 
                                           FROM
                                               serviciopresupuesto s
                                               INNER JOIN cotizaciones c ON c.IdCotizacion = s.IdCotizacion 
                                           WHERE
                                               s.IdServicio =$servicio
                                               AND d_clasificador (
                                               id_estadoobjeto ( 196, c.IdCotizacion ))= 'Adjudicada';";
$stmt = $dbh->prepare($sql);
$stmt->execute();
?>
<table border="1">
  <tr><td>IdCotizacion</td><td>Descuento %</td><td>IdDetServicio</td><td>IdClaServicio</td><td>Cantidad</td><td>PrecioUnitario</td></tr>
<?php
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      echo "<tr><td>".$row["IdCotizacion"]."</td><td>".$row["Descuento"]."</td><td>".$row["IdDetServicio"]."</td><td>".$row["IdClaServicio"]."</td><td>".$row["Cantidad"]."</td><td>".$row["PrecioUnitario"]."</td></tr>";
    }
?></table><?php
echo "<br><br>".$sql."<br><br>";

