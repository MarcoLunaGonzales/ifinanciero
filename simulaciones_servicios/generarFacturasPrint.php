<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once 'htmlFacturaCliente.php';
require_once 'htmlFacturaCliente2.php';

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
set_time_limit(300);
//RECIBIMOS LAS VARIABLES
$codigo = $_GET["codigo"];
$auxiliar = $_GET["tipo"];//de donde viene la solicitud para impresión 1=lista facturas (cod_factura) / 2=lista solicitudes (cod_sol_Fact)
if(isset($_GET["admin"])){//formato de factura, 1 original y copia, 2 original, 3 copia
  $admin=$_GET["admin"];
}else{
  $admin=2;//
}

if($auxiliar==2){
  $codigo_factura=verificamosFacturaGenerada($codigo);
}else{
  $codigo_factura=$codigo;
}
try{
  //descarga la facturaa y lo almacena en una carpeta
  $html_cliente1=generarHTMLFacCliente($codigo,$auxiliar,1);
  $array_html=explode('@@@@@@', $html_cliente1);
  $html1=$array_html[0];
  if($html1!='ERROR'){
    $cod_factura=$array_html[1];
    $nro_factura=$array_html[2];
    descargarPDFFacturasCopiaCliente("IBNORCA-C".$cod_factura."-F".$nro_factura,$html1,$codigo_factura);  
  }else{
    echo "hubo un error al generar la factura";
  }

  if($admin==1 || $admin==0){
    $htmlConta1=generarHTMLFacCliente($codigo,$auxiliar,2);  
    $array_html2=explode('@@@@@@', $htmlConta1);
    $html2=$array_html2[0];
    if($html2!='ERROR'){
      $cod_factura=$array_html2[1];
      $nro_factura=$array_html2[2];    
    }else{
      echo "hubo un error al generar la factura";
    }
      $htmlConta2=generarHTMLFacCliente($codigo,$auxiliar,3);  
      $array_html3=explode('@@@@@@', $htmlConta2);
      // var_dump($array_html3);
      $html2.=$array_html3[0];
    if($html2!='ERROR'){
      $cod_factura=$array_html3[1];
      $nro_factura=$array_html3[2];    
      descargarPDFFacturas("IBNORCA-C".$cod_factura."-F".$nro_factura,$html2,$codigo_factura);
    }else{
      echo "hubo un error al generar la factura";
    }
  }elseif($admin==2){
    $htmlConta1=generarHTMLFacCliente($codigo,$auxiliar,4);  
    $array_html2=explode('@@@@@@', $htmlConta1);
    $html2=$array_html2[0];
    descargarPDFFacturas("IBNORCA-C".$cod_factura."-F".$nro_factura,$html2,$codigo_factura);
    if($html2!='ERROR'){
      $cod_factura=$array_html2[1];
      $nro_factura=$array_html2[2];    
    }else{
      echo "hubo un error al generar la factura";
    }
  }elseif($admin==3){
    $htmlConta1=generarHTMLFacCliente($codigo,$auxiliar,5);
    $array_html2=explode('@@@@@@', $htmlConta1);
    $html2=$array_html2[0];
    descargarPDFFacturas("IBNORCA-C".$cod_factura."-F".$nro_factura,$html2,$codigo_factura);
    if($html2!='ERROR'){
      $cod_factura=$array_html2[1];
      $nro_factura=$array_html2[2];    
    }else{
      echo "hubo un error al generar la factura";
    }

  }elseif($admin==4){
    $htmlConta1=generarHTMLFacCliente2($codigo,$auxiliar,6);
    $array_html2=explode('@@@@@@', $htmlConta1);
    $html2=$array_html2[0];
    descargarPDFFacturas("IBNORCA-C".$cod_factura."-F".$nro_factura,$html2,$codigo_factura);
    if($html2!='ERROR'){
      $cod_factura=$array_html2[1];
      $nro_factura=$array_html2[2];    
    }else{
      echo "hubo un error al generar la factura";
    }

  }
  // //EL PDF DE FACTURA PARA MOSTRAR EN PANTALLA
  // $htmlConta1=generarHTMLFacCliente($codigo,$auxiliar,2);
  // $htmlConta1.=generarHTMLFacCliente($codigo,$auxiliar,3);
?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
