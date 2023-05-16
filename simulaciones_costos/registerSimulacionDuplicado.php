<?php

require_once '../conexion.php';
require_once '../functions.php';

try {
  $dbh = new Conexion();
  
  $sqlCommit="SET AUTOCOMMIT=0;";
  $stmtCommit = $dbh->prepare($sqlCommit);
  $stmtCommit->execute();

  //RECIBIMOS Codigo Simulación Servicio
  $codigo=$_POST['codigo'];  // código del registro original que será duplicado
  
  $codSimCosto=obtenerCodigoSimCosto();

  /*******************************************************/
  /*                  SIMULACION COSTOS                  */
  /*******************************************************/
  $sql = "INSERT INTO simulaciones_costos (codigo,nombre,observacion,fecha,cod_plantillacosto,cod_estadosimulacion,cod_responsable,cod_estadoreferencial,ibnorca,cod_precioplantilla,cantidad_alumnoslocal,utilidad_minimalocal,cantidad_cursosmes,cantidad_modulos,monto_norma,habilitado_norma,cod_tipocurso,fecha_curso,dias_curso,IdModulo,IdCurso,cod_area_registro,cod_cliente,fecha_solicitud_cliente,id_lead) 
          SELECT '$codSimCosto',nombre,observacion,fecha,cod_plantillacosto,cod_estadosimulacion,cod_responsable,cod_estadoreferencial,ibnorca,cod_precioplantilla,cantidad_alumnoslocal,utilidad_minimalocal,cantidad_cursosmes,cantidad_modulos,monto_norma,habilitado_norma,cod_tipocurso,fecha_curso,dias_curso,IdModulo,IdCurso,cod_area_registro,cod_cliente,fecha_solicitud_cliente,id_lead
          FROM simulaciones_costos 
          WHERE codigo = :codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
  $stmt->execute();

  /*************************************************/
  /*                  idPropuesta                  */
  /*************************************************/
  // $sqlIbnorca="UPDATE ibnorca.modulos set idPropuesta=$codSimCosto where IdModulo=$IdModulo";
  // $stmtIbnorca = $dbh->prepare($sqlIbnorca);
  // $stmtIbnorca->execute();

  /**************************************************************/
  /*                  SIMULACION COSTOS NORMAS                  */
  /**************************************************************/
  $sql = "INSERT INTO simulaciones_costosnormas (cod_simulacion,cod_norma,precio,cantidad,catalogo) 
          SELECT '$codSimCosto',cod_norma,precio,cantidad,catalogo
          FROM simulaciones_costosnormas 
          WHERE cod_simulacion = :codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
  $stmt->execute();

  /********************************************************/
  /*                  CUENTAS SIMULACION                  */
  /********************************************************/
  $sql = "INSERT INTO cuentas_simulacion (cod_plancuenta,monto_local,monto_externo,porcentaje,cod_partidapresupuestaria,cod_simulacioncostos,cod_simulacionservicios,cod_anio) 
          SELECT cod_plancuenta,monto_local,monto_externo,porcentaje,cod_partidapresupuestaria,'$codSimCosto',cod_simulacionservicios,cod_anio
          FROM cuentas_simulacion 
          WHERE cod_simulacioncostos = :codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
  $stmt->execute();
  
  
  /********************************************************/
  /*                  SIMULACION DETALLE                  */
  /********************************************************/
  $sql = "INSERT INTO simulaciones_detalle (cod_simulacioncosto,cod_plantillatcp,cod_plantillacosto,cod_partidapresupuestaria,cod_cuenta,cod_tipo,glosa,monto_unitario,cantidad,monto_total,unidad,cod_estadoreferencial,habilitado,editado_alumno) 
          SELECT '$codSimCosto',cod_plantillatcp,cod_plantillacosto,cod_partidapresupuestaria,cod_cuenta,cod_tipo,glosa,monto_unitario,cantidad,monto_total,unidad,cod_estadoreferencial,habilitado,editado_alumno
          FROM simulaciones_detalle 
          WHERE cod_simulacioncosto = :codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
  $stmt->execute();
  
  /**************************************************************/
  /*                  PRECIOS SIMULACION COSTO                  */
  /**************************************************************/
  $sql = "INSERT INTO precios_simulacioncosto (venta_local,venta_externo,cod_simulacioncosto) 
          SELECT venta_local,venta_externo,'$codSimCosto'
          FROM precios_simulacioncosto 
          WHERE cod_simulacioncosto = :codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
  $stmt->execute();

  /***************************************************/
  /*                  SIMULACION CF                  */
  /***************************************************/
  $sql = "INSERT INTO simulaciones_cf (cod_simulacionservicio,cod_simulacioncosto,cod_partidapresupuestaria,cod_cuenta,monto,cantidad,monto_total,cod_anio) 
          SELECT cod_simulacionservicio,'$codSimCosto',cod_partidapresupuestaria,cod_cuenta,monto,cantidad,monto_total,cod_anio
          FROM simulaciones_cf 
          WHERE cod_simulacioncosto = :codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
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