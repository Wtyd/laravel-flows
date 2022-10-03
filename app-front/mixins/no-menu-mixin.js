import { mapMutations } from 'vuex';

export default {
  methods: {
    ...mapMutations('ui', ['setNavigationMenuStatus'])
  },
  activated() {
    this.setNavigationMenuStatus(false);
  },
  beforeMount() {
    this.setNavigationMenuStatus(false);
  }
};
