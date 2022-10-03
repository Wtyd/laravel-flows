import Vue from 'vue/dist/vue.js';
import VueToasted from 'vue-toasted';
import ToastedHelper from './toasted-helper.js';
import { store } from '../app.js';

Vue.use(VueToasted);
Vue.use(ToastedHelper, { engine: Vue.toasted });

const errorInterceptor = error => {
  const { response } = error;
  const { status } = response;

  if (!store.state.auth.checked && status !== 500) {
    return Promise.reject(error);
  }

  if (status === 401) {
    window.location.reload();
  }

  if (status === 403) {
    Vue.prototype.$notify.error('No tienes permisos para realizar esa acción.');
  }

  if (status === 429) {
    Vue.prototype.$notify.error(`Ha superado el límite de peticiones, espere durante 1 minuto.`);
  }

  if (status === 500) {
    Vue.prototype.$notify.error(`Ha ocurrido un error interno en el servidor.`);
  }

  return Promise.reject(error);
};

export default errorInterceptor;
