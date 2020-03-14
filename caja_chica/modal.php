<!-- small modal -->
<div class="modal fade modal-primary" id="modalCuentas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">settings_applications</i>
                  </div>
                  <h4 class="card-title">Cuentas Registradas</h4>
                </div>
                <div class="card-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                  <i class="material-icons">close</i>
                </button>
                  <table class="table table-condensed">
                    <thead>
                      <tr class="text-dark bg-plomo">
                      <th>#</th>
                      <th>Nombre</th>
                      <th>Numero</th>  
                      </tr>
                    </thead>
                    <tbody id="tablas_registradas">
                      
                    </tbody>
                  </table>
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Buscar Cuenta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form name="form1">
        <div class="row">
              <div class="form-group col-sm-4">
                <label for="nro_cuenta" class="bmd-label-floating">Nro. Cuenta:</label>
                <input type="hidden" id="nro_cuenta_id" name="nro_cuenta_id">
                <input type="number" class="form-control" id="nro_cuenta" name="nro_cuenta" onkeypress="buscarCuentaList('numero'); pulsar(event);" onkeyDown="buscarCuentaList('numero');" onkeyUp="buscarCuentaList('numero');" autofocus>
              </div>
              <div class="form-group col-sm-4">
                <label for="cuenta" class="bmd-label-floating">Cuenta:</label>
                <input type="hidden" id="cuenta_id" name="cuenta_id">
                <input type="text" class="form-control" id="cuenta" name="cuenta" onkeypress="buscarCuentaList('nombre');pulsar(event)" onkeyDown="buscarCuentaList('nombre');" onkeyUp="buscarCuentaList('nombre');">
              </div>
              <div class="form-group col-sm-3">
                <select class="selectpicker form-control form-control-sm" name="padre" id="padre" data-style="<?=$comboColor;?>" onchange="buscarCuenta(form1);">
                  <option selected="selected" value="">Todas</option>                
                   <?php
                         $stmt = $dbh->prepare("SELECT codigo, nombre, SUBSTRING(numero, 1, 1) primero  FROM plan_cuentas where nivel=1");
                       $stmt->execute();
                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $primeroX=$row['primero'];
                        $nombreX=$row['nombre'];
                        ?><option value="<?=$primeroX;?>"><?=$nombreX;?></option><?php
                         }
                     ?>
             </select>
          </div>
              <div class="form-group col-sm-1">
                  <button type="button" class="btn btn-just-icon btn-danger btn-link" onclick="buscarCuenta(form1);">
                    <i class="material-icons">search</i>
                  </button>
              </div>
            </div>
            <div class="row" id="divResultadoBusqueda">
              <?php 
              //include "pruebaBusqueda2.php"; 
               include "pruebaBusqueda.php";
              ?>    
              <div class="form-group col-sm-8">
                Resultados de la Búsqueda
                        
              </div>
            </div>
        </form>
      </div>
      <div class="modal-footer">      
        <!--button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button-->
      </div>
    </div>
  </div>
</div>