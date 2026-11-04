<?php

namespace App\Domain\Patrimonial\Protocolo\Services;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Jobs\Patrimonial\Protocolo\NotificarTransferenciaProcessoJob;
use App\Notifications\UserNotification;

class NotificarTransferenciaProcessoService
{
    public function dispatchJobNotificarTransferencia($idUSer, $mensagem)
    {
        $this->iniciarLaravel();
        $id = (int)$idUSer;
        $job = new NotificarTransferenciaProcessoJob(
            $id,
            \DBString::utf8_encode_all($mensagem)
        );
        dispatch($job);
    }

    public function notificarTransferencia($idUSer, $mensagem)
    {
        $user = Usuario::find($idUSer);
        $user->notify(
            new UserNotification(
                "Nova Transferência Recebida",
                $mensagem
            )
        );
    }

    public function iniciarLaravel()
    {
        $app = require_once ECIDADE_PATH . 'bootstrap/app.php';
        // App já iniciado
        if ($app === true) {
            return;
        }
        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
    }
}
