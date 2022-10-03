import Vue from 'vue';

const stateToLocalStorage = state => {
  if (window.localStorage) window.localStorage.setItem('z-tabs-sync', JSON.stringify(state));
};

export default {
  allowTabCache(state, { fullPath, componentName }) {
    Vue.set(state.cachedTabs, fullPath, componentName);
  },
  setNavigationMenuStatus(state, status) {
    state.showNavigationMenu = status;
  },
  toggleNavigationMenu(state) {
    state.showNavigationMenu = !state.showNavigationMenu;
  },
  setCurrentTab(state, route) {
    const { fullPath, path, configPath } = route;
    const toStore = { fullPath, path, configPath: configPath.path };

    state.current = toStore.fullPath;
    Vue.set(state.actives, fullPath, toStore);

    stateToLocalStorage(state);
  },
  setSaveds(state, actives) {
    Vue.set(state, 'actives', actives);
  },
  removeActiveTab(state, fullPath) {
    Vue.delete(state.actives, fullPath);
    Vue.delete(state.cachedTabs, fullPath);
    stateToLocalStorage(state);
  }
};
