import { verifySanctum, fetchUser } from '$network/api.js';

export default {
  middleware: ({ router, store }) => {
    router.beforeEach(async (to, from, next) => {
      // Codigo para autentificacion
      const { state, commit } = store;
      const { checked, logged } = state.auth;

      if (!checked) {
        try {
          // Si la respuesta es 200 el login ha sido satisfactorio, no salta el catch.
          await verifySanctum();
          const response = await fetchUser();
          commit('auth/setCheckedStatus', true);
          commit('auth/setLoggedStatus', true);
          commit('common/setUser', response.data.content);
          next(to.path === '/login' ? '/' : to.path);
        } catch (error) {
          commit('auth/setCheckedStatus', true);
          commit('auth/setLoggedStatus', false);
          next('/login');
          return;
        }
        return;
      }

      if (checked && !logged && to.path !== '/login') {
        next('/login');
        return;
      }

      if (checked && logged && to.path === '/login') {
        next(from.path);
        return;
      }

      // Codigo para las tabs
      if (to.path === '/login') {
        next();
        return;
      }

      const resolved = router.resolve(to.fullPath).route;

      if (resolved.matched.length === 0) {
        throw new Error(`La ruta ${to.fullPath} no esta configurada en vue-router.`);
      }

      let matchedPath = resolved.matched[0].path;
      matchedPath = matchedPath.length === 0 ? '/' : matchedPath;

      const configPath = Object.values(router.options.routes).find(
        route => matchedPath === route.path
      );

      store.commit('ui/setCurrentTab', { ...to, configPath });

      next();
    });
  },
  recover: ({ store, router }) => {
    if (!window.localStorage) {
      console.warn('Este navegador no soporta localStorage.');
      return;
    }

    if (!window.localStorage.getItem('z-tabs-sync')) {
      return;
    }

    const snapshot = JSON.parse(window.localStorage.getItem('z-tabs-sync'));

    if (!snapshot || !snapshot.actives) {
      return;
    }

    // Comprobar si existe una ruta que ya no exista en la app
    const configuredRoutes = router.options.routes;
    const filteredActives = Object.keys(snapshot.actives)
      .filter(key => {
        const active = snapshot.actives[key];
        return configuredRoutes.some(route => route.path === active.configPath);
      })
      .reduce((acc, key) => {
        acc[key] = snapshot.actives[key];
        return acc;
      }, {});

    // Si las configuradas difieren de las guardadas, resincronizar las guardadas
    if (filteredActives.length !== Object.keys(snapshot.actives).length) {
      const secureSnapshot = { ...snapshot, ...{ actives: filteredActives } };
      window.localStorage.setItem('z-tabs-sync', JSON.stringify(secureSnapshot));
    }

    store.commit('ui/setSaveds', filteredActives);
  }
};
