import Vue from 'vue/dist/vue.js';
import VueRouter from 'vue-router';
import Vuex from 'vuex';
import VueClickOutside from 'v-click-outside';
import VueToasted from 'vue-toasted';
import VueTippy from 'vue-tippy';
// import Pusher from 'pusher-js';
import ToastedHelper from './lib/toasted-helper.js';
import storeConfig from './store/index.js';
import tabsStoreLink from './lib/tabs-store-link.js';
import routesList from './lib/merge-routes.js';

import 'normalize.css/normalize.css';
import 'tippy.js/themes/light.css';
import './app.scss';

import App from './components/app.vue';

Vue.use(Vuex);
Vue.use(VueRouter);
Vue.use(VueClickOutside);
Vue.use(VueToasted);
Vue.use(ToastedHelper, { engine: Vue.toasted });
Vue.use(VueTippy);

export const store = new Vuex.Store(storeConfig);
export const router = new VueRouter({ routes: routesList });

tabsStoreLink.recover({ router, store });
tabsStoreLink.middleware({ router, store });

Vue.config.productionTip = false;

/**
 * === Dejo el tema WebScokets con pusherasí porque es una prueba, hay que pasarlo a limpio.
 */
// Pusher.logToConsole = false;

// const pusher = new Pusher('8ace8f27a4659649dc0c', {
//   cluster: 'eu',
//   forceTLS: true
// });

// const channel = pusher.subscribe('my-channel');

// channel.bind('my-event', data => {
//   console.log('1', data);
// });
/* === FIN */

new Vue({
  store,
  router,
  render: h => h(App)
}).$mount('#app');
