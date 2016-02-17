<?php

namespace Chroma\CodeQuality\Infrastructure\PhpLint;

use Chroma\CodeQuality\Infrastructure\Common\ToolHandler;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class PhpLintHandler
{

    private $needle = '/(\.php)|(\.inc)$/';

    private $files;

    private $listener;

    private $formatter;

    private $input;

    private $output;

    public function __construct($listener)
    {
        $this->formatter = $listener->getHelper('formatter');

        $this->input = $listener->input;
        $this->output = $listener->output;
    }

    public function run()
    {
        $this->output->writeln(strtoupper('Running PHP Lint'));

        foreach ($this->files as $file) {
            print_r($file);
            if (!preg_match($this->needle, $file)) {
                continue;
            }

            $process = new ProcessBuilder([
                'php',
                '-l',
                $file
            ]);

            $phpLint = $process->getProcess();
            $phpLint->run();

            if (!$phpLint->isSuccessful()) {

                $errorBlock = $this->formatter->formatSection(
                    'ERROR',
                    $file . '::' . $phpLint->getOutput(),
                    'error'
                );

                $this->output->writeln($errorBlock);

            } else {

                $successBlock = $this->formatter->formatSection(
                    'PASSED',
                    '<info>' . $file . '</info>'
                );

                $this->output->writeln($successBlock);

            }
        }

        $this->output->writeln(strtoupper('<info>Successfully linted PHP files</info>'));
    }

    public function setNeedle($needle)
    {
        $this->needle = $needle;

        return $this;
    }

    public function setFiles($files)
    {
        $this->files = $files;

        return $this;
    }
}
