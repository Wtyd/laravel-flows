export default {
  beforeCreate() {
    if (this.$options.name === undefined) {
      throw new Error('Los componentes que son paginas es obligatorio que definan la opcion name.');
    }

    const { name } = this.$options;
    const route = this.$route;
    const store = this.$store;

    store.commit('ui/allowTabCache', {
      fullPath: route.fullPath,
      componentName: name
    });
  }
};
