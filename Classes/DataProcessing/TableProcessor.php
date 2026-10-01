<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * The core's table as the columns and rows `sds-table` takes.
 *
 * The head row is the first row where the editor put the head on top. A
 * head column is the first column, set as the name a row is read by. A
 * foot row is the last row, and stays a row: the element has no foot.
 *
 *   dataProcessing.1727 = soul-table
 */
final class TableProcessor implements DataProcessorInterface
{
    private const HEAD_TOP = 1;
    private const HEAD_LEFT = 2;

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData,
    ): array {
        $table = array_values(array_filter((array)($processedData[$processorConfiguration['from'] ?? 'table'] ?? []), 'is_array'));
        $head = (int)($cObj->data['table_header_position'] ?? 0);
        $width = $table === [] ? 0 : max(array_map('count', $table));

        $heads = $head === self::HEAD_TOP && $table !== [] ? array_shift($table) : array_fill(0, $width, '');
        $columns = [];
        foreach (array_values($heads) as $at => $label) {
            $column = ['head' => (string)$label];
            if ($at === 0 && $head === self::HEAD_LEFT) {
                $column['cls'] = 'sds-td-name';
            }
            $columns[] = $column;
        }

        $rows = array_map(
            static fn(array $row): array => ['cells' => array_map('strval', array_values($row))],
            $table,
        );

        $processedData['columns'] = $columns;
        $processedData['rows'] = $rows;

        return $processedData;
    }
}
