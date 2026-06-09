<?php

declare(strict_types=1);

namespace Magic\Console;

use Magic\Console\Exceptions\ConsoleException;

final class CommandRegistry
{
    /**
     * @var array<string, Command>
     */
    private array $commands = [];

    /**
     * @var array<string, string>
     */
    private array $aliases = [];

    public function add(Command $command): void
    {
        $name = $command->name();

        if (isset($this->commands[$name]) || isset($this->aliases[$name])) {
            throw new ConsoleException(sprintf('Command "%s" is already registered.', $name));
        }

        $this->commands[$name] = $command;

        foreach ($command->aliases() as $alias) {
            if (isset($this->commands[$alias]) || isset($this->aliases[$alias])) {
                throw new ConsoleException(sprintf('Command alias "%s" is already registered.', $alias));
            }

            $this->aliases[$alias] = $name;
        }
    }

    public function has(string $name): bool
    {
        return isset($this->commands[$name]) || isset($this->aliases[$name]);
    }

    public function get(string $name): ?Command
    {
        if (isset($this->commands[$name])) {
            return $this->commands[$name];
        }

        if (isset($this->aliases[$name])) {
            return $this->commands[$this->aliases[$name]];
        }

        return null;
    }

    /**
     * @return list<Command>
     */
    public function all(bool $includeHidden = false): array
    {
        $commands = array_values($this->commands);

        if ($includeHidden) {
            return $commands;
        }

        return array_values(array_filter(
            $commands,
            static fn (Command $command): bool => ! $command->isHidden(),
        ));
    }
}
