<?php                
                 $ingresoExterno=$precioExternoX*$alumnosExternoX;
                 
                 //porcentajes costo servicio
                 $costoExterno=($totalVariable[3]*$alumnosExternoX)/($ingresoExterno);
                 $pCostoExterno=$costoExterno*100;

                 //gastos operativos
                 //TOTAL DE COSTO FIJO

                 $costoOperExterno=$totalFijo[3]*0.87;
                 $pCostoOperExterno=($costoOperExterno/$ingresoExterno)*100;

                 //utilidad antes de impuestos
                 $utilidadExterno=$ingresoExterno-($totalVariable[3]*$alumnosExternoX)-$costoOperExterno;


                 // impuesto iva
                 $costoTotalExterno=$totalFijo[3]+($totalVariable[3]*$alumnosExternoX);

                 $impuestoIvaExterno=$costoTotalExterno*($iva/100);
                      //porcentaje iva
                 $pImpExterno=($impuestoIvaExterno/$ingresoExterno)*100;


                 //impuesto transacciones
                 $impuestoITExterno=($ingresoExterno/0.87)*($it/100);
                 $pImpItExterno=($impuestoITExterno/$ingresoExterno)*100;
                  
                 //UTILIDAD NETA 
                 $utilidadNetaExterno=$utilidadExterno-$impuestoIvaExterno-$impuestoITExterno;
                 $pUtilidadExterno=($utilidadNetaExterno/$ingresoExterno)*100;