<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\Tests\Functional\ContentElements;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\Soul\Theme\Tests\Functional\SiteTestCase;

/**
 * Every type emits the system's elements and leaves the content between
 * the tags, so a page with no script still has it.
 */
final class ContentElementsTest extends SiteTestCase
{
    #[Test]
    public function anElementHasNoFrameAndItsAnchorStandsOnTheHeading(): void
    {
        $markup = $this->render('/guide/install');

        self::assertStringContainsString('<h2 id="c10">Requirements</h2>', $markup);
        self::assertStringNotContainsString('class="frame', $markup);
        self::assertStringNotContainsString('class="ce-', $markup);
    }

    #[Test]
    public function theHeadingTakesTheLevelAndTheSubheaderIsTheLead(): void
    {
        $markup = $this->render('/guide/configure');

        self::assertMatchesRegularExpression('~<h4 id="c32">A heading at the fourth level</h4>\s*<p class="sds-lead">And the lead under it</p>~', $markup);
    }

    #[Test]
    public function aNoteCarriesItsToneAndItsTextBetweenTheTags(): void
    {
        self::assertStringContainsString(
            '<sds-note tone="warn" heading="Back up the database first"><p>The setup writes to the database.</p></sds-note>',
            $this->render('/guide/install'),
        );
    }

    #[Test]
    public function aCodeBlockCarriesItsLanguageAndTheTextAsWritten(): void
    {
        self::assertStringContainsString(
            '<sds-code code-lang="bash" caption="Install the package" source="composer require typo3/soul" copy></sds-code>',
            $this->render('/guide/install'),
        );
    }

    #[Test]
    public function aQuoteSaysWhoSaidIt(): void
    {
        self::assertStringContainsString(
            '<sds-quote body="One package for the whole site." by="A maintainer" as="of the project" href="https://typo3.org"></sds-quote>',
            $this->render('/guide/install'),
        );
    }

    #[Test]
    public function aTableIsItsColumnsAndRows(): void
    {
        $markup = $this->render('/guide/install');

        self::assertSame([['head' => 'Name'], ['head' => 'Default']], self::json($markup, 'sds-table', 'columns'));
        self::assertSame([['cells' => ['product', 'empty']], ['cells' => ['brand', 'empty']]], self::json($markup, 'sds-table', 'rows'));
        self::assertMatchesRegularExpression('~<sds-table [^>]*\bscrollable></sds-table>~', $markup);
    }

    #[Test]
    public function aNumberedListIsAnOrderedList(): void
    {
        self::assertStringContainsString('<ol><li>First step</li><li>Second step</li></ol>', $this->render('/guide/install'));
    }

    #[Test]
    public function theHeroHasOneDisplayHeadingAndOneAction(): void
    {
        $markup = $this->render('/product');

        self::assertStringContainsString('<sds-eyebrow label="Product"></sds-eyebrow>', $markup);
        self::assertStringContainsString('<h1 class="sds-display" id="c20">Build the site once</h1>', $markup);
        self::assertStringContainsString('<p class="sds-lead">One set for the whole page.</p>', $markup);
        self::assertStringContainsString('<sds-button variant="primary" size="lg" href="/guide">Start now</sds-button>', $markup);
        self::assertSame(1, substr_count($markup, '<h1'));
    }

    #[Test]
    public function aMenuOfPagesIsAWallOfCards(): void
    {
        $markup = $this->render('/product');

        self::assertMatchesRegularExpression(
            '~<sds-grid>\s*<sds-card heading="Guide" href="/guide" body="How to set the site up\."></sds-card>\s*<sds-card heading="About" href="/about" body="Who builds the product\."></sds-card>\s*</sds-grid>~',
            $markup,
        );
    }

    #[Test]
    public function aSitemapIsAListOfLinks(): void
    {
        $markup = $this->render('/guide/configure');

        self::assertMatchesRegularExpression('~<ul>\s*<li>\s*<sds-link label="Install" href="/guide/install"></sds-link>~', $markup);
        self::assertStringContainsString('<sds-link label="Configure" href="/guide/configure"></sds-link>', $markup);
    }

    #[Test]
    public function aDividerInAColumnIsARule(): void
    {
        self::assertStringContainsString('<hr>', $this->render('/guide/configure'));
    }

    #[Test]
    public function theSectionsOfAColumnPageAreItsContents(): void
    {
        $markup = $this->render('/guide/configure');
        $entries = self::json($markup, 'sds-nav-toc', 'entries');

        self::assertMatchesRegularExpression('~<div class="sds-aside">\s*<sds-nav-toc~', $markup);
        self::assertSame('On this page', self::attribute($markup, 'sds-nav-toc', 'label'));
        self::assertSame('Pages of the guide', $entries[0]['label']);
        self::assertSame('#c30', $entries[0]['href']);
        self::assertSame([['label' => 'A heading at the fourth level', 'href' => '#c32']], $entries[0]['items']);
    }

    #[Test]
    public function theSectionsOfABandsPageArePills(): void
    {
        $items = self::json($this->render('/product'), 'sds-nav-pills', 'items');

        self::assertContains(['label' => 'What it costs', 'href' => '#c23'], $items);
        self::assertNotContains('Build the site once', array_column($items, 'label'));
    }
}
