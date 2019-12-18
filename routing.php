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
		if ($_GET['opcion']=='deletePlantillaCosto') {
			require_once('plantillas_costos/saveDelete.php');
		}
		if ($_GET['opcion']=='listPlantillasCostosAdmin') {
			require_once('plantillas_costos/listAdmin.php');
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

        //MES EN CURSO
        if ($_GET['opcion']=='mesCurso') {
			require_once('mes_curso/list.php');
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

		if ($_GET['opcion']=='activofijoAccesorios') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/activofijoAccesorios.php');
		}
		if ($_GET['opcion']=='activofijoEventos') {
			$codigo=$_GET['codigo'];
			require_once('activosFijos/activofijoEventos.php');
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
		
		if ($_GET['opcion']=='deletepersonal') {
			$codigo=$_GET['codigo'];
			require_once('personal/deletepersonal.php');
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

		
		

	}else{
		//require("paginaprincipal.php");
	}

 ?>