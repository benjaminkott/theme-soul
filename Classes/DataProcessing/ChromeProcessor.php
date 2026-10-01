<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * What the bar, the rail, the trail and the footer get, out of the core menus.
 *
 * The core processors resolve the tree, the links and the page the reader is
 * on. The elements take that as data in their own shape, so this only renames
 * and slices. One menu feeds the bar, the rail and the footer, and so the
 * three cannot disagree about where the reader is.
 *
 *   dataProcessing.50 = soul-chrome
 *   dataProcessing.50.product = {$soul.product}
 */
final class ChromeProcessor implements DataProcessorInterface
{
    /** A page that only links somewhere else, in the core's numbering. */
    private const DOKTYPE_LINK = 3;

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData,
    ): array {
        $product = (string)$cObj->stdWrapValue('product', $processorConfiguration, '');
        $navigation = $processedData[$processorConfiguration['navigation'] ?? 'navigation'] ?? [];
        $rootline = $processedData[$processorConfiguration['rootline'] ?? 'rootline'] ?? [];
        $languages = $processedData[$processorConfiguration['languages'] ?? 'languages'] ?? [];

        $items = array_map(
            fn(array $page): array => $this->entry($page) + ['front' => true],
            $this->pages($navigation),
        );

        $processedData[$processorConfiguration['as'] ?? 'chrome'] = [
            'menu' => [
                'label' => $product,
                'href' => (string)($rootline[0]['link'] ?? '/'),
                'items' => $items,
            ],
            'rail' => $this->rail($items),
            'groups' => $this->groups($items, $product),
            'crumbs' => $this->crumbs($rootline),
            'languages' => $this->languages($languages),
        ];

        return $processedData;
    }

    /**
     * One page and everything under it, as `MenuEntry`.
     *
     * @param array<string, mixed> $page
     *
     * @return array<string, mixed>
     */
    private function entry(array $page): array
    {
        $href = (string)($page['link'] ?? '');
        $entry = ['label' => (string)($page['title'] ?? ''), 'href' => $href];

        /* An external link page is somebody else's site. It opens away and
           is never the page the reader is on. */
        if ((int)($page['data']['doktype'] ?? 0) === self::DOKTYPE_LINK && preg_match('#^[a-z][a-z0-9+.-]*://#i', $href) === 1) {
            return $entry + ['external' => true];
        }

        if ((bool)($page['current'] ?? false)) {
            $entry['current'] = true;
        } elseif ((bool)($page['active'] ?? false)) {
            $entry['here'] = true;
        }

        $under = array_map($this->entry(...), $this->pages($page['children'] ?? []));
        if ($under !== []) {
            $entry['items'] = $under;
        }

        return $entry;
    }

    /**
     * The section the reader is in, for the rail. A section that is a single
     * page has no rail: the bar's name for it says all there is.
     *
     * @param list<array<string, mixed>> $items
     *
     * @return array<string, mixed>|null
     */
    private function rail(array $items): ?array
    {
        foreach ($items as $entry) {
            if (($entry['current'] ?? false) === true || ($entry['here'] ?? false) === true) {
                unset($entry['front']);

                return ($entry['items'] ?? []) === [] ? null : $entry;
            }
        }

        return null;
    }

    /**
     * The footer's columns: a column per section, with the pages under it.
     * A page with nothing under it joins the column with the product's name.
     *
     * @param list<array<string, mixed>> $items
     *
     * @return list<array<string, mixed>>
     */
    private function groups(array $items, string $product): array
    {
        $alone = [];
        $sections = [];
        foreach ($items as $entry) {
            $link = $this->link($entry);
            if (($entry['items'] ?? []) === []) {
                $alone[] = $link;
                continue;
            }
            $sections[] = [
                'label' => $entry['label'],
                'href' => $entry['href'],
                'items' => array_map($this->link(...), $entry['items']),
            ];
        }

        return $alone === [] ? $sections : [['label' => $product, 'items' => $alone], ...$sections];
    }

    /**
     * @param array<string, mixed> $entry
     *
     * @return array<string, mixed>
     */
    private function link(array $entry): array
    {
        return array_intersect_key($entry, ['label' => true, 'href' => true, 'external' => true]);
    }

    /**
     * The trail from the root to the page. The last entry is the page itself
     * and no link. A trail of one entry says nothing the bar does not.
     *
     * @param array<int, array<string, mixed>> $rootline
     *
     * @return list<array<string, string>>
     */
    private function crumbs(array $rootline): array
    {
        $crumbs = [];
        foreach (array_values($rootline) as $page) {
            $crumbs[] = ['label' => (string)($page['title'] ?? ''), 'href' => (string)($page['link'] ?? '')];
        }
        if (count($crumbs) < 2) {
            return [];
        }
        unset($crumbs[array_key_last($crumbs)]['href']);

        return $crumbs;
    }

    /**
     * The same page in the site's other languages, as `DropdownChoice`. One
     * language is no choice, and the bar then shows no control.
     *
     * @param array<int, array<string, mixed>> $languages
     *
     * @return list<array<string, mixed>>
     */
    private function languages(array $languages): array
    {
        $choices = [];
        foreach ($languages as $language) {
            $choice = [
                'label' => (string)($language['navigationTitle'] ?? $language['title'] ?? ''),
                'lang' => (string)($language['hreflang'] ?? ''),
            ];
            if ((bool)($language['available'] ?? false)) {
                $choice['href'] = (string)($language['link'] ?? '');
            } else {
                $choice['disabled'] = true;
            }
            if ((bool)($language['active'] ?? false)) {
                $choice['current'] = true;
            }
            $choices[] = $choice;
        }

        return count($choices) < 2 ? [] : $choices;
    }

    /**
     * The pages of a menu level, without the spacers: a spacer separates
     * and goes nowhere.
     *
     * @return list<array<string, mixed>>
     */
    private function pages(mixed $level): array
    {
        if (!is_array($level)) {
            return [];
        }

        return array_values(array_filter(
            $level,
            static fn(mixed $page): bool => is_array($page) && !(bool)($page['spacer'] ?? false),
        ));
    }
}
