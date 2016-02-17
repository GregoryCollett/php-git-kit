<?php

namespace Chroma\CodeQuality\Commands;

use Chroma\CodeQuality\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Chroma\CodeQuality\Infrastructure\Git\ExtractCommitedFiles;
use Chroma\CodeQuality\Infrastructure\Git\ExtractFilesStaged;
use Chroma\CodeQuality\Infrastructure\PhpLint\PhpLintHandler;
use Chroma\CodeQuality\Infrastructure\PhpCsFixer\PhpCsFixerHandler;
use Chroma\CodeQuality\Infrastructure\PhpMD\PhpMDHandler;
use Chroma\CodeQuality\Infrastructure\PhpCs\PhpCsHandler;
use Chroma\Commands\InitCommand;


class ToolkitCommand extends Command
{
    /**
    * @TODO Expand on these regular expressions
    */
    const PHP_FILES_IN_SRC = '/(\.php)$/';
    const COMPOSER_FILES = '/^composer\.(json|lock)$/';

    /**
    * Input
    * This allows us to read data from the command line
    * @var InputInterface
    */
    public $input;

    /**
    * Output
    * This allows us to write data to the command line
    * @var OutputInterface
    */
    public $output;

    /**
    * invokedFrom
    * Where this command was invoked from. Potential options are php and git
    * define types as class constants
    * @var string
    */
    private $invokedFrom = 'GIT';

    /**
    * Files to run code quality tools against
    * @var Array
    */
    private $files;

    /**
    * Tasks to be run
    * @var array
    */
    private $tasks;

    /**
    * Symfony question helper
    * @var object
    */
    private $questionHelper;

    /**
    * [$handlers description]
    * @var [type]
    */
    private $handlers;

    /**
    * Setup the command, name and details
    * @return void
    */
    protected function configure()
    {
        $this->setName('toolkit')
        ->setDescription('Runs code quality tools')
        ->setDefinition([])
        ->addArgument(
            'invokedFrom',
            InputArgument::OPTIONAL,
            'Where was this command invoked from?'
        )
        ->setHelp('');
    }

    /**
    * The code run for the command in question
    *
    * This should probably be abstracted more.
    * @param  InputInterface  $input  reads user input from cli
    * @param  OutputInterface $output pushes output to the users cli
    * @return void
    */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setup($input, $output);

        $this->outputTitle();

        $this->output->writeln('<info>invoked from ' . $this->invokedFrom . '</info>');

