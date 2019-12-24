<!-- Modal PreRequisitos-->
<div class="modal fade" id="modalPrerequisitos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content card">                    
          <input type="hidden" name="cod_mesP" id="cod_mesP" value="0">  
          <div class="card-header card-header-dark  card-header-icon">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>        
            <div class="card-icon">
              <i class="material-icons">chrome_reader_mode</i>
            </div>
            <h4 class="card-title">ACTIVIDADES DE ESTE MES</h4>
          </div>
          <div class="card-body">                                                
            <table class="table table-condensed">                    
              <tbody id="card_prerequisitos">
                
              </tbody>
            </table>
          </div>       
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="RecepcionarAF" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>