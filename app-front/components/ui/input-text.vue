<template>
  <div class="input-text" :class="{ 'with-preinput': $slots.preinput, border }">
    <div v-if="$slots.preinput" class="preinput">
      <slot name="preinput"></slot>
    </div>
    <input
      v-model="stored"
      :type="password ? 'password' : 'text'"
      :placeholder="placeholder"
      @keyup.enter="$emit('enter')"
    />
    <div v-if="$slots.postinput">
      <slot name="postinput"></slot>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    placeholder: {
      type: String
    },
    value: {
      type: [String, Number]
    },
    password: {
      type: Boolean
    },
    border: {
      type: Boolean
    }
  },
  data: () => ({
    stored: ''
  }),
  watch: {
    stored() {
      this.$emit('input', this.stored);
    },
    value() {
      this.stored = this.value;
    }
  }
};
</script>

<style lang="scss">
.input-text {
  align-items: center;
  background: #fff;
  border-radius: 3px;
  border-radius: 3px;
  color: #72809d;
  display: flex;
  flex-flow: row nowrap;
  font-size: 14px;
  overflow: hidden;

  &.border {
    border: 1px solid #d7e1ea;
  }

  &.with-preinput {
    input {
      padding-left: 0;
    }
  }

  .preinput {
    padding: 0 10px;
  }

  input {
    border: 0;
    box-sizing: border-box;
    font-family: 'Rubik', sans-serif;
    font-size: 14px;
    padding: 8px 10px;
    width: 100%;

    &:focus {
      outline: none;
    }
  }
}
</style>
