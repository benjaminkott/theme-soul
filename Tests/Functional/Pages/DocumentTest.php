<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\Tests\Functional\Pages;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\Soul\Theme\Tests\Functional\SiteTestCase;

/**
 * A long document on its own: the paper, and the outline beside it.
 */
final class DocumentTest extends SiteTestCase
{
    #[Test]
    public function aDocumentStandsOnPaperWithNoBarAndNoFooter(): void
    {
        $markup = $this->render('/reference');

        self::assertStringContainsString('<div class="sds-paper">', $markup);
        self::assertStringContainsString('<p class="sds-paper__title">Reference</p>', $markup);
        self::assertStringContainsString('<main class="sds-paper__main" id="main-content">', $markup);
        self::assertStringNotContainsString('<sds-nav-main', $markup);
        self::assertStringNotContainsString('<sds-footer', $markup);
    }

    #[Test]
    public function theOutlineNestsThePartsAsTheHeadingsDo(): void
    {
        $markup = $this->render('/reference');
        $entries = self::json($markup, 'sds-nav-outline', 'entries');

        self::assertSame(['Purpose', 'Decision'], array_column($entries, 'label'));
        self::assertSame([['label' => 'Scope', 'href' => '#c41']], $entries[0]['items']);
        self::assertSame('#c42', $entries[1]['href']);
        self::assertSame('Contents', self::attribute($markup, 'sds-nav-outline', 'label'));
        self::assertMatchesRegularExpression('~<sds-nav-outline [^>]*\bnumbered\b~', $markup);
    }
}
