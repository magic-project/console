<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Magic\Console\Argument;
use Magic\Console\Application;
use Magic\Console\Command;
use Magic\Console\ExitCode;
use Magic\Console\Input;
use Magic\Console\Option;
use Magic\Console\Output;

final class HelloCommand extends Command
{
    public function name(): string
    {
        return 'hello';
    }

    public function description(): string
    {
        return 'Print a friendly greeting.';
    }

    public function arguments(): array
    {
        return [
            new Argument('name', 'Name to greet.', required: true),
        ];
    }

    public function options(): array
    {
        return [
            new Option('times', description: 'Number of greetings to print.', acceptsValue: true, default: '1'),
        ];
    }

    public function aliases(): array
    {
        return ['hi'];
    }

    public function execute(Input $input, Output $output): int
    {
        $name = $input->argument(0);
        $times = max(1, (int) $input->option('times', '1'));

        for ($index = 0; $index < $times; $index++) {
            $output->success(sprintf('Hello, %s!', $name));
        }

        $output->verbose(sprintf('Printed %d greeting(s).', $times));

        return ExitCode::SUCCESS;
    }
}

$app = new Application('example', '1.0.0');
$app->add(new HelloCommand());

exit($app->run());
