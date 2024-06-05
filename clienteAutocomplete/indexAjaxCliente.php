<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once 'conexion.php';
require_once 'styles.php';

?>
<div class="content">
    <div class="container-fluid">
        <div style="overflow-y:scroll;">
            <div class="card">
                <div class="card-body ">
                    <div class="row">
                        <label class="col-sm-2 col-form-label">Cliente:</label>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="input-group">
                                    <input id="autocomplete" class="form-control">
                                    <input id="clienteCodigo" style="display:none;">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="clienteSuccess">
                                            <i id="checkIcon" class="fas fa-check text-success" style="display:none;"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<style>
    /* .ui-autocomplete {
        z-index: 1050;
    } */
    .ui-autocomplete {
        max-height: 200px; /* Establece la altura máxima de la lista */
        overflow-y: auto; /* Habilita el scroll vertical */
        overflow-x: hidden; /* Oculta el scroll horizontal, si es necesario */
        border: 1px solid #ccc; /* Añade un borde suave */
        background-color: #fff; /* Color de fondo */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Sombra suave */
        z-index: 1050;
    }

    .ui-menu-item {
        padding: 8px 12px; /* Ajusta el relleno de los elementos de la lista */
        color: #333; /* Color del texto */
        font-size: 14px; /* Tamaño del texto */
    }

    .ui-menu-item:hover {
        background-color: #f0f0f0; /* Cambia el color de fondo al hacer hover */
    }
    
    .input-group .form-control-feedback {
        position: absolute;
        right: 10px;
        top: calc(50% - 0.5em);
    }
    .ui-autocomplete {
        max-height: 300px;
        max-width: 350px;
        overflow-y: auto;
        overflow-x: hidden;
    }
</style>

<script>
$(function() {
    $("#autocomplete").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "clienteAutocomplete/ajaxBuscarCliente.php",
                type: "GET",
                dataType: "json",
                data: {
                    cliente_nombre: request.term
                },
                success: function(resp) {
                    // console.log(response.data)
                    response(resp.data);
                }
            });
        },
        autoFocus: true,
        minLength: 1,
        select: function(event, ui) {
            // Lógica de selección
            setSelectedValue(ui.item);
            return false;
        },
        change: function(event, ui) {
            if (!ui.item) {
                // Clear the hidden input
                $('#clienteCodigo').val('');
                // Hide the check icon
                $('#checkIcon').hide();
            }
        }
    }).autocomplete("instance")._renderItem = function(ul, item) {
        return $("<li>")
            .append("<div>" + item.label + "</div>")
            .appendTo(ul);
    };

    $('#autocomplete').on('input', function() {
        $('#checkIcon').hide();
    });

    // Función para establecer el valor seleccionado
    function setSelectedValue(item) {
        $('#autocomplete').val(item.label);
        $('#clienteCodigo').val(item.value);
        $('#checkIcon').show();
    }

});
</script>

</body>
</html>
