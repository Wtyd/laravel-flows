import fs from 'fs';
import Path from 'path';
import signale, { Signale } from 'signale';
import shell from 'shelljs';

shell.silent = true;

const CACHE_PATH = Path.resolve(__dirname, '../../../.cache');
let cacheRemoved = false;
let processKilled = false;

const removeDir = path => shell.rm('-rf', path);

if (fs.existsSync(CACHE_PATH)) {
  const interactive = new Signale({ interactive: true });

  interactive.pending('Eliminando .cache...');
  removeDir(CACHE_PATH);

  cacheRemoved = true;
  interactive.success('Cache eliminada.');
}

const output = shell
  .exec('ps ax | grep "node -r esm parcel.js"', { silent: true })
  .stdout.split('\n')
  .filter(line => line.indexOf('grep') === -1 && line.length > 0);

if (output.length > 0) {
  shell.exec("ps aux|grep 'parcel.js' |awk -F' ' '{print $2}'|xargs kill -9", { silent: true });
  processKilled = true;
  signale.success('Cerrado el servidor de front-end.');
}

if (!cacheRemoved && !processKilled)
  signale.note('No existe cache ni servidor de desarrollo iniciado.');
