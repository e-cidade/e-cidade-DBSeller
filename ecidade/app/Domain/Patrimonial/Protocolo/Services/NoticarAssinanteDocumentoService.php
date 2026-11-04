<?php

namespace App\Domain\Patrimonial\Protocolo\Services;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Patrimonial\Protocolo\Model\Processo\Processo;
use App\Jobs\Patrimonial\Protocolo\NotificarEauth;
use App\Notifications\SolicitacaoAssinaturaNotification;
use App\Notifications\UserNotification;
use ECidade\Lib\Request\EAuth\EAuth;
use Illuminate\Support\Facades\DB;

class NoticarAssinanteDocumentoService
{
    private $eauth;

    public function __construct()
    {
        $this->eauth = resolve(EAuth::class);
    }

    public function dispatchJobAssinatura(
        $cpfCnpj,
        $nomeAssinante,
        $email,
        $processoDocumento,
        $solicitante,
        $documento
    ) {
        $this->notificaUserEcidade(
            $cpfCnpj,
            $processoDocumento->p01_protprocesso,
            $processoDocumento->p01_procandamint,
            $solicitante,
            $documento
        );
        $job = new NotificarEauth(
            $cpfCnpj,
            mb_convert_encoding($nomeAssinante, 'UTF-8', 'UTF-8'),
            $email,
            $processoDocumento->p01_protprocesso
        );
        dispatch($job);
    }

    public function notificaAssinatura($cpfCnpj, $nome, $email, $processoDocumento)
    {
        $processo = Processo::find($processoDocumento);
        $numeroAnoProcesso = "{$processo->p58_numero}/{$processo->p58_ano}";
        $mensagem = "Sua assinatura foi solicitada em um documento no Processo {$numeroAnoProcesso}";

        $result = $this->eauth->consultaUserCpf($cpfCnpj);
        if (!$result->success) {
            //não possui user no e-auth
            $this->eauth->salvarUsuarioEauth(
                $nome,
                $email,
                $cpfCnpj,
                null,
                $numeroAnoProcesso,
                true
            );
        } else {
            //possui user no e-auth e verifica se possui acesso ao processo eletronico do municipio
            $result = $this->eauth->verificaUserMunicipio($cpfCnpj);
            if (!$result->success) {
                $this->eauth->salvarUsuarioEauth(
                    $nome,
                    $email,
                    $cpfCnpj,
                    null,
                    $numeroAnoProcesso,
                    true
                );
            } else {
                //possui user no e-auth e no municipio, somente notificar
                $this->eauth->notificaAssinatura($mensagem, $cpfCnpj);
            }
        }
    }

    public function verificarUserEcicidade($cpfCnpj)
    {
        return DB::select("select configuracoes.db_usuacgm.id_usuario from configuracoes.db_usuacgm
                            inner join protocolo.cgm on protocolo.cgm.z01_numcgm = configuracoes.db_usuacgm.cgmlogin
                            where protocolo.cgm.z01_cgccpf = '{$cpfCnpj}' limit 1");
    }

    public function notificaUserEcidade($cpfCnpj, $processo, $andamentoInterno, $solicitante, $documento)
    {
        $processo = Processo::find($processo);
        $numeroAnoProcesso = "{$processo->p58_numero}/{$processo->p58_ano}";
        $nomeSolicitante = utf8_encode($solicitante->z01_nome);
        $mensagem = "{$nomeSolicitante} solicitou sua assinatura em um documento no Processo {$numeroAnoProcesso}";

        $action = "web/patrimonial/protocolo/solicitacao-assinatura-documentos/{$documento->id}";

        $result = $this->verificarUserEcicidade($cpfCnpj);
        if (count($result) > 0) {
            $user = Usuario::find($result[0]->id_usuario);
            $user->notify(new SolicitacaoAssinaturaNotification(
                \DBString::utf8_encode_all("Você tem uma nova solicitação de assinatura"),
                \DBString::utf8_encode_all($mensagem),
                $action
            ));
        }
    }
}
