<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\ViewHelpers;

use TYPO3\Soul\Theme\Html\Attributes;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * The attributes of an element, as `Attributes` writes them.
 *
 *   <sds-footer {soul:attributes(of: {product: settings.soul.product, groups: chrome.groups})}></sds-footer>
 *
 * The tag stays literal in the template, so a reader sees which element it
 * is. `flags` are the boolean attributes, from a field that holds 0 or 1.
 * Any value of a boolean attribute turns it on, `zoomable="0"` too.
 */
final class AttributesViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('of', 'array', 'The attributes, by name in camel case', true);
        $this->registerArgument('flags', 'array', 'The boolean attributes, by name in camel case', false, []);
    }

    public function render(): string
    {
        $attributes = $this->arguments['of'];
        foreach ($this->arguments['flags'] as $name => $on) {
            $attributes[$name] = (bool)$on;
        }

        return Attributes::write($attributes);
    }
}
