alerts = {
  showSwal: function(type, url) {
    if (type == 'success-message') {
      swal("Correcto!", "El proceso se completo correctamente!", "success")
          .then((value) => {
          location.href=url;
      });

    }else if (type == 'error-message') {
      swal("Error!", "El proceso tuvo un problema!. Contacte con el administrador!", "error")
          .then((value) => {
          location.href=url;
      });
    }
    else if (type == 'error-message-capacitacion') {
      swal("Error!", "Hubo un problema al momento de registrar en el Servicio Web!. Contacte con el administrador!", "error")
          .then((value) => {
          location.href=url;
      });
    }
    else if (type == 'warning-message-and-confirmation') {
      swal({
        title: '¿Estás Seguro?',
        text: "¡No podra revertir el borrado!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si, Borrar!',
        cancelButtonText: 'No, Cancelar!',
        buttonsStyling: false
      }).then((result) => {
          if (result.value) {
            location.href=url; 
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        })
    }else if (type == 'warning-message-and-confirmation-anular-factura') {
      swal({
        title: '¿Quieres Anular la Factura?',
        text: "",
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si, Anular!',
        cancelButtonText: 'No, Cancelar!',
        buttonsStyling: false
      }).then((result) => {
          if (result.value) {
            location.href=url; 
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        })
    }else if (type == 'warning-message-and-confirmation-anular-solicitud') {
      swal({
        title: '¿Quieres Anular la Solicitud?',
        text: "",
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si, Anular!',
        cancelButtonText: 'No, Cancelar!',
        buttonsStyling: false
      }).then((result) => {
          if (result.value) {
            location.href=url; 
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        })
    }else if (type == 'warning-message-and-confirmation-generar-factura') {
      swal({
        title: '¿Quieres generar la Factura?',
        text: "",
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si, Generar!',
        cancelButtonText: 'No, Cancelar!',
        buttonsStyling: false
      }).then((result) => {
          if (result.value) {
            location.href=url; 
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        })
    }
    else if (type == 'warning-message-and-confirmation-cambiar-estado') {
      swal({
        title: '¿Quieres Activar ésta Distribución?',
        text: "",
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si, Activar!',
        cancelButtonText: 'No, Cancelar!',
        buttonsStyling: false
      }).then((result) => {
          if (result.value) {
            location.href=url; 
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        })
    }
    else if (type == 'warning-message-and-confirmation-clonar') {
      swal({
        title: 'Duplicar Registro',
        text: "¿Está seguro de realizar el duplicado?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-info',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
        buttonsStyling: false
      }).then((result) => {
          if (result.value) {
            location.href=url; 
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        })
    }
    else if (type == 'warning-message-and-confirmationGeneral') {
      swal({
        title: '¿Estás Seguro?',
        text: "¡No podra revertir el Proceso!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si, Continuar!',
        cancelButtonText: 'No, Cancelar!',
        buttonsStyling: false
      }).then((result) => {
          if (result.value) {
            location.href=url; 
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        })
    }
    else if(type == 'error-message3'){
      swal("Informativo!", "La planilla del mes ya fue registrada. Por favor Prosesar/Reprosesar planilla.", "warning")
          .then((value) => {
          location.href=url;
      });
    }
    else if(type == 'error-message4'){
      swal("Informativo!", "La planilla del año en curso ya fue registrada. Por favor Prosesar/Reprosesar planilla.", "warning")
          .then((value) => {
          location.href=url;
      });
    }
    else if(type == 'error-message5'){
      swal("LO SIENTO! :(", "No puedes registrar un nuevo contrato. Por favor cierre el contrato anterior. Gracias!.", "error")
          .then((value) => {
          location.href=url;
      });
    }
    else if(type == 'error-message6'){
      swal("LO SIENTO! :(", "No puedes retirar Al personal. Por favor cierre el último contrato. Gracias!.", "error")
          .then((value) => {
          location.href=url;
      });
    }
    else if(type == 'error-messageCajaChica'){
      swal("LO SIENTO! :(", "No puedes registrar una nueva caja chica. Por favor cierre el que está activo. Gracias!.", "warning")
          .then((value) => {
          location.href=url;
      });
    }
    else if(type == 'error-messageDepreciaciones'){
      swal("INFORMACION!", "La depreciación de éste mes ya fue realizada. Gracias!.", "warning")
          .then((value) => {
          location.href=url;
      });
    }
    else if(type == 'error-messageDepreciaciones2'){
      swal("LO SIENTO! :(", "Al parecer estás obviando algún mes. Por favor verifique los meses depreciados. Gracias!.", "warning")
          .then((value) => {
          location.href=url;
      });
    }
    else if(type == 'error-messageFacturas'){
      swal("LO SIENTO! :(", "Al parecer No registraste Ninguna Factura. Por favor Registre faturas. Gracias!.", "warning")
          .then((value) => {
          location.href=url;
      });
    }
    else if(type == 'error-messageEnviarCorreoAdjunto'){
      swal("ERROR AL ENVIAR CORREO! :(", "Antes de envíar correo, genere la factura. Gracias!.", "error")
          .then((value) => {
          location.href=url;
      });
    }
    else if(type == 'error-messageCamposVacios'){
      swal("ERROR AL ENVIAR CORREO! :(", "Los Campos marcados con * son obligatorios. Gracias!", "error")
          .then((value) => {
          location.href=url;
      });
    }
    else if (type == 'warning-message-change-user') {
      swal({
        title: '¿Estás Seguro?',
        text: "¡Cambiaras la sesión del usuario!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si, Cambiar!',
        cancelButtonText: 'No, Cancelar!',
        buttonsStyling: false
      }).then((result) => {
          if (result.value) {
            location.href=url; 
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        })
    } 
    
    else if (type == 'warning-message-crear-servicio') {
      swal({
        title: '¿Estás Seguro?',
        text: "¡Se creará el servicio!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-warning',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
        buttonsStyling: false
      }).then((result) => {
          if (result.value) {
            location.href=url; 
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        })
    }
    else if (type == 'warning-message-crear-comprobante') {
      swal({
        title: '¿Estás Seguro?',
        text: "¡Se creará el comprobante!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-warning',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
        buttonsStyling: false
      }).then((result) => {
          if (result.value) {
            location.href=url; 
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        })
    }

    else if (type == 'success-solicitud') {
      swal({
        title: 'SOLICITUD DE RECURSOS',
        text: "Ir a la creación de la solicitud",
        type: 'warning',
        confirmButtonClass: 'btn btn-warning',
        confirmButtonText: 'Ir',
        buttonsStyling: false
      }).then((result) => {
        if (result.value) {
            location.href=url; 
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            location.href=url; 
            return(true);
          }
        })
    }else if (type == 'warning-message-and-confirmation-restart') {
      swal({
        title: '¿Estás Seguro?',
        text: "¡Se restaurará el item Eliminado!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si, Continuar!',
        cancelButtonText: 'No, Cancelar!',
        buttonsStyling: false
      }).then((result) => {
          if (result.value) {
            location.href=url; 
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        })
    }

  },

  


}

