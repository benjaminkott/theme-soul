/*
 * Every element of the design system has a place in the theme.
 *
 * The elements come from the package's catalogue. An element counts where
 * a template writes it, and where an element a template writes draws it in
 * turn, read out of the package's sources. An element that stays out does so
 * by name, with the reason, and the check fails the day it turns up after all.
 */
import { existsSync, readdirSync, readFileSync, statSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const pkg = join(here, 'node_modules', '@typo3', 'soul-frontend');
const templates = join(here, '..', 'Resources', 'Private');

const ELSEWHERE = {
    'sds-modal': 'the surface of a dialog alone; a page opens a dialog, and that is sds-dialog',
    'sds-nav-pagination': 'a list in pages needs a route per page, and the theme has no list of that kind',
};

const walk = (dir, ext) => readdirSync(dir).flatMap((name) => {
    const path = join(dir, name);
    if (statSync(path).isDirectory()) return walk(path, ext);
    return path.endsWith(ext) ? [path] : [];
});

const catalogue = JSON.parse(readFileSync(join(pkg, 'dist', 'custom-elements.json'), 'utf8'));
const tags = catalogue.modules.flatMap((m) => m.declarations ?? []).map((d) => d.tagName).filter(Boolean).sort();

/* What each element draws, out of its source and the helpers it imports.
   Comments go first: they name other elements in prose. */
const draws = new Map();
const components = join(pkg, 'src', 'components');
for (const file of walk(components, '.ts')) {
    const source = readFileSync(file, 'utf8');
    const tag = source.match(/define\('(sds-[a-z-]+)'/)?.[1];
    if (!tag) continue;
    const helpers = [...source.matchAll(/from '\.\.\/lib\/([\w.-]+)'/g)]
        .map((m) => join(pkg, 'src', 'lib', m[1]))
        .filter(existsSync)
        .map((path) => readFileSync(path, 'utf8'));
    const markup = [source, ...helpers].join('\n').replace(/\/\*[\s\S]*?\*\//g, '').replace(/\/\/.*$/gm, '');
    draws.set(tag, [...new Set([...markup.matchAll(/<(sds-[a-z-]+)/g)].map((m) => m[1]))]);
}

const written = new Set();
for (const file of walk(templates, '.html')) {
    const source = readFileSync(file, 'utf8').replace(/<f:comment>[\s\S]*?<\/f:comment>/g, '');
    /* A tag, or an element named in a string: `tag: 'sds-field'`, or a
       variable set to one. A class name in quotes is not an element. */
    for (const m of source.matchAll(/<(sds-[a-z-]+)|(?:tag:\s*|value=)['"](sds-[a-z-]+)['"]/g)) written.add(m[1] ?? m[2]);
}

const reached = new Set();
const follow = (tag) => {
    if (reached.has(tag)) return;
    reached.add(tag);
    for (const next of draws.get(tag) ?? []) follow(next);
};
for (const tag of written) follow(tag);

const problems = [];
for (const tag of tags) {
    if (tag in ELSEWHERE) {
        if (reached.has(tag)) problems.push(`${tag} has a place now; take it out of ELSEWHERE in Build/coverage.js`);
    } else if (!reached.has(tag)) {
        problems.push(`${tag} has no place: no template writes it, and no element a template writes draws it`);
    }
}
for (const tag of written) {
    if (!tags.includes(tag)) problems.push(`a template writes ${tag}, which the design system does not have`);
}

if (problems.length) {
    for (const line of problems) console.error(line);
    process.exit(1);
}
const covered = tags.filter((tag) => reached.has(tag)).length;
console.log(`${covered} of ${tags.length} elements have a place; ${Object.keys(ELSEWHERE).join(' and ')} stay out by name`);
