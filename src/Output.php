<?php

declare(strict_types=1);

namespace Magic\Console;

class Output
{
    /**
     * @param resource|null $stdout
     * @param resource|null $stderr
     */
    public function __construct(
        private readonly mixed $stdout = null,
        private readonly mixed $stderr = null,
        private readonly bool $quiet = false,
        private readonly bool $verbose = false,
        private readonly bool $decorated = true,
    ) {
    }

    public function write(string $message): void
    {
        if ($this->quiet) {
            return;
        }

        $this->writeTo($this->stdout(), $message);
    }

    public function writeln(string $message = ''): void
    {
        $this->write($message . PHP_EOL);
    }

    public function error(string $message): void
    {
        $this->writeTo($this->stderr(), $message);
    }

    public function errorln(string $message = ''): void
    {
        $this->error($message . PHP_EOL);
    }

    public function title(string $message): void
    {
        $this->writeln(Style::bold($message, $this->decorated));
        $this->writeln(str_repeat('=', strlen($message)));
    }

    public function section(string $message): void
    {
        $this->writeln(Style::apply($message, 'cyan', $this->decorated));
    }

    /**
     * @param list<string> $items
     */
    public function listing(array $items): void
    {
        foreach ($items as $item) {
            $this->writeln('  - ' . $item);
        }
    }

    public function codeBlock(string $code): void
    {
        $this->writeln('```');
        $this->writeln($code);
        $this->writeln('```');
    }

    public function stream(string $chunk): void
    {
        $this->write($chunk);
    }

    public function verbose(string $message): void
    {
        if (! $this->verbose) {
            return;
        }

        $this->writeln(Style::apply($message, 'gray', $this->decorated));
    }

    public function success(string $message): void
    {
        $this->writeln(Style::apply($message, 'green', $this->decorated));
    }

    public function warning(string $message): void
    {
        $this->writeln(Style::apply($message, 'yellow', $this->decorated));
    }

    public function failure(string $message): void
    {
        $this->errorln(Style::apply($message, 'red', $this->decorated));
    }

    /**
     * @return resource
     */
    private function stdout(): mixed
    {
        return $this->stdout ?? STDOUT;
    }

    /**
     * @return resource
     */
    private function stderr(): mixed
    {
        return $this->stderr ?? STDERR;
    }

    /**
     * @param resource $stream
     */
    private function writeTo(mixed $stream, string $message): void
    {
        fwrite($stream, $message);
    }
}
