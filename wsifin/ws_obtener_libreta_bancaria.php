<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $datos = json_decode(file_get_contents("php://input"), true); 
    //Parametros de consulta
    $accion=NULL;
    if(isset($datos['accion'])&&isset($datos['sIdentificador'])&&isset($datos['sKey']))
        if($datos['sIdentificador']=="libBan"&&$datos['sKey']=="89i6u32v7xda12jf96jgi30lh"){
        $accion=$datos['accion']; //recibimos la accion
        $codLibreta=$datos['idLibreta'];//recibimos el codigo del proyecto
        $estado=0;
        $mensaje="";
        if($accion=="ObtenerLibretaBancaria"){
           if(isset($datos['anio'])){
             $datos=obtenerDatosLibreta($codLibreta,$datos['anio']);
           }else{
             $datos=obtenerDatosLibreta($codLibreta,0);
           }
                if($datos[0]==0){
                 $estado=2;
                 $mensaje = "Libreta Inexistente";
                 $resultado=array("estado"=>$estado, 
                            "mensaje"=>$mensaje, 
                            "totalComponentes"=>0);
                }else{
                  $estado=1;
                  $libreta = $datos[1]; 
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

function obtenerDatosLibreta($codigo,$anioLib){
  require_once __DIR__.'/../conexion.php';
  require_once __DIR__.'/../functions.php';
  $dbh = new Conexion();
  $sqlX="SET NAMES 'utf8'";
  $stmtX = $dbh->prepare($sqlX);
  $stmtX->execute();
  $sqlCodigo="";
if($codigo!=0){
  $sqlCodigo=" and dc.codigo=".$codigo;
}
  $sql="SELECT p.nombre as banco,dc.* 
FROM libretas_bancarias dc join bancos p on dc.cod_banco=p.codigo
WHERE dc.cod_estadoreferencial=1 $sqlCodigo";
  $stmtFac = $dbh->prepare($sql);
  $stmtFac->execute();
  $filaA=0;
  $datos=null;
  $datosMega=null;
  while ($rowLib = $stmtFac->fetch(PDO::FETCH_ASSOC)) {

    
     $codigoLib=$rowLib['codigo'];
     $datos['CodLibreta']=$rowLib['codigo'];
     $datos['Nombre']=$rowLib['nombre'];
     $datos['Banco']=$rowLib['banco'];
     $datos['CodBanco']=$rowLib['cod_banco'];
     $datos['NumeroCuenta']=$rowLib['nro_cuenta'];
     $datos['IdCuenta']=$rowLib['cod_cuenta'];
    if($anioLib==0){
       $sqlDetalle="SELECT ce.*,(select cod_estadofactura from facturas_venta where codigo=ce.cod_factura) as estado_factura
       FROM libretas_bancariasdetalle ce where ce.cod_libretabancaria=$codigoLib and  ce.cod_estadoreferencial=1 order by ce.codigo";
    }else{
      $sqlDetalle="SELECT ce.*,(select cod_estadofactura from facturas_venta where codigo=ce.cod_factura) as estado_factura
       FROM libretas_bancariasdetalle ce where ce.cod_libretabancaria=$codigoLib and  ce.cod_estadoreferencial=1 and year(ce.fecha_hora)=$anioLib order by ce.codigo";
    }
     $stmtFacDetalle = $dbh->prepare($sqlDetalle);
     $stmtFacDetalle->execute();
     $datosDetalle=[];
     $index=0;
     while ($rowLibDetalle = $stmtFacDetalle->fetch(PDO::FETCH_ASSOC)) {
        $validacion=1;
        
       if($validacion==1){
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
           $sqlFacturaLibreta="SELECT * from facturas_venta where codigo in (select cod_facturaventa from libretas_bancariasdetalle_facturas where cod_libretabancariadetalle=".$rowLibDetalle['codigo'].")";
           $stmtFacLibreta = $dbh->prepare($sqlFacturaLibreta);
           $stmtFacLibreta->execute();
           $sumaImporte=0;
           $datosDetalleFac=[];
           $indexAux=0;
           $existeFactura=0;
           while ($rowFacLib = $stmtFacLibreta->fetch(PDO::FETCH_ASSOC)) {
              if($rowFacLib['cod_estadofactura']!=2){
               $datosFacturas=obtenerDatosFacturaVenta($rowFacLib['codigo']);
               $datosDetalleFac[$indexAux]['FechaFactura']=strftime('%d/%m/%Y',strtotime($datosFacturas[0]));
               $datosDetalleFac[$indexAux]['NumeroFactura']=$datosFacturas[1];
               $datosDetalleFac[$indexAux]['NitFactura']=$datosFacturas[2];
               $datosDetalleFac[$indexAux]['RSFactura']=$datosFacturas[3];
               $datosDetalleFac[$indexAux]['DetalleFactura']=$datosFacturas[4];
               $datosDetalleFac[$indexAux]['MontoFactura']=number_format($datosFacturas[5],2,".","");
               $existeFactura++;           
               $sumaImporte+=$datosFacturas[5];
               $indexAux++;
              } 
            }
            $saldoFactura=$rowLibDetalle['monto'];
            if($existeFactura>0){
              //calcular Saldo
              $saldoFactura=obtenerSaldoLibretaBancariaDetalle($rowLibDetalle['codigo']);      
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
     }
    $datos['detalle']=$datosDetalle; 
    $datosMega[$filaA]=$datos;
    $filaA++;     
 }
 return array($filaA,$datosMega);
}
