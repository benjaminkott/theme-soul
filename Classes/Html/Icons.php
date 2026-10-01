<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\Html;

/**
 * The icons the system knows: the names in the icon index of the drop-in.
 * An element throws on a name it does not know, so a name that is not in
 * the index is no icon.
 */
final class Icons
{
    private const INDEX = __DIR__ . '/../../Resources/Public/Soul/assets/icons/icons.json';

    /**
     * @var array<string, true>|null
     */
    private static ?array $names = null;

    /**
     * The name, if the system knows it. Else the empty string, which is no
     * attribute (see `Attributes`).
     */
    public static function known(mixed $name): string
    {
        $name = is_string($name) ? trim($name) : '';

        return isset(self::names()[$name]) ? $name : '';
    }

    /**
     * @return array<string, true>
     */
    private static function names(): array
    {
        if (self::$names === null) {
            $index = json_decode((string)file_get_contents(self::INDEX), true, 512, JSON_THROW_ON_ERROR);
            $icons = is_array($index) && is_array($index['icons'] ?? null) ? $index['icons'] : [];
            self::$names = array_fill_keys(array_map(strval(...), array_keys($icons)), true);
        }

        return self::$names;
    }
}
