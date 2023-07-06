<!-- MODAL DE LISTA DE IAF -->
<div class="modal fade modal-primary" id="modal_lista_iaf" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
      <div class="card-header card-header-primary card-header-text">
        <div class="card-text">
          <h4>Detalle de IAF</h4>
        </div>
        <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
      </div>
      <div class="card-body">
        <div class="row p-3">
          <?php if ($stmtIAF->rowCount() > 0) { ?>
          <table class="table">
            <tbody>
              <?php
                while ($rowIAF = $stmtIAF->fetch(PDO::FETCH_ASSOC)) {
                  $array_cod_iaf[]  = $rowIAF['cod_iaf'];
                  $title_cod_iaf    = $rowIAF['nombre'];
              ?>
                <tr>
                  <td><i class="material-icons dp48">check</i> <?= $title_cod_iaf; ?></td>
                </tr>
              <?php
                }
              ?>
            </tbody>
          </table> 
          <?php } else { ?>
            <p style="color: red;font-weight: bold;">No se encontraron registros</p>
          <?php } ?>
        </div>
      </div>
    </div>  
  </div>
</div>


<!-- MODAL DE LISTA DE CATEGORIA INOCUIDAD -->
<div class="modal fade modal-danger" id="modal_lista_cat_ino" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
      <div class="card-header card-header-danger card-header-text">
        <div class="card-text">
          <h4>Detalle de Categorias Inocuidad</h4>
        </div>
        <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
      </div>
      <div class="card-body">
        <div class="row p-3">
          <?php if ($stmtCatIno->rowCount() > 0) { ?>
          <table class="table">
            <tbody>
              <?php
                while ($row = $stmtCatIno->fetch(PDO::FETCH_ASSOC)) {
                  $array_cod_categoriainocuidad[]  = $row['cod_categoriainocuidad'];
                  $title_cod_categoriainocuidad    = $row['nombre'];
              ?>
                <tr>
                  <td><i class="material-icons dp48">check</i> <?= $title_cod_categoriainocuidad; ?></td>
                </tr>
              <?php
                }
              ?>
            </tbody>
          </table> 
          <?php } else { ?>
            <p style="color: red;font-weight: bold;">No se encontraron registros</p>
          <?php } ?>
        </div>
      </div>
    </div>  
  </div>
</div>

<!-- MODAL DE LISTA DE ORGNISMO CERTIFICADOR -->
<div class="modal fade modal-warning" id="modal_lista_org_cer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
      <div class="card-header card-header-warning card-header-text">
        <div class="card-text">
          <h4>Detalle de Organismo Certificador</h4>
        </div>
        <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
      </div>
      <div class="card-body">
        <div class="row p-3">
          <?php if ($stmtOrgCer->rowCount() > 0) { ?>
          <table class="table">
            <tbody>
              <?php
                while ($row = $stmtOrgCer->fetch(PDO::FETCH_ASSOC)) {
                  $array_organismo_certificador[]   = $row['cod_orgnismocertificador'];
                  $title_cod_organismo_certificador = $row['nombre'];
              ?>
                <tr>
                  <td><i class="material-icons dp48">check</i> <?= $title_cod_organismo_certificador; ?></td>
                </tr>
              <?php
                }
              ?>
            </tbody>
          </table> 
          <?php } else { ?>
            <p style="color: red;font-weight: bold;">No se encontraron registros</p>
          <?php } ?>
        </div>
      </div>
    </div>  
  </div>
</div>
<!-- MODAL DE LISTA DE NORMAS SELECCIONADAS -->
<div class="modal fade modal-info" id="modal_lista_norma" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
      <div class="card-header card-header-info card-header-text">
        <div class="card-text">
          <h4>Detalle de Normas</h4>
        </div>
        <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
      </div>
      <div class="card-body">
        <div class="row p-3">
          <div class="col-md-6">
            <h5 class="text-primary">
              <button type="button" class="btn btn-primary btn-round btn-fab btn-sm">
                  <i class="material-icons">public</i>
                <div class="ripple-container"></div>
              </button>
              <b>Normas Nacionales</b>
            </h5>
            <?php if ($stmtNorNac->rowCount() > 0) { ?>
            <table class="table">
              <tbody>
                <?php
                  while ($row = $stmtNorNac->fetch(PDO::FETCH_ASSOC)) {
                    $array_norma_nac[] = $row['cod_norma'];
                    $abreviatura       = $row['abreviatura'];
                    $nombre            = $row['nombre'];
                ?>
                <tr>
                  <td><i class="material-icons dp48">check</i> <b><?= $abreviatura; ?></b><?= ' / '.$nombre; ?></td>
                </tr>
                <?php
                  }
                ?>
              </tbody>
            </table>
            <?php } else { ?>
              <p style="color: red;font-weight: bold;">No se encontraron registros</p>
            <?php } ?>
          </div>
          <div class="col-md-6">
            <h5 class="text-danger">
              <button type="button" class="btn btn-danger btn-round btn-fab btn-sm">
                  <i class="material-icons">public</i>
                <div class="ripple-container"></div>
              </button>
              <b>Normas Internacionales</b>
            </h5>
            <?php if ($stmtNorInt->rowCount() > 0) { ?>
            <table class="table">
              <tbody>
                <?php
                  while ($row = $stmtNorInt->fetch(PDO::FETCH_ASSOC)) {
                    $array_norma_int[] = $row['cod_norma'];
                    $abreviatura       = $row['abreviatura'];
                    $nombre            = $row['nombre'];
                ?>
                <tr>
                  <td><i class="material-icons dp48">check</i> <b><?= $abreviatura; ?></b><?= ' / '.$nombre; ?></td>
                </tr>
                <?php
                  }
                ?>
              </tbody>
            </table>
            <?php } else { ?>
              <p style="color: red;font-weight: bold;">No se encontraron registros</p>
            <?php } ?>
          </div>
          <!-- ESTILO PARA BADGES - OTRAS NORMAS -->
          <style>
            .badge-custom {
              display: inline-block;
              padding: 8px;
              background-color: #f2f2f2;
              color: #333333;
              border: 1px solid #e5e5e5; /* Color de borde más suave */
              border-radius: 4px;
              width: 100%;
              text-align: center;
            }
          </style>
          <!-- OTRAS NORMAS -->
          <div class="col-md-12">
            <h5 class="text-success">
              <button type="button" class="btn btn-success btn-round btn-fab btn-sm">
                  <i class="material-icons">public</i>
                <div class="ripple-container"></div>
              </button>
              <b>Otras Normas</b>
            </h5>
            <?php if ($stmtNorOtras->rowCount() > 0) { ?>
              <div class="row">
                <?php
                  while ($row = $stmtNorOtras->fetch(PDO::FETCH_ASSOC)) {
                    $array_norma_otra[] = $row['observaciones'];
                    $nombre             = $row['observaciones'];
                ?>
                  <div class="col-md-4">
                    <span class="badge badge-custom"><?= $nombre; ?></span>
                  </div>
                <?php
                  }
                ?>
              </div>
            <?php } else { ?>
              <p style="color: red;font-weight: bold;">No se encontraron registros</p>
            <?php } ?>
          </div>
          <!-- ********* -->
        </div>
      </div>
    </div>  
  </div>
</div>



