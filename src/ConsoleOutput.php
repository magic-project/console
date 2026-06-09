<?php

declare(strict_types=1);

namespace Magic\Console;

final class ConsoleOutput extends Output
{
    public static function fromInput(Input $input): self
    {
        return new self(
            quiet: $input->isQuiet(),
            verbose: $input->isVerbose(),
        );
    }
}
