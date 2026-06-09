<?php

declare(strict_types=1);

namespace Magic\Console;

final class ArgvInput extends Input
{
    private const SHORTCUTS = [
        'h' => 'help',
        'V' => 'version',
        'v' => 'verbose',
        'q' => 'quiet',
    ];

    /**
     * @param list<string> $argv
     * @param list<string> $valueOptions
     */
    public function __construct(array $argv, array $valueOptions = ['cwd', 'config'])
    {
        parent::__construct(...self::parse($argv, $valueOptions));
    }

    /**
     * @param list<string> $tokens
     * @param list<string> $valueOptions
     */
    public static function fromTokens(array $tokens, array $valueOptions): self
    {
        return new self(array_merge(['magic'], $tokens), $valueOptions);
    }

    /**
     * @param list<string> $argv
     * @param list<string> $valueOptions
     *
     * @return array{0: ?string, 1: list<string>, 2: array<string, mixed>, 3: list<string>}
     */
    private static function parse(array $argv, array $valueOptions): array
    {
        $tokens = array_slice($argv, 1);
        $commandName = null;
        $arguments = [];
        $options = [];

        for ($index = 0; $index < count($tokens); $index++) {
            $token = $tokens[$index];

            if (str_starts_with($token, '--')) {
                [$name, $value, $consumedNext] = self::parseLongOption(
                    $token,
                    $tokens[$index + 1] ?? null,
                    $valueOptions,
                );
                $options[$name] = $value;

                if ($consumedNext) {
                    $index++;
                }

                continue;
            }

            if (str_starts_with($token, '-') && $token !== '-') {
                foreach (self::parseShortOptions($token) as $name => $value) {
                    $options[$name] = $value;
                }

                continue;
            }

            if ($commandName === null) {
                $commandName = $token;

                continue;
            }

            $arguments[] = $token;
        }

        return [$commandName, $arguments, $options, $tokens];
    }

    /**
     * @param list<string> $valueOptions
     *
     * @return array{0: string, 1: mixed, 2: bool}
     */
    private static function parseLongOption(string $token, ?string $nextToken, array $valueOptions): array
    {
        $option = substr($token, 2);

        if (str_contains($option, '=')) {
            [$name, $value] = explode('=', $option, 2);

            return [$name, $value, false];
        }

        if (in_array($option, $valueOptions, true) && $nextToken !== null && ! str_starts_with($nextToken, '-')) {
            return [$option, $nextToken, true];
        }

        return [$option, true, false];
    }

    /**
     * @return array<string, bool>
     */
    private static function parseShortOptions(string $token): array
    {
        $options = [];
        $shortcuts = str_split(substr($token, 1));

        foreach ($shortcuts as $shortcut) {
            $name = self::SHORTCUTS[$shortcut] ?? $shortcut;
            $options[$name] = true;
        }

        return $options;
    }
}
