<?php

namespace App\Domain\Tributario\Diversos\Services;

use App\Domain\Tributario\Diversos\Repositories\ProcedenciaRepository;
use App\Domain\Patrimonial\Protocolo\Requests\ProcedenciaParamsRequest;

class ProcedenciaService
{
    /**
     * @var ProcedenciaRepository
     */
    private $procedenciaRepository;

    public function __construct(ProcedenciaRepository $procedenciaRepository)
    {
        $this->procedenciaRepository = $procedenciaRepository;
    }

    /**
     * Metodo para buscar as labels
     *
     * @return object
     */
    public function getRotulos()
    {
        $data = [];

        $nomeCampos = [
            'dv09_procdiver',
            'dv09_descra',
            'dv09_descr',
            'dv09_receit',
            'dv09_hist',
            'dv09_proced',
            'dv09_tipo',
            'dv09_instit',
            'dv09_dtlimite',
            'dv09_cobranca'
        ];

        $nomeLabels = $this->procedenciaRepository->getRotulos($nomeCampos);

        if (count($nomeCampos) == count($nomeLabels)) {
            for ($index = 0; $index < count($nomeCampos); $index++) {
                $data[$nomeCampos[$index]] = ucfirst($nomeLabels[$index]["rotulo"]);
            }
        }

        return $data;
    }

    /**
     * @param ProcedenciaParamsRequest $request
     * @return array
     */
    public function getByParams($request)
    {
        $sequencial = $request->sequencial ? $request->sequencial : '';
        $receita = $request->receita ? $request->receita : '';
        $descricaoabreviada = $request->descricaoabreviada ? $request->descricaoabreviada : '';
        $descricao = $request->descricao ? $request->descricao : '';
        $filtraTipoCobrancaFalso = $request->filtraTipoCobrancaFalso
            ? $request->filtraTipoCobrancaFalso : '';
        $porPagina = $request->porPagina ? $request->porPagina : 10;

        $resultados = $this->procedenciaRepository->getByParams(
            $sequencial,
            $receita,
            $descricaoabreviada,
            $descricao,
            $filtraTipoCobrancaFalso,
            $porPagina
        )->toArray();

        return $resultados;
    }
}
