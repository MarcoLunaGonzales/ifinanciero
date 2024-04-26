<?php 
	
	if(isset($_GET['opcion'])){
         /*crear servicio en base de datos ibnorca*/
         if ($_GET['opcion']=='crearServicioIbnorca') {
			require_once('simulaciones_servicios/crearServicioIbnorca.php');
		}

		//*********************************************   CONTABILIDAD BASICA         ********************************
		//PLAN DE CUENTAS
		if ($_GET['opcion']=='listPlanCuentas') {
			require_once('plan_cuentas/list_single.php');
		}
		if ($_GET['opcion']=='registerPlanCuenta') {
			$codigo=$_GET['codigo'];
			require_once('plan_cuentas/register.php');
		}
		if ($_GET['opcion']=='editPlanCuenta') {
			$codigo=$_GET['codigo'];
			require_once('plan_cuentas/edit.php');
		}
		if ($_GET['opcion']=='deletePlanCuenta') {
			$codigo=$_GET['codigo'];
			require_once('plan_cuentas/saveDelete.php');
		}
		if ($_GET['opcion']=='listConfigCuentas') {
			require_once('configuracion_cuentas/list.php');
		}

		if ($_GET['opcion']=='listCuentasAux') {
			$codigo=$_GET['codigo'];
			require_once('cuentas_auxiliares/list2.php');
		}
		if ($_GET['opcion']=='registerCuentaAux') {
			$codigo=$_GET['codigo'];
			require_once('cuentas_auxiliares/register2.php');
		}
		if ($_GET['opcion']=='actualizarCuentaAux') {
			$codigo=$_GET['codigo'];
			require_once('cuentas_auxiliares/actualizarCuentaAux.php');
		}
		if ($_GET['opcion']=='editCuentaAux') {
			$codigo=$_GET['codigo'];
			$codigo_padre=$_GET['codigo_padre'];
			require_once('cuentas_auxiliares/edit2.php');
		}
		if ($_GET['opcion']=='deleteCuentaAux') {
			$codigo=$_GET['codigo'];
			$codigo_padre=$_GET['codigo_padre'];
			require_once('cuentas_auxiliares/saveDelete.php');
		}



		//PARTIDAS PRESUPUESTARIAS
		if ($_GET['opcion']=='listPartidasPres') {
			require_once('partidas_presupuestarias/list.php');
		}
		if ($_GET['opcion']=='registerPartidaPres') {
			require_once('partidas_presupuestarias/register.php');
		}
		if ($_GET['opcion']=='editPartidaPres') {
			$codigo=$_GET['codigo'];
			require_once('partidas_presupuestarias/edit.php');
		}
		if ($_GET['opcion']=='deletePartidaPres') {
			$codigo=$_GET['codigo'];
			require_once('partidas_presupuestarias/saveDelete.php');
		}			
		if ($_GET['opcion']=='registerOfCuenta') {
			$codigo=$_GET['codigo'];
			require_once('partidas_presupuestarias/registerOfCuenta.php');
		}
		//entidades	
		if ($_GET['opcion']=='listEntidades') {
			require_once('partidas_presupuestarias/listEntidades.php');
		}
		if ($_GET['opcion']=='registerEntidades') {
			$codigo=$_GET['codigo'];
			require_once('partidas_presupuestarias/registerEntidades.php');
		}
		if ($_GET['opcion']=='entidadesSave') {
			require_once('partidas_presupuestarias/saveEntidades.php');
		}
		if ($_GET['opcion']=='deleteEntidad') {
			$codigo=$_GET['codigo'];
			require_once('partidas_presupuestarias/saveDeleteEntidad.php');
		}
		if ($_GET['opcion']=='registerUnidadesEntidad') {
			$codigo=$_GET['codigo'];
			require_once('partidas_presupuestarias/registerUnidadesEntidad.php');
		}
		if ($_GET['opcion']=='saveUnidadesEntidad') {
			require_once('partidas_presupuestarias/SaveUnidadesEntidad.php');
		}	

		
		//COMPROBANTES
		if ($_GET['opcion']=='listComprobantes') {
			require_once('comprobantes/list.php');
		}
		if ($_GET['opcion']=='listComprobantes2') {
			require_once('comprobantes/list2.php');
		}
		if ($_GET['opcion']=='listComprobantesSis') {
			require_once('comprobantes/listComproSis.php');
		}

		if ($_GET['opcion']=='listComprobantesRegistrados') {
			require_once('comprobantes/listRegistrados.php');
		}
		if ($_GET['opcion']=='editComprobante') {
			$codigo=$_GET['codigo'];
			require_once('comprobantes/edit_prueba.php');
		}
		if ($_GET['opcion']=='registerComprobante') {
			require_once('comprobantes/register.php');
		}
		if ($_GET['opcion']=='deleteComprobante') {
			$codigo=$_GET['codigo'];
			require_once('comprobantes/saveDelete.php');
		}
        
        //REPORTES
		if ($_GET['opcion']=='reportesComprobantes') {
			require_once('reportes/reportesComprobantes.php');
		}
		//REPORTES
		if ($_GET['opcion']=='reportesMayores') {
			require_once('reportes/reportesMayores.php');
		}
		if ($_GET['opcion']=='reportesMayoresDatos') {
			require_once('reportes/reportesMayoresExcel.php');
		}
		if ($_GET['opcion']=='reportesMayoresVerificacion') {
			require_once('reportes/reportesMayoresVerificacion.php');
		}
		//REPORTES
		if ($_GET['opcion']=='reportesLibroCompras') {
			require_once('reportes/reportesLibroCompras.php');
		}
		if ($_GET['opcion']=='reportesLibroComprasMeses') {
			require_once('reportes/reportesLibroComprasMeses.php');
		}
		
		//REPORTES
		if ($_GET['opcion']=='reportesLibroComprasProy') {
			require_once('reportes_compras/reportesLibroComprasProy.php');
		}
		//REPORTES
		if ($_GET['opcion']=='reportesLibroComprasProyRevision') {
			require_once('reportes_compras/reportesLibroComprasProyRevision.php');
		}
		//REPORTES
		if ($_GET['opcion']=='reportesLibroComprasEdit') {
			require_once('reportes_compras_edit_factura/reportesLibroComprasEdit.php');
		}
		//REPORTES
		if ($_GET['opcion']=='reportesFacturasAdministrativo') {
			require_once('reportes_ventas_administrativo/reportesLibroVentasAdministrativo.php');
		}
		//REPORTES
		if ($_GET['opcion']=='reportesLibroVentas') {
			require_once('reportes/reportesLibroVentas.php');
		}
		//REPORTES
		if ($_GET['opcion']=='reportesEstadoCuentas') {
			require_once('reportes/reportesEstadoCuentas.php');
		}
		if ($_GET['opcion']=='reportesEstadoCuentasMigrar') {
			require_once('reportes/reportesEstadoCuentasMigrar.php');
		}
        
        //REPORTES
		if ($_GET['opcion']=='reportesBalanceGeneral') {
			require_once('reportes/reportesBalanceGeneral.php');
		}
		if ($_GET['opcion']=='reportesBalanceGeneralBK') {
			$bkLink=1;
			require_once('reportes/reportesBalanceGeneral.php');
		}
        
        if ($_GET['opcion']=='reportesEstadoResultados') {
			require_once('reportes/reportesEstadoResultados.php');
		}
        
        if ($_GET['opcion']=='reportesLibretasBancarias') {
			require_once('reportes/reportesLibretasBancarias.php');
		}
		if ($_GET['opcion']=='reportesLibretasBancarias2') {
			require_once('reportes/libretasBancarias_from2.php');
		}

		//REPORTES
		if ($_GET['opcion']=='reporteAdminEstadoCuentas') {
			require_once('reportes_internos/reportesEstadoCuentasAdmin.php');
		}
  
        //Contabilizacion de Libretas
        if ($_GET['opcion']=='contabilizarLibretasBancarias') {
			require_once('libretas_bancarias/contaLibretasBancarias.php');
		}

		if ($_GET['opcion']=='contabilizarLibretasBancarias_lista') {
			require_once('libretas_bancarias/contabilizacionLibretasBancarias_lista.php');
		}
		if ($_GET['opcion']=='contabilizarLibretasBancarias_listadetalle') {
			require_once('libretas_bancarias/contabilizacionLibretasBancarias_lista_detalle.php');
		}

 		//TIPO DE CAMBIO
		if ($_GET['opcion']=='tipoDeCambio') {
			require_once('tipos_cambios/list.php');
		}
		if ($_GET['opcion']=='deleteTipoCambio') {
			require_once('tipos_cambios/deleteTipoCambioHoy.php');
		}



		//PLANTILLAS DE COSTO
		if ($_GET['opcion']=='listPlantillasCostos') {
			require_once('plantillas_costos/list.php');
		}
		if ($_GET['opcion']=='registerPlantillaCosto') {
			require_once('plantillas_costos/register2.php');
		}
		if ($_GET['opcion']=='deletePlantillaCosto') {
			require_once('plantillas_costos/saveDelete.php');
		}
		if ($_GET['opcion']=='listPlantillasCostosAdmin') {
			require_once('plantillas_costos/listAdmin.php');
		}
		if ($_GET['opcion']=='clonarPlantillaCosto') {
			require_once('plantillas_costos/saveClonar.php');
		}
		if ($_GET['opcion']=='clonarPlantillaCosto2') {
			require_once('plantillas_costos/saveClonar2.php');
		}
        
        //SIMULACIONES DE COSTO
		if ($_GET['opcion']=='listSimulacionesCostos') {
			require_once('simulaciones_costos/list.php');
		}
		if ($_GET['opcion']=='listSimulacionesCostosAdmin') {
			require_once('simulaciones_costos/listAdmin.php');
		}
		if ($_GET['opcion']=='editSimulacion') {
			require_once('simulaciones_costos/editSimulacion.php');
		}
		if ($_GET['opcion']=='deleteSimulacion') {
			require_once('simulaciones_costos/saveDelete.php');
		}
        if ($_GET['opcion']=='registerSimulacion') {
			require_once('simulaciones_costos/registerSimulaciones.php');
		}
        //SIMULACIONES DE COSTO VERSIONES
		if ($_GET['opcion']=='listSimulacionesCostosVersiones') {
			require_once('simulaciones_costos/listVersiones.php');
		}
		
		
		//solicitud facturacion TCP
		if ($_GET['opcion']=='solicitud_facturacion_principal') {
			$v=$_GET['v'];
			$q=$_GET['q'];
			$s=$_GET['s'];
			$u=$_GET['u'];
			require_once('simulaciones_servicios/solicitud_facturacion_principal.php');
		}


		if ($_GET['opcion']=='solicitud_facturacion') {
			$cod=$_GET['cod'];
			require_once('simulaciones_servicios/solicitud_facturacion.php');
		}
		if ($_GET['opcion']=='registerSolicitud_facturacion') {
			$cod_s=$_GET['cod_s'];
			$cod_f=$_GET['cod_f'];
			$cod_sw=$_GET['cod_sw'];
			require_once('simulaciones_servicios/registerSolicitud_facturacion.php');
		}
		if ($_GET['opcion']=='editSolicitud_facturacion') {
			$codigo_s=$_GET['codigo_s'];			
			require_once('simulaciones_servicios/solicitud_facturacion_edit.php');
		}
		if ($_GET['opcion']=='listSolicitud_facturacion_normas') {			
			require_once('solicitud_facturacion_manual/lista_solicitud_facturacion_normas.php');
		}

		if ($_GET['opcion']=='anular_facturaGenerada') {
			$codigo=$_GET['codigo'];
			$cod_solicitudfacturacion=$_GET['cod_solicitudfacturacion'];
			$cod_comprobante=$_GET['cod_comprobante'];
			require_once('simulaciones_servicios/anular_facturaGenerada.php');
		}
		// if ($_GET['opcion']=='anular_SoliciutdFacturacion') {
		// 	$codigo=$_GET['codigo'];
		// 	require_once('simulaciones_servicios/anular_SoliciutdFacturacion.php');
		// }
		// Solicitud facturacion Capacitacion
		if ($_GET['opcion']=='solicitud_facturacion_costos') {			
			require_once('simulaciones_costos/solicitud_facturacion_costos.php');
		}
		if ($_GET['opcion']=='registro_solicitud_facturacion') {
			$codigo=$_GET['codigo'];	
			$cod_simulacion=$_GET['cod_simulacion'];	
			$cod_facturacion=$_GET['cod_facturacion'];			
			$IdCurso=$_GET['IdCurso'];	
			require_once('simulaciones_costos/registro_solicitud_facturacion.php');
		}
		if($_GET['opcion']=='registro_solicitud_facturacion_empresas') {
			$codigo=$_GET['codigo'];	
			$cod_simulacion=$_GET['cod_simulacion'];	
			$cod_facturacion=$_GET['cod_facturacion'];			
			$IdCurso=$_GET['IdCurso'];	
			require_once('simulaciones_costos/registro_solicitud_facturacion_empresas.php');
		}
		// if ($_GET['opcion']=='solicitudfactura_grupal_estudiantes') {
		// 	require_once('simulaciones_costos/solicitud_facturacion_costos_grupal.php');
		// }
		// if ($_GET['opcion']=='save_solicitud_facturacion_costos_empresa') {
		// 	require_once('simulaciones_costos/save_solicitud_facturacion_costos_empresa.php');
		// }
		if ($_GET['opcion']=='listFacturasServicios_costos_estudiantes') {
			require_once('simulaciones_costos/ajax_busqueda_estudiantes.php');
		}
		if ($_GET['opcion']=='lista_facturasEstudiantes') {
			require_once('simulaciones_costos/lista_facturasEstudiantes.php');
		}
		if ($_GET['opcion']=='listFacturasServicios_costos_empresas') {
			require_once('simulaciones_costos/ajax_busqueda_empresas.php');
		}
		//solicitud facturacion costos empresas
		if ($_GET['opcion']=='solicitud_facturacion_costos_empresas') {			
			require_once('simulaciones_costos/solicitud_facturacion_costos_empresas.php');
		}

		if ($_GET['opcion']=='register_solicitudfacturacion_manual') {
			require_once('solicitud_facturacion_manual/register_solicitud_facturacion_manual.php');
		}
        //PLANTILLAS TCP
        if ($_GET['opcion']=='listPlantillasServicios') {
			require_once('plantillas_servicios/list.php');
		}
		if ($_GET['opcion']=='registerPlantillaServicio') {
			require_once('plantillas_servicios/register.php');
		}
		if ($_GET['opcion']=='deletePlantillaServicios') {
			require_once('plantillas_servicios/saveDelete.php');
		}
		if ($_GET['opcion']=='listPlantillasServiciosAdmin') {
			require_once('plantillas_servicios/listAdmin.php');
		}
		if ($_GET['opcion']=='clonarPlantillaServicio') {
			require_once('plantillas_servicios/saveClonar.php');
		}
		if ($_GET['opcion']=='clonarPlantillaServicio2') {
			require_once('plantillas_servicios/saveClonar2.php');
		}
        
        //SIMULACIONES PLANTILLA SERVICIO
        if ($_GET['opcion']=='listSimulacionesServicios') {
			require_once('simulaciones_servicios/list.php');
		}
		if ($_GET['opcion']=='listSimulacionesServiciosTest') {
			require_once('simulaciones_servicios/listProp2.php');
		}
		if ($_GET['opcion']=='listFacturasServicios') {
			require_once('simulaciones_servicios/listFacturasSolicitadas.php');
		}
		if ($_GET['opcion']=='listFacturasServiciosAreas') {
			require_once('simulaciones_servicios/listFacturasSolicitadasArea.php');
		}
		if ($_GET['opcion']=='listFacturasServicios_conta') {
			require_once('simulaciones_servicios/listFacturasSolicitadas_conta.php');
		}
		if ($_GET['opcion']=='configuracion_edit_sf') {
			require_once('utilitarios/configuracion_edit_sf.php');
		}
		if ($_GET['opcion']=='listFacturasServiciosAdmin') {
			require_once('simulaciones_servicios/listFacturasSolicitadasAdmin.php');
		}

		if ($_GET['opcion']=='listFacturasGeneradas') {
			require_once('simulaciones_servicios/listFacturasGeneradas.php');
		}		

        if ($_GET['opcion']=='registerSimulacionServicio') {
			require_once('simulaciones_servicios/registerSimulaciones.php');
		}
		if ($_GET['opcion']=='listSimulacionesServ') {
			require_once('simulaciones_servicios/list.php');
		}
		if ($_GET['opcion']=='listSimulacionesServAdmin') {
			require_once('simulaciones_servicios/listAdmin.php');
		}
		if ($_GET['opcion']=='deleteSimulacionServicio') {
			require_once('simulaciones_servicios/saveDelete.php');
		}
        //solicitud facturaion servicios & presupuestos
        if ($_GET['opcion']=='listServiciosPresupuestos') {
			require_once('servicios_presupuestos/listServiciosPresupuestos.php');
		}
		if ($_GET['opcion']=='registerSolicitud_facturacion_sp') {		
			$IdServicio=$_GET['IdServicio'];
			$cod_facturacion=$_GET['cod_facturacion'];
			$cod_simulacion=$_GET['cod_simulacion'];
			
			require_once('servicios_presupuestos/register_solicitud_facturacion.php');
		}
		// if ($_GET['opcion']=='save_solicitud_facturacion_sp') {
		// 	require_once('servicios_presupuestos/save_solicitud_facturacion.php');
		// }

        //tarifario servicios
        if ($_GET['opcion']=='listTarifarioServicios') {
			require_once('tarifario_servicios/list.php');
		}       
        //MES EN CURSO
        if ($_GET['opcion']=='mesCurso') {
			require_once('mes_curso/list.php');
		}
		if ($_GET['opcion']=='mesCurso2') {
			require_once('mes_curso/list2.php');
		}
		if ($_GET['opcion']=='mesCursoSolicitud') {
			require_once('mes_curso_solicitud/list.php');
		}

		//CAMBIAR GESTION DE TRABAJO
		if ($_GET['opcion']=='listGestionTrabajo') {
			require_once('utilitarios/listGestionTrabajo.php');
		}
		//CAMBIAR GESTION DE TRABAJO
		if ($_GET['opcion']=='listUnidadOrganizacional') {
			require_once('utilitarios/listUnidadOrganizacional.php');
		}

		//notificaciones SISTEMA
        if ($_GET['opcion']=='listNotificacionesSistema') {
			require_once('notificaciones_sistema/list.php');
		}
		if ($_GET['opcion']=='registrarEventoSistema') {
			require_once('notificaciones_sistema/register.php');
		}
       
        if ($_GET['opcion']=='registrarEventoSistemaNot') {
			require_once('notificaciones_sistema/registerEvento.php');
		}
		if ($_GET['opcion']=='deleteNotificacionesSistema') {
			require_once('notificaciones_sistema/saveDelete.php');
		}

		//RETENCIONES
        if ($_GET['opcion']=='configuracionDeRetenciones') {
			require_once('retenciones/list.php');
		}
		if ($_GET['opcion']=='registerRetenciones') {
			require_once('retenciones/register.php');
		}
		if ($_GET['opcion']=='deleteRetenciones') {
			require_once('retenciones/saveDelete.php');
		}
		if ($_GET['opcion']=='editRetenciones') {
			require_once('retenciones/edit.php');
		}
		//SOLICITUD DE RECURSOS
		if ($_GET['opcion']=='listSolicitudRecursos') {
			require_once('solicitudes/listSolicitudRecursos.php');
		}
		if ($_GET['opcion']=='listSolicitudRecursosAdmin') {
			require_once('solicitudes/listSolicitudRecursosAdmin.php');
		}
		if ($_GET['opcion']=='listFacturasServicios_conta_history') {
			require_once('simulaciones_servicios/listFacturasSolicitadas_conta_historico.php');
		}
		if ($_GET['opcion']=='listFacturasGeneradasManuales') {
			require_once('simulaciones_servicios/listFacturasGeneradasManuales.php');
		}
		
        if ($_GET['opcion']=='deleteSolicitudRecursos') {
			require_once('solicitudes/saveDelete.php');
		}
		if ($_GET['opcion']=='editSolicitudRecursos') {
			require_once('solicitudes/editSolicitudRecursos.php');
		}
        if ($_GET['opcion']=='listSolicitudPagosProveedores') {
			require_once('solicitudes/listPagos.php');
		}
		if ($_GET['opcion']=='listPlanCuentasSolicitudesRecursos') {
			require_once('solicitudes/plandecuentas_list.php');
		}
		if ($_GET['opcion']=='registerPlanCuentaSS') {
			$codigo=$_GET['codigo'];
			require_once('solicitudes/plandecuentas_registrer.php');
		}
		if ($_GET['opcion']=='deleteSolicitudRecursosRestart') {
			require_once('solicitudes/saveDeleteRestart.php');
		}

		if ($_GET['opcion']=='cambiarPasivoCuentaSol') {
			require_once('solicitudes/listPasivo.php');
		}
		if ($_GET['opcion']=='listSolicitudFacturacionNormas') {
			require_once('solicitud_facturacion_manual/listSolicitudFacturacionNormas.php');
		}

		if ($_GET['opcion']=='listSolicitudRecursosAdminReg') {
			require_once('solicitudes/listSolicitudRecursosAdminReg.php');
		}
        if ($_GET['opcion']=='listSolicitudRecursosAdminConta') {
			require_once('solicitudes/listSolicitudRecursosAdminConta.php');
		}

		if ($_GET['opcion']=='listSolicitudRecursosAdminSis') {
			require_once('solicitudes/listSolicitudRecursosAdminSis.php');
		}

		if ($_GET['opcion']=='sr_admin_gestion') {
			require_once('solicitudes/listGestor.php');
		}

		if ($_GET['opcion']=='listSolicitudRecursosAdminContaHistorico') {
			require_once('solicitudes/listSolicitudRecursosAdminContaHistorico.php');
		}
		
		if ($_GET['opcion']=='listSolicitudRecursosSisActividad') {
			require_once('solicitudes/listSolicitudRecursosSisActividad.php');
		}
        
        if ($_GET['opcion']=='listSolicitudRecursosAdminContaMenores') {
			require_once('solicitudes/listSolicitudRecursosAdminContaMenores.php');
		}
		if ($_GET['opcion']=='listSolicitudRecursosAdminRegHistorico') {
			require_once('solicitudes/listSolicitudRecursosAdminRegHistorico.php');
		}

		//ESTADOS DE CUENTAS
		if ($_GET['opcion']=='configuracionEstadosCuenta') {
			require_once('estados_cuenta/list.php');
		}
		if ($_GET['opcion']=='registerConfiguracionEstadoCuenta') {
			require_once('estados_cuenta/register.php');
		}
		if ($_GET['opcion']=='editConfiguracionEstadoCuenta') {
			$codigo=$_GET['codigo'];
			require_once('estados_cuenta/edit.php');
		}
		if ($_GET['opcion']=='deleteConfiguracionEstadoCuenta') {
			require_once('estados_cuenta/saveDelete.php');
		}

		//OBLIGACIONES DE PAGO
        
		if ($_GET['opcion']=='listDiasCreditoProveedores') {
			require_once('dias_credito/list.php');
		}
		if ($_GET['opcion']=='registerDiasCreditoProveedor') {
			require_once('dias_credito/register.php');
		}
		if ($_GET['opcion']=='editDiasCredito') {
			require_once('dias_credito/edit.php');
		}
		if ($_GET['opcion']=='deleteDiasCredito') {
			require_once('dias_credito/saveDelete.php');
		}

         //CHEQUES
        if ($_GET['opcion']=='listCheques') {
			require_once('cheques/list.php');
		}
        if ($_GET['opcion']=='registerChequePago') {
			require_once('cheques/register.php');
		}
		if ($_GET['opcion']=='deleteChequePago') {
			require_once('cheques/saveDelete.php');
		}
		if ($_GET['opcion']=='editChequePago') {
			require_once('cheques/edit.php');
		}
        if ($_GET['opcion']=='listChequesEmitidos') {
			require_once('cheques/listEmitidos.php');
		}
		//ENVIOS CORREO INSTANCIAS
		if ($_GET['opcion']=='listInstanciasEnvio') {
			require_once('instancias_envio/list.php');
		}
		if ($_GET['opcion']=='registerInstanciasEnvio') {
			require_once('instancias_envio/register.php');
		}
		if ($_GET['opcion']=='deleteInstanciasEnvio') {
			require_once('instancias_envio/saveDelete.php');
		}
		if ($_GET['opcion']=='editInstanciasEnvio') {
			require_once('instancias_envio/edit.php');
		}

        //LIBRETA BANCARIA
        if ($_GET['opcion']=='listLibreta') {
			require_once('libretas_bancarias/listLibretas.php');
		}

		if ($_GET['opcion']=='registerLibretaBancaria') {
			require_once('libretas_bancarias/register.php');
		}
		if ($_GET['opcion']=='listLibretasDetalle') {
			require_once('libretas_bancarias/listDetalle.php');
		}
		if ($_GET['opcion']=='deleteLibretaBancaria') {
			require_once('libretas_bancarias/saveDelete.php');
		}
		if ($_GET['opcion']=='deleteLibretaBancariaDetalle') {
			require_once('libretas_bancarias/saveDeleteDetalle.php');
		}
		if ($_GET['opcion']=='editLibretaBancaria') {
			require_once('libretas_bancarias/edit.php');
		}
        //PAGOS listPagoProveedor
        if ($_GET['opcion']=='listPagoProveedor') {
			require_once('obligaciones_pago/listPago.php');
		}

		if ($_GET['opcion']=='listPagoProveedores') {
			require_once('obligaciones_pago/lista.php');
		}
		if ($_GET['opcion']=='listPagoProveedoresAdmin') {
			require_once('obligaciones_pago/listaAdmin.php');
		}

		if ($_GET['opcion']=='listObligacionesPago') {
			require_once('obligaciones_pago/list.php');
		}
		if ($_GET['opcion']=='reportePlanificacion') {
			require_once('simulaciones_costos/reportePlan.php');
		}
		if ($_GET['opcion']=='reportePlanificacionCursos') {
			require_once('simulaciones_costos/reportePlanCurso.php');
		}
		if ($_GET['opcion']=='reportePlanificacionEC') {
			require_once('simulaciones_servicios/reportePlan.php');
		}
		if ($_GET['opcion']=='listPagoProveedorLote') {
			require_once('obligaciones_pago/listPagoLote.php');
		}

		if ($_GET['opcion']=='listPagoProveedoresLotes') {
			require_once('obligaciones_pago/listaLotes.php');
		}

		//REPORTES
		if ($_GET['opcion']=='reportesSolicitudRecursosSis') {
			require_once('reportes_solicitud_recursos/reportesSolicitudRecursosSis.php');
		}
        

        if ($_GET['opcion']=='reporteVentasResumido') {
			require_once('reportes/reportesVentaResumido.php');
		}

		if ($_GET['opcion']=='reporteIngresosPorFacturacion') {
			require_once('reportes_ventas/rptOpIngresoFacturacion.php');
		}

		if ($_GET['opcion']=='reportesFlujoEfectivo') {
			require_once('reportes_flujos_efectivo/reportes_flujos_efectivo.php');
		}
		if ($_GET['opcion']=='reportesSumasSaldos') {
			require_once('reportes_sumas_saldos/reportes_sumas_saldos.php');
		}
		if ($_GET['opcion']=='reportesAnalisisFinanciero') {
			require_once('reportes_analisis_financiero/reportes_analisis_financiero.php');
		}

		//solicitudes factuacion
		//  if ($_GET['opcion']=='reporte_solicitudfacturacion_filtro') {
		// 	require_once('reportes_facturacion/filtro_solicitud_facturacion.php');
		// }
		//******************************ACTIVOS FIJOS***********************************************************
		if ($_GET['opcion']=='listUbicaciones') {
			require_once('activosFijos/ubicacionesLista.php');
		}
		if ($_GET['opcion']=='registerUbicacion') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/ubicacionesForm.php');
		}
		if ($_GET['opcion']=='editUbicacion') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/ubicacionesEdit.php');
		}
		if ($_GET['opcion']=='deleteUbicacion') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/saveDelete2.php');
		}
        if ($_GET['opcion']=='saveUbicaciones') {
            require_once('activosFijos/ubicacionesSave.php');
		}
        if ($_GET['opcion']=='saveEditUbicaciones') {
	        require_once('activosFijos/ubicacionesSave.php');
		}

		if ($_GET['opcion']=='afdardebaja_rpt') {
			require_once('reportes_activosfijos/reporte_bajasaf_filtro.php');
		}
		//RESPONSABLES
		if ($_GET['opcion']=='listResponsables') {
			require_once('activosFijos/list3.php');
		}
		if ($_GET['opcion']=='registerResponsable') {
			require_once('activosFijos/register3.php');
		}
		if ($_GET['opcion']=='editResponsable') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/edit3.php');
		}
		if ($_GET['opcion']=='deleteResponsable') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/saveDelete3.php');
		}

		if ($_GET['opcion']=='saveResponsables') {
			require_once('activosFijos/save3.php');
		}
		if ($_GET['opcion']=='saveEditResponsables') {
			require_once('activosFijos/saveEdit3.php');
		}
		//...
		//depreciaciones=RUBROS
		if ($_GET['opcion']=='listDepreciaciones') {
			require_once('activosFijos/depreciacionesLista.php');
		}		
		if ($_GET['opcion']=='registerDepreciacion') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/depreciacionesRegistro.php');
		}
		if ($_GET['opcion']=='deleteResponsable') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/saveDelete3.php');
		}
		
    	if ($_GET['opcion']=='saveDepreciaciones') {
    		require_once('activosFijos/depreciacionesGuardarNuevo.php');//guarda nuevo y edicion
		}
    	if ($_GET['opcion']=='saveEditResponsables') {
                        require_once('activosFijos/saveEdit3.php');
		}
		if ($_GET['opcion']=='deleteDepr') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/saveDeleteDepr.php');
		}

		//TIPOS DE BIENES
		if ($_GET['opcion']=='listaTiposBienes') {
			require_once('activosFijos/tiposbienesLista.php');
		}
		if ($_GET['opcion']=='registerTipoBien') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/tiposbienesRegistro.php');
		}
		if ($_GET['opcion']=='deleteTiposBienes') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/saveDeleteTipoBien.php');
		}
        if ($_GET['opcion']=='saveTiposBienes') {
	        require_once('activosFijos/tiposBienesSave.php');
		}

		//ACTIVOS FIJOS
		if ($_GET['opcion']=='activosfijosLista') {
			require_once('activosFijos/activosfijosLista.php');
		}
		if ($_GET['opcion']=='activofijoRegister') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/activosfijosRegistro.php');
		}
		if ($_GET['opcion']=='') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/saveDelete3.php');
		}

        if ($_GET['opcion']=='saveActivosfijos') {
            require_once('activosFijos/activosfijosGuardar.php');
		}
		if ($_GET['opcion']=='activofijoPrintAlta') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/afPrintAlta.php');
		}
		if ($_GET['opcion']=='activofijoTransferir') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/activofijoTransferir.php');
		}
		if ($_GET['opcion']=='saveTransferActivosfijos') {
			//$codigo=$_GET['codigoactivo'];
			require_once('activosFijos/saveTransferActivosfijos.php');
		}
		if ($_GET['opcion']=='activofijoCargarImagen') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/activosfijosGuardarImagen.php');
		}
		if ($_GET['opcion']=='activofijoCargarImagen_save') {			
			require_once('activosFijos/activosfijosGuardarImagen_save.php');
		}

		
		if ($_GET['opcion']=='saveReevaluoAF') {
			//$codigo=$_GET['codigoactivo'];
			require_once('activosFijos/saveReevaluoAF.php');
		}

		if ($_GET['opcion']=='activofijoAccesorios') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/activofijoAccesorios.php');
		}
		if ($_GET['opcion']=='activofijoEventos') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/activofijoEventos.php');
		}
		if ($_GET['opcion']=='activofijoRevaluar') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/activofijoRevaluar.php');
		}
		

		//ASIGNACIONES DE ACTIVOS FIJOS
		if ($_GET['opcion']=='asignacionesLista') {
			require_once('activosFijos/asignacionesLista.php');
		}
		if ($_GET['opcion']=='asignacionesRegister') {
			require_once('activosFijos/asignacionesForm.php');
		}
		if ($_GET['opcion']=='editAsignacion') {
			//$codigo=$_GET['codigo'];
			require_once('activosFijos/asignacionesForm.php');
		}
		if ($_GET['opcion']=='') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/saveDeletexxx.php');
		}

		if ($_GET['opcion']=='asignacionesSave') {
			require_once('activosFijos/saveAsignacion.php');
		}
		if ($_GET['opcion']=='activofijoPrintAlta') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/afPrintAlta.php');
		}

		//PROVEEDORES
		if ($_GET['opcion']=='provLista') {
			require_once('activosFijos/proveedoresLista.php');
		}
		if ($_GET['opcion']=='provForm') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/proveedoresForm.php');
		}

		if ($_GET['opcion']=='saveProv') {
			require_once('activosFijos/proveedoresSave.php');
		}
		if ($_GET['opcion']=='activofijoPrintAlta') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/afPrintAlta.php');
		}
		if ($_GET['opcion']=='deleteProv') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/saveDeleteProv.php');
		}

		//EJECUTAR DEPRECIACION
		if ($_GET['opcion']=='ejecutarDepreciacionLista') {
			require_once('activosFijos/executeDepreciacionesLista.php');
		}
		if ($_GET['opcion']=='ejecutarDepreciacionesRegister') {
			require_once('activosFijos/executeDepreciacionesRegister.php');
		}
		if ($_GET['opcion']=='') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/saveDeleteYYY.php');
		}

	    if ($_GET['opcion']=='executeDepreciacionesSave') {
            require_once('activosFijos/executeDepreciacionesSave.php');
		}
	    if ($_GET['opcion']=='saveEditTiposBienes') {
            require_once('activosFijos/saveEdit3.php');
		}
		if ($_GET['opcion']=='executeComprobanteDepreciacion') {
	    	$codigo=$_GET['codigo'];
            require_once('activosFijos/executeComprobanteDepreciacion.php');
		}

        if ($_GET['opcion']=='rptactivosfijos') {
            require_once('activosFijos/rptactivosfijos.php');
		}
		if ($_GET['opcion']=='rptactivosfijosAsignados') {
            require_once('activosFijos/rptactivosfijosAsignados.php');
		}
        if ($_GET['opcion']=='rptxrubrosxmes') {
            require_once('activosFijos/rptxrubrosxmes.php');
		}
		if ($_GET['opcion']=='rptxrubrosxmesTotal') {
            require_once('activosFijos/rptxrubrosxmesTotal.php');
		}	
		
		if ($_GET['opcion']=='rptDepreciacionesDetalladoFiltro') {
            require_once('activosFijos/reporteDepreciacionesDetallado_filtro.php');
		}

		if ($_GET['opcion']=='rptactivosfijosxunidad') {
			require_once('activosFijos/rptactivosfijosxunidad.php');
		}

		//ACTIVOS FIJOS EN CUSTODIA Y ACTUALIZACION DE LA DEPRECIACION
		if ($_GET['opcion']=='aftransaccion') {
			require_once('activosFijos/aftransaccion.php');
		}

		if ($_GET['opcion']=='afEnCustodia') {
			require_once('activosFijos/afEnCustodia.php');
		}
		if ($_GET['opcion']=='actualizarAsignacion') {
			require_once('activosFijos/saveAsignacion.php');
		}

		//impresion etiquetas activos fijos
		if ($_GET['opcion']=='afEtiquetasFiltro') {
			require_once('activosFijos/afEtiquetasFiltro.php');
		}
        
		if ($_GET['opcion']=='afConstanciasTraspaso') {
			require_once('reportes_activosfijos/afConstanciasTraspaso.php');
		}

		//utilitarios AF
        if ($_GET['opcion']=='cambiarResponsableAF') {
			require_once('activosFijos/cambiar_responsable.php');
		}
		if ($_GET['opcion']=='cambiarResponsableAFsave') {
            require_once('activosFijos/cambiar_responsable_save.php');
		}

		//*************************************************************************************************************/
		//*************************************************************************************************************/
		//*************************************************************************************************************/
		//*************************************************************************************************************/
				//***************************************************/
				//A PARTIR DE AQUI ES RECURSOS HUMANOS

		//areas
		if ($_GET['opcion']=='areasLista') {
			require_once('rrhh/areasLista.php'); //ok
		}
		if ($_GET['opcion']=='areasListaInactivo') {
			require_once('rrhh/areasListaInactivo.php'); //Desactivados
		}
		if ($_GET['opcion']=='areasForm') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/areasForm.php');
		}
		if ($_GET['opcion']=='areasSave') {
			require_once('rrhh/areasSave.php');
		}
		if ($_GET['opcion']=='deleteAreas') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/deleteAreas.php');
		}

		

		//cargos
		if ($_GET['opcion']=='cargosLista') {
			require_once('rrhh/cargosLista.php'); //ok
		}
		if ($_GET['opcion']=='cargosListaInactivo') {
			require_once('rrhh/cargosListaInactivo.php'); //Desactivados
		}
		if ($_GET['opcion']=='cargosForm') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/cargosForm.php');
		}
		if ($_GET['opcion']=='cargosSave') {
			require_once('rrhh/cargosSave.php');
		}

		if ($_GET['opcion']=='deleteCargos') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/deleteCargos.php');
		}

		if ($_GET['opcion']=='cargosFunciones') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/cargosFunciones.php');
		}
		if ($_GET['opcion']=='cargosEscalaSalarial') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/cargosEscalaSalarial.php');
		}
		if ($_GET['opcion']=='cargosEscalaSalarialForm') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/cargosEscalaSalarialForm.php');
		}
		if ($_GET['opcion']=='cargosEscalaSalarialSave') {
			require_once('rrhh/cargosEscalaSalarialSave.php');
		}
		if ($_GET['opcion']=='cargoEscalaSalarialGeneral') {
			require_once('rrhh/cargoEscalaSalarialGeneral.php');
		}

		if ($_GET['opcion']=='cargoEscalaSalarialGeneralDelete') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/cargoEscalaSalarialGeneralDelete.php');
		}


		//unidades organizacionales
		if ($_GET['opcion']=='uoLista') {
			require_once('rrhh/uoLista.php'); //ok
		}
		if ($_GET['opcion']=='uoForm') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/uoForm.php');
		}
		if ($_GET['opcion']=='uoSave') {
			require_once('rrhh/uoSave.php');
		}
		if ($_GET['opcion']=='deleteUO') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/uoDelete.php');
		}
		if ($_GET['opcion']=='registerAreasU') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/registerAreasU.php');
		}
		//areas unidades organixaciones		
		if ($_GET['opcion']=='areasuoSave') {
			require_once('rrhh/areas_organizacionSave.php');
		}
		// reporte cambios personal rrhh
		if ($_GET['opcion']=='rptCambiosPersonal') {
			require_once('personal/rptCambiosPersonal.php');
		}		
		if ($_GET['opcion']=='rptCambiosPersonalPrint') {
		$codigo=$_GET['codigo'];
		require_once('personal/rptCambiosPersonalPrint.php');
		}
		//reporte distribucion
		if ($_GET['opcion']=='rptDistribucionSueldos') {
			require_once('personal/rptDistribucionSueldosFiltro.php');
		}
		//reporte ingresos y descuentos
		if ($_GET['opcion']=='rptIngresos_Descuentos') {
			require_once('personal/rptrptIngresosDescuentosFiltro.php');
		}

		//reporte info del personal
		if ($_GET['opcion']=='rptPersonal_from') {
			require_once('personal/rptPersonal_from.php');
		}


		// if ($_GET['opcion']=='saveTiposContrato') {
		// 	require_once('personal/tipos_contratosSave.php');
		// }
		// if ($_GET['opcion']=='deleteTiposContrato') {
		// 	$codigo=$_GET['codigo'];
		// 	require_once('personal/tiposContratoDelete.php');
		// }
		//tipos cargos
		if ($_GET['opcion']=='tiposCargosLista') {
			require_once('personal/tiposCargosLista.php');
		}		
		if ($_GET['opcion']=='formCargosLista') {
			$codigo=$_GET['codigo'];
			require_once('personal/TiposCargosForm.php');
		}
		if ($_GET['opcion']=='saveTiposCargos') {
			require_once('personal/tiposCargosSave.php');
		}
		if ($_GET['opcion']=='deleteTiposCargos') {
			$codigo=$_GET['codigo'];
			require_once('personal/tiposCargosDelete.php');
		}
		
		//tipos personal
		if ($_GET['opcion']=='tipospersonalLista') {
			require_once('personal/tipospersonalLista.php'); //ok
		}
		if ($_GET['opcion']=='tipospersonalForm') {
			$codigo=$_GET['codigo'];
			require_once('personal/tipospersonalForm.php');
		}
		if ($_GET['opcion']=='tipospersonalSave') {
			require_once('personal/tipospersonalSave.php');
		}
		if ($_GET['opcion']=='deletetipospersonal') {
			$codigo=$_GET['codigo'];
			require_once('personal/deletetipospersonal.php');
		}
		if ($_GET['opcion']=='PersonalAreaDistribucionForm') {
			$codigo=$_GET['codigo'];
			require_once('personal/PersonalAreaDistribucionForm.php');
		}
		if ($_GET['opcion']=='FormPersonalContratos') {
			$codigo=$_GET['codigo'];
			require_once('personal/PersonalContratosForm.php');
		}
		// personal otros datos

		if ($_GET['opcion']=='edit_uo_area_personal') {
			$codigo_item=$_GET['codigo_item'];
			$codigo_p=$_GET['codigo_p'];
			require_once('personal/personal_edit_otros.php');
		}
		if ($_GET['opcion']=='personalOtrosSave') {
			require_once('personal/personal_save_otros.php');
		}

		//estados personal
		if ($_GET['opcion']=='estadospersonalLista') {
			require_once('personal/estadospersonalLista.php'); //ok
		}
		if ($_GET['opcion']=='estadospersonalForm') {
			$codigo=$_GET['codigo'];
			require_once('personal/estadospersonalForm.php');
		}
		if ($_GET['opcion']=='estadospersonalSave') {
			require_once('personal/estadospersonalSave.php');
		}
		if ($_GET['opcion']=='deleteestadospersonal') {
			$codigo=$_GET['codigo'];
			require_once('personal/deleteestadospersonal.php');
		}
		
		//estados planilla
		if ($_GET['opcion']=='estadosplanillaLista') {
			require_once('rrhh/estadosplanillaLista.php'); //ok
		}
		if ($_GET['opcion']=='estadosplanillaForm') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/estadosplanillaForm.php');
		}
		if ($_GET['opcion']=='estadosplanillaSave') {
			require_once('rrhh/estadosplanillaSave.php');
		}
		if ($_GET['opcion']=='deleteestadosplanilla') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/deleteestadosplanilla.php');
		}

		//planillas sueldos
		if ($_GET['opcion']=='planillasSueldoPersonal') {
			require_once('planillas/planillasSueldoList.php'); //ok
		}
		if ($_GET['opcion']=='generarPlanillaSueldoPrevia') {
			require_once('planillas/generarPlanillaSueldo.php'); //ok
		}
		// PLANILLA REPORTE VISITAS
		if ($_GET['opcion']=='planillasSueldoPersonalDetail') {
			require_once('planillas/planillasSueldoListDetail.php'); //ok
		}
		//planillas aguinaldos
		if ($_GET['opcion']=='planillasAguinaldosPersonal') {
			require_once('planillas/planillasAguinaldosList.php'); //ok
		}
		if ($_GET['opcion']=='generarPlanillaAguinaldosPrevia') {
			require_once('planillas/generarPlanillaAguinaldos.php'); //ok
		}
		//finiquitos
		if ($_GET['opcion']=='finiquitos_list') {
			require_once('planillas/finiquitosList.php'); //ok
		}
		if ($_GET['opcion']=='finiquitos_form') {
			$codigo=$_GET['codigo'];
			require_once('planillas/finiquitosForm.php');
		}
		if ($_GET['opcion']=='saveFiniquitos') {
			require_once('planillas/FiniquitosSave.php');
		}
		if ($_GET['opcion']=='deleteFiniquito') {
			$codigo=$_GET['codigo'];
			require_once('planillas/finiquitosDelete.php');
		}
		
		//planilla retroactivo
		if ($_GET['opcion']=='planillasRetroactivoPersonal') {
			require_once('planillas/planillasRetroactivosList.php'); //ok
		}
		if ($_GET['opcion']=='planillasRetroactivoPersonal_save') {
			require_once('planillas/planillasRetroactivosSave.php'); //ok
		}

		
		
		//tipo aporte afp
		if ($_GET['opcion']=='tipos_aporteafpLista') {
			require_once('rrhh/tipos_aporteafpLista.php'); //ok
		}
		if ($_GET['opcion']=='tipos_aporteafpForm') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/tipos_aporteafpForm.php');
		}
		if ($_GET['opcion']=='tipos_aporteafpSave') {
			require_once('rrhh/tipos_aporteafpSave.php');
		}
		if ($_GET['opcion']=='deletetipos_aporteafp') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/deletetipos_aporteafp.php');
		}

		//tipos_genero
		if ($_GET['opcion']=='tipos_generoLista') {
			require_once('rrhh/tipos_generoLista.php'); //ok
		}
		if ($_GET['opcion']=='tipos_generoForm') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/tipos_generoForm.php');
		}
		if ($_GET['opcion']=='tipos_generoSave') {
			require_once('rrhh/tipos_generoSave.php');
		}		
		if ($_GET['opcion']=='deletetipos_genero') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/deletetipos_genero.php');
		}

		//areas_organizacion RELACIONAL
		if ($_GET['opcion']=='areas_organizacionLista') {
			require_once('rrhh/areas_organizacionLista.php'); //ok
		}
		if ($_GET['opcion']=='areas_organizacionForm') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/areas_organizacionForm.php');
		}
		if ($_GET['opcion']=='areas_organizacionSave') {
			require_once('rrhh/areas_organizacionSave.php');
		}

		if ($_GET['opcion']=='deleteareas_organizacion') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/deleteareas_organizacion.php');
		}

		//areas contabilizacion
		if ($_GET['opcion']=='listAreas_contabilizacion') {
			require_once('rrhh/areas_contabilizacion.php'); //ok
		}

		if ($_GET['opcion']=='FormAreas_contabilizacion') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/FormAreas_contabilizacion.php');
		}
		if ($_GET['opcion']=='areas_contabilizacionSave') {
			require_once('rrhh/areas_contabilizacionSave.php');
		}
		if ($_GET['opcion']=='deleteareas_contabilizacion') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/deleteareas_contabilizacion.php');
		}
		if ($_GET['opcion']=='list_areas_contabilizacion_Detalle') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/list_areas_contabilizacion_Detalle.php');
		}
		if ($_GET['opcion']=='FormAreas_contabilizacion_detalle') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/FormAreas_contabilizacion_detalle.php');
		}
		if ($_GET['opcion']=='saveAreas_contabilizacion_detalle') {
			require_once('rrhh/areas_contabilizacion_detalle_save.php');
		}

		if ($_GET['opcion']=='delete_areas_conta_detalle') {
			$codigo=$_GET['codigo'];
			$codigox=$_GET['codigox'];
			require_once('rrhh/areas_contabilizacion_detalle_delete.php');
		}
		
		//Personal Financiación Externa (Proyectos)
		if ($_GET['opcion']=='personalFinExterna') {
			require_once('personal_finexterna/list.php'); //ok
		}
		
		if ($_GET['opcion']=='deletePersonalFinExterna') {
			$codigo=$_GET['codigo'];
			require_once('personal_finexterna/saveDelete.php');
		}

		if ($_GET['opcion']=='editPersonalFinExterna') {
			$codigo=$_GET['codigo'];
			require_once('personal_finexterna/edit.php');
		}

		if ($_GET['opcion']=='registerPersonalFinExterna') {
			require_once('personal_finexterna/register.php');
		}
		
		
		


		//personal
		if ($_GET['opcion']=='personalLista') {
			require_once('personal/personalLista.php'); //ok
		}
		if ($_GET['opcion']=='personalForm') {
			$codigo=0;
			$codigo=$_GET['codigo'];
			require_once('personal/personalForm.php');
		}
		if ($_GET['opcion']=='personalSave') {
			require_once('personal/personalSave.php');
		}

		if ($_GET['opcion']=='personalSaveWS') {
			require_once('personal/cargarPersonalWS.php');
		}
		
		if ($_GET['opcion']=='deletepersonal') {
			$codigo=$_GET['codigo'];
			require_once('personal/deletepersonal.php');
		}

		//personal retrirado
		if ($_GET['opcion']=='personalListaRetirado') {
			require_once('personal/personalListaRetirado.php'); //ok
		}
		// PERSONAL PARA APROBACIÓN
		if ($_GET['opcion']=='personalListasAprobacion') {
			require_once('personal/personalListaAprobacion.php'); //ok
		}

		if ($_GET['opcion']=='personalFormRetirado') {			
			$codigo=$_GET['codigo'];
			require_once('personal/personalFormRetirado.php');
		}
		if ($_GET['opcion']=='personalSaveRetirado') {
			require_once('personal/personalSaveRetirado.php');
		}

		//tipos_afpLista AFP PREVISION, BBVA
		if ($_GET['opcion']=='tipos_afpLista') {
			require_once('rrhh/tipos_afpLista.php'); //ok
		}
		if ($_GET['opcion']=='tipos_afpForm') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/tipos_afpForm.php');
		}
		if ($_GET['opcion']=='tipos_afpSave') {
			require_once('rrhh/tipos_afpSave.php');
		}
		if ($_GET['opcion']=='deletetipos_afp') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/deletetipos_afp.php');
		}
		
		//aportes_patronales
		if ($_GET['opcion']=='aportes_patronalesLista') {
			require_once('rrhh/aportes_patronalesLista.php'); //ok
		}
		if ($_GET['opcion']=='aportes_patronalesForm') {
			//$codigo=$_GET['codigo'];
			require_once('rrhh/aportes_patronalesForm.php');
		}
		if ($_GET['opcion']=='aportes_patronalesSave') {
			require_once('rrhh/aportes_patronalesSave.php');
		}
		if ($_GET['opcion']=='deleteaportes_patronales') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/deleteaportes_patronales.php');
		}

		//aportes_laborales
		if ($_GET['opcion']=='aportes_laboralesLista') {
			require_once('rrhh/aportes_laboralesLista.php'); //ok
		}
		if ($_GET['opcion']=='aportes_laboralesForm') {
			//$codigo=$_GET['codigo'];
			require_once('rrhh/aportes_laboralesForm.php');
		}
		if ($_GET['opcion']=='aportes_laboralesSave') {
			require_once('rrhh/aportes_laboralesSave.php');
		}
		if ($_GET['opcion']=='deleteaportes_laborales') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/deleteaportes_laborales.php');
		}

		
		

		//RECURSOS HUMANOS
		//bonos
		if ($_GET['opcion']=='listBonos') {
			require_once('bonos/list.php');
		}

		if ($_GET['opcion']=='deleteBono') {
			$codigo=$_GET['codigo'];
			require_once('bonos/saveDelete.php');
		}

		if ($_GET['opcion']=='editBono') {
			$codigo=$_GET['codigo'];
			require_once('bonos/edit.php');
		}
		
		if ($_GET['opcion']=='registerBono') {
			require_once('bonos/register.php');
		}

		if ($_GET['opcion']=='listMeses') {
			$codigo_bono=$_GET['codigo'];
			require_once('bonos/listMeses.php');
		}
		if ($_GET['opcion']=='listMesPersona') {
			$codigo_bono=$_GET['cod_bono'];
			$codigo_mes=$_GET['cod_mes'];
			require_once('bonos/listMesPersona.php');
		}

		if ($_GET['opcion']=='deleteBonoMesPersona') {
			$codigo=$_GET['codigo'];
			$codigo_bono=$_GET['cod_bono'];
			$codigo_mes=$_GET['cod_mes'];
			require_once('bonos/saveDeleteBonoMesPersona.php');
		}

		if ($_GET['opcion']=='registerBonoMesPersona') {
			$codigo_bono=$_GET['cod_bono'];
			$codigo_mes=$_GET['cod_mes'];
			require_once('bonos/registerBonoMesPersona.php');
		}
        if ($_GET['opcion']=='registerBonoPeriodoPersona') {
			$codigo_bono=$_GET['cod_bono'];
			require_once('bonos/registerBonoPeriodoPersona.php');
		}
		if ($_GET['opcion']=='finBonoPeriodoPersona') {
			$codigo_bono=$_GET['cod_bono'];
			require_once('bonos/finBonoPeriodoPersona.php');
		}
		if ($_GET['opcion']=='subirBonoExcel') {
			$codigo_bono=$_GET['cod_bono'];
			$codigo_mes=$_GET['cod_mes'];
			require_once('bonos/subirExcel.php');
		}

		if ($_GET['opcion']=='subirBonoExcel2') {
			require_once('bonos/subirDatosExcel.php');
		}

		if ($_GET['opcion']=='editBonosMesPersona') {
			$codigo_bono=$_GET['cod_bono'];
			$codigo_mes=$_GET['cod_mes'];
			require_once('bonos/editMesPersona.php');
		}

		if ($_GET['opcion']=='calculaBonoProfesion') {
			$codigo_bono=$_GET['cod_bono'];
			$codigo_mes=$_GET['cod_mes'];
			require_once('bonos/calculaBonoProfesion.php');
		}

		if ($_GET['opcion']=='subirBonoExcel_global_from') {
			require_once('bonos/subirExcel_global_from.php');
		}
		if ($_GET['opcion']=='subirBonoExcel_global_save') {
			require_once('bonos/subirExcel_global_save.php');
		}






		//descuentos
		if ($_GET['opcion']=='listDescuentos') {
			require_once('descuentos/list.php');
		}

		if ($_GET['opcion']=='deleteDescuento') {
			$codigo=$_GET['codigo'];
			require_once('descuentos/saveDelete.php');
		}

		if ($_GET['opcion']=='editDescuento') {
			$codigo=$_GET['codigo'];
			require_once('descuentos/edit.php');
		}
		
		if ($_GET['opcion']=='registerDescuento') {
			require_once('descuentos/register.php');
		}

		if ($_GET['opcion']=='listDescuentoMes') {
			$codigo_descuento=$_GET['codigo'];
			require_once('descuentos/listMeses.php');
		}

		if ($_GET['opcion']=='listDescuentoMesPersona') {
			$codigo_descuento=$_GET['cod_descuento'];
			$codigo_mes=$_GET['cod_mes'];
			require_once('descuentos/listMesPersona.php');
		}

		if ($_GET['opcion']=='deleteDescuentoMesPersona') {
			$codigo=$_GET['codigo'];
			$codigo_descuento=$_GET['cod_descuento'];
			$codigo_mes=$_GET['cod_mes'];
			require_once('descuentos/saveDeleteDescuentoMesPersona.php');
		}

		if ($_GET['opcion']=='registerDescuentoMesPersona') {
			$codigo_descuento=$_GET['cod_descuento'];
			$codigo_mes=$_GET['cod_mes'];
			require_once('descuentos/registerDescuentoMesPersona.php');
		}

		if ($_GET['opcion']=='subirDescuentoExcel') {
			$codigo_descuento=$_GET['cod_descuento'];
			$codigo_mes=$_GET['cod_mes'];
			require_once('descuentos/subirExcel.php');
		}

		if ($_GET['opcion']=='subirDescuentoExcel2') {
			require_once('descuentos/subirDatosExcel.php');
		}

		if ($_GET['opcion']=='editDescuentoMesPersona') {
			$codigo_descuento=$_GET['cod_descuento'];
			$codigo_mes=$_GET['cod_mes'];
			require_once('descuentos/editMesPersona.php');
		}

		if ($_GET['opcion']=='calculaDescuentoRetrasos') {
			$codigo_descuento=$_GET['cod_descuento'];
			$codigo_mes=$_GET['cod_mes'];
			require_once('descuentos/calculaDescuentoRetrasos.php');
		}


		//RC_IVA Personal
		if ($_GET['opcion']=='listRcivaPersonalMes') {
			require_once('rc_ivaPersonal/listMeses.php');
		}

		if ($_GET['opcion']=='listRcivaPersonal') {
			require_once('rc_ivaPersonal/list.php');
		}
		if ($_GET['opcion']=='deleteRcivaPersonal') {
			$cod_rciva=$_GET['cod_rciva'];
			require_once('rc_ivaPersonal/saveDelete.php');
		}

		if ($_GET['opcion']=='editRcivaPersonal') {
			$cod_rciva=$_GET['cod_rciva'];
			require_once('rc_ivaPersonal/edit.php');
		}

		if ($_GET['opcion']=='registerRcivaPersonal') {
			require_once('rc_ivaPersonal/register.php');
		}



		//Anticipos Personal
		if ($_GET['opcion']=='listAnticipoPersonal') {
			require_once('anticipos_personal/list.php');
		}
		if ($_GET['opcion']=='listAnticipoPersonalMes') {
			require_once('anticipos_personal/listMeses.php');
		}
		if ($_GET['opcion']=='deleteAnticipoPersonal') {
			$cod_ant_per=$_GET['cod_ant_per'];
			require_once('anticipos_personal/saveDelete.php');
		}

		if ($_GET['opcion']=='editAnticipoPersonal') {
			$cod_ant_per=$_GET['cod_ant_per'];
			require_once('anticipos_personal/edit.php');
		}

		if ($_GET['opcion']=='registerAnticipoPersonal') {
			require_once('anticipos_personal/register.php');
		}
		
        if ($_GET['opcion']=='subirAnticipoExcel') {
			$codigo_mes=$_GET['cod_mes'];
			require_once('anticipos_personal/subirExcel.php');
		}
		if ($_GET['opcion']=='subirAnticipoExcel2') {
			require_once('anticipos_personal/subirDatosExcel.php');
		}
		       //ayuda documento csv
            if ($_GET['opcion']=='ayudaArchivoCsv') {
			   require_once('anticipos_personal/helpCsv.php');
		    }

		//Escalas Antiguedad
		if ($_GET['opcion']=='listEscalaAntiguedad') {
			require_once('escalas_antiguedad/list.php');
		}

		if ($_GET['opcion']=='deleteEscalaAntiguedad') {
			$cod_esc_ant=$_GET['cod_esc_ant'];
			require_once('escalas_antiguedad/saveDelete.php');
		}

		if ($_GET['opcion']=='editEscalaAntiguedad') {
			$cod_esc_ant=$_GET['cod_esc_ant'];
			require_once('escalas_antiguedad/edit.php');
		}

		if ($_GET['opcion']=='registerEscalaAntiguedad') {
			require_once('escalas_antiguedad/register.php');
		}

		//Politica Descuentos
		if ($_GET['opcion']=='listPoliticaDescuento') {
			require_once('politicas_descuento/list.php');
		}

		if ($_GET['opcion']=='deletePoliticaDescuento') {
			$cod_pol_desc=$_GET['cod_pol_desc'];
			require_once('politicas_descuento/saveDelete.php');
		}

		if ($_GET['opcion']=='editPoliticaDescuento') {
			$cod_pol_desc=$_GET['cod_pol_desc'];
			require_once('politicas_descuento/edit.php');
		}

		if ($_GET['opcion']=='registerPoliticaDescuento') {
			require_once('politicas_descuento/register.php');
		}

		
		
		//Dotaciones
		if ($_GET['opcion']=='listDotacion') {
			require_once('dotaciones/list.php');
		}

		if ($_GET['opcion']=='deleteDotacion') {
			$cod_dot=$_GET['cod_dot'];
			require_once('dotaciones/saveDelete.php');
		}

		if ($_GET['opcion']=='editDotacion') {
			$cod_dot=$_GET['cod_dot'];
			require_once('dotaciones/edit.php');
		}

		if ($_GET['opcion']=='registerDotacion') {
			require_once('dotaciones/register.php');
		}

		//Dotacion Personal
		if ($_GET['opcion']=='listDotacionPersonal') {
			$cod_dot=$_GET['cod_dot'];
			require_once('dotaciones/listDotacionPersonal.php');
		}

		if ($_GET['opcion']=='deleteDotacionPersonal') {
			$cod_dot=$_GET['cod_dot'];
			$cod_dot_per=$_GET['cod_dot_per'];
			require_once('dotaciones/saveDeleteDotacionPersonal.php');
		}

		if ($_GET['opcion']=='registerDotacionPersonal') {
			$cod_dot=$_GET['cod_dot'];
			require_once('dotaciones/registerDotacionPersonal.php');
		}

		//Dotacion Personal Meses
		if ($_GET['opcion']=='listDotPersonalMeses') {
			$cod_dot=$_GET['cod_dot'];
			$cod_dot_per=$_GET['cod_dot_per'];
			require_once('dotaciones/listDotPersonalMeses.php');
		}

		//Distribucion Gastos x Oficina
		if ($_GET['opcion']=='listDistribucionGasto') {
			require_once('distribucion_gastosporcentaje/list.php');
		}

		if ($_GET['opcion']=='editDistribucionGastos') {
			$codigo=$_GET['codigo'];
			require_once('distribucion_gastosporcentaje/editDistribucionGastos.php');
		}
		if ($_GET['opcion']=='registerDistribucionGastos') {
			$codigo=$_GET['codigo'];
			require_once('distribucion_gastosporcentaje/registerDistribucionGastos.php');
		}
		//Distribucion Gastos x AREA
		if ($_GET['opcion']=='listDistribucionGastoArea') {
			require_once('distribucion_gastos_area/list.php');
		}
		if ($_GET['opcion']=='editDistribucionGastosArea') {
			$codigo=$_GET['codigo'];
			require_once('distribucion_gastos_area/editDistribucionGastos.php');
		}
		if ($_GET['opcion']=='registerDistribucionGastosArea') {
			$codigo=$_GET['codigo'];
			require_once('distribucion_gastos_area/registerDistribucionGastos.php');
		}
		if ($_GET['opcion']=='DistribucionGastosDetalleArea') {
			$codigo=$_GET['codigo'];
			require_once('distribucion_gastos_area/list_distribucion_detalle.php');
		}
		if ($_GET['opcion']=='deleteDistribucionArea') {
			$codigo=$_GET['codigo'];
			require_once('distribucion_gastos_area/saveDeleteDistribucion.php');
		}
		if ($_GET['opcion']=='saveCambiarDistribucionGastosArea') {
			$codigo=$_GET['codigo'];
			require_once('distribucion_gastos_area/saveCambiarDistribucionGastos.php');
		}

		//ESCALAS ANTIGUEDAD
		if ($_GET['opcion']=='registerEscalaAntiguedad') {
			require_once('escalas_antiguedad/register.php');
		}

		if ($_GET['opcion']=='deleteEscalaAntiguedad') {
			$cod_esc_ant=$_GET['cod_esc_ant'];
			require_once('escalas_antiguedad/saveDelete.php');
		}
		//refrigerios
		if ($_GET['opcion']=='listRefrigerio') {
			require_once('refrigerios/list.php');
		}

		if ($_GET['opcion']=='registerRefrigerio') {
			require_once('refrigerios/register.php');
		}

		if ($_GET['opcion']=='listRefrigerioDetalle') {
			$cod_refrigerio=$_GET['cod_ref'];
			$cod_mes=$_GET['cod_mes'];
			require_once('refrigerios/listDetalle.php');
		}
		
		if ($_GET['opcion']=='refrigerioImport') {
			$cod_ref=$_GET['cod_ref'];
			$cod_mes=$_GET['cod_mes'];
			require_once('refrigerios/importDetalle.php');
		}
		if ($_GET['opcion']=='editRefrigerioDetalle') {
			$cod_refrigeriodetalle=$_GET['cod_ref_det'];
			require_once('refrigerios/editDetalle.php');
		}

		if ($_GET['opcion']=='aprobarRefrigerio') {
			$cod_refrigerio=$_GET['cod_ref'];
			require_once('refrigerios/aprobarRefrigerio.php');
		}

		if ($_GET['opcion']=='editPoliticaDescuento') {
			$cod_pol_desc=$_GET['cod_pol_desc'];
			require_once('politicas_descuento/edit.php');
		}

		if ($_GET['opcion']=='registerRefrigerioDetalle') {
			$cod_refrigerio=$_GET['cod_ref'];
			require_once('refrigerios/registerRefrigerioDetalle.php');
		}

		if ($_GET['opcion']=='calculaRefrigerioMes') {
			$cod_refrigerio=$_GET['cod_ref'];
			$cod_mes=$_GET['cod_mes'];
			require_once('refrigerios/calculaRefrigerioMes.php');
		}
        

        if ($_GET['opcion']=='homeModulo') {
			$codModulo=$menuModulo;
			require_once('layouts/homeModulo.php');
		}
		if ($_GET['opcion']=='homeModulo2') {
			$codModulo=$menuModulo;
			require_once('layouts/homeModulo2.php');
		}

		//tipo caja chica
		if ($_GET['opcion']=='ListaTipoCajaChica') {
			require_once('caja_chica/tipocajachica_list.php');
		}
		if ($_GET['opcion']=='tipoCajaChicaForm') {
			$codigo=$_GET['codigo'];
			require_once('caja_chica/tipocajachica_form.php');
		}
		if ($_GET['opcion']=='tiposCajaChicaSave') {
			require_once('caja_chica/tipocajachica_save.php');
		}
		if ($_GET['opcion']=='deleteTiposCajaChica') {
			$codigo=$_GET['codigo'];
			require_once('caja_chica/tipocajachica_delete.php');
		}
		//caja chica
		if ($_GET['opcion']=='principal_CajaChica') {
			require_once('caja_chica/principal_cajachica.php');
		}
		if ($_GET['opcion']=='principal_CajaChica_historico') {
			require_once('caja_chica/principal_cajachica_historico.php');
		}
		if ($_GET['opcion']=='ListaCajaChica') {
			$codigo=$_GET['codigo'];
			require_once('caja_chica/cajachica_list.php');
		}
		if ($_GET['opcion']=='CajaChicaForm') {
			$codigo=$_GET['codigo'];
			$cod_tcc=$_GET['cod_tcc'];
			require_once('caja_chica/cajachica_form.php');
		}
		if ($_GET['opcion']=='CajaChicaSave') {
			require_once('caja_chica/cajachica_save.php');
		}
		if ($_GET['opcion']=='deleteCajaChica') {
			$codigo=$_GET['codigo'];
			$cod_tcc=$_GET['cod_tcc'];
			$cod_a=$_GET['cod_a'];

			require_once('caja_chica/cajachica_delete.php');
		}
		//detalle caja chica 
		if ($_GET['opcion']=='ListaDetalleCajaChica') {
			$codigo=$_GET['codigo'];
			$cod_tcc=$_GET['cod_tcc'];
			require_once('caja_chica/detallecajachica_list.php');
		}
		if ($_GET['opcion']=='DetalleCajaChicaForm') {
			$codigo=$_GET['codigo'];
			$cod_tcc=$_GET['cod_tcc'];
			$cod_cc=$_GET['cod_cc'];
			require_once('caja_chica/detallecajachica_form.php');
		}

		if ($_GET['opcion']=='DetalleCajaChicaSave') {
			require_once('caja_chica/detallecajachica_save.php');
		}
		if ($_GET['opcion']=='deleteDetalleCajaChica') {
			$codigo=$_GET['codigo'];
			$cod_tcc=$_GET['cod_tcc'];
			$cod_cc=$_GET['cod_cc'];
			require_once('caja_chica/detallecajachica_delete.php');
		}
		if ($_GET['opcion']=='quitarDetalleCajaChica') {
			$codigo=$_GET['codigo'];
			$cod_tcc=$_GET['cod_tcc'];
			$cod_cc=$_GET['cod_cc'];
			require_once('caja_chica/detallecajachica_quitar.php');
		}
		if ($_GET['opcion']=='ReembolsoCajaChicaForm') {
			$codigo=$_GET['codigo'];
			$cod_tcc=$_GET['cod_tcc'];
			$cod_cc=$_GET['cod_cc'];
			require_once('caja_chica/reembolso_cajachica_form.php');
		}
		if ($_GET['opcion']=='ReembolsoCajaChicaSave') {
			require_once('caja_chica/reembolso_cajachica_save.php');
		}
		if ($_GET['opcion']=='deleteReembolsoCajaChica') {
			$codigo=$_GET['codigo'];
			$cod_tcc=$_GET['cod_tcc'];
			$cod_cc=$_GET['cod_cc'];
			require_once('caja_chica/reembolsocajachica_delete.php');
		}

		//renidicones		
		if ($_GET['opcion']=='ListaRendiciones') {
			require_once('caja_chica/rendiciones_list.php');
		}
		if ($_GET['opcion']=='ListaRendicionesDetalle') {
			$codigo=$_GET['codigo'];
			require_once('caja_chica/rendicionesdetalle_list.php');
		}
		//plan de cuentas caja chica
		if ($_GET['opcion']=='listPlanCuentasCajaChica') {
			require_once('caja_chica/plandecuentas_list.php');
		}
		if ($_GET['opcion']=='registerPlanCuentaCC') {
			$codigo=$_GET['codigo'];
			require_once('caja_chica/plandecuentas_registrer.php');
		}
		if ($_GET['opcion']=='editPlanCuentaCC') {
			$codigo=$_GET['codigo'];
			require_once('caja_chica/plandecuentas_edit.php');
		}
		if ($_GET['opcion']=='deletePlanCuentaCC') {
			$codigo=$_GET['codigo'];
			require_once('caja_chica/plandecuentas_delete.php');
		}

		if ($_GET['opcion']=='listConfigCuentas') {
			require_once('configuracion_cuentas/list.php');
		}


		if ($_GET['opcion']=='listCuentasAuxCC') {
			$codigo=$_GET['codigo'];
			require_once('cuentas_auxiliares/listCC.php');
		}
		if ($_GET['opcion']=='registerCuentaAuxCC') {
			$codigo=$_GET['codigo'];
			require_once('cuentas_auxiliares/registerCC.php');
		}
		if ($_GET['opcion']=='editCuentaAuxCC') {
			$codigo=$_GET['codigo'];
			$codigo_padre=$_GET['codigo_padre'];
			require_once('cuentas_auxiliares/editCC.php');
		}
		if ($_GET['opcion']=='deleteCuentaAuxCC') {
			$codigo=$_GET['codigo'];
			$codigo_padre=$_GET['codigo_padre'];
			require_once('cuentas_auxiliares/saveDeleteCC.php');
		}

		if ($_GET['opcion']=='saveCambiarDistribucionGastos') {
			$codigo=$_GET['codigo'];
			require_once('distribucion_gastosporcentaje/saveCambiarDistribucionGastos.php');
		}
		if ($_GET['opcion']=='DistribucionGastosDetalle') {
			$codigo=$_GET['codigo'];
			require_once('distribucion_gastosporcentaje/list_distribucion_detalle.php');
		}
		if ($_GET['opcion']=='deleteDistribucion') {
			$codigo=$_GET['codigo'];
			require_once('distribucion_gastosporcentaje/saveDeleteDistribucion.php');
		}
		//REPORTES
		if ($_GET['opcion']=='reportesProveedores') {
			require_once('caja_chica/rpt_proveedores_print.php');
		}
		//plan cuentas solicitudes factacion tipo pago
		if ($_GET['opcion']=='listPlanCuentasSolicitudesFacturacion') {
			require_once('simulaciones_servicios/plandecuentas_list.php');
		} 
		//plan cuentas solicitudes factacion areas
		if ($_GET['opcion']=='listPlanCuentasAreas') {
			require_once('simulaciones_servicios/plandecuentas_list_areas.php');
		}

		//flujo efectivo
		if ($_GET['opcion']=='listPlanFlujoEfectivo') {
			require_once('flujos_efectivo/plandecuentas_list_flujo.php');
		}
		if ($_GET['opcion']=='registerPlanCuentaEfectivo') {
			$fl=$_GET['fl'];
			require_once('flujos_efectivo/plandecuentas_registrer.php');
		}

		
		//abm dosificaciones
		if ($_GET['opcion']=='listDosificaciones') {
			require_once('dosificaciones/list.php');
		}
		if ($_GET['opcion']=='registerDosificacion') {						
			$codigo=$_GET['codigo'];
			require_once('dosificaciones/register.php');
		}
		if ($_GET['opcion']=='saveDosificacion') {			
			require_once('dosificaciones/save.php');
		}
		if ($_GET['opcion']=='deleteDosificacion') {
			$codigo=$_GET['codigo'];
			$sw=$_GET['sw'];
			$cod_sucursal=$_GET['cod_sucursal'];
			require_once('dosificaciones/saveDelete.php');
		}


		//incremento Salarial & retroactivos
		if ($_GET['opcion']=='incremento_salarial') {
			require_once('incremento_salarial/main.php'); //ok
		}

		if ($_GET['opcion']=='incremento_salarial_edit') {
			require_once('incremento_salarial/edit_incremento.php'); //ok
		}

		// REPORTES SUSCRIPCIÓN
		
		if ($_GET['opcion']=='reportesSuscripcion') {
			require_once('reportes_suscripcion/list.php');
		}

		// Gestión de responsabilidades y cargos
		// if ($_GET['opcion']=='listConfiguracionCargos') {
		// 	require_once('configuracion_cargo/conf_list.php'); 	     	// Config. Cargo - Lista
		// }
		// if ($_GET['opcion']=='configuracionCargosRegistro') {
		// 	require_once('configuracion_cargo/conf_register.php');   	// Config. Cargo - Registro
		// }
		// if ($_GET['opcion']=='configuracionCargosSave') {
		// 	require_once('configuracion_cargo/conf_save.php');	     	// Config. Cargo - Guardar
		// }
		// if ($_GET['opcion']=='configuracionCargosEditar') {
		// 	$codigo=$_GET['codigo'];
		// 	require_once('configuracion_cargo/conf_edit.php');	     	// Config. Cargo - Editar
		// }
		// if ($_GET['opcion']=='configuracionCargosUpdate') {
		// 	require_once('configuracion_cargo/conf_update.php');     	// Config. Cargo - Actualizar
		// }
		// if ($_GET['opcion']=='configuracionCargosEstado') {
		// 	require_once('configuracion_cargo/conf_estado.php');     	// Config. Cargo - Estado
		// }
		// if ($_GET['opcion']=='configuracionCargosLista') {
		// 	$codigo=$_GET['cod_config_aprobacion'];
		// 	require_once('configuracion_cargo/cargosLista.php');     	// Cargos - Lista
		// }
		// if ($_GET['opcion']=='configuracionCargosFunciones') {
		// 	$codigo = $_GET['codigo'];
		// 	require_once('configuracion_cargo/cargosFunciones.php'); 	// Funciones - Lista
		// }
		// if ($_GET['opcion']=='configuracionCargosAutoridades') {
		// 	$codigo = $_GET['codigo'];
		// 	require_once('configuracion_cargo/cargosAutoridades.php');  // Autoridades - Lista
		// }

		
		if ($_GET['opcion']=='cargosAutoridades') {
			$codigo = $_GET['codigo'];
			require_once('rrhh/cargosAutoridades.php');  // Autoridades - Lista
		}

		// AREAS - Asignación Cargos
		if ($_GET['opcion']=='registerAreasCargos') {
			$codigo=$_GET['codigo'];
			require_once('rrhh/registerAreasCargos.php');
		}
		if ($_GET['opcion']=='areasCargosSave') {
			require_once('rrhh/areasCargosSave.php');
		}
		if ($_GET['opcion']=='areasMapa') { 		// MAPA AREA(Cargos)
			require_once('rrhh/areasMapa.php');
		}
		if ($_GET['opcion']=='areasMapaPersonal') { 		// MAPA AREA(Cargos[Personal])
			require_once('rrhh/areasMapaPersonal.php');
		}
		
		// MANUALES DE APROBACIÓN
		// Lista de Configuración de Etapas
		if ($_GET['opcion']=='listaConfiguracionEtapas') {
			require_once('rrhh/etapaManualAprobacion.php');
		}
		// Lista de Manual para Aprobación
		if ($_GET['opcion']=='listaManualAprobacion') {
			require_once('rrhh/ManualAprobacionLista.php');
		}
		// Lista de Manuales Aprobados (Visto bueno)
		if ($_GET['opcion']=='listaManualesAprobados') {
			require_once('rrhh/ManualesAprobadosLista.php');
		}
		// Lista de Manuales Aprobados - GENERAL SIN FILTRO DE AREA (Visto bueno)
		if ($_GET['opcion']=='listaGeneralManualesAprobadosGeneral') {
			require_once('rrhh/ManualesAprobadosListaGeneral.php');
		}

		/***************************************************/
		// Lista MOF
		if ($_GET['opcion']=='listaMof') {
			require_once('rrhh/mofLista.php');
		}
		// Lista de Configuración de Etapas MOF
		if ($_GET['opcion']=='listaConfiguracionEtapasMof') {
			require_once('rrhh/etapaMofAprobacion.php');
		}
		// Lista de Manual para Aprobación
		if ($_GET['opcion']=='listaManualAprobacionMof') {
			require_once('rrhh/MofAprobacionLista.php');
		}
		// Lista de MoMOF Aprobados (Visto bueno)
		if ($_GET['opcion']=='listaMofAprobados') {
			require_once('rrhh/MofAprobadosLista.php');
		}
		
		/***************************************************/
		// Control de Versiones
		if ($_GET['opcion']=='listaControlVersiones') {
			require_once('rrhh/cargosListaControlVersion.php');
		}

		// Responsabilidades Generales IBNORCA
		if ($_GET['opcion']=='listaResponsabilidades') {
			require_once('responsabilidad_ibnorca/responsabilidadLista.php');
		}
		
		// Cargos - Interino Historial
		if ($_GET['opcion']=='listaInterinoHistorial') {
			require_once('rrhh/cargosListaInterinoHistorial.php');
		}

		// Nueva solicitud de Facturación de Afiliaciones 
		if ($_GET['opcion']=='register_solicitudfacturacion_cuotas') {
			require_once('solicitud_facturacion_manual/register_solicitud_facturacion_cuota.php');
		}
		
		// Verificar Visitas de Manuales Aprobados 
		if ($_GET['opcion']=='listaVistasManuales') {
			require_once('rrhh/listaVistasManuales.php');
		}

		
		// PLANILLA AGUINALDO - REPORTE VISITAS
		if ($_GET['opcion']=='reporteListaAguinaldoVistas') {
			require_once('aguinaldos/reporteListaAguinaldoVistas.php'); //ok
		}

		if($_GET['opcion']=='listaFacturaPendiente') {
			require_once('solicitud_facturacion_pendiente/list.php'); //ok
		}
	}else{
		//require("paginaprincipal.php");
	}

 ?>