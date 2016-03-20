<?php
/**
 * Created by PhpStorm.
 * User: tiago
 * Date: 25/01/16
 * Time: 13:43
 */

namespace MariaFW\FriendConsole;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ModuleController implements InterfaceController
{
    protected $filesystem;
    protected $command;
    protected $path;


    public function __construct($dirApp = DIR_CONSOLE)
    {
        $adapter = new Local(__DIR__.'/');
        $filesystem = new Filesystem($adapter);
        $this->filesystem = $filesystem;
        $this->command = new ConsoleCommand();
        $this->path = $dirApp;
    }

    public function createModule($name){
        $dir = $this->path.ucfirst($name);
        $exists = $this->filesystem->has($dir);
        if($exists === false){
            $this->enablemodule();
            $this->filesystem->createDir([
                $dir,
                $dir.'/migrations',
                $dir.'/template',
             ]);
            $this->filesystem->write($dir.'/Route.php',$this->FileRoute($name));
            $this->filesystem->write($dir.'/Ping.php',$this->FilePing($name));
            $this->filesystem->write($dir.'/PageFactory'.ucfirst($name).'.php',$this->FileFactory($name));
            $this->filesystem->write($dir.'/PageAction'.ucfirst($name).'.php',$this->FileAction($name));
            $this->filesystem->write($dir.'/template/index.'.strtolower($name).'.phtml',$this->FileTemplate($name));
            return $this->enablemodule();
        }else{
            return '<error>:( , O módulo '.$name.' Já Existe!<error>';
        }
    }

    public function enablemodule(){
        $finder = new Finder();
        $busca = $finder->files()->contains('PhpFileProvider');
        return $busca;
    }

    public function FileRoute ($modulo){
        $modulo = ucfirst($modulo);
        $text = "<?php ".PHP_EOL;
        $text .= "namespace App\\$modulo;".PHP_EOL;
        $text .= PHP_EOL;
        $text .= "class Route { ".PHP_EOL;
        $text .= PHP_EOL;
        $text .= "public function __invoke() {".PHP_EOL;
        $text .= "  return [ ".PHP_EOL;
        $text .= "      'routes' => [ ".PHP_EOL;
        $text .= "          [ ".PHP_EOL;
        $text .= "              'name' => 'home.".strtolower($modulo)."', ".PHP_EOL;
        $text .= "              'path' => '/".strtolower($modulo)."', ".PHP_EOL;
        $text .= "              'middleware' => PageAction".$modulo."::class, ".PHP_EOL;
        $text .= "              'allowed_methods' => ['GET'], ".PHP_EOL;
        $text .= "          ],".PHP_EOL;
        $text .= "      ],".PHP_EOL;
        $text .= "      ];".PHP_EOL;
        $text .= "  } ".PHP_EOL;
        $text .= "} ".PHP_EOL;
        return $text;
    }

    public function FilePing($modulo){
        $modulo = ucfirst($modulo);
        $text = "<?php ".PHP_EOL;
        $text .= "namespace App\\$modulo;".PHP_EOL;
        $text .= PHP_EOL;
        /** USES */
        $text .= "use Zend\\Diactoros\\Response\\JsonResponse;".PHP_EOL;
        $text .= "use Psr\\Http\\Message\\ResponseInterface;".PHP_EOL;
        $text .= "use Psr\\Http\\Message\\ServerRequestInterface;".PHP_EOL;

        //** INICIO DA CLASSE */
        $text .= "class PingAction ".PHP_EOL;
        $text .= "{".PHP_EOL;
        $text .= '  public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)'.PHP_EOL;
        $text .= "  {".PHP_EOL;
        $text .= "      return new JsonResponse(['ack' => time()]);".PHP_EOL;
        $text .= "  }".PHP_EOL;
        $text .= "}".PHP_EOL;

        return $text;
    }

