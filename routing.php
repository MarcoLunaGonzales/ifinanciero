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
        
        //SIMULACIONES DE COSTO
		if ($_GET['opcion']=='listSimulacionesCostos') {
			require_once('simulaciones_costos/list.php');
		}
		if ($_GET['opcion']=='listSimulacionesCostosAdmin') {
			require_once('simulaciones_costos/listAdmin.php');
		}

        //MES EN CURSO
        if ($_GET['opcion']=='mesCurso') {
			require_once('mes_curso/list.php');
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
		
		

	}else{
		//require("paginaprincipal.php");
	}

 ?>