const CONFIGS = {
  common: {
    position: 'bottom-right',
    duration: 5000,
    keepOnHover: true,
    iconPack: 'fontawesome',
    theme: 'toasted-primary'
  },
  error: {
    type: 'error'
  }
};

export default {
  install(Vue, { engine }) {
    Vue.prototype.$notify = {
      error(message) {
        const toast = engine.show(message, {
          ...CONFIGS.common,
          ...CONFIGS.error
        });

        toast.el.addEventListener('click', () => toast.goAway(0));

        return toast;
      }
    };
  }
};
