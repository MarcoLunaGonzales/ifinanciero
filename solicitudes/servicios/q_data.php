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
     
$sql="UPDATE estadoobjeto set idResponsable=90 where IdEstadoObjeto=387751";
$stmt = $dbh->prepare($sql);
$flagsuccess=$stmt->execute();

echo $sql."<br><br>";
echo "Query:".$flagsuccess;
