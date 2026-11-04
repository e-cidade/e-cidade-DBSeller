<?php

use ECidade\Core\Autoloader as EcidadeAutoloader;
use ECidade\Core\Config as AppConfig;
use ECidade\V3\Config\Data as ConfigData;
use ECidade\V3\Error\Handler\Error as ErrorHandler;
use ECidade\V3\Error\Handler\Exception as ExceptionHandler;
use ECidade\V3\Error\Handler\Shutdown as ShutdownHandler;
use ECidade\V3\Event\Manager as EventManager;
use ECidade\V3\Extension\Container;
use ECidade\V3\Extension\Logger;
use ECidade\V3\Extension\Registry;

require_once 'bootstrap/autoload.php';

// load definitions (functions and constants)
require_once __DIR__ . DIRECTORY_SEPARATOR . 'definitions.php';

//
// DEFAULT AUTOLOADING
//

// ecidade
require_once(ECIDADE_PATH . 'libs/db_autoload.php');
// composer
require_once(ECIDADE_PATH . 'vendor/autoload.php');

//
// END DEFAULT AUTOLOADING
//

/**********************************************************************************************************************/

//
// ECIDADE CUSTOM AUTOLOADER - PHPFIG-BASED
//

// o autoloader aqui requerido, contém modificacoes especificas para o ecidade
// como por exemplo, a inclusao da funcao modification nos requires da classe.
// a classe é um cópia da classe de autoloader disponibilizado pelo PHP-FIG,
// com o adicional de um método para a utilizacao como um singleton :(

require_once('src/Core/Autoloader.php');

$ecidadeLoader = new EcidadeAutoloader();
$ecidadeLoader->addNamespace("ECidade\\", ECIDADE_PATH . "src/");
$ecidadeLoader->addNamespace("ECidade\\Api\\", ECIDADE_PATH . "api/");
$ecidadeLoader->addNamespace("ECidade\\Tests\\", ECIDADE_PATH . "tests/unitarios/src/");

// o namespace Core não pode ser alterado por modifications (!?)
$ecidadeLoader->addNamespace("ECidade\\Core", ECIDADE_PATH . "src/Core", true, false);
// namespace do ecidade 3, que foi migrado do "extension"; o ultimo parametro indica se o namespace podera ser afetado
// por modifications
$ecidadeLoader->addNamespace("ECidade\\V3", ECIDADE_PATH . "src/V3", true, false);

// o namespace Package pode ser alterado por modifications
$ecidadeLoader->addNamespace('ECidade\\Package\\', ECIDADE_EXTENSION_PACKAGE_PATH, true, true);

$ecidadeLoader->register();

Registry::set('app.loader', $ecidadeLoader);

//
// END ECIDADE CUSTOM AUTOLOADER - PHPFIG-BASED
//

/**********************************************************************************************************************/

//
// CONFIGURATION LOADING AND SETUP
//

Registry::set('app.config', new AppConfig());

require_once ECIDADE_PATH . 'config/application.default.php';

if (file_exists(ECIDADE_PATH . 'config/application.php')) {
  require_once ECIDADE_PATH . 'config/application.php';
}

/**
 * Adicionado umask para criação dos arquivos do sistema
 * umask padrão de 775 (rwxrwxr-x)
 */
umask(0002);

ini_set('display_errors', Registry::get('app.config')->get('php.display_errors'));
ini_set('error_reporting', Registry::get('app.config')->get('php.error_reporting'));
error_reporting(Registry::get('app.config')->get('php.error_reporting'));

//
// END CONFIGURATION LOADING E SETUP
//

/**********************************************************************************************************************/

// CONTAINER
Registry::set('app.container', new Container());

// APP LOGGING
Registry::get('app.container')->register('app.logger', function() {

  $config = Registry::get('app.config');
  $path = $config->get('app.log.path');
  $verbosity = $config->get('app.log.verbosity', Logger::QUIET);
  return new Logger($path, $verbosity);
});

// APP ERROR LOGGING
Registry::get('app.container')->register('app.error.logger', function() {

  $config = Registry::get('app.config');
  $path = $config->get('app.error.log.path');
  return new Logger($path, Logger::ERROR);
});

//
// EVENT SETUP
//

if (!getenv('TEST')) {
    if (Registry::get('app.config')->get('app.error.handler') === 'Whoops') {
        $whoops = new Whoops\Run;

        if (Registry::get('app.config')->get('php.display_errors')) {
            if (Whoops\Util\Misc::isAjaxRequest()) {
                $whoops->appendHandler(new Whoops\Handler\JsonResponseHandler);
            } else {
                $handler = new Whoops\Handler\PrettyPageHandler;
                $handler->handleUnconditionally(true);

                $whoops->appendHandler($handler);
            }
        }

        $whoops->prependHandler(new ECidade\V3\Error\Handler\LogFileHandler);
        $whoops->silenceErrorsInPaths('/(.*)/', !Registry::get('app.config')->get('php.error_reporting'));
        $whoops->allowQuit(true);
        $whoops->register();
    } else {
        // Registra os controladores de erros, caso nao esteja executando testes
        ErrorHandler::register();
        ExceptionHandler::register();
        ShutdownHandler::register();
    }
}

Registry::set('app.eventManager', new EventManager());

Registry::get('app.container')->register('app.configData', function() {
  return ConfigData::restore();
});

//
// END EVENT SETUP
//

$dotenv = \Dotenv\Dotenv::createImmutable(ECIDADE_PATH);
$dotenv->safeLoad();

/**********************************************************************************************************************/

//
// START OF APPLICATION BUSINESS CONTENT
//

Registry::get('app.container')->register('tributario.container', function($applicationContainer) {
  return new ECidade\Tributario\Container($applicationContainer);
});

Registry::get('app.container')->register('patrimonial.container', function($applicationContainer) {
  return new ECidade\Patrimonial\Container($applicationContainer);
});

Registry::get('app.container')->register('configuracao.container', function($applicationContainer) {
  return new ECidade\Configuracao\Container($applicationContainer);
});
//
// END OF APPLICATION BUSINESS CONTENT
//

/**********************************************************************************************************************/

//
// PROFILING XHPROF - ONLY IN LOCAL ENVIRONMENT
//
if (env('APP_ENV') === 'local' && extension_loaded('xhprof')) {
    try {
        if (file_exists('config/xhprof.php') && class_exists('\Xhgui\Profiler\Profiler')) {
            $config = require_once('config/xhprof.php');
            $profiler = new \Xhgui\Profiler\Profiler($config);
            $profiler->start();
        }
    } catch (Exception $e) {
        Registry::get('app.container')->get('app.logger')->warning($e->getMessage());
    }
}
//
// END PROFILING
//
