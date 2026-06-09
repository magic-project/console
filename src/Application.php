<?php

declare(strict_types=1);

namespace Magic\Console;

use Magic\Console\Exceptions\InvalidOptionException;
use Magic\Console\Exceptions\MissingArgumentException;
use Throwable;

final class Application
{
    private readonly CommandRegistry $commands;
    private readonly Router $router;
    private readonly Help $help;
    private readonly ExceptionHandler $exceptions;

    public function __construct(
        private readonly string $name,
        private readonly string $version,
    ) {
        $this->commands = new CommandRegistry();
        $this->router = new Router($this->commands);
        $this->help = new Help();
        $this->exceptions = new ExceptionHandler();
    }

    public function add(Command $command): self
    {
        $this->commands->add($command);

        return $this;
    }

    /**
     * @param list<string>|null $argv
     */
    public function run(?array $argv = null, ?Output $output = null): int
    {
        $input = new ArgvInput($argv ?? $_SERVER['argv'] ?? []);
        $output ??= ConsoleOutput::fromInput($input);

        try {
            return $this->handle($input, $output);
        } catch (Throwable $exception) {
            return $this->exceptions->handle($exception, $input, $output);
        }
    }

    public function commands(): CommandRegistry
    {
        return $this->commands;
    }

    private function handle(Input $input, Output $output): int
    {
        if ($input->isVersion()) {
            $output->writeln(sprintf('%s %s', $this->name, $this->version));

            return ExitCode::SUCCESS;
        }

        if ($input->commandName() === null || $input->commandName() === 'help') {
            return $this->displayHelp($input, $output);
        }

        $command = $this->router->resolve($input);
        $input = $this->reparseInputForCommand($input, $command);

        if ($input->isHelp()) {
            $output->write($this->help->command($this->name, $command));

            return ExitCode::SUCCESS;
        }

        $this->validateOptions($command, $input);
        $this->validateArguments($command, $input);

        $command->before($input, $output);
        $exitCode = $command->execute($input, $output);
        $command->after($input, $output, $exitCode);

        return $exitCode;
    }

    private function displayHelp(Input $input, Output $output): int
    {
        $commandName = $input->argument(0);

        if ($commandName !== null) {
            $command = $this->commands->get($commandName);

            if ($command !== null) {
                $output->write($this->help->command($this->name, $command));

                return ExitCode::SUCCESS;
            }
        }

        $output->write($this->help->application($this->name, $this->version, $this->commands));

        return ExitCode::SUCCESS;
    }

    private function validateArguments(Command $command, Input $input): void
    {
        foreach ($command->arguments() as $index => $argument) {
            if (! $argument->isRequired()) {
                continue;
            }

            if ($input->argument($index) === null) {
                throw new MissingArgumentException(sprintf(
                    'Missing required argument "%s" for command "%s".',
                    $argument->name(),
                    $command->name(),
                ));
            }
        }
    }

    private function validateOptions(Command $command, Input $input): void
    {
        $allowed = [
            'help' => true,
            'version' => true,
            'no-interaction' => true,
            'verbose' => true,
            'quiet' => true,
            'debug' => true,
            'cwd' => true,
            'config' => true,
        ];

        foreach ($command->options() as $option) {
            $allowed[$option->name()] = true;

            if ($option->shortcut() !== null) {
                $allowed[$option->shortcut()] = true;
            }
        }

        foreach (array_keys($input->options()) as $optionName) {
            if (! isset($allowed[$optionName])) {
                throw new InvalidOptionException(sprintf('Unknown option "--%s".', $optionName));
            }
        }
    }

    private function reparseInputForCommand(Input $input, Command $command): Input
    {
        $valueOptions = ['cwd', 'config'];

        foreach ($command->options() as $option) {
            if ($option->acceptsValue()) {
                $valueOptions[] = $option->name();
            }
        }

        return ArgvInput::fromTokens($input->tokens(), $valueOptions);
    }
}
