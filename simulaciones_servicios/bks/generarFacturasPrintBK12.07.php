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

/*  ATENCION ESTA PARTE SE MODIFICO PARA TENER LA IMPRESION CON SOLAMENTE 4 FORMATOS  
$admin=2  original
$admin=3  copia cliente
$admin=7 original especial
$admin=8 copia cliente especial
*/

$htmlImprimir="";

//EL AUXILIAR 2 SE LLAMA DESDE SOLICITUDES
if($auxiliar==2){
  $codigo_factura=verificamosFacturaGenerada($codigo);
}else{
  $codigo_factura=$codigo;
}

/*CON EL CODIGO DE FACTURA VERIFICAMOS SI ES UNA FACTURA CON GLOSA ESPECIAL O NO*/
$sqlVerificaGlosaEsp="SELECT f.glosa_factura3 from facturas_venta f where f.codigo=".$codigo_factura;
$stmtVerificaGlosaEsp = $dbh->prepare($sqlVerificaGlosaEsp);
$stmtVerificaGlosaEsp->execute();
$glosaEspecial="";
while ($rowVerificaGlosaEsp = $stmtVerificaGlosaEsp->fetch(PDO::FETCH_ASSOC)) {                
    $glosaEspecial=$rowVerificaGlosaEsp['glosa_factura3'];        
}    

if($glosaEspecial!=""){
  $admin=7;
}
/*FIN DE REVISION*/

try{
  //descarga la facturaa y lo almacena en una carpeta
  //EN TODOS LOS CASOS ENVIAMOS EL CODIGO DE FACTURA YA NO ES NECESARIO ENVIAR EL AUXILIAR
  $html_cliente1=generarHTMLFacCliente($codigo,$auxiliar,1);
  $array_html=explode('@@@@@@', $html_cliente1);
  $html1=$array_html[0];
  
  $htmlImprimir=$html1;
  
  if($html1!='ERROR'){
    $cod_factura=$array_html[1];
    $nro_factura=$array_html[2];
  }else{
    echo "hubo un error al generar la factura";
  }

  if($admin==1 || $admin==0){//original cliente y conta 
    /*$htmlConta1=generarHTMLFacCliente($codigo,$auxiliar,2);  
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

      $htmlImprimir=$html2;
      
      descargarPDFFacturas("IBNORCA-C".$cod_factura."-F".$nro_factura,$html2,$codigo_factura);
    }else{
      echo "hubo un error al generar la factura";
    }*/
  }elseif($admin==2){ // COPIA ORIGINAL
    $htmlConta1=generarHTMLFacCliente($codigo,$auxiliar,4);  
    $array_html2=explode('@@@@@@', $htmlConta1);
    $html2=$array_html2[0];
  
    $htmlImprimir=$html2;

    descargarPDFFacturas("IBNORCA-C".$cod_factura."-F".$nro_factura,$html2,$codigo_factura);
    if($html2!='ERROR'){
      $cod_factura=$array_html2[1];
      $nro_factura=$array_html2[2];    
    }else{
      echo "hubo un error al generar la factura";
    }
  }elseif($admin==3){ // COPIA CONTABILIDAD 
    $htmlConta1=generarHTMLFacCliente($codigo,$auxiliar,5);
    $array_html2=explode('@@@@@@', $htmlConta1);
    $html2=$array_html2[0];
  
    $htmlImprimir=$html2;

    descargarPDFFacturas("IBNORCA-C".$cod_factura."-F".$nro_factura,$html2,$codigo_factura);
    if($html2!='ERROR'){
      $cod_factura=$array_html2[1];
      $nro_factura=$array_html2[2];    
    }else{
      echo "hubo un error al generar la factura";
    }
  }if($admin==4){//desde aquí para el segundo formato. original y copia de conta ESTE YA NO LO UTILIZAMOS
    /*$htmlConta1=generarHTMLFacCliente2($codigo,$auxiliar,6);  
    $array_html2=explode('@@@@@@', $htmlConta1);
    $htmlImprimir=$html2;
    $html2=$array_html2[0];
    if($html2!='ERROR'){
      $cod_factura=$array_html2[1];
      $nro_factura=$array_html2[2];    
    }else{
      echo "hubo un error al generar la factura";
    }
      $htmlConta2=generarHTMLFacCliente2($codigo,$auxiliar,7);  
      $array_html3=explode('@@@@@@', $htmlConta2);
      // var_dump($array_html3);
      $html2.=$array_html3[0];
      $htmlImprimir=$html2;
    if($html2!='ERROR'){
      $cod_factura=$array_html3[1];
      $nro_factura=$array_html3[2];
      descargarPDFFacturas("IBNORCA-C".$cod_factura."-F".$nro_factura,$html2,$codigo_factura);
    }else{
      echo "hubo un error al generar la factura";
    }*/
  }elseif($admin==5){//original cliente
    /*$htmlConta1=generarHTMLFacCliente2($codigo,$auxiliar,8);
    $array_html2=explode('@@@@@@', $htmlConta1);
    $html2=$array_html2[0];
    $htmlImprimir=$html2;
    descargarPDFFacturas("IBNORCA-C".$cod_factura."-F".$nro_factura,$html2,$codigo_factura);
    if($html2!='ERROR'){
      $cod_factura=$array_html2[1];
      $nro_factura=$array_html2[2];    
    }else{
      echo "hubo un error al generar la factura";
    }*/
  }elseif($admin==6){//copia contabilidad
    /*$htmlConta1=generarHTMLFacCliente2($codigo,$auxiliar,9);
    $array_html2=explode('@@@@@@', $htmlConta1);
    $html2=$array_html2[0];

    $htmlImprimir=$html2;

    descargarPDFFacturas("IBNORCA-C".$cod_factura."-F".$nro_factura,$html2,$codigo_factura);
    if($html2!='ERROR'){
      $cod_factura=$array_html2[1];
      $nro_factura=$array_html2[2];    
    }else{
      echo "hubo un error al generar la factura";
    }*/
  }elseif($admin==7){//original cliente, formato 3
    $htmlConta1=generarHTMLFacCliente2($codigo,$auxiliar,10);
    $array_html2=explode('@@@@@@', $htmlConta1);
    $html2=$array_html2[0];

    $htmlImprimir=$html2;

    descargarPDFFacturas("IBNORCA-C".$cod_factura."-F".$nro_factura,$html2,$codigo_factura);
    if($html2!='ERROR'){
      $cod_factura=$array_html2[1];
      $nro_factura=$array_html2[2];    
    }else{
      echo "hubo un error al generar la factura";
    }
  }

  //AQUI SE GENERA EL ARCHIVO PARA EL CREDITO
  descargarPDFFacturasCopiaCliente("IBNORCA-C".$cod_factura."-F".$nro_factura,$htmlImprimir,$codigo_factura);  

  // //EL PDF DE FACTURA PARA MOSTRAR EN PANTALLA
  // $htmlConta1=generarHTMLFacCliente($codigo,$auxiliar,2);
  // $htmlConta1.=generarHTMLFacCliente($codigo,$auxiliar,3);
?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
