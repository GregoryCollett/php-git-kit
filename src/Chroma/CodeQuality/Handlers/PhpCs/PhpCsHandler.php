<?php

namespace Chroma\CodeQuality\Handlers\PhpCs;

use Chroma\CodeQuality\Contracts\HandlerInterface;

use Chroma\CodeQuality\Infrastructure\Common\ToolHandler;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class PhpCsHandler implements HandlerInterface
{
    private $standard = 'PSR2';

    private $files;

    private $needle = '/(\.php)$/';

    private $input;

    private $output;

    public function __construct($input, $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    public function run()
    {
        $this->output->writeln(strtoupper('<fg=black;bg=cyan>Checking code style</>'));

        foreach ($this->files as $file) {
            if (!preg_match($this->needle, $file)) {
                continue;
            }

            $process = new ProcessBuilder([
            'phpcbf',
            '--standard=' . $this->standard,
            '--tab-width=4',
            $file
            ]);

            $phpCs = $process->getProcess();
            $phpCs->run();

            if (!$phpCs->isSuccessful()) {
                $this->output->writeln('<error>' . $phpCs->getOutput() . '</error>');

            }
        }

        $this->output->writeln(strtoupper('Successfully completed CodeSniffing'));
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

    public function setStandard($standard)
    {
        $this->standard = $standard;

        return $this;
    }
}
