<template>
  <div class="navigation-menu-user">
    <div class="main">
      <div class="avatar">
        <img :src="user.avatar" />
      </div>
      <div class="info">
        <p class="name">{{ user.name }}</p>
        <p class="description">{{ user.description || 'Sin descripción' }}</p>
      </div>
    </div>
    <div class="actions">
      <FloatingInteractive ref="floatingInteractive">
        <button>
          <i class="fas fa-ellipsis-v dusk-menu-tres-puntos-verticales"></i>
        </button>

        <template v-slot:content>
          <ul class="contextual-menu">
            <li>
              <button class="disable-menu-close" @click="logout">Cerrar sesión</button>
            </li>
          </ul>
        </template>
      </FloatingInteractive>
    </div>
  </div>
</template>

<script>
import { mapMutations } from 'vuex';
import * as api from '$network/api.js';
import FloatingInteractive from '$components/ui/floating-interactive.vue';

export default {
  components: {
    FloatingInteractive
  },
  props: {
    user: {
      type: Object,
      required: true
    }
  },
  methods: {
    ...mapMutations('auth', ['setLoggedStatus']),
    async logout() {
      const { $refs } = this;
      const { floatingInteractive } = $refs;

      floatingInteractive.close();
      await api.logout();
      window.localStorage.removeItem('z-tabs-sync');
      window.location.reload();
    }
  }
};
</script>

<style lang="scss" scoped>
.navigation-menu-user {
  align-items: center;
  background: #475170;
  color: #fff;
  display: flex;
  flex-flow: row nowrap;
  justify-content: space-between;
  padding: 15px;

  .main {
    display: flex;
    flex-flow: row nowrap;

    .avatar {
      height: 30px;
      width: 30px;

      img {
        border: 1px solid #f2f4f7;
        border-radius: 30px;
        height: 30px;
        object-fit: cover;
        width: 30px;
      }
    }

    .info {
      padding-left: 13px;

      .name {
        font-size: 16px;
        font-weight: 500;
        margin: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        width: 108px;
      }

      .description {
        font-size: 12px;
        font-weight: 300;
        margin: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        width: 133px;
      }
    }
  }

  .actions {
    button {
      background: none;
      border: 0;
      color: #fff;
      cursor: pointer;
      margin-right: -5px;
      outline: none;
      padding: 10px;
    }
  }
}
</style>