    public function FileFactory($modulo){
        $modulo = ucfirst($modulo);
        $text = "<?php ".PHP_EOL;
        $text .= "namespace App\\$modulo;".PHP_EOL;
        $text .= PHP_EOL;
        /** USES */
        $text .= "use Interop\\Container\\ContainerInterface;".PHP_EOL;
        $text .= "use Zend\\Expressive\\Router\\RouterInterface;".PHP_EOL;
        $text .= "use Zend\\Expressive\\Template\\TemplateRendererInterface;".PHP_EOL;
        //** INICIO DA CLASSE */
        $text .= 'class PageFactory'.ucfirst($modulo).' '.PHP_EOL;
        $text .= "{".PHP_EOL;
        $text .= '  public function __invoke(ContainerInterface $container)'.PHP_EOL;
        $text .= "  {".PHP_EOL;
        $text .= '      $router   = $container->get(RouterInterface::class); '.PHP_EOL;
        $text .= '      $template = ($container->has(TemplateRendererInterface::class))'.PHP_EOL;
        $text .= '          ? $container->get(TemplateRendererInterface::class)'.PHP_EOL;
        $text .= '          : null;'.PHP_EOL;
        $text .= '      return new PageAction'.ucfirst($modulo).'($router, $template);'.PHP_EOL;
        $text .= '  }'.PHP_EOL;
        $text .= '}'.PHP_EOL;
        return $text;
    }

    public function FileAction($modulo){
        $modulo = ucfirst($modulo);
        $text = "<?php ".PHP_EOL;
        $text .= "namespace App\\$modulo;".PHP_EOL;
        $text .= PHP_EOL;
        /** USES */
        $text .= "use Psr\\Http\\Message\\ResponseInterface;".PHP_EOL;
        $text .= "use Psr\\Http\\Message\\ServerRequestInterface;".PHP_EOL;
        $text .= "use Zend\\Diactoros\\Response\\HtmlResponse;".PHP_EOL;
        $text .= "use Zend\\Diactoros\\Response\\JsonResponse;".PHP_EOL;
        $text .= "use Zend\\Expressive\\Router;".PHP_EOL;
        $text .= "use Zend\\Expressive\\Template;".PHP_EOL;
        $text .= "use Zend\\Expressive\\Plates\\PlatesRenderer;".PHP_EOL;

        //** INICIO DA CLASSE */
        $text .= 'class PageAction'.ucfirst($modulo).' '.PHP_EOL;
        $text .= "{".PHP_EOL;
        $text .= "".PHP_EOL;
        $text .= '  private $router;'.PHP_EOL;
        $text .= "".PHP_EOL;
        $text .= '  private $template;'.PHP_EOL;
        $text .= ''.PHP_EOL;
        $text .= '  public function __construct(Router\RouterInterface $router, Template\TemplateRendererInterface $template = null)'.PHP_EOL;
        $text .= '  {'.PHP_EOL;
        $text .= '       $this->router   = $router;'.PHP_EOL;
        $text .= '       $this->template = $template;'.PHP_EOL;
        $text .= '  }'.PHP_EOL;
        $text .= '  public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)'.PHP_EOL;
        $text .= '  {'.PHP_EOL;
        $text .= '      $data = [];'.PHP_EOL;
        $text .= '      return new HtmlResponse($this->template->render(\'module-'.strtolower($modulo).'::index.'.strtolower($modulo).'\', $data));'.PHP_EOL;
        $text .= '  }'.PHP_EOL;
        $text .= '}'.PHP_EOL;
        return $text;
    }

    public function FileTemplate($modulo){
        $text = '<?php $this->layout(\'layout::default\', [\'title\' => \'Home\']) ?>'.PHP_EOL;
        $text .= '<div class="container">
    <div class="page-header">
        <h1>Eu sou um módulo '.$modulo.' do Maria Framework</h1>
    </div>
    <p class="lead">Construído para os desenvolvedores que precisam de velocidade e simplicidade no desenvolvimento
    para criar desde simples aplicações a grandes soluções.</p>
</div>'.PHP_EOL;
        return $text;

    }


}