        if ($this->invokedFrom === 'GIT') {
          // Given I am a developer
          // When I run the command git commit [args]
          // Then codequality toolkit should run
          // TODO switch out the below function for getFilesStagedForCommit()
          // currently extracting files post commit for testing...
            $this->extractCommitedFiles();

            if ($this->hasFiles()) {
                $this->configureExecute();

                // $command = $this->getApplication()->find('init');
                // $arguments = $this->tasks;
                // $input = new ArrayInput($arguments);
                // $command->run($input, $output);

                $this->go();
            }

        } else {
            // we are being run manually so do some stuff
            // here we should branch off and make GIT its own service.
            // we should... check for any arguments..
            // we should expect arguments such as -d -dir (and a directory)
            // if none is provided we should try to run against the current folder
            // where the script is being executed from.
            //
            // Testing out writing basic user stories:
            // Given I am a developer
            // When I run the command codequality toolkit in my bash terminal
            // Then the toolkit should run on the current folder
        }

    }

    /**
     * Configure what we are going to execute.
     * @method configureExecute
     * @return void;
     */
    private function configureExecute()
    {
        $bypassQuestion = new ConfirmationQuestion(
            '<info>Bypass hook and proceed with commit? (n) </info>',
            false
        );

        if ($this->questionHelper->ask($this->input, $this->output, $bypassQuestion)) {
            $this->output->writeln('<comment>Bypassing hook</comment>');
            return;
        }

        // before this step we should look for a config in the repo.
        $tasksQuestion = new ChoiceQuestion(
            '<info>Which tasks would you like to run?</info>',
            ['PHPLINT', 'PHPCSFIXER', 'PHPCS', 'PHPMD', 'PHPUNIT'],
            '0,1,2,3,4'
        );

        $tasksQuestion->setMultiSelect(true);

        $this->tasks = $this->questionHelper->ask($this->input, $this->output, $tasksQuestion);
    }

    /**
     * Output some funky title to the command line.
     * @method outputTitle
     * @return void
     */
    private function outputTitle()
    {
        $this->output->writeln(
            "  ______ ___ _______\n" .
            " / ____/ __ |__   __|\n" .
            "| |   | |  | | | |\n" .
            "| |   | |  | | | |\n" .
            "| |___| |__| | | |\n" .
            " \_____\___\_\ |_|"
        );
    }

    /**
    * Determine which tools need to be run and run them againt $this->files
    * At this stage I think PHP, Composer, JS and such should become types
    * of adaptor going forward (as so the team can adapt per project)
    * @return void
    */
    private function go()
    {
        if ($this->isProcessingAnyComposerFile()) {
            // check that composer.json and .lock are both being commited else
            // something is wrong with this commit.
        }

        if ($this->isProcessingAnyPhpFile()) {
            foreach (array_keys($this->handlers) as $key) {
              print_r($this->handlers);
              print_r($this->tasks);
              print_r($key);
                if (in_array($key, $this->tasks)) {
                    $this->handlers[$key]->setFiles($this->files)->run();
                }
            }
        }
    }

    /**
    * Determine what type of file is currently being processed
    * @return array
    */
    private function processingFiles()
    {
        $files = [
          'php' => false,
          'composer' => false,
        ];

        foreach ($this->files as $file) {
            $isPhpFile = preg_match(self::PHP_FILES_IN_SRC, $file);

            if ($isPhpFile) {
                $files['php'] = true;
            }

            $isComposerFile = preg_match(self::COMPOSER_FILES, $file);

            if ($isComposerFile) {
                $files['composer'] = true;
            }

        }

        return $files;
    }

    /**
    * @return bool
    */
    private function isProcessingAnyComposerFile()
    {
        $files = $this->processingFiles();

        return $files['composer'];
    }

    /**
    * @return bool
    */
    private function isProcessingAnyPhpFile()
    {
        $files = $this->processingFiles();

        return $files['php'];
    }

    /**
    * extracts the commited files... would you have ever guessed?
    * @return void
    */
    private function extractCommitedFiles()
    {
        $this->output->writeln('<info>Extracting files</info>');

        $commited = new ExtractFilesStaged();

        $this->files = $commited->getFiles();

        if ($this->hasFiles()) {
            $this->output->writeln('<comment>Files found</comment>');

            foreach ($this->files as $file) {
                $this->output->writeln($file);
            }

        } else {
            $this->output->writeln('<comment>No files detected</comment>');
        }

    }

    /**
     * checks that there are files to run process against..
     * @method hasFiles
     * @return boolean
     */
    private function hasFiles()
    {
        return (count($this->files) >= 1);
    }

    /**
    * sets up some class props for use in script
    * @param  InputInterface  $input
    * @param  OutputInterface $output
    * @return void
    */
    private function setup(InputInterface $input, OutputInterface $output)
    {
        $this->questionHelper = $this->getHelper('question');
        $this->input = $input;
        $this->output = $output;

        // order of registration is important
        $this->handlers = [
            // 'PHPUNIT' => new PhpUnitHandler(),
            'PHPLINT' => new PhpLintHandler($this),
            'PHPCSFIXER' => new PhpCsFixerHandler($this->input, $this->output),
            'PHPCS' => new PhpCsHandler($this->input, $this->output),
            'PHPMD' => new PhpMdHandler($this->input, $this->output),
        ];

        if ($invokedFrom = $this->input->getArgument('invokedFrom')) {
            $this->invokedFrom = strtoupper($invokedFrom);
        }
    }
}
