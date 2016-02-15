<?php

namespace Chroma\CodeQuality\Infrastructure\Git;

/**
* Class HooksFileCopier.
*/
class HooksFileCopier
{
  const GIT_HOOKS_PATH = '.git/hooks/';

  /**
   * Copies the specified hook to your repos git hooks directory only if it
   * doesn't already exist!
   * @param  string $hook the hook to copy/symlink
   * @return void
   */
  public function copy($hook)
  {
    if (false === file_exists(self::GIT_HOOKS_PATH.$hook)) {
      // exec('cp ' . __DIR__ . '/../../../../hooks/' . $hook . ' ' . GIT_HOOKS_PATH . $hook);
      $copy = new Process('cp '.__DIR__.'/../../../../hooks/'.$hook.' .git/hooks/'.$hook);
      $copy->run();
    }
  }
}
