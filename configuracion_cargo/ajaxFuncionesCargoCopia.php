<?php
require_once '../conexion.php';

// Recuperación de Datos
$cod_cargo_copia = $_POST['cod_cargo_copia'];
$cod_cargo       = $_POST['cod_cargo'];
$cod_config_copia= $_POST['cod_config_copia'];
$cod_config_actual= $_POST['cod_config_actual'];
try {
  $dbh = new Conexion();
  $sqlCommit="SET AUTOCOMMIT=0;";
  $stmtCommit = $dbh->prepare($sqlCommit);
  $stmtCommit->execute();

  /*********************************************/
  /*    COPIA DE RESPONSABILIDADES DE CARGO    */
  /*********************************************/
  $sql = "INSERT INTO cargos_funciones (cod_cargo,nombre_funcion,peso,cod_estado,cod_configuracion)  
          SELECT '$cod_cargo',nombre_funcion,peso,cod_estado,'$cod_config_actual'
          FROM cargos_funciones 
          WHERE cod_cargo = '$cod_cargo_copia'
          AND cod_configuracion = '$cod_config_copia'
          AND cod_estado = 1";
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