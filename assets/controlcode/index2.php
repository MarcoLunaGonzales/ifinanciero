<?php
include 'sin/ControlCode.php';
try{
    $filename="5000CasosPruebaCCVer7.txt";
    $handle = fopen($filename, "r");
    if ($handle) {        
        $controlCode = new ControlCode();
        $count=0;  
		$countError=0;
        while (($line = fgets($handle)) !== false) {        
            $reg = explode("|", $line);        
            //genera codigo de control
            $code = $controlCode->generate($reg[0],//Numero de autorizacion
                                           $reg[1],//Numero de factura
                                           $reg[2],//Número de Identificación Tributaria o Carnet de Identidad
                                           str_replace('/','',$reg[3]),//fecha de transaccion de la forma AAAAMMDD
                                           $reg[4],//Monto de la transacción
                                           $reg[5]//Llave de dosificación
                    );
			echo $code." revisado ".$reg[10]."<br>";
            if($code===$reg[10]){                
                $count+=1;
            }else{
				$countError+=1;
			}
        }        
        echo 'Archivo <b>'.$filename.'</b><br/>';
        echo 'Total registros testeados <b>'.$count.'</b><br/>';        
        echo 'Errores <b>'.$countError.'</b><br/>';        
    fclose($handle);
    
    }else{
         throw new Exception("<b>Could not open the file!</b>");
    }
}catch ( Exception $e ){
     echo "Error (File: ".$e->getFile().", line ".
          $e->getLine()."): ".$e->getMessage();
}