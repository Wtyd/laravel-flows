// Modulos
import common from './common/index.js';
import auth from './auth/index.js';
import ui from './ui/index.js';

const store = {
  strict: true,
  modules: {
    common,
    auth,
    ui
  }
};

export default store;
