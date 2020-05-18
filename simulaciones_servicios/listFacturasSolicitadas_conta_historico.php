<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];
//datos registrado de la simulacion en curso

  $stmt = $dbh->prepare("SELECT sf.*,es.nombre as estado,DATE_FORMAT(sf.fecha_registro,'%d/%m/%Y')as fecha_registro_x,DATE_FORMAT(sf.fecha_solicitudfactura,'%d/%m/%Y')as fecha_solicitudfactura_x FROM solicitudes_facturacion sf join estados_solicitudfacturacion es on sf.cod_estadosolicitudfacturacion=es.codigo where cod_estadosolicitudfacturacion=5 order by codigo desc limit 50");

  $stmt->execute();
  $stmt->bindColumn('codigo', $codigo_facturacion);
  $stmt->bindColumn('cod_simulacion_servicio', $cod_simulacion_servicio);
  $stmt->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
  $stmt->bindColumn('cod_area', $cod_area);
  $stmt->bindColumn('fecha_registro_x', $fecha_registro);
  $stmt->bindColumn('fecha_solicitudfactura_x', $fecha_solicitudfactura);
  $stmt->bindColumn('cod_tipoobjeto', $cod_tipoobjeto);
  $stmt->bindColumn('cod_tipopago', $cod_tipopago);
  $stmt->bindColumn('cod_cliente', $cod_cliente);
  $stmt->bindColumn('cod_personal', $cod_personal);
  $stmt->bindColumn('razon_social', $razon_social);
  $stmt->bindColumn('nit', $nit);
  $stmt->bindColumn('observaciones', $observaciones);
  $stmt->bindColumn('cod_estadosolicitudfacturacion', $codEstado);
  $stmt->bindColumn('estado', $estado);
  $stmt->bindColumn('nro_correlativo', $nro_correlativo);
  $stmt->bindColumn('persona_contacto', $persona_contacto);
  $stmt->bindColumn('codigo_alterno', $codigo_alterno);
  $stmt->bindColumn('tipo_solicitud', $tipo_solicitud);//1 tcp - 2 capacitacion - 3 servicios - 4 manual - 5 venta de normas



  // busquena por Oficina
$stmtUO = $dbh->prepare("SELECT cod_unidadorganizacional,(select u.nombre from unidades_organizacionales u where u.codigo =cod_unidadorganizacional)as nombre, (select u.abreviatura from unidades_organizacionales u where u.codigo =cod_unidadorganizacional)as abreviatura FROM solicitudes_facturacion where cod_estadosolicitudfacturacion=5 GROUP BY nombre");
$stmtUO->execute();
$stmtUO->bindColumn('cod_unidadorganizacional', $codigo_uo);
$stmtUO->bindColumn('nombre', $nombre_uo);
$stmtUO->bindColumn('abreviatura', $abreviatura_uo);


