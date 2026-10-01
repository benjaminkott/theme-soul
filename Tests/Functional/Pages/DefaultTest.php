<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\Tests\Functional\Pages;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\Soul\Theme\Tests\Functional\SiteTestCase;

/**
 * A page that reports: the shell, the bar, the rail, the trail, the footer.
 */
final class DefaultTest extends SiteTestCase
{
    #[Test]
    public function thePageStandsInTheShellOfTheSystem(): void
    {
        $markup = $this->render('/guide/install');

        self::assertStringContainsString('<body class="sds-app"', $markup);
        self::assertMatchesRegularExpression('~<a class="sds-skip sds-btn sds-btn--secondary" href="#main-content">Skip to content</a>\s*<div class="sds-shell">~', $markup);
        self::assertStringContainsString('<main class="sds-body__main" id="main-content">', $markup);
        self::assertStringContainsString('<article class="sds-prose">', $markup);
    }

    #[Test]
    public function theHeadLinksTheDropInOfTheExtension(): void
    {
        $markup = $this->render('/');

        self::assertMatchesRegularExpression('~<link rel="stylesheet" href="[^"]*/Soul/soul\.css[^"]*"~', $markup);
        self::assertMatchesRegularExpression('~<script src="[^"]*/Soul/soul-boot\.js[^"]*"~', $markup);
        self::assertMatchesRegularExpression('~<script[^>]*type="module"[^>]*src="[^"]*/Soul/soul\.js[^"]*"|<script[^>]*src="[^"]*/Soul/soul\.js[^"]*"[^>]*type="module"~', $markup);
        self::assertMatchesRegularExpression('~<link rel="preload" as="font" type="font/woff2" crossorigin href="[^"]*/Soul/fonts/source-sans-3-latin-wght-normal\.woff2"~', $markup);
    }

    #[Test]
    public function theBarCarriesTheWholeTreeAndMarksWhereTheReaderIs(): void
    {
        $markup = $this->render('/guide/install');
        $menu = self::json($markup, 'sds-nav-main', 'menu');

        self::assertSame('Example', self::attribute($markup, 'sds-nav-main', 'product'));
        self::assertSame('Example', $menu['label']);
        self::assertSame(['Guide', 'Product', 'About', 'Reference', 'Components'], array_column($menu['items'], 'label'));
        self::assertTrue($menu['items'][0]['front']);
        self::assertTrue($menu['items'][0]['here']);
        self::assertTrue($menu['items'][0]['items'][0]['current']);
        self::assertArrayNotHasKey('here', $menu['items'][1]);
    }

    #[Test]
    public function theRailShowsTheSectionTheReaderIsIn(): void
    {
        $rail = self::json($this->render('/guide/install'), 'sds-nav-rail', 'entry');

        self::assertSame('Guide', $rail['label']);
        self::assertSame(['Install', 'Configure'], array_column($rail['items'], 'label'));
        self::assertArrayNotHasKey('front', $rail);
    }

    #[Test]
    public function aSectionOfOnePageHasNoRail(): void
    {
        $markup = $this->render('/about');

        self::assertStringNotContainsString('<sds-nav-rail', $markup);
        self::assertStringNotContainsString('sds-body__rail', $markup);
    }

    #[Test]
    public function theTrailEndsOnThePageWithNoLink(): void
    {
        $crumbs = self::json($this->render('/guide/install'), 'sds-nav-breadcrumb', 'items');

        self::assertSame(['Home', 'Guide', 'Install'], array_column($crumbs, 'label'));
        self::assertArrayNotHasKey('href', $crumbs[2]);
    }

    #[Test]
    public function theRootHasNoTrail(): void
    {
        self::assertStringNotContainsString('<sds-nav-breadcrumb', $this->render('/'));
    }

    #[Test]
    public function theFooterHasAColumnPerSectionAndOneForThePagesAlone(): void
    {
        $markup = $this->render('/');
        $groups = self::json($markup, 'sds-footer', 'groups');

        self::assertSame(['Example', 'Guide'], array_column($groups, 'label'));
        self::assertSame(['Product', 'About', 'Reference', 'Components'], array_column($groups[0]['items'], 'label'));
        self::assertSame(['Install', 'Configure'], array_column($groups[1]['items'], 'label'));
        self::assertSame('A site for the tests.', self::attribute($markup, 'sds-footer', 'note'));
        self::assertSame('1.0.0', self::attribute($markup, 'sds-footer', 'version'));
        self::assertNull(self::attribute($markup, 'sds-footer', 'brand'));
    }

    #[Test]
    public function thePagerLeadsThroughTheSectionInTheOrderOfTheRail(): void
    {
        $markup = $this->render('/guide/install');

        self::assertSame('/guide', self::attribute($markup, 'sds-nav-pager', 'previous-href'));
        self::assertSame('Guide', self::attribute($markup, 'sds-nav-pager', 'previous-label'));
        self::assertSame('/guide/configure', self::attribute($markup, 'sds-nav-pager', 'next-href'));
    }

    #[Test]
    public function theLastPageOfASectionHasNoWayOn(): void
    {
        $markup = $this->render('/guide/configure');

        self::assertSame('/guide/install', self::attribute($markup, 'sds-nav-pager', 'previous-href'));
        self::assertNull(self::attribute($markup, 'sds-nav-pager', 'next-href'));
    }

    #[Test]
    public function aPageOutsideASectionHasNoPager(): void
    {
        self::assertStringNotContainsString('<sds-nav-pager', $this->render('/about'));
    }
}
