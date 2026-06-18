<?php

declare(strict_types=1);

namespace Magic\Console;

use Magic\Console\Contracts\ExceptionHandlerInterface;
use Magic\Console\Exceptions\CommandFailedException;
use Magic\Console\Exceptions\InvalidOptionException;
use Magic\Console\Exceptions\MissingArgumentException;
use Magic\Console\Exceptions\UnknownCommandException;
use Throwable;

final class ExceptionHandler implements ExceptionHandlerInterface
{
    public function handle(Throwable $exception, Input $input, Output $output): int
    {
        $output->failure('Error: ' . $exception->getMessage());

        if ($input->isDebug()) {
            $output->errorln();
            $output->errorln($exception->getTraceAsString());
        }

        return $this->exitCode($exception);
    }

    private function exitCode(Throwable $exception): int
    {
        return match (true) {
            $exception instanceof MissingArgumentException,
            $exception instanceof InvalidOptionException => ExitCode::INVALID_ARGUMENT,
            $exception instanceof UnknownCommandException => ExitCode::COMMAND_NOT_FOUND,
            $exception instanceof CommandFailedException => ExitCode::FAILURE,
            default => ExitCode::FAILURE,
        };
    }
}
