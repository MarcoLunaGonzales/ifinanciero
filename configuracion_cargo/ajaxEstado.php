<?php
require_once '../conexion.php';
date_default_timezone_set('America/La_Paz');

// Recuperación de Datos
$codigo = $_POST['codigo'];
try {
  $dbh = new Conexion();
  $sqlCommit="SET AUTOCOMMIT=0;";
  $stmtCommit = $dbh->prepare($sqlCommit);
  $stmtCommit->execute();

  $fecha_actual = date('Y-m-d');

  /***********************************/
  /*    ACTUALIZA REGISTRO ESTADO    */
  /***********************************/
  $sql = "UPDATE aprobacion_configuraciones_cargos 
          SET cod_estadoaprobacion = 
          CASE 
            WHEN codigo = '$codigo' 
              THEN 3 
              ELSE 1 
          END";
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