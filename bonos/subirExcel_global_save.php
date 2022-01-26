<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';
$dbh = new Conexion();


// session_start();
$globalUser=$_SESSION['globalUser'];
// $codBono=$_POST["codBono"];
$codMes=$_POST["cod_mes"];
$codGestion=$_POST["codGestion"];
$opcionCargar=$_POST["opcionCargar"];

$ruta="bonos/upload/";
echo "<br><br><br><br><br>";
foreach ($_FILES as $key){
    $ruta_temporal=$key["tmp_name"];
    if($key["type"]=="text/csv" or $key["type"]=="application/vnd.ms-excel"){
        //echo "<br>aqui";
        $nombre_nuevo= formatoNombreArchivoExcel();
        $destino=$ruta.$nombre_nuevo;
        move_uploaded_file($ruta_temporal,$destino);
        $alert=true;
    }else{
        $alert=false;
        showAlertSuccessError($alert,$urlList);
    }
}
$flagSuccess=false;


$cod_estadoreferencial=1;
if($alert==true){
    $stmtBonos = $dbh->prepare("SELECT codigo,nombre from bonos where cod_estadoreferencial=1 order by codigo");
    $stmtBonos->execute();
    $stmtBonos->bindColumn('codigo', $codigo_bono);
    $stmtBonos->bindColumn('nombre', $nombre_bono);
    $bonos_array=array();
    $i=0;
    while ($row = $stmtBonos->fetch(PDO::FETCH_BOUND)) {
        $bonos_array[$i]=$codigo_bono;
        $i++;
    }
    $stmtdesc = $dbh->prepare("SELECT codigo,nombre from descuentos where cod_estadoreferencial=1 order by codigo");
    $stmtdesc->execute();
    $stmtdesc->bindColumn('codigo', $codigo_descuento);
    $stmtdesc->bindColumn('nombre', $nombre_descuento);
    $descuentos_array=array();
    $i=0;
    while ($row = $stmtdesc->fetch(PDO::FETCH_BOUND)) {
        $descuentos_array[$i]=$codigo_descuento;
        $i++;
    }    

    $delimitador = "|";
    $longitudDeLinea = 1000;
    $x=0;
    $datos=array();
    $fichero=fopen($destino,'r');
    //borrar todo y cargar de nuevo
    if($opcionCargar==3){
        //borramos logicamente
        $stmtkardexDelete = $dbh->prepare("UPDATE personal_kardex_mes SET cod_estadoreferencial=2 
            WHERE  cod_gestion=$codGestion and cod_mes=$codMes");
        $stmtkardexDelete->execute();

        $sql="UPDATE bonos_personal_mes SET cod_estadoreferencial=2 
            WHERE  cod_gestion=$codGestion and cod_mes=$codMes";
            // echo $sql;
        $stmtbonosDelete = $dbh->prepare($sql);
        $stmtbonosDelete->execute(); 
        $stmte = $dbh->prepare("UPDATE descuentos_personal_mes SET cod_estadoreferencial=2 
            WHERE  cod_gestion=$codGestion and cod_mes=$codMes");
        $stmte->execute();
        $stmtAnticipoDelete = $dbh->prepare("UPDATE anticipos_personal SET cod_estadoreferencial=2 
            WHERE  cod_gestion=$codGestion and cod_mes=$codMes");
        $stmtAnticipoDelete->execute(); 
        while((($datos=fgetcsv($fichero,$longitudDeLinea,$delimitador))!=FALSE)){
            $x++;
            if($x>1){
                $cod_personal=$datos[0];
                $sw_personal=verificarExistenciaPersona($cod_personal);
                if($sw_personal){
                    // $ci=$datos[1];
                    // $nombre=$datos[2];
                    // $area=$datos[3];
                    $dias_trabajados_l_v=$datos[4];
                    // $otros_bonos=$datos[5];
                    // $otros=$datos[6];
                    // $atrasos=$datos[7];
                    // $descuentos_RCIVA=$datos[8];
                    // $otros_descuentospersonal=$datos[9];
                    // $anticipos=$datos[10];
                    $sqlKardex="INSERT INTO personal_kardex_mes(cod_personal,cod_gestion,cod_mes,dias_trabajados,cod_estadoreferencial) 
                        VALUES ('$cod_personal','$codGestion','$codMes','$dias_trabajados_l_v',$cod_estadoreferencial)";
                    //echo $sqlKardex;
                    $stmtKardex = $dbh->prepare($sqlKardex);
                    $flagSuccess=$stmtKardex->execute();

                    //BONOS
                    $contador_excel_bonos=5;
                    for ($j=0; $j <count($bonos_array) ; $j++) { 
                        $codDescuento=$bonos_array[$j];
                        if(isset($datos[$contador_excel_bonos])){
                            $monto=formatearNumerosExcel($datos[$contador_excel_bonos]);
                        }else{
                            $monto=0;
                        }
                        $sqlbonos="INSERT INTO bonos_personal_mes (cod_bono, cod_personal,cod_gestion,cod_mes,monto, cod_estadoreferencial,created_at,created_by) 
                        VALUES ($codDescuento,$cod_personal,$codGestion,$codMes,$monto,$cod_estadoreferencial,now(),'$globalUser')";
                        //echo $sqlbonos;
                        $stmtBonos = $dbh->prepare($sqlbonos);
                        $flagSuccess=$stmtBonos->execute();
                        $contador_excel_bonos++;
                    }
                    $contador_excel=$contador_excel_bonos;
                    for ($j=0; $j <count($descuentos_array) ; $j++) { 
                        $codDescuento=$descuentos_array[$j];
                        if(isset($datos[$contador_excel])){
                            $monto=formatearNumerosExcel($datos[$contador_excel]);
                        }else{
                            $monto=0;
                        }
                        //inserta nuevos
                        $stmtDescuentos = $dbh->prepare("INSERT INTO descuentos_personal_mes (cod_descuento, cod_personal,cod_gestion,cod_mes,monto, cod_estadoreferencial) 
                        VALUES ($codDescuento,$cod_personal,$codGestion,$codMes,$monto,$cod_estadoreferencial)");
                        $flagSuccess=$stmtDescuentos->execute();    
                        $contador_excel++;
                    }
                    $anticipos=$datos[$contador_excel];
                    //**INGRESAMOS ANTICIPOS
                    $stmtAnticipos = $dbh->prepare("INSERT INTO anticipos_personal (cod_gestion,cod_mes,cod_personal,monto,fecha_registro, cod_estadoreferencial) 
                        VALUES ($codGestion,$codMes,$cod_personal,$anticipos,now(),$cod_estadoreferencial)");
                    $flagSuccess=$stmtAnticipos->execute();
                }
            }
        }
        showAlertSuccessError($flagSuccess,'?opcion=planillasSueldoPersonal');
    }
    fclose($fichero); 
    unlink($destino); 


}
?>