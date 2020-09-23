<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $datos = json_decode(file_get_contents("php://input"), true); 
    //Parametros de consulta
    $accion=NULL;
    if(isset($datos['accion'])&&isset($datos['sIdentificador'])&&isset($datos['sKey']))
        if($datos['sIdentificador']=="libBan"&&$datos['sKey']=="89i6u32v7xda12jf96jgi30lh"){
        $accion=$datos['accion']; //recibimos la accion
        $estado=0;
        $mensaje="";
        if($accion=="ObtenerLibretaBancaria"){
          $codLibreta=$datos['idLibreta'];//recibimos el codigo del proyecto

          //variables para el filtro
          $montoLibreta=null;
          if(isset($datos['monto'])){
            $montoLibreta=$datos['monto'];
          }
          $fechaLibreta=null;
          if(isset($datos['fecha'])){
            $fechaLibreta=$datos['fecha'];
          }
          $nombreLibreta=null;
          if(isset($datos['nombre'])){
            $nombreLibreta=$datos['nombre'];
          }
          $anioLibreta=0;
          if(isset($datos['anio'])){
            $anioLibreta=$datos['anio'];
          }

          $datosResp=obtenerDatosLibreta($codLibreta,$anioLibreta,$montoLibreta,$fechaLibreta,$nombreLibreta,null);
                
                if($datosResp[0]==0){
                 $estado=2;
                 $mensaje = "Libreta Inexistente";
                 $resultado=array("estado"=>$estado, 
                            "mensaje"=>$mensaje, 
                            "totalComponentes"=>0);
                }else{
                  $estado=1;
                  $libreta = $datosResp[1]; 
                  $resultado=array(
                            "estado"=>$estado,
                            "mensaje"=>"Libreta Obtenida Correctamente", 
                            "libretas"=>$libreta, 
                            "totalComponentes"=>1     
                            );
                }
            }else{
               if($accion=="ObtenerListaLibretaBancaria"){
                  $datosResp=obtenerListaLibretaBancaria();
                  $estado=1;
                  $resultado=array(
                            "estado"=>$estado,
                            "mensaje"=>"Listado Obtenido Correctamente", 
                            "lista"=>$datosResp, 
                            "totalComponentes"=>count($datosResp)    
                            );
               }else{
                    if($accion=="ObtenerLibretaBancariaPorFactura"){
                      $codFactura=0;
                      if(isset($datos['idFactura'])){
                        $codFactura=$datos['idFactura'];
                        $datosResp=obtenerDatosLibreta(0,0,null,null,null,$codFactura);
                      }else{
                        $datosResp=obtenerDatosLibreta(-100,0,null,null,null,$codFactura);
                      }
                
                      if($datosResp[0]==0){
                       $estado=2;
                       $mensaje = "Libreta Inexistente";
                       $resultado=array("estado"=>$estado, 
                                  "mensaje"=>$mensaje, 
                                  "totalComponentes"=>0);
                      }else{
                        $estado=1;
                        $libreta = $datosResp[1]; 
                        $resultado=array(
                            "estado"=>$estado,
                            "mensaje"=>"Libreta Obtenida Correctamente", 
                            "libretas"=>$libreta, 
                            "totalComponentes"=>1     
                            );
                      }
                   }else{
                
                   $resultado=array("estado"=>3, 
                            "mensaje"=>"No existe la Accion Solicitada.");        
                   }
               }
            }        
        }else{
            $resultado=array("estado"=>4, 
                            "mensaje"=>"Credenciales Incorrectas");
        }
            header('Content-type: application/json');
            echo json_encode($resultado);
}else{
    $resp=array("estado"=>5, 
                "mensaje"=>"El acceso al WS es incorrecto");
    header('Content-type: application/json');
    echo json_encode($resp);
}

