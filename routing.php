<?php 
	
	if(isset($_GET['opcion'])){


		//*********************************************   CONTABILIDAD BASICA         ********************************
		//PLAN DE CUENTAS
		if ($_GET['opcion']=='listPlanCuentas') {
			require_once('plan_cuentas/list.php');
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
			require_once('cuentas_auxiliares/list.php');
		}
		if ($_GET['opcion']=='registerCuentaAux') {
			$codigo=$_GET['codigo'];
			require_once('cuentas_auxiliares/register.php');
		}
		if ($_GET['opcion']=='editCuentaAux') {
			$codigo=$_GET['codigo'];
			$codigo_padre=$_GET['codigo_padre'];
			require_once('cuentas_auxiliares/edit.php');
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

		//COMPROBANTES
		if ($_GET['opcion']=='listComprobantes') {
			require_once('comprobantes/list.php');
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

		//TIPO DE CAMBIO
		if ($_GET['opcion']=='tipoDeCambio') {
			require_once('tipos_cambios/list.php');
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
        
        //SIMULACIONES PLANTILLA SERVICIO
        if ($_GET['opcion']=='listSimulacionesServicios') {
			require_once('simulaciones_servicios/list.php');
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
         
        //MES EN CURSO
        if ($_GET['opcion']=='mesCurso') {
			require_once('mes_curso/list.php');
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
        if ($_GET['opcion']=='deleteSolicitudRecursos') {
			require_once('solicitudes/saveDelete.php');
		}
		if ($_GET['opcion']=='editSolicitudRecursos') {
			require_once('solicitudes/editSolicitudRecursos.php');
		}
		//ESTADOS DE CUENTAS
		if ($_GET['opcion']=='configuracionEstadosCuenta') {
			require_once('estados_cuenta/list.php');
		}
		if ($_GET['opcion']=='registerConfiguracionEstadoCuenta') {
			require_once('estados_cuenta/register.php');
		}
		if ($_GET['opcion']=='deleteConfiguracionEstadoCuenta') {
			require_once('estados_cuenta/saveDelete.php');
		}
		

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

		//Distribucion Gastos y Porcentajes
		if ($_GET['opcion']=='listDistribucionGasto') {
			require_once('distribucion_gastosporcentaje/list.php');
		}

		if ($_GET['opcion']=='editDistribucionGastos') {
			require_once('distribucion_gastosporcentaje/editDistribucionGastos.php');
		}

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

		//renidicones		
		if ($_GET['opcion']=='ListaRendiciones') {
			require_once('caja_chica/rendiciones_list.php');
		}
		if ($_GET['opcion']=='ListaRendicionesDetalle') {
			$codigo=$_GET['codigo'];
			require_once('caja_chica/rendicionesdetalle_list.php');
		}
		

	}else{
		//require("paginaprincipal.php");
	}

 ?>