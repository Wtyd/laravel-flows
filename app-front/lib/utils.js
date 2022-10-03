import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc.js';
import 'dayjs/locale/es.js';
import numbro from 'numbro';

dayjs.extend(utc);

/**
 * dateFormat
 * @param {String} date La fecha a formatear.
 * @param {Object} options
 * @param {String} options.format El string de formateo: https://github.com/iamkun/dayjs/blob/dev/docs/en/API-reference.md#format-formatstringwithtokens-string
 * @param {String} options.lang El string de idioma. Por defecto: "es"
 * @param {Boolean} options.toUtc Pasar la fecha a UTC. Por defecto: false
 * @return {String}
 */
export const dateFormat = (date, options = {}) => {
  const { format, lang, toUtc } = options;

  if (!date) {
    return undefined;
  }

  const dateObject = toUtc ? dayjs(date).utc() : dayjs(date);

  if (!dateObject.isValid()) throw new Error('La fecha no es valida.');

  if (!format) {
    return dateObject.toISOString();
  }

  return dateObject.locale(lang || 'es').format(format);
};

/**
 * numberFormat
 * @param {Number} number El numero a formatear.
 * @param {Object} options
 * @param {String} options.thousandsSeparator El delimitador para miles.
 * @param {String} options.decimalsSeparator El delimitador para decimales.
 * @param {Number} options.decimals El numero de decimales permitido.
 * @return {String}
 */
export const numberFormat = (number, options = {}) => {
  const { thousandsSeparator, decimalsSeparator, decimals } = options;

  const decimalsSlug = Array(decimals === undefined ? 2 : decimals)
    .fill()
    .map(() => 0)
    .join('');
  const slug = `0,0.${decimalsSlug}`;
  let modified = numbro(number).format(slug);

  modified = modified
    .replace(/,/g, '[thousand]')
    .replace('.', '[decimal]')
    .replace(/\[thousand\]/g, thousandsSeparator || '.')
    .replace('[decimal]', decimalsSeparator || ',');

  return modified;
};

/**
 * Elimina acentos de una cadena de texto.
 * @param {String} string El texto a procesar.
 */
export const normalize = string => string.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
