<?php

require_once '../conexion.php';

$dbh = new Conexion();

try {
  
  $sqlCommit="SET AUTOCOMMIT=0;";
  $stmtCommit = $dbh->prepare($sqlCommit);
  $stmtCommit->execute();

  // DATAS
  $array_sf         = explode(',', $_POST['array_sf']);
  $array_sf_detalle = $array_sf;
  // SF Antigua para realizar la fusión de Facturas
  $sf_antigua = min($array_sf);
  // Encuentra la clave del elemento más pequeño
  $minKey = array_search(min($array_sf), $array_sf);
  // Elimina el elemento más pequeño del array
  unset($array_sf[$minKey]);
  
  /*===============================================================================================*/
  // 1. Actualiza SF para inhabilitar las que serán fusionadas a la ANTIGUA
  // también se debe tomar en cuenta el cambio en el campo de "observación" donde se notifique
  // el nuevo registor donde se FUSIONA y tambien "cod_estadosolicitudfacturacion"
  $placeholders = implode(', ', $array_sf);
  $texto = ", Fusionado en la SF: Codigo Principal $sf_antigua";
  $sql = "UPDATE solicitudes_facturacion SET observaciones = CONCAT(observaciones, :texto), cod_estadosolicitudfacturacion = 2 WHERE codigo IN ($placeholders)";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':texto', $texto);
  $stmt->execute();
  // 2. Actualizar la SF ANTIGUA en "observación" con los codigos de las SF que se adjuntan para
  // la fusión
  $texto = ", SF fucionada Nros_relativos: $placeholders";
  $sql = "UPDATE solicitudes_facturacion SET observaciones = CONCAT(observaciones, :texto) WHERE codigo = '$sf_antigua'";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':texto', $texto);
  $stmt->execute();
  /*===============================================================================================*/

  /*===============================================================================================*/
  // 3. Actualizar Detalle SF cod_solicitudfacturacion con el codigo SF ANTIGUA
  $sql = "UPDATE solicitudes_facturaciondetalle SET cod_solicitudfacturacion = '$sf_antigua' WHERE cod_solicitudfacturacion IN ($placeholders)";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':texto', $texto);
  $stmt->execute();
  /*===============================================================================================*/

  /*===============================================================================================*/
  // DETALLE DE SUMA TOTAL DE "SOLICITUD_FACTURACION_DETALLE"
  $sql = "SELECT SUM((precio*cantidad)-descuento_bob) as suma_total FROM solicitudes_facturaciondetalle WHERE cod_solicitudfacturacion = '$sf_antigua' limit 1";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $registro = $stmt->fetch(PDO::FETCH_ASSOC);
  $suma_total = $registro['suma_total'];
  // 4. Actualizar solicitudes_facturacion_areas
  $sql = "UPDATE solicitudes_facturacion_areas SET monto = ROUND((porcentaje / 100) * $suma_total, 2) WHERE cod_solicitudfacturacion = '$sf_antigua'";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  // 5. Actualizar solicitudes_facturacion_areas_uo
  $sql = "UPDATE solicitudes_facturacion_areas_uo SET monto = ROUND((porcentaje / 100) * $suma_total, 2) WHERE cod_solicitudfacturacion = '$sf_antigua'";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  // 6. Actualizar solicitudes_facturacion_tipospago
  $sql = "UPDATE solicitudes_facturacion_tipospago SET monto = ROUND((porcentaje / 100) * $suma_total, 2) WHERE cod_solicitudfacturacion = '$sf_antigua'";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  /*===============================================================================================*/

  // Commit de la transacción si no hay errores
  $sqlCommit="COMMIT;SET AUTOCOMMIT=1;";
  $stmtCommit = $dbh->prepare($sqlCommit);
  $stmtCommit->execute();

  // Enviar respuesta en formato JSON
  echo json_encode(array(
      'message' => 'FUSIÓN DE REGISTRO CORRECTO!',
      'status' => true
  ));
} catch (Exception $e) {
  // Rollback en caso de excepción
  $sqlRolBack="ROLLBACK;";
  $stmtRolBack = $dbh->prepare($sqlRolBack);
  $stmtRolBack->execute();
  $sqlCommit="COMMIT;SET AUTOCOMMIT=1;";
  $stmtCommit = $dbh->prepare($sqlCommit);
  $stmtCommit->execute();

  // Enviar respuesta en formato JSON con mensaje de error
  echo json_encode(array(
      'message' => 'ERROR DE REGISTRO: ' . $e->getMessage(),
      'status' => false
  ));
}

?>