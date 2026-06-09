<?php

declare(strict_types=1);

namespace Magic\Console;

class Input
{
    /**
     * @param list<string> $arguments
     * @param array<string, mixed> $options
     * @param list<string> $tokens
     */
    public function __construct(
        private readonly ?string $commandName,
        private readonly array $arguments = [],
        private readonly array $options = [],
        private readonly array $tokens = [],
    ) {
    }

    public function commandName(): ?string
    {
        return $this->commandName;
    }

    /**
     * @return list<string>
     */
    public function arguments(): array
    {
        return $this->arguments;
    }

    public function argument(int $index, ?string $default = null): ?string
    {
        return $this->arguments[$index] ?? $default;
    }

    /**
     * @return array<string, mixed>
     */
    public function options(): array
    {
        return $this->options;
    }

    public function option(string $name, mixed $default = null): mixed
    {
        return $this->options[$name] ?? $default;
    }

    public function hasOption(string $name): bool
    {
        return array_key_exists($name, $this->options);
    }

    public function isHelp(): bool
    {
        return (bool) ($this->option('help') ?? false);
    }

    public function isVersion(): bool
    {
        return (bool) ($this->option('version') ?? false);
    }

    public function isNoInteraction(): bool
    {
        return (bool) ($this->option('no-interaction') ?? false);
    }

    public function isVerbose(): bool
    {
        return (bool) ($this->option('verbose') ?? false);
    }

    public function isQuiet(): bool
    {
        return (bool) ($this->option('quiet') ?? false);
    }

    public function isDebug(): bool
    {
        return (bool) ($this->option('debug') ?? false);
    }

    public function cwd(): ?string
    {
        $cwd = $this->option('cwd');

        return is_string($cwd) ? $cwd : null;
    }

    public function config(): ?string
    {
        $config = $this->option('config');

        return is_string($config) ? $config : null;
    }

    /**
     * @return list<string>
     */
    public function tokens(): array
    {
        return $this->tokens;
    }
}
