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
| `SoulPage` | `Pages/SoulPage` | a page that reports: one column beside the rail of its section, with the trail over it |
| `SoulBands` | `Pages/SoulBands` | a page that argues: full-width bands |

On a `SoulBands` page, a **divider** ends one band and starts the next.
The layout of the divider is the ground of the band it opens: plain or
quiet. The elements before the first divider are the first band.

The bar gets the page tree three levels deep, and the footer has a
column per section. A section that is one page has no rail.

## Content types

The core's types keep their fields and render as the system's elements:
a table in `sds-table`, pages as `sds-card` in `sds-grid`, files as
`sds-figure`. Frames, spaces and alignment are off, because each one
needs a class the system does not define.

| Type | Element | Fields |
| --- | --- | --- |
| `soul_hero` | `sds-eyebrow`, a display heading, `sds-button`, `sds-figure` | subheader, header, text, link, action label, one file |
| `soul_note` | `sds-note` | header, tone, rich text |
| `soul_code` | `sds-code` | header as the caption, language, code |
| `soul_quote` | `sds-quote` | text, header as the speaker, subheader as their role, link |

The types and the fields `tx_themesoul_*` are off for every site and on
for a site that takes the set.

## Develop it

```sh
ddev start
npm --prefix Build ci && npm --prefix Build run build
ddev composer test
ddev composer cgl:ci
ddev composer phpstan
```

`Build/` pins `@typo3/soul-frontend` and copies its drop-in to
`Resources/Public/Soul/`. The output is committed, because a Composer
project installs no npm package. The build fails if the languages
`soul_code` offers differ from the ones `sds-code` colours.

## License

MIT, see `LICENSE`.
