<?php

namespace Chroma\CodeQuality\Contracts;

interface FileReaderInterface
{
    function execute();

    public function getFiles();
}
