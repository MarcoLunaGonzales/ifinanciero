<?php

require_once 'conexion2.php';
ini_set('memory_limit', '1G');


function obtenerSaldoLibretaBancariaNuevo($codigoLibreta,$montoLibreta,$fechaIniFac,$fechaFinFac){
   $codFacturaRaiz=buscarFacturaLibretaRaiz($codigoLibreta,$fechaIniFac,$fechaFinFac);
   $saldoFacturaInicial=obtenerMontoFactura($codFacturaRaiz);   

   $arraySaldoDeposito=saldoLibretaBancariaDesdeRaiz($codFacturaRaiz,$saldoFacturaInicial,$montoLibreta);
   $codDepositoEncontrado=$arraySaldoDeposito[0];
   $saldoDepositoEncontrado=$arraySaldoDeposito[1];

   if($codigoLibreta==$codDepositoEncontrado){
      $saldoDevolver=$saldoDepositoEncontrado;
   }else{
      $saldoDevolver=0;
   }

   return($saldoDevolver);
}

function buscarFacturaLibretaRaiz($codLibretaOrigen,$fechaini,$fechafin){
   $dbh = new Conexion();
   $codFactura=0;
   $sql = "SELECT lf.cod_facturaventa, lf.cod_libretabancariadetalle, f.fecha_factura  from libretas_bancariasdetalle_facturas lf, facturas_venta f where lf.cod_facturaventa=f.codigo and f.cod_estadofactura<>2 and lf.cod_libretabancariadetalle in ($codLibretaOrigen) and f.fecha_factura between '$fechaini 00:00:00' and '$fechafin 23:59:59' ORDER BY f.fecha_factura";
   $stmt = $dbh -> prepare($sql);
   $stmt -> execute();
   if($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
      //sacamos la factura mas antigua para su analisis
      $codFactura=$row['cod_facturaventa'];
   }
   //echo "factura: ".$codFactura."<br>";

   //buscamos el deposito mas antiguo de la factura encontrada
   $codDepositoBuscado=buscarDepositoRaiz($codFactura);
   //echo "Llama deposito raiz de: ".$codDepositoBuscado."<br>";
   
   //si el deposito mas antiguo es el mismo que el origen devolvemos el origen si no seguimos la recursividad
   if($codLibretaOrigen!=$codDepositoBuscado){
      //echo "recursivo: ".$codDepositoBuscado."<br>";
      return(buscarFacturaLibretaRaiz($codDepositoBuscado,$fechaini,$fechafin));
   }else{
      //echo "return: ".$codLibretaOrigen." libs ".$codDepositoBuscado." ".$codFactura."<br>";
      return($codFactura);         
   }
}

function buscarDepositoRaiz($codFactura){
   $dbh1 = new Conexion();
   $codLibreta=0;

   $sql1 = "SELECT lf.cod_facturaventa, lf.cod_libretabancariadetalle, l.fecha_hora  from libretas_bancariasdetalle_facturas lf, libretas_bancariasdetalle l where lf.cod_libretabancariadetalle=l.codigo and lf.cod_facturaventa in ($codFactura) and l.cod_estadoreferencial=1 ORDER BY l.fecha_hora";
   //echo $sql;
   $stmt1 = $dbh1 -> prepare($sql1);
   $stmt1 -> execute();
   if($row1 = $stmt1 -> fetch(PDO::FETCH_ASSOC)){
      //sacamos el deposito mas antiguo para su analisis
      $codLibreta=$row1['cod_libretabancariadetalle'];
   }
   //echo "retornaCodLibreta: ".$codLibreta."<br>";
   return($codLibreta);
}