function obtenerListaLibretaBancaria(){
  require_once __DIR__.'/../conexion.php';
  $dbh = new Conexion();
  $sqlX="SET NAMES 'utf8'";
  $stmtX = $dbh->prepare($sqlX);
  $stmtX->execute();
  $sql="SELECT p.nombre as banco,dc.* 
FROM libretas_bancarias dc join bancos p on dc.cod_banco=p.codigo
WHERE dc.cod_estadoreferencial=1";
  $stmtFac = $dbh->prepare($sql);
  $stmtFac->execute();
  $filaA=0;
  $datosLibretasCabecera=[];
  $ff=0;
  while ($rowLib = $stmtFac->fetch(PDO::FETCH_ASSOC)) {
     $datosLib=[];
     $codigoLib=$rowLib['codigo'];
     $datosLib['codigo']=$rowLib['codigo'];
     $datosLib['nombre']=$rowLib['nombre'];
     $datosLib['banco']=$rowLib['banco'];
     $datosLib['cod_banco']=$rowLib['cod_banco'];
     $datosLib['nro_cuenta']=$rowLib['nro_cuenta'];
     $datosLib['cod_cuenta']=$rowLib['cod_cuenta'];
     $datosLibretasCabecera[$ff]=$datosLib;
     $ff++;
  }
  return $datosLibretasCabecera;
}

