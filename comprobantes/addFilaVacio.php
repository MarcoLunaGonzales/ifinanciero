<script>
      numFilas++;
      cantidadItems++;
      filaActiva=numFilas;
      //aumentar un itemfactura
      var nfac=[];
      itemFacturas.push(nfac);
      itemEstadosCuentas.push(nfac);
      document.getElementById("cantidad_filas").value=numFilas;
      </script> 
       
<div id="div<?=$idFila?>">
 <div class="col-md-12">
  <div class="row">
    <div class="col-sm-1">
          <div class="form-group">
            <span id="numero_fila<?=$idFila?>" style="position:absolute;left:-15px; font-size:16px;font-weight:600; color:#386D93;"><?=$idFila?></span>
          <select class="selectpicker form-control form-control-sm" name="unidad<?=$idFila;?>" id="unidad<?=$idFila;?>" data-style="btn btn-primary" onChange="relacionSolicitudesSIS(<?=$idFila;?>)">
                     <option value="" disabled selected>Unidad</option>
               <?php
                                   $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 2");
                                   $stmt->execute();
                                   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $codigoX=$row['codigo'];
                                    $nombreX=$row['nombre'];
                                    $abrevX=$row['abreviatura'];
                                    //if($codigoX==$unidadDet){
                                    ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
                                    //}
                                    }
                                    ?>
      </select>
      </div>
    </div>
    <div class="col-sm-1">
          <div class="form-group">
          <select class="selectpicker form-control form-control-sm" name="area<?=$idFila;?>" id="area<?=$idFila;?>" data-style="btn btn-primary">
                    <option value="" disabled selected>Area</option>
          <?php
                                  
                                  $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 2");
                                $stmt->execute();
                                $cont=0;
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  $abrevX=$row['abreviatura'];
                                  ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
                                } 
                                ?>
      </select>
    </div>
  </div>

   <div class="col-sm-4">
    <input type="hidden" name="numero_cuenta<?=$idFila;?>" value="" id="numero_cuenta<?=$idFila;?>">
        <input type="hidden" name="cuenta<?=$idFila;?>" value="" id="cuenta<?=$idFila;?>">
        <input type="hidden" name="cuenta_auxiliar<?=$idFila;?>" value="" id="cuenta_auxiliar<?=$idFila;?>">
          <div class="row"> 
          <div class="col-sm-8">
            <div class="form-group" id="divCuentaDetalle<?=$idFila;?>">
                  </div>
          </div>
          <div class="col-sm-4">
            <div class="btn-group">
             <div class="btn-group dropdown">
                      <button type="button" class="btn btn-sm btn-info btn-fab dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="MAYORES">
                      <i class="material-icons">list</i>
                        </button>
                        <div class="dropdown-menu">
                        <a title="Mayores" href="#" id="mayor<?=$idFila?>" onclick="mayorReporteComprobante(<?=$idFila?>)" class="dropdown-item"><span class="material-icons text-info">list</span> Ver Reporte Mayor</a>         
                        <a title="Cerrar Comprobante" id="cerrar_detalles<?=$idFila?>" href="#" onclick="verMayoresCierre(<?=$idFila;?>);" class="dropdown-item"><span class="material-icons text-danger">ballot</span> Cerrar Comprobantes</a>       
                        </div>
                    </div>

             <a title="Cambiar cuenta" href="#" id="cambiar_cuenta<?=$idFila?>" onclick="editarCuentaComprobante(<?=$idFila?>)" class="btn btn-sm btn-warning btn-fab"><span class="material-icons text-dark">edit</span></a>   
              <div class="btn-group dropdown">
                <button type="button" class="btn btn-sm btn-success btn-fab dropdown-toggle material-icons text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Distribucion de Gastos">
                  <i class="material-icons">call_split</i>
                </button>
                <div class="dropdown-menu">   
                  <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucionX<?=$idFila?>" onclick="nuevaDistribucionPonerFila(<?=$idFila;?>,1);" class="dropdown-item">
                    <i class="material-icons">bubble_chart</i> x Oficina
                  </a>
                  <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucionY<?=$idFila?>" onclick="nuevaDistribucionPonerFila(<?=$idFila;?>,2);" class="dropdown-item">
                    <i class="material-icons">bubble_chart</i> x Área
                  </a>
                </div>
              </div>    


               <input type="hidden" id="tipo_estadocuentas<?=$idFila?>" value="-100"><!-- -100=CUENTA PARA MATAR-->
               <input type="hidden" id="tipo_proveedorcliente<?=$idFila?>" value="-100">
               <input type="hidden" id="proveedorcliente<?=$idFila?>" value="-100">
               <input type="hidden" id="tipo_estadocuentas_casoespecial<?=$idFila?>">

               <a title="Estado de Cuentas" id="estados_cuentas<?=$idFila?>" href="#" onclick="verEstadosCuentas(<?=$idFila;?>,0);" class="btn btn-sm btn-danger btn-fab d-none"><span class="material-icons text-dark">ballot</span><span id="nestado<?=$idFila?>" class="bg-warning"></span></a>    
               <!--LIBRETAS BANCARIAS DETALLE-->
               <a title="Libretas Bancarias" id="libretas_bancarias<?=$idFila?>" href="#" onclick="verLibretasBancarias(<?=$idFila;?>);" class="btn btn-sm btn-primary btn-fab d-none"><span class="material-icons text-dark">ballot</span><span id="nestadolib<?=$idFila?>" class="bg-warning"></span></a>       
               <input type="hidden" id="cod_detallelibreta<?=$idFila?>" name="cod_detallelibreta<?=$idFila?>" value="0">
               <input type="hidden" id="descripcion_detallelibreta<?=$idFila?>" value="">
               <input type="hidden" id="tipo_libretabancaria<?=$idFila?>" value="">
               <!--SOLICITUD DE RECURSOS SIS-->
               <input type="hidden" id="cod_detallesolicitudsis<?=$idFila?>" name="cod_detallesolicitudsis<?=$idFila?>" value="0">
               <input type="hidden" id="cod_actividadproyecto<?=$idFila?>" name="cod_actividadproyecto<?=$idFila?>" value="0">
               <input type="hidden" id="cod_accnum<?=$idFila?>" name="cod_accnum<?=$idFila?>" value="0">
               <!---->
              </div>  
          </div>
        </div>
    </div>
    <div class="col-sm-1">
            <div class="form-group">
              <!--<label class="bmd-label-floating">Debe</label>      -->
              <input class="form-control small" type="number" placeholder="0" value="<?=$debe?>" name="debe<?=$idFila;?>" id="debe<?=$idFila;?>" onChange="calcularTotalesComprobante(this.id,event);" OnKeyUp="calcularTotalesComprobante(this.id,event);" step="0.01"> 
      </div>
    </div>
    <div class="col-sm-1">
            <div class="form-group">
              <!--<label class="bmd-label-floating">Haber</label>     -->
              <input class="form-control small" type="number" placeholder="0" value="<?=$haber?>" name="haber<?=$idFila;?>" id="haber<?=$idFila;?>" onChange="calcularTotalesComprobante(this.id,event);" OnKeyUp="calcularTotalesComprobante(this.id,event);" step="0.01">   
      </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
              <!--<label class="bmd-label-floating">GlosaDetalle</label>-->
        <textarea rows="1" class="form-control" name="glosa_detalle<?=$idFila;?>" id="glosa_detalle<?=$idFila;?>"></textarea>
      </div>
    </div>
    <div class="col-sm-1">
      <div class="btn-group">
           <a href="#" id="boton_fac<?=$idFila;?>" onclick="listFac(<?=$idFila;?>);" class="facturas-boton btn btn-info btn-sm btn-fab btn-default text-dark d-none">
               <i class="material-icons">featured_play_list</i><span id="nfac<?=$idFila;?>" class="count bg-warning">0</span>
          </a>
          <a title="Actividad Proyecto SIS" id="boton_actividad_proyecto<?=$idFila?>" href="#" onclick="verActividadesProyectosSis(<?=$idFila;?>);" class="btn btn-sm btn-orange btn-fab d-none"><span class="material-icons">assignment</span><span id="nestadoactproy<?=$idFila?>" class="bg-warning"></span></a>
          <a title="Solicitudes de Recursos SIS" id="boton_solicitud_recurso<?=$idFila?>" href="#" onclick="verSolicitudesDeRecursosSis(<?=$idFila;?>);" class="btn btn-sm btn-default btn-fab d-none"><span class="material-icons text-dark">view_sidebar</span><span id="nestadosol<?=$idFila?>" class="bg-warning"></span></a>
          <a title="Agregar Fila" id="boton_agregar_fila<?=$idFila?>" href="#" onclick="agregarFilaComprobante(<?=$idFila;?>);return false;" class="btn btn-sm btn-primary btn-fab"><span class="material-icons">add</span></a>              
           <a rel="tooltip" href="#" class="btn btn-danger btn-sm btn-fab" id="boton_remove<?=$idFila;?>" onclick="quitarFilaComprobante('<?=$idFila;?>');return false;">
              <i class="material-icons">disabled_by_default</i>
          </a>
        </div>  
    </div>
   </div>
 </div>
 <div class="h-divider"></div>
</div>