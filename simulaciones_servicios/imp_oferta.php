<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';

ob_start();
$codOferta=obtenerOfertaActiva($_GET["cod"]);//$_GET["cod_oferta"];
$default=0;
if($codOferta==0){
 $default=1;
 if($_GET["cod_area"]==39){
  $codOferta=1;
 }else{
 	$codOferta=2;	
 }	
}

  if(isset($_GET["of"])){
      switch ($_GET["of"]) {
        case 'a':
          $pdf_tipo="../assets/libraries/img/logos_oferta/cert2.png";
          break;
        case 'b':
          $pdf_tipo="../assets/libraries/img/logos_oferta/cert2b.png";
          break;
        case 'c':
          $pdf_tipo="../assets/libraries/img/logos_oferta/cert2c.png";
          break;  
        default:
          $pdf_tipo="../assets/libraries/img/logos_oferta/cert2.png";
          break;
      }
   }else{
     $pdf_tipo="../assets/libraries/img/logos_oferta/cert2.png";
   }


if($_GET["cod_area"]==39){
 require 'imp_oferta_html.php';
}else{
 require 'imp_oferta_html_a.php';
}
$html = ob_get_clean();
//echo $html;
descargarPDFOfertaPropuesta("IBNORCA - Oferta -TCP",$html);