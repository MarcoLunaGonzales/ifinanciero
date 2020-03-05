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

    


  },

  


}
