<?php

declare(strict_types=1);

namespace Magic\Console;

final class Argument
{
    public function __construct(
        private readonly string $name,
        private readonly string $description = '',
        private readonly bool $required = false,
        private readonly ?string $default = null,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function default(): ?string
    {
        return $this->default;
    }
}