$stmtCliente = $dbh->prepare("
SELECT cod_cliente,(SELECT c.nombre from  clientes c where c.codigo=cod_cliente) as nombre from solicitudes_facturacion where cod_estadosolicitudfacturacion=5 GROUP BY nombre");
$stmtCliente->execute();
$stmtCliente->bindColumn('cod_cliente', $codigo_cli);
$stmtCliente->bindColumn('nombre', $nombre_cli);


  ?>
  <div class="content">
    <div class="container-fluid">
          <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header card-header-warning card-header-icon">
                    <div class="card-icon">
                      <i class="material-icons">polymer</i>
                    </div>
                    <h4 class="card-title"><b>Historial Solicitudes de Facturación </b></h4>                    
                  </div>
                  <div class="row">
                      <div class="col-sm-12">
                          <div class="form-group" align="right">
                              <button type="button" class="btn btn-warning btn-round btn-fab btn-sm" data-toggle="modal" data-target="#modalBuscador">
                                  <i class="material-icons" title="Buscador Avanzado">search</i>
                              </button>                               
                          </div>
                      </div>
                  </div>
                  <div class="card-body">
                    <div id="data_solicitudes_facturacion">
                      <table class="table" id="tablePaginator">
                        <thead>
                          <tr>
                            <th class="text-center"></th>                          
                            <th>Of - Area</th>                            
                            <th>#Sol.</th>
                            <th>Responsable</th>
                            <th>Código<br>Servicio</th>                            
                            <th>Fecha<br>Registro</th>
                            <!-- <th>Fecha<br>a Facturar</th> -->
                            <th style="color:#cc4545;">#Fact</th>                            
                            <th>Importe<br>(BOB)</th>  
                            <th>Persona<br>Contacto</th>  
                            <th>Concepto</th>
                            <th width="5%">Estado</th>
                            <th class="text-right">Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                          $index=1;
                          $codigo_fact_x=0;
                          $cont= array();
                          while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          switch ($codEstado) {
                            case 1:
                              $btnEstado="btn-default";
                            break;
                            case 2:
                              $btnEstado="btn-danger";
                            break;
                            case 3:
                              $btnEstado="btn-success";
                            break;
                            case 4:
                              $btnEstado="btn-warning";
                            break;
                            case 5:
                              $btnEstado="btn-warning";
                            break;
                            case 6:
                              $btnEstado="btn-default";
                            break;
                          }
                            //verificamos si ya tiene factura generada y esta activa                           
                            $stmtFact = $dbh->prepare("SELECT codigo,nro_factura from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion and cod_estadofactura=1");
                            $stmtFact->execute();
                            $resultSimu = $stmtFact->fetch();
                            $codigo_fact_x = $resultSimu['codigo'];
                            $nro_fact_x = $resultSimu['nro_factura'];
                            if ($nro_fact_x==null)$nro_fact_x="-";
                            //sacamos nombre de los detalles
                            $stmtDetalleSol = $dbh->prepare("SELECT cantidad,precio,descripcion_alterna from solicitudes_facturaciondetalle where cod_solicitudfacturacion=$codigo_facturacion");
                            $stmtDetalleSol->execute();
                            $stmtDetalleSol->bindColumn('cantidad', $cantidad);  
                            $stmtDetalleSol->bindColumn('precio', $precio);     
                            $stmtDetalleSol->bindColumn('descripcion_alterna', $descripcion_alterna);                              
                            $concepto_contabilizacion=$codigo_alterno." - ";
                            while ($row_det = $stmtDetalleSol->fetch()){
                              $precio_natural=$precio/$cantidad;
                              $concepto_contabilizacion.=$descripcion_alterna." / F ".$nro_fact_x." / ".$razon_social."<br>\n";
                              $concepto_contabilizacion.="Cantidad: ".$cantidad." * ".formatNumberDec($precio_natural)." = ".formatNumberDec($precio)."<br>\n";
                            }
                            $concepto_contabilizacion = (substr($concepto_contabilizacion, 0, 100))."..."; //limite de string
                            
                            $cod_area_simulacion=$cod_area;
                            $nombre_simulacion='OTROS';
                            if($tipo_solicitud==1){// la solicitud pertence tcp-tcs
                              //obtenemos datos de la simulacion TCP
                              $sql="SELECT sc.nombre,ps.cod_area,ps.cod_unidadorganizacional
                              from simulaciones_servicios sc,plantillas_servicios ps
                              where sc.cod_plantillaservicio=ps.codigo and sc.cod_estadoreferencial=1 and sc.codigo=$cod_simulacion_servicio";                            
                              $stmtSimu = $dbh->prepare($sql);
                              $stmtSimu->execute();
                              $resultSimu = $stmtSimu->fetch();
                              $nombre_simulacion = $resultSimu['nombre'];
                              $cod_area_simulacion = $resultSimu['cod_area'];
                            }elseif($tipo_solicitud==2){//  pertence capacitacion
                              $sqlCostos="SELECT sc.nombre,sc.cod_responsable,ps.cod_area,ps.cod_unidadorganizacional
                              from simulaciones_costos sc,plantillas_servicios ps
                              where sc.cod_plantillacosto=ps.codigo and sc.cod_estadoreferencial=1 and sc.codigo=$cod_simulacion_servicio order by sc.codigo";
                              $stmtSimuCostos = $dbh->prepare($sqlCostos);
                              $stmtSimuCostos->execute();
                              $resultSimu = $stmtSimuCostos->fetch();
                              $nombre_simulacion = $resultSimu['nombre'];
                              $cod_area_simulacion = $resultSimu['cod_area'];
                            }elseif($tipo_solicitud==3){// pertence a propuestas y servicios
                              $sqlCostos="SELECT Descripcion,IdArea,IdOficina from servicios s where s.IdServicio=$cod_simulacion_servicio";
                              $stmtSimuCostos = $dbh->prepare($sqlCostos);
                              $stmtSimuCostos->execute();
                              $resultSimu = $stmtSimuCostos->fetch();
                              $nombre_simulacion = $resultSimu['Descripcion'];
                              $cod_area_simulacion = $resultSimu['IdArea'];
                            }

                            $name_area_simulacion=trim(abrevArea($cod_area_simulacion),'-');

                            // --------
                            $responsable=namePersonal($cod_personal);//nombre del personal
                            $nombre_contacto=nameContacto($persona_contacto);//nombre del personal
                            $nombre_area=trim(abrevArea($cod_area),'-');//nombre del area
                            $nombre_uo=trim(abrevUnidad($cod_unidadorganizacional),' - ');//nombre de la oficina

                            //los registros de la factura
                            $dbh1 = new Conexion();
                            $sqlA="SELECT sf.*,(select t.Descripcion from cla_servicios t where t.IdClaServicio=sf.cod_claservicio) as nombre_serv from solicitudes_facturaciondetalle sf where sf.cod_solicitudfacturacion=$codigo_facturacion";
                            $stmt2 = $dbh1->prepare($sqlA);                                   
                            $stmt2->execute(); 
                            $nc=0;
                            $sumaTotalMonto=0;
                            $sumaTotalDescuento_por=0;
                            $sumaTotalDescuento_bob=0;

                            while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                              // $dato = new stdClass();//obejto
                              $codFila=(int)$row2['codigo'];
                              $cod_claservicioX=trim($row2['nombre_serv']);
                              $cantidadX=trim($row2['cantidad']);
                              $precioX=trim($row2['precio'])+trim($row2['descuento_bob']);
                              $descuento_porX=trim($row2['descuento_por']);
                              $descuento_bobX=trim($row2['descuento_bob']);                             
                              $descripcion_alternaX=trim($row2['descripcion_alterna']);
                              // $dato->codigo=($nc+1);
                              // $dato->cod_facturacion=$codFila;
                              // $dato->serviciox=$cod_claservicioX;
                              // $dato->cantidadX=$cantidadX;
                              // $dato->precioX=$precioX;
                              // $dato->descuento_porX=$descuento_porX;
                              // $dato->descuento_bobX=$descuento_bobX;
                              // $dato->descripcion_alternaX=$descripcion_alternaX;
                              // $datos[$index-1][$nc]=$dato;                           
                              $nc++;
                              $sumaTotalMonto+=$precioX;
                              $sumaTotalDescuento_por+=$descuento_porX;
                              $sumaTotalDescuento_bob+=$descuento_bobX;
                            }
                            $sumaTotalImporte=$sumaTotalMonto-$sumaTotalDescuento_bob;
                            // $cont[$index-1]=$nc;
                            // $stringCabecera=$nombre_uo."##".$nombre_area."##".$nombre_simulacion."##".$name_area_simulacion."##".$fecha_registro."##".$fecha_solicitudfactura."##".$nit."##".$razon_social;

                            ?>
                          <tr>
                            <td align="center"></td>
                            <td><?=$nombre_uo;?> - <?=$nombre_area;?></td>
                            
                            <td class="text-right"><?=$nro_correlativo;?></td>
                            <td><?=$responsable;?></td>
                            <td><?=$codigo_alterno?></td>
                            <td><?=$fecha_registro;?></td>
                            <!-- <td><?=$fecha_solicitudfactura;?></td>      -->                       
                            <td style="color:#cc4545;"><?=$nro_fact_x;?></td>                             
                            <td class="text-right"><?=formatNumberDec($sumaTotalImporte) ;?></td>
                            <td class="text-left"><?=$nombre_contacto;?></td>
                            <td width="35%"><small><?=$concepto_contabilizacion?></small></td>
                            <td><button class="btn <?=$btnEstado?> btn-sm btn-link"><?=$estado;?></button></td>
                            <!-- <td><?=$nit;?></td> -->

                            <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1){ //
                                  if($codigo_fact_x>0){//print facturas
                                    ?>
                                    <a class="btn btn-success" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_facturacion;?>&tipo=2' target="_blank"><i class="material-icons" title="Imprimir Factura">print</i></a>                                    
                                    <?php 
                                  }                           
                                }
                              ?>
                            </td>
                          </tr>
                          <?php
                              $index++;
                            }
                          ?>
                        </tbody>
                      </table>
                    </div>
                      
                  </div>                    
                  <div class="card-footer fixed-bottom col-sm-9">
                    <a href='<?=$urlListSolicitudContabilidad;?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
                  </div>    
                </div>                   
                </div>     
              </div>
          </div>  
    </div>
  </div>

  <!-- Modal busqueda de comprobantes-->
