<?php

require_once 'conexion.php';

function presupuestoIngresosMes($agencia, $anio, $mes, $organismo, $acumulado, $cuenta){
   $dbh = new Conexion();
   $agencia=str_replace('|', ',', $agencia);
   if($acumulado==1){
     $sql="SELECT sum(p.monto)as monto from po_presupuesto p where p.cod_ano='$anio' and p.cod_mes<='$mes' and p.cod_fondo in ($agencia) and p.cod_cuenta like '4%'";
     if($organismo!=0){
       $sql.=" and p.cod_organismo in ($organismo) ";
     }
     if($cuenta!=0){
        $sql.=" and p.cod_cuenta='$cuenta' ";
     }
   }else{
     $sql="SELECT sum(p.monto)as monto from po_presupuesto p where p.cod_ano='$anio' and p.cod_mes='$mes' and p.cod_fondo in ($agencia) and p.cod_cuenta like '4%'";
     if($organismo!=0){
       $sql.=" and p.cod_organismo in ($organismo)";
     }
      if($cuenta!=0){
        $sql.=" and p.cod_cuenta='$cuenta' ";
     }
   } 
//  echo $sql;
  $stmt = $dbh->prepare($sql);
  $flagSuccess2=$stmt->execute();

   $montoIngreso=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $montoIngreso=$row['monto'];
   }
   return($montoIngreso);
}

function presupuestoIngresosMesVersion($agencia, $anio, $mes, $organismo, $acumulado, $cuenta, $codVersion){
   $dbh = new Conexion();
   $agencia=str_replace('|', ',', $agencia);
   if($acumulado==1){
     $sql="SELECT sum(p.monto)as monto from po_presupuesto_version p where p.cod_ano='$anio' and p.cod_mes<='$mes' and p.cod_fondo in ($agencia) and p.cod_cuenta like '4%' and p.cod_version=$codVersion";
     if($organismo!=0){
       $sql.=" and p.cod_organismo in ($organismo) ";
     }
     if($cuenta!=0){
        $sql.=" and p.cod_cuenta='$cuenta' ";
     }
   }else{
     $sql="SELECT sum(p.monto)as monto from po_presupuesto_version p where p.cod_ano='$anio' and p.cod_mes='$mes' and p.cod_fondo in ($agencia) and p.cod_cuenta like '4%' and p.cod_version=$codVersion";
     if($organismo!=0){
       $sql.=" and p.cod_organismo in ($organismo)";
     }
      if($cuenta!=0){
        $sql.=" and p.cod_cuenta='$cuenta' ";
     }
   } 
//  echo $sql;
  $stmt = $dbh->prepare($sql);
  $flagSuccess2=$stmt->execute();

   $montoIngreso=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $montoIngreso=$row['monto'];
   }
   return($montoIngreso);
}

function ejecutadoIngresosMes($agencia, $anio, $mes, $organismo, $acumulado, $cuenta){
  $dbh = new Conexion();
  $agencia=str_replace('|', ',', $agencia);
  $sql="SELECT distinct(pc.codigo) as codigo, pc.nivel from po_plancuentas pc where pc.codigo like '4%'";
  if($organismo!=0){
    $sql.=" and pc.codigo in (select distinct(pp.cod_cuenta) from po_presupuesto pp where pp.cod_organismo in ($organismo) and pp.cod_cuenta like '4%' and pp.monto>0 and pp.cod_ano='$anio')";
  }else{
    $sql.=" and pc.nivel=5 ";
  }
  if($cuenta!=0){
    $sql.=" and pc.codigo='$cuenta'";
  }
  
  //echo $sql;
  
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $montoIngresoEjecutado=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codPlanCuenta=$row['codigo'];
      $nivelCuenta=$row['nivel'];
      $campoTablaCuenta="";
      if($nivelCuenta==4){
        $campoTablaCuenta="p.cta_n4";
      }else{
        $campoTablaCuenta="p.cuenta";
      }

      if($acumulado==1){
        $sqlMayor="SELECT sum(p.monto)as monto from po_mayores p where p.fondo in ($agencia) and $campoTablaCuenta='$codPlanCuenta' and p.anio='$anio' and p.mes<='$mes'";
      }else{
        $sqlMayor="SELECT sum(p.monto)as monto from po_mayores p where p.fondo in ($agencia) and $campoTablaCuenta='$codPlanCuenta' and p.anio='$anio' and p.mes='$mes'";
      }
      
      //echo $sqlMayor;
      
      $stmtMayor=$dbh->prepare($sqlMayor);
      $stmtMayor->execute();
      while ($rowMayor = $stmtMayor->fetch(PDO::FETCH_ASSOC)) {
          $montoEjecutado=$rowMayor['monto'];
          $montoIngresoEjecutado=$montoIngresoEjecutado+$montoEjecutado;
          //echo $codPlanCuenta." nivel ".$nivelCuenta." ".$montoEjecutado." <br>";
      }
  }
  $montoIngresoEjecutado=$montoIngresoEjecutado*(-1);
  return($montoIngresoEjecutado);
}

