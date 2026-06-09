<?php

declare(strict_types=1);

namespace Magic\Console;

abstract class Command
{
    abstract public function name(): string;

    abstract public function execute(Input $input, Output $output): int;

    public function description(): string
    {
        return '';
    }

    /**
     * @return list<Argument>
     */
    public function arguments(): array
    {
        return [];
    }

    /**
     * @return list<Option>
     */
    public function options(): array
    {
        return [];
    }

    /**
     * @return list<string>
     */
    public function aliases(): array
    {
        return [];
    }

    public function isHidden(): bool
    {
        return false;
    }

    public function before(Input $input, Output $output): void
    {
    }

    public function after(Input $input, Output $output, int $exitCode): void
    {
    }
}
