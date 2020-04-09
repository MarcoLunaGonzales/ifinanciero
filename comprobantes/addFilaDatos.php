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
          <select class="selectpicker form-control form-control-sm" name="unidad<?=$idFila;?>" id="unidad<?=$idFila;?>" data-style="btn btn-primary" >
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
                    <!--<option value="" disabled selected>Area</option>-->
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
    <input type="hidden" name="numero_cuenta<?=$idFila;?>" value="<?=$n_cuenta?>" id="numero_cuenta<?=$idFila;?>">
        <input type="hidden" name="cuenta<?=$idFila;?>" value="<?=$cuenta?>" id="cuenta<?=$idFila;?>">
        <input type="hidden" name="cuenta_auxiliar<?=$idFila;?>" value="" id="cuenta_auxiliar<?=$idFila;?>">
        <div class="row">
          <div class="col-sm-8">
          <div class="form-group" id="divCuentaDetalle<?=$idFila;?>">
           <span class="text-info font-weight-bold">[<?=$n_cuenta?>]-<?=$nom_cuenta?> </span><br><span class="text-info font-weight-bold small"><?=$nom_cuenta_auxiliar?></span>     
            <p class="text-muted"><?=$porcentajeX?> <span>%</span> de <?=$importe?></p>   
          </div>
         </div>
         <div class="col-sm-4">
            <div class="btn-group">
             <a title="Mayores" href="#" id="mayor<?=$idFila?>" onclick="mayorReporteComprobante(<?=$idFila?>)" class="btn btn-sm btn-info btn-fab"><span class="material-icons">list</span></a>      
             <a title="Cambiar cuenta" href="#" id="cambiar_cuenta<?=$idFila?>" onclick="editarCuentaComprobante(<?=$idFila?>)" class="btn btn-sm btn-warning btn-fab"><span class="material-icons text-dark">edit</span></a>   
             <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucion<?=$idFila?>" onclick="nuevaDistribucionPonerFila(<?=$idFila;?>);" class="btn btn-sm btn-default btn-fab"><span class="material-icons">scatter_plot</span></a>    
               <input type="hidden" id="tipo_estadocuentas<?=$idFila?>" value="-100"><!-- -100=CUENTA PARA MATAR-->
               <input type="hidden" id="tipo_proveedorcliente<?=$idFila?>" value="-100">
               <input type="hidden" id="proveedorcliente<?=$idFila?>" value="-100">

               <a title="Estado de Cuentas" id="estados_cuentas<?=$idFila?>" href="#" onclick="verEstadosCuentas(<?=$idFila;?>,0);" class="btn btn-sm btn-danger btn-fab d-none"><span class="material-icons text-dark">ballot</span><span id="nestado<?=$idFila?>" class="bg-warning"></span></a>    
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
        <textarea rows="1" class="form-control" name="glosa_detalle<?=$idFila;?>" id="glosa_detalle<?=$idFila;?>"><?=$glosaX?></textarea>
      </div>
    </div>
    <div class="col-sm-1">
      <div class="btn-group">
          <a href="#" title="Facturas" id="boton_fac<?=$idFila;?>" onclick="listFac(<?=$idFila;?>);" class="btn btn-info btn-sm btn-fab">
               <i class="material-icons">featured_play_list</i><span id="nfac<?=$idFila;?>" class="count bg-warning">0</span>
          </a>
           <a rel="tooltip" href="#" class="btn btn-danger btn-sm btn-fab" id="boton_remove<?=$idFila;?>" onclick="minusCuentaContable('<?=$idFila;?>');">
              <i class="material-icons">remove_circle</i>
          </a>
        </div>  
    </div>
   </div>
 </div>
 <div class="h-divider"></div>
</div>