<template>
  <TabsLayout>
    <h1>Dashboard</h1>
  </TabsLayout>
</template>

<script>
import pageMixin from '$mixins/page-mixin.js';
import TabsLayout from '$components/ui/tabs-layout.vue';
import * as api from '$network/api.js';

export default {
  name: 'Dashboard',
  components: {
    TabsLayout
  },
  mixins: [pageMixin],
  created() {
    this.prueba();
  },
  methods: {
    async prueba() {
      // TODO QUITAR CUANDO FUNCIONE
      const response = await api.fetchUser();
      console.log('fetch', response);
    }
  }
};
</script>
