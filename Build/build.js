/*
 * The drop-in of `@typo3/soul-frontend`, copied to Resources/Public/Soul/.
 *
 * Committed, because a Composer project installs no npm package. The files
 * keep their places beside each other: the stylesheet finds its faces
 * under `fonts/`, and the script finds its icons from its own address.
 * The directory is replaced whole, so a file the package drops goes too.
 */
import { cpSync, existsSync, mkdirSync, readFileSync, rmSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const from = join(here, 'node_modules', '@typo3', 'soul-frontend', 'dist');
const into = join(here, '..', 'Resources', 'Public', 'Soul');

const FILES = ['soul.css', 'soul.js', 'soul-boot.js', 'fonts', 'assets/icons'];

if (!existsSync(from)) {
    console.error(`${from} is not there - run "npm ci" in Build/ first`);
    process.exit(1);
}

rmSync(into, { recursive: true, force: true });
for (const path of FILES) {
    mkdirSync(dirname(join(into, path)), { recursive: true });
    cpSync(join(from, path), join(into, path), { recursive: true });
}
console.log(`Resources/Public/Soul/ holds ${FILES.join(', ')}`);

/* The languages an editor can pick for a code block, against the ones the
   block colours. A name only one side knows is a block that stays grey. */
const union = readFileSync(join(from, 'types', 'src', 'components', 'code.d.ts'), 'utf8')
    .match(/type CodeLangName = ([^;]+);/)?.[1] ?? '';
const colours = [...union.matchAll(/'([a-z0-9-]+)'/g)].map((m) => m[1]).sort();
const tca = readFileSync(join(here, '..', 'Configuration', 'TCA', 'Overrides', 'tt_content.php'), 'utf8')
    .match(/\/\/ CodeLangName\s*\n\s*\[([^\]]+)\]/)?.[1] ?? '';
const offered = [...tca.matchAll(/'([a-z0-9-]+)'/g)].map((m) => m[1]).sort();
if (colours.length === 0 || colours.join() !== offered.join()) {
    console.error(`tx_themesoul_code_lang offers ${offered.join(', ')}`);
    console.error(`sds-code colours ${colours.join(', ')}`);
    process.exit(1);
}
