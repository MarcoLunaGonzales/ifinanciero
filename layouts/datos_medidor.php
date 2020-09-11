<?php
header('Content-Type: application/json');
require_once '../conexion.php';
$dbh = new Conexion();

$oficina="0";
$area="0";
$anio="2020";
$mes="12";

$results=[];
switch($_GET['t']){
		// Buscar ingresos
		case 1:

			$ingresoTotal=obtenerPresupuestoEjecucionPorArea($oficina,$area,$anio,$mes);
			$valorIngreso=number_format(calcularValorEnPoncentaje($ingresoTotal['ejecutado'],$ingresoTotal['presupuesto']),0,'.','');

		    $results=array('ibnorca' => $valorIngreso);
			$json=json_encode($results);
			echo $json;
		break; 
		// Buscar egresos
		default:
			
		break;

}

function calcularValorEnPoncentaje($valor,$total){
	$porcentaje=0;
	if($total>0){
		$porcentaje=($valor*100)/$total;
	}			
  return $porcentaje;
}
function obtenerValorConfiguracion($id){
     $dbh = new Conexion();
     $stmt = $dbh->prepare("SELECT valor_configuracion from configuraciones c where id_configuracion=$id");
     $stmt->execute();
     $codigoComprobante=0;
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $codigoComprobante=$row['valor_configuracion'];
     }
     return($codigoComprobante);
    }
function obtenerPresupuestoEjecucionPorArea($oficina,$area,$anio,$mes){
    $direccion=obtenerValorConfiguracion(45);//direccion del Server del Servicio
    $sIde = "monitoreo"; 
    $sKey="101010"; 

  /*PARAMETROS PARA LA OBTENCION DE LISTAS DE PERSONAL*/
    $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "oficina"=>$oficina, "area"=>$area, "anio"=>$anio, "mes"=>$mes, "accion"=>"listar"); //

    $parametros=json_encode($parametros);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$direccion."ws/wsPresupuestoIngresosTotal.php");
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    curl_close ($ch);
    $datos=json_decode($remote_server_output);
      return array('presupuesto' => $datos->presupuesto, 'ejecutado' => $datos->ejecutado);       
    }
?>
