<template>
  <button class="ui-button">
    <slot></slot>
  </button>
</template>

<script>
export default {};
</script>

<style lang="scss">
.ui-button {
  background: #35425b;
  border: 0;
  border-radius: 3px;
  color: #fff;
  cursor: pointer;
  font-family: 'Rubik', sans-serif;
  font-weight: 500;
  padding: 8px 15px;
}
</style>
