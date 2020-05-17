<?php

namespace AftDev\Console;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

trait CallableTrait
{
    protected $lastOutput;

    /**
     * Call a command directly.
     *
     * @return int 0 if everything went fine, or an error code
     */
    public function call(string $command, array $arguments = [], OutputInterface $output = null): int
    {
        $this->lastOutput = $output ?? new BufferedOutput();

        array_unshift($arguments, $command);

        $this->setCatchExceptions(false);

        $input = new ArrayInput($arguments);
        $result = $this->doRun($input, $this->lastOutput);

        $this->setCatchExceptions(true);

        return $result;
    }

    /**
     * Get the output for the last run command.
     *
     * @return string
     */
    public function output()
    {
        return $this->lastOutput && method_exists($this->lastOutput, 'fetch')
            ? $this->lastOutput->fetch()
            : '';
    }
}
