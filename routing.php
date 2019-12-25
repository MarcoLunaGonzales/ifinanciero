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


		//RC_IVA Personal
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



		
		
		


	}else{
		//require("paginaprincipal.php");
	}

 ?>