//and p.cod_cuenta not in ('5030150','5030151','5030190')
function presupuestoEgresosMes($agencia, $anio, $mes, $organismo, $acumulado, $cuenta){
  $dbh = new Conexion();
  $agencia=str_replace('|', ',', $agencia);
   if($acumulado==1){
     $sql="SELECT sum(p.monto)as monto from po_presupuesto p where p.cod_ano='$anio' and p.cod_mes<='$mes' and p.cod_fondo in ($agencia) and p.cod_cuenta like '5%'"; 
      if($organismo!=0){
        $sql.="and p.cod_organismo in ($organismo)";
      }
      if($cuenta!=0){
        $sql.="and p.cod_cuenta='$cuenta'";
      }
   }else{
     $sql="SELECT sum(p.monto)as monto from po_presupuesto p where p.cod_ano='$anio' and p.cod_mes='$mes' and p.cod_fondo in ($agencia) and p.cod_cuenta like '5%'";
      if($organismo!=0){
        $sql.="and p.cod_organismo in ($organismo)";
      } 
      if($cuenta!=0){
        $sql.="and p.cod_cuenta='$cuenta'";
      }       
  }
  //echo $sql;
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   $montoEgreso=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $montoEgreso=$row['monto'];
   }
   return($montoEgreso);
}

function montoRedistribucionIT($agencia, $anio, $mes, $organismo, $acumulado, $cuenta){
  $dbh = new Conexion();
  //SACAMOS LA CUENTA DE IT
  $stmt = $dbh->prepare("SELECT valor_configuracion FROM configuraciones where id_configuracion=3");
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $cuentaIT=$row['valor_configuracion'];
  }
  $montoIngreso=0;
  $montoIngreso=ejecutadoIngresosMes($agencia, $anio, $mes, $organismo, $acumulado, 0);
  $montoRedistIT=0;
  $montoRedistIT=(($montoIngreso*100)/87)*0.03;
  return($montoRedistIT);
}

