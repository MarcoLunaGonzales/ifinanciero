<?php 
/*REGISTRO DE CONTACTOS XLS FINANCIERO
WS: ws-fin-cliente-contacto.php 
*/
//LLAVES DE ACCESO AL WS

    $sIde = "facifin";
    $sKey = "AX546321asbhy347bhas191001bn0rc4654";  
    // OBTENER MODULOS PAGADOS x CURSO
    // $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
    //         "accion"=>"ObtenerModulosPagados", 
    //         "Identificacion"=>$ci_estudiante, //7666922 ci del alumno
    //         "IdCurso"=>$IdCurso); //1565 

    $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
            "accion"=>"solicitarTipoCambio", 
            "idMoneda"=>2, //7666922 ci del alumno
            "fecha"=>'2021-11-01'); //1565
    $parametros=json_encode($parametros);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/ifinanciero/wsifin/ws_tiposcambio.php");
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    curl_close ($ch);


  header('Content-type: application/json');   
  print_r($remote_server_output);       
  
?>
