<?php

namespace Chroma\CodeQuality\Infrastructure\PhpMD;

use Chroma\CodeQuality\Infrastructure\Common\ToolHandler;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class PhpMDHandler
{
    private $needle = '/(\.php)$/';

    private $files;

    //private $errors = [];

    private $input;

    private $output;

    public function __construct($input, $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    public function run()
    {
        $this->output->writeln(strtoupper('<info>Checking code with PHP Mess Detector</info>'));

        foreach ($this->files as $file) {
            if (!preg_match($this->needle, $file)) {
                continue;
            }

            $process = new ProcessBuilder([
            'phpmd',
            $file,
            'text',
            'PmdRules.xml',
            '--minimumpriority',
            1
            ]);

            $phpMD = $process->getProcess();
            $phpMD->run();

            if (!$phpMD->isSuccessful()) {
                $this->output->writeln('<error>' . $phpMD->getOutput() . '</error>');
            }
        }

        $this->output->writeln(strtoupper('Successfully completed running PHP Mess Detector'));

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
