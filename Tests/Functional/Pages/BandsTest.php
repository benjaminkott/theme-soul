<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\Tests\Functional\Pages;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\Soul\Theme\Tests\Functional\SiteTestCase;

/**
 * A page that argues: bands, cut at each divider.
 */
final class BandsTest extends SiteTestCase
{
    #[Test]
    public function eachDividerStartsTheNextBand(): void
    {
        $markup = $this->render('/product');

        self::assertStringContainsString('<main class="sds-bands" id="main-content">', $markup);
        self::assertSame(3, preg_match_all('~<section class="sds-band[^"]*"~', $markup));
        self::assertStringNotContainsString('<hr', $markup);
        self::assertStringNotContainsString('sds-body', $markup);
    }

    #[Test]
    public function theLayoutOfTheDividerIsTheGroundOfTheBandItOpens(): void
    {
        preg_match_all('~<section class="(sds-band[^"]*)"\s*(?:id="([^"]*)")?~', $this->render('/product'), $bands);

        self::assertSame(['sds-band', 'sds-band sds-band--quiet', 'sds-band'], $bands[1]);
        self::assertSame(['', 'c22', 'c24'], $bands[2]);
    }

    #[Test]
    public function theElementsStandInTheBandsInTheirOrder(): void
    {
        $markup = $this->render('/product');
        $quiet = substr($markup, (int)strpos($markup, 'sds-band--quiet'));
        $quiet = substr($quiet, 0, (int)strpos($quiet, '</section>'));

        self::assertStringContainsString('What it costs', $quiet);
        self::assertStringNotContainsString('Why it exists', $quiet);
    }

    #[Test]
    public function aBandsPageHasNoRailAndNoTrail(): void
    {
        $markup = $this->render('/product');

        self::assertStringNotContainsString('<sds-nav-rail', $markup);
        self::assertStringNotContainsString('<sds-nav-breadcrumb', $markup);
    }
}
