/* eslint-disable no-new */
/* eslint-disable no-restricted-syntax */
/* eslint-disable no-await-in-loop */

const chalk = require('chalk');
const shelljs = require('shelljs');
const path = require('path');
const signale = require('signale');

const ROOT_PATH = path.resolve(__dirname, '../../');
const BIN_PATH = path.resolve(ROOT_PATH, 'node_modules/.bin');
const ESLINT_ALLOWED = ['js', 'vue'];

shelljs.config.silent = true;

signale.start('Ejecutando precommit...');
const interactive = new signale.Signale({ interactive: true });

// Obtener del stage los js, vue, scss y css
const getStagedFiles = () => {
  const result = shelljs.exec(
    `git diff --cached --name-only --diff-filter=ACM | grep ".js$\\|.vue$\\|.scss$\\|.css$"`
  );

  return result.trim().split('\n');
};

// Ejecutar comprobacion de prettier
async function runPrettier(files) {
  interactive.pending('Corrigiendo estilos con Prettier...');
  const cli = `${BIN_PATH}/prettier --config ${path.resolve(
    ROOT_PATH,
    '.prettierrc'
  )} --write "{${files.join(',')}}"`;

  shelljs.exec(cli);
}

// Ejecutar comprobacion de eslint
async function runEslint(files) {
  interactive.pending('Comprobando el codigo con ESLint...');
  const allowedFiles = files.filter(file => ESLINT_ALLOWED.includes(file.split('.').splice(-1)[0]));

  if (allowedFiles.length === 0) {
    interactive.success('No hay archivos a corregir.');
    return;
  }

  const cli =
    allowedFiles.length === 1
      ? `${BIN_PATH}/eslint "${allowedFiles[0]}" -f json --fix --cache`
      : `${BIN_PATH}/eslint "{${allowedFiles.join(',')}}" -f json --fix --cache`;

  const output = shelljs.exec(cli);
  const formatted = JSON.parse(output);

  const counts = formatted.reduce(
    (acc, file) => {
      acc.errors += file.errorCount;
      acc.fixable += file.fixableErrorCount;
      acc.warnings += file.warningCount + file.fixableWarningCount;
      return acc;
    },
    { errors: 0, fixable: 0, warnings: 0 }
  );

  interactive.info(chalk.bold('%d warnings y %d errores.'), counts.warnings, counts.errors, '\n');

  formatted.forEach(file => {
    file.messages.forEach(({ message, severity, line, column }) => {
      const location = `${file.filePath}:${line}:${column}`;
      const description = `${message.replace(/`"|"`/g, '"').replace(/`'|'`/g, "'")}`;
      signale[severity === 2 ? 'error' : 'warn'](location);
      signale.log(description, '\n');
    });
  });

  if (counts.errors > counts.fixable) {
    signale.note(chalk.bold('Corrige los errores antes de continuar con el commit.'), '\n');
    process.exit(1);
  }
}

// Arrancar precommit
async function main() {
  const stagedFiles = await getStagedFiles();
  await runPrettier(stagedFiles);
  await runEslint(stagedFiles);
}

main();
