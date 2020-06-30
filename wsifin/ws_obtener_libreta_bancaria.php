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
                $datos=obtenerDatosLibreta($codLibreta);
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

function obtenerDatosLibreta($codigo){
  require_once __DIR__.'/../conexion.php';
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

     $sqlDetalle="SELECT ce.*,(select cod_estadofactura from facturas_venta where codigo=ce.cod_factura) as estado_factura
FROM libretas_bancariasdetalle ce where ce.cod_libretabancaria=$codigoLib and  ce.cod_estadoreferencial=1 order by ce.codigo";
     $stmtFacDetalle = $dbh->prepare($sqlDetalle);
     $stmtFacDetalle->execute();
     $datosDetalle=[];
     $index=0;
     while ($rowLibDetalle = $stmtFacDetalle->fetch(PDO::FETCH_ASSOC)) {
        $validacion=1;
        
       if($validacion==1){
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
           
           /*$datosDetalle[$index]['FechaFactura']=null;
           $datosDetalle[$index]['NumeroFactura']=null;
           $datosDetalle[$index]['NitFactura']=null;
           $datosDetalle[$index]['RSFactura']=null;
           $datosDetalle[$index]['DetalleFactura']=null;
           $datosDetalle[$index]['MontoFactura']=null;*/
           $saldoFactura=$rowLibDetalle['monto'];
           if($rowLibDetalle['cod_factura']!=""){
           $sqlFacturaLibreta="SELECT codigo,cod_estadofactura FROM facturas_venta where cod_libretabancariadetalle=".$rowLibDetalle['codigo'];
           $stmtFacLibreta = $dbh->prepare($sqlFacturaLibreta);
           $stmtFacLibreta->execute();
           $sumaImporte=0;
           $datosDetalleFac='';
           while ($rowFacLib = $stmtFacLibreta->fetch(PDO::FETCH_ASSOC)) {
              if($rowFacLib['cod_estadofactura']==1){
               $datosFacturas=obtenerDatosFacturaVenta($rowFacLib['codigo']);
               $datosDetalleFac[$index]['FechaFactura']=strftime('%d/%m/%Y',strtotime($datosFacturas[0]));
               $datosDetalleFac[$index]['NumeroFactura']=$datosFacturas[1];
               $datosDetalleFac[$index]['NitFactura']=$datosFacturas[2];
               $datosDetalleFac[$index]['RSFactura']=$datosFacturas[3];
               $datosDetalleFac[$index]['DetalleFactura']=$datosFacturas[4];
               $datosDetalleFac[$index]['MontoFactura']=number_format($datosFacturas[5],2,".","");
               $sumaImporte+=$datosFacturas[5];
              } 
            }
            $saldoFactura=$rowLibDetalle['monto']-$sumaImporte;
            $datosDetalle[$index]['Saldo']=$saldoFactura; 
            $datosDetalle[$index]['DetalleFacturas']=$datosDetalleFac;  
           }else{
            $datosDetalle[$index]['Saldo']=$saldoFactura; 
            $datosDetalle[$index]['DetalleFacturas']=null;
           }  
           $index++;    
       }
     }
    $datos['detalle']=$datosDetalle; 
    $datosMega[$filaA]=$datos;
    $filaA++;     
 }
 return array($filaA,$datosMega);
}

function obtenerDatosFacturaVenta($codigo){
  $dbh = new Conexion();
  $stmtVerif = $dbh->prepare("SELECT * FROM facturas_venta where codigo=$codigo");
  $stmtVerif->execute();
  $resultVerif = $stmtVerif->fetch();    
  $fecha = $resultVerif['fecha_factura'];
  $numero = $resultVerif['nro_factura'];
  $nit = $resultVerif['nit'];
  $razon_social = $resultVerif['razon_social'];
  $detalle = $resultVerif['observaciones'];
  $monto = $resultVerif['importe'];
  return array($fecha,$numero,$nit,$razon_social,$detalle,$monto);
  }