function saldoLibretaBancariaDesdeRaiz($codFactura, $saldoFactura, $saldoDeposito){
   $dbh = new Conexion();
   $saldoDepositoPivote=0;
   $codFacturaSiguiente=0;
   $codDepositoSiguiente=0;
   
   $arrayResult=array();
   
   //echo "entra codFac: ".$codFactura." montoFac: ".$saldoFactura." montoDepo: ".$saldoDeposito."<br>";

   $saldoFacturaPivote=$saldoFactura;   
   $sql = "SELECT lf.cod_facturaventa, lf.cod_libretabancariadetalle, ld.fecha_hora, ld.monto from libretas_bancariasdetalle_facturas lf, libretas_bancariasdetalle ld where lf.cod_libretabancariadetalle=ld.codigo and lf.cod_facturaventa in ($codFactura) and ld.cod_estadoreferencial=1 order by ld.fecha_hora";
   $stmt = $dbh -> prepare($sql);
   $stmt -> execute();
   $indice=1;
   while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
      $codDepositoSiguiente=$row['cod_libretabancariadetalle'];
      
      //echo " deposiguiente: ".$codDepositoSiguiente."<br>";
      
      if($indice==1){
         $montoDeposito=$saldoDeposito;
      }else{
         $montoDeposito=$row['monto'];
      }
      $saldoDepositoPivote=$montoDeposito-$saldoFacturaPivote;
      if($saldoDepositoPivote<0){
         $saldoDepositoPivote=0;
         $saldoFacturaPivote=$saldoFacturaPivote-$montoDeposito;
      }
      $indice++;
   }
   //preguntamos si hay mas facturas asociadas al ultimo deposito
   $codFacturaSiguiente=buscarFacturasAdicionalesDepositos($codDepositoSiguiente, $codFactura);
   $montoFacturaSiguiente=obtenerMontoFactura($codFacturaSiguiente);
   if($codFacturaSiguiente!=0 && $saldoDepositoPivote>0){
      return(saldoLibretaBancariaDesdeRaiz($codFacturaSiguiente,$montoFacturaSiguiente,$saldoDepositoPivote));
   }else{
      $arrayResult[0]=$codDepositoSiguiente;
      $arrayResult[1]=$saldoDepositoPivote;
      return($arrayResult);
   }
}

function buscarFacturasAdicionalesDepositos($codDepositoBuscado, $codFactura){
   $dbh1 = new Conexion();
   $codFacturaBuscada=0;

   $sql1 = "SELECT lf.cod_facturaventa, lf.cod_libretabancariadetalle, f.fecha_factura from libretas_bancariasdetalle_facturas lf, facturas_venta f where lf.cod_facturaventa=f.codigo and lf.cod_facturaventa not in ($codFactura) and lf.cod_libretabancariadetalle=$codDepositoBuscado and f.cod_estadofactura<>2 ORDER BY f.fecha_factura";
   
   //echo "MAS FACTURAS: ".$sql1."<br>";
   
   $stmt1 = $dbh1 -> prepare($sql1);
   $stmt1 -> execute();
   if($row1 = $stmt1 -> fetch(PDO::FETCH_ASSOC)){
      //sacamos la factura mas antiguo para su analisis
      $codFacturaBuscada=$row1['cod_facturaventa'];
   }
   return($codFacturaBuscada);
}

function obtenerMontoFactura($codigo){
    $dbh = new Conexion();
    $sql="SELECT SUM((fd.cantidad*fd.precio)-fd.descuento_bob) as monto_factura from facturas_venta fv, facturas_ventadetalle fd  
      where fv.codigo=fd.cod_facturaventa and fv.cod_estadofactura<>2 and fv.codigo=$codigo";
     $stmt = $dbh->prepare($sql);
     $stmt->execute();
     $valor=0;
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $valor=$row["monto_factura"];
     }
     return $valor; 
}

function obtenerSaldoAnteriorLibreta($fecha,$codLibreta){

   $dbh = new Conexion();
   $sql="SELECT sum(ce.monto)as monto
   FROM libretas_bancariasdetalle ce join libretas_bancarias lb on lb.codigo=ce.cod_libretabancaria 
   where lb.codigo in ($codLibreta) and ce.fecha_hora < '$fecha 00:00:00' and
   ce.cod_estadoreferencial=1 order by ce.fecha_hora";
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   $valor=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
     $valor=$row["monto"];
   }
   return $valor; 
}


?>
