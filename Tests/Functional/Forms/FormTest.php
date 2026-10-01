<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\Tests\Functional\Forms;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Http\Stream;
use TYPO3\Soul\Theme\Tests\Functional\SiteTestCase;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;

/**
 * A form of the form framework, with every field a system control: what
 * it shows, what it refuses, and what it says once it went out.
 */
final class FormTest extends SiteTestCase
{
    private const PREFIX = 'tx_form_formframework[contact-90]';

    protected array $coreExtensionsToLoad = [
        'fluid_styled_content',
        'form',
    ];

    protected array $testExtensionsToLoad = [
        'typo3/theme-soul',
        'typo3/theme-soul-fixtures',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/forms.csv');
    }

    #[Test]
    public function everyFieldIsAControlOfTheSystem(): void
    {
        $markup = $this->render('/contact');

        self::assertStringContainsString('<sds-field name="' . self::PREFIX . '[name]" caption="Name" field-id="contact-90-name" required autocomplete="name" type="text"></sds-field>', $markup);
        self::assertStringContainsString('hint="The answer goes here."', $markup);
        self::assertMatchesRegularExpression('~<sds-field name="tx_form_formframework\[contact-90\]\[email\]"[^>]*type="email"~', $markup);
        self::assertSame([['label' => 'A question', 'value' => 'question'], ['label' => 'A bug', 'value' => 'bug']], self::json($markup, 'sds-select', 'options'));
        self::assertSame('How to reach you', self::attribute($markup, 'sds-radio', 'legend'));
        self::assertSame(self::PREFIX . '[areas][]', self::attribute($markup, 'sds-checkbox-group', 'name'));
        self::assertMatchesRegularExpression('~<sds-textarea name="tx_form_formframework\[contact-90\]\[message\]" caption="Message"~', $markup);
        self::assertMatchesRegularExpression('~<sds-checkbox name="tx_form_formframework\[contact-90\]\[privacy\]" label="I agree that the message is stored\." value="1"></sds-checkbox>~', $markup);
        self::assertStringContainsString('<sds-button variant="primary" type="submit">Send</sds-button>', $markup);
        self::assertStringNotContainsString('<sds-form-errors', $markup);
        self::assertMatchesRegularExpression('~<form [^>]*class="sds-form"~', $markup);
    }

    #[Test]
    public function anAppearanceTurnsAFieldIntoTheControlItNames(): void
    {
        $markup = $this->render('/contact');

        self::assertMatchesRegularExpression('~<sds-switch name="tx_form_formframework\[contact-90\]\[digest\]" label="Send me the digest" value="1"></sds-switch>~', $markup);
        self::assertMatchesRegularExpression('~<sds-range name="tx_form_formframework\[contact-90\]\[urgency\]" caption="Urgency" field-id="contact-90-urgency" min="0" max="100" step="10" unit="%"~', $markup);
        self::assertMatchesRegularExpression('~<sds-field-group>\s*<sds-field name="tx_form_formframework\[contact-90\]\[secret\]\[password\]"[^>]*type="password"></sds-field>\s*<sds-field name="tx_form_formframework\[contact-90\]\[secret\]\[confirmation\]" caption="The password again"~', $markup);
    }

    #[Test]
    public function aRefusedFormSaysWhatStoppedItAtTheTopAndAtTheField(): void
    {
        $markup = $this->submit($this->render('/contact'), ['email' => 'not an address', 'secret' => ['password' => '', 'confirmation' => '']]);

        $errors = self::json($markup, 'sds-form-errors', 'errors');
        self::assertSame(['contact-90-name', 'contact-90-email'], array_column($errors, 'for'));
        self::assertMatchesRegularExpression('~<sds-field name="tx_form_formframework\[contact-90\]\[name\]"[^>]* error="[^"]+"~', $markup);
        self::assertMatchesRegularExpression('~<sds-field name="tx_form_formframework\[contact-90\]\[email\]"[^>]* value="not an address"~', $markup);
    }

    #[Test]
    public function aFormThatWentOutSaysSoInANote(): void
    {
        $markup = $this->submit($this->render('/contact'), ['name' => 'Ada', 'email' => 'ada@example.org', 'areas' => ['frontend'], 'secret' => ['password' => 'a long secret', 'confirmation' => 'a long secret']]);

        self::assertMatchesRegularExpression('~<sds-note tone="ok" id="contact-90">\s*The message is on its way\.\s*</sds-note>~', $markup);
    }

    /**
     * Send the form as a browser does: its hidden fields, and the answers.
     *
     * @param array<string, mixed> $answers
     */
    private function submit(string $form, array $answers): string
    {
        self::assertSame(1, preg_match('~<form [^>]*action="([^"]+)"~', $form, $action));
        preg_match_all('~<input type="hidden" name="([^"]+)" value="([^"]*)"~', $form, $hidden, PREG_SET_ORDER);
        $query = array_map(static fn(array $field): string => urlencode(html_entity_decode($field[1])) . '=' . urlencode(html_entity_decode($field[2])), $hidden);
        parse_str(implode('&', $query), $body);
        $plugin = (array)($body['tx_form_formframework'] ?? []);
        $plugin['contact-90'] = array_replace((array)($plugin['contact-90'] ?? []), $answers);
        $body['tx_form_formframework'] = $plugin;

        /* As the raw body, which the test framework parses the way PHP
           does. A parsed body it takes flat only. */
        $stream = new Stream('php://temp', 'rw');
        $stream->write(http_build_query($body));
        $request = (new InternalRequest(self::BASE . ltrim(html_entity_decode($action[1]), '/')))
            ->withMethod('POST')
            ->withAddedHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($stream);
        $response = $this->executeFrontendSubRequest($request);
        $markup = (string)$response->getBody();
        self::assertSame(200, $response->getStatusCode(), $markup);

        return $markup;
    }
}