function ejecutadoEgresosMes($agencia, $anio, $mes, $organismo, $acumulado, $cuenta){
  $dbh = new Conexion();
  //SACAMOS LA CONFIGURACION PARA REDIST 
  $banderaRedistIT=0;
  $stmt = $dbh->prepare("SELECT valor_configuracion FROM configuraciones where id_configuracion=-1");
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $banderaRedistIT=$row['valor_configuracion'];
  }
  $cuentaIT="";
  $stmt = $dbh->prepare("SELECT valor_configuracion FROM configuraciones where id_configuracion=-3");
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $cuentaIT=$row['valor_configuracion'];
  }
  $cuentaDN="";
  $stmt = $dbh->prepare("SELECT valor_configuracion FROM configuraciones where id_configuracion=-4");
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $cuentaDN=$row['valor_configuracion'];
  }
  $cuentaSA="";
  $stmt = $dbh->prepare("SELECT valor_configuracion FROM configuraciones where id_configuracion=-5");
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $cuentaSA=$row['valor_configuracion'];
  }

   $agenciaX=$agencia;//str_replace('|', ',', $agencia);//filas modificadas
   if($organismo!=0){
      $sqlEgresos="SELECT pc.codigo,pc.numero from plan_cuentas pc where pc.numero like '5%' and pc.nivel=5";//filas modificadas
      if($cuenta!=0){
        $sqlEgresos.=" and pc.numero='$cuenta'";//fila modificada
      }
      $stmt = $dbh->prepare($sqlEgresos);
      $stmt->execute();
      $montoEgresoEjecutado=0;
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $codPlanCuenta=$row['numero']; //fila modificada codigo _ numero

          if($acumulado==1){
            $sqlMayor="SELECT sum(p.monto)as monto from po_mayores p where p.fondo in ($agenciaX) and p.cuenta='$codPlanCuenta' and p.anio='$anio' and p.mes<='$mes' and p.organismo in ($organismo)";
          }else{
            $sqlMayor="SELECT sum(p.monto)as monto from po_mayores p where p.fondo in ($agenciaX) and p.cuenta='$codPlanCuenta' and p.anio='$anio' and p.mes='$mes' and p.organismo in ($organismo)";
          }          
          $stmtMayor=$dbh->prepare($sqlMayor);
          $stmtMayor->execute();
          while ($rowMayor = $stmtMayor->fetch(PDO::FETCH_ASSOC)) {
              $montoEjecutado=$rowMayor['monto'];
              $montoEgresoEjecutado=$montoEgresoEjecutado+$montoEjecutado;
          }
          //REDISTRIBUCION DEL IT
          $montoRedistribucionIT=0;
          if($banderaRedistIT==1 && $cuentaIT==$codPlanCuenta){
            $montoRedistribucionIT=montoRedistribucionIT($agencia,$anio,$mes,$organismo,$acumulado,$codPlanCuenta);
            $montoEgresoEjecutado=$montoEgresoEjecutado+$montoRedistribucionIT;
          }
          if($cuentaDN==$codPlanCuenta){
            $montoDistribuidoDN=distribucionDNSA($agenciaX, $anio, $mes, $organismo, $acumulado, 1);
            $montoEgresoEjecutado=$montoEgresoEjecutado+$montoDistribuidoDN;
          }
          if($cuentaSA==$codPlanCuenta){
            $montoDistribuidoSA=distribucionDNSA($agenciaX, $anio, $mes, $organismo, $acumulado, 2);
            $montoEgresoEjecutado=$montoEgresoEjecutado+$montoDistribuidoSA;
          }

      }
   }else{
      $sqlEgresos="SELECT pc.codigo,pc.numero from plan_cuentas pc where pc.numero like '5%' and pc.nivel=5";//fila modificada po_plancuentas // pc.codigo->pc.numero
      if($cuenta!=0){
        $sqlEgresos.=" and pc.numero='$cuenta'"; //fila modificada
      }
      //echo $sqlEgresos;
      $stmt = $dbh->prepare($sqlEgresos);
      $stmt->execute();
      $montoEgresoEjecutado=0;
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $codPlanCuenta=$row['numero']; //fila afectada codigo _ numero

          if($acumulado==1){
            $sqlMayor="SELECT sum(p.monto)as monto from po_mayores p where p.fondo in ($agenciaX) and p.cuenta='$codPlanCuenta' and p.anio='$anio' and p.mes<='$mes'";
          }else{
            $sqlMayor="SELECT sum(p.monto)as monto from po_mayores p where p.fondo in ($agenciaX) and p.cuenta='$codPlanCuenta' and p.anio='$anio' and p.mes='$mes'";
          }
          $stmtMayor=$dbh->prepare($sqlMayor);
          $stmtMayor->execute();
          while ($rowMayor = $stmtMayor->fetch(PDO::FETCH_ASSOC)) {
              $montoEjecutado=$rowMayor['monto'];
              $montoEgresoEjecutado=$montoEgresoEjecutado+$montoEjecutado;
          }
      }
   }
   return($montoEgresoEjecutado);
}

