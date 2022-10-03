<template>
  <div class="app">
    <KeepAlive :exclude="['Login']" :include="allowedToCache">
      <RouterView></RouterView>
    </KeepAlive>
    <NavigationMenu v-if="$route.path !== '/login'"></NavigationMenu>
  </div>
</template>

<script>
import { mapState } from 'vuex';

import NavigationMenu from '$components/ui/navigation-menu.vue';

export default {
  name: 'App',
  components: {
    NavigationMenu
  },
  computed: {
    ...mapState('ui', ['cachedTabs']),
    allowedToCache() {
      if (Object.values(this.cachedTabs).length === 0) {
        return /(.*)/;
      }

      return Object.values(this.cachedTabs);
    }
  }
};
</script>
