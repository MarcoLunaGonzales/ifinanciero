<?php

require_once '../conexion.php';
require_once '../functions.php';

session_start();

try {
  $dbh = new Conexion();
  
  $sqlCommit="SET AUTOCOMMIT=0;";
  $stmtCommit = $dbh->prepare($sqlCommit);
  $stmtCommit->execute();

  // * RECIBIMOS Codigo Simulación Servicio
  // Código del registro original que será duplicado
  $codigo         = $_POST['codigo'];  
  $tipo_duplicado = empty($_POST['tipo']) ? 0 : $_POST['tipo'];
  
  $codSimCosto=obtenerCodigoSimCosto();

    /*===========================================================/
    /*         MODIFICACIÓN DE ESTADO EN ESTADO IBNORCA         */
    /*==========================================================*/
    $sql = "SELECT sc.codigo, sc.nro_version, 
                (SELECT oc.idOfertaContrato
                FROM ibnorca.ofertacontrato oc
                INNER JOIN bdifinanciero.simulaciones_costos ssc ON ssc.Codigo=oc.idPropuesta
                WHERE ssc.Codigo=sc.codigo AND ibnorca.id_estadoobjeto(4510, oc.idOfertaContrato)<4517) as id_oferta
            FROM simulaciones_costos sc
            WHERE sc.cod_version = (SELECT ssc.cod_version FROM simulaciones_costos ssc WHERE codigo = $codigo LIMIT 1)
            AND sc.estado_version = 1
            LIMIT 1";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // verificación de IdPropuesta y verifica tipo de duplicado "0:Generar nueva versión"
    if(!empty($row['id_oferta']) && $tipo_duplicado == 0){
        // Detalle
        $codigo_activo = $row['codigo'];
        $nro_version   = $row['nro_version'];

        $idUsuario = empty($_SESSION["idUsuario"]) ? 0 : $_SESSION["idUsuario"];
        $sql = "INSERT INTO ibnorca.estadoobjeto(IdTipoObjeto,IdEstado,idResponsable,IdObjeto,FechaEstado,Observaciones) VALUES (4510,4761,$idUsuario,(SELECT oc.idOfertaContrato FROM ibnorca.ofertacontrato oc WHERE oc.idPropuesta=$codigo_activo LIMIT 1),now(),'Cambio de estado Automático por ajuste de propuesta. Codigo Propuesta: $codigo_activo, Codigo Versión:  $nro_version')";
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
    }

  // TIPO 0: Duplicado con dependencia
  // TIPO 1: Duplicado sin dependencia
  if($tipo_duplicado == 0){
        /*---------------------------------------------*/
        /*-------DESACTIVAMOS VERSIÓN ANTERIOR---------*/
        /*---------------------------------------------*/
        $sqlOld="UPDATE simulaciones_costos AS sc1
        JOIN (
            SELECT cod_version
            FROM simulaciones_costos
            WHERE codigo = '$codigo'
            LIMIT 1
        ) AS sc2 ON sc1.cod_version = sc2.cod_version
        SET sc1.estado_version = 0,
        sc1.cod_estadosimulacion = 6"; // Estado * Reemplazado por ajuste
        $stmtOld = $dbh->prepare($sqlOld);
        $stmtOld->execute();
        /*********************************************/
        /*     idPropuesta  IBNORCA - ACTUALIZAR     */
        /*********************************************/
        $sqlIbnorca="UPDATE ibnorca.modulos SET idPropuesta = $codSimCosto WHERE IdModulo=(SELECT IdModulo FROM simulaciones_costos WHERE codigo = $codigo LIMIT 1)";
        $stmtIbnorca = $dbh->prepare($sqlIbnorca);
        $stmtIbnorca->execute();
  }

  /*******************************************************/
  /*                  SIMULACION COSTOS                  */
  /*******************************************************/
  $sql = "INSERT INTO simulaciones_costos (codigo,nombre,observacion,fecha,cod_plantillacosto,cod_estadosimulacion,cod_responsable,cod_estadoreferencial,ibnorca,cod_precioplantilla,cantidad_alumnoslocal,utilidad_minimalocal,cantidad_cursosmes,cantidad_modulos,monto_norma,habilitado_norma,cod_tipocurso,fecha_curso,dias_curso,IdModulo,IdCurso,cod_area_registro,cod_cliente,fecha_solicitud_cliente,id_lead,cod_version,nro_version) 
          SELECT '$codSimCosto',ssc.nombre,ssc.observacion,'".date('Y-m-d')."',ssc.cod_plantillacosto,1,ssc.cod_responsable,ssc.cod_estadoreferencial,ssc.ibnorca,ssc.cod_precioplantilla,ssc.cantidad_alumnoslocal,ssc.utilidad_minimalocal,ssc.cantidad_cursosmes,ssc.cantidad_modulos,ssc.monto_norma,ssc.habilitado_norma,ssc.cod_tipocurso,ssc.fecha_curso,ssc.dias_curso,". ($tipo_duplicado == 0 ? "ssc.IdModulo" : "''" ) .",ssc.IdCurso,ssc.cod_area_registro,ssc.cod_cliente,ssc.fecha_solicitud_cliente,ssc.id_lead, ". ($tipo_duplicado == 0 ? "cod_version" : "$codSimCosto" ) .", ". ($tipo_duplicado == 0 ? "((SELECT COALESCE(MAX(sc.nro_version), 0) FROM simulaciones_costos sc WHERE sc.cod_version = ssc.cod_version) + 1)" : "1" ) ."
          FROM simulaciones_costos ssc
          WHERE ssc.codigo = :codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
  $stmt->execute();


  // TIPO 0: Duplicado con dependencia
  // TIPO 1: Duplicado sin dependencia
//   if($tipo_duplicado == 0){
//         $sqlIbnorca="UPDATE ibnorca.modulos SET idPropuesta = $codSimCosto WHERE IdModulo=(SELECT IdModulo FROM simulaciones_costos WHERE codigo = $codigo LIMIT 1)";
//         $stmtIbnorca = $dbh->prepare($sqlIbnorca);
//         $stmtIbnorca->execute();
//   }

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