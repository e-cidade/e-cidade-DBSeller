<?php

namespace App\Domain\Tributario\Arrecadacao\Services;

use App\Domain\Tributario\Arrecadacao\Repositories\DataLancamentoRepository;

/**
 * Classe para cuidar das datas de lancamento tributario
 * da tabela informacaodebito
 */
class DataLancamentoService
{
    /**
     * @type DataLancamentoRepository
     */
    private $repository;

    /**
     * Construtor da classe
     */
    public function __construct(DataLancamentoRepository $dataLancamentoRepository)
    {
        $this->repository = $dataLancamentoRepository;
    }

    /**
     * @param PesquisaCgmRequest $request
     */
    public function pesquisaCgm($request)
    {
        $porPagina = $request->porPagina ? $request->porPagina : 10;
        $pagina = $request->pagina ? $request->pagina : 1;
        $cgm = $request->cgm;

        return $this->repository->pesquisaCgm($cgm, $porPagina, $pagina);
    }

    /**
     * @param PesquisaMatriculaRequest $request
     */
    public function pesquisaMatricula($request)
    {
        $porPagina = $request->porPagina ? $request->porPagina : 10;
        $pagina = $request->pagina ? $request->pagina : 1;
        $matricula = $request->matricula;

        return $this->repository->pesquisaMatricula($matricula, $porPagina, $pagina);
    }

    /**
     * @param PesquisaInscricaoMunicipalRequest $request
     */
    public function pesquisaInscricaoMunicipal($request)
    {
        $porPagina = $request->porPagina ? $request->porPagina : 10;
        $pagina = $request->pagina ? $request->pagina : 1;
        $inscricao = $request->inscricao;

        return $this->repository->pesquisaInscricaoMunicipal($inscricao, $porPagina, $pagina);
    }

    /**
     * @param RegistroDataLancamentoDebitoRequest $request
     */
    public function editaRegistro($request)
    {
        $numpre = $request->numpre;
        $data = $request->dataLancamento ? $request->dataLancamento : "";
        $observacao = $request->observacao ? $request->observacao : "";
        return $this->repository->editaRegistro($numpre, $data, $observacao);
    }

    /**
     * @param RegistroDataLancamentoDebitoRequest $request
     */
    public function incluiRegistro($request)
    {
        $numpre = $request->numpre;
        $data = $request->dataLancamento ? $request->dataLancamento : "";
        $observacao = $request->observacao ? $request->observacao : "";
        $resultadosPesquisaNumpre = $this->repository->pesquisaNumpre($numpre, false);

        foreach ($resultadosPesquisaNumpre as $item) {
            $numpar = $item->numpar;
            $this->repository->incluiRegistro($numpre, $numpar, $data, $observacao);
        }
    }

    /**
     * @param integer $numpreRegistro
     */
    public function excluiRegistro($numpreRegistro)
    {
        return $this->repository->excluiRegistro($numpreRegistro);
    }

    /**
     * Metodo para buscar as labels para a tabela do frontend
     */
    public function getLabelsLancamentoDebito()
    {
        $data = [];

        $nomeCampos = [
            "k00_numpre",
            "k00_dtoper",
            "k00_tipo",
            "k163_data",
            "k00_numpar"
        ];

        $nomeLabels = $this->repository->getLabelsLancamentoDebito($nomeCampos);

        if (count($nomeCampos) == count($nomeLabels)) {
            for ($index = 0; $index < count($nomeCampos); $index++) {
                $data[$nomeCampos[$index]] = ucfirst($nomeLabels[$index]["rotulo"]);
            }
        }

        return $data;
    }
}