function obtenerDatosLibreta($codigo,$anioLib,$montoLibreta,$fechaLibreta,$nombreLibreta,$codFactura){
  require_once __DIR__.'/../conexion.php';
  require_once __DIR__.'/../functions.php';
  $dbh = new Conexion();
  $sqlX="SET NAMES 'utf8'";
  $stmtX = $dbh->prepare($sqlX);
  $stmtX->execute();
//filtros query cabecera
  $sqlCodigo="";
if($codigo!=0){
  $sqlCodigo=" and dc.codigo=".$codigo;
}
//filtros query detalle
$sqlFiltroDetalle="";
if($anioLib!=0){
  $sqlFiltroDetalle.=" and year(ce.fecha_hora)=".$anioLib." ";
}
if($montoLibreta!=null){
  $sqlFiltroDetalle.=" and ce.monto=".$montoLibreta." ";
}
if($fechaLibreta!=null){
  $sqlFiltroDetalle.=" and date_format(date(ce.fecha_hora),'%Y-%m-%d')='".$fechaLibreta."' ";
}
if($nombreLibreta!=null){
  $sqlFiltroDetalle.=" and (ce.descripcion like '%".$nombreLibreta."%' or ce.informacion_complementaria like '%".$nombreLibreta."%')";
}

  $sql="SELECT p.nombre as banco,dc.* 
FROM libretas_bancarias dc join bancos p on dc.cod_banco=p.codigo
WHERE dc.cod_estadoreferencial=1 $sqlCodigo";
  $stmtFac = $dbh->prepare($sql);
  $stmtFac->execute();
  $filaA=0;
  $datos=null;
  $datosMega=null;

  $datosLibretasCabecera=[];
  $ff=0;
  while ($rowLib = $stmtFac->fetch(PDO::FETCH_ASSOC)) {
     $codigoLib=$rowLib['codigo'];
     $datosLibretasCabecera[$ff]['codigo']=$rowLib['codigo'];
     $datosLibretasCabecera[$ff]['nombre']=$rowLib['nombre'];
     $datosLibretasCabecera[$ff]['banco']=$rowLib['banco'];
     $datosLibretasCabecera[$ff]['cod_banco']=$rowLib['cod_banco'];
     $datosLibretasCabecera[$ff]['nro_cuenta']=$rowLib['nro_cuenta'];
     $datosLibretasCabecera[$ff]['cod_cuenta']=$rowLib['cod_cuenta'];
     $ff++;
  }
  for ($ff=0; $ff <count($datosLibretasCabecera) ; $ff++) { 
     $codigoLib=$datosLibretasCabecera[$ff]['codigo'];
     $datos['CodLibreta']=$datosLibretasCabecera[$ff]['codigo'];
     $datos['Nombre']=$datosLibretasCabecera[$ff]['nombre'];
     $datos['Banco']=$datosLibretasCabecera[$ff]['banco'];
     $datos['CodBanco']=$datosLibretasCabecera[$ff]['cod_banco'];
     $datos['NumeroCuenta']=$datosLibretasCabecera[$ff]['nro_cuenta'];
     $datos['IdCuenta']=$datosLibretasCabecera[$ff]['cod_cuenta'];
     if($codFactura!=null){
        $sqlDetalle="SELECT ce.*,(select cod_estadofactura from facturas_venta where codigo=ce.cod_factura) as estado_factura,(SELECT obtener_saldo_libreta_bancaria_detalle(ce.codigo)) as saldo_libreta_detalle
       FROM libretas_bancariasdetalle ce join libretas_bancariasdetalle_facturas ldf on ldf.cod_libretabancariadetalle=ce.codigo       
       where ce.cod_libretabancaria=$codigoLib and ldf.cod_facturaventa=$codFactura and  ce.cod_estadoreferencial=1 $sqlFiltroDetalle order by ce.fecha_hora desc";
     }else{
    $sqlDetalle="SELECT ce.*,(select cod_estadofactura from facturas_venta where codigo=ce.cod_factura) as estado_factura,(SELECT obtener_saldo_libreta_bancaria_detalle(ce.codigo)) as saldo_libreta_detalle
       FROM libretas_bancariasdetalle ce where ce.cod_libretabancaria=$codigoLib and  ce.cod_estadoreferencial=1 $sqlFiltroDetalle order by ce.fecha_hora desc";
     }
     $stmtFacDetalle = $dbh->prepare($sqlDetalle);
     $stmtFacDetalle->execute();
     $datosDetalle=[];
     $index=0;
     while ($rowLibDetalle = $stmtFacDetalle->fetch(PDO::FETCH_ASSOC)) {
          $codComprobanteDetalle=$rowLibDetalle['cod_comprobantedetalle'];
          $codComprobante=$rowLibDetalle['cod_comprobante'];
           $datosDetalle[$index]['CodLibretaDetalle']=$rowLibDetalle['codigo'];
           $datosDetalle[$index]['Descripcion']=$rowLibDetalle['descripcion'];
           $datosDetalle[$index]['InformacionComplementaria']=$rowLibDetalle['informacion_complementaria'];
           $datosDetalle[$index]['Agencia']=$rowLibDetalle['agencia'];
           $datosDetalle[$index]['NumeroCheque']=$rowLibDetalle['nro_cheque'];
           $datosDetalle[$index]['NumeroDocumento']=$rowLibDetalle['nro_documento'];
           $datosDetalle[$index]['NumeroReferencia']=$rowLibDetalle['nro_referencia'];
           $datosDetalle[$index]['Fecha']=strftime('%d/%m/%Y',strtotime($rowLibDetalle['fecha_hora']));
           $datosDetalle[$index]['Hora']=strftime('%H:%M:%S',strtotime($rowLibDetalle['fecha_hora']));
           $datosDetalle[$index]['FechaHoraCompleta']=$datosDetalle[$index]['Fecha']." ".$datosDetalle[$index]['Hora'];
           $datosDetalle[$index]['monto']=$rowLibDetalle['monto'];
           $datosDetalle[$index]['CodEstado']=$rowLibDetalle['cod_estado'];
           $datosDetalle[$index]['CodComprobante']=$codComprobante;
           $datosDetalle[$index]['CodComprobanteDetalle']=$codComprobanteDetalle;
           /*$datosDetalle[$index]['FechaFactura']=null;
           $datosDetalle[$index]['NumeroFactura']=null;
           $datosDetalle[$index]['NitFactura']=null;
           $datosDetalle[$index]['RSFactura']=null;
           $datosDetalle[$index]['DetalleFactura']=null;
           $datosDetalle[$index]['MontoFactura']=null;*/
           
           //$saldoFactura=0;
           //if($rowLibDetalle['cod_factura']!=""){
           //$sqlFacturaLibreta="SELECT * FROM facturas_venta where cod_libretabancariadetalle=".$rowLibDetalle['codigo'];
           $sqlFacturaLibreta="select f.fecha_factura,f.nro_factura,f.nit,f.razon_social,f.observaciones,f.importe from facturas_venta f join 
libretas_bancariasdetalle_facturas lf on lf.cod_facturaventa=f.codigo 
where lf.cod_libretabancariadetalle=".$rowLibDetalle['codigo']." and f.cod_estadofactura<>2";
           $stmtFacLibreta = $dbh->prepare($sqlFacturaLibreta);
           $stmtFacLibreta->execute();
           $sumaImporte=0;
           $datosDetalleFac=[];
           $indexAux=0;
           $existeFactura=0;
           while ($rowFacLib = $stmtFacLibreta->fetch(PDO::FETCH_ASSOC)) {
               $datosDetalleFac[$indexAux]['FechaFactura']=strftime('%d/%m/%Y',strtotime($rowFacLib['fecha_factura']));
               $datosDetalleFac[$indexAux]['NumeroFactura']=$rowFacLib['nro_factura'];
               $datosDetalleFac[$indexAux]['NitFactura']=$rowFacLib['nit'];
               $datosDetalleFac[$indexAux]['RSFactura']=$rowFacLib['razon_social'];
               $datosDetalleFac[$indexAux]['DetalleFactura']=$rowFacLib['observaciones'];
               $datosDetalleFac[$indexAux]['MontoFactura']=number_format($rowFacLib['importe'],2,".","");
               $existeFactura++;           
               $sumaImporte+=$rowFacLib['importe'];
               $indexAux++; 
            }
            $saldoFactura=$rowLibDetalle['monto'];
            if($existeFactura>0){
              //calcular Saldo
              $saldoFactura=number_format($rowLibDetalle['saldo_libreta_detalle'],2,".","");
             }else{
               if(!($codComprobante==""||$codComprobante==0)){
                  $datosDetalleCompro=obtenerDatosComprobanteDetalle($codComprobanteDetalle);
                  $datosDetalleFac[$indexAux]['FechaFactura']=strftime('%d/%m/%Y',strtotime(obtenerFechaComprobante($codComprobante)));
                  $datosDetalleFac[$indexAux]['NumeroFactura']=nombreComprobante($codComprobante);
                  $datosDetalleFac[$indexAux]['NitFactura']="-";
                  $datosDetalleFac[$indexAux]['DetalleFactura']=$datosDetalleCompro[0];
                  $datosDetalleFac[$indexAux]['RSFactura']=$datosDetalleCompro[2]." [".$datosDetalleCompro[3]."] - ".$datosDetalleCompro[4];
                  $datosDetalleFac[$indexAux]['MontoFactura']=$datosDetalleCompro[1];
                }
             }
            
            $datosDetalle[$index]['Saldo']=$saldoFactura; 
            $datosDetalle[$index]['DetalleFacturas']=$datosDetalleFac;  
           /*}else{
            $datosDetalle[$index]['Saldo']=$saldoFactura; 
            $datosDetalle[$index]['DetalleFacturas']=null;
           }*/  
           $index++;    
     }


    $datos['detalle']=$datosDetalle;
    $datos['totalDetalle']=$index;
    //$datosMega[$filaA]=$datos; 
    if($saldoFactura!=0){
     if($codFactura!=null){
       if($index>0){
        $datosMega[$filaA]=$datos; 
        $filaA++; 
       }  
     }else{
        $datosMega[$filaA]=$datos; 
        $filaA++; 
     }
    }
    
 }

 return array($filaA,$datosMega);
}
