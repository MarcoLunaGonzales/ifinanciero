<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';
set_time_limit (0);
$dbh = new Conexion();

$codigo=$_POST["codigo"];
session_start();

$codPlantillaCosto=$_POST["plantilla"];
$codSimulacion=$_POST["simulacion"];
$ut_i=$_POST['utilidad'];
$dia=empty($_POST['dia']) ? '' : $_POST['dia'];


$monto=$_POST['monto'];
$cantidad=$_POST['cantidad'];
$obs=$_POST['descripcion'];
$alcance=$_POST['alcance'];
$habilitado=$_POST['habilitado'];
$unidad=$_POST['unidad'];
$fijo=$_POST['precio_fijo'];
$productos="";
$atributos= json_decode($_POST['productos']);
$anio=$_POST['anio'];
$anio_fila=$_POST['anio_fila'];
$iteracion=$_POST['iteracion'];
$des_serv=$_POST['des_serv'];
$oficina_servicio=$_POST['oficina_servicio'];

$iaf_primario   = empty($_POST['iaf_primario'])  ? [] : $_POST['iaf_primario'];
$iaf_secundario = empty($_POST['iaf_secundario'])? [] : $_POST['iaf_secundario'];

$objeto_servicio=$_POST['objeto_servicio'];
$tipo_servicio=$_POST['tipo_servicio'];
$mod_cliente=$_POST['mod_cliente'];
$mod_region_cliente=$_POST['mod_region_cliente'];
$mod_tipo_cliente=$_POST['mod_tipo_cliente'];
$normas_tiposervicio=json_decode($_POST['normas_tiposervicio']);
$normas_tiposerviciotext=$_POST['normas_tiposerviciotext'];
$mod_afnor=$_POST['mod_afnor'];

// Cod_servicio
$cod_servicio = $_POST['cod_servicio'];

$sqlEditSet="";
$sqlEditSetTCP="";
if($objeto_servicio!=""){
  $sqlEditSet=",cod_objetoservicio='$objeto_servicio'";
}
if($tipo_servicio!=""){
  $sqlEditSet.=",id_tiposervicio='$tipo_servicio'";
}
  $sqlEditSet.=",afnor='$mod_afnor'";

if($mod_cliente!=""){
  $sqlEditSetTCP.=",cod_cliente='$mod_cliente'";
  $sqlEditSet.=",cod_cliente='$mod_cliente'";
}
if($mod_region_cliente!=""){
  $sqlEditSetTCP.=",cod_tipoclientenacionalidad='$mod_region_cliente'";
}
if($mod_tipo_cliente!=""){
  $sqlEditSetTCP.=",cod_tipocliente='$mod_tipo_cliente'";
}

if(obtenerEntradaSimulacionServicio($codSimulacion)==1){
  $sqlDetallesAuditores="UPDATE simulaciones_servicios_auditores SET dias=0 where cod_simulacionservicio=$codSimulacion";
  $stmtDetallesAuditores = $dbh->prepare($sqlDetallesAuditores);
  $stmtDetallesAuditores->execute(); 
}

if($_POST['tcs']==0){
  $tipo_atributo=1;
  $sqlUpdatePlantilla="UPDATE simulaciones_servicios SET  cod_unidadorganizacional='$oficina_servicio',descripcion_servicio='$des_serv', alcance_propuesta='$alcance', utilidad_minima='$ut_i',dias_auditoria='$dia',productos='$productos',
  cod_servicio='$cod_servicio'
  $sqlEditSetTCP where codigo=$codSimulacion";
}else{
  $tipo_atributo=2;
  $atributosDias= json_decode($_POST['sitios_dias']);
  $sqlUpdatePlantilla="UPDATE simulaciones_servicios SET  cod_unidadorganizacional='$oficina_servicio',descripcion_servicio='$des_serv', alcance_propuesta='$alcance', utilidad_minima='$ut_i',dias_auditoria='$dia',sitios='$productos',
  cod_servicio='$cod_servicio' 
  $sqlEditSet where codigo=$codSimulacion";
}

