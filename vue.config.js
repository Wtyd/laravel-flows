const Path = require('path');
const fs = require('fs');
const chalk = require('chalk');

const resolveLocal = path => Path.resolve(__dirname, path);

if (!fs.existsSync(resolveLocal('.env'))) {
  console.error(chalk.red('Create .env file before build front-end.'));
  process.exit(1);
}

const HMR_PORT = 1234;

// Namespaces
const namespaces = {
  $network: resolveLocal('./app-front/network/'),
  $components: resolveLocal('./app-front/components/'),
  $mixins: resolveLocal('./app-front/mixins'),
  $resources: resolveLocal('./app-front/resources'),
  $lib: resolveLocal('./app-front/lib/')
};

// Loaders
const yamlLoader = { test: /\.yaml$/, use: 'js-yaml-loader' };
const globJs = { test: /\.js/, loader: 'import-glob' };
const globScss = { test: /\.scss/, loader: 'import-glob' };

module.exports = {
  publicPath: '/dist',
  outputDir: resolveLocal('./public/dist'), // Donde se generara la APP
  filenameHashing: true,
  lintOnSave: true,
  productionSourceMap: false,
  configureWebpack: {
    resolve: {
      alias: namespaces
    },
    module: {
      rules: [globJs, globScss, yamlLoader]
    },
    devServer: {
      public: `http://localhost:${HMR_PORT}/dist/`,
      port: HMR_PORT
    }
  },
  chainWebpack: config => {
    // Deshabilitar la copia de public
    config.plugin('copy').tap(() => []);

    // Usar un index.html propio para la generacion
    config
      .plugin('html')
      .tap(([options]) => [{ ...options, template: resolveLocal('./app-front/index.html') }]);
  }
};
