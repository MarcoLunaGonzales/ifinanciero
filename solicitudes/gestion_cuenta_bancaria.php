
<!-- Modal de Lista de Cuenta Bancaria -->
<div class="modal fade" id="cuentaBancariaLista" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content card">
            <div class="card-header card-header-primary card-header-text">
                <div class="card-text">
                    <h5>Lista de Cuentas Bancarias</h5>
                </div>
                <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                </button>
            </div>
            <div class="card-body">
                <button type="button" class="btn btn-success" id="form_reg"><i class="material-icons">add</i> Nueva Cuenta</button>
                <div class="bodyListaCuenta"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Registro y Edicion de Cuenta Bancaria -->
<div class="modal fade" id="cuentaBancariaNuevo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content card">
			<div class="card-header card-header-success card-header-text">
				<div class="card-text">
					<ul class="nav nav-tabs justify-content-end" role="tablist">
						<li class="nav-item" id="modalAgregarEditarLabel">
                            titulo
						</li>
					</ul>
				</div>
				<button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
					<i class="material-icons">close</i>
				</button>
			</div>
            <!-- Detalle de proveedor -->
			<input type="hidden" name="prov_cod_proveedor" id="prov_cod_proveedor" value=""/>
			<input type="hidden" name="prov_nombre_proveedor" id="prov_nombre_proveedor" value=""/>
            <!-- ID cuenta para editar -->
			<input type="hidden" name="IdCuentaBanco" id="IdCuentaBanco" value=""/>

			<div class="card-body tab-content">
				<div class="tab-pane active" id="nuevaCuentaBeneficiario" role="tabpanel">
					<div class="col-sm-12">
						<div class="row">                      
							<label class="col-sm-2 col-form-label" style="color: #4a148c;">Proveedor</label>
							<div class="col-sm-10">
								<div class="form-group">  
									<input class="form-control" readonly type="text" name="prov_nombre_proveedorbeneficiario" id="prov_nombre_proveedorbeneficiario" required="true">                                                                                                                       
								</div>
							</div>
						</div>
						<div class="row">
							<label class="col-sm-2 col-form-label" style="color: #4a148c;">Banco</label>
							<div class="col-sm-4">
								<div class="form-group">
									<select class="selectpicker form-control form-control-sm" name="prov_nuevo_banco" id="prov_nuevo_banco" data-live-search="true" data-size="6" data-style="btn btn-primary">                                  
										<?php 
										$stmt3 = $dbh->prepare("SELECT * from bancos where cod_estadoreferencial=1");
										$stmt3->execute();
										while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
											$codigoSel=$rowSel['codigo'];
											$nombreSelX=$rowSel['nombre'];
											?><option value="<?=$codigoSel;?>"><?=$nombreSelX?></option><?php 
										}
										?>
									</select>
								</div>
							</div>
							<label class="col-sm-2 col-form-label" style="color: #4a148c;">Cuenta Beneficiario</label>
							<div class="col-sm-4">
								<div class="form-group" id="">
									<input class="form-control" type="text" name="prov_nuevo_cuenta_beneficiario" id="prov_nuevo_cuenta_beneficiario" placeholder="123-456-78-90" required="true"/>
								</div>
							</div>                          
						</div>
						<div class="row">                      
							<label class="col-sm-2 col-form-label" style="color: #4a148c;">Nombre Completo Beneficiario</label>
							<div class="col-sm-10">
								<div class="form-group">  
									<input class="form-control" type="text" name="prov_nuevo_nombre_beneficiario" id="prov_nuevo_nombre_beneficiario" required="true">
								</div>
							</div>
						</div>
						<div class="mensaje"></div>
					</div>
					<div class="form-group float-right">
						<button type="button" class="btn btn-warning btn-round" id="guardarRegistro">Guardar</button>
					</div>  
				</div>
			</div>
		</div>
	</div>
</div>

