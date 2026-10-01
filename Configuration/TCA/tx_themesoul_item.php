<?php

declare(strict_types=1);

/*
 * One entry of a content element that holds a set: a question of an
 * accordion, a tab, a step, a stop on a timeline, a figure, a card.
 *
 * The fields carry no meaning of their own. Each content type shows the
 * ones it reads, under its own labels, through `overrideChildTca`. One
 * table keeps the translation, the workspace and the sorting the same for
 * every set.
 */
$labels = 'theme_soul.backend_fields:';

return [
    'ctrl' => [
        'title' => $labels . 'tx_themesoul_item',
        'label' => 'header',
        'label_alt' => 'label,value',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'hideTable' => true,
        'sortby' => 'sorting_foreign',
        'delete' => 'deleted',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource' => 'l10n_source',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'typeicon_classes' => ['default' => 'content-listgroup'],
        'security' => [
            'ignoreWebMountRestriction' => true,
            'ignoreRootLevelRestriction' => true,
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'columns' => [
        'fieldname' => [
            'config' => [
                'type' => 'input',
            ],
        ],
        'header' => [
            'l10n_mode' => 'prefixLangTitle',
            'label' => $labels . 'tx_themesoul_item.header',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
            ],
        ],
        'text' => [
            'l10n_mode' => 'prefixLangTitle',
            'label' => $labels . 'tx_themesoul_item.text',
            'config' => [
                'type' => 'text',
                'cols' => 50,
                'rows' => 6,
                'enableRichtext' => true,
                'softref' => 'typolink_tag,email[subst],url',
            ],
        ],
        'label' => [
            'label' => $labels . 'tx_themesoul_item.label',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 100,
            ],
        ],
        'value' => [
            'label' => $labels . 'tx_themesoul_item.value',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'max' => 100,
            ],
        ],
        'unit' => [
            'label' => $labels . 'tx_themesoul_item.unit',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'max' => 20,
            ],
        ],
        'note' => [
            'label' => $labels . 'tx_themesoul_item.note',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
            ],
        ],
        'link' => [
            'label' => $labels . 'tx_themesoul_item.link',
            'config' => [
                'type' => 'link',
                'size' => 50,
            ],
        ],
        'link_label' => [
            'label' => $labels . 'tx_themesoul_item.link_label',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 60,
            ],
        ],
        'icon' => [
            'label' => $labels . 'tx_themesoul_item.icon',
            'description' => $labels . 'tx_themesoul_item.icon.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 100,
            ],
        ],
        'media' => [
            'label' => $labels . 'tx_themesoul_item.media',
            'config' => [
                'type' => 'file',
                'allowed' => ['common-image-types'],
                'maxitems' => 1,
            ],
        ],
        'marked' => [
            'label' => $labels . 'tx_themesoul_item.marked',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'chosen' => [
            'label' => $labels . 'tx_themesoul_item.chosen',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'tone' => [
            'label' => $labels . 'tx_themesoul_item.tone',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $labels . 'tone.none', 'value' => ''],
                    ['label' => $labels . 'tone.accent', 'value' => 'accent'],
                    ['label' => $labels . 'tone.ok', 'value' => 'ok'],
                    ['label' => $labels . 'tone.warn', 'value' => 'warn'],
                    ['label' => $labels . 'tone.error', 'value' => 'error'],
                ],
                'default' => '',
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'header',
        ],
    ],
];
