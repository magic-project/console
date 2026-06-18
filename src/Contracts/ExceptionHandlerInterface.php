<?php

declare(strict_types=1);

namespace Magic\Console\Contracts;

use Magic\Console\Input;
use Magic\Console\Output;

interface ExceptionHandlerInterface
{
    /**
     * 处理命令执行过程中的异常。
     */
    public function handle(\Throwable $exception, Input $input, Output $output): int;
}
