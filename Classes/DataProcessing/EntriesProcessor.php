<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\DataProcessing;

use TYPO3\CMS\Core\Domain\RecordInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * The entries of a set as the list an element takes as data, where the
 * element takes no entries between its tags.
 *
 * Each key of `fields` is the name the element reads, and its value the
 * field of `tx_themesoul_item` that holds it. An empty value stays out.
 *
 *   dataProcessing.10 = soul-entries
 *   dataProcessing.10.fields {
 *     label = header
 *     state = state
 *   }
 */
final class EntriesProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData,
    ): array {
        $record = $processedData['record'] ?? null;
        $fields = (array)($processorConfiguration['fields.'] ?? []);
        $entries = [];
        if ($record instanceof RecordInterface && $record->has('tx_themesoul_items')) {
            foreach ($record->get('tx_themesoul_items') ?? [] as $item) {
                if (!$item instanceof RecordInterface) {
                    continue;
                }
                $entry = [];
                foreach ($fields as $name => $field) {
                    $value = $item->has((string)$field) ? $item->get((string)$field) : null;
                    if (is_scalar($value) && (string)$value !== '') {
                        $entry[(string)$name] = (string)$value;
                    }
                }
                $entries[] = $entry;
            }
        }

        $processedData[$processorConfiguration['as'] ?? 'entries'] = $entries;

        return $processedData;
    }
}
