/* eslint-disable import/no-unresolved */
import routes from '../components/pages/*/route.js';

const list = Object.values(routes).map(route => route.default);

export default list;