//ESTA FUNCION ES IDENTICA A LA DE ejecutadoEgresosMes
function distribucionDNSA($agencia, $anio, $mes, $organismo, $acumulado, $dn_sa){
   //en la variable dn_sa enviamos el organismo del que queremos ver
  if($dn_sa==1){
    $campo="porcentaje_dn";
    $organismoDNSA=501;
  }else{
    $campo="porcentaje_sa";
    $organismoDNSA=502;
  }
  $dbh = new Conexion();
  //$agencia=str_replace('|', ',', $agencia);
  $sqlRegional="SELECT p.codigo from po_fondos p where p.codigo in ($agencia)";
  //echo $sqlRegional;
  $stmtRegional=$dbh->prepare($sqlRegional);
  $stmtRegional->execute();
  $totalMontoEgresoDNSA=0;
  while($rowRegional=$stmtRegional->fetch(PDO::FETCH_ASSOC)){
    $codRegional=$rowRegional['codigo'];
    
    $sqlEgresos="SELECT pc.codigo, pc.nivel from po_plancuentas pc where pc.codigo like '5%' and pc.nivel=5";
    $stmt = $dbh->prepare($sqlEgresos);
    $stmt->execute();
    $montoEgresoEjecutado=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $codPlanCuenta=$row['codigo'];
        $nivelCuenta=$row['nivel'];

        $campoTablaCuenta="";
        if($nivelCuenta==4){
          $campoTablaCuenta="p.cta_n4";
        }else{
          $campoTablaCuenta="p.cuenta";
        }

        if($acumulado==1){
          $sqlMayor="SELECT sum(p.monto)as monto from po_mayores p where p.fondo in ($codRegional) and $campoTablaCuenta='$codPlanCuenta' and p.anio='$anio' and p.mes<='$mes' and p.organismo in ($organismoDNSA)";
        }else{
          $sqlMayor="SELECT sum(p.monto)as monto from po_mayores p where p.fondo in ($codRegional) and $campoTablaCuenta='$codPlanCuenta' and p.anio='$anio' and p.mes='$mes' and p.organismo in ($organismoDNSA)";
        }
        //echo $sqlMayor;
        $stmtMayor=$dbh->prepare($sqlMayor);
        $stmtMayor->execute();
        while ($rowMayor = $stmtMayor->fetch(PDO::FETCH_ASSOC)) {
            $montoEjecutado=$rowMayor['monto'];
            $montoEgresoEjecutado=$montoEgresoEjecutado+$montoEjecutado;
        }
      }
      //echo $montoEgresoEjecutado." ";
    //SACAMOS EL PORCENTAJE DE DISTRIBUCIONC
    $sqlDetalle="SELECT $campo from po_distribucionunidadesareas where cod_fondo in ($codRegional) and cod_organismo='$organismo'";
    //echo $sqlDetalle;
    $porcentaje=0;
    $stmtDetalle = $dbh->prepare($sqlDetalle);
    $stmtDetalle->execute();
    while ($rowDetalle = $stmtDetalle->fetch(PDO::FETCH_ASSOC)) {
      $porcentaje=$rowDetalle[$campo];
    }
    //echo "anio ".$anio." ".$porcentaje."% ".$montoEgresoEjecutado."monto  dnsa".$dn_sa."<br>";
    $montoEgresoOrganismo=$montoEgresoEjecutado*($porcentaje/100);
    $totalMontoEgresoDNSA=$totalMontoEgresoDNSA+$montoEgresoOrganismo;
  }
  return($totalMontoEgresoDNSA);
}


//ESTAS FUNCIONES SON PARA EL PROYECTO SIS
function devolverCodigos($componente, $nivel, $tipo){
  $dbh = new Conexion();
  $codigosComp="0";
  $codigosPartida="'0'";
  $sql="";
  if($nivel==3){
    $sql="SELECT c.codigo, c.partida from componentessis c where c.codigo='$componente'";
  }
  if($nivel==2){
    $sql="SELECT c.codigo, c.partida from componentessis c where c.cod_padre in (select cd.codigo from componentessis cd where cd.codigo='$componente')";
  }
  if($nivel==1){
    $sql="SELECT c1.codigo, c1.partida from componentessis c1 where c1.cod_padre in (select c.codigo from componentessis c where c.cod_padre in (select cd.codigo from componentessis cd where cd.codigo='$componente'))";
  }
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigosComp=$codigosComp.",".$row['codigo'];
      $codigosPartida=$codigosPartida.",'".$row['partida']."'";
  }  

  $codigosComp=str_replace("''","'-1'",$codigosComp);
  $codigosPartida=str_replace("''","'-1'",$codigosPartida);

  if($tipo==1){return($codigosComp);} 
  if($tipo==2){return($codigosPartida);} 
}
function montoSolicitudComponente($solicitud, $componente, $nivel){
  $dbh = new Conexion();
  $codigosX=devolverCodigos($componente,$nivel,1);
  $sql="SELECT sum(sd.monto)as monto from solicitudfondos_detalle sd where sd.codigo='$solicitud' and sd.cod_componente in ($codigosX)";
  //echo $sql;
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $monto=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto=$row['monto'];
  }
  return($monto);
}

