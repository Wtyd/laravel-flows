<template>
  <div class="login">
    <div class="logo">
      <img :src="logo" alt="Neuro360" class="logo" />
    </div>
    <div class="form">
      <ViuLabel class="mb-20">
        <template #label
          >Usuario <span class="sub-label">(Correo electrónico o teléfono)</span></template
        >
        <ViuInput v-model="user" @enter="login" />
      </ViuLabel>
      <ViuLabel class="mb-20">
        <template #label> Contraseña </template>
        <ViuInput v-model="password" :type="'password'" @enter="login" />
      </ViuLabel>
      <ViuButton :disabled="sending" @click.native="login"
        ><template #text>
          <span v-if="!sending">Login</span>
          <span v-if="sending">Login...</span>
        </template></ViuButton
      >
    </div>
  </div>
</template>

<script>
import { mapMutations } from 'vuex';
import ViuLabel from 'viu/components/viu-label/viu-label.vue';
import ViuInput from 'viu/components/viu-input/viu-input.vue';
import ViuButton from 'viu/components/viu-button/viu-button.vue';
import errorsMixin from '$mixins/errors-mixin.js';
import logo from '$resources/images/logo.png';
import * as api from '$network/api.js';

export default {
  name: 'Login',
  components: {
    ViuLabel,
    ViuInput,
    ViuButton
  },
  mixins: [errorsMixin],
  data: () => ({
    user: '',
    password: '',
    logo,
    sending: false
  }),
  beforeMount() {
    this.setNavigationMenuStatus(false);
  },
  methods: {
    ...mapMutations('ui', ['setNavigationMenuStatus']),
    ...mapMutations('auth', ['setLoggedStatus']),
    ...mapMutations('common', ['setUser']),
    async login() {
      const { user, password, setLoggedStatus, setUser } = this;
      if (this.sending) return;

      if (user.length === 0 || password.length === 0) {
        this.$notify.error('El usuario o la contraseña estan vacios.');
        return;
      }

      this.sending = true;

      await api.verifySanctum();

      try {
        const response = await api.login({ identity: user, password });
        setUser(response.data.data);
        setLoggedStatus(true);
        this.$router.push('/');
      } catch (error) {
        this.sending = false;
        if (error.response.status === 400) {
          this.showErrors(error, 'Ha ocurrido un error inesperado');
          // this.$router.push('/');
          return;
        }

        if (error.response.status === 429) {
          this.$notify.error(
            'Demasiados intentos de login consecutivos. Espere 1 minuto o contacte con el administrador'
          );
          return;
        }

        this.ShowErrors(error, 'Ha ocurrido un error inesperado');
      }
    }
  }
};
</script>

<style lang="scss">
.login {
  align-items: center;
  display: flex;
  flex-flow: column;
  justify-content: center;
  min-height: 100vh;

  .logo,
  .form {
    width: 292px;
  }

  .logo {
    img {
      margin-top: -165px;
    }
  }

  .mb-20 {
    margin-bottom: 20px;
  }

  .form {
    margin-top: 20px;

    .viu-label .label {
      font-size: 13px;

      .sub-label {
        font-size: 12px;
        font-style: italic;
        font-weight: 400;
        opacity: 0.4;
      }
    }

    .viu-input {
      width: 100%;

      input:-webkit-autofill {
        box-shadow: 200px 200px 100px #fff inset;
      }
    }

    .flex {
      display: flex;
      justify-content: space-between;
    }
  }

  .enlace {
    a {
      color: #555;
      font-weight: 100;
      text-decoration: none;
    }
  }
}
</style>
