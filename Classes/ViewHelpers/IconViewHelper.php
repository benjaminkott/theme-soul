<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\ViewHelpers;

use TYPO3\Soul\Theme\Html\Icons;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * The name of an icon the system knows, or nothing.
 *
 *   <sds-card {soul:attributes(of: {icon: '{soul:icon(name: item.icon)}'})}></sds-card>
 *
 * An editor writes the name of an icon by hand. A name the system does not
 * know gives no icon, not an element that fails.
 */
final class IconViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('name', 'string', 'The name of the icon', false, '');
    }

    public function render(): string
    {
        return Icons::known($this->arguments['name']);
    }
}
