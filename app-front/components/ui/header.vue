<template>
  <header class="app-layout-header">
    <div class="app-layout-header-wrap">
      <div class="app-layout-header-left">
        <div class="menu-toggler">
          <button class="disable-menu-close" @click="toggleNavigationMenu">
            <i class="disable-menu-close fas fa-bars dusk-main-menu-icon"></i>
          </button>
        </div>
        <div class="app-layout-header-tabs">
          <router-link
            v-for="active in computedActives"
            :key="active.fullPath"
            :to="active.fullPath"
            class="app-layout-header-link"
            :class="{ 'hide-close': Object.values(computedActives).length === 1 }"
          >
            <span>{{ active.name }}</span>
            <button @click.stop.prevent="closeTab(active)">
              <i class="fas fa-times"></i>
            </button>
          </router-link>
        </div>
      </div>
      <div class="app-layout-header-right-slot">
        <slot name="right"></slot>
      </div>
    </div>
  </header>
</template>

<script>
import { mapState, mapMutations } from 'vuex';

export default {
  name: 'Header',
  computed: {
    ...mapState('ui', ['actives', 'current']),
    computedActives() {
      const { actives, $router } = this;
      const { routes } = $router.options;
      const getRouteName = configPath =>
        Object.values(routes).find(route => route.path === configPath).name;

      return Object.keys(actives)
        .filter(key => key !== '/login')
        .reduce((acc, activeKey) => {
          const value = actives[activeKey];

          acc[activeKey] = {
            ...value,
            name: getRouteName(value.configPath)
          };
          return acc;
        }, {});
    }
  },
  methods: {
    ...mapMutations('ui', ['setSaveds', 'removeActiveTab', 'toggleNavigationMenu']),
    closeTab(active) {
      const { actives, removeActiveTab, $route, $router } = this;

      // No se puede eliminar el ultimo tab
      if (Object.values(actives).length === 1) {
        return;
      }

      // Eliminar el tab de la store
      removeActiveTab(active.fullPath);

      // Evitar calcular la proxima a ver si no esta cerrando la que se esta viendo
      if (active.fullPath !== $route.fullPath) {
        return;
      }

      const list = Object.values(actives).filter(item => item.path !== '/login');
      const currentPosition = list.indexOf(list.find(item => item.fullPath === active.fullPath));
      const mostNearItem = list[currentPosition + 1] || list[currentPosition - 1];

      $router.push(mostNearItem.fullPath);
    }
  }
};
</script>

<style lang="scss">
.app-layout-header {
  align-items: center;
  background: #fff;
  box-shadow: 0 3px 4px rgba(53, 66, 91, 0.1);
  display: flex;
  flex-flow: row nowrap;
  height: 50px;
  justify-content: space-between;
  z-index: 2;

  .app-layout-header-wrap {
    align-items: center;
    display: flex;
    max-height: 40px;

    .app-layout-header-left {
      display: flex;
      flex-flow: row nowrap;
      height: 40px;

      .menu-toggler {
        align-items: center;
        display: flex;
        justify-content: center;

        button {
          background: none;
          border: 0;
          color: #35425b;
          cursor: pointer;
          font-size: 20px;
          outline: 0;
          padding: 0;
          padding-left: 30px;
          padding-right: 30px;
        }
      }

      .app-layout-header-tabs {
        display: flex;
        flex-flow: row nowrap;
      }
    }
  }

  .app-layout-header-link {
    align-items: center;
    background: #f0f4f8;
    border-radius: 3px;
    color: #abb6cc;
    display: flex;
    flex-flow: row nowrap;
    font-size: 14px;
    margin-right: 5px;
    padding: 14px;
    text-decoration: inherit;
    transition: all 0.15s;

    span {
      transform: translate(10px, 0);
      transition: transform 0.15s ease-in-out;
    }

    button {
      background: none;
      border: 0;
      color: #7684ad;
      cursor: pointer;
      opacity: 0;
    }

    &:hover:not(.hide-close) {
      span {
        transform: translate(0, 0);
      }

      button {
        opacity: inherit;
        transform: translate(5px, 0);
      }
    }

    &.router-link-exact-active {
      background: #35425b;
      color: #fff;

      button {
        color: #fff;
      }
    }
  }
}
</style>
