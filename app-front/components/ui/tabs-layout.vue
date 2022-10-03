<template>
  <div class="app-layout">
    <Header></Header>

    <div class="app-layout-content nice-scrollbar">
      <slot></slot>
    </div>
  </div>
</template>

<script>
import Header from '$components/ui/header.vue';

export default {
  name: 'TabsLayout',
  components: {
    Header
  }
};
</script>

<style lang="scss">
.app-layout {
  display: flex;
  flex-flow: column;
  height: 100%;

  .app-layout-content {
    flex: 1;
    overflow-y: auto;
  }
}
</style>
