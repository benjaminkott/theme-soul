<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\Tests\Functional;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Configuration\SiteWriter;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * A site on the `typo3/soul` set and nothing else, with the page tree of
 * `Fixtures/site.csv`. The TypoScript and the page TSconfig both come from
 * the set, as they do in a project that takes it.
 */
abstract class SiteTestCase extends FunctionalTestCase
{
    protected const BASE = 'http://localhost/';

    protected array $coreExtensionsToLoad = [
        'fluid_styled_content',
    ];

    protected array $testExtensionsToLoad = [
        'typo3/theme-soul',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/Fixtures/site.csv');

        $this->get(SiteWriter::class)->write('theme-soul', [
            'rootPageId' => 1,
            'base' => self::BASE,
            'dependencies' => ['typo3/theme-soul'],
            'settings' => [
                'soul' => [
                    'product' => 'Example',
                    'note' => 'A site for the tests.',
                    'version' => '1.0.0',
                ],
            ],
            'languages' => [
                [
                    'title' => 'English',
                    'enabled' => true,
                    'languageId' => 0,
                    'base' => '/',
                    'locale' => 'en_US.UTF-8',
                    'navigationTitle' => 'English',
                ],
            ],
        ]);

        $this->get(CacheManager::class)->flushCaches();
    }

    protected function render(string $path): string
    {
        $response = $this->executeFrontendSubRequest(new InternalRequest(self::BASE . ltrim($path, '/')));
        $markup = (string)$response->getBody();
        self::assertSame(200, $response->getStatusCode(), $markup);

        return $markup;
    }

    /**
     * The value of an attribute on the first element with this tag, as the
     * element reads it.
     */
    protected static function attribute(string $markup, string $tag, string $name): ?string
    {
        if (preg_match('~<' . preg_quote($tag, '~') . '\b[^>]*\s' . preg_quote($name, '~') . '="([^"]*)"~', $markup, $match) !== 1) {
            return null;
        }

        return html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5);
    }

    /**
     * @return array<mixed>
     */
    protected static function json(string $markup, string $tag, string $name): array
    {
        $value = self::attribute($markup, $tag, $name);
        self::assertNotNull($value, sprintf('<%s> has no %s', $tag, $name));

        return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
    }
}