function montoPresupuestoComponente($gestion, $anio, $mes, $componente, $nivel){
  $dbh = new Conexion();
  $codigosX="";
  $codigosX=devolverCodigos($componente,$nivel,2);
  //echo $componente." -  ".$codigosX."<br>";
  //$sql="SELECT sum(p.monto)as monto from sis_presupuesto p where p.cod_gestion='$gestion' and p.cod_ano='$anio' and p.cod_mes<='$mes' and p.cod_cuenta in ($codigosX)";
  $sql="SELECT sum(p.monto)as monto from sis_presupuesto p where p.cod_gestion='$gestion' and p.cod_ano='$anio' and p.cod_cuenta in ($codigosX)";
  //SACAMOS EL PRESUPUESTO TOTAL ESTO DEBE REVISARSE
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $monto=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto=$row['monto'];
  }
  return($monto);
}


function montoEjecucionComponente($anio, $mes, $componente, $nivel){
  $dbh = new Conexion();
  $codigosX=devolverCodigos($componente,$nivel,2);
  //echo $componente." ".$codigosX."<br>";
  //$sql="SELECT sum(m.monto)as monto from po_mayores m where m.anio='$anio' and m.mes<='$mes' and m.ml_partida in ($codigosX) and m.fondo=2001";
  $monto=0;
  if($codigosX!=''){
    $sql="SELECT sum(m.monto)as monto from po_mayores m where m.anio='$anio' and m.mes<='$mes' and m.ml_partida in ($codigosX) and m.fondo=2001 and m.cuenta like '5%'";
    //echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $monto=$row['monto'];
    }    
  }
  return($monto);
}

function montoMayores($cuenta, $anio, $mes, $tipo){//tipo es 1 debe, 2 haber
  $dbh = new Conexion();
  if($tipo==1){
    $sql="SELECT sum(s.monto)as monto from po_mayores s, po_plancuentas pc where pc.codigo=s.cuenta and s.fondo=2001 and YEAR(s.fecha)='$anio' and MONTH(s.fecha)<='$mes' and s.cuenta='$cuenta' and s.monto>0";    
  }else{
    $sql="SELECT sum(s.monto)as monto from po_mayores s, po_plancuentas pc where pc.codigo=s.cuenta and s.fondo=2001 and YEAR(s.fecha)='$anio' and MONTH(s.fecha)<='$mes' and s.cuenta='$cuenta' and s.monto<0";
  }
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $monto=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto=$row['monto'];
  }
  return($monto); 
}
function montoMayoresDetalle($cuenta, $codigo, $fecha, $tipo){//tipo es 1 debe, 2 haber
  $dbh = new Conexion();
  if($tipo==1){
    $sql="SELECT sum(s.monto)as monto from po_mayores s, po_plancuentas pc where pc.codigo=s.cuenta and s.fondo=2001 and s.fecha='$fecha' and s.cuenta='$cuenta' and s.indice='$codigo' and s.monto>0";    
  }else{
    $sql="SELECT sum(s.monto)as monto from po_mayores s, po_plancuentas pc where pc.codigo=s.cuenta and s.fondo=2001 and s.fecha='$fecha' and s.cuenta='$cuenta' and s.indice='$codigo' and s.monto<0";
  }
  //echo $sql;
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $monto=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $monto=$row['monto'];
  }
  return($monto); 
}
//FIN FUNCIONES PROYECTO SIS

?>
