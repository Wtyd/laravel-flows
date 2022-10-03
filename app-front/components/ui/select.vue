<template>
  <select v-model="stored" class="ui-select">
    <option v-for="option in cachedOptions" :key="option.value" :value="option.value">
      {{ option.text }}
    </option>
  </select>
</template>

<script>
import vModelMixin from '$mixins/v-model-mixin.js';

export default {
  mixins: [vModelMixin],
  props: {
    options: {
      type: Array,
      required: true
    }
  },
  computed: {
    cachedOptions() {
      const { options } = this;
      const parsed = options.reduce((acc, item) => {
        const type = typeof item;
        if (type === 'object') acc.push(item);

        if (type === 'string' || type === 'number')
          acc.push({
            value: item.toString(),
            text: item.toString()
          });

        return acc;
      }, []);

      return parsed;
    }
  },
  methods: {
    onChange(...args) {
      this.$emit('change', args);
    }
  }
};
</script>

<style lang="scss">
.ui-select {
  background: #fff;
  border: 1px solid #d7e1ea;
  border-radius: 3px;
  color: #72809d;
  font-family: Rubik;
  font-size: 12px;
  font-weight: normal;
  min-width: 200px;
  padding: 9px 10px;
}
</style>
