<?php

namespace Chroma\CodeQuality\Infrastructure\Git;

use Chroma\CodeQuality\Infrastructure\Common\FileReader;

class ExtractCommitedFiles extends FileReader
{
  /**
   * get the files for the latest commit
   * @return void
   */
  public function execute()
  {
    exec('git rev-parse --verify HEAD', $this->files);

    // remove the ransom commit id being parsed
    unset($this->files[0]);
    // this only works in pre commit so for testing in development we work against
    // the last commit
    //exec("git diff-index --cached --name-status HEAD | egrep '^(A|M)' | awk '{print $2;}'", $this->files);
    exec("git diff-tree --no-commit-id --name-only -r HEAD", $this->files);
  }

}