<div class="modal fade" id="modalBuscador" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Buscador de Solicitudes de Facturación</h4>
      </div>
      <div class="modal-body ">
        <div class="row">
            <label class="col-sm-3 col-form-label text-center">Oficina</label> 
            <label class="col-sm-6 col-form-label text-center">Fechas</label>                  
            <label class="col-sm-3 col-form-label text-center">Cliente</label>                                
        </div> 
        <div class="row">
          <div class="form-group col-sm-3">
    <!--         <select class="selectpicker form-control" title="Seleccione una opcion" name="areas[]" id="areas" data-style="select-with-transition" data-size="5" data-actions-box="true" multiple required> -->

            <select  name="OficinaBusqueda[]" id="OficinaBusqueda" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple>               
              <?php while ($rowUO = $stmtUO->fetch(PDO::FETCH_BOUND)) { ?>
                <option value="<?=$codigo_uo;?>"> <?=$nombre_uo;?></option>
              <?php }?>
            </select>
          </div>
          <div class="form-group col-sm-3">
            <input class="form-control input-sm" type="date" name="fechaBusquedaInicio" id="fechaBusquedaInicio">
          </div>
          <div class="form-group col-sm-3">
            <input class="form-control input-sm" type="date" name="fechaBusquedaFin" id="fechaBusquedaFin">
          </div>
          <div class="form-group col-sm-3">            
            <select name="cliente[]" id="cliente" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple>               
              <?php while ($rowTC = $stmtCliente->fetch(PDO::FETCH_BOUND)) { ?>
                <option value="<?=$codigo_cli;?>"> <?=$nombre_cli;?></option>
              <?php }?>
            </select>
            
          </div>              
        </div> 
        <!-- <div class="row">
          <label class="col-sm-3 col-form-label text-center">Razón Social</label> 
          <div class="form-group col-sm-8">
            <input class="form-control input-sm" type="text" name="glosaBusqueda" id="glosaBusqueda"  >
          </div>           
        </div>  -->

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="botonBuscarComprobante" name="botonBuscarComprobante" onclick="botonBuscarSolicitudes_conta()">Buscar</button>
        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal"> Cerrar </button> -->
      </div>
    </div>
  </div>
</div>

<!-- small modal -->

<?php
//require_once 'simulaciones_servicios/modal_facturacion.php';
?>
  