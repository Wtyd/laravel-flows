<template>
  <div class="link" :class="{ active: link.active }">
    <!-- Contenedores, se muestra si el objeto link tiene hijos-->
    <div v-if="link.childs" class="link-with-childs">
      <div class="parent" :class="{ open, 'active-child': withActiveChild }" @click="toggleChilds">
        <i class="icon" :class="link.icon || 'fas fa-folder'"></i>
        <div class="meta">
          <p>{{ link.name }}</p>
        </div>
        <i
          class="fas dropdown-indicator"
          :class="{
            'fa-chevron-down': open,
            'fa-chevron-right': !open
          }"
        ></i>
      </div>
      <SlideUpDown :active="open" :duration="200">
        <router-link
          v-for="child in link.childs"
          ref="childs"
          :key="child.path"
          :to="child.path"
          class="child"
        >
          {{ child.name }}
        </router-link>
      </SlideUpDown>
    </div>

    <!-- Enlaces, se muestra si no el objeto no tiene hijos -->
    <router-link v-else class="link" :to="link.path">
      <i class="icon" :class="link.icon"></i>
      {{ link.name }}
    </router-link>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import SlideUpDown from 'vue-slide-up-down';

export default {
  components: {
    SlideUpDown
  },
  props: {
    link: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    open: false
  }),
  computed: {
    ...mapState('ui', ['actives']),
    withActiveChild() {
      const { childs } = this.link;
      const activePaths = Object.values(this.actives).map(active => active.path);
      return childs.some(child => activePaths.includes(child.path));
    }
  },
  watch: {
    'link.forceOpen': function(current) {
      const { link } = this;

      if (!link.childs) {
        return;
      }

      this.open = current === true;
    }
  },
  mounted() {
    this.open = this.link.forceOpen === true;
  },
  methods: {
    toggleChilds() {
      this.open = !this.open;
    }
  }
};
</script>

<style lang="scss">
.parent {
  .dropdown-indicator {
    // transition: transform 0.2s ease-in-out;
  }

  &.active-child {
    background: #536589;
    color: #fff;

    .icon,
    .meta p,
    .dropdown-indicator {
      color: #fff;
    }
  }

  // &.open {
  //   .dropdown-indicator {
  //     transform: rotate(90deg);
  //   }
  // }

  .meta {
    display: flex;
    flex: 1;
    flex-flow: row nowrap;

    p {
      margin: 0;
      max-width: 95%;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
  }
}
</style>
