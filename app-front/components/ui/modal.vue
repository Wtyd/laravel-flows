<template>
  <div class="ui-modal" :class="{ 'no-block': noBlock }" @click.stop="close">
    <div class="ui-modal-content" @click.stop>
      <div v-if="header" class="header">
        <p class="title">{{ title }}</p>
        <button @click="close"><i class="fas fa-times-circle"></i></button>
      </div>
      <div class="body">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: String,
    header: {
      type: Boolean,
      default: true
    },
    active: {
      type: Boolean,
      required: true,
      default: false
    },
    noBlock: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    close() {
      this.$emit('close');
    }
  }
};
</script>

<style lang="scss">
.ui-modal {
  align-items: center;
  background: rgba(0, 0, 0, 0.43);
  display: flex;
  flex-flow: column;
  height: 100vh;
  justify-content: center;
  left: 0;
  position: fixed;
  top: 0;
  width: 100vw;
  z-index: 99999;

  &.no-block {
    pointer-events: none;
  }

  .ui-modal-content {
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 3px 4px rgba(53, 66, 91, 0.1);
    overflow: hidden;
    pointer-events: initial;
    width: 600px;

    .header {
      align-items: center;
      background: #35425b;
      display: flex;
      flex-flow: row nowrap;
      justify-content: space-between;
      padding: 9px 20px;

      .title {
        color: #fff;
        font-family: Rubik;
        font-size: 14px;
        font-weight: bold;
        margin: 0;
      }

      button {
        background: none;
        border: 0;
        color: #fff;
        cursor: pointer;
        margin: -8px 0;
      }
    }

    .body {
      padding: 20px;
    }
  }
}
</style>
