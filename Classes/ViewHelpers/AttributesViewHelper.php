<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * The attributes of an element, written the way the element reads them.
 *
 *   <sds-footer {soul:attributes(of: {product: settings.soul.product, groups: chrome.groups})}></sds-footer>
 *
 * A list or a map becomes JSON, which is how an element takes a property
 * from markup. `true` is the bare attribute. An empty value is no attribute:
 * an element tells an absent property from an empty one, and a template
 * cannot. Every value is escaped for the quotes it stands in. The tag stays
 * literal in the template, so a reader and the coverage check both see it.
 *
 * A name is written in camel case, because a Fluid array key cannot hold
 * a dash: `codeLang` is the attribute `code-lang`. `flags` are the boolean
 * attributes, from a field that holds 0 or 1. Any value of a boolean
 * attribute turns it on, `zoomable="0"` too.
 */
final class AttributesViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('of', 'array', 'The attributes, by name', true);
        $this->registerArgument('flags', 'array', 'The boolean attributes, by name', false, []);
    }

    public function render(): string
    {
        $out = [];
        $attributes = $this->arguments['of'];
        foreach ($this->arguments['flags'] as $name => $on) {
            $attributes[$name] = (bool)$on;
        }
        foreach ($attributes as $key => $value) {
            if (!is_string($key) || preg_match('/^[a-z][a-zA-Z0-9]*$/', $key) !== 1) {
                throw new Exception(sprintf('"%s" is not an attribute name in camel case', (string)$key), 1759312801);
            }
            $name = strtolower((string)preg_replace('/[A-Z]/', '-$0', $key));
            if ($value === null || $value === false || $value === '' || $value === []) {
                continue;
            }
            if ($value === true) {
                $out[] = $name;
                continue;
            }
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            } elseif (!is_scalar($value) && !$value instanceof \Stringable) {
                throw new Exception(sprintf('The attribute "%s" holds a %s, which has no form in markup', $name, get_debug_type($value)), 1759312802);
            }
            $out[] = sprintf('%s="%s"', $name, htmlspecialchars((string)$value, ENT_QUOTES | ENT_HTML5));
        }

        return implode(' ', $out);
    }
}
