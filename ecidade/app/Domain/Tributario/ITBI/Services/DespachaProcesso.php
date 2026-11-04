<?php

namespace App\Domain\Tributario\ITBI\Services;

use App\Domain\Patrimonial\Protocolo\Services\ProcessoService;
use App\Domain\Tributario\ITBI\Models\Itbi;

class DespachaProcesso
{
    /**
     * @var Itbi
     */
    private $itbi;
    /**
     * @var ProcessoService
     */
    private $processoService;

    public function __construct(Itbi $itbi)
    {
        $this->itbi            = $itbi;
        $this->processoService = new ProcessoService();
    }


    /**
     * @throws \Exception
     */
    public function execute()
    {
        $processo = $this->itbi->processo;

        if (empty($processo)) {
            throw new \Exception("Processo não encontrado!");
        }

        $transferencia = $processo->ultimaTransferencia();

        if (empty($transferencia)) {
            throw new \Exception("Última transferência não encontrada!");
        }

        $recebimento = $transferencia->recebimento();


        if (empty($recebimento)) {
            $recebimento = $this->processoService->receber($processo, $transferencia, "LIBERAÇÃO DE ITBI");
        }

        $despacho = $this->processoService->despachar(
            $recebimento,
            "Guia {$this->itbi->getGuia()} liberada com sucesso, 
            para emiti-la, basta acessar a opção de REEMISSÃO DA GUIA ITBI."
        );

        $this->processoService->salvarPdfDespacho($processo, $despacho);

        $processoProtocolo = new \processoProtocolo($this->itbi->it01_protprocesso);

        $parametros = new \stdClass();
        $parametros->despachoPublico = true;
        $this->processoService->getAndamentoProcesoService()->setParametros($parametros);
        $this->processoService->getAndamentoProcesoService()->notificar(
            $processoProtocolo,
            'despacho',
            "Seu ITBI foi liberado número {$this->itbi->getGuia()}"
        );
    }
}
