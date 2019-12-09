<?php
                 $ingresoLocal=$precioLocalX*$alumnosX;                 
                 
                 //porcentajes costo servicio
                 $costoLocal=($totalVariable[2]*$alumnosX)/($ingresoLocal);
                 $pCostoLocal=$costoLocal*100;

                 //gastos operativos
                 //TOTAL DE COSTO FIJO
                 $costoOperLocal=$totalFijo[2]*0.87;
                 $pCostoOperLocal=($costoOperLocal/$ingresoLocal)*100;

                 //utilidad antes de impuestos
                 $utilidadLocal=$ingresoLocal-($totalVariable[2]*$alumnosX)-$costoOperLocal;

                 // impuesto iva
                 $costoTotalLocal=$totalFijo[2]+($totalVariable[2]*$alumnosX);

                 $impuestoIvaLocal=$costoTotalLocal*($iva/100);
                      //porcentaje iva
                 $pImpLocal=($impuestoIvaLocal/$ingresoLocal)*100;


                 //impuesto transacciones
                 $impuestoITLocal=($ingresoLocal/0.87)*($it/100);
                 $pImpItLocal=($impuestoITLocal/$ingresoLocal)*100;
                  
                 //UTILIDAD NETA 
                 $utilidadNetaLocal=$utilidadLocal-$impuestoIvaLocal-$impuestoITLocal;
                 $pUtilidadLocal=($utilidadNetaLocal/$ingresoLocal)*100;