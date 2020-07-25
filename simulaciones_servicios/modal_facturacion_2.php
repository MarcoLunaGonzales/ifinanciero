<div class="modal fade" id="modalBuscador_solicitudes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
            <select  name="OficinaBusqueda[]" id="OficinaBusqueda" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple>               
              <?php while ($rowUO = $stmtUO->fetch(PDO::FETCH_BOUND)) { ?>
                <option value="<?=$codigo_uo_b;?>"><?=$nombre_uo_b;?> - <?=$abreviatura_uo_b;?></option>
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
                <option value="<?=$codigo_cli_b;?>"> <?=$nombre_cli_b;?></option>
              <?php }?>
            </select>
            
          </div>              
        </div> 
        <div class="row">
          <label class="col-sm-9 col-form-label text-center">Razón Social</label> 
          <label class="col-sm-3 col-form-label text-center">Nro. Solicitud</label>
        </div> 
        <div class="row">          
          <div class="form-group col-sm-9">
            <input class="form-control input-sm" type="text" name="razon_social_b" id="razon_social_b"  >
          </div>           
          <div class="form-group col-sm-3">
            <input class="form-control input-sm" type="text" name="nro_solicitud_b" id="nro_solicitud_b"  >
          </div>           
        </div> 

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="botonBuscarComprobante" name="botonBuscarComprobante" onclick="botonBuscarSolicitudes_gral()">Buscar</button>
        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal"> Cerrar </button> -->
      </div>
    </div>
  </div>
</div>