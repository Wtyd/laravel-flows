export default {
  props: ['value'],
  data: () => ({
    stored: ''
  }),
  watch: {
    value() {
      this.stored = this.value;
    },
    stored() {
      this.$emit('input', this.stored);
    }
  },
  mounted() {
    this.stored = this.value;
  }
};
