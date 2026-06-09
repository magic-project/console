<?php

declare(strict_types=1);

namespace Magic\Console;

final class Option
{
    public function __construct(
        private readonly string $name,
        private readonly ?string $shortcut = null,
        private readonly string $description = '',
        private readonly bool $acceptsValue = false,
        private readonly mixed $default = null,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function shortcut(): ?string
    {
        return $this->shortcut;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function acceptsValue(): bool
    {
        return $this->acceptsValue;
    }

    public function default(): mixed
    {
        return $this->default;
    }
}
