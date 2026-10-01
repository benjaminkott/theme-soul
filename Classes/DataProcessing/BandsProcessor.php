<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\DataProcessing;

use TYPO3\CMS\Core\Domain\RecordInterface;
use TYPO3\CMS\Core\Page\ContentAreaCollection;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * A column of content as a run of bands, cut at each divider.
 *
 * A band holds several elements: a heading, its text and the table under it.
 * So a band is not one element. The divider an editor already knows is where
 * one band ends and the next starts, and its layout says the ground of the
 * band it opens. Elements before the first divider are the first band.
 *
 *   dataProcessing.60 = soul-bands
 *   dataProcessing.60.area = main
 */
final class BandsProcessor implements DataProcessorInterface
{
    /** The divider's layout that puts its band on the quiet ground. */
    private const QUIET = '1';

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData,
    ): array {
        $content = $processedData[$processorConfiguration['content'] ?? 'content'] ?? null;
        $area = (string)($processorConfiguration['area'] ?? 'main');
        $records = $content instanceof ContentAreaCollection && $content->has($area)
            ? $content->get($area)->getRecords()
            : [];

        $bands = [];
        $band = ['id' => '', 'quiet' => false, 'records' => []];
        foreach ($records as $record) {
            if (!$record instanceof RecordInterface) {
                continue;
            }
            if ($record->getRecordType() !== 'div') {
                $band['records'][] = $record;
                continue;
            }
            if ($band['records'] !== []) {
                $bands[] = $band;
            }
            $band = [
                'id' => 'c' . $record->getUid(),
                'quiet' => $record->has('layout') && (string)$record->get('layout') === self::QUIET,
                'records' => [],
            ];
        }
        if ($band['records'] !== []) {
            $bands[] = $band;
        }

        $processedData[$processorConfiguration['as'] ?? 'bands'] = $bands;

        return $processedData;
    }
}
