<?php

namespace MariaFW\FriendConsole;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Created by PhpStorm.
 * User: tiago
 * Date: 25/01/16
 * Time: 12:07
 */
class ConsoleCommand extends Command
{
    public $dir;

    /**
     * @return mixed
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * @param mixed $dir
     */
    public function setDir($dir)
    {
        $this->dir = $dir;
    }



    protected function configure()
    {
        $this
            ->setName('module:create')
            ->setDescription('Create a new module')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'module name'
            )
            ->addOption(
                'yell',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $text = null;
        $name = $input->getArgument('name');
        if ($name) {
            $modulo = new ModuleController();
            //$output->writeln($modulo->createModule($name));
            print_r($modulo->createModule($name));
        } else {
            $output->writeln('<error>You need to define a name for the module </error>');
            $output->writeln('<question>php friend module:create module name </question>');
        }

        if ($input->getOption('yell')) {
            $text = strtoupper($text);
        }

        $output->writeln($text);
    }



}