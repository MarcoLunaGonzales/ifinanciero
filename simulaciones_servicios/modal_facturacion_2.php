<!-- modal envio de facturas -->
<div class="modal fade" id="modalEnviarCorreo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="background-color:#e2e6e7">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Enviar Correo</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_facturacion_sf" id="codigo_facturacion_sf" value="0">
        <input type="hidden" name="cod_solicitudfacturacion_sf" id="cod_solicitudfacturacion_sf" value="0">
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#000000"><small>Nro. Factura</small></label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input type="text" class="form-control" name="nro_factura_sf" id="nro_factura_sf" value="0" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
          <label class="col-sm-1 col-form-label" style="color:#000000"><small>Razón<br>Social</small></label>
          <div class="col-sm-8">
            <div class="form-group" >              
              <input type="text" class="form-control" name="razon_social_sf" id="razon_social_sf" value="0" readonly="true" style="background-color:#e2d2e0"> 
            </div>
          </div>
        </div>        
        <!-- <input class="form-control" type="email" name="correo_destino" id="correo_destino" required="true" value="" /> -->
        <div class="row">
          <div class="col-sm-12" >
            <h6> Correo Destino : </h6>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12" style="background-color:#FFFFFF">
            <div class="form-group" >
              <input type="text" name="correo_destino_sf" id="correo_destino_sf" class="form-control tagsinput" data-role="tagsinput" data-color="info" required="true" >  
            </div>
          </div>
        </div>
        <?php 
         $sqlInstancia="SELECT codigo,descripcion from instancias_envios_correos where codigo=1";
         $stmtInstancia = $dbh->prepare($sqlInstancia);
         $stmtInstancia->execute();                           
         while ($row = $stmtInstancia->fetch(PDO::FETCH_ASSOC)) {
          $datoInstancia=obtenerCorreosInstanciaEnvio($row['codigo']);
          $correos=implode(",",$datoInstancia[0]);
          $nombres=implode(",",$datoInstancia[1]);
            ?>
         <div class="row">
          <div class="col-sm-12" >
            <h6> <?=$row['descripcion']?> (CC): </h6>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12" style="background-color:#FFFFFF">
            <div class="form-group" >
              <input type="text" readonly value="<?=$nombres?>" name="nombre_correo" id="nombre_correo" class="form-control">  
            </div>
          </div>
        </div> 
        <div class="row d-none">
          <div class="col-sm-12" style="background-color:#FFFFFF">
            <div class="form-group" >
              <input type="text" value="<?=$correos?>" name="correo_copia" id="correo_copia" class="form-control tagsinput" data-role="tagsinput" data-color="info" required="true" >  
            </div>
          </div>
        </div> 
            <?php
         }   
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="EnviarCorreo" name="EnviarCorreo" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>