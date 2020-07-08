<?php
ob_start();
require 'imp_simulacion_costo.php';
$html = ob_get_clean();
descargarPDFSolicitudesRecursos("IBNORCA - PROPUESTAS",$html);