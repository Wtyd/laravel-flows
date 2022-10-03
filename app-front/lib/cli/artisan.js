/* eslint-disable global-require */
/* eslint-disable import/no-dynamic-require */

import inquirer from 'inquirer';
import signale from 'signale';
import path from 'path';
import fs from 'fs';
import Mustache from 'mustache';
import glob from 'glob';
import shelljs from 'shelljs';

const PAGES_PATH = path.resolve(__dirname, '../../components/pages');

const asyncInquirer = question =>
  new Promise((resolve, reject) =>
    inquirer
      .prompt(question)
      .then(answer => resolve(Object.values(answer)[0]))
      .catch(reject)
  );

const getRoutes = () =>
  new Promise((resolve, reject) => {
    glob(`${PAGES_PATH}/**/route.js`, (error, matches) =>
      error ? reject(error) : resolve(matches.map(file => require(file).default))
    );
  });

async function main() {
  const action = await asyncInquirer({
    type: 'list',
    name: 'Elige lo que quieras hacer',
    choices: ['Crear una vista', 'Simular precommit']
  });

  if (action === 'Simular precommit') {
    await shelljs.exec('yarn qa --color', {
      async: true,
      encoding: 'utf8'
    });
    return;
  }

  const component = await asyncInquirer({
    type: 'input',
    name: 'Nombre del componente',
    validate(input) {
      if (input.length === 0) {
        return 'No puede estar vacio.';
      }

      const valid = /^([A-Z][a-z0-9]+)+$/.test(input);

      if (!valid) {
        return 'El nombre del componente debe ser en UpperCamelCase.';
      }

      return true;
    }
  });

  const splitted = component.match(/[A-Z][a-z]+/g);

  if (splitted.slice(-1)[0] === 'Page') {
    splitted.splice(splitted.length - 1, 1);
  }

  const file = `${splitted.map(string => string.toLowerCase()).join('-')}-page.vue`;
  const folder = `${splitted.map(string => string.toLowerCase()).join('-')}`;
  const folderPath = path.resolve(__dirname, '../../components/pages/', `${folder}/`);
  const fullPath = path.resolve(__dirname, '../../components/pages/', `${folder}/${file}`);
  const defaultRoute = `/${splitted.map(string => string.toLowerCase()).join('-')}`;

  const route = await asyncInquirer({
    type: 'input',
    name: `Ruta de la vista`,
    default: defaultRoute,
    async validate(input) {
      const valid = /([a-z\\/-]+)$/.test(input);

      if (!valid) {
        return 'El formato no es valido. Solo se admiten barras (/), letras en minuscula y/o guiones.';
      }

      const routeConfigs = await getRoutes();
      const paths = routeConfigs.map(config => config.path);

      if (paths.includes(input)) {
        return 'Esa ruta ya existe.';
      }

      if (input.length === 0) {
        return 'No puede estar vacio.';
      }

      return true;
    }
  });

  const folderExists = fs.existsSync(folderPath);
  let folderOverride = '';

  if (folderExists) {
    folderOverride = await asyncInquirer({
      type: 'input',
      name: `La carpeta /app-front/components/${folder}/ ya existe, usa otro nombre`,
      validate(input) {
        if (input.length === 0) {
          return 'No puede estar vacio.';
        }

        if (fs.existsSync(path.resolve(__dirname, '../../components/pages', input))) {
          return 'Esa carpeta tambien existe.';
        }

        return true;
      }
    });

    folderOverride = path.resolve(__dirname, '../../components/pages', folderOverride);
  }

  const data = {
    file,
    route: route.replace(/\/\//g, '/'),
    splitted: splitted.join(' '),
    componentName: `${component}Page`,
    folder: folderExists ? folderOverride : folderPath,
    fullPath: folderExists ? path.resolve(__dirname, folderOverride, file) : fullPath,
    routeFullPath: path.resolve(__dirname, folderExists ? folderOverride : folderPath, 'route.js')
  };

  const componentTemplate = fs.readFileSync(path.resolve(__dirname, './component-template.vue'), {
    encoding: 'utf8'
  });
  const routeTemplate = fs.readFileSync(path.resolve(__dirname, './route-template.js'), {
    encoding: 'utf8'
  });
  const componentRendered = Mustache.render(componentTemplate, data);
  const routeRendered = Mustache.render(routeTemplate, data);

  fs.mkdirSync(data.folder);
  fs.writeFileSync(data.fullPath, componentRendered, { encoding: 'utf8' });
  fs.writeFileSync(data.routeFullPath, routeRendered, { encoding: 'utf8' });

  signale.success('Vista creada en', data.fullPath);
  signale.success('Ruta creada en', data.routeFullPath);
}

main();
