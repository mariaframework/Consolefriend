<?php
/**
 * Created by PhpStorm.
 * User: tiago
 * Date: 20/03/16
 * Time: 02:15
 */

namespace MariaFW\FriendConsole;


use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Console
{
    protected $console;
    protected $friendname = 'friend';

    public function __construct()
    {
        if(!isset($this->console)){
            $this->console = new Application($this->friendname, 'Maria Framework');
        }
    }

    public function run(){
        $this->console->add(new ConsoleCommand());
        return $this->console->run();
    }

    public function link($alvo,$link){
        return symlink (  $alvo ,  $link );
    }
}