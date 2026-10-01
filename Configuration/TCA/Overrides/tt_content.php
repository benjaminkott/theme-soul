<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

/*
 * The content types the core does not have, each one a system element.
 *
 * The fields are the core's own wherever one fits: the header, the text, the
 * link, the media. Three columns are new, because no core field means a
 * tone, a language or the words on a button. The core makes their database
 * columns out of this file.
 */
(static function (): void {
    $labels = 'theme_soul.backend_fields:';

    ExtensionManagementUtility::addTCAcolumns('tt_content', [
        'tx_themesoul_tone' => [
            'label' => $labels . 'tt_content.tx_themesoul_tone',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $labels . 'tt_content.tx_themesoul_tone.info', 'value' => 'info'],
                    ['label' => $labels . 'tt_content.tx_themesoul_tone.ok', 'value' => 'ok'],
                    ['label' => $labels . 'tt_content.tx_themesoul_tone.warn', 'value' => 'warn'],
                    ['label' => $labels . 'tt_content.tx_themesoul_tone.error', 'value' => 'error'],
                ],
                'default' => 'info',
            ],
        ],
        /* The names `sds-code` colours. `npm run build` in Build/ holds
           the list below against `CodeLangName` of the npm package. */
        'tx_themesoul_code_lang' => [
            'label' => $labels . 'tt_content.tx_themesoul_code_lang',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => array_map(
                    static fn(string $lang): array => ['label' => $lang, 'value' => $lang],
                    // CodeLangName
                    ['bash', 'css', 'diff', 'html', 'javascript', 'json', 'markdown', 'php', 'scss', 'sql', 'text', 'tsconfig', 'twig', 'typescript', 'typoscript', 'xml', 'yaml'],
                ),
                'default' => 'text',
            ],
        ],
        'tx_themesoul_action' => [
            'label' => $labels . 'tt_content.tx_themesoul_action',
            'description' => $labels . 'tt_content.tx_themesoul_action.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 60,
            ],
        ],
    ]);

    ExtensionManagementUtility::addTcaSelectItemGroup('tt_content', 'CType', 'soul', $labels . 'group.soul', 'after:default');

    $types = [
        'soul_hero' => [
            'icon' => 'content-header',
            'fields' => '--palette--;;headers,bodytext,tx_themesoul_action,assets',
            'body' => 'plain',
        ],
        'soul_note' => [
            'icon' => 'content-info',
            'fields' => 'header,tx_themesoul_tone,bodytext',
            'body' => 'rich',
        ],
        'soul_code' => [
            'icon' => 'content-special-html',
            'fields' => 'header,tx_themesoul_code_lang,bodytext',
            'body' => 'code',
        ],
        'soul_quote' => [
            'icon' => 'content-quote',
            'fields' => 'bodytext,header,subheader,header_link',
            'body' => 'plain',
        ],
    ];

    foreach ($types as $type => $definition) {
        ExtensionManagementUtility::addRecordType(
            [
                'label' => $labels . 'tt_content.CType.' . $type,
                'description' => $labels . 'tt_content.CType.' . $type . '.description',
                'value' => $type,
                'icon' => $definition['icon'],
                'group' => 'soul',
            ],
            '--div--;core.form.tabs:general,--palette--;;general,' . $definition['fields']
                . ',--div--;core.form.tabs:access,--palette--;;hidden,--palette--;;access',
            [
                'columnsOverrides' => [
                    'bodytext' => [
                        'config' => match ($definition['body']) {
                            'rich' => ['enableRichtext' => true],
                            'code' => ['enableRichtext' => false, 'fixedFont' => true, 'rows' => 12, 'wrap' => 'off'],
                            default => ['enableRichtext' => false, 'rows' => 4],
                        },
                    ],
                ],
            ],
        );
    }

    /* The divider ends one band and opens the next on a page in bands. Its
       layout is the ground of the band it opens, and nothing else. */
    $GLOBALS['TCA']['tt_content']['types']['div']['columnsOverrides']['layout']['config']['items'] = [
        ['label' => $labels . 'tt_content.layout.div.plain', 'value' => '0'],
        ['label' => $labels . 'tt_content.layout.div.quiet', 'value' => '1'],
    ];
})();
