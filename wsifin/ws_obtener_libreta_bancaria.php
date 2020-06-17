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
                            "datos"=>$libreta, 
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

  $sql="SELECT p.nombre as banco,dc.* 
FROM libretas_bancarias dc join bancos p on dc.cod_banco=p.codigo
WHERE dc.cod_estadoreferencial=1 and dc.codigo=$codigo";
  $stmtFac = $dbh->prepare($sql);
  $stmtFac->execute();
  $filaA=0;
  $datos=null;
  while ($rowLib = $stmtFac->fetch(PDO::FETCH_ASSOC)) {
     $filaA++;

     $codigoLib=$rowLib['codigo'];
     $datos['Nombre']=$rowLib['nombre'];
     $datos['Banco']=$rowLib['banco'];
     $datos['NumeroCuenta']=$rowLib['nro_cuenta'];
     $datos['IdCuenta']=$rowLib['cod_cuenta'];

     $sqlDetalle="SELECT ce.*,(select cod_estadofactura from facturas_venta where codigo=ce.cod_factura) as estado_factura
FROM libretas_bancariasdetalle ce where ce.cod_libretabancaria=$codigoLib and  ce.cod_estadoreferencial=1 order by ce.codigo";
     $stmtFacDetalle = $dbh->prepare($sqlDetalle);
     $stmtFacDetalle->execute();
     $datosDetalle=[];
     $index=0;
     while ($rowLibDetalle = $stmtFacDetalle->fetch(PDO::FETCH_ASSOC)) {
        $validacion=0;
        if($rowLibDetalle['cod_factura']!=""){
           if($rowLibDetalle['estado_factura']==2){
             $validacion=1;
           }
        }else{
            $validacion=1;
        }
       if($validacion==1){
           $datosDetalle[$index]['CodLibretaDetalle']=$rowLibDetalle['codigo'];
           $datosDetalle[$index]['Descripcion']=$rowLibDetalle['descripcion'];
           $datosDetalle[$index]['InformacionComplementaria']=$rowLibDetalle['informacion_complementaria'];
           $datosDetalle[$index]['Agencia']=$rowLibDetalle['agencia'];
           $datosDetalle[$index]['NumeroCheque']=$rowLibDetalle['nro_cheque'];
           $datosDetalle[$index]['NumeroDocumento']=$rowLibDetalle['nro_documento'];
           $datosDetalle[$index]['Fecha']=strftime('%d/%m/%Y',strtotime($rowLibDetalle['fecha_hora']));
           $datosDetalle[$index]['Hora']=strftime('%H:%M:%S',strtotime($rowLibDetalle['fecha_hora']));
           $datosDetalle[$index]['FechaHoraCompleta']=$datosDetalle[$index]['Fecha']." ".$datosDetalle[$index]['Hora'];
           $datosDetalle[$index]['monto']=$rowLibDetalle['monto'];
           $index++;    
       }
     }
    $datos['detalle']=$datosDetalle;      
 }
 return array($filaA,$datos);
}