<script>
    /****************************
     * GESTIÓN DE CUENTA BANCARIA
     ****************************/
    // LISTA de Cuentas Bancarias
    $("body").on('click','.ges_cuenta_bancaria',function() {
        let index = $(this).data('index');
        let cod_proveedor = $("#proveedor" + index).val();
        var selectedOptionText = $("#proveedor" + index + " option:selected").text();
        $('#prov_cod_proveedor').val(cod_proveedor);
        $('#prov_nombre_proveedor').val(selectedOptionText);

        // Validación de seleccion de proveedor
        if (cod_proveedor === null || cod_proveedor === "") {
            Swal.fire({
                type: 'warning',
                title: 'Ops!',
                text: 'Por favor, seleccione un proveedor antes de continuar.',
            });
            return;
        }
        // Realizar la solicitud AJAX
        $.ajax({
            type: "POST",
            url: "ws_cuentaBancaria_lista.php",
            data: { 
            cod_proveedor: cod_proveedor },
            dataType: "json",
            success: function(response) {
                // Verifica si hay registros en la respuesta
                if (response.totalLista === 0) {
                    // Si no hay registros, muestra un mensaje
                    $(".bodyListaCuenta").html("<p>No se encontraron registros</p>");
                } else {
                    // Si hay registros, genera la tabla
                    var tableHtml = '<table class="table">';
                    tableHtml += '<thead>';
                    tableHtml += '<tr>';
                    tableHtml += '<th>Banco</th>';
                    tableHtml += '<th>Beneficiario</th>';
                    tableHtml += '<th>Tipo de Cuenta</th>';
                    tableHtml += '<th>Tipo de Moneda</th>';
                    tableHtml += '<th>Nro. de Cuenta</th>';
                    tableHtml += '<th>Vigencia</th>';
                    tableHtml += '<th>Acciones</th>';
                    tableHtml += '</tr>';
                    tableHtml += '</thead>';
                    tableHtml += '<tbody>';

                    // Itera a través de los registros en la respuesta
                    $.each(response.lista, function(index, cuenta) {
                        var vigenciaSpan = '<span class="badge badge-' + (cuenta.Vigencia === "1" ? 'success' : 'danger') + '">' + (cuenta.Vigencia === "1" ? 'Activo' : 'Inactivo') + '</span>';
                        var accionesHtml = '<td>';
                        // Botón de edición
                        accionesHtml += '<button type="button" class="btn btn-sm btn-info form_edit" data-id_cuenta_banco="' + cuenta.IdCuentaBanco + '" data-banco="' + cuenta.IdBanco + '" data-cuenta="' + cuenta.NroCuenta + '" data-nombre="' + cuenta.BeneficiarioNombre + '"><i class="material-icons" title="Editar">edit</i></button>';
                        // Botón para activar
                        if (cuenta.Vigencia === "0") {
                            accionesHtml += '<button class="btn btn-sm btn-success form_del" title="Activar" data-id_cuenta_banco="' + cuenta.IdCuentaBanco + '" data-vigencia="' + cuenta.Vigencia + '"><i class="material-icons">check</i></button>';
                        }
                        // Botón para desactivar
                        if (cuenta.Vigencia === "1") {
                            accionesHtml += '<button class="btn btn-sm btn-danger form_del" title="Desactivar" data-id_cuenta_banco="' + cuenta.IdCuentaBanco + '" data-vigencia="' + cuenta.Vigencia + '"><i class="material-icons">close</i></button>';
                        }
                        accionesHtml += '</td>';
                        
                        tableHtml += '<tr>';
                        tableHtml += '<td>' + cuenta.Banco + '</td>';
                        tableHtml += '<td>' + cuenta.BeneficiarioNombre + '</td>';
                        tableHtml += '<td>' + cuenta.TipoCuenta + '</td>';
                        tableHtml += '<td>' + cuenta.TipoMoneda + '</td>';
                        tableHtml += '<td>' + cuenta.NroCuenta + '</td>';
                        tableHtml += '<td>' + vigenciaSpan + '</td>';
                        tableHtml += accionesHtml;
                        tableHtml += '</tr>';
                    });

                    tableHtml += '</tbody>';
                    tableHtml += '</table>';

                    // Inserta la tabla generada en el modal
                    $(".bodyListaCuenta").html(tableHtml);
                }
                $('#cuentaBancariaLista').modal('show');
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX:", error);
            }
        });
    });

    // Función ABRE MODAL REGISTRO
    $("#form_reg").click(function() {
        $('#cuentaBancariaLista').modal('toggle');

        $("#IdCuentaBanco").val("");
        $('#prov_nombre_proveedorbeneficiario').val($('#prov_nombre_proveedor').val());
        $("#prov_nuevo_cuenta_beneficiario").val("");
        $("#prov_nuevo_nombre_beneficiario").val("");

        $("#modalAgregarEditarLabel").text("Agregar Registro");
        $("#cuentaBancariaNuevo").modal("show");
    });

    // Función ABRE MODAL EDITAR
    $("body").on('click', ".form_edit", function() {
        $('#cuentaBancariaLista').modal('toggle');
        
        var IdCuentaBanco = $(this).data("id_cuenta_banco");
        var banco         = $(this).data('banco');
        var cuenta        = $(this).data('cuenta');
        var nombre        = $(this).data('nombre');

        $("#modalAgregarEditarLabel").text("Editar Registro");
        $('#prov_nombre_proveedorbeneficiario').val($('#prov_nombre_proveedor').val());
        $("#IdCuentaBanco").val(IdCuentaBanco);
        $("#prov_nuevo_banco").val(banco).trigger("change");
        $("#prov_nuevo_cuenta_beneficiario").val(cuenta);
        $("#prov_nuevo_nombre_beneficiario").val(nombre);

        $("#cuentaBancariaNuevo").modal("show");
    });

    // Función para GUARDAR Y ACTUALIZAR el registro mediante AJAX
    $("#guardarRegistro").click(function() {
        let formData = new FormData();
        
        formData.append('id_cuenta_banco', $('#IdCuentaBanco').val());
        formData.append('cod_proveedor',$("#prov_cod_proveedor").val());
        formData.append('banco', $('#prov_nuevo_banco').val());
        formData.append('cuenta', $('#prov_nuevo_cuenta_beneficiario').val());
        formData.append('nombre', $('#prov_nuevo_nombre_beneficiario').val());

        $.ajax({
            url: "ws_cuentaBancaria_guardar.php",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json', // Indica que esperas una respuesta JSON
            success: function(response) {
                if (response.estado) {
                    Swal.fire({
                        type: "success",
                        title: response.mensaje,
                        showConfirmButton: false,
                        timer: 2000
                    });
                } else {
                    Swal.fire({
                        type: "error",
                        title: response.mensaje,
                    });
                }

                $("#cuentaBancariaNuevo").modal("hide");
            },
            error: function(xhr, textStatus, errorThrown) {
                // Maneja los errores de la solicitud AJAX
                console.error(textStatus);
            }
        });

    });
    
    // Función para modificar ESTADO
    $("body").on("click", ".form_del", function() {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Deseas cambiar el estado del registro?",
            type: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
        }).then((result) => {
            if (result.value) {
                $('#cuentaBancariaLista').modal('toggle');
                // Si el usuario confirma la acción, procede con la solicitud AJAX
                let formData = new FormData();
                formData.append('id_cuenta_banco', $(this).data('id_cuenta_banco'));
                formData.append('vigencia', $(this).data('vigencia'));
                formData.append('cod_proveedor',$("#prov_cod_proveedor").val());
                $.ajax({
                    url: "ws_cuentaBancaria_estado.php",
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.estado) {
                            Swal.fire({
                                type: "success",
                                title: response.mensaje,
                                showConfirmButton: false,
                                timer: 2000,
                                onClose: function() {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                type: "error",
                                title: response.mensaje,
                            });
                        }
                        $("#modalAgregarEditar").modal("hide");
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        console.error(textStatus);
                    }
                });
            }
        });
    });
</script>
