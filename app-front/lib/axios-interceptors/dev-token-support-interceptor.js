export const injectHeader = axios => {
  axios.interceptors.request.use(
    config => {
      const storedToken = window.localStorage.getItem('csrf.token');

      if (!storedToken) return config;

      const { headers: originalHeaders } = config;
      const headers = { ...originalHeaders, Authorization: `Bearer ${storedToken}` };
      return { ...config, headers };
    },
    error => Promise.reject(error)
  );
};

export const storeToken = axios => {
  axios.interceptors.response.use(
    response => {
      const { config } = response;

      if (config.url.indexOf('/api/login') !== -1) {
        const token = document.cookie.match(new RegExp('(^| )XSRF-TOKEN=([^;]+)'));
        if (token) {
          window.localStorage.setItem('csrf.token', token[2]);
        }
        return response;
      }

      if (config.url.indexOf('/api/logout') !== -1 || response.status === 401) {
        window.localStorage.removeItem('csrf.token');
        window.location.reload();
        return response;
      }

      return response;
    },
    error => Promise.reject(error)
  );
};
