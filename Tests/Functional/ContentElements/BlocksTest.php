<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\Tests\Functional\ContentElements;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\Soul\Theme\Tests\Functional\SiteTestCase;

/**
 * The types that are one element each, set by its attributes alone.
 */
final class BlocksTest extends SiteTestCase
{
    private string $markup = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/blocks.csv');
        $this->markup = $this->render('/components');
    }

    #[Test]
    public function anEmbedFramesTheDocumentAtItsShape(): void
    {
        self::assertStringContainsString('<sds-embed src="https://example.org/map" label="A map of the venue" ratio="4 / 3" caption="Where the sessions are." allowfullscreen></sds-embed>', $this->markup);
    }

    #[Test]
    public function aCopyCarriesItsValue(): void
    {
        self::assertStringContainsString('<sds-copy value="composer require typo3/theme-soul" label="Install command"></sds-copy>', $this->markup);
    }

    #[Test]
    public function aProgressSaysHowFarAndWhatBoundsIt(): void
    {
        self::assertStringContainsString('<sds-progress caption="Translations" label="Translations" value="60" max="100" unit="%" note="Two languages are missing." pulsing></sds-progress>', $this->markup);
    }

    #[Test]
    public function aDiffIsTheLinesOfAUnifiedDiff(): void
    {
        self::assertSame('Configuration/Sets/soul/config.yaml', self::attribute($this->markup, 'sds-diff', 'path'));
        self::assertSame([
            ['kind' => 'context', 'text' => 'name: typo3/theme-soul'],
            ['kind' => 'del', 'text' => 'label: Soul'],
            ['kind' => 'add', 'text' => 'label: Theme: Soul'],
        ], self::json($this->markup, 'sds-diff', 'body'));
    }

    #[Test]
    public function aDirectoryNestsByIndentation(): void
    {
        self::assertSame([
            ['label' => 'Classes/', 'items' => [['label' => 'DataProcessing/', 'note' => 'what the templates read']]],
            ['label' => 'Configuration/'],
            ['label' => 'composer.json'],
        ], self::json($this->markup, 'sds-tree', 'entries'));
    }

    #[Test]
    public function aConfigurationValueCarriesItsFacts(): void
    {
        self::assertStringContainsString('<sds-confval name="soul.product" anchor="c75" type="string" default="empty" required><p>The name of the site.</p></sds-confval>', $this->markup);
    }

    #[Test]
    public function aColourIsAValueWithItsName(): void
    {
        self::assertStringContainsString('<sds-swatch value="#ff8700" name="Accent"></sds-swatch>', $this->markup);
    }

    #[Test]
    public function aRunHandsItsStepsOverAsData(): void
    {
        self::assertSame('running', self::attribute($this->markup, 'sds-run', 'verdict'));
        self::assertSame([
            ['label' => 'Build', 'state' => 'done', 'meta' => '2 min'],
            ['label' => 'Publish', 'state' => 'running', 'note' => 'waits for the tag'],
        ], self::json($this->markup, 'sds-run', 'steps'));
    }

    #[Test]
    public function aDialogOpensFromItsButton(): void
    {
        self::assertStringContainsString('<sds-button variant="secondary" for="dialog-78">Read the boundary</sds-button>', $this->markup);
        self::assertStringContainsString('<sds-dialog id="dialog-78" heading="What the theme leaves out" body="The TYPO3 backend, and official TYPO3 sites." cancel-label="Close"></sds-dialog>', $this->markup);
    }
}
