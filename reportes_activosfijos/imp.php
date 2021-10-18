<?php
ob_start();
require 'imp_constancia_traspaso.php';
$html = ob_get_clean();
// echo $html;
descargarPDFConstanciaActivos("COBOFAR - Constancia Traspaso Activos Fijos",$html);