$stmtUpdatePlantilla = $dbh->prepare($sqlUpdatePlantilla);
$stmtUpdatePlantilla->execute();


  /*************************************************************************************************************************************************************/
  // Limpiar archivos
  $arrayIAFprimario           = empty($_POST['iaf_primario']) ? [] : $_POST['iaf_primario'];
  $arrayInocuidad             = empty($_POST['iaf_secundario']) ? [] : $_POST['iaf_secundario'];
  $arrayOrgnismoCertificador  = empty($_POST['organismo_certificador']) ? [] : $_POST['organismo_certificador'];
  // NUEVO SERVICIO IAF - MULTIPLE
  $values = [];
  foreach($arrayIAFprimario as $arrayIAF){
    $values[]    = "($codSimulacion, $arrayIAF)";
  }
  // Limpiar Registros pasados
  $sqlDelete = "DELETE FROM simulaciones_servicios_iaf WHERE cod_simulacionservicio = '".$codSimulacion."'";
  $stmt      = $dbh->prepare($sqlDelete);
  $stmt->execute();
  if(count($values) > 0){
    // Actualizar
    $sqlInsert = "INSERT INTO simulaciones_servicios_iaf (cod_simulacionservicio, cod_iaf) VALUES\n" . implode(",\n", $values);
    $stmt      = $dbh->prepare($sqlInsert);
    $stmt->execute();
  }
  
  // NUEVAS CATEGORIAS INOCUIDAD - MULTIPLE
  $values = [];
  foreach($arrayInocuidad as $arrayIno){
    $values[]    = "($codSimulacion, $arrayIno)";
  }
  // Limpiar Registros pasados
  $sqlDelete = "DELETE FROM simulaciones_servicios_categoriasinocuidad WHERE cod_simulacionservicio = '".$codSimulacion."'";
  $stmt      = $dbh->prepare($sqlDelete);
  $stmt->execute();
  if(count($values) > 0){
    // Actualizar
    $sqlInsert = "INSERT INTO simulaciones_servicios_categoriasinocuidad (cod_simulacionservicio, cod_categoriainocuidad) VALUES\n" . implode(",\n", $values);
    $stmt      = $dbh->prepare($sqlInsert);
    $stmt->execute();
  }
  
  // NUEVAS ORGANISMO CERTIFICADOR - MULTIPLE
  $values = [];
  foreach($arrayOrgnismoCertificador as $arrayOC){
    $values[]    = "($codSimulacion, $arrayOC)";
  }
  // Limpiar Registros pasados
  $sqlDelete = "DELETE FROM simulaciones_servicios_organismocertificador WHERE cod_simulacionservicio = '".$codSimulacion."'";
  $stmt      = $dbh->prepare($sqlDelete);
  $stmt->execute();
  if(count($values) > 0){
    // Actualizar
    $sqlInsert = "INSERT INTO simulaciones_servicios_organismocertificador (cod_simulacionservicio, cod_orgnismocertificador) VALUES\n" . implode(",\n", $values);
    $stmt      = $dbh->prepare($sqlInsert);
    $stmt->execute();
  }
  /*************************************************************************************************************************************************************/


