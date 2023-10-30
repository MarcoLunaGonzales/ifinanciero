<?php 
/*REGISTRO DE CONTACTOS XLS FINANCIERO
WS: ws-fin-cliente-contacto.php 
*/
//LLAVES DE ACCESO AL WS
// echo "aqui";
    $direccion="http://ibnored.ibnorca.org/wsibnob/";//direccion des servicio web
    $sIde = "ifinanciero";
    $sKey = "ce94a8dabdf0b112eafa27a5aa475751";  
    $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
            "accion"=>"ObtenerModuloxPagarPagadoySaldo", 
            "Identificacion"=>3515572, //7666922 ci del alumno
            "IdCurso"=>4217); //1565
    $parametros=json_encode($parametros);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/capacitacion/ws-inscribiralumno.php"); //PRUEBA
    //curl_setopt($ch, CURLOPT_URL,$direccion."capacitacion/ws-inscribiralumno.php");
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    curl_close ($ch);

    header('Content-type: application/json');   
    print_r($remote_server_output);       
  
?>
