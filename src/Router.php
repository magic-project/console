<?php

declare(strict_types=1);

namespace Magic\Console;

use Magic\Console\Exceptions\UnknownCommandException;

final class Router
{
    public function __construct(
        private readonly CommandRegistry $commands,
    ) {
    }

    public function resolve(Input $input): Command
    {
        $commandName = $input->commandName();

        if ($commandName === null) {
            throw new UnknownCommandException('No command given.');
        }

        $command = $this->commands->get($commandName);

        if ($command === null) {
            throw new UnknownCommandException(sprintf('Unknown command "%s".', $commandName));
        }

        return $command;
    }
}
