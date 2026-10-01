<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

/*
 * The content types the core does not have, each one a system element.
 *
 * The fields are the core's own wherever one fits: the header, the text, the
 * link, the media. A column is new where no core field means the thing: a
 * tone, a language, the words on a button, the entries of a set. The core
 * makes their database columns out of this file.
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
        /* The entries of a content type that holds a set, in one table for
           every such type. */
        'tx_themesoul_items' => [
            'label' => $labels . 'tt_content.tx_themesoul_items',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_themesoul_item',
                'foreign_field' => 'uid_foreign',
                'foreign_table_field' => 'tablename',
                'foreign_match_fields' => [
                    'fieldname' => 'tx_themesoul_items',
                ],
                'appearance' => [
                    'showSynchronizationLink' => false,
                    'showAllLocalizationLink' => true,
                    'showPossibleLocalizationRecords' => true,
                    'expandSingle' => true,
                    'useSortable' => true,
                ],
            ],
        ],
        'tx_themesoul_columns' => [
            'label' => $labels . 'tt_content.tx_themesoul_columns',
            'description' => $labels . 'tt_content.tx_themesoul_columns.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $labels . 'tt_content.tx_themesoul_columns.auto', 'value' => ''],
                    ['label' => '2', 'value' => '2'],
                    ['label' => '3', 'value' => '3'],
                    ['label' => '4', 'value' => '4'],
                ],
                'default' => '',
            ],
        ],
        'tx_themesoul_by' => [
            'label' => $labels . 'tt_content.tx_themesoul_by',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 100,
            ],
        ],
        'tx_themesoul_due' => [
            'label' => $labels . 'tt_content.tx_themesoul_due',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 100,
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
        'soul_accordion' => [
            'icon' => 'content-accordion',
            'fields' => 'header,tx_themesoul_items',
            'items' => 'header,text,marked',
        ],
        'soul_tabs' => [
            'icon' => 'content-tab',
            'fields' => 'header,tx_themesoul_items',
            'items' => 'header,icon,text,marked',
        ],
        'soul_steps' => [
            'icon' => 'content-bullets',
            'fields' => 'header,tx_themesoul_items',
            'items' => 'header,text,marked',
        ],
        'soul_timeline' => [
            'icon' => 'content-timeline',
            'fields' => 'header,tx_themesoul_items',
            'items' => 'label,header,text,marked',
        ],
        'soul_stats' => [
            'icon' => 'content-widget-number',
            'fields' => 'header,tx_themesoul_columns,tx_themesoul_items',
            'items' => 'value,unit,header,note,icon',
        ],
        'soul_surfaces' => [
            'icon' => 'content-panel',
            'fields' => 'header,tx_themesoul_columns,tx_themesoul_items',
            'items' => 'label,icon,header,text',
        ],
        'soul_cards' => [
            'icon' => 'content-card-group',
            'fields' => 'header,tx_themesoul_columns,tx_themesoul_items',
            'items' => 'header,text,link,link_label,media,label,value,icon,note',
        ],
        'soul_icons' => [
            'icon' => 'content-widget-list',
            'fields' => 'header,tx_themesoul_items',
            'items' => 'icon,header,link,label',
        ],
        'soul_facts' => [
            'icon' => 'content-listgroup',
            'fields' => 'header,tx_themesoul_items',
            'items' => 'header,text',
        ],
        'soul_register' => [
            'icon' => 'content-menu-section',
            'fields' => 'header,subheader,tx_themesoul_items',
            'items' => 'header,label,tone,text,note,value',
        ],
        'soul_decision' => [
            'icon' => 'content-idea',
            'fields' => 'header,subheader,bodytext,tx_themesoul_by,tx_themesoul_due,tx_themesoul_items',
            'body' => 'plain',
            'items' => 'value,header,text,marked,chosen',
        ],
    ];

    foreach ($types as $type => $definition) {
        $overrides = [
            'bodytext' => [
                'config' => match ($definition['body'] ?? 'plain') {
                    'rich' => ['enableRichtext' => true],
                    'code' => ['enableRichtext' => false, 'fixedFont' => true, 'rows' => 12, 'wrap' => 'off'],
                    default => ['enableRichtext' => false, 'rows' => 4],
                },
            ],
        ];
        /* A set shows the fields of an entry that this type reads, each
           under the name it has here: a question, a tab, a stop. */
        if (isset($definition['items'])) {
            $columns = [];
            foreach (explode(',', $definition['items']) as $field) {
                $columns[$field]['label'] = $labels . 'tx_themesoul_item.' . $type . '.' . $field;
            }
            $overrides['tx_themesoul_items'] = [
                'label' => $labels . 'tt_content.tx_themesoul_items.' . $type,
                'config' => [
                    'overrideChildTca' => [
                        'types' => ['0' => ['showitem' => $definition['items']]],
                        'columns' => $columns,
                    ],
                ],
            ];
        }
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
            ['columnsOverrides' => $overrides],
        );
    }

    /* The divider ends one band and opens the next on a page in bands. Its
       layout is the ground of the band it opens, and nothing else. */
    $GLOBALS['TCA']['tt_content']['types']['div']['columnsOverrides']['layout']['config']['items'] = [
        ['label' => $labels . 'tt_content.layout.div.plain', 'value' => '0'],
        ['label' => $labels . 'tt_content.layout.div.quiet', 'value' => '1'],
    ];
})();