//insertarNormas
if($tipo_servicio==2778){ //sistemas integrados 
      // $dbhD = new Conexion();
      // $sqlD="DELETE FROM simulaciones_servicios_normas where cod_simulacionservicio=$codSimulacion";
      // $stmtD = $dbhD->prepare($sqlD);
      // $stmtD->execute();     
      // if(isset($_POST['normas_tiposervicio'])){ 
      //  $normasTipo=json_decode($_POST['normas_tiposervicio']);
      //  for ($ntp=0; $ntp < count($normasTipo); $ntp++) { 
      //   $codigoNormasTipo=$normasTipo[$ntp];       
      //   $sqlInsertNormas="INSERT INTO simulaciones_servicios_normas (cod_simulacionservicio,cod_tiposervicio,cod_norma,observaciones) 
      //     VALUES ('".$codSimulacion."','".$tipo_servicio."','".$codigoNormasTipo."','')";
      //    $stmtInsertNormas = $dbh->prepare($sqlInsertNormas);
      //    $flagsuccess=$stmtInsertNormas->execute();
      //  }
      //  if($normas_tiposerviciotext!=""){
      //   $normasTipoText=explode(",",$normas_tiposerviciotext);
      //   for ($ntp=0; $ntp < count($normasTipoText); $ntp++) { 
      //   $nombreNormasTipo=$normasTipoText[$ntp];       
      //   $sqlInsertNormas="INSERT INTO simulaciones_servicios_normas (cod_simulacionservicio,cod_tiposervicio,cod_norma,observaciones) 
      //     VALUES ('".$codSimulacion."','".$tipo_servicio."',0,'".$nombreNormasTipo."')";
      //    $stmtInsertNormas = $dbh->prepare($sqlInsertNormas);
      //    $flagsuccess=$stmtInsertNormas->execute();
      //   }    
      //  }
      // }
  }
  /****************************/
  // Se limpia campos NORMAS
  $dbhD = new Conexion();
  $sqlD="DELETE FROM simulaciones_servicios_normas where cod_simulacionservicio=$codSimulacion";
  $stmtD = $dbhD->prepare($sqlD);
  $stmtD->execute();  
  // OTRAS NORMAS
  if(isset($_POST['normas_tiposerviciotext'])){
    $normasTipoText=explode(",",$_POST['normas_tiposerviciotext']);
    for ($ntp=0; $ntp < count($normasTipoText); $ntp++) { 
      $nombreNormasTipo = $normasTipoText[$ntp];       
      $sqlInsertNormas  = "INSERT INTO simulaciones_servicios_normas (cod_simulacionservicio,cod_tiposervicio,cod_norma,observaciones) 
                          VALUES ('".$codSimulacion."','".$tipo_servicio."',0,'".$nombreNormasTipo."')";
      $stmtInsertNormas = $dbh->prepare($sqlInsertNormas);
      $flagsuccess      = $stmtInsertNormas->execute();
    }
  }
  // NORMAS NACIONALES
  if(isset($_POST['normas_nac'])){
    $normasTipo=json_decode($_POST['normas_nac']);
    for ($ntp=0; $ntp < count($normasTipo); $ntp++) { 
      $codigoNormasTipo=$normasTipo[$ntp];       
      $sqlInsertNormas="INSERT INTO simulaciones_servicios_normas (cod_simulacionservicio,cod_tiposervicio,cod_norma,observaciones, catalogo) 
        VALUES ('".$codSimulacion."','".$tipo_servicio."','".$codigoNormasTipo."','','L')";
      $stmtInsertNormas = $dbh->prepare($sqlInsertNormas);
      $flagsuccess=$stmtInsertNormas->execute();
    }
  }
  // NORMAS INTERNACIONALES
  if(isset($_POST['normas_int'])){ 
    $normasTipo=json_decode($_POST['normas_int']);
    for ($ntp=0; $ntp < count($normasTipo); $ntp++) { 
      $codigoNormasTipo=$normasTipo[$ntp];       
      $sqlInsertNormas="INSERT INTO simulaciones_servicios_normas (cod_simulacionservicio,cod_tiposervicio,cod_norma,observaciones, catalogo) 
        VALUES ('".$codSimulacion."','".$tipo_servicio."','".$codigoNormasTipo."','','I')";
      $stmtInsertNormas = $dbh->prepare($sqlInsertNormas);
      $flagsuccess=$stmtInsertNormas->execute();
    }
  }
  /******************************************************/

if($cantidad==0){
	$cantidad=1;
}

