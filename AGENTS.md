# AGENTS.md

## Rules

* No persistent agent memory for this project. Source of truth: this
  file, the checkout, the git history.
* Never write version facts into this file. Read them from
  `composer.json`, `Build/package.json`, `.ddev/config.yaml` and
  `.github/workflows/ci.yml`.
* Don't guess TYPO3 APIs, TCA keys, icons or labels: read
  `.build/vendor/typo3/`.
* Only report checks you actually ran.
* Everything that serves development only is `export-ignore`d in
  `.gitattributes`.
* Every text is English: code, comments, labels, documents, commits.

## What this is

* A theme: the site set `typo3/theme-soul` in `Configuration/Sets/soul/`.
* Templates emit the `sds-` elements of the Soul design system. A
  template writes no class the Soul stylesheets do not define, and never
  a part of an element (`sds-x__y`): address the element, let it draw.
* A template sets an element by its attributes, and writes nothing else
  it can avoid. Between the tags goes only what an attribute cannot
  carry: the editor's rich text, a player.
* Write the attributes with `{soul:attributes(of: {...})}`, never by
  hand. A list or a map goes over as JSON. A field an editor fills is
  plain text where it ends in an attribute.
* Data an element needs in a shape no field has comes from a processor
  in `Classes/DataProcessing/`, not from a loop in a template.
* An element has no frame: `.sds-band` and `.sds-body__main` trim their
  first and last child, and a wrapper would be that child.

## Commands

* `ddev start` · `ddev composer …`
* `ddev composer test` — lint and functional tests on SQLite
* `ddev composer cgl:ci` (check) · `ddev composer cgl` (rewrites)
* `ddev composer phpstan`
* `npm --prefix Build ci && npm --prefix Build run build` — the drop-in

## Frontend build

* `Build/package.json` pins `@typo3/soul-frontend`.
* Output is committed: `Resources/Public/Soul/`. A version change is the
  pin, the lock and the output in one commit; CI fails on a dirty tree.

## Tests

* `Tests/Functional`: a site on the set and nothing else, with the page
  tree of `Tests/Functional/Fixtures/site.csv`.
* A change to a template comes with a test of the markup it writes.
