<?php

namespace Chroma\CodeQuality\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Yaml\Dumper;


class InitCommand extends Command
{
  /**
  * [configure description]
  * @return void
  */
  protected function configure()
  {
    $this->setName('init')
    ->setDescription('Runs code quality tools')
    ->setDefinition([])
    ->addOption(
      'lint',
      'l',
      InputOption::VALUE_NONE,
      'Enable the PHPLINT task'
    )
    ->addOption(
      'csfixer',
      'f',
      InputOption::VALUE_NONE,
      'Enable the PHPCSFIXER task'
    )
    ->addOption(
      'cs',
      'c',
      InputOption::VALUE_NONE,
      'Enable the PHPCS task'
    )
    ->addOption(
      'md',
      'm',
      InputOption::VALUE_NONE,
      'Enable the PHPMD task'
    )
    ->addOption(
      'unit',
      'u',
      InputOption::VALUE_NONE,
      'Enable the PHPUNIT task'
    )
    ->addArgument(
      'productionBranch',
      InputArgument::OPTIONAL,
      'Name of production branch, hooks cannot be bypassed'
    );
  }

  /**
  * [execute description]
  * @param  InputInterface  $input  [description]
  * @param  OutputInterface $output [description]
  * @return void                  [description]
  */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    /**
    * it contains the selected task to execute
    * @var array
    */
    $productionBranch = $input->getArgument('productionBranch') ?: '';

    $array = [
      'PRODUCTION_BRANCH'     => $productionBranch,
      'PHPLINT'               => $input->getOption('lint'),
      'PHPCSFIXER'            => $input->getOption('csfixer'),
      'PHPCS'                 => $input->getOption('cs'),
      'PHPMD'                 => $input->getOption('md'),
      'PHPUNIT'               => $input->getOption('unit'),
    ];

    $dumper = new Dumper();
    // save the config file
    file_put_contents(BASE_PATH . '/../config.yml', $dumper->dump($array,2));
  }

}
