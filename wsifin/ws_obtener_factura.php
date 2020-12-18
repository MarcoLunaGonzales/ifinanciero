<?php

error_reporting(0);
require '../functions.php';
require '../simulaciones_servicios/htmlFacturaCliente.php';



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $datos = json_decode(file_get_contents("php://input"), true); 
    //Parametros de consulta
    $accion=NULL;
    if(isset($datos['accion'])&&isset($datos['sIdentificador'])&&isset($datos['sKey']))
        if($datos['sIdentificador']=="facifin"&&$datos['sKey']=="rrf656nb2396k6g6x44434h56jzx5g6"){
        $accion=$datos['accion']; //recibimos la accion
        $codFactura=$datos['idFactura'];//recibimos el codigo del proyecto
        $estado=0;
        $mensaje="";
        if($accion=="ObtenerFacturaPDF"){
            try{
                $html_x=generarHTMLFacCliente($codFactura,3,1);
                $array_html=explode('@@@@@@', $html_x);
                $html=$array_html[0];
                if($html=="ERROR"){
                    $estado=2;
                    $mensaje = "Factura Inexistente";
                    $resultado=array("estado"=>$estado, 
                    "mensaje"=>$mensaje, 
                    "factura64"=>array(),
                    "totalComponentes"=>0);
                }else{
                    $estado=1;
                    $factura = datosPDFFacturasVenta($html,$codFactura); 
                    $resultado=array(
                            "estado"=>$estado,
                            "mensaje"=>"Factura Obtenida Correctamente", 
                            "factura64"=>$factura['base64'], 
                            "totalComponentes"=>1     
                            );            
                }
            }catch(Exception $e){
                $estado=2;
                $mensaje = "Factura Inexistente";
                $resultado=array("estado"=>$estado, 
                            "mensaje"=>$mensaje, 
                            "factura64"=>array(),
                            "totalComponentes"=>0);
            }
            
         }else{
            if($accion=="ObtenerFacturaArray"){
                $datos=obtenerDatosFactura($codFactura);
                if($datos[0]==0){
                 $estado=2;
                 $mensaje = "Factura Inexistente";
                 $resultado=array("estado"=>$estado, 
                            "mensaje"=>$mensaje, 
                            "factura64"=>array(),
                            "totalComponentes"=>0);
                }else{
                  $estado=1;
                  $factura = $datos[1]; 
                  $resultado=array(
                            "estado"=>$estado,
                            "mensaje"=>"Factura Obtenida Correctamente", 
                            "datos"=>$factura, 
                            "totalComponentes"=>1     
                            );
                }
            }else{
                if($accion=="ObtenerFechaActual"){
                $fechaActual=obtenerFechaActualBaseDatos();
                if($fechaActual==""){
                 $estado=5;
                 $mensaje = "Ocurrio un error al generar la fecha";
                 $resultado=array("estado"=>$estado, 
                            "mensaje"=>$mensaje, 
                            "fecha"=>$fechaActual,
                            "totalComponentes"=>0);
                }else{
                  $estado=6;
                  $resultado=array(
                            "estado"=>$estado,
                            "mensaje"=>"Fecha Obtenida Correctamente", 
                            "datos"=>$fechaActual, 
                            "totalComponentes"=>1     
                            );
                }
              }else{
                if($accion=="VerificacionPagosCurso"){
                    if(isset($datos['IdCurso'])&&isset($datos['Ci'])){
                      $facturasEncontradas=obtenerValidacionFacturaCurso($datos['IdCurso'],$datos['Ci']);
                      if(count($facturasEncontradas)==0){
                           $estado=7;
                           $mensaje = "No existen registros";
                           $resultado=array("estado"=>$estado, 
                            "mensaje"=>$mensaje, 
                            "Pagos"=>$facturasEncontradas,
                            "totalComponentes"=>0);
                        }else{
                          $estado=8;
                          $resultado=array(
                            "estado"=>$estado,
                            "mensaje"=>"Se encontraron los registros", 
                            "pagos"=>$facturasEncontradas, 
                            "totalComponentes"=>count($facturasEncontradas)    
                            );
                        }
                    }else{
                       $resultado=array("estado"=>9, 
                            "mensaje"=>"Los parametros no son correctos"); 
                    }
                }else{
                  $resultado=array("estado"=>3, 
                            "mensaje"=>"No existe la Accion Solicitada.");
                } 
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
function obtenerValidacionFacturaCurso($curso,$ci){
  $dbh = new Conexion();
  $sqlX="SELECT sfd.codigo,sfd.cod_solicitudfacturacion,f.codigo as codFactura,f.nro_factura,cod_claservicio,
CASE
    WHEN sf.tipo_solicitud =1 THEN (SELECT IdServicio from simulaciones_servicios where codigo=sf.cod_simulacion_servicio)
    WHEN sf.tipo_solicitud = 7  THEN sfd.cod_curso    
    ELSE sf.cod_simulacion_servicio
END as cod_padre,
sf.tipo_solicitud
from solicitudes_facturaciondetalle sfd
join solicitudes_facturacion sf on sf.codigo=sfd.cod_solicitudfacturacion
join facturas_venta f on f.cod_solicitudfacturacion=sf.codigo
WHERE (sf.ci_estudiante='$ci' or sfd.ci_estudiante='$ci') and sf.tipo_solicitud in (2,6,7) 
and f.cod_estadofactura<>2
HAVING cod_padre=$curso;";
  $stmtX = $dbh->prepare($sqlX);
  $stmtX->execute();
  $arrayFacturas=[];
  $index=0;
  while ($row = $stmtX->fetch(PDO::FETCH_ASSOC)) {
    $arrayFacturas[$index]=array('Modulo' => $row['cod_claservicio'],'IdFactura' => $row['codFactura'],'CI' => $ci);
    $index++;
  }
  return $arrayFacturas;
}
function obtenerFechaActualBaseDatos(){
  $dbh = new Conexion();
  $sqlX="SELECT NOW() as fecha";
  $stmtX = $dbh->prepare($sqlX);
  $stmtX->execute();
  $fechaActual="";
  while ($row = $stmtX->fetch(PDO::FETCH_ASSOC)) {
    $fechaActual=$row['fecha'];
  }
  return $fechaActual;
}
function obtenerDatosFactura($codigo){
  require_once __DIR__.'/../conexion.php';
  $dbh = new Conexion();
  $sqlX="SET NAMES 'utf8'";
  $stmtX = $dbh->prepare($sqlX);
  $stmtX->execute();

  $sql="select codigo,nit,codigo_control,nro_factura,fecha_factura,razon_social,importe,nro_autorizacion,observaciones from facturas_venta where codigo=$codigo";
  $stmtFac = $dbh->prepare($sql);
  $stmtFac->execute();
  $filaA=0;
  $datos=null;
  while ($rowFac = $stmtFac->fetch(PDO::FETCH_ASSOC)) {
     $filaA++;
     $codigoFac=$rowFac['codigo'];
     $datos['numero']=$rowFac['nro_factura'];
     $datos['nit']=$rowFac['nit'];
     $datos['control']=$rowFac['codigo_control'];
     $datos['fecha']=$rowFac['fecha_factura'];
     $datos['razon_social']=$rowFac['razon_social'];
     $datos['importe']=$rowFac['importe'];
     $datos['autorizacion']=$rowFac['nro_autorizacion'];
     $datos['observaciones']=$rowFac['observaciones'];

     $sqlDetalle="SELECT sf.codigo, sf.cantidad, sf.precio, sf.descuento_bob, sf.descripcion_alterna from facturas_ventadetalle sf where sf.cod_facturaventa=$codigoFac";
     $stmtFacDetalle = $dbh->prepare($sqlDetalle);
     $stmtFacDetalle->execute();
     $datosDetalle=[];
     $index=0;
     while ($rowFacDetalle = $stmtFacDetalle->fetch(PDO::FETCH_ASSOC)) {
       $datosDetalle[$index]['cantidad']=$rowFacDetalle['cantidad'];
       $datosDetalle[$index]['precio']=$rowFacDetalle['precio'];
       $datosDetalle[$index]['descuento']=$rowFacDetalle['descuento_bob'];
       $datosDetalle[$index]['descripcion']=$rowFacDetalle['descripcion_alterna'];
       $index++;
     }
    $datos['detalle']=$datosDetalle;      
 }
 return array($filaA,$datos);
}

?>