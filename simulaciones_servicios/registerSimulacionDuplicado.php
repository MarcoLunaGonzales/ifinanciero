<?php

require_once '../conexion.php';

try {
  $dbh = new Conexion();

  //RECIBIMOS Codigo Simulación Servicio
  $codigo=$_POST['codigo'];  // código del registro original que será duplicado
  
  /* Datos simulacion servicios */
  /*************************************************************/
  /*                  SIMULACION DE SERVICIOS                  */
  /*************************************************************/
  $sql = "INSERT INTO simulaciones_servicios (nombre,observacion,fecha,cod_plantillaservicio,cod_estadosimulacion,cod_responsable,cod_estadoreferencial,ibnorca,dias_auditoria,utilidad_minima,cod_cliente,productos,norma,idServicio,anios,porcentaje_fijo,sitios,afnor,porcentaje_afnor,id_tiposervicio,cod_objetoservicio,cod_tipoclientenacionalidad,entrada,cod_iaf_primario,cod_iaf_secundario,estado_registro,alcance_propuesta,ingreso_presupuestado,descripcion_servicio,cod_unidadorganizacional,cod_tipocliente,cod_responsableactual,fecha_solicitud_cliente) 
          SELECT nombre,observacion,fecha,cod_plantillaservicio,cod_estadosimulacion,cod_responsable,cod_estadoreferencial,ibnorca,dias_auditoria,utilidad_minima,cod_cliente,productos,norma,idServicio,anios,porcentaje_fijo,sitios,afnor,porcentaje_afnor,id_tiposervicio,cod_objetoservicio,cod_tipoclientenacionalidad,entrada,cod_iaf_primario,cod_iaf_secundario,estado_registro,alcance_propuesta,ingreso_presupuestado,descripcion_servicio,cod_unidadorganizacional,cod_tipocliente,cod_responsableactual,fecha_solicitud_cliente 
          FROM simulaciones_servicios 
          WHERE codigo = :codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
  // $stmt->bindParam(':nuevo_valor', $nuevo_valor);
  $flagSuccess = $stmt->execute();

  // NUEVO CODIGO PROPUESTA DE SERVICIO
  $nuevo_cod_simulacionservicio = $dbh->lastInsertId();
  
  /*******************************************************/
  /*                  SIMULACION DE IAF                  */
  /*******************************************************/
  $sql = "INSERT INTO simulaciones_servicios_iaf (cod_simulacionservicio,cod_iaf) 
          SELECT '$nuevo_cod_simulacionservicio', cod_iaf
          FROM simulaciones_servicios_iaf 
          WHERE cod_simulacionservicio = :codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
  $flagSuccess=$stmt->execute();
  
  /********************************************************************/
  /*                  SIMULACION CATEGORIA INOCUIDAD                  */
  /********************************************************************/
  $sql = "INSERT INTO simulaciones_servicios_categoriasinocuidad (cod_simulacionservicio,cod_categoriainocuidad) 
          SELECT '$nuevo_cod_simulacionservicio',cod_categoriainocuidad
          FROM simulaciones_servicios_categoriasinocuidad 
          WHERE cod_simulacionservicio = :codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
  $flagSuccess=$stmt->execute();

  /****************************************************************/
  /*         SIMULACION SERVICIO ORGANIZADOR CERTIFICADOR         */
  /****************************************************************/
  $sql = "INSERT INTO simulaciones_servicios_organismocertificador (cod_simulacionservicio,cod_orgnismocertificador) 
          SELECT '$nuevo_cod_simulacionservicio',cod_orgnismocertificador
          FROM simulaciones_servicios_organismocertificador 
          WHERE cod_simulacionservicio = :codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
  $flagSuccess=$stmt->execute();

  /****************************************************************/
  /*         SIMULACION SERVICIO ORGANIZADOR CERTIFICADOR         */
  /****************************************************************/
  $sql = "INSERT INTO simulaciones_servicios_organismocertificador (cod_simulacionservicio,cod_orgnismocertificador) 
          SELECT '$nuevo_cod_simulacionservicio',cod_orgnismocertificador
          FROM simulaciones_servicios_organismocertificador 
          WHERE cod_simulacionservicio = :codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
  $flagSuccess=$stmt->execute();

  /**********************************************/
  /*         SIMULACION SERVICIO NORMAS         */
  /**********************************************/
  $sql = "INSERT INTO simulaciones_servicios_normas (cod_simulacionservicio,cod_tiposervicio,cod_norma,observaciones) 
    SELECT '$nuevo_cod_simulacionservicio',cod_tiposervicio,cod_norma,observaciones
    FROM simulaciones_servicios_normas 
    WHERE cod_simulacionservicio = :codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
  $flagSuccess=$stmt->execute();

  /**************************************/
  /*         SIMULACION CUENTAS         */
  /**************************************/
  $sql = "INSERT INTO cuentas_simulacion (cod_plancuenta,monto_local,monto_externo,porcentaje,cod_partidapresupuestaria,cod_simulacioncostos,cod_simulacionservicios,cod_anio) 
    SELECT cod_plancuenta,monto_local,monto_externo,porcentaje,cod_partidapresupuestaria,cod_simulacioncostos,'$nuevo_cod_simulacionservicio',cod_anio
    FROM cuentas_simulacion 
    WHERE cod_simulacionservicios = :codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
  $flagSuccess=$stmt->execute();
  
  /*************************************************/
  /*         SIMULACION SERVICIO AUDITORES         */
  /*************************************************/
  $sql = "INSERT INTO simulaciones_servicios_auditores (cod_simulacionservicio,cod_tipoauditor,cantidad,monto,cod_estadoreferencial,habilitado,cantidad_editado,dias,monto_externo,cod_externolocal,cod_anio,descripcion)
    SELECT '$nuevo_cod_simulacionservicio',cod_tipoauditor,cantidad,monto,cod_estadoreferencial,habilitado,cantidad_editado,dias,monto_externo,cod_externolocal,cod_anio,descripcion
    FROM simulaciones_servicios_auditores 
    WHERE cod_simulacionservicio = :codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
  $flagSuccess=$stmt->execute();
  
  /**************************************/
  /*         SIMULACION SSD SSA         */
  /**************************************/
  $sql = "INSERT INTO simulaciones_ssd_ssa (cod_simulacionservicio,cod_simulacionserviciodetalle,cod_simulacionservicioauditor,monto,dias,cantidad,monto_externo,cod_anio)
    SELECT '$nuevo_cod_simulacionservicio',cod_simulacionserviciodetalle,cod_simulacionservicioauditor,monto,dias,cantidad,monto_externo,cod_anio
    FROM simulaciones_ssd_ssa 
    WHERE cod_simulacionservicio = :codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
  $flagSuccess=$stmt->execute();
  
  /***********************************************/
  /*         SIMULACION SERVICIO DETALLE         */
  /***********************************************/
  $sql = "INSERT INTO simulaciones_serviciodetalle (cod_simulacionservicio,cod_plantillatcp,cod_plantillacosto,cod_partidapresupuestaria,cod_cuenta,cod_tipo,glosa,monto_unitario,cantidad,monto_total,unidad,cod_estadoreferencial,habilitado,editado_personal,editado_personalext,monto_totalext,cod_externolocal,cod_anio)
    SELECT '$nuevo_cod_simulacionservicio',cod_plantillatcp,cod_plantillacosto,cod_partidapresupuestaria,cod_cuenta,cod_tipo,glosa,monto_unitario,cantidad,monto_total,unidad,cod_estadoreferencial,habilitado,editado_personal,editado_personalext,monto_totalext,cod_externolocal,cod_anio
    FROM simulaciones_serviciodetalle 
    WHERE cod_simulacionservicio = :codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
  $flagSuccess=$stmt->execute();
  
  /*********************************/
  /*         SIMULACION CF         */
  /*********************************/
  $sql = "INSERT INTO simulaciones_cf (cod_simulacionservicio,cod_simulacioncosto,cod_partidapresupuestaria,cod_cuenta,monto,cantidad,monto_total,cod_anio)
    SELECT '$nuevo_cod_simulacionservicio',cod_simulacioncosto,cod_partidapresupuestaria,cod_cuenta,monto,cantidad,monto_total,cod_anio
    FROM simulaciones_cf 
    WHERE cod_simulacionservicio = :codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
  $flagSuccess=$stmt->execute();
  
  /*****************************************************/
  /*         SIMULACION SERVICIO TIPO SERVICIO         */
  /*****************************************************/
  $sql = "INSERT INTO simulaciones_servicios_tiposervicio (cod_simulacionservicio,cod_claservicio,observaciones,cantidad,monto,cod_estadoreferencial,habilitado,cantidad_editado,cod_tipounidad,cod_anio)
    SELECT '$nuevo_cod_simulacionservicio',cod_claservicio,observaciones,cantidad,monto,cod_estadoreferencial,habilitado,cantidad_editado,cod_tipounidad,cod_anio
    FROM simulaciones_servicios_tiposervicio 
    WHERE cod_simulacionservicio = :codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
  $flagSuccess=$stmt->execute();
  
  /************************************************/
  /*         SIMULACION SERVICIO ATRIBUTO         */
  /************************************************/
  $sql = "SELECT codigo FROM simulaciones_servicios_atributos WHERE cod_simulacionservicio = :codigo";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
  $stmt->execute();
  
  $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  // Iterar sobre los resultados
  foreach ($resultados as $resultado) {
      // Codigo Antiguo Atributo
      $codigo_atributo_antiguo = $resultado['codigo'];
      
      $sql = "INSERT INTO simulaciones_servicios_atributos (cod_simulacionservicio,nombre,direccion,cod_tipoatributo,habilitado,marca,norma,nro_sello,cod_ciudad,cod_estado,cod_pais)
        SELECT '$nuevo_cod_simulacionservicio',nombre,direccion,cod_tipoatributo,habilitado,marca,norma,nro_sello,cod_ciudad,cod_estado,cod_pais
        FROM simulaciones_servicios_atributos 
        WHERE cod_simulacionservicio = :codigo";
      $stmt = $dbh->prepare($sql);
      $stmt->bindParam(':codigo', $codigo);
      $flagSuccess=$stmt->execute();
      // Codigo Nuevo Atributo
      $nuevo_cod_atributo = $dbh->lastInsertId();
      
      /************************************************/
      /*         SIMULACION SERVICIO ATRIBUTO         */
      /************************************************/
      $sql = "INSERT INTO simulaciones_servicios_atributosnormas (cod_simulacionservicioatributo,cod_norma,precio,cantidad)
        SELECT '$nuevo_cod_atributo',cod_norma,precio,cantidad
        FROM simulaciones_servicios_atributosnormas 
        WHERE cod_simulacionservicioatributo = :codigo";
      $stmt = $dbh->prepare($sql);
      $stmt->bindParam(':codigo', $codigo_atributo_antiguo);
      $flagSuccess=$stmt->execute();
      
      /************************************************/
      /*         SIMULACION SERVICIO ATRIBUTO         */
      /************************************************/
      $sql = "INSERT INTO simulaciones_servicios_atributosdias (cod_simulacionservicioatributo,dias,cod_anio)
        SELECT '$nuevo_cod_atributo',dias,cod_anio
        FROM simulaciones_servicios_atributosdias 
        WHERE cod_simulacionservicioatributo = :codigo";
      $stmt = $dbh->prepare($sql);
      $stmt->bindParam(':codigo', $codigo_atributo_antiguo);
      $flagSuccess=$stmt->execute();
      
      /************************************************/
      /*         SIMULACION SERVICIO ATRIBUTO         */
      /************************************************/
      $sql = "INSERT INTO simulaciones_servicios_atributosauditores (cod_simulacionservicioatributo,cod_auditor,cod_anio,estado)
        SELECT '$nuevo_cod_atributo',cod_auditor,cod_anio,estado
        FROM simulaciones_servicios_atributosauditores 
        WHERE cod_simulacionservicioatributo = :codigo";
      $stmt = $dbh->prepare($sql);
      $stmt->bindParam(':codigo', $codigo_atributo_antiguo);
      $flagSuccess=$stmt->execute();
  }

  echo json_encode(array(
      'status'  => true,
  ));
} catch (\Throwable $th) {
  echo json_encode(array(
      'status' => false
  ));
}
?>