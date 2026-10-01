<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\Html;

/**
 * The attributes of an element, written the way the element reads them.
 *
 * A name is camel case, because a Fluid array key cannot hold a dash:
 * `codeLang` is the attribute `code-lang`. A list or a map is JSON, which
 * is how an element takes a property from markup. `true` is the bare
 * attribute. An empty value is no attribute: an element tells an absent
 * property from an empty one, and a template cannot. Every value is
 * escaped for the quotes it stands in.
 */
final class Attributes
{
    /**
     * @param array<mixed> $attributes
     */
    public static function write(array $attributes): string
    {
        $out = [];
        foreach ($attributes as $key => $value) {
            if (!is_string($key) || preg_match('/^[a-z][a-zA-Z0-9]*$/', $key) !== 1) {
                throw new \InvalidArgumentException(sprintf('"%s" is not an attribute name in camel case', (string)$key), 1759312801);
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
                throw new \InvalidArgumentException(sprintf('The attribute "%s" holds a %s, which has no form in markup', $name, get_debug_type($value)), 1759312802);
            }
            $out[] = sprintf('%s="%s"', $name, htmlspecialchars((string)$value, ENT_QUOTES | ENT_HTML5));
        }

        return implode(' ', $out);
    }

    /**
     * The tag name of a system element, and nothing else.
     */
    public static function tag(string $tag): string
    {
        if (preg_match('/^sds-[a-z]+(-[a-z]+)*$/', $tag) !== 1) {
            throw new \InvalidArgumentException(sprintf('"%s" is not an element of the system', $tag), 1759312803);
        }

        return $tag;
    }
}