//SITIOS 0 PRODUCTOS
$sqlDetAt="SELECT * FROM simulaciones_servicios_atributos where cod_simulacionservicio=$codSimulacion";
$stmtDetAt = $dbh->prepare($sqlDetAt);
$stmtDetAt->execute();

  $dbhA = new Conexion();
  $sqlA="DELETE FROM simulaciones_servicios_atributosauditores where cod_simulacionservicioatributo in (SELECT codigo from simulaciones_servicios_atributos where cod_simulacionservicio=$codSimulacion)";
  $stmtA = $dbhA->prepare($sqlA);
  $stmtA->execute();

  // Nueva función se obtiene el valor del CODIGO SERVICIO ATRIBUTOS
  // Se obtiene el codigo de atributos normas
  $codigo_atributonorma = '';
  while ($rowPreAt = $stmtDetAt->fetch(PDO::FETCH_ASSOC)) {
    // Codigo Atributo Norma
    $codigo_atributonorma = $rowPreAt['codigo'];

    $codigoDetAt=$rowPreAt['codigo'];
    $dbhA = new Conexion();
    $sqlDel="DELETE FROM simulaciones_servicios_atributosdias where cod_simulacionservicioatributo=$codigoDetAt";
    $stmtDel = $dbhA->prepare($sqlDel);
    $stmtDel->execute();
  }

  // NUEVO CAMPO PARA LA ELIMINACIÓN DE ATRIBUTOS NORMAS PARA EVITAR TENER DUPLICADOS DE REGISTROS
  // Limpiamos tabla de Atributos Normas
  $dbhA = new Conexion();
  $sqlA="DELETE FROM simulaciones_servicios_atributosnormas where cod_simulacionservicioatributo=$codigo_atributonorma";
  $stmtA = $dbhA->prepare($sqlA);
  $stmtA->execute();

  
  $dbhA = new Conexion();
  $sqlA="DELETE FROM simulaciones_servicios_atributos where cod_simulacionservicio=$codSimulacion";
  $stmtA = $dbhA->prepare($sqlA);
  $stmtA->execute();

  
  //simulaciones_serviciosauditores
          $nC=cantidadF($atributos);
          for($att=0;$att<$nC;$att++){
              $codigoAtributo=$atributos[$att]->codigo;
              $nombreAtributo=$atributos[$att]->nombre;
              $direccionAtributo=$atributos[$att]->direccion;
              $marcaAtributo=$atributos[$att]->marca;
              $normaAtributo=$atributos[$att]->norma;
              $selloAtributo=$atributos[$att]->sello;

              $paisAtributo=$atributos[$att]->pais;
              $estadoAtributo=$atributos[$att]->estado;
              $ciudadAtributo=$atributos[$att]->ciudad;

              $codSimulacionServicioAtributo=obtenerCodigoSimulacionServicioAtributo();
              $sqlDetalleAtributos="INSERT INTO simulaciones_servicios_atributos (codigo,cod_simulacionservicio, nombre, direccion, cod_tipoatributo,marca,norma,nro_sello,cod_pais,cod_estado,cod_ciudad) 
              VALUES ('$codSimulacionServicioAtributo','$codSimulacion', '$nombreAtributo', '$direccionAtributo', '$tipo_atributo','$marcaAtributo','$normaAtributo','$selloAtributo','$paisAtributo','$estadoAtributo','$ciudadAtributo')";
              $stmtDetalleAtributos = $dbh->prepare($sqlDetalleAtributos);
              $stmtDetalleAtributos->execute();
              $auditoresDias = 0;

              
              /*************************************************/
              // Normas Nacionales
              if(isset($atributos[$att]->atr_norma_nac)){
                $atrCodNorma=$atributos[$att]->atr_norma_nac;
                $normasFila=explode(",",$atrCodNorma);
                for ($ni=0; $ni < count($normasFila); $ni++) { 
                  $codNorma=$normasFila[$ni];
                  $sqlDetalleAtributosNormas="INSERT INTO simulaciones_servicios_atributosnormas (cod_simulacionservicioatributo, cod_norma, precio,cantidad, catalogo) 
                  VALUES ('$codSimulacionServicioAtributo', '$codNorma', '10',1, 'L')";
                  $stmtDetalleAtributosNormas = $dbh->prepare($sqlDetalleAtributosNormas);
                  $flagsuccess=$stmtDetalleAtributosNormas->execute();
                }
              }
              // Normas Internacionales
              if(isset($atributos[$att]->atr_norma_int)){
                $atrCodNorma=$atributos[$att]->atr_norma_int;
                $normasFila=explode(",",$atrCodNorma);
                for ($ni=0; $ni < count($normasFila); $ni++) { 
                  $codNorma=$normasFila[$ni];
                  $sqlDetalleAtributosNormas="INSERT INTO simulaciones_servicios_atributosnormas (cod_simulacionservicioatributo, cod_norma, precio,cantidad, catalogo) 
                  VALUES ('$codSimulacionServicioAtributo', '$codNorma', '10',1, 'I')";
                  $stmtDetalleAtributosNormas = $dbh->prepare($sqlDetalleAtributosNormas);
                  $flagsuccess=$stmtDetalleAtributosNormas->execute();
                }
              }
              /*************************************************/

            if($_POST['tcs']==0){
                //$direccionAtributo="";
                // VERIFICAR FUNCIONALIDAD 
              // $normasFila=explode(",",$normaCodAtributo);
              //   for ($ni=0; $ni < count($normasFila); $ni++) { 
              //    $codNorma=$normasFila[$ni];
              //     $sqlDetalleAtributosNormas="INSERT INTO simulaciones_servicios_atributosnormas (cod_simulacionservicioatributo, cod_norma, precio,cantidad) 
              //    VALUES ('$codSimulacionServicioAtributo', '$codNorma', '10',1)";
              //    $stmtDetalleAtributosNormas = $dbh->prepare($sqlDetalleAtributosNormas);
              //    $stmtDetalleAtributosNormas->execute();
              //  }
              }else{
                /**
                 * ESTA FUNCIONALIDAD ES OBSOLETA Y SE REFERIA A LOS DIAS QUE SE PLANIFICABA POR CADA AUDITOR
                 * EN SITIO
                 */
                // $auditoresDias=json_decode($_POST['auditoresDias']);               
                //  $nCDias=cantidadF($atributosDias);
                //     for($jj=0;$jj<$nCDias;$jj++){
                //        $codigoAtributoDias=$atributosDias[$jj]->codigo_atributo;
                //        $anioAtributoDias=$atributosDias[$jj]->anio;
                //        $diasAtributoDias=$atributosDias[$jj]->dias;
                //        if($codigoAtributoDias==$codigoAtributo){
                //         $sqlDetalleAtributos="INSERT INTO simulaciones_servicios_atributosdias (cod_simulacionservicioatributo, dias, cod_anio) 
                //         VALUES ('$codSimulacionServicioAtributo', '$diasAtributoDias', '$anioAtributoDias')";
                //         $stmtDetalleAtributos = $dbh->prepare($sqlDetalleAtributos);
                //         $stmtDetalleAtributos->execute();
                //         /*$sqlDetalleAu="UPDATE simulaciones_servicios_atributosauditores SET estado=0 where cod_simulacionservicioatributo=$codSimulacionServicioAtributo and cod_anio=$anioAtributoDias";
                //         $stmtDetalleAu = $dbh->prepare($sqlDetalleAu);
                //         $stmtDetalleAu->execute();*/
                //          //aumentar dias a los auditores
                //         $diasAtributoDiasAux=0;
                //          for ($al=0; $al < count($auditoresDias[$jj]); $al++) { 
                //           $valorAuditorDia=explode("####",$auditoresDias[$jj][$al]);
                //           $codigoAuditor=$valorAuditorDia[0];    
                //           if($valorAuditorDia[1]=="SI"){
                //              $sqlDetalleAtributosAud="INSERT INTO simulaciones_servicios_atributosauditores (cod_simulacionservicioatributo, cod_auditor, cod_anio,estado) 
                //              VALUES ('$codSimulacionServicioAtributo', '$codigoAuditor', '$anioAtributoDias',1)";
                //              $stmtDetalleAtributosAud = $dbh->prepare($sqlDetalleAtributosAud);
                //              $stmtDetalleAtributosAud->execute();  
                //              $diasAtributoDiasAux=$diasAtributoDias;
                               
                //           }else{
                //              $sqlDetalleAtributosAud="INSERT INTO simulaciones_servicios_atributosauditores (cod_simulacionservicioatributo, cod_auditor, cod_anio,estado) 
                //              VALUES ('$codSimulacionServicioAtributo', '$codigoAuditor', '$anioAtributoDias',0)";
                //              $stmtDetalleAtributosAud = $dbh->prepare($sqlDetalleAtributosAud);
                //              $stmtDetalleAtributosAud->execute();
                //              $diasAtributoDiasAux=0;
                //           }
                //           if(obtenerEntradaSimulacionServicio($codSimulacion)==1){
                //           $cantidadDiasAnterior=obtenerDiasAuditorSimulacionServicio($codigoAuditor);
                //           $cantidadDiasNuevo=$cantidadDiasAnterior+$diasAtributoDiasAux; 
                //           $sqlDetallesAuditores="UPDATE simulaciones_servicios_auditores SET dias=$cantidadDiasNuevo where codigo=$codigoAuditor";
                //           $stmtDetallesAuditores = $dbh->prepare($sqlDetallesAuditores);
                //           $stmtDetallesAuditores->execute();
                //           }
                //           echo $sqlDetallesAuditores;
                //          }
                //          if(count($auditoresDias[$jj])==0){
                //            if(obtenerEntradaSimulacionServicio($codSimulacion)==1){
                //           $cantidadDiasAnterior=obtenerDiasAuditorSimulacionServicio($codigoAuditor);
                //           $cantidadDiasNuevo=$cantidadDiasAnterior+$diasAtributoDiasAux; 
                //           $sqlDetallesAuditores="UPDATE simulaciones_servicios_auditores SET dias=$cantidadDiasNuevo where codigo=$codigoAuditor";
                //           $stmtDetallesAuditores = $dbh->prepare($sqlDetallesAuditores);
                //           $stmtDetallesAuditores->execute();
                //           }
                //          }
                //        }           
                //     }
              }         
              
         }
         //FIN simulaciones_serviciosauditores


