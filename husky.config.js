const tasks = arr => arr.join(' && ');

module.exports = {
  hooks: {
    'pre-commit': tasks([
      'node ./app-front/qa/precommit.js',
      'php ./vendor/wtyd/githooks/hooks/default.php'
    ])
  }
};
