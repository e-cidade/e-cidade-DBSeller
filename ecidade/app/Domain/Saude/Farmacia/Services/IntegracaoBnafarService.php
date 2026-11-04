<?php

namespace App\Domain\Saude\Farmacia\Services;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Saude\Farmacia\Clients\BnafarClient;
use App\Notifications\UserNotification;
use Exception;

class IntegracaoBnafarService
{
    /**
     * @var BnafarClient
     */
    private $client;

    /**
     * @var integer
     */
    private $user;

    public function __construct(BnafarClient $client, $user)
    {
        $this->client = $client;
        $this->user = $user;
    }

    public function notificaUsuario($titulo, $mensagem)
    {
        $user = Usuario::find($this->user);
        $user->notify(new UserNotification($titulo, $mensagem));
    }

    public function enviar($procedimento, $dados)
    {
        if (is_array($dados) || $dados->codigo == null) {
            return $this->client->criar($procedimento, $dados);
        }
        return $this->client->alterar($procedimento, $dados, $dados->codigo);
    }

    /**
     * @param integer $protocolo
     * @throws Exception
     */
    public function processarProtocolo($protocolo)
    {
        $response = $this->client->protocolo('detalhar-processamento', $protocolo);
        if (!property_exists($response->processamento, 'fimProcessamento')) {
            throw new Exception("Protocolo {$protocolo} em processamento.");
        }

        foreach ($response->itensProcessados as $itemProcessado) {
            if (!$itemProcessado->sucesso) {
                continue;
            }
            BnafarEnviosService::vincular($itemProcessado->codigoBnafar, $itemProcessado->codigoOrigem, $protocolo);
        }

        if ($response->processamento->quantidadeItensInconsistente > 0) {
            $this->processarInconsistencias($protocolo);
        }

        $processados = $response->processamento->quantidadeItensTotal;
        $inconsistencias = $response->processamento->quantidadeItensInconsistente;
        $mensagem = 'Foi concluído o processamento de um lote no BNAFAR.';
        $mensagem .= "\nTotal de itens processados: ${processados}\nTotal de itens inconsistentes: {$inconsistencias}";
        $this->notificaUsuario('Processamento concluído.', $mensagem);
    }

    /**
     * @param integer $protocolo
     * @param integer $pageNumber
     * @throws Exception
     */
    private function processarInconsistencias($protocolo, $pageNumber = 0)
    {
        $response = $this->client->protocolo('inconsistencias', $protocolo, $pageNumber);

        foreach ($response->content as $item) {
            BnafarInconsistenciasService::salvar($item, $protocolo);
        }

        if ($pageNumber < $response->totalPages - 1) {
            $this->processarInconsistencias($protocolo, ++$pageNumber);
        }
    }
}
