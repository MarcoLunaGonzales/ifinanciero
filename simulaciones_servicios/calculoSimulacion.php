<?php
                 $alumnosExternoX=1;
                 $ingresoLocal=$precioLocalX*$alumnosX;                 
                 $ingresoExterno=1;
                 
                 //porcentajes costo servicio
                 $costoLocal=($totalVariable[2]*$alumnosX)/($ingresoLocal);
                 $pCostoLocal=$costoLocal*100;
                 $costoExterno=($totalVariable[3]*$alumnosExternoX)/($ingresoExterno);
                 $pCostoExterno=$costoExterno*100;

                 //gastos operativos
                 //TOTAL DE COSTO FIJO
                 $costoOperLocal=$totalFijo[2]*0.87;
                 $pCostoOperLocal=($costoOperLocal/$ingresoLocal)*100;

                 $costoOperExterno=$totalFijo[3]*0.87;
                 $pCostoOperExterno=($costoOperExterno/$ingresoExterno)*100;

                 //utilidad antes de impuestos
                 $utilidadLocal=$ingresoLocal-($totalVariable[2]*$alumnosX)-$costoOperLocal;
                 $utilidadExterno=$ingresoExterno-($totalVariable[3]*$alumnosExternoX)-$costoOperExterno;

                 // impuesto iva
                 //$costoTotalLocal=$totalFijo[2]+($totalVariable[2]*$alumnosX)+$costoVariablePersonal;
$costoFijoPrincipalPeriodo=0;
for ($eee=1; $eee <=$anioGeneral; $eee++) { 
                 //costos fijos porcentaje configuracion ***************************************************************************************
               /*
               $sumPrecioRegistrado=$precioRegistrado*(($porcentajeFijoX/100)*($eee-1));                  
                 $porcentPreciosPeriodo=($precioLocalXPeriodo*100)/($precioRegistrado+$sumPrecioRegistrado);
                 $costoFijoFinal=$totalFijo[0]*($porcentPreciosPeriodo/100);
                $costoFijoPrincipalPeriodo+=$costoFijoFinal;*/
$precioLocalXPeriodo=obtenerPrecioServiciosSimulacionPeriodo($codigo,$eee);

                 //costos fijos porcentaje configuracion ***************************************************************************************                                
                $precioRegistradoAux=$precioRegistrado;
                if($eee>1){
                    for ($anioAumento=2; $anioAumento <= $eee; $anioAumento++) { 
                      $sumaPrecioRegistrado=$precioRegistradoAux*($porcentajeFijoX/100);
                      $precioRegistradoAux=$precioRegistradoAux+$sumaPrecioRegistrado;
                    }
                  }
                 $porcentPreciosPeriodo=(float)number_format(($precioLocalXPeriodo*100)/($precioRegistradoAux),2,'.','');

                 $costoFijoRegistrado=$totalFijo[0];
                if($eee>1){
                    for ($anioAumento=2; $anioAumento <= $eee; $anioAumento++) { 
                      $sumaCostoFijoRegistrado=$costoFijoRegistrado*($porcentajeFijoX/100);
                      $costoFijoRegistrado=$costoFijoRegistrado+$sumaCostoFijoRegistrado;
                    }
                  }
                 $costoFijoFinal=$costoFijoRegistrado*($porcentPreciosPeriodo/100);
                 $costoFijoPrincipalPeriodo+=$costoFijoFinal;  
                //fin datos para costo fijo             ***************************************************************************************
}


                 $costoTotalLocal=$costoFijoPrincipalPeriodo+($totalVariable[2]*$alumnosX)+ $costoVariablePersonal;
                 $costoTotalExterno=$totalFijo[3]+($totalVariable[3]*$alumnosExternoX);

                 $impuestoIvaLocal=$costoTotalLocal*($iva/100);
                 $impuestoIvaExterno=$costoTotalExterno*($iva/100);
                      //porcentaje iva
                 $pImpLocal=($impuestoIvaLocal/$ingresoLocal)*100;
                 $pImpExterno=($impuestoIvaExterno/$ingresoExterno)*100;


                 //impuesto transacciones
                 $impuestoITLocal=($ingresoLocal/0.87)*($it/100);
                 $impuestoITExterno=($ingresoExterno/0.87)*($it/100);
                 $pImpItLocal=($impuestoITLocal/$ingresoLocal)*100;
                 $pImpItExterno=($impuestoITExterno/$ingresoExterno)*100;
                  
                 //UTILIDAD NETA 
                 $utilidadNetaLocal=$utilidadLocal-$impuestoIvaLocal-$impuestoITLocal;
                 $utilidadNetaExterno=$utilidadExterno-$impuestoIvaExterno-$impuestoITExterno;
                 $pUtilidadLocal=($utilidadNetaLocal/$ingresoLocal)*100;
                 $pUtilidadExterno=($utilidadNetaExterno/$ingresoExterno)*100;