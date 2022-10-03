<template>
  <Tippy
    ref="tippy"
    class="floating-interactive"
    :trigger="trigger"
    :placement="placement"
    :theme="theme"
    :arrow="arrow"
    :interactive="true"
  >
    <template v-slot:trigger>
      <slot class="floating-interactive-trigger"></slot>
    </template>
    <slot name="content"></slot>
  </Tippy>
</template>

<script>
import { TippyComponent as Tippy } from 'vue-tippy';

export default {
  components: {
    Tippy
  },
  props: {
    trigger: {
      type: String,
      default: 'click'
    },
    placement: {
      type: String,
      default: 'bottom'
    },
    theme: {
      type: String,
      default: 'light'
    },
    arrow: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    close() {
      const { tip } = this.$refs.tippy;

      tip.hide();
    }
  }
};
</script>

<style lang="scss">
.floating-interactive {
  .floating-interactive-link {
    cursor: pointer;
  }
}

.contextual-menu {
  background: #fff;
  list-style: none;
  margin: -5px -9px;
  padding: 0;

  button {
    background: #fff;
    border: 0;
    cursor: pointer;
    outline: none;
    padding: 5px 10px;
    transition: 0.15s ease;

    &:hover {
      background: #ecf0f1;
    }
  }
}
</style>
