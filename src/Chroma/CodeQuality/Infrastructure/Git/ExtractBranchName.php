<?php

class ExtractBranchName
{

  /**
   * the current branch name
   * @var string
   */
  private $branchName;

  public function execute()
  {
    // test this, should populate prop branch name with the current git branch
    // should I use process builder in replacement of exec? probably
    exec('git rev-parse --abbrev-ref HEAD 2>/dev/null', $this->branchName);
  }

  public function getBranchName()
  {
    $this->execute();
    return $this->branchName;
  }
}
