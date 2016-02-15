<?php

namespace Chroma\CodeQuality\Infrastructure\Common;

use Symfony\Component\Console\Output\OutputInterface;

class ToolHandler
{
    private $input;

    private $output;

    public function __construct($input, $output)
    {
        $this->input = $input;
        $this->output = $output;
    }
}
