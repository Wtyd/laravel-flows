// Aqui estan todas las peticiones al backend.
// Se encapsulan aqui para agilizar el uso de interceptors, compartir cabeceras, etc.

// Aqui no debe haber logica. Es un puente entre el cliente y el back-end.

import axios from 'axios';
import errorNotifications from '$lib/axios-interceptors/error-notifications-interceptor.js';
import { injectHeader, storeToken } from '$lib/axios-interceptors/dev-token-support-interceptor.js';

axios.defaults.withCredentials = true;

const { VUE_APP_URL } = process.env;

if (process.env.NODE_ENV === 'development') {
  injectHeader(axios);
  storeToken(axios);
}

errorNotifications(axios);

export const verifySanctum = () => axios.get(`${VUE_APP_URL}/sanctum/csrf-cookie`);
export const login = body => axios.post(`${VUE_APP_URL}/api/login`, body);
export const logout = () => axios.post(`${VUE_APP_URL}/api/logout`);
export const fetchUser = () => axios.get(`${VUE_APP_URL}/api/user`);
