<template>
  <nav
    v-click-outside="onClickOutside"
    class="navigation-menu"
    :class="{ open: showNavigationMenu }"
  >
    <div v-if="$slots.top" class="top-slot">
      <slot name="top"></slot>
    </div>
    <NavigationMenuUser :user="user"></NavigationMenuUser>
    <div class="search">
      <InputText v-model="search" placeholder="Buscar en menú...">
        <template v-slot:preinput>
          <i class="icon fas fa-search"></i>
        </template>
      </InputText>
    </div>
    <div class="links nice-scrollbar">
      <NavigationMenuLink
        v-for="link in mappedList"
        :key="link.path"
        :link="link"
      ></NavigationMenuLink>
    </div>
    <div v-if="$slots.bottom" class="bottom-slot">
      <slot name="bottom"></slot>
    </div>
  </nav>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
import links from '../../menu.yaml';
import { normalize } from '$lib/utils.js';

import InputText from './input-text.vue';
import NavigationMenuLink from './navigation-menu-link.vue';
import NavigationMenuUser from './navigation-menu-user.vue';

const searchNormalized = (string, search) =>
  normalize(string)
    .toLowerCase()
    .indexOf(normalize(search).toLowerCase()) !== -1;

export default {
  components: {
    InputText,
    NavigationMenuLink,
    NavigationMenuUser
  },
  data: () => ({
    links,
    search: ''
  }),
  computed: {
    ...mapState('ui', ['showNavigationMenu', 'actives']),
    ...mapState('common', ['user']),
    filteredByPermissions() {
      const { links: storedLinks, permissions } = this;
      const linkHavePermissions = link =>
        link.permissions.some(permissionString =>
          permissions.some(permission => permission === permissionString)
        );
      const getFilteredChilds = childs =>
        childs.reduce((acc, child) => {
          if (!child.permissions) {
            acc.push(child);
            return acc;
          }

          if (!linkHavePermissions(child)) {
            return acc;
          }

          acc.push(child);
          return acc;
        }, []);

      return storedLinks.reduce((acc, link) => {
        // Si no tiene hijos, ni permisos, el link es valido
        if (!link.permissions && !link.childs) {
          acc.push(link);
          return acc;
        }

        // Si tiene permisos, no validos, no añadir
        if (link.permissions && !linkHavePermissions(link)) {
          return acc;
        }

        // Si tiene hijos, comprobar los permisos de los hijos
        if (link.childs) {
          acc.push({ ...link, childs: getFilteredChilds(link.childs) });
          return acc;
        }

        acc.push(link);
        return acc;
      }, []);
    },
    filteredBySearch() {
      const { search, filteredByPermissions } = this;

      if (search.length === 0) {
        return filteredByPermissions;
      }

      return filteredByPermissions.reduce((acc, link) => {
        // Busqueda padres
        if (searchNormalized(link.name, search)) {
          acc.push(link);
          return acc;
        }

        // Sin coincidencias y sin hijos no se continua
        if (!link.childs) {
          return acc;
        }

        // Buscar en hijos
        const filteredChilds = link.childs.filter(child => searchNormalized(child.name, search));

        if (filteredChilds.length !== 0) {
          acc.push({ ...link, forceOpen: true, childs: filteredChilds });
        }

        return acc;
      }, []);
    },
    mappedList() {
      const { filteredBySearch, actives } = this;
      const tabsActives = Object.keys(actives);

      return filteredBySearch.map(link => ({ ...link, active: tabsActives.includes(link.path) }));
    },
    permissions() {
      if (!this.user || !this.user.roles) {
        return [];
      }

      const reduced = this.user.roles.reduce((acc, role) => {
        role.permissions.forEach(permission => {
          if (!acc[permission.name]) {
            acc[permission.name] = true;
          }
        });

        return acc;
      }, {});

      return Object.keys(reduced);
    }
  },
  methods: {
    ...mapMutations('ui', ['setNavigationMenuStatus']),
    onClickOutside({ target }) {
      const { showNavigationMenu, setNavigationMenuStatus } = this;
      const element = target;

      if (!showNavigationMenu) {
        return;
      }

      if (!element) {
        setNavigationMenuStatus(false);
      }

      if (element.classList.contains('disable-menu-close')) {
        return;
      }

      if (
        document.querySelector('.navigation-menu').contains(element) ||
        element.parentNode.parentNode.classList.contains('link-with-childs') ||
        element.parentNode.parentNode.classList.contains('links')
      ) {
        return;
      }

      setNavigationMenuStatus(false);
    }
  }
};
</script>

<style lang="scss">
.navigation-menu {
  background: #35425b;
  box-sizing: border-box;
  display: flex;
  flex-flow: column;
  height: 100vh;
  height: calc(100vh - 50px);
  left: 0;
  max-width: 240px;
  position: fixed;
  top: 50px;
  transform: translate(-240px, 0);
  transition: transform 0.2s ease-in-out;
  z-index: 1;

  &.open {
    transform: inherit;
  }

  .search {
    box-sizing: border-box;
    display: flex;
    padding: 5px;

    .icon {
      &::before {
        color: #72809d;
        font-size: 15px;
        font-weight: 400;
        pointer-events: none;
      }
    }

    .input-text input {
      font-weight: 300;
      padding: 8px 8px 6px 0;
      width: 100%;
    }
  }

  .links {
    overflow: auto;

    .icon {
      font-size: 15px;
    }

    &.nice-scrollbar {
      &::-webkit-scrollbar {
        background-color: #475170;
        border-radius: 9px;
        cursor: pointer;
        overflow: hidden;
        width: 9px;
      }

      &::-webkit-scrollbar-thumb {
        background-color: #31384c;
        border-radius: 9px;
      }

      &::-webkit-scrollbar-track {
        background-color: #475170;
        border-radius: 9px;
        opacity: 0;
      }
    }
  }

  .links a {
    align-items: center;
    display: flex;
    flex-flow: row nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .links a,
  .links .parent {
    color: #98a4bd;
    cursor: pointer;
    display: flex;
    font-size: 12px;
    margin: 0;
    padding: 7.5px 17px;
    text-decoration: none;
    transition: background 0.15s ease-in-out;
    user-select: none;

    &.open {
      background: #536589;
      color: #fff;
    }
  }

  .links {
    display: flex;
    flex-flow: column;

    .link.active a {
      background: #536589;
      color: #fff;
    }

    .parent {
      display: flex;
      flex-flow: row nowrap;
      justify-content: space-between;
    }

    .icon {
      margin-right: 10px;
    }

    .child .icon {
      margin-left: 7px;
      margin-right: 14px;
    }

    .link-with-childs {
      display: flex;
      flex-flow: column;
    }
  }

  a.child {
    background: #475170;

    &.router-link-active {
      color: #fff;
    }
  }
}
</style>
