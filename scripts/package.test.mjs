import assert from 'node:assert/strict';
import fs from 'node:fs';
import os from 'node:os';
import path from 'node:path';
import { test } from 'node:test';
import { glob } from 'glob';
import { getIgnore, getInclude } from '@semantic-release/wordpress/dist/utils/copy-files.js';

for (const allowlist of [true, false]) {
  test(`release excludes sensitive artifacts with allowlist=${allowlist}`, async (t) => {
    const directory = fs.mkdtempSync(path.join(os.tmpdir(), 'stl-package-'));
    t.after(() => fs.rmSync(directory, {recursive: true, force: true}));
    fs.copyFileSync('.distignore', path.join(directory, '.distignore'));
    if (allowlist) fs.copyFileSync('.distinclude', path.join(directory, '.distinclude'));
    fs.mkdirSync(path.join(directory, 'src'));
    fs.writeFileSync(path.join(directory, 'src/App.php'), '<?php // fixture');
    for (const file of ['srbtranslatin.php', 'DOCKER_ENV', 'output.log']) {
      fs.writeFileSync(path.join(directory, file), 'fixture');
    }
    const entries = await glob(getInclude(directory), {
      cwd: directory, ignore: getIgnore(directory), nodir: true,
    });
    assert(entries.includes('src/App.php'));
    assert(entries.includes('srbtranslatin.php'));
    assert(!entries.includes('DOCKER_ENV'));
    assert(!entries.includes('output.log'));
  });
}

const { validateDirectory } = await import('./validate-package.cjs').then(m => m.default);
function stagedFixture(t) {
  const directory = fs.mkdtempSync(path.join(os.tmpdir(), 'stl-release-'));
  t.after(() => fs.rmSync(directory, {recursive: true, force: true}));
  for (const file of ['srbtranslatin.php', 'src/App.php', 'config/settings.php', 'vendor/autoload_packages.php', 'assets/blocks/script-selector/build/index.js', 'assets/js/settings.js', 'languages/srbtranslatin-sr_RS.mo']) {
    fs.mkdirSync(path.dirname(path.join(directory,file)), {recursive:true});
    fs.writeFileSync(path.join(directory,file), 'fixture');
  }
  return directory;
}
test('complete production runtime passes the release guard', t => {
  assert.doesNotThrow(() => validateDirectory(stagedFixture(t)));
});
test('missing runtime classes block a release', t => {
  const directory = stagedFixture(t);
  fs.rmSync(path.join(directory,'src/App.php'));
  assert.throws(() => validateDirectory(directory));
});
test('nested build artifacts block a release', t => {
  const directory = stagedFixture(t);
  fs.writeFileSync(path.join(directory,'vendor/DOCKER_ENV'), 'fixture');
  assert.throws(() => validateDirectory(directory), /Unsafe release entry/);
});
test('symlink entries block a release', t => {
  const directory = stagedFixture(t);
  fs.symlinkSync('srbtranslatin.php', path.join(directory,'linked.php'));
  assert.throws(() => validateDirectory(directory), /Unsafe release entry/);
});
