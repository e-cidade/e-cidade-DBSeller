<?php

namespace App\Providers;

use ECidade\V3\Extension\Registry;
use ECidade\V3\Extension\Request;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class QueueServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Queue::before(function (JobProcessing $processing) {
            if ($processing->connectionName === 'sync' || env('QUEUE_DRIVER') === 'sync') {
                throw new \Exception('Não é possível executar esse serviço de forma síncrona. Contate o suporte.');
            }

            try {
                if (session_status() === PHP_SESSION_ACTIVE) {
                    session_destroy();
                }

                session_start();
            } catch (\Exception $e) {
                Log::error('Não foi possível startar a sessão.', $e->getTrace());
            }

            $fakeRequest = new Request();
            Registry::set('app.request', $fakeRequest);

            $_SESSION["DB_login"] = "dbseller";
            $_SESSION["DB_id_usuario"] = "1";

            require_once(modification("libs/db_stdlib.php"));
            require_once(modification("libs/db_utils.php"));
            global $conn;
            if (!$conn) {
                require_once(modification('libs/db_conecta_cli.php'));
            }
        });

        Queue::looping(function () {
            while (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
        });
    }
}
