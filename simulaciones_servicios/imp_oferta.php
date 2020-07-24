<?php
ob_start();
if($_GET["cod_area"]==38){
 require 'imp_oferta_html.php';
}else{
 require 'imp_oferta_html.php';
}
$html = ob_get_clean();

descargarPDFOfertaPropuesta("IBNORCA - Oferta -TCP",$html);