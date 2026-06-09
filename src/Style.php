<?php

declare(strict_types=1);

namespace Magic\Console;

final class Style
{
    private const COLORS = [
        'red' => '31',
        'green' => '32',
        'yellow' => '33',
        'blue' => '34',
        'magenta' => '35',
        'cyan' => '36',
        'gray' => '90',
    ];

    public static function apply(string $text, string $color, bool $decorated = true): string
    {
        if (! $decorated || ! isset(self::COLORS[$color])) {
            return $text;
        }

        return sprintf("\033[%sm%s\033[0m", self::COLORS[$color], $text);
    }

    public static function bold(string $text, bool $decorated = true): string
    {
        if (! $decorated) {
            return $text;
        }

        return sprintf("\033[1m%s\033[0m", $text);
    }
}
