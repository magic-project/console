<?php

declare(strict_types=1);

namespace Magic\Console;

final class ExitCode
{
    public const SUCCESS = 0;
    public const FAILURE = 1;
    public const INVALID_ARGUMENT = 2;
    public const COMMAND_NOT_FOUND = 3;
    public const CONFIGURATION_ERROR = 4;
    public const EXTERNAL_TOOL_FAILED = 5;
    public const LLM_REQUEST_FAILED = 6;
}
