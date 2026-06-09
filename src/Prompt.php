<?php

declare(strict_types=1);

namespace Magic\Console;

use Magic\Console\Exceptions\MissingArgumentException;

final class Prompt
{
    /**
     * @param resource|null $inputStream
     */
    public function __construct(
        private readonly Input $input,
        private readonly Output $output,
        private readonly mixed $inputStream = null,
    ) {
    }

    public function ask(string $question, ?string $default = null): string
    {
        if ($this->input->isNoInteraction()) {
            if ($default !== null) {
                return $default;
            }

            throw new MissingArgumentException(sprintf('Missing value for prompt "%s".', $question));
        }

        $suffix = $default === null ? ': ' : sprintf(' [%s]: ', $default);
        $this->output->write($question . $suffix);

        $answer = $this->readLine();

        if ($answer === '' && $default !== null) {
            return $default;
        }

        return $answer;
    }

    public function confirm(string $question, bool $default = false): bool
    {
        $defaultLabel = $default ? 'Y/n' : 'y/N';
        $answer = strtolower($this->ask(sprintf('%s [%s]', $question, $defaultLabel), $default ? 'yes' : 'no'));

        return in_array($answer, ['y', 'yes', '1', 'true'], true);
    }

    /**
     * @param non-empty-list<string> $choices
     */
    public function select(string $question, array $choices, ?string $default = null): string
    {
        $this->output->writeln($question);

        foreach ($choices as $index => $choice) {
            $this->output->writeln(sprintf('  [%d] %s', $index + 1, $choice));
        }

        $answer = $this->ask('Select', $default);

        if (ctype_digit($answer)) {
            $index = (int) $answer - 1;

            if (isset($choices[$index])) {
                return $choices[$index];
            }
        }

        if (in_array($answer, $choices, true)) {
            return $answer;
        }

        throw new MissingArgumentException(sprintf('Invalid selection "%s".', $answer));
    }

    /**
     * @param non-empty-list<string> $choices
     *
     * @return list<string>
     */
    public function multiSelect(string $question, array $choices): array
    {
        $this->output->writeln($question);

        foreach ($choices as $index => $choice) {
            $this->output->writeln(sprintf('  [%d] %s', $index + 1, $choice));
        }

        $answer = $this->ask('Select comma separated values', '');

        if ($answer === '') {
            return [];
        }

        $selected = [];

        foreach (explode(',', $answer) as $value) {
            $value = trim($value);

            if (ctype_digit($value)) {
                $index = (int) $value - 1;

                if (isset($choices[$index])) {
                    $selected[] = $choices[$index];
                }
            }
        }

        return $selected;
    }

    public function secret(string $question): string
    {
        return $this->ask($question);
    }

    private function readLine(): string
    {
        $line = fgets($this->inputStream());

        if ($line === false) {
            return '';
        }

        return trim($line);
    }

    /**
     * @return resource
     */
    private function inputStream(): mixed
    {
        return $this->inputStream ?? STDIN;
    }
}
