<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\Tests\Functional\ContentElements;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\Soul\Theme\Tests\Functional\SiteTestCase;

/**
 * The types that hold a set: every entry is a child element, or an entry
 * of the element's own, with its text between the tags.
 */
final class SetsTest extends SiteTestCase
{
    private string $markup = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/sets.csv');
        $this->markup = $this->render('/components');
    }

    #[Test]
    public function anAccordionFoldsEachAnswerBehindItsQuestion(): void
    {
        self::assertMatchesRegularExpression('~<sds-accordion>\s*<sds-accordion-item question="Does it need a build\?"><p>No\.</p></sds-accordion-item>\s*<sds-accordion-item question="Does it need Node\?" open><p>Only to update the drop-in\.</p></sds-accordion-item>\s*</sds-accordion>~', $this->markup);
    }

    #[Test]
    public function tabsCarryTheirPanelsAndTheOneOpenAtTheStart(): void
    {
        self::assertStringContainsString('<sds-tab-item label="Composer" icon="actions-terminal" active><p>composer require</p></sds-tab-item>', $this->markup);
        self::assertStringContainsString('<sds-tab-item label="Classic"><p>Upload the package.</p></sds-tab-item>', $this->markup);
    }

    #[Test]
    public function stepsSayWhichOneIsOptional(): void
    {
        self::assertMatchesRegularExpression('~<sds-steps>\s*<sds-step heading="Require the package"><p>Run composer\.</p></sds-step>\s*<sds-step heading="Choose a layout" optional>~', $this->markup);
    }

    #[Test]
    public function aTimelineMarksWhereTodayStands(): void
    {
        self::assertStringContainsString('<sds-timeline-stop when="2026 Q3" heading="First release"><p>The site set.</p></sds-timeline-stop>', $this->markup);
        self::assertStringContainsString('<sds-timeline-stop when="2026 Q4" heading="Every element" now>', $this->markup);
    }

    #[Test]
    public function figuresStandInADenseGridWithWhatBoundsThem(): void
    {
        self::assertMatchesRegularExpression('~<sds-grid variant="dense" columns="3">\s*<sds-stat value="15" label="content types" icon="actions-list" note="with their own fields"></sds-stat>~', $this->markup);
    }

    #[Test]
    public function statementsStandOnPlanes(): void
    {
        self::assertStringContainsString('<sds-surface label="SOURCE" icon="actions-check" heading="One set" body="For the whole site."></sds-surface>', $this->markup);
    }

    #[Test]
    public function aCardGoesWhereItsLinkGoes(): void
    {
        self::assertMatchesRegularExpression('~<sds-grid columns="2">\s*<sds-card heading="Guide" href="/guide" label="CHAPTER 01" tag="new" footer="Read it" action="Open the guide" body="How to set it up\."></sds-card>~', $this->markup);
    }

    #[Test]
    public function iconsStandInAFlushWall(): void
    {
        self::assertMatchesRegularExpression('~<sds-grid variant="flush">\s*<sds-icon-tile name="actions-arrow-end" href="/guide" tag="BiDi"></sds-icon-tile>~', $this->markup);
    }

    #[Test]
    public function factsAreTheEntriesOfTheElement(): void
    {
        self::assertStringContainsString('<sds-facts entries="[{&quot;term&quot;:&quot;Licence&quot;,&quot;value&quot;:&quot;MIT&quot;}]"></sds-facts>', $this->markup);
        self::assertStringContainsString('<sds-facts entries="[{&quot;term&quot;:&quot;Term&quot;,&quot;value&quot;:&quot;What it means&quot;}]"></sds-facts>', $this->markup);
    }

    #[Test]
    public function aRegisterNumbersItsEntriesUnderItsPrefix(): void
    {
        self::assertStringContainsString('<sds-register name="register-59" prefix="F">', $this->markup);
        self::assertStringNotContainsString('<p class="sds-lead">F</p>', $this->markup);
        self::assertStringContainsString('<sds-entry heading="The bar wraps at 600px" label="open" tone="warn" todo="Fix the order" origin="before the change"><p>It must shed instead.</p></sds-entry>', $this->markup);
    }

    #[Test]
    public function aDecisionPutsItsQuestionAndItsAnswers(): void
    {
        self::assertStringContainsString('<sds-decision question="Which database does the site use?" label="Decision 4" lead="Two answers, weighed." by="The maintainers" due="2026-12-01">', $this->markup);
        self::assertStringContainsString('<sds-answer key="A" heading="MariaDB" recommended><p>What the host has.</p></sds-answer>', $this->markup);
        self::assertStringContainsString('<sds-answer key="B" heading="SQLite" decided><p>One file.</p></sds-answer>', $this->markup);
        self::assertStringNotContainsString('<h2 id="c60">', $this->markup);
    }
}
