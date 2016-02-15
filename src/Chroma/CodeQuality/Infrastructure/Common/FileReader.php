<?php

namespace Chroma\CodeQuality\Infrastructure\Common;

use Chroma\CodeQuality\Contracts\FileReaderInterface;

abstract class FileReader implements FileReaderInterface
{
    /**
     * [$files description]
     * @var [type]
     */
    public $files = [];

    /**
     * [execute description]
     * @return [type] [description]
     */
    abstract function execute();

    /**
     * Gets files returned from the execute result
     * @return array array of files
     */
    public function getFiles()
    {
        $this->execute();

        return $this->files;
    }
}
