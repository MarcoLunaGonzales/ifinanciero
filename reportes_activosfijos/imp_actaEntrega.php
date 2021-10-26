<?php
ob_start();
require 'imp_actaEntrega_html.php';
$html = ob_get_clean();
// echo $html;
descargarPDFConstanciaActivos("IFINANCIERO - ACTA DE ENTREGA BIENES DE USO",$html);