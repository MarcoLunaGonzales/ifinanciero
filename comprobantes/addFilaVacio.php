<script>
      numFilas++;
      cantidadItems++;
      filaActiva=numFilas;
      //aumentar un itemfactura
      var nfac=[];
      itemFacturas.push(nfac);
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

   <div class="col-sm-3">
    <input type="hidden" name="numero_cuenta<?=$idFila;?>" value="" id="numero_cuenta<?=$idFila;?>">
        <input type="hidden" name="cuenta<?=$idFila;?>" value="" id="cuenta<?=$idFila;?>">
        <input type="hidden" name="cuenta_auxiliar<?=$idFila;?>" value="" id="cuenta_auxiliar<?=$idFila;?>">
          <div class="row"> 
          <div class="col-sm-9">
            <div class="form-group" id="divCuentaDetalle<?=$idFila;?>">
          
                  </div>
          </div>
          <div class="col-sm-3">
            <div class="btn-group">
             <a title="Cambiar cuenta" href="#" id="cambiar_cuenta<?=$idFila?>" onclick="editarCuentaComprobante(<?=$idFila?>)" class="btn btn-sm btn-warning btn-fab"><span class="material-icons text-dark">edit</span></a>   
             <!--<a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucion<?=$idFila?>" onclick="nuevaDistribucionPonerFila(<?=$idFila;?>);" class="btn btn-sm btn-default btn-fab"><span class="material-icons">scatter_plot</span></a>-->    
              </div>  
          </div>
        </div>
    </div>
    <div class="col-sm-2">
            <div class="form-group">
              <label class="bmd-label-floating">Debe</label>      
              <input class="form-control small" type="number" placeholder="0" value="<?=$debe?>" readonly name="debe<?=$idFila;?>" id="debe<?=$idFila;?>" onChange="calcularTotalesComprobante(this.id,event);" OnKeyUp="calcularTotalesComprobante(this.id,event);" step="0.01"> 
      </div>
    </div>
    <div class="col-sm-2">
            <div class="form-group">
              <label class="bmd-label-floating">Haber</label>     
              <input class="form-control small" type="number" placeholder="0" value="<?=$haber?>" readonly name="haber<?=$idFila;?>" id="haber<?=$idFila;?>" onChange="calcularTotalesComprobante(this.id,event);" OnKeyUp="calcularTotalesComprobante(this.id,event);" step="0.01">   
      </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
              <label class="bmd-label-floating">GlosaDetalle</label>
        <textarea rows="1" class="form-control" name="glosa_detalle<?=$idFila;?>" id="glosa_detalle<?=$idFila;?>"></textarea>
      </div>
    </div>
    <div class="col-sm-1">
      <div class="btn-group">
           <a href="#" id="boton_fac<?=$idFila;?>" onclick="listFac(<?=$idFila;?>);" class="btn btn-info btn-sm btn-fab">
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