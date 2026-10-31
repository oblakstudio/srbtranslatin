const fs = require('node:fs');
const path = require('node:path');

function validateDirectory(directory) {
  const required = [
    'srbtranslatin.php', 'src/App.php', 'config/settings.php',
    'vendor/autoload_packages.php', 'assets/blocks/script-selector/build/index.js',
    'assets/js/settings.js', 'languages/srbtranslatin-sr_RS.mo',
  ];
  for (const file of required) {
    if (!fs.statSync(path.join(directory, file)).isFile()) {
      throw new Error(`Missing runtime file: ${file}`);
    }
  }
  const forbidden = new Set(['DOCKER_ENV', 'output.log', '.git', '.env', 'node_modules', '.cache']);
  function walk(current) {
    for (const entry of fs.readdirSync(current, { withFileTypes: true })) {
      if (forbidden.has(entry.name) || entry.isSymbolicLink()) {
        throw new Error(`Unsafe release entry: ${path.relative(directory, path.join(current, entry.name))}`);
      }
      if (entry.isDirectory()) walk(path.join(current, entry.name));
    }
  }
  walk(directory);
  if (fs.existsSync(path.join(directory, 'vendor/phpunit'))) {
    throw new Error('Development dependencies must not be shipped.');
  }
}

module.exports = {
  validateDirectory,
  prepare(config) {
    validateDirectory(path.join(config.releasePath, config.slug));
  },
};
if (require.main === module) validateDirectory(process.argv[2]);
