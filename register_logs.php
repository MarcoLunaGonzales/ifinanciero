<?php
function log_querys($query,$tabla='sin tabla',$accion='sin accion',$obs='sin observación',$url='dirección no especificada'){ 
    //quito la comilla doble
     $tabla=trim(preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, trim($tabla)));
     $accion=trim(preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, trim($accion)));
     $query=trim(preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, trim($query)));
     $obs=trim(preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, trim($obs)));
     $url=trim(preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, trim($url)));

     //escribir en el archivo
     $path = "logs_sistema/".$tabla;
     if (!file_exists($path)) {
        mkdir($path, 0777, true);
     }

     $ddf = fopen($path.'/querys_log.log','a');
     fwrite($ddf,""."[".date("r")."][query]#####$tabla#####$accion#####[$query]#####$url#####$obs\r\n"); 
     fclose($ddf); 
}
?>