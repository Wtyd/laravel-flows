import { fetchUser } from '$network/api.js';

export default {
  async fetchUser({ commit }) {
    const { data: user } = await fetchUser();
    commit('setUser', user);
  }
};
