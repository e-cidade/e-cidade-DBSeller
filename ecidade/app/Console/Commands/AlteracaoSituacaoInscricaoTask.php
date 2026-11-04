<?php

namespace App\Console\Commands;

use ECidade\V3\Extension\Registry;
use ECidade\V3\Extension\Request;
use Exception;
use Illuminate\Console\Command;

/**
 * Class AlteracaoSituacaoInscricaoTask
 * @package App\Console\Commands
 */
class AlteracaoSituacaoInscricaoTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matriculaonline:alteracaoinscricao';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Altera automaticamente as inscrições não efetivadas';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws Exception
     */
    public function handle()
    {
        $this->requireDependencies();

        $pluginsService = new \PluginService();
        $pluginsService->getPlugins();
        $sNomePlugin = 'matricula-on-line';
        if ((is_dir("plugins/{$sNomePlugin}") && file_exists("plugins/{$sNomePlugin}/Manifest.xml"))) {
            try {
                $alteracaoSituacaoService = new \ECidade\Educacao\MatriculaOnline\Service\AlteracaoSituacaoService();
                $alteracaoSituacaoService->processar();
                file_put_contents('tmp/alteracaosituacaotask.log', '');
            } catch (Exception $e) {
                $time = date('d/m/Y H:s:i');
                $log = "[{$time}] {$e->getMessage()} " . PHP_EOL;
                file_put_contents('tmp/alteracaosituacaotask.log', $log);
            }
        }
        return 0;
    }

    private function requireDependencies()
    {
        session_start();
        $fakeRequest = new Request();
        Registry::set('app.request', $fakeRequest);

        $_SESSION["DB_acessado"] = 2000287;
        $_SESSION["DB_datausu"] = time();
        $_SESSION["DB_login"] = "dbseller";
        $_SESSION["DB_id_usuario"] = "1";
        $_SESSION["DB_coddepto"] = 1;

        $_SERVER['REQUEST_URI'] = 'localhost';

        require_once(modification("libs/db_stdlib.php"));
        require_once(modification("libs/db_utils.php"));
        require_once(modification("libs/db_conecta.php"));
        require_once(modification("dbforms/db_funcoes.php"));
    }
}
