<?php

namespace Chroma\CodeQuality\Infrastructure\Git;

use Chroma\CodeQuality\Infrastructure\Common\FileReader;

class ExtractFilesStaged extends FileReader
{
  /**
   * get the staged for the in progress commit
   * @return void
   */
  public function execute()
  {
    exec("git diff --staged --name-only HEAD", $this->files);
  }

}
