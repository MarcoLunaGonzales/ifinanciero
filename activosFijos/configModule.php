<?php
//AQUI SE DEFINEN LAS VARIABLES PARA EL ABM
$table="perspectivas";
$moduleNameSingular="Perspectiva";
$moduleNamePlural="Perspectivas";

$urlList="?opcion=listPerspectivas";
$urlRegister="index.php?opcion=registerPerspectiva";
$urlEdit="index.php?opcion=editPerspectiva";
$urlDelete="index.php?opcion=deletePerspectiva";
$urlSave="?opcion=savePerspectivas";
//$urlSave="perspectivas/save.php";
$urlSaveEdit="?opcion=saveEditPerspectivas";
//$urlSaveEdit="perspectivas/saveEdit.php";
$urlSaveDelete="";


//ubicaciones
$moduleNameSingular2="Ubicacion";
$moduleNamePlural2="Ubicaciones";
$urlList2="?opcion=listUbicaciones";
$urlSave2="?opcion=saveUbicaciones";//query guardar
$urlEdit2="index.php?opcion=registerUbicacion";//form
$urlSaveEdit2="?opcion=saveUbicaciones";//guardar
$urlDelete2="index.php?opcion=deleteUbicacion";
$urlRegistrar_ubicacion="index.php?opcion=registerUbicacion";//form

//deprecionaciones
$urlListDepr="index.php?opcion=listDepreciaciones";
$urlDeleteDepr="index.php?opcion=deleteDepr";

//responsables
$moduleNameSingular3="Responsable";
$moduleNamePlural3="Responsables";
$urlList3="?opcion=listResponsables";
$urlSave3="?opcion=saveResponsables";
$urlEdit3="index.php?opcion=editResponsable";
$urlSaveEdit3="?opcion=saveEditResponsables";
$urlDelete3="index.php?opcion=deleteResponsable";
$urlRegister3="index.php?opcion=registerResponsable";

$urlDeleteUb="index.php?opcion=deleteUbicacion";

//tablas de depreciacion
$moduleNameSingular4="Rubro";
$moduleNamePlural4="Rubros";
$urlList4="?opcion=listDepreciaciones";
$urlSave4="?opcion=saveDepreciaciones";
$urlEdit4="index.php?opcion=editDepreciacion";
$urlSaveEdit4="?opcion=saveEditDepreciacion";
$urlDelete4="index.php?opcion=deleteDepreciacion";
$urlRegistrar_depreciacion="index.php?opcion=registerDepreciacion";

//tipos de bienes
$moduleNameSingular5="Tipo de Bien";
$moduleNamePlural5="Tipos de Bienes";
$urlList5="?opcion=listaTiposBienes";
$urlList52="index.php?opcion=listaTiposBienes";
$urlSave5="?opcion=saveTiposBienes";
$urlDelete5="index.php?opcion=deleteTiposBienes";

$urlEdit5="index.php?opcion=registerTipoBien";
$urlRegistrar_tiposbienes="index.php?opcion=registerTipoBien";

//activos fijos
$moduleNameSingular6="Activo Fijo";
$moduleNamePlural6="Activos Fijos";
$urlList6="?opcion=activosfijosLista";
$urlSave6="?opcion=saveActivosfijos";//guardar
$urlSaveTransfer="?opcion=saveTransferActivosfijos";//guardar transferencia de af
$urlSaveReevaluoAF="?opcion=saveReevaluoAF";//
$urlEdit6="index.php?opcion=activofijoRegister";//form

$urlEditTransfer="index.php?opcion=activofijoTransferir";
$urlRevaluarAF="index.php?opcion=activofijoRevaluar";
$urlafAccesorios="index.php?opcion=activofijoAccesorios";
$urlafEventos="index.php?opcion=activofijoEventos";

$urlSaveEdit6="?opcion=saveActivosfijos";//guardar
$urlDelete6="index.php?opcion=deleteDepreciacion";
$urlRegistrar_activosfijos="index.php?opcion=activofijoRegister";//form
//$printAlta="index.php?opcion=activofijoPrintAlta";
$printAlta="activosFijos/afPrintAlta.php";
$printDepreciacion1="activosFijos/afPrintDepreciacion1.php";//de un solo mes

$printAFCustodia="activosFijos/afPrintCustodia.php";//

//ejecutar LA DEDRECIACION
$moduleNameSingular7="Depreciaciones";
$moduleNamePlural7="Depreciaciones";
$urlList7="?opcion=ejecutarDepreciacionLista";
$urlSave7="?opcion=executeDepreciacionesSave";
$urlGenerarCompDepreciacion="?opcion=executeComprobanteDepreciacion";
$urlEdit7="index.php?opcion=editDepreciacion";
$urlSaveEdit7="?opcion=saveEditDepreciacion";
$urlDelete7="index.php?opcion=deleteDepreciacion";
$urlRegistrar7="index.php?opcion=ejecutarDepreciacionesRegister";
//$printAlta="index.php?opcion=activofijoPrintAlta";
$printDepreciacionMes="activosFijos/afPrintDepreciacionMes.php";

//ejecutar LA ASIGNACION
$moduleNameSingular8="Asignacion";
$moduleNamePlural8="Asignaciones";
$urlList8="?opcion=asignacionesLista";
$urlSave8="?opcion=asignacionesSave";
$urlEdit8="index.php?opcion=editAsignacion";
$urlSaveEdit8="?opcion=saveAsignacion";
$urlDelete8="index.php?opcion=deleteAsignaciones";
$urlRegistrar_asignaciones="index.php?opcion=asignacionesRegister";
//$printAlta="index.php?opcion=activofijoPrintAlta";
//$print="activosfijos/afPrintDepreciacionMes.php";

//proveedores
$moduleNameSingularProveedores="Proveedor";
$moduleNamePluralProveedores="Proveedores";
$urlListProv="?opcion=provLista";
$urlSaveProv="?opcion=provSave";
$urlEditProv="index.php?opcion=provForm";
$urlSaveProv="?opcion=saveProv";
$urlDeleteProv="index.php?opcion=deleteProv";
$urlRegistrarProv="index.php?opcion=provForm";
//$printAlta="index.php?opcion=activofijoPrintAlta";
//$print="activosfijos/afPrintDepreciacionMes.php";


$rpt01 = "?opcion=rptxrubrosxmes";
$rpt01procesar="activosFijos/afPrintDepreciacionxrubroxmes.php";
//devolver AF all
//$urldevolver_af_all="activosFijos/saveAsignacionAll.php";

$urlprint_contabilizacion_af="activosfijos/executeComprobanteActivoFijo.php";
$urlImp="comprobantes/impActivosFijos.php";

?>
