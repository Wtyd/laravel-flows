export default {
  methods: {
    showErrors(error, alternativeMessage = '') {
      if (String(error.response.data.status).charAt(0) === '4' && 'errors' in error.response.data) {
        let formattedError = JSON.parse(JSON.stringify(error.response.data.errors));
        if (
          !Array.isArray(formattedError) &&
          typeof formattedError === 'object' &&
          formattedError !== null
        ) {
          formattedError = [formattedError];
        }
        if (Array.isArray(formattedError) && formattedError.length > 0) {
          formattedError.forEach(element => {
            const message =
              'campo' in element ? `${element.campo}: ${element.mensaje}` : element.mensaje;
            this.$notify.error(message);
          });
          return;
        }
      }

      if (alternativeMessage !== '') this.$notify.error(alternativeMessage);
      if (alternativeMessage === '')
        this.$notify.error('Ha ocurrido un error al procesar esta petición');
    }
  }
};