if($fijo!=""){
	$cliente=obtenerCodigoClienteSimulacion($codSimulacion);
	//$productosLista=explode(",", $productos);
        $codTC=$mod_tipo_cliente;
        $nacional=$mod_region_cliente;
        $suma=0;
        for ($i=0; $i < count($atributos); $i++) {
          $aux=obtenerCostoTipoClienteSello(($i+1),$codTC,$nacional);
           if($aux==0){
            $aux=$aux2;
           }else{            
            $aux2=$aux;
           }
           $suma+=$aux;          
        }
       $cantidad=1;
       $monto=$suma; 
}

/*$sqlDetallesAuditores="UPDATE simulaciones_servicios_auditores SET dias=1 where cod_simulacionservicio=$codSimulacion and dias=0";
$stmtDetallesAuditores = $dbh->prepare($sqlDetallesAuditores);
$stmtDetallesAuditores->execute();*/

$sqlDetalles="UPDATE simulaciones_servicios_tiposervicio SET observaciones='$obs',cantidad_editado=$cantidad,monto=$monto,habilitado=$habilitado,cod_tipounidad=$unidad,cod_anio=$anio_fila where codigo=$codigo";
$stmtDetalles = $dbh->prepare($sqlDetalles);
$stmtDetalles->execute();

      
/*
      $sqlDelete="DELETE FROM simulaciones_cf where cod_simulacionservicio=$codSimulacion and cod_anio=$anio_fila";  
      $stmtDelete = $dbh->prepare($sqlDelete);
      $stmtDelete->execute();

  //costos Fijos en tabla
      $cuentasFijas=obtenerListaCuentasPlantillasCostoFijoServicio($codPlantillaCosto);
      while ($rowFijo = $cuentasFijas->fetch(PDO::FETCH_ASSOC)) {
         $nombreCuentaFijo=$rowFijo['nombre'];
         $numeroCuentaFijo=$rowFijo['numero'];
         $codCuentaFijo=$rowFijo['cod_cuenta'];
         $codPartidaFijo=$rowFijo['cod_partidapresupuestaria'];
         $tipoFijo=$rowFijo['tipo'];

         $precioLocalX=obtenerPrecioServiciosSimulacionPorAnio($codSimulacion,$anio_fila);
         $precioRegistrado=obtenerPrecioRegistradoPlantilla($codPlantillaCosto);
         $nCursos=obtenerCantidadAuditoriasPlantilla($codPlantillaCosto); 
         $porcentPrecios=($precioLocalX)/($precioRegistrado);
         if($tipoFijo==1){ 
         $anioSim= date("Y");  
         $monto=ejecutadoEgresosMes($globalUnidad,((int)$anioSim-1),12,$areaGeneralPlantilla,1,$numeroCuentaFijo);          
         }else{
          $monto=obtenerListaCuentasPlantillasCostoFijoServicioManual($codCuentaFijo,$codPartidaFijo,$codPlantillaCosto);
         }
         $montoUnidad=$monto*$porcentPrecios; 
         $dbh = new Conexion();
         $sqlFijos="INSERT INTO simulaciones_cf (cod_simulacionservicio, cod_simulacioncosto,cod_partidapresupuestaria,cod_cuenta,monto,cantidad,monto_total,cod_anio) 
         VALUES ('".$codSimulacion."',0,'".$codPartidaFijo."','".$codCuentaFijo."','".$montoUnidad."',1,'".$montoUnidad."','".$anio_fila."')";
         $stmtFijos = $dbh->prepare($sqlFijos);
         $stmtFijos->execute();
      } 
      */
echo $anio."WWW".$iteracion;
?>
