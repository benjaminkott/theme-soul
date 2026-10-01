# Theme: Soul

The Soul design system as a TYPO3 theme. A site set with two page
layouts, the bar, the rail and the footer, and content elements that
emit the system's `sds-` elements. The pages link the drop-in of
`@typo3/soul-frontend`, which this package carries in
`Resources/Public/Soul/`.

**Status: experimental.** Names of settings, fields and types can still
change. TYPO3 14.3 is the floor.

## Install it

```sh
composer require typo3/theme-soul
```

Add the set to the site configuration:

```yaml
dependencies:
  - typo3/theme-soul
```

The set brings `typo3/fluid-styled-content` with it. Every core content
type renders through templates of this theme.

## Settings

All under **Site Management → Settings → Theme: Soul**, and all optional.

| Setting | What it does |
| --- | --- |
| `soul.product` | The name of the site in the bar and in the footer, verbatim |
| `soul.brand` | Who publishes the site, before the product, with the accent rule between the two |
| `soul.signet` | The mark, as a file: `EXT:my_site/Resources/Public/signet.svg` |
| `soul.note` | One sentence in the footer: what the product is, and who it is for |
| `soul.version` | The version of the product the site describes, in the footer |
| `soul.copyright` | Whose the content is and from when, on a line of its own in the footer |

## Page layouts

| Backend layout | Template | For |
| --- | --- | --- |
| *(none)* | `Pages/Default` | the same as `SoulPage` |
| `SoulPage` | `Pages/SoulPage` | a page that reports: one column beside the rail of its section, with the trail over it and the way on through the section under it |
| `SoulBands` | `Pages/SoulBands` | a page that argues: full-width bands |
| `SoulDocument` | `Pages/SoulDocument` | a long document on its own: the text beside its numbered outline, with no bar and no footer |

On a `SoulBands` page, a **divider** ends one band and starts the next.
The layout of the divider is the ground of the band it opens: plain or
quiet. The elements before the first divider are the first band.

The bar gets the page tree three levels deep, and the footer has a
column per section. A section that is one page has no rail.

The sections of a page are its content elements with a heading at the
second level or deeper. The outline of a document lists them, and so
does the core type **Section index** with no pages selected: as the
contents beside a column, or as pills across a band.

## Content types

The core's types keep their fields and render as the system's elements:
a table in `sds-table`, pages as `sds-card` in `sds-grid`, files as
`sds-figure`. Frames, spaces and alignment are off, because each one
needs a class the system does not define.

Every template sets an element by its attributes. Only the editor's rich
text stands between the tags, because an attribute cannot carry markup.
A list, a table or a tree goes over as JSON in an attribute.

| Type | Element | Fields |
| --- | --- | --- |
| `soul_hero` | `sds-eyebrow`, a display heading, `sds-button`, `sds-figure` | subheader, header, text, link, action label, one file |
| `soul_note` | `sds-note` | header, tone, rich text |
| `soul_code` | `sds-code` | header as the caption, language, code |
| `soul_quote` | `sds-quote` | text, header as the speaker, subheader as their role, link |
| `soul_accordion` | `sds-accordion`, `sds-accordion-item` | entries: question, answer, open at the start |
| `soul_tabs` | `sds-tabs`, `sds-tab-item` | entries: label, icon, panel, open at the start |
| `soul_steps` | `sds-steps`, `sds-step` | entries: step, what to do, optional |
| `soul_timeline` | `sds-timeline`, `sds-timeline-stop` | entries: when, heading, text, today stands here |
| `soul_stats` | `sds-stat` in `sds-grid` | columns; entries: figure, unit, what it counts, what bounds it, icon |
| `soul_surfaces` | `sds-surface` in `sds-grid` | columns; entries: line over the heading, icon, heading, statement |
| `soul_cards` | `sds-card` in `sds-grid` | columns; entries: heading, text, link, call to action, picture, line, tag, icon, footer |
| `soul_icons` | `sds-icon-tile` in `sds-grid` | entries: icon, caption, link, tag |
| `soul_facts` | `sds-facts` | entries: term, what it says |
| `soul_register` | `sds-register`, `sds-entry` | subheader as the number prefix; entries: heading, status, tone, text, still to do, origin |
| `soul_decision` | `sds-decision`, `sds-answer` | header as the question, subheader, lead, decided by, due; entries: key, answer, text, recommended, decided |
| `soul_compare` | `sds-compare` | header, two files: before and after, each with its title as the label and its description as the claim |
| `soul_embed` | `sds-embed` | header as what the frame holds, subheader as the caption, link as the address, shape, full screen |
| `soul_copy` | `sds-copy` | header as what the value is, subheader as the value |
| `soul_progress` | `sds-progress` | header as the job, value, maximum, subheader as the unit, text as the bound, running |
| `soul_diff` | `sds-diff` | header as the path, a unified diff |
| `soul_tree` | `sds-tree` | header, one entry per line, nested by indentation, a note after ` # ` |
| `soul_confval` | `sds-confval` | header as the name, subheader as the type, default, required, rich text |
| `soul_swatches` | `sds-swatch` in `sds-grid` | entries: value, name |
| `soul_run` | `sds-run` | header, subheader as the note, verdict; entries: step, state, meta, note, output |
| `soul_dialog` | `sds-button`, `sds-dialog` | button label, header as the heading, text |

The entries of a set are records of `tx_themesoul_item`, edited inline in
the content element. Each type shows the fields of an entry it reads,
under its own labels. The core bullet list with terms is `sds-facts` too.

The types and the fields `tx_themesoul_*` are off for every site and on
for a site that takes the set.

## Forms

Where the form framework is installed, the set takes it along, and its
forms render with the system's controls: `sds-field`, `sds-textarea`,
`sds-select`, `sds-radio`, `sds-checkbox`, `sds-checkbox-group` and
`sds-file`. What stopped a form stands at the top in `sds-form-errors`,
and each field says it again under itself. The confirmation is an
`sds-note`. `Configuration/Form/Soul/` registers the templates; they
apply to every form of the installation.

Each control writes a named native field into the page, and the name is
in the form's request token, as with the core's own fields. A file sent
before an error is not shown again. The way back to an earlier page of
the form is the class layer's button, because `sds-button` takes no name
and no value.

## Develop it

```sh
ddev start
npm --prefix Build ci && npm --prefix Build run build
ddev composer test
ddev composer cgl:ci
ddev composer phpstan
```

`Tests/Packages/theme_soul_fixtures` holds the forms the tests render,
and the development instance has it too.

`Build/` pins `@typo3/soul-frontend` and copies its drop-in to
`Resources/Public/Soul/`. The output is committed, because a Composer
project installs no npm package. The build fails if the languages
`soul_code` offers differ from the ones `sds-code` colours.

## License

MIT, see `LICENSE`.
