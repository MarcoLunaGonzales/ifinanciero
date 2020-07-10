	//  CREAR EL COMPROBANTE DEBENGADOç

 	// Preparamos
$stmtSolicitud = $dbh->prepare("SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo where sr.cod_estadoreferencial=1 and sr.codigo=$codigo");
// Ejecutamos
$stmtSolicitud->execute();
// bindColumn
$stmtSolicitud->bindColumn('unidad', $unidadX);
$stmtSolicitud->bindColumn('area', $areaX);
$stmtSolicitud->bindColumn('cod_unidadorganizacional', $cod_unidadX);
$stmtSolicitud->bindColumn('cod_area', $cod_areaX);
$stmtSolicitud->bindColumn('cod_simulacion', $codSimulacion);
$stmtSolicitud->bindColumn('cod_proveedor', $codProveedor);
$stmtSolicitud->bindColumn('cod_simulacionservicio', $codSimulacionServicio);
$stmtSolicitud->bindColumn('numero', $numeroSol);
$stmtSolicitud->bindColumn('idServicio', $idServicioX);

while ($rowSolicitud = $stmtSolicitud->fetch(PDO::FETCH_BOUND)) {
      $unidadX=$unidadX;
      $areaX=$areaX;
      $cod_unidadX=$cod_unidadX;
      $cod_areaX=$cod_areaX;
      $codSimulacion=$codSimulacion;
      $codProveedor=$codProveedor;
      $codSimulacionServicio=$codSimulacionServicio;
      $numeroSol=$numeroSol;

      if($codSimulacion!=0){
        $nombreCliente="";
        $nombreSimulacion=nameSimulacion($codSimulacion);
      }else{
        $nombreCliente=nameClienteSimulacionServicio($codSimulacionServicio);
        $nombreSimulacion=nameSimulacionServicio($codSimulacionServicio);
      }
}

 	//crear el comprobante
    $codComprobante=obtenerCodigoComprobante();

    $codGestion=date("Y");
    $tipoComprobante=3;
    $nroCorrelativo=numeroCorrelativoComprobante($globalGestion,$cod_unidadX,3);
    
    $facturaCabecera=obtenerNumeroFacturaSolicitudRecursos($codigo);

    $fechaHoraActual=date("Y-m-d H:i:s");
    //glosa detalle
    //"".." F/".$numeroFac." ".$proveedorX." ".$detalleX
    $IdTipo=obtenerTipoServicioPorIdServicio($idServicioX);
    $codObjeto=obtenerCodigoObjetoServicioPorIdSimulacion($codSimulacionServicio);

    $datosServicio=obtenerServiciosTipoObjetoNombre($codObjeto)." - ".obtenerServiciosClaServicioTipoNombre($IdTipo);


    $glosa="Beneficiario: ".obtenerProveedorSolicitudRecursos($codigo)." ".$datosServicio."  F/".$facturaCabecera." ".$nombreCliente." SR ".$numeroSol;
    $userSolicitud=obtenerPersonalSolicitanteRecursos($codigo);
    $unidadSol=$cod_unidadX;
    $areaSol=$cod_areaX;

    $sqlInsert="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa, created_at, created_by, modified_at, modified_by) 
    VALUES ('$codComprobante', '1', '$cod_unidadX', '$codGestion', '1', '1', '$tipoComprobante', '$fechaHoraActual', '$nroCorrelativo', '$glosa', '$fechaHoraActual', '$userSolicitud', '$fechaHoraActual', '$userSolicitud')";
    //echo $sqlInsert;
    $stmtInsert = $dbh->prepare($sqlInsert);
    $flagSuccessComprobante=$stmtInsert->execute();

    $sqlUpdateSolicitud="UPDATE solicitud_recursos SET cod_comprobante=$codComprobante where codigo=$codigo";
    $stmtUpdateSolicitudRecurso = $dbh->prepare($sqlUpdateSolicitud);
    $stmtUpdateSolicitudRecurso->execute();
    
    $glosa=$nombreCliente." SR ".$numeroSol;

    //insertar en detalle comprobante
    $nuevosDetalles=obtenerDetalleSolicitudParaComprobante($codigo); 

        $sqlDelete="";
        $sqlDelete="DELETE from comprobantes_detalle where cod_comprobante='$codComprobante'";
        $stmtDel = $dbh->prepare($sqlDelete);
        $flagSuccess=$stmtDel->execute();
    $i=0;$codProveedor=0;$sumaDevengado=0;$nombresProveedor="";$nombreProveedor="";
    while ($rowNuevo = $nuevosDetalles->fetch(PDO::FETCH_ASSOC)) {
        $i++;
        
        if($codProveedor!=$rowNuevo['cod_proveedor']){
           if($codProveedor!=0){
            //proveedor devengado //para cuando cambie de proveedor (ULTIMO PROVEEEDOR)
          
            $sumaDevengado=0;
            $i++; 
            
           } 
         $codProveedor=$rowNuevo['cod_proveedor'];
        }
        
        $cuenta=$rowNuevo['cod_plancuenta'];
        $cuentaAuxiliar=0;
        $numeroCuenta=trim(obtieneNumeroCuenta($cuenta));
        $inicioNumero=$numeroCuenta[0];
        $unidadarea=obtenerUnidadAreaCentrosdeCostos($inicioNumero);               ////////////////////////unidad y area para el detalle
        if($unidadarea[0]==0){
            $unidadDetalle=$unidadSol;
            $area=$areaSol;
        }else{
            $unidadDetalle=$unidadarea[0];
            $area=$unidadarea[1];
        }
        
        $debe=$rowNuevo['monto'];
        $haber=0;
        /*if($facturaNueva==){
          $detalleFac="F/";
        }*/
        $glosaDetalle="Beneficiario: ".nameProveedor($rowNuevo['cod_proveedor'])." ".str_replace("-", "",$rowNuevo['glosa'])." F/".obtenerNumeroFacturaSolicitudRecursoDetalle($rowNuevo['codigo'])." - ".$datosServicio." ".$glosa;
        $codSolicitudDetalle=$rowNuevo['codigo'];
        if($rowNuevo['cod_confretencion']==0){
          if(verificarListaDistribucionGastoSolicitudRecurso($codigo)==0){
            //detalle comprobante SIN RETENCION ///////////////////////////////////////////////////////////////
            $sumaDevengado=$debe;
            $codComprobanteDetalle=obtenerCodigoComprobanteDetalle(); 
            $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
            VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$i')";
            $stmtDetalle = $dbh->prepare($sqlDetalle);
            $flagSuccessDetalle=$stmtDetalle->execute();  
          }else{
            //distribuir gastos
             include "distribucionComprobanteDevengado.php";
          }
        }else{
            // SI TIENE RETENCION **********************************************************************************

            $codigoRet=$rowNuevo['cod_confretencion'];
            $importeOriginal=$rowNuevo['monto'];
            $ii=$i;
          // retencion de costos
            $nom_cuenta_auxiliar="";
            $importeOriginal2=0;
            $totalRetencion=0;
            //obtener datos de retenciones
            $stmtRetenciones = $dbh->prepare("SELECT cd.*,c.porcentaje_cuentaorigen from configuracion_retenciones c join configuracion_retencionesdetalle cd on cd.cod_configuracionretenciones=c.codigo where cd.cod_configuracionretenciones=$codigoRet order by cd.codigo");
            $stmtRetenciones->execute();
            $j=0;
            while ($rowRet = $stmtRetenciones->fetch(PDO::FETCH_ASSOC)) {
            $ii++;                         
             $porcentajeX=$rowRet['porcentaje'];                         
             $glosaX=$rowRet['glosa'];
             $debehaberX=$rowRet['debe_haber'];

             $porcentajeCuentaX=$rowRet['porcentaje_cuentaorigen'];
             if($porcentajeCuentaX>100){
               $importe=($porcentajeCuentaX/100)*$importeOriginal;
             }else{
               $importeOriginal2=($porcentajeCuentaX/100)*$importeOriginal;
               $importe=$importeOriginal;
             }
             $montoRetencion=($porcentajeX/100)*$importe;

             $montoRetencion=number_format($montoRetencion, 2, '.', '');   

             if($debehaberX==1){
                $debeRet=$montoRetencion;
                $haberRet=0;    
              }else{
                $debeRet=0;
                $haberRet=$montoRetencion;
              }
             $importe=number_format($importe, 2, '.', '');
             $cuentaRetencion=$rowRet['cod_cuenta'];  
             $cuentaAuxiliar=0;
             $n_cuenta=trim(obtieneNumeroCuenta($cuentaRetencion));
             $nom_cuenta=nameCuenta($cuentaRetencion);
             $inicioNumeroRet=$n_cuenta[0];
             $unidadareaRet=obtenerUnidadAreaCentrosdeCostos($inicioNumeroRet);////////////////////////unidad y area para el detalle
             if($unidadareaRet[0]==0){
                   $unidadDetalleRet=$unidadSol;
                   $areaRet=$areaSol;
             }else{
                  $unidadDetalleRet=$unidadareaRet[0];
                   $areaRet=$unidadareaRet[1];
             }
    
             $retenciones[$j]['cuenta']=$cuentaRetencion;
             $retenciones[$j]['unidad']=$unidadDetalleRet;
             $retenciones[$j]['area']=$areaRet;
             $retenciones[$j]['debe']=$debeRet;
             $retenciones[$j]['haber']=$haberRet;
             $retenciones[$j]['glosa']=$glosaX." - ".$glosaDetalle;
             $retenciones[$j]['numero']=$ii; 
             $retenciones[$j]['debe_haber']=$debehaberX;
           $j++;
          }

         $i=$ii;     
      
            $haber=0;

            if($porcentajeCuentaX<=100){
              $debe=$importeOriginal2;
              $sumaDevengado=$importeOriginal; 
              $debe=number_format($debe, 2, '.', ''); 
            }else{
              $debe=$importe;
              $sumaDevengado=$importeOriginal; 
              $debe=number_format($debe, 2, '.', ''); 
            }
           if(verificarListaDistribucionGastoSolicitudRecurso($codigo)==0){
             //detalle comprobante CON RETENCION //////////////////////////////////////////////////////////////7
              $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
              $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
              VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$i')";
              $stmtDetalle = $dbh->prepare($sqlDetalle);
              $flagSuccessDetalle=$stmtDetalle->execute();
           }else{
              include "distribucionComprobanteDevengado.php";
           }           

           $totalRetencion=0;  
            //if($totalRetencion!=0){
              for ($j=0; $j < count($retenciones); $j++) { 
              $cuentaRetencion=$retenciones[$j]['cuenta'];
              $unidadDetalleRet=$retenciones[$j]['unidad'];
              $areaRet=$retenciones[$j]['area'];
              $debeRet=$retenciones[$j]['debe'];
              $haberRet=$retenciones[$j]['haber'];
              $glosaX=$retenciones[$j]['glosa'];
              $ii=$retenciones[$j]['numero']; 

              if($retenciones[$j]['debe_haber']==1){
                $totalRetencion+=(float)$debeRet;
              }else{
                $totalRetencion+=(float)$haberRet;
              }   
              $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
              $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
              VALUES ('$codComprobanteDetalle','$codComprobante', '$cuentaRetencion', '$cuentaAuxiliar', '$unidadDetalleRet', '$areaRet', '$debeRet', '$haberRet', '$glosaX', '$ii')";
              $stmtDetalle = $dbh->prepare($sqlDetalle);
              $flagSuccessDetalle=$stmtDetalle->execute();

              //$sumaDevengado+=$totalRetencion;   
              }

            // fin de retencion
      //}
        $totalRetencion=0;
      } //fin else *********************************** SI TIENE RETENCION ****************************************************+    
        

       //ASOCIAR PASIVO A DETALLE CUENTA
       //proveedor devengado
          $i++;
          $cuentaProv=obtenerCuentaPasivaSolicitudesRecursos($cuenta);
          $cuentaAuxiliarProv=obtenerCodigoCuentaAuxiliarProveedorCliente(1,$codProveedor);
          $numeroCuentaProv=trim(obtieneNumeroCuenta($cuentaProv));
          $inicioNumeroProv=$numeroCuentaProv[0];
          $unidadareaProv=obtenerUnidadAreaCentrosdeCostos($inicioNumeroProv);
          if($unidadareaProv[0]==0){
              $unidadDetalleProv=$unidadSol;
              $areaProv=$areaSol;
          }else{
              $unidadDetalleProv=$unidadareaProv[0];
              $areaProv=$unidadareaProv[1];
          }

            $debeProv=0;
            $haberProv=$sumaDevengado;
            $glosaDetalleProv="Beneficiario: ".nameProveedor($rowNuevo['cod_proveedor'])." ".str_replace("-", "",$rowNuevo['glosa'])." F/".obtenerNumeroFacturaSolicitudRecursoDetalle($rowNuevo['codigo'])." - ".$datosServicio." ".$glosa;
        
            $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
            $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
            VALUES ('$codComprobanteDetalle','$codComprobante', '$cuentaProv', '$cuentaAuxiliarProv', '$unidadDetalleProv', '$areaProv', '$debeProv', '$haberProv', '$glosaDetalleProv', '$i')";
            $stmtDetalle = $dbh->prepare($sqlDetalle);
            $flagSuccessDetalle=$stmtDetalle->execute();
            print_r($sqlDetalle); 
            $codProveedorEstado=$codProveedor;
              //estado de cuentas devengado
              $codEstadoCuenta=obtenerCodigoEstadosCuenta();
              $sqlDetalleEstadoCuenta="INSERT INTO estados_cuenta (codigo,cod_comprobantedetalle, cod_plancuenta, monto, cod_proveedor, fecha,cod_comprobantedetalleorigen,cod_cuentaaux) 
              VALUES ('$codEstadoCuenta','$codComprobanteDetalle', '0', '$haberProv', '$codProveedorEstado', '$fechaHoraActual','0','$cuentaAuxiliarProv')";
              $stmtDetalleEstadoCuenta = $dbh->prepare($sqlDetalleEstadoCuenta);
              $stmtDetalleEstadoCuenta->execute();             
             //echo $sqlDetalleEstadoCuenta."";
              //actualizamos con el codigo de comprobante detalle la solicitud recursos detalle 
              $sqlUpdateSolicitudRecursoDetalle="UPDATE solicitud_recursosdetalle SET cod_proveedor=$codProveedorEstado,cod_estadocuenta=$codEstadoCuenta,glosa_comprobantedetalle='$glosaDetalleProv' where codigo=$codSolicitudDetalle";
              $stmtUpdateSolicitudRecursoDetalle = $dbh->prepare($sqlUpdateSolicitudRecursoDetalle);
              $stmtUpdateSolicitudRecursoDetalle->execute();

             //echo $sqlUpdateSolicitudRecursoDetalle."";

    }  

        if($sumaDevengado!=0){
        
         }    
        
    //fin de crear comprobante