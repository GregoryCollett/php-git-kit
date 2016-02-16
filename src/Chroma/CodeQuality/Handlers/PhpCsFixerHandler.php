<?php

namespace Chroma\CodeQuality\Infrastructure\PhpCsFixer;

use Chroma\CodeQuality\Infrastructure\Common\ToolHandler;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class PhpCsFixerHandler
{
    private $fixer = '-psr2';

    private $files;

    private $needle = '/(\.php)|(\.inc)$/';

    private $input;

    private $output;

    public function __construct($input, $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    public function run()
    {
        $this->output->writeln(strtoupper('<info>Checking & Fixing Code Style With PHP CS FIXER</info>'));

        $errors = [];

        foreach ($this->files as $file) {
            $srcFile = preg_match($this->needle, $file);

            if (!$srcFile) {
                continue;
            }

            $process = new ProcessBuilder([
            'php-cs-fixer',
            'fix',
            $file,
            '--level=' . $this->fixer
            ]);

            $phpCsFixer = $process->getProcess();
            $phpCsFixer->run();

          // if ($phpCsFixer->isSuccessful()) {
          //
          //   $errors[] = $phpCsFixer->getOutput();
          //
          //   $this->output->writeln('<error>' . $phpCsFixer->getOutput() . '</error>');
          // } else {
          //   $this->output->writeln('<info>' . $file . ' :: NO FIXES REQUIRED</info>');
          // }
        }

        $this->output->writeln(strtoupper('<info>Successfully run PHP CS Fixer</info>'));
    }

    public function setNeedle($needle)
    {
        $this->needle = $needle;

        return $this;
    }

    public function setFiles(array $files)
    {
        $this->files = $files;

        return $this;
    }

    public function setFixer($fixer)
    {
        if (in_array(strtoupper($fixer), ['PSR0', 'PSR1', 'PSR2', 'SYMFONY'])) {
            $this->fixer = $fixer;
        }

        return $this;
    }
}
