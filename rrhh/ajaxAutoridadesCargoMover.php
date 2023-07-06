<?php
require_once '../conexion.php';

// Recuperación de Datos
$cod_cargo_mover = $_POST['cod_cargo_mover'];
$cod_cargo       = $_POST['cod_cargo'];
try {
  $dbh = new Conexion();
  $sqlCommit="SET AUTOCOMMIT=0;";
  $stmtCommit = $dbh->prepare($sqlCommit);
  $stmtCommit->execute();

  /*********************************************/
  /*    MOVER DE AUTORIDADES DE CARGO    */
  /*********************************************/
  $sql = "UPDATE cargos_autoridades 
          SET cod_cargo = '$cod_cargo_mover'
          WHERE cod_cargo = '$cod_cargo'
          AND cod_estadoautoridad = 1";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  // Commit de la transacción si no hay errores
  $sqlCommit="COMMIT;SET AUTOCOMMIT=1;";
  $stmtCommit = $dbh->prepare($sqlCommit);
  $stmtCommit->execute();
  
  echo json_encode(array(
      'status'  => true,
  ));
} catch (\Throwable $th) {
  // Rollback en caso de excepción
  $sqlRolBack="ROLLBACK;";
  $stmtRolBack = $dbh->prepare($sqlRolBack);
  $stmtRolBack->execute();
  $sqlCommit="COMMIT;SET AUTOCOMMIT=1;";
  $stmtCommit = $dbh->prepare($sqlCommit);
  $stmtCommit->execute();
  echo json_encode(array(
      'status' => false,
      'error'  => $th->getMessage()
  ));
}
?>