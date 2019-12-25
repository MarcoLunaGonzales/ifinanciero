<?php
require_once 'conexion.php';

function showAlertSuccessError($bandera, $url){
   if($bandera==true){
      echo "<script>
         alerts.showSwal('success-message','$url');
      </script>";
   }
   if ($bandera==false){
      echo "<script>
         alerts.showSwal('error-message','$url');
      </script>";
   }
}

function showAlertSuccessError2($bandera, $url){
   if($bandera==true){
      echo "<script>
         alerts.showSwal('success-message2','$url');
      </script>";
   }
   if ($bandera==false){
      echo "<script>
         alerts.showSwal('error-message2','$url');
      </script>";
   }
}

function clean_string($string)
{
 
    $string = trim($string);
 
    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );
 
    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );
 
    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );
 
    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );
 
    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );
 
    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $string
    );
 
    $string = str_replace(
        array('.',',',';','  '),
        array('','','',' '),
        $string
    );
    return $string;
}

function string_sanitize($s) { 
  $result = preg_replace("/[^a-zA-Z0-9]+/", "", $s); 
  return $result; 
} 

function formatNumberInt($valor) { 
   $float_redondeado=number_format($valor, 0); 
   return $float_redondeado; 
}

function formatNumberDec($valor) { 
   $float_redondeado=number_format($valor, 2); 
   return $float_redondeado; 
}


function obtieneValorConfig($codigo){
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT valor_configuracion FROM configuraciones where id_configuracion='$codigo'");
  $stmt->execute();
  $valor="";
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $valor=$row['valor_configuracion'];
  }  
  return $valor;
}
function cantidadF($arreglo){
    $cont=0;
    foreach ($arreglo as $elemento) {
      $cont++;
    }
    return $cont;
   }



   

?>
