<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\DataProcessing;

use TYPO3\CMS\Core\Domain\RecordInterface;
use TYPO3\CMS\Core\Page\ContentAreaCollection;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * The sections of a page, out of the headings of its content elements.
 *
 * Every element with a heading that the editor did not hide, and did not
 * leave out of the section index, is a section. A heading at the first
 * level is the title of the page and no section, and so is a hero. A
 * heading at the third level nests under the second before it. The anchor is `c<uid>`, which is
 * where the content templates put the id.
 *
 * The elements come from the page's content area, or from a section menu
 * of the core, which hands them over per page:
 *
 *   dataProcessing.70 = soul-sections
 *   dataProcessing.70.from = content.main
 *
 *   dataProcessing.20 = soul-sections
 *   dataProcessing.20.from = menu
 */
final class SectionsProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData,
    ): array {
        $from = (string)($processorConfiguration['from'] ?? 'content.main');
        $default = (int)($processorConfiguration['defaultLevel'] ?? 2);

        $headings = [];
        foreach ($this->elements($processedData, $from) as $element) {
            $header = trim((string)($element['header'] ?? ''));
            $level = (int)($element['header_layout'] ?? 0);
            if ($header === '' || $level === 100 || $level === 1 || (int)($element['sectionIndex'] ?? 1) === 0) {
                continue;
            }
            if (($element['CType'] ?? '') === 'soul_hero') {
                continue;
            }
            $headings[] = [
                'label' => $header,
                'href' => '#c' . (int)$element['uid'],
                'level' => $level > 0 && $level < 7 ? $level : $default,
            ];
        }

        $processedData[$processorConfiguration['as'] ?? 'sections'] = $this->nest($headings);

        return $processedData;
    }

    /**
     * The fields of each element, whichever form they arrive in.
     *
     * @return list<array<string, mixed>>
     */
    private function elements(array $processedData, string $from): array
    {
        if ($from === 'menu') {
            $elements = [];
            foreach ($processedData['menu'] ?? [] as $page) {
                foreach ($page['content'] ?? [] as $element) {
                    $elements[] = (array)($element['data'] ?? []);
                }
            }

            return $elements;
        }

        [$variable, $area] = array_pad(explode('.', $from, 2), 2, 'main');
        $content = $processedData[$variable] ?? null;
        if (!$content instanceof ContentAreaCollection || !$content->has($area)) {
            return [];
        }
        $elements = [];
        foreach ($content->get($area)->getRecords() as $record) {
            if ($record instanceof RecordInterface && $record->getRawRecord() !== null) {
                $elements[] = $record->getRawRecord()->toArray();
            }
        }

        return $elements;
    }

    /**
     * A flat run of headings as a tree: each one takes the headings after
     * it that stand at a deeper level.
     *
     * @param list<array{label: string, href: string, level: int}> $headings
     *
     * @return list<array<string, mixed>>
     */
    private function nest(array $headings, int &$at = 0, int $above = 0): array
    {
        $items = [];
        while (isset($headings[$at]) && $headings[$at]['level'] > $above) {
            $heading = $headings[$at++];
            $entry = ['label' => $heading['label'], 'href' => $heading['href']];
            $under = $this->nest($headings, $at, $heading['level']);
            if ($under !== []) {
                $entry['items'] = $under;
            }
            $items[] = $entry;
        }

        return $items;
    }
}
