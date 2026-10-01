<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * A text an editor pasted, as the lines an element reads.
 *
 * `diff` reads a unified diff: a line that starts with `+` is added, with
 * `-` removed, and every other line is context. The file headers `+++` and
 * `---` and the hunk headers stay out, because the element names the file.
 *
 * `tree` reads one entry per line, nested by indentation. Text after ` # `
 * is the note on the entry. `facts` reads a term and its value per line,
 * apart by `|`, as the core's list of terms writes them.
 *
 *   dataProcessing.10 = soul-lines
 *   dataProcessing.10.shape = tree
 */
final class LinesProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData,
    ): array {
        $field = (string)($processorConfiguration['field'] ?? 'bodytext');
        $text = str_replace("\r\n", "\n", (string)($cObj->data[$field] ?? ''));
        $lines = explode("\n", rtrim($text, "\n"));

        $processedData[$processorConfiguration['as'] ?? 'lines'] = match ($processorConfiguration['shape'] ?? 'diff') {
            'tree' => $this->tree($lines),
            'facts' => $this->facts($lines),
            default => $this->diff($lines),
        };

        return $processedData;
    }

    /**
     * @param list<string> $lines
     *
     * @return list<array{kind: string, text: string}>
     */
    private function diff(array $lines): array
    {
        $body = [];
        foreach ($lines as $line) {
            if (str_starts_with($line, '+++') || str_starts_with($line, '---') || str_starts_with($line, '@@') || str_starts_with($line, 'diff ')) {
                continue;
            }
            $kind = match ($line[0] ?? ' ') {
                '+' => 'add',
                '-' => 'del',
                default => 'context',
            };
            $text = $kind === 'context' && !str_starts_with($line, ' ') ? $line : substr($line, 1);
            $body[] = ['kind' => $kind, 'text' => $text];
        }

        return $body;
    }

    /**
     * @param list<string> $lines
     *
     * @return list<array{term: string, value: string}>
     */
    private function facts(array $lines): array
    {
        $facts = [];
        foreach ($lines as $line) {
            [$term, $value] = array_pad(explode('|', $line, 2), 2, '');
            if (trim($term) !== '') {
                $facts[] = ['term' => trim($term), 'value' => trim($value)];
            }
        }

        return $facts;
    }

    /**
     * @param list<string> $lines
     *
     * @return list<array<string, mixed>>
     */
    private function tree(array $lines): array
    {
        $entries = [];
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            [$label, $note] = array_pad(explode(' # ', trim($line), 2), 2, '');
            $entry = ['label' => $label];
            if ($note !== '') {
                $entry['note'] = $note;
            }
            $entries[] = ['depth' => strlen($line) - strlen(ltrim($line)), 'entry' => $entry];
        }

        $at = 0;

        return $this->nest($entries, $at, -1);
    }

    /**
     * @param list<array{depth: int, entry: array<string, string>}> $entries
     *
     * @return list<array<string, mixed>>
     */
    private function nest(array $entries, int &$at, int $above): array
    {
        $items = [];
        while (isset($entries[$at]) && $entries[$at]['depth'] > $above) {
            $line = $entries[$at++];
            $entry = $line['entry'];
            $under = $this->nest($entries, $at, $line['depth']);
            if ($under !== []) {
                $entry['items'] = $under;
            }
            $items[] = $entry;
        }

        return $items;
    }
}
