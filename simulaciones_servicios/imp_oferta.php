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
 	//tipo de oferta TCS A B C
  $codOferta=2;
 }	
}

if($_GET["cod_area"]==39){
 require 'imp_oferta_html.php';
}else{
 require 'imp_oferta_html.php';
}
$html = ob_get_clean();
//echo $html;
descargarPDFOfertaPropuesta("IBNORCA - Oferta -TCP",$html);