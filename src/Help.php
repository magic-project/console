<?php

declare(strict_types=1);

namespace Magic\Console;

final class Help
{
    public function application(string $name, string $version, CommandRegistry $commands): string
    {
        $lines = [
            sprintf('%s %s', $name, $version),
            '',
            'Usage:',
            sprintf('  %s <command> [arguments] [options]', $name),
            '',
            'Commands:',
        ];

        foreach ($commands->all() as $command) {
            $lines[] = sprintf('  %-12s %s', $command->name(), $command->description());
        }

        $lines[] = '';
        $lines[] = 'Global Options:';
        $lines[] = '  --help              Display help';
        $lines[] = '  --version           Display version';
        $lines[] = '  --no-interaction    Disable interactive prompts';
        $lines[] = '  --verbose, -v       Display verbose output';
        $lines[] = '  --quiet, -q         Suppress normal output';
        $lines[] = '  --debug             Display debug information';
        $lines[] = '  --cwd=<path>        Set working directory';
        $lines[] = '  --config=<path>     Set config file';

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    public function command(string $applicationName, Command $command): string
    {
        $usage = sprintf('%s %s', $applicationName, $command->name());

        foreach ($command->arguments() as $argument) {
            $usage .= $argument->isRequired()
                ? sprintf(' <%s>', $argument->name())
                : sprintf(' [%s]', $argument->name());
        }

        $usage .= ' [options]';

        $lines = [
            'Usage:',
            '  ' . $usage,
        ];

        if ($command->description() !== '') {
            $lines[] = '';
            $lines[] = 'Description:';
            $lines[] = '  ' . $command->description();
        }

        if ($command->arguments() !== []) {
            $lines[] = '';
            $lines[] = 'Arguments:';

            foreach ($command->arguments() as $argument) {
                $lines[] = sprintf('  %-16s %s', $argument->name(), $argument->description());
            }
        }

        if ($command->options() !== []) {
            $lines[] = '';
            $lines[] = 'Options:';

            foreach ($command->options() as $option) {
                $name = '--' . $option->name();

                if ($option->shortcut() !== null) {
                    $name .= ', -' . $option->shortcut();
                }

                if ($option->acceptsValue()) {
                    $name .= '=<value>';
                }

                $lines[] = sprintf('  %-24s %s', $name, $option->description());
            }
        }

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }
}
