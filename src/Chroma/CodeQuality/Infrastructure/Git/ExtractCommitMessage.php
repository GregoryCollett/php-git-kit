<?php

namespace Chroma\CodeQuality\Infrastructure\Git;

class ExtractCommitMessage
{

  public function extract($commit)
  {
    return file_get_contents($commit);
  }
  
}
