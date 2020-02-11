<?php

//require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';

ini_set('display_errors',1);

$dbh = new Conexion();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {
    $dias_feriados=2;//buscar datos
    //sacamos codigo e identificacion de personal
    $stmtPersonal = $dbh->prepare("SELECT codigo,identificacion from personal where cod_estadoreferencial=1 and identificacion in (9253211,3372876,5976118,2233839)");    
    $stmtPersonal->execute();    
    $stmtPersonal->bindColumn('codigo', $codigo);
    $stmtPersonal->bindColumn('identificacion', $identificacion);
    $dias_asistidos=0;
    while ($row = $stmtPersonal->fetch(PDO::FETCH_BOUND)) {
        //primer caso . el sistema registra  en hra_mar_ent_tar tanto entrada y salida
        $stmt = $dbh->prepare("SELECT DATE(hra_mar_ent_tar) as hra_mar_ent_tar FROM asistencia_prueba
        where id_empleado=$identificacion GROUP BY DATE(hra_mar_ent_tar)");    
        $stmt->execute();
        //bindColumn
        // $stmt->bindColumn('nombre_empleado', $nombre_empleado);
        // $stmt->bindColumn('fecha', $fecha);
        // $stmt->bindColumn('hra_ent_man', $hra_ent_man);
        // $stmt->bindColumn('hra_sal_man', $hra_sal_man);
        $stmt->bindColumn('hra_mar_ent_tar', $hra_mar_ent_tar);
        // $stmt->bindColumn('hra_mar_sal_tar', $hra_mar_sal_tar);
        // $stmt->bindColumn('tolerancia', $tolerancia);
        // $stmt->bindColumn('observacion', $observacion);
        $dias_asistidos=0;
        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {            
            if($hra_mar_ent_tar!=null){
                $dias_asistidos++;            
            }
        }
        //segundo caso . el sistema registra  en hra_mar_sal_tar tanto entrada y salida
        $stmt2 = $dbh->prepare("SELECT DATE(hra_mar_sal_tar) as hra_mar_sal_tar FROM asistencia_prueba
        where id_empleado=$identificacion GROUP BY DATE(hra_mar_sal_tar)");    
        $stmt2->execute();
        $stmt2->bindColumn('hra_mar_sal_tar', $hra_mar_sal_tar);
        while ($row = $stmt2->fetch(PDO::FETCH_BOUND)) {        
            if($hra_mar_sal_tar!=null){
                $dias_asistidos++;            
            }
        }
        //tercer caso. el sistema registra en hra_mar_sal_tar o hra_mar_ent_tar null  
        $stmt3 = $dbh->prepare("SELECT hra_mar_ent_tar,hra_mar_sal_tar,observacion from asistencia_prueba
        where id_empleado=$identificacion");    
        $stmt3->execute();
        $stmt3->bindColumn('hra_mar_ent_tar', $hra_mar_ent_tar);
        $stmt3->bindColumn('hra_mar_sal_tar', $hra_mar_sal_tar);
        $stmt3->bindColumn('observacion', $observacion);
        while ($row = $stmt3->fetch(PDO::FETCH_BOUND)) {
            // echo "hra_mar_sal_tar: ".$hra_mar_sal_tar." - ";
            // echo "hra_mar_ent_tar: ".$hra_mar_ent_tar." - ";
            // echo "observacion: ".$observacion."<br>";
            if($hra_mar_sal_tar==null && $hra_mar_ent_tar==null){
                if($observacion!='-' && $observacion!='Falta'){
                    $dias_asistidos++;
                    // echo "entra <br>";
                }
            }
        }
        $dias_asistidos=$dias_asistidos+$dias_feriados;
        
        //===========vemos la parte de minitos de retraso
        //hora entrada y hora salida
        $stmtHraEnt = $dbh->prepare("SELECT hra_ent_man from asistencia_prueba where hra_ent_man is not null and id_empleado=$identificacion GROUP BY hra_ent_man");    
        $stmtHraEnt->execute();    
        $resultHraEnt=$stmtHraEnt->fetch();
        $hra_ent_man=$resultHraEnt['hra_ent_man'];

        $stmtHraSal = $dbh->prepare("SELECT hra_sal_man from asistencia_prueba where hra_sal_man is not null and id_empleado=$identificacion GROUP BY hra_sal_man");    
        $stmtHraSal->execute();    
        $resultHraSal=$stmtHraSal->fetch();
        $hra_sal_man=$resultHraSal['hra_sal_man'];
        //horas entrada y salida
        $fechaComoEntero = strtotime($hra_ent_man);
        $hora_ingreso = date("H", $fechaComoEntero);
        $hora_ingreso_aux=$hora_ingreso.":10";
        $fechaComoEntero = strtotime($hra_sal_man);
        $hora_salida = date("H", $fechaComoEntero);
        $hora_salida_aux=$hora_salida.":00";

        echo 'id_personal: '.$identificacion." - ";
        echo 'hora_entrada: '.$hora_ingreso.":00 - ";
        echo 'hora_salida: '.$hora_salida.":00 - ";
        echo 'dias_asistidos: '.$dias_asistidos."<br>";
        //minutos atraso
        $stmtMinAtraso = $dbh->prepare("SELECT hra_mar_ent_tar,observacion from asistencia_prueba
        where hra_mar_ent_tar is not null and id_empleado=$identificacion");    
        $stmtMinAtraso->execute();
        $stmtMinAtraso->bindColumn('hra_mar_ent_tar', $hra_mar_ent_tar);
        $stmtMinAtraso->bindColumn('observacion', $observacion);
        while ($row = $stmtMinAtraso->fetch(PDO::FETCH_BOUND)) {
            $fechaComoEntero = strtotime($hra_mar_ent_tar);
            $hora_marcada = date("H", $fechaComoEntero);
            $min_marcado = date("i", $fechaComoEntero);
            $hra_completa_mar_aux=$hora_marcada.":".$min_marcado;
            // $fechaComoEntero = strtotime($hra_sal_man);
            // $hora_salida_mar = date("H", $fechaComoEntero);
            // $min_salida_mar = date("i", $fechaComoEntero);
            // $hra_salida_mar_aux=$hora_salida_mar.":".$min_salida_mar;
            if($hora_ingreso==$hora_marcada || $hora_ingreso<$hora_marcada){//hora entrada


            }elseif($hora_salida==$hora_marcada || $hora_marcada < $hora_salida){//hora salida

            }

            if($hra_mar_sal_tar==null && $hra_mar_ent_tar==null){
                if($observacion!='-' && $observacion!='Falta'){
                    $dias_asistidos++;
                    // echo "entra <br>";
                }
            }
        }
        
    }

    
    



    

    
} catch(PDOException $ex){
    //manejar error
    echo "Un error ocurrio".$ex->getMessage();
}
?>